<template>
	<view class="">
		<fixed>
			<search @input="toSearch" type="input" :padding="30" :radius="0" backgroundColor="#fff"
				placeholder="请输入套餐名称">
			</search>
		</fixed>
		<block v-if="isLoad">
			<view class="pb-md pl-lg pr-lg rel pt-md">
				<view class="s-item radius-20 fill-base flex-between" v-for="(item,index) in list.data" :key="index" 
				@tap="$util.goUrl({url: `/business/pages/package/detail?id=${item.package_id}&storeid=${item.store_id}&jump=1&is_seckill=1`})">
					<view class="">
						<view class="item-cover rel">
							<view class="f-ms-little flex-center abs item-label c-base" v-if="item.is_ad">广告</view>
							<image :src="item.cover" mode="aspectFill" class="item-cover"></image>
						</view>
						<view class="mt-sm flex-y-center">
							<view class="item-slider">
								<view class="item-slider-num" :style="{width: `${12.8*(item.use_stock / item.stock)}rpx`}"></view>
							</view>
							<text class="f-ms-little c-78777B">已抢{{item.stock_rate}}</text>
						</view>
					</view>
					<view class="item-cont rel flex-1">
						<view class="f-title text-bold ellipsis-2">{{item.name}}</view>
						<view class="flex-y-center pt-sm max-400">
							<i class="iconfont icondizhi" :style="{color: primaryColor}"></i>
							<text class="f-icontext c-78777B ellipsis max-200">{{item.store_name}}</text> 
							<view class="item-cont-line"></view>
							<text class="f-icontext c-78777B">{{item.distance}} km</text>
						</view>
						<view class="item-time flex-y-center f-icontext mt-sm">
							<view class="item-time-left flex-center c-base">仅剩</view>
							<view class="item-time-text pl-sm pr-sm text-bold c-warning">
								<min-countdown :targetTime="item.end_time*1000" @callback="countEnd" :type="1"></min-countdown>
							</view>
						</view>
						<view class="abs radius-16 item-btn flex-1 flex-between">
							<view class="flex-y-baseline">
								<text class="f-caption text-bold c-warning">￥</text>
								<text class="f-caption text-bold c-warning f-md-title">{{item.price}}</text>
								<text class="f-caption c-caption text-delete">￥{{item.package_price}}</text>
							</view>
							<image src="https://lbqny.migugu.com/admin/peiwan/seckill-btn.png" mode="aspectFill" class="item-btn-img"></image>
						</view>
					</view>
				</view>
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
					
				let newList = await this.$api.business.seckillList(param)
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
	.item-cont{
		height: 260rpx;
		.item-cont-line{
			height: 24rpx;
			width: 1px;
			border: 1px solid #B1B0B2;
			margin: 0 16rpx;
		}
		.item-time{
			height: 34rpx;
			background: #FFF2ED;
			border-radius: 8rpx;
			display: inline-flex;
		}
		.item-time-left{
			width: 68rpx;
			height: 34rpx;
			border-radius: 8rpx 0 0 8rpx;
			background: #FF3317;
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
		}
		.item-slider{
			width: 128rpx;
			height: 16rpx;
			margin-right: 8rpx;
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
				border: 1px solid #B1B0B2;
				margin: 0 16rpx;
			}
			.item-time{
				height: 34rpx;
				background: #FFF2ED;
				border-radius: 8rpx;
				display: inline-flex;
			}
			.item-time-left{
				width: 68rpx;
				height: 34rpx;
				border-radius: 8rpx 0 0 8rpx;
				background: #FF3317;
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