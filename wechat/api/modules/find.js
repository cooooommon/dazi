import {
	req
} from '../../utils/req.js';
export default {
	// 发现列表
	demandOrderList(param) {
		return req.get("massage/app/Index/demandOrderList", param)
	},
	// 我的发布
	getList(param) {
		return req.get("massage/app/IndexDemand/getList", param)
	},
	//技能列表/服务分类
	getSkill(param) {
		return req.get("massage/app/Index/getSkill", param)
	},
	// 发布邀约订单
	payDemandOrder(param) {
		return req.post("massage/app/IndexOrder/payDemandOrder", param)
	},
	// 接单
	receivingOrder(param) {
		return req.post("massage/app/IndexCoach/receivingOrder", param)
	},
	// 我的邀约
	coachDemandOrderList(param) {
		return req.get("massage/app/IndexCoach/demandOrderList", param)
	},
	// 确认完成邀约  -- 陪玩官端
	complete(param) {
		return req.post("massage/app/IndexCoach/complete", param)
	},
	// 确认完成邀约  -- 用户端
	userComplete(param) {
		return req.post("massage/app/IndexDemand/complete", param)
	},
	// 打赏陪玩官
	payRewardOrder(param) {
		return req.post("massage/app/IndexOrder/payRewardOrder", param)
	},
	// 发布订单详情
	orderInfo(param) {
		return req.post("massage/app/IndexDemand/orderInfo", param)
	},
	// 发布邀约订单 - 支付
	rePayDemandOrder(param) {
		return req.post("massage/app/IndexOrder/rePayDemandOrder", param)
	},
	// 取消订单
	cancel(param) {
		return req.post("massage/app/IndexDemand/cancel", param)
	},
	// 技师报名
	orderApply(param) {
		return req.post("massage/app/IndexCoach/orderApply", param)
	},
	// 技师取消报名
	cancelApply(param) {
		return req.post("massage/app/IndexCoach/cancelApply", param)
	},
	// 查看报名人员
	seeApply(param) {
		return req.get("massage/app/IndexDemand/seeApply", param)
	},
	// 选择向导
	chooseCoach(param) {
		return req.post("massage/app/IndexDemand/chooseCoach", param)
	},
	// 邀约多订单发布
	payManyDemandOrder(param) {
		return req.post("massage/app/IndexOrder/payManyDemandOrder", param)
	},
}
