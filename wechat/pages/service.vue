<template>
	<view class="pages-home">
		<block v-if="isLoad">
			<image mode="aspectFill" lazy-load class="service-page-bg abs"
				src="https://lbqny.migugu.com/admin/playwith/mine/service-nav-bg.png"></image>
			<!-- #ifdef MP-WEIXIN -->
			<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
			<!-- #endif -->
			<!-- #ifdef H5 -->
			<view style="height:20rpx"></view>
			<!-- #endif -->
			<!-- #ifdef APP-PLUS -->
			<view style="height:80rpx"></view>
			<!-- #endif -->
		
			<!-- <view @tap.stop="$util.goUrl({url: `/pages/technician`,openType: `reLaunch`})"
				class="service-search flex-between fill-base ml-lg radius rel">
				<view class="flex-y-center">
					<i class="iconfont iconsousuo c-title mr-md"></i>
					<view class="f-desc">{{configInfo.index_type == 1 ? `搜索服务名称` : `搜索向导昵称`}} </view>
				</view>
				<view class="btn flex-center f-desc c-base radius" :style="{background:primaryColor}">搜索</view>
			</view> -->
			<block v-for="(pageItem,pageIndex) in configInfo.page[1]" :key="pageIndex">
				<view class="ml-lg mr-lg pt-lg" v-if="['search'].includes(pageItem.type)">
					<search @input="toSearch" type="input" :keyword="searchName" :padding="6" :iconLeft="false"
						style="position: absolute;width: calc(100% - 60rpx);" :radius="50" backgroundColor="#fff"
						frontColor="#fff" :placeholder="configInfo.index_type == 1 ? `搜索服务名称` : `搜索${$t('action.attendantName')}昵称`">
					</search>
					<view class="" style="height: 76rpx;"></view>
				</view>
		
				<view class="mt-lg rel" v-if="banner.length > 0 && ['service-banner'].includes(pageItem.type)">
					<banner @change="goBanner" :list="banner" :height="386" :margin="0" :autoplay="true"
						:previousMargin="0" :nextMargin="0" :indicatorActiveColor="primaryColor" :dotWidth="20"
						:dotBottom="5" :borderRadius="30" :widthRL="30">
					</banner>
				</view>
				<view class="mt-lg rel" v-if="['banner'].includes(pageItem.type)">
					<banner @changeIndex="goDiyBanner($event, pageIndex, 'banner')" :list="diyBanner" :height="386" :margin="0"
						:autoplay="true" :previousMargin="0" :nextMargin="0" :indicatorActiveColor="primaryColor"
						:dotWidth="20" :dotBottom="5" :borderRadius="30" :widthRL="30">
					</banner>
				</view>
				<view class="fill-base mt-md ml-lg mr-lg pt-md pl-md pr-md pb-md radius-16 rel"
					v-if="recommend_list && recommend_list.data.length > 0 && pageItem.type == 'column'">
					<view @tap.stop="$util.goUrl({url: `/pages/technician`,openType: `reLaunch`})"
						class="flex-between pb-lg">
						<view class="f-paragraph c-black text-bold">{{pageItem.data.title || `推荐${$t('action.attendantName')}`}}</view>
						<view class="flex-y-center f-caption c-caption">查看更多<i class="iconfont icon-right"
								style="font-size: 24rpx;"></i></view>
					</view>
					<scroll-view scroll-x class="recommend-technician rel">
						<block v-for="(item,index) in recommend_list.data" :key="index">
							<view class="recommend-item type-1 pd-md" @tap="showTechnician(index, 1)">
								<view class="flex-center pb-sm">
									<!-- #ifdef H5 -->
									<view class="cover radius">
										<view class="h5-image cover radius"
											:style="{ backgroundImage : `url('${item.work_img}')`}">
										</view>
									</view>
									<!-- #endif -->
									<!-- #ifndef H5 -->
									<image mode="aspectFill" lazy-load class="cover radius" :src="item.work_img">
									</image>
									<!-- #endif -->
									<view class="flex-1 ml-sm">
										<view class="f-desc ellipsis">{{item.coach_name}}</view>
										<view class="flex-y-baseline" style="margin-top: 4rpx;">
											<i class="iconfont iconyduixingxingshixin icon-font-color iconpingfen1"></i>
											<view class="star-text flex-y-center f-caption">{{item.star}}</view>
										</view>
									</view>
								</view>
								<view class="flex-center">
									<view class="new-technician flex-center f-icontext"
										:style="{color:primaryColor,border:`1rpx solid ${primaryColor}`}"
										v-if="item.is_new">新人
									</view>
									<view class="f-icontext c-caption" v-else>30天接单{{item.order_count||0}}
									</view>
								</view>
							</view>
						</block>
					</scroll-view>
				</view>
				<view class="pt-lg pl-lg pr-lg flex-between module" v-if="pageItem.type == 'settle'">
					<block v-if="configInfo.plugAuth.storeplus">
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toApply(1)" style="width:354rpx">
							<view class="rel" style="width:354rpx">
								<image class="wizard" :src="`https://lbqny.migugu.com/admin/playwith/service/01.png`"
									mode="aspectFill"></image>
								<text class="c-base f-sm-title abs wizard-title">{{$t('action.attendantName')}}入驻</text>
							</view>
						</auth>
						<view class="">
							<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
								:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="businessUrl">
								<view class="rel">
									<image class="business" src="https://lbqny.migugu.com/admin/playwith/service/02.png"
										mode="aspectFill"></image>
									<text class="c-base f-sm-title abs business-title">商家入驻</text>
								</view>
							</auth>
							
							<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width:356rpx;"
								:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({url: `/technician/pages/join-us`})">
								<view class="mt-md rel">
									<image class="join" src="https://lbqny.migugu.com/admin/playwith/service/03.png"
										mode="aspectFill"></image>
									<text class="c-base f-sm-title abs join-title">合作加盟</text>
								</view>
							</auth>
						</view>
					</block>
					<block v-else>
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toApply(1)" style="width:331rpx">
							<view class="rel mr-md" style="width:331rpx">
								<image class="_wizard" :src="`https://lbqny.migugu.com/admin/playwith/service/04.png`"
									mode="aspectFill"></image>
								<text class="c-base f-sm-title abs wizard-title">{{$t('action.attendantName')}}入驻</text>
							</view>
						</auth>
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width:356rpx;"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({url: `/technician/pages/join-us`})">
							<view class="rel">
								<image class="_join" src="https://lbqny.migugu.com/admin/playwith/service/03.png"
									mode="aspectFill"></image>
								<text class="c-base f-sm-title abs join-title">合作加盟</text>
							</view>
						</auth>
					</block>
				</view>
				<view class="pt-lg pl-lg pr-lg rel " v-if="pageItem.type == 'newfangled'">
					<view class="flex-between pb-md">
						<view class="f-title c-black text-bold" style="z-index: 1;">{{pageItem.data.title || `新奇玩法`}}</view>
					</view>
					<block v-for="(item, index) in list.data" :key="index">
						<view class="list-item flex-center pd-md fill-base rel radius-20 mb-md"
							 @tap.stop="goDetail(index)">
							<!-- #ifdef H5 -->
							<view class="cover radius-16">
								<view class="h5-image cover radius-16" :style="{ backgroundImage : `url('${item.cover}')`}">
								</view>
							</view>
							<!-- #endif -->
							<!-- #ifndef H5 -->
							<image mode="aspectFill" lazy-load class="cover radius-16" :src="item.cover"></image>
							<!-- #endif -->
						
							<view class="flex-between flex-1 rel">
								<view class="flex-1 pl-md">
									<view class="flex-1 flex" style="justify-content: space-between;align-items:flex-start">
										<view class="f-mini-title max-330 ellipsis">{{item.title}}</view>
										<view class="flex-y-center">
											<i class="iconfont iconwode2 icon-font-color" style="font-size: 22rpx;"></i>
											<text class="f-icontext c-desc">{{item.total_sale}}人选择</text>
										</view>
									</view>
									<view class="pt-sm max-330 ellipsis f-caption c-caption">{{item.sub_title}}</view>
									<view class="flex-y-center" style="margin-top: 16rpx;">
										<!-- <image src="/static/images/time.png" mode="aspectFill" style="width: 30rpx;height: 30rpx;"></image> -->
										<i class="iconfont iconshijian2" :style="{color:primaryColor}"></i>
										<view class="flex-center" style="padding-left: 4rpx;">
											<text class="f-desc text-bold">{{item.time_long}}</text>
											<text class="f-icontext">分钟</text>
										</view>
									</view>
									<view class="flex-y-center" style="margin-top: 14rpx;">
										<view class="flex-center">
											<text style="color: #FF2404;" class="f-caption">¥</text>
											<view class="flex-y-baseline">
												<text style="color: #FF2404;"
													class="f-sm-title text-bold">{{item.price}}</text>
												<text style="color: #FF2404;" class="f-icontext">起</text>
											</view>
										</view>
										<view class="flex-center mem-price" v-if="item.member_price">
											<view class="rel mem-price-bg flex-center">
												<view class="abs f-ms-little c-base" style="padding-right: 2px;z-index: 2;">会员价</view>
												<image class="mem-price-img" src="https://lbqny.migugu.com/admin/member/member-bg.png" mode="aspectFill"></image>
											</view>
											<view class="mem-price-text text-bold f-ms-little">¥{{item.member_price}}</view>
										</view>
										<!-- <text class="f-icontext c-caption pl-sm" style="text-decoration: line-through">￥{{item.init_price}}</text> -->
									</view>
								</view>
								<view class="abs" style="right: 0rpx;top: 110rpx;" @tap.stop.prevent>
									<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
										:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toChoose(index)" class="abs" style="width: 120rpx;right: 0; ">
										<view class="f-desc c-base item-btn flex-center" 
											:style="{backgroundColor:primaryColor }">下单</view>
									</auth>
									<!-- <view class="f-desc c-base item-btn flex-center" @tap="toChoose(index)"
										:style="{backgroundColor:primaryColor }">下单</view> -->
								</view>
							</view>
						</view>
					</block>
					<abnor v-if="!loading && list.data.length <= 0 && list.current_page == 1"></abnor>
				</view>
				<view class="pl-lg pr-lg pt-lg" v-if="pageItem.type == 'star'">
					<view class="flex-between pb-md">
						<view class="f-title c-black text-bold" style="z-index: 1;">{{pageItem.data.title || `明星${$t('action.attendantName')}`}}</view>
					</view>
					<view class="flex-warp" v-if="recommend_list && recommend_list.data.length > 0">
						<view class="mb-md" :style="{marginRight: index%2 == 0 ? '20rpx':''}"
							v-for="(item,index) in recommend_list.data" :key="index">
							<technician-list-item @workImg="toPreviewImage(index,1)" @selfImg="toPreviewImage(index)"
								@comment="toShowPopup(index,'message')" @collect="toCollect(index)"
								@info="goInfo(index)" @order="toShowPopup(index,'technician', item.is_work )"
								:info="item">
							</technician-list-item>
						</view>
						<!-- <abnor v-if="recommend_list.data.length == 0"></abnor> -->
					</view>
					<abnor v-if="recommend_list && recommend_list.data.length <= 0 && recommend_list.current_page == 1 &&location.lng">
					</abnor>
				</view>
				<!-- 广告图 -->
				<imageWindow v-if="pageItem.type=='imagewindow'" :list="pageItem.data.imagewindowList"
					:colType="pageItem.data.imagewindowName" :wingBlank="pageItem.data.style.wingBlank"
					:whiteSpace="pageItem.data.style.whiteSpace" :layout="2" :isDiyPage="true" @change="goDiyBanner($event, pageIndex, 'imagewindow')"></imageWindow>
			</block>
		
			<load-more :noMore="list.current_page >= list.last_page && list.data.length > 0 &&location.lng" :loading="loading"
				v-if="loading">
			</load-more>
		
			<!-- <block v-if="!loading && !location.lng && (configInfo.realtime_location || configInfo.plugAuth.recommend)"> -->
				<!-- #ifdef H5 -->
				<!-- <abnor type="NOT_LOCATION" title="暂无服务数据"></abnor> -->
				<!-- #endif -->
				<!-- #ifndef H5 -->
				<!-- <abnor type="NOT_LOCATION" @confirm="toOpenLocation" title="暂无服务数据"
					:button="[{ text: '开启定位' , type: 'confirm' }]" btnSize=""></abnor> -->
				<!-- #endif -->
			<!-- </block> -->
		
			<view class="space-footer"></view>
		
		
			<uni-popup ref="coupon_item" type="center" :maskClick="false">
				<view class="coupon-popup f_r_c_c">
					<!-- #ifdef H5 -->
					<view class="h5-image bg-img"
						:style="{ backgroundImage : `url('https://lbqnyv2.migugu.com/bianzu3.png')`}">
					</view>
					<!-- #endif -->
					<!-- #ifndef H5 -->
					<image mode="aspectFill" lazy-load class="bg-img" src="https://lbqnyv2.migugu.com/bianzu3.png">
					</image>
					<!-- #endif -->
		
					<i @tap.stop="$refs.coupon_item.close()" class="iconfont icon-close c-base"></i>
					</image>
					<view class="coupon-info f_c_m_c">
						<view class="tops f_c_m_c">
							<view class="">
								成功领取
							</view>
							<view class="">
								卡券将放入“我的-我的卡券”
							</view>
						</view>
						<view class="lists f_r_c_c">
							<scroll-view scroll-y style="width: 420rpx;height:100%;">
								<view class="list f_r_sb_c" v-for="(item, index) in couponList" :key="index">
									<image src="https://lbqny.migugu.com/admin/anmo/coupon/coupon.png"
										mode="aspectFill">
									</image>
									<view class="f_r_sb_c">
										<view class="f_c_m_c">
											<view class="price">
												{{item.discount}}
											</view>
											<view class="price_text">
												{{item.full*1>0?`满${item.full}可用`:`立减`}}
											</view>
										</view>
										<view class="title f_r_m_c">
											<view class="ellipsis-3">
												{{item.title}}
											</view>
										</view>
									</view>
								</view>
							</scroll-view>
						</view>
					</view>
					<view class="btns f_r_c_c" @tap.stop="userGetCoupon">
						<view class="f_r_c_c">
							领取到卡包
						</view>
					</view>
				</view>
			</uni-popup>
			<view :style="{height: `${configInfo.tabbarHeight}px`}"></view>
			<tabbar :cur="1"></tabbar>
		
			<!-- #ifdef APP-PLUS -->
			<open-location-info ref="open_location_info" :pageActive="pageActive" :home="true"></open-location-info>
			<login-info></login-info>
			<!-- #endif -->
			
			<!-- <change-user-type></change-user-type> -->
			<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
				:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="confirmUrl" ref="auth_box">
			</auth>
		</block>
		<!-- #ifdef MP-WEIXIN -->
		<user-privacy ref="user_privacy" :show="false"></user-privacy>
		<!-- #endif -->
		
		<!-- #ifdef H5 -->
		<open-mini-program ref="open_mini_program" :info="openMiniForm"></open-mini-program>
		<!-- #endif -->
	</view>
