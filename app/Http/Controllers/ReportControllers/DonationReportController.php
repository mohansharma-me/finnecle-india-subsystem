<?php

namespace App\Http\Controllers\ReportControllers;

use App\Donation;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Transaction;
use Illuminate\Http\Request;

class DonationReportController extends Controller
{
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

    	$results = Transaction::select(
    		'transactions.*',
    		'draws.draw_time',
    		'draws.name as draw_name',
    		'centers.id as center_id',
    		'centers.name as center_name'
		);
		$results->join('draws','draws.id','=','transactions.draw_id');		
    	$results->join('centers','centers.id','=','transactions.center_id');
		$daysBetween = $toDate->diffInDays($fromDate);
    	if($daysBetween >= 0) {
    		$results->whereBetween('transactions.created_at', [$fromDate, $toDate]);
    	}
    	if($request->has('center')) {
    		$results->where('centers.name','like','%'.$request->center.'%');
    	}
    	
    	$results->orderBy('transactions.created_at', 'desc');
    	$transactions = $results->paginate(31);

    	return view('admin.reports.donations', [
    		'transactions' => $transactions,
    		'fromDate' => $fromDate,
    		'toDate' => $toDate,
    		'centerSearch'=> $request->center
		]);
    }
}
