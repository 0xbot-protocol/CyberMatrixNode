<?php

namespace App\Utility;

class OrderUtility
{

    public static function getOkexOrder($instId,$tdModel,$side,$posSide,$amount,$ordType='market') {
        $order = [
            'instId' => $instId,
            'tdMode' => $tdModel,
            'clOrdId' => ClientOrderUtil::getClientOrderId("okex"),
            'side' => $side,
            'ordType' => $ordType,
            'posSide' => $posSide,
            'sz' => $amount,
        ];
        return $order;

    }
}