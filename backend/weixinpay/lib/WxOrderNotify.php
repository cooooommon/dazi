<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

use app\massage\model\ManyDemandOrder;
use app\store\model\PackageOrder;
use log\LogUtils;
use think\App;
use think\facade\Db;

require_once "WxPay.Api.php";
require_once 'WxPay.Notify.php';


class WxOrderNotify extends WxPayNotify
{
    protected $app;
    public function __construct ( App $app )
    {
        $this->app = $app;
    }
    //查询订单
    public function Queryorder($transaction_id){


        @file_put_contents('./weixinQuery.txt','in_query',FILE_APPEND);

        $input = new WxPayOrderQuery();

        $input->SetTransaction_id($transaction_id);

        $result = WxPayApi::orderQuery($input);


        if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {

            $arr = json_decode($result['attach'] , true);

            @LogUtils::log('订单回调，回调参数：' . json_encode($arr), 'all_order');

            if(is_array($arr) && $arr['type']=='Balance'){

                $order_model = new \app\massage\model\BalanceOrder();

                $res    = $order_model->orderResult($arr['out_trade_no'],$transaction_id);

                return $res;
            }elseif(is_array($arr) && $arr['type']=='Massage'){

                $order_model = new \app\massage\model\Order();

                $res    = $order_model->orderResult($arr['out_trade_no'],$transaction_id);

                return $res;
            }elseif(is_array($arr) && $arr['type']=='MassageUp'){

                $order_model = new \app\massage\model\UpOrderList();

                $res    = $order_model->orderResult($arr['out_trade_no'],$transaction_id);

                return $res;
            }elseif (is_array($arr) && $arr['type'] == 'demoOrder') {
                $result = json_decode(json_encode($result), true);
                $data = [
                    'total_money' => $result['total_fee'] / 100,
                    'out_trade_no' => $result['out_trade_no'],
                    'transaction_id' => $result['transaction_id']
                ];
                $res = \app\massage\model\DemandOrder::orderResult($data);
                return $res;
            }elseif (is_array($arr) && $arr['type'] == 'packageOrder') {
                $result = json_decode(json_encode($result), true);
                $data = [
                    'total_money' => $result['total_fee'] / 100,
                    'out_trade_no' => $result['out_trade_no'],
                    'transaction_id' => $result['transaction_id']
                ];
                $res = PackageOrder::orderResult($data);
                return $res;
            }elseif (is_array($arr) && $arr['type'] == 'manyDemoOrder') {
                $result = json_decode(json_encode($result), true);
                $data = [
                    'total_money' => $result['total_fee'] / 100,
                    'out_trade_no' => $result['out_trade_no'],
                    'transaction_id' => $result['transaction_id']
                ];
                $res = ManyDemandOrder::orderResult($data);
                return $res;
            } elseif (is_array($arr) && $arr['type'] == 'memberOrder') {
                $result = json_decode(json_encode($result), true);
                $data = [
                    'total_money' => $result['total_fee'] / 100,
                    'out_trade_no' => $result['out_trade_no'],
                    'transaction_id' => $result['transaction_id']
                ];
                $res = \app\member\model\MemberOrder::orderResult($data);
                return $res;
            }
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            file_put_contents('./weixinQuery.txt','输入参数不正确',FILE_APPEND);
            $msg = "输入参数不正确";
            return false;
        }
        file_put_contents('./weixinQuery.txt','abc',FILE_APPEND);
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"],$data[''])){
            $msg = "订单查询失败";
            return false;
        }
        return true;
    }
}


