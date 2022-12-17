<?php

namespace App\Utility;


class SessionUtil
{

    public static function getLoginInfo($sid)
    {
        $redis = CacheTools::getRedis();
        $info = $redis->get($sid);
        return json_decode($info, true);
    }
}