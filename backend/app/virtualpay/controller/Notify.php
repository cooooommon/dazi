<?php
declare (strict_types = 1);

namespace app\virtualpay\controller;

use app\BaseController;
use app\virtualpay\library\VirtualPayService;
use think\facade\Db;

/**
 * 小程序虚拟支付平台推送接收
 *
 * MP 后台【虚拟支付 → 基本配置 → 基础配置 → 发货推送配置】填写：
 *   https://域名/index.php/virtualpay/notify/receive
 * 由微信服务器直接调用，不走登录鉴权，按 Event 分发
 */
class Notify extends BaseController
{

    /**
     * 接收平台推送：xpay_goods_deliver_notify（发货）、xpay_refund_notify（退款）
     */
    public function receive ()
    {
        $xml  = file_get_contents('php://input');
        $data = simplexml_load_string((string)$xml);
        VirtualPayService::log(0, 'notify_raw', '', (string)$xml);
        if ($data === false) {
            return $this->xmlReply(-1, 'xml error');
        }
        $event      = isset($data->Event) ? (string)$data->Event : '';
        $outTradeNo = isset($data->OutTradeNo) ? (string)$data->OutTradeNo : '';
        $wxOrderId  = isset($data->WeChatPayInfo->MchOrderNo) ? (string)$data->WeChatPayInfo->MchOrderNo : '';
        $quantity   = isset($data->GoodsInfo->Quantity) ? intval($data->GoodsInfo->Quantity) : 0;
        if (empty($outTradeNo)) {
            return $this->xmlReply(-1, 'empty OutTradeNo');
        }
        if ($event == 'xpay_goods_deliver_notify' || $event == 'xpay_coin_pay_notify') {
            $order = Db::name('virtualpay_order')->where(['out_trade_no' => $outTradeNo])->find();
            if (empty($order)) {
                return $this->xmlReply(-1, 'order not found');
            }
            //不信任推送内容：主动调用微信 query_order 核实已支付后再发货
            $res = VirtualPayService::queryAndDeliver($order['uniacid'], $order);
            return $this->xmlReply($res ? 0 : -1, $res ? 'success' : 'verify failed');
        }
        if ($event == 'xpay_refund_notify') {
            $order = Db::name('virtualpay_order')->where(['out_trade_no' => $outTradeNo])->find();
            if (empty($order) || intval($order['status']) !== 1) {
                return $this->xmlReply(0, 'ignore');
            }
            //已记录平台单号时必须与推送一致，避免伪造退款
            if (!empty($order['wx_order_id'])) {
                if (empty($wxOrderId) || !hash_equals((string)$order['wx_order_id'], (string)$wxOrderId)) {
                    return $this->xmlReply(-1, 'order mismatch');
                }
            }
            //退款推送：仅标记虚拟支付单状态，业务侧权益回退需人工处理
            Db::name('virtualpay_order')->where(['out_trade_no' => $outTradeNo, 'status' => 1])
                ->update(['status' => 2, 'update_time' => time()]);
            VirtualPayService::log(0, 'refund_notify', $outTradeNo, (string)$wxOrderId);
            return $this->xmlReply(0, 'success');
        }
        return $this->xmlReply(0, 'ignore event ' . $event);
    }

    /**
     * 平台要求的 XML 应答格式
     */
    protected function xmlReply ($code, $msg)
    {
        $msg = htmlspecialchars((string)$msg);
        return \think\Response::create(
            "<xml><ErrCode>{$code}</ErrCode><ErrMsg><![CDATA[{$msg}]]></ErrMsg></xml>",
            'html', 200, ['Content-Type' => 'text/xml']
        );
    }

}
