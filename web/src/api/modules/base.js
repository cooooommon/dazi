/*
 * @Descripttion:
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: wen kun
 * @LastEditTime: 2024-07-19 17:47:21
 */
import {
  get,
  post
} from '../index'
export default {
  // 登录
  login (querys) {
    return post('massage/admin/Admin/login', querys)
  },
  // 配置信息
  getConfig (querys) {
    return get('massage/admin/Admin/getConfig', querys)
  },
  // 获取权限设置
  getSaasAuth (querys) {
    return get('massage/admin/AdminSetting/getSaasAuth', querys)
  },
  // 清除缓存
  clearCache () {
    return get('admin/admin/config/clear')
  },
  // 修改自己的密码
  updatePasswd (querys) {
    return post('massage/admin/AdminSetting/updatePass', querys)
  },
  // 获取w7底部信息
  getW7TmpV2 (querys) {
    return get('massage/admin/Admin/getW7TmpV2', querys)
  },
  // 基本信息
  returnAdmin () {
    return get('card/Admin/returnAdmin')
  },
  // 是否为微擎版，还是为独立版
  getIsWeiVersion () {
    return get('agent/admin/isWe7')
  },
  // 地图地址转坐标
  addressToLocation (querys) {
    return get('massage/admin/AdminSetting/addressToLocation', querys)
  },
  // 地图坐标转地址
  locationToAddress (querys) {
    return get('massage/admin/AdminSetting/locationToAddress', querys)
  }

}
