<?php


namespace App\Utility;



class SignUtil
{






    public static function md5WithKey($data,$appsecret,$tag="default") {

        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if("sign" != $k) {

                    $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "secret=" . $appsecret;
        track_debug("signPairs:".$signPars,$tag);
        return strtolower(md5($signPars));
    }
    public static function AESEncrypt($params,$appKey)
    {
        return openssl_encrypt($params, 'aes-256-ecb', $appKey);
    }
    public static function AESDecrypt($params,$appKey)
    {
        return openssl_decrypt($params, 'aes-256-ecb', $appKey);
    }

    public static function AES128Decrypt($data,$key)
    {
        return openssl_decrypt($data, 'aes-128-ecb', $key, OPENSSL_PKCS1_PADDING);//OPENSSL_PKCS1_PADDING 不知道为什么可以与PKCS5通用,未深究
    }
}