<template>
	<view class="rel">
		<view class="abs" style="z-index: 99;" :style="{top:statusBarHeight + 'px'}">
			<!-- #ifdef MP-WEIXIN -->
			<view @tap.stop="goBack" class="flex-center circle home-return-btn" v-if="!isShare"
				:class="[{'back-user-ios': configInfo.isIos},{'back-user-android': !configInfo.isIos}]">
				<view class="iconfont icon-left c-base text-bold" style="font-size: 40rpx;"></view>
			</view>
			<!-- #endif -->
			<!-- #ifdef H5 -->
			<view @tap="$util.goUrl({url:`/pages/service`,openType:`reLaunch`})"
				:class="[{'back-user-ios': configInfo.isIos},{'back-user-android': !configInfo.isIos}]" v-if="isShare">
				<view class="iconshouye iconfont"></view>
				<view class="back-user_text">回到首页</view>
			</view>
			<!-- #endif -->
		</view>
		<view class='banner'>
			<swiper class='banner-swiper' :style="{background: playVideo && !detail.video_vid ? '#000':'#f4f6f8'}"
				@change='handerSwiperChange' @transition="swiperTransition" :autoplay="playVideo ? false : true"
				:current="current">
				<swiper-item v-for="(item,index) in detail.images" :key="index" @tap='handerBannerClick(index)'>
					<block v-if="index == 0 && detail.video_url">
						<block v-if="!playVideo">
							<view @tap.stop="playCurrent"
								class="banner-swiper c-base iconfont icontushucxuanzebofangtiaozhuan abs flex-center"
								style="top: 0rpx;font-size: 80rpx;z-index: 9;"></view>
							<image mode="aspectFill" class='banner-img' :src='item'></image>
						</block>
						<!-- #ifdef MP-WEIXIN -->
						<txv-video :vid="detail.video_vid" :playerid="detail.video_vid" width="100%" height="100%"
							:controls="true" :autoplay="true" :isHiddenStop="true" v-if="playVideo && detail.video_vid">
						</txv-video>
						<!-- #endif -->
						<view class="video-box" v-if="playVideo && !detail.video_vid">
							<video :id="`video_id`" class="my-video" :loop="false" enable-play-gesture
								:enable-progress-gesture="false" :src="detail.video_url" :autoplay="playVideo"
								@play="onPlay" @pause="onPause" @ended="onEnded" @timeupdate="onTimeUpdate"
								@waiting="onWaiting" @progress="onProgress" @loadedmetadata="onLoadedMetaData"></video>
						</view>
					</block>
					<image mode="aspectFill" class='banner-img' :src='item' v-else></image>
				</swiper-item>
			</swiper>
			<view class='banner-tagitem banner-tagitem_count' v-if="!playVideo && detail.images.length">
				{{current+1}}/{{detail.images.length}}
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from "vuex"
	export default {
		props: {
			detail: {
				type: Object,
				default () {
					return {}
				}
			},
			isShare: {
				type: Boolean,
				default () {
					return false
				}
			},
			setCurrent: {
				type: Number,
				default () {
					return 0
				}
			}
		},
		watch: {
			'detail.images'(newValue, oldValue) {
				this.current = 0
			}
		},
		data() {
			return {
				statusBarHeight: uni.getSystemInfoSync().statusBarHeight,
				videoContexts: {},
				playVideo: false,
				current: 0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		created() {
			this.videoContexts = uni.createVideoContext(`video_id`, this)
		},
		methods: {
			handerSwiperChange(e) {
				let {
					current,
				} = e.detail
				this.current = current;
				this.videoContexts.pause()
				this.playVideo = false;
			},
			swiperTransition(e) {
				// // #ifdef H5
				// if(this.playVideo && this.current == 0 && e.detail.dx < 30){
				// 	this.current = 1
				// 	this.videoContexts.pause()
				// 	this.playVideo = false;
				// 	console.log(e,"======swiperTransition")
				// }
				// // #endif
			},
			playCurrent() {
				this.videoContexts.play()
				this.playVideo = true
			},
			onPlay(e) {},
			onPause(e) {},
			onEnded(e) {},
			onError(e) {},
			onTimeUpdate(e) {},
			onWaiting(e) {},
			onProgress(e) {},
			onLoadedMetaData(e) {},
			handerBannerClick(index) {
				let {
					image_url: url,
					video_url = ''
				} = this.detail
				if (index == 0 && video_url) {
					this.playVideo = true;
					return
				}
				if (!url) return
				this.$util.goUrl({
					openType: 'web', //this.configInfo.methodObj[jump_type],
					url
				})
			},
			goBack() {
				uni.navigateBack({
					delta: 1
				})
			},
		}
	}
</script>

<style>
	.home-return-btn {
		margin-top: 10rpx;
		margin-left: 24rpx;
		width: 60rpx;
		height: 60rpx;
		border: none;
		background-color: rgba(0, 0, 0, 0.3);
	}

	.video-box {
		position: relative;
		width: 100%;
		height: 500rpx;
	}

	.my-video {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 80%;
		align-items: center;
		margin-top: 120rpx;
	}

	.banner {
		position: relative;
	}

	.banner-swiper {
		width: 750rpx;
		height: 564rpx;
	}

	.banner-img {
		width: 100%;
		height: 100%;
	}

	.banner-taglist {
		display: flex;
		align-items: center;
		justify-content: center;
		position: absolute;
		bottom: 32rpx;
		width: 100%;
	}

	.banner-tagitem {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 90rpx;
		height: 42rpx;
		border-radius: 21rpx;
		background: rgba(255, 255, 255, 0.8);
		color: #2b2b2b;
		font-size: 22rpx;
		margin-left: 32rpx;
	}

	.banner-tagitem:nth-child(1) {
		margin-left: 0;
	}

	.banner-tagitem_count {
		background: rgba(0, 0, 0, 0.5);
		color: #fff;
		position: absolute;
		bottom: 32rpx;
		right: 32rpx;
		z-index: 10;
	}

	.banner-tagitem_active {
		background: #19c865;
		color: #fff;
	}
</style>
