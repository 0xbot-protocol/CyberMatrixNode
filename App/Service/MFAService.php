<?php

namespace App\Service;

use App\Model\MFATicketModel;
use App\Model\SessionModel;
use App\Utility\RetUtil;
use EasySwoole\Component\Singleton;

class MFAService
{
    use Singleton;


    public function find_ticket_by_token($token) {
        $filter = [
            'token'=>$token
        ];
        $vo = MFATicketModel::getInstance()->findOne($filter);
        if($vo) {
            return RetUtil::succRet($vo);
        } else {
            return RetUtil::errRet(1,['type'=>'InvalidToken'],'InvalidToken');
        }
    }
}