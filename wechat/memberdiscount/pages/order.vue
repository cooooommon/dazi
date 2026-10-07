<template>
	<view class="member-rule" :style="{background:pageColor}" v-if="isLoad">
		<view class="list-item mt-md ml-md mr-md pl-lg pr-lg pb-lg fill-base radius-16">
			<view class="flex-center f-desc c-paragraph" style="padding-top: 25rpx;">
				<view class="text">购买套餐:</view>
				<view class="flex-1 c-title">{{detail.title}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">购买价格:</view>
				<view class="flex-1 c-title">{{detail.price}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">会员生效时间:</view>
				<view class="flex-1 c-title">{{$util.formatTime(detail.start_time*1000, 'YY-M-D h:m:s')}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">会员到期时间:</view>
				<view class="flex-1 c-title">{{$util.formatTime(detail.end_time*1000, 'YY-M-D h:m:s')}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">支付方式:</view>
				<view class="flex-1 c-title">{{payType[detail.pay_model]}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">付款时间:</view>
				<view class="flex-1 c-title">{{$util.formatTime(detail.pay_time*1000, 'YY-M-D h:m:s')}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">订单编号:</view>
				<view class="flex-1 c-title">{{detail.order_code}}</view>
			</view>
			<view class="flex-center f-desc c-paragraph mt-sm">
				<view class="text">付款单号:</view>
				<view class="flex-1 c-title">{{detail.transaction_id}}</view>
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
	import parser from "@/components/jyf-Parser/index"
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},
				isLoad: false,
				detail: {},
				payType: {
					1: '微信支付',
					2: '余额支付',
					3: '支付宝支付',
					4: '折扣卡支付'
				},
			}
		},
		computed: mapState({}),
		onLoad(options) {
			this.options = options
			this.$util.showLoading()
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				this.detail = this.$util.getPage(-1).list.data[this.options.index]
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.$util.hideAll()
				this.isLoad = true
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
				this.initIndex(true)
			},
			linkpress(res) {
				// #ifdef APP-PLUS
				this.$util.goUrl({
					url: res.href,
					openType: 'web'
				})
				// #endif
			}
		}
	}
</script>


<style lang="scss">
	.cover {
		width: 60rpx;
		height: 60rpx
	}
	
	.text {
		width: 180rpx;
	}
	
	.mt-sm {
		margin-top: 25rpx;
	}
</style>