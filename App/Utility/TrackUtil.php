<?php

namespace App\Utility;

use App\Model\System\EventModel;

class TrackUtil
{

    public static function track($app,$data) {
        $data['create_at'] = ts();
        EventModel::getInstance()->tbl($app)->insertOne($data);
    }
}