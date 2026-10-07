/*
 * @Descripttion: 订单管理
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-01 15:22:56
 */
import {
  get,
  post
} from '../index'
export default {
  // 查看客户标签
  userLabelList (querys) {
    return get('massage/admin/AdminCoach/userLabelList', querys)
  },
  // 订单管理
  orderList (querys) {
    return get('massage/admin/AdminOrder/orderList', querys)
  },
  // 订单详情
  orderInfo (querys) {
    return get('massage/admin/AdminOrder/orderInfo', querys)
  },
  // 订单升级记录
  orderUpRecord (querys) {
    return get('massage/admin/AdminOrder/orderUpRecord', querys)
  },
  // 退款管理
  refundOrderList (querys) {
    return get('massage/admin/AdminOrder/refundOrderList', querys)
  },
  // 退款详情
  refundOrderInfo (querys) {
    return get('massage/admin/AdminOrder/refundOrderInfo', querys)
  },
  // 通知列表
  noticeList (querys) {
    return get('massage/admin/AdminOrder/noticeList', querys)
  },
  // 编辑通知状态
  noticeUpdate (querys) {
    return post('massage/admin/AdminOrder/noticeUpdate', querys)
  },
  // 未查看的数量
  noLookCount (querys) {
    return post('massage/admin/AdminOrder/noLookCount', querys)
  },
  // 全部已读
  allLook (querys) {
    return post('massage/admin/AdminOrder/allLook', querys)
  },
  // 同意退款
  passRefund (querys) {
    return post('massage/admin/AdminOrder/passRefund', querys)
  },
  // 拒绝退款
  noPassRefund (querys) {
    return post('massage/admin/AdminOrder/noPassRefund', querys)
  },
  // 评价标签列表
  commentLableList (querys) {
    return get('massage/admin/AdminOrder/commentLableList', querys)
  },
  // 评价标签详情
  commentLableInfo (querys) {
    return get('massage/admin/AdminOrder/commentLableInfo', querys)
  },
  // 新增评价标签
  commentLableAdd (querys) {
    return post('massage/admin/AdminOrder/commentLableAdd', querys)
  },
  // 编辑评价标签
  commentLableUpdate (querys) {
    return post('massage/admin/AdminOrder/commentLableUpdate', querys)
  },
  // 评价列表
  commentList (querys) {
    return get('massage/admin/AdminOrder/commentList', querys)
  },
  // 编辑评价
  commentUpdate (querys) {
    return post('massage/admin/AdminOrder/commentUpdate', querys)
  },
  // 标签列表
  lableList (querys) {
    return get('massage/admin/AdminOrder/lableList', querys)
  },
  // 新增虚拟评价
  addComment (querys) {
    return post('massage/admin/AdminOrder/addComment', querys)
  },
  // 新增虚拟评价
  addCommentV2 (querys) {
    return post('massage/admin/AdminOrder/addCommentV2', querys)
  },
  // 佣金记录
  commList (querys) {
    return post('massage/admin/AdminUser/commList', querys)
  },
  // 向导转账
  adminUpdateCoachCommisson (querys) {
    return post('massage/admin/AdminUser/adminUpdateCoachCommisson', querys)
  },
  // 立即退款/完成订单
  adminUpdateOrder (querys) {
    return post('massage/admin/AdminOrder/adminUpdateOrder', querys)
  },
  // 转派向导可选列表
  orderChangeCoachList (querys) {
    return get('massage/admin/AdminOrder/orderChangeCoachList', querys)
  },
  // 转派向导
  orderChangeCoach (querys) {
    return post('massage/admin/AdminOrder/orderChangeCoach', querys)
  }
}
