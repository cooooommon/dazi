<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/10/27
 * Time: 13:58
 * docs:
 */

namespace app\massage\model;

use app\BaseModel;

class ChannelStaff extends BaseModel
{
    protected $name = 'massage_channel_staff_list';

    /**
     * @Desc: 插入
     * @param $data
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/10/27 14:00
     */
    public static function add($data)
    {
        $data['create_time'] = $data['update_time'] = time();
        return self::insertGetId($data);
    }

    /**
     * @Desc: 二维码详情
     * @param $where
     * @Auther: shurong
     * @Time: 2023/10/27 14:42
     */
    public static function getInfo($where)
    {
        $data = self::alias('a')
            ->field('a.id,b.user_name,c.avatarUrl,a.user_id,c.nickName,a.status')
            ->where($where)
            ->leftJoin('massage_channel_list b', 'a.channel_id=b.id')
            ->leftJoin('massage_service_user_list c', 'a.channel_user_id=c.id')
            ->find();
        if ($data) {
            $count = mb_strlen($data['user_name']);
            if ($count > 2) {
                $data['user_name'] = mb_substr($data['user_name'], 0, 2);
                $co = $count - 2;
                for ($i = 0; $i < $co; $i++) {
                    $data['user_name'] .= '*';
                }
            }
        }
        return $data;
    }

    /**
     * @Desc: 员工详情
     * @param $where
     * @Auther: shurong
     * @Time: 2023/10/27 14:42
     */
    public static function staffInfo($where)
    {
        return self::alias('a')
            ->field('a.id,a.name,a.balance,a.user_id,a.qr_path,b.user_name,c.avatarUrl,c.nickName')
            ->where($where)
            ->leftJoin('massage_channel_list b', 'a.channel_id=b.id')
            ->leftJoin('massage_service_user_list c', 'a.user_id=c.id')
            ->find();
    }

    /**
     * @Desc: 获取单条数据
     * @param $where
     * @return ChannelStaff|array|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/10/30 10:41
     */
    public static function getFirst($where)
    {
        return self::where($where)->find();
    }

    /**
     * @Desc: 渠道商员工列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/30 10:50
     */
    public static function staffList($where, $dis = [], $where_ = [], $limit = 5)
    {
        $data = self::alias('a')
            ->field('a.id,a.name,a.balance,b.nickName,b.avatarUrl')
            ->where($where)
            ->where(function ($query) use ($dis) {
                $query->whereOr($dis);
            })
            ->leftJoin('massage_service_user_list b', 'a.user_id=b.id')
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($limit)
            ->toArray();

        if ($data['data']) {
            $arr = self::alias('a')
                ->field('a.id,ifnull(sum(c.true_service_price),0)as price')
                ->where($where_)
                ->leftJoin('massage_service_user_list b', 'a.user_id=b.id')
                ->leftJoin('massage_service_order_list c', 'a.id=c.channel_staff_id and c.pay_type > 1')
                ->group('a.id')
                ->order('a.id desc')
                ->select()
                ->toArray();

            $arr1 = self::alias('a')
                ->field('a.id,ifnull(sum(c.channel_cash*c.channel_staff_balance/100),0)as wait_cash')
                ->where($where_)
                ->leftJoin('massage_service_user_list b', 'a.user_id=b.id')
                ->leftJoin('massage_service_order_list c', 'a.id=c.channel_staff_id and c.pay_type > 1 and c.pay_type < 7')
                ->group('a.id')
                ->order('a.id desc')
                ->select()
                ->toArray();

            $arr2 = self::alias('a')
                ->field('a.id,ifnull(sum(c.channel_cash*c.channel_staff_balance/100),0)as total_cash')
                ->where($where_)
                ->leftJoin('massage_service_user_list b', 'a.user_id=b.id')
                ->leftJoin('massage_service_order_list c', 'a.id=c.channel_staff_id and c.pay_type = 7')
                ->group('a.id')
                ->order('a.id desc')
                ->select()
                ->toArray();

            foreach ($data['data'] as &$datum) {

                foreach ($arr as $item) {

                    if ($datum['id'] == $item['id']) {

                        $datum['price'] = round($item['price'], 2);
                    }
                }

                foreach ($arr1 as $item) {

                    if ($datum['id'] == $item['id']) {

                        $datum['wait_cash'] = round($item['wait_cash'], 2);
                    }
                }
                foreach ($arr2 as $item) {

                    if ($datum['id'] == $item['id']) {

                        $datum['total_cash'] = round($item['total_cash'], 2);
                    }
                }

                $datum['price'] = $datum['price'] ?? 0;

                $datum['total_cash'] = $datum['total_cash'] ?? 0;

                $datum['wait_cash'] = $datum['wait_cash'] ?? 0;
            }
        }

        return $data;
    }

    /**
     * @Desc: 员工渠道流水
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/30 14:26
     */
    public static function staffComList($where, $limit = 10)
    {
        $data = Order::alias('a')
            ->field('a.id,b.nickName,a.true_service_price as price,a.create_time,c.status')
            ->where($where)
            ->leftJoin('massage_service_user_list b', 'a.user_id = b.id')
            ->leftJoin('massage_service_order_commission c', 'a.id=c.order_id and c.type=10')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();
        if ($data['data']) {
            foreach ($data['data'] as &$item) {
                $count = mb_strlen($item['nickName']);
                if ($count > 2) {
                    $item['nickName'] = mb_substr($item['nickName'], 0, 2);
                    $co = $count - 2;
                    for ($i = 0; $i < $co; $i++) {
                        $item['nickName'] .= '*';
                    }
                }
                $item['create_time'] = handleTime($item['create_time']);
            }
        }
        return $data;
    }

    /**
     * @Desc: 更换上级
     * @param $data
     * @return ChannelStaff|true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/10/31 16:04
     */
    public static function changeChannel($data)
    {
        $channel = (new ChannelList)->dataInfo(['id' => $data['channel_id']]);

        $staff = self::where('id', $data['channel_staff_id'])->find();

        if ($data['channel_id'] == $staff['channel_id']) {
            return true;
        }

        $login_type = User::where('id', $staff['user_id'])->value('last_login_type');

        $insert = [
            'uniacid' => $staff['uniacid'],
            'status' => $staff['status'],
            'name' => $staff['name'],
            'balance' => $staff['balance'],
            'channel_id' => $data['channel_id'],
            'channel_user_id' => $channel['user_id'],
            'user_id' => $staff['user_id'],
            'create_time' => time(),
            'update_time' => time()
        ];

        $id = self::insertGetId($insert);

        if ($login_type == 0) {

            $input['page'] = 'pages/service';

            $input['channel_staff_id'] = $id;

            $user_model = new User();

            $qr = $user_model->orderQr($input, $staff['uniacid']);

        } else {
            $page = 'https://' . $_SERVER['HTTP_HOST'] . '/h5/#/pages/service?channel_staff_id=' . $id;

            $qr = base64ToPng(getCode($staff['uniacid'], $page));
        }

        self::where('id', $data['channel_staff_id'])->update(['status' => -1]);

        return self::update(['qr_path' => $qr], ['id' => $id]);
    }
}