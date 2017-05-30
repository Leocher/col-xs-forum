@extends('app')
@section('content')
    {{--引入编辑器代码--}}
    @include('editor::head')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1" role="main">
                {!! Form::open(['url'=>'/discussions']) !!}
                @include('forum.form')
                <div>
                    {!! Form::submit('发表帖子',['class'=>'btn btn-primary pull-right']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop