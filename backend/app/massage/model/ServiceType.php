<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2024/6/19
 * Time: 11:59
 * docs:
 */

namespace app\massage\model;

use app\BaseModel;

class ServiceType extends BaseModel
{
    protected $name = 'massage_service_service_type_list';

    /**
     * @Desc: 插入
     * @param $data
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/11/20 11:27
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
     * @return array
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/20 11:54
     */
    public static function getList($where, $limit)
    {
        return self::where($where)
            ->order('top desc,id desc')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: 列表  无分页
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/20 15:30
     */
    public static function getListNoPage($where)
    {
        return self::where($where)->field('id,name')->order('top desc,id desc')->select()->toArray();
    }

    /**
     * @Desc: 详情
     * @param $where
     * @return ServiceType|array|mixed|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/6/19 15:02
     */
    public static function getInfo($where)
    {
        $data = self::where($where)->field('id,name')->find();

        if ($data) {

            $data['banner'] = Banner::where(['service_type' => $data['id'], 'status' => 1])->order('top desc,id desc')->select()->toArray();
        }

        $data['banner'] = empty($data['banner']) ? '' : $data['banner'];

        return $data;
    }
}