<template>
	<view class="order-pages">
		<fixed>
			<view class="flex-center flex-column pd-lg c-base" :style="{background:primaryColor}">
				<view class="flex-y-baseline f-title">¥<view class="money">{{list.total_cash || 0}}</view>
				</view>
				<view class="f-paragraph">总收益</view>
			</view>
			<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1" :activeColor="primaryColor"
				:width="100/tabList.length + '%'" height="100rpx"></tab>
			<view class="b-1px-b"></view>
		</fixed>

		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16" v-for="(item,index) in list.data"
			:key="index">
			<view @tap.stop="goShop(index)" class="flex-between pb-lg">
				<view class="f-paragraph c-title max-380 ellipsis">下单人：{{item.nickName || ''}}</view>
				<view class="f-caption text-bold" :style="{color:item.status==1?primaryColor : '#333'}">
					{{statusType[item.status]}}
				</view>
			</view>
			<view class="flex-center mb-lg" v-for="(aitem,aindex) in item.order_goods" :key="aindex">
				<image mode="aspectFill" class="avatar lg radius-16" :src="aitem.goods_cover"></image>
				<view class="flex-1 ml-md">
					<view class="f-mini-title c-title text-bold max-450 ellipsis"> {{aitem.goods_name}} </view>
					<view class="f-caption c-caption mt-sm mb-sm">服务{{$t('action.attendantName')}}：{{item.coach_name || ''}}</view>
					<view class="flex-between">
						<view class="flex-y-baseline f-caption c-warning">¥<view class="f-title text-bold">
								{{aitem.true_price}}
							</view>
						</view>
						<view class="c-paragraph">x{{aitem.num}}</view>
					</view>
				</view>
			</view>
			<view class="flex-between pt-lg b-1px-t">
				<view class="flex-y-center f-desc c-title">付款：
					<view class="text-bold">¥{{item.pay_price}}</view>
				</view>
				<view class="flex-y-center f-desc c-title">分销佣金：
					<view class="c-warning text-bold">¥{{item.cash}}</view>
				</view>
			</view>
		</view>

		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>

		<view class="space-footer"></view>

		<common-popup @confirm="confirmCancel" ref="cancel_item" type="CANCEL_ORDER" :info="popupInfo"></common-popup>
		<common-popup @confirm="confirmDel" ref="del_item" type="DELETE_ORDER" :info="popupInfo"></common-popup>

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
				activeIndex: 0,
				tabList: [{
					title: '全部',
					id: 0
				}, {
					title: '未入账',
					id: 1,
				}, {
					title: '已入账',
					id: 2
				}],
				statusType: {
					1: '未入账',
					2: '已入账',
				},
				param: {
					page: 1,
					pay_type: 0,
				},
				list: {
					data: []
				},
				loading: true,
				popupInfo: {},
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.$util.showLoading()
			let {
				type = 1
			} = options
			uni.setNavigationBarTitle({
				title: type == 1 ? '返佣记录' : '佣金流水'
			})
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
					tabList,
					activeIndex
				} = this
				param.status = tabList[activeIndex].id
				let newList = await this.$api.mine.commList(param);

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
			}
		}
	}
</script>


<style lang="scss">
	.money {
		font-size: 50rpx;
	}
</style>
