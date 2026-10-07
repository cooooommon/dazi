<template>
	<view class="package-pages" v-if="isLoad">
		<view class="flex-warp package">
			<block v-for="(item,index) in list.data" :key="index">
				<view class="package-item ml-sm mr-sm mb-md fill-base radius-16" @tap="$util.goUrl({url: `/business/pages/package/detail?id=${item.id}`})">
					<view class="rel">
						<image :src="item.cover" mode="aspectFill" class="package-item-img"></image>
						<view class="package-item-label f-icontext c-base abs"
						v-if="item.is_integral">{{item.integral}}积分抵¥{{item.integral_to_money}}</view>
					</view>
					<view class="pl-md pr-md">
						<view class="pt-md pb-sm f-desc text-bold">{{item.name}}</view>
						<view class="f-icontext c-caption">{{item.sub_name}}</view>
						<view class="flex-between" style="padding-top: 15rpx;">
							<text class="f-paragraph text-bold" style="color: #F1270C;">¥{{item.price}}</text>
							<text class="f-ms-little c-caption">年售 {{item.sale | handerSale}}</text>
						</view>
						<view class="pt-md flex-between">
							<view class="flex-y-center">
								<view class="package-discount f-ms-little mr-sm" v-if="item.discount">{{item.discount}}折
								</view>
								<view class="c-caption f-ms-little" style="text-decoration-line:line-through">
									￥{{item.init_price}}</view>
							</view>
							<view class="f-desc c-base flex-center package-btn" :style="{background: primaryColor}">抢购
							</view>
						</view>
					</view>
				</view>
			</block>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-footer"></view>
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
				isLoad: false,
				param: {
					page: 1,
					limit: 10,
					store_id: ''
				},
				list: {
					data: []
				}
			}
		},
		filters: {
			handerSale(val){
				if(val < 10){
					return val
				} 
				if(val < 10000){
					return (val / 10).toFixed(0) * 10 + '+'
				}
				return val > 10000 ? (val / 10000).toFixed(1) + 'w+' : val
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo
		}),
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.$util.showLoading()
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.$util.showLoading()
			this.param.page += 1
			await this.getList()
		},
		onLoad(options) {
			this.param.store_id = options.id
			this.$util.showLoading()
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
				this.getList()
			},
			initRefresh() {
				this.$util.showLoading()
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				let {
					list: oldList,
					param
				} = this
				let newList = await this.$api.business.storePackList(param)
				if (param.page == 1) {
					this.list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList;
				}
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
			}
		}
	}
</script>

<style lang="scss">
	.package-pages {
		.package {
			padding: 0 15rpx;
			padding-top: 30rpx;
			.package-item {
				width: calc(50% - 20rpx);
				overflow: hidden;
				padding-bottom: 26rpx;

				.package-item-img {
					height: 234rpx;
					width: 100%;
				}
				
				.package-item-label{
					height: 40rpx;
					border-radius: 16rpx 0 16rpx 0;
					left: 0;
					top: 0;
					background: linear-gradient( 90deg, #FF4C88 0%, #FF7B7B 100%);
					padding: 0 10rpx;
					line-height: 40rpx;
				}

				.package-btn {
					width: 94rpx;
					height: 54rpx;
					border-radius: 54rpx;
				}

				.package-discount {
					padding: 0px 8rpx;
					border-radius: 4rpx;
					color: #F1270C;
					border: 1px solid #F1270C;
				}
			}
		}
	}
</style>
