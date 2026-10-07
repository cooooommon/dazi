<?php
declare (strict_types = 1);

namespace app\virtualpay\controller;

use app\ApiRest;
use app\virtualpay\library\VirtualPayService;
use think\App;
use think\facade\Db;

/**
 * 虚拟支付用户端接口
 */
class Index extends ApiRest
{

    protected $app;

    public function __construct ( App $app )
    {
        $this->app = $app;
    }


    /**
     * 前端支付后轮询订单状态；未支付时顺带做一次查单兜底
     */
    public function orderStatus ()
    {
        $out_trade_no = $this->request->param('out_trade_no', '');

        if (empty($out_trade_no)) {

            $this->errorMsg('缺少订单号');
        }

        $order = Db::name('virtualpay_order')->where(['out_trade_no' => $out_trade_no])->find();

        if (empty($order)) {

            $this->errorMsg('订单不存在');
        }

        if ($order['status'] == 0 && $order['create_time'] > time() - 86400) {

            VirtualPayService::queryAndDeliver($order['uniacid'], $order);

            $order = Db::name('virtualpay_order')->where(['out_trade_no' => $out_trade_no])->find();
        }

        return $this->success([
            'status'     => intval($order['status']),
            'order_code' => $order['order_code'],
            'type'       => $order['type'],
            'pay_time'   => $order['pay_time'],
        ]);
    }


    /**
     * 批量兜底查单：补发推送丢失的订单（可挂定时任务调用）
     */
    public function checkPending ()
    {
        $orders = Db::name('virtualpay_order')
            ->where(['status' => 0])
            ->where('create_time', '>', time() - 86400)
            ->limit(20)
            ->select()
            ->toArray();

        $delivered = 0;

        foreach ($orders as $order) {

            if (VirtualPayService::queryAndDeliver($order['uniacid'], $order)) {

                $delivered++;
            }
        }

        return $this->success(['pending' => count($orders), 'delivered' => $delivered]);
    }

}
