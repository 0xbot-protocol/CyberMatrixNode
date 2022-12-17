<?php


namespace App\Utility;


class MathUtil
{

    public static function  hexdec($v)
    {
        $num=gmp_init($v);
        return gmp_strval($num, 10);
    }

    public static function div($num,$decimal,$scale=8) {

//        track_info("div($num,$decimal)");
        $value = gmp_init($num);
        $num = gmp_strval($value,10);
        $value = (float) bcdiv((string)$num, (string)$decimal, $scale);
        $v =  number_format($value, $scale,".","");
        $v = rtrim($v,"0");
        $v = rtrim($v,".");
        return $v;
    }

}