/*
 * @Descripttion: 权限管理
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-20 19:28:28
 */
import {
  get, post
} from '../index'
export default {
  // 角色列表
  roleList (querys) {
    return get('node/admin/AdminUser/roleList', querys)
  },
  // 角色下拉
  roleSelect (querys) {
    return get('node/admin/AdminUser/roleSelect', querys)
  },
  // 角色详情
  roleInfo (querys) {
    return get('node/admin/AdminUser/roleInfo', querys)
  },
  // 新增角色
  roleAdd (querys) {
    return post('node/admin/AdminUser/roleAdd', querys)
  },
  // 编辑角色
  roleUpdate (querys) {
    return post('node/admin/AdminUser/roleUpdate', querys)
  },
  // 账号列表
  adminList (querys) {
    return get('node/admin/AdminUser/adminList', querys)
  },
  // 账号详情
  adminInfo (querys) {
    return get('node/admin/AdminUser/adminInfo', querys)
  },
  // 新增账号
  adminAdd (querys) {
    return post('node/admin/AdminUser/adminAdd', querys)
  },
  // 编辑账号
  adminUpdate (querys) {
    return post('node/admin/AdminUser/adminUpdate', querys)
  },
  // 账号所匹配的角色的节点详情
  adminNodeInfo (querys) {
    return get('node/admin/AdminUser/adminNodeInfo', querys)
  },
  // 操作日志
  logList (querys) {
    return get('node/admin/AdminUser/logList', querys)
  }
}
