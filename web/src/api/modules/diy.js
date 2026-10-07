/*
 * @Description: DIY
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-17 16:17:11
 * @LastEditTime: 2023-08-18 10:46:02
 * @LastEditors: DXV-RGWU-TUFH-RFCY-IEGMYY
 */

import {
  get, post
} from '../index'
export default {
  // diy配置
  diyInfo (querys) {
    return get('massage/admin/AdminSetting/diyInfo', querys)
  },
  // diy菜单配置
  getTabbar (querys) {
    return get('massage/admin/AdminSetting/getTabbar', querys)
  },
  // 链接列表
  getFunctionPageList (querys) {
    return get('massage/admin/AdminSetting/getFunctionPageList', querys)
  },
  // 门店链接列表
  getFunctionPageListForStore (querys) {
    return get('massage/admin/AdminSetting/getFunctionPageListForStore', querys)
  },
  // 修改diy配置
  diyUpdate (querys) {
    return post('massage/admin/AdminSetting/diyUpdate', querys)
  }
}
