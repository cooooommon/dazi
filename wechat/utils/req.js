// 接口返回值code说明
// 200=>正常；
// 400=>报错；
// 401=>需要登陆；
// 402=>错误并且弹出报错，报错内容为 error；
// 403=>错误并且弹出报错，报错内容为 error(小程序跳转到个人中心)；
// 404=>错误并且弹出报错，报错内容为 error(小程序跳转到首页)；

var Fly = require("./wx.js") //wx.js为您下载的源码文件
var fly = new Fly; //创建fly实例
var tokenFly = new Fly();
import $api from '@/api/index.js';
import $util from './index.js';
import siteInfo from '../siteinfo.js';
import $store from "@/store/index.js"

// 打印站点信息siteInfo
let allSiteInfo = Object.assign({}, {
	time: "2023年11月22日 17:00",
	remark: "(test)~ update:迭代1.8~",
}, siteInfo)

import {
	networkError,
	serverError,
	msgError
} from './error.js';
import store from '@/store/index.js';
//添加finally方法,用于指定不管 Promise 对象最后状态如何，都会执行的操作
Promise.prototype.finally = function(callback) {
	let P = this.constructor;
	return this.then(
		value => P.resolve(callback()).then(() => value),
		reason => P.resolve(callback()).then(() => {
			throw reason
		})
	);
};

let isW7 = false
let isH5 = false
const formatUrl = function(url) {
	let model_name = "longbing_playwith";
	let baseUrl = isW7 ?
		`${siteInfo.siteroot}?i=${siteInfo.uniacid}&t=${siteInfo.multiid}&v=${siteInfo.version}&from=wxapp&c=entry&a=wxapp&do=api&core=core2&m=${model_name}&s=${url}&urls=${url}` :
		`${siteInfo.siteroot}?i=${siteInfo.uniacid}&m=${model_name}&s=${url}&urls=${url}`

	if (isH5) {
		baseUrl = `/api?i=${siteInfo.uniacid}&m=${model_name}&s=${url}`
	}
	return baseUrl
}
//阿里云地址转为本地域名的 
const formatImageUrl = function(url) {
	return url.includes(siteInfo.siteroot) ? url :
		`${formatUrl("card/getImage")}&path=${encodeURIComponent(url)}`
}

//微信小程序登录
const wxLogin = async function() {
	let {
		query
	} = await uni.getLaunchOptionsSync();
	let url = formatUrl("index/login")
	try {
		uni.showLoading({
			title: "登录中..."
		})
		let [providerErr, providerData] = await uni.getProvider({
			service: 'oauth',
		});
		let [loginErr, loginData] = await uni.login({
			provider: providerData.provider[0]
		});

		let login_param = {
			code: loginData.code,
			pid: query.pid
		}


		let d = await tokenFly.post(url, login_param);
		let {
			code,
			data,
			error
		} = d.data
		
		if (code !== 200) {
			uni.hideLoading()
			serverError({
				code,
				msg: error
			});
			throw d;
		}
		//登录成功
		uni.hideLoading()
		$store.commit('updateUserItem', {
			key: 'userInfo',
			val: data.data
		})
		$store.commit('updateUserItem', {
			key: 'autograph',
			val: data.autograph
		})
		// 加入黑名单后 禁止进入
		if(data.data.is_blacklist){
			$util.goUrl({url: `/pages/interdict`, openType: 'reLaunch'})
			return data
		}
		return data;
	} catch (e) {
		uni.hideLoading()
		let {
			code,
			error
		} = e.response.data
		if (code !== 200) {
			serverError({
				code,
				msg: error
			});
		}
		return await Promise.reject(e);
	}
}


