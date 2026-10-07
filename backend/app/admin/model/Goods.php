<?php

namespace app\admin\model;

use app\BaseModel;
use think\facade\Db;

class Goods extends BaseModel
{
    //定义表名
    protected $name = 'longbing_card_goods';


    protected static function init()
    {
        //TODO:初始化内容
    }

    /**
     * 获取商品列表
     */
    public function goodList($dis, $page = 10)
    {
        return self::where($dis)->paginate($page)->toArray();
    }

    /**
     * 修改商品
     */

    public function goodsUpdate($dis, $data)
    {
        $data['update_time'] = time();
        return self::where($dis)->update($data);
    }

    /**
     *
     * 添加商品
     */

    public function goodsAdd($data)
    {
        $data['create_time'] = time();
        return self::insert($data);
    }

    /**
     * 获取商品详情
     */

    public function goodsInfo($dis)
    {
        $data = self::where($dis)->find();
        return !empty($data) ? $data->toArray() : $data;
    }

    /**
     * 查询所有分类
     */
    public function goodsSelect($dis)
    {
        $data = self::where($dis)->select();
        return !empty($data) ? $data->toArray() : $data;
    }


}