<template>
	<view class="pages-find">
		<block v-if="isLoad">
			<block v-if="configInfo.plugAuth.demand">
				<!-- #ifndef H5 -->
				<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true" :title="navTitle" color="#ffffff"
					:zIndex="99" :backgroundColor="primaryColor">
				</uni-nav-bar>
				<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
				<fixed :top="configInfo.navBarHeight">
				<!-- #endif -->
					<!-- #ifdef H5 -->
					<fixed>
					<!-- #endif -->
						<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1"
							:activeColor="primaryColor" :width="100/tabList.length + '%'" height="100rpx"></tab>
						<view class="b-1px-b"></view>
					</fixed>
					<view class="mt-sm fill-base find-list" v-for="(item,index) in list.data" :key="index">
						<view class="find-item b-1px-b">
							<view class="find-item-top flex-between">
								<view class="flex">
									<image :src="item.is_hide==0 ? item.avatarUrl : avatarUrl" mode="aspectFill"
										class="item-image radius-16"></image>
									<view class="flex-center">
										<view class="pl-md">
											<view class="f-title text-bold">{{item.is_hide==0 ? item.nickName : `匿名用户`}}
											</view>
											<view class="f-icontext c-caption flex-y-center">
												{{item.address | addressMatch}}<text
													class="pl-md">{{item.read_time}}</text>
											</view>
										</view>
									</view>
								</view>
								<view class="f-desc">{{item.distance | handleDistance}} km</view>
							</view>
							<view class="f-min-title pre-wrap" :class="!item.isEllipsis ? 'ellipsis-3' : ''"
								style="line-height: 22px;">{{item.content}}</view>
							<view class="f-min-title abs pre-wrap" :class="`content`+index"
								style="line-height: 22px;z-index: -1;top: 0;">{{item.content}}</view>
							<view class="f-caption mt-md" v-if="textHeight > 66" :style="{color:primaryColor}"
								@tap.stop="updateMore(index)">{{item.isMore ? `展开` : `收起`}}</view>
							<view class="flex-center">
								<view class="find-images flex-warp">
									<block v-for="(citem,index) in item.img" :key="index">
										<block v-if="item.img.length == 1">
											<image :src="citem" mode="aspectFill" class="find-images-1 radius-16"
												@tap.stop="$util.previewImage({current:citem,urls:item.img})"></image>
										</block>
										<block v-if="item.img.length > 1">
											<image :src="citem" mode="aspectFill" class="images-item radius-16"
												@tap.stop="$util.previewImage({current:citem,urls:item.img})"></image>
										</block>
									</block>
								</view>
							</view>
							<view class="find-time radius-16 pt-sm pb-lg rel">
								<view class="find-time-bg abs radius-16" :style="{backgroundColor:primaryColor}"></view>
								<view class="flex-y-center">
									<text class="f-desc pl-md pr-md">剩余</text>
									<min-countdown ref="mincountdown" :newtargetTime="item.start_time" :isPlay="true"
										:type="3" @callback="countEnd"></min-countdown>
								</view>
								<view class="pl-md pr-md pt-sm flex-between rel">
									<image v-if="item.type_img" :src="item.type_img" mode="aspectFill"
										class="ser-image">
									</image>
									<view class="flex-center ser-image" v-else>
										<view class="iconbianzu-8_2x iconfont"
											:style="{color:primaryColor,fontSize: '40px'}"></view>
									</view>
									<view class="flex-1">
										<view class="text-bold f-title">{{item.type_name}}</view>
										<view class="flex-y-center">
											<text class="f-mini-title text-bold"
												style="color: #FF4C88;">{{item.price}}</text>
											<text class="f-icontext">元/单</text>
										</view>
									</view>
									<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
										v-if="item.apply_status == 0 && mineInfo.coach_status == 2" style="width: auto;"
										:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="application(item.id)">
										<view class="c-base text-center f-desc item-btn"
											:style="{backgroundColor:primaryColor}">我要报名</view>
									</auth>
								</view>
							</view>
							<!-- <view class="c-base text-center f-desc item-btn"
				:style="{backgroundColor:primaryColor}" 
				@tap.stop="orderTaking(item.id)"
				v-if="userPageType == 2">接单</view> -->
							<!-- <view class="pt-lg flex-between">
					<view class="flex-y-baseline">
						<view class="f-sm-title" style="color: #FF4C88">{{item.price}}</view>
						<view class="f-caption">元/单</view>
					</view>
					
					<view class="c-base text-center f-desc item-btn"
					:style="{backgroundColor:primaryColor}" 
					@tap.stop="application(item.id)"
					v-if="item.apply_status == 0 && mineInfo.coach_status == 2">我要报名</view>
				</view> -->

							<!-- <view class="pt-sm f-desc" style="color: #8A98AF;" v-if="item.order_code">订单编号: {{item.order_code}}</view> -->
						</view>
					</view>
					<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading"
						v-if="loading">
					</load-more>
					<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
					<view class="space-footer"></view>

					<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
						:type="!userInfo.phone ? 'phone' : 'userInfo'"
						@go="$util.toCheckLogin({url:`/find/pages/index`})">
						<view class="find-release box-shadow-mini flex-center fill-base"
							:style="{background: `linear-gradient(${subColor},${primaryColor})`}">
							<view class="text-center find-release-cont fill-base flex-center flex-column">
								<i class="iconfont iconfabu1" :style="{color: primaryColor,fontSize: '22px'}"></i>
								<view class="f-little c-desc" style="line-height: 1;padding-top: 3px;"
									:style="{color: primaryColor}">发布</view>
							</view>
						</view>
					</auth>
					<!--附近排序-->
					<w-picker mode="selector" :options="distanceList" :themeColor="primaryColor"
						:visible.sync="showDistance" @confirm="selectConfirm($event , 'distance')">
					</w-picker>
					<!--服务分类-->
					<w-picker mode="selector" :options="skill" :themeColor="primaryColor" :visible.sync="showServer"
						@confirm="selectConfirm($event , 'ser_id')" :defaultProps="serverProps">
					</w-picker>
					<!--价格区间-->
					<w-picker mode="selector" :options="priceList" :themeColor="primaryColor" :visible.sync="showPrice"
						@confirm="selectConfirm($event , 'price')">
					</w-picker>
			</block>
			<block v-else>
				<abnor type="NOT_AUTH"></abnor>
			</block>
			<view :style="{height: `${configInfo.tabbarHeight}px`}"></view>
			<tabbar :cur="7"></tabbar>
		</block>
		<!-- #ifdef MP-WEIXIN -->
		<user-privacy ref="user_privacy" :show="false"></user-privacy>
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
				loading: true,
				isLoad: false,
				serverList: [],
				showDistance: false,
				showServer: false,
				showPrice: false,
				serverProps: {
					label: "name",
					value: "id"
				},
				options: {},
				navTitle: ''
			}
		},
		computed: mapState({
			pageActive: state => state.find.pageActive,
			activeIndex: state => state.find.activeIndex,
			tabList: state => state.find.tabList,
			param: state => state.find.param,
			list: state => state.find.list,
			skill: state => state.find.skill,
			distanceList: state => state.find.distanceList,
			priceList: state => state.find.priceList,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			avatarUrl: state => state.config.avatarUrl,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
			location: state => state.user.location,
			userPageType: state => state.user.userPageType,
			coachInfo: state => state.user.coachInfo,
			locaRefuse: state => state.user.locaRefuse,
			userCoachStatus: state => state.user.userCoachStatus,
			changeOnAddr: state => state.user.changeOnAddr,
			noChangeLoca: state => state.user.noChangeLoca,
			isGzhLogin: state => state.user.isGzhLogin,
		}),
		watch: {
			changeOnAddr(newval, oldval) {
				let {
					noloca,
				} = this.noChangeLoca
				if (newval && noloca) {
					this.initUtilLocaData()
					this.updateUserItem({
						key: 'noChangeLoca',
						val: {
							noloca: false
						}
					})
				}
			},
			noChangeLoca(newval, oldval) {
				setTimeout(() => {
					let {
						lat,
						lng,
						unix = 0
					} = this.changeOnAddr
					let {
						noloca
					} = this.noChangeLoca
					let cur_unix = this.$util.DateToUnix(this.$util.formatTime(new Date(), 'YY-M-D h:m:s'))
					if (noloca && ((!lat && !lng) || !unix || (unix && cur_unix - unix >= 1))) {
						this.toResetChangeLoca()
					}
				}, 500)
			}
		},
		onShareAppMessage(e) {
			let {
				id: pid = 0
			} = this.userInfo
			let path = `/pages/find?pid=${pid}`
			this.$util.log(path)
			return {
				path,
			}
		},
		async onLoad(options) {
			if (options.pid) {
				options = await this.updateCommonOptions(options)
			}
			this.options = options
			
			if (!this.configInfo.id) {
				await this.getConfigInfo()
			}
			let {
				tabBar
			} = this.configInfo
			let ind = tabBar.findIndex(item => {
				return item.id == 7
			})
			let navTitle = tabBar[ind].name
			this.navTitle = navTitle
			// #ifdef H5
			uni.setNavigationBarTitle({
				title: navTitle
			})
			// #endif

			await this.initIndex()
			if (this.skill.length == 0) {
				this.getSkill()
			}

			//plus.navigator.setStatusBarStyle('dark');
		},

		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initIndex(true);
			this.getMineInfo()
			uni.stopPullDownRefresh()
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.param.page += 1
			this.updateFindItem({
				key: 'param',
				val: this.param
			})
			this.updateList(this.param)
		},
		methods: {
			...mapActions(['getFindList', 'getConfigInfo', 'getSkill', 'getCoachInfo', 'getMineInfo',
				'updateCommonOptions', 'getUserInfo'
			]),
			...mapMutations(['updateFindItem', 'updateUserItem']),
			initRefresh() {
				this.param.page = 1
				this.updateList(this.param)
			},
			async initIndex(refresh = false) {
				let {
					pid = 0
				} = this.options
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
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.toAppShare()
				}
				// #endif
				
				if (!this.mineInfo.id && uid) {
					await this.getMineInfo()
				}
				await this.getList(1)
			},
			handerTabChange(index) {
				this.updateFindItem({
					key: 'activeIndex',
					val: index
				})
				uni.pageScrollTo({
					scrollTop: 0
				})
				if (index == 0) {
					this.showDistance = true
				} else if (index == 1) {
					this.showServer = true
				} else if (index == 2) {
					this.showPrice = true
				}
			},
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo
				let title = this.navTitle || '发现'
				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/pages/find?pid=${pid}`
				let imageUrl = ''
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, '', href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
			},
			async selectConfirm(e, type) {
				this.$util.showLoading()
				let param = this.$util.deepCopy(this.param)
				if (type == 'price') {
					param = {
						...param,
						...e.value
					}
				} else {
					param[type] = e.value
				}
				param.page = 1
				this.updateFindItem({
					key: 'param',
					val: param
				})

				await this.updateList(param)
				this.$util.hideAll()
			},
			async updateList(param) {
				await this.getFindList(param)
				setTimeout(() => {
					this.list.data.forEach((item, index) => {
						setTimeout(() => {
							uni.createSelectorQuery().select('.content' + index)
								.boundingClientRect(data => { //目标位置的节点，类class或者id
									item.isMore = data && data.height > 66 ? true : false
									item.textHeight = data ? data.height : 0
								}).exec();
						}, 100)
					})
				}, 500)
				this.$util.hideAll()
			},
			async getList(page) {
				// let {
				// 	location
				// } = this
				// if (!location.lat) {
				// 	// #ifdef H5
				// 	if (this.$jweixin.isWechat()) {
				// 		this.$util.showLoading()
				// 		// await this.$jweixin.initJssdk();
				// 		await this.$jweixin.wxReady2();
				// 		let {
				// 			latitude: lat = 0,
				// 			longitude: lng = 0
				// 		} = await this.$jweixin.getWxLocation()
				// 		location = {
				// 			lng,
				// 			lat,
				// 			address: '定位失败',
				// 			province: '',
				// 			city: '',
				// 			district: ''
				// 		}
				// 		if (lat && lng) {
				// 			let key = `${lat},${lng}`
				// 			let data = await this.$api.base.getMapInfo({
				// 				location: key
				// 			})
				// 			let {
				// 				status,
				// 				result
				// 			} = JSON.parse(data)
				// 			if (status == 0) {
				// 				let {
				// 					address,
				// 					address_component
				// 				} = result
				// 				let {
				// 					province,
				// 					city,
				// 					district
				// 				} = address_component
				// 				location = {
				// 					lng,
				// 					lat,
				// 					address,
				// 					province,
				// 					city,
				// 					district
				// 				}
				// 			}
				// 		}
				// 	}
				// 	// #endif
				// 	// #ifndef H5
				// 	location = await this.$util.getLocationInfo()
				// 	// #endif
				// 	this.updateUserItem({
				// 		key: 'location',
				// 		val: location
				// 	})
				// 	this.$util.hideAll()
				// }
				if (page) {
					this.param.page = 1
					this.list.data = []
				}
				let {
					location,
					locaRefuse
				} = this

				let {
					status: coach_status,
					coach_position
				} = this.userCoachStatus

				if (this.param.page == 1) {
					// #ifdef APP-PLUS
					if (!locaRefuse && (!location.lat || (location.lat && location.address == '暂未获取到位置信息'))) {
						// #endif
						// #ifndef APP-PLUS
						if (!location.lat || (location.lat && location.address == '暂未获取到位置信息')) {
							// #endif

							// #ifdef MP-WEIXIN
							let privacyCheck = this.$refs.user_privacy.check()
							if (privacyCheck) {
								this.$refs.user_privacy.open()
								this.isLoad = true
								this.loading = false
								this.$util.hideAll()
								return
							}
							// #endif

							if (coach_status == 2 && coach_position) {
								let {
									lat: change_lat,
									lng: change_lng,
									unix = 0
								} = this.changeOnAddr
								let cur_unix = this.$util.DateToUnix(this.$util.formatTime(new Date(), 'YY-M-D h:m:s'))
								let noloca = change_lat && change_lng && (unix && cur_unix - unix < 2) ? false : true
								if (!noloca) {
									let loca = Object.assign({}, this.location, {
										lat: change_lat,
										lng: change_lng,
										is_util_loca: 1
									})
									this.updateUserItem({
										key: 'location',
										val: loca
									})
								}
								this.updateUserItem({
									key: 'noChangeLoca',
									val: {
										noloca
									}
								})
							} else {
								await this.$util.getUtilLocation()
							}
						}
					}
					if (coach_status == 2 && coach_position && this.param.page == 1 && this.noChangeLoca.noloca) return
					this.initUtilLocaData()

					// let param = this.$util.deepCopy(this.param)
					// if(this.userPageType == 2){
					// 	await this.getCoachInfo()
					// 	param.coach_id = this.coachInfo.id || 0
					// }

					// let {
					// 	lng = 0,
					// 		lat = 0
					// } = location
					// param.lat = lat
					// param.lng = lng
					// await this.updateFindItem({
					// 	key: 'param',
					// 	val: param
					// })
					// await this.updateList(param)
					// this.loading = false
					// #ifndef APP-PLUS
					setTimeout(() => {
						this.loading = false
						this.isLoad = true
					}, 5000)
					// #endif
				},
				async toResetChangeLoca() {
						await this.$util.getUtilLocation()
						this.initUtilLocaData()
						this.updateUserItem({
							key: 'noChangeLoca',
							val: {
								noloca: false
							}
						})
					},
					async initUtilLocaData() {
							let {
								lat = 0, lng = 0, is_util_loca = 0
							} = this.location

							// #ifdef APP-PLUS
							if (!lat && !lng) {
								this.updateStoreItem({
									key: 'list',
									val: {
										data: [],
										last_page: 1,
										current_page: 1
									}
								})
								this.isLoad = true
								this.loading = false
								this.$util.hideAll()
								return
							}
							// #endif
							let {
								list: oldList,
							} = this
							let param = Object.assign({}, this.param, {
								lat,
								lng
							});

							if (this.userPageType == 2) {
								await this.getCoachInfo()
								param.coach_id = this.coachInfo.id || 0
							}
							await this.updateFindItem({
								key: 'param',
								val: param
							})
							await this.updateList(param)
							this.loading = false
							this.isLoad = true
						},
						updateMore(index) {
							let list = this.$util.deepCopy(this.list)
							list.data[index].isEllipsis = !list.data[index].isEllipsis
							list.data[index].isMore = !list.data[index].isMore
							this.updateFindItem({
								key: 'list',
								val: list
							})
						},
						// // 接单
						// async orderTaking(id){
						// 	await this.$api.find.receivingOrder({id})
						// 	this.$util.showToast({
						// 		title: '接单成功'
						// 	});
						// 	this.initRefresh()
						// 	setTimeout(()=>{
						// 		this.$util.goUrl({url:`/technician/pages/invitation/list`})
						// 	},1000)
						// },
						// 我要报名
						async application(id) {
								await this.$api.find.orderApply({
									order_id: id
								})
								this.$util.showToast({
									title: '报名成功'
								});
								this.initRefresh()
								setTimeout(() => {
									this.$util.goUrl({
										url: `/find/pages/invitation/list`
									})
								}, 1000)
							},
							countEnd() {
								this.$util.log("倒计时完了")
								setTimeout(() => {
									this.$util.showLoading()
									this.updateFindItem({
										key: 'list',
										val: {
											data: []
										}
									})
									this.initRefresh()
								}, 1000)
							},
			},
			filters: {
				handleDistance(val) {
					return (val / 1000).toFixed(2)
				},
				addressMatch(val) {
					if (!val) return ''
					var reg = /.+?(省|市|自治区|自治州|县|区)/g;
					let address = Array.from(val.match(reg))[1]
					return address
				}
			}
		}
</script>

<style lang="scss">
	.pages-find {
		.find-list {
			.find-item {
				padding-bottom: 30rpx;
			}

			padding: 0 30rpx;

			.find-item-top {
				height: 144rpx;

				.item-image {
					width: 84rpx;
					height: 84rpx;
				}
			}

			.find-images {
				padding: 25rpx 0;
				width: 680rpx;

				.find-images-1 {
					width: 100%;
					height: 376rpx;
				}

				.images-item {
					width: 216rpx;
					height: 216rpx;
					margin-right: 16rpx;
					margin-bottom: 16rpx;
				}

				.images-item:nth-child(3n) {
					margin-right: 0rpx;
				}
			}

			.find-time {
				min-height: 80rpx;

				.find-time-bg {
					opacity: 0.05;
					width: 100%;
					height: 100%;
					left: 0;
					top: 0;
				}

				.ser-image {
					width: 84rpx;
					height: 84rpx;
					border-radius: 84rpx;
					margin-right: 16rpx;
				}
			}

			.item-btn {
				width: 140rpx;
				height: 56rpx;
				line-height: 56rpx;
				border-radius: 56rpx;
			}
		}

		.find-release {
			width: 102rpx;
			height: 102rpx;
			border-radius: 102rpx;
			position: fixed;
			right: 20rpx;
			bottom: 267rpx;

			.find-release-cont {
				width: 92rpx;
				height: 92rpx;
				border-radius: 92rpx;
				background: #fff;
			}
		}
	}
</style>