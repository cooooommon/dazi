<?php

namespace app\massage\controller;

use app\AdminRest;
use app\massage\model\Article;

use app\massage\model\ArticleList;
use app\massage\model\Cap;
use app\massage\model\Config;
use app\massage\model\Date;

use app\massage\model\DemandOrder;
use app\massage\model\OrderAddress;
use app\massage\model\OrderGoods;
use app\massage\model\RefundOrder;
use app\massage\model\SubData;
use app\massage\model\SubList;
use app\massage\model\User;
use app\massage\model\UserFrom;
use app\massage\model\Wallet;
use app\store\model\PackageOrder;
use longbingcore\wxcore\Excel;
use think\App;
use app\massage\model\Order as Model;


class AdminExcel extends AdminRest
{


    protected $model;

    protected $order_goods_model;

    protected $refund_order_model;

    protected $attendant_name;

    public function __construct(App $app)
    {

        parent::__construct($app);

        $this->model = new Model();

        $this->order_goods_model = new OrderGoods();

        $this->refund_order_model = new RefundOrder();

        $this->attendant_name = getConfigSetting($this->_uniacid, 'attendant_name');;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:43
     * @功能说明:列表
     */
    public function orderList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];
        //时间搜素
        if (!empty($input['start_time']) && !empty($input['end_time'])) {

            $start_time = $input['start_time'];

            $end_time = $input['end_time'];

            $dis[] = ['a.create_time', 'between', "$start_time,$end_time"];
        }
        //商品名字搜索
        if (!empty($input['goods_name'])) {

            $dis[] = ['c.goods_name', 'like', '%' . $input['goods_name'] . '%'];
        }
        //手机号搜索
        if (!empty($input['mobile'])) {

            $order_address_model = new OrderAddress();

            $order_address_dis[] = ['mobile', 'like', '%' . $input['mobile'] . '%'];

            $order_id = $order_address_model->where($order_address_dis)->column('order_id');

            $dis[] = ['a.id', 'in', $order_id];
        }

        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['a.admin_id', 'in', $this->admin_arr];
        }

        //合伙人
        if (!empty($input['partner_id'])) {

            $dis[] = ['a.partner_id', '=', $input['partner_id']];

        }

        if (!empty($input['admin_id'])) {

            $dis[] = ['a.admin_id', '=', $input['admin_id']];
        }

        if (!empty($input['pay_type'])) {
            //订单状态搜索
            $dis[] = ['a.pay_type', '=', $input['pay_type']];

        } else {
            //除开待转单
            $dis[] = ['a.pay_type', '<>', 8];

        }

        $map = [];
        //店铺名字搜索
        if (!empty($input['coach_name'])) {

            $map[] = ['b.coach_name', 'like', '%' . $input['coach_name'] . '%'];

            $map[] = ['d.now_coach_name', 'like', '%' . $input['coach_name'] . '%'];
        }

        if (!empty($input['order_code'])) {

            $dis[] = ['a.order_code', 'like', '%' . $input['order_code'] . '%'];
        }

        if (!empty($input['channel_cate_id'])) {

            $dis[] = ['e.cate_id', '=', $input['channel_cate_id']];

        }

        if (!empty($input['channel_name'])) {

            $dis[] = ['e.user_name', 'like', '%' . $input['channel_name'] . '%'];
        }

        if (!empty($input['is_channel'])) {

            $dis[] = ['a.pay_type', '>', 1];

            $dis[] = ['a.channel_id', '<>', 0];

        }
        //是否是加钟
        if (isset($input['is_add'])) {

            $dis[] = ['a.is_add', '=', $input['is_add']];

        }

        if (!empty($input['is_coach'])) {

            if ($input['is_coach'] == 2) {

                $dis[] = ['a.coach_id', '=', 0];
            } else {

                $dis[] = ['a.coach_id', '>', 0];

            }

        }

        if (!empty($input['is_store'])) {

            if ($input['is_store'] == 1) {

                $dis[] = ['a.store_id', '>', 0];
            } else {

                $dis[] = ['a.store_id', '=', 0];

            }

        }

        $data = $this->model->adminDataSelect($dis, $map);

        if (!empty($input['is_channel'])) {

            if (!empty($input['is_add'])) {

                $name = '渠道财务加单';

                $type = 2;

            } else {

                $name = '渠道财务订单';

                $type = 1;

            }

        } else {

            if (!empty($input['is_add']) && $input['is_add'] == 1) {

                $name = '加单列表';

                $type = 3;

            } else {

                $name = '订单列表';

                $type = 2;

            }

        }

        $header[] = '订单ID';
        $header[] = '服务项目';
        $header[] = '项目价格';
        $header[] = '项目数量';
        $header[] = '下单人';
        $header[] = '手机号';
        $header[] = $this->attendant_name;
        if (empty($input['is_add'])) {
            $header[] = $this->attendant_name . '类型';
        }

        if (!empty($input['is_channel'])) {

            $header[] = '渠道商';

            $header[] = '渠道';
        }

