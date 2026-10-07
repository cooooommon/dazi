<template>
	<view v-if="showPopup" class="uni-popup" :style="{top: top,zIndex: zIndex}">
		<view @click="close(true)" :class="[ani, animation ? 'ani' : '', !custom ? 'uni-custom' : '']"
			class="uni-popup__mask" :style="{zIndex: zIndex, background: `rgba(0, 0, 0, ${bgOpacity})`}" />
		<view @click="close(true)" :class="[type, ani, animation ? 'ani' : '', !custom ? 'uni-custom' : '']"
			class="uni-popup__wrapper" :style="{zIndex: zIndex+1}">
			<view class="uni-popup__wrapper-box" @click.stop="clear">
				<slot />
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'UniPopup',
		props: {
			// 开启动画
			animation: {
				type: Boolean,
				default: true
			},
			// 弹出层类型，可选值，top: 顶部弹出层；bottom：底部弹出层；center：全屏弹出层
			type: {
				type: String,
				default: 'center'
			},
			// 是否开启自定义
			custom: {
				type: Boolean,
				default: false
			},
			maskClick: {
				type: Boolean,
				default: true
			},
			show: {
				type: Boolean,
				default: true
			},
			top: {
				type: String,
				default: '0px'
			},
			zIndex: {
				type: [Number],
				default () {
					return 998
				}
			},
			bgOpacity: {
				type: [Number],
				default () {
					return 0.4
				}
			}
		},
		data() {
			return {
				ani: '',
				showPopup: false
			}
		},
		watch: {
			show(newValue) {
				if (newValue) {
					this.open()
				} else {
					this.close()
				}
			}
		},
		created() {},
		methods: {
			clear() {},
			open() {
				this.$emit('change', {
					show: true
				})
				this.showPopup = true
				this.$nextTick(() => {
					setTimeout(() => {
						this.ani = 'uni-' + this.type
					}, 30)
				})
			},
			close(type) {
				if (!this.maskClick && type) return
				this.$emit('change', {
					show: false
				})
				this.ani = ''
				this.$nextTick(() => {
					setTimeout(() => {
						this.showPopup = false
					}, 300)
				})
			}
		}
	}
</script>
<style lang="scss">
	@charset "UTF-8";

	.uni-popup {
		position: fixed;
		top: 0;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		overflow: hidden
	}

	.uni-popup__mask {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		opacity: 0
	}

	.uni-popup__mask.ani {
		transition: all .3s
	}

	.uni-popup__mask.uni-bottom,
	.uni-popup__mask.uni-center,
	.uni-popup__mask.uni-top {
		opacity: 1
	}

	.uni-popup__wrapper {
		position: absolute;
		box-sizing: border-box
	}

	.uni-popup__wrapper.ani {
		transition: all .3s
	}

	.uni-popup__wrapper.top {
		top: 0;
		left: 0;
		width: 100%;
		transform: translateY(-100%)
	}

	.uni-popup__wrapper.bottom {
		bottom: 0;
		left: 0;
		width: 100%;
		transform: translateY(100%)
	}

	.uni-popup__wrapper.center {
		width: 100%;
		height: 100%;
		display: flex;
		justify-content: center;
		align-items: center;
		transform: scale(1.2);
		opacity: 0
	}

	.uni-popup__wrapper-box {
		position: relative;
		box-sizing: border-box
	}

	.uni-popup__wrapper.uni-custom .uni-popup__wrapper-box {
		/* padding: 30upx; */
		// background: #fff
	}

	.uni-popup__wrapper.uni-custom.center .uni-popup__wrapper-box {
		position: relative;
		/* 		max-width: 80%;
		max-height: 80%; */
		overflow-y: scroll;
		border-radius: 25rpx;
	}

	.uni-popup__wrapper.uni-custom.bottom .uni-popup__wrapper-box {
		width: 100%;
		// max-height: 500px;
		overflow-y: scroll;
		border-radius: 25rpx 25rpx 0 0;
	}

	.uni-popup__wrapper.uni-custom.top .uni-popup__wrapper-box {
		width: 100%;
		// max-height: 500px;
		overflow-y: scroll;
		border-radius: 0 0 25rpx 25rpx;
	}

	.uni-popup__wrapper.uni-bottom,
	.uni-popup__wrapper.uni-top {
		transform: translateY(0)
	}

	.uni-popup__wrapper.uni-center {
		transform: scale(1);
		opacity: 1
	}
</style>
