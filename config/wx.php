<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2 0002
 * Time: 上午 11:42
 */
return [
    'verifyToken' => env('VERIFYToken' , 'wxverifytoken') ,
    'accessTokenBaseUrl' => 'https://api.weixin.qq.com/cgi-bin/token',
    'accessTokenQueryUrl' => '?grant_type=client_credential&appid=%s&secret=%s',
    'APPID' => env('APPID' , ''),
    'APPSECRET' => env('APPSECRET' , ''),
];