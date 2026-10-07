<template>
	<view class="order-pages" v-if="detail.id">

		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-base radius-16"
			:style="{background:primaryColor}">
			<view class="flex-y-baseline text-bold">{{statusType[detail.status]}}
				<view class="f-caption text-normal ml-md" v-if="detail.status == 2">退款金额¥{{detail.refund_price}}</view>
			</view>
		</view>

		<view class="address-info flex-warp mt-lg ml-lg mr-lg pd-lg fill-base radius-16" v-if="!detail.store_id">
			<view class="address-icon flex-center c-base radius"
				:style="{background:`linear-gradient(to right, ${subColor}, ${primaryColor})`}"><i
					class="iconfont iconjuli"></i></view>
			<view class="flex-1 flex-between ml-md">
				<view class="max-540">
					<view class="flex-y-baseline username c-title text-bold">{{detail.address_info.user_name}}
						<view class="ml-md f-desc c-caption">{{detail.address_info.mobile}}</view>
					</view>
					<view class="f-desc c-title">
						{{`${detail.address_info.address} ${detail.address_info.address_info}`}}
					</view>
				</view>
			</view>
		</view>


		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16" v-if="detail.store_id">
			<view class="flex-between">
				<view>下单人</view>
				<view class="c-title">{{detail.address_info.user_name}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>联系方式</view>
				<view class="c-title">{{detail.address_info.mobile}}</view>
			</view>
		</view>

		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16">

			<view class="flex-center mb-lg" v-for="(aitem,aindex) in detail.order_goods" :key="aindex">
				<image mode="aspectFill" class="avatar lg radius-16" :src="aitem.goods_cover"></image>
				<view class="flex-1 ml-md">
					<view class="f-mini-title c-title text-bold max-450 ellipsis"> {{aitem.goods_name}} </view>
					<view class="f-caption c-caption mt-md">
						服务{{$t('action.attendantName')}}：{{detail.coach_info ? detail.coach_info.coach_name : '-'}}</view>
					<view class="flex-between">
						<view class="flex-y-baseline f-caption c-warning">¥<view class="f-title text-bold">
								{{aitem.goods_price}}
							</view>
						</view>
						<view class="c-paragraph">x{{aitem.num}}</view>
					</view>
				</view>
			</view>
			<view class="flex-between">
				<veiw class="flex-1"></veiw>
				<view class="flex-y-center f-desc c-title text-bold">
					共
					<view class="c-warning">{{detail.all_goods_num}}
					</view>
					件
					合计：<view class="f-icontext">¥</view>{{detail.apply_price}}
					<view class="flex-y-center ml-md" v-if="detail.car_price*1>0">含车费：
						<view class="f-icontext">¥</view>{{detail.car_price}}
					</view>
				</view>
			</view>
			<view class="flex-between mt-lg" v-if="detail.status != 2 && options.is_coach != 1">
				<view class="flex-1"></view>
				<button @tap.stop="toTel" class="clear-btn order" v-if="detail.status == 3">联系平台</button>
				<button @tap.stop="toCancel" class="clear-btn order"
					:style="{color:'#fff',background:primaryColor,border:`1rpx solid ${primaryColor}`}"
					v-if="detail.status == 1">取消退款</button>
			</view>
		</view>


		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16">
			<view class="flex-between pb-lg f-title c-title text-bold b-1px-b">退款信息</view>
			<view class="pt-lg f-desc">
				<view class="flex-between mb-md">
					<view>退款单号</view>
					<view class="c-caption flex-y-center ml-md">
						<view class="max-400 ellipsis">{{detail.order_code}}</view>
						<view @tap="$util.goUrl( {url:`${detail.order_code}`,openType:'copy'})"
							class="copy-btn radius-5 f-icontext ml-sm">复制</view>
					</view>
				</view>
				<view class="flex-between mb-md" v-if="detail.out_refund_no">
					<view>微信退款单号</view>
					<view class="c-caption flex-y-center ml-md">
						<view>{{detail.out_refund_no}}</view>
						<view @tap="$util.goUrl( {url:`${detail.out_refund_no}`,openType:'copy'})"
							class="copy-btn radius-5 f-icontext ml-sm">复制</view>
					</view>
				</view>
				<view class="flex-warp mb-md">
					<view>提交日期</view>
					<view class="c-caption text-right flex-1 ml-md">{{detail.create_time}}</view>
				</view>
				<view class="flex-warp mb-md" v-if="detail.status != 1">
					<view>审核日期</view>
					<view class="c-caption text-right flex-1 ml-md">{{detail.refund_time}}</view>
				</view>
				<view class="flex-column mb-md">
					<view>退款原因</view>
					<view class="c-caption mt-sm">{{detail.text}}</view>
				</view>
				<view class="flex-column" v-if="detail.imgs && detail.imgs.length > 0">
					<view>上传图片</view>
					<view class="flex-warp">
						<block v-for="(item,index) in detail.imgs" :key="index">
							<image @tap.top="previewImage(item,detail.imgs)" class="refund-img mt-md mr-md radius-10"
								:src="item" />
						</block>
					</view>
				</view>
			</view>
		</view>

		<view class="space-footer"></view>

		<common-popup @confirm="confirmCancel" ref="cancel_item" type="CANCEL_REFUND_ORDER" :info="popupInfo">
		</common-popup>

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
				statusType: {
					1: '退款中',
					2: '退款成功',
					3: '退款失败',
				},
				detail: {},
				popupInfo: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		methods: {
			...mapMutations(['']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				let {
					id
				} = this.options
				this.detail = await this.$api.order.refundOrderInfo({
					id
				})
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.initIndex(true)
			},
			// 取消退款
			async toCancel() {
				let {
					order_code,
					order_goods,
				} = this.detail
				let {
					goods_cover: image,
				} = order_goods[0]
				this.popupInfo = {
					name: `退款单号：${order_code}`,
					image,
				}
				this.$refs.cancel_item.open()
			},
			// 确认：取消退款
			async confirmCancel() {
				let {
					id,
				} = this.detail
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.order.cancelRefundOrder({
						id
					})
					this.$util.hideAll()
					this.$util.showToast({
						title: `取消成功`
					})
					this.lockTap = false
					this.$refs.cancel_item.close()
					setTimeout(() => {
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: `navigateBack`
						})
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
			toTel() {
				let {
					hide_admin_mobile,
					mobile,
					im_type,
					qywx_company_id,
					qywx_kid
				} = this.configInfo
				
				if (im_type == 3) {
					// #ifdef H5
					window.location.href = qywx_kid
					// #endif
					// #ifndef H5 
					// #ifdef MP-WEIXIN 
					try {
						console.log(qywx_kid, qywx_company_id)
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
						console.log(res, "==============res")
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
			
				let url = mobile
				this.$util.goUrl({
					url,
					openType: 'call'
				})
			},
			previewImage(current, urls) {
				this.$util.previewImage({
					current,
					urls
				})
			}
		}
	}
</script>


<style lang="scss">
	button.order {
		border-radius: 30rpx;
	}
</style>
