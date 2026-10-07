<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/24
 * Time: 11:44
 * docs:
 */

namespace app\store\model;

use app\BaseModel;
use app\card\model\User;
use app\integral\model\UserIntegral;
use app\massage\model\BalanceWater;
use app\massage\model\Commission;
use app\massage\model\StoreApply;
use app\seckill\model\PackageSeckill;
use app\shop\controller\IndexAliPay;
use log\LogUtils;
use think\facade\Db;

class PackageOrder extends BaseModel
{
    protected $name = 'massage_store_package_order_list';


    public function getMobileAttr($value, $data)
    {

        if (!empty($value) && isset($data['uniacid'])) {

            if (numberEncryption($data['uniacid']) == 1) {

                return substr_replace($value, "****", 2, 4);
            }

        }

        return $value;

    }

    /**
     * @Desc: 订单回调
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/24 13:48
     */
    public static function orderResult($data)
    {
        LogUtils::log('门店订单回调，回调参数：' . json_encode($data), 'package_order');

        $order = self::where('order_code', $data['out_trade_no'])->find();

        if (empty($order)) {

            return false;
        }
        if ($order['status'] != 1) {

            return true;
        }

        $update = [
            'status' => 2,
            'transaction_id' => $data['transaction_id'],
            'pay_price' => $data['total_money'],
            'pay_time' => time()
        ];

        Db::startTrans();
        try {
            $res = self::where('order_code', $data['out_trade_no'])->update($update);

            if ($res === false) {

                throw new \Exception('修改订单失败');
            }

            if ($order['pay_model'] == 2) {

                $water_model = new BalanceWater();

                //添加余额流水
                $insert = [
                    'uniacid' => $order['uniacid'],
                    'user_id' => $order['user_id'],
                    'pay_price' => $order['true_package_price'],
                    'id' => $order['id'],
                ];

                $res = $water_model->updateUserBalance($insert, 7);

                if ($res === false) {

                    throw new \Exception('增加余额流水失败');
                }
            }

            $comm_model = new Commission();
            //将分销记录打开
            $comm_model->dataUpdate(['order_id' => $order['id'], 'status' => -1, 'order_type' => 3], ['status' => 1]);

            //增加套餐销量
            $code = StorePackage::updateSale($order['package_id'], $order['num']);

            if ($code['code'] == 1) {

                throw new \Exception('套餐销量增加失败');
            }

            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();

            LogUtils::log('门店订单回调失败，失败信息：' . $exception->getMessage(), 'package_order');
            return false;
        }
        return true;
    }

    /**
     * @Desc: 各类分销
     * @param $order
     * @param $type 1下单 2退款
     * @return array
     * @Auther: shurong
     * @Time: 2023/11/29 16:03
     */
    public function getCashData($order, $type = 1)
    {
        $comm_model = new Commission();

        $order = $comm_model->packageCashData($order, $type);

        if ($type != 1) {

            $comm_model->where([['type', 'in', [14, 15]], ['order_type', '=', 3], ['order_id', '=', $order['id']]])->delete();
        }

        //门店佣金
        $comm_model->storeCommission($order, $type);
        //推广人佣金
        $comm_model->shareCommission($order, $type);

        $arr = ['store_cash', 'share_cash', 'company_cash'];

        foreach ($arr as $value) {

            if (key_exists($value, $order)) {

                $list[$value] = $order[$value];
            }
        }

        $arr_data['order_data'] = $list;

        $arr_data['data'] = $order;

        return $arr_data;
    }

