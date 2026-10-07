<template>
	<view>
		<view class="login-info pd-lg flex-center rel" :style="{bottom:`${configInfo.tabbarHeight}px`}" v-if="pShow">
			<view @tap.stop="toClose" class="login-close flex-center radius abs">
				<i class="iconfont icon-add c-base"></i>
			</view>
			<image mode="aspectFill" lazy-load class="logo-img radius" :src="configInfo.app_logo"></image>
			<view class="flex-center flex-1 ml-md">
				<view class="flex-1">
					<view class="f-title c-base max-380 ellipsis">{{`欢迎来到${configInfo.app_text}`}}</view>
					<view class="text f-caption">登录后获取更多精彩内容</view>
				</view>
				<view @tap.stop="toLogin" class="login-btn flex-center f-desc c-base radius"
					:style="{background:primaryColor}">去登录</view>
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
		components: {},
		props: {},
		mounted() {
			this.init()
		},
		data() {
			return {
				pShow: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			isShowLogin: state => state.user.isShowLogin
		}),
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			...mapMutations(['updateUserItem']),
			init() {
				let {
					isShowLogin
				} = this
				this.$set(this, 'pShow', isShowLogin)
			},
			toClose() {
				this.updateUserItem({
					key: 'isShowLogin',
					val: false
				})
				this.$set(this, 'pShow', false)
			},
			toLogin() {
				let pages = getCurrentPages();
				let {
					route
				} = pages[pages.length - 1]
				this.updateUserItem({
					key: 'loginPage',
					val: `/${route}`
				})
				this.$util.goUrl({
					url: `/pages/login`
				})
			}
		},
	}
</script>

<style scoped lang="scss">
	.login-info {
		position: fixed;
		left: 20rpx;
		width: 710rpx;
		height: 139rpx;
		background: rgba(0, 0, 0, 0.8);
		border-radius: 16rpx;

		.logo-img {
			width: 90rpx;
			height: 90rpx;
		}

		.text {
			color: #9E9E9E;
		}

		.login-btn {
			width: 150rpx;
			height: 54rpx;
		}

		.login-close {
			top: -10rpx;
			right: -10rpx;
			width: 40rpx;
			height: 40rpx;
			background: #000;

			.iconfont {
				font-size: 24rpx;
				transform: rotate(45deg);
			}
		}
	}
</style>
