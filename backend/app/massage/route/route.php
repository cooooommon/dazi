<?php

use think\facade\Route;

//商城后端路由表
Route::group('admin', function () {
    //商品列表
    Route::post('Admin/login', 'Admin/login');

    Route::get('Admin/getConfig', 'Admin/getConfig');

    Route::get('Admin/getW7TmpV2', 'Admin/getW7TmpV2');
    //配置详情
    Route::post('AdminSetting/configInfo', 'AdminSetting/configInfo');
    //车费配置详情
    Route::get('AdminSetting/carConfigInfo', 'AdminSetting/carConfigInfo');
    //编辑车费配置
    Route::post('AdminSetting/carConfigUpdate', 'AdminSetting/carConfigUpdate');
    //配置修改
    Route::post('AdminSetting/configUpdate', 'AdminSetting/configUpdate');

    Route::post('AdminSetting/payConfigInfo', 'AdminSetting/payConfigInfo');

    Route::post('AdminSetting/payConfigUpdate', 'AdminSetting/payConfigUpdate');
    //banne列表
    Route::post('AdminSetting/bannerList', 'AdminSetting/bannerList');
    //banner添加
    Route::post('AdminSetting/bannerAdd', 'AdminSetting/bannerAdd');
    //banner编辑
    Route::post('AdminSetting/bannerUpdate', 'AdminSetting/bannerUpdate');
    //banner详情（id）
    Route::get('AdminSetting/bannerInfo', 'AdminSetting/bannerInfo');
    //修改密码（pass）
    Route::post('AdminSetting/updatePass', 'AdminSetting/updatePass');
    //评价标签列表
    Route::get('AdminSetting/lableList', 'AdminSetting/lableList');
    //评价标签详情
    Route::get('AdminSetting/lableInfo', 'AdminSetting/lableInfo');
    //添加评价标签
    Route::post('AdminSetting/lableAdd', 'AdminSetting/lableAdd');
    //编辑评价标签
    Route::post('AdminSetting/lableUpdate', 'AdminSetting/lableUpdate');

    Route::get('AdminSetting/adminList', 'AdminSetting/adminList');

    Route::get('AdminSetting/adminInfo', 'AdminSetting/adminInfo');

    Route::post('AdminSetting/adminAdd', 'AdminSetting/adminAdd');

    Route::post('AdminSetting/adminUpdate', 'AdminSetting/adminUpdate');

    Route::post('AdminSetting/adminStatusUpdate', 'AdminSetting/adminStatusUpdate');

    Route::get('AdminSetting/userSelect', 'AdminSetting/userSelect');

    Route::get('AdminSetting/cityList', 'AdminSetting/cityList');

    Route::get('AdminSetting/cityInfo', 'AdminSetting/cityInfo');

    Route::get('AdminSetting/citySelect', 'AdminSetting/citySelect');

    Route::get('AdminSetting/getCity', 'AdminSetting/getCity');

    Route::post('AdminSetting/cityAdd', 'AdminSetting/cityAdd');

    Route::post('AdminSetting/cityUpdate', 'AdminSetting/cityUpdate');

    Route::get('AdminSetting/adminSelect', 'AdminSetting/adminSelect');

    Route::get('AdminSetting/getSaasAuth', 'AdminSetting/getSaasAuth');

    Route::get('AdminSetting/helpConfigInfo', 'AdminSetting/helpConfigInfo');

    Route::post('AdminSetting/helpConfigUpate', 'AdminSetting/helpConfigUpate');

    Route::get('AdminSetting/sendMsgConfigInfo', 'AdminSetting/sendMsgConfigInfo');

    Route::post('AdminSetting/sendMsgConfigUpdate', 'AdminSetting/sendMsgConfigUpdate');

    Route::get('AdminSetting/shortCodeConfigInfo', 'AdminSetting/shortCodeConfigInfo');

    Route::post('AdminSetting/shortCodeConfigUpdate', 'AdminSetting/shortCodeConfigUpdate');

    Route::get('AdminSetting/addClockInfo', 'AdminSetting/addClockInfo');

    Route::get('AdminSetting/provinceList', 'AdminSetting/provinceList');

    Route::post('AdminSetting/addClockUpdate', 'AdminSetting/addClockUpdate');

    //向导列表
    Route::get('AdminCoach/coachList', 'AdminCoach/coachList');
    //向导详情
    Route::get('AdminCoach/coachInfo', 'AdminCoach/coachInfo');
    //向导审核(status2通过,3拒绝,sh_text)
    Route::post('AdminCoach/coachUpdate', 'AdminCoach/coachUpdate');
    //向导等级列表
    Route::get('AdminCoach/levelList', 'AdminCoach/levelList');
    //添加向导等级
    Route::post('AdminCoach/levelAdd', 'AdminCoach/levelAdd');
    //编辑向导等级
    Route::post('AdminCoach/levelUpdate', 'AdminCoach/levelUpdate');
    //向导等级详情
    Route::get('AdminCoach/levelInfo', 'AdminCoach/levelInfo');
    //向导提现申请列表(type1是服务费提现，2是车费)
    Route::get('AdminCoach/walletList', 'AdminCoach/walletList');
    //提现详情
    Route::get('AdminCoach/walletInfo', 'AdminCoach/walletInfo');
    //通过提现(online:1线上，0线下)
    Route::post('AdminCoach/walletPass', 'AdminCoach/walletPass');
    //拒绝提现
    Route::post('AdminCoach/walletNoPass', 'AdminCoach/walletNoPass');
    //报警列表
    Route::get('AdminCoach/policeList', 'AdminCoach/policeList');
    //编辑报警
    Route::post('AdminCoach/policeUpdate', 'AdminCoach/policeUpdate');

    Route::post('AdminCoach/coachDataUpdate', 'AdminCoach/coachDataUpdate');
    //技师服务修改
    Route::post('AdminCoach/coachServiceUpdate', 'AdminCoach/coachServiceUpdate');
    //技师服务添加
    Route::post('AdminCoach/coachServiceAdd', 'AdminCoach/coachServiceAdd');

    //优惠券列表(搜索：name)
    Route::get('AdminCoupon/couponList', 'AdminCoupon/couponList');
    //优惠券详情（id）
    Route::get('AdminCoupon/couponInfo', 'AdminCoupon/couponInfo');
    //添加优惠券
    Route::post('AdminCoupon/couponAdd', 'AdminCoupon/couponAdd');
    //编辑优惠券
    Route::post('AdminCoupon/couponUpdate', 'AdminCoupon/couponUpdate');
    //活动详情
    Route::get('AdminCoupon/couponAtvInfo', 'AdminCoupon/couponAtvInfo');
    //编辑活动
    Route::post('AdminCoupon/couponAtvUpdate', 'AdminCoupon/couponAtvUpdate');
    //后台派发卡券(coupon_id,user_id)
    Route::post('AdminCoupon/couponRecordAdd', 'AdminCoupon/couponRecordAdd');


    //储值充值卡列表
    Route::get('AdminBalance/cardList', 'AdminBalance/cardList');
    //储值充值卡列表
    Route::post('AdminBalance/cardAdd', 'AdminBalance/cardAdd');
    //编辑充值卡
    Route::post('AdminBalance/cardUpdate', 'AdminBalance/cardUpdate');
    //充值卡详情
    Route::get('AdminBalance/cardInfo', 'AdminBalance/cardInfo');
    //储值订单列表
    Route::get('AdminBalance/orderList', 'AdminBalance/orderList');
    //充值订单详情
    Route::get('AdminBalance/orderInfo', 'AdminBalance/orderInfo');

    Route::get('AdminBalance/orderInfo', 'AdminBalance/orderInfo');
    //服务列表(搜索：name)
    Route::get('AdminService/serviceList', 'AdminService/serviceList');
    //服务详情
    Route::get('AdminService/serviceInfo', 'AdminService/serviceInfo');
    //添加服务
    Route::post('AdminService/serviceAdd', 'AdminService/serviceAdd');
    //编辑服务|上下架删除
    Route::post('AdminService/serviceUpdate', 'AdminService/serviceUpdate');

    Route::post('AdminService/checkStoreGoods', 'AdminService/checkStoreGoods');


    //后台提现列表
    Route::get('AdminCoach/walletList', 'AdminCoach/walletList');
    //同意打款（id,status=2,online 1：线上，0线下）
    Route::post('AdminCoach/walletPass', 'AdminCoach/walletPass');
    //拒绝打款（id,status=3）
    Route::post('AdminCoach/walletNoPass', 'AdminCoach/walletNoPass');
    //财务管理
    Route::get('AdminCoach/financeList', 'AdminCoach/financeList');

    Route::get('AdminCoach/userLabelList', 'AdminCoach/userLabelList');


    Route::get('AdminCoach/coachUserList', 'AdminCoach/coachUserList');

    Route::post('AdminCoach/coachAdd', 'AdminCoach/coachAdd');

    Route::post('AdminCoach/coachUpdateCheck', 'AdminCoach/coachUpdateCheck');

    Route::get('AdminCoach/coachUpdateInfo', 'AdminCoach/coachUpdateInfo');
    //商品列表
    Route::get('AdminGoods/goodsList', 'AdminGoods/goodsList');
    //审核商品数量
    Route::get('AdminGoods/goodsCount', 'AdminGoods/goodsCount');
    //审核详情
    Route::get('AdminGoods/shInfo', 'AdminGoods/shInfo');
    //审核商品详情
    Route::get('AdminGoods/shGoodsInfo', 'AdminGoods/shGoodsInfo');
    //同意|驳回申请 status 2 同意 3驳回
    Route::post('AdminGoods/shUpdate', 'AdminGoods/shUpdate');
    //用户列表
    Route::get('AdminUser/userList', 'AdminUser/userList');

    Route::group('AdminOrder', function () {

        Route::get('cateList', 'AdminOrder/cateList');
        //退款列表
        Route::get('refundOrderList', 'AdminOrder/refundOrderList');
        //订单列表
        Route::get('orderList', 'AdminOrder/orderList');
        //订单详情
        Route::get('orderInfo', 'AdminOrder/orderInfo');
        //退款详情
        Route::get('refundOrderInfo', 'AdminOrder/refundOrderInfo');
        //拒绝退款
        Route::post('noPassRefund', 'AdminOrder/noPassRefund');
        //同意退款
        Route::post('passRefund', 'AdminOrder/passRefund');
        //订单评价列表
        Route::get('commentList', 'AdminOrder/commentList');
        //编辑订单评价
        Route::post('commentUpdate', 'AdminOrder/commentUpdate');
        //评价标签列表
        Route::get('commentLableList', 'AdminOrder/commentLableList');
        //评价标签详情
        Route::get('commentLableInfo', 'AdminOrder/commentLableInfo');
        //添加评价标签
        Route::post('commentLableAdd', 'AdminOrder/commentLableAdd');
        //编辑评价标签
        Route::post('commentLableUpdate', 'AdminOrder/commentLableUpdate');
        //提示列表(type,have_look,start_time,end_time)
        Route::get('noticeList', 'AdminOrder/noticeList');
        //编辑提示()
        Route::post('noticeUpdate', 'AdminOrder/noticeUpdate');
        //未查看的数量
        Route::post('noLookCount', 'AdminOrder/noLookCount');
        //全部已读
        Route::post('allLook', 'AdminOrder/allLook');

        Route::post('adminUpdateOrder', 'AdminOrder/adminUpdateOrder');

        Route::post('orderChangeCoach', 'AdminOrder/orderChangeCoach');

        Route::get('orderChangeCoachList', 'AdminOrder/orderChangeCoachList');

        Route::get('orderUpRecord', 'AdminOrder/orderUpRecord');

        Route::get('lableList', 'AdminOrder/lableList');

        Route::post('addComment', 'AdminOrder/addComment');


    });
    //订单导出
    Route::get('AdminExcel/orderList', 'AdminExcel/orderList');
    //财务导出
    Route::get('AdminExcel/dateCount', 'AdminExcel/dateCount');

    Route::get('AdminExcel/subDataList', 'AdminExcel/subDataList');
    Route::get('AdminExcel/demandOrder', 'AdminExcel/demandOrder');
    //打印机详情(id)
    Route::get('AdminPrinter/printerInfo', 'AdminPrinter/printerInfo');
    //编辑打印机
    Route::post('AdminPrinter/printerUpdate', 'AdminPrinter/printerUpdate');
    //打印机列表
    Route::get('AdminPrinter/printerList', 'AdminPrinter/printerList');
    //打印机添加
    Route::post('AdminPrinter/printerAdd', 'AdminPrinter/printerAdd');
    //佣金记录
    Route::post('AdminUser/commList', 'AdminUser/commList');

//    Route::get('AdminUser/commList', 'AdminUser/commList');
    Route::get('AdminUser/commList', 'AdminUser/cashList');

    Route::post('AdminUser/adminUpdateCoachCommisson', 'AdminUser/adminUpdateCoachCommisson');

    Route::get('AdminUser/cashList', 'AdminUser/cashList');

    Route::post('AdminUser/delUserLabel', 'AdminUser/delUserLabel');

    Route::post('AdminUser/applyWallet', 'AdminUser/applyWallet');

    Route::get('AdminReseller/resellerList', 'AdminReseller/resellerList');

    Route::get('AdminReseller/resellerInfo', 'AdminReseller/resellerInfo');

    Route::post('AdminReseller/resellerUpdate', 'AdminReseller/resellerUpdate');

    Route::get('AdminReseller/partnerDataList', 'AdminReseller/partnerDataList');


    Route::group('AdminChannel', function () {

        Route::get('cateList', 'AdminChannel/cateList');

        Route::get('cateSelect', 'AdminChannel/cateSelect');

        Route::get('channelSelect', 'AdminChannel/channelSelect');

        Route::post('cateAdd', 'AdminChannel/cateAdd');

        Route::post('cateUpdate', 'AdminChannel/cateUpdate');

        Route::get('cateInfo', 'AdminChannel/cateInfo');

        Route::get('channelList', 'AdminChannel/channelList');

        Route::get('channelInfo', 'AdminChannel/channelInfo');

        Route::post('channelUpdate', 'AdminChannel/channelUpdate');

    });

    /********************按摩6.0接口**********************/
    //添加物料分类
    Route::post('AdminShop/addCarte', 'AdminShop/addCarte');
    //编辑物料分类
    Route::post('AdminShop/editCarte', 'AdminShop/editCarte');
    Route::get('AdminShop/editCarte', 'AdminShop/editCarte');
    //分类列表
    Route::get('AdminShop/carteList', 'AdminShop/carteList');
    //上下架、删除
    Route::post('AdminShop/carteStatus', 'AdminShop/carteStatus');


    //分类下拉
    Route::get('AdminShop/goodsCarteList', 'AdminShop/goodsCarteList');
    //添加商品
    Route::post('AdminShop/addGoods', 'AdminShop/addGoods');
    //编辑商品
    Route::post('AdminShop/editGoods', 'AdminShop/editGoods');
    Route::get('AdminShop/editGoods', 'AdminShop/editGoods');
    //商品列表
    Route::get('AdminShop/goodsList', 'AdminShop/goodsList');
    //商品上下架、删除
    Route::post('AdminShop/goodsStatus', 'AdminShop/goodsStatus');


    //反馈记录列表
    Route::get('AdminSetting/feedbackList', 'AdminSetting/feedbackList');
    //反馈记录详情
    Route::get('AdminSetting/feedbackInfo', 'AdminSetting/feedbackInfo');
    //处理反馈记录
    Route::post('AdminSetting/feedbackHandle', 'AdminSetting/feedbackHandle');
    //申诉记录列表
    Route::get('AdminSetting/appealList', 'AdminSetting/appealList');
    //申诉记录详情
    Route::get('AdminSetting/appealInfo', 'AdminSetting/appealInfo');
    //处理申诉记录
    Route::post('AdminSetting/appealHandle', 'AdminSetting/appealHandle');

    Route::post('AdminSetting/configUpdateSchedule', 'AdminSetting/configUpdateSchedule');

    Route::get('AdminSetting/configInfoSchedule', 'AdminSetting/configInfoSchedule');

    Route::get('AdminSetting/getCarConfigList', 'AdminSetting/getCarConfigList');

    Route::get('AdminSetting/getCarConfigInfo', 'AdminSetting/getCarConfigInfo');

    Route::post('AdminSetting/getCarConfigAdd', 'AdminSetting/getCarConfigAdd');

    Route::post('AdminSetting/getCarConfigUpdate', 'AdminSetting/getCarConfigUpdate');

    Route::post('AdminSetting/getCarConfigDel', 'AdminSetting/getCarConfigDel');

    Route::get('AdminSetting/configSettingInfo', 'AdminSetting/configSettingInfo');

    Route::post('AdminSetting/configSettingUpdate', 'AdminSetting/configSettingUpdate');

    Route::get('AdminSetting/distributionConfigInfo', 'AdminSetting/distributionConfigInfo');

    Route::post('AdminSetting/distributionConfigUpdate', 'AdminSetting/distributionConfigUpdate');


    Route::get('AdminSetting/userLabelList', 'AdminSetting/userLabelList');

    Route::get('AdminSetting/userLabelInfo', 'AdminSetting/userLabelInfo');

    Route::post('AdminSetting/userLabelUpdate', 'AdminSetting/userLabelUpdate');

    Route::post('AdminSetting/userLabelAdd', 'AdminSetting/userLabelAdd');

    Route::get('AdminIndex/orderData', 'AdminIndex/orderData');

    Route::get('AdminIndex/agentOrderData', 'AdminIndex/agentOrderData');

    Route::get('AdminIndex/coachAndUserData', 'AdminIndex/coachAndUserData');
    //
    Route::get('AdminIndex/coachSaleData', 'AdminIndex/coachSaleData');


    Route::group('AdminArticle', function () {

        Route::get('fieldList', 'AdminArticle/fieldList');
        Route::get('fieldSelect', 'AdminArticle/fieldSelect');

        Route::get('fieldInfo', 'AdminArticle/fieldInfo');

        Route::get('articleList', 'AdminArticle/articleList');

        Route::post('fieldAdd', 'AdminArticle/fieldAdd');

        Route::post('fieldUpdate', 'AdminArticle/fieldUpdate');

        Route::get('articleInfo', 'AdminArticle/articleInfo');

        Route::post('articleAdd', 'AdminArticle/articleAdd');

        Route::post('articleUpdate', 'AdminArticle/articleUpdate');

        Route::get('subTitle', 'AdminArticle/subTitle');

        Route::get('subDataList', 'AdminArticle/subDataList');

    });


    //邀约设置
    Route::post('AdminSetting/demandSetting', 'AdminSetting/demandSetting');
    //邀约设置
    Route::get('AdminSetting/demandSetting', 'AdminSetting/demandSetting');
    //邀约审核列表
    Route::get('AdminOrder/demandOrderList', 'AdminOrder/demandOrderList');
    //邀约订单详情
    Route::get('AdminOrder/demandOrderInfo', 'AdminOrder/demandOrderInfo');
    //邀约订单审核
    Route::post('AdminOrder/demandExamine', 'AdminOrder/demandExamine');
    //服务类型列表
    Route::get('AdminSetting/demandTypeList', 'AdminSetting/demandTypeList');
    //服务类型添加
    Route::post('AdminSetting/demandTypeAdd', 'AdminSetting/demandTypeAdd');
    //服务类型编辑
    Route::get('AdminSetting/demandTypeEdit', 'AdminSetting/demandTypeEdit');
    //服务类型编辑
    Route::post('AdminSetting/demandTypeEdit', 'AdminSetting/demandTypeEdit');
    //服务类型删除
    Route::post('AdminSetting/demandTypeDel', 'AdminSetting/demandTypeDel');
    //邀约订单
    Route::get('AdminOrder/demandOder', 'AdminOrder/demandOder');
    //详情
    Route::get('AdminOrder/demandInfo', 'AdminOrder/demandInfo');
    //退款邀约订单列表
    Route::get('AdminOrder/demandRefundOrder', 'AdminOrder/demandRefundOrder');
    //退款邀约订单操作
    Route::post('AdminOrder/demandRefund', 'AdminOrder/demandRefund');
    //个性标签
    Route::get('AdminCoach/coachTag', 'AdminCoach/coachTag');
    //个性标签删除
    Route::post('AdminCoach/coachTagDel', 'AdminCoach/coachTagDel');
    //个性标签编辑
    Route::post('AdminCoach/coachTagEdit', 'AdminCoach/coachTagEdit');
    //个性标签添加
    Route::post('AdminCoach/coachTagAdd', 'AdminCoach/coachTagAdd');
    //个性标签列表不分页
    Route::get('AdminCoach/coachTagList', 'AdminCoach/coachTagList');

    /***********1.3版本*************/
    //入驻协议
    Route::get('AdminSetting/agreement', 'AdminSetting/agreement');
    Route::post('AdminSetting/agreement', 'AdminSetting/agreement');
    //加盟商表单列表
    Route::get('AdminSetting/joinList', 'AdminSetting/joinList');
    //已阅
    Route::post('AdminSetting/joinRead', 'AdminSetting/joinRead');

    /***********1.4版本*************/
    //列表
    Route::get('AdminStore/getList', 'AdminStore/getList');
    //详情
    Route::get('AdminStore/info', 'AdminStore/getInfo');
    //审核
    Route::post('AdminStore/check', 'AdminStore/check');
    //详情
    Route::get('AdminStore/reInfo', 'AdminStore/reInfo');
    //重新审核
    Route::post('AdminStore/reCheck', 'AdminStore/reCheck');
    //修改状态
    Route::post('AdminStore/changeStatus', 'AdminStore/changeStatus');
    //设置向导抽成比例
    Route::post('AdminCoach/setBalance', 'AdminCoach/setBalance');

    /***********1.6版本*************/
    //代理商编辑向导信息
    Route::post('AdminCoach/coachUpdateAdmin', 'AdminCoach/coachUpdateAdmin');


    /***********1.7版本**************/
    //diy相关
    Route::get('AdminSetting/diyInfo', 'AdminSetting/diyInfo');
    Route::get('AdminSetting/getTabbar', 'AdminSetting/getTabbar');
    Route::get('AdminSetting/getFunctionPageList', 'AdminSetting/getFunctionPageList');
    Route::get('AdminSetting/getFunctionPageInfo', 'AdminSetting/getFunctionPageInfo');
    Route::post('AdminSetting/diyUpdate', 'AdminSetting/diyUpdate');

    //设置黑名单
    Route::post('AdminUser/setBlacklist', 'AdminUser/setBlacklist');
    //黑名单列表
    Route::get('AdminUser/blacklist', 'AdminUser/blacklist');
    //获取更新记录
    Route::get('AdminSetting/getUpRecord', 'AdminSetting/getUpRecord');
    //分销商下级
    Route::get('AdminReseller/getSubList', 'AdminReseller/getSubList');
    //渠道商员工
    Route::get('AdminChannel/staffList', 'AdminChannel/staffList');
    //切换上级
    Route::post('AdminChannel/changeChannel', 'AdminChannel/changeChannel');
    //批量设置比例
    Route::post('AdminChannel/changeBalance', 'AdminChannel/changeBalance');
    //手动修改余额列表
    Route::get('AdminChannel/getCashList', 'AdminChannel/getCashList');


    /***********1.8版本**************/
    //门店分类插入
    Route::post('AdminStore/typeAdd', 'AdminStore/typeAdd');
    //门店分类编辑
    Route::post('AdminStore/typeUpdate', 'AdminStore/typeUpdate');
    //门店分类列表
    Route::get('AdminStore/typeList', 'AdminStore/typeList');
    //门店添加
    Route::post('AdminStore/addStore', 'AdminStore/addStore');
    //门店分类无分页
    Route::get('AdminStore/typeListNoPage', 'AdminStore/typeListNoPage');
    //门店置顶
    Route::post('AdminStore/storeTop', 'AdminStore/storeTop');
    //门店置顶
    Route::get('AdminStore/storeUserList', 'AdminStore/storeUserList');
    //diy门店列表
    Route::get('AdminStore/getListDiy', 'AdminStore/getListDiy');
    //门店编辑
    Route::post('AdminStore/editStore', 'AdminStore/editStore');
    //添加评论
    Route::post('AdminOrder/addCommentV2', 'AdminOrder/addCommentV2');
    //技师业绩
    Route::get('AdminCoach/coachCashData', 'AdminCoach/coachCashData');
    //解除绑定
    Route::post('AdminCoach/cancelBroker', 'AdminCoach/cancelBroker');
    //套餐订单导出
    Route::get('AdminExcel/packageOrder', 'AdminExcel/packageOrder');


    /***********1.9行业拆分**********/
    //门店diy内部链接
    Route::get('AdminSetting/getFunctionPageListForStore', 'AdminSetting/getFunctionPageListForStore');


    /***********2.0版本**********/
    Route::get('AdminBalance/payWater', 'AdminBalance/payWater');
    Route::post('AdminBalance/payBalanceOrder', 'AdminBalance/payBalanceOrder');
    Route::post('AdminStore/editStoreBalance', 'AdminStore/editStoreBalance');
    //审核通过发送模板消息
    Route::get('AdminOrder/sendMsg', 'AdminOrder/sendMsg');

    /***********2.1版本**********/
    //套餐订单导出
    Route::get('AdminExcel/userList', 'AdminExcel/userList');

    /***********2.2版本**********/
    //批量设置渠道商绑定时间
    Route::post('AdminChannel/setBindTime', 'AdminChannel/setBindTime');

    /***********2.3版本**********/
    //服务分类插入
    Route::post('AdminService/typeAdd', 'AdminService/typeAdd');
    //服务分类编辑
    Route::post('AdminService/typeUpdate', 'AdminService/typeUpdate');
    //服务分类列表
    Route::get('AdminService/typeList', 'AdminService/typeList');
    //服务分类无分页
    Route::get('AdminService/typeListNoPage', 'AdminService/typeListNoPage');
    //地址解析
    Route::get('AdminSetting/addressToLocation', 'AdminSetting/addressToLocation');
    //逆地址解析
    Route::get('AdminSetting/locationToAddress', 'AdminSetting/locationToAddress');

    Route::get('AdminStore/getStoreList', 'AdminStore/getStoreList');

    /***********2.4版本**********/
    Route::get('AdminUser/getUserInfo', 'AdminUser/getUserInfo');

    Route::get('AdminUser/integralList', 'AdminUser/integralList');
});


