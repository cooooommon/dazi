<?php

namespace app\massage\controller;

use app\ApiRest;

use app\massage\model\ArticleList;
use app\massage\model\City;
use app\massage\model\Coach;
use app\massage\model\CoachCollect;
use app\massage\model\CoachTimeList;
use app\massage\model\Comment;
use app\massage\model\ConfigSetting;
use app\massage\model\Coupon;
use app\massage\model\CouponRecord;
use app\massage\model\DemandOrder;
use app\massage\model\DemandOrderApply;
use app\massage\model\DemandType;
use app\massage\model\Diy;
use app\massage\model\EntryAgreement;
use app\massage\model\JoinForm;
use app\massage\model\MassageConfig;
use app\massage\model\Order;
use app\massage\model\PayConfig;
use app\massage\model\Service;
use app\massage\model\ServiceCoach;
use app\massage\model\ServiceType;
use app\massage\model\ShortCodeConfig;
use app\massage\model\StoreList;
use app\member\model\MemberConfig;
use app\Rest;


use app\massage\model\Banner;

use app\massage\model\Car;
use app\massage\model\Config;

use app\massage\model\User;
use log\LogUtils;
use longbingcore\permissions\AdminMenu;
use think\App;

use think\facade\Db;
use think\Request;


class Index extends ApiRest
{

    protected $model;

    protected $article_model;

    protected $coach_model;

    protected $banner_model;

    protected $car_model;


