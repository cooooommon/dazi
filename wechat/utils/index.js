import Validate from './validate.js';
import $store from "@/store/index.js"
import $api from "api/index.js"
import $util from './index.js';
import { getSystemInfo } from './systemInfo.js';
// #ifdef H5
import $jweixin from "@/utils/jweixin.js"
// #endif
export default {
	Validate,
	//调试开关：上线保持静默；需要排查问题时临时改回 console.log
	log: () => {},
	// log: console.log,
	// 计算直线距离
	getDistance(lat1, lon1, lat2, lon2) {
		const R = 6371e3; // 地球半径，单位米
		const phi1 = lat1 * Math.PI / 180; // 纬度1，弧度制
		const phi2 = lat2 * Math.PI / 180; // 纬度2，弧度制
		const phiDelta = (lat2 - lat1) * Math.PI / 180; // 两点纬度差，弧度制
		const lambdaDelta = (lon2 - lon1) * Math.PI / 180; // 两点经度差，弧度制

		const a = Math.sin(phiDelta / 2) * Math.sin(phiDelta / 2) +
			Math.cos(phi1) * Math.cos(phi2) *
			Math.sin(lambdaDelta / 2) * Math.sin(lambdaDelta / 2)
		const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

		const distance = R * c
		return distance
	},
	//格式化时间
	formatTime(date, format) {
		let newFormat = format || 'YY-M-D h:m:s';
		let formatNumber = this.formatNumber;
		let newDate = date || new Date();
		if (Object.prototype.toString.call(newDate).slice(8, -1) !== "Date") {
			newDate = new Date(date);
		}
		let week = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', '日', '一', '二', '三',
			'四',
			'五', '六'
		];
		return newFormat.replace(/YY|Y|M|D|h|m|s|week|星期/g, function(a) {
			switch (a) {
				case 'YY':
					return newDate.getFullYear();
				case 'Y':
					return (newDate.getFullYear() + '').slice(2);
				case 'M':
					return formatNumber(newDate.getMonth() + 1);
				case 'D':
					return formatNumber(newDate.getDate());
				case 'h':
					return formatNumber(newDate.getHours());
				case 'm':
					return formatNumber(newDate.getMinutes());
				case 's':
					return formatNumber(newDate.getSeconds());
				case '星期':
					return "星期" + week[newDate.getDay() + 7];
				case 'week':
					return week[newDate.getDay()];
			}
		})
	},
	// 日期转时间戳
	DateToUnix: function(string) {
		var f = string.split(' ', 2);
		var d = (f[0] ? f[0] : '').split('-', 3);
		var t = (f[1] ? f[1] : '').split(':', 3);
		return (new Date(
			parseInt(d[0], 10) || null,
			(parseInt(d[1], 10) || 1) - 1,
			parseInt(d[2], 10) || null,
			parseInt(t[0], 10) || null,
			parseInt(t[1], 10) || null,
			parseInt(t[2], 10) || null
		)).getTime() / 1000;
	},
	//格式化数字
	formatNumber(n) {
		n = n.toString();
		return n[1] ? n : '0' + n
	},
	// 保留小数 （不四舍五入）
	formatDecimal(num, decimal) {
		num = num.toString()
		let index = num.indexOf('.')
		if (index !== -1) {
			num = num.substring(0, decimal + index + 1)
		} else {
			num = num.substring(0)
		}
		return parseFloat(num).toFixed(decimal)
	},
	// 两个数字之间的整数
	betweenNumbers(arr, arr2) {
		let a = parseInt(arr)
		let b = parseInt(arr2)
		let c = []
		if (arr - b < 0) {
			const number = Math.abs(a - b) + 1
			for (let i = a; i < a + number; i++) {
				c.push(i)
			}
		} else if (a - b === 0) {
			c.push(a)
		} else if (a - b > 0) {
			const number = Math.abs(a - b)
			for (let i = a; i > number; i--) {
				c.push(i)
			}
		}
		return c.join(',')
	},
	// 实时检测输入金额
	formatMoney(val) {
		if (val.slice(0, 1) == "0" && val.slice(1, 2) > 0) {
			val = val.slice(1, 2);
		}
		if (val.slice(0, 1) == ".") {
			val = '0.';
		}
		if (val == "0.00") {
			val = '0.0';
		}
		return val.replace(/[^\d\.]|^\./g, '').replace(/\.{2}/g, '.').replace(
			/^([1-9]\d*|0)(\.\d{1,2})(\.|\d{1})?$/, '$1$2').replace(/^0\d{1}/g, '0')
	},
	// 根据生日的月份和日期，计算星座。
	getAstro(month, day) {
		var s = '摩羯水瓶双鱼白羊金牛双子巨蟹狮子处女天秤天蝎射手摩羯'
		var arr = [20, 19, 21, 20, 21, 22, 23, 23, 23, 24, 23, 22]
		return s.substr(month * 2 - (day < arr[month - 1] ? 2 : 0), 2) + '座'
	},
	toWeiXinString(date) {
		let str;
		let newDate = date || new Date();
		if (Object.prototype.toString.call(newDate).slice(8, -1) !== "Date") {
			newDate = new Date(date);
		}
		const now = newDate;
		const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		const yesterday = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
		const beforeYesterday = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 2);
		const monday = new Date(today);
		monday.setDate(today.getDate() - (today.getDay() ? today.getDay() - 1 : 6));
		//注意：date初始化默认是按本地时间初始的，但打印默认却是按GMT时间打印的，也就是说打印出的不是本地现在的时间
		//LocaleString的打印也有点问题，"0点"会被打印为"上午12点"
		if (now.getTime() > today.getTime()) {
			str = "";
		} else if (now.getTime() > yesterday.getTime()) {
			str = "昨天";
		} else if (now.getTime() > beforeYesterday.getTime()) {
			str = "前天";
		} else if (now.getTime() > monday.getTime()) {
			const week = {
				"0": "周日",
				"1": "周一",
				"2": "周二",
				"3": "周三",
				"4": "周四",
				"5": "周五",
				"6": "周六"
			};
			str = week[now.getDay() + ""];
		} else {
			const hour = ["凌晨", "早上", "下午", "晚上"];
			const h = now.getHours();
			if (h == 12) str = "中午";
			else str = hour[parseInt(h / 6)];
			str = now.format("MM月dd ") + str;
		}
		str += now.format("HH:ss");
		return str;
	},
	// 两时间之间的差数 （天）
	getDaysBetweenDates(date1, date2) { 
	  const oneDay = 24 * 60 * 60 * 1000; // 一天的毫秒数
	  const time1 = new Date(date1);
	  const time2 = new Date(date2);
	  const difference = Math.abs(time1 - time2);
	  return Math.ceil(difference / oneDay); // Math.floor
	},
	// 两个数字之间的整数
	betweenNumbers(arr, arr2) {
		let a = parseInt(arr)
		let b = parseInt(arr2)
		let c = []
		if (arr - b < 0) {
			const number = Math.abs(a - b) + 1
			for (let i = a; i < a + number; i++) {
				c.push(i)
			}
		} else if (a - b === 0) {
			c.push(a)
		} else if (a - b > 0) {
			const number = Math.abs(a - b)
			for (let i = a; i > number; i--) {
				c.push(i)
			}
		}
		return c.join(',')
	},
	//返回类型
	typeOf(param) {
		return Object.prototype.toString.call(param).slice(8, -1)
	},
	//判断是否为空
	isEmpty(param) {
		//基本类型为空
		let condition1 = param === '' || param === null || param === undefined || param === "NaN";
		let condition2;
		let condition3
		//引用类型为空
		if (!condition1) {
			condition2 = this.typeOf(param) === "Object" && Object.keys(param).length < 1;
			condition3 = this.typeOf(param) === "Array" && param.length < 1;
		}
		return condition1 || condition2 || condition3;
	},
	showLoading({
		title = "加载中",
		mask = true
	} = {}) {
		uni.showLoading({
			title,
			mask
		});
	},
	showToast({
		title,
		icon = "none"
	} = {}) {
		uni.showToast({
			title,
			icon,
			duration: 2000
		});
	},
	hideAll() {
		uni.hideLoading();
		uni.stopPullDownRefresh();
		uni.hideNavigationBarLoading();
	},
	showModal({
		title = "提示",
		content = "没有返回值,检查服务器是否正常"
	}) {
		uni.showModal({
			title,
			content,
			showCancel: false
		});
	},
	setNavigationBarColor({
		color = '#ffffff',
		bg
	}) {
		uni.setNavigationBarColor({
			frontColor: color,
			backgroundColor: bg,
			// animation: {
			// 	duration: 100,
			// 	timingFunc: 'easeIn'
			// }
		})
	},
	getQueryString(name) {
		let reg = new RegExp("([^?&]*)" + name + "=([^&]*)(&|$)");
		let r = window.location.href.match(reg);
		if (r !== null) return unescape(r[2]);
		return false
	},
	getHostname(url) {
		var reg = /^http(s)?:\/\/(.*?)\//
		// 必须是http开头或者https开头，结尾为'/'
		var ToReplace = 'Host/'
		url.replace(reg, ToReplace)
		url = reg.exec(url)[2];
		return url;
	},
	//获取标签上data
	getDataSet(e) {
		return e && e.currentTarget && e.currentTarget.dataset
	},
	//获表单控件值
	getValue(e) {
		return e.detail.value
	},
	async getNavBarHeight() {
		var {
			statusBarHeight,
			navigationBarHeight
		} = getSystemInfo()

		let navBarHeight = statusBarHeight * 1 + 44
		// #ifdef MP-BAIDU
		navBarHeight = statusBarHeight * 1 + navigationBarHeight * 1
		// #endif
		return navBarHeight
	},
	pick(obj, arr) {
		return arr.reduce((acc, curr) => (curr in obj && (acc[curr] = obj[curr]), acc), {});
	},
	getUrlToStr(url, params) {
		let text = url
		if (params) {
			text = `${url}?` + Object.keys(params)
				.filter(key => params[key] || params[key] === 0) // 删除无值的字段，可根据需要进行保留或删除
				.map(key => `${key}=${params[key]}`)
				.toString().replace(/,/g, '&')
		}
		return text
	},
	async toAsyncLogin() {
		let pageArr = ['/pages/order']
		let pages = getCurrentPages()
		let {
			route,
			options = {}
		} = pages[pages.length - 1]
		route = route === `pages/login` ? `pages/service` : route
		let routeUrl = await this.getUrlToStr(`/${route}`, options)
		let {
			userPageType = 1
		} = $store.state.user
		let pageParam = route === 'pages/mine' ? `?type=${userPageType}` : ``
		let loginPage = `${routeUrl}${pageParam}`
	
		let openType = pageArr.includes(`/${route}`) ? `reLaunch` : `navigateTo`
		// uni.setStorageSync('loginPage', loginPage)
		$store.commit('updateUserItem', {
			key: 'loginPage',
			val: loginPage
		})
		this.goUrl({
			url: `/pages/login`,
			openType
		})
	},
	toCheckLogin(param) {
		let {
			loginType = 'weixin',
				userInfo
		} = $store.state.user
		let {
			id: uid = 0,
			phone = ''
		} = userInfo
		let {
			short_code_status = 0,
			bind_phone_type = 0
		} = $store.state.config.configInfo

		let checkLogin = true

		// #ifdef H5
		loginType = 'weixin'
		// #endif

		// #ifndef MP-WEIXIN
		//if (!uid || (loginType === 'weixin' && short_code_status && bind_phone_type && !phone)) {
		if (!uid || (short_code_status && bind_phone_type && !phone)) {
			checkLogin = false
			let pages = getCurrentPages()
			let {
				route,
				options = {}
			} = pages[pages.length - 1]
			let routeUrl = this.getUrlToStr(`/${route}`, options)
			let url = !uid ? `/pages/login` : `/user/pages/phone`
			$store.commit('updateUserItem', {
				key: 'loginPage',
				val: routeUrl
			})
			this.goUrl({
				url
			})
		}
		// #endif

		if (checkLogin) {
			this.goUrl(param)
		}
	},
	goUrl({
		url = "",
		openType = "navigateTo",
		path = "",
		query = ""
	} = {}) {
		let that = this;
		if (!url) return;
		if (this.typeOf(query) == 'Object') {
			let queryStr = Object.keys(query).map(k => `${k}=${query[k]}`).join('&');
			url = `${url}&${queryStr}`
		}
		let list = ["navigateTo", "redirectTo", "switchTab", "reLaunch"];
		if (list.includes(openType)) {
			if (openType == 'navigateTo' && getCurrentPages().length > 9) {
				uni.redirectTo({
					url
				})
			} else {
				uni[openType]({
					url
				})
			}
		}
		//返回
		openType == "navigateBack" && uni.navigateBack({
			delta: url
		})
		//跳转小程序
		openType == "miniProgram" && uni.navigateToMiniProgram({
			appId: url,
			path
		})
		//打电话
		openType == "call" && uni.makePhoneCall({
			phoneNumber: url
		})
		// 复制文本
		openType == 'copy' && uni.setClipboardData({
			data: url,
			success: function(res) {
				uni.getClipboardData({
					success: function(res) {
						// #ifdef MP-BAIDU 
						that.showToast({
							title: '复制成功'
						})
						// #endif
					}
				});
			}
		})
		//跳转网页/播放视频
		if (openType == "web" || openType == 'video') {
			let encode_url = encodeURIComponent(url)
			uni.navigateTo({
				url: `/user/pages/common/${openType}?url=${encode_url}`
			})
		}
	},
	// 刷新上页数据
	back() {
		let pages = getCurrentPages(); //当前页面栈
		if (pages.length > 1) {
			var beforePage = pages[pages.length - 2]; //获取上一个页面实例对象  
			//触发父页面中的方法change()  
			beforePage.$vm.initRefresh()
		}
	},
	//获取页面对象，0时为当前页面
	getPage(index = 0) {
		let pages = getCurrentPages();
		let page = pages[pages.length - 1 + index]
		return page.$vm
	},
	rgbColor(color, rgb = 0.8) {
		const binaryPattern = /^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i;
		const match = color.match(binaryPattern);
		if (match) {
			// 将二进制转换为十进制
			const r = parseInt(match[1], 16);
			const g = parseInt(match[2], 16);
			const b = parseInt(match[3], 16);
			return `rgba(${r}, ${g}, ${b}, ${rgb})`;
		}
		return color
	},
	// 预览图片
	previewImage(param) {
		let {
			current,
			urls
		} = param;
		uni.previewImage({
			current,
			urls,
		})
	},
	// 根据type获取数据字符串 
	getItems(o, type = 'id', sign = ',') {
		let items = [];
		o = o || [];
		o.forEach((item) => {
			items.push(item[type])
		})
		return items.join(sign);
	},
	// 检查授权
	async checkAuth({
		type = "userLocation",
		tip = ""
	} = {}) {
		// #ifdef MP-WEIXIN
		let contentList = {
			phone: "授权获取手机号",
			userLocation: "你的地理位置",
			address: "你的通讯地址",
			invoiceTitle: "发票抬头",
			invoice: "获取发票",
			werun: "微信运动步数",
			record: "你的录音功能",
			writePhotosAlbum: "你的保存到相册功能",
			camera: "摄像头"
		}
		tip = tip || `您暂未开启${contentList[type]}的授权，是否开启?`
		// 声明
		let err, result;
		[err, result] = await uni.getSetting();
		//获取配置失败
		if (err) {
			return Promise.reject(err);
		}
		//如果已授权
		if (result.authSetting[`scope.${type}`]) {
			return true
		}
		// 调用获取权限
		[err, result] = await uni.authorize({
			scope: `scope.${type}`
		})
		//获取成功
		if (!err) {
			return true
		}
		uni.hideLoading()
		//提示去设置
		return new Promise((resove, reject) => {
			uni.showModal({
				content: tip,
				success(res) {
					if (res.confirm) {
						uni.openSetting({
							success(result) {
								if (result.authSetting[`scope.${type}`]) {
									resove(true)
								} else {
									reject()
								}
							}
						})
					} else {
						reject()
					}
				}
			})
		})
		// #endif
	},
	//深拷贝
	deepCopy(o) {
		let that = this;
		if (o instanceof Array) {
			var n = [];
			for (var i = 0; i < o.length; ++i) {
				n[i] = that.deepCopy(o[i]);
			}
			return n;
		} else if (o instanceof Function) {
			var n = new Function("return " + o.toString())();
			return n
		} else if (o instanceof Object) {
			var n = {}
			for (var i in o) {
				n[i] = that.deepCopy(o[i]);
			}
			return n;
		} else {
			return o;
		}
	},
	getOptions(options, comminOptions = {}) {
		return Object.assign({}, comminOptions, this.formatOptions(options))
	},
	//获取用户端公共参数
	getCommonOptions(options) {
		return this.pick(options, ["staff_id"])
	},
	// 微信支付
	async pay(pay_list) {
		let that = this;
		let param = {}

		//小程序虚拟支付（个人主体 short_series_goods，pay_list 由服务端签名生成）
		if (pay_list && pay_list.virtual_pay) {
			return that.virtualPay(pay_list)
		}

		// #ifdef H5
		param = that.pick(pay_list, ['appId', 'timeStamp', 'nonceStr', 'package', 'signType',
			'paySign'
		])
		if (typeof WeixinJSBridge != "undefined") {
			return new Promise((resove, reject) => {
				WeixinJSBridge.invoke(
					'getBrandWCPayRequest', param,
					function(res) {
						if (res.err_msg == "get_brand_wcpay_request:ok") {
							setTimeout(() => {
								resove(true)
							}, 1000)
						} else {
							that.showToast({
								title: `支付失败`
							})
							setTimeout(() => {
								reject("支付失败")
							}, 1000)
						}
					})
			});
		}
		// #endif


		let {
			provider = 'wxpay',
				orderInfo = ''
		} = pay_list

		// #ifdef MP-WEIXIN
		param = that.pick(pay_list, ['nonceStr', 'package', 'signType', 'paySign', 'timeStamp'])
		// #endif
		// #ifdef APP-PLUS
		if (provider == 'wxpay') {
			orderInfo = that.pick(pay_list, ['appid', 'noncestr', 'package', 'partnerid', 'prepayid',
				'timestamp'
			])
			orderInfo.sign = pay_list.paySign
			orderInfo = JSON.stringify(orderInfo)
		}
		param = {
			provider,
			orderInfo
		}
		// #endif
		// #ifndef H5
		let [err, res] = await uni.requestPayment(param)
		if (err) {
			that.showToast({
				title: `支付失败`
			})
			await Promise.reject("支付失败")
		} else {
			return true
		}
		// #endif
	},
	//小程序虚拟支付（wx.requestVirtualPayment，payData 由服务端签名生成）
	async virtualPay(pay_list) {
		let that = this;
		// #ifndef MP-WEIXIN
		return Promise.reject("当前端不支持虚拟支付")
		// #endif
		// #ifdef MP-WEIXIN
		//iOS 需要微信 8.0.68 及以上
		try {
			let sys = getSystemInfo()
			if (sys.platform == 'ios' && that.compareVersion(sys.version || '', '8.0.68') < 0) {
				that.showToast({
					title: `当前微信版本过低，请更新微信后再支付`
				})
				return Promise.reject("微信版本过低")
			}
		} catch (e) {}
		return new Promise((resove, reject) => {
			wx.requestVirtualPayment({
				signData: pay_list.signData,
				paySig: pay_list.paySig,
				signature: pay_list.signature,
				mode: pay_list.mode || 'short_series_goods',
				success: async (res) => {
					//轮询服务端发货结果（发货推送丢失时服务端会查单兜底）
					let outTradeNo = ''
					try {
						outTradeNo = JSON.parse(pay_list.signData).outTradeNo || ''
					} catch (e) {}
					console.log(`[virtualPay] 支付成功，等待发货确认 ${outTradeNo}`)
					let delivered = false
					for (let i = 0; i < 8 && outTradeNo; i++) {
						await new Promise(r => setTimeout(r, 1500))
						try {
							let st = await $api.virtualpay.orderStatus({
								out_trade_no: outTradeNo
							})
							if (st && st.status == 1) {
								delivered = true
								break
							}
						} catch (e) {
							break
						}
					}
					if (delivered) {
						console.log(`[virtualPay] 发货确认完成 ${outTradeNo}`)
					} else {
						//不影响支付结果，服务端推送/查单兜底会异步补发
						console.warn(`[virtualPay] 12秒内未确认发货，等待服务端兜底 ${outTradeNo}`)
					}
					resove(true)
				},
				fail: (err) => {
					//失败原因以 errCode/errMsg 为准（如 -15009 代币未发布、-15014 代币配置同步中）
					console.error('[virtualPay] 支付失败', err && err.errCode, err && err.errMsg)
					let msg = '支付失败'
					if (err && err.errMsg && err.errMsg.indexOf('cancel') > -1) msg = '已取消支付'
					that.showToast({
						title: msg
					})
					reject(msg)
				}
			})
		})
		// #endif
	},
	//版本号比较：v1 > v2 返回 1，等于返回 0，小于返回 -1
	compareVersion(v1 = '', v2 = '') {
		let a1 = String(v1).split('.')
		let a2 = String(v2).split('.')
		let len = Math.max(a1.length, a2.length)
		for (let i = 0; i < len; i++) {
			let n1 = parseInt(a1[i] || '0', 10)
			let n2 = parseInt(a2[i] || '0', 10)
			if (n1 > n2) return 1
			if (n1 < n2) return -1
		}
		return 0
	},
	//小程序自带获取定位
	getLocation() {
		let that = this;
		return new Promise((resove, reject) => {
			uni.getLocation({
				// #ifdef H5
				type: 'wgs84',
				// #endif
				// #ifndef H5
				type: 'gcj02',
				// #endif
				success: function(res) {
					let {
						latitude: lat,
						longitude: lng
					} = res
					let locationInfo = {
						lat,
						lng,
						address: '',
						province: '',
						city: '',
						district: ''
					}
					resove(locationInfo)
				},
				fail: function(e) {
					let locationInfo = {
						lat: 0,
						lng: 0,
						address: '',
						province: '',
						city: '',
						district: ''
					}
					resove(locationInfo)
				}
			})
		})
	},
	// 选择地址
	async chooseLocation(type = 0, changeUseLoca = true) {
		if (changeUseLoca) {
			$store.commit('updateUserItem', {
				key: 'useChooseLocation',
				val: true
			})
		}
		let param = {}
		// #ifdef H5
		if ($jweixin.isWechat()) {
			let wxReady = await $jweixin.wxReady2();
			let {
				latitude,
				longitude
			} = wxReady ? await $jweixin.getWxLocation() : {
				latitude: 0,
				longitude: 0
			}
			param = {
				latitude,
				longitude
			}
		}
		// #endif
		// #ifdef APP-PLUS
		let {
			location = {
				lat: 0
			}
		} = $store.state.user
		if (!location.lat) {
			await this.checkAuth({
				type: 'userLocation',
				checkApp: true
			})
			return {
				lat: 0,
				lng: 0,
				name: '',
				address: ''
			}
		}
		let {
			lat: latitude,
			lng: longitude
		} = await this.getLocation()
		param = {
			latitude,
			longitude
		}
		// #endif 
		// #ifdef MP-WEIXIN
		await this.checkAuth({
			type: 'userLocation'
		})
		// #endif

		let [err, loca_data] = await uni.chooseLocation(param)
		let {
			name = '',
				address = '',
				latitude: lat = '',
				longitude: lng = ''
		} = err ? {
			name: '',
			address: '',
			latitude: '',
			longitude: ''
		} : loca_data
		if (type == 1) {
			address = address || name
		}
		if (type == 2) {
			address = address ? `${address}${name}` : name
		}
		let loca_params = type == 1 ? {
			lat,
			lng,
			name,
			address,
			province: '',
			city: '',
			district: ''
		} : {
			lat,
			lng,
			name,
			address
		}
		if (changeUseLoca) {
			$store.commit('updateUserItem', {
				key: 'useChooseLocation',
				val: false
			})
		}
		return loca_params
	},
	// 获取定位
	async getUtilLocation(updateLoca = true) {
		let locaParams = $util.deepCopy($store.state.user.location)
		// #ifdef H5
		if ($jweixin.isWechat()) {
			let wxReady = await $jweixin.wxReady2();
			let {
				latitude: lat = 0,
				longitude: lng = 0
			} = wxReady ? await $jweixin.getWxLocation() : {
				latitude: 0,
				longitude: 0
			}
			let location = this.toChangeUpdateUtilLoca(updateLoca, locaParams, {
				lat,
				lng,
				is_util_loca: 1
			})
			return location
		}
		// #endif
		// #ifndef H5
		let {
			lat,
			lng
		} = await this.getLocation()
		let location = this.toChangeUpdateUtilLoca(updateLoca, locaParams, {
			lat,
			lng,
			is_util_loca: 1
		})
		return location
		// #endif   
	},
	async toChangeUpdateUtilLoca(updateLoca, loca, locaParams) {
		let location = Object.assign({}, loca, locaParams)
		if (updateLoca) {
			$store.commit('updateUserItem', {
				key: 'location',
				val: location
			})
		}
		return location
	},
	// 解析地址
	async getMapInfo(updateLoca = true, params = {}, changeAddr = false) {
		let {
			location = {}
		} = $store.state.user
		if (params.lat && params.lng) {
			location = params
		}
		let {
			lat,
			lng
		} = location
		if (lat && lng) {
			let data = await $api.base.getMapInfo({
				location: `${lat},${lng}`
			})
			let {
				status,
				result
			} = JSON.parse(data)
			if (status == 0 || status == 1) {
				if (status == 1) {
					result = {
						address: '暂未获取到位置信息',
						address_component: {
							province: '',
							city: '',
							district: ''
						}
					}
				}
				let {
					address = '暂未获取到位置信息',
						address_component = {},
						formatted_addresses = {}
				} = result
				if (status == 0) {
					let {
						standard_address
					} = formatted_addresses
					address = standard_address
				}
				let {
					province,
					city,
					district
				} = address_component
				location = {
					lat,
					lng,
					address,
					province,
					city,
					district
				}
				if (updateLoca) {
					$store.commit('updateUserItem', {
						key: 'location',
						val: location
					})
				}
			}
			let {
				realtime_location = 0
			} = $store.state.config.configInfo
			let {
				userCoachStatus = {},
					coachInfo = {}
			} = $store.state.user

			let {
				status: coach_status = 0,
				coach_position = 0
			} = userCoachStatus
			if (coach_position && coach_status == 2 && ((realtime_location && !changeAddr) || params.lat)) {
				let {
					address: addr
				} = location
				let upParams = {
					lat,
					lng,
					address: addr
				}
				$api.technician.coachUpdate(upParams)
				let changeOnAddr = Object.assign({}, $store.state.user.changeOnAddr, upParams)
				$store.commit('updateUserItem', {
					key: 'changeOnAddr',
					val: changeOnAddr
				})
				let val = Object.assign({}, $util.deepCopy(userCoachStatus), upParams)
				let cval = Object.assign({}, $util.deepCopy(coachInfo), upParams)
				$store.commit('updateUserItem', {
					key: 'userCoachStatus',
					val
				})
				$store.commit('updateUserItem', {
					key: 'coachInfo',
					val: cval
				})
			}
			return location
		}
	},
	//定位+逆地址解析（地图服务后台可配：ims_massage_config.map_type = baidu | tencent）
	//baidu：前端 bmap-wx SDK 直连百度（key 取配置 bmap_key，缺省用内置 key）
	//tencent：微信定位拿坐标 → 服务端 Index/locationAddress 腾讯逆解析（key 取后台 map_secret）
	async getLocationInfo(ak = '', openLocation = false) {
		let that = this;
		let {
			configInfo = {}
		} = ($store.state && $store.state.config) || {}
		let mapType = configInfo.map_type || 'baidu'
		ak = ak || configInfo.bmap_key || 'GoI7BxLpfvBEyf1TcMXCloi99Vov7flZ'

		if (mapType == 'tencent') {
			let [locErr, locRes] = await uni.getLocation({
				type: 'gcj02'
			})
			if (locErr || !locRes || !locRes.latitude) {
				return that.locationFail(openLocation)
			}
			let lat = locRes.latitude
			let lng = locRes.longitude
			try {
				let data = await $api.base.locationAddress({
					lng,
					lat
				})
				let comp = (data && data.result && data.result.address_component) || {}
				return {
					lat,
					lng,
					address: (data && data.result && data.result.address) || '',
					province: comp.province || '',
					city: comp.city || '',
					district: comp.district || ''
				}
			} catch (e) {
				//解析失败不影响定位坐标返回
				console.warn('[location] 逆地址解析失败', e && e.error)
				return {
					lat,
					lng,
					address: '',
					province: '',
					city: '',
					district: ''
				}
			}
		}

		//百度：bmap-wx 定位+逆解析
		return new Promise((resove, reject) => {
			let bmap = require('./bmap-wx.min.js');
			let BMap = new bmap.BMapWX({
				ak
			});
			BMap.regeocoding({
				success: function(data) {
					let addressInfo = data.originalData.result;
					let {
						lat,
						lng
					} = addressInfo.location;
					let {
						formatted_address: address,
						addressComponent
					} = addressInfo;
					let {
						province,
						city,
						district
					} = addressComponent
					resove({
						lat,
						lng,
						address,
						province,
						city,
						district
					})
				},
				fail: function(res) {
					resove(that.locationFail(openLocation, res))
				}
			})
		})
	},
	//定位失败的统一处理：引导授权，仍失败返回默认空对象（调用方以 lat 为空判断）
	async locationFail(openLocation = false, res = {}) {
		let that = this;
		that.hideAll()
		let [modalRes] = await uni.showModal({
			title: "地理位置授权",
			content: "为了更好的为您服务，请开启您手机中的定位授权",
			confirmText: "去授权"
		})
		if (modalRes && modalRes.confirm) {
			let [settingRes] = await uni.openSetting({})
			if (settingRes && settingRes.authSetting && settingRes.authSetting['scope.userLocation']) {
				return that.getLocationInfo('', openLocation)
			}
		}
		return {
			name: '',
			lat: 0,
			lng: 0,
			latitude: 0,
			longitude: 0,
			address: '',
			city: ''
		}
	},
}
