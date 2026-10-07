<?php

namespace app\massage\model;

use app\BaseModel;
use log\LogUtils;
use longbingcore\wxcore\WxSetting;
use think\facade\Db;

class Coach extends BaseModel
{
    //定义表名
    protected $name = 'massage_service_coach_list';


    protected $append = [

        'comment_num',

        'collect_num',
    ];


    /**
     * @author chenniang
     * @DataTime: 2021-07-22 14:33
     * @功能说明:保留两位
     */
    public function getServicePriceAttr($value, $data)
    {

        if (isset($value)) {

            return round($value, 2);
        }

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-22 14:33
     * @功能说明:保留两位
     */
    public function getCarPriceAttr($value, $data)
    {

        if (isset($value)) {

            return round($value, 2);
        }

    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-07 22:47
     * @功能说明:服务数量
     */
    public function getOrderNumAttr($value, $data)
    {

        if (isset($value)) {

            if (!empty($data['id'])) {

                $comm_model = new Order();

                $num = $comm_model->where(['coach_id' => $data['id'], 'pay_type' => 7])->count();

                $value += $num;

            }

            return $value;

        }

    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-07 22:47
     * @功能说明:评论数量
     */
    public function getCommentNumAttr($value, $data)
    {

        if (!empty($data['id'])) {

            $comm_model = new Comment();

            $num = $comm_model->where(['coach_id' => $data['id'], 'status' => 1])->count();

            return $num;

        }

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-07 22:47
     * @功能说明:收藏数量
     */
    public function getCollectNumAttr($value, $data)
    {

        if (!empty($data['id'])) {

            $comm_model = new CoachCollect();

            $num = $comm_model->where(['coach_id' => $data['id']])->count();

            return $num;

        }

    }

    /**
     * @param $value
     * @param $data
     * @功能说明:
     * @author chenniang
     * @DataTime: 2021-03-22 16:47
     */
    public function getIdCardAttr($value, $data)
    {

        if (!empty($value)) {

            return explode(',', $value);
        }

    }

    public function getModelImgAttr($value, $data)
    {

        if (!empty($value)) {

            return explode(',', $value);
        }

    }


    /**
     * @param $value
     * @param $data
     * @功能说明:
     * @author chenniang
     * @DataTime: 2021-03-22 16:47
     */
    public function getLicenseAttr($value, $data)
    {

        if (!empty($value)) {

            return explode(',', $value);
        }

    }


    /**
     * @param $value
     * @param $data
     * @功能说明:
     * @author chenniang
     * @DataTime: 2021-03-22 16:47
     */
    public function getSelfImgAttr($value, $data)
    {

        if (!empty($value)) {

            return explode(',', $value);
        }

    }

    /**
     * 电话号码加密
     * @param $value
     * @param $data
     * @return string|string[]
     */
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
     * @author chenniang
     * @DataTime: 2021-03-15 14:37
     * @功能说明:后台列表
     */
    public function adminDataList($dis, $mapor, $page = 10)
    {

        $data = $this->alias('a')
            ->join('shequshop_school_user_list b', 'a.user_id = b.id')
            ->where($dis)
            ->where(function ($query) use ($mapor) {
                $query->whereOr($mapor);
            })
            ->field('a.*,b.nickName,b.avatarUrl')
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($page)
            ->toArray();

        return $data;

    }

    /**
     * @author chenniang
     * @DataTime: 2020-10-21 15:21
     * @功能说明:保留两位小数
     */
    public function getDistanceAttr($value)
    {

        if (isset($value)) {

            if ($value > 1000) {

                $value = $value / 1000;

                $value = round($value, 2);

                $value = $value . 'km';
            } else {

                $value = round($value, 2);

                $value = $value . 'm';

            }

            return $value;

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

        $res = $this->insert($data);

        return $res;

    }


    /**
     * 修改服务 07-31
     * @param $id
     * @param $uniacid
     * @param $service
     * @return bool
     */
    public function updateSome($id, $uniacid, $service)
    {

        $s_model = new ServiceCoach();

        $s_model->where(['coach_id' => $id])->delete();

        if (!empty($service)) {

            foreach ($service as $value) {

                if (!isset($value['ser_id'])) {

                    $value = Service::where('id', $value)->field('id as ser_id,price')->find()->toArray();
                }

                $insert['ser_id'] = $value['ser_id'];

                $insert['uniacid'] = $uniacid;

                $insert['coach_id'] = $id;

                $insert['price'] = $value['price'];

                $insert['balance'] = $value['balance'] ?? 0;

                $s_model->dataAdd($insert);

            }

        }

        return true;

    }

    /**
     * @Desc: 添加服务
     * @param $input
     * @param $uniacid
     * @return ServiceCoach|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/19 10:32
     */
    public function addService($input, $uniacid)
    {
        $s_model = new ServiceCoach();

        foreach ($input['service'] as $item) {

            $data = [
                'ser_id' => $item['ser_id'], 'uniacid' => $uniacid, 'coach_id' => $input['id']
            ];
            $count = $s_model->where($data)->count();

            $insert = $data;

            $price = Service::where('id', $item['ser_id'])->value('price');

            $insert['price'] = $item['price'] ?? $price;

            if ($count) {

                $s_model->where($data)->update($insert);
            } else {

                $s_model->insert($insert);
            }
        }

        return true;
    }

    /**
     * @Desc: 修改服务
     * @param $input
     * @param $uniacid
     * @return ServiceCoach|bool
     * @Auther: shurong
     * @Time: 2023/12/19 10:39
     */
    public function updateService($input)
    {
        $s_model = new ServiceCoach();

        if (isset($input['status']) && $input['status'] == -1) {

            $res = $s_model->where('id', $input['data_id'])->delete();
        } else {

            $res = $s_model->where('id', $input['data_id'])->update(['price' => $input['price']]);
        }
        return $res;
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis, $data)
    {

        if (isset($data['id']) && $data['id'] == 0) {

            unset($data['id']);
        }

        $res = $this->where($dis)->update($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis, $page = 10, $mapor = [])
    {

        $data = $this->where($dis)->where(function ($query) use ($mapor) {
            $query->whereOr($mapor);
        })->order('id desc')->paginate($page)->toArray();

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis, $file = '*')
    {

        $data = $this->where($dis)->field($file)->find();

        return !empty($data) ? $data->toArray() : [];

    }


    /**
     * @author chenniang
     * @DataTime: 2021-04-06 11:54
     * @功能说明:获取团长的openid
     */
    public function capOpenid($id, $type = 2)
    {

        if ($type == 1) {

            $id = $this->alias('a')
                ->join('massage_service_user_list b', 'a.user_id = b.id')
                ->where(['a.id' => $id])
                ->value('b.wechat_openid');
        } else {

            $id = $this->alias('a')
                ->join('massage_service_user_list b', 'a.user_id = b.id')
                ->where(['a.id' => $id])
                ->value('b.web_openid');
        }


        return $id;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-07 10:21
     * @功能说明:服务向导列表
     */
    public function serviceCoachList($dis, $alh, $order = 'distance asc,a.id desc', $page = 10)
    {
        $data = $this->alias('a')
            ->join('massage_service_service_coach b', 'a.id = b.coach_id', 'left')
            ->join('massage_service_coach_tag c', 'a.tag_id = c.id', 'left')
            ->join('massage_service_order_list d', 'a.id = d.coach_id and d.create_time >' . strtotime('-7 day'), 'left')
            ->where($dis)
            ->field(['a.store_id,a.id,a.work_img,a.coach_name,a.self_img,a.order_num,a.is_work,a.index_top,a.user_id,a.text,a.work_time,a.star,a.nickname,c.name as tag_name,birthday,constellation,height,weight,IFNULL(sum(d.true_service_price),0) as service_price', $alh])
            ->group('a.id')
            ->order($order)
            ->paginate($page)
            ->toArray();
        if ($data['data']) {
            $where = [
                ['b.status', '=', 1]
            ];
            foreach ($dis as $di) {
                if ($di[0] == 'b.ser_id') {
                    $where[] = ['a.ser_id', '=', $di[2]];
                }
            }
            $ids = array_column($data['data'], 'id');
            $arr = Db::name('massage_service_service_coach')
                ->field(['a.coach_id', 'TRIM(TRAILING "." FROM TRIM(TRAILING "0" FROM min(a.price))) as price'])
                ->alias('a')
                ->join('massage_service_service_list b', 'a.ser_id = b.id', 'left')
                ->whereIn('a.coach_id', $ids)
                ->where($where)
                ->group('a.coach_id')
                ->select()
                ->toArray();
            foreach ($data['data'] as &$datum) {
                $datum['price'] = 0;
                foreach ($arr as $item) {
                    if ($datum['id'] == $item['coach_id']) {
                        $datum['price'] = $item['price'];
                    }
                }
            }
        }
        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-07 10:21
     * @功能说明:服务向导列表
     */
    public function typeServiceCoachList($dis, $alh, $order = '', $page = 10)
    {

        $data = $this->alias('a')
            ->join('massage_service_service_coach b', 'a.id = b.coach_id', 'left')
            ->join('massage_service_coach_tag c', 'a.tag_id = c.id', 'left')
            ->where($dis)
            ->field(['a.id,a.store_id,a.work_img,a.coach_name,a.self_img,a.order_num,a.is_work,a.index_top,a.user_id,a.text,a.work_time,a.star,c.name as tag_name,birthday,constellation,height,weight', $alh])
            ->group('a.id')
            ->order($order)
            ->paginate($page)
            ->toArray();

        return $data;

    }

    /**
     * @author chenniang
     * @DataTime: 2023-01-31 10:15
     * @功能说明:推荐向导下拉框
     */
    public function coachRecommendSelect($dis, $alh, $page = 10)
    {

//        $data = $this->where($dis)
//            ->field(['id,coach_name,work_img,star,city_id', $alh])
//            ->order('distance asc,id desc')
//            ->limit(12)
//            ->select()
//            ->toArray();
        $data = $this->alias('a')
            ->join('massage_service_coach_tag c', 'a.tag_id = c.id', 'left')
            ->where($dis)
            ->field(['a.store_id,a.id,a.work_img,a.coach_name,a.self_img,a.order_num,a.is_work,a.index_top,a.user_id,a.text,a.work_time,a.star,a.nickname,c.name as tag_name,birthday,constellation,height,weight,city_id', $alh])
            ->group('a.id')
            ->order('distance asc,id desc')
            ->paginate($page)
            ->toArray();
        if ($data['data']) {
            $ids = array_column($data['data'], 'id');
            $arr = Db::name('massage_service_service_coach')
                ->field(['a.coach_id', 'TRIM(TRAILING "." FROM TRIM(TRAILING "0" FROM min(a.price))) as price'])
                ->alias('a')
                ->join('massage_service_service_list b', 'a.ser_id = b.id', 'left')
                ->whereIn('a.coach_id', $ids)
                ->where('b.status', 1)
                ->group('a.coach_id')
                ->select()
                ->toArray();
            foreach ($data['data'] as &$datum) {
                $datum['price'] = 0;
                foreach ($arr as $item) {
                    if ($datum['id'] == $item['coach_id']) {
                        $datum['price'] = $item['price'];
                    }
                }
            }
        }
        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-07 22:41
     * @功能说明:教练收藏列表
     */
    public function coachCollectList($dis, $alh, $page = 10)
    {

        $data = $this->alias('a')
            ->join('massage_service_coach_collect b', 'a.id = b.coach_id')
            ->where($dis)
            ->field(['a.id,a.work_img,a.coach_name,a.self_img,a.order_num,a.is_work,a.index_top,a.user_id,a.text,a.work_time,a.star,birthday,constellation,height,weight', $alh])
            ->order('distance asc,a.id desc')
            ->paginate($page)
            ->toArray();

        return $data;
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-07 22:41
     * @功能说明:教练收藏列表
     */
    public function typeCoachCollectList($dis, $alh, $page = 10)
    {

        $data = $this->alias('a')
            ->join('massage_service_coach_collect b', 'a.id = b.coach_id')
            ->where($dis)
            ->field(['a.id,a.work_img,a.coach_name,a.self_img,a.order_num,a.is_work,a.index_top,a.user_id,a.text,a.work_time,a.star.birthday,constellation,height,weight', $alh])
            ->order('a.is_work desc,a.index_top desc,distance asc,a.id desc')
            ->paginate($page)
            ->toArray();

        return $data;
    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-07 22:41
     * @功能说明:教练收藏列表
     */
    public function coachCollectCount($user_id, $uniacid)
    {

        $shield_model = new ShieldList();
        //除开屏蔽向导的
        $coach_id = $shield_model->where(['user_id' => $user_id])->where('type', 'in', [2, 3])->column('coach_id');

        $dis[] = ['a.status', '=', 2];

        $dis[] = ['b.user_id', '=', $user_id];

        $config_model = new ConfigSetting();

        $config = $config_model->dataInfo($uniacid);

        if ($config['coach_format'] == 1) {

            $dis[] = ['a.is_work', '=', 1];

        }

        $data = $this->alias('a')
            ->join('massage_service_coach_collect b', 'a.id = b.coach_id')
            ->where($dis)
            ->where('a.id', 'not in', $coach_id)
            ->group('a.id')
            ->count();

        return $data;
    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-08 09:58
     * @功能说明:获取向导等级
     */
    public function getCoachLevelBak($caoch_id, $uniacid)
    {

        $level_model = new CoachLevel();

        $order_model = new Order();

        $dis = [

            'coach_id' => $caoch_id,

            'pay_type' => 7
        ];

        $time_long = $order_model->where($dis)->sum('true_time_long');

        $level = $level_model->where(['uniacid' => $uniacid, 'status' => 1])->order('time_long desc,id desc')->select()->toArray();

        if (!empty($level)) {

            foreach ($level as $value) {

                if ($time_long <= $value['time_long']) {

                    $coach_level = $value;

                } elseif (empty($coach_level)) {

                    $coach_level = $value;
                }

            }

        }

        return !empty($coach_level) ? $coach_level : '';

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-08 09:58
     * @功能说明:获取向导等级
     */
    public function getCoachLevel($caoch_id, $uniacid)
    {

        $config_model = new Config();

        $level_model = new CoachLevel();

        $config = $config_model->dataInfo(['uniacid' => $uniacid]);

        $level_cycle = $config['level_cycle'];

        $is_current = $config['is_current'];
        //时长(分钟)
        $time_long = $level_model->getMinTimeLong($caoch_id, $level_cycle, $is_current);
        //最低业绩
        $price = $level_model->getMinPrice($caoch_id, $level_cycle, 0, $is_current);
        //加钟订单
        $add_price = $level_model->getMinPrice($caoch_id, $level_cycle, 1, $is_current);
        //积分
        $integral = $level_model->getMinIntegral($caoch_id, $level_cycle, $is_current);
        //在线时长
        $online_time = $level_model->getCoachOnline($caoch_id, $level_cycle, $is_current);

        $level = $level_model->where(['uniacid' => $uniacid, 'status' => 1])->order('time_long,id desc')->select()->toArray();

        $coach_level = [];

        $add_balance = $price > 0 ? $add_price / $price * 100 : 0;

        if (!empty($level)) {

            foreach ($level as $key => $value) {
                //时长
                $level_time_long = $key > 0 ? $level[$key - 1]['time_long'] : 0;

                if ($time_long >= $level_time_long && $price >= $value['price'] && $add_balance >= $value['add_balance'] && $integral >= $value['integral'] && $online_time >= $value['online_time']) {

                    $coach_level = $value;

                } elseif (empty($coach_level)) {
                    //都不符合给一个最低都等级
                    $coach_level = $value;
                }

            }

        }

        return !empty($coach_level) ? $coach_level : [];
    }

    /**
     * @Desc: 获取技师比例
     * @param $cash_type
     * @param $coach_id
     * @param $uniacid
     * @return array|int|mixed
     * @Auther: shurong
     * @Time: 2023/12/8 20:31
     */
    public function getCoachBalance($cash_type, $coach_id, $uniacid)
    {
        if ($cash_type == 1) {
            //向导等级
            $coach_level = $this->getCoachLevel($coach_id, $uniacid);

            if (empty($coach_level) && !empty($coach_id)) {

                return ['code' => 300];
            }

            $balance = !empty($coach_level) ? $coach_level['balance'] : 0;
        } else {

            $coach = $this->dataInfo(['id' => $coach_id]);

            if ($coach) {

                $balance = $coach['cash_balance'];
            } else {
                $balance = 0;
            }
        }

        return $balance;
    }

    /**
     * @author chenniang
     * @DataTime: 2022-11-17 11:09
     * @功能说明:获取本期业绩
     */
    public function getCurrentAchievement($caoch_id, $uniacid)
    {

        $config_model = new Config();

        $level_model = new CoachLevel();

        $integral_model = new Integral();

        $config = $config_model->dataInfo(['uniacid' => $uniacid]);

        $level_cycle = $config['level_cycle'];
        //时长(分钟)
        $data['coach_time_long'] = $level_model->getMinTimeLong($caoch_id, $level_cycle, 1);
        //最低业绩
        $data['coach_price'] = $level_model->getMinPrice($caoch_id, $level_cycle, 0, 1);
        //加钟订单
        $data['coach_add_price'] = $level_model->getMinPrice($caoch_id, $level_cycle, 1, 1);
        //积分
//        $data['coach_integral'] = $level_model->getMinIntegral($caoch_id, $level_cycle, 1);
        $dis[] = ['status', '=', 1];
        $dis[] = ['coach_id', '=', $caoch_id];
        $data['coach_integral'] = $integral_model->where($dis)->where('type', '=', 0)->sum('integral');
        $data['coach_integral'] = round($data['coach_integral'], 2);

        //在线时长
        $data['online_time'] = $level_model->getCoachOnline($caoch_id, $level_cycle, 1);

        $data['coach_add_balance'] = $data['coach_price'] > 0 ? round($data['coach_add_price'] / $data['coach_price'] * 100, 2) : 0;;

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-04-01 09:51
     * @功能说明:公众号楼长端订单退款通知
     */
    public function refundSendMsgWeb($order)
    {

        $cap_model = new Coach();

        $cap_id = $order['coach_id'];

        $uniacid = $order['uniacid'];

        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['order_tmp_id'])) {

            return false;
        }
        //获取楼长openid
        $openid = $cap_model->capOpenid($cap_id, 2);

        $store_name = $cap_model->where(['id' => $cap_id])->value('coach_name');

        $wx_setting = new WxSetting($uniacid);

        $access_token = $wx_setting->getGzhToken();

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

        $order_model = new Order();

        $start_time = $order_model->where(['id' => $order['order_id']])->value('start_time');

        $key = explode('&', $x_config['cancel_tmp_id']);

        for ($i = 1; $i < 4; $i++) {

            $arr[$i] = !empty($key[$i]) ? $key[$i] : 'keyword' . $i;
        }

        $data = [
            //用户小程序openid
            'touser' => $openid,
            //公众号appid
            'appid' => $x_config['gzh_appid'],

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/technician/pages/order/detail?id=' . $order['order_id'],
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                'first' => array(

                    'value' => $store_name . '向导，您有一笔订单正在申请退款',

                    'color' => '#93c47d',
                ),

                $arr[1] => array(

                    'value' => implode(',', array_column($order['order_goods'], 'goods_name')),

                    'color' => '#93c47d',
                ),
                //预约时间
                $arr[2] => array(
                    //内容
                    'value' => date('Y-m-d H:i', $start_time),

                    'color' => '#0000ff',
                ),
                //取消原因
                $arr[3] => array(
                    //内容
                    'value' => $order['text'],

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


    /**
     * @param $order
     * @功能说明:发送模版消息
     * @author chenniang
     * @DataTime: 2022-06-01 15:15
     */
    public function refundSendMsg($order)
    {

        $coach_user_id = $this->where(['id' => $order['coach_id']])->value('user_id');

        $user_model = new User();

        $user_info = $user_model->dataInfo(['id' => $coach_user_id]);

        if (empty($user_info)) {

            return false;
        }
        //type 1小程序 2公众号
        $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

        if ($type == 1) {

            $res = $this->refundSendMsgWechat($order);

        } else {

            $res = $this->refundSendMsgWeb($order);


        }

        $this->sendShortMsg($order, 2);

        return $res;
    }


    /**
     * @author chenniang
     * @DataTime: 2021-04-01 09:51
     * @功能说明:公众号楼长端订单退款通知
     */
    public function refundSendMsgWechat($order)
    {

        $cap_model = new Coach();

        $cap_id = $order['coach_id'];

        $uniacid = $order['uniacid'];

        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['order_tmp_id'])) {

            return false;
        }

        $config = longbingGetAppConfig($uniacid);
        //获取楼长openid
        $openid = $cap_model->capOpenid($cap_id, 1);

        $store_name = $cap_model->where(['id' => $cap_id])->value('coach_name');

        $access_token = longbingGetAccessToken($uniacid);

        $page = "master/pages/order/list";
        //post地址
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token={$access_token}";

        $order_model = new Order();

        $start_time = $order_model->where(['id' => $order['order_id']])->value('start_time');

        $data = [
            //用户小程序openid
            'touser' => $openid,

            'mp_template_msg' => [
                //公众号appid
                'appid' => $x_config['gzh_appid'],

                "url" => "http://weixin.qq.com/download",
                //公众号模版id
                'template_id' => $x_config['cancel_tmp_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],
                'data' => array(

                    'first' => array(

                        'value' => $store_name . '向导，您有一笔订单正在申请退款',

                        'color' => '#93c47d',
                    ),

                    'keyword1' => array(

                        'value' => implode(',', array_column($order['order_goods'], 'goods_name')),

                        'color' => '#93c47d',
                    ),
                    //预约时间
                    'keyword2' => array(
                        //内容
                        'value' => date('Y-m-d H:i', $start_time),

                        'color' => '#0000ff',
                    ),
                    //取消原因
                    'keyword3' => array(
                        //内容
                        'value' => $order['text'],

                        'color' => '#0000ff',
                    ),

                )
            ],
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


    /**
     * @author chenniang
     * @DataTime: 2022-06-01 15:17
     * @功能说明:发送模版消息
     */
    public function paySendMsg($order)
    {

        $coach_user_id = $this->where(['id' => $order['coach_id']])->value('user_id');

        $user_model = new User();

        $user_info = $user_model->dataInfo(['id' => $coach_user_id]);

        if (empty($user_info)) {

            return false;
        }
        //type 1小程序 2公众号
        $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

        if ($type == 1) {

            $res = $this->paySendMsgWechat($order);

        } else {

            $res = $this->paySendMsgWeb($order);

        }


        $send_config_model = new SendMsgConfig();
        //给代理商和平台发送模版消息
        $send_config_model->sendOrderTmplAdmin($order);
        //发送短信
        $this->sendShortMsg($order);

        return $res;

    }

    /**
     * @Desc: 代理商发送模版消息
     * @param $order
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2023/10/20 18:29
     */
    public function paySendMsgAdmin($order)
    {
        $admin = (new Admin())->where(['id' => $order['admin_id']])->field('user_id,phone')->find();

        $user_info = (new User())->dataInfo(['id' => $admin['user_id']]);

        //type 1小程序 2公众号
        $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

        if ($type == 1) {

            $res = $this->paySendMsgWechat($order, $user_info['wechat_openid']);

        } else {

            $res = $this->paySendMsgWeb($order, $user_info['web_openid']);


        }

        //发送短信
        $config_model = new ShortCodeConfig();

        $config_model->sendSms($admin['phone'], $order['uniacid'], $order['order_code'], $type);

        return $res;
    }


    /**
     * @param $order
     * @功能说明:发送短信
     * @author chenniang
     * @DataTime: 2022-08-01 14:59
     */
    public function sendShortMsg($order, $type = 1)
    {

        $mobile = $this->where(['id' => $order['coach_id']])->value('mobile');

        $config_model = new ShortCodeConfig();

        $res = $config_model->sendSms($mobile, $order['uniacid'], $order['order_code'], $type);
        return $res;
    }

    /**
     * @author chenniang
     * @DataTime: 2021-04-01 09:51
     * @功能说明:公众号楼长端订单支付通知
     */
    public function paySendMsgWechat($order, $openid = '')
    {

        $cap_model = new Coach();

        $cap_id = $order['coach_id'];

        $uniacid = $order['uniacid'];

        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        $config = longbingGetAppConfig($uniacid);

        if (empty($x_config['gzh_appid']) || empty($x_config['order_tmp_id'])) {

            return false;
        }
        //获取楼长openid
        $openid = !empty($openid) ? $openid : $cap_model->capOpenid($cap_id, 1);

        $store_name = $cap_model->where(['id' => $cap_id])->value('coach_name');

//        $user_model = new User();
//
//        $mobile = $user_model->where(['id' => $order['user_id']])->value('phone');

        $access_token = longbingGetAccessToken($uniacid);

        $virtual_config_model = new \app\virtual\model\Config();

        $mobile_auth = $virtual_config_model->getVirtualAuth($uniacid);

        $page = "master/pages/order/list";
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
                'template_id' => $x_config['order_tmp_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],

                'data' => array(

                    'first' => array(

                        'value' => $store_name . '向导，您有一笔新的订单',

                        'color' => '#93c47d',
                    ),
                    //服务名称
                    'keyword1' => array(

                        'value' => implode(',', array_column($order['order_goods'], 'goods_name')),

                        'color' => '#93c47d',
                    ),
                    //下单人
                    'keyword2' => array(
                        //内容
                        'value' => $order['address_info']['user_name'],

                        'color' => '#0000ff',
                    ),
                    'keyword3' => array(
                        //内容
                        'value' => $mobile_auth == false ? $order['address_info']['mobile'] : '-',

                        'color' => '#0000ff',
                    ),
                    //客户电话
                    'keyword4' => array(
                        //内容
                        'value' => $order['order_code'],

                        'color' => '#0000ff',
                    ),
                    'keyword5' => array(
                        //内容
                        'value' => mb_substr($order['address_info']['address'], 0, 20, 'utf8'),

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

        $rest = json_decode($rest, true);

        return $rest;

    }

    /**
     * @author chenniang
     * @DataTime: 2021-04-01 09:51
     * @功能说明:公众号楼长端订单支付通知
     */
    public function paySendMsgWeb($order, $openid = '')
    {

        $cap_model = new Coach();

        $cap_id = $order['coach_id'];

        $uniacid = $order['uniacid'];

        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['order_tmp_id'])) {

            return false;
        }
        //获取楼长openid
        $openid = !empty($openid) ? $openid : $cap_model->capOpenid($cap_id, 2);

        $store_name = $cap_model->where(['id' => $cap_id])->value('coach_name');

        $wx_setting = new WxSetting($uniacid);

        $access_token = $wx_setting->getGzhToken();

//        $user_model = new User();

//        $mobile = $user_model->where(['id' => $order['user_id']])->value('phone');

        $virtual_config_model = new \app\virtual\model\Config();

        $mobile_auth = $virtual_config_model->getVirtualAuth($uniacid);

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

        $key = explode('&', $x_config['order_tmp_id']);

        for ($i = 1; $i < 6; $i++) {

            $arr[$i] = !empty($key[$i]) ? $key[$i] : 'keyword' . $i;
        }

        $data = [
            //用户小程序openid
            'touser' => $openid,
            //公众号appid
            'appid' => $x_config['gzh_appid'],

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/technician/pages/order/detail?id=' . $order['id'],
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                'first' => array(

                    'value' => $store_name . '向导，您有一笔新的订单',

                    'color' => '#93c47d',
                ),
                //服务名称
                $arr[1] => array(

                    'value' => implode(',', array_column($order['order_goods'], 'goods_name')),

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

        $rest = json_decode($rest, true);

        return $rest;

    }


    /**
     * @author chenniang
     * @DataTime: 2021-08-23 23:57
     * @功能说明:获取正在进行中的向导
     */
    public function getWorkingCoach($uniacid)
    {

        $dis[] = ['uniacid', '=', $uniacid];

        $dis[] = ['pay_type', 'in', [4, 5, 6]];

//        $dis[] = ['start_time', '<=', time()];

//        $dis[] = ['end_time', '>=', time()];

        $order = new Order();

        $data = $order->where($dis)->column('coach_id');

        return $data;
    }


    /**
     * @param $coach_id
     * @param $config
     * @功能说明:
     * @param  int  $type
     * @return false|float|int|mixed|string
     * @author chenniang
     * @DataTime: 2022-10-20 18:50
     */
    public function getCoachEarliestTime($coach_id, $config, $type = 0)
    {
        $key = $type . 'getCoachEarliestTime' . $coach_id;
        $data = getCache($key, $config['uniacid']);
        if (!empty($data) && $type == 0) {
            return $data;
        }
        $coach_model = new Coach();
        $coach = $coach_model->dataInfo(['id' => $coach_id]);
        $i = 0;
        while ($i < $config['max_day'] && empty($data)) {
            $time = $i * 86400;
            $data = $this->getCoachEarliestTimeData($coach, $config, $time, $type);
            $i++;
        }
        if (!empty($data)) {
            setCache($key, $data, 10, $config['uniacid']);
        }
        return $data;
    }


    /**
     * @param $coach_id
     * @param $config
     * @param int $time
     * @功能说明:
     * @author chenniang
     * @DataTime: 2022-10-20 18:50
     */
    public function getCoachEarliestTimeData($coach, $config, $time = 0, $time_style = 0)
    {
        $tt = time();
        $order_model = new Order();
        if (empty($coach['start_time'])) {
            return '';
        }
        $coach_id = $coach['id'];
        $end_time = strtotime($coach['end_time']);
        $start_time = strtotime($coach['start_time']);
        $is_eve = $end_time == $start_time || $end_time - $start_time == 86400 ? 1 : 0;
        //跨日
        if ($end_time <= $start_time) {
            //查看此时处于上一个周期还是这个周期
            //上一个周期
            if ($tt < $end_time) {
                $start_time -= 86400;
            } else {
                //当前周期
                $end_time += 86400;
            }
        }
        $max_day = strtotime(date('Y-m-d', time())) + $config['max_day'] * 86400;
        $start_time += $time;
        $end_time += $time;
        $rest_arr = $this->getCoachRestTime($coach, $start_time, $end_time, $config);
        $i = 0;
        $time = $start_time;
        $where = [];
        $where[] = ['coach_id', '=', $coach_id];
        $where[] = ['end_time', '>=', $time];
        $where[] = ['pay_type', 'not in', [-1, 7]];
        $order = $order_model->where($where)->field('start_time,end_time,order_end_time,pay_type')->order('start_time,end_time')->select()->toArray();
        $time_interval = $config['time_interval'] > 0 ? $config['time_interval'] * 60 - 1 : 0;
        while ($time < $end_time) {
            $time = $start_time + $config['time_unit'] * $i * 60;
            $max_time = $time + $config['time_unit'] * 60 - 1;
            $status = 1;
            if ($time - $time_interval <= time()) {
                $status = 0;
            }
            $time_text = $time_style == 0 ? date('H:i', $time) : $time;
            if (!empty($order)) {
                foreach ($order as $value) {
                    //查询订单
                    $res = is_time_crossV2($time, $max_time, $value['start_time'] - $time_interval, $value['end_time'] + $time_interval);
                    if ($res == false) {
                        $status = 0;
                    }
                }
            }
            if (!empty($rest_arr) && $status == 1) {
                $res = $order_model->checkCoachRestTime($rest_arr, $time, $max_time);
                if (!empty($res['code'])) {
                    $status = 0;
                }
            }
            if (empty($is_eve)) {
                $status = $time == $end_time ? 0 : $status;
            }
            $status = $time < $tt || $time > $end_time ? 0 : $status;
            if ($time >= $max_day) {
                $status = 0;
            }
            if ($status == 1) {
                return $time_text;
            }
            $i++;
        }
        return '';
    }


    /**
     * @author chenniang
     * @DataTime: 2022-10-19 23:03
     * @功能说明:获取向导休息时间
     */
    public function getCoachRestTime($coach, $start_time, $end_time, $config)
    {
        $where = [
            'start_time' => $coach['start_time'],
            'end_time' => $coach['end_time'],
            'coach_id' => $coach['id'],
            'max_day' => $config['max_day'],
            'time_unit' => $config['time_unit']
        ];
        $start_date = date('Y-m-d', $start_time);
        $end_date = date('Y-m-d', $end_time);
        $list = Db::name('massage_service_coach_time')->where($where)->where('date', 'in', [$start_date, $end_date])->select();
        if (!empty($list)) {
            foreach ($list as $value) {
                $info = json_decode($value['info'], true);
                foreach ($info as $vs) {
                    if ($vs['status'] == 0 && $vs['is_click'] == 1) {
                        $vs['time_str_end'] = $vs['time_str'] + $config['time_unit'] * 60;
                        $arr[] = $vs;
                    }
                }
            }
        }
        return !empty($arr) ? $arr : [];
    }

    /**
     * @author chenniang
     * @DataTime: 2021-08-24 18:06
     * @功能说明:获取教练最早可预约时间
     */
    public function getCoachEarliestTimeV2($coach_id, $config)
    {


        $coach_model = new Coach();

        $coach = $coach_model->dataInfo(['id' => $coach_id]);

        if (empty($coach['start_time'])) {

            return '';
        }

        $min_time = strtotime($coach['start_time']) > time() ? strtotime($coach['start_time']) : time();

        $dis[] = ['coach_id', '=', $coach_id];

        $dis[] = ['pay_type', 'in', [2, 3, 4, 5, 6]];

        $dis[] = ['start_time', '<=', $min_time];

        $dis[] = ['end_time', '>=', $min_time];

        $order = new Order();

        $data = $order->dataInfo($dis);

        if (!empty($data)) {

            return date('H:i', $data['end_time']);
        }
        $now_time = strtotime(date('H', time()));
        //整点
        if ($config['time_unit'] == 60) {

            return date('H:i', $now_time + 3600);
        }
        //查看是在上半时还是下半时
        $y = time() - $now_time;
        //下半时
        if ($y > 1800) {

            return date('H:i', $now_time + 1800);

        } else {

            return date('H:i', $now_time + 3600);

        }

    }


    /**
     * @author chenniang
     * @DataTime: 2021-12-07 15:18
     * @功能说明:认证未认证的教练 通过电话号码
     */
    public function attestationCoach($user)
    {

        if (!empty($user['phone'])) {

            $dis = [

                'user_id' => 0,

                'mobile' => $user['phone']
            ];

            $this->dataUpdate($dis, ['user_id' => $user['id']]);

        }

        return true;
    }


    /**
     * @author chenniang
     * @DataTime: 2021-04-01 09:51
     * @功能说明:向导修改结果通知
     */
    public function updateTmpWechat($cap_id, $uniacid, $type, $sh_text)
    {

        $cap_model = new Coach();

        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['coachupdate_tmp_id'])) {

            return false;
        }

        $config = longbingGetAppConfig($uniacid);
        //获取楼长openid
        $openid = $cap_model->capOpenid($cap_id, 1);

        $access_token = longbingGetAccessToken($uniacid);

        $page = "pages/mine?type=2";
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
                'template_id' => $x_config['coachupdate_tmp_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],

                'data' => array(

                    'first' => array(

                        'value' => '向导修改审核通知',

                        'color' => '#93c47d',
                    ),
                    //服务名称
                    'keyword1' => array(

                        'value' => '平台',

                        'color' => '#93c47d',
                    ),
                    'keyword2' => array(

                        'value' => $type == 2 ? '通过 ' . $sh_text : '未通过 ' . $sh_text,

                        'color' => '#93c47d',
                    ),
                    'keyword3' => array(

                        'value' => date('Y-m-d H:i', time()),

                        'color' => '#93c47d',
                    )

                )

            ]

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


    /**
     * @author chenniang
     * @DataTime: 2021-04-01 09:51
     * @功能说明:向导修改结果通知
     */
    public function updateTmpWeb($cap_id, $uniacid, $type, $sh_text)
    {

        $cap_model = new Coach();

        $config_model = new SendMsgConfig();

        $config_model->initData($uniacid);

        $x_config = $config_model->dataInfo(['uniacid' => $uniacid]);

        if (empty($x_config['gzh_appid']) || empty($x_config['coachupdate_tmp_id'])) {

            return false;
        }
        //获取楼长openid
        $openid = $cap_model->capOpenid($cap_id, 2);

        $wx_setting = new WxSetting($uniacid);

        $access_token = $wx_setting->getGzhToken();

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

        $key = explode('&', $x_config['coachupdate_tmp_id']);

        for ($i = 1; $i < 4; $i++) {

            $arr[$i] = !empty($key[$i]) ? $key[$i] : 'keyword' . $i;
        }

        $data = [
            //用户小程序openid
            'touser' => $openid,
            //公众号appid
            'appid' => $x_config['gzh_appid'],

//            "url"   => "http://weixin.qq.com/download",
            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/pages/mine?type=2',
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                'first' => array(

                    'value' => '向导修改审核通知',

                    'color' => '#93c47d',
                ),
                //服务名称
                $arr[1] => array(

                    'value' => '平台',

                    'color' => '#93c47d',
                ),
                $arr[2] => array(

                    'value' => $type == 2 ? '通过 ' . $sh_text : '未通过 ' . $sh_text,

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

        $rest = json_decode($rest, true);

        return $rest;

    }


    /**
     * @author chenniang
     * @DataTime: 2022-06-01 15:17
     * @功能说明:发送模版消息
     */
    public function updateSendMsg($coach_id, $status, $sh_text)
    {

        $coach_user_id = $this->where(['id' => $coach_id])->value('user_id');

        $user_model = new User();

        $user_info = $user_model->dataInfo(['id' => $coach_user_id]);

        if (empty($user_info)) {

            return false;
        }
        //type 1小程序 2公众号
        $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

        if ($type == 1) {

            $res = $this->updateTmpWechat($coach_id, $user_info['uniacid'], $status, $sh_text);

        } else {

            $res = $this->updateTmpWeb($coach_id, $user_info['uniacid'], $status, $sh_text);

        }

        return $res;

    }

    /**
     * 时间管理
     * @param $data
     * @return bool
     */
    public static function timeEdit($data)
    {
        $update = [
            'is_work' => $data['is_work'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ];
        $config_model = new Config();
        $config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);
        $insert = [];
        foreach ($data['time_text'] as $item) {
            $time = $item['sub'];
            $hours = 0;
            foreach ($time as $ti) {
                if ($ti['status'] == 1) {
                    $hours += 1;
                }
            }
            if (!empty($item['sub'])) {
                $insert[] = [
                    'coach_id' => $data['coach_id'],
                    'date' => date('Y-m-d', $item['dat_str']),
                    'info' => $item['sub'],
                    'hours' => $hours * ($data['time_unit'] / 60),
                    'uniacid' => $data['uniacid'],
                    'create_time' => time(),
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'max_day' => $config['max_day'],
                    'time_unit' => $data['time_unit']
                ];
            }
        }
        Db::startTrans();
        try {
            $date = array_column($insert, 'date');
            $ids = CoachTime::where(['coach_id' => $data['coach_id']])->whereIn('date', $date)->column('id');
            if ($ids) {
                CoachTime::whereIn('id', $ids)->delete();
                CoachTimeList::whereIn('time_id', $ids)->delete();
            }
            $res = self::update($update, ['id' => $data['coach_id']]);
            if ($res === false) {
                throw new \Exception();
            }
            if ($insert) {
                foreach ($insert as $item) {
                    $info = $item['info'];
                    $item['info'] = json_encode($item['info']);
                    $id = CoachTime::insertGetId($item);
                    $list_insert = [];
                    foreach ($info as $value) {
                        $list_insert[] = [
                            'uniacid' => $data['uniacid'],
                            'coach_id' => $data['coach_id'],
                            'time_id' => $id,
                            "time_str" => $value['time_str'],
                            "time_str_end" => (int)$value['time_str'] + ($item['time_unit'] * 60),
                            "time_text" => $value['time_text'],
                            "time_texts" => $value['time_texts'],
                            "status" => $value['status'],
                            'create_time' => time(),
                            'is_click' => $value['is_click']
                        ];
                    }
                    $res = CoachTimeList::insertAll($list_insert);
                    if (!$res) {
                        throw new \Exception();
                    }
                }
            }
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * 获取不接单的向导
     * @param $uniacid
     * @return array
     */
    public function getCancelCoach($uniacid)
    {
        return $this->where(['is_work' => 0, 'uniacid' => $uniacid])->column('id');
    }


    /**
     * @author chenniang
     * @DataTime: 2022-11-08 18:27
     * @功能说明:
     */
    public function coachLevelInfo($coach_level)
    {

        if (!empty($coach_level)) {

            $time_long = $coach_level['time_long'];

            $level_model = new CoachLevel();

            $dis = [

                'uniacid' => $coach_level['uniacid'],

                'status' => 1
            ];
            //查下个等级
            $next_level = $level_model->where($dis)->where('time_long', '>', $time_long)->order('time_long,id desc')->find();

            if (!empty($next_level)) {

                $next_level = $next_level->toArray();

                if ($next_level['top'] <= $coach_level['top']) {
                    //相差时间
                    $coach_level['differ_time_long'] = 0;
                    //相差业绩
                    $coach_level['differ_price'] = 0;
                    //相差积分
                    $coach_level['differ_integral'] = 0;
                    //还差加钟
                    $coach_level['differ_add_price'] = 0;

                    $coach_level['differ_online_time'] = 0;

                } else {

                    $min_time_long = $level_model->where($dis)->where('time_long', '<', $time_long)->max('time_long');

                    $min_time_long = !empty($min_time_long) ? $min_time_long : 0;
                    //相差时间
                    $coach_level['differ_time_long'] = $coach_level['time_long'] - $min_time_long > 0 ? $coach_level['time_long'] - $min_time_long : 0;
                    //相差业绩
                    $coach_level['differ_price'] = $next_level['price'] - $coach_level['price'] > 0 ? $next_level['price'] - $coach_level['price'] : 0;
                    //相差积分
                    $coach_level['differ_integral'] = $next_level['integral'] - $coach_level['integral'] > 0 ? $next_level['integral'] - $coach_level['integral'] : 0;
                    //相差在线时长
                    $coach_level['differ_online_time'] = $next_level['online_time'] - $coach_level['online_time'] > 0 ? $next_level['online_time'] - $coach_level['online_time'] : 0;
                    //加钟金额
                    $next_add_price = $next_level['price'] * $next_level['add_balance'] / 100;

                    $now_add_price = $coach_level['price'] * $coach_level['add_balance'] / 100;
                    //还差加钟
                    $coach_level['differ_add_price'] = $next_add_price - $now_add_price > 0 ? round($next_add_price - $now_add_price, 2) : 0;
                }
                //下一等级
                $coach_level['next_level_title'] = $next_level['title'];
            }
        }

        return $coach_level;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-01-29 14:48
     * @功能说明:获取向导工作状态
     */
    public function getCoachWorkStatus($coach_id, $uniacid)
    {

        $config_model = new ConfigSetting();

        $config = $config_model->dataInfo($uniacid);
        //服务中
        $working_coach = $this->getWorkingCoach($uniacid);
        //当前时间不可预约
        $cannot = CoachTimeList::getCannotCoach($uniacid);

        $coach = $this->dataInfo(['id' => $coach_id]);

        $config_model = new Config();

        $configs = $config_model->dataInfo(['uniacid' => $uniacid]);

        $time = $this->getCoachEarliestTime($coach_id, $configs);

        if ($config['coach_format'] == 1) {

            $cannot = array_diff($cannot, $working_coach);

            if (in_array($coach_id, $working_coach)) {

                $text_type = 2;

            } elseif (empty($time) || $coach['is_work'] == 0) {

                $text_type = 4;

            } elseif (!in_array($coach_id, $cannot)) {

                $text_type = 1;

            } else {

                $text_type = 3;
            }

        } else {

            $working_coach = array_merge($working_coach, $cannot);

            $this->where('id', 'not in', $working_coach)->update(['index_top' => 1]);

            $this->where('id', 'in', $working_coach)->update(['index_top' => 0]);

            if ($coach['is_work'] == 0 || empty($time)) {

                $text_type = 4;

            } elseif ($coach['index_top'] == 1) {

                $text_type = 1;

            } else {

                $text_type = 3;
            }
        }

        if ($coach['is_work'] == 0) {

            $text_type = 4;

        }

        return $text_type;

    }


    /**
     * @param $user_id
     * @功能说明:获取屏蔽的向导
     * @author chenniang
     * @DataTime: 2023-01-30 17:42
     */
    public function getShieldCoach($user_id)
    {

        $shield_model = new ShieldList();

        $dis = [

            'user_id' => $user_id,

        ];

        $coach_id = $shield_model->where($dis)->where('type', 'in', [2, 3])->column('coach_id');

        return $coach_id;

    }


    /**
     * @param $order
     * @功能说明:获取订单里想关联服务的向导
     * @author chenniang
     * @DataTime: 2023-02-28 16:19
     */
    public function getOrderServiceCoach($order)
    {

        $service_model = new ServiceCoach();

        $data = [];

        foreach ($order['order_goods'] as $k => $v) {

            $arr = $service_model->where(['ser_id' => $v['goods_id']])->column('coach_id');

            if ($k == 0) {

                $data = $arr;

            } else {

                $data = array_intersect($arr, $data);
            }

        }

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-03 10:53
     * @功能说明:设置可服务的向导
     */
    public function setIndexTopCoach($uniacid)
    {

        $key = 'indexTopCoach_key';

        $value = getCache($key, $uniacid);

        if (!empty($value)) {

            return true;
        }
        //服务中
        $working_coach = $this->getWorkingCoach($uniacid);
        //当前时间不可预约
        $cannot = CoachTimeList::getCannotCoach($uniacid);

        $working_coach = array_merge($working_coach, $cannot);

        $this->where('id', 'in', $working_coach)->update(['index_top' => 0]);

        $this->where('id', 'not in', $working_coach)->update(['index_top' => 1]);

        setCache($key, 1, 5, $uniacid);

        return true;

    }


    /**
     * @param $coach
     * @功能说明:获取合伙人分销比例
     * @author chenniang
     * @DataTime: 2023-03-22 13:52
     */
    public function partnerBalance($coach, $order)
    {

        $balance = 0;

        if ($coach['agent_type'] == 2 && !empty($coach['partner_id'])) {

            $user_model = new User();

            $user = $user_model->dataInfo(['id' => $coach['partner_id']]);

            if (!empty($user)) {

                $order['partner_id'] = $coach['partner_id'];

                $balance = getConfigSetting($coach['uniacid'], 'coach_agent_balance');
            }

        }

        $order['coach_agent_balance'] = $balance;

        return $order;
    }

    /**
     * 排序
     * @param $uniacid
     * @param $working_coach
     * @param $cannot
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function indexListUpdate($uniacid, $working_coach, $cannot)
    {
        $key = 'set_coach_index';
        $value = getCache($key);
        if ($value == false) {
            setCache($key, 1, 10);
            //兼容老数据
            if (self::where(['uniacid' => $uniacid, 'start_time' => ''])->count() > 0) {
                self::where(['uniacid' => $uniacid, 'start_time' => ''])->update(['is_work' => 0]);
            }
            //处理排序
            $data = self::where(['uniacid' => $uniacid, 'status' => 2])->select()->toArray();
            if ($data) {
                $type1 = $type2 = $type3 = $type4 = [];
//                $config_model = new Config();
//                $config = $config_model->dataInfo(['uniacid' => $uniacid]);
                foreach ($data as $v) {
//                    $v['near_time'] = $this->getCoachEarliestTime($v['id'], $config);
                    if (in_array($v['id'], $working_coach)) {
                        $type2[] = $v['id'];
                    } elseif ($v['is_work'] == 0) {//empty($v['near_time']) ||
                        $type4[] = $v['id'];
                    } elseif (!in_array($v['id'], $cannot)) {
                        $type1[] = $v['id'];
                    } else {
                        $type3[] = $v['id'];
                    }
                }
                if ($type1) {
                    $this->whereIn('id', $type1)->update(['coach_index' => 1]);
                }
                if ($type2) {
                    $this->whereIn('id', $type2)->update(['coach_index' => 2]);
                }
                if ($type3) {
                    $this->whereIn('id', $type3)->update(['coach_index' => 3]);
                }
                if ($type4) {
                    $this->whereIn('id', $type4)->update(['coach_index' => 4]);
                }
            }
        }
    }

    /**
     * @Desc: 获取价格区间id
     * @param $uniacid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/10/20 9:59
     */
    public static function getMinPriceId($uniacid, $input)
    {
        //处理价格
        $data = self::where('uniacid', $uniacid)->select()->toArray();
        $ids = array_column($data, 'id');
        $arr = Db::name('massage_service_service_coach')
            ->field(['a.coach_id', 'TRIM(TRAILING "." FROM TRIM(TRAILING "0" FROM min(a.price))) as price'])
            ->alias('a')
            ->join('massage_service_service_list b', 'a.ser_id = b.id', 'left')
            ->whereIn('a.coach_id', $ids)
            ->where('b.status', 1)
            ->group('a.coach_id')
            ->select()
            ->toArray();
        $id_data = [];
        foreach ($data as &$datum) {
            $datum['price'] = 0;
            foreach ($arr as $item) {
                if ($datum['id'] == $item['coach_id']) {
                    $datum['price'] = $item['price'];
                }
            }
            if ((isset($input['min_price']) && isset($input['max_price'])) && $datum['price'] >= $input['min_price'] && $datum['price'] <= $input['max_price']) {
                $id_data[] = $datum['id'];
            } elseif (isset($input['max_price']) && $datum['price'] <= $input['max_price'] && !isset($input['min_price'])) {
                $id_data[] = $datum['id'];
            } elseif (isset($input['min_price']) && $datum['price'] >= $input['min_price'] && !isset($input['max_price'])) {
                $id_data[] = $datum['id'];
            }
        }
        return $id_data;
    }

    /**
     * 报名通知
     * @param $data
     * @return bool|mixed|string|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function applySendMsg($data)
    {
        $order = DemandOrder::where('id', $data['order_id'])->find();
        $user_model = new User();

        $user_info = $user_model->dataInfo(['id' => $order['user_id']]);

        if (empty($user_info)) {

            return false;
        }
        $data['user_id'] = $order['user_id'];
        $data['time'] = date('Y-m-d H:i:s');
        //type 1小程序 2公众号
        $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

        if ($type == 1) {

            $res = self::applySendMsgWechat($data);

        } else {

            $res = self::applySendMsgWeb($data);

        }

        return $res;
    }

    /**
     * 报名通知
     * @param $data
     * @return bool|mixed|string
     */
    public static function applySendMsgWechat($data)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['apply_tmp_id'])) {

            return false;
        }

        $config = longbingGetAppConfig($data['uniacid']);

        $openid = User::where('id', $data['user_id'])->value('wechat_openid');

        $page = "find/pages/invitation/list";

        $access_token = longbingGetAccessToken($data['uniacid']);

        //post地址
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token={$access_token}";

        $coach_name = self::where('id', $data['coach_id'])->value('coach_name');

        $arr = [
            //用户小程序openid
            'touser' => $openid,

            'mp_template_msg' => [
                //公众号appid
                'appid' => $x_config['gzh_appid'],

                "url" => "http://weixin.qq.com/download",
                //公众号模版id
                'template_id' => $x_config['apply_tmp_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],

                'data' => array(
                    //申请人
                    'thing2' => array(

                        'value' => $coach_name,

                        'color' => '#0000ff',
                    ),
                    //申请时间
                    'time3' => array(
                        //内容
                        'value' => $data['time'],

                        'color' => '#0000ff',
                    ),

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
     * 报名公众号通知
     * @param $data
     * @return bool|mixed|string
     */
    public static function applySendMsgWeb($data)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['apply_tmp_id'])) {

            return false;
        }
        $openid = User::where('id', $data['user_id'])->value('web_openid');

        $coach_name = self::where('id', $data['coach_id'])->value('coach_name');

        $key = explode('&', $x_config['apply_tmp_id']);
        $arr = [
            1 => 'thing2',
            2 => 'time3'
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

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/find/pages/invitation/list?userPageType=1',
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                //申请人
                $arr[1] => array(

                    'value' => $coach_name,

                    'color' => '#0000ff',
                ),
                //申请时间
                $arr[2] => array(
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
     * @Desc: 经纪人向导列表
     * @param $where
     * @param $limit
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/8 14:46
     */
    public static function getBrokerCoach($where, $limit)
    {
        return self::alias('a')
            ->field('a.id,a.coach_name,a.work_img,b.agent_name,c.title,a.nickName,d.name')
            ->where($where)
            ->leftJoin('shequshop_school_admin b', 'a.admin_id = b.id and b.status = 1')
            ->leftJoin('massage_service_city_list c', 'a.city_id = c.id and c.status = 1')
            ->leftJoin('massage_broker_list d', 'a.broker_id = d.id')
            ->order('a.create_time')
            ->paginate($limit)
            ->toArray();
    }

    /**
     * @Desc: 抢单通知
     * @param $data
     * @return false|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/4/1 15:25
     */
    public static function robSendMsg($data)
    {
        $config_model = new SendMsgConfig();

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['demand_coach_rob_tmpl_id'])) {

            return false;
        }

        $where = [
            ['uniacid', '=', $data['uniacid']],
            ['is_work', '=', 1],
            ['status', '=', 2],
            ['user_id', '>', 0]
        ];


        $coach = Coach::where($where)->select()->toArray();

        if ($coach) {
            $user_model = new User();

            foreach ($coach as $coa) {

                $user_info = $user_model->dataInfo(['id' => $coa['user_id']]);

                if (empty($user_info)) {

                    continue;
                }

                $data['user_id'] = $coa['user_id'];
                //type 1小程序 2公众号
                $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

                if ($type == 1) {

                    $res = self::robSendMsgWechat($data);

                } else {

                    $res = self::robSendMsgWeb($data);

                }

            }
        }
        return $res;
    }

    /**抢单通知-小程序
     * @Desc:
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/1 15:15
     */
    public static function robSendMsgWechat($data)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['demand_coach_rob_tmpl_id'])) {

            return false;
        }

        $config = longbingGetAppConfig($data['uniacid']);

        $openid = User::where('id', $data['user_id'])->value('wechat_openid');

        $page = "find/pages/invitation/list";

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
                'template_id' => $x_config['demand_coach_rob_tmpl_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],

                'data' => array(
                    //订单号
                    'character_string2' => array(

                        'value' => $data['order_code'],

                        'color' => '#0000ff',
                    ),
                    //类型
                    'thing3' => array(
                        //内容
                        'value' => $data['order_type'],

                        'color' => '#0000ff',
                    ),
                    //金额
                    'amount5' => array(
                        //内容
                        'value' => $data['money'],

                        'color' => '#0000ff',
                    ),
                    //时间
                    'time6' => array(
                        //内容
                        'value' => $data['time'],

                        'color' => '#0000ff',
                    ),
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
     * @Desc: 抢单通知-公众号
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/1 15:18
     */
    public static function robSendMsgWeb($data)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['demand_coach_rob_tmpl_id'])) {

            return false;
        }
        $openid = User::where('id', $data['user_id'])->value('web_openid');

        $key = explode('&', $x_config['demand_coach_rob_tmpl_id']);

        $arr = [
            1 => 'character_string2',
            2 => 'thing3',
            3 => 'amount5',
            4 => 'time6',
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

            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/h5/?#/pages/find',
            //公众号模版id
            'template_id' => $key[0],

            'data' => array(

                //订单号
                $arr[1] => array(

                    'value' => $data['order_code'],

                    'color' => '#0000ff',
                ),
                //类型
                $arr[2] => array(
                    //内容
                    'value' => $data['order_type'],

                    'color' => '#0000ff',
                ),
                //金额
                $arr[3] => array(
                    //内容
                    'value' => $data['money'],

                    'color' => '#0000ff',
                ),
                //时间
                $arr[4] => array(
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
     * @Desc: 售后信息推送给陪玩官
     * @param $data
     * @return false|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/4/7 10:19
     */
    public static function refundOrderSendMsg($data, $type = 1)
    {

        $config_model = new SendMsgConfig();

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['refund_apply_coach_tmpl_id'])) {

            return false;
        }

        $coach = Coach::find($data['coach_id']);

        if ($coach) {

            $user_model = new User();

            $user_info = $user_model->dataInfo(['id' => $coach['user_id']]);

            if (empty($user_info)) {

                return false;

            }

            if ($data['status'] == '申请中') {

                $page = 'find/pages/invitation/detail?id=' . $data['order_id'] . '&userPageType=2';
            } else {

                $page = $type == 1 ? 'user/pages/refund/detail?id=' . $data['order_id'] . '&is_coach=1' : 'find/pages/invitation/detail?id=' . $data['order_id'] . '&userPageType=2';
            }

            $data['user_id'] = $coach['user_id'];
            //type 1小程序 2公众号
            $type = $user_info['last_login_type'] == 0 && !empty($user_info['wechat_openid']) ? 1 : 2;

            if ($type == 1) {

                $res = self::refundOrderSendMsgWechat($data, $page);

            } else {

                $res = self::refundOrderSendMsgWeb($data, $page);

            }
        }
        return $res;
    }

    /**
     * @Desc: 售后信息推送给陪玩官-小程序
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/7 10:14
     */
    public static function refundOrderSendMsgWechat($data, $page)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['refund_apply_coach_tmpl_id'])) {

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
                'template_id' => $x_config['refund_apply_coach_tmpl_id'],

                'miniprogram' => [
                    //小程序appid
                    'appid' => $config['appid'],
                    //跳转小程序地址
                    'page' => $page,
                ],

                'data' => array(
                    //订单号
                    'character_string8' => array(

                        'value' => $data['order_code'],

                        'color' => '#0000ff',
                    ),
                    //退款金额
                    'amount5' => array(
                        //内容
                        'value' => $data['money'],

                        'color' => '#0000ff',
                    ),
                    //时间
                    'time9' => array(
                        //内容
                        'value' => $data['time'],

                        'color' => '#0000ff',
                    ),
                    //状态
                    'const14' => array(
                        //内容
                        'value' => $data['status'],

                        'color' => '#0000ff',
                    ),
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
     * @Desc: 售后信息推送给陪玩官-公众号
     * @param $data
     * @return false|mixed
     * @Auther: shurong
     * @Time: 2024/4/7 10:10
     */
    public static function refundOrderSendMsgWeb($data, $page)
    {
        $config_model = new SendMsgConfig();

        $config_model->initData($data['uniacid']);

        $x_config = $config_model->dataInfo(['uniacid' => $data['uniacid']]);

        if (empty($x_config['gzh_appid']) || empty($x_config['refund_apply_coach_tmpl_id'])) {

            return false;
        }
        $openid = User::where('id', $data['user_id'])->value('web_openid');

        $key = explode('&', $x_config['refund_apply_coach_tmpl_id']);

        $arr = [
            1 => 'character_string8',
            2 => 'amount5',
            3 => 'time9',
            4 => 'const14',
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
                //退款金额
                $arr[2] => array(
                    //内容
                    'value' => $data['money'],

                    'color' => '#0000ff',
                ),
                //时间
                $arr[3] => array(
                    //内容
                    'value' => $data['time'],

                    'color' => '#0000ff',
                ),
                //状态
                $arr[4] => array(
                    //内容
                    'value' => $data['status'],

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
}