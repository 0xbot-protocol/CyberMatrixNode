<?php


namespace App\Bean\Trade;


use EasySwoole\Spl\SplBean;

class ExchangePairBean extends SplBean
{
    public $tag;
    public $base_token;
    public $trade_token;
    public $exchange;
    public $id;

}