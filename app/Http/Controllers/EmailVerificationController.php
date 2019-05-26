<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Cache;
use App\Notifications\EmailVerificationNotification;
use Mail;
use App\Exceptions\InvalidRequestException;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
    	//从url中获取email和token两个参数
    	$email = $request->input('email');
    	$token = $request->input('token');

    	if (!$email || !$token) {
    		throw new InvalidRequestException("验证链接不合法");
    	}
    	//从缓存中读取数据,把url获取的Token与缓冲中数据进行对比
    	//如果缓存不存在或者匹配值不一致,抛出异常
    	if ($token != Cache::get('email_verification_'.$email)) {
    		throw new InvalidRequestException("验证链接不正确或者已过期");
    	}
    	//根据邮箱获取相应的用户
    	if (!$user = User::where('email',$email)->first()) {
    		throw new InvalidRequestException("用户不存在");
    	}
    	//根据key删除缓存
    	Cache::forget('email_verification_'.$token);
    	//更新用户对应的email_verified字段为ture
    	$user->update(['email_verified'=>true]);

    	//通知邮箱验证成功
    	return view('pages.success',['msg'=>'邮箱验证成功']);
    }

     public function send(Request $request)
    {
        $user = $request->user();

        // 判断用户是否已经激活
        if ($user->email_verified) {
            throw new InvalidRequestException("你已经验证过邮箱了");
        }

        // 调用 notify() 方法用来发送我们定义好的通知类
        $user->notify(new EmailVerificationNotification());

        return view('pages.success', ['msg' => '邮件发送成功']);
    }
}
