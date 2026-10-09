<template>
	<view class="user-app-download" v-if="isLoad">
		<block v-if="detail.app_download_img">
			<image mode="widthFix" class="web-code-img" :src="detail.app_download_img">
			</image>
			<block v-if="(detail.ios_link && isIos) || (detail.android_link && !isIos)">
				<view class="download-tip fix" v-if="isWechatAgent">
					<image mode="widthFix" class="tip-img fix" src="https://lbqny.migugu.com/admin/anmo/mine/tip.png">
					</image>
				</view>
				<view style="height: 200rpx;"></view>
				<view class="space-safe"></view>
				<view @tap.stop="toDownLoad" class="fix footer-btn">
					<image mode="aspectFill" class="download-bg abs" src="https://lbqny.migugu.com/admin/anmo/mine/download-btn.png?1">
					</image>
					<view class="flex-center f-md-title c-base">
						<view class="item-btn flex-center radius" :style="{background:detail.primaryColor}">
							<i class="iconfont" :class="[{'icon-apple':isIos},{'icon-android':!isIos}]"></i>
							<view>立即下载</view>
						</view>
					</view>
				</view>
			</block>
		</block>
		<abnor v-if="!detail.app_download_img"></abnor>
	</view>
</template>

<script>

	import { getSystemInfo } from '@/utils/systemInfo.js';
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				isLoad: false,
				options: {},
				isIos: getSystemInfo().system.includes('iOS'),
				isWechatAgent: true,
				detail: {},
			}
		},
		computed: mapState({}),
		async onLoad() {
			await this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initIndex()
			uni.stopPullDownRefresh()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'updateCommonOptions']),
			...mapMutations(['updateConfigItem']),
			async initIndex() {
				// #ifdef H5
				this.isWechatAgent = await this.$jweixin.isWechat()
				// #endif
				this.detail = await this.$api.base.getConfig()
				this.$util.setNavigationBarColor({
					bg: this.detail.primaryColor
				})
				this.$util.hideAll()
				this.isLoad = true
			},
			toDownLoad() {
				let key = this.isIos ? 'ios_link' : 'android_link'
				let url = this.detail[key]
				window.location.href = url
			}
		}
	}
</script>


<style lang="scss">
	page {
		background: #FEFFFE;
	}

	.user-app-download {
		.web-code-img {
			width: 750rpx;
		}

		.download-tip {
			top: 0;
			left: 0;
			width: 100%;
			height: 100vh;
			z-index: 1000;
			background: rgba(000, 000, 000, 0.6);

			.tip-img {
				top: 30rpx;
				right: 30rpx;
				width: 500rpx;
			}
		}


		.footer-btn {
			width: 750rpx;
			height: 500rpx;
			bottom: 0;

			.download-bg {
				width: 750rpx;
				height: 500rpx;
				z-index: -1;
			}

			.item-btn {
				width: 640rpx;
				height: 100rpx;
				margin-top: 260rpx;

				.iconfont {
					font-size: 50rpx;
					margin-right: 10rpx;
				}
			}

		}
	}
</style>
