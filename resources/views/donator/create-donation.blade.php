@extends('layouts.master')

@section('title', "Create Donation Slip")

@if($selected_channel)

    @section('content')
        <div class="row">
            <div class="col-md-12">
                <h1 class="clearfix">
                    <div class="pull-left">
                        Create Donation Slip
                    </div>
                    <a href="{{route('dashboard')}}" class="btn btn-info pull-right">Back</a>
                    <form class="pull-right" style="margin-right: 10px" id="frmChannel">
                        <select id="channel" class="form-control">
                            <option style="display: none" disabled selected>Select Channel</option>
                            @foreach($channels as $channel)
                                @if($selected_channel->id == $channel->id)
                                    <option value="{{$channel->id}}" selected>{{$channel->name}}</option>
                                @else
                                    <option value="{{$channel->id}}">{{$channel->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </form>
                </h1>
                <hr/>
            </div>

            <div class="col-md-12">
                @if(session('error_message'))
                    <div class="alert alert-danger">
                        {{session('error_message')}}
                    </div>
                @endif
                @if(session('success_message') && session('transaction'))
                    <div class="row">
                        <div class="alert alert-info col-md-6 col-md-offset-3">
                            <h2 style="color: white">Donation Slip Created</h2>
                            <small>{{session('success_message')}}</small>
                            <div>
                                <p>Transaction ID: <label id="transaction-id">#{{session("transaction")->ref}}</label></p>
                                <p>Amount: Rs. <label id="transaction-amount">{{session('transaction')->amount()}}</label>/-</p>

                                <div style="display: none">
                                    <label id="draw-detail">{{ session("transaction")->draw->name ." @". session("transaction")->draw->draw_time}}</label>
                                    <label id="note-detail">{{ session("transaction")->note}}</label>
                                    <label id="ngo-detail">{{session('transaction')->ngo_print_string()}}</label>
                                </div>
                                <img name="barcode-img" id="barcode-img" src="data:image/png;base64,{{session('barcode')}}" /><br/><br/>
                                <a name="print-donation-slip" href="#print-donation-slip" id="print-donation-slip" class="print-donation-slip btn btn-default">Print</a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="well">
                    <form method="post" class="form-horizontal" action="{{route('post-create-donation')}}">
                        <legend>Slip Details <small style="float:right; color: #7b8a8b">{{$selected_channel->name}}</small></legend>
                        <fieldset class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inputDraw" class="control-label col-lg-3">Draw :</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="draw" id="inputDraw">
                                            @foreach($draws as $draw)
                                                <option value="{{$draw->id}}">{{$draw->name}} @ {{ $draw->draw_time }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inputDraw" class="control-label col-lg-3">Note :</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control" name="note" placeholder="Note about transaction like mobile number, name etc...">{{old('note')}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <legend>NGO(s)</legend>
                        <fieldset class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs">
                                    <?php $firstFlag = true; ?>
                                    @foreach($games as $game)
                                        @if($firstFlag)
                                            <li class="active"><a href="#tab{{$game->id}}" data-toggle="tab">{{$game->name}}</a></li>
                                        @else
                                            <li><a href="#tab{{$game->id}}"  data-toggle="tab">{{$game->name}}</a></li>
                                        @endif
                                        <?php $firstFlag = false; ?>
                                    @endforeach
                                </ul>
                                <div id="myTabContent" class="tab-content">
                                    <?php $firstFlag = true; ?>
                                    @foreach($games as $game)
                                        <div class="tab-pane fade {{ $firstFlag ? "active in" : "" }}" id="tab{{$game->id}}">
                                            <div class="well">
                                                @if($game->id == 3 || $game->id == 4)
                                                    <div class="row">
                                                        <div class="col-md-1">
                                                            <label>&nbsp;</label>
                                                        </div>
                                                        @foreach($game->ngo_groups->all() as $ngo_group)
                                                            <div class="col-md-1 highlight global">
                                                                <div class="form-group">
                                                                    <label for="inputGlobal{{$ngo_group->id}}">{{$ngo_group->name}}</label>
                                                                    <input data-group="{{$ngo_group->id}}" type="text" required id="inputGlobal{{$ngo_group->id}}" placeholder="Group {{$ngo_group->name}}" value="0" class="form-control global-input" />
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div class="col-md-1">
                                                            <label>&nbsp;</label>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <label>&nbsp;</label>
                                                    </div>
                                                    @foreach($game->ngo_groups->all() as $ngo_group)
                                                        <div class="col-md-1 highlight">

                                                            @foreach($ngo_group->ngos->all() as $ngo)
                                                                <div class="form-group">
                                                                    <label for="inputNGO{{$ngo->id}}">NGO{{$ngo->ngo}}</label>
                                                                    <input data-group-id="{{$ngo_group->id}}" type="text" pattern="[0-9]+" required id="inputNGO{{$ngo->id}}" name="ngo[{{$ngo->id}}]" placeholder="NGO{{$ngo->$ngo}}" value="{{ old('ngo')[$ngo->id] ? old('ngo')[$ngo->id] : 0 }}" class="ngo-input form-control" />
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                    <div class="col-md-1">
                                                        <label>&nbsp;</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $firstFlag = false; ?>
                                    @endforeach
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="row">
                            <div class="col-md-6 col-sm-12 text-center">
                                <button type="submit" class="btn btn-success">Create</button>
                            </div>
                            <div class="col-md-6 col-sm-12 text-center">
                                <button type="reset" class="btn btn-warning">Reset</button>
                            </div>
                        </fieldset>

                        {{csrf_field()}}
                    </form>
                </div>
            </div>

        </div>
    @endsection

@else

    @section('content')
        <div class="col-md-12">
            <h1 class="clearfix">
                <div class="pull-left">
                    Create Donation Slip
                </div>
                <a href="{{route('dashboard')}}" class="btn btn-info pull-right">Back</a>
                <form class="pull-right" style="margin-right: 10px" id="frmChannel">
                    <select id="channel" class="form-control">
                        <option style="display: none" disabled selected>Select Channel</option>
                        @foreach($channels as $channel)
                            <option value="{{$channel->id}}">{{$channel->name}}</option>
                        @endforeach
                    </select>
                </form>
            </h1>
            <hr/>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger">
                    Please select channel from right side of page.
                </div>
            </div>
        </div>
    @endsection

@endif

@push('footer')
<script>
    $(document).ready(function() {
        $(".global-input").change(function() {
            var group_id = $(this).attr('data-group');
            $(".ngo-input[data-group-id='"+group_id+"']").val($(this).val());
        });

        $("select#channel").change(function() {
            $(this).parent().submit();
        });

        $("#frmChannel").submit(function() {
            var channelId = $("select#channel").val();
            $(this).attr('action', '{{URL::to('/dashboard/create-donation/')}}/'+channelId);
            return true;
        });

    });
</script>
@endpush