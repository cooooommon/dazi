<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/23
 * Time: 17:14
 * docs:
 */

namespace app\store\controller;

use app\ApiRest;
use app\integral\model\UserIntegral;
use app\massage\model\User;
use app\seckill\info\PermissionSeckill;
use app\seckill\model\PackageSeckill;
use app\shop\controller\IndexAliPay;
use app\store\info\PermissionStore;
use app\store\model\PackageOrder;
use app\store\model\PackageOrderComment;
use app\store\model\PackageOrderGoods;
use app\store\model\PackageOrderRefund;
use app\store\model\PackageOrderRefundGoods;
use app\store\model\StorePackage;
use app\storeplus\info\PermissionStoreplus;
use think\App;
use think\facade\Db;

class IndexOrder extends ApiRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 下单
     * @return mixed
     * @throws \WxPayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/24 16:28
     */
    public function payOrder()
    {
        $data = request()->only(['package_id', 'num', 'mobile', 'pay_model', 'share_user_id', 'is_seckill']);

        $seckill = PackageSeckill::where([['package_id', '=', $data['package_id']], ['status', '=', 1], ['end_time', '>', time()], ['start_time', '<=', time()]])->find();

        $model = new PermissionSeckill((int)$this->_uniacid);

        $auth = $model->pAuth();

        $data['is_seckill'] = !empty($seckill) && $auth ? 1 : 0;

        if (!empty($data['is_seckill'])) {

            $key = 'package_seckill_' . $data['package_id'];

            incCache($key, 1, $this->_uniacid);

            $value = getCache($key, $this->_uniacid);

            if ($value != 1) {

                decCache($key, 1, $this->_uniacid);

                $this->errorMsg('当前参与人数过多，请稍后');
            }
        }

        //门店权限
        $p = new PermissionStore((int)$this->_uniacid);

        $auth = $p->pAuth();

        $p = new PermissionStoreplus((int)$this->_uniacid);

        $plus_auth = $p->pAuth();

        if (!$auth || !$plus_auth) {

            $this->errorMsg('暂无权限');
        }

        $user = User::find($data['share_user_id']);

        if (empty($user) || $user['is_blacklist'] == 1 || $data['share_user_id'] == $this->getUserId()) {

            $data['share_user_id'] = 0;
        }

        $data['uniacid'] = $this->_uniacid;

        if (empty($data['is_seckill'])) {

            $status = StorePackage::checkStatus($data['package_id'], 1);

            if (!$status) {

                $this->errorMsg('套餐已下架');
            }

            $data['user_id'] = $this->getUserId();

            $data = StorePackage::payInfo($data);
        } else {

            //秒杀信息
            $data = $this->getSeckillPayInfo($key, $data);
        }

        $package_price = $data['init_price'] * $data['num'];
        $true_package_price = $data['price'] * $data['num'];

        $true_package_price_ = $true_package_price - ($data['integral_to_money'] ?? 0);
        $true_package_price_ = $true_package_price_ < 0 ? 0 : $true_package_price_;

        $insert = [
            'uniacid' => $this->_uniacid,
            'user_id' => $this->getUserId(),
            'store_id' => $data['store']['id'],
            'order_code' => orderCode(),
            'mobile' => $data['mobile'],
            'pay_model' => $data['pay_model'],
            'package_price' => $package_price,
            'true_package_price' => $true_package_price_,
            'discount_price' => $package_price > $true_package_price ? $package_price - $true_package_price : 0,
            'num' => $data['num'],
            'can_refund_num' => $data['num'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'over_time' => $data['over_time'],
            'package_id' => $data['package_id'],
            'share_user_id' => $data['share_user_id'],
            'name' => $data['name'],
            'sub_name' => $data['sub_name'],
            'cover' => $data['cover'],
            'price' => $data['price'],
            'init_price' => $data['init_price'],
            'use_start_time' => $data['use_start_time'],
            'use_end_time' => $data['use_end_time'],
            'reservation_day' => $data['reservation_day'],
            'rule_text' => $data['rule_text'],
            'ensure' => $data['ensure'],
            'sku' => json_encode($data['sku']),
            'store_balance' => $data['store']['store_balance'],
            'share_balance' => $data['store']['share_balance'],
            'create_time' => time(),
            'update_time' => time(),
            'app_pay' => $this->is_app,
            'is_seckill' => $data['is_seckill'],
            'seckill_id' => $data['seckill_id'] ?? 0,
            'seckill_end_time' => $data['seckill_end_time'] ?? 0,
            'integral' => $data['integral'] ?? 0,
            'integral_to_money' => $data['integral_to_money'] ?? 0,
            'is_integral' => $data['is_integral'] ?? 0
        ];

        Db::startTrans();

        $order_id = PackageOrder::insertGetId($insert);

        //积分抵扣
        if ($insert['is_integral'] == 1) {

            $integral = [
                'uniacid' => $insert['uniacid'],
                'order_id' => $order_id,
                'user_id' => $insert['user_id'],
                'integral' => $insert['integral'],
            ];
            $res = UserIntegral::handleIntegral($integral, 3, 0);

            if ($res !== true) {

                $this->errorMsg('积分抵扣失败');
            }
        }

        //生成二维码
        //小程序
        if ($this->is_app == 0) {

            $data['page'] = 'business/pages/manage/order/detail';

            $data['order_id'] = $order_id;

            $data['port'] = 'store';

            //获取二维码
            $qr = (new User())->orderQr($data, $this->_uniacid);
        } else {
            $page = 'https://' . $_SERVER['HTTP_HOST'] . '/h5/#/business/pages/manage/order/detail?order_id=' . $order_id . '&port=store';

            $qr = base64ToPng(getCode($this->_uniacid, $page));
        }

        PackageOrder::update(['qr_path' => $qr], ['id' => $order_id]);

        $goods_list = [];
        $code_num = date('ymd' . rand(1000, 9999));
        for ($i = 0; $i < $data['num']; $i++) {
            $goods_list[] = [
                'uniacid' => $this->_uniacid,
                'code_num' => $i == 0 ? $code_num : $code_num + $i,
                'order_id' => $order_id,
                'goods_price' => $data['price'],
                'integral' => isset($data['goods_integral_num']) ? ($i < $data['goods_integral_num'] ? ($data['goods_integral'] ?? 0) : 0) : 0,
                'integral_to_money' => isset($data['goods_integral_num']) ? ($i < $data['goods_integral_num'] ? ($data['goods_integral_to_money'] ?? 0) : 0) : 0,
                'is_integral' => isset($data['goods_integral_num']) ? ($i < $data['goods_integral_num'] ? 1 : 0) : 0,
                'create_time' => time(),
                'update_time' => time()
            ];
        }

        //订单下级
        PackageOrderGoods::insertAll($goods_list);

        $order = PackageOrder::find($order_id);

        //分销
        $order_update = (new PackageOrder())->getCashData($order->toArray());

        if (!empty($order_update['order_data'])) {

            PackageOrder::update($order_update['order_data'], ['id' => $order_id]);
        }

        //秒杀库存修改
        if (!empty($order['is_seckill'])) {

            PackageSeckill::updateSale($order['seckill_id'], $order['num']);

            decCache($key, 1, $this->_uniacid);
        }

        Db::commit();

        //如果是0元
        if ($true_package_price_ <= 0) {
            $data = [
                'total_money' => 0,
                'out_trade_no' => $order['order_code'],
                'transaction_id' => $order['order_code']
            ];

            PackageOrder::orderResult($data);

            $return_data = [
                'is_pay' => 1,
                'order_id' => $order_id
            ];
        } else {

            //余额支付
            if ($data['pay_model'] == 2) {

                $user_model = new User();

                $user_balance = $user_model->where(['id' => $this->getUserId()])->value('balance');

                if ($user_balance < $true_package_price_) {

                    $this->errorMsg('余额不足');
                }

                $arr = [
                    'total_money' => $true_package_price_,
                    'out_trade_no' => $order['order_code'],
                    'transaction_id' => $order['order_code']
                ];
                PackageOrder::orderResult($arr);
                $return_data = [
                    'is_pay' => 1,
                    'order_id' => $order_id
                ];
            } elseif ($data['pay_model'] == 1) {
                //虚拟支付开启时改走虚拟支付
                $virtualParams = \app\virtualpay\library\VirtualPayService::tryCreate($this->_uniacid, $this->getUserInfo()['openid'], 'packageOrder', $order['order_code'], $true_package_price_);
                if (!empty($virtualParams)) {
                    $jsApiParameters = $virtualParams;
                } else {
                    $pay_controller = new \app\shop\controller\IndexWxPay($this->app);
                    //支付
                    $jsApiParameters = $pay_controller->createWeixinPay($this->payConfig($this->_uniacid, $this->is_app), $this->getUserInfo()['openid'], $this->_uniacid, "购买套餐", ['type' => 'packageOrder', 'out_trade_no' => $order['order_code']], $true_package_price_);
                }

                $return_data = [
                    'is_pay' => 0,
                    'pay_model' => 1,
                    'order_id' => $order_id,
                    'pay_list' => $jsApiParameters
                ];
            } elseif ($data['pay_model'] == 3) {
                $pay = new IndexAliPay($this->app);


                $params = [
                    'order_code' => $order['order_code'],
                    'price' => $true_package_price,
                    'body' => '购买套餐',
                    'passback_params' => ['type' => 'packageOrder', 'uniacid' => $this->_uniacid],
                    'is_app' => $this->is_app
                ];
                $jsApiParameters = $pay->aliPay($params);
                $return_data = [
                    'is_pay' => 0,
                    'pay_model' => 3,
                    'order_id' => $order_id,
                    'pay_list' => $jsApiParameters
                ];
            }
        }

        return $this->success($return_data);

    }

    /**
     * @Desc: 获取秒杀下单信息
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/9/25 19:12
     */
    protected function getSeckillPayInfo($key, $data)
    {
        $arr = PackageSeckill::checkStatusAndStock($data['package_id']);

        if (!empty($arr['code'])) {

            $this->errorMsg($arr['msg']);
        }

        $data = array_merge($data, $arr);

        $data = PackageSeckill::getPayInfo($data);

        $data['init_price'] = $data['price'];

        $data['price'] = $data['seckill_price'];

        $count = PackageOrder::where([['seckill_id', '=', $data['seckill_id']], ['is_seckill', '=', 1], ['status', '>', -1], ['user_id', '=', $this->getUserId()]])->count();

        if ($count >= $data['limit'] || ($count + $data['num']) > $data['limit']) {

            decCache($key, 1, $this->_uniacid);

            $this->errorMsg('加购数量超过限购数量，不可下单');
        }

        $arr = [
            'integral' => 0,
            'integral_to_money' => 0,
            'is_integral' => 0
        ];

        $data = array_merge($data, $arr);

        return $data;
    }

    /**
     * @Desc: 订单列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/27 10:47
     */
    public function orderList()
    {
        $pay_config = [
            1 => $this->payConfig($this->_uniacid, $this->is_app),
            2 => [],
            3 => $this->payAliConfig()
        ];
        PackageOrder::chancel($this->_uniacid, $pay_config);

        $status = request()->param('status', 0);
        $limit = request()->param('limit', 10);

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.is_del', '=', 0],
            ['a.user_id', '=', $this->getUserId()],
            ['a.status', '>', 0]
        ];

        if (!empty($status)) {

            $where[] = ['a.status', '=', $status];

            if ($status == 3) {

                $where[] = ['a.is_comment', '=', 0];
            }
        }

        $data = PackageOrder::getList($where, $limit);

        return $this->success($data);
    }

    /**
     * @Desc: 重新支付
     * @return mixed
     * @throws \WxPayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/27 11:46
     */
    public function rePayOrder()
    {
        $order_id = request()->param('order_id', '');

        if (empty($order_id)) {

            $this->errorMsg('请选择订单');
        }

        $order = PackageOrder::find($order_id);

        if ($order['status'] != 1 || $order['is_del'] == 1) {

            $this->errorMsg('此订单不可支付');
        }

        $true_package_price = $order['true_package_price'];


        if ($true_package_price <= 0) {
            $data = [
                'total_money' => 0,
                'out_trade_no' => $order['order_code'],
                'transaction_id' => $order['order_code']
            ];

            PackageOrder::orderResult($data);

            $return_data = [
                'is_pay' => 1,
                'order_id' => $order_id
            ];
        } else {

            //余额支付
            if ($order['pay_model'] == 2) {

                $user_model = new User();

                $user_balance = $user_model->where(['id' => $this->getUserId()])->value('balance');

                if ($user_balance < $true_package_price) {

                    $this->errorMsg('余额不足');
                }

                $arr = [
                    'total_money' => $true_package_price,
                    'out_trade_no' => $order['order_code'],
                    'transaction_id' => $order['order_code']
                ];
                PackageOrder::orderResult($arr);
                $return_data = [
                    'is_pay' => 1,
                    'order_id' => $order_id
                ];
            } elseif ($order['pay_model'] == 1) {
                //虚拟支付开启时改走虚拟支付
                $virtualParams = \app\virtualpay\library\VirtualPayService::tryCreate($this->_uniacid, $this->getUserInfo()['openid'], 'packageOrder', $order['order_code'], $true_package_price);
                if (!empty($virtualParams)) {
                    $jsApiParameters = $virtualParams;
                } else {
                    $pay_controller = new \app\shop\controller\IndexWxPay($this->app);
                    //支付
                    $jsApiParameters = $pay_controller->createWeixinPay($this->payConfig($this->_uniacid, $this->is_app), $this->getUserInfo()['openid'], $this->_uniacid, "购买套餐", ['type' => 'packageOrder', 'out_trade_no' => $order['order_code']], $true_package_price);
                }

                $return_data = [
                    'is_pay' => 0,
                    'pay_model' => 1,
                    'order_id' => $order_id,
                    'pay_list' => $jsApiParameters
                ];
            } elseif ($order['pay_model'] == 3) {
                $pay = new IndexAliPay($this->app);


                $params = [
                    'order_code' => $order['order_code'],
                    'price' => $true_package_price,
                    'body' => '购买套餐',
                    'passback_params' => ['type' => 'packageOrder', 'uniacid' => $this->_uniacid],
                    'is_app' => $this->is_app
                ];
                $jsApiParameters = $pay->aliPay($params);
                $return_data = [
                    'is_pay' => 0,
                    'pay_model' => 3,
                    'order_id' => $order_id,
                    'pay_list' => $jsApiParameters
                ];
            }
        }

        return $this->success($return_data);
    }

    /**
     * @Desc: 删除订单
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/27 13:48
     */
    public function delOrder()
    {
        $order_id = request()->param('order_id', '');

        if (empty($order_id)) {

            $this->errorMsg('请选择订单');
        }

        $order = PackageOrder::find($order_id);

        if (!in_array($order['status'], [-1, 1, 3])) {

            $this->errorMsg('只有取消、待支付、完成的订单才能删除');
        }


        $update = ['is_del' => 1];

        if ($order['status'] == 1) {

            $update['status'] = -1;
        }

        if ($order['is_seckill'] == 1 && $order['status'] == 1) {

            PackageSeckill::updateSale($order['seckill_id'], $order['num'], 2);
        }

        $res = PackageOrder::update($update, ['id' => $order_id]);

        return $this->success($res);
    }

    /**
     * @Desc: 订单详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/27 14:34
     */
    public function orderInfo()
    {
        $order_id = request()->param('order_id', '');

        if (empty($order_id)) {

            $this->errorMsg('请选择订单');
        }

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.id', '=', $order_id]
        ];

        $data = PackageOrder::getInfo($where);

        return $this->success($data);
    }

    /**
     * @Desc: 申请退款
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/29 17:55
     */
    public function applyRefund()
    {
        $data = request()->only(['order_id', 'num', 'text']);

        if (empty($data['order_id'])) {

            $this->errorMsg('请选择订单');
        }

        if (empty($data['num'])) {

            $this->errorMsg('请选择数量');
        }

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['id', '=', $data['order_id']],
            ['user_id', '=', $this->getUserId()],

        ];

        $order = PackageOrder::getFirst($where);

        if (empty($order)) {

            $this->errorMsg('订单不存在');
        }

        if ($order['end_time'] < time() || $order['status'] != 2) {

            $this->errorMsg('此订单不可申请退款');
        }


        if ($data['num'] > $order['can_refund_num']) {

            $this->errorMsg('申请退款数量超过可用数量');
        }

        $data['uniacid'] = $this->_uniacid;
        $data['user_id'] = $this->getUserId();

        $pay_config = [
            1 => $this->payConfig($this->_uniacid, $this->is_app),
            2 => [],
            3 => $this->payAliConfig()
        ];

        $code = PackageOrder::applyRefund($data, $pay_config);
        if ($code['code'] == 1) {

            $this->errorMsg($code['msg']);
        }
        return $this->success('');
    }

    /**
     * @Desc: 订单评价
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/28 11:47
     */
    public function addComment()
    {
        $data = request()->only(['order_id', 'star', 'text', 'img', 'is_hide']);

        $order = PackageOrder::find($data['order_id']);

        if ($order['is_comment'] == 1) {

            $this->errorMsg('你已经评价过了');
        }

        $data['uniacid'] = $this->_uniacid;

        $data['user_id'] = $this->getUserId();

        $res = PackageOrderComment::comment($data);

        if (!$res) {

            $this->errorMsg('评价失败');
        }

        return $this->success($res);
    }

    /**
     * @Desc: 退款订单列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/30 14:02
     */
    public function refundList()
    {
        $limit = request()->param('limit', 10);

        $where = [

            ['a.status', '>', '-1'],
            ['a.is_del', '=', '0'],
            ['a.uniacid', '=', $this->_uniacid],
            ['a.user_id', '=', $this->getUserId()],
        ];

        $data = PackageOrderRefund::getList($where, $limit);

        return $this->success($data);
    }

    /**
     * @Desc: 退款订单详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/30 14:57
     */
    public function refundInfo()
    {
        $id = request()->param('id', '');

        $data = PackageOrderRefund::getInfo($id);

        return $this->success($data);
    }

    /**
     * @Desc: 取消退款
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/30 16:25
     */
    public function refundCancel()
    {
        $id = request()->param('id', '');

        $code = PackageOrderRefund::cancel($id);

        if ($code['code'] == 1) {

            $this->errorMsg($code['msg']);
        }

        return $this->success('');
    }

    /**
     * @Desc: 删除退款
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/30 16:50
     */
    public function refundDel()
    {
        $id = request()->param('id', '');

        $refund = PackageOrderRefund::find($id);

        if ($refund['status'] == 1) {

            $this->errorMsg('申请中的订单，不可删除');
        }

        if ($refund['status'] == -1) {

            $this->errorMsg('已取消');
        }

        $res = PackageOrderRefund::update(['is_del' => 1], ['id' => $id]);

        PackageOrderRefundGoods::where('refund_id', $id)->update(['status' => -1]);

        return $this->success($res);
    }

    /**
     * @Desc: 订单数量
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/1 14:14
     */
    public function orderCount()
    {
        if (!$this->getUserId()) {

            $data = [
                'status_1' => 0,
                'status_2' => 0,
                'status_3' => 0,
                'refund_count' => 0
            ];

            return $this->success($data);
        }

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['is_del', '=', 0],
            ['user_id', '=', $this->getUserId()],
            ['is_comment', '=', 0],
            ['status', '>', 0]
        ];

        $refund_where = [
            ['uniacid', '=', $this->_uniacid],
            ['is_del', '=', 0],
            ['user_id', '=', $this->getUserId()],
            ['status', '=', 1]
        ];

        $status_data = PackageOrder::getCountByList($where, $refund_where);

        return $this->success($status_data);
    }

    /**
     * @Desc: 虚拟电话
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/1 15:49
     */
    public function getVirtualPhone()
    {

        $input = $this->_param;

        $type = !empty($input['order_id']) ? 1 : 2;

        $order = PackageOrder::getVirtualPhoneData($input['order_id'], $type);

        $called = new \app\virtual\model\Config();

        $res = $called->getVirtual($order, 2, 3);

        return $this->success($res);
    }
}