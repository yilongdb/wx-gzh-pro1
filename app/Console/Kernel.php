<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function () {

            $baseUrl = config('wx.accessTokenBaseUrl');
            $accessTokenQueryUrl = config('wx.accessTokenQueryUrl');
            $url = $baseUrl.$accessTokenQueryUrl;
            $appid = config('wx.APPID');
            $appsecret = config('wx.APPSECRET');
            $url = sprintf($url, $appid, $appsecret);
            Log::info("start refresh access token , url : {$url}");
            //refresh access token  '?grant_type=client_credential&appid=%s&secret=%s',
            $client = new Client([
                'base_uri' => $baseUrl,
                'timeout' => 5.0,
            ]);

            $response = $client->get($url);

            $code = $response->getStatusCode();
            Log::info("refresh access token code : {$code}");
            if($code == Response::HTTP_OK){
                $token = json_decode($response->getBody());
                $access_token = $token->access_token;
                $expires_in = $token->expires_in;
//                $redis = new Redis();
                Log::info("token : {$access_token} , expire : {$expires_in}");
//                Redis::hSet('wx_access_token' , 'access_token' , $access_token);
//                Redis::hSet('wx_access_token' , 'expires_in' , $expires_in);
                Redis::setEx('access_token' , 7200 , $access_token);
                Redis::setEx('expires_in' , 7200 , $expires_in);
            }
//            $client->request('GET',
//                $baseUrl,
//                [
//                    'query' => [
//                        'grant_type' => 'client_credential',
//                        'appid' => $appid,
//                        'secret' => $appsecret
//                    ]
//                ]);
            /*
             * "access_token": "17_3RHDTKdjcTG1IzH-MdQNzrpmsBt9I39mhZyiMWqWhS0bn9HHp2h67nCPGE_c2wKWX_wTcgOC6AU0nUM
             * I8z2n5ESx5adVjbAMf4HWGKuVBnzrT02Th5BgcUUSirNANWkSwhLBbIKBPN13ZQBFXVFjAEACAK",
    "expires_in": 7200
             * */
        })->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
