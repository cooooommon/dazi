<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/7
 * Time: 16:57
 * docs:
 */

namespace app\broker\model;

use app\BaseModel;

class CommissionShare extends BaseModel
{
    protected $name = 'massage_service_order_commission_share';

    /**
     * @Desc: 插入数据
     * @param $uniacid
     * @param $id
     * @param $balance
     * @param $cash
     * @param $type
     * @param $share_id
     * @param $order_id
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/12/7 17:01
     */
    public static function add($uniacid, $id, $balance, $cash, $type, $share_id, $order_id, $comm_type)
    {
        $insert = [

            'uniacid' => $uniacid,

            'comm_id' => $id,

            'share_balance' => $balance,

            'share_cash' => $cash,

            'type' => $type,

            'share_id' => $share_id,

            'order_id' => $order_id,

            'comm_type' => $comm_type
        ];

        $res = self::insert($insert);

        return $res;
    }
}