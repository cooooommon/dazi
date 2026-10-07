<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/11/14
 * Time: 16:26
 * docs:
 */

namespace app\integral\model;

use app\BaseModel;
use app\massage\model\DemandOrder;
use app\massage\model\Order;
use app\massage\model\SendMsgConfig;
use app\massage\model\User;
use app\store\model\PackageOrder;
use longbingcore\wxcore\WxSetting;
use think\facade\Db;

class UserIntegral extends BaseModel
{
    //type 1预约订单下单 2邀约订单下单 3套餐订单抵扣 4套餐订单退款

    protected $name = 'massage_member_user_integral';

    protected $append = [
        'order_info'
    ];

    protected static $type_text = [
        1 => '预约订单消费',
        2 => '邀约订单消费',
        3 => '套餐订单抵扣',
        4 => '套餐订单退款',
    ];

    public function getOrderInfoAttr($value, $data)
    {
        if (isset($data['order_id']) && $data['order_id']) {

            $order_id = $data['order_id'];

            $type = $data['type'];

            $order_info = [];

            if ($type == 1) {

                $order_info = Order::where(['id' => $order_id])->field('id,order_code,pay_price')->find();

            } elseif ($type == 2) {

                $order_info = DemandOrder::where(['id' => $order_id])->field('id,order_code,pay_price')->find();
            } elseif ($type == 3) {

                $order_info = PackageOrder::where(['id' => $order_id])->field('id,order_code,pay_price')->find();
            }

            return $order_info;
        }
    }

    /**
     * @Desc: 用户积分变动
     * @param $order
     * @param $type
     * @param $is_add
     * @return true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/18 15:26
     */
    public static function handleIntegral($order, $type = 1, $is_add = 1)
    {

        $user = User::where(['id' => $order['user_id']])->find();

        $data = [
            'uniacid' => $order['uniacid'],
            'order_id' => $order['order_id'],
            'user_id' => $order['user_id'],
            'before' => $user['integral'],
            'change' => $order['integral'],
            'after' => $is_add == 1 ? $user['integral'] + $order['integral'] : $user['integral'] - $order['integral'],
            'type' => $type,
            'create_time' => time(),
            'update_time' => time(),
            'add' => $is_add
        ];

        self::insert($data);

        $integral = $order['integral'];

        if ($is_add == 1) {

            $update = ['integral' => Db::raw("integral+$integral")];

            if ($type != 4) {

                $update['total_integral'] = Db::raw("total_integral+$integral");
            }

            User::where(['id' => $order['user_id']])->update($update);
        } else {

            User::where(['id' => $order['user_id']])->update(['integral' => Db::raw("integral-$integral")]);
        }

        $from = self::$type_text[$type];

        //积分通知
        $type = $user['last_login_type'] == 0 && !empty($user['wechat_openid']) ? 1 : 2;

        $data = [
            'from' => $from,
            'change' => $is_add == 1 ? $integral : '-' . $integral,
            'time' => date('Y年m月d日 H:i', time())
        ];

        if ($type == 1) {

            self::integralSendMsgWeChat($order['uniacid'], $user['wechat_openid'], $data);
        } else {

            self::integralSendMsgWeb($order['uniacid'], $user['web_openid'], $data);
        }
        return true;
    }

    /**
     * @Desc: 积分公众号模板消息
     * @param $unaicid
     * @param $openid
     * @param $data
     * @return false|mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/18 15:21
     */
    public static function integralSendMsgWeChat($unaicid, $openid, $data)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($unaicid);

        $x_config = $config_model->dataInfo(['uniacid' => $unaicid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['integral_tmpl_id'])) {

            return false;
        }

        $config = longbingGetAppConfig($unaicid);

        $access_token = longbingGetAccessToken($unaicid);

        //post地址
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token={$access_token}";

        $arr = [
            //用户小程序openid
            'touser' => $openid,

            'mp_template_msg' => [
                //公众号appid
                'appid' => $x_config['gzh_appid'],

                "url" => "http://weixin.qq.com/download",
                //公众号模版id
                'template_id' => $x_config['integral_tmpl_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => '/pages/mine',
                ],

                'data' => array(
                    //收益来源（购买会员卡、发布邀约单、预约服务下单）
                    'thing2' => array(

                        'value' => $data['from'],

                        'color' => '#0000ff',
                    ),
                    //积分
                    'character_string3' => array(
                        //内容
                        'value' => $data['change'],

                        'color' => '#0000ff',
                    ),
                    //时间 2023年08月01日 20:30
                    'time4' => array(
                        //内容
                        'value' => $data['time'],

                        'color' => '#0000ff',
                    )
                )

            ]

        ];

        $arr = json_encode($arr);

