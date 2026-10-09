<template>
	<view class="pages-user-login" v-if="isLoad">
		<view class="flex-center flex-column" style="height: 100vh">

			<!-- #ifdef H5 -->
			<view class="logo-img mb-md">
				<view class="h5-image logo-img" :style="{ backgroundImage : `url('${configInfo.app_logo}')`}">
				</view>
			</view>
			<!-- #endif -->
			<!-- #ifndef H5 -->
			<image mode="aspectFill" lazy-load class="logo-img mb-md" :src="configInfo.app_logo"></image>
			<!-- #endif -->

			<view class="f-caption c-caption">{{configInfo.app_text}}</view>
			<view style="height: 200rpx;"></view>
			<view @tap.stop="toChooseLogin('weixin')" class="login-btn wechat flex-center f-title c-base radius" :style="{
			background: `linear-gradient(90deg, ${primaryColor} 0%, ${subColor} 99%)`}"><i
					class="iconfont icon-weixin mr-md"></i>微信登录
			</view>
			<!-- #ifdef APP-PLUS -->
			<view @tap.stop="toChooseLogin('apple')" class="login-btn apple flex-center f-title c-black radius"
				v-if="is_ios_login"><i class="iconfont icon-apple mr-sm"></i>Sign in with Apple
			</view>
			<!-- #endif -->
		</view>

		<!-- <uni-popup ref="show_rule_item" type="center" :maskClick="false">
			<view class="common-popup-content fill-base pd-lg radius-34" style="padding:50rpx 30rpx">
				<view class="title">温馨提示</view>
				<view @tap.stop="isAgree=!isAgree" class="f-desc c-title mt-lg">
					<span class="iconfont mr-sm" :class="isAgree?'icon-xuanze-fill':'icon-xuanze'"
						style="font-size: 28rpx;" :style="{color:isAgree?primaryColor:''}"></span>
					我已阅读并同意<span @tap.stop="$util.goUrl({url:`/user/pages/protocol`})" :style="{color:primaryColor}">
						《隐私政策》
					</span>与<span @tap.stop="$util.goUrl({url:`/user/pages/information`})"
						:style="{color:primaryColor}">
						《服务协议》
					</span>
				</view>
				<view class="button">
					<view @tap.stop="$refs.show_rule_item.close()" class="item-child">取消</view>
					<view @tap.stop="toConfirm" class="item-child" :style="{background: primaryColor,color:'#fff'}">确定
					</view>
				</view>
			</view>
		</uni-popup> -->
		<uni-popup ref="show_rule_item" type="center" :maskClick="false">
			<view class="common-popup-content fill-base pd-lg radius-34">
				<view class="title">温馨提示</view>
				<view class="f-desc c-title mt-lg">
					登录即表示您已详细阅读并同意<span @tap.stop="$util.goUrl({url:`/user/pages/protocol`})"
						:style="{color:primaryColor}">
						《用户隐私协议》
					</span>与<span @tap.stop="$util.goUrl({url:`/user/pages/information`})"
						:style="{color:primaryColor}">
						《个人信息保护指引》
					</span>
				</view>
				<view class="button">
					<view @tap.stop="$refs.show_rule_item.close()" class="item-child">取消</view>
					<view @tap.stop="toConfirm" class="item-child" :style="{background: primaryColor,color:'#fff'}">确定
					</view>
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from 'vuex';
	import siteInfo from '@/siteinfo.js';
	export default {
		components: {},
		data() {
			return {
				isLoad: false,
				is_ios_login: false,
				isAgree: false,
				lockTap: false,
			}
		},
		async onLoad(options) {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			let [syserr, sysinfo] = await uni.getSystemInfo()
			let {
				browserVersion = '',
					platform
			} = sysinfo
			this.is_ios_login = platform === 'ios' && browserVersion.split('.')[0] * 1 > 12
			await this.getConfigInfo()
			let {
				userInfo
			} = this
			if (!this.isLoad && (!userInfo || (userInfo && !userInfo.id))) {
				this.updateUserItem({
					key: 'isGzhLogin',
					val: false
				})
			}
			this.isLoad = true
		},
		onShow(){
			// if(uni.getStorageSync('userInfo') && uni.getStorageSync('userInfo').id){
			// 	uni.navigateBack({
			// 		delta: 1
			// 	});
			// }
			['autograph', 'userInfo', 'location', 'appLogin', 'loginType', 'isShowLogin', 'isGzhLogin'].forEach((item,index) => {
				if(!this[item] || !this[item].id){
					if(index == 0){
						this.updateUserItem({
							key: 'isPhone',
							val: true
						})
					}
					this.updateUserItem({
						key: item,
						val: uni.getStorageSync(item)
					})
				}
			})
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			pageArr: state => state.config.pageArr,
			commonOptions: state => state.user.commonOptions,
			autograph: state => state.user.autograph,
			appLogin: state => state.user.appLogin,
			loginType: state => state.user.loginType,
			loginPage: state => state.user.loginPage,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
		}),
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			...mapMutations(['updateConfigItem', 'updateUserItem', 'updateOrderItem']),
			toChooseLogin(type) {
				this.updateUserItem({
					key: 'loginType',
					val: type
				})
				this.$refs.show_rule_item.open()
			},
			toConfirm() {
				// if (!this.isAgree) {
				// 	this.$util.showToast({
				// 		title: `请先阅读并同意《隐私政策》与《服务协议》`
				// 	})
				// 	return
				// }
				if (this.lockTap) return
				this.lockTap = true
				this.$refs.show_rule_item.close()
				// #ifdef H5
				this.toGzhLogin()
				// #endif	
				// #ifdef APP-PLUS
				this.toAppLogin()
				// #endif
			},
			// 公众号登录
			async toGzhLogin() {
				await this.$util.getUtilLocation()
				let {
					siteroot,
					gzh_appid
				} = siteInfo

				this.updateUserItem({
					key: 'isGzhLogin',
					val: true
				})
				uni.setStorageSync('isGzhLogin', true)
				// this.updateUserItem({
				// 	key: 'commonOptions',
				// 	val: this.commonOptions
				// })

				let {
					pageArr,
					loginPage,
					userInfo
				} = this
				let {
					user_force_login,
					short_code_status
				} = this.configInfo
				
				// if (userInfo && userInfo.id) {
				// 	let {
				// 		id: uid = 0,
				// 		phone = ''
				// 	} = userInfo
				// 	let isToPhone = user_force_login == 1 && short_code_status && !phone
				// 	let url = isToPhone ? `/user/pages/phone` : loginPage || `/pages/service`
				// 	this.$util.goUrl({
				// 		url,
				// 		openType: `reLaunch`
				// 	})
				// 	this.lockTap = false
				// 	return
				// }

				if (userInfo && userInfo.id) {
					let {
						id: uid = 0,
						phone = ''
					} = userInfo
					let isToPhone = user_force_login == 1 && short_code_status && !phone
					let url = isToPhone ? `/user/pages/phone` : loginPage || `/pages/service`
					let split_url = url.split('?')[0]
					let isReLaunch = isToPhone || pageArr.includes(split_url)
					let openType = isReLaunch ? `reLaunch` : `navigateBack`
					this.$util.goUrl({
						url: isReLaunch ? url : 1,
						openType
					})
					this.lockTap = false
					return
				}

				this.updateUserItem({
					key: 'autograph',
					val: ''
				})
				
				let optionsArr = loginPage.split('?')

				if (user_force_login == 1 && short_code_status) {
					let new_options = optionsArr && optionsArr.length > 1 ? `${optionsArr[1]}&` : ``
					loginPage = `/user/pages/phone?${new_options}forcetype=1`
				}

				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#${loginPage}`
				let pageUrl = window.location.href;
				let redirect_uri = encodeURIComponent(href)
				let authUrl =
					`https://open.weixin.qq.com/connect/oauth2/authorize?appid=${gzh_appid}&redirect_uri=${redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE&connect_redirect=1#wechat_redirect`;
				window.location.href = authUrl
				this.lockTap = false
			},
			// APP登录
			async toAppLogin() {
				// this.toConfirmLoginApp()
				// return

				let {
					loginType: provider
				} = this

				try {
					let [providerErr, providerData] = await uni.getProvider({
						service: 'oauth',
					});
					if (providerErr) {
						this.$util.showToast({
							title: providerErr
						})
						return
					}
					let [loginErr, loginData] = await uni.login({
						provider
					});
					if (loginErr) {
						this.$util.showToast({
							title: loginErr
						})
						return
					}
					let [infoErr, infoData] = await uni.getUserInfo({
						provider
					})
					if (infoErr) {
						this.$util.showToast({
							title: infoErr
						})
						return
					}

					let {
						userInfo = {}
					} = infoData
					let {
						openId = ''
					} = userInfo
					if (!openId) return
					this.$util.showLoading({
						title: "登录中..."
					})
					try {
						let param = {
							type: provider == 'weixin' ? 2 : 3,
							param: {
								data: userInfo
							},
							init: 1
						}
						this.toConfirmLogin(param)
					} catch (e) {
						this.$util.hideAll()
					}
				} catch (e) {
					this.$util.showToast({
						title: `没有获取到用户信息，请确认已登录`
					});
				}
			},
			async toConfirmLogin(login_params) {
				let {
					loginType
				} = this

				let {
					type,
					param,
					init = 0
				} = login_params

				let methodType = {
					1: 'webLogin',
					2: 'appLogin',
					3: 'iosLogin'
				}
				let methodModel = methodType[type]
				let user_info = await this.$api.base[methodModel](param)
				if (init == 1) {
					this.updateUserItem({
						key: 'appLogin',
						val: param.data
					})
				}


				let {
					autograph,
					coach_status,
					data
				} = user_info
				this.$util.hideAll()
				this.updateUserItem({
					key: 'isShowLogin',
					val: false
				})
				this.updateUserItem({
					key: 'userInfo',
					val: data
				})
				this.updateUserItem({
					key: 'autograph',
					val: autograph
				})

				let {
					user_force_login,
					short_code_status = 0,
					plugAuth = {}
				} = this.configInfo

				let {
					coachport = false
				} = plugAuth
				this.updateUserItem({
					key: 'userPageType',
					val: [2, 3].includes(coach_status) && !coachport ? 2 : 1
				})

				let {
					phone = ''
				} = data

				let {
					pageArr,
					loginPage
				} = this

				// let isToPhone = loginType == 'weixin' && short_code_status && !phone
				let isToPhone = user_force_login == 1 && short_code_status && !phone
				let url = isToPhone ? `/user/pages/phone` : loginPage || `/pages/service`
				let split_url = url.split('?')[0]
				let isReLaunch = isToPhone || pageArr.includes(split_url)
				let openType = isReLaunch ? `reLaunch` : `navigateBack`
				this.$util.goUrl({
					url: isReLaunch ? url : 1,
					openType
				})
			},
			// app调试登录
			toConfirmLoginApp() {
				let user_info = {
					"data": {
						"id": 824,
						"uniacid": 666,
						"openid": "oDG4342lxj6wUH73W7pKLzTTvL6w",
						"nickName": "蕭萧",
						"avatarUrl": "https:\/\/massage.cncnconnect.com\/attachment\/image\/666\/23\/05\/a2f0efd10dfa4190aa125bc7ae522d93.jpg",
						"create_time": 1673263802,
						"status": 1,
						"cap_id": 0,
						"city": "",
						"country": "",
						"gender": 0,
						"language": "",
						"province": "",
						"balance": 92230.44,
						"phone": "18284514093",
						"session_key": "UsZEVHhv8vdYpJ+\/YuEjIA==",
						"pid": 824,
						"cash": 374.67,
						"unionid": "o3fbU0SmMbQLD4BKPyxcoD1KhAko",
						"app_openid": "oOxoV6NEt8Zl3P6lA7JWLH7NxMNc",
						"web_openid": "oFcA90U0Dy1YtuX91vppB5_LYgio",
						"wechat_openid": "oDG4342lxj6wUH73W7pKLzTTvL6w",
						"last_login_type": 0,
						"new_cash": "339.42",
						"lock": 178,
						"is_fx": 1,
						"ios_openid": "0",
						"push_id": "",
						"alipay_number": "18284514093",
						"alipay_name": "",
						"ip": "222.211.236.197"
					},
					"autograph": "f99e8e0daa9fb6d3575e1a32a4b27c57",
					coach_status: 2
				}

				let {
					autograph,
					coach_status,
					data
				} = user_info
				this.$util.hideAll()
				this.updateUserItem({
					key: 'isShowLogin',
					val: false
				})
				this.updateUserItem({
					key: 'userInfo',
					val: data
				})
				this.updateUserItem({
					key: 'autograph',
					val: autograph
				})
				let {
					user_force_login,
					short_code_status = 0,
					plugAuth = {}
				} = this.configInfo

				let {
					coachport = false
				} = plugAuth

				this.updateUserItem({
					key: 'userPageType',
					val: [2, 3].includes(coach_status) && !coachport ? 2 : 1
				})

				let {
					phone = ''
				} = data

				let {
					pageArr,
					loginPage
				} = this

				let isToPhone = user_force_login == 1 && short_code_status && !phone
				let url = isToPhone ? `/user/pages/phone` : loginPage || `/pages/service`
				let split_url = url.split('?')[0]
				let isReLaunch = isToPhone || pageArr.includes(split_url)
				let openType = isReLaunch ? `reLaunch` : `navigateBack`
				this.$util.goUrl({
					url: isReLaunch ? url : 1,
					openType
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background: #fff;
		height: 100%;
	}

	.pages-user-login {

		.page-height {
			height: 100vh;
		}
		.logo-img {
			width: 160rpx;
			height: 160rpx;
		}



		.login-btn {
			width: 220pt;
			height: 44pt;
			font-size: 15pt;
			border: 1rpx solid #000;
			transform: rotateZ(360deg);

			.iconfont {
				font-size: 19pt;
			}
		}

		.wechat {
			border: none;
			font-size: 13pt;
			box-shadow: 0 18rpx 9rpx 0 rgba(0, 188, 82, 0.07);
		}

		.apple {
			margin-top: 40rpx;
		}

		.banner-info {
			width: 100%;

			.banner-img {
				width: 100%;
				height: 100%;
			}

			.swiper-to-home {
				width: 122rpx;
				height: 47rpx;
				z-index: 999;
				right: 40rpx;
				top: 80rpx;
				background: rgba(0, 0, 0, 0.3);
			}
		}
	}
</style>
