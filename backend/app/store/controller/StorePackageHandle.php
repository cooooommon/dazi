<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/28
 * Time: 18:07
 * docs:
 */

namespace app\store\controller;

use app\ApiRest;
use app\massage\model\StoreApply;
use app\seckill\info\PermissionSeckill;
use app\seckill\model\PackageSeckill;
use app\store\model\StorePackage;
use think\App;

class StorePackageHandle extends ApiRest
{
    protected $store;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $data = StoreApply::getInfo([['user_id', '=', $this->getUserId()], ['status', '>', -1]]);

        if (!in_array($data['status'], [2, 3])) {

            $this->errorMsg('你还不是商家');
        }

        $this->store = $data;
    }

    /**
     * @Desc: 插入套餐
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/22 18:13
     */
    public function add()
    {
        $data = request()->only(['name', 'sub_name', 'cover', 'price', 'init_price', 'sale', 'imgs', 'introduce', 'term_type', 'term_start_time', 'term_end_time', 'days', 'use_type', 'use_trade_week', 'use_start_time', 'use_end_time', 'reservation_day', 'rule_text', 'ensure', 'sku', 'status', 'introduce_text', 'is_integral', 'integral', 'integral_to_money']);

        $data['uniacid'] = $this->_uniacid;

        $data['is_admin'] = 0;

        $data['imgs'] = !empty($data['imgs']) ? implode(',', $data['imgs']) : '';

        $data['introduce_text'] = !empty($data['introduce_text']) ? json_encode($data['introduce_text']) : '';

        $data['use_trade_week'] = !empty($data['use_trade_week']) ? implode(',', $data['use_trade_week']) : '';

        $data['total_sale'] = $data['sale'];

        $data['store_id'] = $this->store['id'];

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
        $data = request()->only(['id', 'name', 'sub_name', 'cover', 'price', 'init_price', 'sale', 'imgs', 'introduce', 'term_type', 'term_start_time', 'term_end_time', 'days', 'use_type', 'use_trade_week', 'use_start_time', 'use_end_time', 'reservation_day', 'rule_text', 'ensure', 'sku', 'status', 'introduce_text', 'is_integral', 'integral', 'integral_to_money']);

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
            ['a.status', '>', -1],
            ['a.store_id', '=', $this->store['id']]
        ];

        if (!empty($input['name'])) {

            $where[] = ['a.name', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['status'])) {
            $where[] = ['a.status', '=', $input['status'] == 1 ? 1 : 0];
        }


        $data = StorePackage::getList($where, $input['limit'] ?? 10);

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1],
            ['store_id', '=', $this->store['id']]
        ];

        $data['status_1'] = StorePackage::where($where)->count();

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 0],
            ['store_id', '=', $this->store['id']]
        ];

        $data['status_2'] = StorePackage::where($where)->count();

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
        }

        if ($data['status'] == -1 || $data['status'] == 0) {

            $seckill = PackageSeckill::where(['package_id' => $data['id'], 'status' => 1])->order('end_time desc')->find();

            $model = new PermissionSeckill((int)$this->_uniacid);

            $auth = $model->pAuth();

            if (!empty($seckill) && $seckill['end_time'] > time() && $auth) {

                return $this->error('套餐正在参与秒杀，不可' . ($data['status'] == -1 ? '删除' : '下架'));
            }
        }

        $res = StorePackage::update(['status' => $data['status']], ['id' => $data['id']]);

        return $this->success($res);
    }

    /**
     * @Desc: 新增秒杀
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/9/26 17:24
     */
    public function seckillAdd()
    {
        $data = request()->only(['package_id', 'stock', 'price', 'start_time', 'end_time', 'limit']);

        $info = StorePackage::find($data['package_id']);

        if ($info['status'] != 1) {

            return $this->error('套餐' . ($info['status'] == 0 ? '已下架' : '已删除') . ',不能添加秒杀');
        }

        $seckill = PackageSeckill::where([['package_id', '=', $data['package_id']], ['status', '=', 1], ['end_time', '>', time()]])->find();

        if (!empty($seckill)) {

            return $this->error('此套餐已设为秒杀，不可重复设置');
        }

        $data['uniacid'] = $this->_uniacid;

        $data['store_id'] = $this->store['id'];

        $res = PackageSeckill::add($data);

        return $this->success($res);
    }

    /**
     * @Desc: 列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/9/20/020 21:45
     */
    public function getSeckillList()
    {
        $input = request()->param();

        $where = [

            ['a.uniacid', '=', $this->_uniacid],

            ['a.status', '=', 1],

            ['b.store_id', '=', $this->store['id']]
        ];

        if (!empty($input['name'])) {

            $where[] = ['b.name', 'like', '%' . $input['name'] . '%'];
        }

        $data = PackageSeckill::getList($where, $input['limit'] ?? 10);

        if ($data['data']) {

            foreach ($data['data'] as &$item) {

                $item['stock_rate'] = (round($item['use_stock'] / $item['stock'] * 100, 2)) . '%';

                $item['price'] = $item['price'] + 0;
            }
        }

        return $this->success($data);
    }

    /**
     * @Desc: 秒杀详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/9/20/020 22:05
     */
    public function seckillEdit()
    {
        $data = request()->only(['id', 'status', 'stock', 'use_stock', 'price', 'start_time', 'end_time', 'limit', 'is_ad']);

        if (empty($data['id'])) {

            return $this->error('参数错误');
        }

        $info = PackageSeckill::find($data['id']);

        $info = StorePackage::find($info['package_id']);

        if ($info['status'] != 1 && isset($data['status']) && $data['status'] != -1) {

            return $this->error('套餐' . ($info['status'] == 0 ? '已下架' : '已删除') . ',不能设为秒杀');
        }

        if (request()->isPost()) {

            $res = PackageSeckill::edit(['id' => $data['id']], $data);

            return $this->success($res);
        }

        $info = PackageSeckill::getInfo(['a.id' => $data['id']]);

        return $this->success($info);
    }
}