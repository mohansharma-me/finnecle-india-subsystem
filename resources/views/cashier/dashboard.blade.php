@extends('layouts.master')

@section('title', "Dashboard")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Pay Luckies <small>{{Auth::user()->email}}</small></h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="well">
                <form method="post" action="javascript:void">
                    <legend>Check Donations</legend>
                    <fieldset>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Transaction #</span>
                                <input type="text" class="form-control" placeholder="Transaction ID" id="transaction-id" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn-status btn btn-default hide">Searching</button>
                                </span>
                            </div>
                        </div>
                    </fieldset>
                </form>
                @if(isset($transaction))
                    <div id="transaction-status">
                    <br/>
                    <legend>Transaction Status  <small class="pull-right">{{$transaction->paid ? "Paid ".$transaction->lucky_amount() : "Not Paid (Rs. ".$transaction->lucky_amount()."/-)"}}</small></legend>
                    <fieldset>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th width="20%">Draw : </th>
                                <td> {{$transaction->draw->name}} @ {{$transaction->draw->draw_time}} </td>
                            </tr>
                            <tr>
                                <th width="20%">Donator Center: </th>
                                <td> {{$transaction->center->name}} ({{$transaction->center->user->email}})</td>
                            </tr>
                            <tr>
                                <th width="20%">Note: </th>
                                <td> {{$transaction->note}}</td>
                            </tr>
                            <tr>
                                <th width="20%">Lucky Amount: </th>
                                <td>Rs. {{$transaction->lucky_amount()}}/-</td>
                            </tr>
                            @if(!$transaction->paid && $transaction->lucky_amount() > 0)
                                <tr>
                                    <th colspan="2" class="text-center">
                                        <button class="pay-now btn btn-lg btn-success" type="button">Pay Now</button>
                                    </th>
                                </tr>
                            @endif
                        </table>
                    </fieldset>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('footer')
<script>

    //user is "finished typing," do something
    function doneTyping () {
        var value = $("#transaction-id").val();
        //document.location = "{{route('check-donation', ['transaction' => ''])}}/"+value;

        try {
            $(".btn-status").html('Please wait');
            $(".btn-status").removeClass('hide');

            $.ajax({
                url : "{{route('ajax-check-donation')}}",
                data : {
                    donation : value,
                    _token : "{{csrf_token()}}"
                },
                type: "POST",
                success: function(json) {
                    if(json.success) {
                        $(".btn-status").html('Found, wait...');
                        document.location = json.url;
                    } else {
                        $(".btn-status").html('Not found');
                    }
                },
                error: function(err) {
                    $(".btn-status").html('Try again');
                    console.log(err);
                },
                complete : function () {
                    $("#transaction-id").val('');
                }
            });
        } catch(e) {
            alert("Error while processing your request, please try again.");
        }

    }

    $(document).ready(function () {
        $("#transaction-id").focus();

        var typingTimer;                //timer identifier
        var doneTypingInterval = 1000;  //time in ms, 5 second for example
        var $input = $('#transaction-id');

        //on keyup, start the countdown
        $input.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        //on keydown, clear the countdown
        $input.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        @if(isset($transaction) && !$transaction->paid && $transaction->lucky_amount() > 0)
        $(".pay-now").click(function() {
            $(this).attr("disabled", "disabled");
            $(this).html("Please wait...");
            $.ajax({
                url : "{{route('paid-donation')}}",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    donation: {{$transaction->id}}
                },
                success: function(json) {
                    console.log(json);
                    try {
                        if(json.success) {
                            $("#transaction-status").addClass("hide");
                        } else {
                            $(".pay-now").removeAttr('disabled');
                            $(".pay-now").html("Try again");
                        }
                    } catch(e) {
                        $(".pay-now").removeAttr('disabled');
                        $(".pay-now").html("Error while setting transaction, try again");
                    }
                },
                error: function (err) {
                    $(".pay-now").removeAttr('disabled');
                    $(".pay-now").html("Please try again");
                }
            });
        });
        @endif

    });
</script>
@endpush