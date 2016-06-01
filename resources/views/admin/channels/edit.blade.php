@extends('layouts.master')

@section('title', 'Edit Channel')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                Edit Channel
            </h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
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

            @include('includes.validation_errors')

        </div>

        <div class="col-md-12">
            <form class="well" method="post" action="{{route('edit-channel-post', ['channel'=>$channel->id])}}">
                <legend>Update channel details...</legend>

                <fieldset>
                    <div class="form-group">
                        <label for="inputName" class="control-label">Name :</label>
                        <input type="text" id="inputName" name="name" placeholder="Channel name..." value="{{ $channel->name  }}" class="form-control" />
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{route('channels')}}" class="btn btn-info pull-right">Back</a>

                {{csrf_field()}}
            </form>
        </div>

    </div>
@endsection