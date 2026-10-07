/*
 * @Description: 经纪人管理
 * @Author: wen kun
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-12-13 16:38:32
 * @LastEditors: wen kun
 */

import {
    get,
    post
} from '../index'
export default {
    // 列表
    getList (querys) {
        return get('broker/admin/broker/getList', querys)
    },
    // 编辑/信息
    update (querys) {
        return post('broker/admin/broker/update', querys)
    },
    getInfo (querys) {
        return get('broker/admin/broker/update', querys)
    },
    // 用户列表
    userList (querys) {
        return get('broker/admin/broker/userList', querys)
    },
    // 新增
    add (querys) {
        return post('broker/admin/broker/add', querys)
    },
    // 经纪人数据
    getData (querys) {
        return get('broker/admin/broker/getData', querys)
    },
    // 向导列表
    coachList (querys) {
        return get('broker/admin/broker/coachList', querys)
    }
}
