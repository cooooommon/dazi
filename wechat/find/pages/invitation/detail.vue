<template>
	<view class="order-pages" v-if="isLoad">
		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16">
			<view class="flex-between pb-lg">
				<view class="f-paragraph c-title max-380 ellipsis">预约内容</view>
			</view>
			<view class="flex-center">
				<!-- <block v-for="(item,index) in detail.img" :key="index">
					<image mode="aspectFill" class="avatar lg radius-16" :src="item"></image>
				</block> -->
				<image mode="aspectFill" class="avatar lg radius-16 mr-md" v-if="detail.img[0]" :src="detail.img[0]"
					@tap="$util.previewImage({current: detail.img[0] , urls: detail.img})"></image>
				<view class="flex-1">
					<view class="flex-between">
						<view class="goods-title f-title c-title ellipsis" style="max-width: 450rpx;">
							{{detail.content}}
						</view>
					</view>
					<view class="f-caption c-caption">{{$t('action.attendantName')}}：{{detail.coach_name||'无'}}</view>
					<view class="f-caption c-caption">预约时间：{{detail.start_time}}</view>
					<view class="flex-between">
						<view class="flex-y-baseline f-caption" style="color: #FF4C88">¥<view class="f-title text-bold">
								{{detail.price}}
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class="flex-between">
				<view>服务项目</view>
				<view class="c-title flex-1 pl-md flex" style="justify-content: flex-end;">{{detail.type_name}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>下单人</view>
				<view class="c-title">{{detail.nickName}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>联系方式</view>
				<view class="c-title">{{detail.phone}}</view>
			</view>
			<view class="mt-md">
				<view>服务地址</view>
				<view class="c-title mt-sm">{{detail.address}}
				</view>
			</view>
			<view class="mt-md" v-if="detail.check_text">
				<view>拒绝原因</view>
				<view class="c-title mt-sm pre-wrap">{{detail.check_text}}</view>
			</view>
		</view>
		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class="flex-between" v-if="userPageType == 2">
				<image class="avatar sm radius" :src="detail.is_hide==0 ? detail.avatarUrl : avatarUrl"
					mode="aspectFill"></image>
				<!-- <view class="c-title">{{detail.coach_info && detail.coach_info.coach_name}}</view> -->
			</view>
			<view class="flex-between" v-else-if="detail.coach_id > 0 && userPageType == 1">
				<image class="avatar sm radius" :src="detail.work_img" mode="aspectFill"></image>
				<!-- <view class="c-title">{{detail.coach_info && detail.coach_info.coach_name}}</view> -->
				<view class="f-paragraph flex-1" style="padding-left: 60rpx;">
					<text :style="{color:primaryColor}"
						@tap="$util.goUrl({url: `/user/pages/technician-info?id=${detail.coach_id}`})">查看资料</text>
				</view>
			</view>
			<view class="flex-between mt-md">
				<view>下单时间</view>
				<view class="c-title">{{detail.create_time}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>服务时间</view>
				<view class="c-title">{{detail.start_time}}-{{detail.end_time}}</view>
			</view>
			<!-- <view class="flex-between mt-md">
				<view>服务项目费用</view>
				<view style="color: #FF4C88">¥{{ detail.member_status ? detail.type_price : detail.price}}元/单</view>
			</view> -->
			<block v-if="detail.member_status">
				<view class="flex-between mt-md">
					<view>会员卡折扣</view>
					<view style="color: #FF4C88">{{detail.member_balance}}折 (优惠¥{{detail.member_discount}})</view>
				</view>
			</block>
			<view class="flex-between mt-md">
				<view>实际支付费用</view>
				<view style="color: #FF4C88">¥{{detail.price}}元/单</view>
			</view>
			<view class="flex-between mt-md">
				<view>支付方式</view>
				<view class="flex-y-baseline c-title"><i class="iconfont mr-sm" :class="payType[detail.pay_type].icon"
						:style="{color: payType[detail.pay_type].iconColor}"></i>{{payType[detail.pay_type].text}}
				</view>
			</view>
			<view class=" mt-md">
				<view class="flex-between">
					<view>车费</view>
					<view class="c-title">{{detail.is_car == 1 ? '报销' : '不报销'}}</view>
				</view>
				<view v-if="detail.is_car == 1" class="text-right f-desc" style="color: #E82F21;">{{$t('action.attendantName')}}到达目的地后与客户自行报销</view>
			</view>
			<view class="flex-between mt-md pt-md b-1px-t">
				<view></view>
				<view class="flex-y-baseline c-title">总计：<view style="color: #FF4C88">¥{{detail.price}}</view>
				</view>
			</view>
		</view>
		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class=" flex-y-center flex-warp">
				<view class="flex-between c-title">订单编号：</view>
				<view class="flex-between flex-1 ">
					<view class="c-title">{{detail.order_code}}</view>
					<view class="f-icontext d-copy text-center"
						@tap.stop="$util.goUrl({openType:'copy',url:detail.order_code})"
						:style="{borderColor:primaryColor ,color:primaryColor}">复制</view>
				</view>
			</view>
		</view>
		<view class="space-max-footer"></view>
		<view class="footer-info fix fill-base">
			<view class="flex-between pd-lg">
				<view></view>
				<view class="flex-center f-desc c-title" v-if="options.refund != 4">
					<!--$util.goUrl({url:`/pages/message-detail?order_id=${detail.id}&order_type=${userPageType == 1 ? 2 : 1}`})-->
					<view v-if="detail.status==3" :style="{border:`1px solid #ccc`,backgroundColor:'#fff'}"
						@tap.stop="toTel(detail.id)" class="item-btn flex-center radius mr-md">联系Ta</view>
					<view class="item-btn flex-center radius mr-md"
						:style="{border:`1px solid #ccc`,backgroundColor:'#fff'}"
						v-if="(detail.status==1 || detail.status==2) && userPageType == 1 && [0,3].includes(detail.is_refund) && new Date(detail.new_start_time).getTime() > new Date().getTime()"
						@tap.stop="changeOrder(detail.id, 2 , detail.start_time)">取消订单</view>
					<view class="c-base flex-center f-desc item-btn ml-md pl-md pr-md rel" style="padding: 0 20rpx;"
						v-if="detail.status==2 && userPageType == 1" :style="{backgroundColor:primaryColor}"
						@tap.stop="$util.goUrl({url: `/find/pages/application?id=${detail.id}`})">
						查看报名人员
						<view class="abs bubble" v-if="detail.apply_num > 0">
							<text class="c-base">{{detail.apply_num}}</text>
						</view>
					</view>
					<block v-if="detail.status==2 && userPageType!= 1">
						<view class="c-base flex-center f-desc item-btn ml-md" v-if="detail.apply_status == 0"
							:style="{backgroundColor:primaryColor}"
							@tap.stop="changeOrder(detail.id,3, detail.apply_status)">我要报名</view>
						<view class="c-base flex-center f-desc item-btn ml-md" v-if="detail.apply_status == 1"
							:style="{backgroundColor:primaryColor}"
							@tap.stop="changeOrder(detail.id,3, detail.apply_status)">取消报名</view>
						<view class="c-base flex-center f-desc item-btn ml-md c-caption" v-if="detail.apply_status == 3"
							:style="{backgroundColor:'#EFEFEF'}" @tap.stop="">我要报名</view>
					</block>
					<view class="item-btn flex-center radius mr-md c-base"
						v-if="detail.status==3 && (detail.is_refund == 0 || detail.is_refund == 3)"
						:style="{backgroundColor:primaryColor}" @tap.stop="changeOrder(detail.id,4,detail.end_time)">确认完成</view>
				</view>
			</view>
			<view class="space-safe"></view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				isLoad: false,
				payType: {
					1: {
						icon: 'iconzhifubao',
						iconColor: '#02A9F0',
						text: '支付宝支付'
					},
					2: {
						icon: 'icon-weixin',
						iconColor: '#28C445',
						text: '微信支付'
					},
					3: {
						icon: 'iconqianbao',
						iconColor: '#FF4C88',
						text: '账户余额'
					},
				},
				id: '',
				detail: {},
				userPageType: 2,
				options: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			//userPageType: state => state.user.userPageType,
			avatarUrl: state => state.config.avatarUrl,
		}),
		async onLoad(options) {
			this.userPageType = options.userPageType || 2
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			this.id = options.id
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			if (this.configInfo.balance_character) {
				this.payType[3].text = this.configInfo.balance_character
			}
			this.$util.showLoading()
			this.getOrderInfo()
		},
		methods: {
			initRefresh(loading = 1) {
				if(loading == 1){
					this.$util.showLoading()
				}
				this.getOrderInfo(loading)
			},
			async getOrderInfo(loading = 1) {
				let data = await this.$api.find.orderInfo({
					id: this.id
				})
				data.new_start_time = data.start_time.replace(/-/g, '/')
				data.phone = this.forMate(data.phone, 3, 7, '*')
				this.detail = data
				if(loading == 1){
					this.$util.hideAll()
				}
				this.isLoad = true
			},
			forMate(str, start, end, middle) {
				if (start < end) {
					var len = Math.abs(start - end);
					var text = "";
					for (var i = 0; i < len; i++) {
						text += middle;
					}
					return str.substring(0, start) + text + str.substring(end);
				}
				return false;
			},
			async changeOrder(id, index, type) {
				if (index == 2) {
					let that = this
					uni.showModal({
						title: '提示',
						content: '是否确认取消接单',
						success: async function(res) {
							if (res.confirm) {
								await that.$api.find.cancel({
									id
								})
								that.$util.showToast({
									title: '取消成功'
								});
								that.initRefresh(0)
								setTimeout(()=>{
									that.back()
								},1000)
							}
						}
					});
				} else if (index == 3) {
					if (type == 0) {
						await this.$api.find.orderApply({
							order_id: id
						})
						this.$util.showToast({
							title: '报名成功'
						});
					} else if (type == 1) {
						await this.$api.find.cancelApply({
							order_id: id
						})
						this.$util.showToast({
							title: '取消报名成功'
						});
					}
					this.initRefresh(0)
					setTimeout(()=>{
						this.back()
					},1000)
				} else if (index == 4) {
					let date = type.replace(/-/g, '/')
					if(this.userPageType == 2 && new Date(date).getTime() > new Date().getTime() ){
						this.$util.showToast({
							title: '未到服务完成时间'
						});
						return
					}
					let model = this.userPageType == 1 ? 'userComplete' : 'complete'
					await this.$api.find[model]({
						id
					})
					this.$util.showToast({
						title: '已完成'
					});
					this.initRefresh(0)
					setTimeout(()=>{
						this.back()
					},1000)
				}
			},
			// 联系客户
			async toTel(id) {
				// $util.goUrl({url: userPageType == 2 ? item.user_phone : item.coach_phone, openType: 'call'})
				let model = 'technician'
				if (this.userPageType == 1) { // 用户端
					model = 'order'
				}
				let url = await this.$api[model].getDemandVirtualPhone({
					order_id: id
				})
				this.$util.goUrl({
					url,
					openType: `call`
				})
			},
			// 刷新上页数据
			back () {
				let pages = getCurrentPages(); //当前页面栈
				if (pages.length > 1) {
					var beforePage = pages[pages.length - 2]; //获取上一个页面实例对象  
					//触发父页面中的方法change()  
					beforePage.$vm.initRefresh(false)
				}
			},
		}
	}
</script>

<style lang="scss">
	.item-btn {
		min-width: 140rpx;
		height: 56rpx;
		line-height: 56rpx;
		border-radius: 56rpx;
	
		.bubble {
			right: 0;
			top: -30rpx;
			text {
				background-color: #FF5967;
				padding: 2px 4px;
				font-size: 18rpx;
				border-radius: 30rpx;
			}
		}
	}
</style>
