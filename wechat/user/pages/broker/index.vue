<template>
	<view class="user-channel-index" v-if="isLoad">
		<view class="index-info pl-md pr-md rel">
			<view class="abs index-info-bg" :style="{background: `linear-gradient(${primaryColor},${primaryColor},#f6f6f6)`}"></view>
			<view class="flex-between index-top pl-md pr-lg rel">
				<text class="c-base f-md-title ellipsis flex-1 pr-md">经纪人: {{detail.name}}</text>
				<text class="f-mini-title c-base">提成比例:{{detail.balance}}%</text>
			</view>
			<view class="index-info-box fill-base radius-18 pb-lg rel">
				<!-- <view class="box-top flex-between pl-lg pr-lg">
					<view class="flex-y-center">
						<text class="f-desc">渠道分销商：</text>
						<text class="f-desc text-bold">{{detail.user_name}}</text>
					</view>
					<view class="flex-y-center">
						<text class="f-desc">提成比例：</text>
						<text class="f-desc text-bold">{{detail.balance}}%</text>
					</view>
				</view> -->
				<view class="flex pt-sm ml-lg mr-lg pt-lg pb-lg b-1px-b">
					<view class="flex-1 flex-center">
						<view class="">
							<view class="f-lg-title text-bold flex-center">{{detail.cash}}</view>
							<view class="f-caption c-caption flex-center">可提现(元)</view>
						</view>
					</view>
					<view class="flex-center flex-1">
						<view class="">
							<view class="f-lg-title text-bold flex-center">{{detail.wait_cash}}</view>
							<view class="f-caption c-caption flex-center">未入账(元)</view>
						</view>
					</view>
					<view class="cancel-auth iconfont icon-biaoqian c-caption flex-center abs"
						v-if="detail.status == 3">
						<view class="text-bold f-icontext abs">取消授权</view>
					</view>
				</view>
				<view class="box-count flex">
					<view class="flex-center flex-column flex-1 rel" :class="[{'box-count-item' : index == 1}]" v-for="(item,index) in count" :key="index">
						<view class="f-sm-title text-bold">{{item.number}}</view>
						<view class="f-caption c-caption">{{item.name}}</view>
					</view>
				</view>
				<view class="flex-center">
					<view class="f-desc c-base withdrawal-btn flex-center" :style="{backgroundColor: primaryColor}" 
					@tap="$util.goUrl({url: `/user/pages/cash-out?type=broker`})">我要提现</view>
				</view>
			</view>
		</view>
		<view class="radius-16 fill-base mt-md flex index-cont ml-md mr-md">
			<view class="flex-1 flex-center flex-column" v-for="(item,index) in cont" :key="index" @tap="$util.goUrl({url: item.link})">
				<view class="index-cont-icon flex-center" :style="{backgroundColor: primaryColor}">
					<i class="iconfont c-base" :class="item.icon" style="font-size: 25px;"></i>
				</view>
				<view class="f-desc pt-sm">{{item.name}}</view>
			</view>
		</view>
		<view class="radius-16 fill-base mt-md flex index-cont ml-md mr-md">
			<view class="flex-1 flex-column flex-center">
				<view class="f-sm-title text-bold">{{detail.today_coach_num}}</view>
				<view class="f-paragraph c-paragraph pt-sm">今日新增{{$t('action.attendantName')}}</view>
			</view>
			<view class="flex-1 flex-column flex-center">
				<view class="f-sm-title text-bold">{{detail.coach_num}}</view>
				<view class="f-paragraph c-paragraph pt-sm">累计邀请{{$t('action.attendantName')}}</view>
			</view>
		</view>
		<view @tap="$util.goUrl({url: `/user/pages/broker/poster`})" class="ml-md mr-md invite-box mt-md rel flex-between" :style="{background: `linear-gradient(${subColor},${primaryColor})`}">
			<image src="https://lbqny.migugu.com/admin/peiwan/invite-bg.png" mode="aspectFill" class="invite-bg abs"></image>
			<view class="rel">
				<view class="c-base f-lg-title text-bold">推荐收入</view>
				<view class="pt-sm c-base f-paragraph ">推荐{{$t('action.attendantName')}}入驻, 享高额推荐佣金</view>
			</view>
			<view class="f-desc c-base invite-btn flex-center rel">立即邀请</view>
		</view>
		
		
		<view class="space-footer"></view>
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
			return{
				isLoad: false,
				contOld: [
					{name: '我的收益', icon: 'iconwodeshouyi_1', link: '/user/pages/broker/income'},
					{name: '邀请的'+this.$t('action.attendantName'), icon: 'iconwodequdaoshang', link: '/user/pages/broker/invitation-wizard'},
					{name: '提现记录', icon: 'icontixianjilu1', link: '/user/pages/distribution/record?type=7'}
				],
				cont: [],
				count: [
					{name: '累计佣金(元)', number: 0},
					{name: '已提现(元)', number: 0},
					{name: '总成交金额(元)', number: 0}
				],
				detail: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			plugAuth: state => state.config.configInfo.plugAuth,
		}),
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onLoad() {
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getPlugAuth', 'getUserInfo', 'getMineInfo', 'getCoachInfo',
				'updateCommonOptions',
			]),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				this.$util.showLoading()
				await this.getPlugAuth()
				
				let {
					channelstaff
				} = this.plugAuth
				
				this.cont = this.$util.deepCopy(this.contOld)
				
				await this.getDetail()
				this.isLoad = true
				this.$util.hideAll()
			},
			async getDetail(){
				let data = await this.$api.mine.getBrokerIndex()
				this.count[0].number = data.total_cash
				this.count[1].number = data.extract_total_price
				this.count[2].number = data.order_price
				this.detail = data
			},
			initRefresh() {
				this.initIndex(true)
			},
		}
	}
