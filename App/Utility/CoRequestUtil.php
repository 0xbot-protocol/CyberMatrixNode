<?php


namespace App\Utility;

use \EasySwoole\HttpClient\HttpClient;

class CoRequestUtil
{

    const TIMEOUT=6.5;
    const CONN_TIMEOUT=5;


    public static function requestJSON($url,$items,$tag="") {
        $str = json_encode($items);
        track_debug($url);
        track_debug($str);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($str)));

        curl_setopt($ch, CURLOPT_POSTFIELDS,$str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, self::getAuthHeader());

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_error($ch);
        }
        curl_close($ch);
        //  var_dump($result);
        if($tag) {
            track_debug($url,$tag);
        }
        $t = json_decode($result,true);

        return $t;

    }


    public static function getJsonRequestJsonReturn($url,$items,$headers =[]) {
        $client = new HttpClient($url);
//        track_info($url);
        $client->setContentTypeJson();
        $client->setTimeout(self::TIMEOUT);
        $client->setConnectTimeout(self::CONN_TIMEOUT);
        $client->setSslVerifyPeer(true,true);
        $client->setQuery($items);
        $reponse = $client->get($headers);
        if(is_null($reponse)) {
            return ['code'=>1,'msg'=>'BOT微服务忙，稍后再试'];
        } else {
            return     $reponse->json(true);
        }
    }



    public static function postJsonRequestJsonReturn($url,$items,$headers =[]) {
        $client = new HttpClient($url);
        $client->setTimeout(self::TIMEOUT);
        $client->setContentTypeJson();
        $client->setConnectTimeout(self::CONN_TIMEOUT);
        $client->setSslVerifyPeer(true,true);
        track_info($url);
        $str = json_encode($items);


        $reponse = $client->post($str,$headers);

        $t = $reponse->getBody();


        var_dump($t);
        if(is_null($reponse)) {
            return ['code'=>1,'msg'=>'BOT微服务忙，稍后再试'];
        } else {
            return             $reponse->json(true);
        }
    }

    public static function postRequestJsonReturn($url,$items,$headers =[]) {
        $client = new HttpClient($url);
        $client->setTimeout(self::TIMEOUT);
        $client->setConnectTimeout(self::CONN_TIMEOUT);
        $client->setSslVerifyPeer(true,true);
        track_info($url);

        $reponse = $client->post($items,$headers);

        $t = $reponse->getBody();


        var_dump($t);
        if(is_null($reponse)) {
            return ['code'=>1,'msg'=>'BOT微服务忙，稍后再试'];
        } else {
            return             $reponse->json(true);
        }
    }


    public static function getRequestJsonReturn($url,$items,$headers =[]) {
        $client = new HttpClient($url);
//        track_info($url);
        $client->setTimeout(self::TIMEOUT);
        $client->setConnectTimeout(self::CONN_TIMEOUT);
        $client->setSslVerifyPeer(true,true);
        $client->setQuery($items);
        $reponse = $client->get($headers);
        if(is_null($reponse)) {
            return ['code'=>1,'msg'=>'BOT微服务忙，稍后再试'];
        } else {
            return     $reponse->json(true);
        }
    }

}
