<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/11/11
 * Time: 19:07
 * docs:
 */

namespace app\member\model;

use app\BaseModel;

class MemberCard extends BaseModel
{
    protected $name = 'massage_member_card_list';

    public static function getList($where, $limit = 10)
    {
        $data = self::where($where)
            ->order('top desc,id desc')
            ->paginate($limit)
            ->toArray();

        if ($data['data']) {

            foreach ($data['data'] as &$item) {

                $item['price'] = $item['price'] + 0;

                $item['init_price'] = $item['init_price'] + 0;
            }
        }

        return $data;
    }

    public static function add($insert)
    {
        $insert['create_time'] = $insert['update_time'] = time();

        return self::insert($insert);
    }

    public static function edit($dis, $insert)
    {

        return self::where($dis)->update($insert);
    }

    public static function getInfo($dis)
    {

        $data = self::where($dis)->find();

        if (!empty($data)) {

            $data['price'] = $data['price'] + 0;

            $data['init_price'] = $data['init_price'] + 0;
        }

        return $data;
    }

    public static function getListNoPage($where)
    {
        $data = self::where($where)
            ->field('id,title,price,init_price,text,icon')
            ->order('top desc,id desc')
            ->select()
            ->toArray();
        if ($data) {

            foreach ($data as &$item) {

                $item['price'] = $item['price'] + 0;

                $item['init_price'] = $item['init_price'] + 0;
            }
        }

        return $data;
    }
}