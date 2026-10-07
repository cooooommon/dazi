<template>
	<view class="custom-tabbar fix flex-center fill-base b-1px-t">
		<view @tap.stop="changeTab(item)" class="flex-center flex-column mt-sm"
			:style="{width: (100/configInfo.tabBar.length) + '%',color:cur == item.id ? primaryColor : '#666'}"
			v-for="(item,index) in configInfo.tabBar" :key="index">
			<i class="iconfont"
				:class="[{'icon-font-color':cur == item.id},cur == item.id ? item.selected_img : item.default_img]"
				:style="{ backgroundImage: cur == item.id ? `linear-gradient(180deg, ${gradualColor} 0%, ${primaryColor} 100%)` : '' }"></i>
			<view class="text">{{item.name}}</view>
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
			cur: {
				type: Number,
				default () {
					return 0
				}
			},
		},
		data() {
			return {}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			gradualColor: state => state.config.configInfo.gradualColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userPageType: state => state.user.userPageType,
			activeIndex: state => state.order.activeIndex,
		}),
		async mounted() {
			let that = this;
			let sysheight = uni.getSystemInfoSync().windowHeight
			let {
				navBarHeight
			} = that.configInfo
			setTimeout(() => {
				const query = uni.createSelectorQuery().in(that);
				query.select('.custom-tabbar').boundingClientRect(data => {
					let curSysHeight = sysheight - data.height - navBarHeight
					let configInfo = that.$util.deepCopy(this.configInfo)
					configInfo.curSysHeight = curSysHeight
					configInfo.tabbarHeight = data.height
					that.updateConfigItem({
						key: 'configInfo',
						val: configInfo
					})
				}).exec();
			}, 600)
		},
		methods: {
			...mapActions(['getConfigInfo']),
			...mapMutations(['updateConfigItem']),
			// 点击跳转
			async changeTab(item) {
				if (!item.id) {
					await this.getConfigInfo()
					let arr = this.configInfo.tabBar.filter(aitem => {
						return aitem.name == item.name
					})
					item = arr[0]
				}
				let {
					id: index
				} = item
				let {
					activeIndex = 0,
					cur,
					userPageType
				} = this
				let page = {
					1: `/pages/service`,
					2: `/pages/technician`,
					3: `/pages/dynamic`,
					4: `/pages/order?tab=${activeIndex}`,
					5: `/pages/mine?type=${userPageType}`,
					6: `/pages/shopstore`,
					7: `/pages/find`,
					8: `/pages/store`,
					9: `/pages/seckill`,
				}
				if (index == cur) return
				this.$util.goUrl({
					url: page[index],
					openType: `reLaunch`
				})
			},
		},
	}
</script>

<style scoped lang="scss">
	.custom-tabbar {
		height: 98rpx;
		bottom: 0;
		height: calc(98rpx + env(safe-area-inset-bottom) / 2);
		padding-bottom: calc(env(safe-area-inset-bottom) / 2);

		.iconfont {
			font-size: 40rpx;
		}

		.text {
			font-size: 22rpx;
			margin-top: 5rpx;
			height: 32rpx;
		}
	}
</style>
