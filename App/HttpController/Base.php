<?php

namespace App\HttpController;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;


class Base extends Controller
{


    public function getAccessToken() {
        $accessHeader = $this->request()->getHeader("x-session-token");
        if(!empty($accessHeader)) {
            return $accessHeader[0];
        }
        return "";
    }

    public function getInfo() {
        return ServerManager::getInstance()->getSwooleServer()->connection_info($this->request()->getSwooleRequest()->fd);
    }

    public function writeRet($ret=[],$status = 200) {
        if (!$this->response()->isEndResponse()) {
            if(!isset($ret['result'])) {
                $ret['result']=null;
            }
            if(!isset($ret['code'])) {
                $ret['code']=0;
            }
            if(!isset($ret['msg'])) {
                $ret['msg']="";
            }
            $ret = json_encode($ret, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $this->response()->write($ret);
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($status);
            $this->response()->end();
            return true;
        } else {
            return false;
        }
    }
    function setRet($code,$msg="",$result=[]) {
        return ['code'=>$code,'msg'=>$msg,'result'=>$result];
    }




    public function getForm() {
        $appHeader = $this->request()->getHeader("content-type");
        if($appHeader && strpos($appHeader[0] ,"application/json")!==false) {
            return $this->json();
        }
        return $this->request()->getRequestParam();
    }


    protected function onRequest(?string $action): ?bool
    {
        $method = $this->request()->getMethod();
        if ($method == "OPTIONS") {
            $this->response()->withHeader("Access-Control-Allow-Origin", "*");
            $this->response()->withHeader("Access-Control-Allow-Headers",
                "Access-Control-Allow-Origin,content-type,X-Requested-With,x-app,x-auth");
            $this->response()->withStatus(200);
            return false; //直接返回header
        }
        return true;

    }

}