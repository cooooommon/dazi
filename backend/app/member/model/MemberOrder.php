<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/11/12
 * Time: 14:23
 * docs:
 */

namespace app\member\model;

use app\BaseModel;
use app\massage\model\BalanceWater;
use app\massage\model\Commission;
use app\massage\model\Config;
use app\massage\model\SendMsgConfig;
use app\massage\model\User;
use app\member\info\PermissionMember;
use log\LogUtils;
use think\facade\Db;

class MemberOrder extends BaseModel
{
    protected $name = 'massage_member_order_list';

    public static function add($insert)
    {
        return self::insertGetId($insert);
    }

    /**
     * @Desc: 订单回调
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 16:07
     */
    public static function orderResult($data)
    {
        LogUtils::log('会员卡购买订单回调，回调参数：' . json_encode($data), 'member_order');

        $order = self::where('order_code', $data['out_trade_no'])->find();

        if (empty($order)) {

            return false;
        }
        if ($order['status'] != 1) {

            return true;
        }

        $card = self::getUserCard($order['user_id']);

        if ($card) {

            $start_time = $card['end_time'];
        } else {

            $start_time = time();
        }
        $end_time = strtotime('+' . $order['day'] . ' days', $start_time);

        $update = [
            'status' => 2,
            'transaction_id' => $data['transaction_id'],
            'pay_price' => $data['total_money'],
            'pay_time' => time(),
            'start_time' => $start_time,
            'end_time' => $end_time
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
                    'pay_price' => $order['pay_price'],
                    'id' => $order['id'],
                ];

                $res = $water_model->updateUserBalance($insert, 10);

                if ($res === false) {

                    throw new \Exception('增加余额流水失败');
                }
            }

            if ($order['share_user_id'] > 0 && $order['share_cash'] > 0) {

                $user_model = new User();

                $user = $user_model->dataInfo(['id' => $order['share_user_id']]);

                if (!empty($user)) {
                    $res = $user_model->where(['id' => $order['share_user_id']])->update(['new_cash' => $user['new_cash'] + $order['share_cash'], 'cash' => $user['cash'] + $order['share_cash']]);

                    if ($res == 0) {

                        throw new \Exception('佣金插入失败');
                    }
                }
            }
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();

            LogUtils::log('会员卡购买订单回调失败，失败信息：' . $exception->getMessage(), 'member_order');

            return false;
        }

        SendMsgConfig::paySuccess($order['uniacid'], $order['user_id'], $data['total_money'], $order['pay_model']);
        return true;

    }

    /**
     * @Desc: 佣金数据
     * @param $order_id
     * @return int[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 16:07
     */
    public static function cashData($order_id)
    {
        $order = self::find($order_id);

        $data = [
            'share_user_id' => 0,
            'share_balance' => 0,
            'share_cash' => 0
        ];

        $count = self::where(['user_id' => $order['user_id'], 'status' => 2])->find();

        if ($count) {

            return $data;
        }

        $config = MemberConfig::getInfo(['uniacid' => $order['uniacid']]);

        if (!(float)$config['balance'] > 0) {

            return $data;
        }

        $user_model = new User();

        $config_model = new Config();

        $dis = [

            'id' => $order['user_id'],
        ];

        $total_cash = 0;
        //上级
        $top_id = $user_model->where($dis)->value('pid');

        $top = $user_model->dataInfo(['id' => $top_id]);

        $config_ = $config_model->dataInfo(['uniacid' => $order['uniacid']]);

        if (!empty($top) && $config_['fx_check'] == 1 && $top['is_fx'] == 0) {

            return $data;
        }

        if (!empty($top)) {

            $data['share_user_id'] = $top['id'];

            $total_cash = round($order['price'] * $config['balance'] / 100, 2);
        }

        $data['share_balance'] = $config['balance'];

        $data['share_cash'] = $total_cash;

        return $data;
    }

    public static function edit($where, $update)
    {

        return self::where($where)->update($update);
    }

    /**
     * @Desc: 获取列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 17:50
     */
    public static function getList($where, $limit = 10)
    {
        return self::alias('a')
            ->field('a.id,a.user_id,a.order_code,a.price,a.title,a.start_time,a.end_time,a.pay_model,a.pay_time,a.transaction_id,b.nickName,c.nickName as share_nickName,a.share_cash,share_balance,a.create_time')
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id = b.id')
            ->leftJoin('massage_service_user_list c', 'a.share_user_id=c.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();
    }

    /***
     * @Desc: 获取用户会员卡信息
     * @param $user_id
     * @return MemberOrder|array|mixed|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 18:00
     */
    public static function getUserCard($user_id)
    {
        $where = [
            ['user_id', '=', $user_id],
            ['status', '=', 2],
            ['end_time', '>', time()]
        ];
        $data = self::where($where)->order('create_time desc')->find();

        if ($data) {

            $config = MemberConfig::getInfo(['uniacid' => $data['uniacid']]);

            $data['member_discount'] = $config['discount'];
        }

        return empty($data) ? '' : $data;
    }

    /**
     * @Desc: 获取会员卡状态
     * @param $user_id
     * @param $uniacid
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/14 10:11
     */
    public static function getStatus($user_id, $uniacid)
    {
        $p = new PermissionMember((int)$uniacid);

        $auth = $p->pAuth();

        if (!$auth) {

            return 0;
        }
        $data = MemberConfig::getInfo(['uniacid' => $uniacid]);

        if ($data['status'] == 0) {

            return 0;
        }

        $where = [
            ['user_id', '=', $user_id],
            ['status', '=', 2],
            ['end_time', '>', time()]
        ];
        $data = self::where($where)->order('create_time desc')->find();

        if (empty($data)) {

            return 0;
        }

        return 1;
    }
}