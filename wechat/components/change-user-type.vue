<template>
	<view class="change-user-type">
		<movable-area class="fix chg-area" :style="{height:`300px`,bottom:`${configInfo.tabbarHeight}px`}" v-if="pShow">
			<movable-view class="chg-view" :x="oldMovable.x" :y="oldMovable.y" direction="vertical" @change="onChange">
				<view class="change-user-box flex-center">
					<view class="flex-1"></view>
					<view @tap.stop="$util.goUrl({ url: `/pages/mine?type=2`, openType: `reLaunch` })"
						class="change-user-btn flex-center flex-column f-icontext c-base radius"
						:style="{background:primaryColor}">
						操作端
					</view>
				</view>
			</movable-view>
		</movable-area>
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
			mineInfo: state => state.user.mineInfo,
			userCoachStatus: state => state.user.userCoachStatus,
			userPageType: state => state.user.userPageType,
			oldMovable: state => state.user.oldMovable,
		}),
		watch: {
			mineInfo(newVal, oldVal) {
				let {
					coach_status
				} = newVal
				this.toSetShow(coach_status)
			},
			userCoachStatus(newVal, oldVal) {
				let {
					status
				} = newVal
				this.toSetShow(status)
			}
		},
		methods: {
			...mapActions(['getConfigInfo']),
			...mapMutations(['updateUserItem']),
			async init(refresh = false) {
				let {
					status = 0
				} = this.userCoachStatus
				this.toSetShow(status)
			},
			toSetShow(status) {
				let {
					coachport = false
				} = this.configInfo.plugAuth
				this.$set(this, 'pShow', [2, 3].includes(status) && !coachport)
			},
			onChange(e) {
				let {
					x,
					y
				} = e.detail
				this.updateUserItem({
					key: 'oldMovable',
					val: {
						x,
						y
					}
				})
			}
		},
	}
</script>

<style scoped lang="scss">
	.chg-area {
		width: 122rpx;
	}


	.chg-view {
		width: 112rpx;
		height: 93rpx;
		background: #FFFFFF;
		box-shadow: 0 1rpx 16rpx 0 rgba(127, 127, 127, 0.39);
		border-radius: 0 100rpx 100rpx 0;
	}

	.change-user-box {
		height: 93rpx;
		padding: 5rpx;

		.change-user-btn {
			width: 83rpx;
			height: 83rpx;
		}

	}
</style>