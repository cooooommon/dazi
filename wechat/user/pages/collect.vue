<template>
	<view class="pages-technician">

		<view class="flex-warp pl-md pr-md" v-if="list.data && list.data.length>0">
			<view class="mt-md" :style="{marginRight: index%2 == 0 ? '20rpx':''}" v-for="(item,index) in list.data"
				:key="index">
				<technician-list-item @workImg="toPreviewImage(index,1)" @selfImg="toPreviewImage(index)"
					@comment="toShowPopup(index,'message')" @collect="toCollect(index)" @info="goInfo(index)"
					@order="toShowPopup(index,'technician', item.is_work )" :info="item">
				</technician-list-item>
			</view>

		</view>

		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0&&location.lng" :loading="loading"
			v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1&&location.lng"></abnor>

		<abnor @confirm="$util.checkAuth({ type: 'userLocation' })"
			:tip="[{ text: '定位失败，请开启地理位置授权后刷新页面重试~', color: 0 }]" :button="[{ text: '开启定位' , type: 'confirm' }]"
			btnSize="" v-if="!loading && !location.lng"> </abnor>


		<view class="space-footer"></view>

		<uni-popup ref="technician_item" type="bottom">
			<view class="technician-popup fill-base" v-if="showInd != -1">
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
				<view class="space-safe"
					v-if="(showType == 'message' &&  commentList.data.length > 0 && commentList.last_page == 1) || (showType == 'technician' && serviceList.length>0)">
				</view>
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
	import technicianListItem from "@/components/technician-list-item.vue"
	export default {
		components: {
			technicianListItem
		},
		data() {
			return {
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
					ser_id: 0,
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
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			carList: state => state.order.carList,
		}),
		onLoad() {
			// #ifndef H5
			this.$util.showLoading()
			// #endif
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
			...mapActions(['getCarList']),
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
				uni.setNavigationBarTitle({
					title: '收藏' + this.$t('action.attendantName')
				})
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.showInd = -1
				this.param.page = 1
				this.initIndex(true)
			},
			toSearch(val) {
				this.param.page = 1
				this.param.coach_name = val
				this.getList()
			},
			async getList() {
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
					list: oldList,
					param,
				} = this
				let {
					lng = 0,
						lat = 0
				} = location
				param = Object.assign({}, param, {
					lng,
					lat
				})
				let newList = await this.$api.mine.coachCollectList(param);
				newList.data.map(item => {
					item.is_collect = 1
				})

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
				this.activeIndex = index;
				this.showInd = -1
				this.$util.showLoading()
				this.param.page = 1;
				this.getList()
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
				this.carList.list.map(item => {
					arr.push(item.service_id)
				})
				data.map(item => {
					if (arr.includes(item.id)) {
						let carInd = arr.findIndex(aitem => {
							return aitem == item.id
						})
						item.num = this.carList.list[carInd].num
						item.cart_id = this.carList.list[carInd].id
					}
				})
				this.serviceList = data
			},
			async toCollect(index) {
				let {
					id,
					is_collect,
					collect_num
				} = this.list.data[index]
				await this.$api.mine.delCollect({
					coach_id: id
				})
				this.$util.showToast({
					title: '取消成功'
				})
				this.list.data.splice(index, 1)
			},
			// 向导详情
			goInfo(index) {
				let {
					id
				} = this.list.data[index]
				this.$util.goUrl({
					url: `/user/pages/technician-info?id=${id}`
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
			goDetail(index) {
				let {
					id
				} = this.list.data[index]
				let url = `/user/pages/technician-info?id=${id}`
				this.$util.goUrl({
					url
				})
			}
		}
	}
</script>


<style lang="scss">
	.pages-technician {}
</style>
