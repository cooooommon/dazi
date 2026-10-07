<template>
	<view class="user-coupon-list">
		<fixed>
			<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1" :activeColor="primaryColor"
				width="33.3%" height="100rpx"></tab>
			<view class="b-1px-b"></view>
		</fixed>

		<view @tap.stop="goDetail(index)" class="list-item rel mt-md ml-lg mr-lg fill-base radius-16"
			:class="[{'grayscale':activeIndex>0}]" v-for="(item,index) in list.data" :key="index">
			<view class="item-title flex-center pl-md pr-md abs f-paragraph c-warning">{{item.title}}</view>
			<view class=" mt-lg pd-lg">
				<view class="flex-between">
					<view class="f-caption c-caption">
						<view class="flex-y-baseline">
							<view class="flex-y-baseline c-warning">¥<view class="f-md-title">{{item.discount}}</view>
							</view>
							<view class="ml-sm">{{item.type==0?`满${item.full}元可用`:'无门槛'}}</view>
						</view>
						<view>数量：x{{item.num}}</view>
					</view>
					<view class="use-btn flex-center f-caption radius"
						:style="{color:primaryColor,background:activeIndex>0?'#eee':'',border: activeIndex>0?'1rpx solid #eee':`1rpx solid ${primaryColor}`}">
						{{statusType[item.status]}}
					</view>
				</view>
				<view class="f-caption c-caption mt-sm">有效期：{{item.start_time}}</view>
			</view>
			<view @tap.stop="toShowItem(index)"
				class="flex-between fill-space f-caption c-caption pt-md pb-md pl-lg pr-lg b-1px-t">
				<view>查看详情</view>
				<i class="iconfont icon-right" :class="[{'rotate-90':!item.is_show},{'rotate-270':item.is_show}]"></i>
			</view>
			<view class="pl-lg pr-lg pb-lg fill-space f-desc c-caption" v-if="item.is_show">
				<view class="flex-warp">
					<view>使用规则：</view>
					<view class="flex-1">
						<text decode="emsp" style="word-break:break-all;">{{item.rule}}</text>
					</view>
				</view>
				<view class="flex-warp mt-md">
					<view>优惠详情：</view>
					<view class="flex-1">
						<text decode="emsp" style="word-break:break-all;">{{item.text}}</text>
					</view>
				</view>
				<view class="flex-warp mt-md">
					<view>限用服务：</view>
					<view class="flex-1">
						<view :class="[{'mt-sm':aindex!=0}]" v-for="(aitem,aindex) in item.service" :key="index">
							{{aitem.title}}
						</view>
					</view>
				</view>
			</view>
		</view>

		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor type="COUPON" v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>

		<view class="space-footer"></view>

	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		data() {
			return {
				options: {},
				activeIndex: 0,
				tabList: [{
					title: '待使用',
					id: 1
				}, {
					title: '已使用',
					id: 2,
				}, {
					title: '已过期',
					id: 3
				}],
				statusType: {
					1: '去使用',
					2: '已使用',
					3: '删除',
				},
				param: {
					page: 1,
				},
				list: {
					data: []
				},
				loading: true,
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.options = options
			let {
				tab = 0
			} = options
			this.activeIndex = tab
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
			...mapMutations(['']),
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
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.param.page = 1;
				this.initIndex(true)
			},
			async getList() {
				let {
					list: oldList,
					param,
					tabList,
					activeIndex
				} = this
				param.status = tabList[activeIndex].id
				let newList = await this.$api.mine.userCouponList(param);
				newList.data.map(item => {
					item.is_show = false
				})

				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			handerTabChange(index) {
				this.activeIndex = index;
				this.$util.showLoading()
				this.param.page = 1;
				this.list.data = []
				this.getList()
			},
			toShowItem(index) {
				let {
					is_show
				} = this.list.data[index]
				this.list.data[index].is_show = !is_show
			},
			async goDetail(index) {
				let {
					activeIndex
				} = this

				switch (activeIndex) {
					case 0:
						this.$util.goUrl({
							url: `/pages/service`,
							openType: `reLaunch`
						})
						break;
					case 1:
						break;
					case 2:
						let {
							id: coupon_id
						} = this.list.data[index]
						if (this.lockTap) return
						this.lockTap = true
						this.$util.showLoading()
						try {
							await this.$api.mine.couponDel({
								coupon_id
							})
							this.$util.hideAll()
							this.$util.showToast({
								title: `删除成功`
							})
							this.list.data.splice(index, 1)
							this.lockTap = false
						} catch (e) {
							setTimeout(() => {
								this.lockTap = false
								this.$util.hideAll()
							}, 2000)
						}
						break;
				}

			}
		}
	}
</script>


<style lang="scss">
	.user-coupon-list {
		.list-item {
			overflow: hidden;

			.item-title {
				top: 0;
				left: 0;
				height: 50rpx;
				background: #FFE2E2;
				border-radius: 15rpx 0 15rpx 0;
			}

			.use-btn {
				width: 140rpx;
				height: 56rpx;
				transform: rotateZ(360deg);
			}
		}
	}
</style>
