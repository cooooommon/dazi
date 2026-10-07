<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2024/9/22/022
 * Time: 21:45
 * docs:
 */

namespace app\seckill\controller;

use app\ApiRest;
use app\massage\model\StoreApply;
use app\seckill\model\PackageSeckill;
use app\store\model\StorePackage;
use app\store\model\UserPackageCollect;
use think\App;
use think\facade\Db;

class Index extends ApiRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc: 秒杀列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/9/22/022 22:10
     */
    public function seckillList()
    {
        $input = request()->param();

        $where = [

            ['a.uniacid', '=', $this->_uniacid],

            ['a.status', '=', 1],

            ['a.end_time', '>=', time()],

            ['a.start_time', '<=', time()],

            ['b.status', '=', 1]
        ];

        if (!empty($input['name'])) {

            $where[] = ['b.name', 'like', '%' . $input['name'] . '%'];
        }


        if (!empty($input['type_id'])) {

            $dis[] = ['status', '=', 1];

            $dis[] = ['', 'exp', Db::raw("find_in_set({$input['type_id']},type_id)")];

            $store_ids = StoreApply::where($dis)->column('id');

            $where[] = ['c.id', 'in', $store_ids];
        }

        $lat = !empty($input['lat']) ? $input['lat'] : 0;

        $lng = !empty($input['lng']) ? $input['lng'] : 0;

        $alh = 'ACOS(SIN((' . $lat . ' * 3.1415) / 180 ) *SIN((c.lat * 3.1415) / 180 ) +COS((' . $lat . ' * 3.1415) / 180 ) * COS((c.lat * 3.1415) / 180 ) *COS((' . $lng . ' * 3.1415) / 180 - (c.lng * 3.1415) / 180 ) ) * 6378.137 as distance';

        $order = 'a.is_ad desc,a.create_time desc';

        if (!empty($input['order'])) {

            if ($input['order'] == 1) {
                $order = 'a.is_ad desc,a.create_time desc';
            } elseif ($input['order'] == 2) {

                $order = 'a.is_ad desc,a.use_stock desc';
            } else {

                $order = 'a.is_ad desc,distance asc';
            }
        }

        $data = PackageSeckill::getIndexList($where, $alh, $order, $input['limit'] ?? 10);

        if ($data['data']) {

            foreach ($data['data'] as &$item) {

                $item['distance'] = !empty($item['distance']) ? round($item['distance'], 1) : 0;

                $item['stock_rate'] = (round($item['use_stock'] / $item['stock'] * 100, 2)) . '%';

                $item['price'] = $item['price'] + 0;
            }
        }

        return $this->success($data);
    }

    /**
     * @Desc: 详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/9/23 16:15
     */
    public function seckillInfo()
    {
        $id = request()->param('id', '');

        $data = PackageSeckill::getIndexInfo($id);

        if (empty($data)) {

            $this->errorMsg('套餐不存在');
        }

        $data['discount'] = 0;

        if ($data['price'] < $data['init_price']) {

            $data['discount'] = round(($data['seckill_price'] / $data['init_price']) * 10, 1);
        }

        $data['is_collect'] = UserPackageCollect::checkCollect($this->getUserId(), $data['id']);

        $data['sale'] = $data['total_sale'] > 10 ? (int)($data['total_sale'] / 10) * 10 : $data['total_sale'];

        return $this->success($data);
    }
}