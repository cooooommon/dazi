<template>
	<view class="refund-pages" v-if="isLoad">
		<view class="radius-16 fill-base pd-lg flex-between mt-md ml-md mr-md">
			<image :src="detail.cover" mode="aspectFill" class="refund-img"></image>
			<view class="pl-md flex-1">
				<view class="f-title text-bold ellipsis max-500">{{detail.name}}</view>
				<view class="f-desc pt-md c-icontext">{{detail.wtime}}·{{detail.ensure == 1 ? '随时退·' : ''}}过期退</view>
			</view>
		</view>
		<view class="radius-16 fill-base pd-lg mt-md ml-md mr-md">
			<view class="flex-between b-1px-b pb-lg">
				<view class="">
					<view class="f-mini-title text-bold">退款数量</view>
					<view class="pt-sm f-desc c-icontext">最多可退{{detail.can_refund_num}}张</view>
				</view>
				<view class="flex-center">
					<i class="iconfont iconjian" style="font-size: 22px;color: #DEDEDE;" @tap="changeNum('reduce')"></i>
					<view class="f-paragraph refund-num flex-center">{{param.num}}</view>
					<i class="iconfont iconjia" style="font-size: 22px;" :style="{color: primaryColor}" @tap="changeNum('add')"></i>
				</view>
			</view>
			<view class="flex-between pt-lg">
				<view class="f-mini-title text-bold">退款金额</view>
				<text class="f-desc">¥{{refund_price}}</text>
			</view>
			<view class="f-desc c-icontext pt-sm">1-3个工作日退还至原支付方，以实际退款金额为准</view>
		</view>
		<view class="radius-16 fill-base pl-lg pr-lg pb-lg mt-md ml-md mr-md">
			<view class="refund-title flex-y-center">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">退款原因</text>
			</view>
			<view class="fill-body radius-10 refund-textarea">
				<textarea v-model="param.text" maxlength="200" name="" id="" cols="30" rows="10" 
				placeholder="请输入退款说明，我们将用心倾听您的任何意见" 
				class="pl-lg pr-lg pt-md pb-md f-mini-title"></textarea>
				<view class="text-right f-caption c-caption pb-md pr-lg">{{param.text.length > 200 ? 200 : param.text.length}}/200</view>
			</view>
		</view>
		<fix-bottom-button @confirm="submit" :text="[{type:'confirm',text:'提交' }]" bgColor="#fff" borderRadius="45rpx">
		</fix-bottom-button>
		<view class="space-max-footer"></view>
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
				options: {},
				detail: {},
				isLoad: false,
				param: {
					order_id: '',
					num: 1,
					text: ''
				},
				refund_price: 0,
				code_info: []
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			gradualColor: state => state.config.configInfo.gradualColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.$util.showLoading()
			this.options = options
			this.param.order_id = options.id
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo']),
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
				let data = await this.$api.business.orderInfo({order_id: this.options.id})
				
				let {
					use_start_time,
					use_end_time,
					trade_week,
				} = data
				
				let max = trade_week.substring(trade_week.length - 1)
				let min = trade_week.substring(0, 1)
				let week = ['日', '一', '二', '三', '四', '五', '六'];
				
				data.wtime = `周${week[min]}至周${week[max]}` //` ${use_start_time}-${use_end_time}`
				// if(min == max){
				// 	data.wtime = `周${week[max]} ${use_start_time}-${use_end_time}`
				// }
				// if ((use_start_time == '00:00' && use_end_time == '23:59') || (use_start_time == use_end_time)) {
				// 	data.wtime = `周${week[min]}至周${week[max]} 全天可用`
				// 	if(min == max){
				// 		data.wtime = `周${week[max]} 全天可用`
				// 	}
				// }
				let code_info = this.$util.deepCopy(data.code_info)
				code_info.reverse()
				this.code_info = code_info
				this.handlerPrice()
				this.detail = data
				this.isLoad = true
				this.$util.hideAll()
			},
			handlerPrice(){
				let {
					code_info,
					param
				} = this
				let number = 1
				let refund_price = 0
				for(let i = 0; i< code_info.length ; i++){
					if(code_info[i].status == 1 && param.num >= number){
						number += 1
						let {
							goods_price = 0,
							is_integral = 0,
							integral_to_money = 0,
						} = code_info[i]
						refund_price += (is_integral ? Number((goods_price*1 - integral_to_money*1).toFixed(2)) : Number(goods_price))
					}
				}
				this.refund_price = refund_price
				
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
							title: `最多可退${can_refund_num}张`
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
				this.handlerPrice()
			},
			async submit(){
				if(!this.param.text){
					this.$util.showToast({
						title: '请输入退款原因'
					});
					return	
				}
				let {
					ensure
				} = this.detail
				this.$util.showLoading()
				await this.$api.business.applyRefund(this.param)
				this.$util.hideAll()
				this.$util.showToast({
					title: ensure == 1 ? '退款成功' : '提交成功'
				});
				setTimeout(()=>{
					this.$util.getPage(-2).initRefresh()
					this.$util.goUrl({url: 2 ,openType: 'navigateBack'})
				},1000)
			}
		},
	}
</script>

<style lang="scss">
	.refund-pages {
		.refund-img{
			width: 160rpx;
			height: 160rpx;
		}
		.refund-num{
			width: 48rpx;
		}
		.refund-title{
			height: 92rpx;
		}
		.refund-textarea{
			textarea {
				width: calc(100% - 60rpx);
				height: 280rpx;
			}
		}
	}
</style>
