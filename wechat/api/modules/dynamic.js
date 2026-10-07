import {
	req
} from '../../utils/req.js';
export default {
	// 动态列表
	dynamicList(param) {
		return req.get("dynamic/app/IndexDynamicList/dynamicList", param)
	},
	// 获取关注向导的最新动态数量
	getFollowData(param) {
		return req.get("dynamic/app/IndexDynamicList/getFollowData", param)
	},
	// 关注向导动态列表
	followDynamicList(param) {
		return req.get("dynamic/app/IndexDynamicList/followDynamicList", param)
	},
	// 动态详情
	dynamicInfo(param) {
		return req.get("dynamic/app/IndexDynamicList/dynamicInfo", param)
	},
	// 点赞或者取消点赞
	thumbsAddOrCancek(param) {
		return req.post("dynamic/app/IndexDynamicList/thumbsAddOrCancek", param)
	},
	// 我的关注
	followCoachList(param) {
		return req.get("dynamic/app/IndexDynamicList/followCoachList", param)
	},
	// 关注或者取消关注
	followAddOrCancek(param) {
		return req.post("dynamic/app/IndexDynamicList/followAddOrCancek", param)
	},
	// 评论列表
	commentList(param) {
		return req.get("dynamic/app/IndexDynamicList/commentList", param)
	},
	// 新增评论
	commentAdd(param) {
		return req.post("dynamic/app/IndexDynamicList/commentAdd", param)
	},
	// 删除评论
	commentDel(param) {
		return req.post("dynamic/app/IndexDynamicList/commentDel", param)
	},
	// --------- 向导端
	// 动态列表
	coachDynamicList(param) {
		return req.get("dynamic/app/IndexDynamicCoach/dynamicList", param)
	},
	// 动态详情
	coachDynamicInfo(param) {
		return req.get("dynamic/app/IndexDynamicCoach/dynamicInfo", param)
	},
	// 关注点赞消息详情
	dynamicData(param) {
		return req.get("dynamic/app/IndexDynamicCoach/dynamicData", param)
	},
	// 发布动态
	dynamicAdd(param) {
		return req.post("dynamic/app/IndexDynamicCoach/dynamicAdd", param)
	},
	// 编辑动态
	dynamicUpdate(param) {
		return req.post("dynamic/app/IndexDynamicCoach/dynamicUpdate", param)
	},
	// 删除动态
	dynamicDel(param) {
		return req.post("dynamic/app/IndexDynamicCoach/dynamicDel", param)
	},
	// 收获的赞
	thumbsList(param) {
		return req.get("dynamic/app/IndexDynamicCoach/thumbsList", param)
	},
	// 新增关注
	followList(param) {
		return req.get("dynamic/app/IndexDynamicCoach/followList", param)
	},
	// 收获的评论
	coachCommentList(param) {
		return req.get("dynamic/app/IndexDynamicCoach/commentList", param)
	},
}
