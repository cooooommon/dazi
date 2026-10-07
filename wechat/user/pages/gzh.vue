<template>
	<view class="mine-pages-gzh flex-center flex-column" v-if="isLoad">
		<image mode="widthFix" class="web-code-img" :src="configInfo.web_code_img" v-if="configInfo.web_code_img">
		</image>
		<view class="gzh-img-info rel" v-else>
			<image mode="aspectFill" class="gzh-img" :src="gzh_img"></image>
			<view class="none-text f-title flex-center abs" v-if="!configInfo.web_code_img">商家还没有放公众号二维码哟~</view>
		</view>

		<view @tap.stop="$util.goUrl({url:`/pages/service`,openType:`redirectTo`})"
			class="home-btn flex-center f-sm-title c-base radius" :style="{background:primaryColor}"
			v-if="!configInfo.web_code_img">直接进入首页</view>
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
				gzh_img: 'https://lbqny.migugu.com/admin/anmo/mine/web-code-img.png'
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			isGzhLogin: state => state.user.isGzhLogin,
		}),
		async onLoad(options) {
			options = await this.updateCommonOptions(options)
			this.options = options
			await this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'updateCommonOptions']),
			...mapMutations(['updateConfigItem']),
			async initIndex() {
				let {
					isGzhLogin
				} = this
				// #ifdef H5
				if (isGzhLogin) {
					setTimeout(() => {
						this.getUserInfo()
					}, 1000)
				} else {
					this.getUserInfo()
				}
				// #endif
				// #ifndef H5
				await this.getUserInfo()
				// #endif  
				await this.getConfigInfo()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
			}
		}
	}
</script>


<style lang="scss">
	page {
		background: #FEFFFE;
	}

	.mine-pages-gzh {
		.web-code-img {
			width: 750rpx;
		}

		.gzh-img-info {
			width: 536rpx;
			height: 552rpx;
			margin-top: 114rpx;

			.gzh-img {
				width: 536rpx;
				height: 552rpx;
				border-radius: 30rpx;
			}

			.none-text {
				width: 536rpx;
				color: #2E2E31;
				bottom: -20rpx;
			}
		}

		.home-btn {
			width: 690rpx;
			height: 90rpx;
			margin-top: 126rpx;
		}
	}
</style>
