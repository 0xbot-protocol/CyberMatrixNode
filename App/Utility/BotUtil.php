<?php


namespace App\Utility;




use App\Model\Quant\BotInstrumentsModel;

class BotUtil
{
    public static function getZhangNumRate($exchange,$trade_currency,$redis=null) {
        if($exchange == "okex_simulate") {
            $exchange = "okex";
        }
        if($redis == null) {
            $redis = PoolTools::getRedis();
        }

        if($redis) {
            $key = "ctVal:{$exchange}:{$trade_currency}";
            $v = $redis->get($key);
            if($v>0) {
                return $v;
            }
        }
        $uly = $trade_currency."-USDT";
        $rate = BotInstrumentsModel::getInstance()->getVal($exchange,$uly);
        if($redis && $rate>0) {
            $key = "ctVal:{$exchange}:{$trade_currency}";
            $redis->set($key, $rate);
        }
        return $rate;



    }

    public static function getNameByType($type) {
        $map = [
            'grid'=>'无限网格',
            'martin'=>'智能追踪',
            'martin_super'=>'智能马丁',
        ];
        return $map[$type]??$type;
    }
    public static function getStateStr($state) {
//        public  const READY = 0; //准备开启机器人
//        public  const TRY_OPEN = 1; //开仓命令
//        public const OPENNED = 2; //已开仓
//        public const CLEAR  = 3;

        $map = [
            '0'=>'已停止',
            '1'=>'运行中',
            '2'=>'已清仓',
            '3'=>'未配置',

        ];
        return $map[$state]??$state;
    }


    public static function getDefaultConfig($risk_level="meidum") {

        $add_position_config=[//补仓
            [0.03, 1,0.012,1],
            [0.04, 2,0.012,1],
            [0.04, 4,0.012,1],
            [0.04, 4,0.045,1],
            [0.04, 2,0.035,1],
            [0.04, 1,0.035,1],
            [0.04, 1,0.035,1],
            [0.04, 1,0.035,1],
            [0.04, 1,0.035,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],
            [0.045, 1,0.04,1],

        ];
        return $add_position_config;

    }


    public static function getV2DefaultConfig() {

//        今晚先改FIL、LINK、UNI
//参数：1.2%总仓
//回调0.2%
//        首单：2倍
//杠杆5X
//分仓从4仓开始

//        1仓：3%   1倍      1.2%
//        2仓   4%     2倍      1.2%
//        3仓   4%    4倍        1.2%
//        4仓   4%    4倍         4.5%
//        5仓   4%     2倍        3.5%
//        8仓   4%     1倍         3.5%
//        9仓   4%     1倍         3.5%
//        10-28   4.5%     1倍      4%
        //            [0.03, 1,0.012],
        $add_position_config=[//补仓
            [0.03, 1,0.012],
            [0.04, 2,0.012],
            [0.04, 4,0.012],
            [0.04, 4,0.045],
            [0.04, 2,0.035],
            [0.04, 1,0.035],
            [0.04, 1,0.035],
            [0.04, 1,0.035],
            [0.04, 1,0.035],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],
            [0.045, 1,0.04],


        ];
        $num = count($add_position_config);







        $config =  [
            'use_private_data'=>0,
            'risk_level'=>"medium", //风险偏好:  1,2,3,保守、稳健、激进
            'simulate'=>0, //是不是模拟盘
            'open_position_amount'=>1,//开仓金额
            'open_amt_times'=>2,
            'lever'=>5,
            'add_position_times'=>$num,//补仓次数
            'trend_on'=>0, //趋势判断
            'stop_profit_subposition'=>1, //分仓止盈
            'stop_profit_subposition_seq'=>3,
            'loop'=>1, //是否连续循环
            'tdMode'=>'isolated',
            'stop_profit_back'=>0.002,//全仓止盈回调
            'stop_profit_rate'=>0.012,//全仓止盈
            'add_position_back'=>0.002,//补仓回调
            "add_position_config"=>$add_position_config

        ];
        if($config['open_amt_times']>1) {
            $config['open_position_amount'] = $config['open_amt_times'] * $config['open_position_amount'];
        }


