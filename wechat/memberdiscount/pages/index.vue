<template>
	<view class="memberdiscount-index rel" :style="{background:pageColor}" v-if="isLoad">

		<!-- #ifndef H5 -->
		<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true"
			:leftIcon="options.pid || options.coach_id || options.paysuc ? '' : 'icon-left'" title="会员卡购买" color="#fff"
			:backgroundColor="pageScrollTop>1 || !configInfo.plugAuth.member ?'#1B1D33':''">
			<view @tap="$util.goUrl({url:`/pages/service`,openType:`reLaunch`})" slot="left"
				v-if="options.pid || options.coach_id || options.paysuc">
				<view :class="[{'back-user-ios none': configInfo.isIos},{'back-user-android none': !configInfo.isIos}]"
					style="z-index: 999;margin:0" :style="{background:'rgba(255,255,255,0.5)',color:'#fff'}">
					<view class="iconshouye iconfont"></view>
					<view class="back-user_text">回到首页</view>
				</view>
			</view>
		</uni-nav-bar> 
		<!-- #endif -->

		<!-- #ifdef H5 -->
		<view @tap="$util.goUrl({url:`/pages/service`,openType:`reLaunch`})" class="abs"
			:class="[{'back-user-ios none': configInfo.isIos},{'back-user-android none': !configInfo.isIos}]"
			style="margin-top:14rpx;z-index: 999;" :style="{background:'rgba(255,255,255,0.5)'}"
			v-if="options.pid || options.coach_id || options.paysuc">
			<view class="iconshouye iconfont"></view>
			<view class="back-user_text">回到首页</view>
		</view>
		<!-- #endif -->

		<block v-if="configInfo.plugAuth.member">

			<view class="discount-info rel">
				<image class="abs discount-bg" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/page-bg.png`">
				</image>
				<image class="abs bg-img" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/bg-img.png`">
				</image>
				<image class="abs bg-icon" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/bg-icon.png`">
				</image>
				<view class="rel count-info">
					<view class="flex-center" v-if="!mineInfo.member_info">
						<view class="discount-card-info flex-center f-ms-little">暂未开通
						</view>
						<view class="flex-1"></view>
					</view>
					<view class="f-ms-title text-bold" style="color: #301A0B;margin-top:18rpx">
						{{!mineInfo.member_info?'开通会员享更多特权':`会员卡${$util.formatTime(mineInfo.member_info.end_time*1000, 'YY-M-D')}到期`}}
					</view>
					<view class="mt-sm" style="color: #301A0B;font-size: 25rpx;" v-if="mineInfo.member_info">
						购买后顺延有效期
					</view>
				</view>
			</view>


			<view class="ml-md mr-md fill-base radius-16" style="padding: 14rpx 0 30rpx 30rpx;">
				<scroll-view scroll-x class="card-list" style="width: 100%" :scroll-into-view="'tab'+(chooseInd-1)" :scroll-with-animation="true"
				v-if="detail.list && detail.list.length>0">
					<view @tap.stop="toChangeItem('chooseInd',index)" class="card-item rel"
						data-index="index" :id="'tab'+index"
						:style="{marginRight:index==detail.list.length-1?'30rpx':''}"
						v-for="(item,index) in detail.list" :key="index">
						<view class="card-tag abs flex-center f-icontext c-base" v-if="item.icon">{{item.icon}}</view>
						<image class="abs card-img"
							:src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/card-cur.png`"
							v-if="chooseInd==index">
						</image>
						<view class="flex-center flex-column rel">
							<view class="flex-desc text-bold">{{item.title}}</view>
							<view class="flex-y-baseline f-ms-title text-bold">¥<view class="price"
									style="margin-left: 5rpx;">{{item.price}}</view>
							</view>
							<view class="f-icontext text-delete">¥{{item.init_price}}</view>
							<view class="reduce-btn flex-center mt-md"
								:style="{background:chooseInd==index?'#F7D8B9':'#F4F4F4'}">
								立省{{item | hannelPrice}}元</view>
						</view>
					</view>
				</scroll-view>

				<view class="f-mini-title c-title text-bold mb-sm" style="margin-top: 26rpx;">赠送以下权益</view>
				<view class="rights-list flex-warp flex-between pr-lg">
					<view class="list-item flex-center radius-16">
						<image class="rights-img"
							:src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/discount.png`">
						</image>
						<view class="flex-1 ml-md">
							<view class="title f-paragraph text-bold">会员折扣</view>
							<view class="text f-caption flex-y-center">所有服务<view class="text-bold">{{detail.discount}}
								</view>
								折</view>
						</view>
					</view>
					<!-- <view class="list-item flex-center radius-16" v-if="coupon_discount">
						<image class="rights-img"
							:src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/coupon.png`">
						</image>
						<view class="flex-1 ml-md">
							<view class="title f-paragraph text-bold">优惠券</view>
							<view class="text f-caption flex-y-center">价值<view class="text-bold">
									{{coupon_discount || 0}}
								</view>元
							</view>
						</view>
					</view> -->
					<view class="list-item flex-center radius-16" v-if="detail.integra">
						<image class="rights-img" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/integra.png`">
						</image>
						<view class="flex-1 ml-md">
							<view class="title f-paragraph text-bold">会员积分</view>
							<view class="text f-caption fflex-y-center">积分可抵套餐消费</view>
						</view>
					</view>
					<view class="list-item flex-center radius-16">
						<image class="rights-img" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/kefu.png`">
						</image>
						<view class="flex-1 ml-md">
							<view class="title f-paragraph text-bold">人工客服</view>
							<view class="text f-caption fflex-y-center">您的专属顾问</view>
						</view>
					</view>
					<view class="list-item flex-center radius-16">
						<image class="rights-img"
							:src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/refund.png`">
						</image>
						<view class="flex-1 ml-md">
							<view class="title f-paragraph text-bold">秒退款</view>
							<view class="text f-caption fflex-y-center">不满意秒退</view>
						</view>
					</view>
				</view>
				
				<view class="mt-md mr-lg pd-lg radius-16" style="background: #FBF6F0;" v-if="detail.list[chooseInd] && detail.list[chooseInd].text">
					<view class="f-paragraph text-bold">其他权益</view>
					<view class="f-caption pre-wrap" style="color: #6F5F4E;">{{detail.list[chooseInd].text}}</view>
				</view>
			</view>

			<view @tap.stop="$util.goUrl({url:`/memberdiscount/pages/share`})"
				class="share-info flex-center mt-md ml-md mr-md pd-lg fill-base radius-16"
				v-if="mineInfo.is_fx">
				<image class="icon-img" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/inv-icon.png`">
				</image>
				<view class="flex-1 flex-between ml-md">
					<view>
						<view class="f-paragraph text-bold" style="color: #25140C;">邀请用户买会员</view>
						<view class="f-caption" style="margin-top:4rpx;color:#797979">获得推荐奖励</view>
					</view>
					<view class="share-btn flex-center f-desc radius">去邀请</view>
				</view>
			</view>

			<view class="mt-md ml-md mr-md pd-lg fill-base radius-16">
				<view @tap.stop="toChangeItem('payInd',index)" class="flex-between" :class="[{'mt-lg':index!=0}]"
					v-for="(item,index) in payList" :key="index">
					<view class="pay-item flex-y-center">
						<view class="item-icon flex-center mr-md"
							:class="[{'wechat':item.id==1},{'alipay':item.id==3}]">
							<i class="iconfont icon-font-color" :class="[item.icon]"></i>
						</view>
						<view class="f-desc text-bold item-title">{{item.title}} </view>
					</view>
					<i class="pay-icon iconfont c-caption"
						:class="[{'icon-xuanze':payInd != index},{'icon-radio-fill':payInd == index}]"
						:style="{color:payInd==index?'#D49266':'#BEC3CE'}"></i>
				</view>
			</view>

			<view @tap.stop="$util.goUrl({url:`/memberdiscount/pages/order-list`})"
				class="mt-lg flex-center f-desc text-underline" style="color: #515151;">交易记录</view>

			<view style="height: 120rpx;"></view>
			<view class="space-max-footer"></view>
			<view class="pay-order-info fix fill-base pt-lg pb-lg" v-if="detail.list && detail.list.length>0">
				<view class="order-info ml-md mr-md rel">
					<image class="buy-img abs" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/buy.png`">
					</image>
					<view class="flex-between pl-lg pr-lg rel">
						<view class="flex-y-baseline">
							<view class="flex-y-baseline" style="color: #F8D7BD;">¥<view class="text-bold"
									style="font-size: 50rpx;margin-left:5rpx">{{detail.list[chooseInd].price}}</view>
								<view class="ml-lg f-caption text-delete" style="color: #919191;">
									原价¥{{detail.list[chooseInd].init_price}}</view>
							</view>
						</view>
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toPay" style="width: auto;">
							<view class="order-btn flex-center f-desc text-bold radius"> 立即开通 </view>
						</auth>
					</view>
				</view>
				<view @tap.stop="isAgree = !isAgree" class="flex-center f-caption pt-lg pb-md" style="color: #797979;">
					<i class="iconfont mr-md" :class="isAgree?'icon-xuanze-fill':'icon-xuanze'"
						:style="{color:isAgree?'#D49266':''}"></i>开通前请阅读<view
						@tap.stop="$util.goUrl({url:`/memberdiscount/pages/rule`})" :style="{color:primaryColor}">
						《会员协议规则说明》</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</block>

		<abnor type="NOT_AUTH" v-else></abnor>
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
				isLoad: false,
				options: {},
				pageScrollTop: 0,
				payList: [],
				payInd: 0,
				chooseInd: 0,
				card_id: 0,
				isAgree: false,
				detail: {},
				coupon_discount: 0,
				lockTap: false
			}
		},
		computed: mapState({
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
			primaryColor: state => state.config.configInfo.primaryColor,
		}),
		async onLoad(options) {
			options = await this.updateCommonOptions(options)
			let {
				pid = 0,
					coach_id = 0
			} = options
			options.pid = pid * 1
			options.coach_id = coach_id * 1
			this.options = options
			this.$util.showLoading()
			await this.initIndex()
			if (this.scanRecordId) {
				this.updateScanRecord()
			}
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onPageScroll(e) {
			let {
				scrollTop
			} = e
			this.pageScrollTop = scrollTop
		},
		filters: {
			hannelPrice(val){
				return Number((val.init_price*1 - val.price).toFixed(2))
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'addScanRecord',
				'updateScanRecord', 'updateCommonOptions', 'getMineInfo'
			]),
			...mapMutations(['updateOrderItem']),
			async initIndex(refresh = false) {
				let {
					pid = 0,
						coach_id = 0
				} = this.options
				let code = this.scanRecordId
				// #ifdef H5
				code = this.$util.getQueryString('code')
				// #endif
				if (coach_id && !code) {
					this.addScanRecord({
						type: 13,
						qr_id: coach_id,
						is_qr: pid ? 0 : 1
					})
				}
				let {
					id: uid = 0
				} = this.userInfo

				if (!this.configInfo.id || refresh || (pid && code && !uid)) {
					await this.getConfigInfo()
				}

				if (pid && !code && !uid) {
					await this.getUserInfo()
				}
				this.getMineInfo()

				let {
					alipay_status = 0,
				} = this.configInfo
				let weixinInd = this.payList.findIndex(item => {
					return item.id == 1
				})
				if (weixinInd == -1) {
					this.payList.push({
						id: 1,
						icon: 'icon-weixin',
						title: '微信支付'
					})
				}
				//余额支付（余额不足时后端会提示先去充值）
				let balanceInd = this.payList.findIndex(item => {
					return item.id == 2
				})
				if (balanceInd == -1) {
					this.payList.push({
						id: 2,
						icon: 'iconqianbao',
						title: '余额支付'
					})
				}
				let alipayInd = this.payList.findIndex(item => {
					return item.id == 3
				})
				if (alipay_status && alipayInd == -1) {
					// #ifndef MP-WEIXIN 
					this.payList.push({
						id: 3,
						icon: 'icon-alipay-fill',
						title: '支付宝支付'
					})
					// #endif
				}
				let [data, config] = await Promise.all([this.$api.memberdiscount.cardList(), this.$api.memberdiscount.configInfo()])
				data = {
					...config,
					list: data
				}
				this.detail = data
				
				if(!refresh){
					let ind = data.list.findIndex(item => {
						return item.id == (this.mineInfo.member_info ? this.mineInfo.member_info.id : 0)
					})
					this.chooseInd = ind == -1 ? 0 : ind
				}
				// if (data.list && data.list.length > 0) {
				// 	this.getDiscount()
				// }
				this.isLoad = true
				this.$util.hideAll()
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
			},
			initRefresh() {
				this.initIndex(true)
			},
			// async getDiscount() {
			// 	let {
			// 		id
			// 	} = this.detail.list[this.chooseInd]
			// 	this.card_id = id
			// 	let {
			// 		coupon_discount
			// 	} = await this.$api.memberdiscount.cardDiscount({
			// 		card_id: id
			// 	})
			// 	this.coupon_discount = coupon_discount
			// },
			toChangeItem(key, index) {
				this[key] = index
				// if (key == 'chooseInd') {
				// 	this.getDiscount()
				// }
			},
			// 去支付
			async toPay() {
				let {
					chooseInd,
					payInd,
					isAgree
				} = this

				if (!isAgree) {
					this.$util.showToast({
						title: `开通前请先同意《会员协议规则说明》`
					})
					return
				}

				let {
					id: card_id,
					price = 0
				} = this.detail.list[chooseInd]

				let {
					id: pay_model
				} = this.payList[payInd]
				let {
					coach_id = 0
				} = this.options
				let param = {
					id: card_id,
					coach_id,
					pay_model
				}

				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					let {
						pay_list,
						order_id
					} = await this.$api.memberdiscount.payOrder(param)
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
								order_id,
								page_url: `/pages/mine`,
								pay_type: 'member'
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
								this.initRefresh()
							}, 1000)
						} catch (e) {
							this.lockTap = false;
							return;
						}
					}
					this.$util.showToast({
						title: `支付成功`
					})
					setTimeout(async () => {
						this.lockTap = false
						this.getUserInfo()
						await this.getMineInfo()
						this.initRefresh()
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 1000)
				}
			}
		}
	}
</script>


<style lang="scss">
	.memberdiscount-index {
		.discount-info {
			overflow: hidden;
			/* #ifdef H5 */
			height: 317rpx;
			/* #endif */
			/* #ifndef H5 */
			height: 417rpx;
			/* #endif */

			.discount-bg {
				width: 100%;
				/* #ifdef H5 */
				height: 297rpx;
				/* #endif */
				/* #ifndef H5 */
				height: 397rpx;
				/* #endif */
				top: 0;
				left: 0;
			}

			.bg-img {
				width: 654rpx;
				height: 251rpx;
				left: 50%;
				bottom: -25rpx;
				margin-left: -327rpx;
				z-index: 1;
			}

			.bg-icon {
				width: 273rpx;
				height: 204rpx;
				/* #ifdef H5 */
				top: 50rpx;
				/* #endif */
				/* #ifndef H5 */
				top: 150rpx;
				/* #endif */
				right: 10rpx;
				z-index: 2;
			}

			.count-info {
				z-index: 2;
				/* #ifdef H5 */
				padding: 144rpx 78rpx 0 78rpx;
				/* #endif */
				/* #ifndef H5 */
				padding: 244rpx 78rpx 0 78rpx;
				/* #endif */
			}

			.discount-card-info {
				min-width: 116rpx;
				height: 36rpx;
				padding: 0 15rpx;
				background: linear-gradient(270deg, #301A0B 0%, #301A0B 100%);
				color: #F6E0CE;
				border-radius: 4rpx;
			}

		}

		.card-list {
			height: 295rpx;
			white-space: nowrap;
			padding-top: 16rpx;

			.card-item {
				display: inline-block;
				width: 210rpx;
				height: 260rpx;
				background: #FFFFFF;
				border-radius: 18rpx;
				border: 2rpx solid #E2DFD6;
				margin-left: 12rpx;
				margin-top: 16rpx;
				color: #1A1C2E;

				.card-tag {
					top: -16rpx;
					left: -2rpx;
					min-width: 75rpx;
					height: 31rpx;
					padding: 0 10rpx;
					background: linear-gradient(-270deg, #FF872C 0%, #FB5C14 100%);
					border-radius: 8rpx 8rpx 8rpx 0;
					z-index: 3;
				}

				.card-img {
					width: 210rpx;
					height: 279rpx;
					top: -1rpx;
					left: -1rpx;
				}

				.flex-column {
					height: 260rpx;
				}

				.price {
					font-size: 52rpx;

				}

				.text-delete {
					color: #868686;
					margin-top: 5rpx;
				}

				.reduce-btn {
					width: 159rpx;
					height: 38rpx;
					border-radius: 19rpx;
					font-size: 25rpx;
					color: #553624;
				}
			}

			.card-item:nth-child(1) {
				margin-left: 0;
			}
		}

		.rights-list {
			.list-item {
				width: 315rpx;
				height: 138rpx;
				padding: 34rpx 0 34rpx 25rpx;
				background: #FBF6F0;
				margin-top: 20rpx;

				.rights-img {
					width: 70rpx;
					height: 70rpx;
				}

				.title {
					color: #25140C;
				}

				.text {
					color: #6F5F4E;
					margin-top: 3rpx;

					.text-bold {
						color: #25140C;
					}
				}
			}

			.list-item:nth-child(2n) {
				margin-left: 20rpx;
			}
		}

		.share-info {
			.icon-img {
				width: 91rpx;
				height: 80rpx;
			}

			.share-btn {
				width: 143rpx;
				height: 72rpx;
				background: linear-gradient(270deg, #42454D 0%, #2D2E38 100%);
				color: #F5D6C2;
			}
		}

		.pay-item {
			.item-icon {

				width: 70rpx;
				height: 70rpx;
				border-radius: 35rpx;

				.iconfont {
					font-size: 44rpx;
				}
			}

			.item-icon.wechat {
				background: #E7F9EE;

				.iconfont {
					background-image: linear-gradient(180deg, #90EE90 0%, #32CD32 100%, #19c865 100%);
				}
			}

			.item-icon.alipay {
				background: #E9F5FE;

				.iconfont {
					background-image: linear-gradient(180deg, #46BCFF 0%, #2587FF 100%, #1976FF 100%);
				}
			}

			.item-title {
				color: #2E3541;
			}

		}

		.pay-order-info {
			width: 100%;
			height: auto;
			left: 0;
			bottom: 0;

			.order-info {
				height: 110rpx;

				.buy-img {
					width: 100%;
					height: 110rpx;
				}

				.flex-between {
					height: 110rpx;

					.order-btn {
						width: 182rpx;
						height: 74rpx;
						background: linear-gradient(180deg, #F5D1B9 15%, #F5D8C5 40%, #F1BB96 100%);
						color: #a05a28;
					}
				}

			}
		}

	}
</style>