        $tmp = [

            'url' => $url,

            'data' => $arr,
        ];
        $rest = lbCurlPost($tmp['url'], $tmp['data']);

        $rest = json_decode($rest, true);

        return $rest;
    }

    /**
     * @Desc: 积分公众号模板消息
     * @param $uniacid
     * @param $openid
     * @param $data
     * @return false|mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/18 15:20
     */
    public static function integralSendMsgWeb($uniacid, $openid, $data)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['integral_tmpl_id'])) {

            return false;
        }

        $key = explode('&', $x_config['integral_tmpl_id']);

        $arr = [
            1 => 'thing2',
            2 => 'character_string3',
            3 => 'time4',
        ];
        for ($i = 1; $i < 3; $i++) {
            if (!empty($key[$i])) {
                $arr[$i] = $key[$i];
            }
        }

        $arr = [
            //用户小程序openid
            'touser' => $openid,
            //公众号appid
            'appid' => $x_config['gzh_appid'],

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/pages/mine',
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                //订单号
                $arr[1] => array(

                    'value' => $data['from'],

                    'color' => '#0000ff',
                ),
                //金额
                $arr[2] => array(
                    //内容
                    'value' => $data['change'],

                    'color' => '#0000ff',
                ),
                //时间
                $arr[3] => array(
                    //内容
                    'value' => $data['time'],

                    'color' => '#0000ff',
                ),
            )

        ];

        $wx_setting = new WxSetting($uniacid);

        $access_token = $wx_setting->getGzhToken();

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

        $arr = json_encode($arr);

        $tmp = [

            'url' => $url,

            'data' => $arr,
        ];
        $rest = lbCurlPost($tmp['url'], $tmp['data']);

        $rest = json_decode($rest, true);

        return $rest;
    }

    /**
     * @Desc: 列表
     * @param $where
     * @param $limit
     * @return array
     * @throws \think\db\exception\DbException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/18 11:16
     */
    public static function getList($where, $limit = 10)
    {

        return self::where($where)
            ->field('id,before,change,after,type,create_time,add,order_id')
            ->order('create_time desc')
            ->paginate($limit)
            ->toArray();
    }

    public static function getUserListV2($user_id, $limit = 10)
    {
        $order = Db::name('massage_member_order_integral')->where([['user_id', '=', $user_id], ['status', '>', -1]])
            ->field(['id', 'order_id', 'user_id', 'create_time', 'status', 'if(id<0,0,1) as in_type'])
            ->buildSql();

        $user = Db::name('massage_member_user_integral')->where([['type', 'not in', [1, 2]], ['user_id', '=', $user_id]])
            ->field(['id', 'order_id', 'user_id', 'create_time', 'if(type=3||type=4,2,1) as status', 'if(id<0,0,2) as in_type'])
            ->buildSql();

        $data = Db::name('massage_member_order_integral')->where('id', '<', 0)
            ->field(['id', 'order_id', 'user_id', 'create_time', 'status', 'if(id<0,0,1) as in_type'])
            ->unionAll([$order, $user])
            ->buildSql();

        $arr = Db::table($data . ' a')->order('create_time desc,id desc')->paginate($limit)->toArray();

        if ($arr['data']) {

            foreach ($arr['data'] as &$item) {

                if ($item['in_type'] == 1) {

                    $integral = OrderIntegral::where('id', $item['id'])->field('integral,order_type as type,order_id')->find();

                    $item['change'] = $integral['integral'];

                    $item['type'] = $integral['type'];

                    $item['add'] = 1;

                    $after = UserIntegral::where(['order_id' => $integral['order_id'], 'type' => $integral['type']])->value('after');

                    $item['after'] = $after ?? '';

                    $item['order_id'] = $integral['order_id'];
                } else {

                    $integral = UserIntegral::where('id', $item['id'])->field('change,add,type,after,order_id')->find();

                    $item['change'] = $integral['change'];

                    $item['add'] = $integral['add'];

                    $item['type'] = $integral['type'];

                    $item['after'] = $integral['after'];

                    $item['order_id'] = $integral['order_id'];
                }

                $type = $item['type'];

                $order_id = $item['order_id'];

                if ($type == 1) {

                    $order_info = Order::where(['id' => $order_id])->field('id,order_code,pay_price')->find();
                } elseif ($type == 2) {

                    $order_info = DemandOrder::where(['id' => $order_id])->field('id,order_code,pay_price')->find();
                } else {

                    $order_info = PackageOrder::where(['id' => $order_id])->field('id,order_code,pay_price')->find();
                }

                if ($order_info) {

                    $order_info['pay_price'] = round($order_info['pay_price'], 2);
                }

                $item['order_info'] = $order_info;
            }
        }

        return $arr;
    }
}