        return $config;

    }



    /**
     * @return array[]
     * 默认指标
     */
    public static function getDefaultSwapConfig() {
        $add_position_config=[//补仓
            [0.01, 1, 0.01],
            [0.03, 2, 0.01],
            [0.03, 4, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
            [0.02, 1, 0.01],
        ];
        return $add_position_config;
    }

    public static function getMartinDefault($risk_level="medium",$bot_type='spot') {
        $add_position_config = self::getDefaultConfig();
        $num = count($add_position_config);
        if($bot_type == "spot") {
            if($risk_level == "low") {
                $config =  [
                    'risk_level'=>$risk_level, //风险偏好:  1,2,3,保守、稳健、激进
                    'simulate'=>0, //是不是模拟盘
                    'open_position_amount'=>5,//开仓金额
                    'open_amt_times'=>2,//开仓加倍
                    'add_position_times'=>$num,//补仓次数
                    'trend_on'=>false, //趋势判断
                    'stop_profit_subposition'=>true, //分仓止盈
                    'loop'=>1, //是否连续循环
                    'stop_profit_back'=>0.003,//全仓止盈回调
                    'stop_profit_rate'=>0.012,//全仓止盈
                    'add_position_back'=>0.003,//补仓回调

                    "add_position_config"=>$add_position_config
                ];
            } else if($risk_level == "medium") {
                $config =  [
                    'risk_level'=>$risk_level, //风险偏好:  1,2,3,保守、稳健、激进
                    'simulate'=>0, //是不是模拟盘
                    'open_position_amount'=>20,//开仓金额
                    'open_amt_times'=>2,//开仓加倍

                    'add_position_times'=>$num,//补仓次数
                    'trend_on'=>false, //趋势判断
                    'stop_profit_subposition'=>true, //分仓止盈

                    'loop'=>1, //是否连续循环
                    'stop_profit_back'=>0.003,//全仓止盈回调
                    'stop_profit_rate'=>0.012,//全仓止盈
                    'add_position_back'=>0.003,//补仓回调

                    "add_position_config"=>$add_position_config

                ];
            }else if($risk_level == "high") {
                $config =  [
                    'risk_level'=>$risk_level, //风险偏好:  1,2,3,保守、稳健、激进
                    'simulate'=>0, //是不是模拟盘
                    'open_position_amount'=>50,//开仓金额
                    'open_amt_times'=>2,//开仓加倍

                    'add_position_times'=>$num,//补仓次数
                    'trend_on'=>false, //趋势判断
                    'stop_profit_subposition'=>true, //分仓止盈

                    'loop'=>1, //是否连续循环
                    'stop_profit_back'=>0.003,//全仓止盈回调
                    'stop_profit_rate'=>0.012,//全仓止盈
                    'add_position_back'=>0.003,//补仓回调

                    "add_position_config"=>$add_position_config

                ];
            }



        } else if($bot_type == "swap") {
            if($risk_level == "low") {
                $config =  [
                    'risk_level'=>$risk_level, //风险偏好:  1,2,3,保守、稳健、激进
                    'simulate'=>0, //是不是模拟盘
                    'open_position_amount'=>1,//开仓金额
                    'open_amt_times'=>2,//开仓加倍

                    'add_position_times'=>$num,//补仓次数
                    'trend_on'=>false, //趋势判断
                    'stop_profit_subposition'=>true, //分仓止盈
                    'lever'=>2,
                    'pos_side'=>"long",
                    'tdMode'=>'isolated',

                    'loop'=>1, //是否连续循环
                    'stop_profit_back'=>0.001,//全仓止盈回调
                    'stop_profit_rate'=>0.012,//全仓止盈
                    'add_position_back'=>0.001,//补仓回调

                    "add_position_config"=>$add_position_config

                ];
            } else if($risk_level == "medium") {
                $config =  [
                    'risk_level'=>$risk_level, //风险偏好:  1,2,3,保守、稳健、激进
                    'simulate'=>0, //是不是模拟盘
                    'open_position_amount'=>1,//开仓金额
                    'open_amt_times'=>2,//开仓加倍

                    'add_position_times'=>$num,//补仓次数
                    'trend_on'=>false, //趋势判断
                    'stop_profit_subposition'=>true, //分仓止盈
                    'lever'=>3,
                    'pos_side'=>"long",
                    'tdMode'=>'isolated',
                    'stop_profit_rate'=>0.012,//全仓止盈

                    'loop'=>1, //是否连续循环
                    'stop_profit_back'=>0.001,//全仓止盈回调
                    'add_position_back'=>0.001,//补仓回调

                    "add_position_config"=>$add_position_config

                ];
            }else if($risk_level == "high") {
                $config =  [
                    'risk_level'=>$risk_level, //风险偏好:  1,2,3,保守、稳健、激进
                    'simulate'=>0, //是不是模拟盘
                    'open_position_amount'=>1,//开仓金额
                    'open_amt_times'=>2,//开仓加倍

                    'add_position_times'=>$num,//补仓次数
                    'trend_on'=>false, //趋势判断
                    'stop_profit_subposition'=>true, //分仓止盈
                    'lever'=>5,
                    'pos_side'=>"long",
                    'tdMode'=>'isolated',
                    'stop_profit_rate'=>0.012,//全仓止盈

                    'loop'=>1, //是否连续循环
                    'stop_profit_back'=>0.001,//全仓止盈回调
                    'add_position_back'=>0.001,//补仓回调

                    "add_position_config"=>$add_position_config

                ];
            }

        }

        return $config;
    }

/*
     * @param $type
     * 获取默认参数
     */
    public static function getDefaultParam($type,$risk_level="medium",$bot_type='spot') {

        if($type == "martin_super") {
            return self::getMartinDefault($risk_level,$bot_type);
        }  else {
            run_error_log("机器人类型错误：{$type}");
            return [];
        }


    }

    /**
     * @param $items
     * 输出使用于表单的参数
     */
    public static function formatFormParams($type,$config) {

        if($type == "grid") {
            $config['stop_profit_back'] =  $config['stop_profit_back'] *100;
            $config['stop_profit_rate'] =  $config['stop_profit_rate'] *100;
            $config['add_position_back'] =  $config['add_position_back'] *100;

            if(isset($config['add_position_config']['add_position_config'])) {
                $add_position_config = $config['add_position_config']['add_position_config'];

            } else {
                $add_position_config = $config['add_position_config'];
            }
          //  print_r($config);

            $outs=[];
            foreach ($add_position_config as $item) {
                $o =[
                    'r1' => round($item[0]*100,2,PHP_ROUND_HALF_DOWN),
                    'r2'=>$item[1]
                ];
                $outs[]=$o;
            }
            $config['add_position_config_format']=$outs;
        } else if($type=="grid") {

        }


      //  unset( $config['add_position_config']);

        return $config;



    }

    public static function canOp($bot_id,$op="open") {
        $redis = PoolTools::getRedis();

        $op_list = ['open','clear_position','add_position','close_position','manual_add_position','manual_close_position'];
        foreach($op_list as $op) {
            $opkey = KeyManger::getLockOp($bot_id,$op);
            $v = $redis->get($opkey);
            if($v) {
                $msg = "|{$bot_id}|机器人上次进行{$op}操作的时间是：".date("Y-m-d H:i:s",$v)." $v 还未完成";
                track_error($msg);
                if($v > 1) {
                    return false;
                }
            }
        }

//        $opkey = KeyManger::getLockOp($bot_id,"stop");
//        $v2 = $redis->get($opkey);
//        if($v2) {
//            return false;
//        }
        return true;

    }

    public static function orderFail($bot_id,$op="order_fail") {
        $opkey = KeyManger::getLockOp($bot_id,$op);
        $redis = PoolTools::getRedis();

        $v2 = $redis->get($opkey);
        if($v2) {
            return true;
        }
        return false;
    }

    public static function setOp($bot_id,$op,$ttl=60) {
        $opkey = KeyManger::getLockOp($bot_id,$op);

        $redis = PoolTools::getRedis();
        $ts = time();
        $redis->setex($opkey,$ttl,$ts);


        $msg = "|{$bot_id}|机器人开始进行{$op}操作,时间是：".date("Y-m-d H:i:s",$ts)."";
        track_info($msg);

    }




    public static function opDone($bot_id,$op) {
        $redis = PoolTools::getRedis();
        $opkey = KeyManger::getLockOp($bot_id,$op);
        $redis->del($opkey);

     //   BotStateModel::getInstance()->updateOne(['id'=>$bot_id],['op_status'=>"ready"]);

    }

    public static function clearOp($bot_id) {
        $op_list = ['open','clear_position','add_position','close_position'];
        foreach($op_list as $op) {
            self::opDone($bot_id,$op);
        }
        push_to_list($bot_id,"清除机器人锁定");
    }

    public static function getOpList($bot_id) {
        $op_list = ['open','clear_position','add_position','close_position'];
        $redis = PoolTools::getRedis();
        $outs = [];
        foreach($op_list as $op) {
            $opkey = KeyManger::getLockOp($bot_id,$op);
            $value = $redis ->get($opkey);
            $outs[$op] = $value;

        }
        return $outs;
    }


    public static function setOrderFailCheck($bot_id) {
        $redis = PoolTools::getRedis();

        $opkey = KeyManger::getFailedOrderSetKey();


        $redis->sAdd($opkey,$bot_id);
    }

    /**
     * @param $bot_id
     * 设置需要晴空的机器人
     */
    public static function setAutoClearBot($bot_id) {
        $redis = PoolTools::getRedis();

        $opkey = KeyManger::getAutoClearSetKey();


        $redis->sAdd($opkey,$bot_id);
    }

    public static function getAutoClearBot() {
        $redis = PoolTools::getRedis();
        $opkey = KeyManger::getAutoClearSetKey();
        return $redis->smembers($opkey);
    }

    public static function setOrderClearCheck($bot_id) {
        $redis = PoolTools::getRedis();

        $opkey = KeyManger::getFailedClearSetKey();


        $redis->sAdd($opkey,$bot_id);
    }

}