//公众号登录
let H5LoginNum = 0
let pageArr = ['/pages/order']
const gzhLogin = async function() {
	let code = $util.getQueryString('code')
	let pageUrl = window.location.href
	if (H5LoginNum == 0 && code) {
		code = ''
	}
	if (code) {
		let {
			commonOptions
		} = $store.state.user
		let sOptions = uni.getStorageSync('commonOptions')
		sOptions = sOptions ? sOptions : '{}'
		let coupon_atv_id = $util.getQueryString('coupon_atv_id') || 0
		let pid = $util.getQueryString('pid') || commonOptions.pid || JSON.parse(sOptions).pid || 0
		let channel_id = $util.getQueryString('channel_id') || commonOptions.channel_id || JSON.parse(sOptions).channel_id || 0
		let channel_staff_id = $util.getQueryString('channel_staff_id') || commonOptions.channel_staff_id || JSON.parse(sOptions).channel_staff_id || 0
		let broker_id = $util.getQueryString('broker_id') || commonOptions.broker_id || JSON.parse(sOptions).broker_id || 0
		let admin_id = $util.getQueryString('admin_id') || commonOptions.admin_id || JSON.parse(sOptions).admin_id || 0
		let coach_id = $util.getQueryString('coach_id') || commonOptions.coach_id || JSON.parse(sOptions).coach_id || 0
		let channel_invite_id = $util.getQueryString('channel_invite_id') || commonOptions.channel_invite_id || JSON.parse(sOptions).channel_invite_id || 0
		let is_member = $util.getQueryString('is_member') || commonOptions.is_member || JSON.parse(sOptions).is_member || 0
		let url = formatUrl("index/webLogin")
		let from_type = 1 // 来源类型 1公众号搜索 2分享链接 3分销码 4渠道商邀请用户二维码 5渠道员工码 6技师邀请码 7代理商邀请码 8邀请充值 9渠道商邀请用户二维码 10分享员邀请购买会员卡is_member==1
		let from_id = 0 // 来源ID 1无id 2用户id pid 3分销员id pid 4渠道商id channel_id 5渠道员工id channel_staff_id 6经纪人id broker_id 7代理商id admin_id  8邀请充值 coach_id 9渠道商邀请员工二维码 channel_invite_id
		if(pid){
			from_type = is_member == 1 ? 10 : 2
			from_id = pid
		}
		if(channel_id){
			from_type = 4
			from_id = channel_id
		}
		if(channel_staff_id){
			from_type = 5
			from_id = channel_staff_id
		}
		if(admin_id){
			from_type = 7
			from_id = admin_id
		}
		if(broker_id){
			from_type = 6
			from_id = broker_id
		}
		if(coach_id > 0){
			from_type = 8
			from_id = coach_id
		}
		if(channel_invite_id){
			from_type = 9
			from_id = channel_invite_id
		}
		try {
			let d = await tokenFly.post(url, {
				login_type: 'gzh',
				code,
				pid,
				coupon_atv_id,
				from_type,
				from_id
			});
			H5LoginNum = 0

			let {
				code: res_code,
				data,
				error
			} = d.data

			if (res_code !== 200) {
				uni.hideLoading()
				serverError({
					code: res_code,
					msg: error
				});
				throw d;
			}
			//登录成功
			uni.hideLoading()
			let commonOptions = uni.getStorageSync('commonOptions')
			$store.commit('updateUserItem', {
				key: 'commonOptions',
				val: commonOptions ? JSON.parse(commonOptions) : {}
			})
			$store.commit('updateUserItem', {
				key: 'isGzhLogin',
				val: true
			})
			$store.commit('updateUserItem', {
				key: 'userInfo',
				val: data.data
			})
			$store.commit('updateUserItem', {
				key: 'autograph',
				val: data.autograph
			})
			// 加入黑名单后 禁止进入
			if(data.data.is_blacklist){
				$util.goUrl({url: `/pages/interdict`, openType: 'reLaunch'})
			}
			return data;
		} catch (e) {
			uni.hideLoading()
			let {
				code,
				error
			} = e.response.data
			if (code == 40163) {
				H5LoginNum = 0
				await $util.toAsyncLogin()
				setTimeout(() => {
					$util.hideAll()
					fly.unlock()
				}, 200)
			} else {
				serverError({
					code,
					msg: error
				});
				uni.hideLoading()
			}
			return await Promise.reject(e)
		}
	} else {
		H5LoginNum = 0
		await $util.toAsyncLogin()
		$util.hideAll()
		fly.unlock()
		return await Promise.reject("跳转授权===============")
	}
}


// app登录
let appLoginNum = 0
const appLogin = async function() {
	let {
		autograph = '',
			appLogin = {},
			loginType = 'weixin'
	} = $store.state.user

	if (appLoginNum == 0 && autograph) {
		appLogin = ''
	}
	if (appLogin) {
		code = ''
		let url = formatUrl(loginType == 'weixin' ? 'index/appLogin' : 'index/iosLogin')
		try {
			let d = await tokenFly.post(url, {
				data: appLogin
			});
			appLoginNum = 0

			let {
				code: res_code,
				data,
				error
			} = d.data

			if (res_code !== 200) {
				uni.hideLoading()
				serverError({
					code: res_code,
					msg: error
				});
				throw d;
			}
			//登录成功
			uni.hideLoading()
			$store.commit('updateUserItem', {
				key: 'isShowLogin',
				val: false
			})
			$store.commit('updateUserItem', {
				key: 'userInfo',
				val: data.data
			})
			$store.commit('updateUserItem', {
				key: 'autograph',
				val: data.autograph
			})
			// 加入黑名单后 禁止进入
			if(data.data.is_blacklist){
				$util.goUrl({url: `/pages/interdict`, openType: 'reLaunch'})
			}
			return data;
		} catch (e) {
			uni.hideLoading()
			appLoginNum = 0
			let {
				code,
				error
			} = e.response.data
			if (code !== 200) {
				serverError({
					code,
					msg: error
				});
			}
			return await Promise.reject(e);
		}

	} else {
		appLoginNum = 0
		await $util.toAsyncLogin()
		$util.hideAll()
		fly.unlock()
		return await Promise.reject("跳转授权===============");
	}
}

