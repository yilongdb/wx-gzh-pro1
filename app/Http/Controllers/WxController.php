<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WxController extends Controller
{
    public function tokenVerify(Request $request)
    {
        $wxVerifyToken = config('wx.verifyToken');
        $signature = $request->input("signature");
        $timestamp  = $request->input("timestamp");
        $nonce = $request->input("nonce");
        $echostr = $request->input("echostr");
        $list = [$wxVerifyToken , $timestamp , $nonce];
        sort($list);
        $hash = sha1(implode($list));
        if($hash == $signature){
            return $echostr;
        }else{
            return response()->json(['message' => 'token verify failure'] , Response::HTTP_BAD_REQUEST);
        }
    }
}
