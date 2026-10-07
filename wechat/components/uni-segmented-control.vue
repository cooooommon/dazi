<template>
	<view class="segmented-control" :class="styleType" :style="wrapStyle">
		<view v-for="(item, index) in values" class="segmented-control-item" :class="styleType" :key="index"
			:style="index === currentIndex ? activeStyle : itemStyle" @click="onClick(index)">
			{{item.title}}
		</view>
	</view>
</template>

<script>
	export default {
		name: 'uni-segmented-control',
		props: {
			current: {
				type: Number,
				default: 0
			},
			values: {
				type: Array,
				default () {
					return [];
				}
			},
			activeColor: {
				type: String,
				default: '#007aff'
			},
			styleType: {
				type: String,
				default: 'button'
			},
			haveBorder: {
				type: Boolean,
				default () {
					return false
				}
			},
			lockTap: {
				type: Boolean,
				default () {
					return true
				}
			}
		},
		data() {
			return {
				currentIndex: this.current
			}
		},
		watch: {
			current(val) {
				if (val !== this.currentIndex) {
					this.currentIndex = val;
				}
			}
		},
		computed: {
			wrapStyle() {
				let styleString = '';
				switch (this.styleType) {
					case 'text':
						styleString = `border:0;`;
						break;
					default:
						styleString = this.haveBorder ? `border: 1rpx solid ${this.activeColor};` : ``;
						break;
				}
				return styleString;
			},
			itemStyle() {
				let styleString = '';
				switch (this.styleType) {
					case 'text':
						styleString = `color:#000;border-left:0;`;
						break;
					default:
						styleString = `color:#222;background:#eddbba;border-color:#fff;`;
						break;
				}
				return styleString;
			},
			activeStyle() {
				let styleString = '';
				switch (this.styleType) {
					case 'text':
						styleString = `color:${this.activeColor};border-left:0;border-bottom-style:solid;`;
						break;
					default:
						styleString = `color:#fff;border-color:${this.activeColor};background-color:${this.activeColor}`;
						break;
				}
				return styleString;
			}
		},
		methods: {
			onClick(index) {
				let {
					lockTap,
					currentIndex
				} = this
				console.log(lockTap, currentIndex);
				if (currentIndex !== index || !lockTap) {
					this.currentIndex = index;
					this.$emit('clickItem', index);
				}
			}
		},
	}
</script>

<style>
	.segmented-control {
		display: flex;
		flex-direction: row;
		justify-content: center;
		font-size: 26rpx;
		border-radius: 5rpx;
		box-sizing: border-box;
		margin: 0 auto;
		overflow: hidden;
	}

	.segmented-control.button {
		border-radius: 54rpx;
		box-sizing: border-box;
	}

	.segmented-control.text {
		border: 0;
		border-radius: 0rpx;
	}


	.segmented-control-item {
		flex: 1;
		text-align: center;
		line-height: 54rpx;
		box-sizing: border-box;
	}

	.segmented-control-item.button {
		border-left: 1upx solid;
	}

	.segmented-control-item.button:first-child {
		border-radius: 54rpx 0 0 54rpx;
	}

	.segmented-control-item.button:last-child {
		border-radius: 0 54rpx 54rpx 0;
	}

	.segmented-control-item.text {
		border-left: 0;
	}

	.segmented-control-item:first-child {
		border-left-width: 0;
	}
</style>
