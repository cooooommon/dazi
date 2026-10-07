<template>
	<view class="choose-agent" v-if="isLoad">
		<view class="mt-md ml-md mr-md radius-16 fill-base pd-lg flex-between" v-for="(item,index) in list.data" :key="index" @tap="chooseAgent(index)">
			<view class="flex-center">
				<i class="iconfont" :class="[{'icon-xuanze-fill':check_admin.id == item.id },{'icon-xuanze':check_admin.id != item.id }]" 
				style="font-size: 18px;" :style="{color: check_admin.id == item.id ? primaryColor : '#ccc' }"></i>
			</view>
			<view class="flex-between flex-1 pl-lg">
				<image :src="item.avatarUrl" mode="aspectFill" class="a-header"></image>
				<view class="flex-1 pl-lg">
					<view class="">
						<text class="f-title text-bold">{{item.name}}</text>
						<text class="f-mini-title">({{item.nickName}})</text>
					</view>
					<view class="f-desc c-paragraph pt-sm">{{item.title}}代理</view>
				</view>
			</view>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-max-footer"></view>
		<fix-bottom-button @confirm="toConfirm" :text="[{text:'确定',type:'confirm'}]" bgColor="#fff">
		</fix-bottom-button>
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
					page: 1,
					limit: 10
				},
				check_admin: {}
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
				// #endif
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
				let newList = await this.$api.mine.adminList(param)
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
			chooseAgent(index){
				let {id,name} = this.list.data[index]
				this.check_admin = {
					id,
					agent_name: name
				}
			},
			toConfirm() {
				let {
					id = 0,
				} = this.check_admin
				if (!id) {
					this.$util.showToast({
						title: `请选择代理商`
					})
					return
				}
				this.$util.getPage(-1).check_admin = this.check_admin
				this.$util.goUrl({
					url: 1,
					openType: `navigateBack`
				})
			}
			
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
	.choose-agent{
		.a-header{
			width: 130rpx;
			height: 130rpx;
			border-radius: 130rpx;
		}
	}
</style>