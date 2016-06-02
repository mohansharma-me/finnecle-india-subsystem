@extends('layouts.master')

@section('title', "Dashboard")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard <small>{{Auth::user()->email}}</small></h1>
            <hr/>
        </div>
    </div>
@endsection