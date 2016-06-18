@extends('layouts.master')

@section('title', "Paid Transactions")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Paid Transactions <small>{{Auth::user()->email}}</small></h1>
            <hr/>
        </div>
    </div>
    @if(session('success_message'))
        <div class="alert alert-success">
            {{session('success_message')}}
        </div>
    @endif
    @if(session('error_message'))
        <div class="alert alert-danger">
            {{session('error_message')}}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Slip Ref</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($paid_donations as $paid_transaction)
                        <tr>
                            <td>#{{$paid_transaction->id}}</td>
                            <td>{{$paid_transaction->created_at->format('d-m-Y H:i')}}</td>
                            <td>{{$paid_transaction->transaction->lucky_amount()}}</td>
                            <td>{{$paid_transaction->transaction->ref}}</td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">
                                {{$paid_donations->render()}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection