<template>
	<view class="pages-index" v-if="isLoad">
		<fixed>
			<tab @change="handerTabChange" :list="tabList" :activeIndex="params.status*1" :activeColor="primaryColor"
				:width="100/tabList.length + '%'" height="100rpx"></tab>
		</fixed>
		<view class="list pt-lg">
			<block v-for="(item,index) in list.data" :key="index">
				<view class="find-item b-1px-b fill-base mb-md"
					@tap="$util.goUrl({url:`/find/pages/invitation/detail?id=${item.id}&userPageType=${userPageType}&refund=${params.status*1}`})">
					<view class="find-item-top flex-between">
						<view class="flex">
							<image :src="item.is_hide==0 ? item.avatarUrl : avatarUrl" mode="aspectFill"
								class="item-image radius-16"></image>
							<view class="flex-center">
								<view class="pl-md">
									<view class="f-title text-bold">{{item.is_hide==0 ? item.nickName : `匿名用户`}}</view>
									<view class="f-icontext c-caption">{{item.start_time}}</view>
								</view>
							</view>
						</view>
						<!--退款状态-->
						<view v-if="params.status*1 == 4" class="f-paragraph" :style="{color: refundStatus[item.is_refund].color}">{{refundStatus[item.is_refund].text}}</view>
						<block v-else>
							<view class="f-paragraph" style="color: #F12929;" v-if="item.status == 0">审核不通过</view>
							<view class="f-paragraph c-disable" v-if="item.status == -2">超时取消</view>
							<view class="f-paragraph c-disable" v-if="item.status == -1">用户取消</view>
							<view class="f-paragraph" style="color: #0C76FF;"
								v-if="(item.status == 2 && item.apply_status == 0) || (item.status == 2 && userPageType == 1)">待雇佣</view>
							<view class="f-paragraph" style="color: #4CC745;"
								v-if="item.status == 2 && item.apply_status == 1">已报名</view>
							<view class="f-paragraph c-icontext"
								v-if="item.status == 2 && item.apply_status == 3">报名失败</view>
							<view class="f-paragraph" style="color: #0C76FF;" v-if="item.status == 1">待审核</view>
							<view class="f-paragraph c-success" v-if="item.status == 3">已接单</view>
							<view class="f-paragraph c-disable" v-if="item.status == 4">已完成</view>
						</block>
					</view>
					<view class="f-min-title pre-wrap" :class="!item.ellipsis?'ellipsis-3':''">{{item.content}}</view>
					<view class="f-min-title abs pre-wrap" :class="`content`+index"
						style="line-height: 22px;z-index: -1;top: 0;">{{item.content}}</view>
					<view class="f-caption pt-md" :style="{color:primaryColor}" @tap.stop="setMore(index)" v-if="item.textHeight > 66">
						{{item.isMore? `展开`:`收起`}}</view>
					<view class="flex-center">
						<view class="find-images flex-warp">
							<block v-for="(citem,index) in item.img" :key="index">
								<block v-if="item.img.length == 1">
									<image :src="citem" mode="aspectFill" class="find-images-1 radius-16"
										@tap.stop="$util.previewImage({current:citem,urls:item.img})"></image>
								</block>
								<block v-if="item.img.length > 1">
									<image :src="citem" mode="aspectFill" class="images-item radius-16"
										@tap.stop="$util.previewImage({current:citem,urls:item.img})"></image>
								</block>
							</block>
						</view>
					</view>
					<view class="find-time radius-16 pt-sm pb-lg rel">
						<view class="find-time-bg abs radius-16" :style="{backgroundColor:primaryColor}"></view>
						<view class="flex-y-center" v-if="new Date(item.new_start_time).getTime() > new Date().getTime() &&item.status != 4 ">
							<text class="f-desc pl-md pr-md">剩余</text>
							<min-countdown :newtargetTime="item.start_time" :isPlay="true" :type="3" @callback="countEnd">
							</min-countdown>
						</view>
						<view class="pl-md pr-md pt-sm flex-between rel">
							<image v-if="item.type_img" :src="item.type_img" mode="aspectFill" class="ser-image"></image>
							<view class="flex-center ser-image" v-else>
								<view class="iconbianzu-8_2x iconfont" :style="{color:primaryColor,fontSize: '40px'}"></view>
							</view>
							<view class="flex-1">
								<view class="text-bold f-title">{{item.type_name}}</view>
								<view class="flex-y-center">
									<text class="f-mini-title text-bold" style="color: #FF4C88;">{{item.price}}</text>
									<text class="f-icontext">元/单</text>
								</view>
							</view>
							<view class="flex-y-baseline flex-column" v-if="params.status*1 != 4">
								<view class="text-center c-text item-btn ml-md f-desc" v-if="item.status==3"
									:style="{border:`1px solid #ccc`}" @tap.stop="toTel(item.id)">联系Ta</view>
								<view class="text-center c-text item-btn ml-md f-desc" :style="{border:`1px solid #ccc`}"
									v-if="(item.status==1 || item.status==2 ) && userPageType == 1 && [0,3].includes(item.is_refund) && new Date(item.new_start_time).getTime() > new Date().getTime()"
									@tap.stop="changeOrder(item.id, 2 , item.start_time)">取消订单</view>
								<view class="c-base flex-center f-desc item-btn ml-md pl-md pr-md rel"
									v-if="item.status==2 && userPageType == 1" :style="{backgroundColor:primaryColor}"
									@tap.stop="$util.goUrl({url: `/find/pages/application?id=${item.id}`})">
									查看报名人员
									<view class="abs bubble" v-if="item.apply_num > 0">
										<text class="c-base flex-center">{{item.apply_num > 99 ? (item.apply_num + '+') : item.apply_num}}</text>
									</view>
								</view>
								<block v-if="item.status==2 && userPageType!= 1">
									<view class="c-base flex-center f-desc item-btn ml-md" v-if="item.apply_status == 0"
										:style="{backgroundColor:primaryColor}"
										@tap.stop="changeOrder(item.id,3, item.apply_status)">我要报名</view>
									<view class="c-base flex-center f-desc item-btn ml-md" v-if="item.apply_status == 1"
										:style="{backgroundColor:primaryColor}"
										@tap.stop="changeOrder(item.id,3, item.apply_status)">取消报名</view>
									<view class="c-base flex-center f-desc item-btn ml-md c-caption" v-if="item.apply_status == 3"
										:style="{backgroundColor:'#EFEFEF'}" @tap.stop="">我要报名</view>
								</block>
								<view class="c-base text-center f-desc item-btn ml-md"
									v-if="item.status==3 && (item.is_refund == 0 || item.is_refund == 3)"
									:style="{backgroundColor:primaryColor}" @tap.stop="changeOrder(item.id,4,item.end_time)">确认完成</view>
							</view>
						</view>
					</view>
					<!-- <view class="pt-lg flex-between">
						<view class="flex-y-baseline">
							<view class="f-sm-title" style="color: #FF4C88">{{item.price}}</view>
							<view class="f-caption">元/单</view>
						</view>
						<view class="flex-y-center">
							<view class="text-center c-text item-btn ml-md f-desc" v-if="item.status==3"
								:style="{border:`1px solid #ccc`}" @tap.stop="toTel(item.id)">联系Ta</view>
							<view class="text-center c-text item-btn ml-md f-desc" :style="{border:`1px solid #ccc`}"
								v-if="(item.status==1 || item.status==2 || item.status == 3) && userPageType == 1 && [0,3].includes(item.is_refund) && new Date(item.new_start_time).getTime() > new Date().getTime()"
								@tap.stop="changeOrder(item.id, 2 , item.start_time)">取消订单</view>
							<view class="c-base flex-center f-desc item-btn ml-md pl-md pr-md rel"
								v-if="item.status==2 && userPageType == 1" :style="{backgroundColor:primaryColor}"
								@tap.stop="$util.goUrl({url: `/find/pages/application?id=${item.id}`})">
								查看报名人员
								<view class="abs bubble" v-if="item.apply_num > 0">
									<text class="c-base flex-center">{{item.apply_num > 99 ? (item.apply_num + '+') : item.apply_num}}</text>
								</view>
							</view>
							<block v-if="item.status==2 && userPageType!= 1">
								<view class="c-base flex-center f-desc item-btn ml-md" v-if="item.apply_status == 0"
									:style="{backgroundColor:primaryColor}"
									@tap.stop="changeOrder(item.id,3, item.apply_status)">我要报名</view>
								<view class="c-base flex-center f-desc item-btn ml-md" v-if="item.apply_status == 1"
									:style="{backgroundColor:primaryColor}"
									@tap.stop="changeOrder(item.id,3, item.apply_status)">取消报名</view>
								<view class="c-base flex-center f-desc item-btn ml-md c-caption" v-if="item.apply_status == 3"
									:style="{backgroundColor:'#EFEFEF'}" @tap.stop="">我要报名</view>
							</block>
							<view class="c-base text-center f-desc item-btn ml-md"
								v-if="item.status==3 && (item.is_refund == 0 || item.is_refund == 3)"
								:style="{backgroundColor:primaryColor}" @tap.stop="changeOrder(item.id,4)">确认完成</view>
						</view>
					</view> -->
					<view class="pt-md f-desc" style="color: #8A98AF;" v-if="item.order_code">订单编号: {{item.order_code}}
					</view>
					<!--订单拒绝-->
					<view class="b-1px-t mt-md pt-lg" v-if="item.status == 0">
						<view class="f-paragraph" style="color: #5A677E;">拒绝原因: <text class="pre-wrap">{{item.check_text}}</text></view>
						<view class="flex-y-center pt-lg">
							<i class="iconfont icontixingshixin" style="color: #F12929;font-size: 30rpx;"></i>
							<view class=" f-caption pl-sm" style="color: #F12929;">已拒绝订单,金额自动返回原账户</view>
						</view>
					</view>
				</view>
			</block>
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
				current: 0,
				tabList: [{
					title: '全部',
					id: 0
				}, {
					title: '待雇佣',
					id: 1,
					number: 0,
				}, {
					title: '已接单',
					id: 2,
					number: 0,
				}, {
					title: '已完成',
					id: 3
				}, {
					title: '退款/售后',
					id: 4
				}],
				refundStatus: {
					1: {text: '退款中', color: '#4CC745'},
					2: {text: '已退款', color: '#c7c7c7'},
					3: {text: '退款失败', color: '#F12929'}
				},
				params: {
					status: 0, //状态0全部 1未接单 2已接单 3已完成 4售后
					page: 1
				},
				isLoad: false,
				loading: true,
				list: {
					data: []
				},
				userPageType: 2
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			avatarUrl: state => state.config.avatarUrl,
			userInfo: state => state.user.userInfo,
			//userPageType: state => state.user.userPageType,
		}),
		async onLoad(options) {
			this.userPageType = options.userPageType || 2
			uni.setNavigationBarTitle({
				title: this.userPageType == 1 ? '我的发布' : '我的邀约'
			})
			if (options.tab) {
				this.params.status = options.tab
			}
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.$util.showLoading()
			this.initIndex()
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.params.page += 1
			this.$util.showLoading()
			this.getList()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods: {
			...mapActions(['getConfigInfo']),
			handerTabChange(index) {
				this.params.status = index
				this.initRefresh()
			},
			initRefresh(loading = true) {
				if(loading){
					this.$util.showLoading()
				}
				this.params.page = 1
				this.initIndex(true,loading)
			},
			async initIndex(refresh = false,loading = true) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
					//this.toAppShare()
				}
				// #endif
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				await this.getList(loading)
			},
			async getList(loading = true) {
				let {
					params,
					userPageType,
					list: oldList
				} = this
				let model = userPageType == 1 ? 'getList' : 'coachDemandOrderList'
				let newList = await this.$api.find[model](params)
				let list = {}
				newList.data.forEach(item => {
					item.isMore = false
					item.textHeight = 0
				})
				if (params.page == 1) {
					list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					list = newList;
				}
				this.tabList[1].number = newList.status_1
				this.tabList[2].number = newList.status_2
				this.list = list
				this.list.data.forEach((item, index) => {
					item.new_start_time = item.start_time.replace(/-/g, '/')
					setTimeout(() => {
						uni.createSelectorQuery().select('.content' + index).boundingClientRect(
							data => { //目标位置的节点，类class或者id
								if(data){
									item.isMore = data.height > 66 ? true : false
									item.textHeight = data ? data.height : 0
								}
							}).exec();
					}, 100)
				})
				this.loading = false;
				this.isLoad = true
				if(loading){
					this.$util.hideAll()
				}
			},
			// toAppShare() {
			// 	let {
			// 		id: pid
			// 	} = this.userInfo
			// 	let title = this.userPageType == 1 ? '我的发布' : '我的邀约'
			// 	let page_url = window.location.href
			// 	if (page_url.includes('?pid=')) {
			// 		page_url = page_url.split('?pid=')[0]
			// 	}
			// 	let href = `${page_url}?pid=${pid}`
			// 	let imageUrl = ''
			// 	this.$jweixin.wxReady(() => {
			// 		this.$jweixin.showOptionMenu()
			// 		this.$jweixin.shareAppMessage(title, '', href, imageUrl)
			// 		this.$jweixin.shareTimelineMessage(title, href, imageUrl)
			// 	})
			// },
			async changeOrder(id, index, type) {
				if (index == 2) {
					let that = this;
					uni.showModal({
						title: '提示',
						content: '是否确认取消接单',
						success: async function(res) {
							if (res.confirm) {
								await that.$api.find.cancel({
									id
								})
								that.$util.showToast({
									title: '取消成功'
								});
								that.initRefresh(false)
							}
						}
					});
				} else if (index == 3) {
					if (type == 0) {
						await this.$api.find.orderApply({
							order_id: id
						})
						this.$util.showToast({
							title: '报名成功'
						});
					} else if (type == 1) {
						await this.$api.find.cancelApply({
							order_id: id
						})
						this.$util.showToast({
							title: '取消报名成功'
						});
					}
					this.initRefresh(false)
				} else if (index == 4) {
					let date = type.replace(/-/g, '/')
					if(this.userPageType == 2 && new Date(date).getTime() > new Date().getTime() ){
						this.$util.showToast({
							title: '未到服务完成时间'
						});
						return
					}
					let model = this.userPageType == 1 ? 'userComplete' : 'complete'
					await this.$api.find[model]({
						id
					})
					this.$util.showToast({
						title: '已完成'
					});
					this.initRefresh(false)
				}
			},
			setMore(index) {
				this.list.data[index].isMore = !this.list.data[index].isMore
				this.list.data[index].ellipsis = !this.list.data[index].ellipsis
			},
			// 联系客户
			async toTel(id) {
				// $util.goUrl({url: userPageType == 2 ? item.user_phone : item.coach_phone, openType: 'call'})
				let model = 'technician'
				if (this.userPageType == 1) { // 用户端
					model = 'order'
				}
				let url = await this.$api[model].getDemandVirtualPhone({
					order_id: id
				})
				this.$util.goUrl({
					url,
					openType: `call`
				})
			},
			countEnd() {
				this.$util.log("倒计时完了")
				setTimeout(() => {
					this.initRefresh()
				}, 1000)
			},
		}
	}
