<?php
/**
 * Created by PhpStorm.
 * User: shuixian
 * Date: 2019/11/20
 * Time: 18:29
 */

$attendant_name = getConfigSetting(666, 'attendant_name');;

$action = [

    'update' => '编辑',

    'add'    => '新增',

    'del'    => '删除',

    'recommend_set'    => '设置推荐',

    'recommend_cancel'    => '取消推荐',

    'pass_coach'    => '同意授权',

    'cancel_pass_coach'  => '取消授权',

    'no_pass_coach'  => '拒绝授权',

    'update_admin'  => '编辑代理商',

    'update_partner'  => '编辑合伙人',

    'coach_get_order'  => $attendant_name.'接单',

    'coach_setout_order'  => $attendant_name.'出发',

    'coach_arr_order'  => $attendant_name.'到达',

    'coach_start_order'  => '开始服务',

    'coach_end_order'  => '结束服务',

    'change_order'  => '转单',

    'refund_order'  => '立即退款',

    'pass_refund_order'  => '同意退款',

    'nopass_refund_order'  => '拒绝退款',

    'pass'  => '同意',

    'nopass'  => '拒绝',

    'cancel'  => '取消',

    'top_one'  => '置顶',

    'upgrade'  => '升级',

    'excel'  => '导出',

    'login'  => '登录',

    'send'  => '派发',

    'updatepassworld'  => '修改密码',

    'top_dow'  => '取消置顶',

    'online' => '上线',

    'offline' => '下线',

    'set_balance' => '设置比例'
];


return $action;





