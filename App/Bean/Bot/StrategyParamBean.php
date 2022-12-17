<?php

namespace App\Bean\Bot;

use EasySwoole\Spl\SplBean;

class StrategyParamBean extends SplBean
{
    public $bot_id;
    public $exchange;
    public $base_token;
    public $trade_token;
    public $strategy;
    public $bot_type;
    public $pos_side;

    public $risk_level;


}