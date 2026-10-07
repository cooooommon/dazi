/*
 * @Descripttion: 求救通知
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2022-12-16 17:12:19
 */
import {
  get,
  post
} from '../index'
export default {
  // 求救通知
  policeList (querys) {
    return get('massage/admin/AdminCoach/policeList', querys)
  },
  // 编辑求救通知
  policeUpdate (querys) {
    return post('massage/admin/AdminCoach/policeUpdate', querys)
  },
  // 求救通知配置
  helpConfigInfo (querys) {
    return get('massage/admin/AdminSetting/helpConfigInfo', querys)
  },
  // 修改求救通知
  helpConfigUpate (querys) {
    return post('massage/admin/AdminSetting/helpConfigUpate', querys)
  }
}
