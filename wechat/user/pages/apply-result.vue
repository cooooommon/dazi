<template>
	<view class="user-apply-result" v-if="isLoad">
		<abnor percent="150%" @confirm="confirm" @cancel="cancel" :title="title[status]" :tip="tipArr[status]"
			:button="buttonArr[status]" :image="image[status]" :tipMax="status == 4? '690rpx':''"></abnor>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"

	export default {
		components: {},
		data() {
			return {
				options: {},
				// -1未申请，1审核中，2审核通过，3取消授权，4审核失败(可再次申请)
				tipArr: {
					'1': [],
					'2': [],
					'3': [{
						text: '平台管理员已取消授权',
						color: 0
					}],
					'4': [{
						text: '请联系平台管理人员询问失败原因',
						color: 0
					}]
				},
				buttonArr: {
					'1': [{
						text: '返回',
						type: 'cancel'
					}],
					'2': [{
						text: '',
						type: 'confirm'
					}],
					'3': [{
						text: '返回',
						type: 'cancel'
					}],
					'4': [{
						text: '再次申请',
						type: 'confirm'
					}, {
						text: '返回',
						type: 'cancel'
					}]
				},
				title: {
					'1': '等待审核',
					'2': '',
					'3': '取消授权',
					'4': '申请失败',
				},
				image: {
					'1': 'https://lbqny.migugu.com/admin/public/apply_wait.jpg',
					'2': 'https://lbqny.migugu.com/admin/public/apply_suc.jpg',
					'3': 'https://lbqny.migugu.com/admin/public/apply_fail.jpg',
					'4': 'https://lbqny.migugu.com/admin/public/apply_fail.jpg',
				},
				isLoad: false,
				status: 1
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			mineInfo: state => state.user.mineInfo,
		}),
		async onLoad(options) {
			this.$util.showLoading()
			this.options = options
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods: {
			...mapActions(['getUserInfo', 'getMineInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await this.getMineInfo()
				let {
					type
				} = this.options
				let {
					coach_status,
					sh_text,
					fx_status,
					fx_text,
					channel_status,
					channel_text
				} = this.mineInfo
				let status = type == 1 ? coach_status : type == 2 ? fx_status : channel_status
				let text = type == 1 ? sh_text : type == 2 ? fx_text : channel_text
				this.status = status

				if (status == 1) {
					this.tipArr[status] = type == 1 ? [{
						text: '审核成功后将直接入驻平台',
						color: 0
					}] : [{
						text: '您已经成功提交申请',
						color: 0
					}, {
						text: '审核将在3个工作日内出结果，请耐心等待',
						color: 0
					}]
				}
				if (status == 2) {
					this.tipArr[status] = type == 1 ? [{
						text: '恭喜您，审核通过！',
						color: 0
					}] : type == 2 ? [{
						text: '您已具备分销商资格，快去分享体验吧~',
						color: 0
					}] : [{
						text: '您已具备渠道商资格，快去分享体验吧~',
						color: 0
					}]
					this.buttonArr[status][0].text = type == 1 ? `去管理` : `去分享`
					this.title[status] = type == 1 ? `入驻成功` : `审核通过`
				}
				if (status == 4 && text) {
					this.tipArr[status][0].text = text
				}
				this.$util.hideAll()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
				uni.setNavigationBarTitle({
					title: this.title[status]
				})
			},
			initRefresh() {
				this.isLoad = false
				this.initIndex(true)
			},
			// 去管理/再次申请
			async confirm() {
				let {
					status
				} = this
				let {
					type
				} = this.options
				let page = {
					1: `/technician/pages/apply`,
					2: `/user/pages/distribution/apply`,
					3: `/user/pages/channel/apply`
				}
				let url = status == 2 ? type == 1 ? `/pages/mine?type=2` : `/pages/service` : page[type]
				this.$util.goUrl({
					url,
					openType: status == 2 ? `reLaunch` : `redirectTo`
				})
			},
			// 返回首页
			cancel() {
				if (getCurrentPages().length > 1) {
					this.$util.back()
					this.$util.goUrl({
						url: 1,
						openType: `navigateBack`
					})
					return
				}
				this.$util.goUrl({
					url: `/pages/mine`,
					openType: `reLaunch`
				})
			},
		}
	}
</script>


<style lang="scss">
	page {
		background: white;
	}
</style>
