<?php
//全局bootstrap事件
date_default_timezone_set('Asia/Shanghai');


ini_set('ignore_repeated_errors', TRUE); // always use TRUE
ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', TRUE); // Error/Exception file logging engine.


function config($keypath) {
    return \EasySwoole\EasySwoole\Config::getInstance()->getConf($keypath);
}

function ts($ts=0)
{
    if($ts==0) {
        $ts = time();
    }
    return date("Y-m-d H:i:s",$ts);
}
function track_info($msg,$dst="track.log")
{
    $ts = date("Y-m-d H:i:s");
    $line =  $ts . "|info|" . $msg . "\n";
    echo $line;
}


function track_debug($msg)
{
    $ts = date("Y-m-d H:i:s");
    $line =  $ts . "|info|" . $msg . "\n";
    error_log($line, 3, "debug.log");
    echo $line;
}


function track_error($msg)
{
    $ts = date("Y-m-d H:i:s");
    $line =  $ts . "|error|" . $msg . "\n";
    //$dst = "track.error.".date("Ymd").".log";

    $dst = "track.error.log";

    error_log($line, 3, $dst);

    echo $line;
}
