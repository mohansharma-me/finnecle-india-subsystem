<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Declaration;
use App\Draw;
use App\Game;
use App\Ngo;
use Illuminate\Http\Request;

use App\Http\Requests;

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

        return view('admin.declaration.index', [
            'channels' => Channel::all(),
            'sel_channel' => $channel,
            'sel_draw' =>  $draw,
            'games' => $games
        ]);
    }

    public function getDeclareNgo_channel_draw_ngo(Request $request, Channel $channel, Draw $draw, Ngo $ngo) {
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
            
            $draw->transactions()->update(array('declaration_id'=>$declaration->id));
            // winning logic goes here...
            return redirect()->route('declare-ngo')->with(['success_message'=>"Declaration completed"]);

        } else {
            return redirect()->back()->with(['error_message'=>"There was an error, please try again."]);
        }

    }
}
