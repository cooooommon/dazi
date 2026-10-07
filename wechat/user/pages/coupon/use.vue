<template>
	<view class="user-coupon-use">
		<view @tap.stop="toUse(index)" class="list-item rel mt-md ml-lg mr-lg fill-base radius-16"
			v-for="(item,index) in list.data" :key="index">
			<view class="item-title flex-center pl-md pr-md abs f-paragraph c-warning">{{item.title}}</view>
			<view class="mt-lg pd-lg">
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
						:style="{color: primaryColor, border: `1rpx solid ${primaryColor}`}">
						立即使用
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

		<view class="space-max-footer"></view>
		
		<fix-bottom-button @confirm="toUse" :text="[{text:'不使用卡券',type:'confirm'}]" bgColor="#fff"
			v-if="list.data&&list.data.length>0">
		</fix-bottom-button>
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
				param: {
					page: 1,
					coach_id: ''
				},
				list: {
					data: []
				},
				loading: true
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
		}),
		async onLoad() {
			this.coach_id = this.$util.getPage(-1).options.id
			await this.initIndex()
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
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
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				let {
					list: oldList,
					param,
					coach_id
				} = this
				param.coach_id = coach_id
				let newList = await this.$api.order.couponList(param);
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
			toShowItem(index) {
				let {
					is_show
				} = this.list.data[index]
				this.list.data[index].is_show = !is_show
			},
			toUse(index = -1) {
				let id = 0
				let is_member = 1
				if(index > -1){
					id = this.list.data[index].id
					is_member = 0
				}
				this.$util.getPage(-1).orderInfo.coupon_id = id
				this.$util.getPage(-1).is_member = is_member
				
				this.$util.back()
				setTimeout(() => {
					this.$util.goUrl({
						url: 1,
						openType: 'navigateBack'
					})
				}, 1000)
			},
		}
	}
</script>


<style lang="scss">
	.user-coupon-use {
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
