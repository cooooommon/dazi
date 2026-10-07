<template>
	<view class="index-income">
		<fixed :zIndex="99">
			<view class="search-info flex-between fill-base">
				<view class="item-search pt-sm pb-sm">
					<search @input="toSearch" @confirm="toSearch" type="input" :padding="20" placeholder="搜索渠道码来源"></search>
				</view>
				<view @tap="$refs.rank_item.open()" class="flex-center pr-md f-paragraph c-title">
					<!-- <i class="iconfont iconshaixuan2 mr-sm"></i> -->
					筛选
					<i class="iconfont icon-down" style="font-size: 8px;color: #CDCDCD;margin-left: 6rpx;"></i>
				</view>
			</view>
			<view class="h-90 flex-y-center  f-paragraph pl-md pr-md" style="background: #f6f6f6;">
				<view class="pr-sm flex-y-baseline" >
					<text>累计订单金额</text>
					<text class="f-caption c-caption">(不包含退款)</text>
					<text>：¥{{pay_price}}</text>
				</view>
				<view class="pl-lg flex-y-baseline" >
					<text>预计总佣金：</text>
					<text>¥{{cash}}</text>
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
					<text class="f-paragraph text-bold">¥{{item.pay_price}}</text>
				</view>
				<view class="pt-lg flex pb-lg b-1px-b">
					<text class="item-title f-desc c-paragraph">下单时间</text>
					<text class="f-paragraph text-bold">{{item.create_time}}</text>
				</view>
				<view class="pt-lg flex-between pb-lg">
					<text class="item-title f-desc c-paragraph">预计佣金</text>
					<text class="f-paragraph text-bold c-price">¥{{item.cash}}</text>
				</view>
				<view class="flex-between pb-lg" v-if="item.name">
					<text class="item-title f-desc c-paragraph">渠道码来源</text>
					<text class="f-paragraph text-bold">{{item.name}}</text>
				</view>
			</view>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-footer"></view>
		
		<uni-popup ref="rank_item" type="bottom" :maskClick="false">
			<view class="popup-rank fill-base">
				<view class="flex-between pd-lg">
					<view class="f-title c-title text-bold">选择筛选条件</view>
					<view @tap="toConfirm(1)" class="f-caption c-caption">取消</view>
				</view>
				<view class="pd-lg">
					<view class="pt-lg pb-lg f-paragraph text-bold">状态</view>
					<view class="flex-warp pb-lg">
						<view @tap="toChangeItem(index,'typeId')" class="item-rank flex-center f-desc c-title radius-16"
							:class="[{'ml-md':index!=0}]"
							:style="{background:index==typeId?primaryColor:'',color:index==typeId?'#fff':'',borderColor:index==typeId?primaryColor:''}"
							v-for="(item,index) in tabType" :key="index">{{item.title}}</view>
					</view>
					<view class="pt-lg pb-lg f-paragraph text-bold">时间</view>
					<view class="flex-warp pb-lg">
						<view @tap="toChangeItem(index,'tabInd')" class="item-rank flex-center f-desc c-title radius-16"
							:class="[{'ml-md':index!=0}]"
							:style="{background:index==tabInd?primaryColor:'',color:index==tabInd?'#fff':'',borderColor:index==tabInd?primaryColor:''}"
							v-for="(item,index) in tabList" :key="index">{{item.title}}</view>
					</view>
					<view class="flex-between pt-lg pb-lg" v-if="tabInd==3">
						<view class="f-paragraph text-bold">开始时间</view>
						<picker mode="date" start="1900-01-01" :end="today" @change="pickerChange($event,'start_time')">
							<view class="flex-y-center f-title"
								:class="[{'c-title':tabList[3].start_time},{'f-caption c-caption':!tabList[3].start_time}]">
								{{tabList[3].start_time || '请选择'}} <i class="iconfont icon-right c-caption"></i>
							</view>
						</picker>
					</view>
					<view class="flex-between pt-md pb-lg" v-if="tabInd==3">
						<view class="f-paragraph text-bold">结束时间</view>
						<picker mode="date" start="1900-01-01" :end="today" @change="pickerChange($event,'end_time')">
							<view class="flex-y-center f-title"
								:class="[{'c-title':tabList[3].end_time},{'f-caption c-caption':!tabList[3].end_time}]">
								{{tabList[3].end_time || '请选择'}} <i class="iconfont icon-right c-caption"></i>
							</view>
						</picker>
					</view>
				</view>
				<view class="btn-info flex-center pd-lg">
					<view @tap="toConfirm(2)" class="item-child flex-center fill-base f-desc radius">重置</view>
					<view @tap="toConfirm(3)" class="item-child flex-center f-desc c-base radius"
						:style="{background:primaryColor,borderColor:primaryColor}">查询</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</uni-popup>
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
				tabInd: -1,
				tabList: [{
					id: 1,
					title: '今日',
					start_time: '',
					end_time: ''
				}, {
					id: 2,
					title: '近7日',
					start_time: '',
					end_time: ''
				}, {
					id: 3,
					title: '近30日',
					start_time: '',
					end_time: ''
				}, {
					id: 4,
					title: '自定义',
					start_time: '',
					end_time: ''
				}],
				tabType: [{
					id: 0,
					title: '全部',
				},{
					id: 1,
					title: '未结算',
				},{
					id: 2,
					title: '已结算',
				}],
				typeId: 0,
				today: '',
				preCheck: {
					tabInd: -1,
					typeId: 0,
					start_time: '',
					end_time: ''
				},
				param: {
					page: 1,
					name: ''
				},
				isLoad: false,
				statusList: {
					1: '未入账',
					2: '已入账'
				},
				pay_price: 0,
				cash: 0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
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
				let cur_time = new Date(Math.ceil(new Date().getTime()))
				let today = this.$util.formatTime(cur_time, 'YY-M-D')
				let one = 3600 * 24 * 1000
				let time = this.$util.DateToUnix(today) * 1000
				this.today = today
				this.tabList[0].start_time = today
				this.tabList[0].end_time = today
				this.tabList[1].start_time = this.$util.formatTime(time - 7 * one, 'YY-M-D')
				this.tabList[1].end_time = today
				this.tabList[2].start_time = this.$util.formatTime(time - 30 * one, 'YY-M-D')
				this.tabList[2].end_time = today
				await this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
				this.$util.hideAll()
			},
			initRefresh() {
				this.param.page == 1
				this.initIndex(true)
			},
			toSearch(val) {
				this.param.page = 1
				this.param.name = val
				this.getList()
			},
			async getList() {
				let {
					list: oldList,
					tabInd,
					tabList,
					typeId
				} = this
				
				let param = this.$util.deepCopy(this.param)
				if(tabInd == -1){
					param.start_time = ''
					param.end_time = ''
				}else{
					let {
						start_time,
						end_time
					} = tabList[tabInd]
					param.start_time = this.$util.DateToUnix(start_time)
					param.end_time = this.$util.DateToUnix(end_time) + 24 * 3600 - 1
				}
				
				param.status = typeId
			
				let newList = await this.$api.channel.commList(param);
			
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.pay_price = newList.pay_price
				this.cash = newList.cash
				this.loading = false
				this.$util.hideAll()
			},
			pickerChange(e, type) {
				let {
					start_time,
					end_time
				} = this.tabList[3]
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
			
				this.tabList[3][type] = e.detail.value
			},
			toChangeItem(index, key) {
				if(this[key] == index && key == 'tabInd') return
				this.preCheck.start_time = ''
				this.preCheck.end_time = ''
				this.tabList[3].start_time = ''
				this.tabList[3].end_time = ''
				this[key] = index
			},
			// type 1取消；2重置；3查询
			toConfirm(type, ind = 3) {
				if (type == 2) {
					ind = 0
				}
				let {
					start_time = '',
						end_time = ''
				} = type == 1 ? this.preCheck : this.tabList[ind]
				let {
					tabInd,
					typeId
				} = type == 2 ? {
					tabInd: -1,
					typeId: 0
				} : type == 3 ? this : this.preCheck
				if (type == 3) {
					if ((!start_time || !end_time) && tabInd == 3) {
						this.$util.showToast({
							title: !start_time ? `请选择开始时间` : `请选择结束时间`
						})
						return
					}
					this.preCheck.start_time = start_time
					this.preCheck.end_time = end_time
					this.preCheck.tabInd = tabInd
					this.preCheck.typeId = typeId
				} else {
					this.tabList[ind].start_time = start_time
					this.tabList[ind].end_time = end_time
					this.tabInd = tabInd
					this.typeId = typeId
				}
				
				if (type != 2) {
					this.$refs.rank_item.close()
					this.param.page = 1
					this.getList()
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.index-income{
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
		.h-90{
			height: 90rpx;
		}
	}
	
</style>