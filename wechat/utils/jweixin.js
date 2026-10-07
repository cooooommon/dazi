import $api from "@/api/index.js"
import $util from "@/utils/index.js"
import $store from "@/store/index.js"
var jweixin = require('jweixin-module');

export default {
	//判断是否在微信中    
	async isWechat() {
		var ua = window.navigator.userAgent.toLowerCase();
		if (ua.match(/micromessenger/i) == 'micromessenger') {
			//console.log('是微信客户端')  
			return true;
		} else {
			//console.log('不是微信客户端')  
			return false;
		}
	},
	async initJssdk() {
		// let page = window.location.href.split('#')[0]; //获取当前url然后传递给后台获取授权和签名信息
		// let page = /(Android)/i.test(navigator.userAgent) ? window.location.href.split('?#')[0]+'?' : window.entryUrl;
		let page = window.entryUrl;
		// console.log(page)
		var {
			appid: appId,
			timestamp,
			nonceStr,
			signature
		} = await $api.base.getWebConfig({
			page
		});
		// signature = signature + `abcdef`
		jweixin.config({
			debug: false,
			appId,
			timestamp,
			nonceStr,
			signature,
			jsApiList: [ //这里是需要用到的接口名称  
				'checkJsApi', //判断当前客户端版本是否支持指定JS接口  
				'getLocation', //获取位置  
				'openLocation', //打开位置  
				'scanQRCode', //扫一扫接口  
				'chooseWXPay', //微信支付  
				'chooseImage', //拍照或从手机相册中选图接口  
				'previewImage', //预览图片接口
				'uploadImage', //上传图片  
				'updateAppMessageShareData',
				'updateTimelineShareData',
				'onMenuShareAppMessage',
				'onMenuShareTimeline',
				'hideOptionMenu',
				'showOptionMenu',
				'showMenuItems'
			]
		});
	},
	wxReady: function(fn) {
		jweixin.ready(function() {
			fn()
		})
	},
	async wxReady2() {
		return new Promise((resolve, reject) => {
			jweixin.ready(function() {
				resolve(1)
			})
		});
	},
	// 关闭右上菜单
	async hideOptionMenu() {
		return new Promise((resolve, reject) => {
			jweixin.hideOptionMenu()
		});
	},
	// 二维码扫码
	async getScanQRCode(){
		return new Promise((resolve, reject) => {
			jweixin.scanQRCode({
				needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
				scanType: ['qrCode', 'barCode'], // 可以指定扫二维码还是一维码，默认二者都有
				success: function (res) { 
					resolve(res)
				},
				fail: function (response) {
					reject(response)
					console.log("调用wx.scanQRCode扫码失败");
				},
			});
		});
	},
	// 开启右上菜单
	async showOptionMenu() {
		return new Promise((resolve, reject) => {
			jweixin.showOptionMenu()
			jweixin.showMenuItems({
				menuList: [
					'menuItem:share:appMessage',
					'menuItem:share:timeline'
				]
			})
		})
	},
	/*分享给微信朋友*/
	shareAppMessage: function(title, desc, link, imgUrl) {
		jweixin.updateAppMessageShareData({
			title,
			desc,
			link,
			imgUrl,
			success: function(res) {
				// console.log(res, "======shareAppMessage res")
			}
		})
		jweixin.onMenuShareAppMessage({
			title,
			desc,
			link,
			imgUrl,
			success: function(res) {
				// console.log(res, "======onMenuShareAppMessage res")
			}
		})
	},
	/*分享微信朋友圈*/
	shareTimelineMessage: function(title, link, imgUrl) {
		jweixin.updateTimelineShareData({
			title,
			link,
			imgUrl,
			success: function(res) {
				// console.log(res, "======shareTimelineMessage res")
			}
		})
		jweixin.onMenuShareTimeline({
			title,
			link,
			imgUrl,
			success: function(res) {
				// console.log(res, "======onMenuShareTimeline res")
			}
		})
	},
	async getWxLocation() {
		let location = {
			latitude: 0,
			longitude: 0
		}
		return new Promise((resolve, reject) => {
			try {
				jweixin.getLocation({
					type: 'gcj02',
					complete: function(res) {
						console.log(res, "==========getLocation complete")
						resolve(res.errMsg === 'getLocation:ok' ? res : location)
					}
				})
			} catch (e) {
				console.log(e, "==========getLocation catch")
				resolve(location)
			}
		});
	
	}
	// async getWxLocation() {
	// 	let location = {
	// 		latitude: 0,
	// 		longitude: 0
	// 	}
	// 	return new Promise((resolve, reject) => {
	// 		try {
	// 			jweixin.checkJsApi({
	// 				jsApiList: ['getLocation'],
	// 				success: function(res) {
	// 					// 以键值对的形式返回，可用的api值true，不可用为false
	// 					// 如：{"checkResult":{"chooseImage":true},"errMsg":"checkJsApi:ok"}
	// 					// console.log(res, "=======checkJsApi")
	// 					// alert(`checkJsApi==${JSON.stringify(res)}`)
	// 					let {
	// 						getLocation
	// 					} = res.checkResult
	// 					if (getLocation) {
	// 						jweixin.getLocation({
	// 							type: 'gcj02', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
	// 							success: function(res) {
	// 								// console.log(res, "===== getLocation success")
	// 								resolve(res)},
	// 							fail: function(err) {
	// 								// console.log(err,"===== getLocation fail")
	// 								reject(err)
	// 							}
	// 						});
	// 					} else {
	// 						resolve(location)
	// 					}
	// 				}
	// 			})
	// 		} catch (e) {
	// 			jweixin.error((err) => {
	// 				console.log('wx-config-error', err);
	// 				if (err.errMsg.includes('invalid signature')) {
	// 					resolve(location)
	// 				}
	// 			});
	// 		}

	// 	});

	// }
}
