<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Image;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function register()
    {
        return view('users.register');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\UserRegisterRequest $request)
    {
        //保存用户数据，重定向
        $data = [
            'confirm_code'=>str_random(48),
            'avatar'=>'/images/default-avatar.png'
        ];
        $user = User::create(array_merge($request->all(),$data));
        //send email
        //subject view confirm_code email
        $subject = 'Confirm Your Email';
        $view = 'email.register';
        $this->sendTo($user,$subject,$view,$data);
        return redirect('/');
    }

    public function confirmEmail($confirm_code)
    {
        $user = User::where('confirm_code',$confirm_code)->first();
        if(is_null($user)){
            return redirect('/');
        }
        $user->is_confirmed = 1;
        $user->confirm_code = str_random(48);
        $user->save();
        return redirect('user/login');
    }

    public function login()
    {
        return view('users.login');
    }

    public function signin(Requests\UserLoginRequest $request)
    {
        if(\Auth::attempt([
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'is_confirmed' => 1,
        ])){
            return redirect('/');
        }
        \Session::flash('user_login_failed','密码不正确或邮箱没验证');
        return redirect('/user/login')->withInput();
    }

    public function avatar()
    {
        return view('users.avatar');
    }

    public function changeAvatar(Request $request)
    {
        $file = $request->file('avatar');//获取到上传文件
        $input = array('image' => $file);
        $rules = array(
            'image' => 'image'
        );
        $validator = Validator::make($input, $rules);
        if ( $validator->fails() ) {
            return \Response::json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ]);

        }
        $destinationPath = 'uploads/';//需要提前创建public\uploads文件夹，保存用户上传头像
        $filename = \Auth::user()->id.'_'.time().$file->getClientOriginalName();//防止图片重名被覆盖
        $file->move($destinationPath,$filename);//把文件移到uploads目录
        Image::make($destinationPath.$filename)->fit(400)->save();

        return \Response::json([
            'success' => true,
            //http://localhost/uploads/xxx.png
            'avatar' => asset($destinationPath.$filename),
            //uploads/xxx.png
            'image' => $destinationPath.$filename,
        ]);
    }

    public function cropAvatar(Request $request)
    {
        //获取头像图片信息
        $photo = $request->get('photo');
        $width = (int)$request->get('w');
        $height = (int)$request->get('h');
        $xAlign = (int)$request->get('x');
        $yAlign = (int)$request->get('y');
        //在服务器端裁剪头像
        Image::make($photo)->crop($width,$height,$xAlign,$yAlign)->save();
        //修改当前用户头像并存盘
        $user = \Auth::user();
        $user->avatar = asset($photo);//$photo不带/，使用asset加/
        $user->save();
        //重定向到更换头像页面
        return redirect('/user/avatar');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    private function sendTo($user,$subject,$view,$data = [])
    {
        \Mail::queue($view,$data,function($message) use ($user,$subject){
            $message->to($user->email)->subject($subject);
        });
    }

    public function logout()
    {
        \Auth::logout();
        return redirect('/');
    }
}
