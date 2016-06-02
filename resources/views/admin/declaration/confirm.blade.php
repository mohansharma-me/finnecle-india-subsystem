@extends('layouts.master')

@section('title', "Confirm declaration - Declare NGO")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Declaration <small>{{Auth::user()->email}}</small> <small class="pull-right">{{\Carbon\Carbon::now()->format("d-m-Y H:i")}}</small> </h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-offset-3">
            <form action="{{route('post-declare-ngo', ['channel'=>$channel->id, 'draw'=>$draw->id, 'ngo'=>$ngo->id])}}" method="post">
                <legend>Confirmation</legend>

                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="20%">Channel : </th>
                        <td>{{$channel->name}} <small style="color: #a1a1a1">#{{$channel->id}}</small></td>
                    </tr>
                    <tr>
                        <th>Draw : </th>
                        <td>{{$draw->draw_time}} <small style="color: #a1a1a1">#{{$draw->id}} - {{$draw->name}}</small></td>
                    </tr>
                    <tr>
                        <th>NGO : </th>
                        <td>{{$ngo->ngo}} <small style="color: #a1a1a1">#{{$ngo->id}} - {{$ngo->ngo_group->game->name}} - {{$ngo->description}}</small></td>
                    </tr>
                    <tr>
                        <th>Donations : </th>
                        <td>Rs. {{$ngo->donation_amount($draw->id)}}/-</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            {{csrf_field()}}
                            <input type="submit" class="btn btn-success" value="Declare Now" />
                            <a href="{{route('declare-ngo-channel-draw', ['channel'=>$channel->id, 'draw'=>$draw->id])}}" class="btn btn-info pull-right">Back</a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
@endsection