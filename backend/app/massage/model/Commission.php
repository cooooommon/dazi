<?php

namespace app\massage\model;

use app\BaseModel;
use app\broker\model\Broker;
use app\broker\model\CommissionShare;
use think\facade\Db;

class Commission extends BaseModel
{


    //type 1分销 2加盟商 3技师 4分销商 5上级分销商 6省代分销 7技师拉用户充值余额 8车费 9合伙人 10渠道商 11平台 12业务员 13需求订单(取消) 14门店（门店订单）15推广人 16经纪人
    //order_type 1服务订单 2需求订单 3门店订单
    //定义表名
    protected $name = 'massage_service_order_commission';


    protected $append = [

        'order_goods'

    ];


    public function getOrderGoodsAttr($value, $data)
    {

        if (!empty($data['id'])) {

            $order_goods_model = new CommissionGoods();

            $list = $order_goods_model->goodsList(['a.commission_id' => $data['id']]);

            return $list;

        }

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-25 23:24
     * @功能说明:记录
     */
    public function recordList($dis, $page = 10)
    {

        $data = $this->alias('a')
            ->join('massage_service_user_list b', 'a.user_id = b.id', 'left')
            ->join('massage_service_user_list c', 'a.top_id = c.id', 'left')
            ->join('massage_service_order_list d', 'a.order_id = d.id and a.order_type = 1', 'left')
            ->join('massage_service_demand_order i', 'a.order_id = i.id AND a.order_type = 2', 'left')
            ->where($dis)
            ->field('a.*,b.nickName,c.nickName as top_name,d.order_code,d.pay_type,d.pay_price,d.transaction_id,d.car_price,i.transaction_id as demand_transaction_id,i.pay_price as demand_pay_price,d.cash_type,i.cash_type as demand_cash_type')
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($page)
            ->toArray();

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-25 23:24
     * @功能说明:记录
     */
    public function recordListV2($dis, $where, $code = [], $page = 10)
    {

        $data = $this->alias('a')
            ->join('massage_service_user_list b', 'a.user_id = b.id', 'left')
            ->join('massage_service_user_list c', 'a.top_id = c.id  AND a.type in (1,9)', 'left')
            ->join('massage_service_order_list d', 'a.order_id = d.id AND a.order_type = 1', 'left')
            ->join('shequshop_school_admin e', 'a.top_id = e.id AND a.type in (2,5,6,11)', 'left')
            ->join('massage_service_coach_list f', 'a.top_id = f.id AND a.type in (3,8)', 'left')
            ->join('massage_order_coach_change_logs g', '(d.id = g.order_id OR d.add_pid = g.order_id) AND g.is_new = 1 AND a.top_id=0 AND a.type in (3,8) ', 'left')
            ->join('massage_channel_list h', 'a.top_id = h.id AND a.type = 10', 'left')
            ->join('massage_service_demand_order i', 'a.order_id = i.id AND a.order_type = 2', 'left')
            ->join('massage_broker_list j', 'a.top_id = j.id AND a.type = 16', 'left')
            ->join('massage_store_apply k', 'a.top_id = k.id AND a.type = 14', 'left')
            ->join('massage_service_user_list m', 'a.top_id = m.id AND a.type = 15', 'left')
            ->join('massage_store_package_order_list l', 'a.order_id = l.id AND a.type in (14,15)', 'left')
            ->where($dis)
            ->where(function ($query) use ($where) {
                $query->whereOr($where);
            })
            ->where(function ($query) use ($code) {
                $query->whereOr($code);
            })
            ->field('a.*,h.user_name as channel_name,e.agent_name as admin_name,f.coach_name,b.nickName,c.nickName as top_name,d.order_code,d.pay_type,d.pay_price,d.transaction_id,d.car_price,g.now_coach_name,i.order_code as demand_order_code,i.transaction_id as demand_transaction_id,i.pay_price as demand_pay_price,d.cash_type,i.cash_type as demand_cash_type,j.name as broker_name,m.nickName as t_name,k.name as store_name,l.order_code as package_order_code,l.transaction_id as package_transaction_id,l.pay_price as package_pay_price,d.channel_cash,d.channel_staff_balance,d.channel_staff_id')
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($page)
            ->toArray();

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-25 23:34
     * @功能说明:佣金到账
     */
    public function successCash($order_id)
    {

        $data = $this->dataInfo(['order_id' => $order_id, 'type' => 1]);

        if (!empty($data) && $data['status'] == 1 && $data['cash'] > 0) {

            $user_model = new User();

            $user = $user_model->dataInfo(['id' => $data['top_id']]);

            $res = $user_model->where(['id' => $data['top_id'], 'new_cash' => $user['new_cash']])->update(['new_cash' => $user['new_cash'] + $data['cash'], 'cash' => $user['cash'] + $data['cash']]);

            if ($res == 0) {

                return $res;
            }

        }

        return 1;
    }


    /**
     * @author chenniang
     * @DataTime: 2022-11-07 16:21
     * @功能说明:代理商以及上级代理佣金到账
     */
    public function adminSuccessCash($order_id)
    {

        $dis = [

            'order_id' => $order_id,

            'status' => 1
        ];

        $data = $this->where($dis)->where('type', 'in', [2, 5, 6])->select()->toArray();

        $admin_model = new Admin();

        if (!empty($data)) {

            foreach ($data as $v) {

                $cash = $v['cash'];

                $admin = $admin_model->dataInfo(['id' => $v['admin_id']]);

                if (!empty($admin) && $cash > 0) {

                    $res = $admin_model->where(['id' => $v['admin_id'], 'cash' => $admin['cash']])->update(['cash' => Db::raw("cash+$cash")]);

                    if ($res == 0) {

                        return $res;
                    }
                }

            }

        }
        //结束所有佣金
        $this->dataUpdate(['order_id' => $order_id], ['status' => 2, 'cash_time' => time()]);

        return true;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-31 14:08
     * @功能说明:佣金到账
     */
    public function commissionSucessCash($order_id)
    {

        $dis = [

            'order_id' => $order_id,

            'status' => 1,

            'order_type' => 1
        ];

        $data = $this->where($dis)->select()->toArray();

        $user_model = new User();

        $admin_model = new Admin();

        $channel_model = new ChannelList();

        if (!empty($data)) {

            foreach ($data as $v) {
                //结束所有佣金
                $res = $this->dataUpdate(['id' => $v['id']], ['status' => 2, 'cash_time' => time()]);

                if ($res == 0) {

                    return $res;
                }

                $cash = $v['cash'];

                if ($cash <= 0) {

                    continue;
                }

                if (in_array($v['type'], [1, 9])) {
                    //用户分销 合伙人
                    $user = $user_model->dataInfo(['id' => $v['top_id']]);

                    if (!empty($user)) {
//,'new_cash'=>$user['new_cash']
                        $res = $user_model->where(['id' => $v['top_id']])->update(['new_cash' => $user['new_cash'] + $cash, 'cash' => $user['cash'] + $cash]);

                        if ($res == 0) {

                            return $res;
                        }
                    }

                } elseif (in_array($v['type'], [2, 5, 6])) {
                    //代理商
                    $admin = $admin_model->dataInfo(['id' => $v['admin_id']]);

                    if (!empty($admin)) {
//,'cash'=>$admin['cash']
                        $res = $admin_model->where(['id' => $v['admin_id']])->update(['cash' => Db::raw("cash+$cash")]);

                        if ($res == 0) {

                            return $res;
                        }
                    }
                } elseif (in_array($v['type'], [10])) {
                    //渠道商
                    $channel = $channel_model->dataInfo(['id' => $v['top_id']]);

                    if (!empty($channel)) {

                        $res = $channel_model->where(['id' => $v['top_id']])->update(['total_cash' => Db::raw("total_cash+$cash"), 'cash' => Db::raw("cash+$cash")]);

                        if ($res == 0) {

                            return $res;
                        }

                    }

                } elseif (in_array($v['type'], [16])) {
                    $broker = Broker::find($v['top_id']);

                    if (!empty($broker)) {

                        $res = Broker::where(['id' => $v['top_id']])->update(['total_cash' => Db::raw("total_cash+$cash"), 'cash' => Db::raw("cash+$cash")]);

                        if ($res == 0) {

                            return $res;
                        }

                    }
                }

            }

        }

        return true;

    }

    /**
     * @Desc: 门店套餐佣金到账
     * @param $order_id
     * @return Commission|array|int[]
     * @Auther: shurong
     * @Time: 2023/11/27 17:11
     */
    public static function packageCashSuccess($order_id)
    {
        $where = [
            ['type', 'in', [14, 15]],
            ['order_type', '=', 3],
            ['order_id', '=', $order_id],
            ['status', '=', 1]
        ];

        try {
            $data = self::where($where)->select()->toArray();

            if (!empty($data)) {

                $res = self::where($where)->update(['status' => 2, 'cash_time' => time()]);

                if ($res == 0) {

                    return $res;
                }

                foreach ($data as $item) {
                    $cash = $item['cash'];

                    if ($cash <= 0) {

                        continue;
                    }

                    if ($item['type'] == 14) {
                        //门店佣金
                        $store = StoreApply::where(['id' => $item['top_id']])->find();
                        if (!empty($store)) {

                            $res = StoreApply::where(['id' => $item['top_id']])->update(['total_cash' => $store['total_cash'] + $cash, 'cash' => $store['cash'] + $cash]);

                            if ($res == 0) {

                                throw new \Exception('佣金到账失败');
                            }
                        }
                    } elseif ($item['type'] == 15) {
                        //推广人
                        $user = User::where('id', $item['top_id'])->find();
                        if (!empty($user)) {

                            $res = user::where(['id' => $item['top_id']])->update(['package_cash' => $user['package_cash'] + $cash, 'total_package_cash' => $user['package_cash'] + $cash]);

                            if ($res == 0) {

                                throw new \Exception('佣金到账失败');
                            }
                        }

                    }

                }
            }
        } catch (\Exception $exception) {

            return ['code' => 1, 'msg' => $exception->getMessage()];
        }
        return ['code' => 0];
    }


    /**
     * @author chenniang
     * @DataTime: 2022-11-07 16:21
     * @功能说明:渠道商代理佣金到账
     */
    public function channelSuccessCash($order_id)
    {

        $dis = [

            'order_id' => $order_id,

            'status' => 1
        ];

        $data = $this->where($dis)->where('type', 'in', [2, 5, 6])->select()->toArray();

        $admin_model = new Admin();

        if (!empty($data)) {

            foreach ($data as $v) {

                $cash = $v['cash'];

                $admin = $admin_model->dataInfo(['id' => $v['admin_id']]);

                if (!empty($admin) && $cash > 0) {

                    $res = $admin_model->where(['id' => $v['admin_id'], 'cash' => $admin['cash']])->update(['cash' => Db::raw("cash+$cash")]);

                    if ($res == 0) {

                        return $res;
                    }
                }

            }

        }
        //结束所有佣金
        $this->dataUpdate(['order_id' => $order_id], ['status' => 2, 'cash_time' => time()]);

        return true;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data)
    {

        $data['create_time'] = time();

        $res = $this->insert($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis, $data)
    {

        $data['update_time'] = time();

        $res = $this->where($dis)->update($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis, $page = 10)
    {

        $data = $this->where($dis)->order('top desc,id desc')->paginate($page)->toArray();

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis)
    {

        $data = $this->where($dis)->find();

        return !empty($data) ? $data->toArray() : [];

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-26 23:39
     * @功能说明:添加佣金
     */
    public function commissionAdd($order)
    {
        //向导佣金
        $this->coachCommission($order);
        //加盟商佣金
        $this->adminCommission($order);

        $user_model = new User();
        //上级
        $top = $user_model->where(['id' => $order['user_id']])->value('pid');

        if (!empty($top)) {

            $ser_model = new Service();

            $com_mdoel = new Commission();

            $com_goods_mdoel = new CommissionGoods();

            foreach ($order['order_goods'] as $v) {
                //查看是否有分销
                $ser = $ser_model->dataInfo(['id' => $v['goods_id']]);

                if (!empty($ser['com_balance'])) {

                    $insert = [

                        'uniacid' => $order['uniacid'],

                        'user_id' => $order['user_id'],

                        'top_id' => $top,

                        'order_id' => $order['id'],

                        'order_code' => $order['order_code'],

                    ];

                    $find = $com_mdoel->dataInfo($insert);

                    $cash = $v['true_price'] * $ser['com_balance'] / 100 * $v['num'];

                    if (empty($find)) {

                        $insert['cash'] = $cash;

                        $com_mdoel->dataAdd($insert);

                        $id = $com_mdoel->getLastInsID();

                    } else {

                        $id = $find['id'];

                        $update = [

                            'cash' => $find['cash'] + $cash
                        ];
                        //加佣金
                        $com_mdoel->dataUpdate(['id' => $find['id']], $update);

                    }

                    $insert = [

                        'uniacid' => $order['uniacid'],

                        'order_goods_id' => $v['id'],

                        'commission_id' => $id,

                        'cash' => $cash,

                        'num' => $v['num'],

                        'balance' => $ser['com_balance']
                    ];
                    //添加到自订单记录表
                    $res = $com_goods_mdoel->dataAdd($insert);

                }

            }

        }

        return true;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-26 23:39
     * @功能说明:添加佣金
     */
    public function commissionAddData($order)
    {

        $user_model = new User();

        $config_model = new Config();

        $config = $config_model->dataInfo(['uniacid' => $order['uniacid']]);

        $dis = [

            'id' => $order['user_id'],

        ];
        //上级
        $top_id = $user_model->where($dis)->value('pid');

        $top = $user_model->dataInfo(['id' => $top_id]);

        $total_cash = 0;

        if (!empty($top)) {

            if ($config['fx_check'] == 1 && $top['is_fx'] == 0) {

                return $total_cash;
            }

            $top = $top['id'];

            $ser_model = new Service();

            $com_mdoel = new Commission();

            $com_goods_mdoel = new CommissionGoods();

            foreach ($order['order_goods'] as $v) {
                //查看是否有分销
                $ser = $ser_model->dataInfo(['id' => $v['goods_id']]);

                $user_agent_balance = 0;

                if ($ser['com_balance'] <= 0) {
                    //全局设置
                    $ser['com_balance'] = getConfigSetting($order['uniacid'], 'user_agent_balance');

                    $user_agent_balance = 1;
                }

                if (!empty($ser['com_balance'])) {

                    $insert = [

                        'uniacid' => $order['uniacid'],

                        'user_id' => $order['user_id'],

                        'top_id' => $top,

                        'order_id' => $order['id'],

                        'order_code' => $order['order_code'],

                        'balance' => $user_agent_balance == 1 ? $ser['com_balance'] : 0,

                    ];

                    $find = $com_mdoel->dataInfo($insert);

                    $cash = $v['true_price'] * $ser['com_balance'] / 100 * $v['num'];

                    $total_cash += $cash;

                    if (empty($find)) {

                        $insert['cash'] = $cash;

                        $insert['status'] = -1;

                        $insert['admin_id'] = !empty($order['admin_id']) ? $order['admin_id'] : 0;

                        $com_mdoel->dataAdd($insert);

                        $id = $com_mdoel->getLastInsID();

                    } else {

                        $id = $find['id'];

                        $update = [

                            'cash' => $total_cash
                        ];
                        //加佣金
                        $com_mdoel->dataUpdate(['id' => $find['id']], $update);

                    }

                    $insert = [

                        'uniacid' => $order['uniacid'],

                        'order_goods_id' => $v['id'],

                        'commission_id' => $id,

                        'cash' => $cash,

                        'num' => $v['num'],

                        'balance' => $ser['com_balance']
                    ];
                    //添加到自订单记录表
                    $res = $com_goods_mdoel->dataAdd($insert);

                }

            }

        }

        return $total_cash;

    }


    /**
     * @param $order
     * @功能说明:向导佣金
     * @author chenniang
     * @DataTime: 2022-06-12 13:14
     */
    public function carCommission($order)
    {

        if (isset($order['true_car_price']) && $order['true_car_price'] > 0) {

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['coach_id'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 8,

                'cash' => $order['true_car_price'],

                'admin_id' => $order['admin_id'],

                'balance' => 0,

                'status' => -1,
            ];

            if (empty($order['coach_id'])) {

                $insert['cash_status'] = 0;
            }

            $res = $this->dataAdd($insert);
        }

        return true;
    }

    /**
     * @param $order
     * @功能说明:向导佣金
     * @author chenniang
     * @DataTime: 2022-06-12 13:14
     */
    public function coachCommission($order, $type = 1)
    {

        if (isset($order['coach_cash']) && $order['coach_cash'] > 0) {

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['coach_id'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 3,

                'cash' => $order['coach_cash'],

                'admin_id' => $order['admin_id'],

                'balance' => $order['coach_balance'],

                'status' => $type == 1 ? -1 : 1,

                'order_type' => $type
            ];

            if (empty($order['coach_id'])) {

                $insert['cash_status'] = 0;
            }

            $res = $this->dataAdd($insert);

            return $res;
        }
        return 0;
    }


    /**
     * @author chenniang
     * @DataTime: 2022-06-12 13:32
     * @功能说明:加盟商佣金
     */
    public function adminCommission($order, $type = 1)
    {

        if (!empty($order['admin_id']) && $order['admin_cash'] > 0) {

            $admin_model = new Admin();

            $city_type = $admin_model->where(['id' => $order['admin_id']])->value('city_type');

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['admin_id'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 2,

                'cash' => $order['admin_cash'],

                'admin_id' => $order['admin_id'],

                'balance' => $order['admin_balance'],

                'status' => $type == 1 ? -1 : 1,

                'order_type' => $type,

                'city_type' => $city_type,

            ];
            //如果是线下向导需要把佣金返回给代理商
            if (empty($order['coach_id'])) {

                $insert['coach_cash'] = $order['coach_cash'];

                $insert['car_cash'] = $order['true_car_price'];

            }

            $res = $this->dataAdd($insert);
        }

        return true;

    }


    /**
     * @author chenniang
     * @DataTime: 2022-06-12 13:32
     * @功能说明:加盟商上级佣金
     */
    public function adminLevelCommission($order, $type = 1)
    {

        if (!empty($order['admin_pid'])) {

            $admin_model = new Admin();

            $city_type = $admin_model->where(['id' => $order['admin_pid']])->value('city_type');

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['admin_pid'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 5,

                'cash' => $order['level_cash'],

                'admin_id' => $order['admin_pid'],

                'balance' => $order['level_balance'],

                'status' => $type == 1 ? -1 : 1,

                'order_type' => $type,

                'city_type' => $city_type,

            ];

            $res = $this->dataAdd($insert);

        }

        return true;

    }


    /**
     * @author chenniang
     * @DataTime: 2022-06-12 13:32
     * @功能说明:省代理商佣金
     */
    public function adminProvinceCommission($order, $type = 1)
    {

        if (!empty($order['p_admin_pid'])) {

            $admin_model = new Admin();

            $city_type = $admin_model->where(['id' => $order['p_admin_pid']])->value('city_type');

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['p_admin_pid'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 6,

                'cash' => $order['p_level_cash'],

                'admin_id' => $order['p_admin_pid'],

                'balance' => $order['p_level_balance'],

                'status' => $type == 1 ? -1 : 1,

                'order_type' => $type,

                'city_type' => $city_type,

            ];

            $res = $this->dataAdd($insert);

        }

        return true;

    }

    /**
     * @author chenniang
     * @DataTime: 2021-08-28 14:35
     * @功能说明:佣金到账
     */
    public function successCommission($order_id)
    {

        $comm = $this->dataInfo(['order_id' => $order_id, 'status' => 1]);

        if (!empty($comm)) {

            $user_model = new User();

            $user = $user_model->dataInfo(['id' => $comm['top_id']]);

            if (!empty($user)) {

                $update = [

                    'balance' => $user['balance'] + $comm['cash'],

                    'cash' => $user['cash'] + $comm['cash'],
                ];

                $user_model->dataUpdate(['id' => $comm['top_id']], $update);

                $this->dataUpdate(['id' => $comm['id']], ['status' => 2, 'cash_time' => time()]);

            }

        }

        return true;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-28 14:48
     * @功能说明:退款的时候要减去分销
     */
    public function refundComm($refund_id)
    {

        $refund_model = new RefundOrder();

        $com_goods_mdoel = new CommissionGoods();

        $order_model = new Order();

        $refund_order = $refund_model->dataInfo(['id' => $refund_id]);

        if (!empty($refund_order)) {
            //查询这笔等待有无佣金
            $comm = $this->dataInfo(['order_id' => $refund_order['order_id'], 'status' => 1, 'type' => 1]);

            if (!empty($comm)) {

                foreach ($refund_order['order_goods'] as $v) {

                    $comm_goods = $com_goods_mdoel->dataInfo(['commission_id' => $comm['id'], 'order_goods_id' => $v['order_goods_id']]);

                    if (!empty($comm_goods)) {

                        $comm_goods_cash = $comm_goods['cash'] / $comm_goods['num'];

                        $true_num = $comm_goods['num'] - $v['num'];

                        $true_num = $true_num > 0 ? $true_num : 0;

                        $update = [

                            'num' => $true_num,

                            'cash' => $comm_goods_cash * $true_num
                        ];

                        $com_goods_mdoel->dataUpdate(['id' => $comm_goods['id']], $update);
                    }

                }

                $total_cash = $com_goods_mdoel->where(['commission_id' => $comm['id']])->sum('cash');

                $total_cash = $total_cash > 0 ? $total_cash : 0;

                $update = [

                    'cash' => $total_cash,

                    'status' => $total_cash > 0 ? 1 : -1
                ];

                $this->dataUpdate(['id' => $comm['id']], $update);

                $order_model->dataUpdate(['id' => $refund_order['order_id']], ['user_cash' => $total_cash]);

            }

        }

        return true;

    }


    /**
     * @param $v
     * @param $pay_order
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-03-31 00:35
     */
    public function adminCashCustom($v, $pay_order)
    {

        if ($v['type'] == 2) {

            if ($v['city_type'] == 1) {

                $pay_order['city_balance'] = $v['balance'];

                $pay_order['admin_balance_name'] = 'city_balance';

                $pay_order['admin_cash_name'] = 'city_cash';
            } else {

                $pay_order['district_balance'] = $v['balance'];

                $pay_order['admin_balance_name'] = 'district_balance';

                $pay_order['admin_cash_name'] = 'district_cash';
            }

        }

        if ($v['type'] == 5) {

            $pay_order['city_balance'] = $v['balance'];

            $pay_order['level_balance_name'] = 'city_balance';

            $pay_order['level_cash_name'] = 'city_cash';
        }

        return $pay_order;

    }


    /**
     * @author chenniang
     * @DataTime: 2022-06-13 14:50
     * @功能说明:订单退款修改佣金记录
     */
    public function refundCash($pay_order)
    {

        $order_model = new Order();

        $level_cash_data = $this->where(['order_id' => $pay_order['id'], 'status' => 1, 'order_type' => 1])->where('type', 'in', [1, 2, 5, 6, 9, 10, 11])->select()->toArray();
        //查询有无二级市级代理佣金或者省代
        if (!empty($level_cash_data)) {

            foreach ($level_cash_data as $v) {

                $cash_text = $this->getTypeText($v['type']);

                if (in_array($v['type'], [5, 6])) {

                    $pay_order[$cash_text['admin_id']] = $v['admin_id'];
                }

                if (in_array($v['type'], [9, 10])) {

                    $pay_order[$cash_text['admin_id']] = $v['top_id'];
                }

                $pay_order[$cash_text['balance']] = $v['balance'];

                $pay_order = $this->adminCashCustom($v, $pay_order);

            }
        }
        //修改佣金信息
        $cash_data = $order_model->getCashData($pay_order, 2);

        $cash_data = $cash_data['data'];

        $cash_order_update = [

            'admin_cash' => $cash_data['admin_cash'],

            'coach_cash' => $cash_data['coach_cash'],

            'company_cash' => $cash_data['company_cash'],

            'user_cash' => $cash_data['user_c_cash'],

            'broker_cash' => $cash_data['broker_cash'],

            'channel_cash' => $cash_data['channel_cash'],

            'broker_coach_cash' => $cash_data['broker_coach_cash'],

            'broker_agent_cash' => $cash_data['broker_agent_cash'],

        ];

        $arr = [1, 2, 3, 5, 6, 9, 10, 11, 16];

        foreach ($arr as $value) {

            $cash_text = $this->getTypeText($value);

            if (key_exists($cash_text['cash'], $cash_data)) {

                if ($value == 16) {

                    $id = $this->where(['order_id' => $pay_order['id'], 'type' => $value, 'status' => 1])->value('id');

                    $this->commissionShare($cash_data, $id);
                }

                //修改各类佣金记录
                $this->dataUpdate(['order_id' => $pay_order['id'], 'type' => $value, 'status' => 1], ['cash' => $cash_data[$cash_text['cash']]]);
            }
        }

        $res = $order_model->dataUpdate(['id' => $pay_order['id']], $cash_order_update);

        return $res;
    }

    /**
     * @Desc: 增加分摊记录
     * @param $order
     * @param $id
     * @return true
     * @Auther: shurong
     * @Time: 2023/12/19 16:43
     */
    public function commissionShare($order, $id)
    {

        CommissionShare::where('comm_id', $id)->delete();

        if (isset($order['broker_coach_cash']) && $order['broker_coach_cash'] > 0) {

            CommissionShare::add($order['uniacid'], $id, $order['broker_coach_balance'], $order['broker_coach_cash'], 1, $order['coach_id'], $order['id'], 16);
        }

        if (isset($order['broker_agent_cash']) && $order['broker_agent_cash'] > 0) {

            CommissionShare::add($order['uniacid'], $id, $order['broker_agent_balance'], $order['broker_agent_cash'], 2, $order['admin_id'], $order['id'], 16);
        }

        return true;
    }


    /**
     * @param $type
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-02-17 16:05
     */
    public function getTypeText($type)
    {

        switch ($type) {

            case 1:
                $arr['cash'] = 'user_c_cash';

                $arr['balance'] = 'user_agent_balance';

                break;

            case 2:
                $arr['cash'] = 'admin_cash';

                $arr['balance'] = 'admin_balance';

                $arr['admin_id'] = 'admin_pid';

                break;
            case 3:
                $arr['cash'] = 'coach_cash';

                $arr['balance'] = 'coach_balance';

                break;

            case 5:
                $arr['cash'] = 'level_cash';

                $arr['balance'] = 'level_balance';

                $arr['admin_id'] = 'admin_pid';

                break;

            case 6:
                $arr['cash'] = 'p_level_cash';

                $arr['balance'] = 'p_level_balance';

                $arr['admin_id'] = 'p_admin_pid';

                break;
            case 9:
                $arr['cash'] = 'partner_cash';

                $arr['balance'] = 'coach_agent_balance';

                $arr['admin_id'] = 'partner_id';

                break;
            case 10:
                $arr['cash'] = 'channel_cash';

                $arr['balance'] = 'channel_balance';

                $arr['admin_id'] = 'channel_id';

                break;

            case 11:
                $arr['cash'] = 'company_cash';

                $arr['balance'] = 'company_balance';

                break;
            case 16:
                $arr['cash'] = 'broker_cash';

                $arr['balance'] = 'broker_balance';

                break;
        }

        return $arr;
    }


    /**
     *
     * @param int $type 1下单 2退单
     * @param int $order_type 订单类型 1服务订单 2邀约订单
     * @return array
     */
    public function commissionData($type = 1, $order_type = 1)
    {

        $arr = [
            //向导佣金
            [

                'action_name' => "getCoachCash",

                'parameter' => 'coach_balance',

            ],
            //用户分销
            [

                'action_name' => $order_type == 1 ? ($type == 1 ? 'getUserCash' : 'getUserCashRefund') : 'getDemandUserCash',

                'parameter' => $order_type == 1 ? 'user_agent_balance' : 'demand_user_balance',
            ],
            //合伙人
//            [
//
//                'action_name' => 'getPartnerCash',
//
//                'parameter'   => 'coach_agent_balance',
//            ],
            //平台
            [

                'action_name' => 'getCompanyCash',

                'parameter' => 'admin_balance',
            ],
            //渠道商
            [

                'action_name' => 'getChannelCash',

                'parameter' => 'channel_balance',
            ],
            //省代
//            [
//
//                'action_name' => 'getProvinceCash',
//
//                'parameter'   => 'p_level_balance',
//
//            ],
            //城市代理
//            [
//
//                'action_name' => 'getCityCash',
//
//                'parameter'   => 'level_balance',
//            ],

        ];

        return $arr;
    }

    /**
     * 固定比例
     * @param int $type
     * @return array
     */
    /**
     *
     * @param int $type 1下单 2退单
     * @param int $order_type 订单类型 1服务订单 2邀约订单
     * @return array
     */
    public function commissionData1($type = 1, $order_type = 1)
    {

        $arr = [
            //向导佣金
            [

                'action_name' => "getCoachCash",

                'parameter' => 'coach_balance',

            ],
            //用户分销
            [

                'action_name' => $order_type == 1 ? ($type == 1 ? 'getUserCash' : 'getUserCashRefund') : 'getDemandUserCash',

                'parameter' => $order_type == 1 ? 'user_agent_balance' : 'demand_user_balance',
            ],
            //代理商
            [

                'action_name' => 'getAdminCash',

                'parameter' => 'admin_balance',
            ],
            //渠道商
            [

                'action_name' => 'getChannelCash',

                'parameter' => 'channel_balance',
            ],
            //合伙人
//            [
//
//                'action_name' => 'getPartnerCash',
//
//                'parameter'   => 'coach_agent_balance',
//            ],
            //省代
//            [
//
//                'action_name' => 'getProvinceCash',
//
//                'parameter'   => 'p_level_balance',
//
//            ],
            //城市代理
//            [
//
//                'action_name' => 'getCityCash',
//
//                'parameter'   => 'level_balance',
//            ],

        ];

        return $arr;
    }


    /**
     * @param $order
     * @功能说明:计算各类分销比例
     * @author chenniang
     * @DataTime: 2023-03-22 11:52
     */
    public function balanceData($order, $admin_id = 0)
    {

        $coach_model = new Coach();

        $admin_model = new Admin();

        $clock_model = new ClockSetting();

        $channel_model = new ChannelList();

        $coach = $coach_model->dataInfo(['id' => $order['coach_id']]);

        //技师比例
        $balance = $coach_model->getCoachBalance($order['cash_type'], $order['coach_id'], $order['uniacid']);

        if (isset($balance['code'])) {

            return $balance;
        }

        //向导佣金比列
        $order['coach_balance'] = $balance;
        //加钟的时候比例可能是特殊设置
        $order['coach_balance'] = $clock_model->getCoachBalance($order);

        //单独设置的比例优先级最高
        $coach_balance = $this->getServiceCoachBalance($order);

        if ($coach_balance > 0) {

            $order['coach_balance'] = $coach_balance;
        }

        $order['admin_id'] = $order['partner_id'] = 0;

        if (empty($coach) || $coach['agent_type'] == 1) {

            $admin_id = !empty($coach['admin_id']) ? $coach['admin_id'] : $admin_id;
            //代理商各类分销比例
            $order = $admin_model->agentBalanceData($admin_id, $order);

        } else {
            //合伙人分销比例
            $order = $coach_model->partnerBalance($coach, $order);

        }

        //渠道商比例
        $channel = $channel_model->dataInfo(['id' => $order['channel_id']]);

        $order['channel_balance'] = $channel['balance'] ?? 0;

        //经纪人
        $order = Broker::getBrokerBalance($order);

        return $order;
    }

    /**
     * @Desc:获取服务技师的佣金比例
     * @param $order
     * @return int|mixed
     * @Auther: shurong
     * @Time: 2024/4/15 10:43
     */
    protected function getServiceCoachBalance($order)
    {
        $ser_id = OrderGoods::where('order_id', $order['id'])->value('goods_id');

        $balance = ServiceCoach::where(['ser_id' => $ser_id, 'coach_id' => $order['coach_id']])->value('balance');

        return empty($balance) ? 0 : $balance;
    }

    /**
     * @author chenniang
     * @DataTime: 2023-03-22 10:19
     * @功能说明:计算每类佣金的金额
     */

    public function cashData($order, $type, $order_type = 1)
    {

        //获取计算佣金比例的方法
        if ($order['cash_type'] == 1) {
            $list = $this->commissionData($type, $order_type);
        } else {
            $list = $this->commissionData1($type, $order_type);
        }

        //循环调取
        foreach ($list as $key => $value) {

            $balance = isset($order[$value['parameter']]) ? $order[$value['parameter']] : 0;

            $order['surplus_cash'] = $key == 0 ? $order['true_service_price'] : $order['surplus_cash'];

            $action_name = $value['action_name'];

            $order = $this->$action_name($balance, $order, $order['surplus_cash']);

        }

        //佣金方式不同，最后的佣金获得者也不一样
        if ($order['cash_type'] == 1) {
            //代理商
            if ($order['admin_id'] > 0) {

                $order['admin_cash'] = $order['surplus_cash'];
            } else {

                $order['company_cash'] += $order['surplus_cash'];
            }
        } else {
            //平台
            $order['company_cash'] = $order['surplus_cash'];
        }

        //获取经纪人佣金  只有特殊处理
        $order = $this->getBrokerCash($order);

        return $order;

    }

    /**
     * @Desc: 获取经纪人佣金
     * @param $order
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/7 15:01
     */
    public function getBrokerCash($order)
    {
        $broker_cash = 0;

        $broker_coach_cash = 0;

        $broker_agent_cash = 0;

        $broker_admin_cash = 0;

        if (!empty($order['broker_id']) && $order['broker_balance'] > 0) {

            $all_cash = round($order['true_service_price'] * $order['broker_balance'] / 100, 2);

            if ($order['coach_cash'] > 0) {

                $broker_coach_cash = round($all_cash * $order['broker_coach_balance'] / 100, 2);

                $broker_coach_cash = $order['coach_cash'] > $broker_coach_cash ? $broker_coach_cash : $order['coach_cash'];
            }

            $last_agent_cosh = $all_cash - $broker_coach_cash;
            //有代理商且有分佣
            if (!empty($order['admin_id']) && $order['admin_cash'] > 0) {

                $broker_agent_cash = round($all_cash * $order['broker_agent_balance'] / 100, 2);

                $broker_agent_cash = $last_agent_cosh < $broker_agent_cash ? $last_agent_cosh : $broker_agent_cash;

                $broker_agent_cash = $order['admin_cash'] < $broker_agent_cash ? $order['admin_cash'] : $broker_agent_cash;

            } elseif (!empty($order['admin_id']) && $order['admin_cash'] == 0) {

                //有代理商且无分佣
                $broker_agent_cash = 0;
            } else {
                //无代理商 代理商承担金额由平台承担
                $broker_admin_cash = round($all_cash * $order['broker_agent_balance'] / 100, 2);

                $broker_admin_cash = $last_agent_cosh < $broker_admin_cash ? $last_agent_cosh : $broker_admin_cash;

                $broker_admin_cash = $order['company_cash'] < $broker_admin_cash ? $order['company_cash'] : $broker_admin_cash;
            }

            if (!isset($order['broker_admin_balance'])) {

                $order['broker_admin_balance'] = 100 - $order['broker_agent_balance'] - $order['broker_coach_balance'];
            }

            if ($order['broker_admin_balance'] > 0) {

                $broker_admin_cash += round($all_cash * $order['broker_admin_balance'] / 100, 2);

                $broker_admin_cash = $order['company_cash'] < $broker_admin_cash ? $order['company_cash'] : $broker_admin_cash;
            }


            $broker_cash = $broker_coach_cash + $broker_agent_cash + $broker_admin_cash;

            //经纪人佣金不能大于全部佣金
            if ($broker_cash > $all_cash) {

                $broker_cash = $broker_cash > $all_cash ? $all_cash : $broker_cash;

                $bad = $broker_cash - $all_cash;

                if ($broker_admin_cash > 0) {

                    $broker_admin_cash = $broker_admin_cash > 0 ? $broker_admin_cash - $bad : $broker_admin_cash;
                } elseif ($broker_agent_cash > 0) {

                    $broker_agent_cash = $broker_agent_cash > 0 ? $broker_agent_cash - $bad : $broker_agent_cash;
                } elseif ($broker_coach_cash > 0) {

                    $broker_coach_cash = $broker_coach_cash > 0 ? $broker_coach_cash - $bad : $broker_coach_cash;
                }
            }


            $order['coach_cash'] -= $broker_coach_cash;

            $order['admin_cash'] -= $broker_agent_cash;

            $order['company_cash'] -= $broker_admin_cash;
        }

        $order['broker_cash'] = $broker_cash;

        $order['broker_coach_cash'] = $broker_coach_cash;

        $order['broker_agent_cash'] = $broker_agent_cash;

        $order['broker_admin_cash'] = $broker_admin_cash;

        return $order;
    }

    /**
     * @param $balance
     * @param $order
     * @param $cash
     * @功能说明:合伙人佣金
     * @author chenniang
     * @DataTime: 2023-03-22 11:14
     */
    public function getPartnerCash($balance, $order, $cash)
    {

        $order['partner_cash'] = round($balance * $order['true_service_price'] / 100, 2);

        $order['partner_cash'] = $order['partner_cash'] > $cash ? $cash : $order['partner_cash'];

        $order['surplus_cash'] = $cash - $order['partner_cash'];

        return $order;

    }


    /**
     * @param $balance
     * @param $order
     * @param $cash
     * @功能说明:平台
     * @author chenniang
     * @DataTime: 2023-03-22 11:12
     */
    public function getCompanyCash($balance, $order, $cash)
    {

        $order['company_cash'] = round($balance * $order['true_service_price'] / 100, 2);

        $order['company_cash'] = $order['company_cash'] > $cash ? $cash : $order['company_cash'];

        $order['surplus_cash'] = $cash - $order['company_cash'];

        return $order;
    }

    /**
     * 代理商
     * @param $balance
     * @param $order
     * @param $cash
     * @return mixed
     */
    public function getAdminCash($balance, $order, $cash)
    {
        $order['admin_cash'] = round($balance * $order['true_service_price'] / 100, 2);

        $order['admin_cash'] = $order['admin_cash'] > $cash ? $cash : $order['admin_cash'];

        $order['surplus_cash'] = $cash - $order['admin_cash'];

        return $order;
    }

    /**
     * @param $balance
     * @param $order
     * @param $cash
     * @功能说明:向导佣金
     * @author chenniang
     * @DataTime: 2023-03-21 17:55
     */
    public function getCoachCash($balance, $order, $cash)
    {
        //向导佣金
        $order['coach_cash'] = round($balance * $order['true_service_price'] / 100, 2);

        $order['coach_cash'] = $order['coach_cash'] > $cash ? $cash : $order['coach_cash'];

        $order['surplus_cash'] = $cash - $order['coach_cash'];

        return $order;
    }


    /**
     * @param $balance
     * @param $order
     * @param $cash
     * @功能说明:省代佣金
     * @author chenniang
     * @DataTime: 2023-03-21 18:05
     */
    public function getProvinceCash($balance, $order, $cash)
    {
        //上级代理提成
        $order['p_level_cash'] = round($balance * $order['true_service_price'] / 100, 2);

        $order['p_level_cash'] = $order['p_level_cash'] - $cash > 0 ? $cash : $order['p_level_cash'];

        $order['surplus_cash'] = $cash - $order['p_level_cash'];

        return $order;
    }


    /**
     * @param $balance
     * @param $order
     * @param $cash
     * @功能说明:渠道商佣金
     * @author chenniang
     * @DataTime: 2023-03-21 18:05
     */
    public function getChannelCash($balance, $order, $cash)
    {
        //上级代理提成
        $order['channel_cash'] = round($balance * $order['true_service_price'] / 100, 2);

        $order['channel_cash'] = $order['channel_cash'] - $cash > 0 ? $cash : $order['channel_cash'];

        $order['surplus_cash'] = $cash - $order['channel_cash'];

        return $order;
    }


    /**
     * @param $order
     * @param $cash
     * @功能说明:区县佣金
     * @author chenniang
     * @DataTime: 2023-03-21 18:20
     */
    public function getCityCash($balance, $order, $cash)
    {
        //上级代理提成
        $order['level_cash'] = round($balance * $order['true_service_price'] / 100, 2);

        $order['level_cash'] = $order['level_cash'] - $cash > 0 ? $cash : $order['level_cash'];

        $order['surplus_cash'] = $cash - $order['level_cash'];

        return $order;
    }


    /**
     * @param $balance
     * @param $order
     * @param $cash
     * @功能说明:用户分销
     * @author chenniang
     * @DataTime: 2023-03-22 11:09
     */
    public function getUserCash($balance, $order, $cash)
    {

        $user_model = new User();

        $config_model = new Config();

        $dis = [

            'id' => $order['user_id'],

        ];

        $total_cash = 0;
        //上级
        $top_id = $user_model->where($dis)->value('pid');

        $top = $user_model->dataInfo(['id' => $top_id]);

        $config = $config_model->dataInfo(['uniacid' => $order['uniacid']]);

        if (!empty($top) && $config['fx_check'] == 1 && $top['is_fx'] == 0) {

            return $order;
        }

        if (!empty($top)) {

            $order['user_top_id'] = $top['id'];

            $ser_model = new Service();

            if (!empty($balance) && $balance > 0) {

                $total_cash = $balance * $order['true_service_price'];

            } else {

                foreach ($order['order_goods'] as $v) {
                    //查看是否有分销
                    $ser = $ser_model->dataInfo(['id' => $v['goods_id']]);

                    if (getConfigSetting($order['uniacid'], 'user_agent_balance') > 0) {

                        $ser['com_balance'] = getConfigSetting($order['uniacid'], 'user_agent_balance');
                    }

                    $order['user_balance'] = $ser['com_balance'];

                    $price = $v['true_price'] * $ser['com_balance'] / 100 * $v['num'];

                    $total_cash += $price;
                }
            }

        }

        $order['user_c_cash'] = $total_cash > $cash ? $cash : $total_cash;

        $order['surplus_cash'] = $cash - $order['user_c_cash'];

        return $order;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-28 14:48
     * @功能说明:退款的时候要减去分销
     */
    public function getUserCashRefund($balance, $order, $cash)
    {

        $refund_model = new RefundOrder();

        $com_goods_mdoel = new CommissionGoods();

        $order_model = new Order();

        $refund_order = $refund_model->dataInfo(['id' => $order['refund_id']]);

        $total_cash = 0;

        if (!empty($refund_order)) {
            //查询这笔等待有无佣金
            $comm = $this->dataInfo(['order_id' => $refund_order['order_id'], 'status' => 1, 'type' => 1]);

            if (!empty($comm)) {

                foreach ($refund_order['order_goods'] as $v) {

                    $comm_goods = $com_goods_mdoel->dataInfo(['commission_id' => $comm['id'], 'order_goods_id' => $v['order_goods_id']]);

                    if (!empty($comm_goods)) {

                        $comm_goods_cash = $comm_goods['cash'] / $comm_goods['num'];

                        $true_num = $comm_goods['num'] - $v['num'];

                        $true_num = $true_num > 0 ? $true_num : 0;

                        $update = [

                            'num' => $true_num,

                            'cash' => $comm_goods_cash * $true_num
                        ];

                        $com_goods_mdoel->dataUpdate(['id' => $comm_goods['id']], $update);
                    }

                }

                $total_cash = $com_goods_mdoel->where(['commission_id' => $comm['id']])->sum('cash');

                $total_cash = $total_cash > 0 ? $total_cash : 0;

//                $update = [
//
//                    'cash' => $total_cash,
//
//                    'status' => $total_cash>0?1:-1
//                ];

                //  $this->dataUpdate(['id'=>$comm['id']],$update);

                // $order_model->dataUpdate(['id'=>$refund_order['order_id']],['user_cash'=>$total_cash]);

            }

        }

        $order['user_c_cash'] = $total_cash > $cash ? $cash : $total_cash;

        $order['surplus_cash'] = $cash - $order['user_c_cash'];

        return $order;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-26 23:39
     * @功能说明:添加佣金
     */
    public function commissionAddDataV2($order, $type = 1)
    {

        if (!empty($order['user_top_id']) && $order['user_c_cash'] > 0) {

            $ser_model = new Service();

            $com_mdoel = new Commission();

            $com_goods_mdoel = new CommissionGoods();

            $top = $order['user_top_id'];

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $top,

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'balance' => getConfigSetting($order['uniacid'], 'user_agent_balance'),

                'cash' => $order['user_c_cash'],

                'status' => $type == 1 ? -1 : 1,

                'order_type' => $type,

                'admin_id' => !empty($order['admin_id']) ? $order['admin_id'] : 0

            ];

            $com_mdoel->dataAdd($insert);

            $id = $com_mdoel->getLastInsID();

            foreach ($order['order_goods'] as $v) {
                //查看是否有分销
                $ser = $ser_model->dataInfo(['id' => $v['goods_id']]);

                if (getConfigSetting($order['uniacid'], 'user_agent_balance') > 0) {

                    $ser['com_balance'] = getConfigSetting($order['uniacid'], 'user_agent_balance');
                }

                if (!empty($ser['com_balance'])) {

                    $cash = $v['true_price'] * $ser['com_balance'] / 100 * $v['num'];

                    $insert = [

                        'uniacid' => $order['uniacid'],

                        'order_goods_id' => $v['id'],

                        'commission_id' => $id,

                        'cash' => $cash,

                        'num' => $v['num'],

                        'balance' => $ser['com_balance']
                    ];
                    //添加到自订单记录表
                    $res = $com_goods_mdoel->dataAdd($insert);

                }

            }

        }

        return true;

    }


    /**
     * @param $order
     * @功能说明:增加合伙人佣金
     * @author chenniang
     * @DataTime: 2023-03-22 15:55
     */
    public function partnerCommission($order, $type = 1)
    {

        if (!empty($order['partner_id']) && isset($order['coach_agent_balance'])) {

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['partner_id'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 9,

                'cash' => $order['partner_cash'],

                'admin_id' => $order['partner_id'],

                'balance' => $order['coach_agent_balance'],

                'status' => $type == 1 ? -1 : 1,

                'order_type' => $type,


            ];

            $res = $this->dataAdd($insert);

        }

        return true;


    }


    /**
     * @param $order
     * @功能说明:增加平台佣金
     * @author chenniang
     * @DataTime: 2023-03-22 15:55
     */
    public function companyCommission($order)
    {

        if (!empty($order['company_cash'])) {

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => 0,

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 11,

                'cash' => $order['company_cash'],

                'balance' => $order['company_balance'],

                'status' => -1,

            ];

            $res = $this->dataAdd($insert);

        }

        return true;


    }


    /*
     * @param $order
     * @功能说明:技师佣金 （废弃 2023-07）
     */
    public static function coachCommissionDemand($order_id)
    {
        $order = DemandOrder::getInfo(['id' => $order_id]);
        $insert = [

            'uniacid' => $order['uniacid'],

            'user_id' => $order['user_id'],

            'top_id' => $order['coach_id'],

            'order_id' => $order['id'],

            'order_code' => $order['order_code'],

            'type' => 3,

            'cash' => $order['coach_cash'],

            'balance' => $order['coach_balance'],

            'status' => 1,

            'order_type' => 2
        ];

        $insert['create_time'] = time();
        $res = self::insert($insert);

        return $res;
    }

    /**
     * 获取需求订单分销订单佣金
     * @param $order
     * @return array
     */
    public function getDemandCash($order)
    {
        $order = $this->getDemandRatio($order);
        //没有技师等级
        if (!empty($order['code']) && $order['code'] == 300) {
            return $order;
        }
        $order['true_service_price'] = $order['pay_price'];
        $order = $this->cashData($order, 1, 2);

        //向导佣金记录
        $this->coachCommission($order, 2);
        //用户分销
        $this->demandUserCommission($order);
//        $this->commissionAddDataV2($order,2);
        //有二级
        $this->adminLevelCommission($order, 2);
        //加盟商佣金记录
        $this->adminCommission($order, 2);
        //向导合伙人
        $this->partnerCommission($order, 2);


        $arr = ['coach_balance', 'admin_balance', 'admin_id', 'user_cash', 'company_cash', 'coach_cash', 'admin_cash', 'partner_id', 'user_balance', 'user_fx_id'];
        $list = [];
        foreach ($arr as $value) {

            if (key_exists($value, $order)) {

                $list[$value] = $order[$value];
            }
        }

        $arr_data['update'] = $list;

        $arr_data['data'] = $order;

        return $arr_data;
    }

    /**
     * 获取需求订单各类订单佣金
     * @param $order
     * @return array
     */
    public function getDemandRatio($order)
    {
        $coach_model = new Coach();
        $admin_model = new Admin();
        $coach = $coach_model->dataInfo(['id' => $order['coach_id']]);

        //确定是浮动比例还是固定比例
        if ($order['cash_type'] == 1) {
            //向导等级
            $coach_level = $coach_model->getCoachLevel($order['coach_id'], $order['uniacid']);
            if (empty($coach_level) && !empty($order['coach_id'])) {
                return ['code' => 300];
            }
            $coach_level['balance'] = !empty($coach_level) ? $coach_level['balance'] : 0;
        } else {
            $coach_level['balance'] = $coach['cash_balance'];
        }

        //向导佣金比列
        $order['coach_balance'] = $coach_level['balance'];
        $order['admin_id'] = $order['partner_id'] = 0;
        if ($coach['agent_type'] == 1) {
            $admin_id = $coach['admin_id'];
            //代理商各类分销比例
            $order = $admin_model->agentBalanceData($admin_id, $order);
        } else {
            //合伙人分销比例
            $order = $coach_model->partnerBalance($coach, $order);
        }
        return $order;
    }

    /**
     * 邀约订单用户分销佣金/比例计算
     * @param $balance
     * @param $order
     * @param $cash
     * @return mixed
     */
    public function getDemandUserCash($balance, $order, $cash)
    {
        $user_model = new User();
        $config_model = new Config();
        $dis = [
            'id' => $order['user_id'],
        ];
        //上级
        $top_id = $user_model->where($dis)->value('pid');
        $top = $user_model->dataInfo(['id' => $top_id]);
        if (empty($top)) {
            $order['user_fx_id'] = $top_id;
            $order['user_cash'] = 0;
            return $order;
        }
        $config = $config_model->dataInfo(['uniacid' => $order['uniacid']]);
        if (!empty($top) && $config['fx_check'] == 1 && $top['is_fx'] == 0) {
            return $order;
        }
        if (empty($balance)) {
            $balance = $config['demand_user_balance'];
        }
        $order['user_balance'] = $balance;
        $order['user_fx_id'] = $top_id;
        //向导佣金
        $order['user_cash'] = round($balance * $order['true_service_price'] / 100, 2);
        $order['user_cash'] = $order['user_cash'] > $cash ? $cash : $order['user_cash'];
        $order['surplus_cash'] = $cash - $order['user_cash'];
        return $order;
    }

    /**
     * 邀约订单用户分销佣金记录插入
     * @param $order
     * @return bool|int|string
     */
    public function demandUserCommission($order)
    {
        if ($order['user_cash'] > 0 && $order['user_fx_id'] > 0) {
            $com_mdoel = new Commission();
            $insert = [
                'uniacid' => $order['uniacid'],
                'user_id' => $order['user_id'],
                'top_id' => $order['user_fx_id'],
                'order_id' => $order['id'],
                'order_code' => $order['order_code'],
                'balance' => $order['user_balance'],
                'cash' => $order['user_cash'],
                'status' => 1,
                'order_type' => 2,
                'admin_id' => !empty($order['admin_id']) ? $order['admin_id'] : 0
            ];

            $res = $com_mdoel->dataAdd($insert);
            return $res;
        }
        return true;
    }

    /**
     * @Desc: 渠道商佣金记录
     * @param $order
     * @return true
     * @Auther: shurong
     * @Time: 2023/10/27 17:14
     */
    public function channelCommission($order)
    {

        if (!empty($order['channel_id']) && $order['channel_cash'] > 0) {

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['channel_id'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 10,

                'cash' => $order['channel_cash'],

                'admin_id' => $order['admin_id'],

                'balance' => $order['channel_balance'],

                'status' => -1,


            ];

            $res = $this->dataAdd($insert);

        }

        return true;


    }

    /**
     * @Desc: 渠道商收益
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/27 19:45
     */
    public static function getChannelCommList($where, $dis, $limit = 10)
    {
        $data = self::alias('a')
            ->where($where)
            ->where(function ($query) use ($dis) {
                $query->whereOr($dis);
            })
            ->leftJoin('massage_service_order_list b', 'a.order_id=b.id')
            ->leftJoin('massage_service_user_list c', 'b.user_id=c.id')
            ->leftJoin('massage_channel_list d', 'b.channel_id=d.id and b.channel_staff_id =0')
            ->leftJoin('massage_channel_staff_list e', 'b.channel_staff_id=e.id')
            ->field('a.id,c.nickName,b.pay_price,a.create_time,a.cash,ifnull(e.name,d.user_name) as name,a.status')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();
        if ($data['data']) {
            foreach ($data['data'] as &$item) {

                $item['create_time'] = handleTime($item['create_time']);

                $item['name'] = empty($item['name']) ? '' : $item['name'];
            }
        }
        return $data;
    }

    /**
     * @Desc: 渠道商佣金统计
     * @param $where
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/4/28 14:50
     */
    public static function getChannelStat($where, $dis)
    {
        $data = self::alias('a')
            ->where($where)
            ->where(function ($query) use ($dis) {
                $query->whereOr($dis);
            })
            ->field('ifnull(sum(b.true_service_price),0) as pay_price,ifnull(sum(a.cash),0) as cash')
            ->leftJoin('massage_service_order_list b', 'a.order_id=b.id')
            ->leftJoin('massage_service_user_list c', 'b.user_id=c.id')
            ->leftJoin('massage_channel_list d', 'b.channel_id=d.id and b.channel_staff_id =0')
            ->leftJoin('massage_channel_staff_list e', 'b.channel_staff_id=e.id')
            ->find()
            ->toArray();

        $data['pay_price'] = round($data['pay_price'], 2);

        $data['pay_price'] = $data['pay_price'] < 0 ? 0 : $data['pay_price'];

        $data['cash'] = round($data['cash'], 2);

        return $data;
    }

    /**
     * @Desc: 套餐订单分销
     * @param $order
     * @return array[]
     * @Auther: shurong
     * @Time: 2023/11/24 15:24
     */
    public function packageCashData($order, $type = 1)
    {
        $list = $this->packageCommissionData();

        if ($type != 1) {

            $order['true_package_price'] -= $order['refund_price'];
        }

        foreach ($list as $key => $value) {

            $balance = isset($order[$value['parameter']]) ? $order[$value['parameter']] : 0;

            $order['surplus_cash'] = $key == 0 ? $order['true_package_price'] : $order['surplus_cash'];

            $action_name = $value['action_name'];

            $order = $this->$action_name($balance, $order, $order['surplus_cash']);

        }

        //平台
        $order['company_cash'] = $order['surplus_cash'];

        return $order;
    }

    /**
     * @Desc: 门店订单分销方法
     * @return array[]
     * @Auther: shurong
     * @Time: 2023/11/24 15:33
     */
    public function packageCommissionData()
    {
        $arr = [
            //门店佣金
            [

                'action_name' => "getStoreCash",

                'parameter' => 'store_balance',

            ],
            //推广人佣金
            [

                'action_name' => 'getShareCash',

                'parameter' => 'share_balance',
            ],
        ];

        return $arr;
    }

    /**
     * @Desc: 门店佣金
     * @param $balance
     * @param $order
     * @param $cash
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/24 15:43
     */
    public function getStoreCash($balance, $order, $cash)
    {
        //向导佣金
        $order['store_cash'] = round($balance * $order['true_package_price'] / 100, 2);

        $order['store_cash'] = $order['store_cash'] > $cash ? $cash : $order['store_cash'];

        $order['surplus_cash'] = $cash - $order['store_cash'];

        return $order;
    }

    /**
     * @Desc: 推广人id
     * @param $balance
     * @param $order
     * @param $cash
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/24 15:43
     */
    public function getShareCash($balance, $order, $cash)
    {
        if (!empty($order['share_user_id']) && !empty($balance)) {
            //向导佣金
            $order['share_cash'] = round($balance * $order['true_package_price'] / 100, 2);

            $order['share_cash'] = $order['share_cash'] > $cash ? $cash : $order['share_cash'];

            $order['surplus_cash'] = $cash - $order['share_cash'];
        }

        return $order;
    }

    /**
     * @Desc: 插入门店套餐门店分销记录
     * @param $order
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/11/24 16:03
     */
    public function storeCommission($order, $type)
    {
        if (isset($order['store_cash']) && $order['store_cash'] > 0) {

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['store_id'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 14,

                'cash' => $order['store_cash'],

                'balance' => $order['store_balance'],

                'status' => $type == 1 ? -1 : 1,

                'order_type' => 3
            ];

            $res = $this->dataAdd($insert);

            return $res;
        }
        return 0;
    }


    /**
     * @Desc: 插入门店套餐推广人分销记录
     * @param $order
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/11/24 16:03
     */
    public function shareCommission($order, $type)
    {
        if (isset($order['share_cash']) && $order['share_cash'] > 0 && !empty($order['share_user_id'])) {

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['share_user_id'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 15,

                'cash' => $order['share_cash'],

                'balance' => $order['share_balance'],

                'status' => $type == 1 ? -1 : 1,

                'order_type' => 3
            ];

            $res = $this->dataAdd($insert);

            return $res;
        }
        return 0;
    }

    /**
     * @Desc: 推广人分销列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/4 16:04
     */
    public static function shareCashList($where, $limit)
    {
        return self::alias('a')
            ->field('a.id,a.status,d.nickName,b.name,b.cover,b.price,b.num,c.name as store_name,b.pay_price,a.cash')
            ->where($where)
            ->leftJoin('massage_store_package_order_list b', 'a.order_id=b.id')
            ->leftJoin('massage_store_apply c', 'b.store_id=c.id')
            ->leftJoin('massage_service_user_list d', 'b.user_id=d.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: 经纪人佣金记录
     * @param $order
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/12/7 15:17
     */
    public function brokerCommission($order)
    {
        if (!empty($order['broker_id']) && $order['broker_cash'] > 0) {

            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id' => $order['broker_id'],

                'order_id' => $order['id'],

                'order_code' => $order['order_code'],

                'type' => 16,

                'cash' => $order['broker_cash'],

                'admin_id' => $order['admin_id'],

                'balance' => $order['broker_balance'],

                'status' => -1,

                'order_type' => 1
            ];

            $res = $this->dataAdd($insert);

            $id = $this->getLastInsID();

            $this->commissionShare($order, $id);

            return $res;
        }
        return 0;
    }

    /**
     * @Desc: 经纪人佣金明细
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 17:32
     */
    public static function getBrokerCashList($where, $limit)
    {
        return self::alias('a')
            ->field('a.id,a.status,a.cash,b.create_time,b.true_service_price,c.coach_name,d.user_name as nickName')
            ->where($where)
            ->leftJoin('massage_service_order_list b', 'a.order_id=b.id')
            ->leftJoin('massage_service_coach_list c', 'b.coach_id=c.id')
            ->leftJoin('massage_service_order_address d', 'a.order_id=d.order_id')
            ->order('b.create_time desc')
            ->paginate($limit)
            ->toArray();
    }
}