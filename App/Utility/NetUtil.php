<?php


namespace App\Utility;


class NetUtil
{

    public static function getIp() {
//        return getHostByName(getHostName());
        return config("node_ip");

    }
}