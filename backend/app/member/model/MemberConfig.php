<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/11/11
 * Time: 11:29
 * docs:
 */

namespace app\member\model;

use app\BaseModel;
use app\member\info\PermissionMember;

class MemberConfig extends BaseModel
{
    protected $name = 'massage_member_config';

    public static function edit($where, $update)
    {
        return self::where($where)->update($update);
    }

    public static function getInfo($where)
    {
        $data = self::where($where)->find();

        if (empty($data)) {

            self::insert($where);

            $data = self::where($where)->find();
        }

        return $data;
    }

    public static function getStatus($uniacid)
    {
        $p = new PermissionMember((int)$uniacid);

        $auth = $p->pAuth();

        if (!$auth) {

            return 0;
        }
        $data = self::getInfo(['uniacid' => $uniacid]);

        return $data['status'];
    }
}