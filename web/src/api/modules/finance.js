/*
 * @Description: 财务管理
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-04-12 19:03:07
 * @LastEditors: xiao li
 */

import {
  get,
  post
} from '../index'
export default {
  // 储值充值卡列表
  cardList (querys) {
    return get('massage/admin/AdminBalance/cardList', querys)
  },
  // 储值充值卡详情
  cardInfo (querys) {
    return get('massage/admin/AdminBalance/cardInfo', querys)
  },
  // 新增储值充值卡
  cardAdd (querys) {
    return post('massage/admin/AdminBalance/cardAdd', querys)
  },
  // 编辑储值充值卡
  cardUpdate (querys) {
    return post('massage/admin/AdminBalance/cardUpdate', querys)
  },
  // 储值订单列表
  orderList (querys) {
    return get('massage/admin/AdminBalance/orderList', querys)
  },
  // 储值订单详情
  orderInfo (querys) {
    return get('massage/admin/AdminBalance/orderInfo', querys)
  },
  // 财务管理
  financeList (querys) {
    return get('massage/admin/AdminCoach/financeList', querys)
  },
  // 向导提现申请列表(type1是服务费提现，2是车费)
  walletList (querys) {
    return get('massage/admin/AdminCoach/walletList', querys)
  },
  // 提现详情
  walletInfo (querys) {
    return get('massage/admin/AdminCoach/walletInfo', querys)
  },
  // 同意打款（id,status=2,online 1：线上，0线下）
  walletPass (querys) {
    return post('massage/admin/AdminCoach/walletPass', querys)
  },
  // 拒绝打款（id,status=3）
  walletNoPass (querys) {
    return post('massage/admin/AdminCoach/walletNoPass', querys)
  }
}
