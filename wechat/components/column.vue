<template>
	<view>
		<!-- 轮播图 -->
		<view v-if="type=='swiper'" class='column-box' :style='{borderRadius:`${borderRadius}rpx`,paddingTop:whiteSpace+"rpx",paddingBottom:whiteSpace+"rpx",paddingLeft:wingBlank+"rpx",paddingRight:wingBlank+"rpx"}'>
			<swiper class="swiper-category" @change="handerChange" :style="{height:(146*formatRowNum+20*(formatRowNum-1))+'rpx'}">
				<swiper-item class="swiper-category-item" v-for="(pitem,pindex) in formatList" :key="pindex">
					<view v-for="(item,index) in pitem" :key="index" class="column-item" :style="{width:100/colNum + '%',marginTop:index<colNum? '0' : '20rpx'}"
					 @tap='change(item)'>
						<image mode="aspectFill" v-if="item.icon" class="column-img" :src="item.icon"></image>
						<view v-else class="column-no-img" :style="{background:colorList[index%8],borderRadius:'50%',margin:'0rpx auto'}">
							{{getFirstText(item.cate_name)}}
						</view>
						<view class='column-text'>{{item.cate_name}}</view>
					</view>
				</swiper-item>
			</swiper>
			<view class='dots' v-if="formatList.length>1">
				<view class='dot' v-for="(item,index) in formatList.length" :key="index" 
				:class="[{'pl-sm pr-sm':index==current}]"
				:style='{background:index==current?indicatorActiveColor:indicatorColor, width: dotStyle.width,height: dotStyle.height}'></view>
			</view>
		</view>


		<!-- 滑动 -->
		<view v-if="type=='scroll'" class='column-box' :style='{paddingTop:whiteSpace+"rpx",paddingBottom:whiteSpace+"rpx",paddingLeft:wingBlank+"rpx",paddingRight:wingBlank+"rpx"}'>
			<scroll-view scroll-x class='scroll-x' @scroll="handerScroll">
				<view class='scroll-x-item' v-for="(pItem,pindex) in formatList" :key="pindex">
					<view class='column-item' v-for="(item,index) in pItem" :key="index" :style='{width:(750/colNum)+"rpx"}' @tap='change(item)'>
						<image mode="aspectFill" class='column-img' :src='item.icon'></image>
						<view class='column-text'>{{item.cate_name}}</view>
					</view>
				</view>
			</scroll-view>
			<view class='ink-bar-box' v-if="list.length>formatRowNum*colNum">
				<view class="ui-tabs-ink-bar-wrapper">
					<view class="ui-tabs-ink-bar" :style='{left:left+"rpx",background:indicatorActiveColor}'></view>
				</view>
			</view>
		</view>

	</view>




</template>

<script>

	import { getSystemInfo } from '@/utils/systemInfo.js';
	export default {
		name: 'column',
		props: {
			type: {
				type: String,
				default () {
					return "swiper"
				}
			},
			list: {
				type: Array,
				default () {
					return []
				}
			},
			colNum: {
				type: Number,
				default () {
					return 4
				}
			},
			rowNum: {
				type: Number,
				default () {
					return 2
				}
			},
			indicatorActiveColor: {
				type: String,
				default () {
					return '#f6f5fa'
				}
			},
			indicatorColor: {
				type: String,
				default () {
					return '#f6f5fa'
				}
			},
			wingBlank: {
				type: Number,
				default () {
					return 0
				}
			},
			whiteSpace: {
				type: Number,
				default () {
					return 30
				}
			},
			borderRadius: {
				type: Number,
				default () {
					return 0
				}
			},
			dotStyle: {
				type: Object,
				default () {
					return {
						height: '16rpx',
						width: '16rpx'
					}
				}
			}
		},
		created() {

		},
		data() {
			return {
				activeIndex: 0,
				newList: [],
				left: 0,
				current: 0,
				colorList: ["#fc7f87", "#56b4fc", "#f8ae41", "#11dd9e", "#fba745", "#fb7c85", "#46adfc", "#fc7d86"]
			}
		},
		computed: {
			formatRowNum() {
				let {
					colNum,
					rowNum,
					list
				} = this;
				let length = list.length;
				let newRowNum = length <= colNum ? 1 : rowNum;
				return newRowNum
			},
			formatList() {
				let {
					colNum,
					rowNum,
					type,
					list,
					formatRowNum
				} = this;


				let index = 0;
				let length = list.length;
				let newList = [];

				let count = formatRowNum * colNum
				// let count = type == 'scroll' ? colNum : formatRowNum * colNum
				while (index < length) {
					newList.push(list.slice(index, index += count));
				}
				return newList
			},
		},
		methods: {
			handerScroll: function(e) {
				let {
					scrollLeft,
					scrollWidth
				} = e.detail;
				let windowWidth = getSystemInfo().windowWidth;
				let left = scrollLeft * 30 / (scrollWidth - windowWidth);
				this.left = left
			},
			handerChange: function(e) {
				this.current = e.detail.current;
			},
			change(item) {
				this.$emit("change", item)
			},
			getFirstText(d) {
				d = d || '名称'
				return d.slice(0, 1)
			},
		},
	}
