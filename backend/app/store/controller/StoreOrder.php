<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/27
 * Time: 15:19
 * docs:
 */

namespace app\store\controller;

use app\ApiRest;
use app\massage\model\Commission;
use app\massage\model\Config;
use app\massage\model\StoreApply;
use app\massage\model\Wallet;
use app\store\model\PackageOrder;
use app\store\model\PackageOrderComment;
use app\store\model\PackageOrderRefund;
use think\App;
use think\facade\Db;

class StoreOrder extends ApiRest
{
    protected $store;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $data = StoreApply::getInfo([['user_id', '=', $this->getUserId()], ['status', '>', -1]]);

        if (!in_array($data['status'], [2, 3])) {

            $this->errorMsg('你还不是商家');
        }

        $this->store = $data;
    }

    /**
     * @Desc: 订单详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/27 15:24
     */
    public function orderInfo()
    {
        $order_id = request()->param('order_id', '');

        if (empty($order_id)) {

            $this->errorMsg('请选择订单');
        }

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.id', '=', $order_id],
            ['a.store_id', '=', $this->store['id']]
        ];

        $data = PackageOrder::getInfo($where);

        if (empty($data)) {

            $this->errorMsg('此订单不属于您的门店');
        }

        return $this->success($data);
    }

    /**
     * @Desc: 订单列表
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/27 15:46
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
            ['a.store_id', '=', $this->store['id']],
            ['a.pay_time', '>', 0]
        ];

        if (!empty($status)) {

            $where[] = ['a.status', '=', $status];

            if ($status == 3) {

                $where[] = ['a.is_comment', '=', 1];
            }
        }

        $data = PackageOrder::getList($where, $limit);

        return $this->success($data);
    }

    /**
     * @Desc: 核销订单
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/27 17:18
     */
    public function hxOrder()
    {
        $order_id = request()->param('order_id', '');
        $num = request()->param('num', 0);

        if (empty($order_id)) {

            $this->errorMsg('请选择订单');
        }

        if (empty($num)) {

            $this->errorMsg('请选择数量');
        }

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.id', '=', $order_id],
            ['a.store_id', '=', $this->store['id']]
        ];

        $data = PackageOrder::getInfo($where);

        if (empty($data)) {

            $this->errorMsg('此订单不属于您的门店');
        }

        if ($data['end_time'] < time() || $data['status'] != 2) {

            $this->errorMsg('此订单不可核销');
        }

        if ($num > $data['can_refund_num']) {

            $this->errorMsg('核销数超过可用数量');
        }

        $data = [
            'order_id' => $order_id,
            'num' => $num,
            'uniacid' => $this->_uniacid,
            'store_id' => $this->store['id']
        ];

        $code = PackageOrder::hxOrder($data);

        if ($code['code'] == 1) {

            $this->errorMsg($code['msg']);
        }

        return $this->success('');
    }

    /**
     * @Desc: 门店佣金信息
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/28 17:21
     */
    public function cashData()
    {
        //可提现
        $data['cash'] = $this->store['cash'];
        //总收入
        $data['total_cash'] = $this->store['total_cash'];

        $wallet_model = new Wallet();
        //累计提现金额
        $data['extract_total_price'] = $wallet_model->capCash($this->store['id'], 2, 6);
        //提现中金额
        $data['extract_wallet_price'] = $wallet_model->capCash($this->store['id'], 1, 6);


        $dis = [
            'top_id' => $this->store['id'],
            'type' => 14,
            'status' => 1
        ];

        //未入账金额
        $comm_model = new Commission();
        $data['unrecorded_cash'] = $comm_model->where($dis)->sum('cash');

        //累计抽成金额
        $price = PackageOrder::field(Db::raw("SUM(`share_cash` + `company_cash`) as price"))->where([['store_id', '=', $this->store['id']], ['status', '=', 3]])->find();
        $data['extract_give_price'] = $price['price'] ?? 0;

        //税点
        $data['tax_point'] = getConfigSetting($this->_uniacid, 'tax_point');

        return $this->success($data);

    }

    /**
     * @Desc: 申请提现
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/28 17:54
     */
    public function applyWallet()
    {

        $input = $this->_input;

        $cash_mini = Config::where(['uniacid' => $this->_uniacid])->value('cash_mini');

        if (empty($input['apply_price']) || $input['apply_price'] < $cash_mini || $input['apply_price'] < 0.01) {

            $this->errorMsg('提现金额最低' . $cash_mini > 0 ? $cash_mini : 0.01 . '元');
        }

        if ($input['apply_price'] > $this->store['cash']) {

            $this->errorMsg('余额不足');
        }

        //获取税点
        $tax_point = getConfigSetting($this->_uniacid, 'tax_point');

        $balance = 100 - $tax_point;

        $key = 'store_wallet' . $this->getUserId();
        //加一个锁防止重复提交
        incCache($key, 1, $this->_uniacid);

        $value = getCache($key, $this->_uniacid);

        if ($value != 1) {

            delCache($key, $this->_uniacid);

            $this->errorMsg('网络错误，请刷新重试');

        }

        Db::startTrans();
        //减佣金
        $res = StoreApply::update(['cash' => $this->store['cash'] - $input['apply_price']], ['id' => $this->store['id']]);

        if ($res === false) {

            Db::rollback();
            //减掉
            delCache($key, $this->_uniacid);

            $this->errorMsg('申请失败');
        }

        $insert = [

            'uniacid' => $this->_uniacid,

            'user_id' => $this->getUserId(),

            'coach_id' => $this->store['id'],

            'admin_id' => 0,

            'total_price' => $input['apply_price'],

            'balance' => $balance,

            'apply_price' => round($input['apply_price'] * $balance / 100, 2),

            'service_price' => round($input['apply_price'] * $tax_point / 100, 2),

            'code' => orderCode(),

            'tax_point' => $tax_point,

            'text' => $input['text'],

            'type' => 6,

            'apply_transfer' => !empty($input['apply_transfer']) ? $input['apply_transfer'] : 0

        ];

        $wallet_model = new Wallet();
        //提交审核
        $res = $wallet_model->dataAdd($insert);

        if ($res != 1) {

            Db::rollback();
            //减掉
            delCache($key, $this->_uniacid);

            $this->errorMsg('申请失败');
        }

        Db::commit();
        //减掉
        decCache($key, 1, $this->_uniacid);

        return $this->success($res);

    }


    /**
     * @Desc: 提现流水
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/28 17:54
     */
    public function walletList()
    {

        $wallet_model = new Wallet();

        $input = $this->_param;

        $dis = [

            'coach_id' => $this->store['id']
        ];

        if (!empty($input['status'])) {

            $dis['status'] = $input['status'];
        }

        $dis['type'] = 6;
        //提现记录
        $data = $wallet_model->dataList($dis, 10);

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$v) {

                $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            }
        }
        //累计提现
        $data['extract_total_price'] = $wallet_model->capCash($this->store['id'], 2, 6);

        return $this->success($data);

    }

    /**
     * @Desc: 售后订单列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/1 10:24
     */
    public function refundList()
    {
        $limit = request()->param('limit', 10);

        $where = [

            ['a.status', '>', '-1'],
            ['a.uniacid', '=', $this->_uniacid],
            ['a.store_id', '=', $this->store['id']],
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
     * @Desc: 审核退款
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/1 11:47
     */
    public function refundCheck()
    {
        $data = request()->only(['id', 'status']);

        $pay_config = [
            1 => $this->payConfig($this->_uniacid, $this->is_app),
            2 => [],
            3 => $this->payAliConfig()
        ];

        $code = PackageOrderRefund::refundCheck($data, $pay_config);

        if ($code['code'] == 1) {

            $this->errorMsg($code['msg']);
        }

        return $this->success('');
    }

    /**
     * @Desc: 订单数量
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/1 14:18
     */
    public function orderCount()
    {

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['store_id', '=', $this->store['id']],
            ['status', '>', 0]
        ];

        $refund_where = [
            ['uniacid', '=', $this->_uniacid],
            ['store_id', '=', $this->store['id']],
            ['status', '=', 1]
        ];

        $status_data = PackageOrder::getCountByList($where, $refund_where);

        return $this->success($status_data);
    }

    /**
     * @Desc: 查看评论
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/1 14:43
     */
    public function seeComment()
    {
        $order_id = request()->param('order_id', '');

        $order = PackageOrder::getFirst(['id' => $order_id]);

        if (empty($order) || $order['is_comment'] == 0) {

            $this->errorMsg('此订单未评价');
        }

        $data = PackageOrderComment::getInfo(['a.order_id' => $order_id]);

        return $this->success($data);
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

        $res = $called->getVirtual($order, 1, 3);

        return $this->success($res);
    }
}