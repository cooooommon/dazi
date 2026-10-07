<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/22
 * Time: 17:03
 * docs:
 */

namespace app\store\controller;

use app\AdminRest;
use app\seckill\info\PermissionSeckill;
use app\seckill\model\PackageSeckill;
use app\store\model\StorePackage;
use think\App;

class AdminPackage extends AdminRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 插入套餐
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/22 18:13
     */
    public function add()
    {
        $data = request()->only(['store_id', 'name', 'sub_name', 'cover', 'price', 'init_price', 'sale', 'imgs', 'introduce', 'term_type', 'term_start_time', 'term_end_time', 'days', 'use_type', 'use_trade_week', 'use_start_time', 'use_end_time', 'reservation_day', 'rule_text', 'ensure', 'sku', 'introduce_text', 'is_integral', 'integral', 'integral_to_money']);

        $data['uniacid'] = $this->_uniacid;

        $data['is_admin'] = 1;

        $data['imgs'] = !empty($data['imgs']) ? implode(',', $data['imgs']) : '';

        $data['introduce_text'] = !empty($data['introduce_text']) ? json_encode($data['introduce_text']) : '';

        $data['use_trade_week'] = !empty($data['use_trade_week']) ? implode(',', $data['use_trade_week']) : '';

        $data['total_sale'] = $data['sale'];

        $res = StorePackage::add($data);

        if (isset($res['code'])) {
            return $this->error($res['msg']);
        }
        return $this->success('');
    }

    /**
     * @Desc: 编辑
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/22 18:44
     */
    public function edit()
    {
        $data = request()->only(['id', 'store_id', 'name', 'sub_name', 'cover', 'price', 'init_price', 'sale', 'imgs', 'introduce', 'term_type', 'term_start_time', 'term_end_time', 'days', 'use_type', 'use_trade_week', 'use_start_time', 'use_end_time', 'reservation_day', 'rule_text', 'ensure', 'sku', 'introduce_text', 'is_integral', 'integral', 'integral_to_money']);

        $data['imgs'] = !empty($data['imgs']) ? implode(',', $data['imgs']) : '';

        $data['introduce_text'] = !empty($data['introduce_text']) ? json_encode($data['introduce_text']) : '';

        $data['use_trade_week'] = !empty($data['use_trade_week']) ? implode(',', $data['use_trade_week']) : '';

        $true_sale = StorePackage::where('id', $data['id'])->value('true_sale');

        $data['total_sale'] = $data['sale'] + $true_sale;

        $data['uniacid'] = $this->_uniacid;

        $res = StorePackage::edit($data);

        if (isset($res['code'])) {

            return $this->error($res['msg']);
        }
        return $this->success('');
    }

    /**
     * @Desc: 详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/23 10:24
     */
    public function getInfo()
    {
        $id = request()->param('id', '');

        $data = StorePackage::getInfo($id);

        return $this->success($data);
    }

    /**
     * @Desc: 套餐列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/23 10:52
     */
    public function getList()
    {
        StorePackage::cancel($this->_uniacid);

        $input = $this->_param;

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.status', '>', -1]
        ];

        if (!empty($input['store_id'])) {

            $where[] = ['a.store_id', '=', $input['store_id']];
        }

        if (!empty($input['status'])) {

            $where[] = ['a.status', '=', $input['status']];
        }

        if (!empty($input['name'])) {

            $where[] = ['a.name', 'like', '%' . $input['name'] . '%'];
        }

        $data = StorePackage::getList($where, $input['limit'] ?? 10);

        return $this->success($data);
    }

    /**
     * @Desc: 修改状态
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/23 11:29
     */
    public function updateStatus()
    {
        $data = request()->only(['id', 'status']);

        if ($data['status'] == 1) {

            $bool = StorePackage::checkStatus($data['id']);

            if (!$bool) {

                return $this->error('套餐已过期，不可上架');
            }
        } elseif (in_array($data['status'], [-1, 0])) {

            $seckill = PackageSeckill::where(['package_id' => $data['id'], 'status' => 1])->where('end_time', '>', time())->find();

            //渠道商下级
            $p = new PermissionSeckill((int)$this->_uniacid);

            $auth = $p->pAuth();

            if ($seckill && $auth) {

                return $this->error('该套餐正在参与秒杀活动，不可下架或删除');
            }
        }

        $res = StorePackage::update(['status' => $data['status']], ['id' => $data['id']]);

        return $this->success($res);
    }


    /**
     * @Desc: 套餐列表不分页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/9/19/019 22:10
     */
    public function getListByStoreId()
    {
        StorePackage::cancel($this->_uniacid);

        $input = $this->_param;

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1]
        ];

        if (!empty($input['store_id'])) {

            $where[] = ['store_id', '=', $input['store_id']];
        }

        if (!empty($input['name'])) {

            $where[] = ['name', 'like', '%' . $input['name'] . '%'];
        }

        $data = StorePackage::getListNoPage($where);

        return $this->success($data);
    }
}