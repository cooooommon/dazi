<template>
	<view>
		<view class='record-box fill-base'>
			<view class='record-item rel ml-sm b-1px-l' :style="{padding:index==list.length -1?'0 0 0 30rpx':''}"
				v-for="(item,index) in list" :key="index">

				<text class="item-tag abs" :class="[{'cur':info.pay_type > item.pay_type -1 && info.pay_type != 8}]"
					:style="{border:`2rpx solid ${primaryColor}`,background: info.pay_type > item.pay_type -1 && info.pay_type != 8 ? primaryColor : ''}"></text>

				<view class='c-title'>
					<view class="item-text f-paragraph flex-y-baseline">
						{{item.title}}
						<view class="ml-md f-caption c-caption"
							:style="{color:item.title == '签字确认' && !info.sign_img ? '' : info.pay_type > item.pay_type -1 && info.pay_type != 8 ? primaryColor : ''}">
							{{item.title == '签字确认' && !info.sign_img ? '暂未签字确认' : info.pay_type > item.pay_type -1  && info.pay_type != 8? '' : '状态未开始' }}
						</view>
					</view>
					<view class="c-caption" v-if="info.pay_type > item.pay_type*1-1 && info[item.time]">
						{{info[item.time]}}
					</view>
				</view>
				<block v-if="item.pay_type == 4 && !info.is_add && info.pay_type > 3">
					<view @tap.stop="toMap('serout')" class="flex-y-center mt-md f-caption c-title"
						v-if="info.serout_address">
						<i class="iconfont iconjuli mr-sm" :style="{color:primaryColor}"></i>{{info.serout_address}}
					</view>
				</block>
				<block
					v-if="item.pay_type == 5 && !info.is_add && info.pay_type > 4 && (info.arrive_img || info.arr_address)">
					<block v-if="info.arrive_img">
						<!-- #ifdef H5 -->
						<view @tap.stop="toPreviewImage('arrive_img')" class="item-img mt-md radius-5"
							v-if="info.arrive_img">
							<view class="h5-image item-img mt-md radius-5"
								:style="{ backgroundImage : `url('${info.arrive_img}')`}">
							</view>
						</view>
						<!-- #endif -->
						<!-- #ifndef H5 -->
						<image @tap.stop="toPreviewImage('arrive_img')" mode="widthFix" class="item-img mt-md radius-5"
							:src="info.arrive_img" v-if="info.arrive_img">
						</image>
						<!-- #endif -->
					</block>
					<view @tap.stop="toMap('arr')" class="flex-y-center mt-md f-caption c-title"
						v-if="info.arr_address">
						<i class="iconfont iconjuli mr-sm" :style="{color:primaryColor}"></i>{{info.arr_address}}
					</view>
				</block>
				<block v-if="item.title== '服务完成' && info.pay_type == 7 && (info.end_img || info.end_address)">
					<block v-if="info.end_img">
						<!-- #ifdef H5 -->
						<view @tap.stop="toPreviewImage('end_img')" class="item-img mt-md radius-5">
							<view class="h5-image item-img mt-md radius-5"
								:style="{ backgroundImage : `url('${info.end_img}')`}">
							</view>
						</view>
						<!-- #endif -->
						<!-- #ifndef H5 -->
						<image @tap.stop="toPreviewImage('end_img')" mode="widthFix" class="item-img mt-md radius-5"
							:src="info.end_img">
						</image>
						<!-- #endif -->
					</block>
					<view @tap.stop="toMap('end')" class="flex-y-center mt-md f-caption c-title"
						v-if="info.end_address">
						<i class="iconfont iconjuli mr-sm" :style="{color:primaryColor}"></i>{{info.end_address}}
					</view>
				</block>
				<block v-if="item.title== '签字确认' && info.pay_type == 7">
					<block v-if="info.sign_img">
						<!-- #ifdef H5 -->
						<view @tap.stop="toPreviewImage('sign_img')" class="item-img mt-md radius-5">
							<view class="h5-image item-img mt-md radius-5"
								:style="{ backgroundImage : `url('${info.sign_img}')`}">
							</view>
						</view>
						<!-- #endif -->
						<!-- #ifndef H5 -->
						<image @tap.stop="toPreviewImage('sign_img')" mode="widthFix" class="item-img mt-md radius-5"
							:src="info.sign_img">
						</image>
						<!-- #endif -->
					</block>
					<view class="flex-between" v-if="type==1 && !info.is_add && !info.sign_img">
						<view @tap.stop="toSign" class="item-btn flex-center mt-md c-base radius"
							:style="{background:primaryColor}">
							签字确认
						</view>
						<view></view>
					</view>
				</block>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from "vuex"
	export default {
		name: 'timeline',
		props: {
			list: {
				type: Array,
				default () {
					return {}
				}
			},
			info: {
				type: Object,
				default () {
					return {}
				}
			},
			type: {
				type: Number,
				default () {
					return 0
				}
			}
		},
		data() {
			return {

			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		methods: {
			toPreviewImage(key) {
				let curent = this.info[key]
				this.$util.previewImage({
					curent,
					urls: [curent]
				})
			},
			// 查看定位
			async toMap(key) {
				let {
					info
				} = this
				await this.$util.checkAuth({
					type: 'userLocation'
				})
				await uni.getLocation({
					type: 'gcj02',
				})
				await uni.openLocation({
					latitude: info[`${key}_lat`] * 1,
					longitude: info[`${key}_lng`] * 1,
					name: info[`${key}_address`],
					scale: 28
				})
			},
			toSign() {
				let {
					id
				} = this.info
				let url = `/user/pages/order/sign?id=${id}`
				this.$util.goUrl({
					url
				})
			}
		},
	}
</script>

<style lang="scss">
	.record-item {
		padding: 0 0 30rpx 30rpx;

		.item-tag {
			width: 14px;
			height: 14px;
			display: block;
			background: #fff;
			border-radius: 50%;
			top: 0;
			left: -7px;
			transform: rotateZ(360deg);
		}

		.item-text {
			line-height: 34rpx;
		}

		.item-img {
			width: 180rpx;
			min-height: 118rpx;
		}

		.item-btn {
			width: 160rpx;
			height: 64rpx;
			background: #EEEEEE;
		}
	}

	.record-item.b-1px-l::before {
		border-left: 2px solid #ccc;
	}

	.record-item:last-child {
		padding-bottom: 0;
	}
</style>
