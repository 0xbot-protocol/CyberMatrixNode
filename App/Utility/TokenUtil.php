<?php


namespace App\Utility;


class TokenUtil
{
    private static  $tokenMap = [
        'TRC20'=>'TRX',
        'BSC'=>'BSC'

    ];
    public static function getMainchain($mainchain) {

        return self::$tokenMap[$mainchain];
    }

}