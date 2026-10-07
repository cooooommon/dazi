<template>
	<view style="background-color: #F4F6F7;">
		<view class="hideCanvasView">
			<l-painter class="hideCanvas" ref="painter" useCORS />
		</view>
		<block v-if="src">
			<image :src="src" class="code-img" @tap="previewImage" ></image>
			<!-- <view class="info_text f_c">
				<view class="" style="font-size: 28rpx;margin-bottom:20rpx;color:#2F2F2F">
					邀请3步
				</view>
				<view class="f_r_sb_c" style="font-size: 28rpx;color:#767676">
					<view class="f_r_m_c">
						1.分享海报
					</view>
					<image mode="aspectFill" class="info_text_img" src="https://lbqny.migugu.com/admin/anmo/coupon/btn.png"></image>
					<view class="f_r_m_c">
						2.微信扫码
					</view>
					<image mode="aspectFill" class="info_text_img" src="https://lbqny.migugu.com/admin/anmo/coupon/btn.png"></image>
					<view class="f_r_m_c">
						3.注册登录
					</view>
				</view>
			</view> -->
			<!-- <view class="space-max-footer"></view>
			<fix-bottom-button @confirm="toPreviewSave" :text="[{text: confirmText,type:'confirm'}]" bgColor="#fff"
				:classType="2">
			</fix-bottom-button> -->
		</block>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions
	} from 'vuex';
	export default {
		components: {},
		props: {

		},
		data() {
			return {
				// #ifdef H5
				confirmText: '长按上图保存图片',
				// #endif
				// #ifndef H5
				confirmText: '保存图片至相册',
				// #endif
				src: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		async onLoad(options) {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			this.$util.showLoading()
			await this.getConfigInfo()
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			let that = this
			setTimeout(() => {
				that.canvase()
			}, 1000)
		},
		methods: {
			...mapActions(['getConfigInfo']),
			async canvase() {
				let that = this
				let {
					qr: qr_code,
					user_name
				} = await this.$api.mine.distribution()
				let {
					distribution_poster_img = ''
				} = this.configInfo
				let cover = distribution_poster_img || 'https://lbqny.migugu.com/admin/anmo/mine/fx-share-down.png'
				let qr_radius = '0rpx'
				// #ifdef MP-WEIXIN
				qr_radius = '145rpx'
				// #endif

				let poster = {
					css: {
						width: '750rpx',
						height: '1430rpx',
					},
					views: [{
							type: 'image',
							src: cover,
							css: {
								width: '750rpx',
								height: '1430rpx',
								objectFit: "cover",
								top: '0rpx',
								left: '0rpx',
								position: 'absolute'
							}
						},
						{
							type: 'text',
							text: `${user_name}正在邀请您加入团队，带你赚钱带你飞`,
							css: {
								position: 'absolute',
								top: '348rpx',
								left: '0rpx',
								width: '750rpx',
								fontSize: '30rpx',
								color: '#ffffff',
								textAlign: 'center'
							}
						},
						{
							type: 'image',
							src: qr_code,
							css: {
								position: 'absolute',
								width: '290rpx',
								height: '290rpx',
								bottom: '410rpx',
								left: '230rpx',
								background: '#ffffff',
								// borderRadius: qr_radius
							}
						}
					]
				}
				// 渲染
				this.$refs.painter.render(poster);
				// 生成图片
				this.$refs.painter.canvasToTempFilePathSync({
					fileType: "jpg",
					quality: 1,
					success: (res) => {
						that.$util.hideAll()
						this.src = res.tempFilePath
					},
				});
			},
			previewImage() {
				let finalPath = this.src;
				uni.previewImage({
					current: finalPath,
					urls: [finalPath]
				})
			},
			async saveImage() {
				await this.$util.checkAuth({
					type: "writePhotosAlbum"
				});
				let filePath = this.src;
				let [err, success] = await uni.saveImageToPhotosAlbum({
					filePath
				})
				if (err) return;
				uni.showToast({
					icon: 'none',
					title: '保存成功'
				})
			},
			toPreviewSave() {
				// #ifdef H5
				this.previewImage()
				// #endif
				// #ifndef H5
				this.saveImage()
				// #endif
			}
		}
	}
</script>

<style>
	.info_text {
		width: 750rpx;
		margin-left: 20rpx;
		background-color: #fff;
		padding: 20rpx 40rpx;
		box-sizing: border-box;
	}

	.info_text_img {
		width: 20rpx;
		height: 20rpx;
	}

	.code-img {
		width: 750rpx;
		height: 1430rpx;
	}

	.btns {
		display: flex;
		justify-content: center;
		padding: 15rpx;
		background-color: #fff;
		margin-top: 20rpx
	}

	.save-btn {
		width: 690rpx;
		height: 80rpx;
		line-height: 80rpx;
		margin: 0 auto;
	}

	.hideCanvasView {
		position: relative;
	}

	.hideCanvas {
		position: absolute;
		left: -9999rpx;
		top: -9999rpx
	}
</style>
