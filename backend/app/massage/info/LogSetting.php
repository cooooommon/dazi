<?php
/**
 * Created by PhpStorm.
 * User: shuixian
 * Date: 2019/11/20
 * Time: 18:29
 */
$attendant_name = getConfigSetting(666, 'attendant_name');;
return [
    'AdminShop' => [
        [
            'code_action' => 'editCarte',
            'table' => 'massage_service_shop_carte',
            'action_type' => '',
            'name' => '商品分类',
            'text' => '分类',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            'title' => 'name'
        ],
        [
            'code_action' => 'carteStatus',
            'table' => 'massage_service_shop_carte',
            'action_type' => '',
            'name' => '商品分类',
            'text' => '分类',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            'title' => 'name'
        ],
        [
            'code_action' => 'addCarte',
            'table' => 'massage_service_shop_carte',
            'action_type' => '',
            'name' => '商品分类',
            'text' => '分类',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
            'title' => 'name'
        ],
        [
            'code_action' => 'editGoods',
            'table' => 'massage_service_shop_goods',
            'action_type' => '',
            'name' => '商品管理',
            'text' => '商品',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            'title' => 'name'
        ],
        [
            'code_action' => 'editGoods',
            'table' => 'massage_service_shop_goods',
            'action_type' => '',
            'name' => '商品管理',
            'text' => '商品',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            'title' => 'name'
        ], [
            'code_action' => 'goodsStatus',
            'table' => 'massage_service_shop_goods',
            'action_type' => '',
            'name' => '商品管理',
            'text' => '商品',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            'title' => 'name'
        ],
        [
            'code_action' => 'addGoods',
            'table' => 'massage_service_shop_goods',
            'action_type' => '',
            'name' => '商品管理',
            'text' => '商品',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
            'title' => 'name'
        ]
    ],
    'AdminService' => [
        [
            'code_action' => 'typeAdd',
            'table' => 'massage_service_service_type_list',
            'action_type' => '',
            'name' => '服务分类管理',
            'text' => '分类',
            'parameter' => 'id',
            'title' => 'name',
            'method' => 'POST',
            'action' => 'add'
        ],
        [
            'code_action' => 'typeUpdate',
            'table' => 'massage_service_service_type_list',
            'action_type' => '',
            'name' => '服务分类管理',
            'text' => '分类',
            //自定义参数
            'custom_parameters' => [
                'key' => 'status',
                'value' => -1
            ],
            'parameter' => 'id',
            'title' => 'name',
            'method' => 'POST',
            'action' => 'del'
        ],
        [
            'code_action' => 'typeUpdate',
            'table' => 'massage_service_service_type_list',
            'action_type' => '',
            'name' => '服务分类管理',
            'text' => '分类',
            'parameter' => 'id',
            'title' => 'name',
            'method' => 'POST',
            'action' => 'update'
        ],
        [
            'code_action' => 'serviceAdd',
            'table' => 'massage_service_service_list',
            'action_type' => '',
            'name' => '服务管理',
            'text' => '服务',
            'parameter' => 'id',
            'title' => 'title',
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
            'method' => 'POST',
            'action' => 'add'
        ],
        [
            'code_action' => 'serviceUpdate',
            'table' => 'massage_service_service_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '服务管理',
            'text' => '服务',
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update'
        ],
        [
            'code_action' => 'serviceAdd',
            'title' => 'title',
            'table' => 'massage_service_service_list',
            'action_type' => 'add',
            'name' => '续单服务',
            'text' => '服务',
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add'
        ],
        [
            'code_action' => 'serviceUpdate',
            'title' => 'title',
            'table' => 'massage_service_service_list',
            'action_type' => 'add',
            'name' => '续单服务',
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
            'text' => '服务',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update'
        ],
    ],
    'AdminSetting' => [//
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'channel_config',
            'name' => '推广管理',
            'title' => '',
            'text' => '渠道商设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'channel_bind_type',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'winnerlook_config',
            'name' => '云信配置',
            'text' => '配置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'winnerlook_appid',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'moor_config',
            'name' => '容联七陌配置',
            'text' => '配置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'moor_id',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'shequshop_school_config',
            'action_type' => 'broker_config',
            'title' => '',
            'name' => '推广管理',
            'text' => '经纪人设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'broker_check',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'shequshop_school_config',
            'action_type' => 'wx_app_config',
            'name' => '系统设置',
            'text' => '小程序设置',
            'title' => '',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'app_name',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'shequshop_school_config',
            'action_type' => 'diy_other_config',
            'name' => 'DIY设置',
            'text' => '其他设置',
            'title' => '',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'attendant_name',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'shequshop_school_config',
            'action_type' => 'coach_level',
            'name' => $attendant_name.'等级',
            'text' => '折算周期',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'level_cycle',
            ],
        ],
        [
            'code_action' => 'demandTypeDel',
            'table' => 'massage_service_demand_type',
            'action_type' => '',
            'name' => '邀约管理',
            'text' => '服务类型',
            'title' => 'name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ],
        [
            'code_action' => 'demandTypeEdit',
            'table' => 'massage_service_demand_type',
            'action_type' => '',
            'name' => '邀约管理',
            'text' => '服务类型',
            'title' => 'name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'demandTypeAdd',
            'table' => 'massage_service_demand_type',
            'action_type' => '',
            'name' => '邀约管理',
            'text' => '服务类型',
            'title' => 'name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'demandSetting',
            'table' => 'shequshop_school_config',
            'action_type' => '',
            'name' => '邀约管理',
            'text' => '邀约设置',
            'title' => '',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'bannerUpdate',
            'table' => 'massage_service_banner',
            'action_type' => '',
            'name' => '轮播图设置',
            'text' => '轮播图',
            'parameter' => 'id',
            'method' => 'POST',
            'title' => 'id',
            'action' => 'update'
        ],
        [
            'code_action' => 'updatePass',
            'table' => 'shequshop_school_admin',
            'action_type' => '',
            'name' => '系统管理',
            'text' => '',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'updatepassworld'
        ],
        [
            'code_action' => 'bannerAdd',
            'table' => 'massage_service_banner',
            'action_type' => '',
            'name' => '轮播图设置',
            'text' => '轮播图',
            'title' => 'id',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add'
        ],
        [
            'code_action' => 'addClockUpdate',
            'table' => 'massage_add_clock_setting',
            'action_type' => '',
            'name' => '续单设置',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update'
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'shequshop_school_config',
            'action_type' => 'level_other',
            'name' => $attendant_name.'管理-其他设置',
            'title' => '',
            'text' => '',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'coach_format',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'shequshop_school_config',
            'action_type' => 'fx_config',
            'name' => '推广管理',
            'title' => '',
            'text' => '分销设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'fx_check',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'shequshop_school_config',
            'action_type' => 'agent_update',
            'name' => '代理管理',
            'title' => '',
            'text' => '代理商设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'agent_article_id',
            ],
        ],
        [
            'code_action' => 'adminUpdate',
            'table' => 'shequshop_school_admin',
            'action_type' => '',
            'name' => '代理商账号',
            'title' => 'agent_name',
            'text' => '账号',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'adminStatusUpdate',
            'table' => 'shequshop_school_admin',
            'action_type' => '',
            'title' => 'agent_name',
            'name' => '代理商账号',
            'text' => '账号',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ],
        [
            'code_action' => 'adminAdd',
            'table' => 'shequshop_school_admin',
            'action_type' => '',
            'title' => 'agent_name',
            'name' => '代理商账号',
            'text' => '账号',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'balance_cofig',
            'name' => '财务管理',
            'text' => '储值返佣',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'balance_balance',
            ],
        ],
        [
            'code_action' => 'configUpdateSchedule',
            'table' => 'massage_config',
            'action_type' => 'dynamic_cofig',
            'name' => '营销管理',
            'title' => '',
            'text' => '动态设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'dynamic_check',
            ],
        ],
        [
            'code_action' => 'configUpdateSchedule',
            'table' => 'massage_config',
            'action_type' => 'money_cofig',
            'name' => '财务管理',
            'title' => '',
            'text' => '储值设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'balance_balance',
            ],
        ],
        [
            'code_action' => 'userLabelUpdate',
            'table' => 'massage_service_user_label_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '客户标签',
            'text' => '标签',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'userLabelAdd',
            'table' => 'massage_service_user_label_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '客户标签',
            'text' => '标签',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'helpConfigUpate',
            'table' => 'massage_config',
            'action_type' => '',
            'name' => '通知管理',
            'text' => '求救设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'feedbackHandle',
            'table' => 'massage_service_coach_feedback',
            'action_type' => '',
            'name' => '问题反馈',
            'text' => '问题',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            'title' => 'id'
        ],
        [
            'code_action' => 'appealHandle',
            'table' => 'massage_service_coach_appeal',
            'action_type' => '',
            'name' => '差评申诉',
            'text' => '申诉',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            'title' => 'id'
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'diy_config',
            'name' => 'DIY设置-颜色风格',
            'text' => 'DIY',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'primaryColor',
            ],
        ],
        [
            'code_action' => 'diyUpdate',
            'table' => 'massage_config',
            'action_type' => 'diy_config',
            'name' => 'DIY设置-DIY页面设置',
            'text' => 'DIY',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'diy_other_config',
            'name' => 'DIY设置-其他设置',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'coach_font_color',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'web_config',
            'name' => '系统设置',
            'title' => '',
            'text' => '公众号设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'web_app_id',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'app_config',
            'name' => 'APP设置',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'app_app_id',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'apply_config',
            'name' => '应用设置',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'app_logo',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'conceal_config',
            'name' => '隐私设置',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'information_protection',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'business_config',
            'name' => '交易设置',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'trading_rules',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'putonrecord_config',
            'name' => '备案信息',
            'text' => '信息',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'record_no',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'ali_config',
            'name' => '阿里云配置',
            'text' => '配置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'short_id',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'virtual_config',
            'name' => '虚拟号配置',
            'text' => '配置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'pool_key',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'reminder_config',
            'name' => '来电通知配置',
            'text' => '配置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'reminder_phone',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'carout_config',
            'name' => '出行配置',
            'text' => '配置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'bus_end_time',
            ],
        ],
        [
            'code_action' => 'configUpdate',
            'table' => 'massage_config',
            'action_type' => 'other_config',
            'name' => '其他配置',
            'text' => '配置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'anonymous_evaluate',
            ],
        ],
        [
            'code_action' => 'payConfigUpdate',
            'table' => 'shequshop_school_pay_config',
            'action_type' => 'wechat_config',
            'name' => '微信支付设置',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'mch_id',
            ],
        ],
        [
            'code_action' => 'payConfigUpdate',
            'table' => 'shequshop_school_pay_config',
            'action_type' => 'alipay_config',
            'name' => '支付宝支付设置',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'ali_appid',
            ],
        ],
        [
            'code_action' => 'sendMsgConfigUpdate',
            'table' => 'massage_send_msg_config',
            'action_type' => '',
            'name' => '万能通知',
            'text' => '通知',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'shortCodeConfigUpdate',
            'table' => 'massage_short_code_config',
            'action_type' => '',
            'name' => '短信通知',
            'text' => '通知',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'getCarConfigAdd',
            'table' => 'massage_service_car_price',
            'action_type' => '',
            'name' => '城市车费',
            'text' => '设置',
            'title' => 'id',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'getCarConfigUpdate',
            'table' => 'massage_service_car_price',
            'action_type' => '',
            'name' => '城市车费',
            'text' => '设置',
            'title' => 'id',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'getCarConfigDel',
            'table' => 'massage_service_car_price',
            'action_type' => '',
            'name' => '城市车费',
            'text' => '设置',
            'title' => 'id',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ],
        [
            'code_action' => 'carConfigUpdate',
            'table' => 'massage_service_car_price',
            'action_type' => '',
            'name' => '全局车费',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'cityUpdate',
            'table' => 'massage_service_city_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '城市设置',
            'text' => '城市',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'cityAdd',
            'table' => 'massage_service_city_list',
            'action_type' => '',
            'name' => '城市设置',
            'title' => 'title',
            'text' => '城市',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'agreement',
            'table' => 'massage_coach_entry_agreement',
            'action_type' => '',
            'name' => $attendant_name.'管理-入驻协议',
            'text' => '设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'joinRead',
            'table' => 'massage_join_form',
            'action_type' => '',
            'name' => '代理申请',
            'title' => 'name',
            'text' => '已阅',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
    ],
    'AdminCoach' => [
        //
        [
            'code_action' => 'coachUpdateAdmin',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'setBalance',
            'table' => 'massage_service_coach_list',
            'action_type' => 'cancel',
            'name' => $attendant_name.'管理-批量修改抽成比例',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'ids',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'coachServiceUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => 'cancel',
            'name' => $attendant_name.'管理-取消关联技能',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'coach_id',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => -1
            ],
        ],
        [
            'code_action' => 'coachServiceUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => 'edit',
            'name' => $attendant_name.'管理-修改服务价格',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'coach_id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'coachServiceAdd',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理-选择关联技能',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'coachAdd',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'walletPass',
            'table' => 'massage_service_wallet_list',
            'action_type' => '',
            'name' => '财务管理-提现申请',
            'text' => '提现',
            'title' => 'code',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'walletPass',
            'table' => 'massage_service_wallet_list',
            'action_type' => '',
            'name' => '财务管理-提现申请',
            'text' => '提现',
            'title' => 'code',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'coachDataUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'cancelBroker',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理-修改经纪人',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'coachUpdateCheck',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理-重新审核',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'coachUpdateCheck',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理-重新审核',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 4
            ],
        ],
        [
            'code_action' => 'coachUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'text' => $attendant_name,
            'title' => 'coach_name',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'recommend_set',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'recommend',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'coachUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'title' => 'coach_name',
            'name' => $attendant_name.'管理',
            'text' => $attendant_name,
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'recommend_cancel',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'recommend',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'coachUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'title' => 'coach_name',
            'text' => $attendant_name,
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'coachUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'title' => 'coach_name',
            'text' => $attendant_name,
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'no_pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 4
            ],
        ],
        [
            'code_action' => 'coachUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'title' => 'coach_name',
            'text' => $attendant_name,
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'cancel_pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'coachUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'title' => 'coach_name',
            'text' => $attendant_name,
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => -1
            ],
        ],
        [
            'code_action' => 'coachUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'title' => 'coach_name',
            'text' => $attendant_name,
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update_admin',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'admin_id',
                // 'value' => 4
            ],
        ],
        [
            'code_action' => 'coachTagDel',
            'table' => 'massage_service_coach_tag',
            'action_type' => '',
            'name' => $attendant_name.'管理-个性标签',
            'title' => 'name',
            'text' => '标签',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ],
        [
            'code_action' => 'coachTagAdd',
            'table' => 'massage_service_coach_tag',
            'action_type' => '',
            'name' => $attendant_name.'管理-个性标签',
            'title' => 'name',
            'text' => '标签',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'coachTagEdit',
            'table' => 'massage_service_coach_tag',
            'action_type' => '',
            'name' => $attendant_name.'管理-个性标签',
            'title' => 'name',
            'text' => '标签',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'coachUpdate',
            'table' => 'massage_service_coach_list',
            'action_type' => '',
            'name' => $attendant_name.'管理',
            'title' => 'coach_name',
            'text' => $attendant_name,
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update_partner',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'partner_id',
                // 'value' => 4
            ],
        ]
        , [
            'code_action' => 'levelUpdate',
            'table' => 'massage_service_coach_level',
            'action_type' => '',
            'name' => $attendant_name.'等级',
            'title' => 'title',
            'text' => '等级',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'levelAdd',
            'table' => 'massage_service_coach_level',
            'action_type' => '',
            'name' => $attendant_name.'等级',
            'text' => '等级',
            'title' => 'title',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'walletPass',
            'table' => 'massage_service_wallet_list',
            'action_type' => '',
            'name' => '提现申请',
            'text' => '提现',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
            'title' => 'code'
        ],
        [
            'code_action' => 'walletNoPass',
            'table' => 'massage_service_wallet_list',
            'action_type' => '',
            'name' => '提现申请',
            'text' => '提现',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            'title' => 'code'
        ],
        [
            'code_action' => 'policeUpdate',
            'table' => 'massage_service_coach_police',
            'action_type' => '',
            'name' => '求救通知',
            'text' => '通知',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            'title' => 'id'
        ],
    ],
    'AdminOrder' => [
        //
        [
            'code_action' => 'delComment',
            'table' => 'massage_store_package_order_comment_list',
            'action_type' => '',
            'title' => 'id',
            'name' => '门店管理-评价管理',
            'text' => '评价',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ],
        [
            'code_action' => 'addComment',
            'table' => 'massage_store_package_order_comment_list',
            'action_type' => '',
            'title' => 'id',
            'name' => '门店管理-评价管理',
            'text' => '评价',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'demandRefund',
            'table' => 'massage_service_demand_order',
            'action_type' => 'nopass',
            'title' => 'order_code',
            'name' => '邀约管理-邀约退款管理',
            'text' => '',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass_refund_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'demandRefund',
            'table' => 'massage_service_demand_order',
            'action_type' => 'pass',
            'title' => 'order_code',
            'name' => '邀约管理-邀约退款管理',
            'text' => '',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass_refund_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'addCommentV2',
            'table' => 'massage_service_order_comment',
            'action_type' => '',
            'title' => 'id',
            'name' => '订单管理-评价管理',
            'text' => '评价',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'demandExamine',
            'table' => 'massage_service_demand_order',
            'action_type' => '',
            'name' => '邀约管理-邀约审核',
            'text' => '订单',
            'title' => 'order_code',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'demandExamine',
            'table' => 'massage_service_demand_order',
            'action_type' => '',
            'name' => '邀约管理-邀约审核',
            'text' => '订单',
            'title' => 'order_code',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'coach_get_order',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'title' => 'order_code',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_get_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 3
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'coach_setout_order',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'title' => 'order_code',
            'method' => 'POST',
            'action' => 'coach_setout_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 4
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'coach_arr_order',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_arr_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 5
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'coach_start_order',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_start_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 6
            ],  //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'coach_end_order',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_end_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 7
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'refund_order',
            'title' => 'order_code',
            'name' => '订单管理-服务退款',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'refund_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => -1
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'orderChangeCoach',
            'table' => 'massage_service_order_list',
            'action_type' => 'change_order',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'change_order',
        ], [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'add',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_get_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 3
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'add',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_setout_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 4
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'add',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_arr_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 5
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'add',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_start_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 6
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'adminUpdateOrder',
            'table' => 'massage_service_order_list',
            'action_type' => 'add',
            'title' => 'order_code',
            'name' => '订单管理-服务订单',
            'text' => '订单',
            'parameter' => 'order_id',
            'method' => 'POST',
            'action' => 'coach_end_order',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'type',
                'value' => 7
            ],
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'passRefund',
            'table' => 'massage_service_refund_order',
            'action_type' => 'pass_refund_order',
            'title' => 'order_code',
            'name' => '订单管理-服务退款',
            'text' => '订单',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass_refund_order',
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'passRefund',
            'table' => 'massage_service_refund_order',
            'action_type' => 'add',
            'title' => 'order_code',
            'name' => '续单退款',
            'text' => '订单',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass_refund_order',
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
        ], [
            'code_action' => 'noPassRefund',
            'table' => 'massage_service_refund_order',
            'action_type' => 'nopass_refund_order',
            'title' => 'order_code',
            'name' => '订单管理-服务退款',
            'text' => '订单',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass_refund_order',
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'noPassRefund',
            'table' => 'massage_service_refund_order',
            'action_type' => 'add',
            'title' => 'order_code',
            'name' => '订单管理-续单退款',
            'text' => '订单',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass_refund_order',
            //自定义参数
            'custom_parameters' => [
                'key' => 'is_add',
                'value' => 1
            ],
        ], [
            'code_action' => 'commentLableUpdate',
            'table' => 'massage_service_lable',
            'action_type' => '',
            'name' => '评价标签',
            'title' => 'title',
            'text' => '标签',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'commentLableAdd',
            'table' => 'massage_service_lable',
            'action_type' => '',
            'title' => 'title',
            'name' => '评价标签',
            'text' => '标签',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'commentUpdate',
            'table' => 'massage_service_order_comment',
            'action_type' => '',
            'title' => 'id',
            'name' => '订单管理-评价管理',
            'text' => '评价',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ],
        [
            'code_action' => 'refundCheck',
            'table' => 'massage_store_package_order_refund_list',
            'action_type' => '',
            'name' => '门店管理-套餐退款管理',
            'text' => '退款',
            'title' => 'refund_code',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass_refund_order',
            //自定义参数
            'custom_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'refundCheck',
            'table' => 'massage_store_package_order_refund_list',
            'action_type' => '',
            'name' => '门店管理-套餐退款管理',
            'text' => '退款',
            'title' => 'refund_code',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass_refund_order',
            //自定义参数
            'custom_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'delComment',
            'table' => 'massage_store_package_order_comment_list',
            'action_type' => '',
            'name' => '门店管理-评价管理',
            'text' => '评价',
            'title' => 'id',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ],
    ],
    'AdminReseller' => [
        [
            'code_action' => 'resellerUpdate',
            'table' => 'massage_distribution_list',
            'action_type' => '',
            'name' => '分销商审核',
            'title' => 'user_name',
            'text' => '分销商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'resellerUpdate',
            'table' => 'massage_distribution_list',
            'action_type' => '',
            'title' => 'user_name',
            'name' => '分销商审核',
            'text' => '分销商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'cancel',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'resellerUpdate',
            'table' => 'massage_distribution_list',
            'action_type' => '',
            'title' => 'user_name',
            'name' => '分销商审核',
            'text' => '分销商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 4
            ],
        ],
        [
            'code_action' => 'resellerUpdate',
            'table' => 'massage_distribution_list',
            'action_type' => '',
            'title' => 'user_name',
            'name' => '分销商审核',
            'text' => '分销商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => -1
            ],
        ],
    ],
    'AdminChannel' => [
        //
        [
            'code_action' => 'changeChannel',
            'table' => 'massage_channel_staff_list',
            'action_type' => '',
            'name' => '渠道商审核-更换员工上级',
            'title' => 'channel_id',
            'text' => '渠道商ID',
            'parameter' => 'channel_staff_id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'setBindTime',
            'table' => 'massage_channel_list',
            'action_type' => '',
            'name' => '渠道商审核-批量设置时效',
            'title' => 'user_name',
            'text' => '渠道商',
            'parameter' => 'ids',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'changeBalance',
            'table' => 'massage_channel_list',
            'action_type' => '',
            'name' => '渠道商审核-批量设置比例',
            'title' => 'user_name',
            'text' => '渠道商',
            'parameter' => 'ids',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'channelUpdate',
            'table' => 'massage_channel_list',
            'action_type' => 'del',
            'name' => '渠道商审核',
            'title' => 'user_name',
            'text' => '渠道商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => -1
            ],
        ],
        [
            'code_action' => 'channelUpdate',
            'table' => 'massage_channel_list',
            'action_type' => 'pass',
            'name' => '渠道商审核',
            'title' => 'user_name',
            'text' => '渠道商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'channelUpdate',
            'table' => 'massage_channel_list',
            'action_type' => 'nopass',
            'name' => '渠道商审核',
            'title' => 'user_name',
            'text' => '渠道商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 4
            ],
        ],
        [
            'code_action' => 'channelUpdate',
            'table' => 'massage_channel_list',
            'action_type' => 'cancel',
            'name' => '渠道商审核',
            'title' => 'user_name',
            'text' => '渠道商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'cancel_pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'channelUpdate',
            'table' => 'massage_channel_list',
            'action_type' => 'edit',
            'name' => '渠道商审核',
            'title' => 'user_name',
            'text' => '渠道商',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'cateUpdate',
            'table' => 'massage_channel_cate',
            'action_type' => '',
            'title' => 'title',
            'name' => '渠道类目',
            'text' => '类目',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'cateAdd',
            'table' => 'massage_channel_cate',
            'action_type' => '',
            'title' => 'title',
            'name' => '渠道类目',
            'text' => '类目',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ]
    ],
    'AdminArticle' => [
        [
            'code_action' => 'articleUpdate',
            'table' => 'massage_article_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '文章管理',
            'text' => '文章',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ], [
            'code_action' => 'articleAdd',
            'table' => 'massage_article_list',
            'title' => 'title',
            'action_type' => '',
            'name' => '文章管理',
            'text' => '文章',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'fieldUpdate',
            'table' => 'massage_article_form_field',
            'action_type' => '',
            'title' => 'title',
            'name' => '文章管理-表单字段',
            'text' => '字段',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'fieldAdd',
            'table' => 'massage_article_form_field',
            'action_type' => '',
            'title' => 'title',
            'name' => '文章管理-表单字段',
            'text' => '字段',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ]
    ],
    'AdminCoupon' => [
        [
            'code_action' => 'couponUpdate',
            'table' => 'massage_service_coupon',
            'action_type' => '',
            'title' => 'title',
            'name' => '卡券管理',
            'text' => '卡券',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'couponRecordAdd',
            'table' => 'massage_service_coupon',
            'action_type' => '',
            'title' => 'title',
            'name' => '卡券管理-指定派发卡券',
            'text' => '卡券',
            'parameter' => 'coupon_id',
            'method' => 'POST',
            'action' => 'send',
        ],
        [
            'code_action' => 'couponAdd',
            'table' => 'massage_service_coupon',
            'action_type' => '',
            'title' => 'title',
            'name' => '卡券管理',
            'text' => '卡券',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'couponAtvUpdate',
            'table' => 'massage_service_coupon_atv',
            'action_type' => '',
            'name' => '邀请有礼',
            'text' => '活动',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ]
    ],
    'AdminBalance' => [
        [
            'code_action' => 'payBalanceOrder',
            'table' => 'massage_service_user_list',
            'action_type' => '',
            'name' => '用户管理',
            'title' => 'nickName',
            'text' => '修改余额',
            'parameter' => 'user_id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'cardUpdate',
            'table' => 'massage_service_balance_card',
            'action_type' => '',
            'name' => '储值管理',
            'title' => 'title',
            'text' => '套餐',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'cardAdd',
            'table' => 'massage_service_balance_card',
            'action_type' => '',
            'name' => '储值管理',
            'title' => 'title',
            'text' => '套餐',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ]
    ],
    'AdminDynamicList' => [
        [
            'code_action' => 'dynamicTop',
            'table' => 'massage_dynamic_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '动态管理',
            'text' => '动态',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'top_dow',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'top',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'dynamicTop',
            'table' => 'massage_dynamic_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '动态管理',
            'text' => '动态',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'top_one',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'top',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'dynamicCheck',
            'table' => 'massage_dynamic_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '动态管理',
            'text' => '动态',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'dynamicCheck',
            'table' => 'massage_dynamic_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '动态管理',
            'text' => '动态',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'dynamicDel',
            'table' => 'massage_dynamic_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '动态管理',
            'text' => '动态',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ], [
            'code_action' => 'commentCheck',
            'table' => 'massage_dynamic_comment',
            'action_type' => '',
            'title' => 'id',
            'name' => '动态评论',
            'text' => '评论',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'commentCheck',
            'table' => 'massage_dynamic_comment',
            'action_type' => '',
            'title' => 'id',
            'name' => '动态评论',
            'text' => '评论',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'commentDel',
            'title' => 'id',
            'table' => 'massage_dynamic_comment',
            'action_type' => '',
            'name' => '动态评论',
            'text' => '评论',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ]
    ],
    'AppUpgrade' => [
        [
            'code_action' => 'upgrade',
            'table' => '',
            'action_type' => '',
            'name' => '系统升级',
            'text' => '系统',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'upgrade',
        ]
    ],
    'Config' => [
        [
            'code_action' => 'updateOssConfig',
            'table' => 'shequshop_school_oos_config',
            'action_type' => '',
            'name' => '上传设置',
            'text' => '设置',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ]
    ],
    'AdminPrinter' => [
        [
            'code_action' => 'printerUpdate',
            'table' => 'massage_service_printer',
            'action_type' => '',
            'name' => '打印机设置',
            'text' => '设置',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ]
    ],
    'Admin' => [
        //
        [
            'code_action' => 'cardUpdate',
            'table' => 'massage_member_card_list',
            'action_type' => 'cardDel',
            'title' => 'title',
            'name' => '客户管理-会员管理',
            'text' => '会员卡管理',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => -1
            ]
        ],
        [
            'code_action' => 'cardUpdate',
            'table' => 'massage_member_card_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '客户管理-会员管理',
            'text' => '会员卡管理',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'cardAdd',
            'table' => 'massage_member_card_list',
            'action_type' => '',
            'title' => 'title',
            'name' => '客户管理-会员管理',
            'text' => '会员卡管理',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'configSet',
            'table' => 'massage_member_config',
            'action_type' => '',
            'title' => '',
            'name' => '客户管理',
            'text' => '会员卡设置',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'seckillEdit',
            'table' => 'massage_store_package_seckill_list',
            'action_type' => '',
            'title' => 'id',
            'name' => '门店管理-秒杀活动',
            'text' => '秒杀',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'recommend_cancel',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'is_ad',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'seckillEdit',
            'table' => 'massage_store_package_seckill_list',
            'action_type' => '',
            'title' => 'id',
            'name' => '门店管理-秒杀活动',
            'text' => '秒杀',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'recommend_set',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'is_ad',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'seckillEdit',
            'table' => 'massage_store_package_seckill_list',
            'action_type' => '',
            'title' => 'id',
            'name' => '门店管理-秒杀活动',
            'text' => '秒杀',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'seckillAdd',
            'table' => 'massage_store_package_seckill_list',
            'action_type' => '',
            'title' => 'id',
            'name' => '门店管理-秒杀活动',
            'text' => '秒杀',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'add',
            'table' => 'massage_broker_list',
            'action_type' => '',
            'title' => 'name',
            'name' => '经纪人审核',
            'text' => '经纪人',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'login',
            'table' => 'shequshop_school_admin',
            'action_type' => '',
            'name' => '系统',
            'text' => '',
            'parameter' => '',
            'method' => 'POST',
            'action' => 'login',
        ],
        [
            'code_action' => 'update',
            'table' => 'massage_broker_list',
            'action_type' => 'del',
            'title' => 'name',
            'name' => '经纪人审核',
            'text' => '经纪人',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => -1
            ],
        ],
        [
            'code_action' => 'update',
            'table' => 'massage_broker_list',
            'action_type' => 'nopass',
            'title' => 'name',
            'name' => '经纪人审核',
            'text' => '经纪人',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'cancel_pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'update',
            'table' => 'massage_broker_list',
            'action_type' => 'pass',
            'title' => 'name',
            'name' => '经纪人审核',
            'text' => '经纪人',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'update',
            'table' => 'massage_broker_list',
            'action_type' => 'no_pass_coach',
            'title' => 'name',
            'name' => '经纪人审核',
            'text' => '经纪人',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'no_pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 4
            ],
        ],
        [
            'code_action' => 'update',
            'table' => 'massage_broker_list',
            'action_type' => 'set_balance',
            'title' => 'name',
            'name' => '经纪人审核',
            'text' => '经纪人',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'set_balance',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'balance',
            ],
        ],
        [
            'code_action' => 'update',
            'table' => 'massage_broker_list',
            'action_type' => 'edit',
            'title' => 'name',
            'name' => '经纪人审核',
            'text' => '经纪人',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
    ],
    'AdminExcel' => [
        //
        [
            'code_action' => 'packageOrder',
            'table' => 'massage_store_package_order_list',
            'action_type' => '',
            'name' => '套餐订单管理',
            'text' => '',
            'parameter' => '',
            'method' => 'GET',
            'action' => 'excel',
        ],
        [
            'code_action' => 'demandOrder',
            'table' => 'massage_service_demand_order',
            'action_type' => '',
            'name' => '邀约订单管理',
            'text' => '',
            'parameter' => '',
            'method' => 'GET',
            'action' => 'excel',
        ],
        [
            'code_action' => 'userList',
            'table' => 'massage_service_user_list',
            'action_type' => '',
            'name' => '客户管理',
            'text' => '',
            'parameter' => '',
            'method' => 'GET',
            'action' => 'excel',
        ],
        [
            'code_action' => 'orderList',
            'table' => 'massage_service_order_list',
            'action_type' => 'channel',
            'name' => '推广管理-渠道财务',
            'text' => '',
            'parameter' => '',
            'method' => 'GET',
            'action' => 'excel',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'is_channel',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'orderList',
            'table' => 'massage_service_order_list',
            'action_type' => 'order',
            'name' => '订单管理-服务订单',
            'text' => '',
            'parameter' => '',
            'method' => 'GET',
            'action' => 'excel',
        ],
        [
            'code_action' => 'subDataList',
            'table' => 'massage_service_order_list',
            'action_type' => '',
            'name' => '文章管理',
            'text' => '',
            'parameter' => '',
            'method' => 'GET',
            'action' => 'excel',
        ]
    ],
    'AdminUser' => [//
        [
            'code_action' => 'setBlacklist',
            'table' => 'massage_service_user_list',
            'action_type' => 'cancel',
            'title' => 'nickName',
            'name' => '客户管理-移除黑名单',
            'text' => '用户',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'is_on',
                'value' => 0
            ],
        ],
        [
            'code_action' => 'setBlacklist',
            'table' => 'massage_service_user_list',
            'action_type' => 'set',
            'title' => 'nickName',
            'name' => '客户管理-拉黑用户',
            'text' => '用户',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'is_on',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'delUserLabel',
            'table' => 'massage_service_user_list',
            'action_type' => '',
            'title' => 'nickName',
            'name' => '客户管理-删除标签',
            'text' => '用户',
            'parameter' => 'user_id',
            'method' => 'POST',
            'action' => 'del',
        ],
        [
            'code_action' => 'adminUpdateCoachCommisson',
            'table' => 'massage_service_order_commission',
            'action_type' => '',
            'title' => 'id',
            'name' => '分销佣金-线下'.$attendant_name.'转账',
            'text' => '',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
        ],
    ],
    'AdminStore' => [
        //
        [
            'code_action' => 'reCheck',
            'table' => 'massage_store_apply_update',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-重新审核',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'nopass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'reCheck',
            'table' => 'massage_store_apply_update',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-重新审核',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'addStore',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'editStoreBalance',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-批量设置比例',
            'text' => '门店',
            'parameter' => 'ids',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'check',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店审核',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'check',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店审核',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'no_pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 4
            ],
        ],
        [
            'code_action' => 'changeStatus',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店审核',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'cancel_pass_coach',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 3
            ],
        ],
        [
            'code_action' => 'typeAdd',
            'table' => 'massage_store_type_list',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-门店分类',
            'text' => '分类',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'typeUpdate',
            'table' => 'massage_store_type_list',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-门店分类',
            'text' => '分类',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'editStore',
            'table' => 'massage_store_apply',
            'action_type' => 'edit_balance',
            'title' => 'name',
            'name' => '门店管理-设置比例',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'share_balance'
            ],
        ],
        [
            'code_action' => 'editStore',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'storeTop',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'top_one',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'storeTop',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'top_dow',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 2
            ],
        ],
        [
            'code_action' => 'changeStatus',
            'table' => 'massage_store_apply',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理',
            'text' => '门店',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
        ],
    ],
    'AdminPackage' => [
        [
            'code_action' => 'edit',
            'table' => 'massage_store_package_list',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-团购/套餐管理',
            'text' => '套餐',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'update',
        ],
        [
            'code_action' => 'add',
            'table' => 'massage_store_package_list',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-团购/套餐管理',
            'text' => '套餐',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'add',
        ],
        [
            'code_action' => 'updateStatus',
            'table' => 'massage_store_package_list',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-团购/套餐管理',
            'text' => '套餐',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'del',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => -1
            ],
        ],
        [
            'code_action' => 'updateStatus',
            'table' => 'massage_store_package_list',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-团购/套餐管理',
            'text' => '套餐',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'online',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 1
            ],
        ],
        [
            'code_action' => 'updateStatus',
            'table' => 'massage_store_package_list',
            'action_type' => '',
            'title' => 'name',
            'name' => '门店管理-团购/套餐管理',
            'text' => '套餐',
            'parameter' => 'id',
            'method' => 'POST',
            'action' => 'offline',
            //自定义参数
            'transmit_parameters' => [
                'key' => 'status',
                'value' => 0
            ],
        ],
    ]
];
