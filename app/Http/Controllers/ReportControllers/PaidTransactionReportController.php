<?php

namespace App\Http\Controllers\ReportControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\PaidTransaction;
use Illuminate\Http\Request;

class PaidTransactionReportController extends Controller
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

    	$results = PaidTransaction::select(
    		'paid_transactions.*',
    		'transactions.center_id',
    		'transactions.ref',
    		'centers.name as center_name',
    		'centers.id as center_id'
		);
		$results->join('transactions', 'transactions.id','=','paid_transactions.transaction_id');
		$results->join('centers', 'centers.id','=','paid_transactions.center_id');

		$daysBetween = $toDate->diffInDays($fromDate);
    	if($daysBetween >= 0) {
    		$results->whereBetween('paid_transactions.created_at', [$fromDate, $toDate]);
    	}
    	if($request->has('center')) {
    		$results->where('centers.name','like','%'.$request->center.'%');
    	}
    	$results->orderBy('transactions.center_id');
    	$results->orderBy('paid_transactions.created_at', 'desc');
    	$paid_transactions = $results->paginate(31);

    	return view('admin.reports.paid_transactions', [
    		'paid_transactions' => $paid_transactions,
    		'fromDate' => $fromDate,
    		'toDate' => $toDate,
    		'centerSearch'=> $request->center
		]);
    }
}
