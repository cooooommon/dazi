<template>
	<view>
		<scroll-view scroll-x class='tab-list' :scroll-into-view="'tab'+(activeIndex-1)" :scroll-with-animation="true"
			:style="{background: bgColor,fontSize: fontSize}">
			<view class='tab-item rel' v-for="(item,index) in list" :key="index" @tap='handerTabChange(index)'
				data-index="index" :id="'tab'+index"
				:style='{width,height,lineHeight:height,color:index==activeIndex?activeColor:color}'>
				<view class="flex-y-baseline flex-x-center rel">
					<view class="rel" :class="[index==activeIndex?curClass:'']" v-if="item.number">
						{{item.title || item}}
						<view v-if="numberType == 1" class="item-msg c-base abs" :class="[{'item-msg-radius': isRadius}]"
							:style="{width: item.number<10 ? '30rpx' :item.number<100 ? '40rpx' : '50rpx',right:item.number<10 ? '-34rpx' :item.number<100 ? '-44rpx' : '-54rpx' }">
							{{item.number < 100 ? item.number : '99+'}}
						</view>
						<view class="tab-label c-base abs"
							:style="{width: item.number<10 ? '24rpx' :item.number<100 ? '36rpx': '48rpx',right:item.number<10 ? '-14rpx' :item.number<100 ? '-26rpx' : '-38rpx'}"
							v-if="numberType == 2">
							{{item.number < 100 ? item.number : '99+'}}
						</view>
					</view>
					<block v-else>{{item.title || item}}</block>
					<block v-if="item.is_sign == 1">
						<view class="tab-item-sanjao abs" :style="{right: item.title.length == 3 ? '-20rpx' : ''}">
							<view class="up iconfont icon-up-fill rel"
								:style="{color:index==activeIndex && item.sign == 1?activeColor:'#ccc',}"></view>
							<view class="down iconfont icon-down-fill rel"
								:style="{color:index==activeIndex && item.sign == 0?activeColor:'#ccc',}"></view>
						</view>
					</block>
				</view>
				<view class="abs line" :class="[lineClass]" :style="{background: lineColor || activeColor}"
					v-if="isLine && index==activeIndex && !item.is_sign">
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	export default {
		name: 'tab',
		props: {
			list: {
				type: Array,
				default () {
					return []
				}
			},
			activeIndex: {
				type: Number,
				default () {
					return 0
				}
			},
			color: {
				type: String,
				default () {
					return '#333'
				}
			},
			activeColor: {
				type: String,
				default () {
					return '#e73535'
				}
			},
			lineColor: {
				type: String,
				default () {
					return ''
				}
			},
			bgColor: {
				type: String,
				default () {
					return '#fff'
				}
			},
			width: {
				type: String,
				default () {
					return ''
				}
			},
			height: {
				type: String,
				default () {
					return '100rpx'
				}
			},
			isLine: {
				type: Boolean,
				default () {
					return true
				}
			},
			lineClass: {
				type: String,
				default () {
					return ''
				}
			},
			curClass: {
				type: String,
				default () {
					return ''
				}
			},
			msgRight: {
				type: String,
				default () {
					return '5rpx'
				}
			},
			fontSize: {
				type: String,
				default () {
					return '30rpx'
				}
			},
			numberType: {
				type: Number,
				default () {
					return 1
				}
			},
			isRadius: {
				type: Boolean,
				default () {
					return false
				}
			}
		},
		created() {

		},
		data() {
			return {

			}
		},
		methods: {
			handerTabChange(index) {
				this.$emit('change', index);
			}
		},
	}
</script>

<style lang="scss">
	.tab-list {
		white-space: nowrap;
		background: #fff;
		width: 100%;

		.tab-item {
			display: inline-block;
			text-align: center;
			padding: 0 20rpx;
			border-color: #fff;
			box-sizing: border-box;

			.tab-label {
				width: 24rpx;
				height: 24rpx;
				font-size: 18rpx;
				text-align: center;
				line-height: 24rpx;
				border-radius: 24rpx;
				background: #F1381F;
				top: 12rpx;
				right: -14rpx;
			}

			.item-msg {
				width: 30rpx;
				height: 30rpx;
				font-size: 20rpx;
				text-align: center;
				line-height: 30rpx;
				border-radius: 15rpx 15rpx 15rpx 0;
				background: #f12c20;
				top: 10rpx;
				right: -34rpx;
			}
			.item-msg-radius{
				border-radius: 5000rpx !important;
			}

			.tab-item-sanjao {
				top: 18rpx;
				right: -6rpx;
				width: 30rpx;
				height: 50rpx;
				transform: scale(0.6);

				.iconfont {
					font-size: 28rpx;
				}

				.up {
					top: 0;
					left: 0;
				}

				.down {
					bottom: 10rpx;
					left: 0;
				}
			}

			.line {
				width: 80rpx;
				height: 6rpx;
				border-radius: 6rpx;
				left: 50%;
				bottom: 0rpx;
				margin-left: -40rpx;
			}

			.line.sm {
				width: 40rpx;
				height: 6rpx;
				border-radius: 10rpx;
				left: 50%;
				bottom: 0rpx;
				margin-left: -20rpx;
			}
			
			.line.mini {
				width: 20rpx;
				height: 6rpx;
				border-radius: 6rpx;
				left: 50%;
				bottom: 10rpx;
				margin-left: -10rpx;
			}
		}
	}
</style>
