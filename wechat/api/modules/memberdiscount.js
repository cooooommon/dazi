import {
	req
} from '../../utils/req.js';
export default {
	// 会员卡列表
	cardList(param) {
		return req.get("member/app/cardList", param)
	},
	// 会员设置
	configInfo(param) {
		return req.get("member/app/configInfo", param)
	},
	// 
	payOrder(param) {
		return req.post("member/app/payOrder", param)
	},
	// 交易记录 
	orderList(param) {
		return req.get("member/app/orderList", param)
	},
	// 佣金列表
	cashList(param) {
		return req.get("member/app/cashList", param)
	},
	// 重新支付
	rePayOrder(param) {
		return req.post("member/app/rePayOrder", param)
	},
}
