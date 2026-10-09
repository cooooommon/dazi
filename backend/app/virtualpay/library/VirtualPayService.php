<?php
declare (strict_types = 1);

namespace app\virtualpay\library;

use think\facade\Db;

/**
 * 小程序虚拟支付（代币充值模式 short_series_coin）
 *
 * 配置表  ims_virtualpay_config：offer_id / app_key / enabled / coin_rate（1元=coin_rate个代币，与 MP 后台代币配置一致）
 * 订单表  ims_virtualpay_order：虚拟支付单（以平台单号 wx_order_id 做幂等发货）
 *
 * 所有支付（充值/会员/订单等）统一拉起代币充值，signData 不含 productId/goodsPrice；
 * 代币由平台自动入到用户微信代币账户，发货侧复用传统支付回调的同一套业务模型 orderResult：
 * 充值入账户余额，其余类型直接完成对应业务发货。金额须能折算为整数代币，否则提示走余额支付。
 */
class VirtualPayService
{


    /**
     * 读取虚拟支付配置
     */
    public static function getConfig ($uniacid)
    {
        return Db::name('virtualpay_config')->where(['uniacid' => $uniacid])->find();
    }


    /**
     * 虚拟支付是否已启用，启用返回配置数组，否则返回 false
     */
    public static function enabled ($uniacid)
    {
        $config = self::getConfig($uniacid);

        if (empty($config) || empty($config['enabled']) || empty($config['offer_id']) || empty($config['app_key'])) {

            return false;
        }

        return $config;
    }


    /**
     * 下单入口：虚拟支付未开启时返回 null（走原微信支付），开启时返回前端 payData
     *
     * @param int    $uniacid    站点 id
     * @param string $openid     用户 openid
     * @param string $type       业务类型（Balance/Massage/MassageUp/demoOrder/manyDemoOrder/memberOrder/packageOrder）
     * @param string $order_code 业务系统订单号
     * @param float  $price      订单金额（元）
     * @param int    $user_id    用户 id（可空）
     * @return array|null
     */
    public static function tryCreate ($uniacid, $openid, $type, $order_code, $price, $user_id = 0)
    {
        $config = self::enabled($uniacid);

        //未开启虚拟支付，走原有支付
        if ($config === false) {

            return null;
        }

        $price_fen = intval(round(floatval($price) * 100));

        if ($price_fen <= 0) {

            self::fail('虚拟支付下单金额错误');
        }

        //coin_rate：后台代币兑换比例（1 元 = coin_rate 个代币），需与 MP 后台【代币配置】保持一致
        $coin_rate = intval($config['coin_rate']);

        if ($coin_rate <= 0) {

            self::fail('请先在虚拟支付配置中设置代币兑换比例 coin_rate（与 MP 后台代币配置一致，1元=1代币则填1）');
        }

        $numerator = $price_fen * $coin_rate;

        if ($numerator % 100 != 0) {

            self::fail("金额 " . number_format($price_fen / 100, 2, '.', '') . " 元按当前兑换比例（1元={$coin_rate}代币）无法折算为整数代币，请改用「余额支付」（先充值）或调整后台兑换比例");
        }

        $buy_quantity = intdiv($numerator, 100);

        //同一业务单此前拉起过的虚拟支付单：先查单结算兜底；
        //若已支付成功（业务单已发货）则拒绝再次拉起，防止重复扣款
        $old_orders = Db::name('virtualpay_order')
            ->where(['uniacid' => $uniacid, 'order_code' => $order_code, 'type' => $type])
            ->order('id', 'desc')
            ->select()
            ->toArray();
        foreach ($old_orders as $old) {
            if ($old['status'] == 0) {
                self::queryAndDeliver($uniacid, $old);
                $old['status'] = intval(Db::name('virtualpay_order')->where(['id' => $old['id']])->value('status'));
            }
            if ($old['status'] == 1) {
                self::fail('订单已支付，请勿重复支付');
            }
        }

        if (empty($openid)) {
            self::fail('虚拟支付缺少用户 openid');
        }

        $session_key = Db::name('massage_service_user_list')
            ->where(['openid' => $openid, 'uniacid' => $uniacid])
            ->value('session_key');

        if (empty($session_key)) {
            self::fail('支付会话已失效，请退出小程序重新进入后再支付');
        }

        $out_trade_no = 'VP' . date('YmdHis') . mt_rand(1000, 9999);

        $sign_arr = [
            'offerId'      => $config['offer_id'],
            'buyQuantity'  => $buy_quantity,
            'env'          => intval($config['env']),
            'currencyType' => 'CNY',
            'outTradeNo'   => $out_trade_no,
            'attach'       => json_encode(['type' => $type, 'order_code' => $order_code]),
        ];

        //signData 为实际发往微信的字符串，签名必须与之一致
        $signData  = json_encode($sign_arr, JSON_UNESCAPED_UNICODE);
        $paySig    = hash_hmac('sha256', 'requestVirtualPayment&' . $signData, $config['app_key']);
        $signature = hash_hmac('sha256', $signData, $session_key);

        Db::name('virtualpay_order')->insert([
            'uniacid'      => $uniacid,
            'out_trade_no' => $out_trade_no,
            'order_code'   => $order_code,
            'type'         => $type,
            'openid'       => $openid,
            'user_id'      => intval($user_id),
            'product_id'   => '',
            'goods_price'  => 0,
            'buy_quantity' => $buy_quantity,
            'total_fee'    => $price_fen,
            'wx_order_id'  => '',
            'status'       => 0,
            'create_time'  => time(),
            'update_time'  => time(),
        ]);

        self::log($uniacid, 'create', $out_trade_no, $signData);

        return [
            'virtual_pay'  => 1,
            'mode'         => 'short_series_coin',
            'signData'     => $signData,
            'paySig'       => $paySig,
            'signature'    => $signature,
            'out_trade_no' => $out_trade_no,
        ];
    }


