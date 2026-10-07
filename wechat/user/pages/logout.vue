<template>
	<view class="mine-pages-setting">
		<view class="flex-center flex-column fill-base mt-lg ml-lg mr-lg radius-24">
			<image mode="aspectFill" class="logout-img" src="https://lbqny.migugu.com/admin/anmo/mine/logout.png"></image>
			<view class="f-title c-title text-bold mb-lg">账户注销，将放弃以下权益和财产</view>
			<view class="f-desc c-caption pb-lg" style="padding-right: 50rpx;" v-html="text"></view>
		</view>
		<view class="pd-lg f-desc c-title">为保护您的权益，请确保您所有订单均已完成且无纠纷，否则将暂时无法进行注销操作</view>

		<fix-bottom-button @confirm="toLoginOut" :text="[{ text: '已清楚风险, 确认注销', type: 'confirm' }]" bgColor="#fff">
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
				isLoad: false,
				options: {},
				text: `1、账号将无法登录<br>2、历史订单将无法查询<br>3、放弃钱余额、卡券等资产<br>`,
				user_info: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
		}),
		async onLoad() {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
		},
		methods: {
			...mapActions(['getUserInfo']),
			...mapMutations(['updateUserItem', 'updateServiceItem', 'updateTechnicianItem', 'updateOrderItem']),
			async toLoginOut() {
				await this.$api.user.delUserInfo()
				this.$util.showToast({
					title: `注销成功`
				})
				let methodArr = ['updateServiceItem', 'updateTechnicianItem', 'updateOrderItem']
				methodArr.map(item => {
					this[item]({
						key: 'pageActive',
						val: false
					})
				})

				let arr = ['autograph', 'userInfo', 'location', 'appLogin', 'loginType',
					'isGzhLogin', 'isShowLogin', 'mineInfo', 'coachInfo', 'userPageType',
				]
				arr.map(key => {
					this.updateUserItem({
						key,
						val: key === 'isShowLogin' ? true : key === 'userPageType' ? 1 : ''
					})
				})

				setTimeout(() => {
					this.$util.goUrl({
						url: `/pages/service`,
						openType: `reLaunch`
					})
				}, 1000)
			}

		}
	}
</script>


<style lang="scss">
	.mine-pages-setting {
		.logout-img {
			width: 165rpx;
			height: 160rpx;
			margin-top: 70rpx;
		}

		.iconfont {
			color: #999
		}
	}
</style>
