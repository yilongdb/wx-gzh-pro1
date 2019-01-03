<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'wx'] , function ($router){

    Route::get('token-verify' , 'WxController@tokenVerify');
    Route::post('token-verify' , 'WxController@postMsg');

    Route::post('token-verify1' , 'WxController@postMsg1');
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
