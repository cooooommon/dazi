<template>
	<view class="" v-if="isLoad">
		<!-- #ifndef H5 -->
		<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true" :title="title" color="#ffffff"
			:backgroundColor="primaryColor">
		</uni-nav-bar>
		<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
		<!-- #endif -->
		<block v-for="(pageItem,pageIndex) in configInfo.page[8]" :key="pageIndex" v-show="isStorePage">
			<block v-if="pageItem && pageItem.type == 'search'">
				<!-- #ifndef H5 -->
				<fixed :top="configInfo.navBarHeight">
				<!-- #endif -->
				<!-- #ifdef H5 -->
				<fixed>
					<!-- #endif -->
					<search @input="toSearch" type="input" :padding="30" :radius="0" backgroundColor="#fff"
						placeholder="请输入场地名称">
					</search>
				</fixed>
			</block>
			<block v-if="pageItem && pageItem.type == 'banner'">
				<view class="space-md"></view>
				<banner @changeIndex="goDiyBanner($event, pageIndex)" :list="diyBanner" :height="300" :margin="0" :autoplay="true"
					:previousMargin="0" :nextMargin="0" :indicatorActiveColor="primaryColor" :dotWidth="20"
					:dotBottom="5" :borderRadius="30" :widthRL="20">
				</banner>
			</block>
			<block v-if="pageItem && pageItem.type == 'column'">
				<view class="pt-md pl-md pr-md" v-if="storeType.length > 0">
					<column :list="storeType" :borderRadius="24" @change="goStoreType" :indicatorActiveColor="primaryColor" indicatorColor="#E5E5E5" :dotStyle="dotStyle"></column>
				</view>
			</block>
			<block v-if="pageItem && pageItem.type == 'list'">
				<view class="store-box pt-md pl-md pr-md">
					<block v-for="(item,index) in list.data" :key="index">
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url: `/business/pages/store/detail?id=${item.id}`})">
							<view class="mb-md radius-16 fill-base pd-lg flex-center">
								<view class="store-img radius-16 rel">
									<image :src="item.cover" mode="aspectFill" class="store-img radius-16"></image>
									<text class="abs advert c-base flex-center f-ms-little" v-if="item.is_top" :style="{background: primaryColor}">广告</text>
								</view>
								<view class="flex-1 pl-md">
									<view class="flex-between pb-sm">
										<text class="text-bold f-title ellipsis max-350">{{item.name}}</text>
										<text class="f-desc" :style="{color: primaryColor}">{{item.distance | handleDistance}}km</text>
									</view>
									<view class="f-caption" style="color: #5A677E;">{{item.type_name.join('/') || ''}}</view>
									<view class="flex pt-sm">
										<i class="iconfont iconjuli1" style="color: #5A5A5D;font-size: 13px;margin-right: 2px;margin-top: 6rpx;"></i>
										<text style="color: #5A5A5D;" class="f-caption ellipsis-2">{{item.address+item.info}}</text>
									</view>
								</view>
							</view>
						</auth>
					</block>
				</view>
				<abnor v-if="!loading && list.data.length <= 0 && list.current_page == 1"></abnor>
			</block>
		</block>
		<abnor v-if="!isStorePage"></abnor>
		
		<view class="space-footer"></view>
				
		<view :style="{height: `${configInfo.tabbarHeight}px`}"></view>
		<tabbar :cur="8"></tabbar>
		<!-- #ifdef APP-PLUS -->
		<open-location-info ref="open_location_info" :isAdd="false"></open-location-info>
		<login-info></login-info>
		<!-- #endif -->
		
		<!-- #ifdef MP-WEIXIN -->
		<user-privacy ref="user_privacy" :show="false"></user-privacy>
		<!-- #endif -->
		
		<auth @tap.stop.prevent :needAuth="!userInfo || (userInfo && (!userInfo.phone || !userInfo.nickName))" :must="true"
			:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toConfirmGoUrl" ref="auth_box">
		</auth>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	let play = null
	import tabbar from "@/components/tabbar.vue"
	import siteInfo from '@/siteinfo.js';
	export default{
		components: {
			tabbar
		},
		data(){
			return{
				options: {},
				loading: true,
				isLoad: false,
				dotStyle: {
					height: '8rpx',
					width: '8rpx'
				},
				banner: ['https://lbqny.migugu.com/admin/diy/default.png','https://lbqny.migugu.com/admin/diy/default.png'],
				title: ''
			}
		},
		computed: mapState({
			pageActive: state => state.pagestore.pageActive,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			autograph: state => state.user.autograph,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
			location: state => state.user.location,
			locaRefuse: state => state.user.locaRefuse,
			isGzhLogin: state => state.user.isGzhLogin,
			param: state => state.pagestore.param,
			list: state => state.pagestore.list,
			storeType: state => state.pagestore.storeType,
			userCoachStatus: state => state.user.userCoachStatus,
			changeOnAddr: state => state.user.changeOnAddr,
			noChangeLoca: state => state.user.noChangeLoca,
			diyBanner: state => state.pagestore.diyBanner,
			isStorePage: state => state.pagestore.isStorePage,
		}),
		filters: {
			handleDistance(val){
				return val && (val/1000).toFixed(1)
			}
		},
		async onLoad(options){
			// #ifndef H5
			this.$util.showLoading()
			// #endif
			if(options.pid){
				options = await this.updateCommonOptions(options)
			}
			this.options = options
			this.initIndex()
			this.updateStoreItem({
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
				this.$refs.open_location_info.pShow = false
				await this.getList(1)
				let updateArr = ['updateServiceItem', 'updateTechnicianItem']
				updateArr.map(item => {
					this[item]({
						key: 'pageActive',
						val: false
					})
				})
			}
			// #endif
		},
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
		methods:{
			...mapActions(['updateCommonOptions','getStoreList','getConfigInfo','getUserInfo', 'getStoreTypeList', 'getMineInfo']),
			...mapMutations(['updateStoreItem', 'updateUserItem','updateServiceItem','updateTechnicianItem']),
			async initIndex(refresh = false){
				let {
					pid = 0
				} = this.options
				let {
					isGzhLogin
				} = this
				
				let {
					tabBar
				} = this.configInfo
				let title = ''
				tabBar.forEach(item => {
					if(item.id == 8){
						title = item.name
					}
				})
				this.title = title || '门店'
				
				// #ifdef H5
				uni.setNavigationBarTitle({
					title: title
				})
				// #endif 
				
				if (!refresh && this.pageActive && !pid) {
					// #ifndef H5
					this.$util.setNavigationBarColor({
						bg: this.primaryColor
					})
					// #endif
					this.isLoad = true
					this.loading = false
					this.$util.hideAll()
					return
				}
				
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
					location
				} = this
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				// this.getMineInfo()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				let diyBanner = []
				let isStorePage = false
				this.configInfo.page[8].forEach(item => {
					if(item){
						isStorePage = true
					}
					if (item.type == 'banner') {
						item.data.bannerList.forEach(src => {
							let img = src.img.map(url => {
								return url.url
							})
							diyBanner.push(img)
						})
					}
				})
				this.isStorePage = isStorePage
				this.updateStoreItem({
					key: 'isStorePage',
					val: isStorePage
				})
				this.updateStoreItem({
					key: 'diyBanner',
					val: diyBanner
				})
				await Promise.all([this.getStoreTypeList(), this.getList(1)])
				
			},
			async initRefresh(page = 1){
				let param = this.$util.deepCopy(this.param)
				param.page = page
				await this.updateStoreItem({
					key: 'param',
					val: param
				})
				await this.initIndex(true)
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
						try{
							let privacyCheck = this.$refs.user_privacy.check()
							if (privacyCheck) {
								this.$refs.user_privacy.open()
								this.isLoad = true
								this.loading = false
								this.$util.hideAll()
								return
							}
						}catch(e){
							//TODO handle the exception
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
		
				await this.getStoreList(param)
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
				if (param.page == 1 && lat && lng && is_util_loca) {
					this.$util.getMapInfo()
				}
			},
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo
				let title = this.title || '门店'
				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/pages/store?pid=${pid}`
				let imageUrl = ''
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, '', href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
			},
			async toSearch(val){
				clearTimeout(play)
				play = setTimeout(async ()=>{
					this.$util.showLoading()
					let param = this.$util.deepCopy(this.param)
					param.name = val
					await this.updateStoreItem({
						key: 'param',
						val: param
					})
					this.getList(1)
				},1000)
			},
			async goStoreType(e){
				let {
					user_from_switch = 0
				} = this.configInfo
				if(!this.userInfo || (this.userInfo && !this.userInfo.phone && !this.userInfo.nickName)){
					this.$refs.auth_box.toShowAuth()
				}else if(user_from_switch && this.userInfo.from_type == 1){
					this.$refs.auth_box.toUserFrom(1)
				}else{
					this.$util.goUrl({url: `/business/pages/store/list?id=${e.id}`})
				}
			},
			// diy 轮播图/广告图跳转
			goDiyBanner(e, index) {
				// connect_type 1查看大图，2文章
				let {
					connect_type = '',
						type_id: id = 0,
						img: current = '',
						linkType = 0,
						link = [],
						title = ''
				} = this.configInfo.page[8][index].data.bannerList[e]
			
				switch (connect_type) {
					case 1:
						this.$util.previewImage({
							current,
							urls: [current]
						})
						break;
					case 2:
						this.$util.goUrl({
							url: `/user/pages/article?id=${id}`
						})
						break;
				}
				let {
					url
				} = link[0]
				// this.$util.goUrl({
				// 	url
				// })
				if (linkType == 5) {
					if(current.length > 0){
						current = current[0].url
					}
					this.$util.previewImage({
						current,
						urls: [current]
					})
					return
				}
				let {
					user_from_switch = 0
				} = this.configInfo
				if(!this.userInfo||(this.userInfo && !this.userInfo.phone && !this.userInfo.nickName)){
					this.$refs.auth_box.toShowAuth()
				}else if(user_from_switch && this.userInfo.from_type == 1){
					this.$refs.auth_box.toUserFrom(1)
				}else{
					this.toConfirmGoUrl(linkType, url)
				}
			},
			toConfirmGoUrl(linkType, url){
				let methodObj = {
					1: 'call',
					2: 'miniProgram',
					3: 'web',
					4: 'navigateTo'
				}
				if(!this.isGzhLogin){
					this.$util.toAsyncLogin()
					return
				}
				// this.$util.toCheckLogin({
				// 	url
				// })
				let openType = methodObj[linkType]
				// #ifdef H5
				if (openType === 'web') {
					window.location.href = url
					return
				}
				// #endif
				this.$util.goUrl({
					url,
					openType
				})
			}
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.$util.showLoading()
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.$util.showLoading()
			let param = this.$util.deepCopy(this.param)
			param.page += 1
			await this.updateStoreItem({
				key: 'param',
				val: param
			})
			await this.getStoreList(this.param)
			this.loading = false
			this.$util.hideAll()
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