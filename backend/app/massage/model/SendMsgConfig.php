<?php

namespace app\massage\model;

use AlibabaCloud\Client\AlibabaCloud;
use app\BaseModel;
use Exception;
use log\LogUtils;
use longbingcore\wxcore\WxSetting;
use think\facade\Db;

class SendMsgConfig extends BaseModel
{
    //定义表名
    protected $name = 'massage_send_msg_config';


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data)
    {

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
    public function dataList($dis, $page)
    {

        $data = $this->where($dis)->order('id desc')->paginate($page)->toArray();

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis)
    {

        $data = $this->where($dis)->find();

        if (empty($data)) {

            $this->dataAdd($dis);

            $data = $this->where($dis)->find();

        }

        return !empty($data) ? $data->toArray() : [];

    }


    /**
     * @param $uniacid
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-02-03 10:39
     */
    public function initData($uniacid)
    {

        $data = $this->dataInfo(['uniacid' => $uniacid]);

        $config_model = new Config();

        $config = $config_model->dataInfo(['uniacid' => $uniacid]);
        //开始初始化
        if (!empty($config['gzh_appid'])) {

            $update = [

                'help_tmpl_id' => $config['help_tmpl_id'],
                'order_tmp_id' => $config['order_tmp_id'],
                'cancel_tmp_id' => $config['cancel_tmp_id'],
                'coachupdate_tmp_id' => $config['coachupdate_tmp_id'],
                'gzh_appid' => $config['gzh_appid'],
            ];

            $this->dataUpdate(['id' => $data['id']], $update);

            $prefix = longbing_get_prefix();
            //执行sql删除废弃字段
            $sql = <<<updateSql
            
ALTER TABLE `{$prefix}shequshop_school_config` DROP COLUMN  `help_tmpl_id`;
ALTER TABLE `{$prefix}shequshop_school_config` DROP COLUMN  `order_tmp_id`;
ALTER TABLE `{$prefix}shequshop_school_config` DROP COLUMN  `cancel_tmp_id`;
ALTER TABLE `{$prefix}shequshop_school_config` DROP COLUMN  `coachupdate_tmp_id`;
ALTER TABLE `{$prefix}shequshop_school_config` DROP COLUMN  `gzh_appid`;

                

updateSql;

            $sql = str_replace(PHP_EOL, '', $sql);
            $sqlArray = explode(';', $sql);

            foreach ($sqlArray as $_value) {
                if (!empty($_value)) {

                    try {
                        Db::query($_value);
                    } catch (\Exception $e) {
                        if (!APP_DEBUG) {

                        }

                    }
                }
            }

        }

        return true;
    }

    /**
     * @Desc: 服务通知
     * @param $uniacid
     * @return true
     * @Auther: shurong
     * @Time: 2023/10/23 11:01
     */
    public function orderServiceNotice($uniacid)
    {

        $key = 'orderServiceNotice_key_value';

        incCache($key, 1, $uniacid);

        $value = getCache($key, $uniacid);

        if ($value == 1) {
            //未接单通知 4
            $this->unacceptedOrdersNotice($uniacid);
            //迟到通知 5
            $this->latOrderNotice($uniacid);
            //跳单通知 6
            $this->jumpOrdersNotice($uniacid);
        }

        decCache($key, 1, $uniacid);

        return true;
    }

    /**
     * @author chenniang
     * @DataTime: 2023-08-01 18:26
     * @功能说明:技师未接单通知
     */
    public function unacceptedOrdersNotice($uniacid)
    {

        $setting = getConfigSettingArr($uniacid, ['wechat_tmpl', 'coach_receiving_minute']);

        $order_model = new Order();

        $dis[] = ['uniacid', '=', $uniacid];

        $dis[] = ['pay_type', '=', 2];

        $where = [

            'type' => 4,

            'uniacid' => $uniacid
        ];

        $notice_model = new NoticeList();

        $order_id = $notice_model->where($where)->column('order_id');

        $dis[] = ['id', 'not in', $order_id];

        if (!empty($setting['coach_receiving_minute'])) {

            $data = $order_model->where($dis)->where('pay_time', '<', time() - $setting['coach_receiving_minute'] * 60)->order('id desc')->limit(10)->select()->toArray();
        } else {

            $data = [];
        }

        if (!empty($data)) {

            foreach ($data as $v) {

                $find = $notice_model->dataInfo(['type' => 4, 'order_id' => $v['id']]);

                $notice_model->dataAdd($uniacid, $v['id'], 4, $v['admin_id']);
                //需要发送模版消息
                if ($setting['wechat_tmpl'] == 1 && empty($find)) {

                    $this->webOrderServiceNoticeAdmin($v, 1);

                    $this->webOrderServiceNoticeCompany($v, 1);

                }
            }
        }

        return true;
    }

    public function getNoticeType($type)
    {

        switch ($type) {

            case 1:
                $data['text'] = '未接单通知';
                break;
            case 2:
                $data['text'] = '拒单通知';
                break;
            case 3:
                $data['text'] = '迟到通知';
                break;
            case 4:
                $data['text'] = '跳单通知';
                break;
        }

        return $data;
    }

    /**
     * @author chenniang
     * @DataTime: 2023-08-01 10:44
     * @功能说明:订单服务通知通知平台
     */
    public function webOrderServiceNoticeCompany($order, $type)
    {
        //通知类型
        $text = $this->getNoticeType($type);

        $user_model = new User();

        $uniacid = $order['uniacid'];

        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['order_service_tmpl_id'])) {

            return false;
        }
        //获取平台设置的管理员
        $admin_id = getConfigSetting($order['uniacid'], 'wechat_tmpl_admin');

        if (empty($admin_id)) {

            return false;
        }

        $admin_id = explode(',', $admin_id);

        foreach ($admin_id as $value) {
            //获取楼长openid
            $openid = $user_model->where(['id' => $value])->value('openid');

            $wx_setting = new WxSetting($uniacid);

            $access_token = $wx_setting->getGzhToken();

            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

            $key = explode('&', $x_config['order_service_tmpl_id']);

            for ($i = 1; $i < 3; $i++) {

                $arr[$i] = !empty($key[$i]) ? $key[$i] : 'keyword' . $i;
            }

            $data = [
                //用户小程序openid
                'touser' => $openid,
                //公众号appid
                'appid' => $x_config['gzh_appid'],

                //  "url"   => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/user/pages/order/detail?id=' . $order['id'],
                "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/',
                //公众号模版id
                'template_id' => $key[0],

                'data' => array(

                    $arr[1] => array(

                        'value' => $order['order_code'],

                        'color' => '#93c47d',
                    ),
                    //预约时间
                    $arr[2] => array(
                        //内容
                        'value' => $text['text'],

                        'color' => '#0000ff',
                    )
                )

            ];

            $data = json_encode($data);

            $tmp = [

                'url' => $url,

                'data' => $data,
            ];
            $rest = lbCurlPost($tmp['url'], $tmp['data']);

            $rest = json_decode($rest, true);
        }

        return $rest;
    }


    /**
     * @author chenniang
     * @DataTime: 2023-08-01 10:44
     * @功能说明:订单服务通知通知代理商
     */
    public function webOrderServiceNoticeAdmin($order, $type)
    {

        if (!empty($order['admin_id'])) {
            //获取通知类型
            $text = $this->getNoticeType($type);

            $uniacid = $order['uniacid'];

            $config_model = new SendMsgConfig();

            $config_model->initData($uniacid);

            $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

            if (empty($x_config['gzh_appid']) || empty($x_config['order_service_tmpl_id'])) {

                return false;
            }

            $admin_model = new Admin();

            $user_model = new User();

            $user_id = $admin_model->where(['id' => $order['admin_id']])->value('user_id');
            //获取楼长openid
            $openid = $user_model->where(['id' => $user_id])->value('web_openid');

            $wx_setting = new WxSetting($uniacid);

            $access_token = $wx_setting->getGzhToken();

            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

            $key = explode('&', $x_config['order_service_tmpl_id']);

            for ($i = 1; $i < 3; $i++) {

                $arr[$i] = !empty($key[$i]) ? $key[$i] : 'keyword' . $i;
            }

            $data = [
                //用户小程序openid
                'touser' => $openid,
                //公众号appid
                'appid' => $x_config['gzh_appid'],

                // "url"   => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/user/pages/order/detail?id=' . $order['id'],
                "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5',
                //公众号模版id
                'template_id' => $key[0],

                'data' => array(

                    $arr[1] => array(

                        'value' => $order['order_code'],

                        'color' => '#93c47d',
                    ),
                    //预约时间
                    $arr[2] => array(
                        //内容
                        'value' => $text['text'],

                        'color' => '#0000ff',
                    )

                )

            ];

            $data = json_encode($data);

            $tmp = [

                'url' => $url,

                'data' => $data,
            ];
            $rest = lbCurlPost($tmp['url'], $tmp['data']);

            $rest = json_decode($rest, true);

        }
        return true;
    }

    /**
     * @param $order
     * @功能说明:下单模版消息通知平台和代理商
     * @author chenniang
     * @DataTime: 2023-11-09 11:26
     */
    public function sendOrderTmplAdmin($order)
    {

        $config_model = new HelpConfig();

        $user_model = new User();

        $admin_model = new Admin();

        $x_config = $this->dataInfo(['uniacid' => $order['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['order_tmp_id'])) {

            return false;
        }

        $virtual_config_model = new \app\virtual\model\Config();

        $mobile_auth = $virtual_config_model->getVirtualAuth($order['uniacid']);

        $config = $config_model->dataInfo(['uniacid' => $order['uniacid']]);

        $wx_setting = new WxSetting($order['uniacid']);

        $access_token = $wx_setting->getGzhToken();

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

        $key = explode('&', $x_config['order_tmp_id']);

        for ($i = 1; $i < 6; $i++) {

            $arr[$i] = !empty($key[$i]) ? $key[$i] : 'keyword' . $i;
        }

//        $order['address_info']['address_info'] = !empty($order['address_info']['address_info']) ? $order['address_info']['address_info'] : $order['address_info']['address'];

        //通知管理员
        if ($config['order_tmpl_admin_status'] == 1 && !empty($config['order_tmpl_text']) && (empty($order['admin_id']) || $config['order_tmpl_notice_admin'])) {

            foreach ($config['order_tmpl_text'] as $value) {

                $openid = $user_model->where(['id' => $value])->value('web_openid');

                $data = [
                    //用户小程序openid
                    'touser' => $openid,
                    //公众号appid
                    'appid' => $x_config['gzh_appid'],

                    "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/technician/pages/order/detail?id=' . $order['id'],
                    //公众号模版id
                    'template_id' => $key[0],

                    'data' => array(
                        //服务名称
                        $arr[1] => array(

                            'value' => mb_substr(implode(',', array_column($order['order_goods'], 'goods_name')), 0, 20),

                            'color' => '#93c47d',
                        ),
                        //下单人
                        $arr[2] => array(
                            //内容
                            'value' => $order['address_info']['user_name'],

                            'color' => '#0000ff',
                        ),
                        $arr[3] => array(
                            //内容
                            'value' => $mobile_auth == false ? $order['address_info']['mobile'] : '-',

                            'color' => '#0000ff',
                        ),
                        //客户电话
                        $arr[4] => array(
                            //内容
                            'value' => $order['order_code'],

                            'color' => '#0000ff',
                        ),
                        $arr[5] => array(
                            //内容
                            'value' => mb_substr($order['address_info']['address'], 0, 20, 'utf8'),

                            'color' => '#0000ff',
                        ),
                    )
                ];

                $data = json_encode($data);

                $tmp = [

                    'url' => $url,

                    'data' => $data,
                ];

                $rest = lbCurlPost($tmp['url'], $tmp['data']);
            }
        }
        //给代理商发
        if ($config['order_tmpl_agent_status'] == 1 && !empty($order['admin_id'])) {

            $user_id = $admin_model->where(['id' => $order['admin_id']])->value('user_id');

            $openid = $user_model->where(['id' => $user_id])->value('web_openid');

            $data = [
                //用户小程序openid
                'touser' => $openid,
                //公众号appid
                'appid' => $x_config['gzh_appid'],

                "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/technician/pages/order/detail?id=' . $order['id'],
                //公众号模版id
                'template_id' => $key[0],

                'data' => array(
                    //服务名称
                    $arr[1] => array(

                        'value' => mb_substr(implode(',', array_column($order['order_goods'], 'goods_name')), 0, 20),

                        'color' => '#93c47d',
                    ),
                    //下单人
                    $arr[2] => array(
                        //内容
                        'value' => $order['address_info']['user_name'],

                        'color' => '#0000ff',
                    ),
                    $arr[3] => array(
                        //内容
                        'value' => $mobile_auth == false ? $order['address_info']['mobile'] : '-',

                        'color' => '#0000ff',
                    ),
                    //客户电话
                    $arr[4] => array(
                        //内容
                        'value' => $order['order_code'],

                        'color' => '#0000ff',
                    ),
                    $arr[5] => array(
                        //内容
                        'value' => mb_substr($order['address_info']['address'], 0, 20, 'utf8'),

                        'color' => '#0000ff',
                    ),
                )
            ];

            $data = json_encode($data);

            $tmp = [

                'url' => $url,

                'data' => $data,
            ];

            $rest = lbCurlPost($tmp['url'], $tmp['data']);

        }

        return true;

    }

    /**
     * @author chenniang
     * @DataTime: 2023-08-01 18:26
     * @功能说明:技师迟到提醒
     */
    public function latOrderNotice($uniacid){

        $setting = getConfigSettingArr($uniacid,['wechat_tmpl','service_lat_type','service_lat_minute']);

        $order_model = new Order();

        $dis[] = ['uniacid','=',$uniacid];

        $dis[] = ['is_add','=',0];

        $dis[] = ['pay_type','in',[3,4]];

        $where = [

            'type' => 5,

            'uniacid' => $uniacid
        ];

        $notice_model = new NoticeList();

        $order_id = $notice_model->where($where)->column('order_id');

        $dis[] = ['id','not in',$order_id];

        $dis[] = ['store_id','=',0];
        //服务开始前未到达
        if($setting['service_lat_type']==0){

            $data = $order_model->where('start_time','<',time()+$setting['service_lat_minute']*60)->where($dis)->order('id desc')->limit(10)->select()->toArray();

        }else{
            //服务开始后未到达
            $data = $order_model->where('start_time','<',time()-$setting['service_lat_minute']*60)->where($dis)->order('id desc')->limit(10)->select()->toArray();

        }

        if(!empty($data)){

            foreach ($data as $v){

                $notice_model->dataAdd($uniacid,$v['id'],5,$v['admin_id']);

                $this->webOrderServiceNoticeAdmin($v,3);
                //需要发送模版消息
                if($setting['wechat_tmpl']==1){

                    $this->webOrderServiceNoticeCompany($v,3);

                }
            }
        }

        return true;
    }
    /**
     * @author chenniang
     * @DataTime: 2023-08-01 18:26
     * @功能说明:技师跳单
     */
    public function jumpOrdersNotice($uniacid){

        $setting = getConfigSettingArr($uniacid,['wechat_tmpl','jump_order_minute','jump_order_distance']);

        $order_model = new Order();

        $coach_model = new Coach();

        $notice_model = new NoticeList();

        $dis[] = ['uniacid','=',$uniacid];

        $dis[] = ['pay_type','=',7];

        $dis[] = ['is_safe','=',0];

        $dis[] = ['store_id','=',0];

        $dis[] = ['is_add','=',0];

        $dis[] = ['coach_id','>',0];

        $start_time = time()-$setting['jump_order_minute']*60-3600;

        $end_time   = time()-$setting['jump_order_minute']*60;

        $dis[] = ['order_end_time','between',"$start_time,$end_time"];

        $data = $order_model->where($dis)->order('id desc')->limit(10)->select()->toArray();

        if(!empty($data)){

            foreach ($data as $v){

                $lat = !empty($v['address_info'])?$v['address_info']['lat']:0;

                $lng = !empty($v['address_info'])?$v['address_info']['lng']:0;

                $coach_info = $coach_model->dataInfo(['id'=>$v['coach_id']]);

                $coach_lat  = !empty($coach_info)?$coach_info['lat']:90;

                $coach_lng  = !empty($coach_info)?$coach_info['lng']:90;
                //获取距离
                $distance   = getDriveDistance($coach_lng,$coach_lat,$lng,$lat,$uniacid);

                LogUtils::log($v['id'] . '====' . $distance . '====' . $setting['jump_order_distance'] * 1000, 'distance');

                //有跳单风险
                if($setting['jump_order_distance']>0&&$distance<$setting['jump_order_distance']*1000){

                    $notice_model->dataAdd($uniacid,$v['id'],6,$v['admin_id']);

                    $this->webOrderServiceNoticeAdmin($v,4);
                    //需要发送模版消息
                    if($setting['wechat_tmpl']==1){

                        $this->webOrderServiceNoticeCompany($v,4);
                    }

                    $order_model->dataUpdate(['id'=>$v['id']],['is_safe'=>2]);
                }else{

                    $order_model->dataUpdate(['id'=>$v['id']],['is_safe'=>1]);

                }
            }
        }

        return true;
    }

    /**
     * @Desc: 支付成功通知
     * @param $uniacid
     * @param $user_id
     * @param $money
     * @param $pay_type
     * @return false|mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/19 17:40
     */
    public static function paySuccess($uniacid,$user_id,$money,$pay_type)
    {

        $pay_text = [
            1 => '微信支付',
            2 => '余额支付',
            3 => '支付宝支付',
        ];

        $text = $pay_text[$pay_type];

        $user_model = new User();

        $user_info = $user_model->dataInfo(['id' => $user_id]);

        if (empty($user_info)) {

            return false;
        }

        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['pay_success_tmpl_id'])) {

            return false;
        }

        $wx_setting = new WxSetting($uniacid);

        $access_token = $wx_setting->getGzhToken();

        //post地址
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

        $key = explode('&', $x_config['pay_success_tmpl_id']);

        for ($i = 1; $i < 5; $i++) {

            $arr[$i] = !empty($key[$i]) ? $key[$i] : 'keyword' . $i;
        }

        $data = [
            //用户小程序openid
            'touser' => $user_info['web_openid'],
            //公众号appid
            'appid' => $x_config['gzh_appid'],

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/pages/mine',
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                //客户名称
                $arr[1] => array(

                    'value' => $user_info['nickName'],

                    'color' => '#93c47d',
                ),
                //金额
                $arr[2] => array(
                    //内容
                    'value' => $money.'元',

                    'color' => '#0000ff',
                ),
                //支付渠道
                $arr[3] => array(
                    //内容
                    'value' => $text,

                    'color' => '#0000ff',
                ),
                //下单时间
                $arr[4] => array(
                    //内容 2022年11月11日 10:10
                    'value' => date('Y年m月d日 H:i'),

                    'color' => '#0000ff',
                ),
            )

        ];

        $data = json_encode($data);

        $tmp = [

            'url' => $url,

            'data' => $data,
        ];
        $rest = lbCurlPost($tmp['url'], $tmp['data']);

        $rest = json_decode($rest, true);

        return $rest;

    }

}