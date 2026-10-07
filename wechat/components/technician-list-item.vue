<template>
	<view class="technician-list-item ">
		<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
			:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toEmit('info')">
			<view class="list-item fill-base radius-16 box-shadow">
				<view class="work-img rel">
					<image mode="aspectFill" class="work-img" :src="info.work_img"> <!--@tap.stop="toEmit('workImg')" -->
					</image>
					<view class="abs technician-type" :class="[`text-type-${info.text_type}`]">{{textType[info.text_type]}}</view>
					<view class="abs technician-tag flex-center f-ms-little c-base pl-sm pr-sm" :class="[`tag-type-${info.text_type}`]" v-if="info.tag_name">
						{{info.tag_name}}
					</view>
					
					<view class="abs content-box pl-md pr-md c-base pb-md">
						<view class="text-bold ellipsis f-paragraph max-270">{{info.coach_name}}
						</view>
						<!-- <view class="f-caption c-caption pt-sm ellipsis">身高{{info.height}}·体重{{info.weight}}·{{info.constellation}}</view> -->
						<view class="pt-sm">
							<view class="user-info-item text-center f-caption" style="color: #DCBBFF;border: 1px solid #DCBBFF;">
								<text style="font-size: 0.8em;line-height: normal;">{{info.height}}cm</text>
							</view>
							<view class="user-info-item text-center ml-sm f-caption" style="color: #FFB395;border: 1px solid #FFB395;">
								<text style="font-size: 0.8em;line-height: normal;">{{info.weight}}kg</text>
							</view>
							<view class="user-info-item text-center ml-sm f-caption" style="color: #9DFCDC;border: 1px solid #9DFCDC;">
								<text style="font-size: 0.8em;line-height: normal;">{{info.constellation}}</text>
							</view>
						</view>
						<view class="f-icontext pt-sm ellipsis max-270">{{info.text}}</view>
						<view class="flex-between pt-sm">
							<view class="flex-y-center">
								<i class="icondizhi2 iconfont" style="color: #fff;font-size: 12px;"></i>
								<text class="f-ms-little" style="padding-left: 6rpx;line-height: 12px;">{{info.distance}}</text>
							</view>
							<view class="flex-y-baseline rel">
								<text style="color: #E94A48;" class="f-ms-little">￥</text>
								<view class="flex" v-if="isIos">
									<text style="color: #E94A48;" class="f-title text-bold">{{info.price | handlePrice}}</text>
									<text class="f-ms-little" style="color: #E94A48;padding-top: 5px;">/</text>
								</view>
								<view class="flex-y-baseline" v-if="!isIos">
									<text style="color: #E94A48;" class="f-title text-bold">{{info.price | handlePrice}}</text>
									<text class="f-ms-little" style="color: #E94A48;">/</text>
								</view>
								<text class="f-ms-little" style="color: #E94A48;">单</text>
							</view>
							<!-- <view class="flex rel">
								<text style="color: #E94A48;padding-top: 4px;" class="f-ms-little">￥</text>
								<text style="color: #E94A48;height: 32rpx;line-height: 32rpx;" class="f-title text-bold">{{info.price | handlePrice}}</text>
								<text class="f-ms-little" style="color: #E94A48;line-height: 32rpx;padding-top: 3px;">/单</text>
							</view> -->
							<!-- <view>
								<text style="color: #E94A48;" class="f-ms-little">￥</text>
								<text style="color: #E94A48;" class="f-title text-bold">{{info.price | handlePrice}}</text>
								<text class="f-ms-little" style="color: #E94A48;">/</text>
								<text class="f-ms-little" style="color: #E94A48;">单</text>
							</view> -->
						</view>
					</view>
				</view>
				<!-- <view class="content-info">
					<view class="c-black text-bold ellipsis f-title max-300">{{info.coach_name}}
					</view>
					<view class="f-caption c-caption pt-sm ellipsis">身高{{info.height}}·体重{{info.weight}}·{{info.constellation}}</view>
					<view class="f-caption c-caption pt-sm ellipsis max-300">{{info.text}}</view>
					<view class="flex-between pt-sm">
						<view class="flex-center">
							<text style="color: #E94A48;" class="f-paragraph">￥</text>
							<text style="color: #E94A48;" class="f-md-title text-bold">{{info.price | handlePrice}}</text>
							<text class="f-caption" style="color: #9BA2AA;">/单</text>
						</view>
						<view class="flex-center">
							<i class="icondizhi iconfont" style="color: #DDDDDD;"></i>
							<text class="f-caption c-icontext " style="padding-left: 6rpx;">{{info.distance}}</text>
						</view>
					</view>
				</view> -->
			</view>
		</auth>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		props: {
			from: {
				type: String,
				default () {
					return 'list'
				}
			},
			info: {
				type: Object,
				default () {
					return {}
				}
			}
		},
		data() {
			return {
				textType: {
					1: '可接单',
					2: '接单中',
					3: '休息中',
					4: '不可接单'
				},
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			plugAuth: state => state.config.configInfo.plugAuth,
			userInfo: state => state.user.userInfo,
			isIos: state => state.config.configInfo.isIos,
		}),
		filters: {
			handlePrice(val){
				return val >= 10000 ? (val/10000).toFixed(1) + 'w' : val
			}
		},
		methods: {
			toEmit(key) {
				this.$emit(key)
			}
		}
	}
