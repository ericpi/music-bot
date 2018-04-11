<?php

namespace App\Http\Controllers;

use App\Services\LineService;
use App\Services\MusicService;
use Sunra\PhpSimple\HtmlDomParser;
use App\Services\Aws\S3\S3Client;
use Log;

class MusicController extends Controller
{
    protected $lineService;
    protected $musicService;

    public function __construct()
    {
        $this->lineService = new LineService();
        $this->musicService = new MusicService();
    }

    public function index()
    {
        $json_string = file_get_contents('php://input');
        $array = json_decode($json_string,true);
        if(!is_array($array)) exit;
        foreach($array as $one){
            if($one[0]['type'] == 'message'){
                $userId = $one[0]['source']['userId'];
                $reply_token = $one[0]['replyToken'];
                if(isset($one[0]['message']['text'])){
                    $music_text = $one[0]['message']['text'];
                }else{
                    exit();
                }
            }elseif($one[0]['type'] == 'postback'){
                $userId = $one[0]['source']['userId'];
                $reply_token = $one[0]['replyToken'];
                $music_text = $one[0]['postback']['data'];
            }
        }
        
        try{
        	// 取得 KKBOX TOKEN
	        $token = $this->musicService->getToken();
	        // 抓取使用者輸入文字最接近的第一首歌的資料
	        $music = $this->musicService->getMusic($token,$music_text);
	        $music = json_decode($music,true);
	        $musicInfo = $this->musicService->getMusicInfo($token,$music['tracks']['data'][0]['id']);
	        $musicInfo = json_decode($musicInfo,true);
	        // 爬 KKBOX 網頁取得音樂網址
	        $html =  HtmlDomParser::file_get_html($music['tracks']['data'][0]['url']);
	        $music_url = $html->find('meta[property=music:preview_url:url]',0)->content;
	        if(env('PUT_S3') == 'ON') {
	        	// 將音樂存入本地後轉換到S3 ，再刪除本地
		        $content = file_get_contents($music_url);
				file_put_contents(env('LOCAL_MUSIC_PATH','').str_replace(" ","-",$music['tracks']['data'][0]['name']) . '.mp3', $content);
				$music_url = $this->musicService->putS3(str_replace(" ","-",$music['tracks']['data'][0]['name']));
				if($music_url == 'error'){
					throw new \Exception('查詢失敗');
				}
	        }
	        
	        // 是否轉換成IPHONE相容格式 (一天只能25首，且需2~3秒回應)
	        if(env('CONVERT_M4A') == 'ON'){
	        	file_put_contents(env('LOCAL_MUSIC_PATH','').$music['tracks']['data'][0]['id'].".mp3", fopen($music_url, 'r'));
	        	$music_result = $this->musicService->getMusicM4a($music['tracks']['data'][0]['id']);
				$music_url = 'https://s3-'.env('AWS_REGION').'.amazonaws.com/'.env('AWS_BUCKET').'/'.$music['tracks']['data'][0]['id'].'.m4a';
	        	unlink(env('LOCAL_MUSIC_PATH','').$music['tracks']['data'][0]['id'].'.mp3');
	        }
	        // 送出結果
	        $this->lineService->sendTemplate_music($reply_token,$musicInfo,$music_url);
        }catch(\Exception $ex){
        	Log::debug($ex->getMessage());
        	$this->lineService->sendMsg($reply_token,'查詢失敗');
        }
    }


}
