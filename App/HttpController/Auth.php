<?php

namespace App\HttpController;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;


class Auth extends Base
{


    protected function onRequest(?string $action): ?bool
    {
        $ret = parent::onRequest($action);
        if($ret === false){
            return false;
        }

        $token = $this->getSessionToken();
        if($token == "") {
            $this->writeJson(503,[],"Please Sign In");

            return false;
        }
        return true;
    }


}