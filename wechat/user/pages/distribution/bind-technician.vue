<template>
	<view style="background-color: #F4F6F7;padding:20rpx 0">
		<view class="hideCanvasView">
			<l-painter class="hideCanvas" ref="painter" useCORS />
		</view>
		<block v-if="src">
			<image :src="src" class="code-img" @tap="previewImage"></image>
			<view class="space-max-footer"></view>
			<fix-bottom-button @confirm="toPreviewSave" :text="[{text: confirmText,type:'confirm'}]" bgColor="#fff"
				:classType="2">
			</fix-bottom-button>
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
			uni.setNavigationBarTitle({
				title: '邀请绑定' + this.$t('action.attendantName')
			})
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			let that = this
			setTimeout(() => {
				that.renderToCanvas()
			}, 1000)
		},
		methods: {
			...mapActions(['getConfigInfo']),
			async renderToCanvas() {
				let that = this;
				let qr_code = await this.$api.mine.adminCoachQr()

				let {
					bind_technician_img = ''
				} = this.configInfo

				let cover = bind_technician_img || 'https://lbqnyv2.migugu.com/bianzu18.png'

				let {
					nickName,
					avatarUrl
				} = this.userInfo
				let qr_radius = '0rpx'
				// #ifdef MP-WEIXIN
				qr_radius = '145rpx'
				// #endif

				let poster = {
					css: {
						width: '710rpx',
						height: '1278rpx',
					},
					views: [{
							type: 'image',
							src: cover,
							css: {
								width: '710rpx',
								height: '1138rpx',
								objectFit: "cover",
								top: '0rpx',
								left: '0rpx',
								position: 'absolute'
							}
						},
						{
							type: 'view',
							css: {
								background: '#fff',
								width: '710rpx',
								height: '140rpx',
								bottom: '0rpx',
								left: '0rpx',
								position: 'absolute'
							},
							views: [{
									type: 'image',
									src: avatarUrl,
									css: {
										position: 'absolute',
										width: '94rpx',
										height: '94rpx',
										borderRadius: '50rpx',
										bottom: '23rpx',
										left: '20rpx'
									}
								},
								{
									type: 'text',
									text: nickName,
									css: {
										position: 'absolute',
										bottom: '70rpx',
										left: '130rpx',
										width: '350rpx',
										fontSize: '32rpx',
										fontWeight: '300',
										color: '#0C0C0C'
									}
								},
								{
									type: 'text',
									text: `邀请你成为${this.$t('action.attendantName')} 快快扫码加盟我们吧!`,
									css: {
										position: 'absolute',
										bottom: '25rpx',
										left: '130rpx',
										width: '750rpx',
										fontSize: '26rpx',
										color: '#999999',
									}
								}
							],
						},
						{
							type: 'image',
							src: qr_code,
							css: {
								position: 'absolute',
								width: '290rpx',
								height: '290rpx',
								bottom: '366rpx',
								left: '210rpx',
								background: '#ffffff',
								borderRadius: qr_radius
							}
						},

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
	.code-img {
		width: 710rpx;
		height: 1278rpx;
		margin-left: 20rpx;
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
