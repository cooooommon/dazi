<?php

namespace app\massage\controller;

use app\AdminRest;
use app\broker\model\CommissionShare;
use app\integral\model\UserIntegral;
use app\massage\model\Coach;
use app\massage\model\Commission;
use app\massage\model\Order;
use app\massage\model\User;
use app\massage\model\UserFrom;
use app\massage\model\UserLabelData;
use app\shop\model\Article;
use app\shop\model\Banner;
use app\shop\model\Date;
use app\massage\model\OrderGoods;
use app\massage\model\RefundOrder;
use app\shop\model\Wallet;
use think\App;
use app\massage\model\User as Model;
use think\facade\Db;


class AdminUser extends AdminRest
{


    protected $model;

    protected $order_goods_model;

    protected $refund_order_model;

    public function __construct(App $app)
    {

        parent::__construct($app);

        $this->model = new Model();

        $this->order_goods_model = new OrderGoods();

        $this->refund_order_model = new RefundOrder();

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-24 10:24
     * @功能说明:用户列表
     */
    public function userList()
    {

        $input = $this->_param;

        $dis[] = ['uniacid', '=', $this->_uniacid];
        //是否授权
        if (!empty($input['type'])) {

            if ($input['type'] == 1) {

                $dis[] = ['nickName', '=', ''];

            } else {

                $dis[] = ['nickName', '<>', ''];

            }

        }

        if (!empty($input['form_type'])) {

            $user_id = UserFrom::where('from_type', $input['form_type'])->column('user_id');

            $dis[] = ['id', 'in', $user_id];
        }

        $where = [];

        if (!empty($input['nickName'])) {

            $where[] = ['nickName', 'like', '%' . $input['nickName'] . '%'];

            $where[] = ['phone', 'like', '%' . $input['nickName'] . '%'];
        }

        if (!empty($input['start_time']) && !empty($input['end_time'])) {

            $start_time = $input['start_time'];

            $end_time = $input['end_time'];

            $dis[] = ['create_time', 'between', "$start_time,$end_time"];
        }

        if (!empty($input['id'])) {

            $dis[] = ['id', '=', $input['id']];
        }

        if (!empty($input['phone'])) {

            $dis[] = ['phone', 'like', '%' . $input['phone'] . '%'];
        }

        $dis[] = ['is_blacklist', '=', 0];

        $data = $this->model->dataList($dis, $input['limit'], $where);

        if (!empty($data['data'])) {

            $label_model = new UserLabelData();

            $user_id = array_column($data['data'], 'id');

            $from = UserFrom::getListByUserIds($user_id, $this->_uniacid);

            foreach ($data['data'] as &$v) {

                $v['user_label'] = $label_model->getUserLabel($v['id']);

                foreach ($from as $item) {

                    if ($v['id'] == $item['user_id']) {

                        $v['from_name'] = $item['from_name'];
                    }
                }

                $v['from_name'] = empty($v['from_name']) ? '' : $v['from_name'];
            }

        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-28 23:03
     * @功能说明:佣金记录
     */
    public function commList()
    {

        $input = $this->_param;

        $order_model = new Order();

        $order_model->coachBalanceArr($this->_uniacid);

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.cash', '>', 0];

        $dis[] = ['type', 'not in', [7]];

        if (!empty($input['status'])) {

            $dis[] = ['a.status', '=', $input['status']];
        } else {

            $dis[] = ['a.status', '>', -1];

        }

        $where = [];

        if (!empty($input['top_name'])) {

            $where[] = ['c.nickName', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['e.agent_name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['f.coach_name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['g.now_coach_name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['h.user_name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['j.name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['k.name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['m.nickName', 'like', '%' . $input['top_name'] . '%'];
        }

        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['a.admin_id', 'in', $this->admin_arr];

        }

        if (!empty($input['type'])) {

            if ($input['type'] == 2) {

                $dis[] = ['a.type', 'in', [2, 5, 6]];
            } elseif ($input['type'] == 3) {

                $dis[] = ['a.type', 'in', [3]];
            } else {
                $dis[] = ['a.type', '=', $input['type']];
            }

        }

        $code = [];
        if (!empty($input['order_code'])) {

            $code[] = ['d.order_code', 'like', '%' . $input['order_code'] . '%'];
            $code[] = ['i.order_code', 'like', '%' . $input['order_code'] . '%'];
            $code[] = ['l.order_code', 'like', '%' . $input['order_code'] . '%'];
        }

//        if (!empty($input['order_type'])) {
//            $dis[] = ['a.order_type', '=', $input['order_type']];
//        }

        $comm_model = new Commission();

        $data = $comm_model->recordListV2($dis, $where, $code, $input['limit']);

        $admin_model = new \app\massage\model\Admin();

        $commission_custom = getConfigSetting($this->_uniacid, 'commission_custom');

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$v) {

                $v['balance'] = (float)$v['balance'];

                if ($v['order_type'] == 2) {
                    $v['cash_type'] = $v['demand_cash_type'];
                }

                $v['coach_cash_control'] = $v['status'] == 2 && $v['admin_id'] == 0 && in_array($v['type'], [3, 8]) && $v['top_id'] == 0 ? 1 : 0;

                $v['cash'] = round($v['cash'], 2);

                if ($v['car_price'] > 0) {

                    $v['pay_price'] = $v['pay_price'] . '(含车费' . $v['car_price'] . ')';
                }

                if (in_array($v['type'], [2, 5, 6, 11])) {

                    $v['top_name'] = $v['admin_name'];

                } elseif (in_array($v['type'], [3, 8])) {

                    $v['top_name'] = $v['coach_name'];

                    if ($v['top_id'] == 0 && $v['car_cash'] > 0) {

                        $v['cash'] = $v['cash'] . '(含车费' . $v['car_cash'] . ')';

                    }

                    if (empty($v['top_id'])) {

                        $v['top_name'] = $v['now_coach_name'];
                    }

                } elseif ($v['type'] == 10) {

                    $v['top_name'] = $v['channel_name'];
                } elseif ($v['type'] == 16) {

                    $v['top_name'] = $v['broker_name'];
                } elseif ($v['type'] == 14) {

                    $v['top_name'] = $v['store_name'];
                } elseif ($v['type'] == 15) {

                    $v['top_name'] = $v['t_name'];
                }
                $share_cash = 0;
                if ($v['type'] == 2) {

                    $share_cash = CommissionShare::where(['type' => 2, 'order_id' => $v['order_id'], 'cash_type' => 1])->sum('share_cash');


                    if ($v['cash_type'] == 1) {

                        $v['balance'] = '平台抽成-' . $v['balance'];
                    }

                    $coach_cash = $v['coach_cash'] > 0 ? '包含' . $v['coach_cash'] . '线下向导服务费，' : '';

                    $car_cash = $v['car_cash'] > 0 ? '包含' . $v['car_cash'] . '线下向导车费' : '';

                    $v['cash'] = !empty($coach_cash) || !empty($car_cash) ? $v['cash'] . '(' . $coach_cash . $car_cash . ')' : $v['cash'];
                }

                if ($v['type'] == 3) {

                    $share_cash = CommissionShare::where(['type' => 1, 'order_id' => $v['order_id'], 'cash_type' => 1])->sum('share_cash');
                }

                if (!empty($share_cash)) {

                    $v['cash'] .= '  被分摊金额:' . $share_cash . '元';
                }

                if ($v['type'] == 10 && $v['channel_staff_balance'] > 0 && $v['channel_staff_id'] > 0) {

                    $v['cash'] = $v['cash'] . '  (包含渠道员工佣金￥' . (round($v['channel_cash'] * $v['channel_staff_balance'] / 100, 2)) . ')';
                }


                if ($v['order_type'] == 2) {

                    $v['order_code'] = $v['demand_order_code'];
                    $v['transaction_id'] = $v['demand_transaction_id'];
                    $v['pay_price'] = $v['demand_pay_price'];
                } else if ($v['order_type'] == 3) {

                    $v['order_code'] = $v['package_order_code'];
                    $v['transaction_id'] = $v['package_transaction_id'];
                    $v['pay_price'] = $v['package_pay_price'];
                }
                if ($v['order_type'] == 1) {

                    $v['refund_price'] = Db::name('massage_service_refund_order')->where(['order_id' => $v['order_id'], 'status' => 2])->value('refund_price') ?? 0;
                } else {

                    $v['refund_price'] = 0;
                }
            }

        }

        if ($this->_user['is_admin'] == 0) {
            //可提现记录
            $data['total_cash'] = $admin_model->where(['id' => $this->_user['id']])->sum('cash');

            $dis = [

                'admin_id' => $this->_user['id'],

                'status' => 1,

                'type' => 2
            ];
            //未入账金额
            $data['unrecorded_cash'] = $comm_model->where($dis)->sum('cash');

            $wallet_model = new \app\massage\model\Wallet();

            $dis = [

                'user_id' => $this->_user['id'],

                //  'status'  => 2,

                'type' => 3
            ];
            //加盟商提现
            $data['wallet_cash'] = $wallet_model->where($dis)->where('status', 'in', [1, 2])->sum('total_price');
        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-15 18:25
     * @功能说明:代理商修改线下向导佣金记录状态
     */
    public function adminUpdateCoachCommisson()
    {

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $comm_model = new Commission();

        $data = $comm_model->dataInfo($dis);

        if ($data['status'] != 2) {

            $this->errorMsg('佣金还未到账');
        }

        $res = $comm_model->dataUpdate($dis, ['cash_status' => 1]);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-28 23:03
     * @功能说明:佣金记录
     */
    public function cashList()
    {

        $input = $this->_param;

        $order_model = new Order();

        $order_model->coachBalanceArr($this->_uniacid);

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.cash', '>', 0];

        if (!empty($input['status'])) {

            $dis[] = ['a.status', '=', $input['status']];
        } else {

            $dis[] = ['a.status', '>', -1];

        }

        $where = [];

        if (!empty($input['top_name'])) {

            $where[] = ['c.nickName', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['e.agent_name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['f.coach_name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['g.now_coach_name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['h.user_name', 'like', '%' . $input['top_name'] . '%'];

            $where[] = ['j.name', 'like', '%' . $input['top_name'] . '%'];
        }

        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['a.admin_id', 'in', $this->admin_arr];

        }

        if (!empty($input['type'])) {

            if ($input['type'] == 2) {

                $dis[] = ['a.type', 'in', [2, 5, 6]];

            } elseif ($input['type'] == 3) {

                $dis[] = ['a.type', 'in', [3]];
            } else {
                $dis[] = ['a.type', '=', $input['type']];
            }

        }

        $code = [];
        if (!empty($input['order_code'])) {

            $code[] = ['d.order_code', 'like', '%' . $input['order_code'] . '%'];
            $code[] = ['i.order_code', 'like', '%' . $input['order_code'] . '%'];
        }

        $comm_model = new Commission();

        $data = $comm_model->recordListV2($dis, $where, $code, $input['limit']);

        $admin_model = new \app\massage\model\Admin();

        $commission_custom = getConfigSetting($this->_uniacid, 'commission_custom');

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$v) {

                $v['balance'] = (float)$v['balance'];

                if ($v['order_type'] == 2) {
                    $v['cash_type'] = $v['demand_cash_type'];
                }

                $v['cash'] = round($v['cash'], 2);

                if ($v['car_price'] > 0) {

                    $v['pay_price'] = $v['pay_price'] . '(含车费' . $v['car_price'] . ')';
                }

                if (in_array($v['type'], [2, 5, 6, 11])) {

                    $v['top_name'] = $v['admin_name'];

                } elseif (in_array($v['type'], [3, 8])) {

                    $v['top_name'] = $v['coach_name'];

                    if ($v['top_id'] == 0 && $v['car_cash'] > 0) {

                        $v['cash'] = $v['cash'] . '(含车费' . $v['car_cash'] . ')';

                    }

                    if (empty($v['top_id'])) {

                        $v['top_name'] = $v['now_coach_name'];
                    }

                } elseif ($v['type'] == 10) {

                    $v['top_name'] = $v['channel_name'];
                } elseif ($v['type'] == 16) {

                    $v['top_name'] = $v['broker_name'];
                }

                $share_cash = 0;
                if ($v['type'] == 2) {

                    $share_cash = CommissionShare::where(['type' => 2, 'order_id' => $v['order_id'], 'cash_type' => 1])->sum('share_cash');

                    if ($v['cash_type'] == 1) {

                        $v['balance'] = '平台抽成-' . $v['balance'];
                    }

                    $coach_cash = $v['coach_cash'] > 0 ? '包含' . $v['coach_cash'] . '线下向导服务费，' : '';

                    $car_cash = $v['car_cash'] > 0 ? '包含' . $v['car_cash'] . '线下向导车费' : '';

                    $v['cash'] = !empty($coach_cash) || !empty($car_cash) ? $v['cash'] . '(' . $coach_cash . $car_cash . ')' : $v['cash'];
                }

                if ($v['type'] == 3) {

                    $share_cash = CommissionShare::where(['type' => 1, 'order_id' => $v['order_id'], 'cash_type' => 1])->sum('share_cash');
                }

                if (!empty($share_cash)) {

                    $v['cash'] .= '  被分摊金额:' . $share_cash . '元';
                }

                if ($v['type'] == 10 && $v['channel_staff_balance'] > 0 && $v['channel_staff_id'] > 0) {

                    $v['cash'] = $v['cash'] . '  (包含渠道员工佣金￥' . (round($v['channel_cash'] * $v['channel_staff_balance'] / 100, 2)) . ')';
                }

                if ($v['order_type'] == 2) {
                    $v['order_code'] = $v['demand_order_code'];
                    $v['transaction_id'] = $v['demand_transaction_id'];
                    $v['pay_price'] = $v['demand_pay_price'];
                } else if ($v['order_type'] == 3) {

                    $v['order_code'] = $v['package_order_code'];
                    $v['transaction_id'] = $v['package_transaction_id'];
                    $v['pay_price'] = $v['package_pay_price'];
                }
                if ($v['order_type'] == 1) {

                    $v['refund_price'] = Db::name('massage_service_refund_order')->where(['order_id' => $v['order_id'], 'status' => 2])->value('refund_price') ?? 0;
                } else {

                    $v['refund_price'] = 0;
                }

                $v['coach_cash_control'] = $v['status'] == 2 && $this->_user['id'] == $v['admin_id'] && in_array($v['type'], [3, 8]) && $v['top_id'] == 0 ? 1 : 0;
            }

        }

        if ($this->_user['is_admin'] == 0) {
            //可提现记录
            $data['total_cash'] = $admin_model->where(['id' => $this->_user['id']])->sum('cash');

            $dis = [

                'admin_id' => $this->_user['id'],

                'status' => 1,
            ];
            //未入账金额
            $data['unrecorded_cash'] = $comm_model->where($dis)->where('type', 'in', [2, 5, 6])->sum('cash');

            $wallet_model = new \app\massage\model\Wallet();

            $dis = [

                'user_id' => $this->_user['id'],

                'type' => 3
            ];
            //加盟商提现
            $data['wallet_cash'] = $wallet_model->where($dis)->where('status', 'in', [1, 2])->sum('total_price');

            $data['total_cash'] = round($data['total_cash'], 2);

            $data['unrecorded_cash'] = round($data['unrecorded_cash'], 2);

            $data['wallet_cash'] = round($data['wallet_cash'], 2);
        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-24 13:33
     * @功能说明:团长审核提现
     */
    public function applyWallet()
    {

        $input = $this->_input;

        if (empty($input['apply_price']) || $input['apply_price'] < 0.01) {

            $this->errorMsg('提现费最低一分');
        }

        if ($this->_user['is_admin'] != 0) {

            $this->errorMsg('只有加盟商才能提现');

        }

        $admin_model = new \app\massage\model\Admin();

        $admin_user = $admin_model->dataInfo(['id' => $this->_user['id']]);
        //服务费
        if ($input['apply_price'] > $admin_user['cash']) {

            $this->errorMsg('余额不足');
        }
        //获取税点
        $tax_point = getConfigSetting($this->_uniacid, 'tax_point');

        $balance = 100 - $tax_point;

        $key = 'cap_wallets' . $this->_user['id'];

        $value = getCache($key, $this->_uniacid);

        if (!empty($value)) {

            $this->errorMsg('网络错误，请刷新重试');

        }

        Db::startTrans();
        //减佣金
        $res = $admin_model->dataUpdate(['id' => $this->_user['id'], 'lock' => $admin_user['lock']], ['cash' => $admin_user['cash'] - $input['apply_price'], 'lock' => $admin_user['lock'] + 1]);

        if ($res != 1) {

            Db::rollback();
            //减掉
            delCache($key, $this->_uniacid);

            $this->errorMsg('申请失败');
        }

        $insert = [

            'uniacid' => $this->_uniacid,

            'user_id' => $admin_user['id'],

            'admin_id' => $admin_user['id'],

            'coach_id' => 0,

            'total_price' => $input['apply_price'],

            'balance' => $balance,

            'apply_price' => round($input['apply_price'] * $balance / 100, 2),

            'service_price' => round($input['apply_price'] * $tax_point / 100, 2),

            'tax_point' => $tax_point,

            'code' => orderCode(),

            'text' => !empty($input['text']) ? $input['text'] : '',

            'type' => 3,

        ];

        $wallet_model = new \app\massage\model\Wallet();
        //提交审核
        $res = $wallet_model->dataAdd($insert);

        if ($res != 1) {

            Db::rollback();
            //减掉
            delCache($key, $this->_uniacid);

            $this->errorMsg('申请失败');
        }

        Db::commit();
        //减掉
        delCache($key, $this->_uniacid);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-10-24 16:44
     * @功能说明:删除用户标签
     */
    public function delUserLabel()
    {

        $input = $this->_input;

        $label_model = new UserLabelData();

        $res = $label_model->dataUpdate(['user_id' => $input['user_id'], 'label_id' => $input['label_id']], ['status' => -1]);

        return $this->success($res);

    }

    /**
     * @Desc: 设置黑名单
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/19 18:44
     */
    public function setBlacklist()
    {
        $id = request()->param('id', '');
        if (empty($id)) {
            $this->errorMsg('参数错误');
        }

        $is_blacklist = User::where('id', $id)->value('is_blacklist');
        $res = User::update(['is_blacklist' => $is_blacklist == 1 ? 0 : 1], ['id' => $id]);
        if ($res === false) {
            $this->errorMsg('设置失败');
        }

//        $key = 'longbing_user_autograph_' . $id;
//        $key = md5($key);
//        delCache($key, 999999999999);

        return $this->success('');
    }

    /***
     * @Desc: 黑名单列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/19 18:40
     */
    public function blacklist()
    {
        $input = $this->_param;

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $where = [];
        if (!empty($input['nickName'])) {

            $where[] = ['nickName', 'like', '%' . $input['nickName'] . '%'];

            $where[] = ['phone', 'like', '%' . $input['nickName'] . '%'];
        }

        if (!empty($input['id'])) {
            $dis[] = ['id', '=', $input['id']];
        }

        $dis[] = ['is_blacklist', '=', 1];

        $data = $this->model->dataList($dis, $input['limit'], $where);

        if (!empty($data['data'])) {

            $label_model = new UserLabelData();

            foreach ($data['data'] as &$v) {

                $v['user_label'] = $label_model->getUserLabel($v['id']);

            }

        }

        return $this->success($data);
    }

    /**
     * @Desc: 用户信息
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/15 15:56
     */
    public function getUserInfo()
    {
        $id = request()->param('id');

        $data = User::getInfo(['id' => $id]);

        return $this->success($data);
    }

    /**
     * @Desc: 积分明细
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/18 11:18
     */
    public function integralList()
    {
        $input = \request()->param();

        $data = UserIntegral::getUserListV2($input['user_id'], $input['limit'] ?? 10);

        return $this->success($data);
    }
}
