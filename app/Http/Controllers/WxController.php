<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WxController extends Controller
{
    protected function verifyToken(Request $request){
        $wxVerifyToken = config('wx.verifyToken');
        $signature = $request->input("signature");
        $timestamp  = $request->input("timestamp");
        $nonce = $request->input("nonce");
        $echostr = $request->input("echostr");
        $list = [$wxVerifyToken , $timestamp , $nonce];
        sort($list);
        $hash = sha1(implode($list));



        return $hash == $signature;

    }

    public function tokenVerify(Request $request)
    {
        $echostr = $request->input("echostr");

        Log:info('wx verify msg ========================');
        Log::info(implode($request->all()));

        if($this->verifyToken($request)){
            return $echostr;
        }else{
            return response()->json(['message' => 'token verify failure'] , Response::HTTP_BAD_REQUEST);
        }


//        $wxVerifyToken = config('wx.verifyToken');
//        $signature = $request->input("signature");
//        $timestamp  = $request->input("timestamp");
//        $nonce = $request->input("nonce");
//        $echostr = $request->input("echostr");
//        $list = [$wxVerifyToken , $timestamp , $nonce];
//        sort($list);
//        $hash = sha1(implode($list));
//
//        Log:info('wx verify msg ========================');
//        Log::info(implode($request->all()));
//
//        if($hash == $signature){
//            return $echostr;
//        }else{
//            return response()->json(['message' => 'token verify failure'] , Response::HTTP_BAD_REQUEST);
//        }
    }

    public function postMsg(Request $request){

        Log:info('wx post msg ======================== post msg');
        Log::info(implode($request -> all()));
//        Log::info($request->all());

        $echostr = $request->input("echostr");

        if(isset($echostr)){
            if($this->verifyToken($request)){
                return $echostr;
            }else{
                return response()->json(['message' => 'token verify failure'] , Response::HTTP_BAD_REQUEST);
            }
        }else{
            return $request->all();
        }



    }
}
