<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Draw;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class DrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function getIndex() {
        $draws=Draw::paginate(15);
        return view('admin.draws.index', ["draws"=>$draws]);
    }

    public function postDeleteDraw(Request $request, Draw $draw) {
        if($draw->delete()) {
            return redirect()->back()->with(["success_message"=> "Deleted successfully"]);
        } else {
            return redirect()->back()->with(["error_message"=> "Can't delete"]);
        }
    }

    public function getEditDraw(Request $request, Draw $draw) {
        return view('admin.draws.edit', ["draw"=>$draw, "channels"=>Channel::all()]);
    }

    public function postEditDraw(Request $request, Draw $draw) {

        $this->validate($request, [
            'channel' => 'required',
            'name' => 'required',
            'hh' => 'required',
            'mm' => 'required',
            'automatic_ratio' => 'required'
        ]);

        $channel_id = $request->channel;
        $name = $request->name;
        $hh = $request->hh;
        $mm = $request->mm;
        $automatic_ratio = $request->automatic_ratio;

        $channel = Channel::find($channel_id);

        if($channel) {
            $hours=array();
            for($h=0;$h<24;$h++) $hours[]= $h < 10 ? "0$h" : $h ;

            $minutes=array();
            for($m=0;$m<60;$m++) $minutes[]= $m < 10 ? "0$m" : $m ;

            if(in_array($hh, $hours)) {
                if(in_array($mm, $minutes)) {
                    if(is_numeric($automatic_ratio)) {

                        $draw_check = $channel->draws()->where('draw_time', "$hh:$mm")->where('id', "!=", $draw->id)->get();

                        if(count($draw_check)>0) {
                            return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Time($hh:$mm) is already added in selected channel ($channel->name)"]);
                        } else {
                            //$draw=new Draw();
                            $draw->name = $name;
                            $draw->draw_time = "$hh:$mm";
                            $draw->automatic_ratio = $automatic_ratio;
                            $draw->channel_id = $channel->id;
                            $saved = $draw->update();
                            if($saved) {
                                return redirect()->back()->withInput(Input::all())->with(['success_message'=> "Saved successfully"]);
                            } else {
                                return redirect()->back()->withInput(Input::all())->with(['error_message'=> "There was an error, try again"]);
                            }
                        }

                    } else {
                        return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Automatic Ratio is not valid"]);
                    }
                } else {
                    return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Time (minute) is not valid"]);
                }
            } else {
                return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Time (hour) is not valid"]);
            }
        } else {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Channel not found"]);
        }

    }

    public function getNewDraw() {
        $channels=Channel::all();
        return view('admin.draws.new', ['channels'=>$channels]);
    }

    public function postNewDraw(Request $request) {

        $this->validate($request, [
            'channel' => 'required',
            'name' => 'required',
            'hh' => 'required',
            'mm' => 'required',
            'automatic_ratio' => 'required'
        ]);

        $channel_id = $request->channel;
        $name = $request->name;
        $hh = $request->hh;
        $mm = $request->mm;
        $automatic_ratio = $request->automatic_ratio;

        $channel = Channel::find($channel_id);

        if($channel) {
            $hours=array();
            for($h=0;$h<24;$h++) $hours[]= $h < 10 ? "0$h" : $h ;

            $minutes=array();
            for($m=0;$m<60;$m++) $minutes[]= $m < 10 ? "0$m" : $m ;

            if(in_array($hh, $hours)) {
                if(in_array($mm, $minutes)) {
                    if(is_numeric($automatic_ratio)) {

                        $draw_check = $channel->draws()->where('draw_time', "$hh:$mm")->get();

                        if(count($draw_check)>0) {
                            return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Time($hh:$mm) is already added in selected channel ($channel->name)"]);
                        } else {
                            $draw=new Draw();
                            $draw->name = $name;
                            $draw->draw_time = "$hh:$mm";
                            $draw->automatic_ratio = $automatic_ratio;

                            $saved = $channel->draws()->save($draw);
                            if($saved) {
                                return redirect()->back()->withInput(Input::all())->with(['success_message'=> "Added successfully"]);
                            } else {
                                return redirect()->back()->withInput(Input::all())->with(['error_message'=> "There was an error, try again"]);
                            }
                        }

                    } else {
                        return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Automatic Ratio is not valid"]);
                    }
                } else {
                    return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Time (minute) is not valid"]);
                }
            } else {
                return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Time (hour) is not valid"]);
            }
        } else {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=> "Channel not found"]);
        }

    }
}
