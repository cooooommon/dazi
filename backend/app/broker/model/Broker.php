<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/6
 * Time: 16:11
 * docs:
 */

namespace app\broker\model;

use app\BaseModel;
use app\massage\model\Coach;
use app\massage\model\Commission;
use app\massage\model\Order;
use app\massage\model\Wallet;

class Broker extends BaseModel
{
    protected $name = 'massage_broker_list';

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
     * @Desc:
     * @param $where
     * @return Broker|array|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/6 16:54
     */
    public static function getFirst($where)
    {
        $data = self::where($where)->find();

        if (!empty($data)) {

            $data['nickName'] = User::where('id', $data['user_id'])->value('nickName');
        }
        return $data;
    }

    /**
     * @Desc:
     * @param $insert
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/12/6 16:54
     */
    public static function add($insert)
    {
        $insert['create_time'] = $insert['update_time'] = time();
        return self::insert($insert);
    }

    /**
     * @Desc: 列表
     * @param $where
     * @param $dis
     * @param $limit
     * @Auther: shurong
     * @Time: 2023/12/6 16:54
     */
    public static function getList($where, $dis, $limit = 10)
    {
        return self::alias('a')
            ->field('a.*,b.nickName,b.avatarUrl')
            ->where($where)
            ->where(function ($query) use ($dis) {
                $query->whereOr($dis);
            })
            ->leftJoin('massage_service_user_list b', 'a.user_id=b.id')
            ->order('a.create_time desc')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: 获取单列
     * @param $where
     * @param $field
     * @return array
     * @Auther: shurong
     * @Time: 2023/12/6 18:15
     */
    public static function getColumn($where, $field)
    {
        return self::where($where)->column($field);
    }

    /**
     * @Desc: 经纪人分销比例
     * @param $order
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/7 13:55
     */
    public static function getBrokerBalance($order)
    {

        $config = getConfigSettingArr($order['uniacid'], ['broker_balance', 'broker_coach_balance', 'broker_agent_balance']);

        if (!empty($order['broker_id'])) {

            $broker = self::find($order['broker_id']);

            if ($broker['balance'] > 0) {

                $order['broker_balance'] = $broker['balance'];
            } else {

                $order['broker_balance'] = $config['broker_balance'];
            }
            $order['broker_coach_balance'] = $config['broker_coach_balance'];

            $order['broker_agent_balance'] = $config['broker_agent_balance'];

            $order['broker_admin_balance'] = 100 - $config['broker_agent_balance'] - $config['broker_coach_balance'];
        } else {
            $order['broker_balance'] = 0;

            $order['broker_coach_balance'] = 0;

            $order['broker_agent_balance'] = 0;

            $order['broker_admin_balance'] = 0;
        }

        return $order;
    }

    /**
     * @Desc: 获取经纪人比例
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/8 16:11
     */
    public static function getBalance($id)
    {
        $broker = self::find($id);

        $config = getConfigSetting($broker['uniacid'], 'broker_balance');
        if ($broker['balance'] > 0) {

            $broker_balance = $broker['balance'];
        } else {

            $broker_balance = $config;
        }
        return $broker_balance;
    }

    /**
     * @Desc: 经纪人数据
     * @param $where
     * @param $dis
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 11:04
     */
    public static function getShareData($where, $dis, $limit)
    {
        $data = self::alias('a')
            ->field('a.id,a.uniacid,a.user_id,b.avatarUrl,b.nickName,a.name,a.mobile,a.create_time,a.total_cash')
            ->where($where)
            ->where(function ($query) use ($dis) {
                $query->whereOr($dis);
            })
            ->leftJoin('massage_service_user_list b', 'a.user_id=b.id')
            ->order('a.create_time desc')
            ->group('a.id')
            ->paginate($limit)
            ->toArray();

        if ($data['data']) {

            $broker_id = array_column($data['data'], 'id');

            $coach = Coach::field('broker_id,count(*) as num')->where([['broker_id', 'in', $broker_id], ['status', '=', 2]])->group('broker_id')->select()->toArray();
            $order = Order::field('broker_id,count(*) as num')->where([['broker_id', 'in', $broker_id], ['pay_type', '>', 1]])->group('broker_id')->select()->toArray();

            $price = Order::field('broker_id,sum(broker_cash) as coach')->where([['broker_id', 'in', $broker_id], ['pay_type', '>', 1]])->group('broker_id')->select()->toArray();

            foreach ($data['data'] as &$item) {

                $item['coach_num'] = $item['order_num'] = $item['total_cash'] = 0;

                foreach ($coach as $co) {

                    if ($co['broker_id'] == $item['id']) {

                        $item['coach_num'] = $co['num'];
                    }
                }

                foreach ($order as $or) {
                    if ($or['broker_id'] == $item['id']) {

                        $item['order_num'] = $or['num'];
                    }
                }

                foreach ($price as $value) {
                    if ($value['broker_id'] == $item['id']) {

                        $item['total_cash'] = $value['coach'];
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @Desc: 佣金等信息
     * @param $id
     * @return array
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/12/8 16:03
     */
    public static function getBrokerData($id)
    {
        $wallet_model = new Wallet();

        //未入账
        $data['wait_cash'] = Commission::where(['top_id' => $id, 'type' => 16, 'status' => 1])->sum('cash');
        $data['wait_cash'] = round($data['wait_cash'], 2);
        $data['total_cash'] = Commission::where([['top_id', '=', $id], ['type', '=', 16], ['status', '>', -1]])->sum('cash');
        $data['total_cash'] = round($data['total_cash'], 2);

        //累计提现
        $data['extract_total_price'] = $wallet_model->capCash($id, 2, 7, 'total_price');
        $data['extract_total_price'] = round($data['extract_total_price'], 2);

        //订单金额
        $data['order_price'] = Order::where([['pay_type', '>', -1], ['broker_id', '=', $id]])->sum('true_service_price');
        $data['order_price'] = round($data['order_price'], 2);

        $data['today_coach_num'] = Coach::where(['broker_id' => $id, 'status' => 2])->where('create_time', '>', strtotime(date('Y-m-d')))->count();

        $data['coach_num'] = Coach::where(['broker_id' => $id, 'status' => 2])->count();

        $data['balance'] = self::getBalance($id);

        $data['balance'] = (float)$data['balance'];

        return $data;
    }

    /**
     * @Desc: 数量
     * @param $where
     * @return int
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/12/13 15:12
     */
    public static function getCount($where)
    {
        return self::where($where)->count();
    }
}