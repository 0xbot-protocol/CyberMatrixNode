<?php

namespace App\Bean\Bot;

use EasySwoole\Spl\SplBean;

class SwapBean extends SplBean
{
    public $id;
    public $title;
    public $lang;
    public $from;
    public $to;
    public $items;
    public $fuelToken;
    public $enable;

}