<template>
	<view class="" v-if="isLoad">
		<uni-nav-bar :isAbs="true" :shadow="false" :statusBar="true" :onlyLeft="true" :color="`#fff`" :zIndex="99"
			backgroundColor="none">
			<view slot="left" @tap="goUrl">
				<view class="flex-center c-base radius" style="width:58rpx;height:58rpx;background:rgba(0,0,0,0.4)">
					<i class="iconfont icongengduo" style="font-size: 26rpx;transform: rotate(180deg);"></i>
				</view>
			</view>
		</uni-nav-bar>

		<view class="fill-base">
			<banner @change="goBanner" :list="detail.banner" :margin="0" :autoplay="true"
				:indicatorActiveColor="primaryColor" :height="564" :dotWidth="20" :dotBottom="38" :borderRadius="0"
				v-if="detail.banner.length > 0">
			</banner>
			<view class="pl-lg pr-lg rel fill-base d-content">
				<view class="flex-between pt-lg pb-md">
					<view class="flex-1">
						<view class="f-sm-title text-bold ellipsis max-580">{{detail.name}}</view>
						<view class="pt-sm pb-md flex-warp" v-if="configInfo.plugAuth.store">
							<block v-for="(item,index) in detail.star[0] * 1" :key="index">
								<i class="iconfont iconyduixingxingshixin icon-font-color icon-solid"></i>
							</block>
							<block v-if="detail.star[1] == 0">
								<i class="iconfont iconyduixingxingkongxin icon-empty" v-for="(item,index) in (5 - detail.star[0])" :key="index"></i>
							</block>
							<block v-else>
								<i class="iconfont iconxing2 icon-font-color icon-solid"></i>
								<i class="iconfont iconyduixingxingkongxin icon-empty" v-for="(item,index) in (5 - detail.star[0] - 1)" :key="index"></i>
							</block>
							<text class="f-desc" style="color: #FF9519;">{{detail.star[0]+'.'+detail.star[1]}}</text>
						</view>
						<view class="f-caption c-caption">{{detail.type_name}}</view>
					</view>
					<!-- #ifdef H5 -->
					<view class="flex-center" @tap.stop="$refs.share_box.open()">
						<i class="iconfont iconjishituijianyonghu"></i>
						<text class="f-caption pl-sm">分享</text>
					</view>
					<!-- #endif -->
					<!-- #ifdef APP-PLUS -->
					<view class="flex-center" @tap="toAppShare">
						<i class="iconfont iconjishituijianyonghu"></i>
						<text class="f-caption pl-sm">分享</text>
					</view>
					<!-- #endif -->
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="share" class="flex-center">
						<i class="iconfont iconjishituijianyonghu c-title"></i>
						<text class="f-caption pl-sm c-title">分享</text>
					</button>
					<!-- #endif -->
				</view>
				<view class="pt-sm b-1px-t pt-md flex-y-center">
					<view class="f-ms-little trade-status flex-center rel"
						:style="{color: detail.trade_status == 1 ? primaryColor : `#F8862D`}">
						<view class="trade-status-bg abs"
							:style="{backgroundColor: detail.trade_status == 1 ? primaryColor : `#F8862D`}"></view>
						<text>{{detail.trade_status == 1 ? `营业中` : `休息中`}}</text>
					</view>
					<view class="f-caption pl-sm">{{detail.week_time}}</view>
				</view>
				<view class="flex-warp pb-md ">
					<block v-for="(item,index) in detail.tag" :key="index">
						<view class="flex-center tag-item rel mr-md mb-sm mt-sm">
							<text class="f-caption" style="z-index: 1;">{{item}}</text>
							<view class="tag-bg abs" :style="{backgroundColor: '#F5F5F5'}"></view>
						</view>
					</block>
				</view>
				<view class="flex-between b-1px-t pb-lg pt-md">
					<view class="f-caption pr-lg">{{detail.address + detail.info}}</view>
					<view class="flex-center">
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toMap">
							<view class="flex-column">
								<view class="flex-center icon-ability fill-body">
									<i class="iconfont icondaohang1"></i>
								</view>
								<view class="f-ms-little flex-center">导航</view>
							</view>
						</auth>
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toContact">
							<view class="flex-column" style="margin-left: 50rpx;">
								<view class="flex-center icon-ability fill-body">
									<i class="iconfont icondianhua"></i>
								</view>
								<view class="f-ms-little flex-center">电话</view>
							</view>
						</auth>
					</view>
				</view>
			</view>
		</view>
		<!-- #ifdef H5 -->
		<view class="h-120 flex-y-center tab-box" style="top: 0px;">
		<!-- #endif -->
		<!-- #ifndef H5 -->
		<view style="width: 100%;background: #f6f6f6;z-index: 9;position: fixed;" :style="{height: configInfo.navBarHeight + 'px', top: 0}" v-if="tabBgShow"></view> 
		<view class="h-120 flex-y-center tab-box" :style="{top: configInfo.navBarHeight + 'px'}">
		<!-- #endif -->
			<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex" :activeColor="primaryColor"
				width="100px" height="80rpx" bgColor="#F6F6F6" lineClass="mini"></tab>
		</view>
		<view class="package fill-base" id="scroll1" v-if="configInfo.plugAuth.store">
			<view class="package-top flex-between pl-sm pr-sm">
				<view class="flex-center">
					<view class="flex-center package-label">
						<text class="f-ms-little c-base">团</text>
					</view>
					<text class="f-title text-bold">套餐</text>
				</view>
				<view class="flex-center">
					<block v-if="list.is_ensure">
						<i class="iconfont icongouxuan c-caption " style="font-size: 12px;"></i>
						<text class="pl-sm pr-md c-caption f-ms-little">随时退</text>
					</block>
					<i class="iconfont icongouxuan c-caption " style="font-size: 12px;"></i>
					<text class="pl-sm c-caption f-ms-little">过期退</text>
				</view>
			</view>
			<view class="flex-warp">
				<block v-for="(item,index) in list.data" :key="index">
					<view class="package-item ml-sm mr-sm mb-md fill-body radius-16"
						@tap="$util.goUrl({url: `/business/pages/package/detail?id=${item.id}&storeid=${options.id}&store=1&jump=${autograph?1:2}${item.seckill_id?'&is_seckill=1':''}`})">
						<view class="rel">
							<image :src="item.cover" mode="aspectFill" class="package-item-img"></image>
							<view class="package-item-label f-icontext c-base abs" 
							v-if="item.is_integral && configInfo.plugAuth.integral && item.seckill_id == 0">{{item.integral}}积分抵¥{{item.integral_to_money}}</view>
							<view class="package-item-label f-icontext c-base abs"
							v-if="item.seckill_id > 0">秒杀活动</view>
						</view>
						<view class="pl-md pr-md">
							<view class="pt-md pb-sm f-desc text-bold">{{item.name}}</view>
							<view class="f-icontext c-caption">{{item.sub_name}}</view>
							<view class="flex-between" style="padding-top: 15rpx;">
								<text class="f-paragraph text-bold" style="color: #F1270C;">¥{{item.price}}</text>
								<text class="f-ms-little c-caption">年售 {{item.sale | handerSale}}</text>
							</view>
							<view class="pt-md flex-between">
								<view class="flex-y-center">
									<view class="package-discount f-ms-little mr-sm" v-if="item.discount">
										{{item.discount}}折</view>
									<view class="c-caption f-ms-little" style="text-decoration-line:line-through">￥{{item.init_price}}</view>
								</view>
								<view class="f-desc c-base flex-center package-btn"
									:style="{background: primaryColor}">抢购</view>
							</view>
						</view>
					</view>
				</block>
			</view>
			<view class="pt-sm pb-lg flex-center" v-if="list.total > 4">
				<view class="flex-center"
					@tap="$util.goUrl({url: `/business/pages/package/list?id=${options.id}`})">
					<text class="f-caption">更多{{list.total - 4}}个套餐</text>
					<i class="iconfont iconxiangxiazhankai"></i>
				</view>
			</view>
			<abnor v-if="!list.data.length"></abnor>
		</view>
		<view class="fill-base pr-lg pt-lg pl-lg" id="scroll2" v-if="configInfo.plugAuth.store">
			<view class="info-title f-mini-title text-bold">用户评价({{comment.total}})</view>
			<view class="evaluate-item b-1px-b" v-for="(item,index) in comment.data" :key="index">
				<view class="flex-between">
					<view class="flex-center">
						<image :src="item.avatarUrl" mode="aspectFill" class="evaluate-header">
						</image>
						<view class="pl-sm">
							<view class="f-caption ">{{item.nickName}}</view>
							<view class="f-ms-little c-caption ">{{$util.formatTime(item.create_time * 1000 , 'YY-M-D h:m')}}</view>
						</view>
					</view>
					<view class="flex-warp">
						<block v-for="(s,index) in item.star*1" :key="index">
							<i class="iconfont iconpingjia1 icon-font-color icon-solid"></i>
						</block>
						<block v-for="(s,index) in (5 - item.star*1)" :key="index">
							<i class="iconfont iconpingjia1 icon-empty"></i>
						</block>
					</view>
				</view>
				<view class="pt-md pb-md">
					<view class="f-desc pre-wrap">
						{{ item.is_text ? item.new_text : item.text}}
					</view>
					<view @tap="changeMore(index)" class="f-caption pt-sm" style="color: #F04DAA;" v-if="item.text.length > 95">{{item.is_text ? `全部` : `收起`}}</view>
				</view>
				<view class="evaluate-images flex-y-center rel">
					<block v-for="(src,sindex) in item.img" :key="sindex">
						<image @tap="$util.previewImage({current:src,urls:item.img})" :src="src" mode="aspectFill" class="evaluate-image radius-16" v-if="sindex < 3">
						</image>
					</block>
					<view class="image-more flex-center abs" v-if="item.img.length > 3">
						<view class="image-more-bg abs"></view>
						<i class="iconfont icontupian rel" style="color: #fff;font-size: 12px;"></i>
						<text class="c-base rel f-ms-little" style="padding-left: 6rpx;">{{item.img.length}}</text>
					</view>
				</view>
			</view>
			<view class="all-evaluate flex-center" @tap="$util.goUrl({url: `/business/pages/store/evaluate?id=${options.id}`})">
				<text class="f-caption">查看全部网友点评</text>
				<i class="iconfont icon-right text-bold" style="font-size: 12px;"></i>
			</view>
		</view>

		<view class="fill-base pd-lg" id="scroll3" style="min-height: calc(100vh - 120rpx);">
			<view class="info-title f-mini-title text-bold pb-lg">场地介绍</view>
			<view class="pt-sm">
				<abnor v-if="!detail.intro"></abnor>
				<view v-else class="f-paragraph" style="white-space:pre-wrap">
					{{detail.intro}}
				</view>
			</view>
		</view>
		
		<uni-popup ref="share_box" type="center" :custom="true" :bgOpacity="0.6">
			<view class="share-friend">
				<view class="flex" style="justify-content: flex-end;">
					<image class="share-tips1" src="https://lbqny.migugu.com/admin/card/share-friend2.png"
						mode="aspectFill"></image>
				</view>
				<view class="flex" style="justify-content: flex-end;">
					<image class="share-tips2" src="https://lbqny.migugu.com/admin/card/share-friend1.png"
						mode="aspectFill"></image>
				</view>
				<view class="flex-center">
					<view class="share-box-btn f-title c-base flex-center" @tap="$refs.share_box.close()">知道了</view>
				</view>
			</view>
		</uni-popup>
		
		<view class="space-footer"></view>
		<!-- <view class="space-max-footer"></view>
		
		<fixed position="bottom">
			<view class="fill-base">
				<view class="flex-center bottom-btn">
					<view class="radius-16 info-btn flex-center mr-md" :style="{border: `1px solid ${primaryColor}`,color:primaryColor }" 
					@tap="$util.goUrl({url: detail.mobile ,openType: 'call'})">联系商家</view>
					<view class="radius-16 info-btn flex-center c-base f-paragraph" :style="{backgroundColor: primaryColor}" @tap.stop="toMap">一键导航</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</fixed> -->
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import siteInfo from '@/siteinfo.js';
	export default {
		data() {
			return {
				options: {},
				detail: {},
				isLoad: false,
				star: 3,
				tabList: [{
					title: '优惠',
					id: 0
				}, {
					title: '评价',
					id: 1
				}, {
					title: '场地介绍',
					id: 2,
				}],
				activeIndex: 0,
				list: {
					data: [],
					total: 0
				},
				scroll: {
					0: 0,
					1: 0,
					2: 0
				},
				comment: {
					data: []
				},
				tabBgShow: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			isGzhLogin: state => state.user.isGzhLogin,
			autograph: state => state.user.autograph,
		}),
		onShareAppMessage(e) {
			let {
				id: pid = 0
			} = this.userInfo
			let {
				name: title = '',
				cover: imageUrl
			} = this.detail
			let path = `/business/pages/store/detail?pid=${pid}&id=${this.options.id}&url=1`
			this.$util.log(path)
			return {
				title,
				imageUrl,
				path,
			}
		},
		async onLoad(options) {
			this.$util.showLoading()
			if(options.pid){
				options = await this.updateCommonOptions(options)
			}
			this.options = options
			let {
				pid = 0
			} = options
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
			this.initIndex()
		},
		onShow(){
			if(!this.scroll[0] && !this.scroll[1] && !this.scroll[2] && this.isLoad){
				this.$util.showLoading()
				this.options.url = 0
				this.isLoad = false
				this.initIndex()
			}
		},
		filters: {
			// handerTime(val) {
			// 	let {
			// 		start_time,
			// 		end_time,
			// 		trade_week,
			// 	} = val
			// 	let max = trade_week.substring(trade_week.length - 1)
			// 	let min = trade_week.substring(0, 1)
			// 	let week = ['日', '一', '二', '三', '四', '五', '六'];
			// 	if(min == max){
			// 		return `周${week[max]} ${start_time}-${end_time}`
			// 	}
			// 	return `周${week[min]}至周${week[max]} ${start_time}-${end_time}`
			// },
			handerSale(val) {
				if (val < 10) {
					return val
				}
				if (val < 10000) {
					return (val / 10).toFixed(0) * 10 + '+'
				}
				return val > 10000 ? (val / 10000).toFixed(1) + 'w+' : val
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo' ,'updateCommonOptions']),
			
			async initIndex(refresh = false) {
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})

				let {
					plugAuth = {}
				} = this.configInfo
				if (plugAuth.store) {
					let ind = this.tabList.findIndex(item => {
						return item.title == '优惠'
					})
					if(ind == -1){
						this.tabList.splice(ind , 0, {
							title: '优惠',
							id: 0
						})
					}
					let pind = this.tabList.findIndex(item => {
						return item.title == '评价'
					})
					if(pind == -1){
						this.tabList.splice(pind , 0, {
							title: '评价',
							id: 1
						})
					}
				} else {
					let ind = this.tabList.findIndex(item => {
						return item.title == '优惠'
					})
					if(ind != -1){
						this.tabList.splice(ind , 1)
					}
					let pind = this.tabList.findIndex(item => {
						return item.title == '评价'
					})
					if(pind != -1){
						this.tabList.splice(pind , 1)
					}
				}
				await this.getStorePackList()
				await this.getCommentList()
				await this.getInfo()
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					setTimeout(() => {
						this.toAppShare()
					}, 1200)
				}
				// #endif
			},
			initRefresh() {
				this.$util.showLoading()
				this.initIndex(true)
				this.getStorePackList()
			},
			async getStorePackList() {
				let data = await this.$api.business.storePackList({
					store_id: this.options.id,
					page: 1,
					limit: 4
				})
				this.list = data
			},
			async getCommentList(){
				let data = await this.$api.business.commentList({
					store_id: this.options.id,
					type: 0,
					page: 1,
					limit: 4
				})
				data.data.forEach(item =>{
					item.is_text = false
					item.new_text = item.text
					if(item.text.length > 95){
						item.is_text = true
						item.new_text = item.text.substring(0,95) + '...'
					}
					item.img = item.img ? item.img.split(',') : []
				})
				this.comment = data
			},
			async getInfo() {
				let data = await this.$api.business.getInfo({
					id: this.options.id
				})
				data.banner = data.banner.split(',')
				data.tag = data.tag.split(',')
				data.type_name = data.type_name.join('/')
				data.star = data.star.split('.')
				
				let {
					start_time,
					end_time,
					trade_week,
				} = data
				let max = trade_week.substring(trade_week.length - 1)
				let min = trade_week.substring(0, 1)
				let week = ['日', '一', '二', '三', '四', '五', '六'];
				let week_time = ''
				if(min == max){
					week_time = `周${week[max]} ${start_time}-${end_time}`
				}
				week_time = `周${week[min]}至周${week[max]} ${start_time}-${end_time}`
				data.week_time = week_time
				this.detail = data
				this.isLoad = true
				this.$util.hideAll()
				let that = this
				
				setTimeout(() => {
					const query = uni.createSelectorQuery().in(this);
					query.select('#scroll1').boundingClientRect(res => {
						that.scroll[0] = res ? Math.floor(res.top) : 0
					}).exec();
					query.select('#scroll2').boundingClientRect(res => {
						that.scroll[1] = Math.floor(res.top)
					}).exec();
					query.select('#scroll3').boundingClientRect(res => {
						that.scroll[2] = Math.floor(res.top)
					}).exec();
				},600)
			},
			goBanner(item) {
				let current = item
				let urls = this.detail.banner
				this.$util.previewImage({
					current,
					urls
				})
			},
			linkpress(res) {
				// #ifdef APP-PLUS
				if (/http/.test(res.href))
					this.$util.goUrl({
						url: res.href,
						openType: 'web'
					})
				// #endif
			},
			// 查看定位
			async toMap() {
				let {
					address,
					lat,
					lng
				} = this.detail
				await this.$util.checkAuth({
					type: 'userLocation'
				})
				await uni.getLocation({
					type: 'gcj02',
				})
				await uni.openLocation({
					latitude: lat * 1,
					longitude: lng * 1,
					name: address,
					scale: 28
				})
			},
			toContact() {
				let {
					mobile,
					contact_type,
					qywx_company_id,
					qywx_kid
				} = this.detail
				let {
					im_type
				} = this.configInfo
				
				if (contact_type == 2 && im_type == 3) {
					// #ifdef H5
					window.location.href = qywx_kid
					// #endif
					// #ifndef H5 
					// #ifdef MP-WEIXIN 
					try {
						wx.openCustomerServiceChat({
							extInfo: {
								url: qywx_kid
							},
							corpId: qywx_company_id,
							fail(res) {
								uni.showToast({
									title: res.errorMsg,
									icon: 'none'
								})
							}
						})
					} catch (e) {
						this.$util.showToast({
							title: `请更新微信版本`
						})
					}
					// #endif
					// #ifdef APP-PLUS
					let wechat = null
					plus.share.getServices(res => {
						wechat = res.find(i => i.id === 'weixin')
						if (wechat) {
							wechat.openCustomerServiceChat({
								corpid: qywx_company_id,
								url: qywx_kid
							}, err => {
								uni.showToast({
									title: err.errorMsg
								})
							})
						} else {
							uni.showToast({
								title: '当前环境不支持微信操作',
								icon: 'none'
							})
						}
					}, function() {
						uni.showToast({
							title: '获取服务失败，不支持该操作',
							icon: 'none'
						})
					})
					// #endif
					// #endif
					return
				}
				if(!mobile) return
				let url = mobile
				this.$util.goUrl({
					url,
					openType: 'call'
				})
			},
			goUrl(){
				// let that = this
				// uni.navigateBack({
				// 	url: 1,
				// 	fail() {
				// 		that.$util.goUrl({url: `/pages/service`, openType: 'reLaunch'})
				// 	}
				// })
				if(this.options.url || this.options.jump || this.options.pid){
					this.$util.goUrl({url: `/pages/service`, openType: 'reLaunch'})
				}else{
					this.$util.goUrl({ url: 1, openType: `navigateBack` })
				}
			},
			
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo

				let {
					siteroot
				} = siteInfo
				let {
					name: title = '',
					cover: imageUrl = '',
					week_time = ''
				} = this.detail
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/business/pages/store/detail?pid=${pid}&id=${this.options.id}&url=1`
				let summary = ''
				// #ifdef H5
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, week_time, href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
				// #endif
				// #ifdef APP-PLUS
				uni.share({
					provider: "weixin",
					scene: 'WXSceneSession',
					type: 0,
					href,
					title,
					summary,
					imageUrl,
					success: function(res) {
					},
					fail: function(err) {
					}
				});
				// #endif
			},
			async handerTabChange(index) {
				let {
					plugAuth = {}
				} = this.configInfo
				let scrollTop = 0
				// #ifdef H5
				if(plugAuth.store){
					scrollTop = this.scroll[index] - 60
				}else{
					scrollTop = this.scroll[index + 1] - 60
				}
				// #endif
				// #ifndef H5
				if(plugAuth.store){
					scrollTop = this.scroll[index] - 120
				}else{
					scrollTop = this.scroll[index + 1] - 120
				}
				// #endif
				await uni.pageScrollTo({
					scrollTop ,
					duration: 0
				})
				this.$nextTick(()=>{
					this.activeIndex = index
				})
			},
			changeMore(index){
				let {
					is_text
				} = this.comment.data[index]
				this.comment.data[index].is_text = is_text ? false : true
			}
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onPageScroll(e) {
			
			const query = uni.createSelectorQuery().in(this);
			query.select('.tab-box').boundingClientRect(res => {
				if(res.top == this.configInfo.navBarHeight){
					this.tabBgShow = true
				}else{
					this.tabBgShow = false
				}
			}).exec();
			
			let {
				plugAuth = {}
			} = this.configInfo
			if(plugAuth.store){
				// #ifdef H5
				if(this.scroll[1] - 62 >= e.scrollTop){
					this.activeIndex = 0
				}else if(this.scroll[2] - 62 >= e.scrollTop){
					this.activeIndex = 1
				}else{
					this.activeIndex = 2
				}
				// #endif
				// #ifndef H5
				if(this.scroll[1] - 122 >= e.scrollTop){
					this.activeIndex = 0
				}else if(this.scroll[2] - 122 >= e.scrollTop){
					this.activeIndex = 1
				}else{
					this.activeIndex = 2
				}
				// #endif
			}else{
				// if(this.scroll[2] - 60 > e.scrollTop){
				// 	this.activeIndex = 0
				// }else{
				// 	this.activeIndex = 1
				// }
				this.activeIndex = 0
			}
			
		}
	}
</script>

<style lang="scss">
	.store-img {
		width: 135rpx;
		height: 135rpx;
	}

	.tag-item {
		padding: 2px 10rpx;
		border-radius: 6rpx;

		.tag-bg {
			width: 100%;
			height: 100%;
			border-radius: 6rpx;
		}
	}

	.bottom-btn {
		height: 112rpx;

		.info-btn {
			width: 290rpx;
			height: 85rpx;
		}
	}

	.icon-solid {
		background-image: linear-gradient(#FAD961, #F76B1C);
		margin-right: 2px;
	}

	.icon-empty {
		color: #E4E4E4;
		margin-right: 2px;
	}

	.d-content {
		margin-top: -40rpx;
		border-radius: 30rpx 30rpx 0px 0px;

		.trade-status {
			width: 73rpx;
			height: 28rpx;
			border-radius: 4rpx;

			.trade-status-bg {
				width: 73rpx;
				height: 28rpx;
				border-radius: 4rpx;
				opacity: 0.1;
			}
		}

		.icon-ability {
			width: 45rpx;
			height: 45rpx;
			border-radius: 45rpx;
		}
	}

	.h-120 {
		height: 120rpx;
	}

	.package {
		padding: 0 15rpx;

		.package-top {
			height: 90rpx;

			.package-label {
				width: 34rpx;
				height: 34rpx;
				border-radius: 6rpx;
				background: #F8862D;
				margin-right: 6rpx;
			}
		}

		.package-item {
			width: calc(50% - 20rpx);
			overflow: hidden;
			padding-bottom: 26rpx;

			.package-item-img {
				height: 234rpx;
				width: 100%;
			}
			
			.package-item-label{
				height: 40rpx;
				border-radius: 16rpx 0 16rpx 0;
				left: 0;
				top: 0;
				background: linear-gradient( 90deg, #FF4C88 0%, #FF7B7B 100%);
				padding: 0 10rpx;
				line-height: 40rpx;
			}

			.package-btn {
				width: 94rpx;
				height: 54rpx;
				border-radius: 54rpx;
			}

			.package-discount {
				padding: 0px 8rpx;
				border-radius: 4rpx;
				color: #F1270C;
				border: 1px solid #F1270C;
			}
		}
	}

	.evaluate-item {
		padding: 40rpx 0;

		.evaluate-header {
			width: 70rpx;
			height: 70rpx;
			border-radius: 70rpx;
		}

		.evaluate-image {
			width: 224rpx;
			height: 224rpx;
			margin-right: 8rpx;
			&:nth-child(3n){
				margin-right: 0;
			}
		}
	}

	.all-evaluate {
		height: 84rpx;
	}
	
	.tab-box{
		position: sticky;
		width: 100%;
		background-color: #F6F6F6;
		z-index: 9;
	}
	
	.image-more{
		width: 66rpx;
		height: 34rpx;
		right: 20rpx;
		bottom: 20rpx;
		.image-more-bg{
			width: 66rpx;
			height: 34rpx;
			border-radius: 34rpx;
			left: 0;
			top: 0;
			background-color: #000;
			opacity: 0.5;
		}
	}
	.share-friend {
		width: 100vw;
		height: 100vh;
	
		.share-tips1 {
			width: 123rpx;
			height: 138rpx;
			margin-right: 30rpx;
			margin-top: 10rpx;
		}
	
		.share-tips2 {
			width: 507rpx;
			height: 157rpx;
			margin-top: 37rpx;
			margin-right: 40rpx;
		}
	
		.share-box-btn {
			width: 264rpx;
			height: 105rpx;
			border-radius: 105rpx;
			border: 1px solid #fff;
			margin-top: 130rpx;
		}
	}
</style>
