<template>
	<view class="pages-shopstore" v-if="isLoad">
		<fixed>
			<search @input="toSearch" type="input" :padding="30" :radius="0" backgroundColor="#fff"
				placeholder="请输入门店名称">
			</search>
		</fixed>


		<view @tap.stop="goDetail(index)" class="list-item flex-center pd-lg mt-md ml-md mr-md fill-base radius-16"
			v-for="(item,index) in list.data" :key="index">
			<!-- #ifdef H5 -->
			<view class="item-img radius-16">
				<view class="h5-image item-img radius-16" :style="{ backgroundImage : `url('${item.cover}')`}">
				</view>
			</view>
			<!-- #endif -->
			<!-- #ifndef H5 -->
			<image mode="aspectFill" class="item-img radius-16" :src="item.cover">
			</image>
			<!-- #endif -->
			<view class="flex-1 ml-md max-510">
				<view class="flex-y-center f-title c-title">
					<view class="text-bold max-380 ellipsis">{{item.title}}</view>
					<view @tap.stop="toPreviewImage(index)" class="store-status-btn flex-center ml-sm f-icontext"
						:style="{color:item.work_status==1?primaryColor:'#888',borderColor:item.work_status==1?primaryColor:'#888'}">
						{{item.work_status==1?'营业中':'休息中'}}
					</view>
				</view>
				<view class="flex-between f-icontext mt-sm">
					<view class="icon-item flex-y-center c-caption">
						<view class="flex-y-center mr-lg"><i class="iconfont iconpingfen1 icon-font-color"></i>
							<view class="star-text">{{item.star}}</view>
						</view>
						<view class="flex-y-center"><i
								class="iconfont iconyingyeshijian"></i>{{item.start_time && item.end_time?`${item.start_time} - ${item.end_time}`:'暂未设置'}}
						</view>
					</view>
					<view style="color: #E67D4B;"> {{item.total_num}}+次服务</view>
				</view>
				<view class="flex-between f-icontext mt-md">
					<view class="rate-info flex-center c-base rel" :style="{color:primaryColor}">
						<view class="bg abs" :style="{background:primaryColor}"></view>
						<view class="mr-sm">好评率</view>{{item.positive_rate}}% <view class="ml-md mr-sm">接单率</view>
						{{item.order_rate}}%
					</view>
					<view style="color: #636363;">{{item.distance}}</view>
				</view>
			</view>
		</view>



		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>

		<view class="space-footer"></view>
		<view :style="{height: `${configInfo.tabbarHeight}px`}"></view>
		<tabbar :cur="6"></tabbar>

		<!-- #ifdef APP-PLUS -->
		<login-info></login-info>
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import siteInfo from '@/siteinfo.js';
	import tabbar from "@/components/tabbar.vue"
	export default {
		components: {
			tabbar
		},
		data() {
			return {
				isLoad: false,
				options: {},
				loading: true,
				lockTap: false
			}
		},
		computed: mapState({
			pageActive: state => state.shopstore.pageActive,
			param: state => state.shopstore.param,
			list: state => state.shopstore.list,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			autograph: state => state.user.autograph,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			isGzhLogin: state => state.user.isGzhLogin,
		}),
		async onLoad(options) {
			this.$util.showLoading()
			if(options.pid){
				options = await this.updateCommonOptions(options)
			}
			this.options = options
			// #ifndef H5
			this.$util.showLoading()
			// #endif
			await this.initIndex()
			this.updateShopstoreItem({
				key: 'pageActive',
				val: true
			})
		},
		async onShow() {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.toAppShare()
			}
			// #endif
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
		onShareAppMessage(e) {
			let {
				id: pid = 0
			} = this.userInfo
			let path = `/pages/shopstore?pid=${pid}`
			this.$util.log(path)
			return {
				title: '',
				imageUrl: '',
				path,
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'updateCommonOptions',
				'getShopstoreList'
			]),
			...mapMutations(['updateUserItem', 'updateShopstoreItem']),
			async initIndex(refresh = false) {
				let {
					pid = 0
				} = this.options

				if (!refresh && this.pageActive && !pid) {
					this.isLoad = true
					this.loading = false
					this.$util.hideAll()
					return
				}

				let {
					isGzhLogin
				} = this
				let {
					id: uid = 0
				} = this.userInfo
				if (pid && !uid) {
					// #ifdef H5
					if (isGzhLogin) {
						setTimeout(() => {
							this.getUserInfo()
						}, 1000)
					} else {
						this.getUserInfo()
					}
					// #endif
					// #ifndef H5
					await this.getUserInfo()
					// #endif 
				}

				let {
					id: config_id,
					plugAuth = {}
				} = this.configInfo
				if (!config_id || plugAuth.length == 0 || refresh) {
					await this.getConfigInfo()
				}
				// #ifndef H5
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				// #endif
				await this.getList(1)
			},
			async initRefresh() {
				await this.initIndex(true)
			},
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo
				let title = '动态'
				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/pages/shopstore?pid=${pid}`
				let imageUrl = ''
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, '', href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
			},
			toSearch(val) {
				this.param.title = val
				this.getList(1)
			},
			async getList(flag) {
				if (flag) {
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
					location = await this.$util.getLocationInfo()
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
				} = this
				let param = Object.assign({}, this.param, {
					lat,
					lng
				});
				await this.getShopstoreList(param)
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
			},
			goDetail(index) {
				let {
					id
				} = this.list.data[index]
				this.$util.goUrl({
					url: `/shopstore/pages/detail?id=${id}`
				})
			}
		}
	}
</script>


<style lang="scss">
	.pages-shopstore {
		.list-item {
			.item-img {
				width: 160rpx;
				height: 143rpx;
			}

			.store-status-btn {
				width: 76rpx;
				height: 28rpx;
				border-radius: 2rpx;
				border: 1rpx solid #888;
				transform: rotateZ(360deg);
			}

			.iconpingfen1 {
				background-image: -webkit-linear-gradient(270deg, #FAD961 0%, #F76B1C 100%);
			}

			.icon-item {
				color: #4D4D4D;

				.iconfont {
					font-size: 24rpx;
					margin-right: 6rpx;
				}
			}

			.star-text {
				color: #FF9519;
			}

			.rate-info {
				height: 30rpx;
				border-radius: 4rpx;
				padding: 1rpx 12rpx 0 12rpx;

				.bg {
					opacity: 0.1;
					border-radius: 4rpx;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					z-index: 1;
				}
			}
		}
	}
</style>
