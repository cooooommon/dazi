/*
 * @Description: 门店
 * @Author: xiao li
 * @Date: 2023-03-27 16:20:43
 * @LastEditTime: 2023-03-28 18:09:30
 * @LastEditors: xiao li
 */

import {
  get,
  post
} from '../index'
export default {
  // 门店列表
  storeList (querys) {
    return get('store/admin/AdminStore/storeList', querys)
  },
  // 门店下拉框
  storeSelect (querys) {
    return get('store/admin/AdminStore/storeSelect', querys)
  },
  // 门店详情
  storeInfo (querys) {
    return get('store/admin/AdminStore/storeInfo', querys)
  },
  // 新增门店
  storeAdd (querys) {
    return post('store/admin/AdminStore/storeAdd', querys)
  },
  // 编辑门店
  storeUpdate (querys) {
    return post('store/admin/AdminStore/storeUpdate', querys)
  },
  // 动态审核
  storeCheck (querys) {
    return post('store/admin/AdminStoreList/storeCheck', querys)
  },
  // 删除动态
  storeDel (querys) {
    return post('store/admin/AdminStoreList/storeDel', querys)
  }
}
