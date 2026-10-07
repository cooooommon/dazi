<template>
	<view class="pages-technician" v-if="isLoad">

		<technician-search-fix :params="param" @search="toSearch" @pickerChange="getList(1)"
			@chooseLocation="toChooseLocation" @handerTabChange="handerTabChange" @changeParams="changeParams">
		</technician-search-fix>
		
		
		<view class="fill-base">
			<view class="flex-warp pl-lg pr-lg" v-if="pageActive && list.data && list.data.length>0">
				<view class="mb-md" :style="{marginRight: index%2 == 0 ? '20rpx':''}" v-for="(item,index) in list.data"
					:key="index">
					<technician-list-item @workImg="toPreviewImage(index,1)" @selfImg="toPreviewImage(index)"
						@comment="toShowPopup(index,'message')" @collect="toCollect(index)" @info="goInfo(index)"
						@order="toShowPopup(index,'technician', item.is_work )" :info="item">
					</technician-list-item>
				</view>
			</view>
		</view>
		


		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading"
			v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<!-- #ifndef H5 -->
		<!-- <abnor @confirm="toOpenLocation" :tip="[{ text: '定位失败，请开启地理位置授权后刷新页面重试~', color: 0 }]"
			:button="[{ text: '开启定位' , type: 'confirm' }]" btnSize="" v-if="!loading && !location.lng"> </abnor> -->
		<!-- #endif -->
		<block v-if="!loading && !location.lng && (configInfo.realtime_location || configInfo.plugAuth.recommend)">
			<!-- #ifdef H5 -->
			<!-- <abnor type="NOT_LOCATION" :title="`暂无向导数据`" :tip="[{ text: '定位失败，请开启地理位置授权后刷新页面重试~', color: 0 }]"></abnor> -->
			<!-- #endif -->
			<!-- #ifndef H5 -->
			<!-- <abnor type="NOT_LOCATION" @confirm="toOpenLocation" title="暂无向导数据" :tip="[{ text: '定位失败，请开启地理位置授权后刷新页面重试~', color: 0 }]"
				:button="[{ text: '开启定位' , type: 'confirm' }]" btnSize=""></abnor> -->
			<!-- #endif -->
		</block>

		<view class="space-footer"></view>
		

		<uni-popup ref="technician_item" type="bottom">
			<view class="technician-popup fill-base" v-if="showInd!=-1 && list.data.length > showInd">
				<view class="pd-lg"
					:class="[{'flex-center': showType == 'technician'},{'flex-warp': showType == 'message'}]">
					<image mode="aspectFill" class="item-avatar radius" :src="list.data[showInd].work_img"></image>
					<view @tap.stop="$refs.technician_item.close()" class="flex-1 ml-md">
						<view class="flex-between">
							<view class="flex-y-baseline f-caption c-caption">
								<view class="f-title c-title text-bold mr-sm max-350 ellipsis">
									{{list.data[showInd].coach_name}}
								</view>从业{{list.data[showInd].work_time}}年
							</view>
							<i class="iconfont icon-close"></i>
						</view>
						<scroll-view scroll-y @touchmove.stop.prevent class="technician-text f-caption c-caption mt-sm"
							v-if="showType == 'message'">
							{{list.data[showInd].text}}
						</scroll-view>
					</view>
				</view>
				<view class="space-sm fill-body"></view>
				<scroll-view scroll-y @touchmove.stop.prevent class="list-content">
					<block v-if="showType == 'technician'">
						<view class="list-item flex-center pd-lg fill-base radius-16" :class="[{'b-1px-t':index != 0}]"
							v-for="(item,index) in serviceList" :key="index">
							<image @tap.stop="goDetail(index)" mode="aspectFill" class="avatar lg radius-16"
								:src="item.cover"></image>
							<view class="flex-1 ml-md">
								<view @tap.stop="goDetail(index)" class="f-title c-title max-510 ellipsis">
									{{item.title}}
								</view>
								<view class="f-caption c-caption mt-sm mb-sm ellipsis">{{item.total_sale}}人选择</view>
								<view class="flex-between">
									<view class="flex-y-baseline f-desc c-caption max-350 ellipsis">
										<view class="text-delete mr-sm" v-if="item.init_price">¥{{item.init_price}}
										</view>
										<view class="f-title c-warning mr-sm">¥{{item.price}}</view>/
										{{item.time_long}}分钟
									</view>
									<view class="flex-warp">
										<block v-if="item.num">
											<button @tap.stop="changeNum(-1,index)" class="reduce"
												:style="{borderColor:primaryColor,color:primaryColor}"><i
													class="iconfont icon-jian-bold"></i></button>
											<button class="addreduce clear-btn">{{item.num || 0}}</button>
										</block>
										<button @tap.stop="changeNum(1,index)" class="add"
											:style="{background:primaryColor,borderColor:primaryColor}"><i
												class="iconfont icon-jia-bold"></i></button>
									</view>
								</view>
							</view>
						</view>
					</block>
					<block v-if="showType == 'message'">
						<view class="list-message flex-warp pd-lg" :class="[{'b-1px-t':index!=0}]"
							v-for="(item,index) in commentList.data" :key="index">
							<image mode="aspectFill" class="item-avatar radius" :src="item.avatarUrl"></image>
							<view class="flex-1 ml-md">
								<view class="flex-between">
									<view class="flex-y-center">
										<view class="f-paragraph c-title mr-md max-200 ellipsis">{{item.nickName}}
										</view>
										<view class="flex-warp">
											<i class="iconfont iconpingfen1 icon-font-color"
												:style="{backgroundImage: aindex< item.star?`-webkit-linear-gradient(270deg, #FAD961 0%, #F76B1C 100%)`:`-webkit-linear-gradient(270deg, #f4f6f8 0%, #ccc 100%)`}"
												v-for="(aitem,aindex) in 5" :key="aindex"></i>
										</view>
									</view>
									<view class="f-icontext c-caption">{{item.create_time}}</view>
								</view>
								<view class="flex-warp mt-sm">
									<view class="pt-sm pb-sm pl-md pr-md mt-sm mr-sm radius fill-body f-caption c-desc"
										v-for="(item,index) in item.lable_text" :key="index">{{item}}</view>
								</view>
								<view class="f-caption c-caption mt-md">
									<text decode="emsp" style="word-break:break-all;">{{item.text}}</text>
								</view>
							</view>
						</view>
					</block>
				</scroll-view>
				<view style="margin: 0 100rpx;"
					v-if="!loading&&((showType == 'technician' && serviceList.length<=0) || (showType == 'message' && commentList.data&&commentList.data.length<=0))">
					<abnor></abnor>
				</view>
				<block v-if="showType == 'message' && commentList.last_page > 1">
					<view class="space-lg b-1px-t"></view>
					<view
						@tap.stop="$refs.technician_item.close(),$util.goUrl({url:`/user/pages/comment?index=${showInd}`})"
						class="more-btn flex-center f-paragraph c-base radius"
						style="width:300rpx;height: 80rpx;margin:0 auto" :style="{background:primaryColor}">查看更多
					</view>
					<view class="space-lg"></view>
				</block>
				<view class="flex-between pd-lg b-1px-t" v-if="showType == 'technician' && carList.car_count > 0">
					<view class="flex-center">合计：<view class="f-title c-warning text-bold ml-sm">
							¥{{carList.car_price}}
						</view>
					</view>
					<view @tap.stop="toOrder" class="order-btn flex-center f-desc c-base radius"
						:style="{background: primaryColor}">提交订单
					</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</uni-popup>

		<view :style="{height: `${configInfo.tabbarHeight}px`}"></view>
		<tabbar :cur="2"></tabbar>

		<!-- #ifdef APP-PLUS -->
		<login-info></login-info>
		<!-- #endif -->
		
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
	import technicianSearchFix from "@/components/technician-search-fix.vue"
	import technicianListItem from "@/components/technician-list-item.vue"
	import tabbar from "@/components/tabbar.vue"
	export default {
		components: {
			technicianSearchFix,
			technicianListItem,
			tabbar
		},
		data() {
			return {
				isLoad: false,
				options: {},
				loading: true,
				showInd: -1,
				showType: '',
				lockTap: false,
				serviceList: [],
				commentList: [],
				navTitle: ''
			}
		},
		computed: mapState({
			pageActive: state => state.technician.pageActive,
			haveOperItem: state => state.technician.haveOperItem,
			cityId: state => state.technician.cityId,
			cityIndex: state => state.technician.cityIndex,
			cityList: state => state.technician.cityList,
			activeIndex: state => state.technician.activeIndex,
			tabList: state => state.technician.tabList,
			param: state => state.technician.param,
			list: state => state.technician.list,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			loginType: state => state.user.loginType,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			locaRefuse: state => state.user.locaRefuse,
			changeAddr: state => state.user.changeAddr,
			isGzhLogin: state => state.user.isGzhLogin,
			haveShieldOper: state => state.user.haveShieldOper,
			carList: state => state.order.carList,
			useChooseLocation: state => state.user.useChooseLocation,
			userCoachStatus: state => state.user.userCoachStatus,
			changeOnAddr: state => state.user.changeOnAddr,
			noChangeLoca: state => state.user.noChangeLoca,
		}),
		async onLoad(options) {
			if(options.pid){
				options = await this.updateCommonOptions(options)
			}
			this.options = options
			// uni.setNavigationBarColor({
			// 	frontColor: '#000000',
			// 	backgroundColor: '#000000'
			// })
			let {
				pid = 0,
					city_id = 0,
					coach_id = 0,
					coach_name = ''
			} = options

			let param = this.$util.deepCopy(this.param)
			param.coach_id = coach_id
			param.coach_name = coach_name
			this.updateTechnicianItem({
				key: 'param',
				val: param
			})

			if (coach_id) {
				this.updateTechnicianItem({
					key: 'cityId',
					val: city_id
				})
			}
			
			// #ifdef H5
			let title = ''
			this.configInfo.tabBar.map(item => { 
				if(item.id == 2){
					title = item.name
				}
			})
			uni.setNavigationBarTitle({
				title
			})
			this.navTitle = title
			// #endif
			// #ifndef H5
			this.$util.showLoading()
			// #endif

			let {
				data = []
			} = this.list
			let {
				pageActive,
				changeAddr
			} = this
			console.log(changeAddr,this.location, '==============> changeAddr')
			let {
				realtime_location = 0
			} = this.configInfo
			let city_refresh = data.length == 0 || data[0].city_id != this.cityId
			if (pageActive && (!realtime_location && !changeAddr) && (!coach_id || !pid) && !city_refresh) {
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
				return
			}
			await this.initIndex()
		},
		async onShow() {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.toAppShare()
			}
			// #endif 
			if (this.useChooseLocation) {
				this.updateUserItem({
					key: 'useChooseLocation',
					val: false
				})
			}
			if (this.haveShieldOper == 2 || this.haveOperItem) {
				this.initIndex()
				this.updateUserItem({
					key: 'haveShieldOper',
					val: 0
				})
				this.updateTechnicianItem({
					key: 'haveOperItem',
					val: false
				})
			}
			
			// #ifdef APP-PLUS
			let {
				lat: locaLat = 0
			} = this.location
			if (!locaLat && this.pageActive && !this.locaRefuse) {
				let {
					lng = 0,
						lat = 0
				} = await this.$util.getUtilLocation()
				if (!lat && !lng) return
				await this.getList(1, true)
				let updateArr = ['updateServiceItem','updateStoreItem']
				updateArr.map(item => {
					this[item]({
						key: 'pageActive',
						val: false
					})
				})
			}
			// #endif
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.updateUserItem({
				key: 'changeAddr',
				val: false
			})
			
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.getList(this.param.page + 1);
		},
		onShareAppMessage(e) {
			let {
				id: pid = 0
			} = this.userInfo
			let path = `/pages/technician?pid=${pid}`
			this.$util.log(path)
			return {
				title: '',
				imageUrl: '',
				path,
			}
		},
		watch: {
			changeOnAddr(newval, oldval) {
				let {
					noloca,
					refresh
				} = this.noChangeLoca
				if (newval && noloca) {
					this.initUtilLocaData(refresh)
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
		methods: {
			...mapActions(['updateCommonOptions','getConfigInfo', 'getUserInfo', 'getCarList', 'getCityList', 'getServiceCoachList']),
			...mapMutations(['updateUserItem', 'updateTechnicianItem', 'updateServiceItem','updateStoreItem']),
			async initIndex(refresh = false) {
				// // #ifdef H5
				// if (!refresh && this.$jweixin.isWechat()) {
				// 	await this.$jweixin.initJssdk();
				// 	this.toAppShare()
				// }
				// // #endif
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
						await this.getUserInfo()
					}
					// #endif
					// #ifndef H5
					await this.getUserInfo()
					// #endif 
				}
				this.isLoad = true

				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				await this.getList(1, true)
				this.updateTechnicianItem({
					key: 'pageActive',
					val: true
				})
			},
			initRefresh() {
				this.showInd = -1
				let {
					coach_id,
					coach_name
				} = this.param
				let param = Object.assign({}, this.param, {
					page: 1,
					sex: -1,
					ser_id: 0,
					ser_title: '全部服务',
					coach_name: coach_id ? coach_name : '',
					order: 0
				})
				this.updateTechnicianItem({
					key: 'param',
					val: param
				})
				this.initIndex(true)
			},
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo
				let title = this.navTitle || this.$t('action.attendantName')
				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/pages/technician?pid=${pid}`
				let imageUrl = ''
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, '', href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
			},
			async toOpenLocation() {
				// #ifdef MP-WEIXIN
				let privacyCheck = this.$refs.user_privacy.check()
				if (privacyCheck) {
					this.$refs.user_privacy.open()
					return
				}
				// #endif
				await this.$util.checkAuth({
					type: 'userLocation',
					checkApp: true
				})
				// #ifdef MP-WEIXIN
				this.initIndex()
				// #endif
				
				// this.$util.checkAuth({
				// 	type: 'userLocation'
				// })
				// // #ifdef APP-PLUS
				// uni.getSystemInfo({
				// 	success: (sys) => {
				// 		if (sys.platform == 'ios') {
				// 			plus.runtime.openURL("app-settings://");
				// 		} else {
				// 			var main = plus.android.runtimeMainActivity();
				// 			var Intent = plus.android.importClass(
				// 				"android.content.Intent");
				// 			//可能应该直接进入应用列表的权限设置？=> android.settings.APPLICATION_SETTINGS  
				// 			var mIntent = new Intent(
				// 				'android.settings.LOCATION_SOURCE_SETTINGS'
				// 			);
				// 			main.startActivity(mIntent);
				// 		}
				// 	}
				// })
				// // #endif
			},
			toSearch(val) {
				let param = this.$util.deepCopy(this.param)
				param.coach_id = ''
				param.coach_name = val
				this.updateTechnicianItem({
					key: 'param',
					val: param
				})
				this.getList(1)
			},
			changeParams(param) {
				this.updateTechnicianItem({
					key: 'param',
					val: param
				})
				this.getList(1)
			},
			// // 选择地区
			// async toChooseLocation() {
			// 	await this.getList(1, true, 1)
			// },
			// 选择地区
			async toChooseLocation() {
				// #ifdef MP-WEIXIN
				let privacyCheck = this.$refs.user_privacy && this.$refs.user_privacy.check()
				if (privacyCheck) {
					this.$refs.user_privacy.open()
					return
				}
				// #endif  
				console.log('选择地区=========> ')
				let location = await this.$util.chooseLocation(1)
				let {
					lat,
					lng
				} = location
				if (!lat) return
				console.log('选择地址=========> ',location)
				this.updateUserItem({
					key: 'location',
					val: location
				})
				this.updateUserItem({
					key: 'changeAddr',
					val: true
				})
				if (this.$refs.open_city_item) {
					this.$refs.open_city_item.city = ''
				}
				let {
					coach_id: cid = 0
				} = this.param
				await this.getCityList({
					lat,
					lng,
					change: cid ? 0 : 1
				})
				await this.getList(1)
			},
			async getList(page = 0, refresh = false) {
				if (page) {
					let param = this.$util.deepCopy(this.param)
					param.page = page
					this.updateTechnicianItem({
						key: 'param',
						val: param
					})
				}
				this.loading = true
				let {
					location,
					locaRefuse,
					changeAddr
				} = this
			
				let {
					realtime_location = 0
				} = this.configInfo
			
			
				let {
					status: coach_status,
					coach_position
				} = this.userCoachStatus
			
				if (this.param.page == 1) {
				
				// #ifdef APP-PLUS
				if (!locaRefuse && ((!location.lat || (location.lat && location.address == '暂未获取到位置信息')) || (
						refresh &&
						realtime_location && !changeAddr))) {
					// #endif
					// #ifndef APP-PLUS
					if ((!location.lat || (location.lat && location.address == '暂未获取到位置信息')) || (refresh &&
							realtime_location && !changeAddr)) {
						// #endif
		
						// #ifdef MP-WEIXIN
						let privacyCheck = this.$refs.user_privacy && this.$refs.user_privacy.check()
						if (privacyCheck) {
							this.$refs.user_privacy.open()
							this.isLoad = true
							this.loading = false
							this.$util.hideAll()
							return
						}
						// #endif
						console.log(coach_status, coach_position, '===========> getList')
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
									noloca,
									refresh
								}
							})
						} else {
							await this.$util.getUtilLocation()
						}
					}
				}
				if (coach_status == 2 && coach_position && this.param.page == 1 && this.noChangeLoca.noloca) return
				this.initUtilLocaData(refresh)
			},
			async toResetChangeLoca() {
				let {
					refresh
				} = this.noChangeLoca
				await this.$util.getUtilLocation()
				this.initUtilLocaData(refresh)
				this.updateUserItem({
					key: 'noChangeLoca',
					val: {
						noloca: false
					}
				})
			},
			async initUtilLocaData(refresh) {
				let {
					changeAddr
				} = this
		
				let {
					realtime_location = 0
				} = this.configInfo
		
				let {
					lng = 0,
						lat = 0,
						is_util_loca = 0
				} = this.location
				if (refresh) {
					let {
						coach_id: cid = 0
					} = this.param
					await this.getCityList({
						lng,
						lat,
						change: cid ? 0 : 1
					})
				}
				// #ifdef APP-PLUS
				if (!lat && !lng) {
					this.updateTechnicianItem({
						key: 'cityIndex',
						val: -1
					})
					this.isLoad = true
					this.loading = false
					this.$util.hideAll()
					this.updateTechnicianItem({
						key: 'list',
						val: {
							data: [],
							last_page: 1,
							current_page: 1
						}
					})
					return
				}
				// #endif
		
				let {
					list: oldList,
					activeIndex,
					tabList,
					cityList,
					cityIndex,
					cityId: city_id
				} = this
		
				let param = this.$util.deepCopy(this.param)
		
				let ind = cityList.findIndex(item => {
					return item.id == city_id
				})
		
				if (ind == -1) {
					if (param.page == 1 && (is_util_loca || (realtime_location && !changeAddr))) {
						await this.$util.getMapInfo(true, {}, changeAddr)
					}
					if (this.$refs.open_city_item) {
						this.$refs.open_city_item.initIndex()
					}
					this.loading = false
					this.$util.hideAll()
					this.updateTechnicianItem({
						key: 'list',
						val: {
							data: [],
							last_page: 1,
							current_page: 1
						}
					})
					return
				}
		
		
				city_id = ind == -1 ? 0 : city_id
				cityIndex = ind == -1 ? 0 : ind
				this.updateTechnicianItem({
					key: 'cityIndex',
					val: cityIndex
				})
				this.updateTechnicianItem({
					key: 'cityId',
					val: city_id
				})
				let {
					coach_format = 1,
						coach_filter_show = 0
				} = this.configInfo
				param = Object.assign({}, param, this.check, {
					lat,
					lng,
					city_id
				})
				if (coach_format == 1) {
					param.type = tabList[activeIndex].id
				}
				if (!coach_filter_show) {
					delete param.cate_id
					delete param.recommend
					delete param.free_fare
					delete param.service_type
					delete param.store_name
				} else {
					if (param.recommend === 0) {
						delete param.recommend
					}
					if (param.cate_id === 0) {
						delete param.cate_id
					}
					if (param.service_type === 0) {
						delete param.service_type
					}
				}
				await this.getServiceCoachList(param)
				this.loading = false
				this.$util.hideAll()
				if (param.page == 1 && (is_util_loca || (realtime_location && !changeAddr))) {
					this.$util.getMapInfo(true, {}, changeAddr)
				}
			},
			// async getList(page = 0, refresh = false, change = 0) {
			// 	if (page) {
			// 		let param = this.$util.deepCopy(this.param)
			// 		param.page = page
			// 		this.updateTechnicianItem({
			// 			key: 'param',
			// 			val: param
			// 		})
			// 	}
			// 	let {
			// 		location
			// 	} = this
			// 	if (!location.lat || (location.lat && location.address == '定位失败')) {
			// 		// #ifdef H5
			// 		if (this.$jweixin.isWechat()) {
			// 			this.$util.showLoading()
			// 			// await this.$jweixin.initJssdk();
			// 			await this.$jweixin.wxReady2();
			// 			let {
			// 				lat = '', lng = ''
			// 			} = location
			// 			if (!lat && !lng) {
			// 				let {
			// 					latitude,
			// 					longitude
			// 				} = await this.$jweixin.getWxLocation()
			// 				lat = latitude
			// 				lng = longitude
			// 				location = {
			// 					lng,
			// 					lat,
			// 					address: '定位失败',
			// 					province: '',
			// 					city: '',
			// 					district: ''
			// 				}
			// 			}
			// 			if (lat && lng) {
			// 				let key = `${lat},${lng}`
			// 				let data = await this.$api.base.getMapInfo({
			// 					location: key
			// 				})
			// 				let {
			// 					status,
			// 					result
			// 				} = JSON.parse(data)
			// 				if (status == 0) {
			// 					let {
			// 						address,
			// 						address_component
			// 					} = result
			// 					let {
			// 						province,
			// 						city,
			// 						district
			// 					} = address_component
			// 					location = {
			// 						lng,
			// 						lat,
			// 						address,
			// 						province,
			// 						city,
			// 						district
			// 					}
			// 				}
			// 			}
			// 		}
			// 		// #endif
			// 		// #ifndef H5
			// 		location = await this.$util.getBmapLocation()
			// 		// #endif
			// 		this.updateUserItem({
			// 			key: 'location',
			// 			val: location
			// 		})
			// 	}
			// 	let {
			// 		lng = 0,
			// 			lat = 0
			// 	} = location

			// 	if (refresh) {
			// 		await this.getCityList({
			// 			lng,
			// 			lat,
			// 			change
			// 		})
			// 	}

			// 	let {
			// 		list: oldList,
			// 		tabList,
			// 		activeIndex,
			// 		cityList,
			// 		cityIndex,
			// 		cityId: city_id
			// 	} = this

			// 	let param = this.$util.deepCopy(this.param)

			// 	let ind = cityList.findIndex(item => {
			// 		return item.id == city_id
			// 	})
			// 	city_id = ind == -1 ? 0 : city_id
			// 	cityIndex = ind == -1 ? 0 : ind


			// 	this.updateTechnicianItem({
			// 		key: 'cityIndex',
			// 		val: cityIndex
			// 	})

			// 	this.updateTechnicianItem({
			// 		key: 'cityId',
			// 		val: city_id
			// 	})

			// 	param = Object.assign({}, param, {
			// 		lng,
			// 		lat,
			// 		city_id
			// 	})

			// 	let {
			// 		coach_format = 1
			// 	} = this.configInfo

			// 	if (coach_format == 1) {
			// 		param.type = tabList[activeIndex].id
			// 	}

			// 	await this.getServiceCoachList(param)
			// 	this.loading = false
			// 	this.$util.hideAll()
			// },
			handerTabChange(index) {
				// this.updateTechnicianItem({
				// 	key: 'activeIndex',
				// 	val: index
				// })
				// this.showInd = -1
				// this.$util.showLoading()
				// uni.pageScrollTo({
				// 	scrollTop: 0
				// })
				// this.getList(1)
				
			},
			toPreviewImage(index, key = 0) {
				let {
					self_img: urls,
					work_img
				} = this.list.data[index]
				if (key) {
					urls = [work_img]
				}
				this.$util.previewImage({
					current: urls[0],
					urls
				})
			},
			async toShowChoose(key) {
				this.chooseType = key
				if (key == 'service') {
					await this.getChooseServiceList()
				}
				this.$refs.show_choose_item.open()
			},
			async getChooseServiceList() {

			},
			async toShowPopup(index, key, is_work = 1) {
				if (is_work == 0) return
				this.showInd = index
				this.showType = key
				if (key == 'technician') {
					let {
						user_id = 0
					} = this.list.data[index]
					if (!user_id) return
					await this.getServiceList()
				} else {
					await this.getCommentList()
				}
				this.$refs.technician_item.open()
			},
			async getCommentList() {
				let {
					id: coach_id
				} = this.list.data[this.showInd]
				let param = {
					coach_id,
					page: 1,
				}
				this.commentList = await this.$api.service.commentList(param)
			},
			async getServiceList() {
				let {
					id: coach_id
				} = this.list.data[this.showInd]
				await this.getCarList({
					coach_id
				})
				let {
					data
				} = await this.$api.service.coachServiceList({
					coach_id
				})
				let arr = []
				if (this.carList.list && this.carList.list.length > 0) {
					this.carList.list.map(item => {
						arr.push(item.service_id)
					})
				}
				if (data && data.length > 0) {
					data.map(item => {
						if (arr.includes(item.id)) {
							let carInd = arr.findIndex(aitem => {
								return aitem == item.id
							})
							item.num = this.carList.list[carInd].num
							item.cart_id = this.carList.list[carInd].id
						}
					})
				}
				this.serviceList = data
			},
			async toCollect(index) {
				let {
					id,
					is_collect,
					collect_num
				} = this.list.data[index]
				let methodModel = is_collect ? 'delCollect' : 'addCollect'
				await this.$api.mine[methodModel]({
					coach_id: id
				})
				this.$util.showToast({
					title: is_collect ? '取消成功' : '收藏成功'
				})
				this.list.data[index].is_collect = is_collect == 1 ? 0 : 1
				this.list.data[index].collect_num = is_collect == 1 ? collect_num - 1 :
					collect_num + 1
			},
			// 向导详情
			goInfo(index) {
				let {
					id,
					store = {}
				} = this.list.data[index]
				let {
					plugAuth = {}
				} = this.configInfo
				this.$util.toCheckLogin({
					url: plugAuth.store && store && store.id ? `/shopstore/pages/detail?id=${store.id}` :
						`/user/pages/technician-info?id=${id}`
				})
			},
			// 服务详情
			goDetail(index) {
				let {
					id
				} = this.serviceList[index]
				let url = `/user/pages/detail?id=${id}`
				this.$util.goUrl({
					url
				})
			},
			// 加/减数量
			async changeNum(mol, serInd) {
				let {
					id: coach_id
				} = this.list.data[this.showInd]
				let {
					id: service_id,
					cart_id = 0
				} = this.serviceList[serInd]
				if (this.lockTap) return;
				this.lockTap = true;
				let methodModel = mol > 0 ? 'addCar' : 'delCar'
				let param = mol > 0 ? {
					service_id,
					coach_id,
					num: 1
				} : {
					id: cart_id,
					num: 1
				}
				if (methodModel == 'delCar' && !param.id) {
					this.lockTap = false
					return
				}
				try {
					let add_cart_id = await this.$api.order[methodModel](param)
					let {
						num = 0,
							cart_id = 0
					} = this.serviceList[serInd]
					this.serviceList[serInd].num = num + mol
					if (add_cart_id && mol > 0 && !cart_id) {
						this.serviceList[serInd].cart_id = add_cart_id
					}
					if (this.serviceList[serInd].num < 1) {
						this.serviceList[serInd].cart_id = 0
					}
					await this.getCarList({
						coach_id
					})
					this.lockTap = false
				} catch (e) {
					this.lockTap = false
				}
			},
			// 下单
			toOrder() {
				if (this.carList.car_count < 1) {
					this.$util.showToast({
						title: `请选择服务`
					})
					return
				}
				let {
					id
				} = this.list.data[this.showInd]
				this.$refs.technician_item.close()
				this.$util.goUrl({
					url: `/user/pages/order?id=${id}`
				})
			},
		}
	}
</script>


<style lang="scss">
	page {
		background: #fff
	}
</style>
