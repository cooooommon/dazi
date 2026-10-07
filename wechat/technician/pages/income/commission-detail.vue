<template>
	<view class="technician-commission-detail" v-if="detail.id">
		<view class="mt-md pd-lg fill-base">
			<view class="flex-y-center f-mini-title c-title">
				<view class="dot mr-sm radius" :style="{background:primaryColor}"></view>订单号<view class="ml-sm">
					{{detail.order_code}}
				</view>
			</view>
			<view class="f-mini-title c-title text-bold mt-lg">项目明细</view>
			<view class="flex-between f-paragraph mt-lg" v-for="(item,index) in detail.order_goods" :key="index">
				<span class="c-paragraph max-500">{{item.goods_name}}<span
						class="ml-lg">x{{item.true_num}}</span></span>
				<view class="c-title">¥{{item.true_total_price}}</view>
			</view>
			<view class="flex-between f-paragraph mt-lg">
				<view class="c-paragraph">卡券优惠</view>
				<view class="c-title">-¥{{detail.discount}}</view>
			</view>
			<view class="flex-between f-paragraph mt-lg">
				<view class="c-paragraph">项目总金额</view>
				<view class="c-title">¥{{detail.true_service_price}}</view>
			</view>
			<view class="flex-between f-paragraph mt-lg">
				<view class="c-paragraph">分成比例</view>
				<view class="c-title">{{detail.coach_balance}}%</view>
			</view>
			<view class="flex-between f-paragraph mt-lg mb-lg">
				<view class="c-paragraph">服务时间</view>
				<view class="c-caption">{{detail.time_text}}</view>
			</view>
			<view class="flex-between pt-lg b-1px-t">
				<view class="f-paragraph c-paragraph">应得收益</view>
				<view class="flex-y-center f-caption" :style="{color:primaryColor}">¥<view class="f-mini-title">
						{{detail.true_coach_cash}}
					</view>
				</view>
			</view>
		</view>

		<view class="mt-md pd-lg fill-base" v-if="detail.add_order && detail.add_order.length > 0">
			<view class="f-mini-title c-title text-bold">续单明细</view>
			<block v-for="(item,index) in detail.add_order" :key="index">
				<view class="flex-between f-paragraph mt-lg">
					<view class="c-paragraph max-500">{{`${item.order_goods[0].goods_name} (第${index*1+1}次续单)`}}</view>
					<view class="c-title">¥{{item.true_service_price}}</view>
				</view>
				<view class="flex-between f-paragraph mt-lg mb-lg">
					<view class="c-paragraph">分成比例</view>
					<view class="c-title">{{item.coach_balance}}%</view>
				</view>
				<view class="flex-between f-paragraph mt-lg mb-lg" v-if="detail.broker_id > 0">
					<view class="c-paragraph">分摊扣款</view>
					<view class="c-title">-￥{{item.broker_coach_cash}}</view>
				</view>
				<view class="flex-between f-paragraph mt-lg mb-lg">
					<view class="c-paragraph">应得收益</view>
					<view class="flex-center">
						<view class="c-title">{{`${item.add_coach_text} =`}}</view>
						<view class="f-caption flex-y-center" :style="{color:primaryColor}">￥<text class="f-mini-title" :style="{color:primaryColor}">{{item.coach_cash}}</text></view>
					</view>
				</view>
			</block>
			<view class="flex-between pt-lg b-1px-t">
				<view class="f-paragraph c-paragraph" style="width:360rpx">续单收益</view>
				<span>
					{{`${detail.add_coach_text} =`}}
					<span class="f-caption ml-sm" :style="{color:primaryColor}">¥<span
							class="f-mini-title">{{detail.add_coach_cash}}</span>
					</span>
				</span>
			</view>
		</view>
		<view class="mt-md pd-lg fill-base" v-if="detail.broker_id > 0">
			<view class="f-mini-title c-title text-bold">扣费明细</view>
			<view class="flex-between f-paragraph mt-lg">
				<view class="c-paragraph">经纪人扣费</view>
				<view class="c-title text-bold">¥{{detail.broker_coach_cash}}</view>
			</view>
			<view class="flex-between f-paragraph mt-lg b-1px-t pt-lg">
				<view class="c-paragraph">合计扣费</view>
				<view class="c-warning text-bold">¥{{detail.broker_coach_cash}}</view>
			</view>
		</view>

		<view class="mt-md pd-lg fill-base">
			<view class="f-mini-title c-title text-bold">车费明细</view>
			<view class="flex-between f-paragraph mt-lg">
				<view class="c-paragraph">车费收益</view>
				<view class="c-title text-bold ">¥{{detail.true_car_price}}</view>
			</view>
		</view>

		<view class="space-max-footer"></view>

		<fixed position="bottom">
			<view class="pd-lg fill-base flex-between f-paragraph" style="width:100%">
				<view class="f-mini-title c-title text-bold">实际收益</view>
				<view class="flex-y-center c-warning text-bold">
					{{`${detail.total_order_text} =`}}
					<view class="flex-y-baseline f-caption ml-sm">¥<view class="f-mini-title">
							{{detail.total_order_cash}}
						</view>
					</view>
				</view>
			</view>
			<view class="space-safe fill-base"></view>
		</fixed>
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
				isLoad: false,
				options: {},
				detail: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		async onLoad(options) {
			this.options = options
			this.$util.setNavigationBarColor({
				bg: '#F2F2F2',
				color: '#000000'
			})
			await this.initIndex()
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
				await this.getDetail()
				this.isLoad = true
			},
			initRefresh() {
				this.initIndex(true)
			},
			async getDetail() {
				let {
					id
				} = this.options
				let data = await this.$api.technician.coachCommissionInfo({
					id
				})
				// data.true_service_price * data.coach_balance
				data.true_coach_cash = (Math.round(data.true_service_price * data.coach_balance * 100) / 10000).toFixed(2)
				data.order_goods.map(item => {
					item.true_num = item.num - item.refund_num
					item.true_total_price = item.price * item.true_num
				})
				let add_coach_cash = 0
				let add_coach_text = ''
				data.add_order.map((item, index) => {
					item.coach_cash = data.broker_id>0 ? item.coach_cash : Math.round(item.service_price * item.coach_balance) / 100
					add_coach_cash += item.coach_cash * 1
					add_coach_text +=  `${index==0?'':' + '}${item.coach_cash}` // `${index==0?'':' + '}${item.true_service_price}*${item.coach_balance}%-${item.broker_coach_cash}`
					item.add_coach_text = `${item.true_service_price}*${item.coach_balance}%${data.broker_id>0?(`-`+item.broker_coach_cash): ''}`
				})
				data.add_coach_cash = add_coach_cash.toFixed(2)
				data.add_coach_text = add_coach_text
				
				let {
					coach_cash,
					true_car_price,
					true_coach_cash
				} = data
				data.total_order_text = data.add_order && data.add_order.length > 0 ?
					`${true_coach_cash ? Number(true_coach_cash).toFixed(2) : true_coach_cash} + ${add_coach_cash ? Number(add_coach_cash).toFixed(2): add_coach_cash} ${data.broker_id>0?`-`+data.broker_coach_cash:''} + ${true_car_price?Number(true_car_price).toFixed(2):true_car_price}` :
					`${true_coach_cash?Number(true_coach_cash).toFixed(2):true_coach_cash} ${data.broker_id>0?`-`+data.broker_coach_cash:''} + ${true_car_price?Number(true_car_price).toFixed(2):true_car_price}`

				let total_order_cash = true_coach_cash * 1 + add_coach_cash * 1 + true_car_price * 1 - (data.broker_id>0?data.broker_coach_cash*1: 0)
				data.total_order_cash = total_order_cash.toFixed(2)
				this.detail = data
			}
		}
	}
</script>


<style lang="scss">
	.technician-commission-detail {
		.dot {
			width: 14rpx;
			height: 14rpx;
		}
	}
</style>
