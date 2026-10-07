<?php
/**
 * Created by PhpStorm.
 * User: shuixian
 * Date: 2019/11/20
 * Time: 18:29
 */
//行业版打包时可以用此配置,打包成功

use longbingcore\permissions\Tabbar;

return [
    //行业模块名称
    'app_model_name' => 'longbing_playwith',
    //行业模块标题
    'app_model_title' => '龙兵陪玩',
    //DIY默认数据
    'diy_default_data' =>[

    ] ,
    //控制能开放多少个小程序使用  这个是一个通用权限控制 , 请求授权时,应该知道是那个行业的  暂时还没用到
    'saas_auth_number_config' =>[
        'wxapp_number' => 0 ,
        'card_number' => 0 ,
        'company_number' => 0 ,
    ],
    //控制后台能展示的模块
    'saas_auth_admin_model_list' => [

        'shop'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => false ,
        ],
        'massage'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => false ,
        ],
        'reminder'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => true ,
        ],
        'virtual'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => true ,
        ],
        'dynamic'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => true ,
        ],
        'recommend'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => true ,
        ],
        'node'=>[
            'auth_platform'   => false ,
            'auth_is_platform_check' => false ,
            'auth_is_saas_check' => false ,
        ],
        'store'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => true ,
        ],
        'demand'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => true ,
        ],
        'channelstaff'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => true ,
        ],
        'distributor' => [
            'auth_platform' => true,
            'auth_is_platform_check' => true,
            'auth_is_saas_check' => true,
        ],
        'broker' => [
            'auth_platform' => true,
            'auth_is_platform_check' => true,
            'auth_is_saas_check' => true,
        ],
        'storeplus' => [
            'auth_platform' => true,
            'auth_is_platform_check' => true,
            'auth_is_saas_check' => true,
        ],
        'channel' => [
            'auth_platform' => true,
            'auth_is_platform_check' => true,
            'auth_is_saas_check' => true,
        ],
        'seckill'=>[
            'auth_platform'   => true ,
            'auth_is_platform_check' => true ,
            'auth_is_saas_check' => true ,
        ],
        'member' => [
            'auth_platform' => true,
            'auth_is_platform_check' => true,
            'auth_is_saas_check' => true,
        ],
        'integral' => [
            'auth_platform' => true,
            'auth_is_platform_check' => true,
            'auth_is_saas_check' => true,
        ],
    ],

    //独立版升级使用
    //版本ID
    'version_id'    => '64c7ad0322f14b9c894e95220c9d00d5',
    //分支ID
    'branch_id'     => '9068836a0acd11eab9c765ac55de11af',
    //当前系统版本号
    'version_no'    => 'longbing_playwith_43.1',
    //验证系统平台ID
    'auth_uniacid'  => 1 ,
    //授权的产品ID
    'auth_goods_id' => 21


];