</template>

<script>
	let play = null
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import siteInfo from '@/siteinfo.js';
	import tabbar from "@/components/tabbar.vue"
	import technicianListItem from "@/components/technician-list-item.vue"
	import openMiniProgram from "@/components/open-mini-program.vue"
	import imageWindow from '@/components/image-window/image-window.vue'
	export default {
		components: {
			tabbar,
			technicianListItem,
			openMiniProgram,
			imageWindow
		},
		data() {
			return {
				couponList: [], //优惠券
				isLoad: false,
				options: {},
				loading: true,
				lockTap: false,
				searchName: '',
				goUrlObj: {},
				navTitle: ''
				//banner: ['https://lbqny.migugu.com/admin/playwith/service/banner.png']
			}
		},
		computed: mapState({
			pageActive: state => state.service.pageActive,
			activeIndex: state => state.service.activeIndex,
			tabList: state => state.service.tabList,
			param: state => state.service.param,
			indexParam: state => state.service.indexParam,
			list: state => state.service.list,
			banner: state => state.service.banner,
			recommend_list: state => state.service.recommend_list,
			recommend_style: state => state.service.recommend_style,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			autograph: state => state.user.autograph,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			isGzhLogin: state => state.user.isGzhLogin,
			haveShieldOper: state => state.user.haveShieldOper,
			mineInfo: state => state.user.mineInfo,
			diyBanner: state => state.service.diyBanner,
			locaRefuse: state => state.user.locaRefuse,
			noChangeLoca: state => state.user.noChangeLoca,
			changeOnAddr: state => state.user.changeOnAddr,
			userCoachStatus: state => state.user.userCoachStatus,
			changeAddr: state => state.user.changeAddr,
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
				console.log('===========> changeOnAddr')
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
					console.log(noloca && ((!lat && !lng) || !unix || (unix && cur_unix - unix >= 1)) , '============> noChangeLoca')
					let cur_unix = this.$util.DateToUnix(this.$util.formatTime(new Date(), 'YY-M-D h:m:s'))
					if (noloca && ((!lat && !lng) || !unix || (unix && cur_unix - unix >= 1))) {
						this.toResetChangeLoca()
					}
				}, 500)
			}
		},
		async onLoad(options) {
			// await this.getConfigInfo()
			options = await this.updateCommonOptions(options)
			this.options = options
			// uni.setNavigationBarColor({
			// 	frontColor: '#000000',
			// 	backgroundColor: '#000000'
			// })
			let {
				pageActive,
				changeAddr
			} = this
			let {
				realtime_location = 0
			} = this.configInfo
			if (!pageActive) {
				this.$util.showLoading()
			}
			uni.onNetworkStatusChange((res) => {
				let {
					isConnected
				} = res
				if (isConnected && (!pageActive || (pageActive && (realtime_location || changeAddr)))) {
					this.initIndex()
					return
				}
			})
			await this.initIndex()
		},
		async onShow() {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.toAppShare()
			}
			// let {
			// 		tabBar
			// } = this.configInfo
			// let ind = tabBar.findIndex(item => {
			// 	return item.id == 1
			// })
			// let navTitle = tabBar[ind].name
			// let {
			// 	app_text
			// } = this.configInfo
			// uni.setNavigationBarTitle({
			// 	title: app_text || navTitle
			// })
			// this.navTitle = app_text || navTitle
			// #endif 
			if (this.haveShieldOper == 2) {
				this.initIndex()
				this.updateUserItem({
					key: 'haveShieldOper',
					val: 0
				})
			}
			if (this.pageActive && this.userInfo.id) {
				this.getCouponList()
			}
			
			// #ifdef APP-PLUS
			let {
				lat: locaLat = 0
			} = this.location
			console.log(locaLat ,this.pageActive ,this.locaRefuse,'=======> locaLat')
			if (!locaLat && this.pageActive && !this.locaRefuse) {
				console.log(locaLat ,'=======> locaLat')
				let {
					lng = 0,
						lat = 0
				} = await this.$util.getUtilLocation()
				if (!lat && !lng) return
				this.$refs.open_location_info.pShow = false
				this.getList(1)
				this.$util.getMapInfo()
				let updateArr = ['updateTechnicianItem','updateStoreItem']
				updateArr.map(item => {
					this[item]({
						key: 'pageActive',
						val: false
					})
				})
			}
			// #endif 
		},
		async onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			// this.updateUserItem({
			// 	key: 'changeAddr',
			// 	val: false
			// })
			let param = this.$util.deepCopy(this.param)
			param.name = ''
			await this.updateServiceItem({
				key: 'param',
				val: param
			})
			this.searchName = ''
			let indexParam = this.$util.deepCopy(this.indexParam)
			indexParam.page = 1
			this.updateServiceItem({
				key: 'indexParam',
				val: indexParam
			})
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		async onReachBottom() {
			if (this.configInfo.index_type == 1) {
				if (this.list.current_page >= this.list.last_page || this.loading) return;
				this.loading = true;
				this.getList(this.param.page + 1);
			} else {
				if (this.recommend_list.current_page >= this.recommend_list.last_page) return;
				this.$util.showLoading()
				let indexParam = this.$util.deepCopy(this.indexParam)
				indexParam.page = indexParam.page + 1
				this.updateServiceItem({
					key: 'indexParam',
					val: indexParam
				})
				let {
					lng = 0,
						lat = 0
				} = this.location
				await this.getServiceIndex({
					lat,
					lng,
					name: this.searchName,
					page: indexParam.page,
					limit: indexParam.limit
				})

				this.$util.hideAll()
			}
		},
		onShareAppMessage(e) {
			let {
				id: pid = 0
			} = this.userInfo
			let path = `/pages/service?pid=${pid}`
			this.$util.log(path)
			return {
				title: '',
				imageUrl: '',
				path,
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'updateCommonOptions', 'getServiceIndex', 'getServiceList',
				'getMineInfo'
			]),
			...mapMutations(['updateServiceItem', 'updateTechnicianItem', 'updateUserItem','updateStoreItem']),
			async initIndex(refresh = false) {
				// // #ifdef H5
				// if (!refresh && this.$jweixin.isWechat()) {
				// 	await this.$jweixin.initJssdk();
				// 	this.toAppShare()
				// }
				// // #endif
				
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				// #ifdef H5
				if(!refresh){
					let {
							tabBar
					} = this.configInfo
					let ind = tabBar.findIndex(item => {
						return item.id == 1
					})
					let navTitle = tabBar[ind].name
					let {
						app_text
					} = this.configInfo
					uni.setNavigationBarTitle({
						title: app_text || navTitle
					})
					this.navTitle = app_text || navTitle
				}
				// #endif 
				
				let {
					pid = 0,
						channel_id = 0
				} = this.options
				let {
					realtime_location: rloca = 0
				} = this.configInfo
				if (!refresh && this.pageActive && (!rloca && !this.changeAddr) && (!pid || !channel_id)) {
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
				if ((pid || channel_id) && !uid) {
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
				// this.getMineInfo()
				// if (!this.configInfo.id || refresh) {
				// 	await this.getConfigInfo()
				// }
				let diyBanner = []
				this.configInfo.page[1].forEach(item => {
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
				this.updateServiceItem({
					key: 'diyBanner',
					val: diyBanner
				})
				
				
				this.isLoad = true
				let {
					location,
					locaRefuse,
					changeAddr
				} = this
				let {
					plugAuth = {},
					realtime_location = 0
				} = this.configInfo
				let {
					recommend = false
				} = plugAuth
				
				let {
					status: coach_status,
					coach_position
				} = this.userCoachStatus
				
				// #ifdef APP-PLUS
				if (!locaRefuse && ((realtime_location && !changeAddr) || (!realtime_location && recommend && !location
						.lat))) {
					// #endif
					// #ifndef APP-PLUS
					if ((realtime_location && !changeAddr) || (!realtime_location && recommend && !location.lat)) {
						// #endif
						// #ifdef MP-WEIXIN
						let privacyCheck = this.$refs.user_privacy.check()
						if (privacyCheck) {
							this.$refs.user_privacy.open()
							this.loading = false
							this.$util.hideAll()
							return
						}
						// #endif
						if (coach_status == 2 && coach_position && this.param.page == 1) {
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
					if (coach_status == 2 && coach_position && this.noChangeLoca.noloca) return
					this.initUtilLocaData()

				// if (recommend && !location.lat && !realtime_location) {
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
				// 	}
				// 	// #endif
				// 	// #ifndef H5
				// 	location = await this.$util.getBmapLocation()
				// 	// #endif
				// 	this.updateUserItem({
				// 		key: 'location',
				// 		val: location
				// 	})
				// }
				// let {
				// 	lng = 0,
				// 		lat = 0
				// } = location

				// await this.getServiceIndex({
				// 	lat,
				// 	lng,
				// 	page: this.indexParam.page,
				// 	limit: this.indexParam.limit
				// })
				// this.updateServiceItem({
				// 	key: 'pageActive',
				// 	val: true
				// })
				// this.isLoad = true
				// if (this.userInfo.id) {
				// 	await Promise.all([this.getList(1), this.getCouponList()])
				// } else {
				// 	await this.getList(1)
				// }
				let {
					lng = 0,
						lat = 0
				} = this.location
				await this.getServiceIndex({
					lng,
					lat,
					page: this.indexParam.page,
					limit: this.indexParam.limit
				})
			},
			initRefresh() {
				this.initIndex(true)
			},
			async toOpenLocation() {
				await this.$util.checkAuth({
					type: 'userLocation',
					checkApp: true
				})
				// #ifdef MP-WEIXIN 
				this.initIndex()
				// #endif
			},
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo
				let title = this.navTitle || '首页'
				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/pages/service?pid=${pid}`
				let imageUrl = ''
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, '', href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
			},
			businessUrl(){
				if([2,3].includes(this.mineInfo.store_status)){
					this.$util.toCheckLogin({url: `/business/pages/manage/index`})
				}else{
					this.$util.toCheckLogin({url: `/business/pages/settle-in`})
				}
			},
			// 轮播图/广告图跳转
			goBanner(e) {
				// connect_type 1查看大图，2文章
				let {
					connect_type,
					type_id: id = 0,
					img: current
				} = e
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
			},
			// diy 轮播图/广告图跳转
			goDiyBanner(e, index, type) {
				console.log(e, index)
				// connect_type 1查看大图，2文章
				let {
					connect_type = '',
						type_id: id = 0,
						img: current = '',
						linkType = 0,
						link = [],
						title = ''
				} = type == 'imagewindow' ? e : this.configInfo.page[1][index].data.bannerList[e]

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
				console.log(this.userInfo, user_from_switch ,'===========> showTechnician')
				if(!this.userInfo || (this.userInfo && !this.userInfo.phone && !this.userInfo.nickName)){
					this.$refs.auth_box.toShowAuth()
				} else if(user_from_switch && this.userInfo.from_type == 1){
					this.$refs.auth_box.toUserFrom(1)
				} else{
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
				let {
					userInfo
				} = this
				console.log(this.isGzhLogin, '=========> toConfirmGoUrl')
				if(url == '/technician/pages/apply' && coach_status!=-1){
					if(!userInfo || (userInfo && !userInfo.phone && !userInfo.nickName)){
						this.$util.toAsyncLogin()
						return
					}
					this.$util.toCheckLogin({
						url: `/user/pages/apply-result?type=1`
					})
					return
				}
				
				if (['/agent/pages/apply', '/technician/pages/join-us', '/technician/pages/apply?type=1', '/technician/pages/apply','/business/pages/manage/index','/business/pages/settle-in'].includes(url)) {
					if(!userInfo || (userInfo && !userInfo.phone && !userInfo.nickName)){
						this.$util.toAsyncLogin()
						return
					}
					this.$util.toCheckLogin({
						url
					})
					return
				}
				if(url.indexOf('/business/pages/store/detail')!=-1){
					if(!userInfo || (userInfo && !userInfo.phone && !userInfo.nickName)){
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
			async userGetCoupon() {
				let ids = []
				this.couponList.forEach(v => {
					ids.push(v.id)
				})
				let res = await this.$api.service.userGetCoupon({
					coupon_id: ids
				})
				this.$util.showToast({
					title: `领取成功`
				})
				setTimeout(() => {
					this.$util.goUrl({
						url: '/user/pages/coupon/list'
					})
				}, 1000)
				this.$refs.coupon_item.close()
				this.loading = false
				this.$util.hideAll()
			},
			async getCouponList() {
				let list = await this.$api.service.couponList()
				this.couponList = list
				if (list.length > 0 && this.isLoad) {
					this.$refs.coupon_item.open()
				}
				this.loading = false
				this.$util.hideAll()
			},
			async getList(page = 0) {
				if (page) {
					let param = this.$util.deepCopy(this.param)
					param.page = page
					this.updateServiceItem({
						key: 'param',
						val: param
					})
				}
				let {
					list: oldList,
					param,
					tabList,
					activeIndex
				} = this
				let {
					sort,
					sign
				} = tabList[activeIndex]
				let desc = activeIndex == 0 || sign == 1 ? '' : 'desc'
				param.sort = `${sort} ${desc}`
				await this.getServiceList(param)
				this.loading = false
				this.$util.hideAll()
			},
			handerTabChange(index) {
				this.updateServiceItem({
					key: 'activeIndex',
					val: index
				})
				let tabList = this.$util.deepCopy(this.tabList)
				let {
					is_sign,
					sign,
				} = tabList[index];
				if (is_sign) {
					tabList[index].sign = sign == 0 ? 1 : 0;
				}
				this.updateServiceItem({
					key: 'tabList',
					val: tabList
				})
				this.$util.showLoading()
				uni.pageScrollTo({
					scrollTop: 0
				})
				this.getList(1)
			},
			// 详情
			goDetail(index) {
				let {
					id
				} = this.list.data[index]
				let url = `/user/pages/detail?id=${id}`
				this.$util.goUrl({
					url
				})
			},
			// 选择向导
			toChoose(index) {
				let {
					id
				} = this.list.data[index]
				let url = `/user/pages/choose-technician?id=${id}`
				this.$util.toCheckLogin({
					url
				})
			},
			showTechnician(index){
				this.goUrlObj.type = 1
				this.goUrlObj.index = index
				
				let {
					user_from_switch = 0
				} = this.configInfo
				console.log(this.userInfo, user_from_switch ,'===========> showTechnician')
				if(!this.userInfo || (this.userInfo && !this.userInfo.phone && !this.userInfo.nickName)){
					this.$refs.auth_box.toShowAuth()
				}else if(user_from_switch && this.userInfo.from_type == 1){
					this.$refs.auth_box.toUserFrom(1)
				}else{
					this.toTechnician(index)
				}
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
			toPreviewImage(index, key = 0) {
				let {
					self_img: urls,
					work_img
				} = this.recommend_list.data[index]
				if (key) {
					urls = [work_img]
				}
				this.$util.previewImage({
					current: urls[0],
					urls
				})
			},
			// 向导详情
			goInfo(index) {
				let {
					id,
					store = {}
				} = this.recommend_list.data[index]
				let {
					plugAuth = {}
				} = this.configInfo
				this.$util.toCheckLogin({
					url: plugAuth.store && store && store.id ? `/shopstore/pages/detail?id=${store.id}` :
						`/user/pages/technician-info?id=${id}`
				})
			},
			toSearch(e) {
				console.log(e, this.searchName)
				clearTimeout(play)
				this.searchName = e
				play = setTimeout(async () => {
					if (this.configInfo.index_type == 2) { // 明星向导
						this.$util.showLoading()
						let {
							lng = 0,
								lat = 0
						} = this.location
						await this.getServiceIndex({
							lat,
							lng,
							name: e,
							page: this.indexParam.page,
							limit: this.indexParam.limit
						})

						this.$util.hideAll()
					} else {
						this.$util.showLoading()
						let param = this.$util.deepCopy(this.param)
						param.name = e
						this.updateServiceItem({
							key: 'param',
							val: param
						})
						this.getList(1);
					}
				}, 1000)
			},
			// 申请向导/分销商/渠道商
			async toApply(type) {
				let {
					coach_status = -1,
						fx_status = -1,
						channel_status = -1
				} = this.mineInfo
				let status = type == 1 ? coach_status : type == 2 ? fx_status :
					channel_status
				let page = {
					1: `/technician/pages/apply`,
					2: `/user/pages/distribution/apply`,
					3: `/user/pages/channel/apply`
				}
				// -1未申请，1审核中，2审核通过，3取消授权，4审核失败
				let url = status == -1 ? page[type] :
					`/user/pages/apply-result?type=${type}`
				this.$util.log(url)
				this.$util.toCheckLogin({
					url
				})
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
					lng = 0,
						lat = 0
				} = this.location
				this.updateServiceItem({
					key: 'pageActive',
					val: true
				})
				//this.$util.hideAll()
			
				let {
					plugAuth = {},
						realtime_location = 0
				} = this.configInfo
				let {
					recommend = false
				} = plugAuth
			
				// #ifndef H5
				// #ifdef APP-PLUS
				if (!lat && !lng) {
				// #endif 
				// #ifdef MP-WEIXIN
				if (!lat && !lng && (realtime_location || (!realtime_location && recommend))) {
					// #endif
			
					if (this.userInfo.id && this.have_coupon) {
						this.getCouponList()
					}
					this.updateServiceItem({
						key: 'recommend_list',
						val: {data: []}
					})
					this.updateServiceItem({
						key: 'list',
						val: {
							data: [],
							last_page: 1,
							current_page: 1
						}
					})
					this.loading = false
					this.$util.hideAll()
					return
				}
				// #endif
				// 不清空路径缓存
				this.getMineInfo({empty: false})
				this.getList(1)
				// #ifdef APP-PLUS
				this.$refs.open_location_info.pShow = false
				// #endif 
				if ((realtime_location || (!realtime_location && recommend)) && !this.changeAddr) {
					this.$util.getMapInfo()
				}
				if (this.userInfo.id && this.have_coupon) {
					this.getCouponList()
				}
			},
		}
	}
</script>


<style lang="scss">
	.pages-home {
		.service-page-bg {
			width: 750rpx;
			// height: 739rpx;
			height: 735rpx;
			/* #ifdef H5 */
			z-index: 0;
			/* #endif */
			/* #ifndef H5 */
			z-index: -1;
			/* #endif */
			left: 0;
		}

		.service-bg {
			width: 750rpx;
			height: 100%;
			background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, #FFFFFF 100%);
			/* #ifdef H5 */
			z-index: 0;
			/* #endif */
			/* #ifndef H5 */
			z-index: -1;
			/* #endif */
		}

		.service-search {
			width: 690rpx;
			height: 70rpx;
			padding: 0 4rpx 0 30rpx;

			.flex-y-center {
				.f-desc {
					color: #C7C7C7;
				}
			}

			.btn {
				width: 106rpx;
				height: 62rpx;
			}
		}

		.recommend-technician {
			white-space: nowrap;
			width: 650rpx;

			.recommend-item {
				display: inline-block;
			}

			.recommend-item {
				width: 225rpx;
				height: 151rpx;
				border-radius: 12rpx;
				margin-left: 20rpx;

				.cover {
					width: 70rpx;
					height: 70rpx;
					border-radius: 35rpx;
				}

				.ellipsis {
					max-width: 120rpx;
				}

				.iconpingfen1 {
					font-size: 20rpx;
					background-image: -webkit-linear-gradient(270deg, #FAD961 0%, #F76B1C 100%);
				}

				.star-text {
					height: 26rpx;
					color: #FF9519;
					margin-left: 6rpx;
				}

				.new-tag {
					width: 67rpx;
					height: 30rpx;
					transform: rotateZ(360deg);
					border-radius: 4rpx;
				}

				.new-text {
					height: 30rpx;
					color: #050505
				}
			}

			.recommend-item:nth-child(1) {
				margin-left: 0;
			}

			.recommend-item.type-1:nth-child(3n-2) {
				background: linear-gradient(139deg, #DAF4FF 0%, #E4F1FF 100%);
			}

			.recommend-item.type-1:nth-child(3n-1) {
				background: linear-gradient(139deg, #F0DAFF 0%, #FFE4E4 100%);
			}

			.recommend-item.type-1:nth-child(3n) {
				background: linear-gradient(139deg, #FFEBDA 0%, #FFE4E4 100%);
			}
		}
	}

	.coupon-popup {
		width: 658rpx;
		height: 865rpx;
		position: relative;

		.bg-img {
			width: 100%;
			height: 100%;
		}

		.icon-close {
			font-size: 60rpx;
			position: absolute;
			top: 50rpx;
			right: 60rpx;
			z-index: 999;
		}

		.coupon-info {
			position: absolute;
			width: 100%;
			height: 100%;
			bottom: 0;
			left: 0;

			.tops {
				width: 480rpx;
				color: #FB4523;
				position: absolute;
				top: 260rpx;

				>view:nth-child(1) {
					font-weight: bold;
					font-size: 30rpx;
				}
			}

			.lists {
				width: 500rpx;
				height: 300rpx;
				padding: 10rpx;
				overflow-x: hidden;
				position: absolute;
				bottom: 222rpx;

				.list {
					width: 420rpx;
					height: 130rpx;
					margin-bottom: 10rpx;
					margin-top: 5rpx;
					position: relative;

					>image {
						width: 100%;
						height: 100%;
					}

					>view {
						position: absolute;
						width: 100%;
						height: 100%;
						top: 0;
						left: 8rpx;

						>view:nth-child(1) {
							width: 38%;
						}

						>view:nth-child(2) {
							display: flex;
							justify-content: center;
							flex: 1;
							padding: 0 15rpx;
							box-sizing: border-box;
						}

						.price {
							font-size: 30rpx;
							color: #FB4523;
						}

						.title {
							font-size: 30rpx;
							line-height: 36rpx;
							font-weight: bold;
						}

						.price_text {
							color: #ccc;
						}
					}
				}
			}

		}

		view.btns {
			width: 100%;
			position: absolute;
			height: 82rpx;
			bottom: 0rpx;
			left: 0;

			>view {
				width: 422rpx;
				height: 82rpx;

				border-radius: 40rpx;
				font-size: 34rpx;
				color: #FFFFFF;
			}
		}
	}

	.module {
		.wizard {
			width: 354rpx;
			height: 260rpx;
		}
		._wizard {
			width: 331rpx;
			height: 120rpx;
		}
		.business {
			width: 336rpx;
			height: 120rpx;
		}

		.join {
			width: 356rpx;
			height: 120rpx;
			margin-left: -20rpx;
		}
		._join{
			width: 356rpx;
			height: 120rpx;
			margin-left: 0rpx;
		}

		.wizard-title {
			top: 28rpx;
			left: 28rpx;
		}

		.business-title,
		.join-title {
			top: 22rpx;
			left: 40rpx;
		}
	}

	.list-item {
		.cover {
			width: 180rpx;
			height: 204rpx;
			// width: 150rpx;
			// height: 150rpx;
		}


		.text-delete {
			color: #B9B9B9;
		}

		// .item-btn {
		// 	width: 140rpx;
		// 	height: 52rpx;
		// }
		.iconwode2 {
			background-image: linear-gradient(#9FA4B6, #BFC5CF);
		}

		.item-btn {
			width: 120rpx;
			height: 60rpx;
			border-radius: 60rpx;
		} 
	}
	
	.mem-price{
		border: 1px solid #FF2D22;
		border-radius: 8rpx;
		margin-left: 14rpx;
		.mem-price-bg{
			margin-left: -1px;
		}
		.mem-price-img{
			width: 74rpx;
			height: 27rpx;
		}
		.mem-price-text{
			color: #FF2D22;
			padding: 0 4px;
			line-height: normal;
		}
	}
</style>
