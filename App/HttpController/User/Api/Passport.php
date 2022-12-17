<?php

namespace App\HttpController\User\Api;

use App\Bean\User\UserRegisterBean;
use App\HttpController\Base;
use App\Logic\AppService;
use App\Logic\BlockService;
use App\Logic\CipherServcie;
use App\Logic\UserService;
use App\Model\Customer\UserModel;
use App\Model\Customer\GuestModel;
use App\Model\Miner\MinerUserModel;
use App\Utility\CacheTools;
use App\Utility\PoolTools;
use App\Utility\RetUtil;

class Passport extends Base
{
    public function getNonce() {

        $app = $this->getApp();
        $redis = PoolTools::getRedis();
        $seq = $redis->incr("nonce_seq");
        $ret = RetUtil::succRet(['nonce'=>$seq]);
        $this->writeRet($ret);
    }




    public function logout() {
        $token = $this->getAccessToken();
        $redis = PoolTools::getRedis();
        $i = $redis->del($token);
        $ret = $this->setRet(0,'ok',$i);
        $this->writeRet($ret);
    }


}