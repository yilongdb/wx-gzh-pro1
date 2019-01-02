<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WxController extends Controller
{
    protected function verifyToken(Request $request)
    {
        $wxVerifyToken = config('wx.verifyToken');
        $signature = $request->input("signature");
        $timestamp = $request->input("timestamp");
        $nonce = $request->input("nonce");
        $echostr = $request->input("echostr");
        $list = [$wxVerifyToken, $timestamp, $nonce];
        sort($list);
        $hash = sha1(implode($list));


        return $hash == $signature;

    }

    public function tokenVerify(Request $request)
    {
        $echostr = $request->input("echostr");

        Log:
        info('wx verify msg ========================');
        Log::info(implode($request->all()));

        if ($this->verifyToken($request)) {
            return $echostr;
        } else {
            return response()->json(['message' => 'token verify failure'], Response::HTTP_BAD_REQUEST);
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

    public function postMsg1(Request $request)
    {

        Log::info('wx post msg ======================== post msg');
        Log::info(implode($request->all()));
//        Log::info($request->all());

        $echostr = $request->input("echostr");

        $xml = simplexml_load_string($request->all());

        $json = json_encode($xml);

        if (isset($echostr)) {
            if ($this->verifyToken($request)) {
                return $echostr;
            } else {
                return response()->json(['message' => 'token verify failure'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return $request->all();
        }


    }


    public function postMsg(Request $request)
    {

        $postStr = $request->getContent();
        Log::info("post msg ========== postStr");
        Log::info($postStr);
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            Log::info("post msg ========== post obj");
            Log::info($postObj);

            $RX_TYPE = trim($postObj->MsgType);
            //   $this->test($RX_TYPE) ;

            Log::info("receive text type : ".$RX_TYPE);
            switch ($RX_TYPE) {
                case "text":

                    $resultStr = $this->receive($postObj);
                    break;
                case "image":
                    $resultStr = $this->receive($postObj);
                    break;
                case "voice":
                    $resultStr = $this->receive($postObj);
                    break;
                case "event":
                    $resultStr = $this->receive($postObj);
                    break;
                default:
                    $resultStr = "unknow msg type: " . $RX_TYPE;
                    break;
            }
            return $resultStr;
        } else {
            return "";
//            exit;
        }


    }

    protected function receive($data, $type = 'text')
    {
        $ToUserName = $data->ToUserName ;
        $FromUserName = $data->FromUserName ;
        $CreateTime = $data->CreateTime ;
        $text = 'responsd data laravel php';
        $Content = $data->Content ;
        $xml = <<<XML
        <xml>
        <ToUserName><![CDATA[$ToUserName]]></ToUserName>
        <FromUserName><![CDATA[$FromUserName]]></FromUserName>
        <CreateTime>$CreateTime</CreateTime>
        <MsgType><![CDATA[$text]]></MsgType>
        <Content><![CDATA[$Content]]></Content>
        </xml>
XML;

        Log::info($xml);
        switch ($type) {
            case 'text':

                break;
            default:

                break;
        }
        return $xml;
    }

}
