<template>
	<view>
		<fixed>
			<tab @change="handerTabChange" :isLine="true" :list="tabList" :activeIndex="activeIndex*1" color="#9D9D9D"
				:activeColor="primaryColor" :width="100/tabList.length + '%'" height="100rpx" :numberType="2"></tab>
		</fixed>
		<view class="pd-md">
			<view class="pl-lg pr-lg pb-lg fill-base radius-16 mb-md rel item-box" v-for="(item,index) in list.data" :key="index">
				<view class="abs type-name text-center flex-center">
					<view :style="{backgroundColor:primaryColor}" class="taye-name-bg"></view>
					<text class="c-base f-caption" style="z-index: 9;" :style="{color:primaryColor}">{{item.type_name}}</text>
				</view>
				<view class="item-nav flex-between ">
					<text class="f-paragraph text-bold">订单号 {{item.order_code || '无'}}</text>
					<text :style="{color:primaryColor}" v-if="item.status == 1">未处理</text>
					<text class="c-caption" v-else>已处理</text>
				</view>
				<view class="item-cont radius-16 pd-lg">
					<view class="f-paragraph text-bold pb-md">反馈内容</view>
					<view class="c-paragraph ellipsis-2" style="white-space: pre-wrap;">{{item.content}}</view>
				</view>
				<view class="flex pt-lg" style="justify-content:flex-end;" 
				@tap="$util.goUrl({url:`/user/pages/feedback/detail?id=${item.id}`})">
					<view class="reply f-desc c-base text-center" :style="{backgroundColor:primaryColor}">查看详情</view>
				</view>
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
				tabList: [{
					title: '全部反馈',
					sort: 'top desc',
					sign: 1,
				}, {
					title: '未处理',
					sort: 'price',
					sign: 0,
					number: 0,
				}, {
					title: '已处理',
					sort: 'total_sale',
					sign: 0,
				}],
				activeIndex: 0,
				loading: true,
				isLoad: false,
				param: {
					page: 1,
					limit: 10,
					status: 0
				},
				list: {
					data: []
				}

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
			handerTabChange(index) {
				this.activeIndex = index
				this.param.status = index
				this.initIndex();
			},

			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async initIndex(refresh = false){
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				
				this.getList()
			},
			async getList() {
				this.$util.showLoading()
				let {
					list: oldList,
					param
				} = this
				let newList = await this.$api.mine.listFeedback(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.tabList[1].number = newList.wait
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
			},

		}
	}
</script>

<style lang="scss">
	.item-nav {
		height: 96rpx;
	}
	.item-box{
		padding-top: 80rpx;
	}
	.item-cont {
		background-color: #F7F8FA;
		min-height: 218rpx;
	}
	.type-name{
		top: 0;
		left: 0;
		width: 146rpx;
		height: 47rpx;
		line-height: 47rpx;
		overflow: hidden;
		border-bottom-right-radius: 16rpx;
		border-top-left-radius: 16rpx;
		.taye-name-bg{
			position: absolute;
			width: 100%;
			height: 100%;
			left: 0;
			top: 0;
			opacity: 0.1;
		}
	}
	.reply{
		width: 140rpx;
		height: 56rpx;
		line-height: 56rpx;
		border-radius: 56rpx;
	}
</style>
