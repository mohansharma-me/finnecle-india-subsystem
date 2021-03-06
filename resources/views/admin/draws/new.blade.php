@extends('layouts.master')

@section('title', 'New Draw')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                <label class="pull-left">Draws <small>New Draw</small></label>
                <a href="{{route('draws')}}" class="btn btn-info pull-right">Back</a>
            </h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-12">
            @if(session('error_message'))
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{session('error_message')}}
                </div>
            @endif

            @if(session('success_message'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{session('success_message')}}
                </div>
            @endif
        </div>
        <div class="col-md-6 col-md-offset-3 col-sm-12">
            <div class="well">
                <form class="form-horizontal" method="post" action="{{route('new-draw-post')}}">
                    <fieldset>
                        <legend>New Draw</legend>
                        <div class="form-group">
                            <label for="inputChannel" class="control-label col-lg-3">Channel :</label>
                            <div class="col-lg-9">
                                <select class="form-control" id="inputChannel" name="channel" required>
                                    @foreach($channels as $channel)
                                        <option value="{{$channel->id}}">{{$channel->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="control-label col-lg-3">Draw :</label>
                            <div class="col-lg-9">
                                <input type="text" name="name" placeholder="Draw name..." class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTime" class="control-label col-lg-3">Time :</label>
                            <div class="col-lg-4">
                                <label>Hour: (HH)</label>
                                <select name="hh" class="form-control">
                                    @for($h=0;$h<24;$h++)
                                        {{ $h = $h < 10 ? "0".$h : $h  }}
                                        <option value="{{$h}}">{{$h}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Minute: (MM)</label>
                                <select name="mm" class="form-control">
                                    @for($m=0;$m<60;$m++)
                                        {{ $m = $m < 10 ? "0".$m : $m  }}
                                        <option value="{{$m}}">{{$m}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTime" class="control-label col-lg-3">Winning Ratio:</label>
                            <div class="col-lg-9">
                                <input type="text" name="automatic_ratio" placeholder="Automatic Ratio" class="form-control" pattern="[0-9]+" title="Please enter valid automatic winning ratio (eg. 10)" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-9">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </fieldset>
                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
@endsection