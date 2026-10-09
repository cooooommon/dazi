<template>
	<view class="order-detail" v-if="isLoad">
		<view class="detail-status pl-lg pr-lg flex-y-center pt-lg pb-lg" :style="{background: primaryColor}" v-if="!options.port">
			<view class="">
				<view class="flex-y-center">
					<view class="" style="width: 44rpx;">
						<i class="iconfont iconshijianguanli c-base" style="font-size: 22px;"></i>
					</view>
					<text class="f-lg-title text-bold c-base pl-sm">{{options.refund == 1 ? refundStatus[detail.status] : statusObj[detail.status]}}</text>
				</view>
				<block v-if="options.refund == 1">
					<!-- <view class="f-desc c-base pt-sm" style="padding-left: 54rpx;" v-if="detail.status == 1">
						商家处理中，请耐心等待
					</view> -->
					<view class="f-desc c-base pt-sm pre-wrap" style="padding-left: 54rpx;" v-if="detail.status == 3 && detail.refund_text">
						{{detail.refund_text}}
					</view>
				</block>
				<block v-else>
					<view class="f-desc c-base pt-sm" style="padding-left: 54rpx;" v-if="detail.status == 2">
						请在{{$util.formatTime(detail.end_time * 1000 , 'YY-M-D')}}(含)前到店消费
					</view>
					<view class="f-desc c-base pt-sm" v-if="detail.status == 1">
						剩余：<min-countdown :targetTime="detail.over_time*1000" @callback="countEnd"></min-countdown>
					</view>
				</block>
			</view>
		</view>
		<view class="ml-md mt-md mr-md radius-16 fill-base pl-lg pr-lg pt-sm" v-else>
			<view class="flex-between pt-lg pb-lg">
				<view class="f-mini-title text-bold">核销数量</view>
				<view class="flex-center">
					<i class="iconfont iconjian" style="font-size: 22px;color: #DEDEDE;" @tap="changeNum('reduce')"></i>
					<view class="f-paragraph refund-num flex-center">{{param.num}}</view>
					<i class="iconfont iconjia" style="font-size: 22px;" :style="{color: primaryColor}" @tap="changeNum('add')"></i>
				</view>
			</view>
			<view class="pt-md flex-center pb-lg">
				<view @tap="hxOrder" class="f-mini-title c-base confirm-hx flex-center " :style="{backgroundColor: primaryColor}">确认核销</view>
			</view>
		</view>
		<view class="ml-md mt-md mr-md radius-16 fill-base pl-lg pr-lg h-120 flex-between" v-if="detail.hx_data" @tap="changeHx">
			<view class="f-mini-title text-bold">核销记录</view>
			<view class="flex-center">
				<text class="f-paragraph pr-sm">{{detail.hx_data.length}}次</text>
				<i class="iconfont icongengduo" style="font-size: 10px;color: #AFB2B8;"></i>
			</view>
		</view>
		<view class="ml-md mt-md mr-md radius-16 fill-base pl-lg pr-lg pb-lg">
			<view class="flex-between h-88" v-if="detail.status == 1 && options.refund != 1">
				<i class="iconfont icondianpu_1" :style="{color: primaryColor,fontSize: `18px`}"></i>
				<text class="f-mini-title flex-1 pr-lg text-bold pl-sm ellipsis">{{detail.name}}</text>
				<!-- <text class="text-bold order-status flex-center f-ms-little">休息中</text> -->
				<view class="f-ms-little trade-status flex-center rel"
					:style="{color: detail.trade_status == 1 ? primaryColor : `#F8862D`}">
					<view class="trade-status-bg abs"
						:style="{backgroundColor: detail.trade_status == 1 ? primaryColor : `#F8862D`}"></view>
					<text>{{detail.trade_status == 1 ? `营业中` : `休息中`}}</text>
				</view>
			</view>
			<view class="flex-between pb-lg" :class="[{'pt-sm': detail.status == 1},{'pt-lg': detail.status == 2 || detail.status == 3 || (options.refund == 1&&detail.status == 1)}]" v-if="!options.port">
				<image :src="detail.cover" mode="aspectFill" class="order-img radius-16"></image>
				<view class="pl-md flex-1">
					<view class="flex-between">
						<text class="ellipsis pr-lg f-title text-bold max-400">{{detail.name}}</text>
						<text class="f-desc" style="color: #4D4D4D;">×{{detail.num}}</text>
					</view>
					<view class="pt-md pb-md f-desc c-icontext">{{detail.ensure == 1 ? '随时退·' : ''}}过期退</view>
					<view class="f-mini-title text-bold c-warning">¥{{detail.price | handleNumber}}</view>
				</view>
			</view>
			<block v-if="options.refund != 1">
				<block v-if="detail.status == 1">
					<view class="b-1px-t pt-lg">
						<text class="f-desc d-span">套餐优惠：</text>
						<text class="f-desc text-bold c-warning">-¥{{(detail.init_price*1 - detail.price*1)*detail.num | handleNumber}}</text>
					</view>
					<view class="pt-md" v-if="detail.is_integral">
						<text class="f-desc d-span">积分抵扣：</text>
						<text class="f-desc text-bold c-warning">{{detail.integral}}积分抵扣¥{{detail.integral_to_money}}</text>
					</view>
					<view class="pt-md">
						<text class="f-desc d-span">实付金额：</text>
						<text class="f-desc text-bold ">¥{{detail.pay_price}}</text>
					</view>
				</block>
				<block v-if="detail.status == 2 || detail.status == 3">
					<!-- <view class="flex-center pt-lg b-1px-t pb-lg" v-if="detail.can_refund_num">
						<image :src="detail.qr_path" mode="aspectFill" class="qr-code"></image>
					</view> -->
					<view class="flex pt-lg" style="justify-content: space-between;">
						<view class="">
							<view class="f-mini-title text-bold" style="line-height: 1;">券码信息（{{detail.can_refund_num || 0}}张可用）</view>
							<view class="f-desc c-icontext pt-md">{{$util.formatTime(detail.end_time * 1000 , 'YY-M-D h:m')}}到期</view>
						</view>
						<!-- <view class="refund-btn f-paragraph flex-center" v-if="detail.can_refund_num > 0" 
						@tap="$util.goUrl({url: `/business/pages/manage/order/refund?id=${detail.id}`})">申请退款</view> -->
					</view>
					<block v-for="(item,index) in detail.code_info" :key="index">
						<view class="pt-lg flex-between">
							<view class="flex-center">
								<i class="iconfont iconkaquan" style="font-size: 17px;"></i>
								<text class="f-desc pl-sm">{{item.code_num}}</text>
							</view>
							<text class="c-warning f-desc">{{couponStatus[item.status]}}</text>
						</view>
					</block>
				</block>
			</block>
			<!--退款信息-->
			<block v-if="options.refund == 1">
				<view class="flex pt-lg" style="justify-content: space-between;">
					<view class="">
						<view class="f-mini-title text-bold flex-x-center" style="line-height: 1;">券码信息（{{detail.can_refund_num || 0}}张可用）</view>
						<view class="f-desc c-icontext pt-md">{{$util.formatTime(detail.end_time * 1000 , 'YY-M-D h:m')}}到期</view>
					</view>
					<view class="refund-btn f-paragraph flex-center" v-if="detail.can_refund_num > 0" 
					@tap="$util.goUrl({url: `/business/pages/manage/order/refund?id=${detail.id}`})">申请退款</view>
				</view>
				<block v-for="(item,index) in detail.code_info" :key="index">
					<view class="pt-lg flex-between">
						<view class="flex-center">
							<i class="iconfont iconkaquan" style="font-size: 17px;"></i>
							<text class="f-desc pl-sm">{{item.code_num}}</text>
						</view>
						<text class="c-warning f-desc">{{refundCouponStatus[item.status]}}</text>
					</view>
				</block>
			</block>
			
		</view>
		<view class="ml-md mt-md mr-md radius-16 fill-base pl-lg pr-lg pb-lg flex-between pt-lg" v-if="options.port">
			<image :src="detail.cover" mode="aspectFill" class="order-img radius-16"></image>
			<view class="pl-md flex-1">
				<view class="flex-between">
					<text class="ellipsis pr-lg f-title text-bold max-400">{{detail.name}}</text>
					<text class="f-desc" style="color: #4D4D4D;">×{{detail.num}}</text>
				</view>
				<view class="pt-md pb-md f-desc c-icontext">{{detail.ensure == 1 ? '随时退·' : ''}}过期退</view>
				<view class="f-mini-title text-bold c-warning">¥{{detail.price | handleNumber}}</view>
			</view>
		</view>
		<block v-if="(options.refund != 1 && detail.status != 1) || options.refund == 1 ">
			<view class="ml-md mt-md mr-md radius-16 fill-base pl-lg pr-lg pb-lg" v-if="!options.port">
				<view class="h-88 text-bold f-mini-title flex-y-center">{{detail.store_name}}</view>
				<view class="flex-y-center">
					<view class="f-ms-little trade-status flex-center rel"
						:style="{color: detail.trade_status == 1 ? primaryColor : `#F8862D`}">
						<view class="trade-status-bg abs"
							:style="{backgroundColor: detail.trade_status == 1 ? primaryColor : `#F8862D`}"></view>
						<text>{{detail.trade_status == 1 ? `营业中` : `休息中`}}</text>
					</view>
					<view class="f-caption pl-sm">{{detail.wtime}}</view>
				</view>
				<view class="flex-between pt-lg">
					<view class="pr-lg flex-1 f-caption">
						{{detail.address + detail.info}}
					</view>
					<!-- <view class="pl-md flex-column flex-center" @tap="toTel">
						<view class="icon-box flex-center">
							<i class="iconfont icondianhua" style="font-size: 14px;"></i>
						</view>
						<view class="f-ms-little" style="padding-top: 5rpx;">联系商家</view>
					</view> -->
				</view>
			</view>
			
			<view class="ml-md mt-md mr-md radius-16 fill-base pl-lg pr-lg">
				<view class="pb-lg">
					<view class="h-88 text-bold f-mini-title flex-y-center">团购详情</view>
					<block v-for="(item,index) in detail.sku" :key="index">
						<block v-if="index < skuMore ">
							<view class="f-desc text-bold" :style="{paddingTop: index > 0 ? `40rpx` : `0`}">{{item.name}}</view>
							<view class="flex pt-md" v-for="(citem,cindex) in item.price" :key="cindex">
								<view class="f-desc flex-1">{{citem.name}}</view>
								<view class="flex-between" style="width: 160rpx;">
									<text class="f-desc">{{citem.num}}份</text>
									<text class="f-desc">¥{{citem.price}}</text>
								</view>
							</view>
						</block>
					</block>
				</view>
				<view class="b-1px-t h-80 flex-center" v-if="detail.sku.length > 2" @tap="changeSkuMore">
					<text class="f-caption"> {{skuMore == 2 ? `查看更多` : `收起`}} </text>
					<i class="iconfont iconxiangxiazhankai" :class="[{'rotate-180': skuMore > 2 }]"></i>
				</view>
			</view>
			
			<view class="ml-md mt-md mr-md radius-16 fill-base pl-lg pr-lg" v-if="!options.port">
				<view class="pb-lg">
					<view class="h-88 text-bold f-mini-title flex-y-center">温馨提示</view>
					<view class="fill-base radius-16">
						<view class="f-desc text-bold">有效期</view>
						<view class="pt-sm flex-y-center">
							<text class="mr-sm notice-left"></text>{{$util.formatTime(detail.start_time * 1000 , 'YY-M-D h:m')}} 至 {{$util.formatTime(detail.end_time * 1000 , 'YY-M-D h:m')}}
						</view>
						<view class="f-desc text-bold" style="padding-top: 40rpx;">使用时间</view>
						<view class="pt-sm flex-y-center">
							<text class="mr-sm notice-left"></text>{{detail.wtime}}
						</view>
						<view class="f-desc text-bold" style="padding-top: 40rpx;">预约信息</view>
						<view class="pt-sm flex-y-center">
							<text class="mr-sm notice-left"></text>
							{{detail.reservation_day ? `需提前${detail.reservation_day}天预约` : '无需预约'}}
						</view>
						<view class="f-desc text-bold" style="padding-top: 40rpx;">使用规则</view>
						<abnor v-if="!detail.rule_text"></abnor>
						<view v-else class="f-paragraph rule-text" :class="[{'ellipsis-3': isRule && isRuleMore }]" style="white-space:pre-wrap">
							{{detail.rule_text}}
						</view>
					</view>
				</view>
				<view class="b-1px-t h-80 flex-center" @tap="changeIsRule" v-if="isRule">
					<text class="f-caption"> {{isRuleMore ? '查看更多' : '收起' }} </text>
					<i class="iconfont iconxiangxiazhankai" :class="[{'rotate-180': !isRuleMore }]"></i>
				</view>
			</view>
		</block>
		
		
		<view class="ml-md mt-md mr-md radius-16 fill-base pl-lg pr-lg pb-lg">
			<block v-if="options.refund == 1">
				<view class="h-88 text-bold f-mini-title flex-y-center">退款信息</view>
				<view class="pt-sm flex-y-center">
					<text class="f-desc d-span">退款单号：</text>
					<text class="f-desc pr-sm">{{detail.refund_code}}</text>
					<text class="f-ms-little copy-btn flex-center" @tap="$util.goUrl({url: detail.refund_code , openType: 'copy'})">复制</text>
				</view>
				<view class="order-item-info">
					<text class="f-desc d-span">下单时间：</text>
					<text class="f-desc">{{$util.formatTime(detail.order_create_time * 1000 , 'YY-M-D h:m')}}</text>
				</view>
				<view class="order-item-info">
					<text class="f-desc d-span">提交时间：</text>
					<text class="f-desc">{{$util.formatTime(detail.create_time * 1000 , 'YY-M-D h:m')}}</text>
				</view>
				<view class="order-item-info" v-if="detail.refund_time">
					<text class="f-desc d-span">审核时间：</text>
					<text class="f-desc">{{$util.formatTime(detail.refund_time * 1000 , 'YY-M-D h:m')}}</text>
				</view>
				<view class="order-item-info">
					<text class="f-desc d-span">退款数量：</text>
					<text class="f-desc">{{detail.num}}</text>
				</view>
				<view class="order-item-info">
					<text class="f-desc d-span">退款金额：</text>
					<text class="f-desc">{{detail.price}}</text>
				</view>
				<view class="order-item-info">
					<text class="f-desc d-span">退款原因：</text>
					<text class="f-desc">{{detail.text}}</text>
				</view>
			</block>
			<block v-else>
				<view class="h-88 text-bold f-mini-title flex-y-center">订单信息</view>
				<block v-if="detail.status == 1">
					<view class="pt-sm flex-y-center">
						<text class="f-desc d-span">订单编号：</text>
						<text class="f-desc pr-sm">{{detail.order_code}}</text>
						<text class="f-ms-little copy-btn flex-center" @tap="$util.goUrl({url: detail.order_code , openType: 'copy'})">复制</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span">手机号码：</text>
						<text class="f-desc">{{detail.mobile}}</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span" :decode="true">下&ensp;单&ensp;人：</text>
						<text class="f-desc">{{detail.nickName}}</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span">下单时间：</text>
						<text class="f-desc">{{$util.formatTime(detail.create_time * 1000 , 'YY-M-D h:m')}}</text>
					</view>
				</block>
				<block v-if="detail.status == 2 || detail.status == 3">
					<view class="pt-sm flex-y-center">
						<text class="f-desc d-span">订单编号：</text>
						<text class="f-desc pr-sm">{{detail.order_code}}</text>
						<text class="f-ms-little copy-btn flex-center" @tap="$util.goUrl({url: detail.order_code , openType: 'copy'})">复制</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span">手机号码：</text>
						<text class="f-desc">{{detail.mobile}}</text>
					</view>
					<view class="order-item-info" v-if="detail.hx_time">
						<text class="f-desc d-span">消费时间：</text>
						<text class="f-desc">{{$util.formatTime(detail.hx_time * 1000 , 'YY-M-D h:m')}}</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span">付款时间：</text>
						<text class="f-desc">{{$util.formatTime(detail.pay_time * 1000 , 'YY-M-D h:m')}}</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span">下单时间：</text>
						<text class="f-desc">{{$util.formatTime(detail.create_time * 1000 , 'YY-M-D h:m')}}</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span" :decode="true">数&emsp;&emsp;量：</text>
						<text class="f-desc">{{detail.num}}</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span">商品总价：</text>
						<text class="f-desc">¥{{detail.init_price * detail.num | handleNumber}}</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span">套餐优惠：</text>
						<text class="f-desc">-¥{{(detail.init_price*1 - detail.price*1)*detail.num | handleNumber}}</text>
					</view>
					<view class="pt-md" v-if="detail.is_integral">
						<text class="f-desc d-span">积分抵扣：</text>
						<text class="f-desc c-warning">{{detail.integral}}积分抵扣¥{{detail.integral_to_money}}</text>
					</view>
					<view class="order-item-info">
						<text class="f-desc d-span">实付金额：</text>
						<text class="f-desc">¥{{detail.pay_price}}</text>
					</view>
				</block>
			</block>
		</view>
		
		
		<uni-popup ref="hx_item" type="bottom" :maskClick="true">
			<view class="fill-base content-popup">
				<view class="flex-center f-title text-bold">核销记录</view>
				<scroll-view scroll-y="true" style="max-height: 800rpx;">
					<block v-for="(item,index) in detail.hx_data" :key="index">
						<view class="pt-lg pb-lg" :class="[{'b-1px-t': index > 0}]">
							<view class="flex-y-center">
								<text class="content-name f-desc">消费时间</text>
								<view class="flex-1">{{$util.formatTime(item.create_time * 1000 , 'YY-M-D h:m')}}</view>
							</view>
							<view class="flex-y-center pt-sm">
								<text class="content-name f-desc">券号</text>
								<view class="flex-1">
									<view class="pt-sm pb-sm" v-for="(citem,cindex) in item.code_num" :key="cindex">{{citem}}</view>
								</view>
							</view>
							<view class="flex-y-center pt-sm">
								<text class="content-name f-desc">使用份数</text>
								<view class="flex-1">{{item.num}}</view>
							</view>
						</view>
					</block>
				</scroll-view>
			</view>
		</uni-popup>
		
		<view class="space-max-footer"></view>

		<fixed position="bottom" :zIndex="99" v-if="((options.refund == 1&&detail.status == 1) || (options.refund != 1 && [2,3].includes(detail.status)))&& !options.port">
			<view class="fill-base">
				<view class="flex-between bottom-btn">
					<!-- <view class="pl-md flex-column flex-center" @tap="toTel">
						<view class="icon-box flex-center">
							<i class="iconfont icondianhua" style="font-size: 14px;"></i>
						</view>
						<view class="f-ms-little" style="padding-top: 5rpx;">联系商家</view>
					</view> -->
					<view></view>
					<view class="flex-center">
						<block v-if="options.refund == 1">
							<block v-if="detail.status == 1">
								<view @tap="handleRefund(index, 3)" class="hollow f-mini-title c-warning flex-center" :style="{border: `1px solid #FF2404`}">拒绝退款</view>
								<view @tap="handleRefund(index, 2)" class="panic-buying f-mini-title flex-center ml-md"
									:style="{border: `1px solid ${primaryColor}`,color:primaryColor}">同意退款</view>
							</block>
							
						</block>
						<block v-else>
							<view class="panic-buying f-mini-title c-base flex-center ml-md" v-if="detail.status == 3"
								:style="{background: primaryColor}"
								@tap="$util.goUrl({url: `/business/pages/package/order/evaluate?id=${detail.id}&type=see`})">查看评价</view>
							<view class="panic-buying f-mini-title c-base flex-center ml-md" v-if="detail.status == 2"
								:style="{background: primaryColor}"
								@tap="toHx">去核销</view>
						</block>
					</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</fixed>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import siteInfo from '@/siteinfo.js';
	import parser from "@/components/jyf-Parser/index"
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},// port: 'store' 核销参数
				detail: {
					trade_status: 2
				},
				isLoad: false,
				statusObj: {
					1: '待支付',
					2: '待核销',
					3: '已核销',
					'-1': '已取消'
				},
				refundStatus: {
					1: '退款申请中',
					2: '退款成功',
					3: '退款失败'
				},
				refundCouponStatus: {
					1: '退款中',
					2: '已退款',
					3: '退款失败'
				},
				couponStatus: {
					1: '待使用',
					2: '已使用',
					3: '退款中',
					4: '已退款'
				},
				skuMore: 2,
				isRule: false,
				isRuleMore: true,
				param: {
					num: 1,
					
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			gradualColor: state => state.config.configInfo.gradualColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		filters: {
			handerSale(val) {
				if (val < 10) {
					return val
				}
				if (val < 10000) {
					return (val / 10).toFixed(0) * 10 + '+'
				}
				return val > 10000 ? (val / 10000).toFixed(1) + 'w+' : val
			},
			handleNumber(val){
				return val.toFixed(2)
			}
		},
		async onLoad(options) {
			this.$util.showLoading()
			if(options.port == 'store'){
				options.id = options.order_id
			}
			this.options = await this.updateCommonOptions(options)
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo','updateCommonOptions']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				this.getInfo()
			},
			initRefresh() {
				this.$util.showLoading()
				this.getInfo()
			},
			async getInfo(){
				
				let param = {
					order_id: this.options.id
				}
				let methodModel = 'storeOrderInfo'
				if(this.options.refund == 1){
					methodModel = 'refundInfo'
					param = {
						id: this.options.id
					}
				}
				
				let data = await this.$api.business[methodModel](param)
				
				let {
					use_start_time,
					use_end_time,
					trade_week,
				} = data
				
				let max = trade_week.substring(trade_week.length - 1)
				let min = trade_week.substring(0, 1)
				let week = ['日', '一', '二', '三', '四', '五', '六'];
				
				data.wtime = `周${week[min]}至周${week[max]} ${use_start_time}-${use_end_time}`
				if(min == max){
					data.wtime = `周${week[max]} ${use_start_time}-${use_end_time}`
				}
				if ((use_start_time == '00:00' && use_end_time == '23:59') || (use_start_time == use_end_time)) {
					data.wtime = `周${week[min]}至周${week[max]} 全天可用`
					if(min == max){
						data.wtime = `周${week[max]} 全天可用`
					}
				}
				if(data.status == 3 && data.is_comment == 1){
					this.statusObj[data.status] = '已评价'
				}
				this.detail = data
				this.isLoad = true
				this.$util.hideAll()
				
				
				let that = this
				this.$nextTick(function(){
					const query = uni.createSelectorQuery().in(that);
					query.select('.rule-text').boundingClientRect(res => {
						if(res.height > 66){
							that.isRule = true
						}
					}).exec();
				})
				
			},
			async toTel(){
				let {
					refund = 0
				} = this.options
				
				let data = await this.$api.business.getStoreVirtualPhone({order_id: refund == 1 ? this.detail.order_id : this.detail.id})
				this.$util.goUrl({url: data , openType: 'call'})
			},
			changeSkuMore(){
				this.skuMore = this.skuMore == 2 ? this.detail.sku.length : 2
			},
			changeIsRule(){
				this.isRuleMore = !this.isRuleMore
			},
			// 再来一单
			async toAgain(){
				let {
					package_id
				} = this.detail
				let {status} = await this.$api.business.storePackInfo({
					id: package_id
				})
				let msg = {
					'-1': '套餐已删除',
					0: '套餐已下架'
				}
				if(status == 0 || status == -1){
					this.$util.showToast({
						title: msg[status]
					})
					return
				}
				this.$util.goUrl({
					url: `/business/pages/manage/detail?id=${this.detail.package_id}&storeid=${this.detail.store_id}&type=order`
				})
			},
			changeHx(){
				this.$refs.hx_item.open()
			},
			countEnd() {
				this.$util.log("倒计时完了")
				setTimeout(() => {
					this.initRefresh()
					this.$util.back()
				}, 1000)
			},
			toHx(){
				let that = this
				// #ifdef H5
				this.$jweixin.getScanQRCode().then(res => {
					var result = res.resultStr; // 当 needResult 为 1 时，扫码返回的结果
					var resultArr = result.split(','); // 扫描结果以逗号分割数组(一维码)
					var codeContent = resultArr[resultArr.length - 1]; // 获取数组最后一个元素，也就是最终的内容 
					window.location.href = codeContent
				})
				// #endif
				// #ifndef H5
				uni.scanCode({
					success: function (res) {
						that.$util.goUrl({url: `/${res.path}`})
					}
				});
				// #endif
			},
			// 拒绝退款
			async handleRefund(index, status){
				let [res_del, {
					confirm
				}] = await uni.showModal({
					content: status == 3 ? `请确认是否要拒绝退款` : `请确认是否要退款`,
				})
				if (!confirm) return;
				let {
					id
				} = this.detail
				await this.$api.business.refundCheck({
					id,
					status
				})
				this.$util.showToast({
					title: `操作成功`
				})
				this.detail.status = status
				this.$util.back()
			},
			changeNum(type){
				let {
					can_refund_num
				} = this.detail
				let {
					num
				} = this.param
				if(type == 'add'){
					if(num == can_refund_num){
						this.$util.showToast({
							title: `最多可核销${can_refund_num}张`
						})
						return
					}
					this.param.num ++
				}else{
					if(num == 1){
						this.$util.showToast({
							title: '最少1张'
						});
						return
					}
					this.param.num --
				}
			},
			async hxOrder(){
				let {
					id: order_id
				} = this.options
				let {
					num
				} = this.param
				await this.$api.business.hxOrder({
					order_id,
					num
				})
				this.$util.showToast({
					title: '核销成功'
				});
				setTimeout(()=>{
					// let pages = getCurrentPages(); //当前页面栈
					// if (pages.length > 1) {
					// 	var beforePage = pages[pages.length - 2]; //获取上一个页面实例对象  
					// 	if(beforePage.$page.fullPath.indexOf('business/pages/manage/index') != -1 
					// 	|| beforePage.$page.fullPath.indexOf('business/pages/manage/order/list') != -1
					// 	|| beforePage.$page.fullPath.indexOf('business/pages/manage/order/detail') != -1){
					// 		this.$util.back()
					// 		this.$util.goUrl({openType: 'navigateBack',url: 1})
					// 		return
					// 	}
					// }
					this.$util.goUrl({url: `/business/pages/manage/index`})
				},1000)
			}
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		}
	}
