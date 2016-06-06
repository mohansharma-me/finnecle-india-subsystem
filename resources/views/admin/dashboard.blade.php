@extends('layouts.master')

@section('title', "Dashboard")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard <small>{{Auth::user()->email}}</small></h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Center</th>
                            <th>Date</th>
                            <th>Amount & Slips</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clear_requests as $clear_request)
                            <tr>
                                <td>{{$clear_request->id}}</td>
                                <td>{{$clear_request->center->name}} <small>({{$clear_request->center->user->role}})</small></td>
                                <td>{{\Carbon\Carbon::parse($clear_request->created_at)->format('d-m-Y H:i')}}</td>
                                <td>Rs. {{$clear_request->amount}}/- ({{$clear_request->slips}} slips)</td>
                                <td>
                                    @if($clear_request->status == "pending")
                                        <a href="{{route('accept-clear-request', ['clearRequest'=>$clear_request->id])}}" class="btn btn-xs btn-success" onclick="return confirm('Are you sure to accept this clear request ? \nNote: You CANNOT undo this action once its done')">Accept</a>
                                        <a href="{{route('reject-clear-request', ['clearRequest'=>$clear_request->id])}}" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure to reject this clear request ? \nNote: You CANNOT undo this action once its done')">Reject</a>
                                    @else
                                        @if($clear_request->status == "accepted")
                                            <label class="label label-success">Accepted</label>
                                        @else
                                            <label class="label label-danger">Rejected</label>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">
                                {{$clear_requests->render()}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection