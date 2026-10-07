<?php


namespace app\massage\model;


use app\BaseModel;

class JoinForm extends BaseModel
{
    protected $name = 'massage_join_form';

    /**
     * 添加
     * @param $data
     * @return int|string
     */
    public static function add($data)
    {
        $data['create_time'] = time();
        return self::insert($data);
    }

    /**
     * 电话号码加密
     * @param $value
     * @param $data
     * @return string|string[]
     */
    public function getMobileAttr($value, $data)
    {

        if (!empty($value) && isset($data['uniacid'])) {

            if (numberEncryption($data['uniacid']) == 1) {

                return substr_replace($value, "****", 2, 4);
            }

        }

        return $value;

    }

    /**
     * 列表
     * @param $where
     * @param $limit
     * @return array
     * @throws \think\db\exception\DbException
     */
    public static function getList($where, $limit)
    {
        return self::field('id,uniacid,name,mobile,city,status,create_time')->order('id desc')->where($where)->paginate($limit)->toArray();
    }
}