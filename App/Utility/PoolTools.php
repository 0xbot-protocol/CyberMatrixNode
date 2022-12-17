<?php


namespace App\Utility;

use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use Redis;
use Simps\DB\BaseModel;
use Simps\DB\BaseRedis;
use Simps\DB\PDO;
use Simps\Singleton;
use Swoole\Coroutine;

class PoolTools
{
    public static function getRedis():BaseRedis {
        $redis = new BaseRedis();
        return  $redis;
    }

    public static function getOkexRedis():BaseRedis {
        $redis = new BaseRedis();
        return  $redis;
    }
    public static function getHuobiRedis():BaseRedis {
        $redis = new BaseRedis();
        return  $redis;
    }

    public static function getBianRedis():BaseRedis {
        $redis = new BaseRedis();
        return  $redis;
    }

    public static function returnRedis($redis) {

    }

    public static function getDB():BaseModel {
        return new BaseModel();
    }




    public static function initPool() {
        try {

//            $config = config('database');
//            PDO::getInstance($config);
            $config = config('redis');
            \Simps\DB\Redis::getInstance($config);




        } catch (\Exception $ex) {
            track_info($ex->getMessage());
          //  print_r($ex);
            Coroutine::sleep(3);
        }


    }

    public static function getStat() {
        $pdo = PDO::getInstance();
        return ['size'=>$pdo->getSize(),'idle'=>$pdo->idle()];
    }

}