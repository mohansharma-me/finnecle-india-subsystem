@extends('layouts.master')

@section('title', 'Channels')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                Channels
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

        </div>
        <div class="col-md-9 col-sm-12">
            <div class="well">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Channel name</th>
                            <th>Last Modified Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($channels as $channel)
                            <tr>
                                <td>{{$channel->id}}</td>
                                <td>{{$channel->name}}</td>
                                <td>{{$channel->updated_at}}</td>
                                <td>
                                    <form method="post" action="{{route('delete-channel', ['channel' => $channel->id])}}">
                                        <a href="{{route('edit-channel', ['channel'=>$channel->id])}}" class="btn btn-xs btn-warning">Edit</a>
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure to delete all records related to this channel ?')">Delete</button>
                                        {{csrf_field()}}
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <center>{!!$channels->render()!!}</center>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="well">
                <form method="post" action="{{route('create-new-channel')}}" class="form-horizontal">
                    <fieldset>
                        <legend>Create new channel</legend>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <label for="inputChannel">Channel name :</label>
                                <input class="form-control" id="inputChannel" placeholder="Channel name..." type="text" name="name">
                            </div>
                        </div>
                        <div class="">
                            @if(count($errors))
                                <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p>{{$error}}</p>
                                @endforeach
                                </div>
                            @endif
                            @if(session('create_message'))
                                <div class="alert alert-{{session('create_success') ? 'success':'danger'}}">
                                    {{session('create_message')}}
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-success">
                                    Create
                                </button>
                            </div>
                        </div>
                    </fieldset>
                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
@endsection