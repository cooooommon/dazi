/*
 * @Description: 邀约
 * @Author: wen kun
 * @Date: 2022-11-03 11:21:27
 * @LastEditTime: 2024-04-01 18:22:04
 * @LastEditors: wen kun
 */
import { get, post } from '../index'
export default {
    // 邀约设置信息
    demandSetting (querys) {
        return get('/massage/admin/AdminSetting/demandSetting', querys)
    },
    // 邀约设置
    demandSettingPost (querys) {
        return post('/massage/admin/AdminSetting/demandSetting', querys)
    },
    // 邀约订单列表
    demandOder (querys) {
        return get('/massage/admin/AdminOrder/demandOder', querys)
    },
    // 邀约退款订单列表
    demandRefundOrder (querys) {
        return get('/massage/admin/AdminOrder/demandRefundOrder', querys)
    },
    // 邀约订单退款
    demandRefund (querys) {
        return post('/massage/admin/AdminOrder/demandRefund', querys)
    },
    // 邀约订单详情
    demandInfo (querys) {
        return get('/massage/admin/AdminOrder/demandInfo', querys)
    },
    // 邀约审核列表
    demandOrderList (querys) {
        return get('/massage/admin/AdminOrder/demandOrderList', querys)
    },
    // 邀约订单审核
    demandExamine (querys) {
        return post('/massage/admin/AdminOrder/demandExamine', querys)
    },
    // 邀约订单详情
    demandOrderInfo (querys) {
        return get('/massage/admin/AdminOrder/demandOrderInfo', querys)
    },
    // 发送通知
    sendMsg (querys) {
        return get('/massage/admin/AdminOrder/sendMsg', querys)
    }
}