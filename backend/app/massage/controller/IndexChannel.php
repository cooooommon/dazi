<?php

namespace app\massage\controller;

use app\ApiRest;

use app\massage\model\BalanceWater;
use app\massage\model\ChannelCate;
use app\massage\model\ChannelList;
use app\massage\model\ChannelStaff;
use app\massage\model\Coach;

use app\massage\model\Commission;
use app\massage\model\Config;
use app\massage\model\Goods;

use app\massage\model\Order;
use app\massage\model\OrderGoods;
use app\massage\model\Police;
use app\massage\model\RefundOrder;
use app\massage\model\RefundOrderGoods;
use app\massage\model\User;
use app\massage\model\Wallet;
use longbingcore\wxcore\WxSetting;
use think\App;
use think\facade\Db;
use think\Request;


class IndexChannel extends ApiRest
{

    protected $model;

    protected $channel_info;

    protected $order_model;

    protected $user_model;

    public function __construct(App $app)
    {

        parent::__construct($app);

        $this->model = new ChannelList();

        $this->order_model = new Order();

        $this->user_model = new User();

        $cap_dis[] = ['user_id', '=', $this->getUserId()];

        $cap_dis[] = ['status', 'in', [2, 3]];

        $this->channel_info = $this->model->dataInfo($cap_dis);

        if (empty($this->channel_info)) {

            $this->errorMsg('你还不是渠道商');
        }

    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-08 11:39
     * @功能说明:向导首页
     */
    public function index()
    {

        $this->order_model->coachBalanceArr($this->_uniacid);

        $input = $this->_param;

        $data = $this->channel_info;

        $order_data = $this->order_model->channelData($this->channel_info['id'], $input);

        $data = array_merge($data, $order_data);
        //税点
        $data['tax_point'] = getConfigSetting($this->_uniacid, 'tax_point');

        $data['cate_name'] = ChannelCate::where('id', $data['cate_id'])->value('title');

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-30 14:18
     * @功能说明:渠道码
     */
    public function channelQr()
    {

        $input = $this->_param;

        $key = 'channel_qr' . $this->channel_info['id'] . '-' . $this->is_app;

        $qr = getCache($key, $this->_uniacid);

        if (empty($qr)) {
            //小程序
            if ($this->is_app == 0) {

                $input['page'] = 'pages/service';

                $input['channel_id'] = $this->channel_info['id'];
                //获取二维码
                $qr = $this->user_model->orderQr($input, $this->_uniacid);

            } else {

                $page = 'https://' . $_SERVER['HTTP_HOST'] . '/h5/#/pages/service?channel_id=' . $this->channel_info['id'];

                $qr = base64ToPng(getCode($this->_uniacid, $page));

            }

            setCache($key, $qr, 86400, $this->_uniacid);
        }

        $qr = !empty($qr) ? $qr : 'https://' . $_SERVER['HTTP_HOST'] . '/favicon.ico';

        return $this->success($qr);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-30 14:34
     * @功能说明:订单列表
     */
    public function orderList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.pay_type', '>', 1];

        $dis[] = ['a.channel_id', '=', $this->channel_info['id']];

        if (!empty($input['start_time']) && !empty($input['end_time'])) {

            $dis[] = ['a.create_time', 'between', "{$input['start_time']},{$input['end_time']}"];

        }

        $where = [];

        if (!empty($input['name'])) {

            $where[] = ['b.goods_name', 'like', '%' . $input['name'] . '%'];

            $where[] = ['a.order_code', 'like', '%' . $input['name'] . '%'];

        }

        $data = $this->order_model->indexDataList($dis, $where);

        if (!empty($data['data'])) {

            $refund_model = new RefundOrder();

            $com_model = new Commission();

            foreach ($data['data'] as &$v) {

                $v['refund_price'] = $refund_model->where(['order_id' => $v['id'], 'status' => 2])->sum('refund_price');
                //渠道商佣金
                $v['channel_cash'] = $com_model->where(['order_id' => $v['id'], 'type' => 10])->where('status', '>', -1)->value('cash');

            }
        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-24 13:33
     * @功能说明:渠道商申请提现
     */


    public function applyWallet()
    {

        $input = $this->_input;

        if (empty($input['apply_price']) || $input['apply_price'] < 0.01) {

            $this->errorMsg('提现费最低一分');
        }

        if ($input['apply_price'] > $this->channel_info['cash']) {

            $this->errorMsg('余额不足');
        }

        //获取税点
        $tax_point = getConfigSetting($this->_uniacid, 'tax_point');

        $balance = 100 - $tax_point;

        $key = 'channel_wallet' . $this->getUserId();
        //加一个锁防止重复提交
        incCache($key, 1, $this->_uniacid);

        $value = getCache($key, $this->_uniacid);

        if ($value != 1) {

            delCache($key, $this->_uniacid);

            $this->errorMsg('网络错误，请刷新重试');

        }

        Db::startTrans();
        //减佣金
        $res = $this->model->dataUpdate(['id' => $this->channel_info['id']], ['cash' => $this->channel_info['cash'] - $input['apply_price']]);

        if ($res != 1) {

            Db::rollback();
            //减掉
            delCache($key, $this->_uniacid);

            $this->errorMsg('申请失败');
        }

        $insert = [

            'uniacid' => $this->_uniacid,

            'user_id' => $this->getUserId(),

            'coach_id' => $this->channel_info['id'],

            'admin_id' => 0,

            'total_price' => $input['apply_price'],

            'balance' => $balance,

            'apply_price' => round($input['apply_price'] * $balance / 100, 2),

            'service_price' => round($input['apply_price'] * $tax_point / 100, 2),

            'code' => orderCode(),

            'tax_point' => $tax_point,

            'text' => $input['text'],

            'type' => 5,

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
     * @author chenniang
     * @DataTime: 2021-03-30 14:39
     * @功能说明:渠道商提现记录
     */
    public function walletList()
    {

        $wallet_model = new Wallet();

        $input = $this->_param;

        $dis = [

            'coach_id' => $this->channel_info['id']
        ];

        if (!empty($input['status'])) {

            $dis['status'] = $input['status'];
        }

        $dis['type'] = 5;
        //提现记录
        $data = $wallet_model->dataList($dis, 10);

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$v) {

                $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            }
        }
        //累计提现
        $data['extract_total_price'] = $wallet_model->capCash($this->channel_info['id'], 2, 5);

        return $this->success($data);


    }

    /**
     * @Desc: 渠道商生成邀请员工绑定二维码
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/27 14:02
     */
    public function inviteStaffQr()
    {
        $data = \request()->only(['name', 'balance']);

        if ($data['balance'] > $this->channel_info['balance']) {

            return $this->error('员工佣金比例不可超过自己的提成比例。');

        }

        $data['channel_user_id'] = $this->getUserId();

        $data['channel_id'] = $this->channel_info['id'];

        $data['uniacid'] = $this->_uniacid;

        $qr_id = ChannelStaff::add($data);
        //小程序
        if ($this->is_app == 0) {

            $input['page'] = 'user/pages/channel/bind-staff';

            $input['channel_invite_id'] = $qr_id;

            //获取二维码
            $qr = $this->user_model->orderQr($input, $this->_uniacid);

        } else {
            $page = 'https://' . $_SERVER['HTTP_HOST'] . '/h5/#/user/pages/channel/bind-staff?channel_invite_id=' . $qr_id;

            $qr = base64ToPng(getCode($this->_uniacid, $page));

        }

        return $this->success(['path' => $qr]);
    }

    /**
     * @Desc: 渠道商佣金收益
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/27 19:45
     */
    public function commList()
    {
        $input = $this->_param;
        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.top_id', '=', $this->channel_info['id']],
            ['a.status', '>', -1],
            ['a.type', '=', 10],
            ['a.order_type', '=', 1]
        ];

        $dis = [];

        if (!empty($input['name'])) {
            $dis[] = ['e.name', 'like', '%' . $input['name'] . '%'];
            $dis[] = ['d.user_name', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['status'])) {
            $where[] = ['a.status', '=', $input['status']];
        }
        if (!empty($input['start_time'])) {
            $where[] = ['a.create_time', '>', $input['start_time']];
        }
        if (!empty($input['end_time'])) {
            $where[] = ['a.create_time', '<=', strtotime(handleTime($input['end_time'], 'Y-m-d') . ' 23:59:59')];
        }

        $data = Commission::getChannelCommList($where, $dis, $input['limit'] ?? 10);

        $arr = Commission::getChannelStat($where, $dis);

        $data = array_merge($data, $arr);

        return $this->success($data);
    }

    /**
     * @Desc: 我的员工
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/30 10:51
     */
    public function staffList()
    {
        $input = $this->_param;
        $where[] = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.user_id', '>', 0],
            ['a.status', '=', 1],
            ['a.channel_id', '=', $this->channel_info['id']]
        ];
        $dis = [];
        if (!empty($input['name'])) {

            $dis[] = ['a.name', 'like', '%' . $input['name'] . '%'];
            $dis[] = ['b.nickName', 'like', '%' . $input['name'] . '%'];
        }
        $where_ = $where;
        if (!empty($input['start_time'])) {

            $where_[] = ['c.create_time', '>', $input['start_time']];
        }

        if (!empty($input['end_time'])) {

            $where_[] = ['c.create_time', '<=', $input['end_time']];
        }

        $data = ChannelStaff::staffList($where, $dis, $where_, $input['limit'] ?? 10);
        return $this->success($data);

    }

    /**
     * @Desc: 修改员工信息
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/30 17:54
     */
    public function updateStaff()
    {
        $data = \request()->only(['id', 'name', 'balance', 'status']);
        $res = ChannelStaff::update($data, ['id' => $data['id']]);
        if ($res) {
            return $this->success('编辑成功');
        }
        return $this->error('编辑失败');
    }
}
