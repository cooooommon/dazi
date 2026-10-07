<template>
	<view class="user-channel-index" v-if="isLoad">
		<view class="index-info pl-md pr-md pt-lg rel">
			<view class="abs index-info-bg" :style="{backgroundColor: primaryColor}"></view>
			<view class="index-info-box fill-base radius-32 pb-lg">
				<view class="box-top flex-between pl-lg pr-lg">
					<view class="flex-y-center">
						<text class="f-desc">渠道分销商：</text>
						<text class="f-desc text-bold">{{detail.user_name}}</text>
					</view>
					<view class="flex-y-center">
						<text class="f-desc">提成比例：</text>
						<text class="f-desc text-bold">{{detail.balance}}%</text>
					</view>
				</view>
				<view class="flex pt-sm ml-lg mr-lg pb-lg b-1px-b">
					<view class="flex-1 flex-center">
						<view class="">
							<view class="f-caption c-caption">可提现(元)</view>
							<view class="f-lg-title text-bold" :style="{color: primaryColor}">{{detail.cash}}</view>
						</view>
					</view>
					<view class="flex-center flex-1">
						<view class="">
							<view class="f-caption c-caption">未入账(元)</view>
							<view class="f-lg-title text-bold">{{detail.wait_price}}</view>
						</view>
					</view>
					<view class="cancel-auth iconfont icon-biaoqian c-caption flex-center abs"
						v-if="detail.status == 3">
						<view class="text-bold f-icontext abs">取消授权</view>
					</view>
				</view>
				<view class="box-count flex">
					<view class="flex-center flex-column flex-1" v-for="(item,index) in count" :key="index">
						<view class="f-sm-title text-bold">{{item.number}}</view>
						<view class="f-caption c-caption">{{item.name}}</view>
					</view>
				</view>
				<view class="flex-center">
					<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width: auto;"
						:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url: `/user/pages/cash-out?type=channel`})">
						<view class="f-desc c-base withdrawal-btn flex-center" :style="{backgroundColor: primaryColor}">我要提现</view>
					</auth>
				</view>
			</view>
		</view>
		<view class="radius-24 fill-base mt-md flex index-cont ml-md mr-md">
			
			<block v-for="(item,index) in cont" :key="index" >
				<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" class="flex-1"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url: item.link})">
					<view class="flex-1 flex-center flex-column" >
						<view class="index-cont-icon flex-center" :class="'index-cont-icon'+(index + 1)">
							<i class="iconfont c-base" :class="item.icon" style="font-size: 25px;"></i>
						</view>
						<view class="f-desc pt-sm">{{item.name}}</view>
					</view>
				</auth>
			</block>
		</view>
		<view class="radius-24 fill-base mt-md flex-between index-item ml-md mr-md pl-lg pr-lg " v-if="plugAuth.channelstaff">
			<view class="flex-y-center">
				<image src="https://lbqny.migugu.com/admin/peiwan/index-01.png" mode="aspectFill" class="index-item-icon"></image>
				<view class="pl-md">
					<view class="f-title text-bold">邀请员工</view>
					<view class="f-caption c-paragraph pt-sm">扩大推广范围，获得更多提成</view>
				</view>
			</view>
			<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width: auto;"
				:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url: `/user/pages/channel/generated-code`})">
				<view class="index-item-btn rel flex-center" :style="{borderColor: primaryColor}">
					<view class="abs item-bg" :style="{backgroundColor: primaryColor}"></view>
					<text class="f-desc" :style="{color: primaryColor}">绑定员工</text>
				</view>
			</auth>
		</view>
		<view class="radius-24 fill-base mt-md flex-between index-item ml-md mr-md pl-lg pr-lg ">
			<view class="flex-y-center">
				<image src="https://lbqny.migugu.com/admin/peiwan/index-02.png" mode="aspectFill" class="index-item-icon"></image>
				<view class="pl-md">
					<view class="f-title text-bold">我的渠道码</view>
					<view class="f-caption c-paragraph pt-sm">邀请用户下单获得高额抽成</view>
				</view>
			</view>
			<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width: auto;"
				:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url:`/user/pages/channel/poster`})">
				<view class="index-item-btn rel flex-center" :style="{borderColor: primaryColor}">
					<view class="abs item-bg" :style="{backgroundColor: primaryColor}"></view>
					<text class="f-desc" :style="{color: primaryColor}">查看邀请码</text>
				</view>
			</auth>
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
					{name: '我的收益', icon: 'iconwodeshouyi_1', link: '/user/pages/channel/my-income'},
					{name: '我的员工', icon: 'iconwodeyuangong', link: '/user/pages/channel/staff'},
					{name: '提现记录', icon: 'icontixianjilu2', link: '/user/pages/distribution/record?type=4'}
				],
				cont: [],
				count: [
					{name: '订单总金额(元)', number: 0},
					{name: '订单数量', number: 0},
					{name: '累计佣金', number: 0}
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
				if(!channelstaff){
					this.cont.splice(1,1)
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				await this.getDetail()
				this.isLoad = true
				this.$util.hideAll()
			},
			async getDetail(){
				let data = await this.$api.channel.index()
				this.count[0].number = data.order_price
				this.count[1].number = data.order_count
				this.count[2].number = data.total_cash
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
			height: 200rpx;
			border-radius: 0 0 24rpx 24rpx;
			width: 100%;
			top: 0;
			left: 0;
			z-index: -1;
		}
		
		.index-info-box{
			.box-top{
				height: 106rpx;
			}
			.box-count{
				margin-top: 40rpx;
			}
			.withdrawal-btn{
				width: 404rpx;
				height: 76rpx;
				border-radius: 76rpx;
				margin-top: 44rpx;
			}
		}
		
		.index-cont{
			padding: 35rpx 0;
			.index-cont-icon{
				width: 90rpx;
				height: 90rpx;
				border-radius: 90rpx;
			}
			.index-cont-icon1{
				background-image: linear-gradient(-135deg ,#FF8FA4,#FF447F)
			}
			.index-cont-icon2{
				background-image: linear-gradient(-135deg ,#FF9227,#FF5346)
			}
			.index-cont-icon3{
				background-image: linear-gradient(-135deg ,#876DFF,#7F65FF)
			}
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
	}
</style>