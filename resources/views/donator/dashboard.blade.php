@extends('layouts.master')

@section('title', "Donations")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Donations <small>{{Auth::user()->email}}</small></h1>
            <hr/>
        </div>
    </div>
@endsection