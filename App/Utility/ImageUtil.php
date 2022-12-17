<?php

namespace App\Utility;

class ImageUtil
{

    public static function  createImg($srcdir,$dstdir) {
        $bg_w = 600; // 背景图片宽度
        $bg_h = 759; // 背景图片高度

        $background = imagecreatetruecolor($bg_w, $bg_h); // 背景图片
        $color      = imagecolorallocate($background, 202, 201, 201); // 为真彩色画布创建白色背景，再设置为透明
        imagefill($background, 0, 0, $color);
        imageColorTransparent($background, $color);

        $wing      = mt_rand(2, 12);
        $resource = imagecreatefrompng($srcdir.'/chi/' . $wing . '.png');
        imagecopyresized($background, $resource, 0, 0, 0, 0, $bg_w, $bg_h, imagesx($resource), imagesy($resource));

        $body      = mt_rand(2, 12);
        $resource = imagecreatefrompng($srcdir.'/shenti/' . $body . '.png');
        imagecopyresized($background, $resource, 0, 0, 0, 0, $bg_w, $bg_h, imagesx($resource), imagesy($resource));

        $head      = mt_rand(2, 12);
        $resource = imagecreatefrompng($srcdir.'/tou/' . $head . '.png');
        imagecopyresized($background, $resource, 0, 0, 0, 0,  $bg_w, $bg_h, imagesx($resource), imagesy($resource));

        $dst = md5($wing."-".$body."-".$head).".png";
        imagepng($background, $dstdir."/$dst");
        return [$dst,$wing,$body,$head];
    }
}