</script>

<style lang="scss">
	.order-detail {
		.detail-status{
			min-height: 211rpx;
		}
		.h-88{
			height: 88rpx;
		}
		.h-80{
			height: 80rpx;
		}
		.h-120{
			height: 120rpx;
		}
		.order-status{
			width: 73rpx;
			height: 28rpx;
			border-radius: 4rpx;
			background-color: #FCF0E8;
			color: #F8862D;
		}
		.order-img{
			width: 160rpx;
			height: 160rpx;
		}
		.d-span{
			color: #B5B5B5;
		}
		
		.icon-box{
			width: 45rpx;
			height: 45rpx;
			background: #F5F5F5;
			border-radius: 45rpx;
		}
		.bottom-btn{
			height: 116rpx;
			padding: 0 40rpx;
			.panic-buying{
				width: 190rpx;
				height: 80rpx;
				border-radius: 80rpx;
			}
		}
		.qr-code{
			width: 255rpx;
			height: 255rpx;
		}
		.refund-btn{
			width: 163rpx;
			height: 65rpx;
			border-radius: 65rpx;
			border: 1px solid #D1D1D1;
		}
		.trade-status {
			width: 73rpx;
			height: 28rpx;
			border-radius: 4rpx;
		
			.trade-status-bg {
				width: 73rpx;
				height: 28rpx;
				border-radius: 4rpx;
				opacity: 0.1;
			}
		}
		.notice-left {
			width: 2px;
			height: 2px;
			border-radius: 2rpx;
			background: #333;
		}
		.copy-btn{
			width: 55rpx;
			height: 30rpx;
			border-radius: 4rpx;
			background-color: #EAEAEA;
			color: #414141;
		}
		.hollow{
			width: 190rpx;
			height: 80rpx;
			border-radius: 80rpx;
			border: 1px solid #D1D1D1;
		}
		.rule-text{
			line-height: 44rpx;
		}
		
		.content-popup{
			padding: 40rpx 30rpx;
			.content-name{
				width: 200rpx;
			}
		}
		
		.refund-num{
			width: 48rpx;
		}
		.confirm-hx{
			width: 492rpx;
			height: 90rpx;
			border-radius: 90rpx;
		}
		.order-item-info{
			padding-top: 22rpx
		}
	}
</style>
