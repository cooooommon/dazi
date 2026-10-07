/*
 * @Description: 服务管理
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-06-24 11:31:54
 * @LastEditors: wen kun
 */

import { get, post } from '../index'
export default {
  // 服务列表
  serviceList (querys) {
    return get('massage/admin/AdminService/serviceList', querys)
  },
  // 服务详情
  serviceInfo (querys) {
    return get('massage/admin/AdminService/serviceInfo', querys)
  },
  // 新增服务
  serviceAdd (querys) {
    return post('massage/admin/AdminService/serviceAdd', querys)
  },
  // 编辑服务
  serviceUpdate (querys) {
    return post('massage/admin/AdminService/serviceUpdate', querys)
  },
  // 轮播图列表
  bannerList (querys) {
    return post('massage/admin/AdminSetting/bannerList', querys)
  },
  // 轮播图信息
  bannerInfo (querys) {
    return get('massage/admin/AdminSetting/bannerInfo', querys)
  },
  // 新增轮播图
  bannerAdd (querys) {
    return post('massage/admin/AdminSetting/bannerAdd', querys)
  },
  // 编辑轮播图
  bannerUpdate (querys) {
    return post('massage/admin/AdminSetting/bannerUpdate', querys)
  },
  // 续单设置详情
  addClockInfo (querys) {
    return get('massage/admin/AdminSetting/addClockInfo', querys)
  },
  // 续单设置
  addClockUpdate (querys) {
    return post('massage/admin/AdminSetting/addClockUpdate', querys)
  },
  // 服务分类
  // 添加
  typeAdd (querys) {
    return post('massage/admin/AdminService/typeAdd', querys)
  },
  // 列表
  typeList (querys) {
    return get('massage/admin/AdminService/typeList', querys)
  },
  // 编辑
  typeUpdate (querys) {
    return post('massage/admin/AdminService/typeUpdate', querys)
  },
  // 列表不分页
  typeListNoPage (querys) {
    return get('massage/admin/AdminService/typeListNoPage', querys)
  },

}
