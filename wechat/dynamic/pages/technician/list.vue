<template>
	<view class="dynamic-technician-list" v-if="isLoad">
		<fixed>
			<view class="fill-body">
				<view class="count-list flex-center fill-base">
					<view @tap.stop="$util.goUrl({url:`/dynamic/pages/technician/thumbs`})"
						class="count-item flex-center flex-column f-caption c-title">
						<view class="tag thumbs flex-center rel">
							<i class="iconfont icon-shoucang-fill"></i>
							<view class="count-tag flex-center f-icontext c-base abs"
								:style="{width: count.thumbs_num < 10 ? '26rpx':'50rpx',right:count.thumbs_num < 10 ? '-13rpx':'-38rpx'}"
								v-if="count.thumbs_num">
								{{count.thumbs_num < 100 ? count.thumbs_num : '99+'}}
							</view>
						</view>
						<view class="mt-md">收获的赞</view>
					</view>
					<view @tap.stop="$util.goUrl({url:`/dynamic/pages/technician/follow`})"
						class="count-item flex-center flex-column f-caption c-title">
						<view class="tag follow flex-center rel">
							<i class="iconfont iconxinzengguanzhu"></i>
							<view class="count-tag flex-center f-icontext c-base abs"
								:style="{width: count.follow_num < 10 ? '26rpx':'50rpx',right:count.follow_num < 10 ? '-13rpx':'-38rpx'}"
								v-if="count.follow_num">
								{{count.follow_num < 100 ? count.follow_num : '99+'}}
							</view>
						</view>
						<view class="mt-md">新增关注</view>
					</view>
					<view @tap.stop="$util.goUrl({url:`/dynamic/pages/technician/comment`})"
						class="count-item flex-center flex-column f-caption c-title">
						<view class="tag comment flex-center rel">
							<i class="iconfont iconshouhuodepinglun"></i>
							<view class="count-tag flex-center f-icontext c-base abs"
								:style="{width: count.comment_num < 10 ? '26rpx':'50rpx',right:count.comment_num < 10 ? '-13rpx':'-38rpx'}"
								v-if="count.comment_num">
								{{count.comment_num < 100 ? count.comment_num : '99+'}}
							</view>
						</view>
						<view class="mt-md">收获的评论</view>
					</view>
				</view>
				<view class="rel">
					<view class="flex-between pd-lg">
						<view class="f-title text-bold">我的发布</view>
						<view @tap.stop="toChangeItem(-1)" class="flex-y-center f-desc">
							{{statusList[statusInd].title}}<i class="iconfont c-desc ml-sm"
								:class="[{'iconshaixuanxia-1':showSort},{'iconshaixuanshang-1':!showSort}]"
								style="font-size: 20rpx"></i>
						</view>
					</view>
					<view class="dynamic-sort pt-md pb-md f-desc abs" v-if="showSort">
						<view @tap.stop="toChangeItem(index)" class="sort-item flex-center"
							:style="{color:statusInd == index ? primaryColor : ''}" v-for="(item,index) in statusList"
							:key="index">
							{{item.title}}
						</view>
					</view>
				</view>
			</view>
		</fixed>

		<wfalls-flow :list="list.data" :path="2" ref="wfalls" v-if="list.data.length > 0"></wfalls-flow>
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
	import wfallsFlow from "@/components/wfalls-flow.vue"
	export default {
		components: {
			wfallsFlow
		},
		data() {
			return {
				isLoad: false,
				statusList: [{
					id: 0,
					title: '全部'
				}, {
					id: 1,
					title: '审核中'
				}, {
					id: 2,
					title: '审核通过'
				}, {
					id: 3,
					title: '已驳回'
				}],
				statusInd: 0,
				showSort: false,
				loading: true,
				param: {
					page: 1,
					limit: 10,
					status: 0
				},
				list: {
					data: []
				},
				count: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			haveShieldOper: state => state.user.haveShieldOper,
		}),
		onLoad() {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.$util.showLoading()
			this.initIndex()
		},
		async onShow() {
			if (this.haveShieldOper == 1) {
				await this.initRefresh()
				this.updateUserItem({
					key: 'haveShieldOper',
					val: 0
				})
			} else {
				this.getDynamicData()
			}
		},
		async onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			await this.initRefresh();
			uni.stopPullDownRefresh()
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.param.page = this.param.page + 1;
			this.loading = true;
			await this.getList();
			setTimeout(() => {
				this.$refs.wfalls.handleViewRender();
			}, 0)
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
				if (refresh) {
					await Promise.all([this.getDynamicData(), this.getList(1)])
				} else {
					await this.getList(1)
				}
			},
			async initRefresh() {
				await this.initIndex(true)
			},
			async getDynamicData() {
				this.count = await this.$api.dynamic.dynamicData()
			},
			async getList(flag) {
				if (flag) {
					this.showSort = false
					this.param.page = 1
					this.list.data = []
				}
				let {
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

				let {
					list: oldList,
					param,
					statusList,
					statusInd
				} = this
				param.lat = lat
				param.lng = lng
				param.status = statusList[statusInd].id
				let newList = await this.$api.dynamic.coachDynamicList(param)
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
			toChangeItem(index) {
				if (index == -1) {
					this.showSort = !this.showSort
				} else {
					this.statusInd = index
					this.showSort = false
					this.initRefresh()
				}
			},
		}
	}
</script>

<style lang="scss">
	.dynamic-technician-list {
		.count-list {
			width: 750rpx;
			height: 204rpx;

			.count-item {
				width: 33.33%;

				.tag {
					width: 85rpx;
					height: 85rpx;
					border-radius: 29rpx;

					.iconfont {
						font-size: 44rpx;
					}

					.count-tag {
						top: 0;
						right: -13rpx;
						width: 26rpx;
						height: 26rpx;
						background: #E82F21;
						border-radius: 26rpx;
					}
				}

				.thumbs {
					color: #ff6262;
					background: #FFEFEF;
				}

				.follow {
					color: #FC8218;
					background: #FEF6E7;
				}

				.comment {
					color: #44A860;
					background: #ECF6EF;
				}
			}
		}

		.dynamic-sort {
			top: 80rpx;
			right: 32rpx;
			width: 145rpx;
			background: #FEFFFE;
			box-shadow: 0 14rpx 20rpx 0 rgba(132, 132, 132, 0.08);
			border-radius: 4rpx;
			border: 1rpx solid #EFEFEF;
			transform: rotateZ(360deg);
			z-index: 1;

			.sort-item {
				padding: 15rpx 0;
			}
		}
	}
</style>
