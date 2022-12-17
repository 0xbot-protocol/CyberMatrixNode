<?php


namespace App\Utility;


class RedisSplitUtil
{

    public static function selectDB(&$redis,$exchange) {

        if($exchange == "okex") {
            $redis->select(2);
        } else {

        }

    }

}