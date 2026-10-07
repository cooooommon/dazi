<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/11/11
 * Time: 11:21
 * docs:
 */

namespace app\member\controller;

use app\AdminRest;
use app\member\model\MemberCard;
use app\member\model\MemberConfig;
use app\member\model\MemberOrder;
use think\App;

class Admin extends AdminRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 会员卡配置
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/11 11:36
     */
    public function configSet()
    {
        $input = request()->only(['status', 'discount', 'balance', 'text', 'integra']);

        if (request()->isPost()) {

            $res = MemberConfig::edit(['uniacid' => $this->_uniacid], $input);

            return $this->success($res);
        }

        $data = MemberConfig::getInfo(['uniacid' => $this->_uniacid]);

        return $this->success($data);
    }

    /**
     * @Desc: 会员卡列表
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 10:19
     */
    public function cardList()
    {

        $input = request()->param();

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', '>', -1];

        $data = MemberCard::getList($dis, $input['limit'] ?? 10);

        return $this->success($data);
    }

    /**
     * @Desc: 会员卡添加
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 10:18
     */
    public function cardAdd()
    {

        $input = request()->param();

        $insert = [

            'uniacid' => $this->_uniacid,

            'day' => $input['day'],

            'title' => $input['title'],

            'price' => $input['price'],

            'init_price' => $input['init_price'],

            'top' => $input['top'],

            'icon' => $input['icon'],

            'text' => $input['text']
        ];

        $res = MemberCard::add($insert);

        return $this->success($res);
    }

    /**
     * @Desc: 会员卡编辑
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 10:19
     */
    public function cardUpdate()
    {

        $input = request()->only(['id', 'day', 'title', 'price', 'init_price', 'top', 'icon', 'text', 'status']);

        $dis = [

            'id' => $input['id']
        ];

        $res = MemberCard::edit($dis, $input);

        return $this->success($res);
    }

    /**
     * @Desc: 会员卡详情
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 10:19
     */
    public function cardInfo()
    {

        $input = request()->param();

        $dis = [

            'id' => $input['id']
        ];

        $data = MemberCard::getInfo($dis);

        return $this->success($data);
    }

    /**
     * @Desc: 订单列表
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/12 18:37
     */
    public function orderList()
    {
        $input = request()->param();

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.status', '=', 2]
        ];

        if (!empty($input['order_code'])) {

            $where[] = ['a.order_code', 'like', '%' . $input['order_code'] . '%'];
        }

        if (!empty($input['start_time'])) {

            $where[] = ['a.create_time', '>', $input['start_time']];
        }

        if (!empty($input['end_time'])) {

            $where[] = ['a.create_time', '<', $input['end_time']];
        }

        $list = MemberOrder::getList($where, $input['limit'] ?? 10);

        return $this->success($list);
    }
}