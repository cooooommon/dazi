<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/28
 * Time: 10:22
 * docs:
 */

namespace app\store\model;

use app\BaseModel;

class PackageOrderRefundGoods extends BaseModel
{
    protected $name = 'massage_store_package_order_refund_goods_list';

    /**
     * @Desc: 列表
     * @param $refund_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/30 14:56
     */
    public static function getList($refund_id)
    {
        return self::where(['refund_id' => $refund_id])
            ->field('code_num,status')
            ->order('code_num asc')
            ->select()
            ->toArray();
    }
}