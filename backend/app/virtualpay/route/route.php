<?php

use think\facade\Route;

//虚拟支付平台推送（微信服务器调用，无需登录）
Route::any('Notify/receive', 'Notify/receive');

//用户端
Route::post('Index/orderStatus', 'Index/orderStatus');
Route::post('Index/checkPending', 'Index/checkPending');

//管理端
Route::group('admin', function () {

    Route::get('AdminSetting/configInfo', 'AdminSetting/configInfo');

    Route::post('AdminSetting/configUpdate', 'AdminSetting/configUpdate');

});
