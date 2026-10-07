/*
 * @Description: 会员卡
 * @Author: xiao li
 * @Date: 2021-07-06 18:32:16
 * @LastEditTime: 2024-11-26 15:48:39
 * @LastEditors: wen kun
 */

import {
    get,
    post
} from '../index'
export default {
    // 列表
    cardList (querys) {
        return get('member/admin/cardList', querys)
    },
    // 添加
    cardAdd (querys) {
        return post('member/admin/cardAdd', querys)
    },
    // 编辑
    cardUpdate (querys) {
        return post('member/admin/cardUpdate', querys)
    },
    // 详情
    cardInfo (querys) {
        return get('member/admin/cardInfo', querys)
    },
    // 设置
    getConfigSet (querys) {
        return get('member/admin/configSet', querys)
    },
    // 设置
    configSet (querys) {
        return post('member/admin/configSet', querys)
    },
    // 会员卡订单列表
    cardOrderList (querys) {
        return get('member/admin/orderLIst', querys)
    },
}
