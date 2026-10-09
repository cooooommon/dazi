<template>
	<view class="memberdiscount-share rel" :style="{background:'#fdcdc9'}">
		<image class="share-img abs" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/inv-user-poster.png`">
		</image>
		<view class="balance-text flex-center text-bold abs">
			<view class="flex-y-baseline font-num">
				<view style="font-size: 110rpx;">{{detail.balance || 0}}</view>
				<view style="font-size: 70rpx;">%</view>
			</view>
			<view class="ml-sm" style="font-size: 48rpx;">现金奖励</view>
		</view>
		<view class="inv-text text-bold c-base abs">
			<!-- #ifdef MP-WEIXIN -->
			<button open-type="share" class="clear-btn flex-center" style="font-size: 44rpx;color:#fff">立即邀请</button>
			<!-- #endif -->
			<!-- #ifdef H5 -->
			<view @tap.stop="$refs.show_oper_item.open()" class="flex-center">立即邀请</view>
			<!-- #endif -->
			<!-- #ifdef APP-PLUS -->
			<view @tap.stop="toAppShare" class="flex-center">立即邀请</view>
			<!-- #endif -->
		</view>
		<view class="share-text abs">
			<view class="flex-between">
				<view class="f-title">生成海报, 面对面邀请好友</view>
				<view @tap.stop="$util.goUrl({url:`/memberdiscount/pages/poster`})"
					class="code-btn flex-center f-mini-title text-bold radius">
					<image class="code-img" :src="`https://lbqny.migugu.com/admin/anmo/memberdiscount/code.png`">
					</image>
					海报邀请
				</view>
			</view>
		</view>

		<uni-popup ref="show_oper_item" type="top" :maskClick="false" :bgOpacity="0.6">
			<view class="pupup-oper-item">
				<view @tap.stop="$refs.show_oper_item.close()"
					class="close-btn fill-base flex-center mt-lg ml-lg f-paragraph c-title abs radius">
					跳过引导</view>
				<view class="flex-between" style="padding:0 92rpx;">
					<view></view>
					<image class="jiantou-img" :src="`https://lbqny.migugu.com/admin/anmo/alipay/jiantou.png`"></image>
				</view>
				<view class="oper-rule f-mini-title c-title rel">
					<image class="bg-img abs" :src="`https://lbqny.migugu.com/admin/anmo/alipay/bg.png`"></image>
					<image class="bg-icon abs" :src="`https://lbqny.migugu.com/admin/anmo/alipay/bg-icon.png`"></image>
					<view class="rel" style="z-index: 2;">
						<view class="flex-y-center" style="margin: 30rpx 0;">
							<view class="num flex-center mr-md f-sm-title c-base radius">1</view>
							点击右上角“<view class="dot"></view>
							<view class="dot md"></view>
							<view class="dot"></view>”按钮
						</view>
						<view class="flex-y-center">
							<view class="num flex-center mr-md f-sm-title c-base radius">2</view>
							选择“<i class="iconfont iconfenxianggeipengyou"
								style="color:#44A860;font-size: 46rpx;margin-right: 5rpx;"></i>
							转发给朋友”
						</view>
					</view>
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	import $util from "@/utils/index.js"
	export default {
		components: {},
		data() {
			return {
				detail: {}
			}
		},
		computed: mapState({
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			coachInfo: state => state.user.coachInfo,
		}),
		async onLoad() {
			await this.initIndex()
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
				id: coach_id = 0
			} = this.coachInfo
			let title = '限时特惠, 邀您购买会员卡'
			let imageUrl = 'https://lbqny.migugu.com/admin/anmo/memberdiscount/inv-share.png'
			let path = `/memberdiscount/pages/index?pid=${pid}&is_member=1`
			this.$util.log(path)
			return {
				title,
				imageUrl,
				path,
			}
		},
		methods: {
			...mapMutations([]),
			async initIndex(refresh = false) {
				this.detail = this.$util.getPage(-1).detail
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.toAppShare()
					})
				}
				// #endif
			},
			initRefresh() {
				this.initIndex(true)
			},
			toAppShare() {
				let {
					id: pid = 0
				} = this.userInfo
				let {
					id: coach_id = 0
				} = this.coachInfo
				let title = '限时特惠, 邀您购买会员卡'
				let summary = '权益多多, 快来看看吧~'
				let imageUrl = 'https://lbqny.migugu.com/admin/anmo/memberdiscount/inv-share.png'

				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/memberdiscount/pages/index?pid=${pid}&is_member=1`

				// #ifdef H5
				this.$jweixin.showOptionMenu()
				this.$jweixin.shareAppMessage(title, summary, href, imageUrl)
				this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				// #endif
				// #ifdef APP-PLUS 
				this.$refs.show_item.close()
				let item = {
					href,
					title,
					summary,
					imageUrl,
				}
				if (this.configInfo.isIos) {
					this.$util.showLoading()
					this.toTransImg(item)
				} else {
					this.sharePage(item)
				}
				// #endif
			},
			toTransImg(item) {
				let that = this
				let cur_unix = this.$util.DateToUnix(this.$util.formatTime(new Date(), 'YY-M-D h:m:s'))
				uni.downloadFile({
					url: item.imageUrl,
					success: (e) => {
						// 将png转换成jpg格式 只有jpg格式支持压缩api
						plus.zip.compressImage({
								src: e.tempFilePath,
								dst: `_doc/${cur_unix}.jpg`,
								format: "jpg"
							},
							response => {
								// 压缩图片
								plus.zip.compressImage({
									src: response.target,
									dst: `_doc/${cur_unix}-img.jpg`,
									quality: 50,
								}, res => {
									item.imageUrl = res.target || `/static/img/logo.png`
									that.sharePage(item)
								}, error => {
									item.imageUrl = `/static/img/logo.png`
									that.sharePage(item)
								})
							})
					}
				})
			},
			sharePage(item) {
				this.$util.hideAll()
				let {
					href,
					title,
					summary,
					imageUrl,
				} = item
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
			},
		}
	}
</script>


<style lang="scss">
	.memberdiscount-share {
		height: 100vh;
		.share-img {
			width: 100%;
			height: 1447rpx;
		}


		.balance-text {
			width: 100%;
			height: 140rpx;
			top: 600rpx;
			color: #DB060E;
		}

		.inv-text {
			width: 100%;
			height: 94rpx;
			font-size: 44rpx;
			top: 1016rpx;

			.flex-center {
				height: 94rpx;
			}
		}

		.share-text {
			width: 100%;
			height: 94rpx;
			top: 1140rpx;
			color: #E60026;
			padding: 0 56rpx;

			.code-btn {
				width: 240rpx;
				height: 76rpx;
				border: 2rpx solid #FD5059;

				.code-img {
					width: 39rpx;
					height: 39rpx;
					margin-right: 16rpx;
				}
			}

		}

		.pupup-oper-item {

			.close-btn {
				top: 30rpx;
				left: 0;
				width: 148rpx;
				height: 56rpx;
			}

			.jiantou-img {
				width: 206rpx;
				height: 268rpx;
			}

			.oper-rule {
				width: 630rpx;
				height: 346rpx;
				padding: 80rpx 60rpx;
				margin: 0 auto;

				.bg-img {
					left: 0;
					top: 0;
					width: 630rpx;
					height: 346rpx;
				}

				.bg-icon {
					top: -56rpx;
					right: -24rpx;
					width: 174rpx;
					height: 166rpx;
				}

				.num {
					width: 45rpx;
					height: 45rpx;
					background: #000000;
				}

				.dot {
					width: 8rpx;
					height: 8rpx;
					background: #000000;
					border-radius: 50%;
					margin: 0 3rpx;
				}

				.dot.md {
					width: 12rpx;
					height: 12rpx;
				}

				.map-img {
					width: 42rpx;
					height: 42rpx;
				}
			}
		}
	}
</style>