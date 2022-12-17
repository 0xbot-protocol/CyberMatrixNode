<?php

namespace App\Utility;

class BlockUtil
{
    public static function formatAddress($address) {
        $start = strlen($address)-40;
        echo $start;
        return '0x'.substr($address,$start,40);
    }

}