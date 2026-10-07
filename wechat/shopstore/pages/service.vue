<template>
	<view class="shopstore-service">

		<view @tap.stop="goDetail(index)" class="list-item flex-center mt-md ml-md mr-md pd-lg fill-base radius-16"
			v-for="(item, index) in list.data" :key="index">
			<!-- #ifdef H5 -->
			<view class="cover radius-16">
				<view class="h5-image cover radius-16" :style="{ backgroundImage : `url('${item.cover}')`}">
				</view>
			</view>
			<!-- #endif -->
			<!-- #ifndef H5 -->
			<image mode="aspectFill" lazy-load class="cover radius-16" :src="item.cover"></image>
			<!-- #endif -->

			<view class="flex-1 ml-md" style="max-width: 450rpx;">
				<view class="flex-between">
					<view class="f-title c-title text-bold max-270 ellipsis">{{ item.title }}</view>
					<view class="f-icontext c-caption">{{ item.total_sale }}人选择</view>
				</view>
				<view class="f-caption c-caption mt-sm mb-sm ellipsis" style="height: 36rpx;">{{ item.sub_title || '' }}
				</view>
				<view class="flex-y-center">
					<view class="flex-y-baseline f-little c-warning mr-sm">¥<view class="f-sm-title text-bold">
							{{ item.price }}
						</view>
					</view>
					<view class="text-delete" v-if="item.init_price">¥{{ item.init_price }}</view>
				</view>
				<view class="flex-between">
					<view class="f-caption c-caption">
						<view class="flex-center"><i class="iconfont iconshijian mr-sm"
								style="font-size: 26rpx;margin-left:-4rpx"
								:style="{color:primaryColor}"></i>{{ item.time_long }}分钟</view>
					</view>
					<view @tap.stop="toChoose(index)" class="item-btn flex-center f-caption c-base radius"
						:style="{ background: primaryColor }">
						选择{{$t('action.attendantName')}}
					</view>
				</view>
			</view>
		</view>

		<load-more :noMore="list.current_page >= list.last_page && list.data.length > 0" :loading="loading"
			v-if="loading">
		</load-more>
		<abnor v-if="!loading && list.data.length <= 0 && list.current_page == 1"></abnor>

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
				options: {},
				loading: true,
				param: {
					page: 1,
				},
				list: {
					data: []
				},
				count: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
		}),
		onLoad(options) {
			this.options = options
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.$util.showLoading()
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
		methods: {
			...mapMutations([]),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				this.getList()
			},
			async initRefresh() {
				this.param.page = 1
				await this.initIndex(true)
			},
			async getList() {
				let {
					list: oldList,
					param,
				} = this
				let {
					id
				} = this.options
				param.id = id
				let newList = await this.$api.shopstore.storeServiceList(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			// 详情
			goDetail(index) {
				let {
					id
				} = this.list.data[index]
				let url = `/user/pages/detail?id=${id}`
				this.$util.goUrl({
					url
				})
			},
			// 选择向导
			toChoose(index) {
				let {
					id
				} = this.list.data[index]
				let {
					id: sid
				} = this.options
				let url = `/user/pages/choose-technician?id=${id}&store_id=${sid}`
				this.$util.goUrl({
					url
				})
			},
		}
	}
</script>

<style lang="scss">
	.shopstore-service {
		.list-item {
			.cover {
				width: 180rpx;
				height: 180rpx;
			}


			.text-delete {
				font-size: 20rpx;
				color: #B9B9B9;
			}

			.item-btn {
				width: 140rpx;
				height: 52rpx;
			}
		}
	}
</style>
