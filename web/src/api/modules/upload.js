/*
 * @Descripttion: 文件上传
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2022-09-07 15:58:15
 */
import { post, get, postUpload } from '../index'

export default {
  // 新增分组
  addGroup (querys) {
    return post('admin/admin/file/createGroup', querys)
  },
  // 修改分组
  updateGroup (querys) {
    return post('admin/admin/file/updateGroup', querys)
  },
  // 移动分组
  moveGroup (querys) {
    return post('admin/admin/file/moveGroup', querys)
  },
  // 删除单个分组
  delGroup (querys) {
    return post('admin/admin/file/delGroup', querys)
  },
  delGroups (querys) {
    return post('admin/admin/file/delAllGroup', querys)
  },
  // 获取上传商品分组列表
  getGroupsList () {
    return get('admin/admin/file/listGroup')
  },
  // 上传图片
  uploadFiles (querys, fn) {
    return postUpload('admin/admin/file/uploadFiles', querys, fn)
  },
  // 上传文件到云存储后返回给后端
  uploadAddFile (querys) {
    return post('admin/admin/file/addFile', querys)
  },
  // 获取图片，视频，音频等    1=>图片  2=>音频  3=>视频
  getUploadFiles (querys) {
    return get('admin/admin/file/listFile', querys)
  },
  // 删除图片，视频，音乐等
  delFiles (querys) {
    return post('admin/admin/file/delFile', querys)
  }
}
