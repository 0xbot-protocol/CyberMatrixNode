<?php


namespace App\Utility;


use Simps\DB\PDO;

class DeployUtility
{
    public static function getPublicOrderChannelConfig($exchange) {
        if(is_test()) {
            $file = "test_bot";
        } else {
            $file = "bot";
        }
        $v =  config($file)['order_channel'][$exchange];
        return [$v['host'],$v['port'],$v['path'],$v['ssl']];
    }


    public static function getPublicPriceChannelConfig($exchange = "okex") {
        if(is_test()) {
          $file = "test_bot";
        } else {
            $file = "bot";
        }
        $v =  config($file)['price_channel'][$exchange];
        return [$v['host'],$v['port'],$v['path'],$v['ssl']];
    }

    public static function getPrivateUserChannelConfig($exchange = "okex") {
        if(is_test()) {
            $file = "test_bot";
        } else {
            $file = "bot";
        }
        $v =  config($file)['user_channel'][$exchange];
        return [$v['host'],$v['port'],$v['path'],$v['ssl']];
    }

    public static function getPublicKlineChannelConfig($exchange = "okex") {
        if(is_test()) {
            $file = "test_bot";
        } else {
            $file = "bot";
        }
        $v =  config($file)['kline_channel'][$exchange];
        return [$v['host'],$v['port'],$v['path'],$v['ssl']];
    }


}