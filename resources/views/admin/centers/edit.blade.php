@extends('layouts.master')

@section('title', 'Edit Center')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                <label class="pull-left">Centers <small>Edit Center</small></label>
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
                <form class="form-horizontal" method="post" action="{{route('edit-center-post', ["center"=>$center->id])}}">
                    <fieldset>
                        <legend>Edit Center</legend>
                        <div class="form-group">
                            <label for="inputType" class="control-label col-lg-3">Type :</label>
                            <div class="col-lg-9">
                                <select id="inputType" name="type" class="form-control" onchange="type_changed()">
                                    <option value="cashier" {{$center->user->hasRole('cashier') ? 'selected' : ''}}>Cashier</option>
                                    <option value="donator" {{$center->user->hasRole('donator') ? 'selected' : ''}}>Donator</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="control-label col-lg-3">Center name:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Center name..." id="inputName" name="name" value="{{ old('name') ? old('name') : $center->name}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="control-label col-lg-3">E-mail address:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="E-mail address" id="inputEmail" name="email" value="{{ old('email') ? old('email') : $center->user->email}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="control-label col-lg-3">Password:</label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" placeholder="Password..." id="inputPassword" name="password" value="{{old('password')}}" />
                                <small>Leave blank if you do not want to change password</small>
                            </div>
                        </div>
                        <div class="form-group" id="divCommissionRatio"  style="{{ $center->user->hasRole('cashier') ? 'display:none' : '' }}">
                            <label for="inputRatio" class="control-label col-lg-3">Commission Ratio:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Commission ratio in digits..." id="inputRatio" name="commission_ratio" value="{{old('commission_ratio') ? old('commission_ratio') : $center->commission_ratio}}" />
                            </div>
                        </div>
                    </fieldset>

                    <div id="divLuckyRatios" style="{{ $center->user->hasRole('cashier') ? 'display:none' : '' }}">
                        <legend>Lucky Ratios</legend>
                        <fieldset>
                            @foreach($games as $game)
                                <div class="form-group">
                                    <label for="inputPassword" class="control-label col-lg-3">{{$game->name}}: </label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="{{$game->name}} lucky ratio" id="input{{$game->id}}" name="game[{{$game->id}}]"  value="{{ old('game') ? old('game')[$game->id] : ($center->ratios()->where('game_id', $game->id)->first() ? $center->ratios()->where('game_id', $game->id)->first()->ratio : 0)}}" />
                                    </div>
                                </div>
                            @endforeach
                        </fieldset>
                    </div>

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

@push('footer')
<script type="text/javascript">
function type_changed() {
    var $value = $("select[name='type']").val();
    if($value == 'donator') {
        $("div#divLuckyRatios").slideDown();
        $("div#divCommissionRatio").slideDown();
    } else {
        $("div#divLuckyRatios").slideUp();
        $("div#divCommissionRatio").slideUp();
    }
}
</script>
@endpush