<template>
	<view class="dynamic-follow">
		<view @tap.stop="goDetail(index)"
			class="list-item flex-center fill-base mt-md ml-md mr-md pd-lg radius-16 box-shadow"
			v-for="(item,index) in list.data" :key="index">
			<image mode="aspectFill" class="avatar radius" :src="item.work_img"></image>
			<view class="flex-1 ml-md">
				<view class="flex-between">
					<view class="f-title c-black text-bold ellipsis" style="max-width: 150rpx;">{{item.coach_name}}
					</view>
					<view class="flex-y-baseline f-desc c-black"> <i class="iconfont iconjuli"
							:style="{color:primaryColor}"></i>
						{{item.distance}}
					</view>
				</view>
				<view class="flex-y-baseline">
					<view class="text flex-y-center f-desc">已接单<view class="ml-sm" :style="{color:primaryColor}">
							{{item.order_num}}
						</view>
					</view>
					<view class="flex-y-center f-caption c-caption ml-md"> 粉丝数 <view class="c-title ml-sm">
							{{item.fans_num}}
						</view>
					</view>
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
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
		}),
		onLoad() {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.$util.showLoading()
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
				let {
					list: oldList,
					param,
					location
				} = this
				if (!location.lat) {
					// #ifdef H5
					if (this.$jweixin.isWechat()) {
						this.$util.showLoading()
						// await this.$jweixin.initJssdk();
						await this.$jweixin.wxReady2();
						let {
							latitude: lat = 0,
							longitude: lng = 0
						} = await this.$jweixin.getWxLocation()
						location = {
							lng,
							lat,
							address: '定位失败',
							province: '',
							city: '',
							district: ''
						}
						if (lat && lng) {
							let key = `${lat},${lng}`
							let data = await this.$api.base.getMapInfo({
								location: key
							})
							let {
								status,
								result
							} = JSON.parse(data)
							if (status == 0) {
								let {
									address,
									address_component
								} = result
								let {
									province,
									city,
									district
								} = address_component
								location = {
									lng,
									lat,
									address,
									province,
									city,
									district
								}
							}
						}
					}
					// #endif
					// #ifndef H5
					location = await this.$util.getBmapLocation()
					// #endif
					this.updateUserItem({
						key: 'location',
						val: location
					})
				}
				let {
					lat = 0, lng = 0
				} = location
				param.lat = lat
				param.lng = lng

				let newList = await this.$api.dynamic.followCoachList(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			goDetail(index) {
				let {
					coach_id
				} = this.list.data[index]
				this.$util.goUrl({
					url: `/user/pages/technician-info?id=${coach_id}`
				})
			}
		}
	}
</script>

<style lang="scss">
	.dynamic-follow {
		.list-item {
			.avatar {
				width: 124rpx;
				height: 124rpx;
			}

			.text {
				color: #4D4D4D;
				margin-top: 6rpx;
			}
		}
	}
</style>
