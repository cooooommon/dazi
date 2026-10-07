<template>
	<view class="pages-index" v-if="isLoad">
		<view class="space-md"></view>
		<view class="ml-lg mr-lg radius-16 fill-base mb-md" style="overflow: hidden;" v-for="(item,index) in list.data" :key="index">
			<view class="rel" @tap="$util.goUrl({url: `/user/pages/technician-info?id=${item.coach_id}`})">
				<image :src="item.work_img" mode="aspectFill" class="header"></image>
				<view class="abs application-info pl-lg pr-lg">
					<view class="f-title c-base text-bold" style="padding-top: 56rpx;">{{item.coach_name}}</view>
					<view class="flex pt-sm">
						<text class="f-caption c-base">身高 {{item.height}}cm</text>
						<text class="f-caption c-base pl-lg">体重 {{item.weight}}kg</text>
						<text class="f-caption c-base pl-lg">{{item.constellation}}</text>
					</view>
				</view>
				<view class="abs item-status flex-center c-warning fill-base" v-if="item.status == 3">已拒绝</view>
			</view>
			<view class="flex-center cont-btn" v-if="item.status == 1">
				<view class="mr-lg c-title f-desc flex-center refuse-btn btn" @tap="onChange(3 , item.coach_id)">残忍拒绝</view>
				<view class="btn f-desc c-base flex-center" :style="{background: primaryColor}" @tap="onChange(2, item.coach_id)">我要邀约</view>
			</view>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-footer"></view>
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
				options: {},
				list: {
					data: []
				},
				isLoad: false,
				params: {
					page: 1,
					order_id: ''
				},
				loading: true
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		async onLoad(options) {
			this.options = options
			this.params.order_id = options.id
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.getList()
			// 刷新上一个页面 小气泡数据
			this.$util.back()
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
			this.params.page = this.params.page + 1
			this.loading = true;
			this.getList()
		},
		methods: {
			async initRefresh(){
				this.params.page = 1
				this.getList()
			},
			async getList(){
				this.$util.showLoading()
				let {
					params,
					list: oldList
				} = this
				let newList = await this.$api.find.seeApply(params)
				if (params.page == 1) {
					this.list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList;
				}
				this.loading = false;
				this.isLoad = true
				this.$util.hideAll()
			},
			async onChange(status, coach_id){
				if(status == 3){
					let that = this
					uni.showModal({
						title: '提示',
						content: '是否拒绝',
						success:async function (res) {
							if (res.confirm) {
								await that.$api.find.chooseCoach({
									status,
									coach_id,
									order_id: that.options.id
								})
								that.$util.showToast({
									title: '操作成功'
								});
								that.initRefresh()
							}
						}
					});
				}else{
					await this.$api.find.chooseCoach({
						status,
						coach_id,
						order_id: this.options.id
					})
					this.$util.showToast({
						title: '操作成功'
					});
					this.initRefresh()
					this.$util.back()
					this.$util.goUrl({url: 1,openType: 'navigateBack'})
				}
			}
		}
	}
</script>

<style lang="scss">
	.pages-index {
		.header{
			width: 100%;
			height: 570rpx;
		}
		.cont-btn{
			height: 132rpx;
			.btn{
				width: 270rpx;
				height: 73rpx;
				border-radius: 13rpx;
			}
			.refuse-btn{
				border: 1px solid #999;
			}
		}
		.application-info{
			width: 100%;
			height: 172rpx;
			background: linear-gradient(180deg, rgba(0,0,0,0) 0%, #000000 100%);
			bottom: 0;
		}
		.item-status{
			width: 160rpx;
			transform: rotate(45deg);
			top: 20rpx;
			right: -40rpx;
			text-align: center;
			font-size: 20rpx;
			padding: 4rpx 0;
		}
	}
</style>
