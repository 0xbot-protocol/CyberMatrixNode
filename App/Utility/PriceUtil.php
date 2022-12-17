<?php


namespace App\Utility;


use function Swoole\Coroutine\Http\request;

class PriceUtil
{

    public static function getSimulatePrice($exchange, $symbol, $type = "SPOT", $uid = "") {
        $redis = PoolTools::getRedis();
        $pricequene = "price:{$exchange}:$symbol";
        $price = $redis->rPop($pricequene);

        return $price;

    }
    /**
     * @param $exchange
     * @param $symbol
     * @param string $type
     * @param string $uid
     * @return bool|int|mixed|string
     * 获得价格$exchange, $symbol, $type,$uid
     */
    public static function getPrice($exchange, $symbol, $type = "SPOT", $uid = "")
    {

        if($exchange == "okex_simulate") {
            $exchange = "okex";
        }

        $symbol = str_replace("-SWAP","",$symbol);
        $redis = PoolTools::getRedis();
        $type = strtoupper($type);

        if ($uid == "") {
            $priceKey =  KeyManger::getPriceKey($type, $exchange, $symbol);
            $pricestr =    $redis->get($priceKey);
            $item['price_set'] = json_decode($pricestr, true);
            $last =  $item['price_set']['bidPx'] ?? 0;;
        } else {
            $priceKey =  KeyManger::getPriceKey($type, $exchange, $symbol, $uid);
            $pricestr =    $redis->get($priceKey);
            $item['price_set'] = json_decode($pricestr, true);
            $last =  $item['price_set']['bidPx'] ?? 0;;
        }
        return $last;
    }

    public static function setPrice($bot_type,$exchange, $symbol, $price,$ts, $uid = "")
    {
        $redis = PoolTools::getRedis();

        if ($uid == "") {
            $priceKey =  KeyManger::getPriceKey($bot_type, $exchange, $symbol);
        } else {
            $priceKey =  KeyManger::getPriceKey($bot_type, $exchange, $symbol, $uid);

        }

        $item = [
            'symbol'=>$symbol,
            'last'=>$price,
            'lastSz'=>$price,
            'askPx'=>$price,
            'askSz'=>$price,
            'bidPx'=>$price,
            'bidSz'=>$price,
            'ts'=>$ts,
            'exchange'=>$exchange

        ];
     //   track_info($priceKey."=>".json_encode($item));
        $redis->setex($priceKey,30,json_encode($item));

    }
}
