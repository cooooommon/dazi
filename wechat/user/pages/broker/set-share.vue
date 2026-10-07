<template>
	<view class="share-page" v-if="isLoad">
		<view class="flex-between pl-lg pr-lg h-120 fill-base radius-16 ml-md mr-md mt-md" 
		@tap="$util.goUrl({url: `/user/pages/broker/choose-agent`})">
			<text class="f-mini-title">为谁拉{{$t('action.attendantName')}}</text>
			<view class=" flex-center ">
				<view class="f-mini-title max-300"
					:class="[{'c-title text-bold':check_admin.id},{'c-caption':!check_admin.id}]">
					{{check_admin.id ? check_admin.agent_name : '选择代理商'}}
				</view>
				<i class="iconfont icongengduo" style="font-size: 13px;"></i>
			</view>
		</view>
		<view class=" pl-lg pr-lg fill-base radius-16 ml-md mr-md mt-md">
			<view class="h-110 flex-y-center text-bold f-mini-title">{{$t('action.attendantName')}}经纪人说明：</view>
			<view class="f-paragraph" style="padding-bottom: 80rpx;">
				{{$t('action.attendantName')}}经纪人申请和审核由平台处理，经纪人邀请{{$t('action.attendantName')}}不限制地区，
				经纪人邀请{{$t('action.attendantName')}}时，可以选择为某个代理商邀请{{$t('action.attendantName')}}，邀请成功之后，
				该{{$t('action.attendantName')}}产生了订单，经纪人可获得分佣，分佣由代理商提成部分扣除，
				例如后台设置经纪人获得5%分佣，某A{{$t('action.attendantName')}}成单后，
				应该由A{{$t('action.attendantName')}}以及A{{$t('action.attendantName')}}绑定的上级代理商/平台承担分销费用，
				则该经纪人可获得佣金=订单实际金额*5%
			</view>
		</view>
		
		<view class="space-footer"></view>
		<fix-bottom-button @cancel="toConfirm(1)" @confirm="toConfirm"
			:text="[{text:'为平台邀请',type:'cancel'},{text:'为代理商邀请',type:'confirm'}]" bgColor="#fff">
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
			return{
				isLoad: true,
				check_admin: {
					id: 0
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			plugAuth: state => state.config.configInfo.plugAuth,
		}),
		onLoad() {
			//this.$util.showLoading()
			this.initIndex()
			
		},
		methods: {
			...mapActions(['getConfigInfo', 'getPlugAuth', 'getUserInfo', 'getMineInfo', 'getCoachInfo',
				'updateCommonOptions',
			]),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				//await this.getList(1)
			},
			initRefresh() {
				this.$util.showLoading()
				this.initIndex(true)
			},
			toConfirm(type = 0) {
				let {
					id = 0,
				} = this.check_admin
				if (type == 0 && !id) {
					this.$util.showToast({
						title: `请选择代理商`
					})
					return
				}
				this.$util.getPage(-1).check_admin = type == 1 ? {
					id: 0,
					agent_name: '平台'
				} : this.check_admin
				this.$util.back()
				this.$util.goUrl({
					url: 1,
					openType: `navigateBack`
				})
			}
		},
	}
</script>

<style lang="scss" scoped>
	.share-page{
		.h-120{
			height: 120rpx;
		}
		.h-110{
			height: 110rpx;
		}
	}
</style>