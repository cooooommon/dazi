<template>
	<view class="mine-pages-phone flex-center flex-column" v-if="isLoad">
		<view class="f-md-title c-black" style="margin-top: 50rpx;">请输入手机号</view>
		<view class="f-caption c-caption">您暂未绑定手机号，快来绑定吧</view>
		<view class="flex-between mt-lg" style="width:630rpx">
			<view class="confirm-btn sm radius-16">
				<input v-model="subForm.phone" type="number"
					class="item-input flex-y-center pl-lg pr-lg f-sm-title c-title" placeholder-class="c-placeholder"
					:placeholder="rule[0].errorMsg" />
			</view>
			<view @tap="submit(1)" class="send-btn flex-center f-sm-title c-base radius-16"
				:style="{background:primaryColor}">
				{{authTime>0?`(${authTime}s)`:'发送'}}
			</view>
		</view>
		<view class="confirm-btn radius-16">
			<input v-model="subForm.short_code" type="number"
				class="item-input flex-y-center pl-lg pr-lg f-sm-title c-title" placeholder-class="c-placeholder"
				:placeholder="rule[1].errorMsg" />
		</view>

		<view class="mt-lg"></view>
		<view @tap="submit" class="confirm-btn flex-center f-sm-title text-bold c-base radius-16"
			:style="{background: primaryColor}">确定
		</view>
		
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				isLoad: false,
				options: {},
				authTime: 0,
				timer: null,
				subForm: {
					phone: '',
					short_code: ''
				},
				rule: [{
					name: "phone",
					checkType: "isMobile",
					errorMsg: "请输入手机号",
					regText: "手机号"
				}, {
					name: "short_code",
					checkType: "isNotNull",
					errorMsg: "请输入短信验证码",
					regText: "短信验证码"
				}],
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			pageArr: state => state.config.pageArr,
			userInfo: state => state.user.userInfo,
			loginPage: state => state.user.loginPage,
			isGzhLogin: state => state.user.isGzhLogin,
			autograph: state => state.user.autograph,
		}),
		async onLoad(options) {
			
			this.options = options
			let {
				isGzhLogin
			} = this
			let {
				id: uid = 0
			} = this.userInfo
			let {
				forcetype = 0
			} = this.options
			let time = isGzhLogin && !uid && forcetype == 1 ? 1000 : 0
			this.$util.showLoading()
			setTimeout(() => {
				this.initIndex()
			}, time)
		},
		// #ifdef H5
		destroyed() {
			this.timer && clearInterval(this.timer)
		},
		// #endif
		// #ifndef H5
		onUnload() {
			this.timer && clearInterval(this.timer)
		},
		// #endif  
		methods: {
			...mapActions(['getUserInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex() {
				let {
					forcetype = 0
				} = this.options
				if (forcetype == 1) {
					await this.getUserInfo()
				}
				let {
					phone
				} = this.userInfo
				if (phone) {
					let {
						pageArr,
						loginPage
					} = this
					console.log(loginPage, "===========loginPage")
					let url = loginPage || `/pages/service`
					let split_url = url.split('?')[0]
					let isReLaunch = forcetype == 1 || pageArr.includes(split_url)
					let openType = isReLaunch ? `reLaunch` : `navigateBack`
					this.$util.goUrl({
						url: isReLaunch ? url : 1,
						openType
					})
					this.$util.hideAll()
					return
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				uni.setNavigationBarTitle({
					title: '绑定手机号'
				})
				this.$util.hideAll()
				this.isLoad = true
			},
			//表单验证
			validate(param, subType) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					if (name == 'short_code' && subType == 1) return
					validate.add(param[name], item)
				})
				let message = validate.start();
				return message;
			},
			async submit(subType = 0) {
				let {
					authTime
				} = this
				if (authTime && subType == 1) return
				let param = this.$util.deepCopy(this.subForm)
				let msg = this.validate(param, subType);
				if (msg) {
					this.$util.showToast({
						title: msg
					})
					return
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					let methodModel = subType === 1 ? 'sendShortMsg' : 'bindUserPhone'
					if (subType == 1) {
						delete param.short_code
					}
					await this.$api.user[methodModel](param)
					this.$util.showToast({
						title: subType == 1 ? '发送成功' : '绑定成功'
					})
					this.$util.hideAll()
					this.lockTap = false
					if (subType == 1) {
						this.subForm.short_code = ''
						let time = 60
						this.timer = setInterval(() => {
							if (time === 0) {
								clearInterval(this.timer)
								return
							}
							time--
							this.authTime = time
						}, 1000)
						return
					}
					await this.getUserInfo()
					this.timer && clearInterval(this.timer)
					setTimeout(() => {
						let {
							loginPage,
							pageArr
						} = this
						let url = loginPage || `/pages/service`
						// console.log(this.loginPage, loginPage, "====loginPage phone-page")
						let split_url = url.split('?')[0]
						let isReLaunch = pageArr.includes(split_url)
						let openType = isReLaunch ? `reLaunch` : `navigateBack`
						this.$util.goUrl({
							url: isReLaunch ? url : 1,
							openType
						})
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}

			}
		}
	}
</script>


<style lang="scss">
	page {
		background: #fff;
	}

	.mine-pages-phone {
		.title {
			font-size: 54rpx;
			margin: 80rpx 0;

		}

		.confirm-btn {
			width: 630rpx;
			height: 110rpx;
			background: #F7F7F7;
			margin-top: 40rpx;

			.item-input {
				height: 110rpx;
			}
		}

		.confirm-btn.sm {
			width: 460rpx;
		}

		.send-btn {
			width: 150rpx;
			height: 110rpx;
			margin-top: 40rpx;
		}
	}
</style>