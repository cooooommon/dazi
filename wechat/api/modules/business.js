import {
	req
} from '../../utils/req.js';
export default {
	// 获取门店信息
	getStore(param) {
		return req.get("massage/app/IndexStore/getStore", param)
	},
	// 门店申请
	apply(param) {
		return req.post("massage/app/IndexStore/apply", param)
	},
	// 门店修改
	edit(param) {
		return req.post("massage/app/IndexStore/edit", param)
	},
	// 门店列表
	getList(param) {
		return req.get("massage/app/IndexStore/getList", param)
	},
	// 门店详情
	getInfo(param) {
		return req.get("massage/app/IndexStore/getInfo", param)
	},
	// 门店分类不分页
	storeTypeList(param) {
		return req.get("massage/app/IndexStore/storeTypeList", param)
	},
	// 门店套餐列表
	storePackList(param) {
		return req.get("store/app/IndexPackage/storePackList", param)
	},
	// 套餐详情
	storePackInfo(param) {
		return req.get("store/app/IndexPackage/storePackInfo", param)
	},
	// 套餐添加
	packageAdd(param) {
		return req.post("store/app/StorePackage/add", param)
	},
	// 套餐列表
	packageList(param) {
		return req.get("store/app/StorePackage/getList", param)
	},
	// 修改状态
	packageUpdateStatus(param) {
		return req.post("store/app/StorePackage/updateStatus", param)
	},
	// 详情
	packageInfo(param) {
		return req.get("store/app/StorePackage/getInfo", param)
	},
	// 编辑
	packageEdit(param) {
		return req.post("store/app/StorePackage/edit", param)
	},
	// 套餐下单 
	payOrder(param) {
		return req.post("store/app/IndexOrder/payOrder", param)
	},
	// 套餐订单
	// 列表
	orderList(param) {
		return req.get("store/app/IndexOrder/orderList", param)
	},
	// 重新支付
	rePayOrder(param) {
		return req.post("store/app/IndexOrder/rePayOrder", param)
	},
	// 订单数量
	orderCount(param) {
		return req.get("store/app/IndexOrder/orderCount", param)
	},
	// 删除订单
	delOrder(param) {
		return req.post("store/app/IndexOrder/delOrder", param)
	},
	// 订单详情
	orderInfo(param) {
		return req.get("store/app/IndexOrder/orderInfo", param)
	},
	// 获取虚拟手机号
	getVirtualPhone(param) {
		return req.post("store/app/IndexOrder/getVirtualPhone", param)
	},
	// 申请售后
	applyRefund(param) {
		return req.post("store/app/IndexOrder/applyRefund", param)
	},
	// 退款订单列表
	refundList(param) {
		return req.get("store/app/IndexOrder/refundList", param)
	},
	// 取消退款订单
	refundCancel(param) {
		return req.post("store/app/IndexOrder/refundCancel", param)
	},
	// 删除退款订单
	refundDel(param) {
		return req.post("store/app/IndexOrder/refundDel", param)
	},
	// 退款订单详情
	refundInfo(param) {
		return req.get("store/app/IndexOrder/refundInfo", param)
	},
	// 评价订单
	addComment(param) {
		return req.post("store/app/IndexOrder/addComment", param)
	},
	// 评论列表
	commentList(param) {
		return req.get("massage/app/IndexStore/commentList", param)
	},
	// 收藏
	collect(param) {
		return req.post("store/app/IndexPackage/collect", param)
	},
	// 商家端
	// 订单列表
	storeOrderList(param) {
		return req.get("store/app/StoreOrder/orderList", param)
	},
	// 订单详情
	storeOrderInfo(param) {
		return req.get("store/app/StoreOrder/orderInfo", param)
	},
	// 订单数量
	storeOrderCount(param) {
		return req.get("store/app/StoreOrder/orderCount", param)
	},
	// 退款订单列表
	storeRefundList(param) {
		return req.get("store/app/StoreOrder/refundList", param)
	},
	// 审核退款订单
	refundCheck(param) {
		return req.post("store/app/StoreOrder/refundCheck", param)
	},
	// 核销订单
	hxOrder(param) {
		return req.post("store/app/StoreOrder/hxOrder", param)
	},
	// 获取虚拟手机号
	getStoreVirtualPhone(param) {
		return req.post("store/app/storeOrder/getVirtualPhone", param)
	},
	// 查看评论
	seeComment(param) {
		return req.get("store/app/StoreOrder/seeComment", param)
	},
	// 推广员返佣列表
	shareCashList(param) {
		return req.get("massage/app/IndexUser/shareCashList", param)
	},
	// 推广人申请提现
	applyWallet(param) {
		return req.post("massage/app/IndexUser/shareApplyWallet", param)
	},
	// 提现佣金信息
	storeCashData(param) {
		return req.get("store/app/StoreOrder/cashData", param)
	},
	// 申请提现
	storeApplyWallet(param) {
		return req.post("store/app/StoreOrder/applyWallet", param)
	},
	// 提现流水
	walletList(param) {
		return req.get("store/app/StoreOrder/walletList", param)
	},
	// 秒杀
	// 秒杀列表
	seckillList(param) {
		return req.get("seckill/app/seckillList", param)
	},
	// 添加活动
	seckillAdd(param) {
		return req.post("store/app/StorePackage/seckillAdd", param)
	},
	// 编辑活动
	seckillEdit(param) {
		return req.post("store/app/StorePackage/seckillEdit", param)
	},
	// 活动详情
	getSeckillInfo(param) {
		return req.get("store/app/StorePackage/seckillEdit", param)
	},
	// 商家端 活动列表 
	getSeckillList(param) {
		return req.get("store/app/StorePackage/getSeckillList", param)
	},
}
