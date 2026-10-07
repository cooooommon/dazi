/*
 * @Description: 财务管理
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-02-08 10:07:31
 * @LastEditors: xiao li
 */

import {
  get,
  post
} from '../index'
export default {
  // 动态列表
  dynamicList (querys) {
    return get('dynamic/admin/AdminDynamicList/dynamicList', querys)
  },
  // 动态详情
  dynamicInfo (querys) {
    return get('dynamic/admin/AdminDynamicList/dynamicInfo', querys)
  },
  // 动态审核
  dynamicCheck (querys) {
    return post('dynamic/admin/AdminDynamicList/dynamicCheck', querys)
  },
  // 删除动态
  dynamicDel (querys) {
    return post('dynamic/admin/AdminDynamicList/dynamicDel', querys)
  },
  // 置顶
  dynamicTop (querys) {
    return post('dynamic/admin/AdminDynamicList/dynamicTop', querys)
  },
  // 评论列表
  commentList (querys) {
    return get('dynamic/admin/AdminDynamicList/commentList', querys)
  },
  // 评论详情
  commentInfo (querys) {
    return get('dynamic/admin/AdminDynamicList/commentInfo', querys)
  },
  // 删除评论
  commentDel (querys) {
    return post('dynamic/admin/AdminDynamicList/commentDel', querys)
  },
  // 评论审核
  commentCheck (querys) {
    return post('dynamic/admin/AdminDynamicList/commentCheck', querys)
  }
}
