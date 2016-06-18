@extends('layouts.master')

@section('title', "Declarations Report")

@push('head')
    <link rel="stylesheet" href="{{asset('css/bootstrap-datetimepicker.css')}}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Declarations Report</h1>
            <hr/>
        </div>
        <div class="col-md-12">
            @include('includes.validation_errors')
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form>
                <div class="form-group col-lg-3">
                    <div class="input-group">
                        <span class="input-group-addon">From Date :</span>
                        <input type="text" class="form-control" name="fromDate" value="{{$fromDate->format('d-m-Y')}}" />
                    </div>
                </div>
                <div class="form-group col-lg-3">
                    <div class="input-group">
                        <span class="input-group-addon">To Date :</span>
                        <input type="text" class="form-control" name="toDate" value="{{$toDate->format('d-m-Y')}}" />
                    </div>
                </div>
                <div class="form-group col-lg-3 col-lg-offset-3 text-right">
                    <button type="submit" class="btn btn-default">Filter</button>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th>Draw</th>
                            <th width="10%">NGO</th>
                            <th width="30%">Status</th>
                            <th width="20%">Auto/Manullay</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($declarations as $declaration)
                            <tr>
                                <td>#{{$declaration->id}}</td>
                                <td>{{$declaration->draw_name}} on <u>{{$declaration->created_at->format('d-m-Y')}} {{$declaration->draw_time}}</u></td>
                                <td>{{$declaration->ngo_name}}</td>
                                <td>{{$declaration->status}}</td>
                                <td>
                                    @if($declaration->status == 0)
                                        Manually
                                    @else
                                        Auto
                                    @endif
                                </td>
                                <td>{{$declaration->created_at->format('d-m-Y H:i')}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody>
                        <tr>
                            <td colspan="6">{{$declarations->appends(['fromDate'=>$fromDate->format("d-m-Y"),'toDate'=>$toDate->format("d-m-Y")])->render()}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script type="text/javascript" src="{{asset('js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
    $(document).ready(function() {
        $("input[name='fromDate'],input[name='toDate']").datetimepicker({format:"DD-MM-YYYY"});
    });
    </script>
@endpush