@extends('layouts.master')

@section('title', "Settings")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Settings <small>{{Auth::user()->email}}</small></h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
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
        <div class="col-md-12">
            <div class="table-responsive">
                <form action="{{route('general-settings-post')}}" method=post>
                    <table class="table table-bordered table-striped table-hover">
                        <tbody>
                            <tr>
                                <td>Cashier Commission :</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-control" placeholder="Commission ratio..." type="text" name="cashier_commission_ratio" required title="Ratio amount should be numeric value" value="{{$settings->cashier_commission_ratio}}" />
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <button type="submit" class='btn btn-primary'>Save</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
@endsection