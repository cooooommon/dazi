<template>
	<view :style="{background:pageColor}">
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

		<!-- #ifdef MP-WEIXIN -->
		<user-privacy ref="user_privacy" :show="false"></user-privacy>
		<!-- #endif -->
		<!-- #ifdef APP-PLUS -->
		<longbingbing-app-check-auth type="save" ref="app_check_item"
			@confirm="toConfirmPreviewSave"></longbingbing-app-check-auth>
		<longbingbing-preview-image ref="preview_image_item"></longbingbing-preview-image>
		<!-- #endif -->
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
				options: {},
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
			configInfo: state => state.config.configInfo,
			mineInfo: state => state.user.mineInfo,
		}),
		async onLoad(options) {
			this.options = options
			this.$util.showLoading()
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			let that = this
			setTimeout(() => {
				that.renderToCanvas()
			}, 1000)
			// #ifdef H5 
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
		},
		methods: {
			...mapActions(['getConfigInfo']),
			initRefresh() {
				this.src = ''
				this.$util.showLoading()
				this.renderToCanvas(true)
			},
			async renderToCanvas(refresh = false) {
				let that = this;
				let qr_code = await this.$api.mine.userCommQr({
					is_member: 1
				})
				let cover = 'https://lbqny.migugu.com/admin/anmo/memberdiscount/poster-share-img.png'
				let {
					avatarUrl = '',
					nickName = ''
				} = this.mineInfo
				nickName = nickName.length > 8 ? nickName.substring(0, 8) + '...' : nickName
				let qr_radius = '0rpx'
				// #ifdef MP-WEIXIN
				qr_radius = '135rpx'
				// #endif

				let poster = {
					css: {
						width: '750rpx',
						height: '1200rpx',
					},
					views: [{
							type: 'image',
							src: cover,
							css: {
								width: '750rpx',
								height: '1200rpx',
								objectFit: "cover",
								top: '0rpx',
								left: '0rpx',
								position: 'absolute'
							}
						},
						{
							type: 'view',
							css: {
								width: '750rpx',
								height: '110rpx',
								top: '424rpx',
								left: '0rpx',
								position: 'absolute'
							},
							views: [{
									type: 'image',
									src: avatarUrl,
									css: {
										position: 'absolute',
										width: '68rpx',
										height: '68rpx',
										objectFit: "cover",
										borderRadius: '34rpx',
										top: '0rpx',
										left: '341rpx'
									}
								},
								{
									type: 'text',
									text: `${nickName}邀请你领取福利`,
									css: {
										position: 'absolute',
										bottom: '0rpx',
										left: '0rpx',
										width: '750rpx',
										fontSize: '26rpx',
										color: '#F74153',
										textAlign: 'center'
									}
								}
							],
						},
						{
							type: 'image',
							src: qr_code,
							css: {
								position: 'absolute',
								width: '270rpx',
								height: '270rpx',
								top: '750rpx',
								left: '240rpx',
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
				// #ifdef APP-PLUS 
				if (plus.os.name == 'Android' && plus.navigator.checkPermission(
						'android.permission.WRITE_EXTERNAL_STORAGE') === 'undetermined') {
					this.$refs.preview_image_item
						.previewImage({
							current: finalPath,
							urls: [finalPath]
						})
				} else {
					this.$util.previewImage({
						current: finalPath,
						urls: [finalPath]
					})
				}
				// #endif
				// #ifndef APP-PLUS
				this.$util.previewImage({
					current: finalPath,
					urls: [finalPath]
				})
				// #endif
			},
			async saveImage() {
				// #ifdef MP-WEIXIN
				let privacyCheck = this.$refs.user_privacy.check()
				if (privacyCheck) {
					this.$refs.user_privacy.open()
					return
				}
				// #endif
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
				// #ifdef APP-PLUS
				if (plus.os.name == 'Android' && plus.navigator.checkPermission(
						'android.permission.WRITE_EXTERNAL_STORAGE') === 'undetermined') {
					this.$refs.app_check_item.open()
				} else {
					this.toConfirmPreviewSave()
				}
				// #endif
				// #ifndef APP-PLUS
				this.toConfirmPreviewSave()
				// #endif
			},
			toConfirmPreviewSave() {
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
		width: 750rpx;
		height: 1200rpx;
	}
</style>