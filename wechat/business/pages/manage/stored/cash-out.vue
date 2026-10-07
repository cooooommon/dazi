<template>
	<view class="technician-income-index" v-if="isLoad">
		<view @tap.stop="$util.goUrl({url:`/user/pages/cash-out?type=store`})" class="mine-menu-list c-base"
			:style="{background:primaryColor}">
			<view class="space-lg"></view>
			<view class="space-lg"></view>
			<view class="flex-center f-caption mt-sm">可提现金额(元)</view>
			<view class="money-info flex-center flex-y-baseline mt-sm mb-sm">¥<view class="money">
					{{detail.cash}}
				</view>
			</view>
			<view class="flex-center pt-md pb-md f-caption c-base-rgba">每月提现不限次数</view>
			<view class="cash-out-btn flex-center fill-base f-paragraph radius" :style="{color: primaryColor}">提现
			</view>
			<view class="space-lg"></view>
			<view class="space-lg"></view>
		</view>
		<view class="money-count fill-base flex-center pt-md pb-md">
			<view class="item-child flex-center flex-column">
				<view class="flex-y-baseline f-md-title mb-sm">{{detail.extract_total_price}}</view>
				<view class="f-caption c-caption flex-center">总收入(元)<i class="iconfont iconchangjianwenti c-caption" @tap="changeTips('总收入','已完成订单佣金累计总和')"></i></view>
			</view>
			<view class="item-child flex-center flex-column b-1px-l">
				<view class="flex-y-baseline f-md-title mb-sm">{{detail.extract_give_price}}</view>
				<view class="f-caption c-caption flex-center">累计抽成(元)<i class="iconfont iconchangjianwenti c-caption" @tap="changeTips('累计抽成','门店给到平台或者推广者的提成')"></i></view>
			</view>
			<view class="item-child flex-center flex-column b-1px-l">
				<view class="flex-y-baseline f-md-title mb-sm">{{detail.unrecorded_cash}}</view>
				<view class="f-caption c-caption">未入账(元)</view>
			</view>
		</view>
		<view @tap.stop="$util.goUrl({url:`/user/pages/distribution/record?type=6`})"
			class="flex-between mt-md pt-lg pb-lg pl-lg pr-md fill-base">
			<view class="f-title c-title">提现记录</view>
			<view class="flex-y-center f-paragraph c-title">
				<view class="c-warning" v-if="detail.extract_wallet_price*1>0">提现中 {{detail.extract_wallet_price}}元</view>
				<i class="iconfont icon-right ml-sm"></i>
			</view>
		</view>
		<view class="space-footer"></view>
		
		<uni-popup ref="show_rule_item" type="center" :maskClick="false">
			<view class="common-popup-content fill-base pd-lg radius-34">
				<view class="title">{{popupObj.title}}</view>
				<view class="f-desc c-title mt-lg">
					{{popupObj.popupContent}}
				</view>
				<view class="button">
					<view @tap.stop="$refs.show_rule_item.close()" class="item-child c-base"
						:style="{background: primaryColor,color:'#fff'}">知道了</view>
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	import tabbar from "@/components/tabbar.vue"
	export default {
		components: {
			tabbar
		},
		data() {
			return {
				detail: {},
				isLoad: false,
				popupObj: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.initIndex()
		},
		onUnload() {
			this.$util.back()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
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
				this.$util.showLoading()
				this.detail = await this.$api.business.storeCashData()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
				this.$util.hideAll()
			},
			initRefresh() {
				this.initIndex(true)
			},
			changeTips(title,tips){
				this.popupObj = {
					title,
					popupContent: tips
				}
				this.$refs.show_rule_item.open()
			}
		}
	}
</script>


<style lang="scss">
	.technician-income-index {

		.mine-menu-list {
			.money-info {
				font-size: 50rpx;

				.money {
					font-size: 70rpx;
				}
			}

			.cash-out-btn {
				width: 169rpx;
				height: 56rpx;
				margin: 0 auto;
			}

			.menu-title {
				height: 90rpx;

				.iconfont {
					font-size: 24rpx;
				}
			}

			.item-child {
				width: 25%;
				margin: 10rpx 0;

				.iconfont {
					font-size: 46rpx;
				}
			}


		}


		.money-count {
			.item-child {
				width: 50%;
			}
		}

	}
</style>
