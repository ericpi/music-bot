<?php

namespace App\Services;

use Log;

class LineService
{
    public function sendMsg($reply_token,$text){
        $access_token = env('LINE_ACCESS_TOKEN', '');
        $post_data = [
            "replyToken" => $reply_token,
            "messages" => [
                [
                    "type" => "text",
                    "text" => $text
                ]
            ]
        ];
        $ch = curl_init("https://api.line.me/v2/bot/message/reply");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$access_token
            //'Authorization: Bearer '. TOKEN
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function sendAudio($reply_token,$text){
        $access_token = env('LINE_ACCESS_TOKEN', '');
        $post_data = [
            "replyToken" => $reply_token,
            "messages" => [
                [
                    "type" => "audio",
                    "originalContentUrl" => $text,
                    "duration"=> 30000
                ]
            ]
        ];
        $ch = curl_init("https://api.line.me/v2/bot/message/reply");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$access_token
            //'Authorization: Bearer '. TOKEN
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public static function sendTemplate_music($reply_token,$text,$music_url)
    {
        $access_token = env('LINE_ACCESS_TOKEN', '');
        $post_data = [
            "replyToken" => $reply_token,
            "messages" => [
                [
                    'type' => 'template',
                    'altText' => 'this is a buttons template',
                    'template' =>
                        array (
                            'type' => 'buttons',
                            'thumbnailImageUrl' => $text['album']['images'][2]['url'],
                            'title' => $text['name'],
                            'text' => $text['album']['name'],
                            'actions' =>
                                array (
                                    // 0 =>
                                    //     array (
                                    //         'type' => 'uri',
                                    //         'label' => 'KKBOX試聽',
                                    //         'uri' => $text['url'],
                                    //     ),
                                    // 1 =>
                                    //     array (
                                    //         'type' => 'uri',
                                    //         'label' => 'KKBOX專輯',
                                    //         'uri' => $text['album']['url'],
                                    //     ),

                                    0 =>
                                        array (
                                            'type' => 'uri',
                                            'label' => '試聽下載點',
                                            'uri' => $music_url,
                                        ),
                                    1 =>
                                        array (
                                            'type' => 'uri',
                                            'label' => '關於開發者',
                                            'uri' => 'http://yorha2b.com/',
                                        ),

                                ),
                        ),
                ],
	            [
	                "type" => "audio",
	                "originalContentUrl" => $music_url,
	                "duration"=> 30000
	            ]
			]
        ];
        $ch = curl_init("https://api.line.me/v2/bot/message/reply");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$access_token
            //'Authorization: Bearer '. TOKEN
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
