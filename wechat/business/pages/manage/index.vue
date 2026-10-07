<template>
	<view class="manage-pages rel">
		<view class="abs manage-bg" :style="{background: `linear-gradient(${primaryColor}, #F5F5F5)`}"></view>
		<view class="flex-center pl-lg pr-lg pt-lg pb-lg rel">
			<i class="iconfont icondianpu_1 c-base" style="font-size: 20px;"></i>
			<view class="flex-1 f-ms-title c-base ellipsis pr-lg pl-sm">{{storeInfo.name}}</view>
			<i class="iconfont iconsaoyisao c-base" style="font-size: 20px;" @tap="toHx" v-if="configInfo.plugAuth.store"></i>
		</view>
		<view class="ml-md mr-md radius-18 fill-base pb-lg rel" v-if="configInfo.plugAuth.store">
			<view class="cancel-auth iconfont icon-biaoqian c-caption flex-center abs"
				v-if="storeInfo.status == 3">
				<view class="text-bold f-icontext abs">取消授权</view>
			</view>
			<view class="flex-between pt-lg pl-lg pr-lg">
				<text class="f-ms-title">营业数据</text>
				<view class="switch-date flex-center">
					<block v-for="(item,index) in dateTab" :key="index">
						<text class="switch-date-text f-caption flex-center" @tap="changeDate(item.id)" 
						:style="{background:dateCurrent == item.id ? `#fff` : ``}">{{item.name}}</text>
					</block>
				</view>
			</view>
			<view class="income flex-center">
				<view class="flex-1 flex-column" :class="[{'b-1px-l b-1px-r': index == 1}]" v-for="(item,index) in incomeList" :key="index">
					<view class="f-sm-title flex-center">{{item.type == 'all_price' ? `¥`+storeInfo.sale[item.type] :storeInfo.sale[item.type] }}</view>
					<view class="flex-center">
						<text class="f-caption c-caption">{{item.name}}</text>
						<i class="iconfont iconchangjianwenti c-caption" @tap="changeTips(item.name,item.tips)"></i>
					</view>
				</view>
			</view>
			<view class="flex-between pl-lg pr-lg">
				<view class="pd-lg flex-1 mr-md fill-body radius-16">
					<view class="f-sm-title">¥{{storeInfo.sale.today_price}}</view>
					<view class="flex-y-center">
						<text class="f-caption c-caption">今日营收</text>
						<i class="iconfont iconchangjianwenti c-caption" @tap="changeTips('今日营收','今日门店内完成支付的订单总额，包含退款')"></i>
					</view>
				</view>
				<view class="pd-lg flex-1 fill-body radius-16">
					<view class="f-sm-title">{{storeInfo.sale.today_order_num}}</view>
					<view class="flex-y-center">
						<text class="f-caption c-caption">今日单量</text>
						<i class="iconfont iconchangjianwenti c-caption" @tap="changeTips('今日单量','今日下单量，包含退款')"></i>
					</view>
				</view>
			</view>
			<view class="mt-md pd-lg radius-16 fill-body ml-lg mr-lg">
				<view class="f-sm-title">{{storeInfo.sale.today_refund}}</view>
				<view class="flex-y-center">
					<text class="f-caption c-caption">今日退款</text>
					<i class="iconfont iconchangjianwenti c-caption" @tap="changeTips('今日退款','今日已退款的金额')"></i>
				</view>
			</view>
		</view>
		<view class="fill-base radius-16 pt-lg ml-md mr-md mt-md rel">
			<view class="f-ms-title pb-sm pl-lg pr-lg">操作台</view>
			<view class="flex-warp pt-lg">
				<view class="flex-column pb-lg" style="width: 25%;" v-for="(item,index) in operateList" :key="index" 
				@tap="$util.goUrl({url: item.url + (item.type ? `?store_id=${storeInfo.id}` : '')})">
					<i class="iconfont flex-center" style="font-size: 50rpx;" :class="item.icon" :style="{color: primaryColor}"></i>
					<text class="f-caption pt-sm flex-center">{{item.name}}</text>
				</view>
			</view>
		</view>
		
		<uni-popup ref="show_rule_item" type="center" :maskClick="false">
			<view class="common-popup-content fill-base pd-lg radius-34">
				<view class="title">{{popupObj.title}}</view>
				<view class="f-desc c-title mt-lg">
					{{popupObj.popupContent}}
				</view>
				<view class="button">
					<view @tap.stop="$refs.show_rule_item.close()" class="item-child c-base"
						:style="{background: primaryColor,color:'#fff'}">知道了</view>
				</view>
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
				dateCurrent: 1,
				incomeList: [
					{name: '门店收入', price: '320.78', tips: '已支付未退款的订单总额，不包含退款', type: 'all_price'},
					{name: '未核销订单', price: '320.78', tips: '累计未核销的订单', type: 'no_hx_order_num'},
					{name: '订单量', price: '320.78', tips: '累计未核销的订单和已完成核销的订单量，不包含退款', type: 'order_num'}
				],
				operateAuth: [
					{'icon': 'iconmendianguanli', name: '门店管理', url: '/business/pages/manage/edit'},
					{'icon': 'icontaocanguanli_1', name: '订单管理', url: '/business/pages/manage/order/list'},
					{'icon': 'icontaocanguanli', name: '套餐管理', url: '/business/pages/package/manage/list'},
					{'icon': 'iconmiaosha-2', name: '秒杀活动', url: '/business/pages/seckill/manage/list', type: 1},
					{'icon': 'iconwoyaotixian', name: '我要提现', url: '/business/pages/manage/stored/cash-out'}
				],
				operateHide: [
					{'icon': 'iconmendianguanli', name: '门店管理', url: '/business/pages/manage/edit'}
				],
				operateList: [],
				popupObj: {
					title: '',
					popupContent: ''
				},
				storeInfo: '',
				dateTab: [
					{id: 1, name: '本周'},
					{id: 2, name: '本月'}
				]
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
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
		methods: {
			...mapActions(['getConfigInfo']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await this.getConfigInfo()
				
				let {
					plugAuth = {}
				} = this.configInfo
				
				if(!plugAuth.seckill){
					let seckillInd = this.operateAuth.findIndex(item => {
						return item.name == '秒杀活动'
					})
					if(seckillInd != -1){
						this.operateAuth.splice(seckillInd, 1)
					}
				}
				
				if(plugAuth.store){
					this.operateList = this.operateAuth
				}else{
					this.operateList = this.operateHide
				}
				
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.getStore()
			},
			initRefresh(){
				this.initIndex(true)
			},
			async getStore(){
				let data = await this.$api.business.getStore({time_type : this.dateCurrent})
				this.storeInfo = data
			},
			changeDate(index){
				this.dateCurrent = index
				this.getStore()
			},
			changeTips(title,tips){
				this.popupObj = {
					title,
					popupContent: tips
				}
				this.$refs.show_rule_item.open()
			},
			toHx(){
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
		}
	}
</script>

<style lang="scss">
	.manage-bg{
		height: 300rpx;
		width: 100%;
	}
	.switch-date{
		border-radius: 55rpx;
		background-color: #F2F2F2;
		padding: 4rpx;
		.switch-date-text{
			padding: 0 15rpx;
			height: 52rpx;
			border-radius: 52rpx;
		}
	}
	.income{
		padding: 40rpx 0;
	}
	.iconchangjianwenti{
		font-size: 13px;
	}
	.cancel-auth {
		width: 110rpx;
		height: 100rpx;
		font-size: 100rpx;
		top: 220rpx;
		right: 0rpx;
	
		.text-bold {
			height: 26rpx;
			transform: rotate(-32deg);
		}
	}
</style>