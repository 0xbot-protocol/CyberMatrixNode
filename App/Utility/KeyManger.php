<?php


namespace App\Utility;


class KeyManger
{
    public const PRICE_OKEX_CHANNEL="channel_okex_price";

    public static function getFilledOrder($orderId) {
        return "order:fill:failed:{$orderId}";
    }
    public static function getuserBalanceKey($exchange,$uid,$token="USDT") {
        return "asset:{$exchange}:{$token}{$uid}";
    }
    public static function getFailedOrderSetKey() {
        return "check:fail:orderset";
    }
    public static function getAutoClearSetKey() {
        return "check:clear:botset";
    }
    public static function getFailedClearSetKey() {
        return "check:fail:clear:orderset";
    }

    public static function getLockOp($bot_id,$op) {
        return  "lock:bot:{$bot_id}:{$op}";
    }

    //一轮不要启动2个机器人
    public static function getLockRound($bot_id) {
       return  "lock:bot:{$bot_id}";
    }


    public static function getAskKey($exchange,$symbol) {
        return $exchange.":ask:".$symbol;
    }
    public static function getBidKey($exchange,$symbol) {
        return $exchange.":bid:".$symbol;
    }

    public static function getSimulateKey($exchange,$bot_type) {
        return "simulate:keyset:{$exchange}:{$bot_type}";
    }
    public static function getSimulateUserBotPriceKey($app,$exchange,$symbol,$uid,$bot_type) {
        return "simulate:price:".$app.":".$exchange.":".$symbol.":".$uid.":".$bot_type;
    }
    public static function getUserCoinBalanceKey($uid,$coin) {
        return 'user_balance:' . $uid . ':' . $coin . ':' . $coin;

    }
    public static function formatBalanceKey($exchange,$value) {

        if($exchange == "okex") {
            return $value;
        }
        return $value;
    }

    public static function getTrendFallKey($symbol) {
        return "trend:{$symbol}";
    }

    public static function getBotConfigKey($id) {
        return "bot:config:{$id}";
    }
    public static function getNodeBotKey($exchange) {
        return "bots:{$exchange}";
    }

    public static function getOpKey() {
        return "oplog";
    }

    public static function getOrderSetByBotId($bot_id) {
        return "orderset:".$bot_id;
    }
    public static function getBotStatusKey($bot_id) {
        return "status:{$bot_id}";
    }
    /*
     * 获取当前的币种，有哪些用户开启来相关的机器人
     */
    public static function getUidSetBySymbol($app,$exchange,$symbol) {
       return $app.":".$exchange.":".$symbol.":uidset";
    }

    public static function getBotRunningSet($app,$exchange) {
        return $app.":".$exchange.":botrunning";
    }

    public static function getAccountKey($app,$exchange) {
        return $app.":".$exchange.":apikeyset";
    }

//    public static function getAccountKey($exchange) {
//        return $exchange."_apikey_set";
//    }

    public static function getBotRunningKey($app,$bot_id) {
        return $app.":".$bot_id;
    }

    public static function getSymbolSetByUid($app,$uid) {
        return $app.":symbolset:{$uid}";
    }

    public static function getSymbolKey($app,$exchange,$symbol) {
        return $app.":".$exchange.":".$symbol;
    }

    public static function getSymbolSetKey($exchange) {
        return $exchange.":symbolset";
    }


    public static function getUidSetKey($app,$exchange) {
        return $app.":".$exchange.":uidset";
    }

    public static function getExchangeApikey($app,$exchange,$uid) {
        return $app.":".$exchange.":apikey:".$uid;
    }
    public static function getUidHandler($app,$exchange,$uid) {
        return $app.":".$exchange.":uhandler:".$uid;
    }

    public static function  getOrderQuene() {
            return "order:quene";
    }

    public static function getPriceSubKey($exchange) {
        return "sub:price_{$exchange}";
    }



    public static function  getUniPriceKey($exchange,$symbol,$uid=0)
    {
        if($uid) {
            return "price:{$exchange}:{$symbol}:{$uid}";
        }
        return "price:{$exchange}:{$symbol}";
    }


    public static function  getPriceKey($exchange,$symbol,$uid=0)
    {
        if($exchange == "okex_simulate") {
            $exchange = "okex";
        }
        if($uid) {
//            return "price:{$exchange}:{$type}:{$symbol}:{$uid}";
            return "price:{$exchange}:{$symbol}:{$uid}";
        }
        return "price:{$exchange}:{$symbol}";
    }

    public static function formatPriceKey($symbol) {
        return "price:".$symbol;
    }

}