    /**
     * 发货（幂等）：以虚拟支付单状态占位防止并发重复发货
     *
     * @param string $outTradeNo 虚拟支付业务单号 outTradeNo
     * @param string $wxOrderId  平台单号（推送 WeChatPayInfo.MchOrderNo / 查单返回）
     * @param int    $quantity   购买数量（推送 GoodsInfo.Quantity）
     * @param string $source     来源标识（notify/query）
     * @return bool true=已发货或已处理，false=处理失败（平台会重试）
     */
    public static function deliver ($outTradeNo, $wxOrderId = '', $quantity = 0, $source = '')
    {
        $order = Db::name('virtualpay_order')->where(['out_trade_no' => $outTradeNo])->find();

        if (empty($order)) {

            self::log(0, 'deliver_order_not_found', $outTradeNo, (string)$wxOrderId);

            return false;
        }

        //同一订单重复通知只发一次货
        if ($order['status'] == 1) {

            self::log($order['uniacid'], 'deliver_idempotent', $outTradeNo, (string)$wxOrderId);

            return true;
        }

        $update = ['status' => 1, 'pay_time' => time(), 'update_time' => time()];

        if (!empty($wxOrderId)) $update['wx_order_id'] = (string)$wxOrderId;

        if (!empty($quantity)) $update['buy_quantity'] = intval($quantity);

        //原子占位：并发通知只有一个进程能占位成功
        $claimed = Db::name('virtualpay_order')
            ->where(['out_trade_no' => $outTradeNo, 'status' => 0])
            ->update($update);

        if (!$claimed) {

            return Db::name('virtualpay_order')->where(['out_trade_no' => $outTradeNo])->value('status') == 1;
        }

        try {

            self::dispatch($order['uniacid'], $order['type'], $order['order_code'], $order['total_fee'],
                !empty($wxOrderId) ? (string)$wxOrderId : $outTradeNo);

        } catch (\Throwable $e) {

            //发货失败回滚状态，返回失败让平台重试（最多 15 次）
            Db::name('virtualpay_order')->where(['out_trade_no' => $outTradeNo])
                ->update(['status' => 0, 'update_time' => time()]);

            self::log($order['uniacid'], 'deliver_error:' . $e->getMessage(), $outTradeNo, (string)$wxOrderId);

            return false;
        }

        self::log($order['uniacid'], 'deliver_ok:' . $source, $outTradeNo, (string)$wxOrderId);

        return true;
    }


