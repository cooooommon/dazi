<template>
	<view>
		<view class="header flex-center pt-md">
			<image src="https://lbqny.migugu.com/admin/shop/succ.png" mode="aspectFill"></image>
		</view>
		<view class="as-box-cont text-bold text-center">提交成功</view>
		<view class="f-mini-title c-caption text-center pl-lg pr-lg pt-sm">您已经成功提交申请，审核进度和结果可在反馈记录里查看。</view>
		<view class="as-btn c-base f-mini-title text-center ml-lg mr-lg" :style="{backgroundColor:primaryColor}"
		@tap="$util.goUrl({url:`/user/pages/feedback/list`})">查看反馈记录</view>
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
			return {}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.initIndex()
		},
		methods:{
			async initIndex(refresh = false){
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
			}
		}
	}
</script>

<style>
	.header image{
		width: 260rpx;
		height: 260rpx;
	}
	.as-btn{
		margin-top: 98rpx;
		height: 84rpx;
		line-height: 84rpx;
		border-radius: 84rpx;
	}
	.as-box-cont{
		padding-top: 40rpx;
		font-size: 46rpx;
	}
</style>
