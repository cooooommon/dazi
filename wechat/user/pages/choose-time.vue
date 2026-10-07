<template>
	<view>
		<scroll-view class="scroll-left fill-base" scroll-y :scroll-into-view="scrollNav" :scroll-with-animation="true">
			<block v-for="(item,index) in storeDay" :key="index">
				<view @tap="onChangeNav(index,1)" :id="`scrollNav${index}`"
					class="item-child flex-center f-paragraph c-title" :class="[{'active':index==scrollInd}]"
					:style="{color: index == scrollInd ? primaryColor: ''}">
					<view class="flex-center child ellipsis"
						:style="{borderLeft: index == scrollInd ? `5rpx solid ${primaryColor}`:''}">
						{{`${item.dat_text} (${item.week})`}}
					</view>
				</view>
			</block>
		</scroll-view>
		<view class="scroll-right abs fill-base pl-lg">
			<view @tap="onChangeNav(index,2)" class="item-child flex-center f-paragraph c-paragraph"
				style="padding-right: 25rpx;" :style="{color: index == checkInd ? primaryColor: ''}"
				v-for="(item,index) in storeTime" :key="index">
				<view class="flex-1" :class="[{'text-delete': item.status != 1}]">{{item.time_text}}</view>
				<i class="iconfont icon-xuanze-fill" v-if="index == checkInd"></i>
			</view>
			<view class="mg-lg">
				<abnor v-if="!loading&&storeTime.length<=0"></abnor>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions
	} from 'vuex';
	export default {
		data() {
			return {
				options: {},
				storeDay: [],
				storeTime: [],
				loading: false,
				scrollNav: 'scrollNav1',
				scrollInd: 0,
				checkInd: -1
			}
		},
		computed: mapState({
			commonOptions: state => state.user.commonOptions,
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		async onLoad(options) {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			let {
				index = -1
			} = options
			if (index && index.includes('_')) {
				let arr = index.split('_')
				this.scrollInd = arr[0]
				this.checkInd = arr[1]
			}
			this.options = options
			this.$util.showLoading()
			await this.getStoreDay()
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
		},
		methods: {
			async getStoreDay() {
				this.storeDay = await this.$api.order.dayText()
				await this.getStoreTime()
				this.loading = false
				this.$util.hideAll()
			},
			async getStoreTime() {
				let {
					id: coach_id
				} = this.options
				let {
					scrollInd
				} = this
				if (scrollInd < 0) return
				let {
					dat_str: day
				} = this.storeDay[scrollInd]
				this.storeTime = await this.$api.order.timeText({
					coach_id,
					day
				});
				this.loading = false
				this.$util.hideAll()
			},
			async onChangeNav(index, type = 1) {
				let key = type == 1 ? 'storeDay' : 'storeTime'
				if (type == 1) {
					this.checkInd = this.scrollInd == index ? this.checkInd : -1
					this.scrollInd = index
					this.scrollNav = `scrollNav${index}`
					await this.getStoreTime()
				} else {
					let {
						status,
					} = this[key][index]
					if (status != 1) return
					this.checkInd = index
					let item = this[key][index]
					item.time = `${this.storeDay[this.scrollInd].dat_text} ${item.time_text}`
					let send_info = {
						time_index: `${this.scrollInd}_${index}`,
						time: item
					}
					this.$util.getPage(-1).send_info = send_info
					this.$util.goUrl({
						url: 1,
						openType: 'navigateBack'
					})
				}
			},
		},
	}
</script>

<style>
	.scroll-left {
		width: 260rpx;
		height: 100vh;
		position: fixed;
		top: 0;
		bottom: 0;
	}

	.item-child {
		height: 98rpx;
	}

	.item-child.active {
		background: #F9F9F9;
	}

	.item-child .child {
		width: 100%;
		height: 32rpx;
		padding: 0 5%;
	}

	.scroll-right {
		min-height: 100vh;
		top: 0;
		width: 460rpx;
		margin-left: 292rpx;
	}
</style>
