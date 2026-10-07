<template>
	<view class="">
		<fixed>
			<search @input="toSearch" type="input" :padding="30" :radius="0" backgroundColor="#fff"
				placeholder="搜索套餐名称">
			</search>
		</fixed>
		<block v-if="isLoad">
			<view class="pb-md pl-lg pr-lg rel pt-md">
				<view class="s-item radius-20 fill-base flex-between" v-for="(item,index) in list.data" :key="index">
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
						<view class="flex-y-baseline mt-sm max-400">
							<text class="f-caption text-bold c-warning">￥</text>
							<text class="f-caption text-bold c-warning f-md-title">{{item.price}}</text>
							<text class="f-caption c-caption text-delete">￥{{item.package_price}}</text>
						</view>
						<view class="item-time flex-y-center f-icontext mt-sm" v-if="(item.end_time*1000 > new Date().getTime()) && (item.start_time*1000 < new Date().getTime())">
							<view class="item-time-left flex-center c-base">仅剩</view>
							<view class="item-time-text text-bold c-warning flex-center" :style="{minWidth: configInfo.isIos ? '174rpx' : '160rpx'}">
								<min-countdown :targetTime="item.end_time*1000" @callback="countEnd" :type="6"></min-countdown>
							</view>
						</view>
						<view class="flex-y-center f-icontext mt-sm c-base" v-else>
							<text class="item-time-disable pl-sm pr-sm" v-if="item.start_time*1000 > new Date().getTime()">活动未开始</text>
							<text class="item-time-disable pl-sm pr-sm" v-if="item.end_time*1000 < new Date().getTime()">活动已结束</text>
						</view>
						<view class="abs radius-10 item-btn flex-center f-desc c-warning" @tap="updateStatus(index, -1)">删除活动</view>
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
					limit: 10,
					store_id: ''
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
			this.param.store_id = options.store_id
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
					list: oldList
				} = this
				let newList = await this.$api.business.getSeckillList(this.param)
				if (this.param.page == 1) {
					this.list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList;
				}
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
			},
			async toSearch(val){
				clearTimeout(play)
				play = setTimeout(async ()=>{
					this.$util.showLoading()
					this.param.name = val
					this.getList(1)
				},1000)
			},
			async updateStatus(index, status){
				let {
					id
				} = this.list.data[index]
				let [res_del, {
					confirm
				}] = await uni.showModal({
					content: '是否确认删除'
				})
				if (!confirm) return;
				await this.$api.business.seckillEdit({id, status})
				this.$util.showToast({
					title: '删除成功'
				});
				this.list.data.splice(index , 1)
			}
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.$util.showLoading()
			this.param.page += 1
			await this.getList()
		},
	}
</script>

<style lang="scss">
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
			.item-time-disable{
				height: 34rpx;
				background: #ccc;
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
				width: 137rpx;
				height: 54rpx;
				bottom: 0;
				right: 0;
				border: 1px solid #FF2404;
			}
		}
		.c-78777B{
			color: #78777B;
		}
	}
</style>