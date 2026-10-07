import {
	req
} from '../../utils/req.js';
export default {
	// 服务标签列表
	serviceSelect(param) {
		return req.get("massage/app/Index/serviceSelect", param)
	},
	// 申请向导
	coachApply(param) {
		return req.post("massage/app/IndexUser/coachApply", param)
	},
	// 向导信息
	coachInfo(param) {
		return req.get("massage/app/IndexUser/coachInfo", param)
	},
	// 编辑向导
	coachUpdate(param) {
		return req.post("massage/app/IndexCoach/coachUpdate", param)
	},
	// 编辑向导
	coachUpdateV2(param) {
		return req.post("massage/app/IndexCoach/coachUpdateV2", param)
	},
	// 向导等级
	coachLevel(param) {
		return req.get("massage/app/IndexCoach/coachLevel", param)
	},
	// 向导首页
	coachIndex(param) {
		return req.get("massage/app/IndexCoach/coachIndex", param)
	},
	// 向导报警
	police(param) {
		return req.post("massage/app/IndexCoach/police", param)
	},
	// 订单列表
	orderList(param) {
		return req.get("massage/app/IndexCoach/orderList", param)
	},
	// 订单详情
	orderInfo(param) {
		return req.get("massage/app/IndexCoach/orderInfo", param)
	},
	// 修改订单状态(type,order_id)
	updateOrder(param) {
		return req.post("massage/app/IndexCoach/updateOrder", param)
	},
	// 拨打客户电话
	getVirtualPhone(param) {
		return req.post("massage/app/IndexCoach/getVirtualPhone", param)
	},
	getDemandVirtualPhone(param) {
		return req.post("massage/app/IndexCoach/getDemandVirtualPhone", param)
	},
	//佣金信息
	capCashInfo(param) {
		return req.get("massage/app/IndexCoach/capCashInfo", param)
	},
	//佣金信息（车费）
	capCashInfoCar(param) {
		return req.get("massage/app/IndexCoach/capCashInfoCar", param)
	},
	//申请提现 (apply_price,text,type：1服务费提现，2车费提现)
	applyWallet(param) {
		return req.post("massage/app/IndexCoach/applyWallet", param)
	},
	//提现记录
	capCashList(param) {
		return req.get("massage/app/IndexCoach/capCashList", param)
	},
	//时间管理回显
	timeConfig(param) {
		return req.get("massage/app/IndexCoach/timeConfig", param)
	},
	//时间管理设置
	setTimeConfig(param) {
		return req.post("massage/app/IndexCoach/timeConfig", param)
	},
	//根据接单时间获取时间节点
	getTime(param) {
		return req.get("massage/app/IndexCoach/getTime", param)
	},
	//根据接单时间获取时间节点
	getOrderNum(param) {
		return req.get("massage/app/IndexCoach/getOrderNum", param)
	},
	// 邀约订单
	getDemandNum(param) {
		return req.get("massage/app/IndexCoach/getDemandNum", param)
	},
	//物料商城-商品列表
	goodsList(param) {
		return req.get("massage/app/IndexCoach/goodsList", param)
	},
	//物料商城-分类列表
	carteList(param) {
		return req.get("massage/app/IndexCoach/carteList", param)
	},
	//物料商城-商品详情
	goodsInfo(param) {
		return req.get("massage/app/IndexCoach/goodsInfo", param)
	},
	//车费明细列表
	carMoneyList(param) {
		return req.get("massage/app/IndexCoach/carMoneyList", param)
	},
	//差评申诉 订单列表
	appealOrder(param) {
		return req.get("massage/app/IndexCoach/appealOrder", param)
	},
	//差评申诉 提交申诉
	addAppeal(param) {
		return req.post("massage/app/IndexCoach/addAppeal", param)
	},
	//差评申诉 申诉记录列表
	appealList(param) {
		return req.get("massage/app/IndexCoach/appealList", param)
	},
	//标签列表
	labelList(param) {
		return req.get("massage/app/IndexCoach/labelList", param)
	},
	//添加用户评价
	userLabelAdd(param) {
		return req.post("massage/app/IndexCoach/userLabelAdd", param)
	},
	//获取用户当前标签
	userLabelList(param) {
		return req.get("massage/app/IndexCoach/userLabelList", param)
	},
	//储值返佣列表
	balanceCommissionList(param) {
		return req.get("massage/app/IndexCoach/balanceCommissionList", param)
	},
	//储值返佣金额统计
	balanceCommissionData(param) {
		return req.get("massage/app/IndexCoach/balanceCommissionData", param)
	},
	//分成明细
	coachCommissionList(param) {
		return req.get("massage/app/IndexCoach/coachCommissionList", param)
	},
	//分成明细金额统计
	coachCommissionData(param) {
		return req.get("massage/app/IndexCoach/coachCommissionData", param)
	},
	//收益详情
	coachCommissionInfo(param) {
		return req.get("massage/app/IndexCoach/coachCommissionInfo", param)
	},
	//拉黑用户
	shieldUserAdd(param) {
		return req.post("massage/app/IndexCoach/shieldUserAdd", param)
	},
	//移除拉黑用户
	shieldUserDel(param) {
		return req.post("massage/app/IndexCoach/shieldUserDel", param)
	},
	//拉黑用户列表
	shieldCoachList(param) {
		return req.get("massage/app/IndexCoach/shieldCoachList", param)
	},
	//合作加盟
	participate(param) {
		return req.post("massage/app/Index/participate", param)
	},
	// 入驻协议
	agreement(param) {
		return req.get("massage/app/Index/agreement", param)
	},
	// 邀约明细
	demandCommissionDetail(param) {
		return req.get("massage/app/IndexCoach/demandCommissionDetail", param)
	},
	// 服务列表
	serviceList(param) {
		return req.get("massage/app/Index/serviceList", param)
	},
	// 服务分类详情
	serviceTypeInfo(param) {
		return req.get("massage/app/Index/serviceTypeInfo", param)
	},
}
