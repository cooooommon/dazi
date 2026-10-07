<template>
	<view class="pages-index" v-if="isLoad">
		<fixed>
			<tab @change="handerTabChange" :list="tabList" :activeIndex="currentInd*1" :activeColor="primaryColor"
				:width="100/tabList.length + '%'" height="100rpx" :isRadius="true"></tab>
		</fixed>
		<view class="list pt-md pl-md pr-md">
			<block v-for="(item,index) in list.data" :key="index">
				<view class="radius-16 fill-base pl-lg pr-lg order-item mb-md pb-lg rel">
					<view class="flex-between b-1px-b h-88" @tap="goStore(index)">
						<i class="iconfont icondianpu_1" :style="{color: primaryColor,fontSize: `18px`}"></i>
						<text class="f-mini-title flex-1 pr-lg text-bold pl-sm ellipsis">{{item.store_name}}</text>
						<block v-if="params.status == 4">
							<text class="c-warning text-bold" v-if="item.status == 3">退款失败</text>
							<text class="c-icontext text-bold" v-if="item.status == 2">退款成功</text>
							<text class="text-bold" :style="{color: primaryColor}" v-if="item.status == 1">退款中</text>
						</block>
						<block v-else>
							<text class="c-icontext text-bold" v-if="item.status == 3 && item.is_comment == 1">已评价</text>
							<text class="text-bold" :style="{color: primaryColor}" v-if="item.status == 3 && item.is_comment == 0">待评价</text>
							<text class="text-bold c-title" v-if="item.status == -1">已取消</text>
							<text class="text-bold" :style="{color: primaryColor}" v-if="item.status == 2">待使用</text>
						</block>
					</view>
					<view class="flex-y-center order-cont" @tap="$util.goUrl({url: `/business/pages/manage/order/detail?id=${item.id}&refund=${params.status == 4 ? 1 : 0}`})">
						<image class="order-img radius-16" :src="item.cover" mode="aspectFill"></image>
						<view>
							<view class="f-paragraph c-paragraph line-h48">下单时间：{{$util.formatTime(item.create_time*1000, 'YY-M-D h:m')}}</view>
							<view class="f-paragraph c-paragraph line-h48">数量：{{item.num}}</view>
							<view class="f-paragraph flex-y-center line-h48 c-paragraph">
								<block v-if="params.status == 4">
									退款金额：<text class="c-warning text-bold">¥{{item.apply_price}}</text>
								</block>
								<block v-else>
									实付：<text class="c-warning text-bold">¥{{item.pay_price}}</text>
								</block>
							</view>
						</view>
					</view>
					<view class="f-paragraph pb-md" style="color: #8E8E8E;" v-if="item.status == 3 && item.is_comment == 0">该用户暂未评价, 系统会在7个自然日后默认好评</view>
					<view class="flex-between">
						<block v-if="params.status == 4">
							<text v-if="item.status != 1" class="order-more f-paragraph" @tap.stop="openMore(index)">更多</text>
							<view v-else></view>
						</block>
						<block v-else>
							<text v-if="item.status != 2" class="order-more f-paragraph" @tap.stop="openMore(index)">更多</text>
							<view v-else></view>
						</block>
						<view class="flex-center">
							<block v-if="params.status == 4">
								<block v-if="item.status == 1">
									<view @tap="handleRefund(index, 3)" class="flex-center order-btn c-warning" :style="{border: `1px solid #FF2404`}">拒绝退款</view>
									<view @tap="handleRefund(index, 2)" class="flex-center c-base order-btn ml-md" :style="{border: `1px solid ${primaryColor}`,color:primaryColor}">同意退款</view>
								</block>
							</block>
							<block v-else>
								<block v-if="item.status == 3 && item.is_comment == 1">
									<view @tap.stop="$util.goUrl({url: `/business/pages/manage/order/evaluate?id=${item.id}`})" class="flex-center c-base order-btn ml-md" :style="{background: primaryColor}">查看用户评价</view>
								</block>
								<block v-if="item.status == 2">
									<view class="flex-center c-base order-btn ml-md" :style="{background: primaryColor}" @tap="toHx(index)">去核销</view>
								</block>
							</block>
							
						</view>
					</view>
					<view class="del-order abs" v-if="item.isMore">
						<view class="del-order-top abs"></view>
						<view class="f-paragraph del-order-btn radius-10" @tap="delOrder(index)">删除订单</view>
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
import { hydrate } from "vue"
import { highlight } from "../../../../components/jyf-Parser/libs/config"
	export default {
		data() {
			return {
				current: 0,
				tabList: [{
					title: '全部',
					id: 0,
					number: 0
				}, {
					title: '待核销',
					id: 2,
					number: 0,
				}, {
					title: '已评价',
					id: 3
				}, {
					title: '退款/售后',
					id: 4,
					number: 0
				}],
				params: {
					status: 0, //0全部 2待核销 3已评价
					page: 1
				},
				currentInd: 0,
				isLoad: false,
				loading: true,
				list: {
					data: []
				},
				userPageType: 2,
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			avatarUrl: state => state.config.avatarUrl,
			userInfo: state => state.user.userInfo,
		}),
		async onLoad(options) {
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
				this.currentInd = index
				this.params.status = this.tabList[index].id
				this.initRefresh()
			},
			initRefresh() {
				this.$util.showLoading()
				this.params.page = 1
				this.list.data = []
				this.initIndex(true)
			},
			async initIndex(refresh = false) {
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
				this.getOrderCount()
				await this.getList()
			},
			// 订单计数
			async getOrderCount(){
				let data = await this.$api.business.storeOrderCount()
				this.tabList[1].number = data.status_2
				this.tabList[3].number = data.refund_count
			},
			async getList() {
				let {
					params,
					list: oldList
				} = this
				
				let methodModel = params.status == 4 ? 'storeRefundList' : 'storeOrderList'
				let newList = await this.$api.business[methodModel](params)
				let list = {}
				newList.data.forEach(item => {
					item.isMore = false
				})
				if (params.page == 1) {
					list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					list = newList;
				}
				this.list = list
				this.loading = false;
				this.isLoad = true
				this.$util.hideAll()
			},
			toAppShare() {
				let {
					id: pid
				} = this.userInfo
				let title = '套餐订单'
				let page_url = window.location.href
				if (page_url.includes('?pid=')) {
					page_url = page_url.split('?pid=')[0]
				}
				let href = `${page_url}?pid=${pid}`
				let imageUrl = ''
				this.$jweixin.wxReady(() => {
					this.$jweixin.showOptionMenu()
					this.$jweixin.shareAppMessage(title, '', href, imageUrl)
					this.$jweixin.shareTimelineMessage(title, href, imageUrl)
				})
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
			openMore(index){
				this.list.data[index].isMore = !this.list.data[index].isMore
			},
			toHx(index){
				let that = this
				// #ifdef H5
				this.$jweixin.getScanQRCode().then(res => {
					console.log(res)
					var result = res.resultStr; // 当 needResult 为 1 时，扫码返回的结果
					var resultArr = result.split(','); // 扫描结果以逗号分割数组(一维码)
					var codeContent = resultArr[resultArr.length - 1]; // 获取数组最后一个元素，也就是最终的内容 
					window.location.href = codeContent
				})
				// #endif
				// #ifndef H5
				uni.scanCode({
					success: function (res) {
						that.$util.goUrl({url: `/${res.path}`})
					}
				});
				// #endif
			},
			
			// 删除订单 待核销订单不能删除
			async delOrder(index){
				let [res_del, {
					confirm
				}] = await uni.showModal({
					content: `请确认是否要删除订单`,
				})
				if (!confirm) return;
				let {
					id
				} = this.list.data[index]
				if(this.params.status == 4){
					await this.$api.business.refundDel({
						id
					})
				}else{
					await this.$api.business.delOrder({
						order_id: id
					})
				}
				
				this.$util.showToast({
					title: `删除成功`
				})
				this.list.data.splice(index, 1)
				this.getOrderCount()
			},
			// 再来一单
			async toAgain(index){
				let {
					package_id,
					store_id
				} = this.list.data[index]
				await this.$api.business.storePackInfo({
					id: package_id
				})
				this.$util.goUrl({
					url: `/business/pages/manage/detail?id=${package_id}&storeid=${store_id}&type=order`
				})
			},
			async goStore(index){
				let {
					store_id
				} = this.list.data[index]
				let {status} = await this.$api.business.getInfo({
					id: store_id
				})
				if(status != 2 ){
					this.$util.showToast({
						title: '门店不存在'
					})
					return
				}
				this.$util.goUrl({
					url: `/business/pages/store/detail?id=${store_id}`
				})
			},
			// 拒绝退款
			async handleRefund(index, status){
				let [res_del, {
					confirm
				}] = await uni.showModal({
					content: status == 3 ? `请确认是否要拒绝退款` : `请确认是否要退款`,
				})
				if (!confirm) return;
				let {
					id
				} = this.list.data[index] 
				await this.$api.business.refundCheck({
					id,
					status
				})
				this.$util.showToast({
					title: `操作成功`
				})
				this.params.page = 1
				this.getList()
			},
		}
	}
</script>

<style lang="scss">
	.pages-index {
		.order-item{
			.order-cont{
				padding: 28rpx 0 25rpx 0;
			}
			.order-img{
				width: 140rpx;
				height: 140rpx;
				margin-right: 26rpx;
			}
			.order-more{
				color: #8E8E8E;
			}
			.order-btn{
				min-width: 160rpx;
				height: 63rpx;
				border-radius: 63rpx;
				padding: 0 25rpx;
			}
			.h-88{
				height: 88rpx;
			}
			.line-h48{
				line-height: 48rpx;
			}
			.del-order{
				left: 20rpx;
				bottom: -50rpx;
				z-index: 99;
				.del-order-top{
					width: 0rpx;
					height: 0rpx;
					border: 6px solid #fff;
					border-left-color: transparent;
					border-top-color: transparent;
					border-right-color: transparent;
					top: -12px;
					margin-left: 26rpx;
				}
				
				.del-order-btn{
					width: 180rpx;
					background: #fff;
					height: 70rpx;
					line-height: 70rpx;
					padding-left: 20rpx;
					box-shadow: -1px 0px 6px 0px rgba(200, 200, 200, 1);
				}
			}
		}
	}
</style>
