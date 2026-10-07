<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/11/12
 * Time: 11:22
 * docs:
 */

namespace app\member\controller;

use app\ApiRest;
use app\massage\model\User;
use app\member\model\MemberCard;
use app\member\model\MemberConfig;
use app\member\model\MemberOrder;
use app\shop\controller\IndexAliPay;
use longbingcore\wxcore\PayModel;
use think\App;
use think\facade\Db;

class Index extends ApiRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 会员卡列表
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 11:26
     */
    public function cardList()
    {
        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1]
        ];

        $list = MemberCard::getListNoPage($where);

        return $this->success($list);
    }

    /**
     * @Desc: 设置会员卡配置信息
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 11:31
     */
    public function configInfo()
    {
        $config = MemberConfig::getInfo(['uniacid' => $this->_uniacid]);

        $config['balance'] = $config['balance'] + 0;

        return $this->success($config);
    }

    /**
     * @Desc:下单
     * @return mixed
     * @throws \WxPayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 17:03
     */
    public function payOrder()
    {
        $input = request()->only(['id', 'pay_model']);

        $card = MemberCard::getInfo(['id' => $input['id']]);

        if (empty($card)) {

            return $this->error('会员卡不存在');
        } elseif ($card['status'] != 1) {

            return $this->error('会员卡已下架');
        }

        $order_insert = [
            'uniacid' => $this->_uniacid,
            'card_id' => $input['id'],
            'user_id' => $this->getUserId(),
            'day' => $card['day'],
            'title' => $card['title'],
            'price' => $card['price'],
            'order_code' => orderCode(),
            'pay_model' => $input['pay_model'],
            'create_time' => time(),
            'update_time' => time(),
            'app_pay' => $this->is_app
        ];

        Db::startTrans();

        $order_id = MemberOrder::add($order_insert);

        if (!$order_id) {

            Db::rollback();

            return $this->error('下单失败');
        }

        $cash_data = MemberOrder::cashData($order_id);

        $res = MemberOrder::edit(['id' => $order_id], $cash_data);

        if ($res === false) {

            Db::rollback();

            return $this->error('下单失败');
        }

        Db::commit();

        //如果是0元
        if ($order_insert['price'] <= 0) {

            $notify = [
                'total_money' => 0,
                'out_trade_no' => $order_insert['order_code'],
                'transaction_id' => $order_insert['order_code']
            ];

            MemberOrder::orderResult($notify);

            $return_data = [
                'is_pay' => 1,
                'order_id' => $order_id
            ];
        } else {

            $pay_model = $input['pay_model'];

            //余额支付
            if ($pay_model == 2) {

                $user_model = new User();

                $user_balance = $user_model->where(['id' => $this->getUserId()])->value('balance');

                if ($user_balance < $order_insert['price']) {

                    $this->errorMsg('余额不足');
                }

                $notify = [
                    'total_money' => $order_insert['price'],
                    'out_trade_no' => $order_insert['order_code'],
                    'transaction_id' => $order_insert['order_code']
                ];

                MemberOrder::orderResult($notify);

                $return_data = [
                    'is_pay' => 1,
                    'order_id' => $order_id
                ];
            } elseif ($pay_model == 3) {

                $pay = new IndexAliPay($this->app);

                $params = [
                    'order_code' => $order_insert['order_code'],
                    'price' => $order_insert['price'],
                    'body' => '购买会员订单',
                    'passback_params' => 'type=memberOrder&uniacid=' . $this->_uniacid,
                    'is_app' => $this->is_app
                ];

                $jsApiParameters = $pay->aliPay($params);

                $return_data = [
                    'is_pay' => 0,
                    'pay_model' => 3,
                    'order_id' => $order_id,
                    'pay_list' => $jsApiParameters
                ];

            } else {
                //虚拟支付开启时改走虚拟支付
                $virtualParams = \app\virtualpay\library\VirtualPayService::tryCreate($this->_uniacid, $this->getUserInfo()['openid'], 'memberOrder', $order_insert['order_code'], $order_insert['price']);
                if (!empty($virtualParams)) {
                    $jsApiParameters = $virtualParams;
                } else {
                    $pay_controller = new \app\shop\controller\IndexWxPay($this->app);
                    //支付
                    $jsApiParameters = $pay_controller->createWeixinPay($this->payConfig($this->_uniacid, $this->is_app), $this->getUserInfo()['openid'], $this->_uniacid, "购买会员卡", ['type' => 'memberOrder', 'out_trade_no' => $order_insert['order_code']], $order_insert['price']);
                }

                $return_data = [
                    'is_pay' => 0,
                    'pay_model' => 1,
                    'order_id' => $order_id,
                    'pay_list' => $jsApiParameters
                ];
            }
        }
        return $this->success($return_data);
    }

    /**
     * @Desc: 重新支付
     * @return mixed
     * @throws \WxPayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/27 15:09
     */
    public function rePayOrder()
    {

        $input = $this->_input;

        $order_insert = MemberOrder::where(['id' => $input['id']])->find();

        if ($order_insert['status'] != 1) {

            $this->errorMsg('订单状态错误');

        }

        if ($order_insert['app_pay'] == 1 && $this->is_app != 1) {

            $this->errorMsg('请到APP完成支付');

        }

        if ($order_insert['app_pay'] == 0 && $this->is_app != 0) {

            $this->errorMsg('请到小程序完成支付');
        }

        if ($order_insert['app_pay'] == 2 && $this->is_app != 2) {

            $this->errorMsg('请到公众号完成支付');

        }

        if ($order_insert['pay_model'] == 2) {

            $user_model = new User();

            $user_balance = $user_model->where(['id' => $this->getUserId()])->value('balance');

            if ($user_balance < $order_insert['pay_price']) {

                $this->errorMsg('余额不足');
            }
            $notify = [
                'total_money' => $order_insert['price'],
                'out_trade_no' => $order_insert['order_code'],
                'transaction_id' => $order_insert['order_code']
            ];
            MemberOrder::orderResult($notify);

            return $this->success(true);

        } elseif ($order_insert['pay_model'] == 3) {

            $pay = new IndexAliPay($this->app);

            $params = [
                'order_code' => $order_insert['order_code'],
                'price' => $order_insert['price'],
                'body' => '购买会员订单',
                'passback_params' => 'type=memberOrder&uniacid=' . $this->_uniacid,
                'is_app' => $this->is_app
            ];

            $jsApiParameters = $pay->aliPay($params);

            $arr['pay_list'] = $jsApiParameters;
        } else {
            //虚拟支付开启时改走虚拟支付
            $virtualParams = \app\virtualpay\library\VirtualPayService::tryCreate($this->_uniacid, $this->getUserInfo()['openid'], 'memberOrder', $order_insert['order_code'], $order_insert['price']);
            if (!empty($virtualParams)) {
                $jsApiParameters = $virtualParams;
            } else {
                $pay_controller = new \app\shop\controller\IndexWxPay($this->app);
                //支付
                $jsApiParameters = $pay_controller->createWeixinPay($this->payConfig(), $this->getUserInfo()['openid'], $this->_uniacid, "购买商品", ['type' => 'memberOrder', 'out_trade_no' => $order_insert['order_code']], $order_insert['price']);
            }

            $arr['pay_list'] = $jsApiParameters;
        }

        return $this->success($arr);

    }


    /**
     * @Desc: 交易记录
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 17:50
     */
    public function orderList()
    {
        $input = request()->param();

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.user_id', '=', $this->getUserId()],
            ['a.status', '=', 2]
        ];

        $list = MemberOrder::getList($where, $input['limit'] ?? 10);

        return $this->success($list);
    }

    /**
     * @Desc: 佣金记录
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/13 11:30
     */
    public function cashList()
    {
        $input = request()->param();

        $where = [
            ['a.uniacid', '=', $this->_uniacid],

            ['a.share_user_id', '=', $this->getUserId()],

            ['a.share_cash', '>', 0],

            ['a.status', '=', 2]
        ];

        $data = MemberOrder::getList($where, $input['limit'] ?? 10);

        return $this->success($data);
    }
}