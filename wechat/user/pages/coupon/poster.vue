<template>
	<view>
		<view class="hideCanvasView">
			<l-painter class="hideCanvas" ref="painter" useCORS />
		</view>
		<block v-if="src">
			<image :src="src" class="code-img" @tap="previewImage"></image>
			<view @tap.stop="toPreviewSave" class="save-btn flex-center f-title c-base radius"
				:style="{background:primaryColor}">{{confirmText}}</view>
			<view class="space-safe"></view>
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
				options: '',
				src: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		async onLoad(options) {
			this.options = options
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.widget = this.$refs.canvas
			this.$util.showLoading()
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			let that = this
			setTimeout(() => {
				that.canvase()
			}, 1000)
		},
		methods: {
			...mapActions(['user/report']),
			async canvase() {
				let that = this;
				let {
					nickName,
					avatarUrl
				} = this.userInfo

				let {
					qr_code,
					detail
				} = this.$util.getPage(-1)

				let {
					share_img
				} = detail 

				nickName = nickName.substring(0, 10)
				let width = nickName.length * 12 + 33

				let app_name = this.configInfo.app_name


				let poster = {
					css: {
						width: '652rpx',
						height: '966rpx',
						background: '#fff',
						borderRadius: '20rpx'
					},
					views: [{
						type: 'image',
						src: share_img,
						css: {
							width: '652rpx',
							height: '652rpx',
							objectFit: "cover",
							position: 'absolute',
							top: '0rpx',
							left: '0rpx',
							borderRadius: '20rpx 20rpx 0 0'
						}
					}, {
						type: 'text',
						text: nickName,
						css: {
							width,
							borderRadius: '27rpx',
							position: 'absolute',
							top: '30rpx',
							left: '67rpx',
							fontSize: '26rpx',
							background: '#555',
							color: '#fff',
							textAlign: 'center',
							padding: '7rpx 5rpx 12rpx 30rpx',
						}
					}, {
						type: 'image',
						src: avatarUrl,
						css: {
							width: '64rpx',
							height: '64rpx',
							position: 'absolute',
							top: '23rpx',
							left: '33rpx',
							borderRadius: '32rpx',
							border: '3rpx solid #fff'
						}
					}, {
						type: 'text',
						text: app_name,
						css: {
							position: 'absolute',
							bottom: '176rpx',
							left: '50rpx',
							fontSize: '28rpx',
							color: '#333',
							textAlign: 'left'
						}
					}, {
						type: 'text',
						text: `微信扫一扫立即使用`,
						css: {
							position: 'absolute',
							bottom: '122rpx',
							left: '50rpx',
							fontSize: '28rpx',
							color: '#333',
							textAlign: 'left'
						}
					}, {
						type: 'image',
						src: cover,
						css: {
							width: '242rpx',
							height: '242rpx',
							position: 'absolute',
							bottom: '40rpx',
							right: '45rpx',
							borderRadius: '121rpx'
						}
					}]
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
			async extraImage() {
				const p2 = this.widget.canvasToTempFilePath();
				p2.then(res => {
					this.src = res.tempFilePath
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
		width: 652rpx;
		height: 966rpx;
		margin: 20rpx auto;
		border-radius: 20rpx;
	}

	.save-btn {
		width: 490rpx;
		height: 80rpx;
		margin: 68rpx auto;
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
