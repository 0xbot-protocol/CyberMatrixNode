<?php


namespace App\Utility;


class RetUtil
{
    public static function setSimulateRet($code,$data) {
        $item['sCode'] = 0;
        $item['sMsg'] = "";
        $item['tag'] = "";

    }
    public static function succRet($data="",$msg="succ") {
        return [
            'code'=>0,
            'msg'=>$msg,
            'result'=>$data
        ];
    }
    public  static function err($msg="error",$data=[]) {
        return [
            'code'=>1,
            'msg'=>$msg,
            'result'=>$data
        ];
    }
    public  static function succ($msg="succ") {
        return [
            'code'=>0,
            'msg'=>$msg,
            'result'=>[]
        ];
    }

    public  static function errRet($code,$data,$msg="error") {
        return [
            'code'=>$code,
            'msg'=>$msg,
            'result'=>$data
        ];
    }
    public  static  function setRet($code,$data,$msg="succ") {
        return [
            'code'=>$code,
            'msg'=>$msg,
            'result'=>$data
        ];
    }

    public static function isSucc($data)
    {
        if (isset($data['code']) && ($data['code'] == 0 || $data['code']==200)) {
            return true;
        }
        return false;
    }

    public static function isError($data)
    {
        if (!isset($data['code']) || $data['code'] != 0) {
            return true;
        }
        return false;
    }

    public static function msg($data)
    {
        if (isset($data['msg'])) {
            return $data['msg'];
        }
        return "系统错误";

    }
}