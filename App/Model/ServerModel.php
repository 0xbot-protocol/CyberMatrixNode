<?php

namespace App\Model;

use EasySwoole\Component\Singleton;

class ServerModel extends BaseModel
{
    public static $table = "account";
    use Singleton;

}