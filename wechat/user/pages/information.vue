<template>
	<view class="user-pages-information fill-base" v-if="isLoad">
		<view class="pd-lg f-paragraph">
			<parser :html="detail.information_protection" @linkpress="linkpress" show-with-animation lazy-load>加载中...
			</parser>
		</view>
		<view class="space-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import parser from "@/components/jyf-Parser/index"
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},
				isLoad: false,
				detail: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			loginPage: state => state.user.loginPage,
		}),
		onLoad(options) {
			this.options = options
			this.$util.showLoading()
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				let data = await this.$api.base.getConfig()
				this.detail = data
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.$util.hideAll()
				this.isLoad = true
			},
			initRefresh() {
				this.initIndex(true)
			},
			linkpress(res) {
				// #ifdef APP-PLUS
				this.$util.goUrl({
					url: res.href,
					openType: 'web'
				})
				// #endif
			}
		}
	}
</script>


<style lang="scss">
</style>
