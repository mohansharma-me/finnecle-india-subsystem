<?php

namespace App\Http\Controllers\ReportControllers;

use App\Declaration;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeclarationReportController extends Controller
{
    public function __construct() {
    	$this->middleware('role:admin');
    }

    public function getIndex(Request $request) {

    	$fromDate = \Carbon\Carbon::now();
    	$toDate = \Carbon\Carbon::now()->addDays(1);

    	if($request->has('fromDate')) {
    		$from = null;
    		try {
    			$from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->fromDate." 00:00:00");
    		} catch(\Exception $e) {}
    		$fromDate = $from ? $from : $fromDate;
    	}

    	if($request->has('toDate')) {
    		$to = null;
    		try {
    			$to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->toDate." 23:59:59");
    		} catch(\Exception $e) {}
    		$toDate = $to ? $to : $toDate;
    	}

    	$results = Declaration::select(
    		'declarations.*',
    		DB::raw("draws.name as draw_name, draws.draw_time as draw_time"),
    		DB::raw("channels.name as channel_name, channels.id as channel_id"),
    		DB::raw("ngos.ngo as ngo_name")
		);
		$results->join('draws', 'draws.id','=','declarations.draw_id');
		$results->join('channels','channels.id','=','draws.channel_id');
		$results->join('ngos','ngos.id','=','declarations.ngo_id');

		$daysBetween = $toDate->diffInDays($fromDate);
    	if($daysBetween >= 0) {
    		$results->whereBetween('declarations.created_at', [$fromDate, $toDate]);
    	}
    	$results->orderBy('channel_id');
    	$results->orderBy('declarations.created_at', 'desc');
    	$declarations = $results->paginate(31);

    	return view('admin.reports.declarations', [
    		'declarations' => $declarations,
    		'fromDate' => $fromDate,
    		'toDate' => $toDate
		]);
    }
}
