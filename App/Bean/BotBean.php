<?php

namespace App\Bean;

use EasySwoole\Spl\SplBean;

class BotBean extends SplBean
{

    public $bot_id;
    public $position;//当前持仓数量
    public $symbol;
    public $state; //状态
    public $add_num;//增加仓位数量
    public $close_num;//减仓数量
    public $pre_add; //加仓flag 0 || 1
    public $pre_close; //减仓flag  0 || 1
    public $add_trigger_price; //增加触发价格
    public $close_trigger_price; //减仓触发价格


}