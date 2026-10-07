<template>
	<view class="pages-technician">

		<technician-search-fix from="choose" :params="param" @search="toSearch" @pickerChange="pickerChange"
			@chooseLocation="toChooseLocation" @changeParams="changeParams">
		</technician-search-fix>


		<view class="flex-warp pl-md pr-md" v-if="list.data && list.data.length>0">

			<view class="mb-md" :style="{marginRight: index%2 == 0 ? '20rpx':''}" v-for="(item,index) in list.data"
				:key="index">
				<technician-list-item @workImg="toPreviewImage(index,1)" @selfImg="toPreviewImage(index)"
					@comment="toShowPopup(index,'message')" @collect="toCollect(index)" @info="toOrder(index, item.is_work)"
					@order="toOrder(index , item.is_work)" :info="item">
				</technician-list-item>
			</view>

		</view>


		<!-- #ifdef H5 -->
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<!-- #endif -->
		<!-- #ifndef H5 -->
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0&&location.lng" :loading="loading"
			v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1&&location.address"></abnor>
		<abnor @confirm="toOpenLocation" :tip="[{ text: '定位失败，请开启地理位置授权后刷新页面重试~', color: 0 }]"
			:button="[{ text: '开启定位' , type: 'confirm' }]" btnSize="" v-if="!loading && !location.address"> </abnor>
		<!-- #endif -->

		<view class="space-footer"></view>

		<uni-popup ref="technician_item" type="bottom">
			<view class="technician-popup fill-base" v-if="showInd!=-1">
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
					<block v-if="showType == 'message'">
						<view class=" list-message flex-warp pd-lg" :class="[{'b-1px-t':index!=0}]"
							v-for="(item,index) in commentList.data" :key="index">
							<image mode="aspectFill" class="item-avatar radius" :src="item.avatarUrl"></image>
							<view class="flex-1 ml-md">
								<view class="flex-between">
									<view class="flex-y-center">
										<view class="f-paragraph c-title mr-md">{{item.nickName}}</view>
										<view class="flex-warp">
											<i class="iconfont iconyduixingxingshixin icon-font-color"
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
				<view style="margin: 0 100rpx;" v-if="!loading&&(showType == 'message' && commentList.data.length<=0)">
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
				<view class="space-safe" v-if="commentList.data.length > 0 && commentList.last_page == 1"></view>
			</view>
		</uni-popup>

		<uni-popup ref="choose_city_item" type="top" :custom="true" :zIndex="999">
			<view :style="{height:configInfo.navBarHeight + 8 + 'px'}"></view>
			<view class="choose-city-popup rel">
				<image class="city-img" src="https://lbqny.migugu.com/admin/anmo/technician/no_city.png"></image>
				<view class="text f-paragraph c-base abs">
					很抱歉,该城市未开通, 点击左上角切换其他城市下单服务
				</view>
				<view class="flex-between">
					<view></view>
					<view @tap.stop="$refs.choose_city_item.close()"
						class="item-btn flex-center f-paragraph c-title text-bold radius">知道了</view>
				</view>
			</view>
		</uni-popup>


		<view class="space-max-footer"></view>
		<fix-bottom-button @confirm="toBack" :text="[{type:'confirm',text:pageLen>1?`返回上页`:`返回首页`}]" bgColor="#fff">
		</fix-bottom-button>
		
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
	import technicianSearchFix from "@/components/technician-search-fix.vue"
	import technicianListItem from "@/components/technician-list-item.vue"
	export default {
		components: {
			technicianSearchFix,
			technicianListItem,
		},
		data() {
			return {
				isLoad: false,
				options: {},
				imgType: {
					1: 'top',
					2: 'hot',
					3: 'new'
				},
				textType: {
					1: '可服务',
					2: '服务中',
					3: '可预约',
					4: '不可预约'
				},
				param: {
					page: 1,
					sex: -1,
					ser_id: 0,
					ser_title: '',
					coach_name: ''
				},
				list: {
					data: []
				},
				loading: true,
				showInd: -1,
				showType: '',
				lockTap: false,
				serviceList: [],
				commentList: [],
				pageLen: 1
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			cityId: state => state.technician.cityId,
			cityIndex: state => state.technician.cityIndex,
			cityList: state => state.technician.cityList,
			loginType: state => state.user.loginType,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			changeAddr: state => state.user.changeAddr,
			locaRefuse: state => state.user.locaRefuse,
			useChooseLocation: state => state.user.useChooseLocation,
			userCoachStatus: state => state.user.userCoachStatus,
			changeOnAddr: state => state.user.changeOnAddr,
			noChangeLoca: state => state.user.noChangeLoca,
		}),
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
		async onLoad(options) {
			this.options = options
			uni.setNavigationBarColor({
				frontColor: '#000000',
				backgroundColor: '#000000'
			})
			// #ifndef H5
			this.$util.showLoading()
			// #endif
			await this.initIndex()
			this.isLoad = true
		},
		async onShow() {
			if (this.useChooseLocation) {
				this.updateUserItem({
					key: 'useChooseLocation',
					val: false
				})
			}
			// #ifdef APP-PLUS
			let {
				lat: locaLat = 0
			} = this.location
			if (!locaLat && !this.locaRefuse) {
				let {
					lng = 0,
						lat = 0
				} = await this.$util.getUtilLocation()
				if (!lat && !lng) return
				await this.initRefresh()
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
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
		methods: {
			...mapActions(['getCityList','getConfigInfo']),
			...mapMutations(['updateUserItem', 'updateServiceItem', 'updateTechnicianItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				if(!this.configInfo.id || refresh){
					this.getConfigInfo()
				}
				this.pageLen = getCurrentPages().length
				this.getList(refresh === false ? this.cityList.length == 0 : true)
			},
			initRefresh() {
				this.showInd = -1
				this.param = Object.assign({}, this.param, {
					page: 1,
					// sex: -1,
					coach_name: ''
				})
				this.initIndex(true)
			},
			pickerChange() {
				this.param.page = 1
				this.getList()
			},
			// toOpenLocation() {
			// 	this.$util.checkAuth({
			// 		type: 'userLocation'
			// 	})
			// 	// #ifdef APP-PLUS
			// 	uni.getSystemInfo({
			// 		success: (sys) => {
			// 			if (sys.platform == 'ios') {
			// 				plus.runtime.openURL("app-settings://");
			// 			} else {
			// 				var main = plus.android.runtimeMainActivity();
			// 				var Intent = plus.android.importClass(
			// 					"android.content.Intent");
			// 				//可能应该直接进入应用列表的权限设置？=> android.settings.APPLICATION_SETTINGS  
			// 				var mIntent = new Intent(
			// 					'android.settings.LOCATION_SOURCE_SETTINGS'
			// 				);
			// 				main.startActivity(mIntent);
			// 			}
			// 		}
			// 	})
			// 	// #endif
			// },
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
				this.initRefresh()
				// #endif
			},
			toSearch(val) {
				this.param.page = 1
				this.param.coach_name = val
				this.getList()
			},
			changeParams(param) {
				this.param = param
				this.getList()
			},
			// 选择地区
			async toChooseLocation() {
				// #ifdef MP-WEIXIN
				let privacyCheck = this.$refs.user_privacy.check()
				if (privacyCheck) {
					this.$refs.user_privacy.open()
					return
				}
				// #endif
				let location = await this.$util.chooseLocation(1)
				let {
					lat,
					lng,
					name,
					address
				} = location
				if (!lat) return
				if (this.realtimeTimer) clearInterval(this.realtimeTimer)
				this.updateUserItem({
					key: 'location',
					val: location
				})
				this.updateUserItem({
					key: 'changeAddr',
					val: true
				})
				this.param.page = 1
				await this.getCityList({
					lng,
					lat,
					change: 1
				})
				await this.getList()
			},
			async getList(refresh = false, change = 0) {
				let {
					location,
					changeAddr
				} = this

				let {
					realtime_location = 0
				} = this.configInfo

				let {
					status: coach_status,
					coach_position
				} = this.userCoachStatus

				if (this.param.page == 1 && ((!location.lat || (location.lat && location.address == '暂未获取到位置信息')) || (
						refresh && realtime_location && !changeAddr))) {
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
					this.updateServiceItem({
						key: 'pageActive',
						val: false
					})
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
					await this.getCityList({
						lng,
						lat,
						change: 1
					})
				}
			
				let {
					id: ser_id,
					store_id = 0
				} = this.options
				let {
					list: oldList,
					param,
					cityList,
					cityIndex,
					cityId: city_id
				} = this
			
				let ind = cityList.findIndex(item => {
					return item.id == city_id
				})
			
				if (ind == -1) {
					this.$refs.choose_city_item.open()
					this.loading = false
					this.$util.hideAll()
					this.list = {
						data: [],
						last_page: 1,
						current_page: 1
					}
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
			
				param = Object.assign({}, param, {
					ser_id,
					city_id,
					lat,
					lng,
				})
			
				let {
					coach_format = 1
				} = this.configInfo
			
				let methodModel = coach_format == 1 ? 'serviceCoachList' : 'typeServiceCoachList'
			
				let newList = await this.$api.service[methodModel](param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
				if (param.page == 1 && (is_util_loca || (realtime_location && !changeAddr))) {
					this.$util.getMapInfo(true, {}, changeAddr)
				}
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
			async toShowPopup(index, key) {
				this.showInd = index
				this.showType = key
				await this.getCommentList()
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
				this.list.data[index].collect_num = is_collect == 1 ? collect_num - 1 : collect_num + 1
			},
			// 查看详情
			async goInfo(index) {
				let {
					id,
					store = {}
				} = this.list.data[index]
				let {
					plugAuth = {}
				} = this.configInfo
				this.$util.goUrl({
					url: plugAuth.store && store && store.id ? `/shopstore/pages/detail?id=${store.id}` :
						`/user/pages/technician-info?id=${id}`
				})
			},
			// 预约
			async toOrder(index, is_work) {
				console.log(index, is_work)
				if (is_work == 0) return
				let {
					id: coach_id,
					user_id = 0
				} = this.list.data[index]
				let {
					place_order_path
				} = this.configInfo
				console.log(this.lockTap, 12121212)
				// if (!user_id) return
				let {
					id: service_id
				} = this.options
				if (this.lockTap) return;
				this.lockTap = true;
				try {
					if(place_order_path == 2){
						this.lockTap = false
						this.$util.goUrl({
							url: `/user/pages/technician-info?id=${coach_id}&ser_id=${service_id}`
						})
						return 
					}
					await this.$api.order.addCar({
						service_id,
						coach_id,
						num: 1,
						is_top: 1
					})
					this.lockTap = false
					let url = `/user/pages/order?id=${coach_id}&ser_id=${service_id}`
					this.$util.goUrl({
						url
					})
				} catch (e) {
					this.lockTap = false
				}
			},
			toBack() {
				let {
					pageLen
				} = this
				let url = pageLen > 1 ? 1 : `/pages/service`
				let openType = pageLen > 1 ? `navigateBack` : `reLaunch`
				this.$util.goUrl({
					url,
					openType
				})
			}
		}
	}
</script>


<style lang="scss">
	.pages-technician {}
	page{
		background-color: #fff;
	}
</style>
