<?php

namespace App\Http\Controllers;

use App\Center;
use App\Game;
use App\GeneralSetting;
use App\Http\Requests;
use App\LuckyRatio;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function getIndex() {
        return view('admin.centers.index', ['centers'=>Center::paginate(15)]);
    }

    public function getNewCenter() {
        return view('admin.centers.new', ['games'=>Game::all()]);
    }

    public function postNewCenter(Request $request) {

        $validation_array = array(
            'type'=>'required|in:donator,cashier',
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:5'
        );

        $validation_messages = array();

        $game = $request["game"];
        $ratios = array();

        if($request->type == 'donator') {
            $validation_array["commission_ratio"] = 'required|numeric';
            if(is_array($game)) {
                foreach($game as $game_id=>$ratio) {
                    $validation_array["game.$game_id"]="required|numeric";
                    $game_m = Game::find($game_id);
                    if(!$game_m) {
                        return redirect()->back()->with(['error_message'=>"Invalid game, try again"]);
                    }
                    $validation_messages["game.$game_id.required"] = "The ".$game_m->name." lucky ratio is required";
                    $validation_messages["game.$game_id.numeric"]  = "The ".$game_m->name." lucky ratio is invalid (it must be an number)";

                    $ratios[$game_id] = $ratio;
                }
            } else {
                return redirect()->back()->with(['error_message'=>"Invalid request, try again"]);
            }
        }

        $this->validate($request, $validation_array, $validation_messages);

        $type = $request->type;
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        $commission_ratio = $request->commission_ratio;

        $user = new User();
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->role = $type;

        if($user_id = $user->save()) {

            $center = new Center();
            $center->user_id = $user->id;
            $center->commission_ratio = $type == 'donator' ? $commission_ratio : GeneralSetting::settings()->cashier_commission_ratio;
            $center->name = $name;

            if($center_id = $center->save()) {

                if($type == 'donator') {
                    $saveRatios = array();

                    foreach($ratios as $game_id => $ratio) {
                        $saveRatios[] = new LuckyRatio(['game_id'=>$game_id, 'ratio'=>$ratio]);
                    }

                    if($center->ratios()->saveMany($saveRatios)) {
                        return redirect()->back()->with(['success_message'=>"Center created successfully"]);
                    } else {
                        $center->delete();
                        $user->delete();
                        return redirect()->back()->with(['error_message'=>"There was an error saving ratios, try again"]);
                    }
                } else {
                    return redirect()->back()->with(['success_message'=>"Center created successfully"]);
                }

            } else {
                $user->delete();
                return redirect()->back()->with(['error_message'=>"There was an error creating center, try again"]);
            }

        } else {
            return redirect()->with(['error_message'=>"There was an error while creating your account, try again"]);
        }

    }

    public function postDeleteCenter(Request $request, Center $center) {

        if($center->delete()) {
            return redirect()->back()->with(['success_message'=> "Deleted successfully"]);
        } else {
            return redirect()->back()->with(['error_message'=> "There was an error while deleting an center, try again"]);
        }

    }

    public function getEditCenter(Request $request, Center $center) {
        return view('admin.centers.edit', ["center"=>$center, 'games'=>Game::all()]);
    }

    public function postEditCenter(Request $request, Center $center) {

        $validation_array = array(
            'type'=>'required|in:donator,cashier',
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$center->user->id
        );

        $validation_messages = array();

        if(isset($request["password"]) && !empty($request["password"])) {
            $validation_array["password"]='required|min:5';
        }
        
        $game = $request["game"];
        $ratios = array();

        if($request->type == 'donator') {
            $validation_array["commission_ratio"] = 'required|numeric';
            if(is_array($game)) {
                foreach($game as $game_id=>$ratio) {
                    $validation_array["game.$game_id"]="required|numeric";
                    $game_m = Game::find($game_id);
                    if(!$game_m) {
                        return redirect()->back()->with(['error_message'=>"Invalid game, try again"]);
                    }
                    $validation_messages["game.$game_id.required"] = "The ".$game_m->name." lucky ratio is required";
                    $validation_messages["game.$game_id.numeric"]  = "The ".$game_m->name." lucky ratio is invalid (it must be an number)";

                    $ratios[$game_id] = $ratio;
                }
            } else {
                return redirect()->back()->with(['error_message'=>"Invalid request, try again"]);
            }
        }

        $this->validate($request, $validation_array, $validation_messages);

        $type = $request->type;
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        $commission_ratio = $request->commission_ratio;

        $center->user->email = $email;
        if($password) {
            $center->user->password = bcrypt($password);
        }
        $center->user->role = $type;

        if($center->user->update()) {

            $center->commission_ratio = $type == 'donator' ? $commission_ratio : GeneralSetting::settings()->cashier_commission_ratio;
            $center->name = $name;

            if($center->update()) {

                if($type == 'donator') {
                    $backedUpRatios = $center->ratios;
                    $center->ratios()->delete();
                    $saveRatios = [];

                    foreach($ratios as $game_id => $ratio) {
                        // foreach($saveRatios as $saved_ratio) {
                        //     if($saved_ratio->game_id == $game_id) {
                        //         $saved_ratio->ratio = $ratio;
                        //     }
                        // }
                        $saveRatios[] = new LuckyRatio(['game_id'=>$game_id, 'ratio'=>$ratio]);
                    }

                    if($center->ratios()->saveMany($saveRatios)) {
                        return redirect()->back()->with(['success_message'=>"Saved successfully"]);
                    } else {
                        return redirect()->back()->with(['error_message'=>"There was an error saving ratios, try again"]);
                    }
                } else {
                    return redirect()->back()->with(['success_message'=>"Saved successfully"]);
                }

            } else {
                return redirect()->back()->with(['error_message'=>"There was an error creating center, try again"]);
            }

        } else {
            return redirect()->with(['error_message'=>"There was an error while creating your account, try again"]);
        }

    }

}
