<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/28
 * Time: 10:21
 * docs:
 */

namespace app\store\model;

use app\BaseModel;
use app\integral\model\UserIntegral;
use app\massage\model\BalanceWater;
use app\massage\model\Commission;
use app\seckill\model\PackageSeckill;
use app\shop\controller\IndexAliPay;
use think\facade\Db;

class PackageOrderRefund extends BaseModel
{
    protected $name = 'massage_store_package_order_refund_list';

    /**
     * @Desc: 同意退款
     * @param $refund_id
     * @param $pay_config
     * @return array|int[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/29 16:19
     */
    public static function refundOrder($refund_id, $pay_config, $text = '', $refund_type = 1)
    {

        $refund = self::where('id', $refund_id)->find();

        $order = PackageOrder::where('id', $refund['order_id'])->find();

        if ($order) {

            $order = $order->toArray();
        }

        $key = 'package_order_refund_order' . $refund_id;

        incCache($key, 1, $refund['uniacid']);

        $key_value = getCache($key, $refund['uniacid']);

        if ($key_value != 1) {

            decCache($key, 1, $refund['uniacid']);
            return ['code' => 1, 'msg' => '当前订单正在处理中，请稍后再试'];
        }

        //退款 修改退款订单  明细  修改佣金信息
        Db::startTrans();
        try {

            if ($refund['apply_price'] > 0) {

                //退款
                switch ($refund['pay_model']) {
                    case 1:
                        $response = orderRefundApi($pay_config, $order['pay_price'], $refund['apply_price'], $order['transaction_id']);

                        if (isset($response['return_code']) && isset($response['result_code']) && $response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {

                            $response['out_refund_no'] = !empty($response['out_refund_no']) ? $response['out_refund_no'] : $order['order_code'];

                            self::update(['out_refund_code' => $response['out_refund_no']], ['id' => $refund_id]);
                        } else {

                            $discption = !empty($response['err_code_des']) ? $response['err_code_des'] : $response['return_msg'];

                            throw new \Exception($discption);
                        }

                    case 2:
                        $water_model = new BalanceWater();
                        $insert = [
                            'uniacid' => $order['uniacid'],
                            'user_id' => $refund['user_id'],
                            'pay_price' => $refund['apply_price'],
                            'id' => $order['id'],
                        ];
                        $res = $water_model->updateUserBalance($insert, 8, 1);
                        if (!$res) {

                            throw new \Exception('退款失败，请重试');
                        }
                        break;

                    case 3:
                        $pay = new IndexAliPay(\app());
                        $res = $pay->aliRefund($order['transaction_id'], $refund['apply_price']);
                        if (isset($res['alipay_trade_refund_response']['code']) && $res['alipay_trade_refund_response']['code'] == 10000) {

                            self::update(['out_refund_code' => $res['alipay_trade_refund_response']['out_trade_no']], ['id' => $refund_id]);

                        } else {

                            throw new \Exception($res['alipay_trade_refund_response']['sub_msg']);
                        }
                        break;
                }
            }

            //退积分
            if ($refund['integral'] > 0) {

                $integral = [
                    'uniacid' => $refund['uniacid'],
                    'order_id' => $refund['order_id'],
                    'user_id' => $refund['user_id'],
                    'integral' => $refund['integral'],
                ];

                $res = UserIntegral::handleIntegral($integral, 4, 1);

                if ($res !== true) {

                    throw new \Exception('退回积分失败');
                }
            }

            //修改退款订单
            self::where('id', $refund_id)->update(['status' => 2, 'refund_price' => $refund['apply_price'], 'refund_time' => time(), 'refund_type' => $refund_type, 'refund_text' => $text]);

            //退款订单明细
            PackageOrderRefundGoods::where('refund_id', $refund_id)->update(['status' => 2]);

            //订单明细
            $order_goods_id = PackageOrderRefundGoods::where('refund_id', $refund_id)->column('order_goods_id');
            PackageOrderGoods::where('id', 'in', $order_goods_id)->update(['status' => 4]);

            //订单主订单
            $update = ['refund_price' => $order['refund_price'] + $refund['apply_price']];

            PackageOrder::where('id', $order['id'])->update($update);
            //重新计算佣金
            $order = PackageOrder::find($order['id']);
            $order = !empty($order) ? $order->toArray() : [];
            $order_update = (new PackageOrder())->getCashData($order, 2);
            if (!empty($order_update['order_data'])) {

                PackageOrder::where('id', $order['id'])->update($order_update['order_data']);;

            }

            $res = StorePackage::updateSale($refund['package_id'], $refund['num'], 2);

            if ($res['code'] == 1) {

                throw new \Exception('销量减少失败');
            }

            //恢复秒杀库存
            if (!empty($order['is_seckill'])) {

                PackageSeckill::updateSale($order['seckill_id'], $refund['num'], 2);
            }

            Db::commit();
        } catch (\Exception $exception) {

            Db::rollback();
            return ['code' => 1, 'msg' => $exception->getMessage()];
        }

        decCache($key, 1, $refund['uniacid']);

        return ['code' => 0];
    }

    /**
     * @Desc: 拒绝退款
     * @param $refund_id
     * @return array|int[]
     * @Auther: shurong
     * @Time: 2023/12/1 11:39
     */
    public static function passRefund($refund_id, $text)
    {
        //退款订单修改  明细修改
        //订单修改
        Db::startTrans();
        try {
            $refund = self::find($refund_id);

            $order = PackageOrder::where('id', $refund['order_id'])->find();

            $goods_id = PackageOrderRefundGoods::where('refund_id', $refund_id)->column('order_goods_id');

            self::where('id', $refund_id)->update(['status' => 3, 'refund_time' => time(), 'refund_type' => 2, 'refund_text' => $text]);

            PackageOrderRefundGoods::where('refund_id', $refund_id)->update(['status' => 3]);

            PackageOrder::where('id', $refund['order_id'])->update(['can_refund_num' => $order['can_refund_num'] + $refund['num']]);

            PackageOrderGoods::where('id', 'in', $goods_id)->update(['status' => 1]);

            Db::commit();
        } catch (\Exception $exception) {

            Db::rollback();
            return ['code' => 1, 'msg' => $exception->getMessage()];
        }

        return ['code' => 0];
    }

    /**
     * @Desc: 处理退款
     * @param $data
     * @param $pay_config
     * @return array|int[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/1 11:39
     */
    public static function refundCheck($data, $pay_config)
    {
        $refund = self::find($data['id']);

        if (empty($refund)) {

            return ['code' => 1, '订单不存在'];
        } elseif ($refund['status'] != 1) {

            return ['code' => 1, '此订单不可处理'];
        }
        $text = $data['text'] ?? '';
        if ($data['status'] == 2) {

            $code = self::refundOrder($data['id'], $pay_config[$refund['pay_model']], $text, 2);
        } else {
            $code = self::passRefund($data['id'], $text);
        }

        if ($code['code'] == 1) {

            return $code;
        }

        //验证订单是否完成
        $code = PackageOrder::orderStatusCheckAndUpdate($refund['order_id']);

        return $code;
    }

    /**
     * @Desc: 售后订单列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/30 14:01
     */
    public static function getList($where, $limit)
    {
        return self::alias('a')
            ->where($where)
            ->field('a.id,a.status,a.create_time,a.num,a.apply_price,b.name,b.cover,c.name as store_name,c.cover as store_cover')
            ->leftJoin('massage_store_package_order_list b', 'a.order_id = b.id')
            ->leftJoin('massage_store_apply c', 'a.store_id=c.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: 退款订单详情
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/30 14:57
     */
    public static function getInfo($id, $type = 1)
    {
        $data = self::alias('a')
            ->where(['a.id' => $id])
            ->field('a.id,a.create_time,a.num,a.apply_price,a.refund_code,a.out_refund_code,a.status,a.refund_price,a.text,a.refund_text,a.refund_time,b.name,b.cover,b.mobile,b.sku,b.create_time as order_create_time,b.pay_time,b.start_time,b.end_time,b.use_start_time,b.use_end_time,b.rule_text,c.name as store_name,c.cover as store_cover,c.trade_week,c.start_time as store_start_time,c.end_time as store_end_time,c.address,c.info,d.nickName,b.price,b.order_code,a.order_id,a.integral')
            ->leftJoin('massage_store_package_order_list b', 'a.order_id = b.id')
            ->leftJoin('massage_store_apply c', 'a.store_id=c.id')
            ->leftJoin('massage_service_user_list d', 'b.user_id=d.id')
            ->find();
        if ($data) {
            $data = $data->toArray();

            if ($type == 1) {

                $data['mobile'] = substr_replace($data['mobile'], "****", 3, 4);
            }

            $data['sku'] = json_decode($data['sku'], true);

            $arr = [
                'trade_week' => $data['trade_week'],
                'start_time' => $data['store_start_time'],
                'end_time' => $data['store_end_time'],
            ];

            $trade = getTradeStatus($arr);

            $data = array_merge($data, $trade);

            $data['code_info'] = PackageOrderRefundGoods::getList(['order_id' => $data['id']]);

        }

        return $data;
    }

    /**
     * @Desc: 取消退款
     * @param $refund_id
     * @return array|int[]
     * @Auther: shurong
     * @Time: 2023/11/30 16:26
     */
    public static function cancel($refund_id)
    {
        //取消申请退款订单 取消退款明细
        //修改主订单
        //修改订单明细

        Db::startTrans();
        try {
            $refund = self::find($refund_id);

            if (empty($refund)) {

                throw new \Exception('此订单不存在');
            }

            if ($refund['status'] != 1) {

                throw new \Exception('此订单不可取消');
            }

            $order = PackageOrder::find($refund['order_id']);

            $goods_id = PackageOrderRefundGoods::where('refund_id', $refund_id)->column('order_goods_id');

            self::where('id', $refund_id)->update(['status' => -1]);

            PackageOrderRefundGoods::where('refund_id', $refund_id)->update(['status' => -1]);

            $count = self::where(['order_id' => $refund['order_id'], 'status' => 1])->count();

            PackageOrder::where('id', $refund['order_id'])->update(['is_refund' => $count > 0 ? 1 : 0, 'can_refund_num' => $order['can_refund_num'] + $refund['num']]);

            PackageOrderGoods::whereIn('id', $goods_id)->update(['status' => 1]);

            Db::commit();
        } catch (\Exception $exception) {

            Db::rollback();
            return ['code' => 1, 'msg' => $exception->getMessage()];
        }

        return ['code' => 0];
    }

    /**
     * @Desc: 退款订单列表
     * @param $where
     * @param $dis
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/4 18:15
     */
    public static function getAdminList($where, $dis, $limit)
    {
        return self::alias('a')
            ->field('a.id,b.name,b.cover,b.price,b.reservation_day,b.ensure,b.num,a.apply_price,b.order_code,a.refund_code,a.out_refund_code,a.create_time,a.status,c.name as store_name,d.nickName')
            ->where($where)
            ->where(function ($query) use ($dis) {
                $query->whereOr($dis);
            })
            ->leftJoin('massage_store_package_order_list b', 'a.order_id=b.id')
            ->leftJoin('massage_store_apply c', 'b.store_id=c.id')
            ->leftJoin('massage_service_user_list d', 'b.user_id=d.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();

    }
}