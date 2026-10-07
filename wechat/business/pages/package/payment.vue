<template>
	<view class="pay-pages" v-if="isLoad">
		<view class="success-pages" v-if="status == 2">
			<view class="flex-center icon-box">
				<i class="iconfont icon-xuanze-fill" :style="{color: primaryColor,fontSize: '233rpx'}"></i>
			</view>
			<view class="flex-center f-paragraph">您已支付成功</view>
			<view class="flex-center success-btn-box">
				<view class="success-btn flex-center f-mini-title"
					:style="{border: `1px solid ${primaryColor}`,color: primaryColor}"
					@tap="$util.goUrl({url: `/business/pages/package/order/list`})">查看订单详情</view>
			</view>
		</view>
		<block v-if="status == 1">
			<view class="radius-16 ml-md mr-md mt-md pd-lg fill-base">
				<view class="f-title text-bold">{{detail.name}}</view>
				<view class="flex-y-baseline b-1px-b">
					<view class="f-mini-title pt-lg c-warning pb-lg text-bold">¥{{detail.price}}</view>
					<view class="f-ms-little text-delete pl-sm c-caption" v-if="options.is_seckill">¥{{detail.init_price}}</view>
				</view>
				<view class="pay-number flex-between">
					<text class="f-paragraph text-bold">数量</text>
					<view class="flex-center">
						<i class="iconfont iconjian" style="font-size: 22px;color: #DEDEDE;"
							@tap="changeNum('reduce')"></i>
						<text class="f-paragraph flex-center" style="width: 48rpx;">{{param.num}}</text>
						<i class="iconfont iconjia" style="font-size: 22px;" :style="{color:primaryColor}"
							@tap="changeNum('add')"></i>
					</view>
				</view>
				<blcok v-if="options.is_seckill != 1">
					<view class="flex-between">
						<text class="f-paragraph text-bold">商品总价</text>
						<view class="flex-center f-desc">¥{{param.init_price}}</view>
					</view>
					<view class="pt-lg flex-between">
						<text class="f-paragraph text-bold">套餐优惠</text>
						<view class="flex-center f-desc">-¥{{param.discount}}</view>
					</view>
					<view class="pt-lg flex-between">
						<text class="f-paragraph text-bold">订单总价</text>
						<view class="flex-center f-desc c-warning">¥{{param.price}}</view>
					</view>
					<view class="flex-between">
						<text class="f-paragraph"></text>
						<view class="flex-center f-caption c-warning">(已优惠:¥{{param.discount}})</view>
					</view>
					<view class="pt-lg flex-between" v-if="detail.is_integral && configInfo.plugAuth.integral">
						<text class="f-paragraph text-bold">积分抵扣</text>
						<view class="flex-center f-desc c-warning" 
						v-if="detail.user_integral >= detail.integral">
						{{integral}}积分抵¥{{integral_to_money}}</view>
						<view class="flex-center f-desc c-warning" v-else>积分不足</view>
					</view>
				</blcok>
			</view>
			<view class="pl-lg pr-lg mt-md radius-16 ml-md mr-md flex-center h-130 fill-base">
				<text class="f-mini-title text-bold">手机号码</text>
				<input type="text" class="flex-1 text-right h-130" placeholder="请输入手机号" v-model="param.mobile" />
			</view>
			<view class="mt-md ml-md mr-md fill-base radius-16">
				<view class="text-bold f-mini-title pt-lg pl-lg pb-sm">支付方式</view>
				<view @tap.stop="toChangeItem(index,2)" class="flex-between pt-lg pb-lg pl-lg pr-md"
					v-for="(item,index) in payList" :key="index">
					<view class="flex-y-center f-paragraph c-title">
						<view class="wechat-icon flex-center mr-md" style="background-color: #E7F9EE;"
							v-if="item.id==1">
							<i class="iconfont icon-weixin" style="font-size: 22px;color: #19C865;"></i>
						</view>
						<view class="wechat-icon flex-center mr-md" style="background-color: #f5f5f5;"
							v-else-if="item.id==2">
							<i class="iconfont" :class="item.icon" style="font-size: 22px;"></i>
						</view>
						<i class="iconfont mr-md" :class="item.icon" v-else :style="{fontSize:'70rpx'}"></i>
						{{item.title}}
						<view class="f-paragraph c-caption ml-md" v-if="item.id == 2">余额{{balance || 0}}元</view>
					</view>
					<view class="flex-y-center c-caption" :style="{color:payInd == index ? primaryColor:''}">
						<i class="pay-icon iconfont icon-xuanze mr-sm"
							:class="[{'icon-radio-fill':item.is_disabled || payInd == index}]"></i>
					</view>
				</view>
			</view>
			<view class="fill-base radius-16 pd-lg ml-md mr-md mt-md">
				<view class="f-mini-title text-bold pb-lg">购买须知</view>
				<view class="f-desc text-bold">有效期</view>
				<view class="pt-sm flex-y-center">
					<text class="mr-sm notice-left"></text>{{detail.tday}}
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
				<view v-else class="f-paragraph" style="white-space:pre-wrap">
					{{detail.rule_text}}
				</view>
			</view>
			<!-- <view class="fill-base radius-16 pd-lg ml-md mr-md mt-md">
				<parser :html="configInfo.trading_rules" @linkpress="linkpress" show-with-animation lazy-load>加载中...
				</parser>
			</view> -->
			<view class="space-max-footer"></view>
			<fixed position="bottom" :zIndex="99">
				<view class="fill-base b-1px-t">
					<view class="flex-between bottom-btn">
						<view class="flex-y-baseline">
							<text class="f-title text-bold c-warning">¥{{param.pay_price < 0 ? 0 : param.pay_price}}</text>
							<text class="c-icontext f-caption pl-sm init-price">¥{{param.init_price}}</text>
						</view>
						<view class="panic-buying f-paragraph text-bold c-base flex-center"
							:style="{background: primaryColor}" @tap="toPay(1)">立即支付</view>
						<!--立即抢购-->
					</view>
					<view class="space-safe"></view>
				</view>
			</fixed>
		</block>
		
		<uni-popup ref="show_payment_tips" type="center" :custom="true" :maskClick="false">
			<view class="popup-payment radius-16 pl-lg pr-lg fill-base">
				<view class="rel popup-payment-title flex-center">
					<text class="f-title text-bold">下单说明</text>
					<!-- <text class="f-paragraph c-paragraph abs popup-payment-close" @tap="$refs.show_payment_tips.close()">取消</text> -->
				</view>
				<view class="c-title f-paragraph text-center popup-payment-cont">我方提供服务为正规绿色服务</view>
				<view class="flex-center">
					<view class="popup-payment-btn flex-center" @tap="toPay(2)" :style="{color: primaryColor, borderColor: primaryColor}">我已知晓</view>
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import parser from "@/components/jyf-Parser/index"
	import siteInfo from '@/siteinfo.js';
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},
				isLoad: false,
				param: {
					package_id: '',
					num: 1,
					mobile: '',
					pay_model: '', // 1微信2余额3支付宝
					share_user_id: '',

					init_price: '',
					discount: '',
					price: '',
					pay_price: ''
				},
				// 1微信支付；2余额支付；3支付宝支付
				payList: [{
					id: 1,
					icon: 'iconweixinzhifu1',
					title: '微信支付'
				}, {
					id: 2,
					icon: 'iconqianbao c-balance',
					title: '账户余额',
					is_disabled: false
				}],
				payInd: 0,
				balanceInd: 1,
				balance: 0,
				detail: {},
				lockTap: false,
				status: 1, //1待支付 2支付成功 
				integral: 0,
				integral_to_money: 0,
				integral_money: 0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			mineInfo: state => state.user.mineInfo,
			commonOptions: state => state.user.commonOptions,
		}),
		onLoad(options) {
			this.param.package_id = options.id
			this.param.share_user_id = this.commonOptions.pid || 0
			this.options = options
			this.$util.showLoading()
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getMineInfo']),
			...mapMutations(['updateUserItem', 'updateOrderItem']),
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
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				if (this.configInfo.balance_character) {
					this.payList.forEach(item => {
						if (item.id == 2) {
							item.title = this.configInfo.balance_character
						}
					})
				}
				await this.getMineInfo()
				await this.getInfo()
			},
			async getInfo() {
				let {
					id,
					is_seckill
				} = this.options
				let data = await this.$api.business.storePackInfo({
					id,
					is_seckill
				})
				data.banner = data.imgs.split(',')
				let {
					use_start_time,
					use_end_time,
					use_trade_week,
					reservation_day,
					term_type,
					days,
					term_start_time,
					term_end_time,
					sku,
					use_type,
					store
				} = data
				let rday = reservation_day > 0 ? '需预约' : '无需预约'
				let tday = term_type == 2 ? `购买后${days}天内有效` : this.$util.formatTime(term_end_time * 1000, 'YY-M-D') +
					'结束'
				let max = use_trade_week.substring(use_trade_week.length - 1)
				let min = use_trade_week.substring(0, 1)
				if (use_type == 1) {
					max = store.trade_week.substring(store.trade_week.length - 1)
					min = store.trade_week.substring(0, 1)
					use_start_time = store.start_time
					use_end_time = store.end_time
				}
				let week = ['日', '一', '二', '三', '四', '五', '六'];
				data.rday = rday
				data.tday = term_type == 2 ? `购买后${days}天内有效` : this.$util.formatTime(term_start_time * 1000,
					'YY-M-D') + ' 至 ' + this.$util.formatTime(term_end_time * 1000, 'YY-M-D')
				data.wtime = `周${week[min]}至周${week[max]} ${use_start_time}-${use_end_time}`
				if (min == max) {
					data.wtime = `周${week[max]} ${use_start_time}-${use_end_time}`
				}
				if ((use_start_time == '00:00' && use_end_time == '23:59') || (use_start_time == use_end_time)) {
					data.wtime = `周${week[min]}至周${week[max]} 全天可用`
					if (min == max) {
						data.wtime = `周${week[max]} 全天可用`
					}
				}
				data.discount = data.init_price * 1 - data.price * 1 || 0
				data.day_week = `${rday}·${data.wtime}·${tday}`
				this.param.init_price = Number(data.init_price.toFixed(2))
				this.param.discount = Number(data.discount.toFixed(2))
				this.param.price = Number(data.price.toFixed(2))

				let {
					alipay_status = 0
				} = this.configInfo

				if (alipay_status) {
					// #ifndef MP-WEIXIN
					let pay = this.payList.findIndex(item => {
						return item.id == 3
					})
					if (pay === -1) {
						this.payList.splice(1, 0, {
							id: 3,
							icon: 'icon-alipay-fill c-alipay',
							title: '支付宝支付'
						})
						this.balanceInd = 2
					}
					// #endif
				}
				let {
					balance
				} = this.mineInfo
				let {
					balanceInd
				} = this

				this.payList[balanceInd].is_disabled = balance * 1 < data.price * 1
				this.balance = balance


				this.detail = data
				this.hannelIntegral()
				this.isLoad = true
				this.$util.hideAll()
			},
			hannelIntegral(){
				let {
					user_integral = 0,
					integral = 0,
					integral_to_money = 0,
					is_integral = 0
				} = this.detail
				let {
					num = 1,
					price
				} = this.param
				if(!is_integral || !this.configInfo.plugAuth.integral){
					this.param.pay_price = Number(price) || 0
					return
				}
				if(user_integral >= integral*num && this.options.is_seckill != 1){
					this.integral_money = (integral_to_money * num).toFixed(1)
					this.integral_to_money = this.integral_money
					this.integral = (integral * num).toFixed(0)
				}
				this.param.pay_price = Number((price*1 - this.integral_money).toFixed(2)) || 0
			},
			// 选择出行方式/支付方式/服务方式
			async toChangeItem(index, key = 1) {
				let {
					balanceInd
				} = this
				if (index == balanceInd && this.payList[balanceInd].is_disabled) return
				this.payInd = index
			},
			changeNum(type) {
				let {
					init_price,
					discount,
					price
				} = this.param

				if (type == 'add') {
					this.param.num++
					this.param.discount = Number((this.detail.discount * this.param.num).toFixed(2))
					this.param.init_price = Number((this.detail.init_price * this.param.num).toFixed(2))
					this.param.price = Number((this.detail.price * this.param.num).toFixed(2))
				} else {
					if (this.param.num == 1) {
						this.$util.showToast({
							title: '最少1份'
						});
						return
					}
					this.param.num--
					this.param.discount = Number((this.detail.discount * this.param.num).toFixed(2))
					this.param.init_price = Number((this.detail.init_price * this.param.num).toFixed(2))
					this.param.price = Number((this.detail.price * this.param.num).toFixed(2))
				}
				let {
					balance
				} = this.mineInfo
				let {
					balanceInd,
					payInd
				} = this

				this.payList[balanceInd].is_disabled = balance * 1 < this.param.price

				if (balance * 1 < this.param.price && payInd == balanceInd) {
					this.payInd = 0
				}
				this.hannelIntegral()
			},
			async toPay(type) {
				let param = this.$util.deepCopy(this.param)
				param.pay_model = this.payList[this.payInd].id
				let pay_model = this.payList[this.payInd].id
				if (!param.mobile) {
					this.$util.showToast({
						title: '请输入手机号'
					});
					return
				}
				if (!/^(1[0-9]{10})$/.test(param.mobile)) {
					this.$util.showToast({
						title: `${param.mobile} 手机号无效`
					});
					return
				}
				param.is_seckill = this.options.is_seckill || 0
				if(type == 1){
					this.$refs.show_payment_tips.open()
					return
				}else{
					this.$refs.show_payment_tips.close()
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					let {
						pay_list,
						order_id = 0
					} = await this.$api.business.payOrder(param)
					this.$util.hideAll()
					// 微信/支付宝支付
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
								order_id,
								page_url: `/business/pages/package/order/list?tab=2`
							})
							this.updateOrderItem({
								key: 'alipayOrderParams',
								val: pay_list
							})
							this.$util.goUrl({
								url: '/user/pages/alipay-result',
								openType: `redirectTo`
							})
							return
						}
						// #endif

						try {
							await this.$util.pay(pay_list)
							this.$util.showToast({
								title: `支付成功`
							})
							setTimeout(() => {
								this.lockTap = false
								// this.$util.goUrl({
								// 	url: '/business/pages/package/success',
								// 	openType: `reLaunch`
								// })
								this.status = 2
							}, 1000)
						} catch (e) {
							this.$util.showToast({
								title: `支付失败`
							})
							setTimeout(() => {
								this.lockTap = false
								this.$util.goUrl({
									url: '/business/pages/package/order/list?tab=1',
									openType: `reLaunch`
								})
							}, 1000)
						}
						return
					}
					// 余额支付/零元支付
					this.$util.showToast({
						title: `支付成功`
					})
					setTimeout(() => {
						this.lockTap = false
						// this.$util.goUrl({
						// 	url: '/business/pages/package/success',
						// 	openType: `reLaunch`
						// })
						this.status = 2
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
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
	.pay-pages {
		.pay-number {
			height: 110rpx;
		}

		.h-130 {
			height: 130rpx;
		}

		.wechat-icon {
			width: 70rpx;
			height: 70rpx;
			border-radius: 70rpx;
		}

		.bottom-btn {
			height: 130rpx;
			padding: 0 40rpx;

			.panic-buying {
				width: 283rpx;
				height: 90rpx;
				border-radius: 90rpx;
			}

			.init-price {
				text-decoration-line: line-through;
			}
		}
	}

	.success-pages {
		.icon-box {
			padding-top: 127rpx;
		}

		.success-btn-box {
			padding-top: 90rpx;

			.success-btn {
				width: 285rpx;
				height: 92rpx;
				border-radius: 92rpx;

			}
		}
	}
	
	.popup-payment{
		width: 670rpx;
		height: 384rpx;
		.popup-payment-title{
			height: 126rpx;
		}
		.popup-payment-close{
			right: 0;
		}
		.popup-payment-cont{
			height: 128rpx;
		}
		.popup-payment-btn{
			width: 187rpx;
			height: 66rpx;
			border-radius: 66rpx;
			border: 1px solid transparent;
		}
	}
</style>
