<?php
declare(strict_types=1);

namespace app\shop\controller;

use app\ApiRest;
use app\massage\model\Config;
use app\massage\model\ManyDemandOrder;
use app\massage\model\RewardOrder;
use app\massage\model\UserVipOrder;
use app\member\model\MemberOrder;
use app\store\model\PackageOrder;
use log\LogUtils;
use think\App;
use think\facade\Db;

class IndexAliPay extends ApiRest
{

    // static protected $uniacid;

    public function __construct(App $app)
    {

    }


    /**
     * 查询订单
     * @return bool|mixed|\SimpleXMLElement
     * @throws \Exception
     */
    public function findOrder()
    {

        $pay_config = $this->payConfig();

        require_once EXTEND_PATH . 'alipay/aop/AopClient.php';

        require_once EXTEND_PATH . 'alipay/aop/request/AlipayTradeQueryRequest.php';

        $aop = new \AopClient ();

        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        // $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';

        $aop->appId = $pay_config['payment']['ali_appid'];

        $aop->rsaPrivateKey = $pay_config['payment']['ali_privatekey'];

        $aop->alipayrsaPublicKey = $pay_config['payment']['ali_publickey'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'UTF-8';
        $aop->format = 'json';
        $object = new \stdClass();
        $object->out_trade_no = '20150320010101001';
//$object->trade_no = '2014112611001004680073956707';
        $json = json_encode($object);
        $request = new \AlipayTradeQueryRequest();
        $request->setBizContent($json);

        $result = $aop->execute($request);

        return $result;
    }


    /**
     * 支付宝支付
     * @param $params
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function aliPay($params)
    {

        require_once EXTEND_PATH . 'alipay/aop/AopClient.php';

        require_once EXTEND_PATH . 'alipay/aop/request/AlipayTradeAppPayRequest.php';
        require_once EXTEND_PATH . 'alipay/aop/request/AlipayTradeWapPayRequest.php';

        $pay_config = $this->payConfig();

        $aop = new \AopClient ();

        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';

//        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';

        $aop->appId = $pay_config['payment']['ali_appid'];

        $aop->rsaPrivateKey = $pay_config['payment']['ali_privatekey'];;

        $aop->alipayrsaPublicKey = $pay_config['payment']['ali_publickey'];;

        $aop->apiVersion = '1.0';

        $aop->signType = 'RSA2';

        $aop->postCharset = 'UTF-8';

        $aop->format = 'json';
        $object = new \stdClass();

        $object->out_trade_no = $params['order_code'];

        $object->total_amount = $params['price'];

        $object->subject = $params['body'];

        $object->product_code = 'QUICK_WAP_WAY';

        $object->passback_params = urlencode($params['passback_params']);

        //$object->time_expire = date('Y-m-d H:i:s',time());

        $json = json_encode($object);

//        $request = new \AlipayTradeAppPayRequest();
        $request = $pay_config['is_app'] == 1 ? new \AlipayTradeAppPayRequest() : new \AlipayTradeWapPayRequest();


        $request->setNotifyUrl("https://" . $_SERVER['HTTP_HOST'] . '/index.php/shop/IndexAliPay/aliNotify');

        $request->setBizContent($json);

        $result = $pay_config['is_app'] == 1 ? $aop->sdkExecute($request) : $aop->pageExecute($request);

        return $result;

    }


    /**
     * 退款
     * @param $order_code
     * @param $price
     * @return array|bool|mixed|\SimpleXMLElement
     * @throws \Exception
     */
    public function aliRefund($order_code, $price)
    {

        require_once EXTEND_PATH . 'alipay/aop/AopClient.php';

        require_once EXTEND_PATH . 'alipay/aop/request/AlipayTradeRefundRequest.php';

        $pay_config = $this->payConfig();

        $aop = new \AopClient ();

        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';

        $aop->appId = $pay_config['payment']['ali_appid'];

        $aop->rsaPrivateKey = $pay_config['payment']['ali_privatekey'];;

        $aop->alipayrsaPublicKey = $pay_config['payment']['ali_publickey'];;

        $aop->apiVersion = '1.0';

        $aop->signType = 'RSA2';

        $aop->postCharset = 'UTF-8';

        $aop->format = 'json';

        $object = new \stdClass();

        $object->trade_no = $order_code;

        $object->refund_amount = $price;

        $object->out_request_no = 'HZ01RF001';

        $json = json_encode($object);

        $request = new \AlipayTradeRefundRequest();

        $request->setBizContent($json);

        $result = $aop->execute($request);

        return $result;

    }

    /**
     * 异步回调
     */
    public function aliNotify()
    {
        require_once EXTEND_PATH . 'alipay/aop/AopClient.php';

        $data = $_POST;

        if (empty($data)) {

            $data = $_GET;
        }

        LogUtils::log('支付宝回调参数：        ' . json_encode($data), 'alipay_notify', 'alipay_notify');

        $data = json_decode(json_encode($data), true);

        $arr_ = urldecode($data['passback_params']);

        $arr_ = explode('&', $arr_);

        $arr = [];

        foreach ($arr_ as $item) {

            $item = explode('=', $item);

            $arr[$item[0]] = $item[1];
        }

        $aop = new \AopClient;

        $pay_config = $this->payConfig();

        $aop->alipayrsaPublicKey = $pay_config[ 'payment' ][ 'ali_publickey' ];

        $flag = $aop->rsaCheckV1($data, NULL, "RSA2");
        if (!$flag) {
            echo 'fail';
            die;
        }
        if ($data['trade_status'] == 'TRADE_SUCCESS') {
            if (is_array($arr) && $arr['type'] == 'Balance') {
                $order_model = new \app\massage\model\BalanceOrder();
                $res = $order_model->orderResult($data['out_trade_no'], $data['trade_no']);
            } elseif (is_array($arr) && $arr['type'] == 'demoOrder') {
                $result = $data;
                $data = [
                    'total_money' => $result['total_amount'],
                    'out_trade_no' => $result['out_trade_no'],
                    'transaction_id' => $result['trade_no']
                ];
                $res = \app\massage\model\DemandOrder::orderResult($data);
            } elseif (is_array($arr) && $arr['type'] == 'vipOrder') {
//                $result = $data;
//                $data = [
//                    'total_money' => $result['total_amount'],
//                    'out_trade_no' => $result['out_trade_no'],
//                    'transaction_id' => $result['trade_no']
//                ];
//                $res = UserVipOrder::orderResult($data);
            } elseif (is_array($arr) && $arr['type'] == 'Massage') {
                $order_model = new \app\massage\model\Order();
                $res = $order_model->orderResult($arr['out_trade_no'], $data['trade_no']);
            } elseif (is_array($arr) && $arr['type'] == 'rewardOrder') {
//                $result = $data;
//                $data = [
//                    'total_money' => $result['total_amount'],
//                    'out_trade_no' => $result['out_trade_no'],
//                    'transaction_id' => $result['trade_no']
//                ];
//                $res = RewardOrder::orderResult($data);
            } elseif (is_array($arr) && $arr['type'] == 'packageOrder') {
                $result = $data;
                $data = [
                    'total_money' => $result['total_amount'],
                    'out_trade_no' => $result['out_trade_no'],
                    'transaction_id' => $result['trade_no']
                ];
                $res = PackageOrder::orderResult($data);
            } elseif (is_array($arr) && $arr['type'] == 'manyDemoOrder') {
                $result = $data;
                $data = [
                    'total_money' => $result['total_amount'],
                    'out_trade_no' => $result['out_trade_no'],
                    'transaction_id' => $result['trade_no']
                ];
                $res = ManyDemandOrder::orderResult($data);
            } elseif (is_array($arr) && $arr['type'] == 'memberOrder') {
                $result = $data;
                $data = [
                    'total_money' => $result['total_amount'],
                    'out_trade_no' => $result['out_trade_no'],
                    'transaction_id' => $result['trade_no']
                ];
                $res = MemberOrder::orderResult($data);
            }
            if ($res) {
                echo 'success';
                die;
            }
        }
        echo 'fail';
        die;
    }
}