<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Declaration;
use App\Donation;
use App\Draw;
use App\Game;
use App\Http\Requests;
use App\Ngo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class DeclarationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function getDeclareNgo(Request $request) {
        return view('admin.declaration.index', [
            'channels' => Channel::all()
        ]);
    }

    public function getDeclareNgo_channel(Request $request, Channel $channel) {
        return view('admin.declaration.index', [
            'channels' => Channel::all(),
            'sel_channel' => $channel
        ]);
    }

    public function getDeclareNgo_channel_draw(Request $request, Channel $channel, Draw $draw) {

        $games = Game::all();

        $totalDonations = Donation::totalDonation($draw->id);

        return view('admin.declaration.index', [
            'channels' => Channel::all(),
            'sel_channel' => $channel,
            'sel_draw' =>  $draw,
            'games' => $games,
            'totalDonationAmount' => $totalDonations
        ]);
    }

    public function getDeclareNgo_channel_draw_ngo(Request $request, Channel $channel, Draw $draw, Ngo $ngo) {

        if($ngo->ngo_group->game->id == 1 || $ngo->ngo_group->game->id == 2) {
            return redirect()->back()->withInput(Input::all())->with(['error_message' => "This NGO isn't allowed"]);
        }

        return view('admin.declaration.confirm', [
            'channel' => $channel,
            'draw' => $draw,
            'ngo' => $ngo
        ]);
    }

    public function postDeclareNgo_channel_draw_ngo(Request $request, Channel $channel, Draw $draw, Ngo $ngo) {

        //validate transaction date also with draw_time...
        //validate that draw is already declared for current date

        $declaration = new Declaration();
        $declaration->draw_id = $draw->id;
        $declaration->ngo_id = $ngo->id;
        $declaration->status = 'processing';
        if($declaration->save()) {
            
            $draw->transactions()
                ->whereDate('created_at', '=', Carbon::parse($declaration->created_at)->toDateString())
                ->update(array('declaration_id'=>$declaration->id));

            //////////////////////////////////////
            /// WINNING LOGIC START //////////////
            //////////////////////////////////////

            // echo '<pre>';
            // foreach($declaration->transactions as $transaction) {
            //     foreach($transaction->donations as $donation) {
            //         list($status, $amount) = $donation->won();
            //         //echo ($status?'t':'f').", $amount == ".$donation->id."<br/>";
            //     }
            // }
            // echo '</pre>';

            //////////////////////////////////////
            /// WINNING LOGIC END ////////////////
            //////////////////////////////////////

            return redirect()->route('declare-ngo')->with(['success_message'=>"Declaration completed"]);

        } else {
            return redirect()->back()->with(['error_message'=>"There was an error, please try again."]);
        }

    }
}
