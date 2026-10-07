<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/6
 * Time: 19:22
 * docs:
 */

namespace app\broker\controller;

use app\ApiRest;
use app\broker\model\Broker;
use app\massage\model\Coach;
use app\massage\model\CoachLevel;
use app\massage\model\CoachTimeList;
use app\massage\model\Commission;
use app\massage\model\Config;
use app\massage\model\StoreApply;
use app\massage\model\User;
use app\massage\model\Wallet;
use think\App;
use think\facade\Db;

class IndexBroker extends ApiRest
{
    public $broker_info;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $dis = [
            ['uniacid', '=', $this->_uniacid],
            ['user_id', '=', $this->getUserId()],
            ['status', '>', -1]
        ];
        $this->broker_info = Broker::getFirst($dis);

        if (empty($this->broker_info) || !in_array($this->broker_info['status'], [2, 3])) {

            $this->errorMsg('你还不是经纪人');
        }

    }

    /**
     * @Desc: 经纪人分享二维码
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/6 19:27
     */
    public function brokerQr()
    {
        $admin_id = request()->param('admin_id', 0);

        $key = 'broker_qr' . $this->broker_info['id'] . '-' . $this->is_app . '-' . $admin_id;

        $qr = getCache($key, $this->_uniacid);

        if (empty($qr)) {
            //小程序
            if ($this->is_app == 0) {

                $input['page'] = 'technician/pages/apply';

                $input['broker_id'] = $this->broker_info['id'];

                $input['admin_id'] = $admin_id;

                $user_model = new User();

                //获取二维码
                $qr = $user_model->orderQr($input, $this->_uniacid);

            } else {

                $page = 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/technician/pages/apply?broker_id=' . $this->broker_info['id'] . '&admin_id=' . $admin_id;

                $qr = base64ToPng(getCode($this->_uniacid, $page));

            }

            setCache($key, $qr, 86400, $this->_uniacid);
        }

        $qr = !empty($qr) ? $qr : 'https://' . $_SERVER['HTTP_HOST'] . '/favicon.ico';

        return $this->success($qr);

    }

    /**
     * @Desc: 首页数据
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/12/8 16:03
     */
    public function index()
    {
        $arr = Broker::getBrokerData($this->broker_info['id']);

        $data = [
            'id' => $this->broker_info['id'],
            'name' => $this->broker_info['name'],
            'cash' => (float)$this->broker_info['cash'],
//            'total_cash' => $this->broker_info['total_cash'],
        ];

        $data = array_merge($data, $arr);

        return $this->success($data);
    }

    /**
     * @Desc: 申请提现
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 15:57
     */
    public function applyWallet()
    {

        $input = $this->_input;

        $cash_mini = Config::where(['uniacid' => $this->_uniacid])->value('cash_mini');

        if (empty($input['apply_price']) || $input['apply_price'] < $cash_mini || $input['apply_price'] < 0.01) {

            $this->errorMsg('提现金额最低' . $cash_mini > 0 ? $cash_mini : 0.01 . '元');
        }

        if ($input['apply_price'] > $this->broker_info['cash']) {

            $this->errorMsg('余额不足');
        }

        //获取税点
        $tax_point = getConfigSetting($this->_uniacid, 'tax_point');

        $balance = 100 - $tax_point;

        $key = 'broker_wallet' . $this->getUserId();
        //加一个锁防止重复提交
        incCache($key, 1, $this->_uniacid);

        $value = getCache($key, $this->_uniacid);

        if ($value != 1) {

            delCache($key, $this->_uniacid);

            $this->errorMsg('网络错误，请刷新重试');

        }

        Db::startTrans();
        //减佣金
        $res = Broker::update(['cash' => $this->broker_info['cash'] - $input['apply_price']], ['id' => $this->broker_info['id']]);

        if ($res === false) {

            Db::rollback();
            //减掉
            delCache($key, $this->_uniacid);

            $this->errorMsg('申请失败');
        }

        $insert = [

            'uniacid' => $this->_uniacid,

            'user_id' => $this->getUserId(),

            'coach_id' => $this->broker_info['id'],

            'admin_id' => 0,

            'total_price' => $input['apply_price'],

            'balance' => $balance,

            'apply_price' => round($input['apply_price'] * $balance / 100, 2),

            'service_price' => round($input['apply_price'] * $tax_point / 100, 2),

            'code' => orderCode(),

            'tax_point' => $tax_point,

            'text' => $input['text'],

            'type' => 7,

            'apply_transfer' => !empty($input['apply_transfer']) ? $input['apply_transfer'] : 0

        ];

        $wallet_model = new Wallet();
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
        decCache($key, 1, $this->_uniacid);

        return $this->success($res);

    }


    /**
     * @Desc: 提现流水
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 15:57
     */
    public function walletList()
    {

        $wallet_model = new Wallet();

        $input = $this->_param;

        $dis = [

            'coach_id' => $this->broker_info['id']
        ];

        if (!empty($input['status'])) {

            $dis['status'] = $input['status'];
        }

        $dis['type'] = 7;
        //提现记录
        $data = $wallet_model->dataList($dis, 10);

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$v) {

                $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            }
        }
        //累计提现
        $data['extract_total_price'] = $wallet_model->capCash($this->broker_info['id'], 2, 7);

        return $this->success($data);

    }

    /**
     * @Desc: 技师列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 16:53
     */
    public function getCoach()
    {
        $limit = request()->param('limit', 10);

        $where = ['a.broker_id' => $this->broker_info['id'], 'a.status' => 2];

        $data = Coach::getBrokerCoach($where, $limit);

        $coach_model = new Coach();

        $config_model = new Config();

        $level_model = new CoachLevel();

        $config = $config_model->dataInfo(['uniacid' => $this->_uniacid]);

        $level_cycle = $config['level_cycle'];
        //服务中
        $working_coach = $coach_model->getWorkingCoach($this->_uniacid);
        //当前时间不可预约
        $cannot = CoachTimeList::getCannotCoach($this->_uniacid);

        if ($data['data']) {

            foreach ($data['data'] as &$item) {

                $item['agent_name'] = empty($item['agent_name']) ? '平台' : $item['agent_name'];

                $item['near_time'] = $coach_model->getCoachEarliestTime($item['id'], $config);

                if (in_array($item['id'], $working_coach)) {

                    $text_type = 2;

                } elseif (empty($item['near_time'])) {

                    $text_type = 4;

                } elseif (!in_array($item['id'], $cannot)) {

                    $text_type = 1;

                } else {

                    $text_type = 3;
                }

                $item['text_type'] = $text_type;

                $item['price'] = $level_model->getMinPrice($item['id'], $level_cycle, 0, 1);
            }

        }

        return $this->success($data);
    }

    /**
     * @Desc: 收益
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 17:32
     */
    public function cashList()
    {
        $limit = request()->param('limit', 10);
        $name = request()->param('name', '');
        $status = request()->param('status', '0');
        $start_time = request()->param('start_time', '');
        $end_time = request()->param('end_time', '');

        $where = [
            ['a.type', '=', 16],
            ['a.status', '>', -1],
            ['a.top_id', '=', $this->broker_info['id']]
        ];

        if (!empty($name)) {

            $where[] = ['c.coach_name', 'like', '%' . $name . '%'];
        }

        if (!empty($start_time)) {

            $where[] = ['b.create_time', '>=', $start_time];
        }

        if (!empty($end_time)) {

            $where[] = ['b.create_time', '<=', $end_time];
        }

        if (!empty($status)) {

            $where[] = ['a.status', '=', $status];
        }

        $data = Commission::getBrokerCashList($where, $limit);

        if ($data['data']) {
            foreach ($data['data'] as &$item) {

                $item['cash'] = round($item['cash'], 2);
            }
        }

        return $this->success($data);
    }

    /**
     * @Desc: 代理商
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 17:46
     */
    public function adminList()
    {
        $limit = request()->param('limit');
        $dis[] = ['a.status', '>', -1];

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.is_admin', '=', 0];

        $data = \app\massage\model\Admin::brokerGetList($dis, $limit);

        return $this->success($data);
    }
}