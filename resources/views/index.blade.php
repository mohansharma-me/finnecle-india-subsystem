@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="well">
            <form class="form-horizontal" method="post" action="{{route('login')}}">
                <fieldset>
                    <legend>Login to your account</legend>
                    @include('includes.validation_errors')
                    @if(session('error_message'))
                        <p class="alert alert-warning">
                            {{session('error_message')}}
                        </p>
                    @endif
                    <div class="form-group">
                        <label for="inputEmail" class="col-lg-2 control-label">Email</label>
                        <div class="col-lg-10">
                            <input class="form-control" name="email" id="inputEmail" placeholder="Email" type="text" value="{{old('email')}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class="col-lg-2 control-label">Password</label>
                        <div class="col-lg-10">
                            <input class="form-control" name="password" id="inputPassword" placeholder="Password" type="password" value="{{old('password')}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-10 col-lg-offset-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </fieldset>
                {{csrf_field()}}
            </form>
        </div>
    </div>
</div>
@endsection