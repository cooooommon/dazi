<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/9/19
 * Time: 14:47
 * docs:
 */

namespace app\seckill\controller;

use app\AdminRest;
use app\seckill\model\PackageSeckill;
use app\store\model\StorePackage;
use think\App;

class Admin extends AdminRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 新增
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/9/19/019 23:00
     */
    public function seckillAdd()
    {
        $data = request()->only(['store_id', 'package_id', 'stock', 'price', 'start_time', 'end_time', 'limit']);

        $info = StorePackage::find($data['package_id']);

        if ($info['status'] != 1) {

            return $this->error('套餐' . ($info['status'] == 0 ? '已下架' : '已删除') . ',不能添加秒杀');
        }

        $seckill = PackageSeckill::where([['package_id', '=', $data['package_id']], ['status', '=', 1], ['end_time', '>', time()]])->find();

        if (!empty($seckill)) {

            return $this->error('此套餐已设为秒杀，不可重复设置');
        }

        $where = [
            ['package_id', '=', $data['package_id']],
            ['status', '=', 1]
        ];

        $result = PackageSeckill::where($where)->whereRaw("NOT ({$data['end_time']} < start_time OR {$data['start_time']} > end_time)")
            ->count();

        if ($result > 0) {

            return $this->error('此套餐时间段内已有秒杀活动，请重新选择时间');
        }

        $data['uniacid'] = $this->_uniacid;

        $res = PackageSeckill::add($data);

        return $this->success($res);
    }

    /**
     * @Desc: 列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/9/20/020 21:45
     */
    public function getList()
    {
        $input = request()->param();

        $where = [

            ['a.uniacid', '=', $this->_uniacid],

            ['a.status', '=', 1]
        ];

        if (!empty($input['name'])) {

            $where[] = ['b.name', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['store_id'])) {

            $where[] = ['b.store_id', '=', $input['store_id']];
        }

        $data = PackageSeckill::getList($where, $input['limit'] ?? 10);

        return $this->success($data);
    }

    /**
     * @Desc: diy使用
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/8 15:55
     */
    public function getListV2()
    {
        $input = request()->param();

        $where = [

            ['a.uniacid', '=', $this->_uniacid],

            ['a.status', '=', 1],

            ['a.end_time', '>', time()]
        ];

        if (!empty($input['name'])) {

            $where[] = ['b.name', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['title'])) {

            $where[] = ['b.name', 'like', '%' . $input['title'] . '%'];
        }

        $data = PackageSeckill::getListV2($where, $input['limit'] ?? 10);

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
        $data = request()->param();

        if (empty($data['id'])) {

            return $this->error('参数错误');
        }

        if (request()->isPost()) {

            $info = PackageSeckill::find($data['id']);

            $package_id = $info['package_id'];

            $info = StorePackage::find($info['package_id']);

            if ($info['status'] != 1 && ((isset($data['status']) && $data['status'] != -1) || !isset($data['status']))) {

                return $this->error('套餐' . ($info['status'] == 0 ? '已下架' : '已删除') . ',不能设为秒杀');
            }

            if (isset($data['start_time']) && isset($data['end_time'])) {

                $where = [
                    ['package_id', '=', $package_id],
                    ['status', '=', 1],
                    ['id', '<>', $data['id']]
                ];

                $result = PackageSeckill::where($where)->whereRaw("NOT ({$data['end_time']} < start_time OR {$data['start_time']} > end_time)")
                    ->count();

                if ($result > 0) {

                    return $this->error('此套餐时间段内已有秒杀活动，请重新选择时间');
                }
            }

            $res = PackageSeckill::edit(['id' => $data['id']], $data);

            return $this->success($res);
        }

        $info = PackageSeckill::getInfo(['a.id' => $data['id']]);

        return $this->success($info);
    }
}