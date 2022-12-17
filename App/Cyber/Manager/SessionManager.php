<?php

namespace App\Cyber\Manager;

use App\Utility\PoolTools;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\ServerManager;

class SessionManager
{
    use Singleton;
    const ONLINE_IM = 'subscribed_user';

    public function setOnlineUser($fd) {
        track_info("setOnlineUser($fd)");

        $redis = PoolTools::getRedis();
        $redis->sAdd(self::ONLINE_IM, $fd);
    }

    public function offline($fd) {

        track_info("offline($fd)");
        $redis = PoolTools::getRedis();
        $redis->sRem(self::ONLINE_IM,$fd);


    }

    public function unsetOnlineUser ($uuid)
    {
        $redis = PoolTools::getRedis();
        return $redis->hdel(self::ONLINE_IM, $uuid);
    }

    public function checkUserOnline ($uuid)
    {
        $redis = PoolTools::getRedis();

        $flag =  $redis->hexists(self::ONLINE_IM, $uuid);
        echo "UUID:{$uuid},online:{$flag}\n";
        return $flag;
    }

    public function getFdFromRedis ($uuid)
    {
        $redis = PoolTools::getRedis();

        return $redis->hget(self::ONLINE_IM, $uuid);
    }

    public function send($fd,$msg) {
        $server = ServerManager::getInstance()->getSwooleServer();

        $ack = $server->push($fd,$msg);
        track_info("send ack:".$ack);
        return $ack;

    }


}