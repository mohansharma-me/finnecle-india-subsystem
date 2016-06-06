<?php

namespace App\Http\Controllers;

use App\Channel;
use App\ClearRequest;
use App\Donation;
use App\Draw;
use App\Game;
use App\LuckyRatio;
use App\Ngo;
use App\PaidTransaction;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Prophecy\Doubler\NameGenerator;
use Symfony\Component\HttpFoundation\Response;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:donator', [
            'only' => ['getCreateDonation', 'postCreateDonation', 'getDonations', 'postClearRequest']
        ]);

        $this->middleware('role:cashier', [
            'only' => ['getCheckDonation', 'getAjaxDonation', 'postPaidDonation']
        ]);

        //$this->middleware('software');
    }

    public function getCreateDonation(Request $request, Channel $channel = null) {

        $selected_channel = false;
        $draws = array();

        $current_time = Carbon::now()->format('H:i');

        if($channel->exists) {
            $selected_channel = $channel;
            $draws = $selected_channel->remainingDraws();
        }

        return view('donator.create-donation',[
            'channels' => Channel::all(),
            'games' => Game::all(),
            'draws' => $draws,
            'selected_channel' => $selected_channel
        ]);
    }

    public function postCreateDonation(Request $request) {
        $this->validate($request, [
            "draw" => 'required|numeric'
        ]);

        $draw_id = $request->draw;
        $draw = Draw::find($draw_id);
        if($draw) {
            if(count($request->ngo) > 0) {
                $ngos = array_diff_key($request->ngo, array_flip(array_keys($request->ngo, 0)));

                if(count($ngos) == 0) {
                    return redirect()->back()->withInput(Input::all())->with(['error_message' => "No ngo amount entered"]);
                }

                foreach($ngos as $ngo_id => $ngo) {
                    $ngo = Ngo::find($ngo_id);
                    if($ngo) {

                    } else {
                        return redirect()->back()->withInput(Input::all())->with(['error_message' => "Sorry, some ngo's entries are not found, please try again."]);
                    }
                }

                $current_user = Auth::user();
                $center = $current_user->center;

                $transaction = new Transaction();
                $transaction->draw_id = $draw->id;
                $transaction->center_id = $center->id;
                $transaction->paid = false;
                $transaction->center_commission = $center->commission_ratio;
                $transaction->note = $request->note;
                if($transaction->save()) {
                    $transaction->ref = $center->id.$draw->id.$transaction->id.rand($transaction->id, $transaction->id*100);
                    $transaction->update();

                    // transaction completed , create donation
                    $donationCreated = array();
                    foreach($ngos as $ngo_id => $amount) {
                        $ngo = Ngo::find($ngo_id);
                        $game_id = $ngo->ngo_group->game->id;
                        $center->ratios()->where('game_id', $game_id);
                        $luckyRatio = $center->ratios()->where('game_id', $game_id)->first();

                        $donation = new Donation();
                        $donation->ngo_id = $ngo_id;
                        $donation->amount = $amount;
                        $donation->lucky_ratio = $luckyRatio->ratio;
                        if($transaction->donations()->save($donation)) {

                        } else {
                            $transaction->delete();
                            return redirect()->back()->withInput(Input::all())->with(['error_message'=> "There was an error while submitting donations, try again."]);
                        }
                    }

                    // all done
                    $barcodeGenerator = new \App\Lib\BarcodeGenerator\BarcodeGeneratorPNG();
                    return redirect()->back()->with([
                        "success_message" => "Donation slip created.",
                        "transaction"=>$transaction,
                        "barcode" => base64_encode($barcodeGenerator->getBarcode($transaction->ref, $barcodeGenerator::TYPE_CODE_128))
                    ]);

                } else {
                    return redirect()->back()->withInput(Input::all())->with(['error_message' => "There was an error while processing transaction, try again."]);
                }
            } else {
                return redirect()->back()->withInput(Input::all())->with(['error_message' => "Sorry, No ngo's entries are found, please try again."]);
            }
        } else {
            return redirect()->back()->withInput(Input::all())->with(['error_message' => "Sorry, draw not found, please try again."]);
        }
    }

    public function getDonations(Request $request) {

        $this->validate($request,[
            'date' => 'date_format:d-m-Y',
            'transaction_id' => 'numeric'
        ], [
            'date.date_format' => "Input date isn't in valid format (DD-MM-YYYY)",
            'transaction_id.numeric' => "Transaction ID should be unique numeric value"
        ]);

        $date = $request->has('date') ? $request->date : Carbon::now()->format('d-m-Y');
        $db = Auth::user()->isCenter()->transactions()->whereBetween('created_at', [Carbon::parse($date), Carbon::parse($date)->addDay()]);
        if($request->has('transaction_id')) {
            $db->where('ref', $request->transaction_id);
        }
        $transactions = $db->orderBy('id', 'desc')->paginate(10);
        return view('donator.donations', [
            'transactions' => $transactions,
            "current_date" => $date,
            "transaction_id" => $request->transaction_id
        ]);
    }

    public function getCheckDonation(Request $request, Transaction $transaction) {
        return view('cashier.dashboard', ['transaction' => $transaction]);
    }

    public function postAjaxDonation(Request $request) {

        if($request->has('donation') && $request->isXmlHttpRequest()) {
            $x_donation = $request->donation;
            if(is_numeric($x_donation)) {
                $donation_m = Transaction::where('ref',$x_donation)->first();
                if(isset($donation_m)) {
                    return response()->json(['success'=>true, 'url'=> route('check-donation', ['transaction'=>$donation_m->id])]);
                }
            }
        }

        return response()->json(['success'=>false]);
    }

    public function postPaidDonation(Request $request) {

        if($request->has("donation") && $request->isXmlHttpRequest()) {
            $x_donation = $request->donation;
            if(is_numeric($x_donation)) {
                $donation_m = Transaction::find($x_donation);
                if(isset($donation_m)) {
                    $paid_t = new PaidTransaction();
                    $paid_t->transaction_id = $donation_m->id;
                    $paid_t->center_id = Auth::user()->isCenter()->id;
                    $paid_t->center_commission = Auth::user()->isCenter()->commission_ratio;
                    if($paid_t->save()) {
                        $donation_m->paid = true;
                        $donation_m->update();
                        return response()->json(['success'=>true, 'url'=> route('check-donation', ['transaction'=>$donation_m->id])]);
                    }
                }
            }
        }

        return response()->json(['success'=>false]);
    }

    public function postClearRequest(Request $request) {

        if($request->has("t")) {

            $t = $request["t"];
            $transactions = Transaction::whereIn("id", $t)->get();

            if(count($t) == count($transactions)) {

                $sumAmount = 0;
                foreach($transactions as $transaction) {
                    $tr_amount = $transaction->amount();
                    $sumAmount += $tr_amount - ($tr_amount * $transaction->center_commission / 100);
                }

                $clearRequest = new ClearRequest();
                $clearRequest->center_id = Auth::user()->isCenter()->id;
                $clearRequest->amount = $sumAmount;
                $clearRequest->status = "pending";
                $clearRequest->slips = count($transactions);

                if($clearRequest->save()) {

                    foreach($transactions as $transaction) {
                        $transaction->clear_request_id = $clearRequest->id;
                        $transaction->update();
                    }

                    return redirect()->back()->with(['success_message'=>"Clear request is successfully sent!"]);

                } else {
                    return redirect()->back()->with(['error_message'=>"There was an error while requesting new clear request, try again."]);
                }

            } else {
                return redirect()->back()->with(['error_message'=>"Request transaction(s) are missing or already in process, try again"]);
            }

        } else {
            return redirect()->back()->with(['error_message'=>"Request isn't valid, try again"]);
        }

        return redirect()->back()->with(['error_message'=>"Invalid request, try again"]);

    }

    public function postCashierClearRequest(Request $request) {

        if($request->has("t")) {

            $t = $request["t"];
            $transactions = PaidTransaction::whereIn("id", $t)->get();

            if(count($t) == count($transactions)) {

                $sumAmount = 0;
                foreach($transactions as $transaction) {
                    $tr_amount = $transaction->transaction->amount();
                    $sumAmount += $tr_amount - ($tr_amount * $transaction->center_commission / 100);
                }

                $clearRequest = new ClearRequest();
                $clearRequest->center_id = Auth::user()->isCenter()->id;
                $clearRequest->amount = $sumAmount;
                $clearRequest->status = "pending";
                $clearRequest->slips = count($transactions);

                if($clearRequest->save()) {

                    foreach($transactions as $transaction) {
                        $transaction->clear_request_id = $clearRequest->id;
                        $transaction->update();
                    }

                    return redirect()->back()->with(['success_message'=>"Clear request is successfully sent!"]);

                } else {
                    return redirect()->back()->with(['error_message'=>"There was an error while requesting new clear request, try again."]);
                }

            } else {
                return redirect()->back()->with(['error_message'=>"Request transaction(s) are missing or already in process, try again"]);
            }

        } else {
            return redirect()->back()->with(['error_message'=>"Request isn't valid, try again"]);
        }

        return redirect()->back()->with(['error_message'=>"Invalid request, try again"]);

    }

    public function getCashierClearRequests() {
        return view('cashier.requests', [
            'clear_requests' => Auth::user()->isCenter()->clear_requests()->orderBy('id', 'desc')->paginate(30)
        ]);
    }

}
