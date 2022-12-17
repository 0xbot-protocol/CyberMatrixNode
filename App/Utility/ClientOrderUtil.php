<?php


namespace App\Utility;


use EasySwoole\Utility\SnowFlake;

class ClientOrderUtil
{

    public static function getClientOrderId($exchange,$app="nbc") {
        $s = SnowFlake::make(1,1)."";
        return $s;
//        $db = PoolTools::getDB();
//        $id = $db->insert("bot_client_order",['exchange'=>$exchange]);
//
//        if($id) {
//            if($exchange == "okex") {
//                return "432fa90157d1BCDE".date("Ymd").$id;
//
//            } else {
//                return "BOT".date("Ymd").$id;
//            }
//        } else {
//            track_error("获取clOrdId ID失败");
//            return "";
//        }



    }
}