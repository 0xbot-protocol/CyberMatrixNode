<?php


namespace App\Utility;


class CashTypeUtil
{
    public static $typeMap = [
      'deposit'=>'存币',
      'withdraw'=>'提币',
        'system_deposit'=>'系统存币',
        'transfer'=>'转账',
        'transfer_in'=>'转入',
        'transfer_out'=>'转出',
        'be_yearly_memmber'=>'年会会员',
        'be_every_half_year_memmber'=>'半年会员',
        'be_monthly_memmber'=>'月会员',
        'frozen_asset'=>'冻结资产',
        'system_deposit'=>'系统存币',
        'transfer_out_fee'=>'转出手续费',
        'transfer_out'=>'转出',
        'withdraw_deny'=>'提现拒绝',
        'switch_tt'=>'兑换TT'

    ];

    public static $cashTypeMap = [
        '全部'=>'',
        '直推奖励'=>'direct_reward',
        '社区奖励'=>'team_reward',
        '服务分红'=>'earn',
        '燃料费'=>'fuel',
//        '转入'=>'transfer_in',
//        '转出'=>'transfer_out',
    ];

    public static function getType($type) {

        return self::$typeMap[$type]??"";
    }

    public static function getTypeByHZ($name) {
        return self::$cashTypeMap[$name]??$name;
    }

}