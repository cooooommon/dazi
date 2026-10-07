<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/23
 * Time: 17:03
 * docs:
 */

namespace app\store\model;

use app\BaseModel;

class UserPackageCollect extends BaseModel
{
    protected $name = 'massage_user_store_package_collect';

    /**
     * @Desc: 是否收藏
     * @param $user_id
     * @param $id
     * @return int
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/23 17:07
     */
    public static function checkCollect($user_id, $id)
    {
        $count = self::where(['user_id' => $user_id, 'package_id' => $id])->count();

        if ($count > 0) {
            return 1;
        }
        return 0;
    }

    /**
     * @Desc: 收藏
     * @param $user_id
     * @param $id
     * @return bool|int|string
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/27 9:55
     */
    public static function collect($where)
    {
        $count = self::where($where)->count();

        if ($count == 0) {

            $where = [
                'create_time' => time()
            ];
            self::insert($where);
        }
        return true;
    }

    /**
     * @Desc: 取消收藏
     * @param $where
     * @return bool
     * @Auther: shurong
     * @Time: 2023/11/27 10:29
     */
    public static function cancelCollect($where)
    {
        return self::where($where)->delete();
    }

    /**
     * @Desc: 收藏列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/27 10:20
     */
    public static function getList($where, $limit)
    {
        return self::alias('a')
            ->where($where)
            ->field('b.id,b.name,b.total_sale,b.cover,b.ensure,b.price,c.name as store_name,c.cover as store_cover')
            ->leftJoin('massage_store_package_list b', 'a.package_id=b.id')
            ->leftJoin('massage_store_apply c', 'b.store_id=c.id')
            ->order('')
            ->paginate($limit)
            ->toArray();
    }
}