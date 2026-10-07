import {
	req
} from '../../utils/req.js';
export default {
	// 渠道商下拉
	channelCateSelect(param) {
		return req.get("massage/app/IndexUser/channelCateSelect", param)
	},
	// 申请渠道商
	applyChannel(param) {
		return req.post("massage/app/IndexUser/applyChannel", param)
	},
	// 渠道商信息
	channelInfo(param) {
		return req.get("massage/app/IndexUser/channelInfo", param)
	},
	// 渠道商首页
	index(param) {
		return req.get("massage/app/IndexChannel/index", param)
	},
	// 渠道商二维码
	channelQr(param) {
		return req.get("massage/app/IndexChannel/channelQr", param)
	},
	// 订单列表
	orderList(param) {
		return req.get("massage/app/IndexChannel/orderList", param)
	},
	// 我的收益
	commList(param) {
		return req.get("massage/app/IndexChannel/commList", param)
	},
	// 生成邀请员工二维码
	inviteStaffQr(param) {
		return req.post("massage/app/IndexChannel/inviteStaffQr", param)
	},
	// 绑定渠道商
	bindChannel(param) {
		return req.post("massage/app/IndexUser/bindChannel", param)
	},
	// 邀请员工二维码信息
	inviteStaffQrInfo(param) {
		return req.get("massage/app/IndexUser/inviteStaffQrInfo", param)
	},
	// 我的员工列表
	staffList(param) {
		return req.get("massage/app/IndexChannel/staffList", param)
	},
	// 编辑员工
	updateStaff(param) {
		return req.post("massage/app/IndexChannel/updateStaff", param)
	},
	//申请提现
	applyWallet(param) {
		return req.post("massage/app/IndexChannel/applyWallet", param)
	},
	//提现记录
	walletList(param) {
		return req.get("massage/app/IndexChannel/walletList", param)
	},
	//渠道流水
	staffCommList(param) {
		return req.get("massage/app/IndexChannelStaff/commList", param)
	},
	//员工基础信息
	staffIndex(param) {
		return req.get("massage/app/IndexChannelStaff/index", param)
	}
}
