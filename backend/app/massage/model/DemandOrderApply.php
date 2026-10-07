<?php


namespace app\massage\model;


use app\BaseModel;

class DemandOrderApply extends BaseModel
{
    protected $name = 'massage_service_demand_order_apply';

    public static function num($where)
    {
        return self::where($where)->count();
    }

    public static function add($data)
    {
        $data['create_time'] = time();
        return self::insert($data);
    }

    /**
     * 获取对应的技师报名
     * @param $where
     * @return mixed
     */
    public static function seeApply($where, $order_id, $limit)
    {
        self::where('order_id', $order_id)->update(['is_look' => 1]);
        return self::alias('a')
            ->field('a.coach_id,a.status,b.coach_name,b.work_img,b.constellation,b.height,b.weight,b.nickname')
            ->where($where)
            ->join('massage_service_coach_list b', 'a.coach_id=b.id', 'left')
            ->order('a.status asc')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * 数量
     * @param $where
     * @return int
     * @throws \think\db\exception\DbException
     */
    public static function getCount($where)
    {
        return self::where($where)->count();
    }

    /**
     * 删除
     * @param $where
     * @return bool
     */
    public static function del($where)
    {
        return self::where($where)->delete();
    }
}