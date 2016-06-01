@extends('layouts.master')

@section('title', 'New Center')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                <label class="pull-left">Centers <small>New Center</small></label>
                <a href="{{route('centers')}}" class="btn btn-info pull-right">Back</a>
            </h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-12">

            @include('includes.validation_errors')

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
                <form class="form-horizontal" method="post" action="{{route('new-center-post')}}">
                    <fieldset>
                        <legend>New Center</legend>
                        <div class="form-group">
                            <label for="inputType" class="control-label col-lg-3">Type :</label>
                            <div class="col-lg-9">
                                <select id="inputType" name="type" class="form-control">
                                    <option value="donator">Donator</option>
                                    <option value="cashier">Cashier</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="control-label col-lg-3">Center name:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Center name..." id="inputName" name="name" value="{{old('name')}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="control-label col-lg-3">E-mail address:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="E-mail address" id="inputEmail" name="email" value="{{old('email')}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="control-label col-lg-3">Password:</label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" placeholder="Password..." id="inputPassword" name="password" value="{{old('password')}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputRatio" class="control-label col-lg-3">Commission Ratio:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Commission ratio in digits..." id="inputRatio" name="commission_ratio" value="{{old('commission_ratio')}}" />
                            </div>
                        </div>
                    </fieldset>

                    <legend>Lucky Ratios</legend>
                    <fieldset>
                        @foreach($games as $game)
                            <div class="form-group">
                                <label for="inputPassword" class="control-label col-lg-3">{{$game->name}}: </label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="{{$game->name}} lucky ratio" id="input{{$game->id}}" name="game[{{$game->id}}]"  value="{{old('game')[$game->id]}}" />
                                </div>
                            </div>
                        @endforeach
                    </fieldset>

                    <div class="form-group">
                        <div class="col-lg-3"></div>
                        <div class="col-lg-9">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>

                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
@endsection