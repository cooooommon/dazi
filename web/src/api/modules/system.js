/*
 * @Descripttion: 系统设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: wen kun
 * @LastEditTime: 2023-10-25 18:23:45
 */
import {
  get,
  post
} from '../index'
export default {
  // 获取授权的升级信息
  getUpgradeInfo () {
    return get('/agent/admin/getUpgradeInfo')
  },
  // 升级记录
  getUpRecord (querys) {
    return get('massage/admin/AdminSetting/getUpRecord', querys)
  },
  // 执行升级后台管理系统
  upgrade () {
    return post('/agent/admin/upgrade')
  },
  // 回显
  wxUploadInfo (querys) {
    return get('admin/admin/config/wxUploadInfo', querys)
  },
  // 上传微信审核
  wxUploadUpdate (querys) {
    return post('admin/admin/config/wxUploadUpdate', querys)
  },
  // 获取小程序版本
  wxappVersion (querys) {
    return post('admin/admin/config/wxappVersion', querys)
  },
  // 升级小程序
  uploadWxapp (querys) {
    return post('admin/admin/config/uploadWxapp', querys)
  },
  // 获取上传配置信息
  getOssConfig (querys) {
    return get('admin/admin/config/getOssConfig', querys)
  },
  // 设置上传配置信息
  updateOssConfig (querys) {
    return post('admin/admin/config/updateOssConfig', querys)
  },
  // 获取系统配置信息
  configInfo (querys) {
    return post('massage/admin/AdminSetting/configInfo', querys)
  },
  // 设置系统配置信息
  configUpdate (querys) {
    return post('massage/admin/AdminSetting/configUpdate', querys)
  },
  // 获取支付配置信息
  payConfigInfo (querys) {
    return post('massage/admin/AdminSetting/payConfigInfo', querys)
  },
  // 设置支付配置信息
  payConfigUpdate (querys) {
    return post('massage/admin/AdminSetting/payConfigUpdate', querys)
  },
  // 车费配置详情
  carConfigInfo (querys) {
    return get('massage/admin/AdminSetting/carConfigInfo', querys)
  },
  // 编辑车费配置详情
  carConfigUpdate (querys) {
    return post('massage/admin/AdminSetting/carConfigUpdate', querys)
  },
  // 城市车费列表
  getCarConfigList (querys) {
    return get('massage/admin/AdminSetting/getCarConfigList', querys)
  },
  // 城市车费详情
  getCarConfigInfo (querys) {
    return get('massage/admin/AdminSetting/getCarConfigInfo', querys)
  },
  // 新增城市车费
  getCarConfigAdd (querys) {
    return post('massage/admin/AdminSetting/getCarConfigAdd', querys)
  },
  // 编辑城市车费
  getCarConfigUpdate (querys) {
    return post('massage/admin/AdminSetting/getCarConfigUpdate', querys)
  },
  // 删除城市车费
  getCarConfigDel (querys) {
    return post('massage/admin/AdminSetting/getCarConfigDel', querys)
  },
  // 打印机详情
  printerInfo (querys) {
    return get('massage/admin/AdminPrinter/printerInfo', querys)
  },
  // 打印机设置
  printerUpdate (querys) {
    return post('massage/admin/AdminPrinter/printerUpdate', querys)
  },
  // 城市列表
  cityList (querys) {
    return get('massage/admin/AdminSetting/cityList', querys)
  },
  // 新增城市
  cityAdd (querys) {
    return post('massage/admin/AdminSetting/cityAdd', querys)
  },
  // 编辑城市
  cityUpdate (querys) {
    return post('massage/admin/AdminSetting/cityUpdate', querys)
  },
  // 城市信息
  cityInfo (querys) {
    return post('massage/admin/AdminSetting/cityInfo', querys)
  },
  // 城市下拉
  citySelect (querys) {
    return get('massage/admin/AdminSetting/citySelect', querys)
  },
  getCity (querys) {
    return get('massage/admin/AdminSetting/getCity', querys)
  },
  // 反馈列表
  feedbackList (querys) {
    return get('massage/admin/AdminSetting/feedbackList', querys)
  },
  // 反馈详情
  feedbackInfo (querys) {
    return get('massage/admin/AdminSetting/feedbackInfo', querys)
  },
  // 处理反馈
  feedbackHandle (querys) {
    return post('massage/admin/AdminSetting/feedbackHandle', querys)
  },
  // 申述列表
  appealList (querys) {
    return get('massage/admin/AdminSetting/appealList', querys)
  },
  // 申述详情
  appealInfo (querys) {
    return get('massage/admin/AdminSetting/appealInfo', querys)
  },
  // 处理申诉
  appealHandle (querys) {
    return post('massage/admin/AdminSetting/appealHandle', querys)
  },
  // 获取虚拟号码配置信息
  virtualConfigInfo (querys) {
    return get('virtual/admin/AdminSetting/configInfo', querys)
  },
  // 设置虚拟号码配置信息
  virtualConfigUpdate (querys) {
    return post('virtual/admin/AdminSetting/configUpdate', querys)
  },
  // 虚拟号码录音文件
  phoneRecordList (querys) {
    return get('virtual/admin/AdminSetting/phoneRecordList', querys)
  },
  // 获取来电提醒配置信息
  reminderConfigInfo (querys) {
    return get('/reminder/admin/AdminSetting/configInfo', querys)
  },
  // 设置来电提醒配置信息
  reminderConfigUpdate (querys) {
    return post('/reminder/admin/AdminSetting/configUpdate', querys)
  },
  // 获取模版通知配置信息
  sendMsgConfigInfo (querys) {
    return get('massage/admin/AdminSetting/sendMsgConfigInfo', querys)
  },
  // 设置模版通知配置信息
  sendMsgConfigUpdate (querys) {
    return post('massage/admin/AdminSetting/sendMsgConfigUpdate', querys)
  },
  // 获取短信配置信息
  shortCodeConfigInfo (querys) {
    return get('massage/admin/AdminSetting/shortCodeConfigInfo', querys)
  },
  // 设置短信配置信息
  shortCodeConfigUpdate (querys) {
    return post('massage/admin/AdminSetting/shortCodeConfigUpdate', querys)
  },
  // 动态配置
  configInfoSchedule (querys) {
    return get('massage/admin/AdminSetting/configInfoSchedule', querys)
  },
  // 编辑动态配置
  configUpdateSchedule (querys) {
    return post('massage/admin/AdminSetting/configUpdateSchedule', querys)
  },
  // 服务类型列表
  demandTypeList (querys) {
    return get('/massage/admin/AdminSetting/demandTypeList', querys)
  },
  // 服务类型添加
  demandTypeAdd (querys) {
    return post('/massage/admin/AdminSetting/demandTypeAdd', querys)
  },
  // 服务类型编辑
  demandTypeEdit (querys) {
    return post('/massage/admin/AdminSetting/demandTypeEdit', querys)
  },
  // 服务类型删除
  demandTypeDel (querys) {
    return post('/massage/admin/AdminSetting/demandTypeDel', querys)
  }
}
