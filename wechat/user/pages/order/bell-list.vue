<template>
	<view class="order-pages">
		<view @tap.stop="goDetail(index,'detail')" class="item-child mt-md ml-md mr-md pd-lg fill-base radius-16 rel"
			v-for="(item,index) in list.data" :key="index">
			<view v-if="item.is_add">
				<view class="bell-tag flex-center f-icontext c-base abs" :style="{background:primaryColor}">续单服务
				</view>
				<view class="space-md"></view>
			</view>
			<view class="flex-between pb-lg b-1px-b">
				<view class="f-paragraph c-title max-500 ellipsis"
					@tap.stop="$util.goUrl({openType:'copy',url:item.order_code})">订单号：{{item.order_code}}
				</view>
				<!-- // pay_type 1待支付，2待服务，3向导接单，4向导出发，5向导到达，6服务中，7服务完成 -->
				<view class="f-caption text-bold"
					:style="{color:item.pay_type==1?primaryColor: [2,3,4,5,6].includes(item.pay_type)?subColor: item.pay_type == 7 ? '#11C95E' : '#333'}">
					{{statusType[item.pay_type]}}
				</view>
			</view>
			<view class="flex-center mt-lg" v-for="(aitem,aindex) in item.order_goods" :key="aindex">
				<!-- #ifdef H5 -->
				<view class="cover radius-16">
					<view class="h5-image cover radius-16" :style="{ backgroundImage : `url('${aitem.goods_cover}')`}">
					</view>
				</view>
				<!-- #endif -->
				<!-- #ifndef H5 -->
				<image mode="aspectFill" class="cover radius-16" :src="aitem.goods_cover"></image>
				<!-- #endif -->
				<view class="flex-1 ml-md">
					<view class="flex-between">
						<view class="f-mini-title c-title text-bold max-400 ellipsis">
							{{aitem.goods_name}}
						</view>
						<view class="f-desc c-desc">x{{aitem.num}}</view>
					</view>
					<view class="flex-center" style="margin-top: 6rpx;">
						<view class="flex-1 flex-y-center f-caption c-caption"> 服务{{$t('action.attendantName')}}<view class="ml-md">
								{{item.coach_info.coach_name}}
							</view>
						</view>
						<view class="f-caption c-warning" v-if="aitem.refund_num>0">已退x{{aitem.refund_num}}
						</view>
					</view>
					<view class="flex-y-center f-caption c-caption">预约时间<view class="ml-md">{{item.start_time}}
						</view>
					</view>
					<view class="flex-y-center f-caption c-caption">总计：
						<view class="flex-y-baseline c-warning text-bold">¥
							<view class="f-title">{{item.pay_price}}</view>
						</view>
					</view>
				</view>
			</view>
			<view class="flex-between pt-lg">
				<view></view>
				<view class="flex-warp">
					<!-- // pay_type 1待支付，2待服务，3向导接单，4向导出发，5向导到达，6服务中，7服务完成 -->
					<!-- 待支付 -->
					<block v-if="item.pay_type == 1">
						<button @tap.stop="toCancel(index)" class="clear-btn order">取消订单</button>
						<button @tap.stop="toPay(index)" class="clear-btn order"
							:style="{color:'#fff',background:primaryColor,borderColor:primaryColor}">去支付</button>
					</block>
					<!-- 支付超时 -->
					<block v-if="item.pay_type == -1 || item.pay_type == 7">
						<button @tap.stop="toCancel(index)" class="clear-btn order">删除</button>
					</block>
					<!-- 待服务 -->
					<button @tap.stop="goDetail(index,'refund')" class="clear-btn order"
						v-if="item.can_refund > 0">申请退款</button>
					<!-- 服务中 -->
					<!-- is_add 续单订单 -->
					<block v-if="item.pay_type == 6 && (item.is_add || (!item.is_add && item.can_add_order))">
						<button
							@tap.stop="$util.goUrl({url:`/user/pages/bell/list?id=${item.id}&coach_id=${item.coach_id}`})"
							class="clear-btn order"
							:style="{color:'#fff',background:primaryColor,borderColor:primaryColor}"
							v-if="">{{item.is_add?'更换项目':'更换/续单'}}</button>
					</block>
					<!-- 已完成 -->
					<block v-if="item.pay_type == 7 && !item.is_add">
						<button @tap.stop="goDetail(index,'evaluate')" class="clear-btn order"
							v-if="!item.is_comment">去评价</button>
						<button @tap.stop="toAgain(index)" class="clear-btn order"
							:style="{color:'#fff',background:primaryColor,borderColor:primaryColor}"
							v-if="item.can_again">再来一单</button>
					</block>
				</view>
			</view>
		</view>

		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-footer"></view>

		<common-popup @confirm="confirmCancel" ref="cancel_item" type="CANCEL_ORDER" :info="popupInfo">
		</common-popup>
		<common-popup @confirm="confirmDel" ref="del_item" type="DELETE_ORDER" :info="popupInfo"></common-popup>

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
				statusType: {
					'-1': '已取消',
					1: '待支付',
					2: '待服务',
					3: this.$t('action.orderTaking'),
					4: this.$t('action.setOut'),
					5: this.$t('action.arrive'),
					6: '服务中',
					7: '已完成',
					8: '待转单'
				},
				param: {
					page: 1,
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
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		async onLoad(options) {
			this.options = options
			this.param.order_id = options.id
			await this.initIndex()
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
			this.loading = true;
			this.getList(this.param.page + 1);
		},
		methods: {
			...mapActions(['getConfigInfo', 'getOrderList']),
			...mapMutations(['updateOrderItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif 
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				await this.getList(1)
			},
			initRefresh() {
				this.initIndex(true)
			},
			async getList(page = 0) {
				if (page) {
					this.param.page = page
				}
				let {
					list: oldList,
					param,
				} = this
				let newList = await this.$api.order.getAddClockOrder(param);

				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			// 取消/删除订单
			async toCancel(index) {
				let {
					id,
					order_code,
					order_goods,
					pay_type,
				} = this.list.data[index]
				let {
					goods_cover: image,
				} = order_goods[0]
				this.popupInfo = {
					id,
					name: `系统单号：${order_code}`,
					image,
					index,
				}
				let refs_key = pay_type == 1 ? 'cancel_item' : 'del_item'
				this.$refs[refs_key].open()
			},
			// 确认：取消订单
			async confirmCancel() {
				let {
					id,
					index,
				} = this.popupInfo
				await this.$api.order.cancelOrder({
					id
				})
				if (this.activeIndex == 0) {
					this.list.data[index].pay_type = -1
				} else {
					this.list.data.splice(index, 1)
				}
				this.$util.showToast({
					title: `取消成功`
				})
				this.$refs.cancel_item.close()
			},
			// 确认：删除订单
			async confirmDel() {
				let {
					id,
					index,
				} = this.popupInfo
				await this.$api.order.delOrder({
					id
				})
				this.list.data.splice(index, 1)
				this.$util.showToast({
					title: `删除成功`
				})
				this.$refs.del_item.close()
				this.$util.back()
			},
			// 去支付
			async toPay(index) {
				if (this.lockTap) return;
				this.lockTap = true;
				this.$util.showLoading()
				let {
					id,
					pay_model
				} = this.list.data[index]

				try {
					let {
						pay_list
					} = await this.$api.order.rePayOrder({
						id
					})
					this.$util.hideAll()
					if (pay_list) {
						if (pay_model == 3) {
							pay_list = {
								orderInfo: pay_list,
								provider: 'alipay'
							}
						}

						// #ifdef H5

						if (pay_model == 3) {
							pay_list = Object.assign({}, pay_list, {
								order_id: id,
								page_url: `/pages/order?tab=2`
							})
							this.updateOrderItem({
								key: 'alipayOrderParams',
								val: pay_list
							})
							this.$util.goUrl({
								url: '/user/pages/alipay-result'
							})
							this.lockTap = false
							setTimeout(() => {
								this.initRefresh()
							}, 3000)
							return
						}
						// #endif

						try {
							await this.$util.pay(pay_list)
							this.$util.showToast({
								title: `支付成功`
							})
							if (this.activeIndex == 0) {
								this.list.data[index].pay_type = 2
							} else {
								this.list.data.splice(index, 1)
							}
							this.lockTap = false;
						} catch (e) {
							this.lockTap = false;
							return
						}
					}
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
			// 去核销
			async toHx(index) {
				let {
					id,
					qr: image
				} = this.list.data[index]
				this.popupInfo = {
					id,
					image
				}
				this.$refs.hx_item.open()
			},
			// 订单详情/申请退款/去评价
			goDetail(index, page) {
				let {
					id
				} = this.list.data[index]
				let url = `/user/pages/order/${page}?id=${id}`
				this.$util.goUrl({
					url
				})
			},
			// 再来一单
			async toAgain(index) {
				let {
					id: order_id,
					coach_id
				} = this.list.data[index]
				await this.$api.order.onceMoreOrder({
					order_id
				})
				this.$util.goUrl({
					url: `/user/pages/order?id=${coach_id}`
				})
			}
		}
	}
</script>


<style lang="scss">
</style>
