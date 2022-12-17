<?php


namespace App\Bean\User;


use EasySwoole\Spl\SplBean;

class UserRegisterBean extends SplBean
{
    public $username;
    public $nickname;
    public $password;
    public $repassword;
    public $refcode;
    public $phone;
    public $remark;

}