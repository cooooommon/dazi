<template>
	<view class="pages-home rel" v-if="detail.id">
		<!-- #ifdef H5 -->
		<view @tap="$util.goUrl({url:`/pages/service`,openType:`reLaunch`})" class="abs"
			:class="[{'back-user-ios': configInfo.isIos},{'back-user-android': !configInfo.isIos}]"
			style="margin-top:25rpx;z-index: 1;" v-if="options.pid">
			<view class="iconshouye iconfont"></view>
			<view class="back-user_text">回到首页</view>
		</view>
		<!-- #endif -->
		<banner :list="detail.imgs" :margin="0" :autoplay="true" :height="562" :indicatorActiveColor="primaryColor">
		</banner>
		<view class="fill-base pd-lg">
			<view class="flex">
				<view class="f-title c-title flex-1">{{detail.title}}</view>
				<view class="c-caption pl-lg">{{detail.total_sale}}人选择</view>
			</view>
			<view class="flex-between f-desc c-caption mt-sm">
				<view class="flex-y-baseline flex-1">
					<!-- <view class="text-delete mr-sm" v-if="detail.init_price">¥{{detail.init_price}}</view> -->
					<view class="f-md-title c-warning mr-sm">¥{{detail.price}} <text class="f-icontext c-warning">起</text></view>
					<view class="flex-center mem-price mr-sm" v-if="detail.member_price">
						<view class="rel mem-price-bg flex-center">
							<view class="abs f-ms-little c-base" style="padding-right: 2px;z-index: 2;">会员价</view>
							<image class="mem-price-img" src="https://lbqny.migugu.com/admin/member/member-bg.png" mode="aspectFill"></image>
						</view>
						<view class="mem-price-text text-bold f-ms-little">¥{{detail.member_price}}</view>
					</view>
				</view>
				<view class="">{{detail.time_long}}分钟</view>
			</view>
		</view>

		<view class="space-md"></view>
		<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1" :activeColor="primaryColor"
			width="50%" height="100rpx"></tab>
		<view class="space-md"></view>
		<view class="fill-base pd-lg f-paragraph c-desc">
			<view class="c-title text-bold mb-md">{{tabList[activeIndex].title}}</view>
			<parser :html="detail[rule[activeIndex]]" @linkpress="linkpress" show-with-animation lazy-load>加载中...
			</parser>
		</view>


		<view class="space-max-footer"></view>

		<fix-bottom-button @confirm="$util.goUrl({url:`/user/pages/choose-technician?id=${options.id}`})"
			:text="[{type:'confirm',text:'选择'+$t('action.attendantName'),isAuth:true}]" bgColor="#fff">
		</fix-bottom-button>

		<!-- #ifdef APP-PLUS -->
		<view @tap.stop="toAppShare"
			class="common-share-btn detail fix flex-center flex-column c-base box-shadow radius"
			:style="{background:primaryColor}" v-if="userInfo.id">
			<i class="iconfont iconweixin"></i>
			<view class="f-icontext">分享</view>
		</view>
		<view class="space-max-footer"></view>
		<!-- #endif -->

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
				activeIndex: 0,
				tabList: [{
					title: '项目介绍',
					id: 0
				},
				// {
				// 	title: '禁忌说明',
				// 	id: 1,
				// }, 
				{
					title: '下单须知',
					id: 2
				}],
				rule: {
					0: 'introduce',
					//1: 'explain',
					1: 'notice'
				},
				detail: {},
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			isGzhLogin: state => state.user.isGzhLogin,
		}),
		async onLoad(options) {
			this.$util.showLoading()
			this.options = await this.updateCommonOptions(options)
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onShareAppMessage(e) {
			let {
				id: pid = 0
			} = this.userInfo
			let {
				id,
				title,
				cover: imageUrl
			} = this.detail
			let path = `/user/pages/detail?pid=${pid}&id=${id}`
			this.$util.log(path)
			return {
				title,
				imageUrl,
				path,
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'updateCommonOptions']),
			...mapMutations([]),
			async initIndex(refresh = false) {
				let {
					pid = 0
				} = this.options
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
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				await this.getDetail()
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					setTimeout(() => {
						this.toAppShare()
					}, 1200)
				}
				// #endif
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.initIndex(true)
			},
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo
				let {
					id,
					title,
					cover: imageUrl
				} = this.detail
				let summary = ''

				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/user/pages/detail?id=${id}&pid=${pid}`

				// #ifdef H5
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, summary, href, imageUrl)
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
			async getDetail() {
				let {
					id
				} = this.options
				this.detail = await this.$api.service.serviceInfo({
					id
				});
				this.$util.hideAll()
			},
			handerTabChange(index) {
				this.activeIndex = index;
			},
			linkpress(res) {
				// #ifdef APP-PLUS
				this.$util.goUrl({
					url: res.href,
					openType: 'web'
				})
				// #endif
			},
		}
	}
</script>


<style lang="scss">
	.pages-home {
		.list-item {
			.item-btn {
				width: 129rpx;
				height: 54rpx;
			}
		}
		.mem-price{
			border: 1px solid #FF2D22;
			border-radius: 8rpx;
			margin-left: 4rpx;
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
	}
</style>