    /**
     * @Desc: 套餐订单
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/27 10:47
     */
    public static function getList($where, $limit)
    {
        return self::alias('a')
            ->where($where)
            ->field('a.id,a.order_code,a.status,a.create_time,a.name,a.num,a.pay_price,a.cover,a.can_refund_num,a.is_comment,b.name as store_name,b.cover as store_cover,a.package_id,b.id as store_id,a.true_package_price,a.is_seckill,a.start_time,a.end_time,a.seckill_end_time')
            ->leftJoin('massage_store_apply b', 'a.store_id=b.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: 订单列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/4 16:52
     */
    public static function getAdminList($where, $limit)
    {
        $data = self::alias('a')
            ->where($where)
            ->field('a.id,a.uniacid,a.order_code,a.status,a.create_time,a.name,a.num,a.pay_price,a.cover,a.can_refund_num,a.is_comment,a.reservation_day,a.ensure,a.price,a.hx_num,a.store_balance,a.store_cash,a.share_balance,a.share_cash,a.pay_model,b.name as store_name,b.cover as store_cover,c.nickName,ifnull(d.nickName,"--") as share_name,a.mobile,a.transaction_id')
            ->leftJoin('massage_store_apply b', 'a.store_id=b.id')
            ->leftJoin('massage_service_user_list c', 'a.user_id=c.id')
            ->leftJoin('massage_service_user_list d', 'a.share_user_id=d.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();
        if ($data['data']) {
            foreach ($data['data'] as &$item) {

                $item['store_balance'] = (float)$item['store_balance'];

                $item['share_balance'] = (float)$item['share_balance'];
            }
        }
        return $data;
    }

    /**
     * @Desc: 导出
     * @param $where
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/12 13:40
     */
    public static function getExcelList($where)
    {
        return self::alias('a')
            ->where($where)
            ->field('a.id,a.uniacid,a.order_code,a.status,a.create_time,a.name,a.num,a.pay_price,a.cover,a.can_refund_num,a.is_comment,a.reservation_day,a.ensure,a.price,a.hx_num,a.store_balance,a.store_cash,a.share_balance,a.share_cash,a.pay_model,b.name as store_name,b.cover as store_cover,c.nickName,ifnull(d.nickName,"--") as share_name,a.mobile,a.transaction_id')
            ->leftJoin('massage_store_apply b', 'a.store_id=b.id')
            ->leftJoin('massage_service_user_list c', 'a.user_id=c.id')
            ->leftJoin('massage_service_user_list d', 'a.share_user_id=d.id')
            ->order('a.create_time desc')
            ->select()
            ->toArray();
    }

    /**
     * @Desc: 数量
     * @param $where
     * @return array
     * @Auther: shurong
     * @Time: 2023/12/4 17:22
     */
    public static function getCountData($where)
    {

        $order_count = self::alias('a')
            ->where($where)
            ->leftJoin('massage_store_apply b', 'a.store_id=b.id')
            ->leftJoin('massage_service_user_list c', 'a.user_id=c.id')
            ->leftJoin('massage_service_user_list d', 'a.share_user_id=d.id')
            ->count();

        $where[] = ['a.status', '>', -1];

        $data = self::alias('a')
            ->where($where)
            ->leftJoin('massage_store_apply b', 'a.store_id=b.id')
            ->leftJoin('massage_service_user_list c', 'a.user_id=c.id')
            ->leftJoin('massage_service_user_list d', 'a.share_user_id=d.id')
            ->select()
            ->toArray();

        if ($data) {

            $pay_price = array_sum(array_column($data, 'pay_price'));
            $refund_price = array_sum(array_column($data, 'refund_price'));
            $store_cash = array_sum(array_column($data, 'store_cash'));
            $share_cash = array_sum(array_column($data, 'share_cash'));
            $company_cash = array_sum(array_column($data, 'company_cash'));
        } else {

            $pay_price = $refund_price = $store_cash = $share_cash = $company_cash = 0;
        }

        return [
            'order_count' => $order_count,
            'order_price' => round($pay_price - $refund_price, 2),
            'pay_price' => round($pay_price, 2),
            'refund_price' => round($refund_price, 2),
            'store_cash' => round($store_cash, 2),
            'share_cash' => round($share_cash, 2),
            'company_cash' => round($company_cash, 2)
        ];
    }


    /**
     * @Desc: 取消订单
     * @param $uniacid
     * @Auther: shurong
     * @Time: 2023/11/27 11:42
     */
    public static function chancel($uniacid, $pay_config)
    {

        $cancel_key = 'package_order_auto_chancel';

        incCache($cancel_key, 1, $uniacid);

        $value = getCache($cancel_key, $uniacid);

        if ($value != 1) {

            return false;
        }

        //待支付订单  过期自动取消
        $where = [
            ['uniacid', '=', $uniacid],
            ['status', '=', 1],
            ['over_time', '<', time()]
        ];

        $cancel_data = self::where($where)->select()->toArray();

        if ($cancel_data) {

            foreach ($cancel_data as $item) {
                //秒杀则要加库存
                if ($item['is_seckill'] == 1) {

                    PackageSeckill::updateSale($item['seckill_id'], $item['num'], 2);
                }

                //积分支付则需要退积分
                if ($item['is_integral'] == 1) {

                    $integral = [
                        'uniacid' => $item['uniacid'],
                        'order_id' => $item['id'],
                        'user_id' => $item['user_id'],
                        'integral' => $item['integral'],
                    ];
                    UserIntegral::handleIntegral($integral, 4, 1);
                }
            }
        }

        $ids = array_column($cancel_data, 'id');

        self::where('id', 'in', $ids)->update(['status' => -1]);

        //七天默认好评
        $where = [
            ['uniacid', '=', $uniacid],
            ['status', '=', 3],
            ['is_comment', '=', 0]
        ];

        $order = self::where($where)->select()->toArray();
        if (!empty($order)) {
            $time = 604800;
            foreach ($order as $item) {

                if ($item['hx_time'] + $time < time()) {

                    self::addComment($item);
                }
            }
        }

        //待完成  已支付订单  过期退款
        $where = [
            ['uniacid', '=', $uniacid],
            ['status', '=', 2],
            ['end_time', '<', time()]
        ];

        $key = 'package_order_auto_chancel_refund';

        incCache($key, 1, $uniacid);

        $key_value = getCache($key, $uniacid);

        if ($key_value == 1) {

            $data = self::where($where)->select()->toArray();

            if (!empty($data)) {

                foreach ($data as $datum) {

                    $pay_config_ = $pay_config[$datum['pay_model']];

                    self::autoOrderChancel($datum, $pay_config_);
                }
            }
        }

        decCache($key, 1, $uniacid);

        decCache($cancel_key, 1, $uniacid);

        return true;
    }

    /**
     * @Desc: 自动评价
     * @param $order
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/22 16:12
     */
    public static function addComment($order)
    {
        $data = [
            'order_id' => $order['id'],
            'star' => 5,
            'text' => '该用户已默认好评',
            'img' => '',
            'is_hide' => 1,
            'uniacid' => $order['uniacid'],
            'user_id' => $order['user_id']
        ];
        PackageOrderComment::comment($data);
    }

    /**
     * @Desc: 订单自动取消
     * @param $order
     * @param $pay_config
     * @return array|true
     * @Auther: shurong
     * @Time: 2023/12/4 11:21
     */
    public static function autoOrderChancel($order, $pay_config)
    {
        //修改订单状态  退款  修改券码状态

        //查看有没有退款订单  有则取消退款订单

        //自动退款  不受保障方式限制  都自动退款

        $key = 'package_order_auto_chancel' . $order['id'];

        incCache($key, 1, $order['uniacid']);

        $key_value = getCache($key, $order['uniacid']);

        if ($key_value == 1) {
            Db::startTrans();
            try {

                $num = PackageOrderGoods::where([['order_id', '=', $order['id']], ['status', 'in', [1, 3]]])->count();

                $can_refund_price = $order['price'] * $num;

                $can_refund_num = $order['can_refund_num'];

                //退款
                switch ($order['pay_model']) {
                    case 1:
                        $response = orderRefundApi($pay_config, $order['pay_price'], $can_refund_price, $order['transaction_id']);

                        if (isset($response['return_code']) && isset($response['result_code']) && $response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {

                            $response['out_refund_no'] = !empty($response['out_refund_no']) ? $response['out_refund_no'] : $order['order_code'];

                            $update = ['out_refund_code' => $response['out_refund_no'], 'refund_price' => $order['refund_price'] + $can_refund_price, 'can_refund_num' => $can_refund_num - $num];
                        } else {

                            $discption = !empty($response['err_code_des']) ? $response['err_code_des'] : $response['return_msg'];

                            throw new \Exception($discption);
                        }

                    case 2:
                        $water_model = new BalanceWater();
                        $insert = [
                            'uniacid' => $order['uniacid'],
                            'user_id' => $order['user_id'],
                            'pay_price' => $can_refund_price,
                            'id' => $order['id'],
                        ];
                        $res = $water_model->updateUserBalance($insert, 8, 1);
                        if (!$res) {

                            throw new \Exception('退款失败，请重试');
                        }
                        $update = [
                            'refund_price' => $order['refund_price'] + $can_refund_price,
                            'can_refund_num' => $can_refund_num - $num
                        ];
                        break;

                    case 3:
                        $pay = new IndexAliPay(\app());
                        $res = $pay->aliRefund($order['transaction_id'], $can_refund_price);
                        if (isset($res['alipay_trade_refund_response']['code']) && $res['alipay_trade_refund_response']['code'] == 10000) {

                            $update = ['out_refund_code' => $res['alipay_trade_refund_response']['out_trade_no'], 'refund_price' => $order['refund_price'] + $can_refund_price, 'can_refund_num' => $can_refund_num - $num];
                        } else {

                            throw new \Exception($res['alipay_trade_refund_response']['sub_msg']);
                        }
                        break;
                }

                //修改退款单号  退款金额  可退金额 可退数量
                if (!empty($update)) {

                    self::update($update, ['id' => $order['id']]);
                }

                //订单明细修改
                PackageOrderGoods::where([['order_id', '=', $order['id']], ['status', 'in', [1, 3]]])->update(['status' => 4]);

                //退款明细有申请中 修改退款订单和退款明细
                $refund_id = PackageOrderRefund::where([['order_id', '=', $order['id']], ['status', '=', 1]])->column('id');
                if (!empty($refund_id)) {

                    PackageOrderRefund::where([['order_id', '=', $order['id']], ['status', '=', 1]])->update(['status' => 2, 'refund_type' => 1]);

                    PackageOrderRefundGoods::where('refund_id', 'in', $refund_id)->update(['status' => 2]);
                }


                //重新计算佣金
                $order = self::find($order['id']);
                $order = !empty($order) ? $order->toArray() : [];
                $order_update = (new PackageOrder())->getCashData($order, 2);
                if (!empty($order_update['order_data'])) {

                    PackageOrder::where('id', $order['id'])->update($order_update['order_data']);;
                }

                //套餐减少销量
                $res = StorePackage::updateSale($order['package_id'], $num, 2);

                if ($res['code'] == 1) {

                    throw new \Exception('销量减少失败');
                }

                //恢复秒杀库存
                if (!empty($order['is_seckill'])) {

                    PackageSeckill::updateSale($order['seckill_id'], $num, 2);
                }

                //验证订单状态
                $code = self::orderStatusCheckAndUpdate($order['id']);
                if ($code['code'] == 1) {

                    throw new \Exception($code['msg']);
                }

                Db::commit();
            } catch (\Exception $exception) {

                Db::rollback();
                return ['code' => 1, 'msg' => $exception->getMessage()];
            }
        }
        decCache($key, 1, $order['uniacid']);
        return true;
    }


    /**
     * @Desc: 订单详情
     * @param $where
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/27 14:33
     */
    public static function getInfo($where, $type = 1)
    {
        $data = self::alias('a')
            ->field('a.id,a.uniacid,a.order_code,a.status,a.create_time,a.name,a.num,a.rule_text,a.package_price,a.pay_price,a.init_price,a.discount_price,a.cover,a.can_refund_num,a.is_comment,a.mobile,a.create_time,a.pay_time,a.start_time,a.end_time,a.qr_path,a.sku,a.pay_model,a.use_start_time,a.use_end_time,a.hx_time,a.over_time,a.is_refund,a.refund_price,b.name as store_name,b.cover as store_cover,b.trade_week,b.start_time as store_start_time,b.end_time as store_end_time,b.address,b.info,c.nickName,a.transaction_id,a.price,a.package_id,b.id as store_id,a.reservation_day,a.ensure,a.true_package_price,a.is_integral,a.integral,a.integral_to_money')
            ->where($where)
            ->leftJoin('massage_store_apply b', 'a.store_id=b.id')
            ->leftJoin('massage_service_user_list c', 'a.user_id=c.id')
            ->find();

        if (!empty($data)) {
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

            $data['code_info'] = PackageOrderGoods::getList(['order_id' => $data['id']]);

            $hx_data = PackageOrderHx::where('order_id', $data['id'])->order('create_time desc')->select()->toArray();

            if (!empty($hx_data)) {
                foreach ($hx_data as &$hx) {

                    $hx['code_num'] = explode(',', $hx['code_num']);
                }
            }

            $data['hx_data'] = empty($hx_data) ? '' : $hx_data;
        }

        return $data;
    }

    /**
     * @Desc: 获取虚拟手机号需要的数据
     * @param $order_id
     * @param $type 1套餐订单 2套餐退款订单
     * @return array
     * @Auther: shurong
     * @Time: 2023/12/1 15:57
     */
    public static function getVirtualPhoneData($order_id, $type = 1)
    {
        if ($type == 2) {

            $order_id = PackageOrderRefund::where('id', $order_id)->value('order_id');
        }

        $data = self::alias('a')
            ->field('a.id,a.uniacid,a.order_code,a.mobile,b.mobile as store_mobile')
            ->where(['a.id' => $order_id])
            ->leftJoin('massage_store_apply b', 'a.store_id=b.id')
            ->find();

        $address_info = [
            'mobile' => $data['mobile']
        ];
        $coach_info = [
            'mobile' => $data['store_mobile']
        ];
        $data['address_info'] = $address_info;
        $data['coach_info'] = $coach_info;

        return $data;
    }

    /**
     * @Desc: 单条数据
     * @param $where
     * @return PackageOrder|array|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/28 10:17
     */
    public static function getFirst($where)
    {
        return self::where($where)->find();
    }

    /**
     * @Desc: 获取订单各状态数量
     * @param $where
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/27 14:53
     */
    public static function getCountByList($where, $refund_where)
    {
        $data = self::where($where)
            ->field('status,count(*) as num')
            ->group('status')
            ->select()
            ->toArray();
        $arr = [
            'status_1' => 0,
            'status_2' => 0,
            'status_3' => 0,
        ];
        if (!empty($data)) {

            foreach ($data as $datum) {

                $arr['status_' . $datum['status']] = $datum['num'];
            }
        }

        $arr['refund_count'] = PackageOrderRefund::where($refund_where)->count();

        return $arr;
    }

    /**
     * @Desc: 数量
     * @param $where
     * @return int
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/27 15:45
     */
    public static function getCount($where)
    {

        return self::where($where)->count();
    }

    /**
     * @Desc: 核销订单
     * @param $data
     * @return array|int[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/27 17:17
     */
    public static function hxOrder($data)
    {
        $order = self::where('id', $data['order_id'])->find();

        $goods = PackageOrderGoods::where(['order_id' => $data['order_id'], 'status' => 1])->select()->toArray();

        if (empty($goods)) {

            return ['code' => 1, 'msg' => '待核销数量不足'];
        }

        if ($data['num'] > count($goods)) {

            return ['code' => 1, 'msg' => '核销数超过可用数量'];
        }

        $update = [
            'can_refund_num' => $order['can_refund_num'] - $data['num'],
            'hx_time' => time(),
            'hx_num' => $order['hx_num'] + $data['num']
        ];
        Db::startTrans();
        try {

            $res = PackageOrder::where(['id' => $data['order_id']])->update($update);

            if ($res === false) {

                throw new \Exception('订单核销失败');
            }

            $ids = PackageOrderGoods::where(['order_id' => $data['order_id'], 'status' => 1])
                ->order('code_num asc')
                ->limit($data['num'])
                ->column('id');

            $res = PackageOrderGoods::where('id', 'in', $ids)->update(['status' => 2]);

            if ($res === false) {

                throw new \Exception('套餐核销失败');
            }

            $code_num = PackageOrderGoods::where('id', 'in', $ids)->column('code_num');

            $insert = [
                'uniacid' => $order['uniacid'],
                'order_id' => $data['order_id'],
                'code_num' => implode(',', $code_num),
                'num' => $data['num'],
                'create_time' => time()
            ];
            //核销记录
            PackageOrderHx::insert($insert);

            $code = self::orderStatusCheckAndUpdate($data['order_id']);

            if ($code['code'] == 1) {

                throw new \Exception($code['msg']);
            }
            Db::commit();
        } catch (\Exception $exception) {

            Db::rollback();
            return ['code' => 1, 'msg' => $exception->getMessage()];
        }

        return ['code' => 0];
    }

    /**
     * @Desc: 门店销售数据
     * @param $data
     * @param $start
     * @param $end
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/28 14:35
     */
    public static function getShopData($data, $start, $end)
    {
        if (empty($data['id'])) {

            return '';
        }
        $startTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $endTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
        //门店收入
        $price = self::field(Db::raw("SUM(`pay_price` - `refund_price`) as price"))->where([['store_id', '=', $data['id']], ['status', '>', 1]])->whereBetween('create_time', [$start, $end])->find();
        $arr['all_price'] = $price['price'] ?? 0;
        //未核销订单
        $arr['no_hx_order_num'] = self::where([['store_id', '=', $data['id']], ['status', '=', 2]])->whereBetween('create_time', [$start, $end])->count();
        //订单量
        $arr['order_num'] = self::where([['store_id', '=', $data['id']], ['pay_time', '>', 0]])->whereBetween('create_time', [$start, $end])->count();
        //今日营收 金额
        $arr['today_price'] = self::where([['store_id', '=', $data['id']], ['status', '>', 1]])->whereBetween('create_time', [$startTime, $endTime])->sum('pay_price');
        //今日单量
        $arr['today_order_num'] = self::where([['store_id', '=', $data['id']], ['pay_time', '>', 0]])->whereBetween('create_time', [$startTime, $endTime])->count();
        //今日退款金额
        $arr['today_refund'] = PackageOrderRefund::where([['store_id', '=', $data['id']], ['status', '=', 2]])->whereBetween('create_time', [$startTime, $endTime])->sum('refund_price');

        $data['sale'] = $arr;

        return $data;
    }

    /**
     * @Desc: 申请退款
     * @param $data
     * @param $pay_config
     * @return array|int[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/29 16:23
     */
    public static function applyRefund($data, $pay_config)
    {
        //插入退款主表 子表
        //判断是不是所有的都申请完成了  已结束的订单需要佣金到账
        //修改订单主表 子表
        //自动退 修改主表佣金  佣金明细

        $order = self::where('id', $data['order_id'])->find();


        $goods = PackageOrderGoods::where(['order_id' => $data['order_id'], 'status' => 1])
            ->order('code_num desc')
            ->limit($data['num'])
            ->select()
            ->toArray();

        $price = $order['price'] * $data['num'];

        $integral_to_money = array_sum(array_column($goods, 'integral_to_money'));

        $integral = array_sum(array_column($goods, 'integral'));

        $insert = [
            'uniacid' => $data['uniacid'],
            'order_id' => $data['order_id'],
            'store_id' => $order['store_id'],
            'user_id' => $data['user_id'],
            'refund_code' => orderCode(),
            'status' => 1,
            'apply_price' => $price - $integral_to_money,
            'integral' => $integral,
            'text' => $data['text'],
            'package_id' => $order['package_id'],
            'num' => $data['num'],
            'pay_model' => $order['pay_model'],
            'apply_type' => 1,//用户申请
            'create_time' => time(),
            'update_time' => time()
        ];

        Db::startTrans();
        try {

            //插入退款主订单
            $refund_id = PackageOrderRefund::insertGetId($insert);

            $insert_goods = [];

            foreach ($goods as $good) {
                $insert_goods[] = [
                    'uniacid' => $data['uniacid'],
                    'code_num' => $good['code_num'],
                    'refund_id' => $refund_id,
                    'order_goods_id' => $good['id'],
                    'status' => 1,
                    'create_time' => time(),
                    'update_time' => time()
                ];
            }

            //插入退款明细
            PackageOrderRefundGoods::insertAll($insert_goods);

            //修改原订单标识当前有售后
            self::where('id', $data['order_id'])->update(['is_refund' => 1, 'can_refund_num' => $order['can_refund_num'] - $data['num']]);

            //修改原订单明细状态
            PackageOrderGoods::where('id', 'in', array_column($goods, 'id'))->update(['status' => 3]);

            //自动退款
            if ($order['ensure'] == 1) {

                $code = PackageOrderRefund::refundOrder($refund_id, $pay_config[$order['pay_model']]);

                if ($code['code'] == 1) {

                    throw new \Exception($code['msg']);
                }

                self::where('id', $data['order_id'])->update(['is_refund' => 0]);
            }
            //验证订单是否完成
            $code = self::orderStatusCheckAndUpdate($data['order_id']);

            if ($code['code'] == 1) {

                throw new \Exception($code['msg']);
            }

            Db::commit();
        } catch (\Exception $exception) {

            Db::rollback();
            return ['code' => 1, 'msg' => $exception->getMessage()];
        }

        return ['code' => 0];
    }

    /***
     * @Desc: 验证订单并且佣金到账
     * @param $order_id
     * @return array|int[]
     * @Auther: shurong
     * @Time: 2023/11/29 17:53
     */
    public static function orderStatusCheckAndUpdate($order_id)
    {
        $status = PackageOrderGoods::where('order_id', $order_id)->column('status');

        if (!in_array(1, $status) && !in_array(3, $status)) {

            if (!in_array(2, $status)) {

                $update_status = -1;

            } else {

                $update_status = 3;
            }

            Db::startTrans();
            try {

                $res = self::where('id', $order_id)->update(['status' => $update_status]);

                if ($res === false) {

                    throw new \Exception('订单状态变更失败');
                }

                if ($update_status == 3) {

                    //佣金到账
                    $code = Commission::packageCashSuccess($order_id);

                    if ($code['code'] == 1) {

                        throw new \Exception($code['msg']);
                    }
                }

                Db::commit();
            } catch (\Exception $exception) {

                Db::rollback();
                return ['code' => 1, 'msg' => $exception->getMessage()];
            }

        }

        return ['code' => 0];
    }

}