<?php

namespace App\Utility;

class ExchangeUtil
{

    public static function getBinanceSymbol($symbol) {
        return str_replace("-","",$symbol);
    }
}