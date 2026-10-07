<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/11/14
 * Time: 16:53
 * docs:
 */

namespace app\integral\model;

use app\BaseModel;
use app\integral\info\PermissionIntegral;
use app\massage\model\Order;
use app\massage\model\RefundOrder;
use app\member\model\MemberConfig;
use app\member\model\MemberOrder;

class OrderIntegral extends BaseModel
{
    protected $name = 'massage_member_order_integral';

    //order_type 1预约订单 2邀约订单

    /**
     * @Desc: 处理订单积分
     * @param $order
     * @param $type
     * @return true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/14 17:45
     */
    public static function getIntegralData($order, $type = 1, $status = -1)
    {
        $in_status = self::getStatus($order['uniacid'], $order['user_id']);

        if (!$in_status) {

            return true;
        }

        $config = MemberConfig::getInfo(['uniacid' => $order['uniacid']]);

        $integral = round($config['integra'] * $order['true_service_price']);

        if (!$integral > 0) {

            return true;
        }

        Order::where('id', $order['id'])->update(['config_integral' => $config['integra']]);

        $insert = [
            'uniacid' => $order['uniacid'],
            'order_id' => $order['id'],
            'user_id' => $order['user_id'],
            'integral' => $integral,
            'order_type' => $type,
            'status' => $status,
            'create_time' => time(),
            'update_time' => time()
        ];

        self::insert($insert);

        return true;
    }

    /**
     * @Desc: 退款积分计算
     * @param $order
     * @param $type
     * @return true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/15 10:48
     */
    public static function refundIntegral($order, $type = 1)
    {

        self::where(['order_id' => $order['id'], 'order_type' => $type])->update(['status' => -1]);

        if ($type == 2) {

            return true;
        }

        $model = new RefundOrder();

        $refund_success = $model->checkRefundNum($order['id']);

        if ($refund_success) {

            return true;
        }

        $integral = round($order['config_integral'] * $order['true_service_price']);

        if (!$integral > 0) {

            return true;
        }

        $data = self::where(['order_id' => $order['id'], 'order_type' => $type])->find();

        if (empty($data)) {

            return true;
        }

        self::where('id', $data['id'])->update(['integral' => $integral, 'status' => 1]);

        return true;
    }

    /**
     * @Desc: 用户积分状态
     * @param $uniacid
     * @param $user_id
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/14 17:00
     */
    public static function getStatus($uniacid, $user_id)
    {
        $status = MemberOrder::getStatus($user_id, $uniacid);

        if (!$status) {

            return 0;
        }

        $p = new PermissionIntegral((int)$uniacid);

        $auth = $p->pAuth();

        if (!$auth) {

            return 0;
        }

        return 1;
    }

    public static function edit($where, $data)
    {
        return self::where($where)->update($data);
    }

    /**
     * @Desc: 处理订单积分
     * @param $order_id
     * @param $type
     * @return true|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/14 18:56
     */
    public static function endIntegral($order_id, $type = 1)
    {
        $data = self::where(['order_id' => $order_id, 'order_type' => $type, 'status' => 1])->select()->toArray();

        if (!$data) {

            return true;
        }

        foreach ($data as $datum) {

            UserIntegral::handleIntegral($datum, $type);
        }

        $ids = array_column($data, 'id');

        self::edit([['id', 'in', $ids]], ['status' => 2]);

        return true;
    }
}