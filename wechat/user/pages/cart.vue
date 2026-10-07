<template>
	<view class="pages-technician">
		<view class="list-item flex-center pd-lg fill-base radius-16" :class="[{'b-1px-t':index != 0}]"
			v-for="(item,index) in carList.list" :key="index">
			<image mode="aspectFill" class="avatar lg radius-16" :src="item.cover"></image>
			<view class="flex-1 ml-md">
				<view class="f-title c-title max-510 ellipsis">{{item.title}}</view>
				<view class="f-caption c-caption mt-sm mb-sm ellipsis">{{item.total_sale}}人选择</view>
				<view class="flex-between">
					<view class="flex-y-center f-desc c-caption max-350 ellipsis">
						<view class="f-title c-warning mr-sm">¥{{item.price}}</view>/ {{item.time_long}}分钟
					</view>
					<view class="flex-warp">
						<block v-if="item.num">
							<button @tap.stop="changeNum(-1,index)" class="reduce"
								:style="{borderColor:primaryColor,color:primaryColor}"><i
									class="iconfont icon-jian-bold"></i></button>
							<button class="addreduce clear-btn">{{item.num || 0}}</button>
						</block>
						<button @tap.stop="changeNum(1,index)" class="add"
							:style="{background:primaryColor,borderColor:primaryColor}"><i
								class="iconfont icon-jia-bold"></i></button>
					</view>
				</view>
			</view>
		</view>


		<abnor v-if="!loading&&carList.list.length<=0"></abnor>

		<view class="space-max-footer"></view>

		<view class="fix fill-base pd-lg b-1px-t" style="bottom: 0;">
			<view class="flex-between">
				<view class="flex-center">合计：<view class="f-title c-warning text-bold ml-sm">¥{{carList.car_price}}
					</view>
				</view>
				<view @tap.stop="$util.goUrl({url:`/user/pages/order?id=${options.id}`})"
					class="order-btn flex-center f-desc c-base radius" :style="{background: primaryColor}">提交订单
				</view>
			</view>
			<view class="space-safe"></view>
		</view>

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
				param: {},
				loading: true,
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
			carList: state => state.order.carList,
		}),
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods: {
			...mapActions(['getCarList']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.initIndex(true)
			},
			async getList() {
				let {
					list: oldList,
					param,
					options
				} = this
				param.coach_id = options.id
				this.getCarList(param)
				this.loading = false
				this.$util.hideAll()
			},
			// 加/减数量
			async changeNum(mol, index) {
				let {
					id,
					service_id
				} = this.carList.list[index]
				let {
					id: coach_id
				} = this.options
				if (this.lockTap) return;
				this.lockTap = true;
				let methodModel = mol > 0 ? 'addCar' : 'delCar'
				let param = mol > 0 ? {
					service_id,
					coach_id,
					num: 1
				} : {
					id,
					num: 1
				}
				try {
					let changeNum = await this.$api.order[methodModel](param)
					this.lockTap = false
					await this.getCarList({
						coach_id
					})
				} catch (e) {
					this.lockTap = false
				}
			}
		}
	}
</script>


<style lang="scss">
	.pages-technician {

		.item-avatar {
			width: 88rpx;
			height: 88rpx;
			background: #f4f6f8;
		}

		.icon-close {
			font-size: 50rpx;
		}

		.list-content {
			max-height: 60vh;
			overflow-y: auto;

			.list-message {
				.item-avatar {
					width: 52rpx;
					height: 52rpx;
					background: #f4f6f8;
				}

				.iconfont {
					font-size: 28rpx;
					margin-right: 5rpx;
				}
			}
		}

		.order-btn {
			width: 200rpx;
			height: 72rpx;
		}
	}
</style>
