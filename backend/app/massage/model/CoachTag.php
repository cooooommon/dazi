<?php


namespace app\massage\model;


use app\BaseModel;

class CoachTag extends BaseModel
{
    protected $name = 'massage_service_coach_tag';

    public static function getList($where, $limit)
    {
        return self::where($where)->paginate($limit)->toArray();
    }

    public static function del($id)
    {
        $res = self::where('id', $id)->update(['status' => -1]);
        Coach::update(['tag_id' => 0], ['tag_id' => $id]);
        return $res;
    }
}