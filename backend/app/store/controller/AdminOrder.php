<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/4
 * Time: 16:31
 * docs:
 */

namespace app\store\controller;

use app\AdminRest;
use app\store\model\PackageOrder;
use app\store\model\PackageOrderComment;
use app\store\model\PackageOrderRefund;
use think\App;

class AdminOrder extends AdminRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 订单列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/4 16:55
     */
    public function getList()
    {
        $input = $this->_param;

        $pay_config = [
            1 => $this->payConfig(),
            2 => [],
            3 => $this->payAliConfig()
        ];
        PackageOrder::chancel($this->_uniacid, $pay_config);

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

            $where[] = ['d.nickName', 'like', '%' . $input['nickName'] . '%'];
        }

        if (!empty($input['status'])) {

            if (in_array($input['status'], [4, 5])) {

                $where[] = ['a.status', '=', 3];

                $where[] = ['a.is_comment', '=', $input['status'] == 4 ? 0 : 1];
            }
        }

        $data = PackageOrder::getAdminList($where, $input['limit'] ?? 10);

        $count_data = PackageOrder::getCountData($where);

        $data = array_merge($data, $count_data);

        return $this->success($data);
    }

    /**
     * @Desc: 订单详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/4 17:30
     */
    public function getInfo()
    {
        $order_id = request()->param('order_id', '');

        if (empty($order_id)) {

            $this->errorMsg('请选择订单');
        }

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.id', '=', $order_id]
        ];

        $data = PackageOrder::getInfo($where, 2);

        return $this->success($data);

    }

    /**
     * @Desc: 退款订单列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/4 18:16
     */
    public function getRefundList()
    {
        $input = $this->_param;

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.status', '>', -1]
        ];

        $dis = [];

        if (!empty($input['name'])) {

            $where[] = ['b.name', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['store_id'])) {

            $where[] = ['a.store_id', '=', $input['store_id']];
        }

        if (!empty($input['status'])) {

            $where[] = ['a.status', '=', $input['status']];
        }

        if (!empty($input['refund_code'])) {

            $dis[] = ['a.refund_code', 'like', '%' . $input['refund_code'] . '%'];
            $dis[] = ['b.order_code', 'like', '%' . $input['refund_code'] . '%'];
        }

        $data = PackageOrderRefund::getAdminList($where, $dis, $input['limit'] ?? 10);

        return $this->success($data);
    }

    /**
     * @Desc: 退款详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/4 18:33
     */
    public function refundInfo()
    {
        $id = request()->param('id', '');

        $data = PackageOrderRefund::getInfo($id, 2);

        return $this->success($data);
    }

    /**
     * @Desc: 退款操作
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/9 14:16
     */
    public function refundCheck()
    {
        $data = request()->only(['id', 'status', 'text']);

        $order_id = PackageOrderRefund::where('id', $data['id'])->value('order_id');

        $order = PackageOrder::find($order_id);

        $pay_config = [
            1 => $this->payConfig($order['app_pay']),
            2 => [],
            3 => $this->payAliConfig()
        ];

        $code = PackageOrderRefund::refundCheck($data, $pay_config);

        if ($code['code'] == 1) {

            $this->errorMsg($code['msg']);
        }

        return $this->success('');
    }

    /**
     * @Desc: 评论列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/4 19:57
     */
    public function commentList()
    {
        $input = $this->_param;

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.status', '=', 1]
        ];

        if (!empty($input['star'])) {

            $where[] = ['a.star', '=', $input['star']];
        }

        if (!empty($input['store_name'])) {

            $where[] = ['c.name', 'like', '%' . $input['store_name'] . '%'];
        }

        if (!empty($input['package_name'])) {

            $where[] = ['b.name', 'like', '%' . $input['package_name'] . '%'];
        }

        $data = PackageOrderComment::getAdminList($where, $input['limit'] ?? 10);

        return $this->success($data);
    }

    /**
     * @Desc: 删除评论
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/4 20:20
     */
    public function delComment()
    {
        $id = request()->param('id', '');

        $comment = PackageOrderComment::find($id);

        $res = PackageOrderComment::update(['status' => -1], ['id' => $id]);

        if ($comment['is_admin'] != 1) {

            PackageOrderComment::updateStar($comment['store_id']);
        }

        return $this->success($res);
    }

    /**
     * @Desc: 添加评论/不计入评分系统
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/4 20:25
     */
    public function addComment()
    {
        $data = request()->only(['star', 'text', 'store_id']);

        $data['uniacid'] = $this->_uniacid;

        $data['is_admin'] = 1;

        $res = PackageOrderComment::add($data);

        return $this->success($res);
    }
}