</script>

<style lang="scss" scoped>
	.user-channel-index{
		.index-info-bg{
			height: 340rpx;
			width: 100%;
			top: 0;
			left: 0;
		}
		
		.index-top{
			height: 173rpx;
		}
		
		.index-info-box{
			.box-top{
				height: 106rpx;
			}
			.box-count{
				margin-top: 30rpx;
				.box-count-item{
					&::before{
						content: '';
						width: 1px;
						height: 30rpx;
						border-left: 1px solid #F2F2F2;
						left: 0;
						top: 50%;
						transform: translate(0, -50%);
						position: absolute;
					}
					&::after{
						content: '';
						width: 1px;
						height: 30rpx;
						border-left: 1px solid #F2F2F2;
						top: 50%;
						transform: translate(0, -50%);
						right: 0;
						position: absolute;
					}
				}
			}
			.withdrawal-btn{
				width: 606rpx;
				height: 76rpx;
				border-radius: 76rpx;
				margin-top: 40rpx;
			}
		}
		
		.index-cont{
			padding: 35rpx 0;
			.index-cont-icon{
				width: 90rpx;
				height: 90rpx;
				border-radius: 90rpx;
			}
			// .index-cont-icon1{
			// 	background-image: linear-gradient(-135deg ,#FF8FA4,#FF447F)
			// }
			// .index-cont-icon2{
			// 	background-image: linear-gradient(-135deg ,#FF9227,#FF5346)
			// }
			// .index-cont-icon3{
			// 	background-image: linear-gradient(-135deg ,#876DFF,#7F65FF)
			// }
		}
		.index-item{
			height: 205rpx;
			.index-item-icon{
				width: 116rpx;
				height: 116rpx;
			}
			
			.index-item-btn{
				width: 166rpx;
				height: 72rpx;
				border-radius: 72rpx;
				border: 1px solid;
				.item-bg{
					left: 0;
					top: 0;
					width: 166rpx;
					height: 72rpx;
					border-radius: 72rpx;
					opacity: 0.1;
				}
			}
		}
		.cancel-auth {
			width: 110rpx;
			height: 100rpx;
			font-size: 100rpx;
			top: 20rpx;
			right: 0rpx;
		
			.text-bold {
				height: 26rpx;
				transform: rotate(-32deg);
			}
		}
		.invite-box{
			height: 180rpx;
			border-radius: 180rpx;
			padding: 0 40rpx 0 60rpx;
			.invite-bg{
				width: 329rpx;
				height: 131rpx;
				right: 100rpx;
				bottom: 20rpx;
			}
			.invite-btn{
				width: 150rpx;
				height: 70rpx;
				border-radius: 70rpx;
				background-color: #FFCA0E;
			}
		}
	}
</style>