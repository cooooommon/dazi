<template>
	<view>
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
				options: {},
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
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
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
			...mapActions([]),
			async canvase() {
				let that = this;

				let {
					nickName,
				} = this.userInfo

				let {
					qr_code,
					shareInd,
					list
				} = this.$util.getPage(-1)

				let bg = 'https://lbqny.migugu.com/admin/anmo/mine/balance-text-bg.png'
				let cover = 'https://lbqny.migugu.com/admin/anmo/mine/balance-share.png'

				let {
					price = 0,
						true_price = 0
				} = list.data[shareInd]
				let reduce = true_price - price

				let poster = {
					css: {
						width: '750rpx',
						height: '1400rpx',
						background: '#fff'
					},
					views: [{
						type: 'text',
						text: `充${price}送${reduce}`,
						css: {
							position: 'absolute',
							top: '70rpx',
							left: '0rpx',
							width: '750rpx',
							fontSize: '70rpx',
							fontWeight: '1000',
							color: '#209620',
							textAlign: 'center'
						}
					}, {
						type: 'image',
						src: bg,
						css: {
							position: 'absolute',
							width: '484rpx',
							height: '46rpx',
							top: '190rpx',
							left: '132rpx'
						}
					}, {
						type: 'text',
						text: `充值优惠多多`,
						css: {
							position: 'absolute',
							top: '190rpx',
							left: '0rpx',
							width: '750rpx',
							fontSize: '34rpx',
							color: '#fff',
							fontWeight: '400',
							textAlign: 'center'
						}
					}, {
						type: 'image',
						src: cover,
						css: {
							width: '632rpx',
							height: '464rpx',
							objectFit: "cover",
							position: 'absolute',
							top: '360rpx',
							left: '58rpx'
						}
					}, {
						type: 'image',
						src: qr_code,
						css: {
							width: '290rpx',
							height: '290rpx',
							position: 'absolute',
							top: '920rpx',
							left: '230rpx'
						}
					}, {
						type: 'text',
						text: nickName,
						css: {
							position: 'absolute',
							top: '1235rpx',
							left: '0rpx',
							width: '750rpx',
							fontSize: '30rpx',
							color: '#209620',
							textAlign: 'center'
						}
					}, {
						type: 'text',
						text: `邀请你充值有优惠~`,
						css: {
							position: 'absolute',
							top: '1280rpx',
							left: '0rpx',
							width: '750rpx',
							fontSize: '28rpx',
							color: '#333',
							textAlign: 'center'
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
		},

	}
</script>

<style>
	.code-img {
		width: 750rpx;
		height: 1400rpx;
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
