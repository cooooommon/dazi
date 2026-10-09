<template>
	<view class="order-pages" v-if="orderInfo.coach_id">

		<view class="mt-lg ml-lg mr-lg fill-base radius-16" v-if="configInfo.plugAuth.store && orderInfo.is_store == 1">
			<view class="flex-between pd-lg">
				<view class="f-title c-title text-bold">服务方式</view>
				<view class="flex-center">
					<view @tap.stop="toChangeItem(index,3)" class="flex-center service-type-item c-caption"
						:class="[{'ml-lg':index!=0}]"
						:style="{background:serviceTypeInd == index ? primaryColor:'',color:serviceTypeInd == index ? '#fff':''}"
						v-for="(item,index) in serviceTypeList" :key="index">{{item.title}}
					</view>
				</view>
			</view>
		</view>

		<view v-if="orderInfo.order_goods && orderInfo.order_goods.length > 0">

			<block v-if="serviceTypeList[serviceTypeInd].id === 1">

				<view class="store-info mt-md ml-lg mr-lg pd-lg  fill-base radius-16">
					<view class="f-mini-title c-title text-bold pb-md">
						{{orderInfo.store_info.title}}
					</view>
					<view class="flex-between">
						<view class="flex-y-center" style="color: #303030;">
							<i class="iconfont icondizhi1 mr-sm"></i>
							<view class="c-title flex-1 mr-md">
								<span>{{orderInfo.store_info.address || `暂未设置门店地址`}}</span>
								<span @tap.stop="$util.goUrl({url:orderInfo.store_info.address,openType:'copy'})"
									class="store-copy-btn radius-5 f-icontext ml-sm"
									:style="{color:primaryColor,borderColor:primaryColor}"
									v-if="orderInfo.store_info.address">复制</span>
							</view>
						</view>
						<view class="flex-center">
							<view @tap.stop="$util.goUrl({url:orderInfo.store_info.phone,openType:'call'})"
								class="item-icon rel flex-center radius-16">
								<view class="item-icon radius-16 abs" :style="{background:primaryColor}"></view>
								<i class="iconfont icondadianhua_1" :style="{color:primaryColor}"></i>
							</view>
							<view @tap.stop="toMap" class="item-icon rel flex-center radius-16 ml-md"
								v-if="orderInfo.store_info.address">
								<view class="item-icon radius-16 abs" :style="{background:primaryColor}"></view>
								<i class="iconfont icondizhi_1" :style="{color:primaryColor}"></i>
							</view>
						</view>
					</view>
				</view>

				<view class="mt-md ml-lg mr-lg pl-lg pr-lg fill-base radius-16">
					<view class="flex-between b-1px-b">
						<view class="item-text flex-y-center"><i class="iconfont icon-required c-warning"></i>姓名</view>
						<input v-model="form.user_name" type="text" class="item-input flex-1" maxlength="20"
							:placeholder="rule[0].errorMsg" />
					</view>
					<view class="flex-between">
						<view class="item-text flex-y-center"><i class="iconfont icon-required c-warning"></i>手机号</view>
						<input v-model="form.user_phone" type="text" class="item-input flex-1"
							:placeholder="rule[1].errorMsg" />
					</view>
				</view>

			</block>

			<view @tap.stop="$util.goUrl({url:`/user/pages/address/list?check=1`})"
				class="address-info flex-warp mt-md ml-lg mr-lg pd-lg fill-base radius-16"
				v-if="serviceTypeList[serviceTypeInd].id === 0">
				<view class="address-icon flex-center c-base radius"
					:style="{background:`linear-gradient(to right, ${subColor}, ${primaryColor})`}"><i
						class="iconfont iconjuli"></i></view>
				<view class="flex-1 flex-between ml-md">
					<view class="max-500">
						<block v-if="orderInfo.address_info.id">
							<view class="flex-y-baseline username c-title text-bold">
								{{orderInfo.address_info.user_name}}
								<view class="ml-md f-desc c-caption">{{orderInfo.address_info.mobile}}</view>
							</view>
							<view class="f-desc c-title ellipsis">
								{{`${orderInfo.address_info.address} ${orderInfo.address_info.address_info}`}}
							</view>
						</block>
						<block v-else>
							<view class="username c-title text-bold">请选择地址</view>
						</block>
					</view>
					<i class="iconfont icon-right"></i>
				</view>
			</view>


			<view class="mt-md ml-lg mr-lg fill-base radius-16">
				<view @tap.stop="toShowTime" class="flex-between pt-lg pb-lg pl-lg pr-md">
					<view class="f-title c-title text-bold">服务时间</view>
					<view class="flex-y-center f-paragraph c-caption ml-sm">
						<view class="c-caption mr-sm">{{send_info.time || '请选择预约时间'}}</view>
						<i class="iconfont icon-right"></i>
					</view>
				</view>
				<view class="flex-between pd-lg b-1px-t" v-if="serviceTypeList[serviceTypeInd].id === 0 && showCar">
					<view class="f-title c-title text-bold">出行方式</view>
					<view class="flex-center">
						<view @tap.stop="toChangeItem(index)" class="flex-y-center" :class="[{'ml-lg':index!=0}]"
							:style="{color:carTypeInd == index ? primaryColor:''}" v-for="(item,index) in carTypeList"
							v-show="item.id == 0 ? isBus == 1 : true" :key="index"><i class="iconfont icon-xuanze mr-sm"
								:class="[{'icon-radio-fill':carTypeInd == index}]"></i>{{item.title}}
						</view>
					</view>
				</view>
			</view>
			<view class="mt-md ml-lg mr-lg  fill-base radius-16">
				<view class="list-item flex-center pd-lg" :class="[{'b-1px-t':index!=0}]"
					v-for="(item,index) in orderInfo.order_goods" :key="index">
					<!-- #ifdef H5 -->
					<view class="item-img radius-16">
						<view class="h5-image item-img radius-16" :style="{ backgroundImage : `url('${item.cover}')`}">
						</view>
					</view>
					<!-- #endif -->
					<!-- #ifndef H5 -->
					<image mode="aspectFill" class="item-img radius-16" :src="item.cover"></image>
					<!-- #endif -->
					<view class="flex-1 ml-md">
						<view class="f-title c-title text-bold ellipsis">{{item.title}}</view>
						<view class="flex-y-center f-desc c-caption">
							<view class="f-title c-warning mr-sm">¥{{item.price}}</view>/ {{item.time_long}}分钟
						</view>
						<view class="flex-between">
							<view class="f-caption c-caption mt-sm mb-sm max-300">
								服务{{$t('action.attendantName')}}：{{orderInfo.coach_info.coach_name}}
							</view>
							<view class="flex-warp">
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
			</view>
			<view class="mt-md ml-lg mr-lg fill-base radius-16">
				<view class="flex-between pd-lg b-1px-b" @tap="$util.goUrl({url: `/user/pages/technician-info?id=${orderInfo.coach_info.id}`})">
					<image class="coach-header" :src="orderInfo.coach_info.work_img" mode="aspectFill"></image>
					<text class="f-title text-bold">{{orderInfo.coach_info.coach_name}}</text>
				</view>
				<view @tap.stop="$util.goUrl({url:`/user/pages/coupon/use`})"
					class="flex-between pt-lg pb-lg pl-lg pr-md"
					:class="[{'b-1px-b':carTypeList[carTypeInd].id === 1}]">
					<view class="f-title c-title text-bold">卡券优惠</view>
					<view class="flex-y-center f-paragraph c-caption ml-sm">
						<view class="c-warning mr-sm">
							{{orderInfo.coupon_id ? `-¥${orderInfo.discount}` : `${orderInfo.canUseCoupon}张可用`}}
						</view>
						<i class="iconfont icon-right"></i>
					</view>
				</view>
				<view @tap.stop="toUseDiscountCard" class="flex-between pl-lg pr-lg pt-lg pb-lg b-1px-t"
					v-if="orderInfo.member_auth"> 
					<view class="f-title c-title text-bold">会员折扣</view>
					<view class="flex-y-center f-paragraph c-caption">
						<view
							:class="[{'c-warning':orderInfo.member_balance*1>0 || orderInfo.member_discount*1>0}]"
							v-if="orderInfo.member_status">
							{{(orderInfo.member_balance*1>0 || orderInfo.member_discount*1>0)  ? `${orderInfo.member_balance}折 (优惠¥${orderInfo.member_discount})` : '-'}}
						</view>
						<view class="flex-y-center" v-if="!orderInfo.member_status">
							<span class="flex-center f-icontext c-base mr-sm"
								style="min-width:30rpx;height:34rpx;padding:0 12rpx; background: #F13F26;border-radius: 8rpx;">{{orderInfo.coupon_id?'无':'待使用'}}</span>{{orderInfo.coupon_id?'会员优惠与卡券不可同时使用':'点击使用会员优惠'}}
						</view>
					</view>
				</view>
				<block v-if="serviceTypeList[serviceTypeInd].id === 0 && carTypeList[carTypeInd].id === 1 && showCar">
					<view class="flex-between pd-lg">
						<view class="f-title c-title text-bold">往返车费</view>
						<view class="f-paragraph c-caption c-warning">¥{{orderInfo.car_price}}</view>
					</view>
					<view class="pl-lg pr-lg pb-lg f-caption c-caption">
						全程共{{orderInfo.distance}}，出租出行{{orderInfo.car_config.start_distance}}公里内，起步{{orderInfo.car_config.start_price}}元。里程计价：{{orderInfo.car_config.distance_price}}元/公里
					</view>
				</block>
			</view>

			<view class="mt-md ml-lg mr-lg fill-base radius-16">
				<view @tap.stop="toChangeItem(index,2)" class="flex-between pt-lg pb-lg pl-lg pr-md b-1px-b"
					v-for="(item,index) in payList" :key="index">
					<view class="flex-y-center f-title c-title">
						<i class="iconfont mr-md" :class="item.icon"
							:style="{color:item.id==1?primaryColor:'',fontSize:'50rpx'}"></i>
						{{item.title}}
						<view class="f-paragraph c-caption ml-md" v-if="item.id == 2">余额{{balance || 0}}元</view>
					</view>
					<view class="flex-y-center c-caption" :style="{color:payInd == index ? primaryColor:''}">
						<i class="pay-icon iconfont icon-xuanze mr-sm"
							:class="[{'icon-radio-fill':item.is_disabled || payInd == index}]"></i>
					</view>
				</view>
			</view>
			
			
			<view class="mt-md ml-lg mr-lg pd-lg fill-base radius-16">
				<view class="flex-between pb-lg">
					<view class="flex-y-baseline">
						<view class="f-title c-title text-bold">订单备注</view>
						<view class="f-paragraph c-caption ml-sm">(选填)</view>
					</view>
				</view>
				<view class="f-caption c-caption fill-body radius-16">
					<textarea v-model="form.text" class="item-textarea f-paragraph pd-lg"
						placeholder-class="f-paragraph" maxlength="100" placeholder="输入订单备注" />
					<view class="text-right pb-lg pr-lg">{{form.text.length>100?100:form.text.length}}/100
					</view>
				</view>
			</view>


			<view @tap.stop="isAgree=!isAgree"
				class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-title flex-y-center radius-16"
				v-if="configInfo.trading_rules && configInfo.trading_rules.length>0">
				<i class="iconfont mr-sm" :class="isAgree?'icon-xuanze-fill':'icon-xuanze'"
					:style="{color:isAgree?primaryColor:''}"></i>
				我已阅读并同意<view @tap.stop="$refs.show_rule_item.open()" :style="{color:primaryColor}">《平台交易规则》</view>
			</view>

			<view class="space-max-footer"></view>

			<view class="pay-info fix flex-between text-right pl-lg pr-lg fill-base">
				<view class="flex-y-center f-paragraph c-title text-bold ml-sm mr-lg">合计：
					<view class="flex-y-baseline f-title c-warning">¥{{orderInfo.pay_price}}</view>
				</view>
				<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toPay(1)" style="width: 182rpx;">
					<view class="pay-btn flex-center f-paragraph c-base radius" :style="{background:primaryColor}">
						立即支付</view>
				</auth>
			</view>

		</view>








		<abnor @confirm="$util.goUrl({url:`/pages/service`,openType:`reLaunch`})" :tip="[{ text: '该服务已下架~', color: 0 }]"
			:button="[{ text: '去看看其他服务' , type: 'confirm' }]" btnSize="" v-else>
		</abnor>

		<uni-popup ref="show_rule_item" type="center" :maskClick="false">
			<view class="popup-rule">
				<view class="fill-base pd-lg radius-26">
					<view class="f-title c-title text-bold flex-center pd-lg">平台交易规则</view>
					<scroll-view scroll-y @touchmove.stop.prevent class="rule-text">
						<parser :html="configInfo.trading_rules" @linkpress="linkpress" show-with-animation lazy-load>
							加载中...
						</parser>
					</scroll-view>
				</view>
				<view @tap="$refs.show_rule_item.close()" class="flex-center pd-lg"><i
						class="iconfont icon-close c-base"></i></view>
			</view>
		</uni-popup>
		<uni-popup ref="show_time_item" type="bottom">
			<view class="popup-time fill-base">
				<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1" :activeColor="primaryColor"
					height="100rpx"></tab>
				<scroll-view scroll-y @touchmove.stop.prevent class="time-list">
					<view class="flex-warp">
						<view @tap.stop="toChooseTime(index)" class="time-item flex-center flex-column"
							:class="[{'can-choose':item.status && send_info.time_str != item.time_str},{'cur-check':send_info.time_str == item.time_str}]"
							:style="{background:send_info.time_str == item.time_str ?primaryColor:''}"
							v-for="(item,index) in timeList" :key="index">
							<view class="f-title">{{item.time_text}}</view>
							<view class="f-caption">{{item.status?'可预约':'不可预约'}}</view>
						</view>
					</view>
				</scroll-view>
				<view class="space-safe"></view>
			</view>
		</uni-popup>
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
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},
				serviceTypeList: [{
					id: 0,
					title: '上门服务'
				}, {
					id: 1,
					title: '到店服务'
				}],
				showCar: 1, // 是否计车费
				serviceTypeInd: 0,
				carTypeList: [{
					id: 1,
					title: '出租车'
				}, {
					id: 0,
					title: '公交/地铁'
				}],
				carTypeInd: 0,
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
				tabList: [],
				activeIndex: 0,
				timeList: [],
				send_info: {},
				orderInfo: {
					coupon_id: 0,
					address_info: {
						id: 0
					},
					member_status: 0,
					is_remember: 0,
				},
				form: {
					user_name: '',
					user_phone: '',
					text: ''
				},
				rule: [{
						name: "user_name",
						checkType: "isNotNull",
						errorMsg: "请输入您的姓名",
						regType: 2
					},
					{
						name: "user_phone",
						checkType: "isMobile",
						errorMsg: "请输入手机号"
					}
				],
				lockTap: false,
				isBus: 0,
				isAgree: false,
				is_member: 0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
			carList: state => state.order.carList,
		}),
		watch: {
			'send_info.time'(value) {
				let {
					time_str: start_time
				} = this.send_info
				this.getIsBusCall(start_time)
			}
		},
		async onLoad(options) {
			this.options = options
			this.$util.showLoading()
			await this.initIndex()
			let {
				time = 0
			} = this.send_info
			if (!time) {
				this.getIsBusCall('')
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getMineInfo', 'getCarList', 'updateCommonOptions']),
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
				await this.getConfigInfo()
				if(this.configInfo.balance_character){
					this.payList.forEach(item => {
						if(item.id == 2){
							item.title = this.configInfo.balance_character
						}
					})
				}
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
							icon: 'icon-alipay c-alipay',
							title: '支付宝支付'
						})
						this.balanceInd = 2
					}
					// #endif
				}
				let {
					id: coach_id,
					ser_id = 0
				} = this.options
				let {
					coupon_id,
					address_info
				} = this.orderInfo
				let {
					id: address_id
				} = address_info
				if (!address_id) {
					let address_info = await this.$api.mine.getDefultAddress()
					address_id = address_info && address_info.id ? address_info.id : null
				}
				let car_type = this.carTypeList[this.carTypeInd].id
				let {
					id: is_store = 0
				} = this.serviceTypeList[this.serviceTypeInd]
				let {
					is_member
				} = this
				let [data, mineInfo] = await Promise.all([this.$api.order.payOrderInfo({
					is_store,
					service_id: ser_id,
					coach_id,
					car_type,
					coupon_id: coupon_id,
					address_id,
					is_member
				}), this.getMineInfo()])
				this.$util.hideAll()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				data.address_info = data.address_info.id ? data.address_info : {},
				data.member_discount = Number(data.member_discount.toFixed(2))
				this.orderInfo = data
				let {
					near_time = {
						str: '',
						text: ''
					}
				} = data

				let {
					text: time = '',
					str: time_str = ''
				} = near_time

				let {
					time: send_info_time = 0
				} = this.send_info
				if (!send_info_time && time && time_str) {
					let dat_text = this.$util.formatTime(time_str * 1000, 'M-D')
					this.send_info = {
						time,
						time_str,
						dat_text
					}
				}
				let {
					balance
				} = this.mineInfo
				let {
					balanceInd
				} = this
				this.payList[balanceInd].is_disabled = balance * 1 < data.pay_price * 1
				this.balance = balance
				this.showCar = data.order_goods[0].is_car
			},
			initRefresh() {
				this.initIndex(true)
			},
			linkpress(res) {
				// #ifdef APP-PLUS
				this.$util.goUrl({
					url: res.href,
					openType: 'web'
				})
				// #endif
			},
			async getIsBusCall(start_time) {
				let {
					serviceTypeList,
					serviceTypeInd
				} = this
				let {
					id: is_store = 0
				} = serviceTypeList[serviceTypeInd]
				let isBus = is_store ? 1 : await this.$api.order.getIsBus({
					start_time
				})
				this.isBus = isBus
				if (isBus == 0) {
					this.carTypeInd = 0
					await this.initRefresh()
				}
			},
			// 选择出行方式/支付方式/服务方式
			async toChangeItem(index, key = 1) {
				let {
					address_info
				} = this.orderInfo
				switch (key) {
					case 1:
						if (index == 1 && !address_info.id) {
							this.$util.showToast({
								title: `请选择地址`
							})
							return
						}
						if (index == 1 && !this.send_info.time) {
							this.$util.showToast({
								title: `请选择预约时间`
							})
							return
						}
						this.carTypeInd = index
						await this.initRefresh()
						break
					case 2:
						let {
							balanceInd
						} = this
						if (index == balanceInd && this.payList[balanceInd].is_disabled) return
						this.payInd = index
						break
					case 3:
						this.serviceTypeInd = index
						this.carTypeInd = index == 1 ? 1 : 0
						await this.initRefresh()
						break
				}
			},
			// 加/减数量
			async changeNum(mol, index) {
				let {
					id,
					service_id,
					min_num,
					num
				} = this.orderInfo.order_goods[index]
				let {
					id: coach_id
				} = this.options
				if(mol < 0 && num == min_num){
					this.$util.showToast({
						title: `起购数量最小为${min_num}`
					})
					return
				}
				if (this.lockTap) return;
				this.lockTap = true;
				let methodModel = mol > 0 ? 'addCar' : 'delCar'
				let param = mol > 0 ? {
					service_id,
					coach_id,
					num: 1,
					is_add: 1
				} : {
					id,
					num: 1,
					is_add: 1
				}
				try {
					let changeNum = await this.$api.order[methodModel](param)
					// await this.getCarList({
					// 	coach_id
					// })
					// await this.initRefresh()
					let {
						id: coach_id,
						ser_id = 0
					} = this.options
					let {
						coupon_id,
						address_info
					} = this.orderInfo
					let {
						id: address_id
					} = address_info
					if (!address_id) {
						let address_info = await this.$api.mine.getDefultAddress()
						address_id = address_info && address_info.id ? address_info.id : null
					}
					let car_type = this.carTypeList[this.carTypeInd].id
					let {
						id: is_store = 0
					} = this.serviceTypeList[this.serviceTypeInd]
					let {
						is_member
					} = this
					let [data] = await Promise.all([this.$api.order.payOrderInfo({
						is_store,
						service_id: ser_id,
						coach_id,
						car_type,
						coupon_id,
						address_id,
						is_member
					})])
					data.address_info = data.address_info.id ? data.address_info : {},
					data.member_discount = Number(data.member_discount.toFixed(2))
					this.orderInfo = data
					this.lockTap = false
				} catch (e) {
					this.lockTap = false
				}
			},
			async toShowTime() {
				await this.getStoreDay()
				this.$refs.show_time_item.open()
			},
			async getStoreDay() {
				let data = await this.$api.order.dayText()
				data.map(item => {
					item.title = `${item.dat_text} ${item.week}`
				})
				this.tabList = data
				let {
					dat_text = ''
				} = this.send_info
				if (dat_text) {
					this.activeIndex = this.tabList.findIndex(item => {
						return item.dat_text == dat_text
					})
				}
				await this.getStoreTime()
				this.loading = false
				this.$util.hideAll()
			},
			async getStoreTime() {
				let {
					id: coach_id
				} = this.options
				let {
					activeIndex
				} = this
				if (activeIndex < 0) return
				let {
					dat_str: day
				} = this.tabList[activeIndex]
				let {
					id: is_store = 0
				} = this.serviceTypeList[this.serviceTypeInd]
				this.timeList = await this.$api.order.timeText({
					is_store,
					coach_id,
					day
				});
				this.loading = false
				this.$util.hideAll()
			},
			handerTabChange(index) {
				this.activeIndex = index
				this.getStoreTime()
			},
			toChooseTime(index) {
				let {
					tabList,
					activeIndex
				} = this
				let item = this.timeList[index]
				if (item.status != 1) return
				item.time = `${tabList[activeIndex].dat_text} ${tabList[activeIndex].week} ${item.time_text}`
				this.send_info = item
				this.$refs.show_time_item.close()
			},
			checkInput(e, key) {
				let val = this.$util.formatMoney(e.detail.value)
				this.$nextTick(() => {
					this.form[key] = val
				})
			},
			toUseDiscountCard() {
				this.orderInfo.coupon_id = 0
				this.orderInfo.member_status = 1
				this.is_member = 1
				let {
					id
				} = this.payList[this.payInd]
				if (id == 4) {
					this.payInd = this.payList.findIndex(item => {
						return item.id == 1
					})
				}
				this.initRefresh()
			},
			// 查看定位
			async toMap() {
				let {
					address,
					lat,
					lng
				} = this.orderInfo.store_info
				if (!address) return
				await this.$util.checkAuth({
					type: 'userLocation'
				})
				await uni.getLocation({
					type: 'gcj02',
				})
				await uni.openLocation({
					latitude: lat * 1,
					longitude: lng * 1,
					name: address,
					scale: 28
				})
			},
			//表单验证
			validate(param) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					validate.add(param[name], item);
				})
				let message = validate.start();
				return message;
			},
			async toPay(type) {
				let param = this.$util.deepCopy(this.form)
				let {
					id: is_store = 0
				} = this.serviceTypeList[this.serviceTypeInd]
				if (is_store) {
					let msg = this.validate(param);
					if (msg) {
						this.$util.showToast({
							title: msg
						});
						return
					}
				} else {
					delete param.user_name
					delete param.user_phone
				}
				let {
					coupon_id = 0,
						address_info = {}
				} = this.orderInfo
				let {
					id: address_id
				} = address_info
				if (!is_store && !address_id) {
					this.$util.showToast({
						title: `请选择地址`
					})
					return
				}
				let {
					carTypeList,
					carTypeInd,
					payList,
					payInd,
					send_info
				} = this
				if (!send_info.time) {
					this.$util.showToast({
						title: `请选择预约时间`
					})
					return
				}
				let {
					trading_rules
				} = this.configInfo
				if (trading_rules && trading_rules.length > 0 && !this.isAgree) {
					this.$util.showToast({
						title: `请先阅读并同意《平台交易规则》`
					})
					return
				}
				let {
					id: car_type
				} = carTypeList[carTypeInd]
				let {
					id: pay_model
				} = payList[payInd]
				let {
					id: coach_id
				} = this.options
				let {
					time_str: start_time
				} = send_info

				let {
					channel_id = 0,
					channel_staff_id = 0
				} = this.commonOptions
				param = Object.assign({}, param, {
					coach_id,
					coupon_id,
					address_id,
					is_store,
					car_type,
					pay_model,
					start_time,
					channel_id,
					channel_staff_id
				})
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
					} = await this.$api.order.payOrder(param)
					this.$util.hideAll()
					this.updateCommonOptions({channel_id: '',channel_staff_id: '' })
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
								page_url: `/pages/order?tab=2`
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
								this.$util.goUrl({
									url: '/pages/order?tab=2',
									openType: `reLaunch`
								})
							}, 1000)
						} catch (e) {
							setTimeout(() => {
								this.lockTap = false
								this.$util.goUrl({
									url: '/pages/order?tab=1',
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
						this.$util.goUrl({
							url: '/pages/order?tab=2',
							openType: `reLaunch`
						})
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			}
		}
	}
</script>


<style lang="scss">
	.order-pages {
		.service-type-item {
			width: 143rpx;
			height: 59rpx;
			background: #F5F5F5;
			border-radius: 8rpx
		}

		.item-text {
			width: 200rpx;
			height: 30rpx;
			line-height: 30rpx;
			font-size: 30rpx;
			color: #1F1F1F;
		}

		.item-input {
			min-height: 30rpx;
			line-height: 30rpx;
			padding: 25rpx 0;
			font-size: 26rpx;
			color: #A9A9A9;
		}

		.item-input.text {
			padding: 30rpx 0;
		}

		.list-item {
			.item-img {
				width: 140rpx;
				height: 140rpx;
			}

			.ellipsis {
				max-width: 466rpx;
			}

			.item-tag {
				width: 100rpx;
				height: 36rpx;
				margin-top: -18rpx;
			}

			.iconyduixingxingshixin {
				font-size: 28rpx;
			}

			.item-btn {
				width: 129rpx;
				height: 54rpx;
			}
		}

		.pay-info {
			height: 110rpx;
			bottom: 0;
			height: calc(110rpx + env(safe-area-inset-bottom) / 2);
			padding-bottom: calc(env(safe-area-inset-bottom) / 2);

			.pay-btn {
				width: 182rpx;
				height: 74rpx;
			}
		}

		.popup-rule {
			width: 680rpx;
			height: auto;

			.rule-text {
				min-height: 300rpx;
				max-height: 60vh;
			}

			.iconfont {
				font-size: 60rpx;
			}
		}
		.coach-header{
			width: 94rpx;
			height: 94rpx;
			border-radius: 4rpx;
		}

		.popup-time {
			.time-list {
				width: calc(100% - 20rpx);
				max-height: 70vh;
				padding: 30rpx 10rpx;

				.time-item {
					width: 162rpx;
					height: 110rpx;
					color: #C7C7C7;
					background: #E5E5E5;
					border-radius: 16rpx;
					border: 1px solid #E5E5E5;
					margin: 10rpx;
				}

				.can-choose {
					color: #5A677E;
					background: #FFFFFF;
				}

				.cur-check {
					color: #fff;
				}
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
