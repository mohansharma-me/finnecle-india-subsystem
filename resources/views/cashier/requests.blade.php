@extends('layouts.master')

@section('title', "Clear Requests")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Clear Requests <small>{{Auth::user()->email}}</small></h1>
            <hr/>
        </div>
    </div>
    @if(session('success_message'))
        <div class="alert alert-success">
            {{session('success_message')}}
        </div>
    @endif
    @if(session('error_message'))
        <div class="alert alert-danger">
            {{session('error_message')}}
        </div>
    @endif
    <div class="row">
        <div class="col-md-9">
            <div class="well">
                <legend>Clear Requests</legend>
                <small>All your previously requested entries...</small>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Clear Amount</th>
                            <th>Slips</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($clear_requests as $clear_request)
                        <tr>
                            <td>{{$clear_request->id}}</td>
                            <td>{{\Carbon\Carbon::parse($clear_request->created_at)->format('d-m-Y H:i')}}</td>
                            <td>Rs. {{$clear_request->amount}}/-</td>
                            <td>{{$clear_request->slips}}</td>
                            <td>
                                <?php
                                    $labelClass = "label-default";
                                    switch($clear_request->status) {
                                        case "pending":
                                            $labelClass = "label-warning";
                                            break;
                                        case "accepted":
                                            $labelClass = "label-success";
                                            break;
                                        case "rejected":
                                            $labelClass = "label-danger";
                                            break;
                                    }
                                ?>
                                <label class="label {{$labelClass}}">
                                    {{ucwords($clear_request->status)}}
                                </label>
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
        <div class="col-md-3">
            <form method="post" class="well" action="{{route('post-cashier-clear-request')}}">
                <?php
                $clearAmount = Auth::user()->getUnclearAmount();
                ?>
                <legend>Uncleared Balance</legend>
                <fieldset>
                    @if(count($clearAmount[1]) > 0)
                        {{csrf_field()}}
                        @foreach($clearAmount[1] as $key=>$tid)
                            <input type="hidden" name="t[{{$key}}]" value="{{$tid}}" />
                        @endforeach
                        <div class="form-group">
                            <label for="inputSlips" class="control-label">Slips :</label>
                            <input type="text" id="inputSlips" readonly class="form-control" value="{{count($clearAmount[1])}}" />
                        </div>
                        <div class="form-group">
                            <label for="inputComms" class="control-label">Commission :</label>
                            <input type="text" id="inputComms" readonly class="form-control" value="{{$clearAmount[2]}}" />
                        </div>
                        <div class="form-group">
                            <label for="inputAmount" class="control-label">Amount : (Rs.)</label>
                            <input type="text" id="inputAmount" readonly class="form-control" value="{{$clearAmount[0]+$clearAmount[2]}} ( - {{$clearAmount[2]}}) = {{$clearAmount[0]}}" />
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Send Clear Request</button>
                    @else
                        <div class="text-center">
                            <div class="label label-primary">Zero slip(s)</div>
                        </div>
                    @endif
                </fieldset>
            </form>
        </div>
    </div>
@endsection