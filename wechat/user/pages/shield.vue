<template>
	<view class="user-shield" v-if="isLoad">
		<!-- <fixed v-if="configInfo.plugAuth.dynamic">
			<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1" :activeColor="primaryColor"
				width="50%" height="100rpx" color="#999999"></tab>
		</fixed> -->
		<view class="list-item flex-center fill-base mt-md ml-md mr-md pd-lg radius-16"
			v-for="(item,index) in list.data" :key="index">
			<image mode="aspectFill" class="avatar radius" :src="item.work_img"></image>
			<view class="flex-1 flex-between ml-md">
				<view class="f-title c-black text-bold max-300 ellipsis">{{item.coach_name}}</view>
				<view @tap.stop="toRemove(index)" class="remove-btn flex-center f-desc radius"
					:style="{color:primaryColor,border:`1rpx solid ${primaryColor}`}">移除列表</view>
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
			return {
				isLoad: false,
				tabList: [
				{
					id: 2,
					title: '不喜欢Ta'
				}, 
				{
					id: 1,
					title: '不想看Ta的作品'
				}],
				activeIndex: 0,
				loading: true,
				param: {
					page: 1
				},
				list: {
					data: []
				},
				lockTap: false

			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.initIndex()
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
		methods: {
			...mapActions(['getConfigInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await Promise.all([this.getConfigInfo(), this.getList()])
				this.isLoad = true
			},
			handerTabChange(index) {
				this.activeIndex = index
				this.param.status = index
				this.getList();
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				this.$util.showLoading()
				let {
					list: oldList,
					param,
					tabList,
					activeIndex
				} = this
				let {
					id
				} = tabList[activeIndex]
				param.type = 1 //id
				let newList = await this.$api.mine.shieldCoachList(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			async toRemove(index) {
				let {
					id
				} = this.list.data[index]
				let {
					id: val
				} = this.tabList[this.activeIndex]
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.mine.shieldCoachDel({
						id
					})
					this.lockTap = false
					this.$util.hideAll()
					this.$util.showToast({
						title: `移除成功`
					})
					this.list.data.splice(index, 1)
					this.updateUserItem({
						key: 'haveShieldOper',
						val
					})
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			}
		}
	}
</script>

<style lang="scss">
	.user-shield {
		.list-item {
			.avatar {
				width: 124rpx;
				height: 124rpx;
			}

			.text {
				color: #4D4D4D;
				margin-top: 6rpx;
			}

			.remove-btn {
				width: 146rpx;
				height: 56rpx;
				transform: rotateZ(360deg);
			}
		}
	}
</style>
