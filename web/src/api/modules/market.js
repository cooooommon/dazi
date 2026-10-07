/*
 * @Description: 营销管理
 * @Author: xiao li
 * @Date: 2021-07-06 18:32:16
 * @LastEditTime: 2022-12-12 14:40:21
 * @LastEditors: xiao li
 */

import {
  get,
  post
} from '../index'
export default {
  // 优惠券列表
  couponList (querys) {
    return get('massage/admin/AdminCoupon/couponList', querys)
  },
  // 优惠券详情
  couponInfo (querys) {
    return get('massage/admin/AdminCoupon/couponInfo', querys)
  },
  // 新增优惠券
  couponAdd (querys) {
    return post('massage/admin/AdminCoupon/couponAdd', querys)
  },
  // 编辑优惠券
  couponUpdate (querys) {
    return post('massage/admin/AdminCoupon/couponUpdate', querys)
  },
  // 派发卡券
  couponRecordAdd (querys) {
    return post('massage/admin/AdminCoupon/couponRecordAdd', querys)
  },
  // 活动详情
  couponAtvInfo (querys) {
    return get('massage/admin/AdminCoupon/couponAtvInfo', querys)
  },
  // 编辑活动
  couponAtvUpdate (querys) {
    return post('massage/admin/AdminCoupon/couponAtvUpdate', querys)
  },
  // 文章列表
  articleList (querys) {
    return get('massage/admin/AdminArticle/articleList', querys)
  },
  // 文章详情
  articleInfo (querys) {
    return get('massage/admin/AdminArticle/articleInfo', querys)
  },
  // 新增文章
  articleAdd (querys) {
    return post('massage/admin/AdminArticle/articleAdd', querys)
  },
  // 编辑文章
  articleUpdate (querys) {
    return post('massage/admin/AdminArticle/articleUpdate', querys)
  },
  // 表单字段列表
  fieldList (querys) {
    return get('massage/admin/AdminArticle/fieldList', querys)
  },
  // 表单字段下拉框
  fieldSelect (querys) {
    return get('massage/admin/AdminArticle/fieldSelect', querys)
  },
  // 新增表单字段
  fieldAdd (querys) {
    return post('massage/admin/AdminArticle/fieldAdd', querys)
  },
  // 编辑表单字段
  fieldUpdate (querys) {
    return post('massage/admin/AdminArticle/fieldUpdate', querys)
  },
  // 表单表头
  subTitle (querys) {
    return get('massage/admin/AdminArticle/subTitle', querys)
  },
  // 表单提交列表
  subDataList (querys) {
    return get('massage/admin/AdminArticle/subDataList', querys)
  }
}
