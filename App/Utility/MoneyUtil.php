<?php


namespace App\Utility;


class MoneyUtil
{
    public static function  floorFloat($num,$digits) {
        $num  = floatval($num);
        $multiple = pow(10,$digits);
        $temp = floor($num*$multiple);
        $t =  sprintf('%'.$digits.'f',$temp/$multiple);
        return round($t,$digits,PHP_ROUND_HALF_DOWN);
    }
//    $num = floatval($num);
//    $multiple = pow(10, $digits);
//    $tempNum = floor($num*$multiple);
//    return sprintf('%.'.$digits.'f', $tempNum/$multiple);
//}
    public static function getMoneyInt($amount) {
        $float = self::getMoneyFloat($amount);
        return intval($float);
    }

    public static function getMoneyFloat($amount,$prec=0) {


        if(is_null($amount)) {
            $v = 0;
        }
        else if(is_object($amount)) {
            $v =  (float)($amount->__toString());
        } else {
             $v = floatval($amount);
        }
        if($prec>0) {
            $num = pow(10,$prec);
            $r = floor($v*$num);
            return round($r/$num,$prec);

        } else {
            return $v;
        }
    }

    public static function getMoneyValue($amount,$token,$type) {
        if($token == 'NULS' || $token == 'TPU' || $token == 'USDI' || $token == 'NVT') {
            $amount  = round($amount/100000000.0,8);
            return $amount;
        }
        if($type == "withdraw" || $type=="fee") {
            $amount = $amount*(-1);
        }
        return $amount/100000000.0;
    }

    public static function stripZero($amount,$precs=3) {
        return round($amount,$precs);
    }


    public static function getTokenUrl($token) {
        $map = [
            'NULS'=>'http://s.yqkkn.com/nuls2.png',
            'USDI'=>'http://s.yqkkn.com/USDI.png',
            'NVT'=>'http://s.yqkkn.com/NVT.png',
            'TPU'=>'http://s.yqkkn.com/tpu.png'
        ];
        if(isset($map[$token])) {
            return $map[$token];
        }
        return  $map['TPU'];
    }
}