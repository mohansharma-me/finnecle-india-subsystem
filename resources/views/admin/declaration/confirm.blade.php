@extends('layouts.master')

@section('title', "Declare NGO")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Declaration <small>{{Auth::user()->email}}</small> <small class="pull-right">{{\Carbon\Carbon::now()->format("d-m-Y H:i")}}</small> </h1>
            <hr/>
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
                            @foreach($channel->draws as $draw)
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
                <div>
                    <legend>NGOs</legend>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            @foreach($games as $game)
                                <thead>
                                    <tr>
                                        <th colspan="5" class="info">{{$game->name}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="active">
                                        <th width="10%">Group</th>
                                        <th width="10%">NGO</th>
                                        <th>Description</th>
                                        <th width="10%">Donation</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                    @foreach($game->ngo_groups as $ngo_group)
                                        @foreach($ngo_group->ngos as $ngo)
                                            <tr>
                                                <td>{{$ngo_group->name}}</td>
                                                <td>{{$ngo->ngo}}</td>
                                                <td>{{$ngo->description}}</td>
                                                <td align="center">Rs. 0/-</td>
                                                <td>
                                                    <a href="{{route('declare-ngo-channel-draw-ngo', ['channel'=>$sel_channel->id, 'draw'=>$sel_draw->id, 'ngo'=>$ngo->id])}}" class="btn btn-success btn-xs">Declare</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            @endforeach
                        </table>
                    </div>
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