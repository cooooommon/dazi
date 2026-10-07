<?php


namespace app\massage\model;


use app\BaseModel;
use app\integral\model\OrderIntegral;
use app\massage\controller\IndexWxPay;
use app\member\model\MemberConfig;
use app\member\model\MemberOrder;
use app\shop\controller\IndexAliPay;
use log\LogUtils;
use longbingcore\wxcore\WxSetting;
use think\facade\Db;

class DemandOrder extends BaseModel
{
    protected $name = 'massage_service_demand_order';

    /**
     * 电话加密
     * @param $value
     * @param $data
     * @return string|string[]
     */
    public function getPhoneAttr($value, $data)
    {

        if (!empty($value) && isset($data['uniacid'])) {

            if (numberEncryption($data['uniacid']) == 1) {

                return substr_replace($value, "****", 2, 4);
            }

        }

        return $value;

    }

    /**
     * 添加
     * @param $input
     * @return int|string
     */
    public static function add($input)
    {
        $input['create_time'] = time();
        return self::insertGetId($input);
    }

    public static function getInfo($where)
    {
        return self::where($where)->find();
    }

    /**
     * 陪玩官佣金（废弃 2023-07）
     * @param $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getCashData($order)
    {
        $config = Config::where('uniacid', $order['uniacid'])->find();
        //技师佣金比列
        $order['coach_balance'] = $config['coach_tc_ratio'];
        //技师佣金
        $order['coach_cash'] = round($order['price'] * $order['coach_balance'] / 100, 2);
        return [
            'coach_balance' => $order['coach_balance'],
            'coach_cash' => $order['coach_cash']
        ];
    }

    /**
     * 邀约订单回调
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function orderResult($data)
    {
        LogUtils::log('邀约订单回调，回调参数：' . json_encode($data), 'demand_order');
        /*
          $data = [
                    'total_money' => 0,
                    'out_trade_no' => $input['order_code'],
                    'transaction_id' => $input['order_code']
                ];
         * */
        $order = self::where('order_code', $data['out_trade_no'])->find();
        if (empty($order)) {
            return false;
        }
        if ($order['is_pay'] == 1) {
            return true;
        }
        $config = Config::where('uniacid', $order['uniacid'])->find();
        $update = [
            'is_pay' => 1,
            'pay_price' => $data['total_money'],
            'transaction_id' => $data['transaction_id'],
            'status' => $config['is_demand_order_check'] == 0 ? 2 : 1,
            'pay_time' => time()
        ];
        Db::startTrans();
        try {
            $res = self::where('order_code', $data['out_trade_no'])->update($update);
            if ($res === false) {
                throw new \Exception('修改订单失败');
            }
            if ($order['pay_type'] == 3) {

                $water_model = new BalanceWater();

                //添加余额流水
                $insert = [
                    'uniacid' => $order['uniacid'],
                    'user_id' => $order['user_id'],
                    'pay_price' => $order['price'],
                    'id' => $order['id'],
                ];
                $res = $water_model->updateUserBalance($insert, 5);
                if ($res === false) {
                    throw new \Exception('增加余额流水失败');
                }
            }

            $integral = [
                'uniacid' => $order['uniacid'],
                'user_id' => $order['user_id'],
                'true_service_price' => $order['price'],
                'id' => $order['id']
            ];
            //积分相关
            $res = OrderIntegral::getIntegralData($integral, 2, 1);

            if ($res !== true) {

                throw new \Exception('处理订单积分失败');
            }

            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            LogUtils::log('邀约订单回调失败，失败信息：' . $exception->getMessage(), 'demand_order');
            return false;
        }

        if ($update['status'] == 2) {

            curlSend("https://" . $_SERVER['HTTP_HOST'] . '/massage/app/Index/sendMsg?id=' . $order['id'] . '&urls=massage/app/Index/sendMsg&i=' . $order['uniacid']);
        }

        if ($order['pay_type'] == 3) {

            $type = 2;
        } elseif ($order['pay_type'] == 1) {

            $type = 3;
        } else {

            $type = 1;
        }