//设置超时
fly.config.timeout = 30000;

//设置请求基地址

//给所有请求添加自定义header
fly.config.headers = tokenFly.config.headers = {
	"content-type": "application/json"
}


let isapp = 0
// #ifdef APP-PLUS
isapp = 1;
// #endif
// #ifdef H5
isapp = 2;
// #endif

//添加请求拦截器
fly.interceptors.request.use(
	async (request) => {
		//添加验证token
		request.headers['autograph'] = $store.state.user.autograph || '';
		request.headers['isapp'] = isapp;
		return request;
	})

//添加响应拦截器，响应拦截器会在then/catch处理之前执行
fly.interceptors.response.use(
	async (response) => {
			// #ifdef H5
			let h5code = $util.getQueryString('code')
			if (h5code && !$store.state.user.autograph && H5LoginNum == 0) {
				response.data.code = 401
				H5LoginNum++
			}
			// #endif

			//token过期验证
			if (response.data.code != 401) return response;
			fly.lock()

			//#ifdef  MP-WEIXIN
			await wxLogin()
			//#endif

			//#ifdef H5
			if ($store.state.user.autograph) {
				let storeArr = ['userInfo', 'mineInfo', 'coachInfo', 'userPageType']
				storeArr.map(key => {
					$store.commit('updateUserItem', {
						key,
						val: key == 'coachInfo' ? {
							id: -1,
						} : key == 'userPageType' ? 1 : ''
					})
				})
				H5LoginNum++
			}
			await gzhLogin()
			//#endif

			//#ifdef  APP-PLUS 
			if (!$store.state.user.autograph) {
				appLoginNum++
			}
			await appLogin()
			//#endif

			response.request.headers["autograph"] = $store.state.user.autograph || ''
			fly.unlock();
			return fly.request(response.request);
		},
		async (err) => {
			let {
				status = 0,
			} = err

			$util.hideAll()
			console.error('[api] 网络请求失败', status, err && (err.message || err.errMsg))
			// networkError({
			// 	code: status,
			// })
			//网络错误
			return await Promise.reject(err);
		}
)

//统一处理请求,satus=200网络正常code=200服务器正常
const httpType = ["post", "get"]
const formatReq = function() {
	let req = {};
	httpType.forEach((type) => {
		req[type] = async function(url, param) {
			//构造请求地址
			url = formatUrl(url);

			let res = await fly[type](url, param)
			// #ifdef MP-BAIDU
			res.data = typeof(res.data) == "string" ? JSON.parse(res.data) : res.data;
			// #endif
			let {
				code,
				error,
				data
			} = res.data
			code = code * 1
			if (code === 200) return data;
			//业务错误统一记录，便于线上排查（urls 为模块/方法路由）
			console.warn(`[api] ${url.split('&urls=')[1] || url} -> code=${code} ${error || ''}`)
			//code!=200抛出错误
			$util.hideAll();
			if (code == 400 && error) {
				msgError({
					msg: error
				})
			}else if (code == 407) {
				$util.goUrl({
					url: `/pages/interdict`,
					openType: `reLaunch`
				})
			}
			return await Promise.reject(res.data);
		}
	})
	return req;
}
const req = formatReq();


// 定义上传,picture--代表图片 audio--音频 video--视频,默认picture
const uploadFile = async (url, {
	name = "file",
	filePath,
	header = {
		autograph: $store.state.user.autograph || '',
		isapp: isapp
	},
	formData = {
		type: 'picture'
	}
} = {}) => {
	url = formatUrl(url);
	let [, res] = await uni.uploadFile({
		url,
		filePath,
		name,
		formData,
		header,
	})

	if (res.statusCode != 200) {
		$util.hideAll()
		networkError();
		return await Promise.reject(res);
	}
	let parseData = JSON.parse(res.data)
	//服务器错误
	let {
		code,
		error,
		data
	} = parseData;
	if (code != 200) {
		$util.hideAll()
		if (code === 401) {
			fly.lock()
			//#ifdef  MP-WEIXIN
			await wxLogin()
			//#endif

			//#ifdef H5
			if ($store.state.user.autograph) {
				let storeArr = ['userInfo', 'autograph', 'mineInfo', 'coachInfo', 'userPageType']
				storeArr.map(key => {
					$store.commit('updateUserItem', {
						key,
						val: key == 'coachInfo' ? {
							id: -1,
						} : key == 'userPageType' ? 1 : ''
					})
				})
				H5LoginNum = 0
			}
			await gzhLogin()
			//#endif

			//#ifdef  APP-PLUS 
			if (!$store.state.user.autograph) {
				appLoginNum++
			}
			await appLogin()
			//#endif
		} else {
			serverError({
				code,
				msg: error
			});
		}
		return await Promise.reject(res);
	}
	return data
}

export {
	fly,
	req,
	uploadFile,
	formatImageUrl,
	formatUrl,
	wxLogin,
}
