{{--Title Field--}}
<div class="form-group">
    {!! Form::label('title','Title:') !!}
    {!! Form::text('title',null,['class'=>'form-control']) !!}
</div>
{{--Body Field--}}
<div class="form-group">
    {{--编辑器一定要被一个 class 为 editor 的容器包住--}}
    <div class="editor">
        {{--创建一个 textarea 而已，具体的看手册，主要在于它的 id 为 myEditor--}}
        {!! Form::textarea('body',null,['class'=>'form-control','id'=>'myEditor']) !!}
    </div>
</div>