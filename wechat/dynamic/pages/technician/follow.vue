<template>
	<view class="dynamic-technician-follow">
		<view class="list-item flex-center ml-lg mr-lg pt-lg pb-lg" :class="[{'b-1px-t':index!==0}]"
			v-for="(item,index) in list.data" :key="index">
			<image mode="aspectFill" class="avatar radius" :src="item.avatarUrl"></image>
			<view class="flex-1 flex-between ml-md">
				<view>
					<view class="f-paragraph c-title text-bold max-470 ellipsis">{{item.nickName}}</view>
					<view class="text f-caption">开始关注了你</view>
				</view>
				<view class="time f-icontext">{{item.friend_time}}</view>
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
				loading: true,
				param: {
					page: 1
				},
				list: {
					data: []
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
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				this.getList();
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
					param
				} = this
				let newList = await this.$api.dynamic.followList(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},

		}
	}
</script>

<style lang="scss">
	page {
		background: #fff;
	}

	.dynamic-technician-follow {
		.list-item {
			.avatar {
				width: 72rpx;
				height: 72rpx;
			}

			.text {
				color: #ADADAD;
				margin-top: 6rpx;
			}

			.time {
				color: #9B9B9B;
			}
		}
	}
</style>
