<?php


namespace app\massage\model;


use app\BaseModel;
use app\member\model\MemberConfig;
use app\member\model\MemberOrder;

class DemandType extends BaseModel
{
    protected $name = 'massage_service_demand_type';

    /**
     * 列表
     * @param $where
     * @param $page
     * @return array
     * @throws \think\db\exception\DbException
     */
    public static function getList($where, $page)
    {
        return self::where($where)
            ->order('top desc')
            ->paginate($page)
            ->each(function ($item) {
                $item['create_time'] = handleTime($item['create_time']);
                return $item;
            })
            ->toArray();
    }

    /**
     * 单条
     * @param $where
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getInfo($where)
    {
        return self::where($where)->find();
    }

    /**
     * @Desc: 获取服务价格
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/4/1 17:30
     */
    public static function getPrice($data, $user_id, $uniacid)
    {
        $id = array_column($data, 'ser_id');

        $ser = self::whereIn('id', $id)->select()->toArray();

        foreach ($data as &$datum) {

            foreach ($ser as $item) {

                if ($datum['ser_id'] == $item['id']) {

                    $datum['price'] = $item['price'];
                }
            }

            $datum = self::getSerPrice($user_id, $uniacid, $datum);

            $datum['total_price'] = $datum['price'] * $datum['num'];
        }

        return $data;
    }

    /**
     * @Desc: 会员折扣
     * @param $user_id
     * @param $uniacid
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/26 17:30
     */
    public static function getSerPrice($user_id, $uniacid, $data)
    {
        $data['member_discount'] = $data['member_status'] = $data['member_balance'] = 0;

        $status = MemberOrder::getStatus($user_id, $uniacid);

        if (!$status) {

            return $data;
        }

        $config = MemberConfig::getInfo(['uniacid' => $uniacid]);

        $member_price = round($data['price'] * $config['discount'] / 10, 2);

        $data['member_discount'] = $data['price'] - $member_price;

        $data['member_balance'] = $config['discount'];

        $data['member_status'] = 1;

        $data['price'] = $member_price;

        return $data;
    }
}