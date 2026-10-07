import {
	req
} from '../../utils/req.js';
export default {
	// 首页轮播图
	index(param) {
		return req.get("massage/app/Index/index", param)
	},
	//文章详情
	articleInfo(param) {
		return req.get("massage/app/IndexArticle/articleInfo", param)
	},
	//文章详情-提交表单
	subArticleForm(param) {
		return req.post("massage/app/IndexArticle/subArticleForm", param)
	},
	// 服务列表
	serviceList(param) {
		return req.get("massage/app/Index/serviceList", param)
	},
	// 服务详情
	serviceInfo(param) {
		return req.get("massage/app/Index/serviceInfo", param)
	},
	// 服务向导列表无筛选项(ser_id，服务id,lat,lng)
	typeServiceCoachList(param) {
		return req.get("massage/app/Index/typeServiceCoachList", param)
	},
	// 服务向导列表(ser_id，服务id,lat,lng,type)
	serviceCoachList(param) {
		return req.get("massage/app/Index/serviceCoachList", param)
	},
	// 向导服务列表(coach_id)
	coachServiceList(param) {
		return req.get("massage/app/Index/coachServiceList", param)
	},
	// 向导服务列表 - 分页
	coachServiceListPage(param) {
		return req.get("massage/app/Index/coachServiceListPage", param)
	},
	// 向导服务列表(coach_id)
	commentList(param) {
		return req.get("massage/app/Index/commentList", param)
	},
	// 向导信息
	coachInfo(param) {
		return req.get("massage/app/Index/coachInfo", param)
	},
	//优惠券列表
	couponList(param) {
		return req.get("massage/app/Index/couponList", param)
	},
	//领取优惠券
	userGetCoupon(param) {
		return req.post("massage/app/Index/userGetCoupon", param)
	},
	//获取可升级的服务
	getUpOrderGoods(param) {
		return req.get("massage/app/IndexOrder/getUpOrderGoods", param)
	},
}
