<?php

namespace App\Model;

use EasySwoole\Component\Singleton;

class MFATicketModel extends BaseModel
{
    public static $table = "mfa_ticket";
    use Singleton;

}