<template>
	<view class="invite-page">
		<fixed v-if="configInfo.plugAuth.distributor">
			<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1" :activeColor="primaryColor"
				:width="100/tabList.length + '%'" height="100rpx"></tab>
		</fixed>
		<block v-if="activeIndex == 0">
			<view class="invite-item fill-base radius-16 mt-md ml-md mr-md flex-y-center pd-lg" v-for="(item,index) in list.data" :key="index">
				<image :src="item.avatarUrl" mode="aspectFill" class="item-header"></image>
				<view class="pl-md flex-1">
					<view class="f-title text-bold">{{item.nickName}}</view>
					<view class="flex-between pt-sm">
						<text class="f-desc c-paragraph">订单总金额</text>
						<text class="f-paragraph text-bold">¥{{item.pay_price}}</text>
					</view>
					<view class="pt-sm flex-between">
						<text class="f-desc c-paragraph">绑定时间</text>
						<text class="f-desc c-paragraph">{{item.create_time}}</text>
					</view>
				</view>
			</view>
			<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
			</load-more>
			<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		</block>
		<block v-if="activeIndex == 1">
			<view class="invite-item fill-base radius-16 mt-md ml-md mr-md flex-y-center pd-lg" v-for="(item,index) in list.data" :key="index">
				<text class="pr-lg">{{index + 1}}</text>
				<image :src="item.avatarUrl" mode="aspectFill" class="item-header"></image>
				<view class="f-title text-bold pl-md flex-1">{{item.nickName}}</view>
				<view class="f-title text-bold c-warning">￥{{item.pay_price}}</view>
			</view>
			<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
			</load-more>
			<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		</block>
		
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
			return {
				activeIndex: 0,
				isLoad: false,
				loading: true,
				tabList: [
					{title: '我的下级'},
					{title: '我的用户'}
				],
				list: {
					data: []
				},
				param: {
					page: 1,
					type: 1
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.$util.showLoading()
			this.initIndex()
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods: {
			...mapActions(['getConfigInfo']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				if(refresh || !this.configInfo.id){
					await this.getConfigInfo()
				}
				
				let {
					plugAuth = {}
				} = this.configInfo
				if(!plugAuth.distributor){
					this.param.type = 2
					this.activeIndex = 1
				}
				
				await this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			handerTabChange(e){
				this.activeIndex = e
				this.param.type = e + 1
				this.list.data = []
				this.$util.showLoading()
				this.initRefresh()
			},
			async getList() {
				let {
					list: oldList,
					param,
				} = this
				let newList = await this.$api.mine.myTeam(param);
			
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
				this.$util.showLoading()
				this.initIndex(true)
			},
		}
	}
</script>

<style lang="scss" scoped>
	.invite-page{
		.invite-item{
			.item-header{
				width: 124rpx;
				height: 124rpx;
				border-radius: 124rpx;
			}
		}
	}
</style>