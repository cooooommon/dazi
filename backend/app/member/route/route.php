<?php
/**
 * Created by PhpStorm
 * User: shurong(贝润网络)
 * Date: 2024/11/11
 * Time: 11:37
 * docs:
 */


use think\facade\Route;

//商城后端路由表
Route::group('admin', function () {

    Route::post('configSet', 'Admin/configSet');

    Route::get('configSet', 'Admin/configSet');

    Route::get('cardList', 'Admin/cardList');

    Route::post('cardAdd', 'Admin/cardAdd');

    Route::post('cardUpdate', 'Admin/cardUpdate');

    Route::get('cardInfo', 'Admin/cardInfo');

    Route::get('orderList', 'Admin/orderList');
});

Route::group('app', function () {

    Route::get('cardList', 'Index/cardList');

    Route::get('configInfo', 'Index/configInfo');

    Route::post('payOrder', 'Index/payOrder');

    Route::get('orderList', 'Index/orderList');

    Route::get('cashList', 'Index/cashList');

    Route::post('rePayOrder', 'Index/rePayOrder');
});