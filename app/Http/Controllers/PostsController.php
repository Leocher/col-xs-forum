<?php

namespace App\Http\Controllers;

use App\MarkDown\MarkDown;
use Illuminate\Http\Request;
use App\Discussion;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use EndaEditor;

class PostsController extends Controller
{
    protected $markdown;

    public function __construct(MarkDown $markdown)
    {
        $this->middleware('auth',['only'=>['create','store','edit','update']]);
        $this->markdown = $markdown;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discussions = Discussion::latest()->get();//以最新发表的时间来排序
        return view('forum.index',compact('discussions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('forum.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\StoreBlogPostRequest $request)
    {
        $data = [
            'user_id'=>\Auth::user()->id,
            'last_user_id'=>\Auth::user()->id,
        ];
        $discussion = Discussion::create(array_merge($request->all(),$data));
        return redirect()->action('PostsController@show',['id'=>$discussion->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $discussion = Discussion::findOrFail($id);
        $html = $this->markdown->markdowm($discussion->body);
        return view('forum.show',compact('discussion','html'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $discussion = Discussion::findOrFail($id);
        if(\Auth::user()->id !== $discussion->user_id){
            return redirect('/');
        }
        return view('forum.edit',compact('discussion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\StoreBlogPostRequest $request, $id)
    {
        $discussion = Discussion::findOrFail($id);
        $discussion->update($request->all());
        return redirect()->action('PostsController@show',['id'=>$discussion->id]);
    }

    public function upload()
    {
        //存储到public\uploads
        $data = EndaEditor::uploadImgFile('uploads');
        return json_encode($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
