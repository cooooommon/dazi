<template>
	<view class="shopstore-store-info rel" v-if="detail.id">
		<view class="bg abs" :style="{background: `linear-gradient(180deg, ${primaryColor} 0%, #fff 100%)`}"></view>
		<view class="ml-md mr-md">
			<view class="space-lg"></view>
			<view class="fill-base pd-lg f-paragraph c-paragraph radius-18">
				<view class="flex-y-center pb-lg c-title b-1px-b">
					<i class="iconfont icongongsi"></i>
					<view class="flex-1 ml-md f-mini-title text-bold ellipsis">{{detail.attestation}}</view>
				</view>
				<view class="count-item flex-x-center pt-lg">
					<view class="flex-center flex-column">
						<view class="f-title c-title">{{detail.total_num}}+</view>
						<view class="f-icontext c-caption">服务次数</view>
					</view>
					<view class="flex-center flex-column">
						<view class="f-title c-title">{{detail.order_rate}}%</view>
						<view class="f-icontext c-caption">接单率</view>
					</view>
					<view class="flex-center flex-column">
						<view class="f-title c-title">{{detail.positive_rate}}%</view>
						<view class="f-icontext c-caption">好评率</view>
					</view>
				</view>
			</view>
			<view class="fill-base mt-md pd-lg f-paragraph c-paragraph radius-18">
				<view class="f-mini-title c-title text-bold mb-lg">营业执照</view>
				<view class="flex-center img-list radius-16">
					<image
						@tap.stop="$util.previewImage({current:detail.business_license,urls:[detail.business_license]})"
						mode="aspectFill" class="img-item radius-16" :src="detail.business_license">
					</image>
				</view>
			</view>
			<view class="fill-base mt-md pd-lg f-paragraph c-paragraph radius-18">
				<view class="f-mini-title c-title text-bold mb-md">商家简介</view>
				<text class="f-paragraph c-paragraph" decode="emsp" style="word-break:break-all;">{{detail.text}}</text>
			</view>
		</view>
		<view class="space-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		data() {
			return {
				options: {},
				detail: {},
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		methods: {
			...mapMutations(['updateUserItem']),
			async initIndex() {
				// #ifdef H5
				if (this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await this.getDetail()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			async getDetail() {
				let {
					id
				} = this.options
				let {
					info
				} = await this.$api.shopstore.storeInfo({
					id
				})
				let {
					title
				} = info
				uni.setNavigationBarTitle({
					title
				})
				this.detail = info
				this.$util.hideAll()
			}
		}
	}
</script>


<style lang="scss">
	.shopstore-store-info {
		.bg {
			width: 100%;
			height: 172rpx;
			z-index: -1;
		}

		.count-item {
			.flex-column {
				width: 33.33%;
			}
		}

		.img-list {
			height: 282rpx;
			background: #F5F5F5;

			.img-item {
				width: 339rpx;
				height: 242rpx;
			}
		}

	}
</style>