</script>


<style lang="scss">
	/* 轮播图 */
	.column-box {
		background: #fff;
		font-size: 24rpx;
		color: #888;
		overflow: hidden;

		.swiper-category {
			width: 100%;
			background: #fff;
			font-size: 24rpx;
			color: #666;
			border-radius: 10rpx;

			.swiper-category-item {
				box-sizing: border-box;
				display: flex;
				align-items: flex-start;
				flex-wrap: wrap;
				background: #fff;


				.column-item {
					margin-top: 20rpx;
					float: left;


					.column-img {
						width: 100rpx;
						height: 100rpx;
						margin: 0 auto;
					}

					.column-no-img {
						width: 100rpx;
						height: 100rpx;
						border-radius: 50%;
						display: flex;
						align-items: center;
						justify-content: center;
						font-size: 36rpx;
						color: #fff;
					}

					.column-text {
						margin-top: 10rpx;
						text-align: center;
						color: #454A53;
						width: 100%;
						padding: 0 10%;
						display: block;
						overflow: hidden;
						white-space: nowrap;
						text-overflow: ellipsis;
					}

				}
			}
		}

		.scroll-x {
			white-space: nowrap;
			background: #fff;
			padding: 20rpx 0;

			.scroll-x-item {
				position: relative;
				display: inline-block;
				vertical-align: text-top;
				display: flex;
				flex-direction: column;
			}

			.column-item {
				margin-top: 20rpx;
				float: left;


				.column-img {
					width: 100rpx;
					height: 100rpx;
					margin: 0 auto;
				}

				.column-text {
					margin-top: 10rpx;
					text-align: center;
					color: #454A53;
					width: 100%;
					padding: 0 10%;
					display: block;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
				}

			}
		}


		.dots {
			background: white;
			display: flex;
			align-items: center;
			justify-content: center;
			width: 100%;
			padding-top: 20rpx;

			.dot {
				height: 16rpx;
				width: 16rpx;
				background: #ddd;
				border-radius: 8rpx;
				margin: 0 8rpx;
			}
			
			.dotCustom{
				width: 20rpx;
			}
		}

		.ink-bar-box {
			background: #fff;
			padding-bottom: 20rpx;

			.ui-tabs-ink-bar-wrapper {
				width: 60rpx;
				background: #ddd;
				position: relative;
				left: 345rpx;
				bottom: 0;
				height: 8rpx;
				transform-origin: 50% 50%;
				transition: width 250ms ease-out, left 250ms ease-out;
				overflow: hidden;
				border-radius: 4rpx;

				.ui-tabs-ink-bar {
					position: absolute;
					width: 30rpx;
					background: #e74d45;
					height: 8rpx;
					border-radius: 4rpx;
				}

			}
		}



	}
</style>
