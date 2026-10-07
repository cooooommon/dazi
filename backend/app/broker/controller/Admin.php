<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/6
 * Time: 16:08
 * docs:
 */

namespace app\broker\controller;

use app\AdminRest;
use app\broker\model\Broker;
use app\broker\model\User;
use app\massage\model\Coach;
use app\massage\model\Order;
use app\massage\model\Wallet;
use think\App;

class Admin extends AdminRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 用户列表
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/12/6 18:21
     */
    public function userList()
    {
        $limit = request()->param('limit', 10);

        $name = request()->param('name', '');

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', 'in', [1, 2, 3]],
        ];

        $user_id = Broker::getColumn($where, 'user_id');

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['id', 'not in', $user_id],
        ];

        $where1 = [];

        if (!empty($name)) {

            $where1[] = ['nickName', 'like', '%' . $name . '%'];

            $where1[] = ['phone', 'like', '%' . $name . '%'];
        }

        $data = User::getList($where, $limit, $where1);

        return $this->success($data);
    }


    /**
     * @Desc: 增加经纪人
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/6 16:54
     */
    public function add()
    {
        $data = request()->only(['user_id', 'name', 'mobile', 'text']);

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '>', -1],
            ['user_id', '=', $data['user_id']]
        ];

        $info = Broker::getFirst($where);

        if (!empty($info) && in_array($info['status'], [1, 2, 3])) {

            $this->errorMsg('此用户已申请，不可重复申请');
        }

        $insert = [
            'uniacid' => $this->_uniacid,
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'text' => $data['text'],
            'status' => 2
        ];

        if (!empty($info) && $info['status'] == 4) {

            $res = Broker::update($insert, ['id' => $info['id']]);
        } else {

            $res = Broker::add($insert);
        }

        return $this->success($res);
    }

    /**
     * @Desc: 列表
     * @Auther: shurong
     * @Time: 2023/12/6 16:55
     */
    public function getList()
    {
        $data = request()->param();

        $dis = [];

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.status', '>', -1]
        ];

        if (!empty($data['status'])) {

            $where[] = ['a.status', '=', $data['status']];
        }

        if (!empty($data['name'])) {

            $dis[] = ['a.name', 'like', '%' . $data['name'] . '%'];
            $dis[] = ['a.mobile', 'like', '%' . $data['name'] . '%'];
        }

        if (!empty($data['start_time'])) {

            $where[] = ['a.create_time', '>=', $data['start_time']];
        }

        if (!empty($data['end_time'])) {

            $where[] = ['a.create_time', '<=', $data['end_time']];
        }

        $data = Broker::getList($where, $dis, $data['limit'] ?? 10);

        $balance = getConfigSetting($this->_uniacid, 'broker_balance');

        if ($data['data']) {
            foreach ($data['data'] as &$datum) {

                $datum['balance'] = floatval($datum['balance']);

                if (!$datum['balance'] > 0) {

                    $datum['balance'] = $balance;
                }
            }
        }

        $data['all_count'] = Broker::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['status', '>', -1],
        ]);
        $data['apply_count'] = Broker::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1],
        ]);
        $data['pass_count'] = Broker::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 2],
        ]);
        $data['refuse_count'] = Broker::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 4],
        ]);

        return $this->success($data);
    }

    /**
     * @Desc: 编辑
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/6 17:38
     */
    public function update()
    {
        $data = request()->only(['id', 'user_id', 'name', 'mobile', 'text', 'status', 'balance', 'sh_text']);

        if (request()->isPost()) {
            if (isset($data['status']) && in_array($data['status'], [2, 4])) {

                $data['sh_time'] = time();
            }

            if (isset($data['status']) && $data['status'] == -1) {

                $broker = Broker::find($data['id']);

                if ($broker['cash'] > 0) {

                    $this->errorMsg('此经纪人还有佣金未提现，不可删除');
                }

                $wallet_dis = [

                    ['coach_id', '=', $data['id']],

                    ['status', '=', 1],

                    ['type', '=', 7]
                ];

                $wallet = (new Wallet())->dataInfo($wallet_dis);

                if (!empty($wallet)) {

                    $this->errorMsg('该经纪人还有提现申请中，无法删除。提现id：' . $wallet['id']);

                }

                $order_model = new Order();

                $where[] = ['uniacid', '=', $this->_uniacid];

                $where[] = ['broker_id', '=', $data['id']];

                $where[] = ['pay_type', 'in', [2, 3, 4, 5, 6, 8]];

                $order = $order_model->dataInfo($where);

                if (!empty($order)) {

                    $this->errorMsg('该经纪人还有未完成的订单，无法删除。订单id：' . $order['id']);
                }

                $arr_dis = [

                    'pay_type' => 7,

                    'have_tx' => 0,

                    'broker_id' => $data['id']
                ];

                $no_arr_order = $order_model->dataInfo($arr_dis);

                if (!empty($no_arr_order)) {

                    $this->errorMsg('该经纪人还有冻结订单，无法删除');
                }
            }

            $res = Broker::update($data, ['id' => $data['id']]);

            return $this->success($res);
        }
        $data = Broker::getFirst(['id' => $data['id']]);

        return $this->success($data);
    }

    /**
     * @Desc: 经纪人数据
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 11:05
     */
    public function getData()
    {

        $data = request()->param();

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.status', 'in', [2, 3]]
        ];

        $dis = [];

        if (!empty($data['name'])) {

            $dis[] = ['a.name', 'like', '%' . $data['name'] . '%'];
            $dis[] = ['a.mobile', 'like', '%' . $data['name'] . '%'];
            $dis[] = ['b.nickName', 'like', '%' . $data['name'] . '%'];
        }

        if (!empty($data['start_time'])) {

            $where[] = ['a.create_time', '>=', $data['start_time']];
        }

        if (!empty($data['end_time'])) {

            $where[] = ['a.create_time', '<=', $data['end_time']];
        }

        $data = Broker::getShareData($where, $dis, $data['limit'] ?? 10);

        return $this->success($data);
    }

    /**
     * @Desc: 向导列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 14:46
     */
    public function coachList()
    {
        $broker_id = request()->param('broker_id', '');

        $limit = request()->param('limit', 10);

        $where = ['a.broker_id' => $broker_id, 'a.status' => 2];

        $data = Coach::getBrokerCoach($where, $limit);

        if ($data['data']) {

            foreach ($data['data'] as &$item) {

                $item['agent_name'] = empty($item['agent_name']) ? '平台' : $item['agent_name'];
            }
        }

        return $this->success($data);
    }
}