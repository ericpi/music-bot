<?php

namespace App\Services;

use Log;
use \CloudConvert\Api;
use Aws\S3\S3Client;

class MusicService
{
    public function getToken()
    {
        $ch = curl_init("https://account.kkbox.com/oauth2/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.env('KKBOX_KEY', '')
        ));
        $result = curl_exec($ch);
        $result = json_decode($result,true);
        curl_close($ch);
        
        return $result['access_token'];
    }

    public function getMusic($token,$text)
    {
    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.kkbox.com/v1.1/search?q=".urlencode($text)."&type=track&territory=TW&offset=0&limit=1",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "accept: application/json",
		    "authorization: Bearer " . $token
		  ),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		return $response;
    }
    
    public function getMusicInfo($token,$text)
    {
    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.kkbox.com/v1.1/tracks/".$text."?territory=TW",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "accept: application/json",
		    "authorization: Bearer " . $token
		  ),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		return $response;
    }
    
    public function putS3($music_name)
    {
    	//Instantiate the client.
		$s3 = new S3Client([
		    'version' => 'latest',
		    'region'  => env('AWS_REGION',''),
		    'credentials' => [
		        'key'    => env('AWS_KEY',''),
		        'secret' => env('AWS_SERECT','')
		    ]
		]);
		
		$bucket = env('AWS_BUCKET','');
		$keyname = $music_name . '.mp3';
		
		// Prepare the upload parameters.
		$source = env('LOCAL_MUSIC_PATH','') . $keyname;
		
		try {
			$s3->putObject([
		        'Bucket' => $bucket,
		        'Key'    => $keyname,
		        'Body'   => fopen($source, 'r'),
		    ]);
		    unlink(env('LOCAL_MUSIC_PATH',''). $keyname);
		    $music_url = 'https://s3-'.env('AWS_REGION').'.amazonaws.com/'.env('AWS_BUCKET').'/' . urlencode($music_name) . '.mp3';
		} catch (Aws\S3\Exception\S3Exception $e) {
			unlink(env('LOCAL_MUSIC_PATH',''). $keyname);
			$music_url = 'error';
		}
		
		return $music_url;
    }
    
    public function getMusicM4a($text)
    {
    	$api = new Api(env('CONVERT_KEY'));
 
		$api->convert([
		    "inputformat" => "mp3",
		    "outputformat" => "m4a",
		    "input" => "download",
		    "file" => "https://".env('AWS_BUCKET')."/".$text.".mp3",
		    "output" => [
		        "s3" => [
		            "accesskeyid" => env('AWS_KEY'),
		            "secretaccesskey" => env('AWS_SERECT'),
		            "bucket" => env('AWS_BUCKET'),
		            "region" => env('AWS_REGION'),
		        ],
		    ],
		    "save" => true,
		])
		->wait();
    }
    
}
