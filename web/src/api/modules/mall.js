/*
 * @Description: 商城
 * @Author: wen kun
 * @Date: 2022-10-11 15:42:01
 * @LastEditTime: 2022-10-11 18:55:03
 * @LastEditors: xiao li
 */
import {
    get,
    post
} from '../index'
export default {
    // 分类添加
    addCarte (querys) {
        return post('massage/admin/AdminShop/addCarte', querys)
    },
    // 分类列表
    carteList (querys) {
        return get('massage/admin/AdminShop/carteList', querys)
    },
    // 上架、下架、删除分类
    carteStatus (querys) {
        return post('massage/admin/AdminShop/carteStatus', querys)
    },
    // 分类编辑回显
    editCarte (querys) {
        return get('massage/admin/AdminShop/editCarte', querys)
    },
    // 分类编辑提交
    editCartePost (querys) {
        return post('massage/admin/AdminShop/editCarte', querys)
    },
    // 分类下拉
    goodsCarteList (querys) {
        return get('massage/admin/AdminShop/goodsCarteList', querys)
    },
    // 商品列表
    goodsList (querys) {
        return get('massage/admin/AdminShop/goodsList', querys)
    },
    // 上下架、删除商品
    goodsStatus (querys) {
        return post('massage/admin/AdminShop/goodsStatus', querys)
    },
    // 添加商品
    addGoods (querys) {
        return post('massage/admin/AdminShop/addGoods', querys)
    },
    // 编辑商品 回显
    editGoods (querys) {
        return get('massage/admin/AdminShop/editGoods', querys)
    },
    // 编辑商品 提交
    editGoodsPost (querys) {
        return post('massage/admin/AdminShop/editGoods', querys)
    },
}