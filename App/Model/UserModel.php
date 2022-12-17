<?php

namespace App\Model;

use EasySwoole\Component\Singleton;

class UserModel extends BaseModel
{
    public static $table = "user";
    use Singleton;

}