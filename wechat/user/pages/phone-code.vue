<template>
	<view class="mine-pages-phone flex-center flex-column">
		<view class="title c-black">请输入验证码</view>
		<view class="flex-y-center f-caption c-caption mt-lg">验证码已发送至<view class="ml-sm" :style="{color:primaryColor}">
				{{subForm.phone}}
			</view>
		</view>
		<view class="space-lg"></view>
		<view class="space-lg"></view>
		<view class="space-md"></view>

		<view>
			<xt-verify-code v-model="subForm.short_code" @confirm="confirm"></xt-verify-code>
		</view>

		<view class="flex-center f-caption c-caption mt-lg">
			<view class="flex-center" v-if="authTime > 0">
				<view class="c-title mr-sm">{{`${authTime}s`}}</view>后
			</view>
			<view @tap="toResetCode" :style="{color:authTime > 0?'':primaryColor}">重新获取</view>
		</view>

		<view @tap="submit" class="confirm-btn flex-center f-sm-title text-bold c-base radius-16"
			:style="{background:subForm.short_code.length > 0 ? primaryColor:'#CCE9DD'}">确定
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import xtVerifyCode from "@/components/xt-verify-code"
	export default {
		components: {
			xtVerifyCode
		},
		data() {
			return {
				isLoad: false,
				options: {},
				authTime: 60,
				timer: null,
				subForm: {
					phone: '',
					short_code: ''
				},
				rule: [{
					name: "short_code",
					checkType: "isNotNull",
					errorMsg: "请输入短信验证码",
					regText: "短信验证码"
				}],
				lockTap: false,
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			loginPage: state => state.user.loginPage
		}),
		async onLoad(options) {
			this.$util.setNavigationBarColor({
				color: '#000000',
				bg: '#ffffff'
			})
			let {
				phone
			} = options
			this.subForm.phone = phone
			this.initIndex()
		},
		methods: {
			...mapActions(['getUserInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex() {
				let time = 60
				this.timer = setInterval(() => {
					if (time === 0) {
						clearInterval(this.timer)
						return
					}
					time--
					this.authTime = time
				}, 1000)
			},
			initRefresh() {
				this.initIndex()
			},
			async toResetCode() {
				let {
					authTime
				} = this
				if (authTime) return
				this.timer && clearTimeout(this.timer)
				let {
					phone
				} = this.subForm
				await this.$api.user.sendShortMsg({
					phone
				})
				this.$util.showToast({
					title: `验证码发送成功`
				})
				this.initRefresh()
			},
			confirm() {

			},
			//表单验证
			validate(param) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					validate.add(param[name], item);
				})
				let message = validate.start();
				return message;
			},
			async submit() {
				let param = this.$util.deepCopy(this.subForm)
				let msg = this.validate(param);
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
					await this.$api.user.bindUserPhone(param)
					this.$util.hideAll()
					this.lockTap = false
					this.timer && clearTimeout(this.timer)
					this.updateUserItem({
						key: 'isShowLogin',
						val: false
					})
					await this.getUserInfo()
					setTimeout(() => {
						let {
							loginPage: url
						} = this
						console.log(url, '=========> loginPage')
						let pageArr = ['/pages/service', '/pages/technician', '/pages/store', '/pages/find', '/pages/order', '/pages/mine']
						let openType = pageArr.includes(url) ? `reLaunch` : `navigateBack`
						this.$util.goUrl({
							url: pageArr.includes(url) ? url : 3,
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
			margin-top: 80rpx;
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
	}
</style>
