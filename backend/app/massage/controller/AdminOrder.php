<?php

namespace app\massage\controller;

use app\AdminRest;
use app\dynamic\model\DynamicList;
use app\massage\model\Coach;
use app\massage\model\CoachChangeLog;
use app\massage\model\CoachLevel;
use app\massage\model\CoachTimeList;
use app\massage\model\Comment;
use app\massage\model\CommentGoods;
use app\massage\model\CommentLable;
use app\massage\model\Commission;
use app\massage\model\Config;
use app\massage\model\CouponRecord;
use app\massage\model\DemandOrder;
use app\massage\model\DemandType;
use app\massage\model\HelpConfig;
use app\massage\model\Lable;
use app\massage\model\NoticeList;
use app\massage\model\Order;

use app\massage\model\OrderAddress;
use app\massage\model\OrderData;
use app\massage\model\OrderGoods;
use app\massage\model\OrderLog;
use app\massage\model\Police;
use app\massage\model\RefundOrder;
use app\massage\model\ShortCodeConfig;
use app\massage\model\StoreList;
use app\massage\model\UpOrderList;
use app\massage\model\User;
use app\massage\model\WorkLog;
use app\virtual\info\PermissionVirtual;
use longbingcore\permissions\SaasAuthConfig;
use longbingcore\wxcore\aliyun;
use longbingcore\wxcore\Fdd;
use longbingcore\wxcore\PayModel;
use longbingcore\wxcore\WxSetting;
use think\App;
use app\shop\model\Order as Model;
use think\facade\Db;
use tp5er\Backup;


class AdminOrder extends AdminRest
{
    protected $model;
    protected $refund_order_model;

    protected $comment_model;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new Order();
        $this->refund_order_model = new RefundOrder();
        $this->comment_model = new Comment();
    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:43
     * @功能说明:列表
     */
    public function orderList()
    {
//        $a = imagecreatefrompng( 'http://longbinghuoke.migugu.com/image/9351/23/04/ghgYNXxPBT5GWdpZXGfHfVAFznB3RSTt.png' );
//
//        dump($a);exit;

//        $p = new PermissionVirtual(666);
//
//        $auth = $p->pAuth();
//
//        dump(1);exit;

        $input = $this->_param;
        //超时自动取消订单
        $this->model->autoCancelOrder($this->_uniacid);

        $dis[] = ['a.uniacid', '=', $this->_uniacid];
        //时间搜素
        if (!empty($input['start_time']) && !empty($input['end_time'])) {

            $start_time = $input['start_time'];

            $end_time = $input['end_time'];

            $dis[] = ['a.create_time', 'between', "$start_time,$end_time"];
        }
        //商品名字搜索
        if (!empty($input['goods_name'])) {

            $order_goods_dis[] = ['goods_name', 'like', '%' . $input['goods_name'] . '%'];

            $order_goods_dis[] = ['status', '=', 1];

            $order_goods_model = new OrderGoods();

            $order_id = $order_goods_model->where($order_goods_dis)->column('order_id');

            $dis[] = ['a.id', 'in', $order_id];

        }
        //手机号搜索
        if (!empty($input['mobile'])) {

            $order_address_model = new OrderAddress();

            $order_address_dis[] = ['mobile', 'like', '%' . $input['mobile'] . '%'];

            $order_id = $order_address_model->where($order_address_dis)->column('order_id');

            $dis[] = ['a.id', 'in', $order_id];
        }

        if (!empty($input['pay_type'])) {
            //订单状态搜索
            $dis[] = ['a.pay_type', '=', $input['pay_type']];

        } else {
            //除开待转单
            $dis[] = ['a.pay_type', '<>', 8];

        }
        //是否是渠道商
        if (!empty($input['is_channel'])) {

            $dis[] = ['a.pay_type', '>', 1];

            $dis[] = ['a.channel_id', '<>', 0];

        }

        $map = [];
        //店铺名字搜索
        if (!empty($input['coach_name'])) {

            $map[] = ['b.coach_name', 'like', '%' . $input['coach_name'] . '%'];

            $map[] = ['c.now_coach_name', 'like', '%' . $input['coach_name'] . '%'];

        }
        //订单号搜索
        if (!empty($input['order_code'])) {

            $dis[] = ['a.order_code', 'like', '%' . $input['order_code'] . '%'];
        }
        //代理商端查询
        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['a.admin_id', 'in', $this->admin_arr];
        }
        //代理商查询
        if (!empty($input['admin_id'])) {

            $dis[] = ['a.admin_id', '=', $input['admin_id']];
        }
        //合伙人
        if (!empty($input['partner_id'])) {

            $dis[] = ['a.partner_id', '=', $input['partner_id']];

        }
        //渠道搜索
        if (!empty($input['channel_cate_id'])) {

            $dis[] = ['e.cate_id', '=', $input['channel_cate_id']];

        }
        //渠道商搜索
        if (!empty($input['channel_name'])) {

            $dis[] = ['e.user_name', 'like', '%' . $input['channel_name'] . '%'];
        }
        //是否是加钟
        if (isset($input['is_add'])) {

            $dis[] = ['a.is_add', '=', $input['is_add']];

        }
        //订单类型 门店|上门服务
        if (!empty($input['is_store'])) {

            if ($input['is_store'] == 1) {

                $dis[] = ['a.store_id', '>', 0];
            } else {

                $dis[] = ['a.store_id', '=', 0];

            }

        }
        //是否是线下向导
        if (!empty($input['is_coach'])) {

            if ($input['is_coach'] == 2) {

                $dis[] = ['a.coach_id', '=', 0];
            } else {

                $dis[] = ['a.coach_id', '>', 0];

            }

        }

