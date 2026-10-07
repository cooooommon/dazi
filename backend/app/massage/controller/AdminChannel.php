<?php

namespace app\massage\controller;

use app\AdminRest;
use app\massage\model\BalanceCard;
use app\massage\model\BalanceOrder;
use app\massage\model\ChannelCashWater;
use app\massage\model\ChannelCate;
use app\massage\model\ChannelList;
use app\massage\model\ChannelStaff;
use app\massage\model\Coupon;
use app\massage\model\Order;
use app\massage\model\User;
use app\shop\model\Article;
use app\shop\model\Banner;
use app\shop\model\Cap;
use app\shop\model\Date;
use app\shop\model\MsgConfig;
use app\shop\model\OrderAddress;
use app\shop\model\OrderGoods;
use app\shop\model\RefundOrder;
use app\shop\model\RefundOrderGoods;
use app\shop\model\Wallet;
use think\App;
use app\shop\model\Order as Model;
use think\facade\Db;


class AdminChannel extends AdminRest
{


    protected $model;

    protected $cate_model;

    public function __construct(App $app)
    {

        parent::__construct($app);

        $this->model = new ChannelList();

        $this->cate_model = new ChannelCate();


    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-04 19:09
     * @功能说明:类目列表
     */
    public function cateList()
    {

        $input = $this->_param;

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', '>', -1];

        if (!empty($input['title'])) {

            $dis[] = ['title', 'like', '%' . $input['title'] . '%'];

        }

        $data = $this->cate_model->dataList($dis, $input['limit']);

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-30 14:54
     * @功能说明:渠道商下拉
     */
    public function cateSelect()
    {

        $input = $this->_param;

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', '=', 1];

        $data = $this->cate_model->where($dis)->select()->toArray();

        return $this->success($data);

    }

    /**
     * @author chenniang
     * @DataTime: 2022-08-30 10:53
     * @功能说明:添加类目
     */
    public function cateAdd()
    {

        $input = $this->_input;

        $input['uniacid'] = $this->_uniacid;

        $res = $this->cate_model->dataAdd($input);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-30 10:53
     * @功能说明:添加类目
     */
    public function cateUpdate()
    {

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $input['uniacid'] = $this->_uniacid;

        $res = $this->cate_model->dataUpdate($dis, $input);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-30 10:56
     * @功能说明:分类详情
     */
    public function cateInfo()
    {

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];

        $res = $this->cate_model->dataInfo($dis);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-30 14:54
     * @功能说明:渠道商下拉
     */
    public function channelSelect()
    {

        $input = $this->_param;

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', 'in', [2, 3]];

        $data = $this->model->where($dis)->field('id,user_name')->select()->toArray();

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-30 11:30
     * @功能说明:渠道商列表
     */
    public function channelList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        if (!empty($input['status'])) {

            $dis[] = ['a.status', '=', $input['status']];

        } else {

            $dis[] = ['a.status', '>', -1];

        }

        if (!empty($input['start_time']) && !empty($input['end_time'])) {

            $start_time = $input['start_time'];

            $end_time = $input['end_time'];

            $dis[] = ['a.create_time', 'between', "$start_time,$end_time"];

        }

        $where = [];

        if (!empty($input['name'])) {

            $where[] = ['a.user_name', 'like', '%' . $input['name'] . '%'];

            $where[] = ['a.mobile', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['ids'])) {

            $dis[] = ['a.id', 'not in', $input['ids']];
        }

        $data = $this->model->adminDataList($dis, $input['limit'], $where);

        $list = [

            0 => 'all',

            1 => 'ing',

            2 => 'pass',

            4 => 'nopass'
        ];

        foreach ($list as $k => $value) {

            $dis_s = [];

            $dis_s[] = ['uniacid', '=', $this->_uniacid];

            if (!empty($k)) {

                $dis_s[] = ['status', '=', $k];

            } else {

                $dis_s[] = ['status', '>', -1];

            }

            $data[$value] = $this->model->where($dis_s)->count();

        }

        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-03 11:53
     * @功能说明:
     */
    public function channelInfo()
    {

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];

        $info = $this->model->dataInfo($dis);

        $user_model = new User();

        $user = $user_model->where(['id' => $info['user_id']])->find();

        $info['nickName'] = $user['nickName'];

        $info['avatarUrl'] = $user['avatarUrl'];

        $info['staff_count'] = ChannelStaff::where(['status' => 1, 'channel_id' => $input['id']])->where('user_id', '>', 0)->count();

        $order = (new Order())->channelData($input['id']);

        $info['order_price'] = $order['order_price'];

        $info['extract_total_price'] = (new \app\massage\model\Wallet())->capCash($input['id'], 2, 5);


        return $this->success($info);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-03 00:15
     * @功能说明:审核(2通过,3取消,4拒绝)
     */
    public function channelUpdate()
    {

        $input = $this->_input;

        $diss = [

            'id' => $input['id']
        ];

        $info = $this->model->dataInfo($diss);

        if (!empty($input['status']) && in_array($input['status'], [2, 4, -1])) {

            if ($input['status'] == -1) {

                if (!empty(floatval($info['cash']))) {

                    return $this->error('未全部提现的用户不可删除渠道商身份');
                }
                //删除渠道商、和员工解除绑定关系
                ChannelStaff::where('channel_id', $input['id'])->update(['status' => -1]);
            }

            $input['sh_time'] = time();
        }

        if (isset($input['cash']) && $input['cash'] != $info['cash']) {

            ChannelCashWater::record($this->_uniacid, $input['id'], $info['cash'], $input['cash']);
        }

        $data = $this->model->dataUpdate($diss, $input);

        return $this->success($data);

    }

    /**
     * @Desc: 渠道商员工列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/31 11:16
     */
    public function staffList()
    {
        $input = $this->_param;

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.channel_id', '=', $input['id']],
            ['a.status', '=', 1],
            ['a.user_id', '>', 0],
        ];
        $where_ = $where;

        $dis = [];
        if (!empty($input['name'])) {

            $dis[] = ['a.name', 'like', '%' . $input['name'] . '%'];
            $dis[] = ['b.nickName', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['start_time'])) {

            $where_[] = ['c.create_time', '>', $input['start_time']];
        }

        if (!empty($input['end_time'])) {

            $where_[] = ['c.create_time', '<=', $input['end_time']];
        }

        $data = ChannelStaff::staffList($where, $dis, $where_, $input['limit'] ?? 10);

        $order = (new Order())->channelData($input['id'], $input);

        $data['order_price'] = $order['order_price'];

        return $this->success($data);
    }

    /**
     * @Desc: 切换上级
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/31 11:51
     */
    public function changeChannel()
    {
        $data = request()->only(['channel_id', 'channel_staff_id']);

        $res = ChannelStaff::changeChannel($data);

        return $this->success($res);
    }

    /**
     * @Desc: 批量设置比例
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/31 14:02
     */
    public function changeBalance()
    {
        $data = request()->only(['ids', 'balance']);

        $res = $this->model->update(['balance' => $data['balance']], [['id', 'in', $data['ids']], ['status', '=', 2]]);

        return $this->success($res);

    }

    /**
     * @Desc: 手动余额变动记录
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/1 11:29
     */
    public function getCashList()
    {
        $input = $this->_param;

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['channel_id', '=', $input['id']]
        ];

        $data = ChannelCashWater::getCashList($where, $input['limit'] ?? 10);

        return $this->success($data);
    }

    /**
     * @Desc: 批量设置绑定时间
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/4/28 10:47
     */
    public function setBindTime()
    {
        $ids = request()->param('ids', []);

        $channel_bind_time = request()->param('channel_bind_time', 0);

        if (empty($ids)) {

            return $this->error('请选择渠道商');
        }

        $res = $this->model->update(['channel_bind_time' => $channel_bind_time], [['id', 'in', $ids]]);

        return $this->success($res);
    }

}
