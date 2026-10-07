<template>
	<view class="index-income">
		<fixed>
			<view class="fill-base pb-md">
				<view class="pl-md pr-md flex-between index-top">
					<view class="">
						<text class="f-paragraph c-paragraph">姓名：</text>
						<text class="f-paragraph text-bold">{{detail.name}}</text>
					</view>
					<view class="">
						<text class="f-paragraph c-paragraph">上级：</text>
						<text class="f-paragraph text-bold">{{detail.user_name}}</text>
					</view>
					<view class="" @tap="$util.goUrl({url: `/user/pages/channel/poster?qr_code=${detail.qr_path}`})">
						<text class="f-paragraph c-alipay">查看渠道码</text>
					</view>
				</view>
				<view class="flex-between ml-md mr-md search-box radius-10">
					<view class="flex-1 flex-center">
						<view class="flex-1 flex-center">
							<picker mode="date" start="1900-01-01" :end="today" @change="pickerChange($event,'start_time')">
								<view class="flex-y-center"
									:class="[{'f-paragraph c-title':param.start_time},{'f-caption c-caption':!param.start_time}]">
									{{param.start_time || '请选择开始时间'}} 
								</view>
							</picker>
						</view>
						<view class="pl-md pr-md c-caption">——</view>
						<view class="flex-1 flex-center">
							<picker mode="date" start="1900-01-01" :end="today" @change="pickerChange($event,'end_time')">
								<view class="flex-y-center"
									:class="[{'f-paragraph c-title':param.end_time},{'f-caption c-caption':!param.end_time}]">
									{{param.end_time || '请选择结束时间'}} 
								</view>
							</picker>
						</view>
					</view>
					<view class="radius-10 f-paragraph c-base flex-center pl-lg pr-lg pt-sm pb-sm" :style="{background: primaryColor}" @tap="toSearch">搜索</view>
				</view>
			</view>
			
		</fixed>
		
		
		<view class="order-list pl-md pr-md" v-if="isLoad">
			<view class="mt-md fill-base radius-24 pl-lg pr-lg" v-for="(item,index) in list.data" :key="index">
				<view class="pt-lg flex-between">
					<view class="flex">
						<text class="item-title f-desc c-paragraph">下单人</text>
						<text class="f-paragraph text-bold">{{item.nickName}}</text>
					</view>
					<view class="f-paragraph" :style="{color: item.status == 1 ? primaryColor : `#666666`}">{{statusList[item.status]}}</view>
				</view>
				<view class="pt-lg flex">
					<text class="item-title f-desc c-paragraph">付款</text>
					<text class="f-paragraph text-bold">¥{{item.price}}</text>
				</view>
				<view class="pt-lg flex pb-lg">
					<text class="item-title f-desc c-paragraph">下单时间</text>
					<text class="f-paragraph text-bold">{{item.create_time}}</text>
				</view>
				<!-- <view class="pt-lg flex-between">
					<text class="item-title f-desc c-paragraph">预计佣金</text>
					<text class="f-paragraph text-bold c-price">¥230.00</text>
				</view>
				<view class="pt-lg flex-between pb-lg">
					<text class="item-title f-desc c-paragraph">渠道码来源</text>
					<text class="f-paragraph text-bold">曾香寒</text>
				</view> -->
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
				list: {
					data: []
				},
				loading: true,
				tabInd: 0,
				today: '',
				preCheck: {
					tabInd: 0,
					start_time: '',
					end_time: ''
				},
				isLoad: false,
				param: {
					start_time: '',
					end_time: '',
					page: 1
				},
				detail: {},
				statusList: {
					1: '未入账',
					2: '已入账'
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
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
		onLoad() {
			this.today = this.$util.formatTime(new Date(), 'YY-M-D ')
			this.initIndex()
		},
		methods: {
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				this.$util.showLoading()
				await this.getStaffIndex()
				await this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
				this.$util.hideAll()
			},
			async getStaffIndex(){
				this.detail = await this.$api.channel.staffIndex()
			},
			toSearch(){
				this.param.page = 1
				this.getList()
			},
			pickerChange(e, type) {
				let {
					start_time,
					end_time
				} = this.param
				if (type == 'start_time') {
					start_time = e.detail.value
				} else {
					end_time = e.detail.value
				}
			
				if (start_time && end_time && this.$util.DateToUnix(start_time) > this.$util.DateToUnix(end_time)) {
					this.$util.showToast({
						title: `开始时间不能大于结束时间`
					})
					return
				}
				this.param[type] = e.detail.value
			},
			initRefresh(){
				this.param.page = 1
				this.param.start_time = ''
				this.param.end_time = ''
				this.initIndex(true)
			},
			async getList() {
				let {
					list: oldList
				} = this
				let {
					start_time,
					end_time
				} = this.param
				let param = this.$util.deepCopy(this.param)
				if(param.start_time && param.end_time){
					param.start_time = this.$util.DateToUnix(start_time)
					param.end_time = this.$util.DateToUnix(end_time) + 24 * 3600 - 1
				}
				
				let newList = await this.$api.channel.staffCommList(param);
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
		}
	}
</script>

<style lang="scss" scoped>
	.index-income{
		.index-top{
			height: 100rpx;
		}
		.search-info {
			border-radius: 0 0 16rpx 16rpx;
			overflow: hidden;
		
			.item-search {
				flex: 1;
			}
		
			.iconshaixuanxia-1 {
				font-size: 20rpx;
				color: #CDCDCD;
				transform: scale(0.65);
			}
		}
		.order-list{
			.item-title{
				width: 147rpx;
			}
			.c-price{
				color: #FF0000;
			}
		}
		.popup-rank {
			border-radius: 34rpx 34rpx 0 0;
	
			.item-rank {
				width: 157rpx;
				height: 72rpx;
				border: 1px solid #E5E5E5;
			}
	
			.btn-info {
				background: #F9F9F9;
	
				.item-child {
					width: 320rpx;
					height: 80rpx;
					background: #FFFFFF;
					border: 1rpx solid #C7C7C7;
					margin: 0 14rpx;
				}
			}
	
			.space-safe {
				background: #F9F9F9;
			}
		}
		.search-box{
			border: 1px solid #ccc;
		}
	}
	
</style>