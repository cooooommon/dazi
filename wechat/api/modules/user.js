import {
	req
} from '../../utils/req.js';
export default {
	// 用户信息
	userInfo(param) {
		return req.get("massage/app/IndexUser/userInfo", param)
	},
	// 更新用户信息
	userUpdate(param) {
		return req.post("massage/app/IndexUser/userUpdate", param)
	},
	// 注销用户
	delUserInfo(param) {
		return req.post("/massage/app/IndexUser/delUserInfo", param)
	},
	// 获取手机号
	reportPhone(param) {
		return req.post("massage/app/IndexUser/reportPhone", param)
	},
	// 验证码
	sendShortMsg(param) {
		return req.post("massage/app/IndexUser/sendShortMsg", param)
	},
	// 绑定手机号
	bindUserPhone(param) {
		return req.post("massage/app/IndexUser/bindUserPhone", param)
	},
	// 获取订单数量
	getOrderNum(param) {
		return req.get("massage/app/IndexUser/getOrderNum", param)
	},
	// 修改用户来源
	updateFrom(param) {
		return req.post("massage/app/IndexUser/updateFrom", param)
	},
	// 积分明细
	integralList(param) {
		return req.get("massage/app/IndexUser/integralList", param)
	}
}
