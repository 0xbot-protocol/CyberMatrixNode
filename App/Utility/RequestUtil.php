<?php


namespace App\Utility;


use EasySwoole\EasySwoole\Config;
use EasySwoole\HttpClient\HttpClient;

class RequestUtil
{






    public static function request($url,$items) {
        $str = json_encode($items);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, self::getAuthHeader());

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_error($ch);
        }
        curl_close($ch);
        return $result;

    }

    public static function requestJSON($url,$items,$tag="") {
        $str = json_encode($items);
        track_debug($url);
        track_debug($str);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($str)));

        curl_setopt($ch, CURLOPT_POSTFIELDS,$str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);


        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $msg = curl_error($ch);
            track_error($msg);
        }
        curl_close($ch);
        var_dump($result);
        if($tag) {
            track_debug($url,$tag);
        }
        $t = json_decode($result,true);

        return $t;

    }

    public static function postRequestJsonReturn($url,$items,$headers =[]) {
        $str = "";
        foreach ($items as $k=>$v) {
            $str .= "$k=$v&";
        }
//        track_debug("curl -d $str $url ");

        $ch = curl_init($url);
//        track_debug("1111");
//        var_dump($ch);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$items);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);


        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
//        track_debug("2222");

        $result = curl_exec($ch);
//        var_dump($result);
//        track_debug("3333");

        if (curl_errno($ch)) {
            track_error(" postRequest($url,".json_encode($items).",".json_encode($headers)." failed:".curl_error($ch));
        }
        curl_close($ch);
//        echo "xxx";
//        var_dump($result);
        $t = json_decode($result,true);
        if(is_null($t)) {
            return ['code'=>1,'msg'=>'BOT微服务忙，稍后再试'];
        } else {
            return $t;
        }
    }


    public static function getRequestJsonReturn($url,$items,$headers =[]) {
        $str = "";
        foreach ($items as $k=>$v) {
            $str .= "$k=$v&";
        }
        if(substr($url,-1)=="?") {
            $url .= $str;
        } else {
            $url = $url."?{$str}";
        }
        track_debug("curl  $url ");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT,5);   //只需要设置一个秒的数量就可以

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            track_error(" postRequest($url,".json_encode($items).",".json_encode($headers)." failed:".curl_error($ch));
        }
        curl_close($ch);

        $t =  json_decode($result,true);
        if(is_null($t)) {
            track_error($result);
            return ['code'=>1,'msg'=>'BOT微服务忙，稍后再试','result'=>[]];
        } else {
            return $t;
        }
    }

    public static function postRequest($url,$items,$headers =[]) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$items);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);


        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            track_error(" postRequest($url,".json_encode($items).",".json_encode($headers)." failed:".curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public static function getJsonRequest($url,$items=[],$headers =[])
    {
        $st = "";
        foreach ($items as $k=>$v) {
            $st .= "{$k}={$v}&";
        }
        $url = $url."?".$st;
        $client = new HttpClient($url);
        $respons = $client->get();
        if($respons) {
           return $respons->json(true);
        }
        return false;



    }
        public static function getRequest($url,$items=[],$headers =[]) {


//        if(strpos($url,"?") && !empty($items)) {
//            $url = $url."&".http_build_query($items);
//        } else if(!empty($items)) {
//            $url  = $url."?".http_build_query($items);
//        }
        $st = "";
        foreach ($items as $k=>$v) {
            $st .= "{$k}={$v}&";
        }
        $st = rtrim($st,"&");
        if(!empty($st)) {
            $url = $url."?".$st;
        }




        track_debug($url);
        $ch = curl_init($url);

//        echo $str;exit;
//        echo $url;exit;
//        $host = "127.0.0.1";
//        $port = "8888";
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

//        curl_setopt($ch, CURLOPT_PROXY, $host);
//        curl_setopt($ch, CURLOPT_PROXYPORT, $port);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
//        curl_setopt($ch, CURLOPT_POSTFIELDS,$items);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            track_error(" getRequest($url,$items,$headers =[]) failed:".curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public static function jsonGet($url,$items=[],$headers =[]) {
        $ret = self::getRequest($url,$items,$headers );
        return json_decode($ret,true);
    }



}
