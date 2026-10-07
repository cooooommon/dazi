<template>
	<view class="fare" v-if="isLoad">
		<fixed>
			<view class="fare-top rel">
				<view class="abs fare-top-bgcolor" :style="{backgroundColor:primaryColor}"></view>
				<view class="pl-md fill-base flex flex-y-center fare-search box-shadow">
					<search class="flex-1" @input="toSearch" type="input" :confirmSearch="false" :padding="0"
						:radius="30" placeholder="输入订单号查询"></search>
					<view class="rel fare-top-time flex-center ml-md mr-lg" @tap="showDate = true">
						<view class="fare-top-bg abs" :style="{backgroundColor:primaryColor}"></view>
						<i class="iconfont iconriqi" :style="{color:primaryColor,fontSize:'18px'}"></i>
					</view>
				</view>
			</view>
		</fixed>
		<view class="fare-list pl-md pr-md pt-md">
			<view class="fill-base radius-16 pl-lg pr-lg mb-md" v-for="(item , index) in list.data" :key="index">
				<view class="item-top flex-between b-1px-b">
					<view class="f-paragraph c-paragraph max-450 ellipsis">订单号 {{item.order_code}}</view>
					<view class="f-paragraph max-200 ellipsis" :style="{color:primaryColor}">
						{{ item.trip_end_address | addressMatch}}
					</view>
				</view>
				<view class="item-address flex">
					<view class="item-address-label flex-center c-base" style="background-color: #44A860;">起</view>
					<view class="c-title f-paragraph flex-1 pl-sm">{{item.trip_start_address}}</view>
				</view>
				<view class="pt-md flex">
					<view class="item-address-label flex-center c-base" style="background-color: #FF8213;">终</view>
					<view class="c-title f-paragraph flex-1 pl-sm">{{item.trip_end_address}}</view>
				</view>
				<view class="pt-lg f-desc c-caption">出发时间 {{item.serout_time}}</view>
				<view class="pt-sm f-desc c-caption">到达时间 {{item.arrive_time}}</view>
				<view class="pt-md pb-md"></view>
				<view class="pt-lg pb-lg b-1px-t ">
					<view class="f-paragraph flex-y-center">
						<text>金额</text>
						<text class="c-caption pl-sm pr-md">(往返车费):</text>
						<text class="c-warning">{{item.car_price}}元</text>
					</view>
				</view>
			</view>
		</view>
		<w-picker mode="range" :startYear="startYear*1-10" :endYear="startYear" :value="toDay" :current="false"
			fields="day" @confirm="onConfirm($event)" :disabled-after="false" ref="day" :themeColor="primaryColor"
			:visible.sync="showDate">
		</w-picker>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-footer"></view>
	</view>
</template>

<script>
	let play = null
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				startYear: '',
				showDate: false,
				toDay: '',
				loading: true,
				isLoad: false,
				list: {
					data: []
				},
				param: {
					start_time: '',
					end_time: '',
					name: '',
					page: 1
				}
			}
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
			this.toDay = this.$util.formatTime(new Date(), 'YY-M-D')
			this.startYear = this.$util.formatTime(new Date(), 'YY')
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
			this.getList()
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
				this.getList();
			},
			toSearch(val) {
				clearTimeout(play)
				play = setTimeout(() => {
					this.param.name = val
					this.initRefresh()
				}, 1000)
			},
			onConfirm(e) {
				let start_time = e.value[0]
				let end_time = e.value[1]
				this.param.start_time = this.$util.DateToUnix(start_time)
				this.param.end_time = this.$util.DateToUnix(end_time) + 24 * 3600 - 1
				this.initRefresh()
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				this.$util.showLoading()
				let {
					list: oldList,
					param
				} = this
				let newList = await this.$api.technician.carMoneyList(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
			},
		},
		filters: {
			addressMatch(val) {
				if (!val) return ''
				var reg = /.+?(省|市|自治区|自治州|县|区)/g;
				let address = Array.from(val.match(reg))[1]
				return address
			}
		}
	}
</script>

<style lang="scss">
	.fare {
		.fare-top {
			padding-top: 10rpx;

			.fare-top-bgcolor {
				height: 60rpx;
				top: 0;
				left: 0;
				width: 100%;
				z-index: -1;
			}
		}

		.fare-search {
			height: 130rpx;
			border-radius: 40rpx;
			overflow: hidden;
			margin-bottom: -2rpx;

			.fare-top-time {
				width: 70rpx;
				height: 70rpx;

				.fare-top-bg {
					width: 70rpx;
					height: 70rpx;
					border-radius: 70rpx;
					opacity: 0.1;
				}
			}
		}

		.fare-list {
			.item-top {
				line-height: 88rpx;
				height: 88rpx;
			}

			.item-address {
				padding-top: 40rpx;
			}

			.item-address-label {
				width: 30rpx;
				height: 30rpx;
				border-radius: 30rpx;
				font-size: 18rpx;
				margin-top: 6rpx;
			}
		}

	}
</style>
