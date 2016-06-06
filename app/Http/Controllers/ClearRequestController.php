<?php

namespace App\Http\Controllers;

use App\ClearRequest;
use Illuminate\Http\Request;

use App\Http\Requests;

class ClearRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function getAcceptRequest(Request $request, ClearRequest $clearRequest) {

        $clearRequest->status = "accepted";
        if($clearRequest->update()) {
            return redirect()->back()->with(['success_message' => "Request accepted"]);
        } else {
            return redirect()->back()->with(['error_message' => "There was an error while accepting your request, try again"]);
        }

    }

    public function getRejectRequest(Request $request, ClearRequest $clearRequest) {

        $clearRequest->status = "rejected";
        if($clearRequest->update()) {
            return redirect()->back()->with(['success_message' => "Request rejected"]);
        } else {
            return redirect()->back()->with(['error_message' => "There was an error while rejecting your request, try again"]);
        }

    }
}
