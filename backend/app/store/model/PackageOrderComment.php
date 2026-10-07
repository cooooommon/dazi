<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/28
 * Time: 11:30
 * docs:
 */

namespace app\store\model;

use app\BaseModel;
use app\massage\model\StoreApply;
use think\facade\Db;

class PackageOrderComment extends BaseModel
{
    protected $name = 'massage_store_package_order_comment_list';

    /**
     * @Desc: 评价
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/28 11:47
     */
    public static function comment($data)
    {
        $order = PackageOrder::where('id', $data['order_id'])->find();

        $data['store_id'] = $order['store_id'];

        $data['create_time'] = $data['update_time'] = time();

        $data['img'] = empty($data['img']) ? '' : implode(',', $data['img']);

        Db::startTrans();

        $res = self::insert($data);

        if ($res == 0) {

            Db::rollback();

            return false;

        }

        $res = PackageOrder::where('id', $data['order_id'])->update(['is_comment' => 1]);

        if ($res === false) {

            Db::rollback();

            return false;

        }
        $star = self::where('store_id', $order['store_id'])->sum('star');

        $count = self::where('store_id', $order['store_id'])->count();

        $new_str = round($star / $count, 1);

        $res = StoreApply::where('id', $order['store_id'])->update(['star' => $new_str]);

        if ($res === false) {

            Db::rollback();

            return false;

        }

        Db::commit();

        return true;
    }

    /**
     * @Desc: 新增
     * @param $data
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/12/5 13:47
     */
    public static function add($data)
    {
        $data['create_time'] = $data['update_time'] = time();

        return self::insert($data);

    }

    /**
     * @Desc: 评论列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/4 19:56
     */
    public static function getAdminList($where, $limit)
    {
        return self::alias('a')
            ->field('a.id,b.name,b.cover,b.reservation_day,b.ensure,b.price,b.num,a.user_id,d.nickName,c.name as store_name,a.star,a.text,a.img,a.create_time,b.order_code,a.order_id')
            ->where($where)
            ->leftJoin('massage_store_package_order_list b', 'a.order_id=b.id')
            ->leftJoin('massage_store_apply c', 'a.store_id=c.id')
            ->leftJoin('massage_service_user_list d', 'b.user_id=d.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();

    }

    /**
     * @Desc: 列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/5 10:59
     */
    public static function getList($where, $limit)
    {
        $data = self::alias('a')
            ->field('a.id,a.text,a.img,a.create_time,a.star,a.is_hide,a.user_id,b.nickName,b.avatarUrl')
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id=b.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$item) {

                if (empty($item['user_id']) || $item['is_hide'] == 1) {

                    $item['nickName'] = '匿名用户';

                    $item['avatarUrl'] = 'https://lbqny.migugu.com/admin/farm/default-user.png';

//                    $item['img'] = empty($item['img']) ? '' : explode(',', $item['img']);
//
//                    $item['img_count'] = 0;
//
//                    if (is_array($item['img'])) {
//                        $item['img_count'] = count($item['img']);
//                    }

                }
            }
        }

        return $data;
    }

    /**
     * @Desc: 更新门店星级
     * @param $store_id
     * @return StoreApply
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/12/5 14:18
     */
    public static function updateStar($store_id)
    {
        $where = [
            ['store_id', '=', $store_id],
            ['is_admin', '=', 0],
            ['status', '>', -1]
        ];
        $star = self::where($where)->sum('star');

        $count = self::where($where)->count();

        $new_star = $count > 0 ? (round($star / $count, 1) > 5 ? 5 : round($star / $count, 1)) : 5;

        return StoreApply::update(['star' => $new_star], ['id' => $store_id]);
    }

    /**
     * @Desc: 数量
     * @param $where
     * @return int
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/12/11 11:15
     */
    public static function getCount($where)
    {
        return self::where($where)->count();
    }

    /**
     * @Desc: 单条
     * @param $where
     * @return PackageOrderComment|array|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/11 14:35
     */
    public static function getFirst($where)
    {
        return self::where($where)->find();
    }

    /**
     * @Desc:
     * @param $where
     * @Auther: shurong
     * @Time: 2023/12/21 19:22
     */
    public static function getInfo($where)
    {
        $item = self::alias('a')
            ->field('a.id,a.text,a.img,a.create_time,a.star,a.is_hide,a.user_id,b.nickName,b.avatarUrl')
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id=b.id')
            ->order('a.create_time desc')
            ->find();

        if (empty($item['user_id']) || $item['is_hide'] == 1) {

            $item['nickName'] = '匿名用户';

            $item['avatarUrl'] = 'https://lbqny.migugu.com/admin/farm/default-user.png';

//            $item['img'] = empty($item['img']) ? '' : explode(',', $item['img']);
//
//            $item['img_count'] = 0;
//
//            if (is_array($item['img'])) {
//                $item['img_count'] = count($item['img']);
//            }

        }

        return $item;
    }
}