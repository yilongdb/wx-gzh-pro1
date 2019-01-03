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
        $data = $request;
        $ToUserName = $data->ToUserName ;
        $FromUserName = $data->FromUserName ;
        $CreateTime = $data->CreateTime ;
        $msgType = $data->MsgType ;
        $Content = $data->Content ;
        $PicUrl = $data->PicUrl ;
        $MediaId = $data->MediaId ;
        $MsgId = $data->MsgId ;

        $res = '';
        if($msgType == 'text'){
            $xml = <<<XML
<xml><ToUserName><![CDATA[$ToUserName]]></ToUserName><FromUserName><![CDATA[$FromUserName]]></FromUserName><CreateTime>$CreateTime</CreateTime><MsgType><![CDATA[$msgType]]></MsgType><Content><![CDATA[$Content]]></Content></xml>
XML;

            $res = $xml;
        }else if($msgType == 'image'){
            $imgXml="<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><PicUrl><![CDATA[%s]]></PicUrl><MediaId><![CDATA[%s]]></MediaId><MsgId>%s</MsgId></xml>";


            $res = sprintf($imgXml,$ToUserName,$FromUserName,$CreateTime,$msgType,$PicUrl ,$MediaId, $MsgId);
        }else{


        }



   return response($res)->header('Content-Type' , 'application/xml');


    }


    public function token(Request $request){
        $token = \Redis::get('access_token');
        $ex = \Redis::get('expires_in');

        if($ex){
            Log::info("token : {$token} , ex : {$ex}");
        }


        return response(['token' => $token , 'ex' => $ex]);
    }
    public function postMsg(Request $request)
    {

        $token = \Redis::get('access_token');
        $ex = \Redis::get('expires_in');

        if($ex){
            Log::info("token : {$token} , ex : {$ex}");
        }
        $postStr = $request->getContent();
        $resultStr = '';
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

                    $resultStr = $this->receive($postObj , $RX_TYPE);
                    break;
                case "image":
                    $resultStr = $this->receive($postObj , $RX_TYPE);
                    break;
                case "voice":
                    $resultStr = $this->receive($postObj , $RX_TYPE);
                    break;
                case "event":
                    $resultStr = $this->receive($postObj , $RX_TYPE);
                    break;
                default:
                    $resultStr = "unknow msg type: " . $RX_TYPE;
                    break;
            }
            Log::info("string coding index : {$resultStr[0]} , {$resultStr[1]} , {$resultStr[2]} , {$resultStr[3]} , {$resultStr[4]} , ");
//            echo $resultStr;
//            exit(0);

            Log::info("string coding index : {$resultStr[0]} , {$resultStr[1]} , {$resultStr[2]} , {$resultStr[3]} , {$resultStr[4]} , ");
//            return response($resultStr);
            return response()->json($resultStr);
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
        $msgType = $data->MsgType ;
        $Content = $data->Content ;
        $PicUrl = $data->PicUrl ;
        $MediaId = $data->MediaId ;
        $MsgId = $data->MsgId ;
        $xml = <<<XML
<xml><ToUserName><![CDATA[$ToUserName]]></ToUserName><FromUserName><![CDATA[$FromUserName]]></FromUserName><CreateTime>$CreateTime</CreateTime><MsgType><![CDATA[$msgType]]></MsgType><Content><![CDATA[$Content]]></Content></xml>
XML;

        $imgXml ="<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><PicUrl><![CDATA[%s]]></PicUrl><MediaId><![CDATA[%s]]></MediaId><MsgId>%s</MsgId></xml>";

        $len = strlen($xml);
        Log::info($xml."string len : {$len}");
        switch ($type) {
            case 'text':

                break;
            case 'image':
                $imgXml = sprintf($imgXml,$ToUserName,$FromUserName,$CreateTime,$msgType,$PicUrl ,$MediaId, $MsgId);
                $len1 = strlen($imgXml);
                Log::info("img : {$imgXml}"."image len : {$len1}");
                return $imgXml;
            default:
            $xml = 'success';
                Log::info('return success default');
                break;
        }
        return $xml;
    }

}
