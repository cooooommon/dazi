<template>
	<view class="order-pages">
		<fixed v-if="configInfo.plugAuth.member">
			<tab @change="handerTabChange" :list="tabList" :activeIndex="currentInd*1" :activeColor="primaryColor"
				:width="100/tabList.length + '%'" height="100rpx" :isRadius="true"></tab>
		</fixed>
		<view class="order-item ml-lg mr-md b-1px-b pt-lg pb-lg flex-between" v-for="(item,index) in list.data" :key="index">
			<block v-if="currentInd == 0">
				<view class="">
					<view class="f-paragraph text-bold">
						<text>{{item.type == 1 ? '用户' : '下级'}}</text>
						<text class="pl-sm">{{item.type == 1? item.nickName : item.user_name}}</text>
						<text class="pl-sm">{{item.type == 1 ? `下单完成服务` : `完成服务`}}</text>
					</view>
					<view class="pt-sm f-caption c-caption">{{item.create_time}}</view>
				</view>
				<view class="">
					<view class="f-title text-bold" :style="{color: primaryColor}">+{{item.pay_price}}</view>
					<view class="f-title text-bold c-success" v-if="item.type == 1">{{item.user_balance}}%</view>
				</view>
			</block>
			<block v-else>
				<view class="">
					<view class="f-paragraph text-bold">
						<text>粉丝</text>
						<text class="pl-sm">{{item.nickName}}</text>
						<text class="pl-sm">{{`购买${item.title}`}}</text>
					</view>
					<view class="pt-sm f-caption c-caption">{{$util.formatTime(item.pay_time*1000, 'YY-M-D h:m:s')}}</view>
				</view>
				<view class="">
					<view class="f-title text-bold" :style="{color: primaryColor}">+{{item.price}}</view>
					<view class="f-title text-bold c-success">{{Number(item.share_balance)}}%</view>
				</view>
			</block>
		</view>
		
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		
		<view class="space-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				list: {
					data: []
				},
				isLoad: false,
				loading: true,
				param: {
					page: 1
				},
				tabList: [{
					title: '订单佣金',
					id: 1,
				}, {
					title: '会员卡佣金',
					id: 2,
				}],
				currentInd: 0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.$util.showLoading()
			this.initIndex()
		},
		onUnload() {
			this.$util.back()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods: {
			...mapMutations([]),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				
				await this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				
			},
			handerTabChange(e){
				this.$util.showLoading()
				this.loading = true
				this.param.page = 1
				this.currentInd = e
				this.list.data = []
				this.getList()
			},
			async getList() {
				let {
					list: oldList,
					param,
					currentInd = 0
				} = this
				let methodArr = {
					0: {
						methodKey: 'mine',
						methodModel: 'myTeamWater'
					},
					1: {
						methodKey: 'memberdiscount',
						methodModel: 'cashList',
					}
				}
				
				let newList = await this.$api[methodArr[currentInd].methodKey][methodArr[currentInd].methodModel](param);
			
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
	}
</script>

<style>
	page{
		background-color: #fff;
	}
</style>