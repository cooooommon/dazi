<?php


namespace app\massage\controller;


use app\ApiRest;
use app\massage\model\Coach;
use app\massage\model\DemandOrder;
use app\massage\model\DemandOrderApply;
use think\App;

class IndexDemand extends ApiRest
{

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

//    protected $middleware = [
//        'user_login'
//    ];

    /**
     * 列表
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList()
    {
        $pay_config = [
            1 => $this->payAliConfig(),
            2 => $this->payConfig($this->_uniacid, $this->is_app),
            3 => []
        ];
        //取消到期未接单订单
        DemandOrder::cancelOrder($this->_uniacid, $pay_config, $this->getUserId());
        $page = $this->request->param('limit', 10);
        $status = $this->request->param('status', '');
        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.user_id', '=', $this->getUserId()],
            ['is_pay', '=', 1]
        ];
        if (!empty($status)) {
            switch ($status) {
                case 1:
                    $where[] = ['a.status', '=', 2];
                    break;
                case 2:
                    $where[] = ['a.status', '=', 3];
                    break;
                case 3:
                    $where[] = ['a.status', '=', 4];
                    break;
                case 4:
                    $where[] = ['a.is_refund', '>', 0];
                    break;
            }
        }
        $data = DemandOrder::getList($where, $page);
        return $this->success($data);
    }

    /**
     * 查看报名
     * @return mixed
     */
    public function seeApply()
    {
        $limit = $this->request->param('limit', 10);
        $order_id = $this->request->param('order_id', '');
        if (empty($order_id)) {
            return $this->error('订单不存在');
        }
        $data = DemandOrderApply::seeApply([['a.order_id', '=', $order_id], ['b.status', '<>', -1]], $order_id, $limit);
        return $this->success($data);
    }

    /**
     * 选择向导
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function chooseCoach()
    {
        $data = $this->request->only(['order_id', 'coach_id', 'status']);
        if (empty($data['order_id']) || empty($data['coach_id'])) {
            return $this->error('请选择向导');
        }
        $order = DemandOrder::getInfo(['id' => $data['order_id']]);
        if ($order['status'] != 2 || $order['start_time'] < time()) {
            return $this->error('此订单不可接单');
        }

        if ($order['is_refund'] == 1) {

            return $this->error('申请退款中,不可邀约');
        }

        $count = DemandOrderApply::num(['order_id' => $data['order_id'], 'coach_id' => $data['coach_id'], 'status' => 1]);
        if (empty($count)) {
            return $this->error('不可接单');
        }
        $coach = Coach::where('id', $data['coach_id'])->find();
        if ($coach['status'] != 2) {
            return $this->error('不可接单');
        }
        $data['uniacid'] = $this->_uniacid;
        $code = DemandOrder::choose($data);
        if ($code['code'] == 1) {
            return $this->error($code['msg']);
        }

        //消息通知
        DemandOrder::chooseSendMsg($data);

        return $this->success('');
    }

    /**
     * 取消邀约订单
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function cancel()
    {
        $id = $this->request->param('id', '');
        if (empty($id)) {
            return $this->error('订单不存在');
        }
        $order = DemandOrder::getInfo(['id' => $id, 'user_id' => $this->getUserId(), 'uniacid' => $this->_uniacid, 'is_pay' => 1]);
        if (empty($order)) {
            return $this->error('订单不存在');
        }
        if (!in_array($order['status'], [1, 2, 3]) || $order['start_time'] < time() || in_array($order['is_refund'], [1, 2])) {
            return $this->error('此订单不可取消');
        }
//        if ($order['pay_type'] = 1) {
//            $pay_config = $this->payAliConfig();
//        } elseif ($order['pay_type'] == 2) {
//            $pay_config = $this->payConfig(1);
//        } elseif ($order['pay_type'] == 3) {
//            $pay_config = [];
//        }
//        $code = DemandOrder::cancel($order, $pay_config);
        $res = DemandOrder::update(['is_refund' => 1], ['id' => $id]);
        if ($res === false) {
            return $this->error('取消失败');
        }

        if ($order['coach_id'] > 0) {

            $data = [
                'coach_id' => $order['coach_id'],
                'uniacid' => $order['uniacid'],
                'order_code' => $order['order_code'],
                'money' => $order['price'],
                'time' => date('Y年m月d日 H:i', $order['create_time']),
                'status' => '申请中',
                'order_id' => $order['id']
            ];
            Coach::refundOrderSendMsg($data, 2);
        }

        return $this->success('');
    }

    /**
     * 完成
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function complete()
    {
        $id = $this->request->param('id', '');
        if (empty($id)) {
            return $this->error('订单不存在');
        }
        $order = DemandOrder::getInfo(['id' => $id, 'user_id' => $this->getUserId(), 'uniacid' => $this->_uniacid, 'is_pay' => 1]);
        if (empty($order)) {
            return $this->error('订单不存在');
        }
        if ($order['status'] != 3 || in_array($order['is_refund'], [1, 2])) {
            return $this->error('此订单不可完成');
        }
        $res = DemandOrder::complete($id);
        if ($res) {
            return $this->success('');
        }
        return $this->error('操作失败');
    }

    /**
     * 订单详情
     * @return \think\Response
     */
    public function orderInfo()
    {
        $id = $this->request->param('id', 0);
        if (empty($id)) {
            return $this->error('订单不存在');
        }
        $order = DemandOrder::getDetail(['a.id' => $id]);
        if (empty($order)) {
            return $this->error('订单不存在');
        }
        $cap_dis[] = ['user_id', '=', $this->getUserId()];
        $cap_dis[] = ['status', 'in', [2, 3]];
        $coach_id = Coach::where($cap_dis)->value('id');
        $order['apply_status'] = 0;
        if ($coach_id) {
            $apply = DemandOrderApply::where(['order_id' => $id, 'coach_id' => $coach_id])->find();
            if (!empty($apply)) {
                $order['apply_status'] = $apply['status'];
            }
        }
        return $this->success($order);
    }
}