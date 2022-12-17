<?php

namespace App\Utility;

use App\Model\Customer\UserModel;

class I18n
{
    public static function getLang($app,$uid) {
        $redis = PoolTools::getRedis();
        $key = "{$app}:i18n:{$uid}";
        $lang = $redis->get($key);
        if($lang) {
            return $lang;
        } else {
            $lang = UserModel::getInstance()->getMetaInfo($app,$uid);
            $redis->set($key,$lang);
        }
        return  $lang;
    }

    public static function setLang($app,$uid,$lang) {
        $redis = PoolTools::getRedis();
        $key = "{$app}:i18n:{$uid}";
        $redis->set($key,$lang);
        UserModel::getInstance()->setLang($app,$uid,$lang);

    }

}