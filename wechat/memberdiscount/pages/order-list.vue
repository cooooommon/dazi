<template>
	<view class="memberdiscount-order-list" style="padding-top: 1rpx;" :style="{background:pageColor}">

		<view class="list-item mt-md ml-md mr-md pl-lg pr-lg pb-lg fill-base radius-16"
			v-for="(item,index) in list.data" :key="index">
			<view class="flex-center pt-lg pb-lg b-1px-b">
				<image class="cover" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/member.png`"></image>
				<view class="flex-1 ml-md f-mini-title c-title text-bold">{{item.title}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-lg">
				<view class="text">会员生效时间:</view>
				<view class="flex-1 c-title">{{$util.formatTime(item.start_time*1000, 'YY-M-D h:m:s')}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">会员到期时间:</view>
				<view class="flex-1 c-title">{{$util.formatTime(item.end_time*1000, 'YY-M-D h:m:s')}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">支付方式:</view>
				<view class="flex-1 c-title">{{payType[item.pay_model]}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">付款时间:</view>
				<view class="flex-1 c-title">{{$util.formatTime(item.pay_time*1000, 'YY-M-D h:m:s')}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">订单编号:</view>
				<view class="flex-1 c-title">{{item.order_code}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">付款单号:</view>
				<view class="flex-1 c-title">{{item.transaction_id}}</view>
			</view>
			<view class="flex-between mt-lg">
				<view class="f-desc" style="color: #F1381F;">¥{{item.price}}</view>
				<!-- <view class="f-paragraph flex-center order-btn radius" @tap="$util.goUrl({url: `/memberdiscount/pages/order?index=${index}`})">查看详情</view> -->
			</view>
		</view>

		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor :isCenter="true" v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>

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
		components: {
			// #ifdef H5
			MediaRecorder
			// #endif
		},
		data() {
			return {
				options: {},
				payType: {
					1: '微信支付',
					2: '余额支付',
					3: '支付宝支付',
					4: '折扣卡支付'
				},
				param: {
					page: 1
				},
				list: {
					data: []
				},
				loading: true
			}
		},
		computed: mapState({
			configInfo: state => state.config.configInfo
		}),
		async onLoad(options) {
			this.options = options
			this.initIndex()
		},
		async onPullDownRefresh() {
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
			...mapActions(['getConfigInfo', 'getCoachInfo', 'toPlayAudio']),
			...mapMutations(['updateUserItem', 'updateOrderItem']),
			async initIndex(refresh = false) {
				if (!this.configInfo.id) {
					await this.getConfigInfo()
				}
				await this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList(flag = false) {
				let {
					list: oldList,
					param,
				} = this
				let newList = await this.$api.memberdiscount.orderList(param)

				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			// 订单详情
			goDetail(index) {
				let {
					id
				} = this.list.data[index]
				let url = `/memberdiscount/pages/order/detail?id=${id}`
				this.$util.goUrl({
					url
				})
			}
		}
	}
</script>


<style lang="scss">
	.memberdiscount-order-list {
		.cover {
			width: 60rpx;
			height: 60rpx
		}

		.text {
			width: 180rpx;
		}

		.order-btn {
			width: 163rpx;
			height: 65rpx;
			color: #484848;
			border: 1rpx solid #D1D1D1;
			transform: rotateZ(360deg);
		}

		.mt-sm {
			margin-top: 15rpx;
		}
	}
</style>