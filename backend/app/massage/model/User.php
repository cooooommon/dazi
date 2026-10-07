<?php

namespace app\massage\model;

use app\BaseModel;
use app\integral\model\OrderIntegral;
use app\member\model\MemberOrder;
use longbingcore\wxcore\WxSetting;
use think\facade\Db;

class User extends BaseModel
{
    //定义表名
    protected $name = 'massage_service_user_list';

    /**
     * 电话加密
     * @param $value
     * @param $data
     * @return string|string[]
     */
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
     * @author chenniang
     * @DataTime: 2021-08-29 21:18
     * @功能说明:
     */
    public function getBalanceAttr($value, $data)
    {

        if (isset($value)) {

            return round($value, 2);
        }

    }

    /**
     * @author chenniang
     * @DataTime: 2021-08-29 21:18
     * @功能说明:
     */
    public function getCashAttr($value, $data)
    {

        if (isset($value)) {

            return round($value, 2);
        }

    }

    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data)
    {

        $data['create_time'] = time();

        $data['status'] = 1;

        $res = $this->insert($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis, $data)
    {

        $res = $this->where($dis)->update($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis, $page, $mapor = [])
    {

        $data = $this->where($dis)->where(function ($query) use ($mapor) {
            $query->whereOr($mapor);
        })->order('id desc')->paginate($page)->toArray();

        return $data;

    }

    /**
     * @Desc: 获取导出数据
     * @param $dis
     * @param $mapor
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/4/18 10:33
     */
    public static function getExcelList($uniacid, $dis, $mapor = [])
    {
        $data = self::where($dis)
            ->field('id,nickName,phone,balance')
            ->where(function ($query) use ($mapor) {
                $query->whereOr($mapor);
            })->order('id desc')
            ->select()
            ->toArray();

        if ($data) {

            $ids = array_column($data, 'id');

            $from = UserFrom::alias('a')
                ->field('a.user_id,a.from_type,a.from_name,b.nickName,c.user_name,d.user_name as channel_name,e.name as staff_name,f.name as broker_name,g.agent_name as admin_name,h.coach_name,i.channel_id')
                ->whereIn('a.user_id', $ids)
                ->leftJoin('massage_service_user_list b', 'a.from_id=b.id and a.from_type=2')
                ->leftJoin('massage_distribution_list c', 'a.from_id=c.id and a.from_type in (3,10)')
                ->leftJoin('massage_channel_list d', 'a.from_id=d.id and a.from_type=4')
                ->leftJoin('massage_channel_staff_list e', 'a.from_id=e.id and a.from_type=5')
                ->leftJoin('massage_broker_list f', 'a.from_id=f.id and a.from_type=6')
                ->leftJoin('shequshop_school_admin g', 'a.from_id=g.id and a.from_type=7')
                ->leftJoin('massage_service_coach_list h', 'a.from_id=h.id and a.from_type=8')
                ->leftJoin('massage_channel_staff_list i', 'a.from_id=i.id and a.from_type=9')
                ->select()->toArray();

            if ($from) {
                $attendant_name = getConfigSetting($uniacid, 'attendant_name');

                foreach ($from as $item) {

                    switch ($item['from_type']) {
                        case 1:
                            $from_name = '公众号搜索';
                            break;
                        case 2:
                            $from_name = $item['nickName'] . '-分享链接';
                            break;
                        case 3:
                            $from_name = $item['user_name'] . '-分销码';
                            break;
                        case 4:
                            $from_name = $item['channel_name'] . '-渠道商邀请用户二维码';
                            break;
                        case 5:
                            $from_name = $item['staff_name'] . '-渠道员工码';
                            break;
                        case 6:
                            $from_name = $item['broker_name'] . '-经纪人邀请' . $attendant_name . '码';
                            break;
                        case 7:
                            $from_name = $item['admin_name'] . '-代理商邀请' . $attendant_name . '码';
                            break;
                        case 8:
                            $from_name = $item['coach_name'] . '-' . $attendant_name . '充值邀请码';
                            break;
                        case 9:
                            $from_name = ChannelList::where('id', $item['channel_id'])->value('user_name') . '-渠道商邀请员工二维码';
                            break;
                        case 10:
                            $from_name = $item['user_name'] . '-分销员邀请用户购买会员卡';
                            break;
                        case 11:
                            $from_name = '抖音-' . (empty($item['from_name']) ? '无' : $item['from_name']);
                            break;
                        case 12:
                            $from_name = '视频号-' . (empty($item['from_name']) ? '无' : $item['from_name']);
                            break;
                        case 13:
                            $from_name = '小红书-' . (empty($item['from_name']) ? '无' : $item['from_name']);
                            break;
                        case 14:
                            $from_name = '其他-' . (empty($item['from_name']) ? '无' : $item['from_name']);
                            break;
                        default:
                            $from_name = '';
                    }

                    foreach ($data as &$datum) {

                        if ($datum['id'] == $item['user_id']) {

                            $datum['from_name'] = $from_name;
                        }
                    }

                }
            }
        }

        return $data;
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis, $field = '*')
    {

        $data = $this->where($dis)->field($field)->find();

        return !empty($data) ? $data->toArray() : [];

    }

    /**
     * @author chenniang
     * @DataTime: 2020-10-27 15:42
     * @功能说明:订单自提码
     */
    public function orderQr($input, $uniacid)
    {

        $data = longbingCreateWxCode($uniacid, $input, $input['page']);

        $data = transImagesOne($data, ['qr_path'], $uniacid);

        $qr = $data['qr_path'];

        return $qr;
    }

    /**
     * @Desc: 获取分销商粉丝数和金额
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/10/26 16:32
     */
    public function getSubUserData($user_id)
    {
        $where = [
            'pid' => $user_id,
            'is_fx' => 0
        ];

        $info['user_count'] = $this->where($where)->count();
        $dis = [
            ['user_top_id', '=', $user_id],
            ['pay_type', '>', 1]
        ];

        $data = Order::where($dis)
            ->field('ifnull(SUM(true_service_price),0) as pay_price,ifnull(SUM(user_cash),0) as user_cash')
            ->group('id')
            ->order('pay_price desc,id desc')
            ->select()
            ->toArray();

        $info['price'] = array_sum(array_column($data, 'pay_price'));

        $info['cash'] = array_sum(array_column($data, 'user_cash'));
        return $info;
    }

    /**
     * @Desc: 获取分销商下级数量和金额
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/10/26 16:40
     */
    public function getSubDistributionData($user_id)
    {
        $where = [
            'pid' => $user_id,
            'is_fx' => 1
        ];
        $info['distribution_count'] = $this->where($where)->count();
        return $info;
    }

    /**
     * @Desc: 分销商下级列表
     * @param $dis
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/26 17:05
     */
    public function getSubDistributionList($dis)
    {
        return $this->alias('a')
            ->where($dis)
            ->join('massage_service_order_list b', 'a.id = b.user_top_id AND b.pay_type > 1', 'left')
            ->join('massage_distribution_list c', 'a.id=c.user_id and c.status > -1', 'left')
            ->join('massage_service_refund_order d', 'b.id=d.order_id and d.status = 2', 'left')
            ->field('a.id,c.user_name as nickName,a.avatarUrl,ifnull(SUM(b.true_service_price),0) as pay_price,ifnull(SUM(d.refund_price),0) as refund_price,ifnull(COUNT(b.id),0) as order_count,a.bind_time as create_time')
            ->group('a.id')
            ->order('pay_price desc,a.id desc')
            ->paginate(15)
            ->toArray();
    }

    /**
     * @Desc: 分销商粉丝列表
     * @param $dis
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/26 17:06
     */
    public function getSubUserList($dis)
    {
        $data = $this->alias('a')
            ->join('massage_service_order_list b', 'a.id = b.user_id AND b.pay_type > 1', 'left')
            ->where($dis)
            ->field('a.id,a.nickName,a.avatarUrl,ifnull(SUM(b.true_service_price),0) as pay_price,ifnull(COUNT(b.id),0) as order_count,a.bind_time as create_time')
            ->group('a.id')
            ->order('pay_price desc,a.id desc')
            ->paginate(15)
            ->toArray();

        if ($data['data']) {

            foreach ($data['data'] as &$item) {

                $cash = MemberOrder::where(['user_id' => $item['id'], 'status' => 2])->order('create_time asc')->value('pay_price');

                $item['pay_price'] += $cash;

                $pay_price = DemandOrder::where([['user_id', '=', $item['id']], ['status', '>', 1]])->sum('pay_price');

                $item['pay_price'] += $pay_price;
            }
        }

        return $data;
    }

    /**
     * @Desc:退款不通过通知
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/1 15:53
     */
    public static function refundNoPassSendMsg($data, $type = 1)
    {
        $user_model = new User();

        $user_info = $user_model->dataInfo(['id' => $data['user_id']]);

        if (empty($user_info)) {

            return false;
        }
        $page = $type == 1 ? 'user/pages/refund/list' : 'find/pages/invitation/list?userPageType=1';

        //type 1小程序 2公众号
        $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

        if ($type == 1) {

            $res = self::refundNoPassSendMsgWechat($data, $page);

        } else {

            $res = self::refundNoPassSendMsgWeb($data, $page);

        }

        return $res;
    }

    /**
     * @Desc: 退款不通过通知-小程序
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/1 15:50
     */
    public static function refundNoPassSendMsgWechat($data, $page)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['refund_nopass_tmpl_id'])) {

            return false;
        }

        $config = longbingGetAppConfig($data['uniacid']);

        $openid = User::where('id', $data['user_id'])->value('wechat_openid');

        $access_token = longbingGetAccessToken($data['uniacid']);

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
                'template_id' => $x_config['refund_nopass_tmpl_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],

                'data' => array(
                    //订单号
                    'character_string1' => array(

                        'value' => $data['order_code'],

                        'color' => '#0000ff',
                    ),
                    //金额
                    'amount3' => array(
                        //内容
                        'value' => $data['money'],

                        'color' => '#0000ff',
                    ),
                    //时间
                    'time5' => array(
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
     * @Desc:退款不通过通知-公众号
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/1 15:52
     */
    public static function refundNoPassSendMsgWeb($data, $page)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['refund_nopass_tmpl_id'])) {

            return false;
        }
        $openid = User::where('id', $data['user_id'])->value('web_openid');

        $key = explode('&', $x_config['refund_nopass_tmpl_id']);

        $arr = [
            1 => 'character_string1',
            2 => 'amount3',
            3 => 'time5',
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

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/' . $page,
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                //订单号
                $arr[1] => array(

                    'value' => $data['order_code'],

                    'color' => '#0000ff',
                ),
                //金额
                $arr[2] => array(
                    //内容
                    'value' => $data['money'] . '元',

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

        $wx_setting = new WxSetting($data['uniacid']);

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
     * @Desc:退款通过通知
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/1 15:53
     */
    public static function refundPassSendMsg($data, $type = 1)
    {
        $user_model = new User();

        $user_info = $user_model->dataInfo(['id' => $data['user_id']]);

        if (empty($user_info)) {

            return false;
        }

        $page = $type == 1 ? 'user/pages/refund/list' : 'find/pages/invitation/list?userPageType=1';

        //type 1小程序 2公众号
        $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

        if ($type == 1) {

            $res = self::refundPassSendMsgWechat($data, $page);

        } else {

            $res = self::refundPassSendMsgWeb($data, $page);

        }

        return $res;
    }

    /**
     * @Desc: 退款通过通知-小程序
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/1 15:50
     */
    public static function refundPassSendMsgWechat($data, $page)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['refund_pass_tmpl_id'])) {

            return false;
        }

        $config = longbingGetAppConfig($data['uniacid']);

        $openid = User::where('id', $data['user_id'])->value('wechat_openid');

        $access_token = longbingGetAccessToken($data['uniacid']);

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
                'template_id' => $x_config['refund_pass_tmpl_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],

                'data' => array(
                    //订单号
                    'character_string1' => array(

                        'value' => $data['order_code'],

                        'color' => '#0000ff',
                    ),
                    //金额
                    'amount2' => array(
                        //内容
                        'value' => $data['money'],

                        'color' => '#0000ff',
                    ),
                    //时间
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
     * @Desc:退款通过通知-公众号
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/1 15:52
     */
    public static function refundPassSendMsgWeb($data, $page)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['refund_pass_tmpl_id'])) {

            return false;
        }
        $openid = User::where('id', $data['user_id'])->value('web_openid');

        $key = explode('&', $x_config['refund_pass_tmpl_id']);

        $arr = [
            1 => 'character_string1',
            2 => 'amount2',
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

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/' . $page,
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                //订单号
                $arr[1] => array(

                    'value' => $data['order_code'],

                    'color' => '#0000ff',
                ),
                //金额
                $arr[2] => array(
                    //内容
                    'value' => $data['money'] . '元',

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

        $wx_setting = new WxSetting($data['uniacid']);

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
     * @Desc: 用户信息
     * @param $where
     * @return User|array|mixed|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/15 15:56
     */
    public static function getInfo($where)
    {

        $info = self::where($where)->field('id,uniacid,nickName,avatarUrl,phone,alipay_number,alipay_name,create_time,balance,total_integral,integral')->find();

        $info = $info ? $info->toArray() : [];

        $from = UserFrom::alias('a')
            ->field('a.user_id,a.from_type,a.from_name,b.nickName,c.user_name,d.user_name as channel_name,e.name as staff_name,f.name as broker_name,g.agent_name as admin_name,h.coach_name,i.channel_id')
            ->where('a.user_id', $info['id'])
            ->leftJoin('massage_service_user_list b', 'a.from_id=b.id and a.from_type=2')
            ->leftJoin('massage_distribution_list c', 'a.from_id=c.id and a.from_type in (3,10)')
            ->leftJoin('massage_channel_list d', 'a.from_id=d.id and a.from_type=4')
            ->leftJoin('massage_channel_staff_list e', 'a.from_id=e.id and a.from_type=5')
            ->leftJoin('massage_broker_list f', 'a.from_id=f.id and a.from_type=6')
            ->leftJoin('shequshop_school_admin g', 'a.from_id=g.id and a.from_type=7')
            ->leftJoin('massage_service_coach_list h', 'a.from_id=h.id and a.from_type=8')
            ->leftJoin('massage_channel_staff_list i', 'a.from_id=i.id and a.from_type=9')
            ->find();

        $from = $from ? $from->toArray() : [];

        $attendant_name = getConfigSetting($info['uniacid'], 'attendant_name');

        $from['from_type'] = empty($from) ? 0 : $from['from_type'];

        switch ($from['from_type']) {
            case 1:
                $from_name = '公众号搜索';
                break;
            case 2:
                $from_name = $from['nickName'] . '-分享链接';
                break;
            case 3:
                $from_name = $from['user_name'] . '-分销码';
                break;
            case 4:
                $from_name = $from['channel_name'] . '-渠道商邀请用户二维码';
                break;
            case 5:
                $from_name = $from['staff_name'] . '-渠道员工码';
                break;
            case 6:
                $from_name = $from['broker_name'] . '-经纪人邀请' . $attendant_name . '码';
                break;
            case 7:
                $from_name = $from['admin_name'] . '-代理商邀请' . $attendant_name . '码';
                break;
            case 8:
                $from_name = $from['coach_name'] . '-' . $attendant_name . '充值邀请码';
                break;
            case 9:
                $from_name = ChannelList::where('id', $from['channel_id'])->value('user_name') . '-渠道商邀请员工二维码';
                break;
            case 10:
                $from_name = $from['user_name'] . '-分销员邀请用户购买会员卡';
                break;
            case 11:
                $from_name = '抖音-' . (empty($from['from_name']) ? '无' : $from['from_name']);
                break;
            case 12:
                $from_name = '视频号-' . (empty($from['from_name']) ? '无' : $from['from_name']);
                break;
            case 13:
                $from_name = '小红书-' . (empty($from['from_name']) ? '无' : $from['from_name']);
                break;
            case 14:
                $from_name = '其他-' . (empty($from['from_name']) ? '无' : $from['from_name']);
                break;
            default:
                $from_name = '';
        }

        $info['from_name'] = $from_name;

        $info['member_info'] = MemberOrder::getUserCard($info['id']);

        $label_model = new UserLabelData();

        $info['user_label'] = $label_model->getUserLabel($info['id']);

        $info['total_consumption'] = Order::getUserConsumption($info['id']);

        $info['wait_integral'] = OrderIntegral::where(['user_id' => $info['id'], 'status' => 1])->sum('integral');

        return $info;
    }
}