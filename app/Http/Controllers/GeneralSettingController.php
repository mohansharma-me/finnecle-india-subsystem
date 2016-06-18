<?php

namespace App\Http\Controllers;

use App\GeneralSetting;
use App\Http\Requests;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    
    public function __construct() {
    	$this->middleware('role:admin');
    }

    public function getIndex() {
    	return view('admin.settings', ['settings'=>GeneralSetting::settings()]);
    }

    public function postSaveSettings(Request $request) {
    	$rules = array('cashier_commission_ratio' => 'required');
    	$this->validate($request, $rules);

    	$settings = GeneralSetting::settings();
    	$settings->cashier_commission_ratio = $request->cashier_commission_ratio;

    	if($settings->update()) {
    		return redirect()->back()->with(['success_message' => "Settings saved"]);
    	} else {
    		return redirect()->back()->with(['error_message' => "Unable to save settings, try again."]);
    	}

    }

}
