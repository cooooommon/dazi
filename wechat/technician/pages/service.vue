<template>
	<view class="technician-service" v-if="isLoad">
		<view class="flex-warp mt-lg pt-sm ml-lg mr-lg">
			<block v-for="(item,index) in list.data" :key="index">
				<view @tap.stop="toCheckItem(item.id)" class="list-item flex-center mr-md mb-md f-desc rel">
					<view class="list-item abs" :style="{background:primaryColor}" v-if="service.includes(item.id)">
					</view>
					<view :style="{color:service.includes(item.id)?primaryColor:''}">{{item.title}}</view>
				</view>
			</block>
		</view>

		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-max-footer"></view>

		<fix-bottom-button @cancel="cancel" @confirm="confirm"
			:text="[{text:'重置',type:'cancel',color:'#142C57'},{text:'确定',type:'confirm'}]" bgColor="#fff" :classType="2"
			borderRadius="16rpx">
		</fix-bottom-button>
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
				loading: true,
				param: {
					page: 1
				},
				list: {
					data: []
				},
				lockTap: false,
				service: []
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			let {
				service
			} = this.$util.getPage(-1).form
			this.service = this.$util.deepCopy(service)
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
		methods: {
			...mapActions(['getConfigInfo']),
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
				await this.getList()
				this.isLoad = true
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				this.$util.showLoading()
				let {
					list: oldList,
					param,
				} = this
				let newList = await this.$api.technician.serviceSelect(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			toCheckItem(id) {
				let ind = this.service.findIndex(item => {
					return item == id
				})
				if (ind == -1) {
					this.service.push(id)
				} else {
					this.service.splice(ind, 1)
				}
			},
			cancel() {
				this.service = []
			},
			confirm() {
				this.$util.getPage(-1).form.service = this.service
				this.$util.goUrl({
					url: 1,
					openType: `navigateBack`
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background: #fff;
	}

	.technician-service {
		.list-item {
			min-width: 168rpx;
			height: 68rpx;
			padding: 0 20rpx;
			color: #4A4A4A;
			background: #F6F7F8;
			border-radius: 34rpx;
		}

		.list-item.abs {
			opacity: 0.1;
			border-radius: 34rpx;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 1;
		}
	}
</style>