        $data = $this->model->adminDataList($dis, $input['limit'], $map);
        //订单金额
        $data['order_price'] = $this->model->adminOrderPrice($dis, 1, $map);
        //车费
        $data['car_price'] = $this->model->adminOrderPrice($dis, 2, $map);

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:58
     * @功能说明:订单详情
     */
    public function orderInfo()
    {

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];

        $data = $this->model->dataInfo($dis);

        $data['distance'] = distance_text($data['distance']);
        //加钟订单
        if ($data['is_add'] == 0) {

            $data['add_order_id'] = $this->model->where(['add_pid' => $data['id']])->where('pay_type', '>', 1)->field('id,order_code')->select()->toArray();

        } else {

            $data['add_pid'] = $this->model->where(['id' => $data['add_pid']])->field('id,order_code')->find();

        }

        $order_model = new OrderData();
        //订单附表
        $order_data = $order_model->dataInfo(['order_id' => $input['id'], 'uniacid' => $this->_uniacid]);

        $data = array_merge($order_data, $data);
        //订单转派记录
        $change_log_model = new CoachChangeLog();

        $data['dispatch_record'] = $change_log_model->orderChangeLog($input['id']);

        $admin_model = new \app\massage\model\Admin();

        $data['admin_name'] = $admin_model->where(['id' => $data['admin_id']])->value('agent_name');
        //门店订单
        if (!empty($data['store_id'])) {

            $store_model = new StoreList();

            $data['store_info'] = $store_model->where(['id' => $data['store_id']])->field('title,cover,address,lng,lat,phone')->find();
        }

        $data['pay_price'] = round($data['pay_price'], 2);

        $data['true_service_price'] = round($data['true_service_price'], 2);

        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-19 17:50
     * @功能说明:退款订单详情
     */
    public function refundOrderInfo()
    {

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];

        $data = $this->refund_order_model->dataInfo($dis);

        $data['pay_order_code'] = $this->model->where(['id' => $data['order_id']])->value('order_code');

        $data['create_time'] = date('Y-m-d H:i:s', $data['create_time']);

        $data['refund_time'] = !empty($data['refund_time']) ? date('Y-m-d H:i:s', $data['refund_time']) : "";

        $pay_order = $this->model->dataInfo(['id' => $data['order_id']]);

        $data['car_type'] = $pay_order['car_type'];

        $data['distance'] = distance_text($pay_order['distance']);

        $data['pay_car_price'] = $pay_order['car_price'];

        $admin_model = new \app\massage\model\Admin();

        $data['admin_name'] = $admin_model->where(['id' => $data['admin_id']])->value('agent_name');

        $data['store_id'] = $pay_order['store_id'];
        //门店订单
        if (!empty($pay_order['store_id'])) {

            $store_model = new StoreList();

            $data['store_info'] = $store_model->where(['id' => $pay_order['store_id']])->field('title,cover,address,lng,lat,phone')->find();
        }