</script>

<style lang="scss">
	.pages-index {
		.list {

			.find-item {
				padding: 0rpx 35rpx 40rpx 35rpx;
			}

			.find-item-top {
				height: 144rpx;

				.item-image {
					width: 84rpx;
					height: 84rpx;
				}
			}

			.find-images {
				padding: 25rpx 0;
				width: 680rpx;

				.find-images-1 {
					width: 100%;
					height: 376rpx;
				}

				.images-item {
					width: 216rpx;
					height: 216rpx;
					margin-right: 16rpx;
					margin-bottom: 16rpx;
				}

				.images-item:nth-child(3n) {
					margin-right: 0rpx;
				}
			}

			.find-time {
				min-height: 80rpx;
				.find-time-bg{
					opacity: 0.05;
					width: 100%;
					height: 100%;
					left: 0;
					top: 0;
				}
				.ser-image{
					width: 84rpx;
					height: 84rpx;
					border-radius: 84rpx;
					margin-right: 16rpx;
				}
			}

			.item-btn {
				min-width: 140rpx;
				height: 56rpx;
				line-height: 56rpx;
				border-radius: 56rpx;
				margin-top: 6rpx;
				margin-bottom: 6rpx;

				.bubble {
					right: 0;
					top: -15rpx;
					text {
						background-color: #FF5967;
						font-size: 18rpx;
						border-radius: 28rpx;
						width: 28rpx;
						height: 28rpx;
					}
				}
			}

			.item-btn-left {
				width: 140rpx;
				height: 56rpx;
				line-height: 56rpx;
				border-radius: 56rpx;
				border: 1px solid #c7c7c7;
			}

		}
	}
</style>
