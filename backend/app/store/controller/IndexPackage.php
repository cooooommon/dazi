<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/23
 * Time: 14:18
 * docs:
 */

namespace app\store\controller;

use app\ApiRest;
use app\integral\info\PermissionIntegral;
use app\massage\model\User;
use app\seckill\info\PermissionSeckill;
use app\seckill\model\PackageSeckill;
use app\store\model\StorePackage;
use app\store\model\UserPackageCollect;
use think\App;

class IndexPackage extends ApiRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 店铺套餐列表
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/23 15:08
     */
    public function storePackList()
    {
        StorePackage::cancel($this->_uniacid);

        $input = request()->param();

        $where = [
            ['store_id', '=', $input['store_id']],
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1]
        ];

        $model = new PermissionSeckill((int)$this->_uniacid);

        $auth = $model->pAuth();

        $data = StorePackage::getIndexList($where, $auth, $input['limit'] ?? 10);

        $where[] = ['ensure', '=', 1];

        $count = StorePackage::where($where)->count();

        $data['is_ensure'] = $count > 0 ? 1 : 0;

        return $this->success($data);
    }

    /**
     * @Desc: 套餐详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/23 16:50
     */
    public function storePackInfo()
    {
        $id = request()->param('id', '');

        $seckill = PackageSeckill::where([['package_id', '=', $id], ['status', '=', 1], ['end_time', '>', time()], ['start_time', '<=', time()]])->find();

        $is_seckill = request()->param('is_seckill', 0);

        $is_seckill = !empty($seckill) ? 1 : $is_seckill;

        $model = new PermissionSeckill((int)$this->_uniacid);

        $auth = $model->pAuth();

        if ($is_seckill == 1 && $auth) {

            $data = $this->getSeckillInfo($id);

            $data['is_seckill'] = $is_seckill;

            return $this->success($data);
        }

        $data = StorePackage::getInfo($id);

        if (empty($data)) {

            $this->errorMsg('套餐不存在');
        }

        $data['discount'] = 0;

        if ($data['price'] < $data['init_price']) {

            $data['discount'] = round(($data['price'] / $data['init_price']) * 10, 1);
        }

        $data['is_collect'] = UserPackageCollect::checkCollect($this->getUserId(), $id);

        $data['sale'] = $data['total_sale'] > 10 ? (int)($data['total_sale'] / 10) * 10 : $data['total_sale'];

        $data['is_seckill'] = $is_seckill;

        if ($this->getUserId()) {
            $integral = User::where('id', $this->getUserId())->value('integral');

            $data['user_integral'] = $integral;
        } else {
            $data['user_integral'] = 0;
        }

        if ($data['is_integral'] == 1) {

            $p = new PermissionIntegral((int)$this->_uniacid);

            $auth = $p->pAuth();

            if (!$auth) {

                $data['is_integral'] == 0;
            }
        }

        return $this->success($data);
    }

    protected function getSeckillInfo($id)
    {
        $data = PackageSeckill::where([['package_id', '=', $id], ['status', '=', 1], ['end_time', '>', time()], ['start_time', '<=', time()]])->find();

        if (empty($data)) {

            $this->errorMsg('该秒杀已下架');
        }

        if ($data['use_stock'] >= $data['stock']) {

            $this->errorMsg('该秒杀已售罄');
        }

        $data = PackageSeckill::getIndexInfo($data['id']);

        if (empty($data)) {

            $this->errorMsg('该秒杀已下架');
        }

        if (!empty($data) && $data['end_time'] < time()) {

            $this->errorMsg('该秒杀已结束');
        }

        $data['discount'] = 0;

        if ($data['seckill_price'] < $data['price']) {

            $data['discount'] = round(($data['seckill_price'] / $data['price']) * 10, 1);
        }

        $data['is_collect'] = UserPackageCollect::checkCollect($this->getUserId(), $id);

        $data['sale'] = $data['use_stock'] > 10 ? (int)($data['use_stock'] / 10) * 10 : $data['use_stock'];

        $data['init_price'] = $data['price'];

        $data['price'] = $data['seckill_price'] + 0;

        return $data;
    }

    /**
     * @Desc: 收藏
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/27 9:55
     */
    public function collect()
    {
        $id = request()->param('id', '');

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['user_id', '=', $this->getUserId()],
            ['package_id', '=', $id]
        ];

        $res = UserPackageCollect::collect($where);

        return $this->success($res);
    }

    /**
     * @Desc: 取消收藏
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/27 10:29
     */
    public function cancelCollect()
    {
        $ids = request()->param('ids', '');

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['user_id', '=', $this->getUserId()],
            ['package_id', 'in', $ids]
        ];

        $res = UserPackageCollect::cancelCollect($where);

        return $this->success($res);
    }

    /**
     * @Desc: 收藏列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/27 10:21
     */
    public function collectList()
    {
        $limit = request()->param('limit', 10);
        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.user_id', '=', $this->getUserId()],
            ['b.status', '=', 1],
        ];

        $data = UserPackageCollect::getList($where, $limit);

        return $this->success($data);
    }
}