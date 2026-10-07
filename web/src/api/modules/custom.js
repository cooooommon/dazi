/*
 * @Descripttion: 客户管理
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: wen kun
 * @LastEditTime: 2024-11-18 10:22:21
 */
import {
  get,
  post
} from '../index'
export default {
  // 客户列表
  userList (querys) {
    return get('massage/admin/AdminUser/userList', querys)
  },
  // 客户列表删除标签
  delUserLabel (querys) {
    return post('massage/admin/AdminUser/delUserLabel', querys)
  },
  // 标签列表
  userLabelList (querys) {
    return get('massage/admin/AdminSetting/userLabelList', querys)
  },
  // 标签详情
  userLabelInfo (querys) {
    return get('massage/admin/AdminSetting/userLabelInfo', querys)
  },
  // 新增标签
  userLabelAdd (querys) {
    return post('massage/admin/AdminSetting/userLabelAdd', querys)
  },
  // 编辑标签
  userLabelUpdate (querys) {
    return post('massage/admin/AdminSetting/userLabelUpdate', querys)
  },
  // 加入/移除黑名单
  setBlacklist (querys) {
    return post('massage/admin/AdminUser/setBlacklist', querys)
  },
  // 黑名单列表
  blacklist (querys) {
    return get('massage/admin/AdminUser/blacklist', querys)
  },
  // 流水
  payWater (querys) {
    return get('massage/admin/AdminBalance/payWater', querys)
  },
  // 修改余额
  payBalanceOrder (querys) {
    return post('massage/admin/AdminBalance/payBalanceOrder', querys)
  },
  // 用户信息
  getUserInfo (querys) {
    return get('massage/admin/AdminUser/getUserInfo', querys)
  },
  // 积分明细
  integralList (querys) {
    return get('massage/admin/AdminUser/integralList', querys)
  }
}
