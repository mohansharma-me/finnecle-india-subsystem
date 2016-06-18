@extends('layouts.master')

@section('title', "Declare NGO")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Declaration <small>{{Auth::user()->email}}</small> <small class="pull-right">{{\Carbon\Carbon::now()->format("d-m-Y H:i")}}</small> </h1>
            <hr/>
        </div>
        <div class="col-md-12">
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="well">
                <legend>Channel</legend>
                <fieldset>
                    <div class="form-group">
                        <select class="form-control" id="channel">
                            <option style="display:none" disabled selected>Select Channel</option>
                            @foreach($channels as $channel)
                                @if(isset($sel_channel) && $sel_channel->id == $channel->id)
                                    <option value="{{$channel->id}}" selected>{{$channel->name}}</option>
                                @else
                                    <option value="{{$channel->id}}">{{$channel->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </fieldset>
            </div>
        </div>
        @if(isset($sel_channel))
            <div class="col-md-8">
                <div class="well">
                    <legend>Channel Detail</legend>
                    <blockquote>
                        <b>Name : </b> {{$sel_channel->name}}
                    </blockquote>

                    <legend>Last 10 Declarations</legend>
                    <ul class="list-inline list-unstyled">
                        @foreach($sel_channel->last10Declarations() as $declaration)    
                            <li><label class="label label-primary">NGO{{$declaration->ngo_name}}</label></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
    @if(isset($sel_channel))
    <div class="row">
        <div class="col-md-4">
            <div class="well">
                <legend>Draw</legend>
                <fieldset>
                    <div class="form-group">
                        <select class="form-control" id="draw">
                            <option style="display:none" disabled selected>Select Draw</option>
                            @foreach($sel_channel->remainingDraws() as $draw)
                                @if(isset($sel_draw) && $sel_draw->id == $draw->id)
                                    <option value="{{$draw->id}}" selected>{{$draw->draw_time}} - {{$draw->name}}</option>
                                @else
                                    <option value="{{$draw->id}}">{{$draw->draw_time}} - {{$draw->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </fieldset>
            </div>
        </div>
        @if(isset($sel_draw))
            <div class="col-md-8">
                <div class="well">
                    <legend>Draw Detail</legend>
                    <fieldset>
                        <blockquote>
                            <b>Draw Name : </b> {{$sel_draw->name}}<br/>
                            <b>Draw Time : </b> {{$sel_draw->draw_time}}<br/>
                            <b>Auto Ratio : </b> {{$sel_draw->automatic_ratio}}
                        </blockquote>
                    </fieldset>
                </div>
            </div>
        @endif
    </div>
    @endif

    @if(isset($sel_channel, $sel_draw))
        <div class="row">
            <div class="col-md-12">
                <div class="well">
                    <legend>Total Donation : <label class='pull-right'>Rs. {{$totalDonationAmount}}/-</label></legend>
                    <legend>Total Donotor Comm : <label class='pull-right'>Rs. {{$totalDonatorComm}}/-</label></legend>
                    <legend>Total Cashier Comm : <label class='pull-right'>Rs. {{$totalCashierComm}}/-</label></legend>
                    <legend>Total Amount : <label class='pull-right'>Rs. {{$totalDonationAmount - $totalDonatorComm - $totalCashierComm}}/-</label></legend>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <?php $firstFlag = true; ?>
                    @foreach($games as $game)
                        @if($firstFlag)
                            <li class="active"><a href="#tab{{$game->id}}" data-toggle="tab">{{$game->name}}</a></li>
                        @else
                            <li><a href="#tab{{$game->id}}"  data-toggle="tab">{{$game->name}}</a></li>
                        @endif
                        <?php $firstFlag = false; ?>
                    @endforeach
                </ul>
                <div id="myTabContent" class="tab-content">
                    <?php $firstFlag = true; ?>
                    @foreach($games as $game)
                        <div class="tab-pane fade {{ $firstFlag ? "active in" : "" }}" id="tab{{$game->id}}" style="padding: 10px 15px">
                            <table class="table table-bordered">
                                <?php
                                $current = 1;
                                ?>
                                @foreach($game->ngo_groups as $ngo_group)
                                    @foreach($ngo_group->ngos as $ngo)
                                        @if($current == 1) <tr> @endif
                                            <td>
                                            <div class="text-center">
                                                <b>NGO {{$ngo->ngo}}</b><br/>   
                                                <u>Rs. {{$ngo->return_amount($sel_draw->id)}}/-</u>
                                                <!--<p>Return : Rs. {{$ngo->return_amount($sel_draw->id)}}/-</p>
                                                <p>Commission : Rs. {{$ngo->return_commission_amount($sel_draw->id)}}/-</p>-->
                                                @if($game->id > 2)
                                                <p><a href="{{route('declare-ngo-channel-draw-ngo', ['channel'=>$sel_channel->id, 'draw'=>$sel_draw->id, 'ngo'=>$ngo->id])}}" class="btn btn-xs btn-success">Declare</a></p>
                                                @endif
                                            </div>
                                            </td>
                                        @if(++$current == 16) <?php $current = 1; ?> </tr> @endif
                                    @endforeach
                                @endforeach
                            </table>
                        </div>
                        <?php $firstFlag = false; ?>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endsection

@push('footer')
<script>
    $(document).ready(function() {
        var url = "{{route('declare-ngo')}}";
        $("#channel").change(function() {
            document.location = url + "/" + $(this).val();
        });
        @if(isset($sel_channel))
        $("#draw").change(function() {
            document.location = url + "/" + "{{$sel_channel->id}}/" + $(this).val();
        });
        @endif
    });
</script>
@endpush