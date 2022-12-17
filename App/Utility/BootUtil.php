<?php

namespace App\Utility;

use App\Model\Business\BizUserModel;

class BootUtil
{

    public static function initBizUser($app,$username,$password) {

//        $app = "nbc";
        $username = strval($username);
        $password = strval($password);
        $password = hash('sha256',$password);
        $filter = [
            'username'=>$username,
            'app'=>$app
        ];

        BizUserModel::getInstance()->tbl($app)->deleteOne($filter);
        $password = BizUserModel::getInstance()->getPassword($app,$password);
        $vo = BizUserModel::getInstance()->tbl($app)->findOne($filter);
        if(empty($vo)) {
            $data = [
                'app'=>$app,
                'uid'=>BizUserModel::getInstance()->appSeq($app,"biz_user"),
                'username'=>$username,
                'password'=>$password,
                'status'=>1,
                'create_at'=>ts()
            ];
            BizUserModel::getInstance()->tbl($app)->insertOne($data);
        } else {
            $r =BizUserModel::getInstance()->tbl($app)->updateOne($filter,['$set'=>['password'=>$password,'status'=>1]],['upsert'=>true]);
            $t = $r->getUpsertedCount();
          //  var_dump($t);
        }
    }
}