<template>
	<view class="open-location-info flex-center fix" :style="{top:isAdd?`${configInfo.navBarHeight}px`:`0px`}">
		<view class="open-location pd-md flex-center radius-16 abs" v-if="pShow">
			<view @tap.stop="toClose" class="open-close flex-center abs">
				<i class="iconfont icon-add c-base"></i>
			</view>
			<view class="flex-1 c-base">
				<view class="f-mini-title text-bold">定位服务未开启</view>
				<view class="f-caption mt-sm">开启后才能看到附近的信息哟～</view>
			</view>
			<view @tap.stop="$util.checkAuth({ type: 'userLocation' , checkApp:true})"
				class="open-btn flex-center f-desc c-base radius" :style="{background:primaryColor}">去开启</view>
		</view>

		<view class="open-location address pd-md radius-16 abs" v-if="home && !pageActive && mShow">
			<view class="flex-y-center f-caption text-bold c-caption"><i class="iconfont iconjuli mr-sm"
					:style="{color:primaryColor}"></i>
				位置权限使用说明</view>
			<view class="f-mini-title c-base mt-sm">根据您的位置信息为您推荐服务的城市</view>
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
		props: {
			isAdd: {
				type: Boolean,
				default () {
					return true
				}
			},
			pageActive: {
				type: Boolean,
				default () {
					return false
				}
			},
			home: {
				type: Boolean,
				default () {
					return false
				}
			}
		},
		mounted() {
			this.init()
		},
		data() {
			return {
				pShow: false,
				mShow: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			location: state => state.user.location,
			isShowOpenLocation: state => state.user.isShowOpenLocation
		}),
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			...mapMutations(['updateUserItem']),
			init() {
				let {
					lat,
					lng
				} = this.location
				let show = lat && lng ? false : true
				this.$set(this, 'pShow', show)
				this.$set(this, 'mShow', show)
			},
			toClose() {
				this.$set(this, 'pShow', false)
			}
		}
	}
</script>

<style scoped lang="scss">
	.open-location-info {
		left: 0rpx;
		width: 100%;
		z-index: 1000;
	}

	.open-location {
		top: 30rpx;
		width: 690rpx;
		height: 130rpx;
		background: rgba(0, 0, 0, 0.8);
		z-index: 1001;

		.open-btn {
			width: 150rpx;
			height: 54rpx;
			margin-top: 24rpx;
		}

		.open-close {
			top: 0;
			right: 0;
			width: 40rpx;
			height: 40rpx;
			background: #C0C0C0;
			border-radius: 0 16rpx 0 16rpx;

			.iconfont {
				font-size: 24rpx;
				transform: rotate(45deg);
			}
		}
	}

	.open-location.address {
		top: 0rpx;
		width: 730rpx;
		height: auto;
		background: #000;
		z-index: 1002;
	}
</style>