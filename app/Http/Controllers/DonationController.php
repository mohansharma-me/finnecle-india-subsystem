<?php

namespace App\Http\Controllers;

use App\Channel;
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
            'only' => ['getCreateDonation', 'postCreateDonation', 'getDonations']
        ]);

        $this->middleware('role:cashier', [
            'only' => ['getCheckDonation', 'getAjaxDonation']
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
                $donation_m = Transaction::find($x_donation);
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
}
