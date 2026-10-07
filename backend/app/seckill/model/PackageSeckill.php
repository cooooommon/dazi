<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2024/9/19/019
 * Time: 22:37
 * docs:
 */

namespace app\seckill\model;

use app\BaseModel;
use app\massage\model\Config;
use app\massage\model\StoreApply;
use app\store\model\PackageSku;
use app\store\model\SkuPrice;
use app\store\model\StorePackage;

class PackageSeckill extends BaseModel
{
    protected $name = ' massage_store_package_seckill_list';

    /**
     * @Desc: 新增
     * @param $data
     * @return int|string
     * @Auther: shurong
     * @Time: 2024/9/19/019 22:58
     */
    public static function add($data)
    {
        $data['create_time'] = $data['update_time'] = time();

        return self::insert($data);
    }

    /**
     * @Desc: 列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/9/20/020 21:45
     */
    public static function getList($where, $limit = 10)
    {
        return self::alias('a')
            ->where($where)
            ->field('a.id,b.name,b.name as title,b.cover,a.price,b.price as package_price,c.name as store_name,a.stock,a.use_stock,a.start_time,a.end_time,a.is_ad,a.create_time')
            ->leftJoin('massage_store_package_list b', 'a.package_id = b.id')
            ->leftJoin('massage_store_apply c', 'a.store_id = c.id')
            ->order('a.is_ad desc,a.id desc')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: diy使用
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/8 15:57
     */
    public static function getListV2($where, $limit = 10)
    {
        return self::alias('a')
            ->where($where)
            ->field('b.id,b.name as title,c.name as store_name,a.stock,a.use_stock')
            ->leftJoin('massage_store_package_list b', 'a.package_id = b.id')
            ->leftJoin('massage_store_apply c', 'b.store_id = c.id')
            ->order('a.is_ad desc,a.id desc')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: 编辑
     * @param $where
     * @param $update
     * @return PackageSeckill
     * @Auther: shurong
     * @Time: 2024/9/20/020 22:03
     */
    public static function edit($where, $update)
    {

        return self::where($where)->update($update);
    }

    /**
     * @Desc: 详情
     * @param $where
     * @return PackageSeckill|array|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/9/20/020 22:04
     */
    public static function getInfo($where)
    {

        return self::alias('a')
            ->field('a.*,b.name,b.cover,b.price as package_price,c.name as store_name,b.true_sale')
            ->where($where)
            ->leftJoin('massage_store_package_list b', 'a.package_id = b.id')
            ->leftJoin('massage_store_apply c', 'a.store_id = c.id')
            ->find();
    }

    /**
     * @Desc: 秒杀列表
     * @param $where
     * @param $alh
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/9/22/022 22:08
     */
    public static function getIndexList($where, $alh, $order, $limit = 10)
    {
        return self::alias('a')
            ->where($where)
            ->field(['a.id,b.name,b.cover,a.price,b.price as package_price,c.name as store_name,a.stock,a.use_stock,a.start_time,a.end_time,a.is_ad,a.create_time,a.store_id,b.id as package_id', $alh])
            ->leftJoin('massage_store_package_list b', 'a.package_id = b.id')
            ->leftJoin('massage_store_apply c', 'a.store_id = c.id')
            ->order($order)
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: 秒杀详情
     * @param $id
     * @return StorePackage|array|mixed|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/9/23 16:11
     */
    public static function getIndexInfo($id)
    {
        $seckill = self::where('id', $id)->field('id,package_id,price,start_time,end_time,is_ad,create_time,stock,use_stock,limit')->find();

        if (empty($seckill)) {

            return [];
        }

        $data = StorePackage::where('id', $seckill['package_id'])->find();

        $sku = PackageSku::getList(['package_id' => $seckill['package_id']]);

        $price = SkuPrice::getList(['package_id' => $seckill['package_id']]);

        $data['store'] = StoreApply::where('id', $data['store_id'])->field('id,name,trade_week,start_time,end_time,store_balance,share_balance,mobile')->find();

        $arr = [];

        foreach ($sku as $key => $item) {

            $arr[$key] = [

                'name' => $item['name']
            ];

            foreach ($price as $value) {

                if ($item['id'] == $value['sku_id']) {

                    $arr[$key]['price'][] = [
                        'name' => $value['name'],
                        'num' => $value['num'],
                        'price' => $value['price']
                    ];
                }
            }
        }

        $data['sku'] = $arr;

        $data['stock_rate'] = (round($seckill['use_stock'] / $seckill['stock'] * 100, 2)) . '%';

        $data['end_time'] = $seckill['end_time'];

        $data['seckill_price'] = $seckill['price'];

        $data['seckill_id'] = $seckill['id'];

        $data['use_stock'] = $seckill['use_stock'];

        $data['stock'] = $seckill['stock'];

        $data['limit'] = $seckill['limit'];

        $data['seckill_start_time'] = $seckill['start_time'];

        $data['seckill_end_time'] = $seckill['end_time'];

        return $data;
    }

    /**
     * @Desc:验证秒杀状态和库存
     * @param $package_id
     * @return StorePackage|array|mixed|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/9/25 18:59
     */
    public static function checkStatusAndStock($package_id)
    {
        $seckill = PackageSeckill::where([['package_id', '=', $package_id], ['status', '=', 1], ['start_time', '<=', time()], ['end_time', '>', time()]])->find();

        if (empty($seckill)) {

            return ['code' => 1, 'msg' => '该秒杀已下架'];
        }

        if ($seckill['use_stock'] >= $seckill['stock']) {

            return ['code' => 1, 'msg' => '该秒杀已售罄'];
        }

        $data = PackageSeckill::getIndexInfo($seckill['id']);

        if (empty($data)) {

            return ['code' => 1, 'msg' => '该秒杀已下架'];
        }

        if (!empty($data) && $data['start_time'] > time()) {

            return ['code' => 1, 'msg' => '该秒杀未开始'];
        }

        if (!empty($data) && $data['end_time'] < time()) {

            return ['code' => 1, 'msg' => '该秒杀已结束'];
        }

        return $data->toArray();
    }

    /**
     * @Desc: 获取下单信息
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/9/25 19:06
     */
    public static function getPayInfo($data)
    {

        $over_time = Config::where('uniacid', $data['uniacid'])->value('over_time');

        if ($data['term_type'] == 1) {

            $data['start_time'] = $data['term_start_time'];

            $data['end_time'] = $data['term_end_time'];
        } else {
            $data['start_time'] = time();

            $data['end_time'] = strtotime('+ ' . $data['days'] . 'days');
        }

        $data['over_time'] = time() + $over_time * 60;

        return $data;
    }

    /**
     * @Desc: 修改销量
     * @param $id
     * @param $num
     * @param $type 1使用 2恢复
     * @return true
     * @Auther: shurong(贝润网络)
     * @Time: 2024/9/26 11:24
     */
    public static function updateSale($id, $num, $type = 1)
    {
        if ($type == 1) {

            self::where('id', $id)->inc('use_stock', $num)->update();
        } else {

            self::where('id', $id)->dec('use_stock', $num)->update();
        }
        return true;
    }
}