<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Donation;
use App\Draw;
use App\Game;
use App\LuckyRatio;
use App\Ngo;
use App\Transaction;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Prophecy\Doubler\NameGenerator;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:donator');
        //$this->middleware('software');
    }

    public function getCreateDonation(Request $request, Channel $channel = null) {

        $selected_channel = false;
        $draws = array();

        if($channel->exists) {
            $selected_channel = $channel;
            $draws = $selected_channel->draws->all();
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
}
