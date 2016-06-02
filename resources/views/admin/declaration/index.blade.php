@extends('layouts.master')

@section('title', "Declare NGO")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Declaration <small>{{Auth::user()->email}}</small></h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="well">
                <legend>Declare Draw <small class="pull-right">{{\Carbon\Carbon::now()->format("d-m-Y H:i")}}</small></legend>
                <fieldset>

                </fieldset>
            </div>
        </div>
    </div>
@endsection