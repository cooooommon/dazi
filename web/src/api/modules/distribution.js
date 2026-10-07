/*
 * @Description: 分销商管理
 * @Author: xiao li
 * @Date: 2021-07-06 18:32:16
 * @LastEditTime: 2022-08-03 11:52:05
 * @LastEditors: xiao li
 */

import {
  get,
  post
} from '../index'
export default {
  // 审核列表
  resellerList (querys) {
    return get('massage/admin/AdminReseller/resellerList', querys)
  },
  // 分销商详情
  resellerInfo (querys) {
    return get('massage/admin/AdminReseller/resellerInfo', querys)
  },
  // 审核分销商
  resellerUpdate (querys) {
    return post('massage/admin/AdminReseller/resellerUpdate', querys)
  },
  // 分销商下级
  getSubList (querys) {
    return get('massage/admin/AdminReseller/getSubList', querys)
  }
}