//        $header[] = '服务方式';

        $header[] = '服务开始时间';

        if (empty($input['is_add'])) {

            $header[] = '出行费用';
        }

        $header[] = '服务项目费用';

        if (empty($input['is_add'])) {

            $header[] = '实收金额';
        }
        $header[] = '退款金额';

        if (empty($input['is_add'])) {

            $header[] = '子订单号';

        } else {

            $header[] = '主订单号';

        }

        $header[] = '系统订单号';
        $header[] = '付款订单号';
        $header[] = $this->attendant_name . '所属上级';
        $header[] = '下单时间';
        $header[] = '支付方式';
        $header[] = '状态';

        $new_data = [];

        foreach ($data as $v) {

            $info = array();

            $info[] = $v['id'];

            $info[] = $v['goods_name'];

            $info[] = $v['price'];

            $info[] = $v['num'];

            $info[] = $v['user_name'];

            $info[] = $v['mobile'];

            $info[] = !empty($v['coach_info']['coach_name']) ? $v['coach_info']['coach_name'] : '';

            if (empty($input['is_add'])) {

                $info[] = $v['coach_id'] > 0 ? '入驻' . $this->attendant_name : '非入驻' . $this->attendant_name;
            }

            if (!empty($input['is_channel'])) {

                $info[] = $v['channel_name'];

                $info[] = $v['channel'];
            }

//            $info[] = $v['store_id']>0?'到店服务':'上门服务';

            $info[] = date('Y-m-d H:i:s', $v['start_time']);

            if (empty($input['is_add'])) {

                $info[] = $v['car_price'];

            }

            $info[] = $v['init_service_price'];

            if (empty($input['is_add'])) {

                $info[] = $v['pay_price'];
            }

            $info[] = $v['refund_price'];

            if (empty($input['is_add'])) {

                $info[] = !empty($v['add_order_id'][0]['order_code']) ? $v['add_order_id'][0]['order_code'] : '';

            } else {

                $info[] = !empty($v['add_pid']['order_code']) ? $v['add_pid']['order_code'] : '';;

            }

            $info[] = $v['order_code'];

            $info[] = $v['transaction_id'];

            $info[] = !empty($v['partner_id']) ? $v['partner_name'] : $v['admin_name'];

            $info[] = date('Y-m-d H:i:s', $v['create_time']);

            $info[] = $this->payModel($v['pay_model']);

            $info[] = $this->orderStatusText($v['pay_type']);

            $new_data[] = $info;
        }


        $excel = new Excel();

        $excel->excelExport($name, $header, $new_data, '', $type);

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-12-26 21:59
     * @功能说明:支付方式
     */
    public function payModel($type)
    {

        switch ($type) {

            case 1;

                $text = '微信支付';
                break;

            case 2;
                $balance_character = Config::where('uniacid', $this->_uniacid)->value('balance_character');
                $text = empty($balance_character) ? '余额支付' : $balance_character;
                break;
            case 3;

                $text = '支付宝支付';
                break;
        }

        return $text;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-30 16:32
     * @功能说明:
     */
    public function orderStatusText($status)
    {

        switch ($status) {

            case 1:
                return '待支付';

                break;
            case 2:
                return '待服务';

                break;
            case 3:
                return $this->attendant_name . '接单';

                break;
            case 4:
                return $this->attendant_name . '出发';

                break;
            case 5:
                return $this->attendant_name . '到达';

                break;
            case 6:
                return '服务中';

                break;

            case 7:
                return '已完成';

                break;
            case 8:
                return '待转单';

                break;

            case -1:
                return '已取消';

                break;

        }

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-18 13:37
     * @功能说明:财务数据统计导出
     */
    public function dateCount()
    {

        $input = $this->_param;

        $cap_id = $input['cap_id'];

        $date_model = new Date();

        $wallet_model = new Wallet();

        $cap_model = new Cap();

        $date_model->dataInit($this->_uniacid);

        $dis[] = ['uniacid', '=', $this->_uniacid];
        //时间搜素
        if (!empty($input['start_time']) && !empty($input['end_time'])) {

            $start_time = $input['start_time'];

            $end_time = $input['end_time'];

            $dis[] = ['date_str', 'between', "$start_time,$end_time"];
        }

        $date_list = $date_model->dataList($dis, 100000);
        //店铺名字
        $store_name = $cap_model->where(['id' => $cap_id])->value('store_name');
        //开始时间结束时间
        if (!empty($start_time)) {

            $date_list['start_time'] = $start_time;

            $date_list['end_time'] = $end_time;

        } else {

            $date_list['start_time'] = $date_model->where(['uniacid' => $this->_uniacid])->min('date_str');

            $date_list['end_time'] = $date_model->where(['uniacid' => $this->_uniacid])->max('date_str');

        }

        if (!empty($date_list['data'])) {

            foreach ($date_list['data'] as $k => $v) {
                //订单金额
                $date_list['data'][$k]['order_price'] = $this->model->datePrice($v['date_str'], $this->_uniacid, $cap_id);
                //退款金额
                $date_list['data'][$k]['refund_price'] = $this->refund_order_model->datePrice($v['date_str'], $this->_uniacid, $cap_id);
                //提现金额
                $date_list['data'][$k]['wallet_price'] = $wallet_model->datePrice($v['date_str'], $this->_uniacid, $cap_id);

            }

        }

        $name = $store_name . '财务报表';

        $header = [
            '收支时间',
            '订单收入',
            '订单退款',
            '提现（元）',
        ];

        $new_data = [];

        foreach ($date_list['data'] as $v) {

            $info = array();

            $info[] = $v['date'];

            $info[] = $v['order_price'];

            $info[] = $v['refund_price'];

            $info[] = $v['wallet_price'];

            $new_data[] = $info;
        }

        $excel = new Excel();

        $excel->excelExport($name, $header, $new_data);

        return $this->success($date_list);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-12-15 12:05
     * @功能说明:提交内容导出
     */
    public function subDataList()
    {

        $input = $this->_param;

        $article_model = new ArticleList();

        $sub_list_model = new SubList();

        $sub_data_model = new SubData();

        $article_title = $article_model->where(['id' => $input['article_id']])->value('title');
        //获取导出标题
        $title_data = $article_model->getFieldTitle($input['article_id']);

        $title = ['用户ID', '微信昵称'];

        $title = array_merge($title, array_column($title_data, 'title'));

        $title[] = '提交时间';

        $name = '文章表单数据导出-' . $article_title;

        $diss[] = ['article_id', '=', $input['article_id']];

        $diss[] = ['status', '=', 1];

        if (!empty($input['start_time']) && !empty($input['end_time'])) {

            $diss[] = ['create_time', 'between', "{$input['start_time']},{$input['end_time']}"];

        }

        if (!empty($input['id'])) {

            $diss[] = ['id', 'in', $input['id']];

        }

        $list = $sub_list_model->where($diss)->order('id desc')->select()->toArray();

        $new_data = [];

        if (!empty($list)) {

            $user_model = new User();

            foreach ($list as &$v) {

                $user_info = $user_model->where(['id' => $v['user_id']])->field('nickName,avatarUrl')->find();

                $info = array();

                $info[] = $v['user_id'];

                $info[] = $user_info['nickName'];

                if (!empty($title_data)) {

                    foreach ($title_data as $vs) {

                        $dis = [

                            'field_id' => $vs['field_id'],

                            'sub_id' => $v['id']
                        ];

                        $find = $sub_data_model->where($dis)->value('value');;

                        $info[] = !empty($find) ? $find : '';
                    }
                }

                $info[] = date('Y-m-d H:i:s', $v['create_time']);

                $new_data[] = $info;

            }

        }

        $excel = new Excel();

        $excel->excelExport($name, $title, $new_data);

        return $this->success(true);

    }


    public function demandOrder()
    {
        $input = $this->request->param();
        $where = [];
        $where[] = ['a.uniacid', '=', $this->_uniacid];
        if (!empty($input['ser_name'])) {
            $where[] = ['c.name', 'like', '%' . $input['ser_name'] . '%'];
        }
        if (!empty($input['order_code'])) {
            $where[] = ['a.order_code', 'like', '%' . $input['order_code'] . '%'];
        }
        if (!empty($input['phone'])) {
            $where[] = ['a.phone', 'like', '%' . $input['phone'] . '%'];
        }
        if (!empty($input['start_time'])) {
            $where[] = ['a.create_time', '>=', $input['start_time']];
        }
        if (!empty($input['end_time'])) {
            $where[] = ['a.create_time', '<=', $input['end_time']];
        }
        $where[] = ['a.status', '>', 0];
        $where[] = ['a.is_refund', 'in', [0, 3]];
        if (!empty($input['status'])) {
            switch ($input['status']) {
                case 1:
                    $where[] = ['a.status', '=', 2];
                    break;
                case 2:
                    $where[] = ['a.status', '=', 3];
                    break;
                case 3:
                    $where[] = ['a.status', '=', 4];
                    break;
                case 4:
                    $where[] = ['a.is_refund', '>', 0];
                    break;
                case 5:
                    $where[] = ['a.status', '=', 1];
                    break;
            }
        }
        if (!empty($input['coach_id'])) {
            $where[] = ['a.coach_id', '=', $input['coach_id']];
        }
        if (!empty($input['coach_name'])) {
            $where[] = ['d.coach_name', 'like', '%' . $input['coach_name'] . '%'];
        }
        $header = [
            'ID', '服务类型', '服务时间', '邀约服务金额', '客户昵称', '手机号', '接单人', '支付方式', '系统订单号', '商户订单号', '下单时间', '状态'
        ];
        $data = DemandOrder::getExcelList($where);
        $new_data = [];
        $balance_character = Config::where('uniacid', $this->_uniacid)->value('balance_character');
        if ($data) {
            foreach ($data as $item) {
                switch ($item['status']) {
                    //-1取消  0拒绝  1待审核  2待接单  3已接单  4已完成
                    case -2:
                        $status = '超时取消';
                        break;
                    case -1:
                        $status = '取消';
                        break;
                    case 0:
                        $status = '拒绝';
                        break;
                    case 1:
                        $status = '待审核';
                        break;
                    case 2:
                        $status = '待接单';
                        break;
                    case 3:
                        $status = '已接单';
                        break;
                    case 4:
                        $status = '已完成';
                        break;
                }
                $new_data[] = [
                    $item['id'],
                    $item['name'],
                    $item['start_time'] . '-' . $item['end_time'],
                    $item['price'],
                    $item['nickName'],
                    $item['phone'],
                    $item['coach_name'] ?? '',
                    $item['pay_type'] == 1 ? '支付宝' : ($item['pay_type'] == 2 ? '微信' : (empty($balance_character) ? '余额' : $balance_character)),
                    $item['order_code'],
                    $item['transaction_id'],
                    $item['create_time'],
                    $status,
                ];
            }
        }
        $excel = new Excel();
        $name = '邀约订单导出';
        $excel->excelExport($name, $header, $new_data);
        return $this->success(true);
    }

    /**
     * @Desc: 套餐订单导出
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/12 13:59
     */
    public function packageOrder()
    {
        $input = $this->_param;

        $where = [
            ['a.uniacid', '=', $this->_uniacid]
        ];

        if (!empty($input['name'])) {

            $where[] = ['a.name', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['store_id'])) {

            $where[] = ['a.store_id', '=', $input['store_id']];
        }

        if (!empty($input['order_code'])) {

            $where[] = ['a.order_code', 'like', '%' . $input['order_code'] . '%'];
        }

        if (!empty($input['mobile'])) {

            $where[] = ['a.mobile', 'like', '%' . $input['mobile'] . '%'];
        }

        if (!empty($input['start_time'])) {

            $where[] = ['a.create_time', '>=', $input['start_time']];
        }

        if (!empty($input['end_time'])) {

            $where[] = ['a.create_time', '<=', $input['end_time']];
        }

        if (!empty($input['nickName'])) {

            $where[] = ['b.nickName', 'like', '%' . $input['nickName'] . '%'];
        }

        if (!empty($input['status'])) {

            $where[] = ['a.status', '=', $input['status']];
        }

        $data = PackageOrder::getExcelList($where);

        $header = [
            'ID', '套餐名称', '套餐保障', '套餐金额', '套餐数量', '下单人', '下单手机号', '套餐实付', '核销数量', '门店', '门店提成', '推广者', '推广提成', '系统订单号', '付款订单号', '下单时间', '支付方式', '状态'
        ];

        $pay_type = [
            1 => '微信',
            2 => '余额',
            3 => '支付宝'
        ];

        $status = [
            -1 => '已取消',
            1 => '待支付',
            2 => '待核销',
            4 => '待评价',
            5 => '已评价'
        ];

        $new_data = [];

        if ($data) {

            foreach ($data as $item) {

                $ensure = ($item['ensure'] == 1 ? '过期自动退' : '人工审核') . ' · ' . ($item['reservation_day'] > 0 ? '提前' . $item['reservation_day'] . '天预约' : '无需预约');

                $item['status'] = $item['status'] == 3 && $item['is_comment'] == 0 ? 4 : ($item['status'] == 3 && $item['is_comment'] == 1 ? 5 : $item['status']);

                $new_data[] = [
                    $item['id'],
                    $item['name'],
                    $ensure,
                    $item['price'],
                    $item['num'],
                    $item['nickName'],
                    $item['mobile'],
                    $item['pay_price'],
                    $item['hx_num'],
                    $item['store_name'],
                    $item['store_balance'],
                    $item['share_name'],
                    $item['share_balance'],
                    $item['order_code'],
                    $item['transaction_id'],
                    handleTime($item['create_time']),
                    $pay_type[$item['pay_model']],
                    $status[$item['status']]
                ];
            }
        }

        $excel = new Excel();
        $name = '邀约订单导出';
        $excel->excelExport($name, $header, $new_data);
        return $this->success(true);
    }

    /**
     * @Desc: 用户列表导出
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/4/18 10:37
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

        if (!empty($input['form_type'])) {

            $user_id = UserFrom::where('from_type', $input['form_type'])->column('user_id');

            $dis[] = ['id', 'in', $user_id];
        }

        $data = User::getExcelList($this->_uniacid, $dis, $where);

        $header = [
            '微信昵称',
            '手机号',
            '账户余额',
            '客户来源'
        ];

        $new_data = [];

        foreach ($data as $datum) {

            $new_data[] = [
                $datum['nickName'],
                $datum['phone'],
                $datum['balance'],
                $datum['from_name'] ?? ''
            ];
        }
        $excel = new Excel();

        $name = '客户资料导出';

        $excel->excelExport($name, $header, $new_data);

        return $this->success(true);
    }
}
