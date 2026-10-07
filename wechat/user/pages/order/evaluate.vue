<template>
	<view class="evaluate-pages" v-if="detail.id">
		<view class="mt-md radius-32 fill-base pt-lg evaluate-box">
			<view class="b-1px-b pb-lg">
				<view class="f-mini-title pb-lg">您的评价对我们很重要哦</view>
				<textarea class="pt-md" v-model="param.text" @input="bindInput" maxlength="300" cols="30" rows="10" style="width: 100%;height: 400rpx;" placeholder="请输入评价内容~"></textarea>
				<view class="text-right f-desc pt-md" style="color: #BCBCBC;">已输入{{param.text.length > 300 ? 300 : param.text.length}}/300</view>
			</view>
			<view class="evaluate-score">
				<view class="flex pb-lg">
					<image :src="detail.coach_info.work_img" mode="aspectFill" class="wizard-header"></image>
					<view class="pl-lg flex-1">
						<view class="f-ms-title pb-sm text-bold">{{detail.coach_info.coach_name}}</view>
						<view class="service-box rel">
							<view class="service-box-sj abs"></view>
							<view class="service-cont c-base f-desc pt-lg pb-lg pr-lg radius-16 rel flex-y-center">
								<view class="flex-center service-icon">
									<i class="iconfont icondyvmsyuyinfuwu c-base" style="font-size: 24px;"></i>
								</view>
								<text class="flex-1">您对我提供的【{{detail.order_goods[0].goods_name}}】的服务还算满意吗?</text>
							</view>
						</view>
					</view>
				</view>
				<view class="flex-warp pt-lg">
					<text class="f-mini-title pr-md">{{$t('action.attendantName')}}颜值</text>
					<block v-for="(item,index) in 5" :key="index">
						<i @tap="changeStar(index*1+1, 'star')" class="iconfont mr-sm iconpingfen1" style="font-size: 22px;"
							:class="[{'icon-empty':param.star<index*1+1},{'icon-font-color icon-solid':param.star>=index*1+1}]"></i>
					</block>
				</view>
				<view class="flex-warp pt-lg">
					<text class="f-mini-title pr-md">服务态度</text>
					<block v-for="(item,index) in 5" :key="index">
						<i @tap="changeStar(index*1+1, 'attitude_star')" class="iconfont mr-sm iconpingfen1" style="font-size: 22px;"
							:class="[{'icon-empty':param.attitude_star<index*1+1},{'icon-font-color icon-solid':param.attitude_star>=index*1+1}]"></i>
					</block>
				</view>
				<view class="flex-warp pt-lg">
					<text class="f-mini-title pr-md">响应速度</text>
					<block v-for="(item,index) in 5" :key="index">
						<i @tap="changeStar(index*1+1, 'speed_star')" class="iconfont mr-sm iconpingfen1" style="font-size: 22px;"
							:class="[{'icon-empty':param.speed_star<index*1+1},{'icon-font-color icon-solid':param.speed_star>=index*1+1}]"></i>
					</block>
				</view>
			</view>
			
			
		</view>
		<fixed position="bottom">
			<view class="fill-base pb-lg pt-lg b-1px-t">
				<view class="release flex-between ">
					<view class="">
						<view class="flex-y-center" @tap="changeAnony">
							<i class="iconfont icon-xuanze-fill" :style="{color: primaryColor}" v-if="param.is_hide == 1"></i>
							<i class="iconfont icon-xuanze" style="color: #BEC3CE;" v-else></i>
							<text class="f-caption pl-sm">匿名</text>
						</view>
						<view class="pt-sm f-caption c-caption">匿名会隐藏头像和昵称</view>
					</view>
					<view class="flex-center release-btn" :style="{background: primaryColor}" @tap="submit" v-if="!options.type">
						<i class="iconfont iconfabu c-base"></i>
						<text class="c-base f-mini-title pl-sm">发布</text>
					</view>
				</view>
				<view class="space-safe"></view>
			</view>
			
		</fixed>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import siteInfo from '@/siteinfo.js';
	import parser from "@/components/jyf-Parser/index"
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},
				detail: {},
				param: {
					order_id: '',
					text: '',
					star: 5,
					attitude_star: 5,
					speed_star: 5,
					is_hide: 0
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			gradualColor: state => state.config.configInfo.gradualColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.param.order_id = options.id
			this.options = options
			this.$util.showLoading()
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				this.getInfo()
			},
			async getInfo(){
				let data = await this.$api.order.orderInfo({
					id: this.options.id
				})
				this.detail = data
				this.$util.hideAll()
			},
			changeAnony(){
				let {
					is_hide
				} = this.param
				this.param.is_hide = is_hide == 1 ? 0 : 1
			},
			changeStar(index,type){
				this.param[type] = index
			},
			async submit(){
				let param = this.$util.deepCopy(this.param)
				let {
					text
				} = param
				if (!text) {
					this.$util.showToast({
						title: '请输入评价内容'
					})
					return
				}
				this.$util.showLoading()
				await this.$api.order.addCommentV2(param)
				this.$util.hideAll()
				this.$util.showToast({
					title: '评价成功'
				})
				setTimeout(()=>{
					this.$util.getPage(-1).initRefresh()
					this.$util.goUrl({url: 1 ,openType: 'navigateBack'})
				},1000)
			},
			bindInput(e) {
				let that = this
				console.log(e)
				this.$nextTick(function() {
					that.param.text = e.detail.value.substring(0, 300);
				})
			},
		}
	}
</script>

<style lang="scss">
	.evaluate-pages {
		.add-upload{
			min-height: 219rpx;
		}
		.evaluate-score{
			padding-top: 100rpx;
			.wizard-header{
				width: 124rpx;
				height: 124rpx;
				border-radius: 124rpx;
			}
		}
		.evaluate-box{
			padding-top: 50rpx;
			padding-left: 50rpx;
			padding-right: 50rpx;
			border-bottom-left-radius: 0;
			border-bottom-right-radius: 0;
			padding-bottom: 400rpx;
		}
		.icon-solid {
			background-image: linear-gradient(#FAD961, #F76B1C);
			margin-right: 5px;
		}
		.icon-empty {
			color: #E4E4E4;
			margin-right: 5px;
		}
		.release{
			height: 66rpx;
			padding-left: 50rpx;
			padding-right: 50rpx;
			.release-btn{
				width: 198rpx;
				height: 66rpx;
				border-radius: 66rpx;
			}
		}
		
		.service-box{
			padding-top: 40rpx;
			.service-box-sj{
				width: 0;
				height: 0;
				border-left: 10rpx solid transparent;
				border-right: 50rpx solid transparent;
				border-bottom: 100rpx solid #7C86FF;
				transform: rotate(-20deg);
				top: -10rpx;
				left: 40rpx;
			}
			.service-cont{
				background: linear-gradient(to right, #7C86FF ,#9D74FF);
				.service-icon{
					width: 77rpx;
					height: 77rpx;
				}
			}
		}
	}
</style>
