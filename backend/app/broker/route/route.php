<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/6
 * Time: 16:33
 * docs:
 */

use think\facade\Route;

//后端路由表
Route::group('admin', function () {

    Route::group('broker', function () {
        //用户列表
        Route::get('userList', 'Admin/userList');
        //增加经纪人
        Route::post('add', 'Admin/add');
        //列表
        Route::get('getList', 'Admin/getList');
        //编辑经纪人
        Route::post('update', 'Admin/update');
        //经纪人信息
        Route::get('update', 'Admin/update');
        //经纪人数据
        Route::get('getData', 'Admin/getData');
        //经纪人向导列表
        Route::get('coachList', 'Admin/coachList');
    });


});

Route::group('app', function () {
    //申请经纪人
    Route::post('Index/applyBroker', 'Index/applyBroker');
    //二维码
    Route::get('IndexBroker/brokerQr', 'IndexBroker/brokerQr');
    //首页
    Route::get('IndexBroker/index', 'IndexBroker/index');
    //申请提现
    Route::post('IndexBroker/applyWallet', 'IndexBroker/applyWallet');
    //提现流水
    Route::get('IndexBroker/walletList', 'IndexBroker/walletList');
    //技师列表
    Route::get('IndexBroker/getCoach', 'IndexBroker/getCoach');
    //收益列表
    Route::get('IndexBroker/cashList', 'IndexBroker/cashList');
    //代理商列表
    Route::get('IndexBroker/adminList', 'IndexBroker/adminList');
    //经纪人信息
    Route::get('Index/info', 'Index/info');
});