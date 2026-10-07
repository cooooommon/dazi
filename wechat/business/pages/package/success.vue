<template>
	<view class="success-pages" v-if="isLoad">
		<view class="flex-center icon-box">
			<i class="iconfont icon-xuanze-fill" :style="{color: primaryColor,fontSize: '233rpx'}"></i>
		</view>
		<view class="flex-center f-paragraph">您已支付成功</view>
		<view class="flex-center success-btn-box">
			<view class="success-btn flex-center f-mini-title" :style="{border: `1px solid ${primaryColor}`,color: primaryColor}"
			@tap="$util.goUrl({url: `/business/pages/package/order/list`})">查看订单详情</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import siteInfo from '@/siteinfo.js';
	export default {
		data() {
			return {
				options: {},
				loading: true,
				isLoad: true
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			mineInfo: state => state.user.mineInfo,
		}),
		onLoad(options) {
			//this.$util.showLoading()
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				
			},
			// 选择出行方式/支付方式/服务方式
			async toChangeItem(index, key = 1) {
				let {
					balanceInd
				} = this
				if (index == balanceInd && this.payList[balanceInd].is_disabled) return
				this.payInd = index
			},
			
		}
	}
</script>

<style lang="scss">
	.success-pages {
		.icon-box{
			padding-top: 127rpx;
		}
		.success-btn-box{
			padding-top: 90rpx;
			.success-btn{
				width: 285rpx;
				height: 92rpx;
				border-radius: 92rpx;
				
			}
		}
	}
</style>