</script>

<style scoped lang="scss">
	.technician-list-item {
		.list-item {
			width: 334rpx; 
			.work-img {
				width: 334rpx;
				height: 548rpx;
				border-radius: 16rpx;
				overflow: hidden;

				.near-time-order {
					width: 334rpx;
					height: 34rpx;
					padding: 0 16rpx;
					font-size: 20rpx;
					background: rgba(0, 0, 0, 0.64);
					left: 0;
					bottom: 0;
					z-index: 2;
				}
			}
			
			.content-box{
				padding-top: 50rpx;
				width: 334rpx;
				left: 0;
				bottom: 0;
				background: linear-gradient( 180deg, rgba(0,0,0,0) 0%, #000000 100%);
				.user-info-item{
					min-width: 71rpx;
					// height: 30rpx;
					border-radius: 30rpx;
					flex-direction: column;
					padding: 6rpx 0 7rpx 0;
					line-height: 1;
					box-sizing: content-box;
					display: inline-block;
					transform: rotate(360deg);
				}
			}

			.content-info {
				padding: 18rpx 15rpx;
				border-bottom-left-radius: 16rpx;
				border-bottom-right-radius: 16rpx;
				.star {
					color: #FF9300;

					.iconfont {
						font-size: 26rpx;
						margin-right: 5rpx;
					}
				}


				.btn-list {
					margin-top: 13rpx;

					.btn-item {
						width: 150rpx;
						height: 53rpx;
						border-radius: 8rpx;
						transform: rotateZ(360deg);
					}
				}

				.count-list {
					color: #B1A7A9;
					font-size: 22rpx;

					.iconfont {
						font-size: 28rpx;
						color: #999;
						margin-right: 4rpx;
					}

				}
			}
			.technician-type{
				width: 160rpx;
				-webkit-transform: rotate(45deg);
				transform: rotate(45deg);
				top: 20rpx;
				right: -40rpx;
				text-align: center;
				font-size: 20rpx;
				padding: 4rpx 0;
			}
			.technician-status {
				top: -1rpx;
				left: 0;
				min-width: 207rpx;
				height: 40rpx;
				padding: 0 15rpx;
				border-radius: 16rpx 0 26rpx 0;
				border: 1rpx solid #FFFFFF;
				border-top: transparent;
				border-left: transparent;
				transform: rotateZ(360deg);
				z-index: 2;

				.line {
					width: 1rpx;
					height: 17rpx;
				}

				.iconfont {
					font-size: 22rpx;
					margin-right: 4rpx;
				}
			}
			
			.technician-tag{
				height: 39rpx;
				top: 18rpx;
				left: 18rpx;
				border-radius: 39rpx;
				z-index: 2;
				line-height: 39rpx;
			}

			.text-type-1 {
				color: #4600E6;
				background: linear-gradient(270deg, #AE95F9 0%, #D9CCFF 51%, #C8B6FF 100%);

				.line {
					background: #4600E6
				}
			}
			// .tag-type-1{
			// 	color: #fff;
			// 	background: #FF4C88;
			// }
			.tag-type-1 ,.tag-type-2, .tag-type-3 , .tag-type-4{
				color: #fff;
				background: rgba(0, 0, 0, 0.4)
			}
			.text-type-2 {
				color: #FF1E48;
				background: linear-gradient(270deg, #FFA3B4 0%, #FFCCD5 37%, #FFC5CF 100%);

				.line {
					background: #FF1E48
				}
			}

			.text-type-3 {
				color: #FF701E;
				background: linear-gradient(270deg, #FFCFA3 0%, #FFE0CC 43%, #FFD0A4 100%);

				.line {
					background: #FF701E
				}
			}

			.text-type-4 {
				color: #333;
				background: linear-gradient(270deg, #A9A9A9 0%, #DCDCDC 43%, #C0C0C0 100%);

				.line {
					background: #333
				}
			}

		}

	}
</style>
