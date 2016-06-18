@extends('layouts.master')

@section('title', $current_date . " - Donations")

@push('head')
    <link rel="stylesheet" href="{{asset('css/bootstrap-datetimepicker.css')}}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                <label class="pull-left">Donations <small>{{Auth::user()->email}}</small></label>
                <form class="pull-right">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Date :</span>
                            <input name="date" type="text" class="form-control" id="donation_datetime" value="{{$current_date}}" />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-default">Go</button>
                            </span>
                        </div>
                    </div>
                </form>
            </h1>
            <hr/>
        </div>
        <div class="col-md-12">
            @include('includes.validation_errors')
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <form>
                    <input type="hidden" name="page" value="1" />
                    <input type="hidden" name="date" value="{{$current_date}}" />
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Transaction #:</span>
                            <input name="transaction_id" type="text" class="form-control" value="{{$transaction_id}}" required pattern="[0-9]+" title="Transaction number should be unique numeric value" />
                            <span class="input-group-btn"><button type="submit" class="btn btn-default">Filter</button><a href="?page={{Request::has('page') ? Request::get('page') : 1}}&date={{$current_date}}" class="btn btn-default">RESET</a></span>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th>Draw</th>
                            <th width="10%">Donation</th>
                            <th width="30%">NGOs</th>
                            <th width="20%">Note</th>
                            <th width="20%">Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{$transaction->ref}}</td>
                                <td>{{$transaction->draw->draw_time}} <small style="color: #9d9d9d">({{$transaction->draw->name}})</small></td>
                                <td>Rs. {{$transaction->amount()}}/-</td>
                                <td>{{$transaction->ngo_print_string()}}</td>
                                <td><label class="label label-default">{{\Carbon\Carbon::parse($transaction->created_at)->format("d-m-Y H:i:s")}}</label><br/>{{$transaction->note}}</td>
                                <td>{{$transaction->created_at->format('d-m-Y H:i')}}</td>
                                <td>
                                    @if($transaction->declaration_id == 0)
                                        <label class="label label-default">Not Declared</label>
                                    @else
                                        <label class="label label-primary">{{$transaction->declaration->ngo->description}} declared</label>
                                        <?php
                                        $total_won = 0;
                                        ?>
                                        @foreach($transaction->donations as $donation)
                                            @if($won_arr = $donation->won())
                                                @if($won_arr[0])
                                                    <?php
                                                    $total_won += intval($won_arr[1]);
                                                    ?>
                                                @endif
                                            @endif
                                        @endforeach
                                        <label class="label label-{{$total_won > 0 ? "success" : "danger"}}"><b>Rs. {{$total_won}}/-</b></label>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">
                                @if(\Illuminate\Support\Facades\Request::has('transaction_id'))
                                    {{$transactions->appends(['date'=>$current_date, 'transaction_id'=>\Illuminate\Support\Facades\Request::get('transaction_id')])->render()}}
                                @else
                                    {{$transactions->appends(['date'=>$current_date])->render()}}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script type="text/javascript" src="{{asset('js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
    $(document).ready(function() {
        $("#donation_datetime").datetimepicker({format:"DD-MM-YYYY"});
    });
    </script>
@endpush