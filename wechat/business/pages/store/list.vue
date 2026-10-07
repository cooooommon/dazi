<template>
	<view class="">
		<fixed>
			<search @input="toSearch" type="input" :padding="30" :radius="0" backgroundColor="#fff"
				placeholder="请输入场地名称">
			</search>
		</fixed>
		<block v-if="isLoad">
			<view class="store-box pt-md pl-md pr-md" v-if="isLoad">
				<block v-for="(item,index) in list.data" :key="index">
					<!-- <auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
						:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({url: `/business/pages/store/detail?id=${item.id}`})">
						
					</auth> -->
					<view class="mb-md radius-16 fill-base pd-lg flex-center" @tap="$util.goUrl({url: `/business/pages/store/detail?id=${item.id}`})">
						<view class="store-img radius-16 rel">
							<image :src="item.cover" mode="aspectFill" class="store-img radius-16"></image>
							<text class="abs advert c-base flex-center f-ms-little" v-if="item.is_top" :style="{background: primaryColor}">广告</text>
						</view>
						<view class="flex-1 pl-md">
							<view class="flex-between pb-sm">
								<text class="text-bold f-title ellipsis max-350">{{item.name}}</text>
								<text class="f-desc" :style="{color: primaryColor}">{{item.distance | handleDistance}}km</text>
							</view>
							<view class="f-caption" style="color: #5A677E;">{{item.type_name | handleType}}</view>
							<view class="flex pt-sm">
								<i class="iconfont iconjuli1" style="color: #5A5A5D;font-size: 13px;margin-right: 2px;margin-top: 4rpx;"></i>
								<text style="color: #5A5A5D;" class="f-caption ellipsis-2">{{item.address+item.info}}</text>
							</view>
						</view>
					</view>
				</block>
			</view>
			<abnor v-if="!loading && list.data.length <= 0 && list.current_page == 1"></abnor>
			<view class="space-footer"></view>
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
	let play = null
	import siteInfo from '@/siteinfo.js';
	export default{
		data(){
			return{
				options: {},
				loading: true,
				isLoad: false,
				param: {
					page: 1,
					name: '',
					lng: 0,
					lat: 0,
					limit: 10,
					type_id: ''
				},
				list: {
					data: []
				}
			}
		},
		computed: mapState({
			pageActive: state => state.pagestore.pageActive,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			autograph: state => state.user.autograph,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			locaRefuse: state => state.user.locaRefuse,
			isGzhLogin: state => state.user.isGzhLogin,
			userCoachStatus: state => state.user.userCoachStatus,
			changeOnAddr: state => state.user.changeOnAddr,
			noChangeLoca: state => state.user.noChangeLoca,
		}),
		filters: {
			handleDistance(val){
				return val && (val/1000).toFixed(2)
			},
			handleType(val){
				return val.join('/')
			}
		},
		onLoad(options){
			this.$util.showLoading()
			this.options = options
			this.param.type_id = options.id
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.$util.showLoading()
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods:{
			...mapActions(['getConfigInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
					//this.toAppShare()
				}
				// #endif
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.getList(1)
			},
			initRefresh(){
				this.getList(1)
			},
			async getList(page) {
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
			},
			async initUtilLocaData() {
				let {
					lat = 0, lng = 0, is_util_loca = 0
				} = this.location
					
				// #ifdef APP-PLUS
				if (!lat && !lng) {
					this.list = {
						data: [],
						last_page: 1,
						current_page: 1
					}
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
					
				let newList = await this.$api.business.getList(param)
				if (param.page == 1) {
					this.list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList;
				}
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
				if (param.page == 1 && lat && lng && is_util_loca) {
					this.$util.getMapInfo()
				}
			},
			async toSearch(val){
				clearTimeout(play)
				play = setTimeout(async ()=>{
					this.$util.showLoading()
					this.param.name = val
					this.getList(1)
				},1000)
			},
			// toAppShare() {
			// 	let {
			// 		id: pid = 0
			// 	} = this.userInfo
				
			// 	let title = '门店列表'
			// 	let {
			// 		siteroot
			// 	} = siteInfo
			// 	let url = siteroot.split('/index.php')[0]
			// 	let href = `${url}/h5/#/business/pages/store/list?pid=${pid}&id=${this.options.id}`
			// 	let imageUrl = ''
			// 	this.$jweixin.wxReady(() => {
			// 		this.$jweixin.showOptionMenu()
			// 		this.$jweixin.shareAppMessage(title, '', href, imageUrl)
			// 		this.$jweixin.shareTimelineMessage(title, href, imageUrl)
			// 	})
			// },
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.$util.showLoading()
			this.param.page += 1
			await this.initUtilLocaData()
		},
	}
</script>

<style lang="scss">
	.store-box{
		.store-img{
			width: 135rpx;
			height: 135rpx;
			overflow: hidden;
		}
		.advert{
			width: 54rpx;
			height: 29rpx;
			border-radius: 0px 5rpx 5rpx 0px;
			top: 0;
			left: 0;
		}
	}
</style>