        SendMsgConfig::paySuccess($order['uniacid'], $order['user_id'], $data['total_money'], $type);

        return true;
    }

    /**
     * 列表
     * @param $where
     * @param $page
     * @return mixed
     */
    public static function getList($where, $page = 10)
    {
        return self::alias('a')
            ->field('a.id,content,a.img,is_hide,a.price,a.start_time,a.end_time,a.status,order_code,coach_id,a.address,a.create_time,a.check_text,a.is_refund,b.nickName,b.avatarUrl,a.phone as user_phone,c.mobile as coach_phone,d.img as type_img,d.price as type_price,d.name as type_name')
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id = b.id')
            ->leftJoin('massage_service_coach_list c', 'a.coach_id = c.id')
            ->leftJoin('massage_service_demand_type d', 'a.ser_id = d.id')
            ->order('a.create_time desc')
            ->paginate($page)
            ->each(function ($item) {
                $item['start_time'] = handleTime($item['start_time'], 'Y-m-d H:i');
                $item['end_time'] = handleTime($item['end_time'], 'Y-m-d H:i');
                $item['create_time'] = handleTime($item['create_time'], 'Y-m-d H:i');
                $item['img'] = !empty($item['img']) ? explode(',', $item['img']) : [];
                if ($item['status'] == 2) {
                    $item['apply_num'] = DemandOrderApply::getCount(['order_id' => $item['id'], 'is_look' => 0]);
                } else {
                    $item['apply_num'] = 0;
                }
                return $item;
            })
            ->toArray();
    }

    public static function getExcelList($where)
    {

        $data = self::alias('a')
            ->field('a.id,content,a.img,is_hide,a.price,a.start_time,a.end_time,a.status,order_code,coach_id,a.address,a.create_time,a.check_text,a.is_refund,b.nickName,b.avatarUrl,c.name,a.phone,d.coach_name,a.pay_type,a.transaction_id')
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id = b.id')
            ->leftJoin('massage_service_demand_type c', 'a.ser_id = c.id')
            ->leftJoin('massage_service_coach_list d', 'a.coach_id = d.id')
            ->order('a.create_time desc')
            ->select()
            ->toArray();
        if ($data) {
            foreach ($data as $item) {
                $item['start_time'] = handleTime($item['start_time'], 'Y-m-d H:i');
                $item['end_time'] = handleTime($item['end_time'], 'Y-m-d H:i');
                $item['create_time'] = handleTime($item['create_time'], 'Y-m-d H:i');
                $item['img'] = !empty($item['img']) ? explode(',', $item['img']) : [];
            }

        }
        return $data;
    }

    /**
     * 详情
     * @param $where
     * @return mixed
     */
    public static function getDetail($where)
    {
        $item = self::alias('a')
            ->field('a.id,content,a.img,is_hide,a.price,a.start_time,a.end_time,a.status,order_code,transaction_id,coach_id,a.address,a.create_time,a.check_text,a.is_refund,b.nickName,b.avatarUrl,c.name,a.phone,d.coach_name,a.pay_type,d.work_img,c.img as type_img,c.price as type_price,c.name as type_name,a.is_car,a.member_discount,a.member_balance,a.member_status')
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id = b.id')
            ->leftJoin('massage_service_demand_type c', 'a.ser_id = c.id')
            ->leftJoin('massage_service_coach_list d', 'a.coach_id = d.id')
            ->find();
        if ($item) {
            $item['start_time'] = handleTime($item['start_time'], 'Y-m-d H:i');
            $item['end_time'] = handleTime($item['end_time'], 'Y-m-d H:i');
            $item['create_time'] = handleTime($item['create_time'], 'Y-m-d H:i');
            $item['img'] = !empty($item['img']) ? explode(',', $item['img']) : [];
            if ($item['status'] == 2) {
                $item['apply_num'] = DemandOrderApply::getCount(['order_id' => $item['id'], 'is_look' => 0]);
            } else {
                $item['apply_num'] = 0;
            }
        }
        return $item;
    }

    /***
     * 审核邀约订单
     * @param $data
     * @param $pay_config
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function demandExamine($data, $pay_config)
    {
        if ($data['status'] == 1) {
            $update = [
                'status' => 2,
                'check_text' => !empty($data['check_text']) ? $data['check_text'] : '',
                'check_time' => time()
            ];
        } else {
            $update = [
                'status' => 0,
                'check_text' => !empty($data['check_text']) ? $data['check_text'] : '',
                'check_time' => time()
            ];
            $code = self::demandRefund($data['id'], $pay_config);
            if ($code['code'] == 1) {
                return ['code' => 1, 'msg' => $code['msg']];
            }
        }
        $res = self::where('id', $data['id'])->update($update);
        if ($res === false) {
            return ['code' => 1, 'msg' => '审核失败'];
        }

        //主动发送了  不需要了
        /*if ($update['status'] == 2) {

            curlSend("https://" . $_SERVER['HTTP_HOST'] . '/massage/app/Index/sendMsg?id=' . $data['id'] . '&urls=massage/app/Index/sendMsg&i=' . 666);
        }*/
        return ['code' => 0];
    }

    /**
     * 邀约订单退款
     * @param $order_id
     * @param $pay_config
     * @return array
     */
    public static function demandRefund($order_id, $pay_config)
    {
        Db::startTrans();
        try {
            $order = self::where('id', $order_id)->find();
            if (!in_array($order['status'], [1, 2, 3]) || $order['is_pay'] != 1 || in_array($order['is_refund'], [2])) {
                throw new \Exception('状态错误');
            }
            switch ($order['pay_type']) {
                case 1:
                    $pay = new IndexAliPay(\app());
                    $res = $pay->aliRefund($order['transaction_id'], $order['price']);
                    if (!$res) {
                        throw new \Exception('退款失败，请重试');
                    }
                    break;
                case 2:
                    $pay_price = ManyDemandOrder::where('transaction_id', $order['transaction_id'])->value('pay_price');

                    $pay_price = !empty($pay_price) ? $pay_price : $order['pay_price'];

                    $res = orderRefundApi($pay_config, $pay_price, $order['pay_price'], $order['transaction_id']);
                    if (!empty($res['code'])) {
                        throw new \Exception($res['msg']);
                    }
                    if (isset($res['return_code']) && isset($res['result_code']) && ($res['return_code'] != 'SUCCESS' || $res['result_code'] != 'SUCCESS')) {
                        $discption = !empty($res['err_code_des']) ? $res['err_code_des'] : $res['return_msg'];

                        throw new \Exception($discption);
                    }

                    if ($res != true) {
                        throw new \Exception('退款失败，请重试');
                    }
                    break;
                case 3:
                    $water_model = new BalanceWater();
                    $insert = [
                        'uniacid' => $order['uniacid'],
                        'user_id' => $order['user_id'],
                        'pay_price' => $order['price'],
                        'id' => $order['id'],
                    ];
                    $res = $water_model->updateUserBalance($insert, 6, 1);
                    if ($res == 0) {
                        throw new \Exception('退款失败，请重试');
                    }
                    break;
            }
            $comm_model = new Commission();
            //将分销记录关闭
            if ($comm_model->where(['order_id' => $order['id'], 'type' => 13])->count() > 0) {
                $res = $comm_model->dataUpdate(['order_id' => $order['id'], 'type' => 13], ['status' => -1]);
                if ($res === false) {
                    throw new \Exception('分销记录关闭失败，请重试');
                }
            }
            //将积分记录关闭
            $res = OrderIntegral::refundIntegral($order, 2);

            if ($res === false) {

                throw new \Exception('积分记录关闭失败，请重试');
            }
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            return ['code' => 1, 'msg' => $exception->getMessage()];
        }
        return ['code' => 0];
    }

    /**
     * 计算距离的列表
     * @param $where
     * @param $alh
     * @param $page
     * @return mixed
     */
    public static function getList1($where, $alh, $dis, $page)
    {
        $model = self::alias('a')
            ->field(['a.id', 'content', 'a.img', 'is_hide', 'a.price', 'a.start_time', 'a.end_time', 'a.status', 'order_code', 'coach_id', 'a.address', 'a.create_time', 'b.nickName', 'b.avatarUrl', 'c.img as type_img', 'c.price as type_price', 'c.name as type_name', $alh])
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id = b.id')
            ->leftJoin('massage_service_demand_type c', 'a.ser_id = c.id');
        if ($dis == 1) {
            $model = $model->order('distance asc');
        } else {
            $model = $model->order('distance desc');
        }
        return $model->order('a.create_time desc')
            ->paginate($page)
            ->each(function ($item) {
                $item['read_time'] = lb_friendly_date($item['create_time']);
                $item['count_down'] = $item['start_time'] > time() ? $item['start_time'] - time() : 0;
                $item['start_time'] = handleTime($item['start_time'], 'Y-m-d H:i');
                $item['end_time'] = handleTime($item['end_time'], 'Y-m-d H:i');
                $item['create_time'] = handleTime($item['create_time'], 'Y-m-d H:i');
                $item['img'] = !empty($item['img']) ? explode(',', $item['img']) : [];
                $item['apply_status'] = 0;
                return $item;
            })
            ->toArray();
    }

    /**
     * 取消订单
     * @param $order
     * @param $pay_config
     * @return array
     */
    public static function cancel($order, $pay_config, $status = -1)
    {
        $update = [
            'status' => $status,
            'cancel_time' => time(),
            'is_refund' => 2
        ];
        $code = self::demandRefund($order['id'], $pay_config);
        if ($code['code'] == 1) {
            return ['code' => 1, 'msg' => $code['msg']];
        }
        $res = self::where('id', $order['id'])->update($update);
        if ($res === false) {
            return ['code' => 1, 'msg' => '取消订单失败'];
        }
        return ['code' => 0];
    }

    /**
     * 完成
     * @param $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function complete($id)
    {
        $order = self::where('id', $id)->find();
        $update = [
            'complete_time' => time(),
            'status' => 4,
        ];
        Db::startTrans();
        $res = self::update($update, ['id' => $id]);
        if ($res === false) {
            Db::rollback();
            return false;
        }
        $res = Commission::where(['order_id' => $id, 'order_type' => 2])->update(['status' => 2, 'cash_time' => time()]);
        if ($res === false) {
            Db::rollback();
            return false;
        }
        if (!empty($order['coach_cash'])) {
            $res = Coach::where(['id' => $order['coach_id']])->update(['service_price' => Db::raw("service_price+{$order['coach_cash']}")]);
            if ($res === false) {
                Db::rollback();
                return false;
            }
        }
        if (!empty($order['admin_cash'])) {
            $res = Admin::where(['id' => $order['admin_id']])->update(['cash' => Db::raw("cash+{$order['admin_cash']}")]);
            if ($res === false) {
                Db::rollback();
                return false;
            }
        }
        if (!empty($order['user_cash'])) {
            $res = User::where(['id' => $order['user_fx_id']])->update(['cash' => Db::raw("cash+{$order['user_cash']}"), 'new_cash' => Db::raw("new_cash+{$order['user_cash']}")]);
            if ($res === false) {
                Db::rollback();
                return false;
            }
        }

        $res = OrderIntegral::endIntegral($order['id'], 2);

        if ($res !== true) {

            Db::rollback();
            return false;
        }

        Db::commit();
        return true;
    }

    /**
     * 取消到期未接单订单
     * @param $uniacid
     * @param $pay_config
     * @param int $user_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function cancelOrder($uniacid, $pay_config, $user_id = 0)
    {
        self::where([['is_pay', '=', 0], ['start_time', '<', time()]])->update(['status' => -2]);
        $where = [
            ['uniacid', '=', $uniacid],
            ['is_pay', '=', 1],
            ['status', 'in', [1, 2]],
            ['start_time', '<', time()],
            ['is_refund', 'in', [0, 3]]
        ];
        if (!empty($user_id)) {
            $where[] = ['user_id', '=', $user_id];
        }
        $order = self::where($where)->select();
        if ($order) {
            foreach ($order as $item) {
                $config = $pay_config[$item['pay_type']];
                self::cancel($item, $config, -2);
            }
        }
    }

    /**
     * 后台列表
     * @param $where
     * @param $limit
     * @return mixed
     */
    public static function getList2($where, $limit)
    {
        return self::alias('a')
            ->field('a.id,a.uniacid,content,a.img,is_hide,a.price,a.start_time,a.end_time,a.status,order_code,coach_id,a.address,a.create_time,a.check_text,a.is_refund,b.nickName,b.avatarUrl,c.name,a.phone,d.coach_name,a.pay_type,a.transaction_id')
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id = b.id')
            ->leftJoin('massage_service_demand_type c', 'a.ser_id = c.id')
            ->leftJoin('massage_service_coach_list d', 'a.coach_id = d.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->each(function ($item) {
                $item['start_time'] = handleTime($item['start_time'], 'Y-m-d H:i');
                $item['end_time'] = handleTime($item['end_time'], 'Y-m-d H:i');
                $item['create_time'] = handleTime($item['create_time'], 'Y-m-d H:i');
                $item['img'] = !empty($item['img']) ? explode(',', $item['img']) : [];
                return $item;
            })
            ->toArray();
    }

    /**
     * 退款
     * @param $data
     * @param $pay_config
     * @return array
     */
    public static function handleDemandOrder($data, $pay_config)
    {
        $order = self::where('id', $data['id'])->find();

        if ($data['status'] == 1) {
            $update = [
                'status' => -1,
                'cancel_time' => time(),
                'is_refund' => 2
            ];
            $code = self::demandRefund($data['id'], $pay_config);
            if ($code['code'] == 1) {
                return ['code' => 1, 'msg' => $code['msg']];
            }

            if ($order['coach_id'] > 0) {

                $send = [
                    'coach_id' => $order['coach_id'],
                    'uniacid' => $order['uniacid'],
                    'order_code' => $order['order_code'],
                    'money' => $order['price'],
                    'time' => date('Y年m月d日 H:i', $order['create_time']),
                    'status' => '已退款',
                    'order_id' => $order['id']
                ];
            }

            $us_send = [
                'user_id' => $order['user_id'],
                'uniacid' => $order['uniacid'],
                'order_code' => $order['order_code'],
                'money' => $order['price'],
                'time' => date('Y年m月d日 H:i:s', $order['create_time'])
            ];
            //发送消息-用户
            User::refundPassSendMsg($us_send, 2);
        } else {
            if ($order['coach_id'] > 0) {

                $send = [
                    'coach_id' => $order['coach_id'],
                    'uniacid' => $order['uniacid'],
                    'order_code' => $order['order_code'],
                    'money' => $order['price'],
                    'time' => date('Y年m月d日 H:i', $order['create_time']),
                    'status' => '已拒绝',
                    'order_id' => $order['id']
                ];

            }
            $update = [
                'is_refund' => 3,
                'cancel_time' => time(),
            ];

            $us_send = [
                'user_id' => $order['user_id'],
                'uniacid' => $order['uniacid'],
                'order_code' => $order['order_code'],
                'money' => $order['price'],
                'time' => date('Y-m-d H:i:s', $order['create_time'])
            ];
            //发送消息-用户
            User::refundNoPassSendMsg($us_send, 2);
        }

        $res = self::where('id', $data['id'])->update($update);
        if ($res === false) {
            return ['code' => 1, 'msg' => '退款失败'];
        }

        //发送消息-技师
        if (!empty($send)) {

            Coach::refundOrderSendMsg($send, 2);
        }

        return ['code' => 0];
    }

    public static function getFirst($where)
    {
        $data = self::where($where)->find();
        if ($data && $data['coach_id'] > 0) {
            $data['coach_info'] = Coach::where('id', $data['coach_id'])->find();
            $arr['mobile'] = $data['phone'];
            $data['address_info'] = $arr;
        }
        return $data;
    }

    /**
     * 选择向导
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function choose($data)
    {
        $order = self::where('id', $data['order_id'])->find()->toArray();
        $order['coach_id'] = $data['coach_id'];
        $order_update = [
            'coach_id' => $data['coach_id'],
            'receiving_time' => time(),
            'status' => 3
        ];
        Db::startTrans();
        try {
            if ($data['status'] == 2) {
                $order = (new Commission())->getDemandCash($order);
                if (!empty($order['code']) && $order['code'] == 300) {
                    throw new \Exception('请添加向导等级');
                }
                if (!empty($order['update'])) {
                    $order_update = array_merge($order_update, $order['update']);
                }
                $res = self::where('id', $data['order_id'])->update($order_update);
                if ($res === false) {
                    throw new \Exception('选择失败');
                }
                DemandOrderApply::update(['status' => 3], [['order_id', '=', $data['order_id']], ['coach_id', '<>', $data['coach_id']]]);
            }
            DemandOrderApply::update(['status' => $data['status']], ['order_id' => $data['order_id'], 'coach_id' => $data['coach_id']]);
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            return ['code' => 1, 'msg' => $exception->getMessage()];
        }
        return ['code' => 0];
    }

    /**
     * 需求订单技师分销明细
     * @param $where
     * @param $input
     * @return array
     * @throws \think\db\exception\DbException
     */
    public static function demandCommissionDetail($where, $input)
    {
        $total_price = self::where($where)->sum('coach_cash');
        if (!empty($input['month'])) {
            $where[] = ['create_time', '<=', strtotime('+1 month', $input['month'])];
        }
        if (!empty($input['start_time'])) {
            $where[] = [
                'create_time', 'between', "{$input['start_time']},{$input['end_time']}"
            ];
        }
        $data = self::where($where)
            ->field('id,order_code,price,pay_price,create_time,coach_cash,coach_balance')
            ->order('create_time desc')
            ->paginate($input['limit'] ?? 10)
            ->each(function ($item) use ($where) {
                $item['month'] = date('Y-m', $item['create_time']);
                $item['month_text'] = date('Y年m月', $item['create_time']);
                $item['create_time'] = handleTime($item['create_time']);
                $item['total_cash'] = self::where($where)->whereMonth('create_time', $item['month'])->sum('coach_cash');
                $item['total_count'] = self::where($where)->whereMonth('create_time', $item['month'])->count();
            })
            ->toArray();
        $data['total_price'] = $total_price;
        $data['search_price'] = self::where($where)->sum('coach_cash');
        $data['search_count'] = self::where($where)->count();
        return $data;
    }

    /**
     * 选择向导通知消息
     * @param $data
     * @return bool|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function chooseSendMsg($data)
    {
        $coach = Coach::where('id', $data['coach_id'])->find();

        $user_info = User::where('id', $coach['user_id'])->find();

        $data['user_id'] = $coach['user_id'];
        //type 1小程序 2公众号
        $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

        if ($type == 1) {

            $res = self::chooseSendMsgWechat($data);

        } else {

            $res = self::chooseSendMsgWeb($data);

        }

        return $res;
    }

    /**
     * 选择向导通知消息
     * @param $data
     * @return bool|mixed|string
     */
    public static function chooseSendMsgWechat($data)
    {
        $cap_model = new Coach();

        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if ($data['status'] == 2 && empty($x_config['apply_ok_tmp_id'])) {
            return false;
        } elseif ($data['status'] == 3 && empty($x_config['apply_ok_tmp_id'])) {
            return false;
        }

        if (empty($x_config['gzh_appid'])) {

            return false;
        }

        $config = longbingGetAppConfig($data['uniacid']);

        $openid = $cap_model->capOpenid($data['coach_id'], 1);

        $page = "find/pages/invitation/list?tab=1";

        $access_token = longbingGetAccessToken($data['uniacid']);

        //post地址
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token={$access_token}";


        $arr = [
            //用户小程序openid
            'touser' => $openid,

            'mp_template_msg' => [
                //公众号appid
                'appid' => $x_config['gzh_appid'],

                "url" => "http://weixin.qq.com/download",
                //公众号模版id
                'template_id' => $data['status'] == 2 ? $x_config['apply_ok_tmp_id'] : $x_config['apply_no_tmp_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],

                'data' => array(
                    //审核时间
                    'time3' => array(

                        'value' => date('Y-m-d H:i:s'),

                        'color' => '#0000ff',
                    ),
                    //结果
                    'thing4' => array(
                        //内容
                        'value' => $data['status'] == 2 ? '恭喜你！报名成功' : '很抱歉，对方拒绝了你的报名',

                        'color' => '#0000ff',
                    ),

                )

            ]

        ];

        $arr = json_encode($arr);

        $tmp = [

            'url' => $url,

            'data' => $arr,
        ];
        $rest = lbCurlPost($tmp['url'], $tmp['data']);

        $rest = json_decode($rest, true);

        return $rest;
    }

    /**
     * 选择向导通知消息
     * @param $data
     * @return bool|mixed|string
     */
    public static function chooseSendMsgWeb($data)
    {
        $cap_model = new Coach();

        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if ($data['status'] == 2 && empty($x_config['apply_ok_tmp_id'])) {
            return false;
        } elseif ($data['status'] == 3 && empty($x_config['apply_ok_tmp_id'])) {
            return false;
        }

        if (empty($x_config['gzh_appid'])) {

            return false;
        }
        $openid = $cap_model->capOpenid($data['coach_id'], 2);

        if ($data['status'] == 2) {
            $key = explode('&', $x_config['apply_ok_tmp_id']);
        } else {
            $key = explode('&', $x_config['apply_no_tmp_id']);
        }

        $arr = [
            1 => 'time3',
            2 => 'thing4'
        ];
        for ($i = 1; $i < 3; $i++) {
            if (!empty($key[$i])) {
                $arr[$i] = $key[$i];
            }
        }

        $arr = [
            //用户小程序openid
            'touser' => $openid,
            //公众号appid
            'appid' => $x_config['gzh_appid'],

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/find/pages/invitation/list?tab=1',
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                //审核时间
                $arr[1] => array(

                    'value' => date('Y-m-d H:i:s'),

                    'color' => '#0000ff',
                ),
                //结果
                $arr[2] => array(
                    //内容
                    'value' => $data['status'] == 2 ? '恭喜你！报名成功' : '很抱歉，对方拒绝了你的报名',

                    'color' => '#0000ff',
                ),
            )

        ];

        $wx_setting = new WxSetting($data['uniacid']);

        $access_token = $wx_setting->getGzhToken();

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

        $arr = json_encode($arr);

        $tmp = [

            'url' => $url,

            'data' => $arr,
        ];
        $rest = lbCurlPost($tmp['url'], $tmp['data']);

        $rest = json_decode($rest, true);

        return $rest;
    }

    /**
     * @Desc: 会员折扣
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/26 16:48
     */
    public static function handleOrder($data)
    {
        $data['member_discount'] = $data['member_status'] = $data['member_balance'] = 0;

        $status = MemberOrder::getStatus($data['user_id'], $data['uniacid']);

        if (!$status) {

            return $data;
        }

        $config = MemberConfig::getInfo(['uniacid' => $data['uniacid']]);

        $member_price = round($data['price'] * $config['discount'] / 10, 2);

        $data['member_discount'] = $data['price'] - $member_price;

        $data['member_balance'] = $config['discount'];

        $data['member_status'] = 1;

        $data['price'] = $member_price;

        return $data;

    }
}