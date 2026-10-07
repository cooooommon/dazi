<template>
	<view class="technician-shield" v-if="isLoad">
		<view class="list-item flex-center fill-base mt-md ml-md mr-md pd-lg radius-16"
			v-for="(item,index) in list.data" :key="index">
			<view class="flex-1 mr-md">
				<view class="f-title c-black text-bold max-380 ellipsis">{{item.nickName}}</view>
				<view class="flex-y-center f-caption mt-md" style="color:#4D4D4D;margin-top:14rpx">拉黑时间：<view
						class="c-caption">
						{{item.create_time}}
					</view>
				</view>
			</view>
			<view @tap.stop="toRemove(index)" class="remove-btn flex-center f-desc"
				:style="{color:primaryColor,border:`1rpx solid ${primaryColor}`}">移除列表</view>
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
				await this.getList()
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
				} = this
				let newList = await this.$api.technician.shieldCoachList(param)
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
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.technician.shieldUserDel({
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
	.technician-shield {
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
				width: 140rpx;
				height: 52rpx;
				border-radius: 8rpx;
				transform: rotateZ(360deg);
			}
		}
	}
</style>
