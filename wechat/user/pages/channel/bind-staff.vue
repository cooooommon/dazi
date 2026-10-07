<template>
	<view class="staff-page">
		<block v-if="detail.type == 2">
			<view class="f-paragraph text-center" style="padding-top: 500rpx;">您已经是《{{detail.user_name}}》的下级，<br/>不可绑定在多个渠道商的下面。</view>
		</block>
		<block v-if="isLoad">
			<view class="flex-center top-box">
				<image class="top-header" :src="detail.avatarUrl" mode="aspectFill"></image>
			</view>
			<view class="f-paragraph c-paragraph pt-lg flex-center">{{detail.user_name}}正在邀请你绑定渠道码</view>
			<view class="f-title text-bold flex-center">请问是否确定</view>
			<view class="flex-center" style="margin: 0 44rpx;">
				<view class="mr-md bind-btn flex-center f-title close-btn flex-1" @tap="$util.goUrl({url: `/pages/mine`, openType: 'reLaunch'})">取消</view>
				<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" class="flex-1"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="bindChannel">
					<view class="bind-btn flex-center f-title c-base flex-1" :style="{background: primaryColor}">确认绑定</view>
				</auth>
			</view>
		</block>
		
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
				isLoad: false,
				options: {},
				detail: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
		}),
		async onLoad(option) {
			this.options = await this.updateCommonOptions(option)
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
				this.$util.showLoading()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				// this.getMineInfo()
				await this.inviteStaffQrInfo()
			},
			async inviteStaffQrInfo(){
				let data = await this.$api.channel.inviteStaffQrInfo(this.options)
				this.detail = data
				if(data.type == 1){
					this.$util.goUrl({url: `/user/pages/channel/index`})
					return
				}else if(data.type == 2){
					this.$util.hideAll()
					return
				}
				this.isLoad = true
				this.$util.hideAll()
			},
			async bindChannel(){
				this.$util.showLoading()
				await this.$api.channel.bindChannel(this.options)
				this.$util.hideAll()
				this.$util.showToast({
					title: '绑定成功'
				});
				setTimeout(()=>{
					this.$util.goUrl({url: `/user/pages/channel/my-turnover`})
				},1000)
			}
		}
	}
</script>

<style lang="scss">
	page{
		background: #fff;
	}
	.staff-page{
		.item-title{
			padding-top: 50rpx;
		}
		.item-input{
			height: 110rpx;
			input{
				height: 110rpx;
				line-height: 110rpx;
			}
		}
		.bind-btn{
			height: 96rpx;
			border-radius: 96rpx;
			margin-top: 105rpx;
		}
		
		.top-box{
			padding-top: 45rpx;
			.top-header{
				width: 280rpx;
				height: 280rpx;
				border-radius: 280rpx;
			}
		}
		.close-btn{
			border: 1px solid #979797;
		}
	}
</style>