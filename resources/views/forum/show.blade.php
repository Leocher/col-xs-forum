@extends('app')

@section('content')
    <div class="jumbotron">
        <div class="container">
            <div class="media">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object img-circle" alt="64x64" src="{{ $discussion->user->avatar }}" style="width: 64px">
                    </a>
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                        {{ $discussion->title }}
                        @if(Auth::check() && Auth::user()->id == $discussion->user_id)
                        <a class="btn btn-primary btn-lg pull-right" href="/discussions/{{$discussion->id}}/edit" role="button">修改帖子</a>
                        @endif
                    </h4>
                    {{ $discussion->user->name }}
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-9" role="main" id="post">
                {!! $html !!}
                <hr>
                @foreach($discussion->comments as $comment)
                    <div class="media">
                        <div class="media-left">
                            <a href="#">
                                <img class="media-object img-circle" alt="64x64" src="{{ $comment->user->avatar }}" style="width: 64px">
                            </a>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">{{ $comment->user->name }}</h4>
                            {{ $comment->body }}
                        </div>
                    </div>
                @endforeach
                <div class="media" v-for="comment in comments">
                    <div class="media-left">
                        <a href="#">
                            {{--@{{  }}的意思是不让blade解析，交给vuejs解析--}}
                            <img v-bind:src="comment.avatar" class="media-object img-circle" alt="64x64" style="width: 64px">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">{{ comment.name }}</h4>
                        @{{ comment.body }}
                    </div>
                </div>
                <hr>
                @if(Auth::check())
                    {{--监听submit事件，触发Vue实例中的methods方法--}}
                    {!! Form::open(['url'=>'/comment','v-on:submit'=>'onSubmitForm']) !!}
                    {!! Form::hidden('discussion_id',$discussion->id) !!}
                    {{--Body Field--}}
                    <div class="form-group">
                        {!! Form::textarea('body',null,['class'=>'form-control','v-model'=>'newComment.body']) !!}
                    </div>
                    <div>
                        {!! Form::submit('发表评论',['class'=>'btn btn-success pull-right']) !!}
                    </div>
                    {!! Form::close() !!}
                @else
                    <a href="/user/login" class="btn btn-block btn-success">登录参与评论</a>
                @endif
            </div>
        </div>
    </div>
    <script>
        Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
        new Vue({
            el:'#post',
            data:{
                comments:[],
                newComment:{
                    //三个字段主要是用来展示给用户看的
                    name:'{{Auth::user()->name}}',
                    avatar:'{{Auth::user()->avatar}}',
                    body:''
                },
                newPost:{
                    //三个字段主要用户储存到数据库
                    discussion_id:'{{$discussion->id}}',
                    user_id:'{{Auth::user()->id}}',
                    body:''
                }
            },
            methods:{
                onSubmitForm:function(e){
                    e.preventDefault();//防止form跳转提交
                    var comment = this.newComment;
                    var post = this.newPost;
                    post.body = comment.body;
                    this.$http.post('/comment',post).then(function(){
                        this.comments.push(comment);
                    });
                    this.newComment = {//清空输入框
                        name:'{{Auth::user()->name}}',
                        avatar:'{{Auth::user()->avatar}}',
                        body:''
                    };
                }
            }
        })
    </script>
@stop