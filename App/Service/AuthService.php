<?php

namespace App\Service;

use App\Model\AccountModel;
use App\Model\SessionModel;
use App\Utility\RetUtil;
use EasySwoole\Component\Singleton;

class AuthService
{
    use Singleton;


    public function find_account($id) {
        $acc = AccountModel::getInstance()->findOne(['_id'=>$id]);
        if($acc) {
            return RetUtil::succRet($acc);
        } else {
            return RetUtil::errRet(1,['type'=>'UnknownUser'],'UnknownUser');
        }
    }

    public function find_account_by_normalised_email($normalised_email) {
        $acc = AccountModel::getInstance()->findOne(['normalised_email'=>$normalised_email]);
        if($acc) {
            return RetUtil::succRet($acc);
        } else {
            return RetUtil::errRet(1,['type'=>'UnknownUser'],'UnknownUser');
        }
    }

    public function find_account_with_email_verification($token) {
        $filter = [
            'verification.token'=>$token,
            'verification.expire'=>['$gte'=>ts()]
        ];
        $acc = AccountModel::getInstance()->findOne($filter);
        if($acc) {
            return RetUtil::succRet($acc);
        } else {
            return RetUtil::errRet(1,['type'=>'TokenInvalid'],'TokenInvalid');
        }

    }

    //Find account with active password reset
    public function find_account_with_password_reset($token) {
        $filter = [
            'password_reset.token'=>$token,
            'password_reset.expire'=>['$gte'=>ts()]
        ];
        $acc = AccountModel::getInstance()->findOne($filter);
        if($acc) {
            return RetUtil::succRet($acc);
        } else {
            return RetUtil::errRet(1,['type'=>'TokenInvalid'],'TokenInvalid');
        }
    }

    public function find_account_with_deletion_token($token) {
        $filter = [
            'deletion.token'=>$token,
            'deletion.expire'=>['$gte'=>ts()]
        ];
        $acc = AccountModel::getInstance()->findOne($filter);
        if($acc) {
            return RetUtil::succRet($acc);
        } else {
            return RetUtil::errRet(1,['type'=>'TokenInvalid'],'TokenInvalid');
        }
    }

    public function find_session($id) {
        $filter = ['_id'=>$id];
        $session = SessionModel::getInstance()->findOne($filter);
        if($session) {
            return RetUtil::succRet($session);
        } else {
            return RetUtil::errRet(1,['type'=>'UnknownUser'],'UnknownUser');
        }

    }
    public function find_sessions($user_id) {
        $filter = ['user_id'=>$user_id];
        $sessions = SessionModel::getInstance()->gets($filter);
        if($sessions) {
            return RetUtil::succRet($sessions);
        } else {
            return RetUtil::errRet(1,['type'=>'DatabaseError'],'DatabaseError');
        }
    }
    public function find_sessions_with_subscription($user_ids) {
        $filter = [
            'user_id'=>['$in'=>$user_ids],
            'subscription'=>['$exists'=>true]
        ];
        $sessions = SessionModel::getInstance()->gets($filter);
        if($sessions) {
            return RetUtil::succRet($sessions);
        } else {
            return RetUtil::errRet(1,['type'=>'DatabaseError'],'DatabaseError');
        }
    }
    public function find_session_by_token($token) {
        $filter = [
            'token'=>$token
        ];
        $session = SessionModel::getInstance()->findOne($filter);
        if($session) {
            return RetUtil::succRet($session);
        } else {
            return RetUtil::errRet(1,['type'=>'DatabaseError'],'DatabaseError');
        }
    }

    public function save_account($account) {
        $filter = [
            '_id'=>$account['id']
        ];
        $update = [
            '$set'=>$account
        ];
        $affected = AccountModel::getInstance()->updateOne($filter,$update);
        if($affected) {
            return RetUtil::succRet();
        } else {
            return RetUtil::errRet(1,['type'=>'DatabaseError'],'DatabaseError');
        }
    }


}