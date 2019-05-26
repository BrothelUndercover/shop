<?php

namespace App\Exceptions;

use Exception;

class InternalException extends Exception
{
    //处理系统内部异常
    protected $msgForUser;

    public function __construct(string $message,string $msgForUser = '系统内部错误', int $code = 500)
    {
    	parent::__construct($message,$code);

    	$this->msgForUser = $msgForUser;
    }

    public  function  render(Request $request)
    {
    	if ($request->exceptsJson()) {
    		return response()->json(['msg'=>$this->message],$this->code);
    	}
    	return view('pages.error',['msg'=>$this->msgForUser]);
    }
}
