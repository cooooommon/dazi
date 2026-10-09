import {
	req,
	uploadFile
} from '../../utils/req.js';
export default {
	// 小程序登录
	login(param) {
		return req.post("index/login", param)
	},
	// 公众号登录
	webLogin(param) {
		return req.post("index/webLogin", param)
	},
	// app微信登录
	appLogin(param) {
		return req.post("index/appLogin", param)
	},
	// app苹果登录
	iosLogin(param) {
		return req.post("index/iosLogin", param)
	},
	// app登录配置
	getConfig(param) {
		return req.get("index/getConfig", param)
	},
	// 获取配置
	getWebConfig(param) {
		return req.get("index/getWebConfig", param)
	},
	// 系统配置
	configInfo(param) {
		return req.get("massage/app/Index/configInfo", param)
	},
	// 获取地图定位
	getMapInfo(param) {
		return req.get("massage/app/Index/getMapInfo", param)
	},
	// 坐标逆地址解析（腾讯位置服务）
	locationAddress(param) {
		return req.post("massage/app/Index/locationAddress", param)
	},
	// 解析二维码
	getWxCodeData(param) {
		return req.get("card/app/getWxCodeData", param)
	},
	// base64ToImg
	base64ToImg(param) {
		return req.get("massage/app/IndexUser/base64ToImg", param)
	},
	// 上传图片
	uploadFile(param) {
		return uploadFile("admin/app/wx/uploadFile", param)
	},
	// 上传视频
	uploadVideo(param) {
		return uploadFile("admin/app/wx/uploadVideo", param)
	},
	// 获取城市
	getCity(param) {
		return req.get("massage/app/Index/getCity", param)
	},
	// 获取插件授权
	plugAuth(param) {
		return req.get("massage/app/Index/plugAuth", param)
	},
}