    /**
     * 按业务类型分发到对应模型的 orderResult（与传统支付回调同一条发货链路）
     */
    protected static function dispatch ($uniacid, $type, $order_code, $total_fee, $transaction_id)
    {
        switch ($type) {

            case 'Balance':
                (new \app\massage\model\BalanceOrder())->orderResult($order_code, $transaction_id);
                break;

            case 'Massage':
                (new \app\massage\model\Order())->orderResult($order_code, $transaction_id);
                break;

            case 'MassageUp':
                (new \app\massage\model\UpOrderList())->orderResult($order_code, $transaction_id);
                break;

            case 'demoOrder':
                \app\massage\model\DemandOrder::orderResult([
                    'total_money'    => $total_fee / 100,
                    'out_trade_no'   => $order_code,
                    'transaction_id' => $transaction_id
                ]);
                break;

            case 'manyDemoOrder':
                \app\massage\model\ManyDemandOrder::orderResult([
                    'total_money'    => $total_fee / 100,
                    'out_trade_no'   => $order_code,
                    'transaction_id' => $transaction_id
                ]);
                break;

            case 'memberOrder':
                \app\member\model\MemberOrder::orderResult([
                    'total_money'    => $total_fee / 100,
                    'out_trade_no'   => $order_code,
                    'transaction_id' => $transaction_id
                ]);
                break;

            case 'packageOrder':
                \app\store\model\PackageOrder::orderResult([
                    'total_money'    => $total_fee / 100,
                    'out_trade_no'   => $order_code,
                    'transaction_id' => $transaction_id
                ]);
                break;

            default:
                throw new \Exception("未知的虚拟支付业务类型 {$type}");
        }
    }


    /**
     * 主动查单（query_order）并按需发货，用于发货推送丢失时的兜底
     * 响应原文会记录到 runtime/virtualpay.log，便于核对平台字段
     */
    public static function queryAndDeliver ($uniacid, $order)
    {
        if (empty($order) || $order['status'] == 1) {

            return true;
        }

        $config = self::enabled($uniacid);

        if ($config === false) {

            return false;
        }

        $body    = json_encode([
            'openid'   => $order['openid'],
            'env'      => intval($config['env']),
            'order_id' => $order['out_trade_no']
        ]);
        $pay_sig = hash_hmac('sha256', '/xpay/query_order&' . $body, $config['app_key']);
        $token   = longbingGetAccessToken($uniacid);

        if (empty($token)) {

            self::log($uniacid, 'query_no_token', $order['out_trade_no'], '');

            return false;
        }

        $url = 'https://api.weixin.qq.com/xpay/query_order?access_token=' . $token . '&pay_sig=' . $pay_sig;
        $res = self::httpPost($url, $body);

        self::log($uniacid, 'query_response', $order['out_trade_no'], (string)$res);

        $data = json_decode($res, true);

        if (empty($data) || !empty($data['errcode'])) {

            return false;
        }

        $wxOrder = !empty($data['order']) && is_array($data['order']) ? $data['order'] : $data;

        $wxOrderId = '';

        foreach (['wx_order_id', 'order_id', 'transaction_id'] as $key) {

            if (!empty($wxOrder[$key])) {

                $wxOrderId = (string)$wxOrder[$key];
                break;
            }
        }

        //已支付判定：现金单 status 2-已支付待发货 3-发货中 4-已发货；字段如有出入以 query_response 日志为准调整
        $status    = isset($wxOrder['status']) ? intval($wxOrder['status']) : -1;
        $paid_time = '';

        foreach (['paid_time', 'pay_time'] as $key) {

            if (!empty($wxOrder[$key])) {

                $paid_time = (string)$wxOrder[$key];
                break;
            }
        }

        if (empty($paid_time) && !in_array($status, [2, 3, 4])) {

            return false;
        }

        return self::deliver($order['out_trade_no'], $wxOrderId,
            !empty($wxOrder['quantity']) ? intval($wxOrder['quantity']) : 0, 'query');
    }


    /**
     * 错误中断（与 ApiRest::errorMsg 相同的响应行为）
     */
    public static function fail ($msg)
    {
        $response = \think\Response::create(['error' => $msg, 'code' => 400], 'json', 200);

        throw new \think\exception\HttpResponseException($response);
    }


    /**
     * 日志（排查签名/查单/发货问题的唯一依据）
     */
    public static function log ($uniacid, $action, $outTradeNo, $detail)
    {
        $line = date('Y-m-d H:i:s') . " [{$uniacid}] {$action} {$outTradeNo} {$detail}\n";

        @file_put_contents(runtime_path() . 'virtualpay.log', $line, FILE_APPEND | LOCK_EX);
    }


    /**
     * POST 请求
     */
    protected static function httpPost ($url, $body)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $res = curl_exec($ch);

        curl_close($ch);

        return $res ?: '';
    }

}
