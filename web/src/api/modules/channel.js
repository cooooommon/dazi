/*
 * @Description: 分销商管理
 * @Author: xiao li
 * @Date: 2021-07-06 18:32:16
 * @LastEditTime: 2023-11-01 16:47:07
 * @LastEditors: wen kun
 */

import {
  get,
  post
} from '../index'
export default {
  // 审核列表
  channelList (querys) {
    return get('massage/admin/AdminChannel/channelList', querys)
  },
  // 下拉列表
  channelSelect (querys) {
    return get('massage/admin/AdminChannel/channelSelect', querys)
  },
  // 分销商详情
  channelInfo (querys) {
    return get('massage/admin/AdminChannel/channelInfo', querys)
  },
  // 审核分销商
  channelUpdate (querys) {
    return post('massage/admin/AdminChannel/channelUpdate', querys)
  },
  // 类目列表
  cateList (querys) {
    return get('massage/admin/AdminChannel/cateList', querys)
  },
  // 类目下拉
  cateSelect (querys) {
    return get('massage/admin/AdminChannel/cateSelect', querys)
  },
  // 类目详情
  cateInfo (querys) {
    return get('massage/admin/AdminChannel/cateInfo', querys)
  },
  // 新增类目
  cateAdd (querys) {
    return post('massage/admin/AdminChannel/cateAdd', querys)
  },
  // 编辑类目
  cateUpdate (querys) {
    return post('massage/admin/AdminChannel/cateUpdate', querys)
  },
  // 渠道商员工列表
  staffList (querys) {
    return get('massage/admin/AdminChannel/staffList', querys)
  },
  // 切换上级
  changeChannel (querys) {
    return post('massage/admin/AdminChannel/changeChannel', querys)
  },
  // 批量设置比例
  changeBalance (querys) {
    return post('massage/admin/AdminChannel/changeBalance', querys)
  },
  // 批量设置时效
  setBindTime (querys) {
    return post('massage/admin/AdminChannel/setBindTime', querys)
  },
  // 余额记录
  getCashList (querys) {
    return get('massage/admin/AdminChannel/getCashList', querys)
  }
}