    public function __construct(App $app)
    {

        parent::__construct($app);

        $this->model = new Service();

        $this->banner_model = new Banner();

        $this->car_model = new Car();

        $this->coach_model = new Coach();

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-23 09:20
     * @功能说明:首页
     */
    public function index()
    {

        $input = $this->_param;

        $dis = [

            'uniacid' => $this->_uniacid,

            'status' => 1
        ];
        $data['banner'] = $this->banner_model->where($dis)->field('id,img,link,type_id,connect_type,banner_type,video_url')->order('top desc,id desc')->select()->toArray();
        //判断插件权限没有返回空
        $auth = AdminMenu::getAuthList((int)$this->_uniacid, ['recommend']);

        if (!empty($auth['recommend']) || $auth['recommend'] == true) {

            $where[] = ['a.uniacid', '=', $this->_uniacid];

            $where[] = ['a.status', '=', 2];

            $where[] = ['a.is_work', '=', 1];

            $where[] = ['a.recommend', '=', 1];

//            $where[] = ['a.user_id', '>', 0];

            if (!empty($input['city_id'])) {

                $where[] = ['a.city_id', '=', $input['city_id']];
            }

            if (!empty($this->getUserId())) {

                $shield_coach = $this->coach_model->getShieldCoach($this->getUserId());

                if (!empty($shield_coach)) {

                    $where[] = ['a.id', 'not in', $shield_coach];
                }
            }

            $lat = !empty($input['lat']) ? $input['lat'] : 0;

            $lng = !empty($input['lng']) ? $input['lng'] : 0;

            $alh = 'ACOS(SIN((' . $lat . ' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS((' . $lat . ' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS((' . $lng . ' * 3.1415) / 180 - (lng * 3.1415) / 180 ) ) * 6378.137*1000 as distance';

            if (!empty($input['name'])) {
                $where[] = ['a.coach_name', 'like', '%' . $input['name'] . '%'];
            }

            $page = $this->request->param('limit', 10);

            $list = $this->coach_model->coachRecommendSelect($where, $alh, $page);

            if (!empty($list)) {

                $order_model = new Order();
                //最近七天注册
                $seven = $this->model->getSaleTopSeven($this->_uniacid);

                //服务中
                $working_coach = $this->coach_model->getWorkingCoach($this->_uniacid);
                //当前时间不可预约
                $cannot = CoachTimeList::getCannotCoach($this->_uniacid);

//                $working_coach = array_diff($working_coach, $cannot);
                $config_model = new Config();
                $config = $config_model->dataInfo(['uniacid' => $this->_uniacid]);
                foreach ($list['data'] as &$v) {
                    //是否是新人
                    $v['is_new'] = in_array($v['id'], $seven) ? 1 : 0;
                    //近30天单量
                    $v['order_count'] = $order_model->where(['coach_id' => $v['id'], 'pay_type' => 7])->whereTime('create_time', '-30 days')->count();
                    $v['near_time'] = $this->coach_model->getCoachEarliestTime($v['id'], $config);

                    $v['age'] = getAge($v['birthday']);

//                    $price = ServiceCoach::getMinPrice($v['id']);
//                    $v['price'] = $price ?? 0;

                    if (in_array($v['id'], $working_coach)) {

                        $text_type = 2;

                    } elseif (empty($v['near_time'])) {

                        $text_type = 4;

                    } elseif (!in_array($v['id'], $cannot)) {

                        $text_type = 1;

                    } else {

                        $text_type = 3;
                    }

                    $v['text_type'] = $text_type;
                }

            }

            $config_model = new ConfigSetting();

            $config = $config_model->dataInfo($this->_uniacid);

            $data['recommend_style'] = $config['recommend_style'];

            $data['recommend_list'] = $list;

        } else {
            $config_model = new ConfigSetting();

            $config = $config_model->dataInfo($this->_uniacid);

            $data['recommend_style'] = $config['recommend_style'];
            $data['recommend_list'] = [
                'current_page' => 1,
                'data' => [],
                'last_page' => 1,
                'per_page' => 10,
                'total' => 0
            ];
        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:43
     * @功能说明:服务列表
     */
    public function serviceList()
    {

        $input = $this->_param;

        $page = $input['limit'] ?? 10;

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', '=', 1];

        $dis[] = ['type', '=', 1];

        $dis[] = ['check_status', '=', 2];

        $dis[] = ['is_add', '=', 0];

        if (!empty($input['name'])) {

            $dis[] = ['title', 'like', '%' . $input['name'] . '%'];

        }

        if (!empty($input['service_type'])) {

            $data = ServiceType::where(['uniacid' => $this->_uniacid, 'id' => $input['service_type'], 'status' => 1])->find();

            if (empty($data)) {

                return $this->success('');
            }
            $dis[] = ['service_type', '=', $input['service_type']];
        }

        $where = [
            [['b.status', '=', 2], ['b.is_work', '=', 1]]
        ];
        if (!empty($this->getUserId())) {

            $shield_coach = $this->coach_model->getShieldCoach($this->getUserId());

            if (!empty($shield_coach)) {

                $where[] = ['b.id', 'not in', $shield_coach];
            }
        }

        $input['sort'] = !empty($input['sort']) ? $input['sort'] : 'top desc';

        $data = $this->model->indexDataList($dis, $page, $input['sort'], $where);

        if ($data['data']) {

            $auth_status = MemberConfig::getStatus($this->_uniacid);

            $config = MemberConfig::getInfo(['uniacid' => $this->_uniacid]);

            foreach ($data['data'] as &$datum) {

                if ($auth_status) {

                    $datum['member_price'] = round($datum['price'] * $config['discount'] / 10, 2);
                }
            }
        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:58
     * @功能说明:审核详情
     */
    public function serviceInfo()
    {

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];
        $where = [
            [['b.status', '=', 2], ['b.is_work', '=', 1]]
        ];
        if (!empty($this->getUserId())) {

            $shield_coach = $this->coach_model->getShieldCoach($this->getUserId());

            if (!empty($shield_coach)) {

                $where[] = ['b.id', 'not in', $shield_coach];
            }
        }

        $data = $this->model->dataInfo($dis);
        $data['price'] = Db::name('massage_service_service_coach')
            ->alias('a')
            ->leftJoin('massage_service_coach_list b', 'a.coach_id=b.id')
            ->where($where)
            ->where('ser_id', $data['id'])
            ->min('price');

        $config = MemberConfig::getInfo(['uniacid' => $this->_uniacid]);

        $auth_status = MemberConfig::getStatus($this->_uniacid);

        if ($auth_status) {

            $data['member_price'] = round($data['price'] * $config['discount'] / 10, 2);
        }

        return $this->success($data);


    }

    /**
     * @author chenniang
     * @DataTime: 2021-03-23 14:16
     * @功能说明:获取配置信息
     */
    public function configInfo()
    {

        $dis = [

            'uniacid' => $this->_uniacid
        ];

        $config_model = new Config();

        $config_model->dataInfo($dis);

        $arr = 'uniacid,appsecret,app_app_secret,appid,app_app_id,web_app_id,web_app_secret,gzh_appid,order_tmp_id,cancel_tmp_id,max_day,time_unit,service_cover_time,can_tx_time,company_pay,short_id,short_secret';

        $config = $config_model->where($dis)->withoutField($arr)->find()->toArray();

        $pay_config_model = new PayConfig();

        $pay_config = $pay_config_model->dataInfo($dis);

        $config['alipay_status'] = $pay_config['alipay_status'];

        $short_config_model = new ShortCodeConfig();

        $short_config = $short_config_model->dataInfo($dis);

        $config['short_code_status'] = $short_config['short_code_status'];

        $config['bind_phone_type'] = $short_config['bind_phone_type'];
        //代理商文章标题
        if (!empty($config['agent_article_id'])) {

            $article_model = new ArticleList();

            $config['agent_article_title'] = $article_model->where(['id' => $config['agent_article_id']])->value('title');
        }

        $config_model = new ConfigSetting();

        $data = $config_model->dataInfo($this->_uniacid, ['wechat_transfer', 'alipay_transfer', 'under_transfer', 'coach_format', 'recommend_style', 'coach_level_show', 'tax_point', 'recharge_status', 'gradualColor', 'agent_phone', 'qywx_company_id', 'qywx_kid', 'qywx_kid', 'user_force_login', 'place_order_path', 'realtime_location', 'broker_check', 'broker_poster', 'attendant_name', 'video_limit', 'user_from_switch']);

        if (!empty($data['qywx_kid'])) {
            $qywx_kid = explode(',', $data['qywx_kid']);
            $data['qywx_kid'] = $qywx_kid[array_rand($qywx_kid)];
        }

        $config = array_merge($config, $data);

        $diy_model = new Diy();

        $diy_config = $diy_model->dataInfo(['uniacid' => $this->_uniacid]);

        $page = json_decode($diy_config['page'], true);

//        if (empty($page[8])) {
//
//            $page[8] = [
//                [
//                    "id" => "store-search-1",
//                    "compontents" => "base",
//                    "title" => "搜索",
//                    "type" => "search",
//                    "icon" => "iconsousuo2",
//                    "isDelete" => true,
//                    "addNumber" => 1,
//                    "attr" => [],
//                    "data" => [
//                        "title" => "搜索",
//                        "placeholder" => "请输入场地名称"
//                    ]
//                ],
//                "",
//                [
//                    "title" => "门店分类",
//                    "type" => "column",
//                    "icon" => "icondaohang",
//                    "isDelete" => true,
//                    "addNumber" => 1,
//                    "attr" => [],
//                    "data" => [
//                        "addMouduleName" => "columnList",
//                        "columnList" => []
//                    ],
//                    "id" => 1709179158414,
//                    "compontents" => "base"
//                ],
//                [
//                    "id" => "store-list-1",
//                    "compontents" => "base",
//                    "title" => "门店列表",
//                    "type" => "list",
//                    "icon" => "iconliebiao",
//                    "isDelete" => true,
//                    "addNumber" => 1,
//                    "attr" => [],
//                    "data" => [
//                        "title" => "门店列表"
//                    ]
//                ]
//            ];
//        }
        $config['page'] = $page;

        $config['tabBar'] = json_decode($diy_config['tabbar'], true);

        return $this->success($config);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-11 17:12
     * @功能说明:向导的服务列表
     */

    public function coachServiceList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.status', '=', 1];

        $dis[] = ['a.type', '=', 1];

        if (!empty($input['coach_id'])) {

            $dis[] = ['b.coach_id', '=', $input['coach_id']];
        }

        $is_add = !empty($input['is_add']) ? $input['is_add'] : 0;

        $dis[] = ['a.is_add', '=', 0];

        $data['data'] = $this->model->serviceCoachList($dis);

        if (!empty($data['data'])) {

            $car_model = new Car();

            foreach ($data['data'] as &$v) {

                $dis = [

                    'service_id' => $v['id'],

                    'coach_id' => $input['coach_id']
                ];

                $v['car_num'] = $car_model->where($dis)->sum('num');

            }
        }

        return $this->success($data);

    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-11 17:12
     * @功能说明:向导的服务列表
     */

    public function coachServiceListPage()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.status', '=', 1];

        $dis[] = ['a.type', '=', 1];

        if (!empty($input['coach_id'])) {

            $dis[] = ['b.coach_id', '=', $input['coach_id']];
        }
        if (!empty($input['ser_id'])) {

            $dis[] = ['a.id', '=', $input['ser_id']];
        }

        $dis[] = ['a.is_add', '=', 0];

        $data = $this->model->serviceCoachListPage($dis, $input['limit']);

        if (!empty($data['data'])) {
            $auth_status = MemberConfig::getStatus($this->_uniacid);

            $config = MemberConfig::getInfo(['uniacid' => $this->_uniacid]);

            $car_model = new Car();

            foreach ($data['data'] as &$v) {

                $dis = [

                    'service_id' => $v['id'],

                    'coach_id' => $input['coach_id']
                ];

                $v['car_num'] = $car_model->where($dis)->sum('num');

                if ($auth_status) {

                    $v['member_price'] = round($v['price'] * $config['discount'] / 10, 2);
                }
            }
        }

        return $this->success($data);

    }

    /**
     * @Desc: 分裂详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/6/19 15:03
     */
    public function serviceTypeInfo()
    {
        $id = \request()->param('id');

        $data = ServiceType::getInfo(['uniacid' => $this->_uniacid, 'id' => $id, 'status' => 1]);

        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-07 10:21
     * @功能说明:服务向导列表
     */
    public function serviceCoachList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.status', '=', 2];

        $dis[] = ['a.is_work', '=', 1];

//        $dis[] = ['a.user_id', '>', 0];

        if (!empty($input['ser_id'])) {
//            $coach_id = Db::name('massage_service_service_coach')->where('ser_id', $input['ser_id'])->column('coach_id');
            $dis[] = ['b.ser_id', '=', $input['ser_id']];
        }

        if (isset($input['sex']) && $input['sex'] != -1) {

            $dis[] = ['a.sex', '=', $input['sex']];

        }

        if (!empty($input['coach_name'])) {

            $dis[] = ['a.coach_name', 'like', '%' . $input['coach_name'] . '%'];
        }

        if (!empty($input['city_id'])) {

            $dis[] = ['a.city_id', '=', $input['city_id']];
        }

        if (!empty($input['store_id'])) {

            $dis[] = ['a.store_id', '=', $input['store_id']];

        }
        //服务中
        $working_coach = $this->coach_model->getWorkingCoach($this->_uniacid);
        //当前时间不可预约
        $cannot = CoachTimeList::getCannotCoach($this->_uniacid);
        //排序
        (new Coach)->indexListUpdate($this->_uniacid, $working_coach, $cannot);

//        $working_coach = array_diff($working_coach, $cannot);
        //如果登录不返回被屏蔽的向导
        if (!empty($this->getUserId())) {

            $shield_coach = $this->coach_model->getShieldCoach($this->getUserId());

            if (!empty($shield_coach)) {

                $dis[] = ['a.id', 'not in', $shield_coach];
            }

        }
        if (isset($input['min_price']) || isset($input['max_price'])) {
            $ids = Coach::getMinPriceId($this->_uniacid, $input);
            $dis[] = ['a.id', 'in', $ids];
        }
        //可服务不可服务
        if (!empty($input['type'])) {
            //可服务
            if ($input['type'] == 1) {

                $array = array_merge($working_coach, $cannot);

                $dis[] = ['a.id', 'not in', $array];

            } elseif ($input['type'] == 2) {//服务中

                $dis[] = ['a.id', 'in', $working_coach];

            } elseif ($input['type'] == 3) {//不可预约

                $dis[] = ['a.id', 'in', $cannot];
            }

        }
        $order = 'coach_index asc,distance asc,a.id desc';
        if (!empty($input['order'])) {
            if ($input['order'] == 1) {
                $order = 'service_price desc,a.id desc';
            } else {
                $time = strtotime('-30 days');
                $dis[] = ['a.create_time', '>=', $time];
                $order = 'a.id desc,distance asc';
            }
        }

//        $dis[] = ['e.status', '=', '1'];
        $lat = !empty($input['lat']) ? $input['lat'] : 0;

        $lng = !empty($input['lng']) ? $input['lng'] : 0;

        $alh = 'ACOS(SIN((' . $lat . ' * 3.1415) / 180 ) *SIN((a.lat * 3.1415) / 180 ) +COS((' . $lat . ' * 3.1415) / 180 ) * COS((a.lat * 3.1415) / 180 ) *COS((' . $lng . ' * 3.1415) / 180 - (a.lng * 3.1415) / 180 ) ) * 6378.137*1000 as distance';

        $data = $this->coach_model->serviceCoachList($dis, $alh, $order);

        if (!empty($data['data'])) {

            $collect_model = new CoachCollect();

            $config_model = new Config();

            $coach_model = new Coach();

            $store_model = new StoreList();

            $config = $config_model->dataInfo(['uniacid' => $this->_uniacid]);
            //销冠
            $top = $this->model->getSaleTopOne($this->_uniacid);
            //销售单量前5
            $five = $this->model->getSaleTopFive($this->_uniacid, $top);
            //最近七天注册
            $seven = $this->model->getSaleTopSeven($this->_uniacid);

            $user_id = !empty($this->getUserId()) ? $this->getUserId() : 0;

            $collect = $collect_model->where(['user_id' => $user_id])->column('coach_id');

            foreach ($data['data'] as &$v) {

                $v['store'] = $store_model->where(['id' => $v['store_id'], 'status' => 1])->field('id,title')->find();

                $v['is_collect'] = in_array($v['id'], $collect) ? 1 : 0;

                $v['near_time'] = $coach_model->getCoachEarliestTime($v['id'], $config);

                if (in_array($v['id'], $working_coach)) {

                    $text_type = 2;

                } elseif (empty($v['near_time'])) {

                    $text_type = 4;

                } elseif (!in_array($v['id'], $cannot)) {

                    $text_type = 1;

                } else {

                    $text_type = 3;
                }

                $v['text_type'] = $text_type;

                if ($v['id'] == $top) {

                    $v['coach_type_status'] = 1;

                } elseif (in_array($v['id'], $five)) {

                    $v['coach_type_status'] = 2;

                } elseif (in_array($v['id'], $seven)) {

                    $v['coach_type_status'] = 3;

                } else {

                    $v['coach_type_status'] = 0;

                }

                $v['age'] = getAge($v['birthday']);

//                $price = ServiceCoach::getMinPrice($v['id']);
//                $v['price'] = $price ?? 0;
            }

        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-02-21 17:03
     * @功能说明:第二中板式第向导列表
     */
    public function typeServiceCoachList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.status', '=', 2];

        $dis[] = ['a.user_id', '>', 0];

        if (!empty($input['ser_id'])) {

            $dis[] = ['b.ser_id', '=', $input['ser_id']];
        }

        if (!empty($input['coach_name'])) {

            $dis[] = ['a.coach_name', 'like', '%' . $input['coach_name'] . '%'];
        }

        if (!empty($input['store_id'])) {

            $dis[] = ['a.store_id', '=', $input['store_id']];

        }

        if (!empty($input['city_id'])) {

            $dis[] = ['a.city_id', '=', $input['city_id']];
        }
        $this->coach_model->setIndexTopCoach($this->_uniacid);
        //如果登录不返回被屏蔽的向导
        if (!empty($this->getUserId())) {

            $shield_coach = $this->coach_model->getShieldCoach($this->getUserId());

            if (!empty($shield_coach)) {

                $dis[] = ['a.id', 'not in', $shield_coach];
            }

        }

        $order = 'a.is_work desc,a.index_top desc,distance asc,a.id desc';
        if (!empty($input['order'])) {
            if ($input['order'] == 1) {
                $order = 'a.order_num desc,a.id desc';
            } else {
                $time = strtotime('-30 days');
                $dis[] = ['a.create_time', '>=', $time];
            }
        }

        $lat = !empty($input['lat']) ? $input['lat'] : 0;

        $lng = !empty($input['lng']) ? $input['lng'] : 0;

        $alh = 'ACOS(SIN((' . $lat . ' * 3.1415) / 180 ) *SIN((a.lat * 3.1415) / 180 ) +COS((' . $lat . ' * 3.1415) / 180 ) * COS((a.lat * 3.1415) / 180 ) *COS((' . $lng . ' * 3.1415) / 180 - (a.lng * 3.1415) / 180 ) ) * 6378.137*1000 as distance';

        $data = $this->coach_model->typeServiceCoachList($dis, $alh, $order);

        if (!empty($data['data'])) {

            $collect_model = new CoachCollect();

            $config_model = new Config();

            $coach_model = new Coach();

            $store_model = new StoreList();

            $config = $config_model->dataInfo(['uniacid' => $this->_uniacid]);
            //销冠
            $top = $this->model->getSaleTopOne($this->_uniacid);
            //销售单量前5
            $five = $this->model->getSaleTopFive($this->_uniacid, $top);
            //最近七天注册
            $seven = $this->model->getSaleTopSeven($this->_uniacid);

            $user_id = !empty($this->getUserId()) ? $this->getUserId() : 0;

            $collect = $collect_model->where(['user_id' => $user_id])->column('coach_id');

            foreach ($data['data'] as &$v) {

                $v['store'] = $store_model->where(['id' => $v['store_id'], 'status' => 1])->field('id,title')->find();

                $v['is_collect'] = in_array($v['id'], $collect) ? 1 : 0;

                $v['near_time'] = $coach_model->getCoachEarliestTime($v['id'], $config);

                if ($v['is_work'] == 0) {

                    $text_type = 4;

                } elseif ($v['index_top'] == 1) {

                    $text_type = 1;

                } else {

                    $text_type = 3;
                }

                $v['text_type'] = $text_type;

                if ($v['id'] == $top) {

                    $v['coach_type_status'] = 1;

                } elseif (in_array($v['id'], $five)) {

                    $v['coach_type_status'] = 2;

                } elseif (in_array($v['id'], $seven)) {

                    $v['coach_type_status'] = 3;

                } else {

                    $v['coach_type_status'] = 0;

                }
                $v['age'] = getAge($v['birthday']);

                $price = ServiceCoach::getMinPrice($v['id']);
                $v['price'] = $price ?? 0;
            }

        }

        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-24 14:07
     * @功能说明:购物车信息
     */
    public function carInfo()
    {

        $input = $this->_param;

        $order_id = !empty($input['order_id']) ? $input['order_id'] : 0;
        //购物车信息
        $car_info = $this->car_model->carPriceAndCount($this->getUserId(), $input['coach_id'], 1, $order_id);

        return $this->success($car_info);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-23 09:48
     * @功能说明:再来一单
     */
    public function onceMoreOrder()
    {

        $input = $this->_input;

        $order_model = new Order();

        $order = $order_model->dataInfo(['id' => $input['order_id']]);

        $coach = $this->coach_model->dataInfo(['id' => $order['coach_id']]);

        if ($coach['status'] != 2 || $coach['is_work'] == 0) {

            $this->errorMsg('向导未上班');
        }
        //清空购物车
        $this->car_model->where(['user_id' => $this->getUserId(), 'coach_id' => $order['coach_id']])->delete();

        Db::startTrans();

        foreach ($order['order_goods'] as $v) {

            $ser = $this->model->dataInfo(['id' => $v['goods_id']]);

            if (empty($ser) || $ser['status'] != 1) {

                Db::rollback();

                $this->errorMsg('服务已经下架');
            }

            $dis = [

                'user_id' => $this->getUserId(),

                'uniacid' => $this->_uniacid,

                'coach_id' => $order['coach_id'],

                'service_id' => $v['goods_id'],

                'num' => $v['num']
            ];

            $res = $this->car_model->dataAdd($dis);
        }

        Db::commit();

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-24 14:46
     * @功能说明:添加到购物车
     */
    public function addCar()
    {

        $input = $this->_input;

        $order_id = !empty($input['order_id']) ? $input['order_id'] : 0;

        $insert = [

            'uniacid' => $this->_uniacid,

            'user_id' => $this->getUserId(),

            'coach_id' => $input['coach_id'],

            'service_id' => $input['service_id'],

            'order_id' => $order_id,

        ];
        //目前只能加钟一个
//        if (!empty($order_id)) {
//
//            $this->car_model->where(['order_id' => $order_id])->delete();
//        }

        //下单只能下一个
        $is_add = $this->request->param('is_add', '');
        if ($is_add == 1) {
            $where = [
                ['user_id', '=', $this->getUserId()],
                ['coach_id', '=', $input['coach_id']],
                ['service_id', '<>', $input['service_id']]
            ];
        } else {
            $where = [
                ['user_id', '=', $this->getUserId()],
                ['coach_id', '=', $input['coach_id']],
            ];
        }
        $this->car_model->where($where)->delete();

        $info = $this->car_model->dataInfo($insert);
        //增加数量
        if (!empty($info)) {

            if (!empty($input['is_top'])) {

                return $this->success(1);

            }

            $res = $this->car_model->dataUpdate(['id' => $info['id']], ['num' => $info['num'] + $input['num']]);

        } else {
            $num = Service::where('id', $input['service_id'])->value('min_num');
            //添加到购物车
            $insert['num'] = empty($input['order_id']) ? $num : $input['num'];

            $insert['status'] = 1;

            $res = $this->car_model->dataAdd($insert);

            $id = $this->car_model->getLastInsID();

            return $this->success($id);
        }

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-24 14:54
     * @功能说明:删除购物车
     */
    public function delCar()
    {

        $input = $this->_input;

        $info = $this->car_model->dataInfo(['id' => $input['id']]);
        //加少数量
        if ($info['num'] > $input['num']) {

            $res = $this->car_model->dataUpdate(['id' => $info['id']], ['num' => $info['num'] - $input['num']]);

        } else {

            $res = $this->car_model->where(['id' => $info['id']])->delete();
        }

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-25 10:39
     * @功能说明:
     */
    public function carUpdate()
    {

        $input = $this->_input;

        $res = $this->car_model->where('id', 'in', $input['id'])->update(['status' => $input['status']]);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-24 14:59
     * @功能说明:批量删除购物车
     */
    public function delSomeCar()
    {

        $input = $this->_input;

        $dis = [

            'uniacid' => $this->_uniacid,

            'user_id' => $this->getUserId(),

            'coach_id' => $input['coach_id'],

        ];

        $res = $this->car_model->where($dis)->delete();

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-05 23:16
     * @功能说明:评价列表
     */
    public function commentList()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.status', '=', 1];

        if (!empty($input['coach_id'])) {

            $dis[] = ['a.coach_id', '=', $input['coach_id']];
        }

        if (!empty($input['coach_name'])) {

            $dis[] = ['d.coach_name', 'like', '%' . $input['coach_name'] . '%'];
        }

        if (!empty($input['goods_name'])) {

            $dis[] = ['c.goods_name', 'like', '%' . $input['goods_name'] . '%'];

        }

        $comment_model = new Comment();

        $config_model = new Config();

        $data = $comment_model->dataList($dis);

//        $anonymous_evaluate = $config_model->where(['uniacid' => $this->_uniacid])->value('anonymous_evaluate');

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$v) {

                $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
                //开启匿名评价
                if ($v['is_admin'] == 1 || $v['is_hide'] == 1) {

                    $v['nickName'] = '匿名用户';

                    $v['avatarUrl'] = 'https://lbqny.migugu.com/admin/farm/default-user.png';
                }

            }
        }

        return $this->success($data);

    }

    /**
     * @Desc: 评论列表-1.8改版
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/5 15:44
     */
    public function commentListV2()
    {

        $input = $this->_param;

        $dis[] = ['a.uniacid', '=', $this->_uniacid];

        $dis[] = ['a.status', '=', 1];

        if (!empty($input['coach_id'])) {

            $dis[] = ['d.id', '=', $input['coach_id']];
        }

        if (!empty($input['coach_name'])) {

            $dis[] = ['d.coach_name', 'like', '%' . $input['coach_name'] . '%'];
        }

        if (!empty($input['goods_name'])) {

            $dis[] = ['c.goods_name', 'like', '%' . $input['goods_name'] . '%'];

        }

        $comment_model = new Comment();

        $data = $comment_model->dataList($dis);

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$v) {

                $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
                //开启匿名评价
                if ($v['is_admin'] == 1 || $v['is_hide'] == 1) {

                    $v['nickName'] = '匿名用户';

                    $v['avatarUrl'] = 'https://lbqny.migugu.com/admin/farm/default-user.png';
                }

            }
        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:58
     * @功能说明:向导详情
     */
    public function coachInfo()
    {

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];

        $data = $this->coach_model->where($dis)->withoutField('id_card,id_code,mobile,service_price')->find()->toArray();

        $user_model = new User();

        $data['nickName'] = $user_model->where(['id' => $data['user_id']])->value('nickName');

        $city_model = new City();

        $data['city'] = $city_model->where(['id' => $data['city_id']])->value('title');
        //岁数
        $data['age'] = floor((time() - $data['birthday']) / (86400 * 365));

        $service_model = new Service();
        //服务
        $data['service'] = $service_model->serviceCoachList(['b.coach_id' => $input['id'], 'a.status' => 1]);

        $collect_model = new CoachCollect();

        $find = $collect_model->dataInfo(['user_id' => $this->getUserId(), 'coach_id' => $input['id']]);

        $data['is_collect'] = !empty($find) ? 1 : 0;

        $shield_coach = $this->coach_model->getShieldCoach($this->getUserId());

        $data['is_shield'] = in_array($input['id'], $shield_coach) ? 1 : 0;

        //评价
        $coach_comment_ratio = getConfigSetting($this->_uniacid, 'coach_comment_ratio');
        $coach_comment_ratio = explode(',', $coach_comment_ratio);
        $data['star'] = round($data['star'] * $coach_comment_ratio[0] / 100 + $data['attitude_star'] * $coach_comment_ratio[1] / 100 + $data['speed_star'] * $coach_comment_ratio[2] / 100, 1);
        $data['star'] = $data['star'] > 5 ? 5 : $data['star'];

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-02-28 11:50
     * @功能说明:获取腾讯地图信息
     */
    public function getMapInfo()
    {

        $input = $this->_param;

        $dis = [

            'uniacid' => $this->_uniacid
        ];

        $config_model = new Config();

        $config = $config_model->dataInfo($dis);

        $key = $input['location'];

        $data = getCache($key, $this->_uniacid);

        if (empty($data)) {

            $url = 'https://apis.map.qq.com/ws/geocoder/v1/?location=';

            $url = $url . $input['location'] . '&key=' . $config['map_secret'] . '&poi_options=policy=1';

            $data = longbingCurl($url, []);

            $data_arr = json_decode($data, true);

            if (isset($data_arr['status']) && $data_arr['status'] == 0) {

                setCache($key, $data, 300, $this->_uniacid);

            } else {

                $msg = !empty($data_arr['message']) ? $data_arr['message'] : '定位失败';

                LogUtils::log($data, 'TX_webService', 'webService');

                $this->errorMsg($msg);
            }

        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-06-15 15:48
     * @功能说明:获取
     */
    public function getCity()
    {

        $input = $this->_param;

        $city_model = new City();

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', '=', 1];

        $where[] = ['city_type', '=', 1];

        $where[] = ['is_screen', '=', 1];

        $lat = $input['lat'];

        $lng = $input['lng'];

        $alh = 'ACOS(SIN((' . $lat . ' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS((' . $lat . ' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS((' . $lng . ' * 3.1415) / 180 - (lng * 3.1415) / 180 ) ) * 6378.137*1000 as distance';

        $data = $city_model->where($dis)->field(['*', $alh])
            ->where(function ($query) use ($where) {
                $query->whereOr($where);
            })
            ->order('distance asc,id desc')
            ->select()
            ->toArray();

        if (!empty($data)) {

            $key_find = getConfigSetting($this->_uniacid, 'index_city_find');
            //是否必须定位到当前城市
            if ($key_find == 1) {

                $keys = round($lng, 3) . '-' . round($lat, 3);

                $city = getCache($keys, $this->_uniacid);

                if (empty($city)) {

                    $city = getCityByLongLat($lng, $lat, $this->_uniacid);

                    if (!empty($city)) {

                        setCache($keys, $city, 864000, $this->_uniacid);
                    }
                }

                $key = array_search($city, array_column($data, 'city'));

                if (isset($key) && is_numeric($key)) {

                    $data[$key]['is_select'] = 1;
                }

            } else {

                $data[0]['is_select'] = 1;

            }

            $data = $this->citySort($data);
        }

        if (empty($lat)) {

            $key = 'articleJsapiTicket-';

            $keys = 'articleToken-';

            setCache($key, '', 1, $this->_uniacid);

            setCache($keys, '', 1, $this->_uniacid);

        }

        return $this->success($data);
    }

    /**
     * @Desc: 排序
     * @param $data
     * @return array
     * @Auther: shurong
     * @Time: 2024/6/19 18:21
     */
    public function citySort($data)
    {

        $arr = [];

        foreach ($data as $datum) {

            if ($datum['city_type'] == 1) {

                $arr[] = $datum;

                foreach ($data as $value) {

                    if ($value['pid'] == $datum['id']) {

                        $arr[] = $value;
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * @author chenniang
     * @DataTime: 2022-06-15 16:29
     * @功能说明:优惠券
     */
    public function couponList()
    {

        if (empty($this->getUserId())) {

            return $this->success([]);
        }

        $coupon_record_model = new CouponRecord();

        $user_model = new User();

        $user_info = $user_model->dataInfo(['id' => $this->getUserId()]);

        $have_get = $coupon_record_model->where(['user_id' => $this->getUserId()])->column('coupon_id');

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['send_type', '=', 2];

        $dis[] = ['status', '=', 1];

        $dis[] = ['stock', '>', 0];

        $dis[] = ['id', 'not in', $have_get];

        $data = Db::name('massage_service_coupon')->where($dis)->field('id,title,user_limit,full,discount')->select();

        $list = [];

        $time = strtotime(date('Y-m-d', time()));

        if (!empty($data)) {

            foreach ($data as $v) {

                if ($v['user_limit'] == 2 && $user_info['create_time'] > $time) {

                    $list[] = $v;

                } elseif ($v['user_limit'] == 1) {

                    $list[] = $v;

                }

            }
        }

        $list = array_values($list);

        return $this->success($list);

    }


    /**
     * @author chenniang
     * @DataTime: 2022-06-15 22:49
     * @功能说明:用户获取卡券
     */
    public function userGetCoupon()
    {

        $input = $this->_input;

        $coupon_record_model = new CouponRecord();

        $coupon_model = new Coupon();

        if (!empty($input['coupon_id'])) {

            foreach ($input['coupon_id'] as $value) {

                $dis = [

                    'coupon_id' => $value,

                    'user_id' => $this->getUserId()
                ];
                //判断是否领取过
                $find = $coupon_record_model->dataInfo($dis);

                if (!empty($find)) {

                    continue;
                }

                $dis = [

                    'status' => 1,

                    'uniacid' => $this->_uniacid,

                    'send_type' => 2,

                    'id' => $value
                ];
                //检查优惠券
                $coupon = $coupon_model->dataInfo($dis);

                if (!empty($coupon)) {

                    $coupon_record_model->recordAdd($value, $this->getUserId());
                }

            }

        }
        return $this->success(true);
    }


    /**
     * @author chenniang
     * @DataTime: 2023-01-30 16:59
     * @功能说明:获取插件的权限
     */
    public function plugAuth()
    {

        $data = AdminMenu::getAuthList((int)$this->_uniacid, ['dynamic', 'recommend', 'store', 'demand', 'channelstaff', 'distributor', 'broker', 'storeplus', 'channel', 'seckill', 'integral']);

        $config_model = new MassageConfig();

        $config = $config_model->dataInfo(['uniacid' => $this->_uniacid]);

        $data['dynamic'] = !empty($config['dynamic_status']) ? $data['dynamic'] : 0;

        $data['member'] = MemberConfig::getStatus($this->_uniacid);

        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-14 10:18
     * @功能说明:服务分页50
     */
    public function serviceSelect()
    {

        $service_model = new Service();

        $dis = [

            'uniacid' => $this->_uniacid,

            'status' => 1,

            'is_add' => 0
        ];

        $data = $service_model->where($dis)->field('id,title')->order('top desc,id desc')->paginate(50)->toArray();

        return $this->success($data);

    }


    /**
     * 技能列表
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSkill()
    {
        $is_all = $this->request->param('is_all', 0);
        $name = $this->request->param('name', '');
        $where = [['uniacid', '=', $this->_uniacid], ['status', '=', 1]];
        if (!empty($name)) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        $list = DemandType::where($where)->field('id,name,price,img')->order('top desc')->select()->toArray();
        if ($is_all == 1) {
            $list[] = ['id' => 0, 'name' => '其他'];
        }
        return $this->success($list);
    }

    /**
     * 邀约订单列表
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function demandOrderList()
    {
        $pay_config = [
            1 => $this->payAliConfig(),
            2 => $this->payConfig($this->_uniacid, $this->is_app),
            3 => []
        ];
        //取消到期未接单订单
        DemandOrder::cancelOrder($this->_uniacid, $pay_config);
        $input = $this->request->param();
        $page = $this->request->param('limit', 10);
        $dis = $input['distance'] ?? 1;
        $lat = !empty($input['lat']) ? $input['lat'] : 0;
        $lng = !empty($input['lng']) ? $input['lng'] : 0;
        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.is_pay', '=', 1],
            ['a.status', '=', 2],
            ['a.start_time', '>', time()],
            ['a.is_refund', 'in', [0, 3]]
        ];
        $alh = '(2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(' . $lat . '-a.lat)/360),2)+COS(3.1415926535898*' . $lat . '/180)* COS(' . $lat . ' * 3.1415926535898/180)*POW(SIN(3.1415926535898*(' . $lng . '-a.lng)/360),2))))*1000 as distance';
        if (!empty($input['ser_id'])) {
            $where[] = ['a.ser_id', '=', $input['ser_id']];
        }
        if (!empty($input['start_price'])) {
            $where[] = ['a.price', '>=', $input['start_price']];
        }
        if (!empty($input['end_price'])) {
            $where[] = ['a.price', '<', $input['end_price']];
        }
        $data = DemandOrder::getList1($where, $alh, $dis, $page);
        if (!empty($data['data']) && !empty($input['coach_id'])) {
            $order_id = array_column($data['data'], 'id');
            $order = DemandOrderApply::whereIn('order_id', $order_id)->where('coach_id', $input['coach_id'])->select()->toArray();
            foreach ($data['data'] as &$datum) {
                if ($datum['status'] == 2) {
                    foreach ($order as $item) {
                        if ($datum['id'] == $item['order_id']) {
                            $datum['apply_status'] = $item['status'];
                        }
                    }
                }
            }
        }

        return $this->success($data);
    }

    /**
     * 向导协议
     * @return mixed
     */
    public function agreement()
    {
        $data = EntryAgreement::getInfo($this->_uniacid);
        return $this->success($data);
    }

    /**
     * 加盟商表单提交
     * @return mixed
     */
    public function participate()
    {
        $data = $this->request->only(['name', 'mobile', 'city', 'code']);
        $data['uniacid'] = $this->_uniacid;
        $data['user_id'] = $this->getUserId();
        $short_code = getCache($data['mobile'], $this->_uniacid);
        //验证码验证手机号
        if ($data['code'] != $short_code) {
            return $this->error('验证码错误');
        }
        unset($data['code']);
        $res = JoinForm::add($data);
        if ($res) {
            return $this->success('');
        }
        return $this->error('');
    }

    /**
     * @Desc: 发送消息
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/4/1 18:40
     */
    public function sendMsg()
    {
        $id = request()->param('id', '');

        $order = DemandOrder::find($id);

        if (empty($order)) {

            return $this->error('订单不存在');
        }

        $order_type = DemandType::where('id', $order['ser_id'])->value('name');

        $send['order_code'] = $order['order_code'];
        $send['uniacid'] = $order['uniacid'];
        $send['time'] = date('Y年m月d日 H:i', $order['create_time']);
        $send['order_type'] = $order_type;
        $send['money'] = $order['price'];

        $res = Coach::robSendMsg($send);

        return $this->success($res);
    }
}
