<template>
	<view class="dynamic-technician-comment">
		<view @longpress="toDel(index)" class="list-item flex-warp ml-lg mr-lg pt-lg pb-lg"
			:class="[{'b-1px-t':index!==0}]" v-for="(item,index) in list.data" :key="index">
			<image mode="aspectFill" class="avatar radius" :src="item.avatarUrl"></image>
			<view class="flex-1 ml-md">
				<view class="flex-between">
					<view>
						<view class="flex-y-center f-paragraph c-title text-bold">
							<view class="max-400 ellipsis">{{item.nickName}}</view>
							<view class="examine-btn flex-center f-icontext ml-md radius" v-if="item.status==1">审核中
							</view>
						</view>
						<view class="text flex-y-center f-caption">评论了你的动态<view class="ml-md">{{item.friend_time}}
							</view>
						</view>
					</view>
					<image mode="aspectFill" class="cover radius-16" :src="item.cover"></image>
				</view>
				<view class="comment f-paragraph mt-md"> {{item.text}} </view>
			</view>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-footer"></view>
		<common-popup @confirm="confirmDel" ref="del_item" type="DELETE_ORDER" title="删除评论" desc="请确认是否删除评论，删除后将无法恢复"
			:info="popupInfo"></common-popup>
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
				},
				popupInfo: {},
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
				let newList = await this.$api.dynamic.coachCommentList(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			toDel(index) {
				let {
					id,
					cover: image
				} = this.list.data[index]
				this.popupInfo = {
					id,
					name: ``,
					image,
					index,
				}
				this.$refs.del_item.open()
			},
			// 确认：删除评论
			async confirmDel() {
				let {
					id,
					index,
				} = this.popupInfo
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.dynamic.commentDel({
						id
					})
					this.lockTap = false
					this.$util.hideAll()
					this.list.data.splice(index, 1)
					this.$util.showToast({
						title: `删除成功`
					})
					this.$refs.del_item.close()
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
		}
	}
</script>

<style lang="scss">
	page {
		background: #fff;
	}

	.dynamic-technician-comment {
		.list-item {
			.avatar {
				width: 72rpx;
				height: 72rpx;
				margin-top: 16rpx;
			}

			.cover {
				width: 107rpx;
				height: 107rpx;
			}

			.text {
				color: #ADADAD;
				margin-top: 6rpx;
			}

			.comment {
				color: #3A3A3A;
				line-height: 1.4;
			}

			.examine-btn {
				width: 82rpx;
				height: 34rpx;
				color: #F96246;
				background: rgba(249, 98, 70, 0.1);
			}
		}
	}
</style>
