<template>
	<view class="box ">
		<view class="pl-lg pr-lg">
			<view class="box-item">
				<view class="box-item-title f-paragraph pb-lg text-bold flex">
					<text class="c-warning f-sm-title">*</text>
					<text>选择关联订单</text>
				</view>
				<view class="fill-body radius-16"
					@tap="$util.goUrl({url:`/technician/pages/bad-comments/choice?id=${orderInfo.id || 0}`})">
					<view class="ml-lg mr-lg f-mini-title flex-between box-item-cont" :class="orderInfo.id && 'b-1px-b'">
						<text class="c-5A677E">关联订单</text>
						<view class="flex-y-center">
							<text class="pr-md">{{ orderInfo.id ? '重新选择':'请选择'}}</text>
							<i class="iconfont icon-right text-bold"></i>
						</view>
					</view>
					<blcok v-if="orderInfo.id">
						<view class="flex-y-center pt-lg pl-md pr-md pb-lg"
							v-for="(item,index) in orderInfo.order_goods" :key="index">
							<image mode="aspectFill" class="item-image radius-16" :src="item.goods_cover"></image>
							<view class="pl-md">
								<view class="f-mini-title text-bold">{{item.goods_name}}</view>
								<view class="c-caption f-caption pt-md pb-md">
									<text class="pr-md">预约时间</text>
									<text>{{orderInfo.start_time}}</text>
								</view>
								<view class="flex-y-center c-warning" style="line-height: 1;">
									<text class="text-bold f-icontext">¥</text>
									<text class="f-sm-title text-bold">{{item.true_price}}</text>
								</view>
							</view>
						</view>
					</blcok>
				</view>
			</view>
			<view class="box-item pt-md">
				<view class="box-item-title f-paragraph pb-lg text-bold flex">
					<text class="c-warning f-sm-title">*</text>
					<text>申诉理由</text>
				</view>
				<view class="pd-lg radius-16 fill-body" style="overflow: hidden;">
					<textarea class="c-paragraph" @input="bindInput" style="width: 100%;" cols="30" rows="10"
						placeholder="请输入申诉理由" :value="content" maxlength="1000"></textarea>
					<view class="pt-md text-right c-5A677E f-paragraph">
						{{content.length > 1000 ? 1000 : content.length}} / 1000
					</view>
				</view>
			</view>
		</view>
		<view class="space-max-footer"></view>

		<fix-bottom-button @cancel="$util.goUrl({url:`/technician/pages/bad-comments/list`})" @confirm="confirm"
			:text="[{text:'申诉记录',type:'cancel'},{text:'提交申诉',type:'confirm',isAuth:true}]" bgColor="#fff"
			:classType="2">
		</fix-bottom-button>
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
				orderInfo: {},
				content: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.initIndex()
		},
		methods: {
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
			},
			bindInput(e) {
				this.$nextTick(function() {
					this.content = e.detail.value;
				})
			},
			confirm() {
				let {
					content,
					orderInfo
				} = this
				if (!content.trim()) {
					return this.$util.showToast({
						title: '请输入申诉理由'
					})
				}
				if (!orderInfo.id) {
					return this.$util.showToast({
						title: '请选择关联订单'
					})
				}
				this.$util.showLoading()
				this.$api.technician.addAppeal({
					content,
					order_code: orderInfo.order_code
				}).then(res => {
					//this.$util.showToast({title: '提交成功'})
					//setTimeout(()=>{this.$util.goUrl({url:1,openType:`navigateBack`})},1500)
					this.$util.hideAll()
					this.$util.goUrl({
						openType: 'redirectTo',
						url: `/technician/pages/bad-comments/success`
					})
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff;
	}

	.box-item {
		.box-item-title {
			padding-top: 40rpx;
		}

		.box-item-cont {
			min-height: 110rpx;
		}

		.c-5A677E {
			color: #5A677E;
		}

		.item-image {
			width: 155rpx;
			height: 155rpx;
			overflow: hidden;
			vertical-align: bottom;
		}
	}
</style>
