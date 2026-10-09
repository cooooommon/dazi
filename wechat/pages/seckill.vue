<template>
	<view class="" v-if="isLoad">
		<!-- #ifndef H5 -->
		<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true" :title="title" color="#ffffff"
			:backgroundColor="primaryColor">
		</uni-nav-bar>
		<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
		<!-- #endif -->
		<view class="seck-bg"></view>
		<block v-for="(pageItem,pageIndex) in configInfo.page[9]" :key="pageIndex" v-show="isStorePage">
			<block v-if="pageIndex == 0">
				<!-- #ifndef H5 -->
				<fixed :top="configInfo.navBarHeight" :initHeight="initHeight">
				<!-- #endif -->
				<!-- #ifdef H5 -->
				<fixed :initHeight="initHeight">
				<!-- #endif -->
					<view class="seck-bg-top">
						<view class="flex-between pl-lg pr-lg pt-md pb-md seck-top">
							<image src="https://lbqny.migugu.com/admin/peiwan/seckill-text.png" mode="aspectFill" class="seckill-icon"></image>
							<view class="flex-center pl-lg" @tap.stop="toChooseLocation">
								<i class="iconfont icondizhi" :style="{color: primaryColor}"></i> <!--icondizhi5 -->
								<text class="f-paragraph text-bold ellipsis max-400" style="line-height: normal;">{{location&&location.address ? location.address :  '定位中...'}}</text>
								<i class="iconfont icon-right text-bold" style="font-size: 12px;"></i>
							</view>
						</view>
						<block v-if="pageItem && pageItem.type == 'search'">
							<view class="ml-lg mr-lg pb-md" v-if="['search'].includes(pageItem.type)">
								<search @input="toSearch" type="input" :keyword="searchName" :padding="6" :iconLeft="false"
									style="position: absolute;width: calc(100% - 60rpx);" :radius="50" backgroundColor="#fff"
									frontColor="#fff" placeholder="搜索套餐名称">
								</search>
								<view class="" style="height: 76rpx;"></view>
							</view>
						</block>
					</view>
				</fixed>
			</block>
			<block v-if="pageItem && pageItem.type == 'banner'">
				<banner @changeIndex="goDiyBanner($event, pageIndex, 'banner')" :list="diyBanner" :height="400" :margin="0" :autoplay="true"
					:previousMargin="0" :nextMargin="0" :indicatorActiveColor="primaryColor" :dotWidth="20"
					:dotBottom="5" :borderRadius="30" :widthRL="20"> 
				</banner>
				<!-- <view class="space-md"></view> -->
			</block>
			<block v-if="pageItem && pageItem.type == 'search'&&pageIndex!= 0">
				<view class="ml-lg mr-lg pb-md" v-if="['search'].includes(pageItem.type)">
					<search @input="toSearch" type="input" :keyword="searchName" :padding="6" :iconLeft="false"
						style="position: absolute;width: calc(100% - 60rpx);" :radius="50" backgroundColor="#fff"
						frontColor="#fff" placeholder="搜索套餐名称">
					</search>
					<view class="" style="height: 76rpx;"></view>
				</view>
			</block>
			<block v-if="pageItem && pageItem.type == 'column'">
				<view class="pb-md pl-lg pr-lg rel" v-if="storeType.length > 0">
					<column :colNum="5" :list="storeType" :borderRadius="24" @change="goStoreType" :indicatorActiveColor="primaryColor" indicatorColor="#E5E5E5" :dotStyle="dotStyle"></column>
				</view>
			</block>
			<block v-if="pageItem && pageItem.type == 'seckill'">
				<view class="flex-center pl-lg pr-lg rel pt-md">
					<view class="tab-item flex-center flex-1 radius-20 rel" v-for="(item,index) in tabList" :key="index"
					:style="{background: activeIndex==index?'#fff':'rgba(0,0,0,0.04)',color:activeIndex==index?primaryColor:'#78777b'}"
					:class="[{'text-bold': activeIndex==index}]" @tap="handleChange(index)"
					>
						{{item.title}}
					</view>
				</view>
				<view class="pl-lg pr-lg pt-md rel">
					<block v-for="(item,index) in list.data" :key="index">
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url: `/business/pages/package/detail?id=${item.package_id}&storeid=${item.store_id}&jump=1&is_seckill=1`})">
							<view class="s-item radius-20 fill-base flex-between">
								<view class="">
									<view class="item-cover rel">
										<view class="f-ms-little flex-center abs item-label c-base" v-if="item.is_ad">广告</view>
										<image :src="item.cover" mode="aspectFill" class="item-cover"></image>
									</view>
									<view class="mt-sm flex-between" style="width: 220rpx;">
										<view class="item-slider">
											<view class="item-slider-num" :style="{width: `${128/item.stock*item.use_stock}rpx`}"></view>
										</view>
										<text class="f-ms-little c-78777B" style="word-break: keep-all">已抢{{item.stock_rate}}</text>
									</view>
								</view>
								<view class="item-cont rel flex-1">
									<view class="f-title text-bold ellipsis-2">{{item.name}}</view>
									<!-- <auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
										:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url: `/business/pages/store/detail?id=${item.store_id}`})">
									</auth> -->
									<view class="flex-y-center pt-sm max-400" @tap.stop="goStoreUrl(item)">
										<i class="iconfont icondizhi" :style="{color: primaryColor,fontSize: '14px'}"></i>
										<text class="f-icontext c-78777B ellipsis max-200">{{item.store_name}}</text> 
										<view class="item-cont-line"></view>
										<text class="f-icontext c-78777B">{{item.distance}} km</text>
									</view>
									<view class="item-time flex-y-center f-icontext mt-sm">
										<view class="item-time-left flex-center c-base">仅剩</view>
										<view class="item-time-text text-bold c-warning flex-center" :style="{minWidth: configInfo.isIos ? '174rpx' : '160rpx'}">
											<min-countdown :targetTime="item.end_time*1000" @callback="countEnd" :type="6"></min-countdown>
										</view>
									</view>
									<view class="abs radius-16 item-btn flex-1 flex-between">
										<view class="flex-y-baseline">
											<text class="f-caption text-bold c-warning">￥</text>
											<text class="f-caption text-bold c-warning f-md-title">{{item.price}}</text>
											<text class="f-caption c-caption text-delete">￥{{item.package_price}}</text>
										</view>
										<image src="https://lbqny.migugu.com/admin/peiwan/seckill-btn-1.png" mode="aspectFill" class="item-btn-img"></image>
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
		
		<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
			:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="confirmUrl" ref="auth_box">
		</auth>
		
		<view class="space-footer"></view>
				
		<view :style="{height: `${configInfo.tabbarHeight}px`}"></view>
		<tabbar :cur="9"></tabbar>
		<!-- #ifdef APP-PLUS -->
		<open-location-info ref="open_location_info" :isAdd="false"></open-location-info>
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
				title: '',
				initHeight: 0
			}
		},
		computed: mapState({
			pageActive: state => state.seckill.pageActive,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			autograph: state => state.user.autograph,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
			location: state => state.user.location,
			locaRefuse: state => state.user.locaRefuse,
			isGzhLogin: state => state.user.isGzhLogin,
			param: state => state.seckill.param,
			list: state => state.seckill.list,
			storeType: state => state.seckill.storeType,
			userCoachStatus: state => state.user.userCoachStatus,
			changeOnAddr: state => state.user.changeOnAddr,
			noChangeLoca: state => state.user.noChangeLoca,
			diyBanner: state => state.seckill.diyBanner,
			isStorePage: state => state.seckill.isStorePage,
			tabList: state => state.seckill.tabList,
			activeIndex: state => state.seckill.activeIndex,
			changeAddr: state => state.user.changeAddr,
		}),
		filters: {
			handleDistance(val){
				return val && (val/1000).toFixed(2)
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
			this.updateSeckillItem({
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
				let updateArr = ['updateStoreItem','updateServiceItem', 'updateTechnicianItem']
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
			...mapActions(['updateCommonOptions','getSeckillList','getConfigInfo','getUserInfo', 'getSeckillTypeList']),
			...mapMutations(['updateSeckillItem','updateStoreItem', 'updateUserItem','updateServiceItem','updateTechnicianItem']),
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
					if(item.id == 9){
						title = item.name
					}
				})
				this.title = title || '秒杀'
				
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
				
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				let diyBanner = []
				let isStorePage = false
				this.configInfo.page[9].forEach(item => {
					if(item){
						isStorePage = true
					}
					if (item.type == 'banner') {
						item.data.bannerList.forEach(src => {
							let img = src.img.map(url => {
								return url.url
							})
							diyBanner.push({
								img: img[0],
								jump_type: src.linkType == 6 ? 'video' : 'image',
								jump_url: src.linkType == 6 ? src.link[0].url : ''
							})
						})
					}
				})
				// this.isStorePage = isStorePage
				this.updateSeckillItem({
					key: 'isStorePage',
					val: isStorePage
				})
				this.updateSeckillItem({
					key: 'diyBanner',
					val: diyBanner
				})
				await Promise.all([this.getSeckillTypeList(), this.getList(1, true)])
				
			},
			async initRefresh(page = 1){
				let param = this.$util.deepCopy(this.param)
				param.page = page
				await this.updateSeckillItem({
					key: 'param',
					val: param
				})
				await this.initIndex(true)
				this.initHeight = new Date().getTime()
			},
			async getList(page = 0, refresh = false) {
				if (page) {
					this.param.page = 1
					this.list.data = []
				}
				let {
					location,
					locaRefuse,
					changeAddr
				} = this
			
				let {
					status: coach_status,
					coach_position
				} = this.userCoachStatus
				
				let {
					realtime_location = 0
				} = this.configInfo
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
					this.updateSeckillItem({
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
		
				await this.getSeckillList(param)
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
				let title = this.title || '秒杀'
				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/pages/seckill?pid=${pid}`
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
					await this.updateSeckillItem({
						key: 'param',
						val: param
					})
					this.getList(1)
				},1000)
			},
			async goStoreType(e){
				this.$util.goUrl({url: `/business/pages/store/list?id=${e.id}`})
			},
			goStoreUrl(item){
				let {
					user_from_switch = 0
				} = this.configInfo
				if(!this.userInfo || (this.userInfo && !this.userInfo.phone && !this.userInfo.nickName)){ // && isAuth
					this.$refs.auth_box.toShowAuth()
				}else if(user_from_switch && this.userInfo.from_type == 1){
					this.$refs.auth_box.toUserFrom(1)
				}else{
					this.$util.goUrl({url: `/business/pages/store/detail?id=${item.store_id}`})
				}
			},
			// diy 轮播图/广告图跳转
			goDiyBanner(e, index, type) {
				// connect_type 1查看大图，2文章
				let {
					connect_type = '',
						type_id: id = 0,
						img: current = '',
						linkType = 0,
						link = [],
						title = ''
				} = type == 'imagewindow' ? e : this.configInfo.page[9][index].data.bannerList[e]
			
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
				let urlList = ['/technician/pages/join-us','/technician/pages/apply','/business/pages/settle-in','/business/pages/store/detail']
				let isAuth = false
				urlList.forEach(item => {
					if(url.indexOf(item) != -1){
						isAuth = true
					}
				})
				if(title == '商家入驻' && [2,3].includes(this.mineInfo.store_status)){
					url = `/business/pages/manage/index`
				}
				// this.isAuth = true
				let {
					userInfo
				} = this
				this.goUrlObj = {
					url, 
					linkType, 
					current: current[0].url,
					type: 2
				}
				let {
					plugAuth = {},
					user_from_switch = 0
				} = this.configInfo
				let {
					storeplus = false
				} = plugAuth
				let urlAuth = ['business/pages/store/detail','business/pages/store/list','business/pages/manage/index','business/pages/settle-in']
				let isPage = false
				urlAuth.forEach(item => {
					if(url.indexOf(item) != -1){
						isPage = true
					}
				})
				if(isPage && !storeplus) {
					return 
				}
				if(!this.userInfo || (this.userInfo && !this.userInfo.phone && !this.userInfo.nickName)){ // && isAuth
					this.$refs.auth_box.toShowAuth()
				}else if(user_from_switch && this.userInfo.from_type == 1){
					this.$refs.auth_box.toUserFrom(1)
				}else{
					this.toConfirmGoUrl(url, linkType, current[0].url)
				}
				// this.toConfirmGoUrl(url, linkType, current[0].url)
			},
			confirmUrl(){
				let {goUrlObj} = this
				let {index} = goUrlObj
				if(goUrlObj.type == 2){
					this.toConfirmGoUrl(goUrlObj.url,goUrlObj.linkType, goUrlObj.current)
				}else if(goUrlObj.type == 1){
					this.toTechnician(index)
				}
			},
			toConfirmGoUrl(url, linkType, current) {
				let {
					coach_status = -1,
						fx_status = -1,
						channel_status = -1
				} = this.mineInfo
				if(url == '/technician/pages/apply' && coach_status!=-1){
					if(!this.isGzhLogin){
						this.$util.toAsyncLogin()
						return
					}
					this.$util.toCheckLogin({
						url: `/user/pages/apply-result?type=1`
					})
					return
				}
				
				if (['/agent/pages/apply', '/technician/pages/join-us', '/technician/pages/apply?type=1', '/technician/pages/apply','/business/pages/manage/index','/business/pages/settle-in'].includes(url)) {
					if(!this.isGzhLogin){
						this.$util.toAsyncLogin()
						return
					}
					this.$util.toCheckLogin({
						url
					})
					return
				}
				if(url.indexOf('/business/pages/store/detail')!=-1){
					if(!this.isGzhLogin){
						this.$util.toAsyncLogin()
						return
					}
					this.$util.toCheckLogin({
						url
					})
					return
				}
				if (linkType == 5) {
					this.$util.previewImage({
						current,
						urls: [current]
					})
					return
				}
				let methodObj = {
					1: 'call',
					2: 'miniProgram',
					3: 'web',
					4: 'navigateTo'
				}
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
			},
			toTechnician(index) {
				let {
					id,
					city_id,
					coach_name
				} = this.recommend_list.data[index]
				this.updateTechnicianItem({
					key: 'pageActive',
					val: false
				})
				// this.$util.goUrl({
				// 	url: `/pages/technician?coach_id=${id}&coach_name=${coach_name}&city_id=${city_id}`,
				// 	openType: `reLaunch`
				// })
				this.$util.toCheckLogin({
					url: `/user/pages/technician-info?id=${id}`
				})
			
			},
			handleChange(index){
				this.updateSeckillItem({
					key: 'activeIndex',
					val: index
				})
				this.$util.showLoading()
				this.getList(1)
			},
			// 选择地区
			async toChooseLocation() {
				// #ifdef MP-WEIXIN
				let privacyCheck = this.$refs.user_privacy && this.$refs.user_privacy.check()
				if (privacyCheck) {
					this.$refs.user_privacy.open()
					return
				}
				// #endif  
				let location = await this.$util.chooseLocation(1)
				let {
					lat,
					lng
				} = location
				if (!lat) return
				this.updateUserItem({
					key: 'location',
					val: location
				})
				this.updateUserItem({
					key: 'changeAddr',
					val: true
				})
				await this.getList(1)
			},
			countEnd() {
				this.$util.log("倒计时完了")
				setTimeout(() => {
					this.initRefresh()
				}, 1000)
			},
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.$util.showLoading()
			this.updateUserItem({
				key: 'changeAddr',
				val: false
			})
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.$util.showLoading()
			let param = this.$util.deepCopy(this.param)
			param.page += 1
			await this.updateSeckillItem({
				key: 'param',
				val: param
			})
			await this.getSeckillList(this.param)
			this.loading = false
			this.$util.hideAll()
		},
	}
</script>

<style lang="scss">
	.seckill-icon{
		width: 185rpx;
		height: 52rpx;
	}
	.seck-bg{
		position: fixed;
		width: 750rpx;
		height: 586rpx;
		background: #f5f5f5 linear-gradient(20deg, rgba(157, 104, 255, 0) 50%, rgba(157, 104, 255, 0.6) 100%);
		border-radius: 0px 0px 0px 0px;
	}
	.seck-bg-top{
		background: #f5f5f5 linear-gradient(20deg, rgba(157, 104, 255, 0) 10%, rgba(157, 104, 255, 0.6) 100%);
	}
	.seck-top{
		background: #f5f5f5 linear-gradient(20deg, rgba(157, 104, 255, 0.1) 10%, rgba(157, 104, 255, 0.6) 100%);
	}
	.tab-item{
		height: 72rpx;
		margin-left: 16rpx;
	}
	.tab-item:nth-child(1){
		margin-left: 0;
	}
	.s-item{
		margin-bottom: 16rpx;
		padding: 24rpx;
		.item-cover{
			width: 220rpx;
			height: 220rpx;
			border-radius: 20rpx;
			margin-right: 24rpx;
		}
		.item-label{
			width: 72rpx;
			height: 36rpx;
			border-radius: 20rpx 0 20rpx 0;
			left: 0;
			top: 0;
			background-color: #FF8B00;
			z-index: 9;
		}
		.item-slider{
			width: 128rpx;
			height: 16rpx;
			background: #FFF2ED;
			border-radius: 16rpx;
			.item-slider-num{
				height: 16rpx;
				border-radius: 16rpx;
				background: linear-gradient( 90deg, #FF8559 0%, #FF3317 100%);
			}
		}
		.item-cont{
			height: 260rpx;
			.item-cont-line{
				height: 24rpx;
				width: 1px;
				border-left: 1px solid #B1B0B2;
				margin: 0 16rpx;
			}
			.item-time{
				height: 34rpx;
				border-radius: 8rpx;
				display: inline-flex;
			}
			.item-time-left{
				width: 68rpx;
				height: 34rpx;
				border-radius: 8rpx 0 0 8rpx;
				background: #FF3317;
			}
			.item-time-text{
				background: #FFF2ED;
				border-radius: 0 8rpx 8rpx 0;
				height: 34rpx;
			}
			.item-btn{
				height: 64rpx;
				background-color: #FFF2ED;
				bottom: 0;
				width: 100%;
				padding: 0 0 0 16rpx;
			}
			.item-btn-img{
				width: 106rpx;
				height: 64rpx;
			}
		}
		.c-78777B{
			color: #78777B;
		}
	}
</style>