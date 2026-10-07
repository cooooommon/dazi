<?php


namespace app\massage\model;


use app\BaseModel;

class StoreApplyUpdate extends BaseModel
{
    protected $name = 'massage_store_apply_update';

    /**
     * 编辑
     * @param $data
     * @return int|string
     */
    public static function edit($data)
    {
        $data['create_time'] = $data['update_time'] = time();
        StoreApply::where('id', $data['store_id'])->update(['is_update' => 1]);
        return self::insert($data);
    }

    /**
     * 详情
     * @param $where
     * @return StoreApplyUpdate|array|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getInfo($where)
    {
        return self::where($where)->find();
    }
}