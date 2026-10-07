<template>
	<view class="member-rule" :style="{background:pageColor}" v-if="isLoad">
		<view class="pd-lg f-paragraph">
			<parser :html="detail.text" @linkpress="linkpress" show-with-animation lazy-load>加载中...
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
		computed: mapState({}),
		onLoad(options) {
			this.options = options
			this.$util.showLoading()
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				this.detail = this.$util.getPage(-1).detail
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.$util.hideAll()
				this.isLoad = true
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
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