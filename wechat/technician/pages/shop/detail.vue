<template>
	<view class="shop" v-if="isLoad">
		<view class="shop-swiper">
			<shopBanner :detail="shopInfo" :isShare="isShare" />
		</view>
		<view>
			<view class="pd-lg fill-base flex-between rel" style="align-items: flex-end;">
				<view>
					<view class="f-sm-title text-bold c-black">{{shopInfo.name}}</view>
					<view class="pt-md c-warning" style="line-height: 1;">
						<text class="f-icontext">￥</text>
						<text class="f-sm-title text-bold">{{shopInfo.price}}</text>
					</view>
				</view>
				<!-- #ifdef MP-WEIXIN -->
				<button class="flex-center share-btn abs" open-type="share">
					<view class="iconfenxiang iconfont" style="font-size: 42rpx;" :style="{color:primaryColor}"></view>
					<text class="f-icontext c-caption" style="padding-left: 6rpx;">分享</text>
				</button>
				<!-- #endif -->
				<!-- #ifdef APP-PLUS -->
				<view class="flex-center share-btn abs" @tap.stop="toAppShare">
					<view class="iconfenxiang iconfont" style="font-size: 42rpx;" :style="{color:primaryColor}"></view>
					<text class="f-icontext c-caption" style="padding-left: 6rpx;">分享</text>
				</view>
				<!-- #endif -->
			</view>
			<view class="mt-md fill-base pl-lg pr-lg">
				<view class="f-mini-title c-black shop-box-title">商品详情</view>
				<view class="fill-base pt-lg pb-lg">
					<parser :html="shopInfo.desc" @linkpress="linkpress" show-with-animation lazy-load>加载中...</parser>
				</view>
				<!-- <rich-text :nodes="shopInfo.desc"></rich-text> -->
			</view>
		</view>
		<view class="space-max-footer"></view>
		<fix-bottom-button @confirm="$util.goUrl({url:shopInfo.phone ,openType:`call`})"
			:text="[{type:'confirm',text:'联系平台'}]" bgColor="#fff">
		</fix-bottom-button>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import parser from "@/components/jyf-Parser/index"
	import shopBanner from "@/components/shop-banner"
	import siteInfo from '@/siteinfo.js';
	export default {
		components: {
			parser,
			shopBanner
		},
		data() {
			return {
				isLoad: false,
				options: {},
				shopInfo: {},
				isShare: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			isGzhLogin: state => state.user.isGzhLogin,
		}),
		onLoad(options) {
			if (options.pid) {
				this.isShare = true
			}
			this.options = options
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh()
			uni.stopPullDownRefresh()
		},
		onShareAppMessage() {
			let {
				id: pid = 0
			} = this.userInfo
			let {
				id,
				name: title,
				cover: imageUrl
			} = this.shopInfo
			let path = `/technician/pages/shop/detail?id=${id}&pid=${pid}`
			return {
				title,
				imageUrl,
				path
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			initRefresh() {
				this.initIndex(true)
			},
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
				await this.goodsInfoCall()
				// #ifdef H5
				if (this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					// setTimeout(() => {
					// 	this.toAppShare()
					// }, 1200)
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
			},
			async goodsInfoCall() {
				this.$util.showLoading()
				let {
					id
				} = this.options
				this.shopInfo = await this.$api.technician.goodsInfo({
					id
				})
				this.isLoad = true
				this.$util.hideAll()
			},
			swiperChange(e) {},
			linkpress(res) {
				// #ifdef APP-PLUS
				if (/http/.test(res.href))
					this.$util.goUrl({
						url: res.href,
						openType: 'web'
					})
				// #endif
			},
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo
				let {
					id,
					name: title,
					cover: imageUrl
				} = this.shopInfo
				let summary = ''
				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/technician/pages/shop/detail?id=${id}&pid=${pid}`

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
			}
		}
	}
</script>

<style lang="scss">
	.shop {
		.shop-swiper {
			height: 564rpx;
		}

		.shop-box-title {
			line-height: 110rpx;
			height: 110rpx;
		}

		.shop-box-item {
			width: 100%;
			height: 388rpx;

			image {
				vertical-align: bottom;
				width: 100%;
				height: 100%;
			}
		}

		.share-btn {
			right: 30rpx;
			bottom: 30rpx;
			height: 42rpx;
		}
	}
</style>
