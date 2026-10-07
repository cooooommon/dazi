/*
 * @Description: 门店
 * @Author: xiao li
 * @Date: 2023-03-27 16:20:43
 * @LastEditTime: 2024-09-26 11:04:16
 * @LastEditors: wen kun
 */

import {
    get,
    post
} from '../index'
export default {
    // 门店列表
    getList (querys) {
        return get('massage/admin/AdminStore/getList', querys)
    },
    // 详情
    info (querys) {
        return get('massage/admin/AdminStore/info', querys)
    },
    // 审核
    check (querys) {
        return post('massage/admin/AdminStore/check', querys)
    },
    // 重新审核详情
    reInfo (querys) {
        return get('massage/admin/AdminStore/reInfo', querys)
    },
    // 重新审核
    reCheck (querys) {
        return post('massage/admin/AdminStore/reCheck', querys)
    },
    // 修改状态
    changeStatus (querys) {
        return post('massage/admin/AdminStore/changeStatus', querys)
    },
    // 分类
    // 列表
    typeList (querys) {
        return get('massage/admin/AdminStore/typeList', querys)
    },
    // 添加
    typeAdd (querys) {
        return post('massage/admin/AdminStore/typeAdd', querys)
    },
    // 编辑
    typeUpdate (querys) {
        return post('massage/admin/AdminStore/typeUpdate', querys)
    },
    // 添加门店
    addStore (querys) {
        return post('massage/admin/AdminStore/addStore', querys)
    },
    // 用户列表
    storeUserList (querys) {
        return get('massage/admin/AdminStore/storeUserList', querys)
    },
    // 门店分类不分页
    typeListNoPage (querys) {
        return get('massage/admin/AdminStore/typeListNoPage', querys)
    },
    // 门店置顶
    storeTop (querys) {
        return post('massage/admin/AdminStore/storeTop', querys)
    },
    // 套餐
    // 列表
    packageList (querys) {
        return get('store/admin/package/getList', querys)
    },
    // 套餐添加
    packageAdd (querys) {
        return post('store/admin/package/add', querys)
    },
    // 修改状态
    packageUpdateStatus (querys) {
        return post('store/admin/package/updateStatus', querys)
    },
    // 详情
    packageInfo (querys) {
        return get('store/admin/package/getInfo', querys)
    },
    // 套餐编辑
    packageEdit (querys) {
        return post('store/admin/package/edit', querys)
    },
    // 设置比例
    editStore (querys) {
        return post('massage/admin/AdminStore/editStore', querys)
    },
    // 订单列表
    orderGetList (querys) {
        return get('store/admin/PackageOrder/getList', querys)
    },
    // 订单详情
    orderGetInfo (querys) {
        return get('store/admin/PackageOrder/getInfo', querys)
    },
    // 退款列表
    getRefundList (querys) {
        return get('store/admin/PackageOrder/getRefundList', querys)
    },
    // 退款详情
    refundInfo (querys) {
        return get('store/admin/PackageOrder/refundInfo', querys)
    },
    // 评价管理
    // 评论列表
    commentList (querys) {
        return get('store/admin/PackageOrder/commentList', querys)
    },
    // 新增评论
    addComment (querys) {
        return post('store/admin/PackageOrder/addComment', querys)
    },
    // 删除评论
    delComment (querys) {
        return post('store/admin/PackageOrder/delComment', querys)
    },
    // 退款
    refundCheck (querys) {
        return post('store/admin/PackageOrder/refundCheck', querys)
    },
    // 解除绑定
    cancelBroker (querys) {
        return post('massage/admin/AdminCoach/cancelBroker', querys)
    },
    // 批量设置门店比例
    editStoreBalance (querys) {
        return post('massage/admin/AdminStore/editStoreBalance', querys)
    },
    // 列表
    seckillGetList (querys) {
        return get('seckill/admin/getList', querys)
    },
    // 门店列表不分页
    getStoreList (querys) {
        return get('massage/admin/AdminStore/getStoreList', querys)
    },
    // 编辑
    seckillEdit (querys) {
        return post('seckill/admin/seckillEdit', querys)
    },
    // 新增
    seckillAdd (querys) {
        return post('seckill/admin/seckillAdd', querys)
    },
    // 详情
    getSeckillEdit (querys) {
        return get('seckill/admin/seckillEdit', querys)
    },
}