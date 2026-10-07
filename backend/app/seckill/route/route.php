<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2024/9/19/019
 * Time: 23:00
 * docs:
 */

use think\facade\Route;

//商城后端路由表
Route::group('admin', function () {

    Route::post('seckillAdd', 'Admin/seckillAdd');

    Route::get('getList', 'Admin/getList');

    Route::get('getListV2', 'Admin/getListV2');

    Route::post('seckillEdit', 'Admin/seckillEdit');

    Route::get('seckillEdit', 'Admin/seckillEdit');
});


Route::group('app', function () {

    Route::get('seckillList', 'Index/seckillList');

    Route::get('seckillInfo', 'Index/seckillInfo');
});

