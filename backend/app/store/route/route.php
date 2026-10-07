<?php

use think\facade\Route;

//商城后端路由表
Route::group('admin', function () {

    Route::group('AdminStore', function () {
        //分类
        Route::get('storeList', 'AdminStore/storeList');

        Route::get('storeSelect', 'AdminStore/storeSelect');

        Route::get('storeInfo', 'AdminStore/storeInfo');

        Route::post('storeAdd', 'AdminStore/storeAdd');

        Route::post('storeUpdate', 'AdminStore/storeUpdate');

    });


    /*****************1.8版本******************/

    //套餐
    Route::group('package', function () {
        //添加
        Route::post('add', 'AdminPackage/add');
        //编辑
        Route::post('edit', 'AdminPackage/edit');
        //详情
        Route::get('getInfo', 'AdminPackage/getInfo');
        //列表
        Route::get('getList', 'AdminPackage/getList');
        //修改状态
        Route::post('updateStatus', 'AdminPackage/updateStatus');
        //列表不分页
        Route::get('getListByStoreId', 'AdminPackage/getListByStoreId');
    });

    //套餐
    Route::group('PackageOrder', function () {
        //列表
        Route::get('getList', 'AdminOrder/getList');
        //详情
        Route::get('getInfo', 'AdminOrder/getInfo');
        //退款列表
        Route::get('getRefundList', 'AdminOrder/getRefundList');
        //退款详情
        Route::get('refundInfo', 'AdminOrder/refundInfo');
        //评论列表
        Route::get('commentList', 'AdminOrder/commentList');
        //删除评论
        Route::post('delComment', 'AdminOrder/delComment');
        //新增评论
        Route::post('addComment', 'AdminOrder/addComment');
        //退款审核
        Route::post('refundCheck', 'AdminOrder/refundCheck');
    });

});


//商城后端路由表
Route::group('app', function () {

    Route::group('IndexStore', function () {
        //分类
        Route::get('storeList', 'IndexStore/storeList');

        Route::get('storeSelect', 'IndexStore/storeSelect');

        Route::get('storeInfo', 'IndexStore/storeInfo');

        Route::get('storeServiceList', 'IndexStore/storeServiceList');

        Route::get('commentList', 'IndexStore/commentList');

        Route::post('storeAdd', 'IndexStore/storeAdd');

        Route::post('storeUpdate', 'IndexStore/storeUpdate');

    });


    /*****************1.8版本******************/

    Route::group('IndexPackage', function () {
        //套餐列表
        Route::get('storePackList', 'IndexPackage/storePackList');
        //套餐详情
        Route::get('storePackInfo', 'IndexPackage/storePackInfo');
        //收藏
        Route::post('collect', 'IndexPackage/collect');
        //取消收藏
        Route::post('cancelCollect', 'IndexPackage/cancelCollect');
        //收藏列表
        Route::get('collectList', 'IndexPackage/collectList');
    });

    //用户套餐订单
    Route::group('IndexOrder', function () {
        //下单
        Route::post('payOrder', 'IndexOrder/payOrder');
        //订单列表
        Route::get('orderList', 'IndexOrder/orderList');
        //重新支付
        Route::post('rePayOrder', 'IndexOrder/rePayOrder');
        //删除订单
        Route::post('delOrder', 'IndexOrder/delOrder');
        //订单详情
        Route::get('orderInfo', 'IndexOrder/orderInfo');
        //评价订单
        Route::post('addComment', 'IndexOrder/addComment');
        //订单申请退款
        Route::post('applyRefund', 'IndexOrder/applyRefund');
        //退款订单列表
        Route::get('refundList', 'IndexOrder/refundList');
        //退款订单详情
        Route::get('refundInfo', 'IndexOrder/refundInfo');
        //取消退款
        Route::post('refundCancel', 'IndexOrder/refundCancel');
        //删除售后订单
        Route::post('refundDel', 'IndexOrder/refundDel');
        //订单数量
        Route::get('orderCount', 'IndexOrder/orderCount');
        //虚拟手机号
        Route::post('getVirtualPhone', 'IndexOrder/getVirtualPhone');
    });

    //门店套餐订单
    Route::group('StoreOrder', function () {
        //订单详情
        Route::get('orderInfo', 'StoreOrder/orderInfo');
        //订单列表
        Route::get('orderList', 'StoreOrder/orderList');
        //核销订单
        Route::post('hxOrder', 'StoreOrder/hxOrder');
        //佣金信息
        Route::get('cashData', 'StoreOrder/cashData');
        //申请提现
        Route::post('applyWallet', 'StoreOrder/applyWallet');
        //提现流水
        Route::get('walletList', 'StoreOrder/walletList');
        //售后列表
        Route::get('refundList', 'StoreOrder/refundList');
        //退款订单详情
        Route::get('refundInfo', 'StoreOrder/refundInfo');
        //退款订单审核
        Route::post('refundCheck', 'StoreOrder/refundCheck');
        //订单数量
        Route::get('orderCount', 'StoreOrder/orderCount');
        //查看评论
        Route::get('seeComment', 'StoreOrder/seeComment');
        //虚拟手机号
        Route::post('getVirtualPhone', 'StoreOrder/getVirtualPhone');
    });

    //门店套餐管理
    Route::group('StorePackage', function () {
        //添加
        Route::post('add', 'StorePackageHandle/add');
        //编辑
        Route::post('edit', 'StorePackageHandle/edit');
        //详情
        Route::get('getInfo', 'StorePackageHandle/getInfo');
        //详情
        Route::get('getList', 'StorePackageHandle/getList');
        //修改状态
        Route::post('updateStatus', 'StorePackageHandle/updateStatus');
        //添加秒杀
        Route::post('seckillAdd', 'StorePackageHandle/seckillAdd');
        //秒杀列表
        Route::get('getSeckillList', 'StorePackageHandle/getSeckillList');
        //秒杀修改
        Route::post('seckillEdit', 'StorePackageHandle/seckillEdit');
        //秒杀修改
        Route::get('seckillEdit', 'StorePackageHandle/seckillEdit');
    });

});
























