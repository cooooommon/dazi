<template>
	<view class="detail-pages" v-if="isLoad">
		<!-- #ifndef H5 -->
		<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true" title="积分明细" color="#000"
			:zIndex="99" leftIcon="icon-left" backgroundColor="transparent">
		</uni-nav-bar>
		<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
		<!-- #endif -->
		<view class="detail-bg abs"></view>
		<view class="rel pl-md pr-md" style="padding-top: 40rpx;">
			<view class="detail-box radius-16 flex-y-center rel">
				<view class="">
					<view class="f-caption" style="color: #F5EFFF;">我的积分</view>
					<view class="text-bold c-base" style="font-size: 80rpx;">{{Number((mineInfo.integral*1 + mineInfo.wait_integral*1).toFixed(2)) || 0}}</view>
				</view>
				<image class="integra-bg abs" src="https://lbqny.migugu.com/admin/peiwan/integra-detail.png" mode="aspectFill"></image>
			</view>
			<view class="pt-lg f-mini-title pb-md pl-md mt-sm">积分明细</view>
			<view class="pl-lg pr-lg radius-32 fill-base">
				<view class="detail-item flex-y-center" v-for="(item,index) in list.data" :key="index" :class="[{'b-1px-t': index > 0}]">
					<view class="flex-1">
						<view class="flex-between">
							<view class="f-paragraph text-bold">{{integralTypeText[item.type]}}</view>
							<text class="f-paragraph text-bold" :style="{color: item.add ? `#44A860` : `#F1381F`}" >{{(item.add ? '+' : '-') + item.change}}</text>
						</view>
						<view class="flex-between">
							<view class="f-desc c-paragraph pt-sm">{{$util.formatTime(item.create_time*1000, 'YY.M.D h:m:s')}}</view>
							<text class="f-desc" v-if="item.add && [1,2].includes(item.type)" :style="{color: statusText[item.status].color}">{{statusText[item.status].title}}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading && list.data.length <= 0 && list.current_page == 1"></abnor>
		<view class="space-footer"></view>
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
				loading: true,
				param: {
					page: 1
				},
				list: {
					data: []
				},
				integralTypeText: { 1: '预约订单下单', 2: '邀约订单下单', 3: '套餐订单抵扣', 4: '套餐订单退款' },
				statusText: {
					1: {title: '冻结中', color: '#FF2404'},
					2: {title: '已到账', color: '#1BCA62'}
				}
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
			mineInfo: state => state.user.mineInfo,
		}),
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.getList(this.param.page + 1);
		},
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
				await this.getList(1)
				this.isLoad = true
			},
			initRefresh() {
				this.initIndex(true)
			},
			async getList(page = 0) {
				if (page) {
					this.param.page = page
				}
				let {
					list: oldList,
					param,
				} = this
				let newList = await this.$api.user.integralList(param);
			
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
		},
	};
</script>

<style lang="scss" scoped>
	.detail-pages{
		.detail-bg{
			width: 100%;
			left: 0;
			//#ifdef H5
			top: -88px;
			//#endif
			//#ifndef H5
			top: 0px;
			//#endif
			height: 611rpx;
			background: linear-gradient( 178deg, #E7E1F8 0%, rgba(243,243,250,0) 100%);
		}
		.detail-box{
			height: 239rpx;
			background: linear-gradient(to right, #9B65FF,#945AFF);
			padding-left: 46rpx;
			.integra-bg{
				width: 240rpx;
				height: 192rpx;
				right: 24rpx;
			}
		}
		.detail-item{
			height: 145rpx;
		}
	}
</style>
