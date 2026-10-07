<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2024/4/1
 * Time: 17:45
 * docs:
 */

namespace app\massage\model;

use app\BaseModel;
use app\integral\model\OrderIntegral;
use think\facade\Db;

class ManyDemandOrder extends BaseModel
{
    protected $name = 'massage_service_many_demand_order';

    /**
     * @Desc: 插入
     * @param $input
     * @return int|string
     * @Auther: shurong
     * @Time: 2024/4/2 10:50
     */
    public static function add($input)
    {
        $input['create_time'] = time();
        return self::insertGetId($input);
    }

    /**
     * @Desc: 多订单回调
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/4/1 18:50
     */
    public static function orderResult($data)
    {
        $order = self::where('order_code', $data['out_trade_no'])->find();

        if (empty($order)) {

            return false;
        }

        if ($order['is_pay'] == 1) {

            return true;
        }

        $config = Config::where('uniacid', $order['uniacid'])->find();
        $update = [
            'is_pay' => 1,
            'pay_price' => $data['total_money'],
            'transaction_id' => $data['transaction_id'],
            'status' => $config['is_demand_order_check'] == 0 ? 2 : 1,
            'pay_time' => time()
        ];

        Db::startTrans();
        try {
            $res = self::where('order_code', $data['out_trade_no'])->update($update);

            if ($res === false) {

                throw new \Exception('修改订单失败');
            }
            $order = self::where('order_code', $data['out_trade_no'])->find()->toArray();


            $ser_data = json_decode($order['ser_data'], true);

            unset($order['ser_data'], $order['id']);

            $water_model = new BalanceWater();

            foreach ($ser_data as $key => $item) {

                for ($i = 0; $i < $item['num']; $i++) {

                    $insert = $order;

                    $insert['order_code'] = orderCode();

                    $insert['ser_id'] = $item['ser_id'];

                    $insert['price'] = $insert['pay_price'] = $item['price'];

                    $insert['coach_cash'] = round($item['price'] * $config['coach_tc_ratio'] / 100, 2);

                    $insert['member_discount'] = $item['member_discount'];

                    $insert['member_balance'] = $item['member_balance'];

                    $insert['member_status'] = $item['member_status'];

                    $order_id = DemandOrder::insertGetId($insert);

                    if ($order['pay_type'] == 3 && $item['price'] > 0) {

                        //添加余额流水
                        $insert_ = [
                            'uniacid' => $order['uniacid'],
                            'user_id' => $order['user_id'],
                            'pay_price' => $item['price'],
                            'id' => $order_id,
                        ];
                        $res = $water_model->updateUserBalance($insert_, 5);

                        if ($res === false) {

                            throw new \Exception('增加余额流水失败');
                        }
                    }

                    $integral = [
                        'uniacid' => $insert['uniacid'],
                        'user_id' => $insert['user_id'],
                        'true_service_price' => $insert['price'],
                        'id' => $order_id
                    ];
                    //积分相关
                    $res = OrderIntegral::getIntegralData($integral, 2, 1);

                    if ($res !== true) {

                        throw new \Exception('处理订单积分失败');
                    }
                }
            }
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            return false;
        }

        if ($update['status'] == 2) {

            curlSend("https://" . $_SERVER['HTTP_HOST'] . '/massage/app/Index/sendMsg?id=' . $order_id . '&urls=massage/app/Index/sendMsg&i=' . $order['uniacid']);
        }
        if ($order['pay_type'] == 3) {

            $type = 2;
        } elseif ($order['pay_type'] == 1) {

            $type = 3;
        } else {

            $type = 1;
        }

        SendMsgConfig::paySuccess($order['uniacid'], $order['user_id'], $data['total_money'], $type);

        return true;
    }

}