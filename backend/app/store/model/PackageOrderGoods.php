<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/24
 * Time: 11:45
 * docs:
 */

namespace app\store\model;

use app\BaseModel;

class PackageOrderGoods extends BaseModel
{
    protected $name = 'massage_store_package_order_goods_list';

    /**
     * @Desc: 订单券码列表
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/27 14:31
     */
    public static function getList($where)
    {
        return self::where($where)->field('id,code_num,status,is_integral,integral,integral_to_money,goods_price')->select()->toArray();
    }
}