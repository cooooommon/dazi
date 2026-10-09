<template>
	<view class="order-pages" v-if="detail.id">

		<view class="item-child pd-lg fill-base f-paragraph c-base" :style="{background:primaryColor}">
			<view class="flex-y-baseline">
				<view class="text-bold">{{statusType[detail.pay_type]}}</view>
				<view @tap.stop="$refs.refuse_item.open()" class="ml-md"
					v-if="(detail.pay_type == -1 || detail.pay_type == 8) && detail.coach_refund_time"> {{$t('action.attendantName')}}拒单，查看原因</view>
			</view>
			<view class="f-caption mt-sm" v-if="detail.pay_type == 1 && detail.end_time > 0">请在<min-countdown
					:targetTime="over_time_text" @callback="countEnd"></min-countdown>内完成支付，逾期未支付，订单将自动取消</view>
			<view class="space-lg"></view>
		</view>

		<!-- // pay_type 1待支付，2待服务，3向导接单，4向导出发，5向导到达，6服务中，7服务完成 -->
		<view
			class="menu-list flex-warp rel ml-lg mr-lg pt-lg pb-lg pl-md pr-md fill-base f-paragraph c-caption radius-16"
			:class="[{'add-bell':detail.is_add || detail.store_id}]">
			<view class="menu-line abs b-1px-b"></view>
			<block v-for="(item,index) in lineList" :key="index">
				<view class="item-child flex-center flex-column f-icontext c-paragraph"
					:style="{color:(detail.pay_type > item.pay_type -1) && detail.pay_type != 8 ?primaryColor:''}"
					v-if="item.icon">
					<view class="item-img fill-base flex-center mb-sm radius"
						:style="{borderColor:(detail.pay_type > item.pay_type -1) && detail.pay_type != 8?primaryColor:''}">
						<i class="iconfont" :class="item.icon"></i>
					</view>
					<view>{{item.title}}</view>
				</view>
			</block>
		</view>
		
		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16 flex-y-center">
			<text class="pr-md f-paragraph">服务还有</text>
			<min-countdown :targetTime="detail.end_service_time * 1000" @callback="countEnd" :isPlay="true" :type="4" style="height: 46rpx;"></min-countdown>
			<text class="pl-md">结束</text>
		</view>
		

		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16">
			<view class="flex-between pb-lg">
				<view class="f-paragraph c-title max-380 ellipsis">服务内容</view>
			</view>
			<view class="flex-center" :class="[{'mb-lg':aindex != detail.order_goods.length -1}]"
				v-for="(aitem,aindex) in detail.order_goods" :key="aindex">
				<!-- #ifdef H5 -->
				<view class="avatar lg radius-16">
					<view class="h5-image avatar lg radius-16"
						:style="{ backgroundImage : `url('${aitem.goods_cover}')`}">
					</view>
				</view>
				<!-- #endif -->
				<!-- #ifndef H5 -->
				<image mode="aspectFill" class="avatar lg radius-16" :src="aitem.goods_cover"></image>
				<!-- #endif -->
				<view class="flex-1 ml-md">
					<view class="flex-between">
						<view class="f-mini-title c-title text-bold ellipsis"
							:class="[{'max-300':aitem.refund_num>0},{'max-450':aitem.refund_num == 0}]">
							{{aitem.goods_name}}
						</view>
						<view class="f-caption c-warning" v-if="aitem.refund_num>0">已退x{{aitem.refund_num}}</view>
					</view>
					<view class="f-caption c-caption">服务{{$t('action.attendantName')}}：{{detail.coach_info ? detail.coach_info.coach_name : '-'}}
					</view>
					<view class="f-caption c-caption">服务时长：{{aitem.time_long}}分钟</view>
					<view class="flex-between">
						<view class="flex-y-baseline f-caption c-warning">¥<view class="f-title text-bold">
								{{aitem.price}}
							</view>
						</view>
						<view class="c-paragraph">x{{aitem.num}}</view>
					</view>
				</view>
			</view>
		</view>

		<view class="store-info mt-md ml-lg mr-lg pd-lg  fill-base radius-16" v-if="detail.store_id">
			<view class="f-mini-title c-title text-bold pb-md">
				{{detail.store_info.title}}
			</view>
			<view class="flex-between">
				<view class="flex-y-center" style="color: #303030;">
					<i class="iconfont icondizhi1 mr-sm"></i>
					<view class="c-title flex-1 mr-md">
						<span>{{detail.store_info.address || `暂未设置门店地址`}}</span>
						<span @tap.stop="$util.goUrl({url:detail.store_info.address,openType:'copy'})"
							class="store-copy-btn radius-5 f-icontext ml-sm"
							:style="{color:primaryColor,borderColor:primaryColor}"
							v-if="detail.store_info.address">复制</span>
					</view>
				</view>
				<view class="flex-center">
					<view @tap.stop="$util.goUrl({url:detail.store_info.phone,openType:'call'})"
						class="item-icon rel flex-center radius-16">
						<view class="item-icon radius-16 abs" :style="{background:primaryColor}"></view>
						<i class="iconfont icondadianhua_1" :style="{color:primaryColor}"></i>
					</view>
					<view @tap.stop="toMap('store_info')" class="item-icon rel flex-center radius-16 ml-md"
						v-if="detail.store_info.address">
						<view class="item-icon radius-16 abs" :style="{background:primaryColor}"></view>
						<i class="iconfont icondizhi_1" :style="{color:primaryColor}"></i>
					</view>
				</view>
			</view>
		</view>


		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class="flex-between">
				<view>下单人</view>
				<view class="c-title">{{detail.address_info.user_name}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>联系方式</view>
				<view class="c-title">{{detail.address_info.mobile}}</view>
			</view>
			<view class="mt-md" v-if="!detail.store_id">
				<view>服务地址</view>
				<view class="c-title mt-sm">{{`${detail.address_info.address}${detail.address_info.address_info}`}}
				</view>
			</view>
			<view class="mt-md" v-if="detail.text">
				<view>订单备注</view>
				<view class="c-title mt-sm">{{detail.text}}</view>
			</view>
		</view>

		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class="flex-center">
				<image mode="aspectFill" class="avatar sm radius" :src="detail.coach_info.work_img" 
				@tap="$util.goUrl({url: `/user/pages/technician-info?id=${detail.coach_info.id}`})"></image>
				<view class="flex-1 flex-between ml-lg">
					<view class="c-title max-380 ellipsis" 
					@tap="$util.goUrl({url: `/user/pages/technician-info?id=${detail.coach_info.id}`})">{{detail.coach_info ? detail.coach_info.coach_name : ''}}
					</view>
					<view @tap.top="toTel" class="call-btn flex-center f-desc text-bold" v-if="configInfo.user_contact_coach == 1"
						:style="{color:primaryColor,border:`1rpx solid ${primaryColor}`}">联系Ta
					</view>
				</view>
			</view>
			<!-- <view class="flex-between mt-md"
				v-if="(detail.pay_type != -1 && detail.pay_type != 8) && detail.coach_refund_time">
				<view>原向导</view>
				<view @tap.stop="$refs.refuse_item.open()" class="flex-y-center c-title">{{detail.old_coach_name}}
					<view class="ml-sm" :style="{color:primaryColor}">拒单原因</view>
				</view>
			</view> -->
			<view class="flex-between mt-md">
				<view>下单时间</view>
				<view class="c-title">{{detail.create_time}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>服务时间</view>
				<view class="c-title">{{detail.start_time}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>服务时长</view>
				<view class="c-title">{{detail.time_long}}分钟</view>
			</view>
			<block v-if="!detail.is_add && !detail.store_id">
				<view class="flex-between mt-md">
					<view>车费详情</view>
					<view class="flex-y-center c-title">{{carType[detail.car_type]}}
						<view class="ml-md" v-if="detail.car_type == 1">全程{{detail.distance}}</view>
					</view>
				</view>
				<view class="flex-between mt-md" v-if="detail.car_type == 1">
					<view>出行费用</view>
					<view class="c-warning">出租车 ¥{{detail.car_price}}</view>
				</view>
			</block>
			<view class="flex-between mt-md">
				<view>服务项目费用</view>
				<view class="c-warning">¥{{detail.init_service_price}}</view>
			</view>
			<view class="flex-between mt-md" v-if="detail.discount*1 > 0">
				<view>卡券优惠</view>
				<view class="c-warning">-¥{{detail.discount}}</view>
			</view>
			<view class="flex-between mt-md" v-if="detail.member_status">
				<view>会员卡折扣</view>
				<view class="c-warning">{{detail.member_balance}}折 (优惠¥{{detail.member_discount}})</view>
			</view>
			<view class="flex-between mt-md">
				<view>支付方式</view>
				<view class="flex-y-baseline c-title">{{payType[detail.pay_model]}}</view>
			</view>
			<view class="flex-between mt-md pt-md b-1px-t">
				<view></view>
				<view class="flex-y-baseline c-title">总计：<view class="c-warning">¥{{detail.pay_price}}</view>
				</view>
			</view>
		</view>

		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class=" flex-y-center pb-lg flex-warp">
				<view class="flex-between c-title">订单编号：</view>
				<view class="flex-between flex-1 ">
					<view class="c-title">{{detail.order_code}}</view>
					<view class="f-icontext d-copy text-center"
						@tap.stop="$util.goUrl({openType:'copy',url:detail.order_code})"
						:style="{borderColor:primaryColor ,color:primaryColor}">复制</view>
				</view>
			</view>
			<timeline :list="lineList" :info="detail" :type="1"></timeline>
		</view>

		<view class="space-max-footer"></view>
		<view class="footer-info fix fill-base" v-if="detail.pay_type != -1">
			<view class="flex-between pd-lg">
				<view></view>
				<view class="flex-center f-desc c-title">
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="contact" class="clear-btn item-btn flex-center f-desc"
						style="border-radius: 100rpx;" v-if="configInfo.im_type == 2">联系客服</button>
					<button @tap.stop="toContact" class="clear-btn item-btn flex-center f-desc"
						style="border-radius: 100rpx;" v-else>联系客服</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<button @tap.stop="toContact" class="clear-btn item-btn flex-center f-desc"
						style="border-radius: 100rpx;">联系客服</button>
					<!-- #endif -->
					<!-- 待支付 -->
					<block v-if="detail.pay_type == 1">
						<view @tap.stop="toCancel" class="item-btn flex-center ml-md radius">取消订单</view>
						<view @tap.stop="toPay" class="item-btn flex-center ml-md c-base radius"
							:style="{background:primaryColor}">
							去支付
						</view>
					</block>
					<!-- 支付超时 -->
					<!-- 	<block v-if="detail.pay_type == -1 || detail.pay_type == 7">
							<view @tap.stop="toCancel" class="item-btn flex-center ml-md radius">删除</view>
						</block> -->
					<!-- 待服务 -->
					<view @tap.stop="goDetail('refund')" class="item-btn flex-center ml-md radius"
						v-if="detail.can_refund*1>0">
						申请退款</view>
					<!-- 服务中 -->
					<view
						@tap.stop="$util.goUrl({url:`/user/pages/bell/list?id=${detail.id}&coach_id=${detail.coach_id}`})"
						class="item-btn flex-center ml-md c-base radius" :style="{background:primaryColor}"
						v-if="detail.pay_type == 6 && (detail.is_add || (!detail.is_add && detail.can_add_order))">
						{{detail.is_add?'更换项目':'更换/续单'}}
					</view>
					<!-- 已完成 -->
					<!-- 	<block
							v-if="detail.pay_type == 7 && !detail.is_add && (!detail.is_comment || detail.can_again)">
							<view @tap.stop="goDetail('evaluate')" class="item-btn flex-center ml-md radius"
								v-if="!detail.is_comment">
								去评价</view>
							<view @tap.stop="toAgain" class="item-btn flex-center ml-md c-base radius"
								:style="{background:primaryColor}" v-if="detail.can_again">
								再来一单
							</view>
						</block> -->
					<view
						@tap.stop="$util.goUrl({url:`/user/pages/order/detail?id=${detail.add_pid.id}`,openType:`redirectTo`})"
						class="item-btn flex-center ml-md c-base radius" :style="{background:primaryColor}"
						v-if="detail.is_add">
						查看主订单
					</view>
					<view
						@tap.stop="$util.goUrl({url:`/user/pages/order/bell-list?id=${detail.id}`,openType:`redirectTo`})"
						class="item-btn flex-center ml-md c-base radius" :style="{background:primaryColor}"
						v-if="detail.add_order_id && detail.add_order_id.length > 0">
						查看续单
					</view>
				</view>
			</view>
			<view class="space-safe"></view>
		</view>

		<common-popup @confirm="confirmCancel" ref="cancel_item" type="CANCEL_ORDER" :info="popupInfo"></common-popup>
		<common-popup @confirm="confirmDel" ref="del_item" type="DELETE_ORDER" :info="popupInfo"></common-popup>

		<uni-popup ref="refuse_item" type="center" :custom="true">
			<view class="common-popup-content fill-base pd-lg radius-34">
				<view class="title">拒单原因</view>
				<scroll-view scroll-y @touchmove.stop.prevent class="refund-text mt-lg">
					<view>
						<text decode="emsp"
							style="word-break:break-all;">{{detail.coach_refund_text || '没有填写原因哦'}}</text>
					</view>
					<!-- <view class="mt-md f-caption c-caption">拒单时间：{{detail.coach_refund_time}}</view> -->
				</scroll-view>
				<view class="button">
					<view @tap.stop="$refs.refuse_item.close()" class="item-child c-base"
						:style="{background: primaryColor,color:'#fff'}">知道了</view>
				</view>
			</view>
		</uni-popup>

		<uni-popup ref="show_phone_item" type="center" :maskClick="false">
			<view class="common-popup-content fill-base pd-lg radius-34">
				<view class="title">拨打电话</view>
				<view class="space-lg"></view>
				<view @tap.stop="toCallPhone(configInfo.mobile)" class="flex-between mt-md f-mini-title c-caption"
					style="width: 100%;">
					<view class="flex-y-center">平台：<view class="c-title">{{configInfo.mobile}}
						</view>
					</view>
					<view :style="{color:primaryColor}">拨打</view>
				</view>
				<view @tap.stop="toCallPhone(detail.admin_phone)" class="flex-between mt-md f-mini-title c-caption"
					style="width: 100%;" v-if="configInfo.agent_phone == 1">
					<view class="flex-y-center">代理商：<view class="c-title">{{detail.admin_phone}}
						</view>
					</view>
					<view :style="{color:primaryColor}">拨打</view>
				</view>
			</view>
			<view class="flex-center mt-lg"><i @tap.stop="$refs.show_phone_item.close()"
					class="iconfont icon-close c-base" style="font-size: 60rpx;"></i>
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
	import timeline from '@/components/timeline.vue'
	export default {
		components: {
			timeline
		},
		data() {
			return {
				options: {},
				statusPayType: [1],
				// statusPayType: [-1, 1, 7],
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
				carType: {
					0: '公交/地铁',
					1: '出租车'
				},
				payType: {
					1: '微信支付',
					2: '余额支付',
					3: '支付宝支付'
				},
				lineList: [],
				base_service: [{
					pay_type: 3,
					title: this.$t('action.orderTaking'),
					time: 'receiving_time',
					icon: 'iconjishijiedan'
				}, {
					pay_type: 4,
					title: this.$t('action.setOut'),
					time: 'serout_time',
					icon: 'iconjishichufa'
				}, {
					pay_type: 5,
					title: this.$t('action.arrive'),
					time: 'arrive_time',
					icon: 'iconjishidaoda'
				}, {
					pay_type: 6,
					title: '开始服务',
					time: 'start_service_time',
					icon: 'iconjishifuwu'
				}, {
					pay_type: 7,
					title: '服务完成',
					time: 'order_end_time',
					icon: 'iconjishiwancheng'
				}, {
					pay_type: 7,
					title: '签字确认',
					time: 'sign_time',
					icon: ''
				}],
				base_bell: [{
					pay_type: 3,
					title: this.$t('action.orderTaking'),
					time: 'receiving_time',
					icon: 'iconjishijiedan'
				}, {
					pay_type: 6,
					title: '开始服务',
					time: 'start_service_time',
					icon: 'iconjishifuwu'
				}, {
					pay_type: 7,
					title: '服务完成',
					time: 'order_end_time',
					icon: 'iconjishiwancheng'
				}],
				detail: {
					pay_type: 0
				},
				popupInfo: {},
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			over_time_text() {
				return new Date().getTime() + this.detail.over_time * 1000
			}
		}),
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo']),
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
					this.payType[2] = this.configInfo.balance_character
				}
				let {
					id
				} = this.options
				let data = await this.$api.order.orderInfo({
					id
				})
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				data.is_balance = data.balance * 1 > 0 ? 1 : 0
				let {
					is_add = 0,
						store_id = 0
				} = data
				let lineList = this.$util.deepCopy(is_add || store_id ? this.base_bell : this.base_service)
				if (store_id) {
					lineList.push({
						pay_type: 7,
						title: '签字确认',
						time: 'sign_time',
						icon: ''
					})
				}
				this.lineList = lineList
				this.detail = data
			},
			initRefresh() {
				this.initIndex(true)
			},
			countEnd() {
				this.$util.log("倒计时完了")
				setTimeout(() => {
					this.initRefresh()
					this.$util.back()
				}, 1000)
			},
			// 取消/删除订单
			async toCancel() {
				let {
					order_code,
					order_goods,
					pay_type
				} = this.detail
				let {
					goods_cover: image,
				} = order_goods[0]
				this.popupInfo = {
					name: `系统单号：${order_code}`,
					image,
				}
				let refs_key = pay_type == 1 ? 'cancel_item' : 'del_item'
				this.$refs[refs_key].open()
			},
			// 确认：取消订单
			async confirmCancel() {
				let {
					id,
				} = this.detail
				await this.$api.order.cancelOrder({
					id
				})
				this.$util.showToast({
					title: `取消成功`
				})
				this.detail.pay_type = -1
				this.$refs.cancel_item.close()
				this.$util.back()
			},
			// 确认：删除订单
			async confirmDel() {
				let {
					id,
				} = this.detail
				await this.$api.order.delOrder({
					id
				})
				this.$util.showToast({
					title: `删除成功`
				})
				this.$refs.del_item.close()
				this.$util.back()
				setTimeout(() => {
					this.$util.goUrl({
						url: 1,
						openType: 'navigateBack'
					})
				}, 1000)
			},
			// 去支付
			async toPay(index) {
				if (this.lockTap) return;
				this.lockTap = true;
				this.$util.showLoading()
				let {
					id,
					pay_model
				} = this.detail

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
								page_url: `/user/pages/order/detail?id=${id}`
							})
							this.updateOrderItem({
								key: 'alipayOrderParams',
								val: pay_list
							})
							this.$util.goUrl({
								url: '/user/pages/alipay-result'
							})
							this.lockTap = false;
							setTimeout(() => {
								this.initRefresh()
								this.$util.back()
							}, 3000)
							return
						}
						// #endif


						try {
							await this.$util.pay(pay_list)
							this.$util.showToast({
								title: `支付成功`
							})
							this.initRefresh()
							this.lockTap = false;
							this.$util.back()
						} catch (e) {
							this.lockTap = false;
							return;
						}
					}
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
			// 订单详情/申请退款/去评价/签字确认
			goDetail(page) {
				let {
					id
				} = this.options
				let url = `/user/pages/order/${page}?id=${id}`
				this.$util.goUrl({
					url
				})
			},
			// 再来一单
			async toAgain() {
				let {
					id: order_id,
					coach_id
				} = this.detail

				await this.$api.order.onceMoreOrder({
					order_id
				})
				this.$util.goUrl({
					url: `/user/pages/order?id=${coach_id}`
				})
			},
			toContact() {
				let {
					hide_admin_mobile,
					mobile,
					im_type,
					qywx_company_id,
					qywx_kid
				} = this.configInfo
				let {
					admin_phone
				} = this.detail
			
				if (!hide_admin_mobile && admin_phone && im_type != 3) {
					this.$refs.show_phone_item.open()
					return
				}
			
				if (im_type == 3) {
					// #ifdef H5
					window.location.href = qywx_kid
					// #endif
					// #ifndef H5 
					// #ifdef MP-WEIXIN 
					try {
						wx.openCustomerServiceChat({
							extInfo: {
								url: qywx_kid
							},
							corpId: qywx_company_id,
							fail(res) {
								uni.showToast({
									title: res.errorMsg,
									icon: 'none'
								})
							}
						})
					} catch (e) {
						this.$util.showToast({
							title: `请更新微信版本`
						})
					}
					// #endif
					// #ifdef APP-PLUS
					let wechat = null
					plus.share.getServices(res => {
						wechat = res.find(i => i.id === 'weixin')
						if (wechat) {
							wechat.openCustomerServiceChat({
								corpid: qywx_company_id,
								url: qywx_kid
							}, err => {
								uni.showToast({
									title: err.errorMsg
								})
							})
						} else {
							uni.showToast({
								title: '当前环境不支持微信操作',
								icon: 'none'
							})
						}
					}, function() {
						uni.showToast({
							title: '获取服务失败，不支持该操作',
							icon: 'none'
						})
					})
					// #endif
					// #endif
					return
				}
			
				let url = hide_admin_mobile && admin_phone ? admin_phone : mobile
				this.$util.goUrl({
					url,
					openType: 'call'
				})
			},
			// 联系向导
			async toTel() {
				let {
					id: order_id,
					pay_type
				} = this.detail
				if ([2, 3, 4, 5, 6].includes(pay_type)) {
					let url = await this.$api.order.getVirtualPhone({
						order_id
					})
					this.$util.goUrl({
						url,
						openType: `call`
					})
				} else {
					let msg = pay_type == 7 ? '服务结束' : '服务取消'
					this.$util.showToast({
						title: `${msg}不能联系${this.$t('action.attendantName')}哦`
					})
				}
			},
			toCallPhone(url) {
				this.$refs.show_phone_item.close()
				this.$util.goUrl({
					url,
					openType: 'call'
				})
			},
			// 查看定位
			async toMap(key) {
				let {
					address,
					address_info = '',
					lat,
					lng
				} = this.detail[key]
				await this.$util.checkAuth({
					type: 'userLocation'
				})
				await uni.getLocation({
					type: 'gcj02',
				})
				await uni.openLocation({
					latitude: lat * 1,
					longitude: lng * 1,
					name: address_info ? `${address} ${address_info}` : address,
					scale: 28
				})
			},
			// countEnd() {
			// 	this.$util.log("倒计时完了")
			// 	setTimeout(() => {
			// 		this.initRefresh()
			// 		this.$util.back()
			// 	}, 1000)
			// },
		}
	}
</script>


<style lang="scss">
	.order-pages {
		.call-btn {
			width: 128rpx;
			height: 49rpx;
			border-radius: 8rpx
		}
	}

	.common-popup-content {
		.refund-text {
			width: 540rpx;
			min-height: 200rpx;
			max-height: 50vh;
		}
	}

	.d-copy {
		border: 1px solid transparent;
		width: 70rpx;
		border-radius: 8rpx;
	}
</style>
