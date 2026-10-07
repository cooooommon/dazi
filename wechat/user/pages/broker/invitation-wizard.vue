<template>
	<view class="wizard-index" v-if="isLoad">
		<view class="mt-md ml-md mr-md radius-16 fill-base pd-lg flex-between" v-for="(item,index) in list.data" :key="index">
			<view class="rel pb-sm">
				<image class="w-header" :src="item.work_img" mode="aspectFill"></image>
				<view class="flex-center abs w-status f-icontext " :class="[{'c-base': [1,2].includes(item.text_type)}]" 
				:style="{background: item.text_type == 1 ? primaryColor : textTypeColor[item.text_type]}">{{textType[item.text_type]}}</view>
			</view>
			<view class="flex-1 pl-lg">
				<view class="flex-between">
					<text class="f-ms-title text-bold flex-1 ellipsis pr-lg max-400">{{item.coach_name}}</text>
					<text class="f-paragraph c-icontext">{{item.title}}</text>
				</view>
				<view class="flex-y-center" style="margin-top: 6rpx;">
					<text class="c-paragraph">服务金额：</text>
					<text class="pl-lg c-warning">¥{{item.price}}</text>
				</view>
				<view class="flex" style="margin-top: 6rpx;">
					<text class="c-paragraph">所属代理商：</text>
					<text class="pl-lg flex-1">{{item.agent_name}}</text>
				</view>
			</view>
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
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return{
				isLoad: false,
				loading: true,
				list: {
					data: []
				},
				param: {
					page: 1
				},
				textType: {
					1: '可接单',
					2: '接单中',
					3: '休息中',
					4: '不可接单'
				},
				textTypeColor: {
					2: '#24C858',
					3: '#D6D6D6',
					4: '#D6D6D6'
				}
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
			this.$util.showLoading()
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
				uni.setNavigationBarTitle({
					title: '邀请的' + this.$t('action.attendantName')
				})
				// #endif
				//await this.getPlugAuth()
				await this.getList(1)
			},
			async getList(page){
				if(page){
					this.param.page = 1
				}
				let {
					list: oldList,
					param
				} = this
				let newList = await this.$api.mine.getCoach(param)
				if (param.page == 1) {
					this.list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList;
				}
				this.loading = false;
				this.isLoad = true
				this.$util.hideAll()
			},
			initRefresh() {
				this.$util.showLoading()
				this.initIndex(true)
			},
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.$util.showLoading()
			this.param.page += 1
			await this.getList()
		},
	}
</script>

<style lang="scss" scoped>
	.wizard-index{
		.w-header{
			width: 140rpx;
			height: 140rpx;
			border-radius: 140rpx;
		}
		.w-status{
			width: 100rpx;
			height: 32rpx;
			border-radius: 32rpx;
			bottom: 0;
			left: 50%;
			transform: translate(-50% , 0);
		}
	}
</style>