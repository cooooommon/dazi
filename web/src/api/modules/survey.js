/*
 * @Description: 数据概览
 * @Author: xiao li
 * @Date: 2022-07-14 09:50:19
 * @LastEditTime: 2022-10-27 10:32:51
 * @LastEditors: xiao li
 */
import {
  get
} from '../index'
export default {
  // 平台总金额
  orderData (querys) {
    return get('massage/admin/AdminIndex/orderData', querys)
  },
  // 代理商销售数据
  agentOrderData (querys) {
    return get('massage/admin/AdminIndex/agentOrderData', querys)
  },
  // 向导用户数据
  coachAndUserData (querys) {
    return get('massage/admin/AdminIndex/coachAndUserData', querys)
  },
  // 向导销售数据
  coachSaleData (querys) {
    return get('massage/admin/AdminIndex/coachSaleData', querys)
  }
}
