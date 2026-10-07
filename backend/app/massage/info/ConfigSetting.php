<?php
/**
 * Created by PhpStorm.
 * User: shuixian
 * Date: 2019/11/20
 * Time: 18:29
 */

$data = [

    [

        'key' => 'wechat_transfer',

        'default_value' => 0,

        'text' => '微信转账',

        'field_type' => 1
    ],
    [

        'key' => 'alipay_transfer',

        'default_value' => 0,

        'text' => '支付宝转账',

        'field_type' => 1,
    ],
    [

        'key' => 'under_transfer',

        'default_value' => 1,

        'text' => '线下转账',

        'field_type' => 1,
    ],
    [

        'key' => 'coach_format',

        'default_value' => 1,

        'text' => '向导列表的版式',

        'field_type' => 1,
    ],
    [

        'key' => 'recommend_style',

        'default_value' => 1,

        'text' => '推荐向导样式',

        'field_type' => 1,
    ],
    [

        'key' => 'coach_level_show',

        'default_value' => 1,

        'text' => '向导比例是否显示',

        'field_type' => 1,
    ],
    [

        'key' => 'order_dispatch',

        'default_value' => 0,

        'text' => '是否派单',

        'field_type' => 1,
    ],
    [

        'key' => 'index_city_find',

        'default_value' => 0,

        'text' => '是否必须定位所在城市',

        'field_type' => 1,
    ],
    [

        'key' => 'coach_partner',

        'default_value' => 0,

        'text' => '向导合伙人',

        'field_type' => 1,
    ],
    [

        'key' => 'user_agent_balance',

        'default_value' => 0,

        'text' => '用户返佣',

        'field_type' => 1,
    ],
    [

        'key' => 'coach_agent_balance',

        'default_value' => 0,

        'text' => '邀请向导返佣',

        'field_type' => 1,
    ]
    ,
    [

        'key' => 'commission_custom',

        'default_value' => 0,

        'text' => '佣金自定义',

        'field_type' => 1,
    ],
    [

        'key' => 'tax_point',

        'default_value' => 0,

        'text' => '提现税点',

        'field_type' => 1,
    ],
    [

        'key' => 'recharge_status',

        'default_value' => 1,

        'text' => '余额充值入口',

        'field_type' => 1,
    ],
    [

        'key' => 'gradualColor',

        'default_value' => '#DF52FF',

        'text' => '导航渐变底色',

        'field_type' => 2,
    ],
    [

        'key' => 'agent_phone',

        'default_value' => 1,

        'text' => '代理商电话',

        'field_type' => 1,
    ],
    [

        'key' => 'number_encryption',

        'default_value' => 0,

        'text' => '是否号码加密',

        'field_type' => 1,
    ],
    [

        'key' => 'number_encryption_ip',

        'default_value' => 0,

        'text' => '公司ip可以看到真实号码',

        'field_type' => 2,
    ],
    [

        'key' => 'copyright',

        'default_value' => '',

        'text' => '版权',

        'field_type' => 2,
    ],
    [

        'key' => 'wechat_tmpl',

        'default_value' => 0,

        'text' => '公众号模版消息通知管理员',

        'field_type' => 1,
    ],
    [

        'key' => 'wechat_tmpl_admin',

        'default_value' => '',

        'text' => '公众号模版消息通知管理员信息',

        'field_type' => 2,
    ],
    [

        'key' => 'coach_receiving_minute',

        'default_value' => 0,

        'text' => '技师接单超时/分钟',

        'field_type' => 1,
    ],
    [

        'key' => 'qywx_company_id',

        'default_value' => '',

        'text' => '企业微信id',

        'field_type' => 2,
    ],
    [

        'key' => 'qywx_kid',

        'default_value' => '',

        'text' => '企业微信客服id',

        'field_type' => 2,
    ],
    [

        'key' => 'user_contact_coach',

        'default_value' => 1,

        'text' => '用户联系向导',

        'field_type' => 1,
    ],
    [

        'key' => 'user_force_login',

        'default_value' => 0,

        'text' => '登录强制手机验证',

        'field_type' => 1,
    ],
    [
        'key' => 'place_order_path',

        'default_value' => 1,

        'text' => '用户下单路径',

        'field_type' => 1,
    ],
    [

        'key' => 'realtime_location',

        'default_value' => 0,

        'text' => '实时定位',

        'field_type' => 1,
    ],
    [

        'key' => 'coach_comment_ratio',

        'default_value' => '100,0,0',

        'text' => '向导评分权重设置',

        'field_type' => 2,
    ],
    [

        'key' => 'broker_check',

        'default_value' => '0',

        'text' => '向导经纪人审核开关',

        'field_type' => 1,
    ],
    [

        'key' => 'broker_balance',

        'default_value' => '0',

        'text' => '经纪人分销返佣比例',

        'field_type' => 1,
    ],
    [

        'key' => 'broker_coach_balance',

        'default_value' => '50',

        'text' => '经纪人分销向导承担比例',

        'field_type' => 1,
    ],
    [

        'key' => 'broker_agent_balance',

        'default_value' => '50',

        'text' => '经纪人分销代理商承担比例',

        'field_type' => 1,
    ],
    [

        'key' => 'broker_poster',

        'default_value' => 'https://lbqny.migugu.com/admin/peiwan/invite-poster.png',

        'text' => '经纪人邀请海报',

        'field_type' => 2,
    ],
    [

        'key' => 'attendant_name',

        'default_value' => '向导',

        'text' => '行业服务人员名称',

        'field_type' => 2,
    ],
    [

        'key' => 'video_limit',

        'default_value' => 1,

        'text' => '向导个人视频限制',

        'field_type' => 1,
    ],
    [

        'key' => 'channel_bind_type',

        'default_value' => 1,

        'text' => '渠道码绑定方式 1可换绑 2不可换绑',

        'field_type' => 1,
    ],
    [

        'key' => 'channel_bind_time',

        'default_value' => 1,

        'text' => '绑定渠道商失效/小时',

        'field_type' => 1,
    ],
    [

        'key' => 'jump_order_minute',

        'default_value' => 0,

        'text' => '完成订单后，技师多少分钟内没离开提醒平台(分钟)',

        'field_type' => 1,
    ],
    [

        'key' => 'jump_order_distance',

        'default_value' => '',

        'text' => '完成订单后，技师多少分钟内没离开提醒平台（距离）',

        'field_type' => 2,
    ],
    [

        'key' => 'service_lat_type',

        'default_value' => 0,

        'text' => '技师服务迟到提醒，0服务开始前，1服务开始后',

        'field_type' => 1,
    ],
    [

        'key' => 'service_lat_minute',

        'default_value' => 0,

        'text' => '技师服务迟到提醒，分钟',

        'field_type' => 1,
    ],
    [
        'key' => 'user_from_switch',

        'default_value' => '0',

        'text' => '用户来源表单开关',

        'field_type' => 1,
    ],
];


return $data;





