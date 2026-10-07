<template>
	<view class="user-coupon-share" v-if="isLoad">
		<!-- #ifdef H5 -->
		<view @tap="$util.goUrl({url:`/pages/service`,openType:`reLaunch`})" class="abs"
			:class="[{'back-user-ios none': configInfo.isIos},{'back-user-android none': !configInfo.isIos}]"
			style="z-index: 1;right:30rpx;" v-if="options.pid">
			<view class="iconshouye iconfont"></view>
			<view class="back-user_text">回到首页</view>
		</view>
		<!-- #endif -->
		<image mode="aspectFill" class="coupon-img" :src="shareImg"></image>

		<view class="coupon-content">
			<view class="flex-center flex-column">
				<view class="flex-center f-paragraph pt-lg pb-md" :style="{color:primaryColor}">
					{{detail.status == 1 ? '距活动结束还剩':'活动已结束'}}
				</view>
				<view class="count-down">
					<countdown type="2" :targetTime="over_time_text" @callback="countEnd">
					</countdown>
				</view>
				<view class="space-lg"></view>
				<view class="space-lg"></view>
				<!-- status 1进行中，2成功，3失败 -->
				<view class="ml-lg mr-lg pt-lg pb-sm pl-lg pr-lg rel radius-20" style="background: #FFFAF4;">
					<view class="menu-img mt-lg abs">
						<image mode="aspectFill" class="menu-img" src="https://lbqny.migugu.com/admin/anmo/coupon/menu.png"></image>
						<view class="menu-title flex-center abs" :style="{color:primaryColor}">邀请记录</view>
					</view>
					<view class="space-lg"></view>
					<view class="space-lg"></view>
					<view class="flex-center f-desc pb-md" :style="{color:primaryColor}">
						<view class="user-item">用户</view>
						<view class="time-item">时间</view>
					</view>
					<image mode="aspectFill" class="line-img" src="https://lbqny.migugu.com/admin/anmo/coupon/line.png"></image>
					<scroll-view scroll-y class="user-list-info" v-if="record_list.data.length > 0">
						<view class="user-list flex-between f-desc c-title b-1px-b"
							v-for="(item,index) in record_list.data" :key="index">
							<view class="flex-y-center pt-md pb-md">
								<image mode="aspectFill" class="avatar radius" :src="item.avatarUrl"></image>
								<view class="ml-md max-300 ellipsis">{{item.nickName || `用户${item.id}`}}</view>
							</view>
							<view>{{item.create_time}}</view>
						</view>
					</scroll-view>
					<abnor percent="70%" v-if="record_list.data.length == 0"></abnor>
					<view class="space-sm"></view>


					<!-- #ifndef H5 -->
					<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
						:type="!userInfo.phone ? 'phone':'userInfo'" @go="toShare" v-if="detail.status == 1">
						<view class="share-img mt-lg rel">
							<image mode="aspectFill" class="share-img" src="https://lbqny.migugu.com/admin/anmo/coupon/btn.png"></image>
							<view class="share-title flex-center abs" :style="{color:primaryColor}">
								{{detail.user_id == userInfo.id?'邀请有奖': userInfo.nickName ?'帮TA邀请':'我要参加'}}
							</view>
						</view>
					</auth>

					<view class="share-img grayscale mt-lg rel" v-else>
						<image mode="aspectFill" class="share-img" src="https://lbqny.migugu.com/admin/anmo/coupon/btn.png"></image>
						<view class="share-title flex-center abs" :style="{color:primaryColor}">
							{{detail.user_id == userInfo.id?'邀请有奖': userInfo.nickName ?'帮TA邀请':'我要参加'}}
						</view>
					</view>
					<!-- #endif -->

				</view>
				<view class="mt-md ml-lg mr-lg pt-lg pb-lg pl-lg pr-lg rel radius-20" style="background: #FFFAF4;"
					v-if="detail.id">
					<view class="atv-rule pd-md radius-16" :style="{border: `1px solid ${primaryColor}`}">
						<view class="flex-center f-paragraph pt-sm pb-md" :style="{color:primaryColor}">活动规则</view>
						<view class="f-desc c-desc">1、活动总计可发起{{detail.atv_num}}次；</view>
						<view class="f-desc c-desc" v-if="detail.to_inv_user == 1">2、被推荐人授权用户信息后即可获得相应奖励；</view>
						<view class="f-desc c-desc">
							{{detail.to_inv_user == 1?3:2}}、活动发起人每成功邀请{{detail.inv_user_num}}位好友授权用户信息后，即可获得以下卡券：
						</view>
						<view class="flex-warp f-desc c-desc" v-for="(item,index) in detail.coupon" :key="index">
							<view>（{{index*1+1}}）卡券：{{item.title}}；数量：x{{item.num}}</view>
						</view>
						<view class="space-md"></view>
					</view>
				</view>
				<view class="space-footer"></view>
			</view>
		</view>

		<uni-popup ref="show_share_item" type="bottom">
			<view class="popup-share pd-lg f-desc c-desc fill-base">
				<view class="flex-center">
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="share" class="clear-btn list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/wechat.png"></image>
						<view style="font-size: 26rpx;color:#666;">分享给好友</view>
					</button>
					<view @tap.stop="toPoster" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/picture.png"></image>
						<view>生成海报码</view>
					</view>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<view @tap.stop="toAppShare(1)" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/wechat.png"></image>
						<view style="font-size: 26rpx;color:#666;">分享给好友</view>
					</view>
					<view @tap.stop="toAppShare(2)" class="list-item flex-center flex-column">
						<image mode="aspectFill" class="item-image" src="https://lbqny.migugu.com/admin/anmo/coupon/wechat-moments.png"></image>
						<view>分享到朋友圈</view>
					</view>
					<!-- #endif -->
				</view>
				<view class="space-footer"></view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import countdown from '@/components/min-countdown.vue'
	import siteInfo from '@/siteinfo.js';
	export default {
		components: {
			countdown
		},
		data() {
			return {
				shareImg: 'https://lbqny.migugu.com/admin/anmo/coupon/bg.png',
				isLoad: false,
				options: {},
				detail: {
					end_time: ''
				},
				record_list: {},
				qr_code: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
			isGzhLogin: state => state.user.isGzhLogin,
			over_time_text() {
				return new Date().getTime() + this.detail.end_time * 1000
			}
		}),
		async onLoad(options) {
			this.$util.showLoading()
			options = await this.updateCommonOptions(options)
			this.options = options
			// #ifdef MP-WEIXIN
			uni.hideShareMenu()
			// #endif
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
				id,
				share_img: imageUrl
			} = this.detail
			let {
				id: pid = 0
			} = this.userInfo
			let path =
				`/user/pages/coupon/share?pid=${pid}&coupon_atv_id=${id}`
			this.$util.log(path)
			this.$refs.show_share_item.close()
			return {
				title: '',
				imageUrl,
				path,
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'updateCommonOptions']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				let {
					id: uid = 0
				} = this.userInfo
				let {
					isGzhLogin
				} = this
				// #ifdef H5
				if (isGzhLogin && !uid) {
					setTimeout(() => {
						this.getDetail()
					}, 1000)
				} else {
					this.getDetail()
				}
				// #endif
				// #ifndef H5
				await this.getDetail()
				// #endif
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
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
				this.initIndex(true)
			},
			async getDetail() {
				let {
					coupon_atv_id = 0
				} = this.options
				let {
					record_list,
					atv_info
				} = await this.$api.mine.couponAtvInfo({
					id: coupon_atv_id
				})
				if (atv_info.status !== 1) {
					atv_info.end_time = 0
				}
				this.detail = atv_info
				this.record_list = record_list
				this.isLoad = true
				this.$util.hideAll()
			},
			countEnd() {
				this.$util.log("倒计时完了")
				setTimeout(() => {
					this.initRefresh()
					this.$util.back()
				}, 1000)
			},
			async toShare() {
				if (this.detail.status != 1) return
				// #ifndef APP-PLUS
				if (!this.qr_code) {
					this.$util.showLoading()
					let {
						id: coupon_atv_id
					} = this.detail
					let qr_code = await this.$api.mine.atvQr({
						page: 'user/pages/coupon/share',
						coupon_atv_id
					})
					this.$util.hideAll()
					this.qr_code = qr_code
				}
				// #endif
				this.$refs.show_share_item.open()
				if (this.detail.user_id == this.userInfo.id) return
				this.getDetail()
			},
			toPoster() {
				this.$refs.show_share_item.close()
				this.$util.goUrl({
					url: `/user/pages/coupon/poster`
				})
			},
			toAppShare(type = 1) {
				let {
					id,
					share_img: imageUrl,
					status
				} = this.detail
				let {
					id: pid = 0
				} = this.userInfo
				let methodModel = status * 1 == 1 ? 'showOptionMenu' : 'hideOptionMenu'
				let title = '邀请有礼'
				let summary = '邀请新人 获得福利'

				let {
					siteroot
				} = siteInfo
				let url = siteroot.split('/index.php')[0]
				let href = `${url}/h5/#/user/pages/coupon/share?coupon_atv_id=${id}&pid=${pid}`

				// #ifdef H5
				this.$jweixin.wxReady(() => {
					this.$jweixin[methodModel]()
					this.$jweixin.shareAppMessage(title, summary, href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
				// #endif
				// #ifdef APP-PLUS
				this.$refs.show_share_item.close()
				let scene = type == 1 ? 'WXSceneSession' : 'WXSceneTimeline'
				uni.share({
					provider: "weixin",
					scene,
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
		}
	}
</script>


<style lang="scss">
	.user-coupon-share {
		.coupon-img {
			width: 750rpx;
			height: 560rpx;
		}

		.coupon-content {
			width: 750rpx;
			min-height: 1431rpx;
			background: linear-gradient(0deg, #A40035 0%, #FFAE89 100%);

			.count-down {
				height: 56rpx;
			}

			.menu-img {
				width: 426rpx;
				height: 100rpx;
			}

			.menu-img.abs {
				top: -56rpx;
				left: 50%;
				margin-left: -213rpx;
			}

			.menu-title {
				width: 426rpx;
				height: 90rpx;
				font-size: 36rpx;
				top: 0;
			}

			.line-img {
				width: 630rpx;
				height: 1rpx;
			}

			.user-item {
				width: 360rpx;
			}

			.time-item {
				width: 220rpx;
			}

			.user-list-info {
				/* #ifdef H5 */
				height: 746rpx;
				/* #endif */
				/* #ifndef H5 */
				height: 576rpx;
				/* #endif */

				.user-list {
					.avatar {
						width: 56rpx;
						height: 56rpx;
						background: #FFAE89;
					}
				}

			}


			.share-img {
				width: 541rpx;
				height: 140rpx;
				margin: 0 auto;
			}

			.share-title {
				width: 541rpx;
				height: 125rpx;
				font-size: 36rpx;
				top: 0;
			}

			.atv-rule {
				transform: rotateZ(360deg);
			}

		}

		.popup-share {
			.list-item {
				width: 50%;

				.item-image {
					width: 66rpx;
					height: 66rpx;
				}
			}
		}

	}
</style>
