<template>
	<view class="package-detail">
		<block v-if="isLoad">
			<uni-nav-bar :isAbs="true" :shadow="false" :statusBar="true" :onlyLeft="true" :color="`#fff`" :zIndex="99"
				backgroundColor="none">
				<view slot="left" @tap="goUrl(0)">
					<view class="flex-center c-base radius" style="width:58rpx;height:58rpx;background:rgba(0,0,0,0.4)">
						<i class="iconfont icongengduo" style="font-size: 26rpx;transform: rotate(180deg);"></i>
					</view>
				</view>
			</uni-nav-bar>
			<view class="fill-body package-box" id="packagebox">
				<banner @change="goBanner" :list="detail.banner" :margin="0" :autoplay="true"
					:indicatorActiveColor="primaryColor" :height="527" :dotWidth="20" :dotBottom="38" :borderRadius="0"
					v-if="detail.banner.length > 0">
				</banner>
				<view class="pl-lg pr-lg rel fill-base seckill-basic" v-if="detail.is_seckill == 1 && configInfo.plugAuth.seckill">
					<view class="abs info-bg">
						<image src="https://lbqny.migugu.com/admin/peiwan/seckill-info-01.png" mode="aspectFill" class="abs info-bg"></image>
						<image src="https://lbqny.migugu.com/admin/peiwan/seckill-info-03.png" mode="aspectFill" class="abs info-bg1"></image>
					</view>
					<view class="flex-between package-basic-box">
						<view class="rel">
							<view class="flex-y-center ">
								<view class="flex-y-baseline">
									<text class="f-paragraph c-base text-bold">¥</text>
									<text class="f-md-title c-base text-bold" style="padding-left: 3px;">{{detail.price}}</text>
								</view>
								<!-- <view class="package-discount f-paragraph mr-sm ml-sm c-warning fill-base flex-center"
									v-if="detail.discount">{{detail.discount}}
									<text class="f-ms-little" style="padding-left: 2px;">折</text></view> -->
								<view class="init-price f-ms-little" style="padding-top: 18rpx;">￥{{detail.init_price}}</view>
							</view>
							<view class="mt-sm flex-y-center">
								<view class="item-slider mr-sm">
									<view class="item-slider-num" v-if="detail.stock > 0" :style="{width: `${128/detail.stock*detail.use_stock}rpx`}"></view>
								</view>
								<text class="f-paragraph c-base">已抢{{detail.stock_rate}}</text>
							</view>
						</view>
						<view class="rel pt-md">
							<view class="f-caption pb-sm text-right" style="color: #FF3A3D;">活动结束仅剩</view>
							<min-countdown :targetTime="detail.end_time*1000" @callback="countEnd" :type="5"></min-countdown>
						</view>
						<view class="abs package-cont-top fill-body"></view>
					</view>
				</view>
				<view class="pl-lg pr-lg rel fill-base package-basic" v-else
					:style="{background: `linear-gradient(to right, ${ primaryColor } 0%, ${gradualColor} 100%)`}">
					<view class="flex-between package-basic-box">
						<view class="flex-center">
							<view class="flex-y-baseline">
								<text class="f-paragraph c-base">¥</text>
								<text class="f-big-title c-base">{{detail.price}}</text>
							</view>
							<view class="package-discount f-ms-little mr-sm ml-sm" :style="{color: primaryColor}"
								v-if="detail.discount">{{detail.discount}}折</view>
							<view class="init-price f-ms-little">￥{{detail.init_price}}</view>
						</view>
						<view class="package-sale f-ms-little">年售 {{detail.sale | handerSale}}</view>
						<view class="abs package-cont-top fill-body"></view>
					</view>
				</view>
				<view class="c-base f-icontext package-label ml-md rel" 
				v-if="detail.is_integral && configInfo.plugAuth.integral && detail.is_seckill!=1">{{detail.integral}}积分抵¥{{detail.integral_to_money}}</view>
				<view class="package-cont pt-lg pl-md pr-md rel">
					<view class="fill-base radius-16 pd-lg">
						<view class="text-bold f-sm-title pb-md b-1px-b">{{detail.name}}</view>
						<view class="pt-md flex">
							<text class="f-caption text-bold">须知</text>
							<text class="pl-lg flex-1">{{detail.day_week}}</text>
						</view>
						<view class="pt-md" style="line-height: 1;">
							<text class="f-caption text-bold">保障</text>
							<text class="pl-lg">{{detail.ensure == 1 ? '随时退·' : ''}}过期退</text>
						</view>
					</view>
				</view>
			</view>
			<!-- #ifdef H5 -->
			<view class="h-120 flex-y-center tab-box" style="top: 0px;" id="tabbox" v-if="configInfo.plugAuth.store">
			<!-- #endif -->
			<!-- #ifndef H5 -->
			<view style="width: 100%;background: #f6f6f6;z-index: 9;position: fixed;" :style="{height: configInfo.navBarHeight + 'px', top: 0}" v-if="tabBgShow"></view>
			<view class="h-120 flex-y-center tab-box" id="tabbox" :style="{top: configInfo.navBarHeight + 'px'}"
				v-if="configInfo.plugAuth.store">
				<!-- #endif -->
				<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex" :activeColor="primaryColor"
					width="100px" height="80rpx" bgColor="#F6F6F6" lineClass="mini"></tab>
			</view>
			<view class="fill-base pl-lg pr-lg ml-md mr-md radius-16" id="scroll1">
				<view class="pt-lg dumpling-item" v-for="(item,index) in detail.sku" :key="index">
					<view class="f-desc text-bold">{{item.name}}</view>
					<view class="f-desc flex-between pt-md" v-for="(citem,cindex) in item.price" :key="cindex">
						<view class="pr-lg max-380">{{citem.name}}</view>
						<view class="flex-between dumpling-right">
							<view class="f-desc">{{citem.num}}份</view>
							<view class="f-desc">¥{{citem.price}}</view>
						</view>
					</view>
				</view>
				<block v-if="detail.introduce">
					<view class="f-min-title img-text-title flex-center">
						<view class="img-text-line"></view>
						<text class="abs fill-base text-center f-mini-title" style="width: 192rpx;">图文详情</text>
					</view>
					<!-- <abnor v-if="!detail.introduce"></abnor> -->
					<view class="f-paragraph pb-lg" style="white-space:pre-wrap" >
						<parser :html="detail.introduce" @linkpress="linkpress" show-with-animation lazy-load>加载中...
						</parser>
					</view>
				</block>
			
			</view>
			<view class="ml-md mr-md" id="scroll2" style="min-height: calc(100vh - 120rpx);">
				<view class="pt-lg pb-lg text-bold f-title">购买须知</view>
				<view class="fill-base radius-16 pd-lg" >
					<view class="f-desc text-bold">有效期</view>
					<view class="pt-sm flex-y-center">
						<text class="mr-sm notice-left"></text>{{detail.tday}}
					</view>
					<view class="f-desc text-bold" style="padding-top: 40rpx;">使用时间</view>
					<view class="pt-sm flex-y-center">
						<text class="mr-sm notice-left"></text>{{detail.wtime}}
					</view>
					<view class="f-desc text-bold" style="padding-top: 40rpx;">预约信息</view>
					<view class="pt-sm flex-y-center">
						<text class="mr-sm notice-left"></text>
						{{detail.reservation_day ? `需提前${detail.reservation_day}天预约` : '无需预约'}}
					</view>
					<view class="f-desc text-bold" style="padding-top: 40rpx;">使用规则</view>
					<abnor v-if="!detail.rule_text"></abnor>
					<view v-else class="f-paragraph" style="white-space:pre-wrap">
						{{detail.rule_text}}
					</view>
				</view>
			</view>
			<view class="space-max-footer"></view>
			
			<fixed position="bottom" :zIndex="99">
				<view class="fill-base b-1px-t">
					<view class="flex-between bottom-btn">
						<view class="flex-between bottom-left">
							<button class="flex-column" style="line-height: 20px;" @tap="goUrl(1)">
								<i class="iconfont icondianpu1 text-center c-black" style="font-size: 17px;"></i>
								<text class="f-caption text-center c-black" style="padding-top: 6rpx;">门店</text>
							</button>
							<!-- <button class="flex-column" style="line-height: 20px;" @tap="changeCollect">
								<i class="iconfont text-center c-black" :style="{color: detail.is_collect ? primaryColor : `#000` }"
								:class="[{'iconyishoucang': detail.is_collect},{'iconshoucang3': !detail.is_collect}]"></i>
								<text class="f-caption text-center c-black" style="padding-top: 6rpx;">收藏</text>
							</button> -->
							<!-- #ifdef H5 -->
							<button class="share-btn" style="line-height: 20px;" @tap.stop="$refs.share_box.open()">
								<view class="flex-column">
									<i class="iconfont iconfenxiangdibu text-center c-black" style="font-size: 17px;"></i>
									<text class="f-caption text-center c-black" style="padding-top: 6rpx;">分享</text>
								</view>
							</button>
							<!-- #endif -->
							<!-- #ifdef MP-WEIXIN -->
							<button class="share-btn" open-type="share" style="line-height: 20px;">
								<view class="flex-column">
									<i class="iconfont iconfenxiangdibu text-center c-black" style="font-size: 17px;"></i>
									<text class="f-caption text-center c-black" style="padding-top: 6rpx;">分享</text>
								</view>
							</button>
							<!-- #endif -->
							<!-- #ifdef APP-PLUS -->
							<button class="flex-column" @tap="toAppShare" style="line-height: 20px;">
								<i class="iconfont iconfenxiangdibu text-center c-black" style="font-size: 17px;"></i>
								<text class="f-caption text-center c-black" style="padding-top: 6rpx;">分享</text>
							</button>
							<!-- #endif -->
						</view>
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width: 290rpx;"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url: `/business/pages/package/payment?id=${options.id}&is_seckill=${configInfo.plugAuth.seckill ? (detail.is_seckill || 0) : 0}`})">
							<view class="panic-buying f-paragraph text-bold c-base flex-center"
								:style="{background: primaryColor}">{{detail.is_seckill==0?'立即抢购':'秒杀下单'}}</view>
						</auth>
						
						<!--立即抢购-->
					</view>
					<view class="space-safe"></view>
				</view>
			</fixed>
		</block>
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
		<abnor v-if="isPage" percent="180%"></abnor>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import siteInfo from '@/siteinfo.js';
	import parser from "@/components/jyf-Parser/index"
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},
				detail: {},
				isLoad: false,
				star: 3,
				tabList: [{
					title: '团购详情',
					id: 0
				}, {
					title: '购买须知',
					id: 1,
				}],
				activeIndex: 0,
				list: {
					data: [],
					total: 0
				},
				scroll: {
					0: 0,
					1: 0
				},
				isPage: false,
				tabBgShow: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			gradualColor: state => state.config.configInfo.gradualColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			autograph: state => state.user.autograph,
		}),
		filters: {
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
		onShareAppMessage(e) {			let {				id: pid = 0			} = this.userInfo
			let {
				name: title = '',
				cover: imageUrl,
				is_seckill = 0
			} = this.detail
			let {
				id,
				storeid = 0,
				store = 0,
			} = this.options			let path = `/business/pages/package/detail?pid=${pid}&id=${id}&storeid=${storeid}&store=${store}&is_seckill=${is_seckill}`			this.$util.log(path)			return {
				title,				path,
				imageUrl			}
		},
		async onLoad(options) {
			this.isLoad = false
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
				if (isGzhLogin || uni.getStorageSync('isGzhLogin')) {
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
			console.log(this.scroll, '=======> onShow')
			if(!this.scroll[0] && !this.scroll[1] && this.isLoad){
				this.$util.showLoading()
				this.options.url = 0
				this.isLoad = false
				this.initIndex()
			}
		},
		methods: {
			...mapActions(['getConfigInfo','getUserInfo','updateCommonOptions']),
			async initIndex(refresh = false) {
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				
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
			},
			async getInfo() {
				let {
					id,
					is_seckill = 0
				} = this.options
				let data = {}
				try{
					data = await this.$api.business.storePackInfo({
						id,
						is_seckill
					})
				}catch(e){
					console.log(e, '=============> error')
					this.isPage = true
					this.isLoad = false
					return
				}
				data.banner = data.imgs.split(',')
				let {
					use_start_time,
					use_end_time,
					use_trade_week,
					reservation_day,
					term_type,
					days,
					term_start_time,
					term_end_time,
					sku,
					use_type,
					store
				} = data
				let rday = reservation_day > 0 ? '需预约' : '无需预约'
				let tday = term_type == 2 ? `购买后${days}天内有效` : this.$util.formatTime(term_end_time * 1000, 'YY-M-D') +
					'结束'
				let max = use_trade_week.substring(use_trade_week.length - 1)
				let min = use_trade_week.substring(0, 1)
				if(use_type == 1){
					max = store.trade_week.substring(store.trade_week.length - 1)
					min = store.trade_week.substring(0, 1)
					use_start_time = store.start_time
					use_end_time = store.end_time
				}
				let week = ['日', '一', '二', '三', '四', '五', '六'];
				data.rday = rday
				data.tday = term_type == 2 ? `购买后${days}天内有效` : this.$util.formatTime(term_start_time * 1000,
					'YY-M-D') + ' 至 ' + this.$util.formatTime(term_end_time * 1000, 'YY-M-D')
				data.wtime = `周${week[min]}至周${week[max]} ${use_start_time}-${use_end_time}`
				if(min == max){
					data.wtime = `周${week[max]} ${use_start_time}-${use_end_time}`
				}
				if ((use_start_time == '00:00' && use_end_time == '23:59') || (use_start_time == use_end_time)) {
					data.wtime = `周${week[min]}至周${week[max]} 全天可用`
					if(min == max){
						data.wtime = `周${week[max]} 全天可用`
					}
				}
				data.day_week = `${rday}·${data.wtime}·${tday}`
				let sku_price = 0
				sku.map(item => {
					item.price && item.price.map(citem => {
						sku_price += citem.price
					})
				})
				data.sku_price = sku_price
				uni.setNavigationBarTitle({
					title: data.is_seckill == 0 ? '套餐详情' : '活动详情'
				})
				this.detail = data
				this.isLoad = true
				this.$util.hideAll()
				let that = this
				setTimeout(() => {
					const query = uni.createSelectorQuery().in(this);
					query.select('#scroll1').boundingClientRect(res => {
						that.scroll[0] = Math.floor(res.top)
					}).exec();
					query.select('#scroll2').boundingClientRect(res => {
						that.scroll[1] = Math.floor(res.top)
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
			goUrl(type = 0) {
				console.log(this.options.pid)
				if(type == 1 && this.detail.is_seckill == 1){
					this.$util.goUrl({
						url: `/business/pages/store/detail?id=${this.detail.store.id}`
					})
				}else if(this.options.type && type == 1){
					this.$util.goUrl({
						url: `/business/pages/store/detail?id=${this.detail.store.id}`
					})
				}else if(this.options.jump == 2){
					if(this.autograph){
						this.$util.goUrl({
							url: `/business/pages/store/detail?id=${this.detail.store.id}&jump=1`
						})
					}else{
						uni.navigateBack({
							delta: 1
						})
					}
				}else if (this.options.url) {
					if (type == 1) {
						this.$util.goUrl({
							url: `/business/pages/store/detail?id=${this.detail.store.id}`
						})
					} else {
						this.$util.goUrl({
							url: `/pages/service`,
							openType: 'reLaunch'
						})
					}
				} else {
					uni.navigateBack({
						delta: 1
					})
				}
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
					sub_name = '',
					is_seckill = 0
				} = this.detail
				let {
					id,
					storeid = 0,
					store = 0,
				} = this.options
				let url = siteroot.split('/index.php')[0]
				let href =
					`${url}/h5/#/business/pages/package/detail?pid=${pid}&id=${id}&storeid=${storeid}&store=${store}&url=1&is_seckill=${is_seckill}`
				let summary = ''
				// #ifdef H5
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, sub_name, href, imageUrl)
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
						console.log("success:" + JSON.stringify(res));
					},
					fail: function(err) {
						console.log("fail:" + JSON.stringify(err));
					}
				});
				// #endif
			},
			handerTabChange(index) {
				uni.pageScrollTo({
					scrollTop: this.scroll[index] - 60,
					duration: 20
				})
				// this.activeIndex = this.tabList[index].id
			},
			async changeCollect() {
				let {
					is_collect,
					id
				} = this.detail
				await this.$api.business.collect({id})
				this.detail.is_collect = is_collect == 0 ? 1 : 0
				this.$util.showToast({
					title: is_collect == 0 ? '收藏成功' : '取消收藏成功'
				});
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
			console.log(e ,'===========> onPageScroll',this.scroll)
			const query = uni.createSelectorQuery().in(this);
			query.select('.tab-box').boundingClientRect(res => {
				// console.log(res , this.configInfo.navBarHeight)
				if(res.top == this.configInfo.navBarHeight){
					this.tabBgShow = true
				}else{
					this.tabBgShow = false
				}
			}).exec();
			if(this.scroll[1] - 60 > e.scrollTop){
				this.activeIndex = 0
			}else{
				this.activeIndex = 1
			}
		}
	}
</script>

<style lang="scss">
	.package-detail {
		.package-basic {
			height: 155rpx;
			margin-top: -40rpx;
			border-radius: 24rpx 24rpx 0 0;

			.package-basic-box {
				height: 125rpx;
			}
			
			.package-discount {
				padding: 0px 8rpx;
				border-radius: 4rpx;
				background-color: #FDFDFD;
			}
			
			.init-price {
				text-decoration-line: line-through;
				color: #FDFDFD;
			}
			
			.package-sale {
				color: #FDFDFD;
			}
			
			.package-cont-top {
				height: 30rpx;
				width: 100%;
				left: 0;
				bottom: -1px;
				z-index: 1;
				border-radius: 24rpx 24rpx 0 0;
			}
		}
		.seckill-basic {
			height: 172rpx;
			margin-top: -40rpx;
			border-radius: 32rpx 32rpx 0 0;
			background: #fff linear-gradient( 90deg, rgba(254,205,206,0.89) 45%, #FFF4E0 100%);
			.package-basic-box {
				height: 140rpx;
			}
			.package-discount {
				padding: 2px 8rpx;
				border-radius: 32rpx 36rpx 36rpx 0;
				line-height: normal;
			}
			.package-sale {
				color: #FDFDFD;
			}
			.init-price {
				text-decoration-line: line-through;
				color: rgba(255,255,255,0.6);
			}
			.package-cont-top {
				height: 30rpx;
				width: 100%;
				left: 0;
				bottom: -1px;
				z-index: 1;
				border-radius: 24rpx 24rpx 0 0;
			}
			.info-bg{
				width: 524rpx;
				height: 172rpx;
				left: 0;
			}
			.info-bg1{
				width: 72rpx;
				height: 140rpx;
				top: 0;
				right: 14rpx;
			}
			.item-slider{
				width: 128rpx;
				height: 16rpx;
				background: rgba(255,255,255,0.28);
				border-radius: 16rpx;
				.item-slider-num{
					height: 16rpx;
					border-radius: 16rpx;
					background: #fff;
				}
			}
		}

		.h-120 {
			height: 120rpx;
		}


		.package-cont {
			border-radius: 24rpx 24rpx 0 0;
			margin-top: -30rpx;
		}

		.dumpling-item {
			padding-bottom: 40rpx;

			.dumpling-right {
				min-width: 200rpx;
			}
		}

		.img-text-title {
			padding: 70rpx 0 50rpx 0;

			.img-text-line {
				width: 472rpx;
				height: 1px;
				border-bottom: 1px solid #979797;
			}
		}

		.bottom-btn {
			height: 130rpx;
			padding: 0 40rpx;

			.bottom-left {
				width: 130rpx;
				//width: 236rpx;
			}

			.panic-buying {
				width: 290rpx;
				height: 90rpx;
				border-radius: 90rpx;
			}
		}

		.notice-left {
			width: 2px;
			height: 2px;
			border-radius: 2rpx;
			background: #333;
		}

		.tab-box {
			position: sticky;
			width: 100%;
			background-color: #F6F6F6;
			z-index: 99;
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
		
		.package-label{
			margin-top: -5rpx;
			margin-bottom: 25rpx;
			background: linear-gradient( 235deg, #FF9E6C 0%, #FF447F 100%);
			height: 46rpx;
			padding: 0 10rpx;
			display: inline-block;
			border-radius: 6rpx;
			z-index: 9;
			line-height: 46rpx;
		}
	}
</style>
