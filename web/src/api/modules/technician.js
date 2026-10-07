/*
 * @Description: 向导管理
 * @Author: xiao li
 * @Date: 2021-07-06 18:30:52
 * @LastEditTime: 2023-07-13 10:53:40
 * @LastEditors: wen kun
 */

import {
  get,
  post
} from '../index'
export default {
  // 门店下拉框
  storeSelect (querys) {
    return get('store/admin/AdminStore/storeSelect', querys)
  },
  // 向导列表
  coachList (querys) {
    return get('massage/admin/AdminCoach/coachList', querys)
  },
  // 向导详情
  coachInfo (querys) {
    return get('massage/admin/AdminCoach/coachInfo', querys)
  },
  // 向导审核(status2通过,3拒绝,sh_text)
  coachUpdate (querys) {
    return post('massage/admin/AdminCoach/coachUpdate', querys)
  },
  // 向导详情
  coachUpdateInfo (querys) {
    return get('massage/admin/AdminCoach/coachUpdateInfo', querys)
  },
  // 向导审核(status2通过,3拒绝,sh_text)
  coachUpdateCheck (querys) {
    return post('massage/admin/AdminCoach/coachUpdateCheck', querys)
  },
  // 关联用户
  coachUserList (querys) {
    return get('massage/admin/AdminCoach/coachUserList', querys)
  },
  // 新增向导
  coachAdd (querys) {
    return post('massage/admin/AdminCoach/coachAdd', querys)
  },
  // 编辑向导
  coachDataUpdate (querys) {
    return post('massage/admin/AdminCoach/coachDataUpdate', querys)
  },
  // 向导等级列表
  levelList (querys) {
    return get('massage/admin/AdminCoach/levelList', querys)
  },
  // 向导等级详情
  levelInfo (querys) {
    return get('massage/admin/AdminCoach/levelInfo', querys)
  },
  // 新增向导等级
  levelAdd (querys) {
    return post('massage/admin/AdminCoach/levelAdd', querys)
  },
  // 编辑向导等级
  levelUpdate (querys) {
    return post('massage/admin/AdminCoach/levelUpdate', querys)
  },
  // 个性标签 - 列表
  coachTag (querys) {
    return get('massage/admin/AdminCoach/coachTag', querys)
  },
  // 删除
  coachTagDel (querys) {
    return post('massage/admin/AdminCoach/coachTagDel', querys)
  },
  // 编辑
  coachTagEdit (querys) {
    return post('massage/admin/AdminCoach/coachTagEdit', querys)
  },
  // 添加
  coachTagAdd (querys) {
    return post('massage/admin/AdminCoach/coachTagAdd', querys)
  },
  // 个性标签 - 列表 不分页
  coachTagList (querys) {
    return get('massage/admin/AdminCoach/coachTagList', querys)
  },
  // 入驻协议
  agreement (querys) {
    return get('massage/admin/AdminSetting/agreement', querys)
  },
  // 入驻协议 -- 提交
  agreementPost (querys) {
    return post('massage/admin/AdminSetting/agreement', querys)
  },
  // 批量修改抽成比例
  setBalance (querys) {
    return post('massage/admin/AdminCoach/setBalance', querys)
  },
  // 编辑 - 代理商
  coachUpdateAdmin (querys) {
    return post('massage/admin/AdminCoach/coachUpdateAdmin', querys)
  },
  // 向导业绩
  coachCashData (querys) {
    return get('massage/admin/AdminCoach/coachCashData', querys)
  },
  // 向导编辑关联技能价格修改
  coachServiceUpdate (querys) {
    return post('massage/admin/AdminCoach/coachServiceUpdate', querys)
  },
  // 向导编辑关联技能添加
  coachServiceAdd (querys) {
    return post('massage/admin/AdminCoach/coachServiceAdd', querys)
  }
}
