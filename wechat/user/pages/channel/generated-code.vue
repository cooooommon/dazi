<template>
	<view class="staff-page" v-if="isLoad">
		<view class="item pl-lg pr-lg">
			<view class="f-min-title text-bold pb-md item-title">员工姓名</view>
			<view class="fill-body item-input radius-16 flex-center">
				<input type="text" class="f-mini-title pl-lg pr-lg flex-1" :placeholder="rule[0].errorMsg" v-model="param.name" />
			</view>
		</view>
		<view class="item pl-lg pr-lg">
			<view class="f-min-title text-bold pb-md item-title">佣金比例</view>
			<view class="fill-body item-input radius-16 flex-center">
				<input type="text" class="f-mini-title pl-lg pr-lg flex-1" :placeholder="rule[1].errorMsg" v-model="param.balance" />
				<text class="f-mini-title text-bold pr-lg">%</text>
			</view>
		</view>
		<view class="ml-lg mr-lg bind-btn flex-center f-title c-base" :style="{background: primaryColor}" @tap="getInviteStaffQr">生成二维码</view>
		
		<uni-popup type="center" ref="code_box">
			<view class="fill-base flex-center">
				<view class="pd-lg radius-5">
					<image :src="qr_code" mode="aspectFill" style="width: 360rpx;height: 360rpx;"></image>
				</view>
			</view>
		</uni-popup>
		
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
			return{
				isLoad: false,
				param: {
					name: '',
					balance: ''
				},
				lockTap: false,
				rule: [{
					name: "name",
					checkType: "isNotNull",
					errorMsg: "请输入员工姓名",
					regType: 2
				},{
					name: "balance",
					checkType: "isPercent",
					errorMsg: "请输入佣金比例",
					regType: 1
				}],
				qr_code: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
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
				//this.$util.showLoading()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
				//this.$util.hideAll()
			},
			async getInviteStaffQr(){
				let msg = this.validate(this.param);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try{
					let {path} = await this.$api.channel.inviteStaffQr(this.param)
					this.qr_code = path
					if(path){
						this.$refs.code_box.open()
					}
					this.$util.hideAll()
					this.lockTap = false
				}catch(e){
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
			//表单验证
			validate(param) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					validate.add(param[name], item);
				})
				let message = validate.start();
				return message;
			},
			
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
	}
</style>