        return $this->success($data);

    }

    /**
     * @author chenniang
     * @DataTime: 2021-03-17 17:44
     * @功能说明:退款订单列表
     */
    public function refundOrderList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];
        //商品名字搜索
        if (!empty($input['goods_name'])) {

            $dis[] = ['c.goods_name', 'like', '%' . $input['goods_name'] . '%'];
        }
        //订单状态搜索
        if (!empty($input['status'])) {

            $dis[] = ['a.status', '=', $input['status']];

        } else {

            $dis[] = ['a.status', '>', -1];

        }

        $where = [];

        if (!empty($input['order_code'])) {

            $where[] = ['a.order_code', 'like', '%' . $input['order_code'] . '%'];

            $where[] = ['d.order_code', 'like', '%' . $input['order_code'] . '%'];
        }

        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['a.admin_id', 'in', $this->admin_arr];

        }

        if (!empty($input['admin_id'])) {

            $dis[] = ['a.admin_id', '=', $input['admin_id']];
        }

        if (!empty($input['partner_id'])) {

            $dis[] = ['a.partner_id', '=', $input['partner_id']];

        }

//        $is_add = !empty($input['is_add']) ? $input['is_add'] : 0;
//
//        $dis[] = ['a.is_add', '=', $is_add];

        $data = $this->refund_order_model->adminDataList($dis, $input['limit'], $where);

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-18 09:21
     * @功能说明:拒绝退款
     */
    public function noPassRefund()
    {

        $input = $this->_input;

        $res = $this->refund_order_model->noPassRefund($input['id']);

        if (!empty($res['code'])) {

            $this->errorMsg($res['msg']);
        }

        return $this->success($res);

    }


    /**\
     * @author chenniang
     * @DataTime: 2021-03-18 09:28
     * @功能说明:同意退款
     */
    public function passRefund()
    {

        $input = $this->_input;

        $order = $this->refund_order_model->dataInfo(['id' => $input['id']]);

        $is_app = $this->model->where(['id' => $order['order_id']])->value('app_pay');

        $res = $this->refund_order_model->passOrder($input['id'], $input['price'], $this->payConfig($is_app), $this->_user['id'], $input['text']);

        if (!empty($res['code'])) {

            $this->errorMsg($res['msg']);
        }

        return $this->success($res);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-05 23:16
     * @功能说明:评价列表
     */
    public function commentList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.status', '>', -1];

        if (!empty($input['star'])) {

            $dis[] = ['a.star', '=', $input['star']];
        }

        if (!empty($input['coach_name'])) {

            $dis[] = ['d.coach_name', 'like', '%' . $input['coach_name'] . '%'];
        }

        if (!empty($input['goods_name'])) {

            $dis[] = ['c.goods_name', 'like', '%' . $input['goods_name'] . '%'];

        }

        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['a.admin_id', 'in', $this->admin_arr];
        }

        if (!empty($input['order_id'])) {
            $dis[] = ['a.order_id', '=', $input['order_id']];
        }

        $data = $this->comment_model->dataList($dis, $input['limit']);

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-05 23:31
     * @功能说明:编辑评价
     */
    public function commentUpdate()
    {

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $data = $this->comment_model->dataUpdate($dis, $input);
        //删除评价需要重新计算分数
        if (!empty($input['status']) && $input['status'] == -1) {

            $info = $this->comment_model->dataInfo($dis);

            $this->comment_model->updateStarV2($info['coach_id']);
        }

        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-04 18:53
     * @功能说明:评价标签列表
     */
    public function commentLableList()
    {

        $input = $this->_param;

        $lable_model = new Lable();

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', '>', -1];

        $data = $lable_model->dataList($dis, $input['limit']);

        return $this->success($data);


    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-04 18:56
     * @功能说明:添加评价标签
     */
    public function commentLableAdd()
    {

        $input = $this->_input;

        $input['uniacid'] = $this->_uniacid;

        $lable_model = new Lable();

        $res = $lable_model->dataAdd($input);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-04 18:57
     * @功能说明:编辑评价标签
     */
    public function commentLableUpdate()
    {

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $lable_model = new Lable();

        $res = $lable_model->dataUpdate($dis, $input);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-04 18:59
     * @功能说明:评价标签详情
     */
    public function commentLableInfo()
    {

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $lable_model = new Lable();

        $res = $lable_model->dataInfo($dis);

        return $this->success($res);


    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-16 10:27
     * @功能说明:提示列表
     */
    public function noticeList()
    {

        $input = $this->_param;

        $notice_model = new NoticeList();

        $dis[] = ['uniacid', '=', $this->_uniacid];

        if (!empty($input['type'])) {

            $dis[] = ['type', '=', $input['type']];
        }

        if (!empty($input['types'])) {

            $dis[] = ['type', 'in', explode(',', $input['types'])];
        }

        if (isset($input['have_look'])) {

            if (in_array($this->_user['is_admin'], [1, 2])) {

                $dis[] = ['have_look', '=', $input['have_look']];

            } else {

                $dis[] = ['agent_have_look', '=', $input['have_look']];

            }

        } else {

            if (in_array($this->_user['is_admin'], [1, 2])) {

                $dis[] = ['have_look', '>', -1];

            } else {

                $dis[] = ['agent_have_look', '>', -1];

            }

        }

        if (!empty($input['start_time'])) {

            $dis[] = ['create_time', 'between', "{$input['start_time']},{$input['end_time']}"];
        }

        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['admin_id', 'in', $this->admin_arr];
        }

        $data = $notice_model->dataList($dis, $input['limit']);

        if (!empty($data['data'])) {

            $order_model = new Order();

            $refund_model = new RefundOrder();

            foreach ($data['data'] as &$v) {

                if ($v['type'] == 1) {

                    $v['is_add'] = $order_model->where(['id' => $v['order_id']])->value('is_add');

                } elseif ($v['type'] == 2) {

                    $order_id = $refund_model->where(['id' => $v['order_id']])->value('order_id');

                    $v['is_add'] = $order_model->where(['id' => $order_id])->value('is_add');
                }
                if ($this->_user['is_admin'] == 0) {

                    $v['have_look'] = $v['agent_have_look'];

                }
            }
        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-16 10:38
     * @功能说明:未查看的数量
     */
    public function noLookCount()
    {
        $input = $this->_param;
        $dis[] = ['uniacid', '=', $this->_uniacid];
        if (in_array($this->_user['is_admin'], [1, 2])) {
            $dis[] = ['have_look', '=', 0];
        } elseif ($this->_user['user_id'] > 0) {
            $dis[] = ['agent_have_look', '=', 0];
        }
        if ($this->_user['is_admin'] == 0) {
            $dis[] = ['admin_id', 'in', $this->admin_arr];
        }
        $where = $dis;
        if (!empty($input['types'])) {
            $where[] = ['type', 'in', explode(',', $input['types'])];
        }
        $notice_model = new NoticeList();
        $num = $notice_model->where($where)->count();
        $final = $notice_model->getFinalInfo($where);
        $data['notice_num'] = $num;
        if ($this->_user['is_admin'] != 0) {
            $dis[] = ['status', '>', -1];
            $police_model = new Police();
            $data['police_num'] = $police_model->where($dis)->count();
            if ($data['police_num'] > 0) {
                $config = new HelpConfig();
                $data['help_voice'] = $config->where(['uniacid' => $this->_uniacid])->value('help_voice');
                $final = $police_model->getFinalInfo($dis, $final);
            }
        }
        $data['final'] = empty($final) ? '' : $final;
        //异步执行订单消息通知
//        publisher(json_encode(['action' => 'orderServiceNotice', 'uniacid' => $this->_uniacid], true));
        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-16 10:41
     * @功能说明:全部已读
     */
    public function allLook()
    {
        $input = $this->_param;
        $dis[] = ['uniacid', '=', $this->_uniacid];
        //区分平台和代理商
        if (in_array($this->_user['is_admin'], [1, 2])) {
            $dis[] = ['have_look', '=', 0];
            $update = ['have_look' => 1, 'is_pop' => 1];
        } else {
            $dis[] = ['agent_have_look', '=', 0];
            $update = ['agent_have_look' => 1, 'is_pop' => 1];
        }
        $notice_model = new NoticeList();
        $data = $notice_model->dataUpdate($dis, $update);
        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-16 10:28
     * @功能说明:
     */
    public function noticeUpdate()
    {
        $input = $this->_input;
        //区分平台和代理商
        if ($this->_user['is_admin'] == 0) {
            $input['agent_have_look'] = $input['have_look'];
            if (isset($input['have_look'])) {
                unset($input['have_look']);
            }
        }
        $notice_model = new NoticeList();
        $data = $notice_model->dataUpdate(['id' => $input['id']], $input);
        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2022-12-15 11:34
     * @功能说明:订单升级记录
     */
    public function orderUpRecord()
    {
        $input = $this->_param;
        $order_model = new UpOrderList();
        $data = $order_model->orderUpRecord($input['order_id']);
        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-11 22:34
     * @功能说明:向导修改订单信息)
     */
    public function adminUpdateOrder()
    {
        $input = $this->_input;
        $order = $this->model->dataInfo(['id' => $input['order_id']]);
        $update = $this->model->coachOrdertext($input, 1);

        $refund_model = new RefundOrder();
        //判断有无申请中的退款订单
        $refund_order = $refund_model->dataInfo(['order_id' => $order['id'], 'status' => 1]);

        if (!empty($refund_order)) {

            $this->errorMsg('该订单正在申请退款，请先联系处理再进行下一步');

        }

        if ($order['pay_type'] < 1) {

            $this->errorMsg('订单已被取消');
        }

        Db::startTrans();

        if ($input['type'] == 7) {

            if ($order['pay_type'] != 6 && !empty($order['coach_id'])) {

                $this->errorMsg('订单状态错误');
            }

            $this->model->hxOrder($order);

        } elseif ($input['type'] == -1) {

            if ($order['pay_price'] > 0 && $order['pay_type'] > 1) {

                $refund_model = new RefundOrder();

                $res = $refund_model->refundCash($this->payConfig($order['app_pay']), $order, $order['pay_price']);

                if (!empty($res['code'])) {

                    Db::rollback();

                    $this->errorMsg($res['msg']);

                }

                if ($res != true) {

                    Db::rollback();

                    $this->errorMsg('退款失败，请重试2');

                }

            }
            //取消订单
            $res = $this->model->cancelOrder($order);

            if (!empty($res['code'])) {

                Db::rollback();

                $this->errorMsg($res['msg']);

            }

        }
        $this->model->dataUpdate(['id' => $input['order_id']], $update);

        $log_model = new OrderLog();

        $log_model->addLog($input['order_id'], $this->_uniacid, $input['type'], $order['pay_type'], 1, $this->_user['id']);

        Db::commit();

        return $this->success(true);
    }


    /**
     * @author chenniang
     * @DataTime: 2023-02-27 14:52
     * @功能说明:订单更换向导
     */
    public function orderChangeCoach()
    {

        $input = $this->_input;

        $refund_model = new RefundOrder();
        //判断有无申请中的退款订单
        $refund_order = $refund_model->dataInfo(['order_id' => $input['order_id'], 'status' => 1]);

        if (!empty($refund_order)) {

            $this->errorMsg('该订单正在申请退款，请先处理再进行下一步');

        }

        $success_add_order = $this->model->dataInfo(['add_pid' => $input['order_id'], 'pay_type' => 7]);

        if (!empty($success_add_order)) {

            $this->errorMsg('该订单续单订单已经完成，无法转单');

        }

        $order = $this->model->dataInfo(['id' => $input['order_id']]);

        $change_model = new CoachChangeLog();

        $coach_name = !empty($input['coach_name']) ? $input['coach_name'] : '';

        $text = !empty($input['text']) ? $input['text'] : '';

        $phone = !empty($input['mobile']) ? $input['mobile'] : '';

        $admin_id = !empty($input['admin_id']) ? $input['admin_id'] : 0;

        $res = $change_model->orderChangeCoach($order, $input['coach_id'], $this->_user['id'], $admin_id, $coach_name, $text, $phone);

        if (!empty($res['code'])) {

            $this->errorMsg($res['msg']);

        }

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-02-28 14:21
     * @功能说明:订单转派向导列表
     */
    public function orderChangeCoachList()
    {

        $input = $this->_param;

        $coach_model = new Coach();

        $order = $this->model->dataInfo(['id' => $input['order_id']]);
        //获取订单里想关联服务的向导
        $coach_id = $coach_model->getOrderServiceCoach($order);

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', '=', 2];

        $dis[] = ['id', 'in', $coach_id];

        $dis[] = ['id', '<>', $order['coach_id']];

        $dis[] = ['is_work', '=', 1];

        $dis[] = ['user_id', '>', 0];

        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['admin_id', 'in', $this->admin_arr];
        }

        if (!empty($input['coach_name'])) {

            $dis[] = ['coach_name', 'like', '%' . $input['coach_name'] . '%'];

        }
        //门店订单 只能转同门店的向导
        if (!empty($order['store_id'])) {

            $dis[] = ['store_id', '=', $order['store_id']];

        }

        $list = $coach_model->where($dis)->select()->toArray();

        $log_model = new CoachChangeLog();
        //转派向导时候 获取满足条件的向导 并获取最近的服务时间
        $arr = $log_model->getNearTimeCoach($order, $list);

        $top = !empty($input['type']) && $input['type'] == 1 ? 'distance asc,id desc' : 'near_time asc,id desc';

        $lat = $order['address_info']['lat'];

        $lng = $order['address_info']['lng'];

        $alh = 'ACOS(SIN((' . $lat . ' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS((' . $lat . ' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS((' . $lng . ' * 3.1415) / 180 - (lng * 3.1415) / 180 ) ) * 6378.137*1000 as distance';

        $data = $coach_model->where('id', 'in', $arr)->field(['*', $alh])->order($top)->paginate($input['limit'])->toArray();

        if (!empty($data['data'])) {

            $admin_model = new \app\massage\model\Admin();

            $user_model = new User();

            foreach ($data['data'] as &$v) {

                $v['partner_name'] = $user_model->where(['id' => $v['partner_id']])->value('nickName');

                $v['near_time'] = date('m-d H:i', $v['near_time']);

                $v['admin_info'] = $admin_model->where(['id' => $v['admin_id']])->field('city_type,agent_name as username')->find();

            }

        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-12 14:01
     * @功能说明:添加评价
     */
    public function addComment()
    {

        $input = $this->_input;

        $insert = [

            'uniacid' => $this->_uniacid,

            'user_id' => 0,

            'order_id' => 0,

            'star' => $input['star'],

            'text' => $input['text'],

            'coach_id' => $input['coach_id'],

            'admin_id' => 0,

        ];

        Db::startTrans();

        $comment_model = new Comment();

        $comment_lable_model = new CommentLable();

        $lable_model = new Lable();

        $res = $comment_model->dataAdd($insert);

        if ($res == 0) {

            Db::rollback();

            $this->errorMsg('评价失败');
        }

        $comment_id = $comment_model->getLastInsID();

        if (!empty($input['lable'])) {

            foreach ($input['lable'] as $value) {

                $title = $lable_model->where(['id' => $value])->value('title');

                $insert = [

                    'uniacid' => $this->_uniacid,

                    'comment_id' => $comment_id,

                    'lable_id' => $value,

                    'lable_title' => $title,

                ];

                $comment_lable_model->dataAdd($insert);
            }

        }

        $comment_model->updateStar($input['coach_id']);

        Db::commit();

        return $this->success($res);

    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-13 00:18
     * @功能说明:评价标签
     */
    public function lableList()
    {

        $dis = [

            'uniacid' => $this->_uniacid,

            'status' => 1
        ];

        $lable_model = new Lable();

        $res = $lable_model->where($dis)->order('top desc,id desc')->select()->toArray();

        return $this->success($res);

    }

    /**
     * 列表
     * @return \think\Response
     */
    public function demandOrderList()
    {
        $data = $this->request->param();
        $page = isset($data['limit']) ? $data['limit'] : 10;
        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.is_pay', '=', 1],
            ['a.status', '<>', -1],
            ['a.status', '<>', -2],
            ['a.is_refund', 'in', [0, 3]]
        ];
        if (isset($data['status']) && in_array($data['status'], [0, 1])) {
            switch ($data['status']) {
                case 0:
                    $where[] = ['a.status', '=', 1];
                    break;
                case 1:
                    $where[] = ['a.status', '<>', 1];
                    break;
            }
        }
        if (!empty($data['start_time'])) {
            $where[] = ['a.create_time', '>=', $data['start_time']];
        }
        if (!empty($data['end_time'])) {
            $where[] = ['a.create_time', '<=', $data['end_time']];
        }
        $data = DemandOrder::getList($where, $page);
        return $this->success($data);
    }

    /**
     * 邀约详情
     * @return \think\Response
     */
    public function demandOrderInfo()
    {
        $id = $this->request->param('id', '');
        if (empty($id)) {
            return $this->error('参数错误');
        }
        $order = DemandOrder::getDetail(['a.id' => $id]);
        return $this->success($order);
    }

    /**
     * 审核邀约订单
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function demandExamine()
    {
        $data = $this->request->only(['id', 'status', 'check_text']);
        if (empty($data['id']) || empty($data['status']) || !in_array($data['status'], [1, 2])) {
            return $this->error('数据错误');
        }
        $order = DemandOrder::getInfo(['id' => $data['id']]);
        if (empty($order) || $order['is_pay'] != 1 || $order['status'] != 1 || in_array($order['is_refund'], [1, 2])) {
            return $this->error('此订单不可审核');
        }
        if ($order['pay_type'] == 1) {
            $pay_config = $this->payAliConfig();
        } elseif ($order['pay_type'] == 2) {
            $pay_config = $this->payConfig($order['app_pay']);
        } elseif ($order['pay_type'] == 3) {
            $pay_config = [];
        }
        $code = DemandOrder::demandExamine($data, $pay_config);
        if ($code['code'] == 1) {
            return $this->error($code['msg']);
        }
        return $this->success('');
    }


    /**
     * 邀约订单列表
     * @return \think\Response
     */
    public function demandOder()
    {
        $input = $this->request->param();
        $limit = $this->request->param('limit', 10);
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
        $data = DemandOrder::getList2($where, $limit);
        return $this->success($data);
    }

    /**
     * 获取详情
     * @return \think\Response
     */
    public function demandInfo()
    {
        $id = $this->request->param('id', '');
        if (empty($id)) {
            return $this->error('参数错误');
        }
        $data = DemandOrder::getDetail(['a.id' => $id, 'a.uniacid' => $this->_uniacid]);
        return $this->success($data);
    }

    /**
     * 邀约退款订单
     * @return \think\Response
     */
    public function demandRefundOrder()
    {
        $input = $this->request->param();
        $limit = $this->request->param('limit', 10);
        $where = [
            ['a.is_refund', '>', 0],
            ['a.is_pay', '=', 1]
        ];
        $where[] = ['a.uniacid', '=', $this->_uniacid];
        if (!empty($input['status'])) {
            $where[] = ['a.is_refund', '=', $input['status']];
        }
        if (!empty($input['order_code'])) {
            $where[] = ['a.order_code', 'like', '%' . $input['order_code'] . '%'];
        }
        $data = DemandOrder::getList2($where, $limit);
        return $this->success($data);
    }

    /**
     * 邀约订单退款操作
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function demandRefund()
    {
        $id = $this->request->param('id', '');
        $status = $this->request->param('status', '');
        if (empty($id) || empty($status)) {
            return $this->error('参数错误');
        }
        $order = DemandOrder::getInfo(['id' => $id, 'uniacid' => $this->_uniacid, 'is_pay' => 1]);
        if (empty($order)) {
            return $this->error('订单不存在');
        }
        if (!in_array($order['status'], [1, 2, 3]) || $order['is_refund'] != 1) {
            return $this->error('此订单不可处理');
        }

        if ($order['pay_type'] == 1) {
            $pay_config = $this->payAliConfig();
        } elseif ($order['pay_type'] == 2) {
            $pay_config = $this->payConfig($order['app_pay']);
        } elseif ($order['pay_type'] == 3) {
            $pay_config = [];
        }
        $code = DemandOrder::handleDemandOrder(['id' => $id, 'status' => $status], $pay_config);
        if ($code['code'] == 1) {
            return $this->error($code['msg']);
        }
        return $this->success('');
    }

    /**
     * @Desc: 添加评价
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/12/5 16:44
     */
    public function addCommentV2()
    {
        $input = $this->_input;

        $insert = [

            'uniacid' => $this->_uniacid,

            'star' => $input['star'],

            'text' => $input['text'],

            'coach_id' => $input['coach_id'],

            'attitude_star' => $input['attitude_star'],

            'speed_star' => $input['speed_star'],

            'is_admin' => 1,

            'order_id' => 0
        ];


        $comment_model = new Comment();

        $res = $comment_model->dataAdd($insert);

        if ($res == 0) {

            $this->errorMsg('评价失败');
        }

        $comment_model->updateStarV2($input['coach_id']);

        return $this->success($res);
    }

    /**
     * @Desc: 发送消息
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/4/1 16:14
     */
    public function sendMsg()
    {
        $id = request()->param('id', '');

        $order = DemandOrder::find($id);

        if (empty($order)) {

            return $this->error('订单不存在');
        }

        $order_type = DemandType::where('id', $order['ser_id'])->value('name');

        $send['order_code'] = $order['order_code'];
        $send['uniacid'] = $order['uniacid'];
        $send['time'] = date('Y年m月d日 H:i', $order['create_time']);
        $send['order_type'] = $order_type;
        $send['money'] = $order['price'];

        $res = Coach::robSendMsg($send);

        return $this->success($res);
    }
}
