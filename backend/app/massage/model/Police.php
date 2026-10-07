<?php

namespace app\massage\model;

use app\BaseModel;
use longbingcore\wxcore\WxSetting;
use think\facade\Db;

class Police extends BaseModel
{
    //定义表名
    protected $name = 'massage_service_coach_police';


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data)
    {
        $data['create_time'] = time();
        return $this->insert($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis, $data)
    {
        return $this->where($dis)->update($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis, $page = 10)
    {
        return $this->alias('a')
            ->join('massage_service_coach_list b', 'b.id = a.coach_id')
            ->where($dis)
            ->field('a.*,b.coach_name')
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($page)
            ->toArray();
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis)
    {
        $data = $this->where($dis)->find();
        return !empty($data) ? $data->toArray() : [];
    }


    /**
     * @author chenniang
     * @DataTime: 2021-04-01 09:51
     * @功能说明:向导求救通知小程序
     */
    public function helpSendMsgWechat($uniacid, $coach_id, $address, $openid)
    {
        $cap_model = new Coach();
        $config_model = new SendMsgConfig();
        $config_model->initData($uniacid);
        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);
        $config = longbingGetAppConfig($uniacid);
        if (empty($x_config['gzh_appid']) || empty($x_config['help_tmpl_id'])) {
            return false;
        }
        $coach_name = $cap_model->where(['id' => $coach_id])->value('coach_name');
        $access_token = longbingGetAccessToken($uniacid);
        $page = "/pages/index";
        //post地址
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token={$access_token}";
        $data = [
            //用户小程序openid
            'touser' => $openid,

            'mp_template_msg' => [
                //公众号appid
                'appid' => $x_config['gzh_appid'],
                "url" => "http://weixin.qq.com/download",
                //公众号模版id
                'template_id' => $x_config['help_tmpl_id'],
                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],
                'data' => array(
                    //服务名称
                    'keyword1' => array(
                        'value' => $coach_name,
                        'color' => '#93c47d',
                    ),
                    //下单人
                    'keyword2' => array(
                        //内容
                        'value' => $address,
                        'color' => '#0000ff',
                    ),
                    'keyword3' => array(
                        //内容
                        'value' => date('Y-m-d H:i', time()),
                        'color' => '#0000ff',
                    ),
                )
            ]
        ];
        $data = json_encode($data);
        $tmp = [
            'url' => $url,
            'data' => $data,
        ];
        $rest = lbCurlPost($tmp['url'], $tmp['data']);
        return json_decode($rest, true);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-04-01 09:51
     * @功能说明:向导求救通知公众号
     */
    public function helpSendMsgWeb($uniacid, $coach_id, $address, $openid)
    {
        $cap_model = new Coach();
        $config_model = new SendMsgConfig();
        $config_model->initData($uniacid);
        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);
        if (empty($x_config['gzh_appid']) || empty($x_config['help_tmpl_id'])) {
            return false;
        }
        $coach_name = $cap_model->where(['id' => $coach_id])->value('coach_name');
        $wx_setting = new WxSetting($uniacid);
        $access_token = $wx_setting->getGzhToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";
        $key = explode('&', $x_config['help_tmpl_id']);
        for ($i = 1; $i < 4; $i++) {
            $arr[$i] = !empty($key[$i]) ? $key[$i] : 'keyword'.$i;
        }
        $data = [
            //用户小程序openid
            'touser' => $openid,
            //公众号appid
            'appid' => $x_config['gzh_appid'],
            "url" => 'https://'.$_SERVER['HTTP_HOST'].'/h5/',
            //公众号模版id
            'template_id' => $key[0],
            'data' => array(
                //服务名称
                $arr[1] => array(
                    'value' => $coach_name,
                    'color' => '#93c47d',
                ),
                $arr[2] => array(
                    'value' => $arr[2] == 'keyword2' ? $address : mb_substr($address, 0, 20),
                    'color' => '#93c47d',
                ),
                $arr[3] => array(
                    'value' => date('Y-m-d H:i', time()),
                    'color' => '#93c47d',
                )
            )
        ];
        $data = json_encode($data);
        $tmp = [
            'url' => $url,
            'data' => $data,
        ];
        $rest = lbCurlPost($tmp['url'], $tmp['data']);
        return json_decode($rest, true);
    }


    /**
     * @author chenniang
     * @DataTime: 2022-06-01 15:17
     * @功能说明:发送向导求救消息
     */
    public function helpSendMsg($uniacid, $coach_id, $address)
    {
        $help_model = new HelpConfig();
        $coach_model = new Coach();
        $user_model = new User();
        $help_config = $help_model->dataInfo(['uniacid' => $uniacid]);
        $coach_info = $coach_model->dataInfo(['id' => $coach_id]);
        //通知平台管理员
        if ($help_config['tmpl_admin_status'] == 1 && !empty($help_config['help_user_id']) && (empty($coach_info['admin_id']) || $help_config['tmpl_notice_admin'] == 1)) {
            $user_list = $user_model->where('id', 'in', $help_config['help_user_id'])->select()->toArray();
            if (empty($user_list)) {
                return false;
            }
            foreach ($user_list as $user_info) {
                //pe 1小程序 2公众号
                $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;
                if ($type == 1) {
                    $res = $this->helpSendMsgWechat($uniacid, $coach_id, $address, $user_info['wechat_openid']);
                } else {
                    $res = $this->helpSendMsgWeb($uniacid, $coach_id, $address, $user_info['web_openid']);
                }
            }
        }
        //通知代理商
        if ($help_config['tmpl_agent_status'] == 1 && !empty($coach_info['admin_id'])) {
            $admin_model = new Admin();
            $user_id = $admin_model->where(['id' => $coach_info['admin_id']])->value('user_id');
            $user_info = $user_model->dataInfo(['id' => $user_id]);
            if (!empty($user_info)) {
                //pe 1小程序 2公众号
                $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;
                if ($type == 1) {
                    $res = $this->helpSendMsgWechat($uniacid, $coach_id, $address, $user_info['wechat_openid']);
                } else {
                    $res = $this->helpSendMsgWeb($uniacid, $coach_id, $address, $user_info['web_openid']);
                }
            }
        }
        return $res ?? true;

    }

    /**
     * @param $insert
     * @param $caoch_info
     * @功能说明:发送求救通知
     * @author chenniang
     * @DataTime: 2023-09-28 15:15
     */
    public function sendPoliceNotice($uniacid, $coach_id, $address)
    {
        $config_model = new ShortCodeConfig();
        $reminder_model = new \app\reminder\model\Config();
        //发送短信通知
        $config_model->sendHelpCode($uniacid, $coach_id, $address);
        //发送公众号通知
        $this->helpSendMsg($uniacid, $coach_id, $address);
        $coach_model = new Coach();
        $caoch_info = $coach_model->dataInfo(['id' => $coach_id]);
        //发送语音通知
        $reminder_model->sendCalledPolice($caoch_info);
        return true;
    }

    /***
     * @Desc: 对比最后一条数据时间
     * @param $dis
     * @param $final
     * @return Police|array|mixed|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/10/25 13:48
     */
    public function getFinalInfo($dis, $final)
    {
        $data = $this->where($dis)->order('id desc')->find();
        if (!empty($data)) {
            $data['notice_type'] = 1;
        }
        if (!empty($final) && (!empty($data) && $data['create_time'] < strtotime($final['create_time']))) {
            $data = $final;
        }
        return $data;
    }

}