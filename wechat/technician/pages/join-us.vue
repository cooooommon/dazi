<template>
	<view class="rel">
		<image src="https://lbqny.migugu.com/admin/playwith/service/join-bg.png" mode="aspectFill" class="join-bg"></image>
		<image src="https://lbqny.migugu.com/admin/playwith/service/join-title.png" mode="aspectFill" class="join-title abs"></image>
		<view class="join-box abs">
			<image src="https://lbqny.migugu.com/admin/playwith/service/join-form.png" mode="aspectFill" class="join-form"></image>
			<view class="abs join-content">
				<view class="item">
					<view class="f-title text-bold">您的姓名</view>
					<view class="item-cont radius-10 mt-md">
						<input type="text" placeholder="请输入您的姓名" class="f-mini-title pl-lg pr-lg" v-model="param.name"/>
					</view>
				</view>
				<view class="item">
					<view class="f-title text-bold">手机号</view>
					<view class="item-cont radius-10 mt-md">
						<input type="text" placeholder="请输入联系方式" class="f-mini-title pl-lg pr-lg" v-model="param.mobile"/>
						<view class="flex-between pr-lg" v-if="configInfo.short_code_status == 1">
							<input type="text" placeholder="请输入验证码" class="f-mini-title pl-lg pr-lg flex-1" v-model="param.code"/>
							<text class="f-mini-title" style="color: #3742DC;" @tap="toSend">{{authTime>0?`(${authTime}s)`:'获取验证码'}}</text>
						</view>
					</view>
				</view>
				<view class="item">
					<view class="f-title text-bold">申请加入的城市</view>
					<view class="item-cont radius-10 mt-md">
						<input type="text" placeholder="请输入申请加入的城市" class="f-mini-title pl-lg pr-lg" v-model="param.city"/>
					</view>
				</view>
			</view>
			<view class="join-btn mt-lg" @tap="submit">
				<image src="https://lbqny.migugu.com/admin/playwith/service/join-btn.png" mode="aspectFill"></image>
			</view>
		</view>
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
				param: {
					name: '',
					mobile: '',
					city: '',
					code: ''
				},
				authTime: 0,
				timer: null,
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
		}),
		async onLoad(options) {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			this.getConfigInfo()
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
		},
		methods: {
			...mapActions(['getConfigInfo']),
			async toSend(){
				let {
					authTime
				} = this
				if (authTime) return
				let {
					mobile: phone = ''
				} = this.param
				if(!phone){
					return this.$util.showToast({title: '请输入联系方式'})
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.user.sendShortMsg({
						phone
					})
					this.$util.hideAll()
					this.lockTap = false
					let time = 60
					this.timer = setInterval(() => {
						if (time === 0) {
							clearInterval(this.timer)
							return
						}
						time--
						this.authTime = time
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
				
			},
			async submit(){
				let {
					name = '',
					mobile = '',
					city = '',
					code = ''
				} = this.param
				
				if(!name){
					return this.$util.showToast({title: '请输入您的姓名'})
				}
				if(!mobile){
					return this.$util.showToast({title: '请输入联系方式'})
				}
				if(!code && this.configInfo.short_code_status == 1){
					return this.$util.showToast({title: '请输入验证码'})
				}
				if(!city){
					return this.$util.showToast({title: '请输入申请加入的城市'})
				}
				this.$util.showLoading()
				await this.$api.technician.participate(this.param)
				this.$util.hideAll()
				this.$util.showToast({title: '提交成功'})
				setTimeout(() => {
					this.$util.goUrl({url: 1 ,openType: 'navigateBack'})
				}, 1500);
			}
		}
	}
</script>

<style lang="scss">
	.join-bg{
		width: 750rpx;
		height: 1752rpx;
	}
	.join-title{
		width: 458rpx;
		height: 288rpx;
		top: 80rpx;
		left: 50%;
		margin-left: -229rpx;
	}
	.join-box{
		top: 730rpx;
		left: 50%;
		margin-left: -337rpx;
		width: 674rpx;
		height: 861rpx;
		.join-form{
			width: 674rpx;
			height: 861rpx;
		}
		.join-content{
			width: 674rpx;
			height: 861rpx;
			top: 0;
			left: 0;
			padding-top: 90rpx;
		}
		.item{
			padding: 60rpx 35rpx 0 35rpx;
			.item-cont{
				border: 1px solid #D9C3F8;
				min-height: 74rpx;
				
				input{
					width: 100%;
					height: 74rpx;
				}
			}
		}
		.join-btn{
			image{
				width: 662rpx;
				height: 93rpx;
			}
			
		}
	}
</style>