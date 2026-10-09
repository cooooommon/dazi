<template>
	<view class="pages-technician">
		<fixed :initHeight="configInfo.coach_format">
			<view class="fix-info fill-body rel" :style="{height:'124rpx'}">
				<view class="search-info fill-base pd-lg abs" style="border-radius:0 0 40rpx 40rpx">
					<search @input="toSearch" type="input" :keyword="param.coach_name" :padding="0" :radius="30"
						backgroundColor="#F0F0F0" :placeholder="`请输入${$t('action.attendantName')}昵称`">
					</search>
				</view>
			</view>
		</fixed>
		<block v-if="list.data && list.data.length>0">
			<view class="list-item flex-center pd-lg mt-md ml-md mr-md fill-base radius-16 rel"
				v-for="(item,index) in list.data" :key="index">
				<image mode="aspectFill" class="king-img abs" src="https://lbqny.migugu.com/admin/anmo/mine/king.gif"
					v-if="item.coach_type_status==1">
				</image>
				<view class="flex-center flex-column">
					<view class="item-img rel">
						<!-- #ifdef H5 -->
						<view class="item-img radius">
							<view @tap.stop="toPreviewImage(index,1)" class="h5-image item-img radius"
								:style="{ backgroundImage : `url('${item.work_img}')`}">
							</view>
						</view>
						<view @tap.stop="item.coach_type_status==1?toPreviewImage(index,1):''" class="h5-image abs"
							:class="[`${imgType[item.coach_type_status]}-img`]"
							:style="{ backgroundImage :  `url('https://lbqny.migugu.com/admin/anmo/mine/${imgType[item.coach_type_status]}_2.gif')`}"
							v-if="item.coach_type_status">
						</view>
						<!-- #endif -->
						<!-- #ifndef H5 -->
						<image @tap.stop="toPreviewImage(index,1)" mode="aspectFill" class="item-img radius"
							:src="item.work_img">
						</image>
						<image @tap.stop="item.coach_type_status==1?toPreviewImage(index,1):''" class="abs"
							:class="[`${imgType[item.coach_type_status]}-img`]"
							:src="`https://lbqny.migugu.com/admin/anmo/mine/${imgType[item.coach_type_status]}_2.gif`"
							v-if="item.coach_type_status">
						</image>
						<!-- #endif -->
					</view>
					<view class="item-tag flex-center f-icontext c-base radius-20"
						:style="{background: item.text_type === 1 ? configInfo.service_btn_color : item.text_type == 3?primaryColor: item.text_type==4?'#e1493b':'',color:item.text_type === 1 ? configInfo.service_font_color :[3,4].includes(item.text_type) ? '#fff' : ''}">
						{{textType[item.text_type]}}
					</view>
				</view>
				<view class="flex-1 ml-md max-510">
					<view class="flex-between">
						<view class="flex-y-center f-title c-title">
							<view @tap.stop="toPreviewImage(index)"
								class="more-img flex-center mr-sm f-icontext c-base rel" :style="{color:primaryColor}">
								<view class="bg abs" :style="{background:primaryColor}"></view>
								更多照片
							</view>
							<view class="text-bold max-200 ellipsis">{{item.coach_name}}</view>
						</view>
						<view class="flex-y-center f-icontext order-num">
							已服务<view class="text-bold" :style="{color:primaryColor}">
								{{item.order_num > 9999 ? '9999+' : item.order_num}}
							</view>单</view>
					</view>
					<view class="icon-item flex-y-center f-icontext c-caption mt-sm mb-md pb-md b-1px-b">
						<view class="flex-y-center mr-lg" v-if="item.near_time">
							<i class="iconfont iconzuizaokeyue1 c-caption"></i>
							<view class="flex-y-center">最早可约<view style="margin-left: 4rpx;"
									:style="{color:item.near_time?primaryColor:'#fff'}">
									{{item.near_time}}
								</view>
							</view>
						</view>
						<view class="flex-y-center mr-lg"><i class="iconfont iconpingfen1 icon-font-color"></i>
							<view class="star-text">{{item.star}}</view>
						</view>
						<view @tap.stop="toShowPopup(index,'message')" class="flex-y-center mr-lg"><i
								class="iconfont iconpingjia"></i>{{item.comment_num}} </view>
						<view @tap.stop="toCollect(index)" class="flex-y-center"><i class="iconfont"
								:class="[{'iconshoucang12':!item.is_collect},{'iconshoucang22':item.is_collect}]"
								:style="{color:item.is_collect ? primaryColor :''}"></i>{{item.collect_num}} </view>

					</view>
					<view class="flex-between">
						<view class="icon1-item flex-center f-icontext c-paragraph">
							<view class="flex-y-center" style="width: 140rpx;"><i
									class="iconfont iconjuli3 c-caption"></i>{{item.distance}}
							</view>
							<view @tap.stop="goInfo(index)" class="flex-1 flex-center">
								<i class="iconfont c-caption"
									:class="[{'icondianming':config.plugAuth.store && item.store && item.store.id},{'iconxiangqing':!config.plugAuth.store || !item.store}]"></i>
								<view class="max-170 ellipsis">
									{{config.plugAuth.store && item.store && item.store.id?item.store.title:'详情'}}
								</view>
							</view>
						</view>
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)"
							:must="true" :type="!userInfo.phone ? 'phone' : 'userInfo'"
							@go="toShowPopup(index,'technician', item.is_work == 0 )" style="width:130rpx;">
							<view class="item-btn flex-center f-desc c-base"
								:style="{background:item.user_id? item.is_work == 0 ? '#ccc' : primaryColor:'#888'}">
								立即预约
							</view>
						</auth>
					</view>
				</view>
			</view>
		</block>

		<!-- #ifdef H5 -->
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<!-- #endif -->
		<!-- #ifndef H5 -->
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0&&location.lng" :loading="loading"
			v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1&&location.lng"></abnor>
		<abnor @confirm="toOpenLocation" :tip="[{ text: '定位失败，请开启地理位置授权后刷新页面重试~', color: 0 }]"
			:button="[{ text: '开启定位' , type: 'confirm' }]" btnSize="" v-if="!loading && !location.lng"> </abnor>
		<!-- #endif -->

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
					v-if="!loading&&((showType == 'technician' && serviceList.length<=0) || (showType == 'message' && commentList.data.length<=0))">
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
					<view class="flex-center">合计：<view class="f-title c-warning text-bold ml-sm">¥{{carList.car_price}}
						</view>
					</view>
					<view @tap.stop="toOrder" class="order-btn flex-center f-desc c-base radius"
						:style="{background: primaryColor}">提交订单
					</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		components: {},
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
				activeIndex: 0,
				param: {
					page: 1,
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
				commentList: []
			}
		},
		computed: mapState({
			pageActive: state => state.technician.pageActive,
			tabList: state => state.technician.tabList,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			loginType: state => state.user.loginType,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			isGzhLogin: state => state.user.isGzhLogin,
			haveShieldOper: state => state.user.haveShieldOper,
			carList: state => state.order.carList,
		}),
		async onLoad(options) {
			this.options = options
			// #ifndef H5
			this.$util.showLoading()
			// #endif
			await this.initIndex()
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			uni.setNavigationBarTitle({
				title: this.$t('action.attendantName') + '列表'
			})
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
			...mapActions(['getConfigInfo', 'getUserInfo', 'getCarList']),
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
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				await this.getList()
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			toOpenLocation() {
				this.$util.checkAuth({
					type: 'userLocation'
				})
				// #ifdef APP-PLUS
				uni.getSystemInfo({
					success: (sys) => {
						if (sys.platform == 'ios') {
							plus.runtime.openURL("app-settings://");
						} else {
							var main = plus.android.runtimeMainActivity();
							var Intent = plus.android.importClass(
								"android.content.Intent");
							//可能应该直接进入应用列表的权限设置？=> android.settings.APPLICATION_SETTINGS  
							var mIntent = new Intent(
								'android.settings.LOCATION_SOURCE_SETTINGS'
							);
							main.startActivity(mIntent);
						}
					}
				})
				// #endif
			},
			toSearch(val) {
				this.param.coach_name = val
				this.getList(1)
			},
			async getList(flag = 0, refresh = false, change = 0) {
				if (flag) {
					this.param.page = 1
				}
				let {
					location
				} = this
				if (!location.lat || (location.lat && location.address == '定位失败')) {
					// #ifdef H5
					if (this.$jweixin.isWechat()) {
						this.$util.showLoading()
						// await this.$jweixin.initJssdk();
						await this.$jweixin.wxReady2();
						let {
							lat = '', lng = ''
						} = location
						if (!lat && !lng) {
							let {
								latitude,
								longitude
							} = await this.$jweixin.getWxLocation()
							lat = latitude
							lng = longitude
							location = {
								lng,
								lat,
								address: '定位失败',
								province: '',
								city: '',
								district: ''
							}
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
					lng = 0,
						lat = 0
				} = location

				let {
					list: oldList,
					tabList,
					activeIndex,
				} = this
				let {
					id: store_id
				} = this.options
				let param = this.$util.deepCopy(this.param)
				param = Object.assign({}, param, {
					lng,
					lat,
					store_id
				})

				let {
					coach_format = 1
				} = this.configInfo

				if (coach_format == 1) {
					param.type = tabList[activeIndex].id
				}


				let methodModel = coach_format == 1 ? 'serviceCoachList' : 'typeServiceCoachList'

				let newList = await this.$api.service[methodModel](param);

				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			handerTabChange(index) {
				this.activeIndex = index
				this.showInd = -1
				this.$util.showLoading()
				uni.pageScrollTo({
					scrollTop: 0
				})
				this.getList(1)
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
			async toShowPopup(index, key, is_work) {
				if (is_work) return
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
				this.$util.goUrl({
					url: plugAuth.store && store && store.id ? `/shopstore/pages/detail?id=${store.id}` :
						`/user/pages/technician-info?id=${id}`
				})
			},
			// 服务详情
			goDetail(index) {
				let {
					id: sid
				} = this.options
				let {
					id
				} = this.serviceList[index]
				let url = `/user/pages/detail?id=${id}&store_id=${sid}`
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
</style>
