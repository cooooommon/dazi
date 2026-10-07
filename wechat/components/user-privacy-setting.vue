<template>
	<uni-popup ref="user_privacy_setting" type="center" :custom="true" :zIndex="9999999992">
		<view class="common-popup-content fill-base radius-26">
			<view class="title">小程序隐私保护指引</view>
			<span class="f-paragraph c-title mt-lg">
				在您使用之前，请仔细阅读<span @tap.stop="openPrivacyContract"
					:style="{color:primaryColor}">{{userPrivacySetting.privacyContractName}}</span>，如您同意{{userPrivacySetting.privacyContractName}},请点击“同意”开始使用。
			</span>
			<view class="button">
				<view @tap.stop="toConfirmOper(1)" class="item-child">拒绝</view>
				<button id="agree-btn" open-type="agreePrivacyAuthorization"
					@agreeprivacyauthorization="toConfirmOper(2)" class="item-child"
					:style="{background:primaryColor,color:'#fff'}">
					同意
				</button>
			</view>
		</view>
	</uni-popup>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		name: 'userPrivacySetting',
		components: {},
		props: {
			show: {
				type: Boolean,
				default () {
					return true
				}
			}
		},
		mounted() {
			this.init()
		},
		data() {
			return {}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userPrivacySetting: state => state.user.userPrivacySetting
		}),
		methods: {
			...mapActions(['getPrivacySetting']),
			...mapMutations(['updateUserItem']),
			async init() {
				await this.getPrivacySetting() 
				if (!this.show) return
				let privacyCheck = this.check()
				if (privacyCheck) {
					this.open()
				}
			},
			check() {
				let {
					needAuthorization
				} = this.userPrivacySetting
				return needAuthorization
			},
			open() {
				this.$refs.user_privacy_setting.open()
			},
			openPrivacyContract() {
				wx.openPrivacyContract({
					success: () => {}, // 打开成功
					fail: () => {}, // 打开失败
					complete: () => {}
				})
			},
			async toConfirmOper(type = 1) {
				let val = type == 1 ? {
					event: 'disagree'
				} : {
					buttonId: 'agree-btn',
					event: 'agree'
				}
				this.updateUserItem({
					key: 'resolvePrivacy',
					val
				})
				if (type == 2) {
					await this.getPrivacySetting()
				}
				this.$refs.user_privacy_setting.close()
			}
		}
	}
</script>

<style scoped lang="scss">
</style>