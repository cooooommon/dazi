<template>
	<view class="box">
		<view class="pl-lg pr-lg pb-lg fill-base">
			<view class="box-item">
				<view class="box-item-title f-paragraph pb-md text-bold flex">反馈类型</view>
				<view class="radius-10 rel box-item-cont text-center">
					<view class="box-item-bg" :style="{backgroundColor:primaryColor }"></view>
					<text class="abs f-caption box-item-cont-text" :style="{color:primaryColor}">{{feedInfo.type_name}}</text>
				</view>
			</view>
			<view class="box-item pt-md">
				<view class="box-item-title f-paragraph pb-md text-bold flex">订单编号</view>
				<view class="c-paragraph f-mini-title">{{feedInfo.order_code || '无'}}</view>
			</view>
			<view class="box-item pt-md">
				<view class="box-item-title f-paragraph pb-md text-bold flex">反馈内容</view>
				<view class="c-paragraph box-item-content f-paragraph fill-body pd-lg radius-16" >{{feedInfo.content || '无'}}</view>
			</view>
			<view class="box-item pt-md flex" v-if="feedInfo.images&&feedInfo.images.length">
				<bolck v-for="(item,index) in feedInfo.images" :key="index">
					<image @tap="$util.previewImage({current:item ,urls:feedInfo.images})" :src="item" mode="aspectFill" class="item-image radius-16" :class="index > 0 && 'ml-md'"></image>
				</bolck>
			</view>
			<view class="box-item pt-md flex" v-if="feedInfo.video_url">
				<video :src="feedInfo.video_url" class="item-image radius-16"></video>
			</view>
			<view class="c-caption f-caption pt-md">注: 您反馈的意见问题，平台不会透露给他人，保护您的隐私</view>
			<view class="box-item pt-md" v-if="feedInfo.status == 2">
				<view class="box-item-title f-paragraph pb-md text-bold flex">平台回复</view>
				<view class="c-paragraph f-paragraph fill-body pd-lg radius-16" style="white-space: pre-wrap">{{feedInfo.reply_content || '无'}}</view>
			</view>
		</view>
		<view class="space-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				options: {},
				feedInfo:{}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		methods: {
			async initIndex(refresh = false){
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				this.$util.showLoading()
				let { id } = this.options
				this.feedInfo = await this.$api.mine.feedbackInfo({id})
				this.$util.hideAll()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			}
		}
	}
</script>

<style lang="scss">
	page{
		background-color: #fff;
	}
	.box-item {
		.box-item-title {
			padding-top: 40rpx;
		}
		.c-5A677E {
			color: #5A677E;
		}
		.box-item-cont{
			line-height: 47rpx;
			height: 47rpx;
			width: 146rpx;
			overflow: hidden;
			.box-item-bg{
				width: 146rpx;
				height: 47rpx;
				opacity: 0.1;
			}
			.box-item-cont-text{
				left: 0;
				line-height: 47rpx;
				top: 0;
				width: 100%;
			}
		}
		.item-image{
			width: 216rpx;
			height: 216rpx;
		}
		.box-item-content{
			white-space: pre-wrap;
			max-height: 400rpx;
			overflow-y: scroll;
		}
	}
</style>
