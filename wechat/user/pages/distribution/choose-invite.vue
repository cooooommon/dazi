<template>
	<view class="choose-invite" v-if="isLoad">
		<view class="item pl-lg pr-md radius-24 fill-base flex-center ml-md mr-md mt-md" @tap="$util.goUrl({url: `/user/pages/distribution/poster`})">
			<image src="https://lbqny.migugu.com/admin/peiwan/index-03.png" mode="aspectFill" class="item-icon"></image>
			<view class="pl-md flex-1 flex-between">
				<view class="flex-1">
					<view class="f-title text-bold">邀请用户</view>
					<view class="pt-sm c-paragraph f-caption">直邀用户, 获得更多抽成</view>
				</view>
				<view class="item-btn rel flex-center" :style="{borderColor: primaryColor}">
					<view class="abs item-bg" :style="{backgroundColor: primaryColor}"></view>
					<text class="f-desc" :style="{color: primaryColor}">前往邀请</text>
				</view>
			</view>
		</view>
		<view class="item pl-lg pr-md radius-24 fill-base flex-center ml-md mr-md mt-md" v-if="configInfo.plugAuth.distributor" 
		@tap="$util.goUrl({url: `/user/pages/distribution/poster-below`})">
			<image src="https://lbqny.migugu.com/admin/peiwan/index-04.png" mode="aspectFill" class="item-icon"></image>
			<view class="pl-md flex-1 flex-between">
				<view class="flex-1">
					<view class="f-title text-bold">邀请下级</view>
					<view class="pt-sm c-paragraph f-caption">直邀用户, 获得更多抽成</view>
				</view>
				<view class="item-btn rel flex-center" :style="{borderColor: primaryColor}"
				@tap="">
					<view class="abs item-bg" :style="{backgroundColor: primaryColor}"></view>
					<text class="f-desc" :style="{color: primaryColor}">前往邀请</text>
				</view>
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
		data() {
			return{
				isLoad: false,
				type: 1
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getPlugAuth', 'getUserInfo', 'getMineInfo', 'getCoachInfo',
				'updateCommonOptions',
			]),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				//this.$util.showLoading()
				
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
				//this.$util.hideAll()
			},
		}
	}
</script>

<style lang="scss">
	.choose-invite{
		.item-icon{
			width: 116rpx;
			height: 116rpx;
			border-radius: 116rpx;
		}
		.item{
			height: 205rpx;
		}
		.item-btn{
			width: 156rpx;
			height: 72rpx;
			border-radius: 72rpx;
			border: 1px solid;
			.item-bg{
				left: 0;
				top: 0;
				width: 156rpx;
				height: 72rpx;
				border-radius: 72rpx;
				opacity: 0.1;
			}
		}
	}
</style>