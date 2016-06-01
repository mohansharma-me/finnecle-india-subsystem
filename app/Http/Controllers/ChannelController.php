<?php

namespace App\Http\Controllers;

use App\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ChannelController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function getIndex(Request $request) {

        $channels = Channel::paginate(10);

        return view('admin.channels.index', [
            'channels'=> $channels
        ]);
    }

    public function postNewChannel(Request $request) {

        $this->validate($request, [
            'name' => 'required|min:3|unique:channels'
        ],[
            'name.required'=>'Channel name is required.',
            'name.min'=>'Channel name must be 3 characters long.',
            'name.unique'=>'Channel name is already exists.'
        ]);

        $channel = new Channel();
        $channel->name = $request->name;
        $saved = $channel->save();
        $message = $saved ? "Channel created." : "Can't create channel";
        return redirect()->route('channels')->with(['create_success'=>$saved, 'create_message'=> $message]);
    }

    public function getEditChannel(Request $request, Channel $channel) {
        return view('admin.channels.edit', [ 'channel'=>$channel ]);
    }

    public function postEditChannel(Request $request, Channel $channel) {

        $this->validate($request, [
            'name' => 'required|min:3|unique:channels,name,'.$channel->id
        ],[
            'name.required'=>'Channel name is required.',
            'name.min'=>'Channel name must be 3 characters long.',
            'name.unique'=>'Channel name is already exists.'
        ]);

        $channel->name = $request->name;
        if($channel->update()) {
            return redirect()->back()->with(['success_message' => "Saved successfully."]);
        } else {
            return redirect()->back()->with(['error_message' => "Can't save."]);
        }

    }

    public function postDeleteChannel(Request $request, Channel $channel) {

        if($channel->delete()) {
            return redirect()->route('channels')->with(['success_message' => "Deleted successfully."]);
        } else {
            return redirect()->route('channels')->with(['error_message' => "Can't delete."]);
        }

    }

}
