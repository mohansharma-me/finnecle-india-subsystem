@extends('layouts.master')

@section('title', 'Centers')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                <label class="pull-left">Centers</label>
                <a href="{{route('new-center')}}" class="btn btn-success pull-right">New Center</a>
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
                            <th style="border-bottom: 1px solid darkgray;">Name</th>
                            <th style="border-bottom: 1px solid darkgray;">E-mail</th>
                            <th style="border-bottom: 1px solid darkgray;">Commission</th>
                            <th style="border-bottom: 1px solid darkgray;">Lucky Ratio</th>
                            <th style="border-bottom: 1px solid darkgray;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($centers as $center)
                        <tr>
                            <td>{{$center->id}}</td>
                            <td>{{$center->name}} <i>({{$center->user->role}})</i></td>
                            <td>{{$center->user->email}}</td>
                            <td>{{$center->commission_ratio}}</td>
                            <td>
                                @foreach($center->ratios as $ratio)
                                    <li>{{$ratio->game->name}} ({{$ratio->ratio}})</li>
                                @endforeach
                            </td>
                            <td>
                                <form method="post" action="{{route('delete-center-post', ['center'=>$center->id])}}">
                                    <a href="{{route('edit-center', ['center'=>$center->id])}}" class="btn btn-xs btn-success">Edit</a>
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure to delete all related to this center ?')">Delete</button>
                                    {{csrf_field()}}
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <center>{!!$centers->render()!!}</center>
            </div>
        </div>
    </div>
@endsection