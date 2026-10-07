<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/6
 * Time: 18:18
 * docs:
 */

namespace app\broker\model;

use app\BaseModel;

class User extends BaseModel
{
    protected $name = 'massage_service_user_list';

    public function getPhoneAttr($value, $data)
    {

        if (!empty($value) && isset($data['uniacid'])) {

            if (numberEncryption($data['uniacid']) == 1) {

                return substr_replace($value, "****", 2, 4);
            }

        }

        return $value;

    }

    /**
     * @Desc: 用户列表
     * @param $dis
     * @param $page
     * @param $mapor
     * @return array
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/12/6 18:20
     */
    public static function getList($dis, $page, $mapor = [])
    {

        return self::where($dis)->where(function ($query) use ($mapor) {
            $query->whereOr($mapor);
        })->order('id desc')
            ->paginate($page)
            ->toArray();
    }
}