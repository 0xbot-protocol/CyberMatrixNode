<?php

namespace App\Model;

use EasySwoole\Component\Singleton;

class SessionModel extends BaseModel
{
    public static $table = "session";
    use Singleton;

}