<?php

namespace App\Utility;
use EasySwoole\Component\Singleton;
use Swoole\Table;


class FdManager
{
    use Singleton;

    private $fdUserId;//fd=>userId
    private $userIdFd;//userId=>fd

    function __construct(int $size = 1024*256)
    {
        $this->fdUserId = new Table($size);
        $this->fdUserId->column('uid',Table::TYPE_INT,16);
        $this->fdUserId->create();
        $this->userIdFd = new Table($size);
        $this->userIdFd->column('fd',Table::TYPE_INT,10);
        $this->userIdFd->create();
    }
    function bind(int $fd,int $uid)
    {
        $this->fdUserId->set($fd,['uid'=>$uid]);
        $this->userIdFd->set($uid,['fd'=>$fd]);
    }
    function delete(int $fd)
    {
        $userId = $this->fdUserId($fd);
        if($userId){
            $this->userIdFd->del($userId);
        }
        $this->fdUserId->del($fd);
    }

    function fdUserId(int $fd):?int
    {
        $ret = $this->fdUserId->get($fd);
        if($ret){
            return $ret['uid'];
        }else{
            return null;
        }
    }

    function userIdFd(int $userId):?int
    {
        $ret = $this->userIdFd->get($userId);
        if($ret){
            return $ret['fd'];
        }else{
            return null;
        }
    }

}