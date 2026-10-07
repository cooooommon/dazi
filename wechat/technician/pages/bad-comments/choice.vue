<template>
	<view class="order" v-if="isLoad">
		<fixed>
			<view class="fare-top rel">
				<view class="abs fare-top-bgcolor" :style="{backgroundColor:primaryColor}"></view>
				<view class="pl-lg pr-lg fill-base flex flex-y-center fare-search box-shadow">
					<search class="flex-1" @input="toSearch" type="input" :confirmSearch="false" :padding="0"
						:radius="30" placeholder="输入服务名称或订单号"></search>
				</view>
			</view>
		</fixed>
		<view class="order-list pd-md">
			<block v-for="(item,index) in list.data" :key="index">
				<view class="order-item fill-base pl-lg pr-lg pb-lg radius-16 mb-md" @tap="setOrderState(index)">
					<view class="order-item-title b-1px-b flex-y-center">
						<view v-if="current == index" class="icon-radio-fill iconfont" style="font-size: 20px;"
							:style="{color:primaryColor}"></view>
						<view v-else class="icon-xuanze iconfont" style="font-size: 20px;color: #BEC3CE;"></view>

						<view class="f-paragraph c-paragraph pl-md">订单号 {{item.order_code}}</view>
					</view>
					<view class="flex-y-center pt-md" v-for="(citem,cindex) in item.order_goods" :key="cindex">
						<image mode="aspectFill" class="item-image radius-16" :src="citem.goods_cover"></image>
						<view class="pl-md">
							<view class="f-mini-title text-bold">{{citem.goods_name}}</view>
							<view class="c-caption f-caption pt-md pb-md">
								<text class="pr-md">预约时间</text>
								<text>{{item.start_time}}</text>
							</view>
							<view class="flex-y-center c-warning" style="line-height: 1;">
								<text class="text-bold f-icontext">¥</text>
								<text class="f-sm-title text-bold">{{citem.true_price}}</text>
							</view>
						</view>
					</view>
				</view>
			</block>
		</view>
		
		<view class="space-max-footer"></view>

		<fix-bottom-button @cancel="$util.goUrl({url:1,openType:`navigateBack`})" @confirm="confirm"
			:text="[{text:'关闭',type:'cancel'},{text:'确定',type:'confirm'}]" bgColor="#fff" :classType="2">
		</fix-bottom-button>
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
				loading: true,
				isLoad: false,
				current: -1,
				param: {
					page: 1,
					limit: 10
				},
				list: {
					data: []
				},
				id: 0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.id = options.id
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
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				this.$util.showLoading()
				let {
					id = 0,
						list: oldList,
						param
				} = this
				let newList = await this.$api.technician.appealOrder(param)
				newList.data.map((item, index) => {
					if (item.id == id) {
						this.current = index
					}
				})
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
			toSearch(val) {
				play = setTimeout(() => {
					this.param.name = val
					this.current = -1
					this.initRefresh()
				}, 1000)
			},
			setOrderState(index) {
				this.current = index
			},
			confirm() {
				let {
					current = -1
				} = this
				if (current == -1) {
					this.$util.showToast({
						title: `请选择订单`
					})
					return
				}
				this.$util.getPage(-1).orderInfo = this.list.data[current]
				this.$util.goUrl({
					url: 1,
					openType: `navigateBack`
				})
			},
		},
	}
</script>

<style lang="scss">
	.order {
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

		.order-list {
			.order-item-title {
				line-height: 95rpx;
				height: 95rpx;
			}

			.item-image {
				width: 155rpx;
				height: 155rpx;
				overflow: hidden;
				vertical-align: bottom;
			}
		}

		.box-footer {
			position: fixed;
			bottom: 0;
			width: 100%;
			left: 0;
			padding-left: 40rpx;
			padding-right: 40rpx;

			.btn {
				height: 90rpx;
				line-height: 90rpx;
				border-radius: 90rpx;
			}

			.btn1 {
				border: 1px solid #ccc;
			}
		}
	}
</style>
