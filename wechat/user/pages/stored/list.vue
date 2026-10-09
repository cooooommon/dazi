<template>
	<view class="user-stored-list">
		<!-- #ifdef H5 -->
		<view @tap="$util.goUrl({url:`/pages/service`,openType:`reLaunch`})" class="abs"
			:class="[{'back-user-ios none': configInfo.isIos},{'back-user-android none': !configInfo.isIos}]"
			style="z-index: 1;" v-if="options.pid">
			<view class="iconshouye iconfont"></view>
			<view class="back-user_text">回到首页</view>
		</view>
		<!-- #endif -->
		<view class="stored-bg c-base abs" :style="{background:primaryColor}">
		</view>
		<view class="space-lg"></view>
		<view class="record-info fill-base ml-md mr-md radius-32 rel">
			<view class="pl-lg pr-lg flex-between cash-box">
				<view class="">
					<text class="f-paragraph">可提现余额：</text>
					<text class="f-mini-title text-bold">¥{{mineInfo.package_cash || 0}}</text>
				</view>
				<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width: auto;"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url: `/user/pages/stored/cash-out`})">
					<view class="cash-btn c-base f-mini-title flex-center" :style="{background:primaryColor}">提现</view>
				</auth>
			</view>
			<view class="flex-center flex-column pd-lg">
				<view class="money text-bold">{{Number((mineInfo.balance*1 + mineInfo.package_cash*1).toFixed(2)) || 0}}</view>
				<view class="f-caption c-caption mt-sm mb-sm">当前{{configInfo.balance_character || `余额`}}(元)</view>
			</view>
			<view class="record-list flex-x-center pb-lg f-paragraph c-title">
				<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" class="flex-1 flex-center"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url:`/user/pages/stored/commission`})">
					<view class="list-item flex-center" style="word-break: keep-all;">返佣记录</view>
				</auth>
				<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" class="flex-1 flex-center"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url:`/user/pages/stored/record`})">
					<view class="list-item flex-center" style="word-break: keep-all;">充值记录</view>
				</auth>
				<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" class="flex-1 flex-center"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url:`/user/pages/stored/consume`})">
					<view class="list-item flex-center" style="word-break: keep-all;">消费明细</view>
				</auth>
			</view>
		</view>

		<view class="fill-base mt-md ml-md mr-md pd-lg radius-24">
			<view class="store-list flex-warp">
				<view @tap.stop="toChangeItem('chooseInd',index)" class="list-item mb-md pd-lg fill-base radius-16"
					:class="[{'mr-md':index%2==0}]" :style="{background:index==chooseInd?primaryColor:'#F7F8FA'}"
					v-for="(item,index) in list.data" :key="index">
					<view class="ellipsis" :class="[{'c-title':index!=chooseInd},{'c-base':index==chooseInd}]"
						style="font-size: 50rpx;">
						¥{{item.price}}</view>
					<view class="f-caption ellipsis"
						:style="{color:index==chooseInd?'rgba(255,255,255,0.5)':'#8D9092'}">
						{{item.title}}
					</view>
					<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
						:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toShare(index)" v-if="mineInfo.coach_status === 2">
						<view class="f-caption mt-sm"
							:style="{color:index==chooseInd?'#FAFF5B':primaryColor}">邀请充值
						</view>
					</auth>
				</view>
			</view>

			<view @tap.stop="options.coach_id?'':$util.goUrl({url:`/user/pages/stored/choose-technician`})"
				class="flex-between mt-md mb-md">
				<view class="f-title text-bold">选择为{{$t('action.attendantName')}}充值</view>
				<view class="flex-y-center f-title">
					<view class="max-200 ellipsis" :style="{color:!coach_info.id ? '#4A4A4A':''}">
						{{coach_info.id ? coach_info.coach_name : '去选择'}}
					</view>
					<i class="iconfont icon-right" v-if="!options.coach_id"></i>
				</view>
			</view>
			<view @tap.stop="toChangeItem('payInd',index)" class="flex-between mt-lg" v-for="(item,index) in payList"
				:key="index">
				<view class="pay-item flex-y-center">
					<view class="item-icon flex-center mr-md" :class="[{'wechat':item.id==1},{'alipay':item.id==3}]">
						<i class="iconfont icon-font-color" :class="[item.icon]"></i>
					</view>
					<view class="flex-y-baseline item-title">{{item.title}}
					</view>
				</view>
				<i class="pay-icon iconfont c-caption"
					:class="[{'icon-xuanze':payInd != index},{'icon-radio-fill':payInd == index}]"
					:style="{color:payInd==index?primaryColor:'#BEC3CE'}"></i>
			</view>
			<view class="mt-md pt-lg f-caption c-caption">
				<view>1.充值的本金和赠送金额均不可提现、转移、转赠</view>
				<view class="mt-sm">2.使用范围：本平台所有项目皆可购买，以及交通费支付</view>
			</view>
		</view>
		<view class="space-max-footer"></view>
		<fixed position="bottom" v-if="chooseInd!=-1" :zIndex="99">
			<view class="pd-lg fill-base">
				<view class="flex-between">
					<view class="flex-y-center f-paragraph c-caption">充值金额：<view class="c-warning">
							{{ list.data[chooseInd] ? list.data[chooseInd].price : 0}}元
						</view>
					</view>
					<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
						:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toPay" style="width: auto;">
						<view class="pay-btn flex-center f-title c-base radius"
							:style="{background:primaryColor}">充值</view>
					</auth>
				</view>
				<view class="space-safe"></view>
			</view>
		</fixed>

		<uni-popup ref="show_share_item" type="bottom" zIndex="1000">
			<view class="popup-share pd-lg f-desc c-desc fill-base">
				<view class="flex-center">
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="share" class="clear-btn list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/wechat.png"></image>
						<view style="font-size: 26rpx;color:#666;">分享给好友</view>
					</button>
					<view @tap.stop="toPoster" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/picture.png"></image>
						<view>生成海报码</view>
					</view>
					<!-- #endif -->
					<!-- #ifdef H5 -->
					<view @tap.stop="toAppShare" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/wechat.png"></image>
						<view style="font-size: 26rpx;color:#666;">分享给好友</view>
					</view>
					<view @tap.stop="toPoster" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/picture.png"></image>
						<view>生成海报码</view>
					</view>
					<!-- #endif -->
					<!-- #ifdef APP-PLUS -->
					<view @tap.stop="toAppShare(1)" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/wechat.png"></image>
						<view style="font-size: 26rpx;color:#666;">分享给好友</view>
					</view>
					<view @tap.stop="toAppShare(2)" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/wechat-moments.png"></image>
						<view>分享到朋友圈</view>
					</view>
					<view @tap.stop="toPoster" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/picture.png"></image>
						<view>生成海报码</view>
					</view>
					<!-- #endif -->
				</view>
				<view class="space-footer"></view>
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
	import siteInfo from '@/siteinfo.js';
	export default {
		components: {},
		data() {
			return {
				options: {},
				color: '#ffffff',
				payList: [{
					id: 1,
					icon: 'icon-weixin',
					title: '微信支付'
				}],
				payInd: 0,
				chooseInd: 0,
				qr_code: '',
				imageUrl: 'https://lbqny.migugu.com/admin/anmo/mine/balance-wechat-share.png',
				coach_info: {},
				param: {
					page: 1,
					limit: 10
				},
				list: {
					data: []
				},
				loading: true,
				lockTap: false,
				shareInd: 0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
			isGzhLogin: state => state.user.isGzhLogin,
		}),
		async onLoad(options) {
			this.$util.showLoading()
			options = await this.updateCommonOptions(options)
			this.options = options
			// #ifdef MP-WEIXIN
			uni.hideShareMenu()
			// #endif
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
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
		onShareAppMessage(e) {
			if (e.from === 'button') {
				let {
					imageUrl
				} = this
				let {
					id,
					title,
				} = this.list.data[0]
				let {
					id: pid = 0
				} = this.userInfo
				let {
					coach_id
				} = this.mineInfo
				let path = `/user/pages/stored/list?id=${id}&pid=${pid}&coach_id=${coach_id}`
				this.$util.log(path)
				this.$refs.show_share_item.close()
				return {
					title,
					imageUrl,
					path,
				}
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'getMineInfo', 'updateCommonOptions']),
			...mapMutations(['updateOrderItem']),
			async initIndex(refresh = false) {
				let {
					isGzhLogin
				} = this
				let {
					id: uid = 0
				} = this.userInfo
				if (!uid) {
					// #ifdef H5
					if (isGzhLogin) {
						setTimeout(() => {
							this.getUserInfo()
						}, 1000)
					} else {
						this.getUserInfo()
					}
					// #endif
					// #ifndef H5
					await this.getUserInfo()
					// #endif 
				}
				await Promise.all([this.getConfigInfo(), this.getMineInfo(), this.getList()])
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
					// setTimeout(() => {
					// 	this.toAppShare()
					// }, 1200)
				}
				// #endif

				let {
					alipay_status = 0
				} = this.configInfo
				if (alipay_status && this.payList.length == 1) {
					// #ifndef MP-WEIXIN
					this.payList.push({
						id: 3,
						icon: 'icon-alipay-fill',
						title: '支付宝支付'
					})
					// #endif
				}
				
				uni.setNavigationBarTitle({
					title: this.configInfo.balance_character ? (this.configInfo.balance_character + '充值') : '余额充值'
				})
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
				} = this
				let {
					coach_id = 0
				} = this.commonOptions
				param.coach_id = coach_id
				let newList = await this.$api.mine.cardList(param);
				if (coach_id) {
					let {
						coach_name = ''
					} = newList
					this.coach_info = {
						id: coach_id,
						coach_name
					}
				}
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
				if (this.list.data.length == 0) {
					this.chooseInd = -1
				}
			},
			toChangeItem(key, val) {
				this[key] = val
				this.lockTap = false
			},
			// 去支付
			async toPay() {
				let {
					chooseInd,
					payInd
				} = this
				let {
					id: card_id,
					true_price
				} = this.list.data[chooseInd]
				let {
					id: coach_id = 0
				} = this.coach_info
				let {
					id: pay_model
				} = this.payList[payInd]
				let param = {
					card_id,
					coach_id,
					pay_model
				}

				// #ifdef H5
				if (pay_model == 3) {
					this.updateOrderItem({
						key: 'alipayOrderParams',
						val: {
							order_id: '',
							card_id,
							coach_id,
							orderInfo: ''
						}
					})
					this.$util.goUrl({
						url: `/user/pages/alipay-result`
					})
					setTimeout(() => {
						this.getMineInfo()
						this.$util.back()
					}, 3000)
					return
				}

				// #endif

				if (this.lockTap) return;
				this.lockTap = true;
				this.$util.showLoading()
				try {
					let {
						pay_list
					} = await this.$api.mine.payBalanceOrder(param)
					this.$util.hideAll()
					if (pay_list) {
						if (pay_model == 3) {
							pay_list = {
								orderInfo: pay_list,
								provider: 'alipay'
							}
						}
						try {
							await this.$util.pay(pay_list)
							this.$util.showToast({
								title: `支付成功`
							})
							await this.getMineInfo()
							setTimeout(() => {
								this.lockTap = false;
								this.$util.back()
							}, 1000)
						} catch (e) {
							this.lockTap = false;
							return;
						}
					}
				} catch (e) {
					this.$util.showToast({
						title: `支付失败`
					})
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 1000)
				}
			},
			async toShare(index) {
				this.shareInd = index
				if (!this.qr_code) {
					this.$util.showLoading()
					let qr_code = await this.$api.mine.coachBalanceQr()
					this.$util.hideAll()
					this.qr_code = qr_code
				}
				// #ifndef H5
				this.$refs.show_share_item.open()
				// #endif
				// #ifdef H5
				this.$util.goUrl({
					url: `/user/pages/stored/poster`
				})
				// #endif
			},
			toPoster() {
				this.$refs.show_share_item.close()
				this.$util.goUrl({
					url: `/user/pages/stored/poster`
				})
			},
			toAppShare(type = 1) {
				let {
					shareInd = 0,
						imageUrl
				} = this

				let {
					id: pid = 0
				} = this.userInfo
				let {
					coach_id = 0
				} = this.mineInfo

				let {
					id,
					title,
				} = this.list.data[shareInd]
				let summary = '邀请你充值有优惠～'

				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/user/pages/stored/list?id=${id}&coach_id=${coach_id}&pid=${pid}`

				// #ifdef H5
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, summary, href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
				// #endif
				// #ifdef APP-PLUS
				this.$refs.show_share_item.close()
				let scene = type == 1 ? 'WXSceneSession' : 'WXSceneTimeline'
				uni.share({
					provider: "weixin",
					scene,
					type: 0,
					href,
					title,
					summary,
					imageUrl,
					success: function(res) {
					},
					fail: function(err) {
					}
				});
				// #endif
			},
		}
	}
</script>


<style lang="scss">
	.user-stored-list {

		.stored-bg {
			width: 100%;
			height: 163rpx;
			border-radius: 0 0 20rpx 20rpx;
			top: 0;
		}

		.record-info {
			.money {
				font-size: 44rpx;
			}

			// .record-list {
			// 	.list-item {
			// 		width: 33.33%;
			// 	}
			// }

		}

		.store-list {
			.list-item {
				width: 305rpx;
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

		.pay-btn {
			width: 283rpx;
			height: 90rpx;
		}

		.popup-share {
			.list-item {
				width: 50%;

				.item-image {
					width: 66rpx;
					height: 66rpx;
				}
			}
		}

		.cash-box{
			height: 130rpx;
			.cash-btn{
				width: 138rpx;
				height: 62rpx;
				border-radius: 62rpx;
			}
		}
	}
</style>
