<?php

namespace App\Model;

use EasySwoole\Component\Singleton;

class AccountModel extends BaseModel
{
    public static $table = "account";
    use Singleton;

}