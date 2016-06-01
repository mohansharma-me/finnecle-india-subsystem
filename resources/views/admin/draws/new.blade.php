@extends('layouts.master')

@section('title', 'Draws')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                <label class="pull-left">Draws</label>
                <a href="{{route('new-draw')}}" class="btn btn-success pull-right">New Draw</a>
            </h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
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
        <div class="col-md-12 col-sm-12">
            <div class="well">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="border-bottom: 1px solid darkgray;">#</th>
                            <th style="border-bottom: 1px solid darkgray;">Draw</th>
                            <th style="border-bottom: 1px solid darkgray;">Channel</th>
                            <th style="border-bottom: 1px solid darkgray;">Time</th>
                            <th style="border-bottom: 1px solid darkgray;">Automatic Ratio</th>
                            <th style="border-bottom: 1px solid darkgray;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
                <center>{!!$draws->render()!!}</center>
            </div>
        </div>
    </div>
@endsection