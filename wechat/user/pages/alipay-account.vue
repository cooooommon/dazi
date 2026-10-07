<template>
	<view class="apply-pages" v-if="isLoad">
		<view class="apply-form">
			<view class="fill-base pd-lg">
				<view class="flex-between b-1px-b">
					<view class="item-text">支付宝账号</view>
					<input v-model="form.alipay_number" type="text" class="item-input flex-1"
						:class="[{'mr-md':form.alipay_number}]" maxlength="20" :placeholder="rule[0].errorMsg" />
					<i @tap.stop="toRest('alipay_number')" class="iconfont icon-jia-bold rotate-45"
						v-if="form.alipay_number"></i>
				</view>
				<view class="flex-between">
					<view class="item-text">支付宝账号姓名</view>
					<input v-model="form.alipay_name" type="text" class="item-input flex-1"
						:class="[{'mr-md':form.alipay_name}]" maxlength="20" :placeholder="rule[1].errorMsg" />
					<i @tap.stop="toRest('alipay_name')" class="iconfont icon-jia-bold rotate-45"
						v-if="form.alipay_name"></i>
				</view>
				<view class="flex-center mt-lg pt-md pb-lg">
					<view @tap.stop="submit" class="bind-btn flex-center f-title c-base radius"
						:style="{background:primaryColor}">立即绑定</view>
				</view>
			</view>
		</view>
		<view class="pd-lg">
			<view class="f-title text-bold mb-md">支付宝账号获取：</view>
			<view class="f-paragraph c-desc mt-md mb-md">1)、打开【支付宝】-【我的】页面</view>
			<image mode="widthFix" class="item-image radius-16"
				src="https://lbqny.migugu.com/admin/anmo/mine/alipay-0.jpeg">
			</image>
			<view class="f-paragraph c-desc mt-md mb-md">2)、打开上图箭头所示，个人信息页面</view>
			<image mode="widthFix" class="item-image radius-16"
				src="https://lbqny.migugu.com/admin/anmo/mine/alipay-1.jpeg">
			</image>
			<view class="fill-base pd-lg mt-lg radius-26">
				<view class="f-title text-bold mb-md">身份信息</view>
				<view class="f-paragraph c-title mb-md">根据相关法律法规要求，请保持身份信息完整，以便使用支付宝相关功能</view>
				<image mode="widthFix" class="radius-16" src="https://lbqny.migugu.com/admin/anmo/mine/alipay-2.jpeg">
				</image>
			</view>
		</view>

		<view class="space-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		data() {
			return {
				isLoad: false,
				options: {},
				form: {
					alipay_number: '',
					alipay_name: ''
				},
				rule: [{
					name: "alipay_number",
					checkType: "isNotNull",
					errorMsg: "请输入您的支付宝账号",
					regType: 2
				}, {
					name: "alipay_name",
					checkType: "isNotNull",
					errorMsg: "请输入您的支付宝账号姓名",
					regType: 2
				}],
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
		}),
		async onLoad(options) {
			this.$util.showLoading()
			this.options = options
			await this.initIndex()
			this.isLoad = true
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				let {
					alipay_number = '',
						alipay_name = ''
				} = this.userInfo
				this.form.alipay_number = alipay_number
				this.form.alipay_name = alipay_name
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.$util.hideAll()
			},
			initRefresh() {
				this.initIndex(true)
			},
			toRest(key) {
				this.$nextTick(() => {
					this.form[key] = ''
				})
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
				let param = this.$util.deepCopy(this.form)
				let msg = this.validate(param);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.mine.bindAlipayNumber(param)
					this.$util.hideAll()
					this.$util.showToast({
						title: `绑定成功`
					});
					setTimeout(() => {
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: `navigateBack`
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
	.apply-pages {
		.apply-form {
			.item-text {
				width: 250rpx;
			}
		}

		.bind-btn {
			width: 600rpx;
			height: 80rpx;
		}

		.item-image {
			width: 690rpx
		}
	}
</style>
