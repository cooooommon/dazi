/*
 * @Description: 
 * @Author: xiao li
 * @Date: 2022-07-14 09:50:19
 * @LastEditTime: 2023-03-15 18:40:50
 * @LastEditors: xiao li
 */
import {
  get,
  post
} from '../index'
export default {
  // 代理商下拉
  adminSelect (querys) {
    return get('massage/admin/AdminSetting/adminSelect', querys)
  },
  // 代理商列表
  franchiseeList (querys) {
    return get('massage/admin/AdminSetting/adminList', querys)
  },
  // 代理商用户列表
  userList (querys) {
    return get('massage/admin/AdminSetting/userSelect', querys)
  },
  // 添加代理商
  adminAdd (querys) {
    return post('massage/admin/AdminSetting/adminAdd', querys)
  },
  // 删除代理商
  adminStatusUpdate (querys) {
    return post('massage/admin/AdminSetting/adminStatusUpdate', querys)
  },
  // 编辑代理商
  adminUpdate (querys) {
    return post('massage/admin/AdminSetting/adminUpdate', querys)
  },
  // 代理商详情
  adminInfo (querys) {
    return get('massage/admin/AdminSetting/adminInfo', querys)
  },
  // 佣金列表
  cashList (querys) {
    return get('massage/admin/AdminUser/cashList', querys)
  },
  // 佣金提现
  applyWallet (querys) {
    return post('massage/admin/AdminUser/applyWallet', querys)
  },
  // 代理申请
  joinList (querys) {
    return get('massage/admin/AdminSetting/joinList', querys)
  },
  // 已阅
  joinRead (querys) {
    return post('massage/admin/AdminSetting/joinRead', querys)
  }
}
