<template>
	<view class="master-income-record" v-if="isLoad">
		<view class="mine-menu-list c-base" :style="{background:primaryColor}">
			<view class="space-lg"></view>
			<view class="space-lg"></view>
			<view class="flex-center f-caption mt-sm mb-sm">已累计提现金额(元)</view>
			<view class="money-info flex-center flex-y-baseline">¥<view class="money">{{list.extract_total_price}}
				</view>
			</view>
			<view class="space-md"></view>
			<view class="space-lg"></view>
		</view>
		<view class="fill-base">
			<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1" :activeColor="primaryColor"
				width="25%" height="100rpx"></tab>
			<view class="ml-lg mr-lg b-1px-b"></view>

			<view class="list-item pd-lg" :class="[{'b-1px-t':index!=0}]" v-for="(item,index) in  list.data"
				:key="index">
				<view class="flex-warp">
					<view class="item-tag mt-sm mr-md radius"
						:style="{background: item.status == 1 ? primaryColor : item.status==2? subColor :'#999'}">
					</view>
					<view class="flex-1">
						<view class="flex-between">
							<view class="flex-y-center">
								<view class="f-title c-title text-bold">{{statusType[item.status]}}</view>
							</view>
							<view class="f-caption c-caption">{{item.create_time}}</view>
						</view>
						<view class="flex-between mt-sm">
							<view class="f-caption c-caption">
								<view>申请提现金额: ¥{{item.total_price}}</view>
								<view class="flex-y-center">手续费扣除: {{item.tax_point}}%<view class="ml-md">
										扣费: ¥{{item.service_price}}</view>
								</view>
							</view>
							<view class="f-md-title">{{item.status == 2?'+':'-'}}{{item.apply_price}}</view>
						</view>
					</view>
				</view>
			</view>
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
		mapMutations
	} from "vuex"
	export default {
		components: {},
		data() {
			return {
				options: {},
				isLoad: false,
				activeIndex: 0,
				tabList: [{
					title: '全部',
					id: 0
				}, {
					title: '未到账',
					id: 1,
				}, {
					title: '已到账',
					id: 2
				}, {
					title: '已拒绝',
					id: 3
				}],
				statusType: {
					1: '未到账',
					2: '已到账',
					3: '已拒绝',
				},
				param: {
					page: 1,
					status: 0
				},
				list: {
					data: []
				},
				loading: true,
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		async onLoad(options) {
			let {
				type
			} = options
			this.options = options
			let titleArr = ['', '提现记录', '服务费提现记录', '车费提现记录', '渠道商提现记录', '提现记录','提现记录', '提现记录']
			uni.setNavigationBarTitle({
				title: titleArr[type * 1]
			})
			this.$util.showLoading()
			await this.initIndex()
			this.isLoad = true
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
				await this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				let {
					list: oldList,
					param,
					activeIndex,
					tabList
				} = this
				let status = tabList[activeIndex].id
				param.status = status
				let methodArr = {
					1: {
						methodKey: 'mine',
						methodModel: 'walletList'
					},
					2: {
						methodKey: 'technician',
						methodModel: 'capCashList',
						type: 1 // 向导服务费
					},
					3: {
						methodKey: 'technician',
						methodModel: 'capCashList',
						type: 2 // 车费
					},
					4: {
						methodKey: 'channel',
						methodModel: 'walletList'
					},
					5: { // 余额提现
						methodKey: 'mine',
						methodModel: 'shareWalletList'
					},
					6: { // 门店
						methodKey: 'business',
						methodModel: 'walletList'
					},
					7: { // 经纪人提现
						methodKey: 'mine',
						methodModel: 'brokerWalletList'
					}
				}
				let {
					type: opType
				} = this.options
				let {
					methodKey,
					methodModel,
					type = 0
				} = methodArr[opType]
				if (type) {
					param.type = type
				}
				let newList = await this.$api[methodKey][methodModel](param);
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			handerTabChange(index) {
				this.activeIndex = index;
				this.$util.showLoading()
				this.param.page = 1;
				this.getList()
			},
			toCopy(index) {
				let {
					order_code: url
				} = this.list.data[index]
				this.$util.goUrl({
					url,
					openType: 'copy'
				})
			},
		}
	}
</script>


<style lang="scss">
	.master-income-record {

		.mine-menu-list {

			.money-info {
				font-size: 50rpx;

				.money {
					font-size: 70rpx;
				}
			}

		}

		.list-item {
			.item-tag {
				width: 24rpx;
				height: 24rpx;
			}
		}
	}
</style>
