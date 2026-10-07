<template>
	<view class="user-order-sign" v-if="isLoad">
		<v-sign :width="winWidth + 'px'" :height="winWidth*2" :bgColor="subColor" @init="onSignInit">
			<view class="control-area">
				<v-sign-pen :active-color="primaryColor"></v-sign-pen>
				<view class="space-sm"></view>
				<v-sign-color></v-sign-color>
			</view>
		</v-sign>


		<view class="space-max-footer"></view>

		<fix-bottom-button @cancel="signCtx.clear()" @confirm="confirmSign"
			:text="[{text:'清空签名',type:'cancel'},{text:'确定完成',type:'confirm'}]" bgColor="#fff" :classType="2">
		</fix-bottom-button>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				isLoad: false,
				winWidth: 0
			};
		},
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
		}),
		methods: {
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif 
				const {
					windowWidth
				} = uni.getSystemInfoSync()
				this.winWidth = windowWidth
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
			},
			onSignInit(signCtx) {
				this.signCtx = signCtx;
			},
			save(res) {
			},
			async confirmSign(item) {
				let line = this.signCtx.getLineData()
				if (line == 0) {
					this.$util.showToast({
						title: `请先进行签名`
					})
					return

				}
				const filePath = await this.signCtx.canvasToTempFilePath()
				this.$util.showLoading()
				let {
					attachment_path: sign_img
				} = await this.$api.base.uploadFile({
					filePath,
					formData: {
						type: 'picture'
					}
				})
				let {
					id
				} = this.options
				await this.$api.order.userSignOrder({
					id,
					sign_img
				})
				this.$util.hideAll()
				this.$util.showToast({
					title: `签名成功`
				})
				setTimeout(() => {
					this.$util.back()
					this.$util.goUrl({
						url: 1,
						openType: `navigateBack`
					})
				}, 1000)

			}
		},
	};
</script>

<style lang="scss" scoped>
	.user-order-sign {
		width: 100vw;
		height: 100vh;

		.control-area {
			padding: 10rpx;
		}
	}
</style>
