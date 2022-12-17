<?php


namespace App\Utility;


use EasySwoole\RedisPool\RedisPool;
use Simps\DB\BaseRedis;

class CacheTools
{

    public static function getRedis()
    {
//        $redis = RedisPool::defer();
        $redis = new BaseRedis();
        return $redis;

    }
    public static function getPermantRedis() {
        return RedisPool::defer();
    }

    public static function push($quene,$value) {
        $redis = self::getRedis();
        $redis->lPush($quene,$value);
    }

    public static function pop($quene) {
        $redis = self::getRedis();
        $redis->rPop($quene);
    }


    public static function get($key) {
        $v =  self::getRedis()->get($key);
        return unserialize($v);
    }
    public static function set($key,$value,$expire=600) {
        self::getRedis()->setEx($key,$expire,serialize($value));
    }
}