//商城后端路由表
Route::group('app', function () {
    //首页
    Route::get('Index/index', 'Index/index');

    Route::get('Index/serviceSelect', 'Index/serviceSelect');

    Route::get('Index/plugAuth', 'Index/plugAuth');

    Route::get('Index/recommendCoach', 'Index/recommendCoach');

    Route::get('Index/getCity', 'Index/getCity');

    Route::get('Index/couponList', 'Index/couponList');

    Route::post('Index/userGetCoupon', 'Index/userGetCoupon');

    Route::get('Index/coachInfo', 'Index/coachInfo');
    //再来一单(order_id)
    Route::post('Index/onceMoreOrder', 'Index/onceMoreOrder');
    //评价列表(coach_id)
    Route::get('Index/commentList', 'Index/commentList');
    //服务列表(sort:price 价格排序 ，total_sale销量排序 star评价排序 ,)
    Route::get('Index/serviceList', 'Index/serviceList');
    //服务详情(id)
    Route::get('Index/serviceInfo', 'Index/serviceInfo');
    //服务向导列表(ser_id，服务id,lat,lng)
    Route::get('Index/serviceCoachList', 'Index/serviceCoachList');
    //向导服务列表(coach_id)
    Route::get('Index/coachServiceList', 'Index/coachServiceList');
    //向导服务列表(coach_id)
    Route::get('Index/coachServiceListPage', 'Index/coachServiceListPage');

    Route::get('Index/getMapInfo', 'Index/getMapInfo');

    Route::get('Index/typeServiceCoachList', 'Index/typeServiceCoachList');

    Route::post('IndexUser/delUserInfo', 'IndexUser/delUserInfo');
    //用户授权
    Route::post('IndexUser/userUpdate', 'IndexUser/userUpdate');

    Route::post('IndexUser/attestationCoach', 'IndexUser/attestationCoach');
    //申请向导
    Route::post('IndexUser/coachApply', 'IndexUser/coachApply');
    //教练收藏列表
    Route::get('IndexUser/coachCollectList', 'IndexUser/coachCollectList');
    //添加向导收藏(coach_id)
    Route::post('IndexUser/addCollect', 'IndexUser/addCollect');
    //删除向导收藏(coach_id)
    Route::post('IndexUser/delCollect', 'IndexUser/delCollect');

    Route::post('IndexUser/shieldCoachAdd', 'IndexUser/shieldCoachAdd');

    Route::post('IndexUser/shieldCoachDel', 'IndexUser/shieldCoachDel');

    Route::get('IndexUser/shieldCoachList', 'IndexUser/shieldCoachList');

    Route::get('IndexUser/userInfo', 'IndexUser/userInfo');

    Route::post('IndexUser/reportPhone', 'IndexUser/reportPhone');
    //优惠券活动详情
    Route::post('IndexUser/couponAtvInfo', 'IndexUser/couponAtvInfo');
    //用户|团长个人中心
    Route::get('IndexUser/index', 'IndexUser/index');
    //个人团长信息
    Route::get('IndexUser/coachInfo', 'IndexUser/coachInfo');
    //用户地址列表
    Route::get('IndexUser/addressList', 'IndexUser/addressList');

    Route::get('IndexUser/getVirtualPhone', 'IndexUser/getVirtualPhone');

    Route::get('IndexUser/getDemandVirtualPhone', 'IndexUser/getDemandVirtualPhone');
    //地址详情
    Route::get('IndexUser/addressInfo', 'IndexUser/addressInfo');
    //添加地址
    Route::post('IndexUser/addressAdd', 'IndexUser/addressAdd');
    //编辑地址
    Route::post('IndexUser/addressUpdate', 'IndexUser/addressUpdate');
    //删除地址
    Route::post('IndexUser/addressDel', 'IndexUser/addressDel');
    //获取默认地址
    Route::get('IndexUser/getDefultAddress', 'IndexUser/getDefultAddress');
    //活动二维码
    Route::post('IndexUser/atvQr', 'IndexUser/atvQr');
    //用户优惠券列表（status1，2，3）
    Route::get('IndexUser/userCouponList', 'IndexUser/userCouponList');
    //删除优惠券（coupon_id）
    Route::post('IndexUser/couponDel', 'IndexUser/couponDel');
    //获取配置信息
    Route::get('Index/configInfo', 'Index/configInfo');
    //向导首页
    Route::get('IndexCoach/coachIndex', 'IndexCoach/coachIndex');
    //向导编辑
    Route::post('IndexCoach/coachUpdate', 'IndexCoach/coachUpdate');
    //团长核销订单（id）
    Route::post('IndexCoach/hxOrder', 'IndexCoach/hxOrder');
    //订单列表
    Route::get('IndexCoach/orderList', 'IndexCoach/orderList');
    //团长佣金信息
    Route::get('IndexCoach/capCashInfo', 'IndexCoach/capCashInfo');
    //团长佣金信息(车费)
    Route::get('IndexCoach/capCashInfoCar', 'IndexCoach/capCashInfoCar');

    Route::get('IndexCoach/balanceCommissionList', 'IndexCoach/balanceCommissionList');

    Route::get('IndexCoach/balanceCommissionData', 'IndexCoach/balanceCommissionData');
    //提现记录
    Route::get('IndexCoach/capCashList', 'IndexCoach/capCashList');
    //申请提现(apply_price,text,type：1服务费提现，2车费提现)
    Route::post('IndexCoach/applyWallet', 'IndexCoach/applyWallet');
    //向导获取虚拟电话 order_id
    Route::post('IndexCoach/getVirtualPhone', 'IndexCoach/getVirtualPhone');
    //向导获取虚拟电话 order_id
    Route::post('IndexCoach/getDemandVirtualPhone', 'IndexCoach/getDemandVirtualPhone');
    //报警
    Route::post('IndexCoach/police', 'IndexCoach/police');
    //向导修改订单信息(type,order_id)
    Route::post('IndexCoach/updateOrder', 'IndexCoach/updateOrder');

    Route::post('IndexCoach/coachUpdateV2', 'IndexCoach/coachUpdateV2');

    Route::post('IndexCoach/shieldUserAdd', 'IndexCoach/shieldUserAdd');

    Route::post('IndexCoach/shieldUserDel', 'IndexCoach/shieldUserDel');

    Route::get('IndexCoach/shieldCoachList', 'IndexCoach/shieldCoachList');


    Route::get('IndexGoods/indexCapList', 'IndexGoods/indexCapList');
    //选择楼长(cap_id)
    Route::post('IndexGoods/selectCap', 'IndexGoods/selectCap');
    //分类列表
    Route::get('IndexGoods/cateList', 'IndexGoods/cateList');
    //商品首页信息
    Route::get('IndexGoods/index', 'IndexGoods/index');

    //商品列表
    Route::get('IndexGoods/goodsList', 'IndexGoods/goodsList');
    //商品详情
    Route::get('IndexGoods/goodsInfo', 'IndexGoods/goodsInfo');

    //购物车信息（coach_id）
    Route::get('Index/carInfo', 'Index/carInfo');
    //添加购物车（service_id,coach_id,num = 1）
    Route::post('Index/addCar', 'Index/addCar');
    //删除购物车|减少购物车商品数量（id,num=1）
    Route::post('Index/delCar', 'Index/delCar');
    //批量删除购物车（coach）
    Route::post('Index/delSomeCar', 'Index/delSomeCar');
    //修改购物车（ID ：arr）
    Route::post('Index/carUpdate', 'Index/carUpdate');
    //
    Route::post('IndexOrder/payOrder', 'IndexOrder/payOrder');
    //下单的那个页面(coach_id，有优惠券就传 coupon_id)
    Route::get('IndexOrder/payOrderInfo', 'IndexOrder/payOrderInfo');
    //用户订单列表（pay_type,name）
    Route::get('IndexOrder/orderList', 'IndexOrder/orderList');

    Route::post('IndexOrder/delOrder', 'IndexOrder/delOrder');

    Route::get('IndexOrder/getUpOrderGoods', 'IndexOrder/getUpOrderGoods');

    Route::any('IndexOrder/upOrderGoods', 'IndexOrder/upOrderGoods');
    //订单详情
    Route::get('IndexOrder/orderInfo', 'IndexOrder/orderInfo');
    //重新支付
    Route::post('IndexOrder/rePayOrder', 'IndexOrder/rePayOrder');
    //取消订单
    Route::post('IndexOrder/cancelOrder', 'IndexOrder/cancelOrder');
    //申请退款（order_id,list:['id','num']）
    Route::post('IndexOrder/applyOrder', 'IndexOrder/applyOrder');
    //取消退款
    Route::post('IndexOrder/cancelRefundOrder', 'IndexOrder/cancelRefundOrder');
    //用户端退款列表（name,status）
    Route::get('IndexOrder/refundOrderList', 'IndexOrder/refundOrderList');
    //退款详情
    Route::get('IndexOrder/refundOrderInfo', 'IndexOrder/refundOrderInfo');
    //刷新订单二维码(id)
    Route::post('IndexOrder/refreshQr', 'IndexOrder/refreshQr');

    Route::post('IndexOrder/checkAddOrder', 'IndexOrder/checkAddOrder');
    //选中时间(coach_id,day)
    Route::get('IndexOrder/timeText', 'IndexOrder/timeText');

    Route::get('IndexOrder/dayText', 'IndexOrder/dayText');
    //添加评价(order_id,text，star)
    Route::post('IndexOrder/addComment', 'IndexOrder/addComment');

    Route::get('IndexOrder/lableList', 'IndexOrder/lableList');
    //可用的优惠券(coach_id)
    Route::get('IndexOrder/couponList', 'IndexOrder/couponList');

    Route::post('IndexOrder/userSignOrder', 'IndexOrder/userSignOrder');

    Route::get('IndexOrder/orderUpRecord', 'IndexOrder/orderUpRecord');

    Route::get('IndexOrder/getAddClockOrder', 'IndexOrder/getAddClockOrder');

    Route::post('IndexOrder/upOrderInfo', 'IndexOrder/upOrderInfo');

    //储值充值卡列表
    Route::get('IndexBalance/cardList', 'IndexBalance/cardList');

    Route::get('IndexBalance/coachList', 'IndexBalance/coachList');
    //充值余额(card_id)
    Route::post('IndexBalance/payBalanceOrder', 'IndexBalance/payBalanceOrder');
    //充值订单列表(时间筛选 start_time,end_time)
    Route::get('IndexBalance/balaceOrder', 'IndexBalance/balaceOrder');
    //消费明细
    Route::get('IndexBalance/payWater', 'IndexBalance/payWater');
    //佣金列表 status 0,1,2
    Route::get('IndexUser/commList', 'IndexUser/commList');
    //img
    Route::post('IndexUser/base64ToImg', 'IndexUser/base64ToImg');

    Route::get('IndexUser/adminCoachQr', 'IndexUser/adminCoachQr');

    Route::get('IndexUser/userCashInfo', 'IndexUser/userCashInfo');

    Route::post('IndexUser/applyWallet', 'IndexUser/applyWallet');

    Route::post('IndexUser/bindAlipayNumber', 'IndexUser/bindAlipayNumber');

    Route::get('IndexUser/walletList', 'IndexUser/walletList');

    Route::get('IndexUser/myTeam', 'IndexUser/myTeam');

    Route::get('IndexUser/userCommQr', 'IndexUser/userCommQr');
    //申请分销商 user_name mobile
    Route::post('IndexUser/applyReseller', 'IndexUser/applyReseller');

    Route::get('IndexUser/resellerInfo', 'IndexUser/resellerInfo');


    Route::get('IndexOrder/getIsBus', 'IndexOrder/getIsBus');

    //申请分销商 user_name mobile
    Route::post('IndexUser/applyChannel', 'IndexUser/applyChannel');

    Route::get('IndexUser/channelInfo', 'IndexUser/channelInfo');

    Route::get('IndexUser/channelCateSelect', 'IndexUser/channelCateSelect');

    Route::post('IndexUser/sendShortMsg', 'IndexUser/sendShortMsg');

    Route::post('IndexUser/bindUserPhone', 'IndexUser/bindUserPhone');

    Route::get('IndexUser/getStoreSelect', 'IndexUser/getStoreSelect');


    Route::group('IndexChannel', function () {

        Route::get('index', 'IndexChannel/index');

        Route::get('channelQr', 'IndexChannel/channelQr');

        Route::get('orderList', 'IndexChannel/orderList');

        Route::post('applyWallet', 'IndexChannel/applyWallet');

        Route::get('walletList', 'IndexChannel/walletList');


    });

    /********************按摩6.0接口**********************/

    //向导时间管理回显
    Route::get('IndexCoach/timeConfig', 'IndexCoach/getTimeConfig');
    //向导时间管理设置
    Route::post('IndexCoach/timeConfig', 'IndexCoach/setTimeConfig');
    //向导接单时间获取时间节点
    Route::get('IndexCoach/getTime', 'IndexCoach/getTime');
    //向导车费明细列表
    Route::get('IndexCoach/carMoneyList', 'IndexCoach/carMoneyList');
    //订单数量
    Route::get('IndexCoach/getOrderNum', 'IndexCoach/getOrderNum');
    //物料商城-商品列表
    Route::get('IndexCoach/goodsList', 'IndexCoach/goodsList');
    //物料商城-分类列表
    Route::get('IndexCoach/carteList', 'IndexCoach/carteList');
    //物料商城-商品详情
    Route::get('IndexCoach/goodsInfo', 'IndexCoach/goodsInfo');
    //添加反馈
    Route::post('IndexCoach/addFeedback', 'IndexUser/addFeedback');
    //反馈列表
    Route::get('IndexCoach/listFeedback', 'IndexUser/listFeedback');
    //反馈详情
    Route::get('IndexCoach/feedbackInfo', 'IndexUser/feedbackInfo');
    //提交申诉
    Route::post('IndexCoach/addAppeal', 'IndexCoach/addAppeal');
    //申诉记录列表
    Route::get('IndexCoach/appealList', 'IndexCoach/appealList');
    //订单列表
    Route::get('IndexCoach/appealOrder', 'IndexCoach/appealOrder');

    Route::get('IndexCoach/userLabelList', 'IndexCoach/userLabelList');

    Route::get('IndexCoach/labelList', 'IndexCoach/labelList');

    Route::get('IndexCoach/orderInfo', 'IndexCoach/orderInfo');

    Route::post('IndexCoach/userLabelAdd', 'IndexCoach/userLabelAdd');

    Route::get('IndexCoach/coachBalanceQr', 'IndexCoach/coachBalanceQr');
    Route::get('IndexCoach/coachLevel', 'IndexCoach/coachLevel');

    Route::get('IndexCoach/coachCommissionList', 'IndexCoach/coachCommissionList');

    Route::get('IndexCoach/coachCommissionData', 'IndexCoach/coachCommissionData');

    Route::get('IndexCoach/coachCommissionInfo', 'IndexCoach/coachCommissionInfo');


    Route::group('IndexCoach', function () {

        Route::get('getAttestationInfo', 'IndexCoach/getAttestationInfo');

        Route::get('getPersonVerifyUrl', 'IndexCoach/getPersonVerifyUrl');

        Route::post('Extsign', 'IndexCoach/Extsign');


    });

    Route::group('IndexReseller', function () {

        Route::get('partnerIndex', 'IndexReseller/partnerIndex');

        Route::get('partnerCoachList', 'IndexReseller/partnerCoachList');

    });

    Route::group('IndexArticle', function () {

        Route::get('articleList', 'IndexArticle/articleList');

        Route::get('articleInfo', 'IndexArticle/articleInfo');

        Route::post('subArticleForm', 'IndexArticle/subArticleForm');

    });

    //邀约订单下单
    Route::post('IndexOrder/payDemandOrder', 'IndexOrder/payDemandOrder');
    //邀约订单重新支付
    Route::post('IndexOrder/rePayDemandOrder', 'IndexOrder/rePayDemandOrder');
    //技能列表
    Route::get('Index/getSkill', 'Index/getSkill');
    //发现列表
    Route::get('Index/demandOrderList', 'Index/demandOrderList');
    //接单
    Route::post('IndexCoach/receivingOrder', 'IndexCoach/receivingOrder');
    //技师邀约订单
    Route::get('IndexCoach/demandOrderList', 'IndexCoach/demandOrderList');
    //技师完成邀约订单
    Route::post('IndexCoach/complete', 'IndexCoach/complete');
    //我的发布
    Route::get('IndexDemand/getList', 'IndexDemand/getList');
    //取消邀约订单
    Route::post('IndexDemand/cancel', 'IndexDemand/cancel');
    //用户完成邀约订单
    Route::post('IndexDemand/complete', 'IndexDemand/complete');
    //订单详情
    Route::post('IndexDemand/orderInfo', 'IndexDemand/orderInfo');
    //订单详情
    Route::get('IndexCoach/getDemandNum', 'IndexCoach/getDemandNum');

    /***********1.3版本*************/
    //订单详情
    Route::get('Index/agreement', 'Index/agreement');
    //提交加盟商表单
    Route::post('Index/participate', 'Index/participate');

    /**********1.4**************/
    //门店申请
    Route::post('IndexStore/apply', 'IndexStore/apply');
    //门店详情
    Route::get('IndexStore/getStore', 'IndexStore/getStore');
    //门店详情
    Route::post('IndexStore/edit', 'IndexStore/edit');
    //列表
    Route::get('IndexStore/getList', 'IndexStore/getList');
    //详情
    Route::get('IndexStore/getInfo', 'IndexStore/getInfo');

    /***********1.5***********/
    //报名
    Route::post('IndexCoach/orderApply', 'IndexCoach/orderApply');
    //查看报名
    Route::get('IndexDemand/seeApply', 'IndexDemand/seeApply');
    //选择向导
    Route::post('IndexDemand/chooseCoach', 'IndexDemand/chooseCoach');
    //取消报名
    Route::post('IndexCoach/cancelApply', 'IndexCoach/cancelApply');
    //邀约明细
    Route::get('IndexCoach/demandCommissionDetail', 'IndexCoach/demandCommissionDetail');

    /***********1.6**********/
    //订单数量
    Route::get('IndexUser/getOrderNum', 'IndexUser/getOrderNum');

    /***********1.7**********/
    //申请分销商二维码
    Route::get('IndexUser/distribution', 'IndexUser/distribution');
    //我的收益
    Route::get('IndexUser/myTeamWater', 'IndexUser/myTeamWater');

    //渠道商生成邀请员工绑定二维码
    Route::post('IndexChannel/inviteStaffQr', 'IndexChannel/inviteStaffQr');
    //渠道商邀请员工绑定二维码详情
    Route::get('IndexUser/inviteStaffQrInfo', 'IndexUser/inviteStaffQrInfo');
    //绑定渠道商
    Route::post('IndexUser/bindChannel', 'IndexUser/bindChannel');
    //渠道商收益
    Route::get('IndexChannel/commList', 'IndexChannel/commList');
    //我的员工
    Route::get('IndexChannel/staffList', 'IndexChannel/staffList');
    //编辑员工
    Route::post('IndexChannel/updateStaff', 'IndexChannel/updateStaff');
    //员工详情
    Route::get('IndexChannelStaff/index', 'IndexChannelStaff/index');
    //员工渠道流水
    Route::get('IndexChannelStaff/commList', 'IndexChannelStaff/commList');


    /***********1.8**********/
    //完成服务
    Route::post('IndexOrder/completeOrder', 'IndexOrder/completeOrder');
    //判断用户是否是技师
    Route::get('IndexUser/getUserCoachStatus', 'IndexUser/getUserCoachStatus');
    //门店分类
    Route::get('IndexStore/storeTypeList', 'IndexStore/storeTypeList');
    //分销
    Route::get('IndexUser/shareCashList', 'IndexUser/shareCashList');
    //门店评论
    Route::get('IndexStore/commentList', 'IndexStore/commentList');
    //评论订单-改版
    Route::post('IndexOrder/addCommentV2', 'IndexOrder/addCommentV2');
    //技师评论列表-改版
    Route::post('Index/commentListV2', 'Index/commentListV2');
    //推广人申请提现
    Route::post('IndexUser/shareApplyWallet', 'IndexUser/shareApplyWallet');
    //推广人提现记录
    Route::get('IndexUser/shareWalletList', 'IndexUser/shareWalletList');
    //推广人佣金信息
    Route::get('IndexUser/shareCash', 'IndexUser/shareCash');


    /***********2.0版本**********/
    //发送模板消息
    Route::get('Index/sendMsg', 'Index/sendMsg');
    //发布邀约多订单
    Route::post('IndexOrder/payManyDemandOrder', 'IndexOrder/payManyDemandOrder');
    //技师退款订单
    Route::get('IndexCoach/refundOrderList', 'IndexCoach/refundOrderList');
    //邀约重新发布
    Route::post('IndexOrder/rePayDemandManyOrder', 'IndexOrder/rePayDemandManyOrder');


    /***********2.3版本**********/
    //发送模板消息
    Route::get('Index/serviceTypeInfo', 'Index/serviceTypeInfo');

    /***********2.4版本**********/
    Route::post('IndexUser/updateFrom', 'IndexUser/updateFrom');

    Route::get('IndexUser/integralList', 'IndexUser/integralList');

});


//支付
Route::any('IndexWxPay/returnPay', 'IndexWxPay/returnPay');

Route::any('IndexWxPay/aliNotify', 'IndexWxPay/aliNotify');

Route::any('IndexWxPay/aliNotifyBalance', 'IndexWxPay/aliNotifyBalance');

Route::any('CallBack/fddCallBack', 'CallBack/fddCallBack');

Route::any('Test/test', 'Test/test');

Route::any('Test/test1', 'Test/test1');
Route::any('Test/test2', 'Test/test2');















