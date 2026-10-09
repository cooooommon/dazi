<template>
	<view class="list-pages rel">
		<fixed>
			<view class="pt-lg" :style="{background: primaryColor}">
				<view class="fill-base list-search">
					<view class="flex-y-center ">
						<view class="add-btn flex-center f-paragraph c-base" :style="{background: primaryColor}" 
						@tap="$util.goUrl({url: `/business/pages/package/manage/add`})">+ 套餐</view>
						<search class="flex-1" @input="toSearch" :keyword="param.name" type="input" :padding="20" :height="80" :radius="0" backgroundColor="#fff"
							placeholder="请输入关键词查询套餐">
						</search>
					</view>
				</view>
				<view class="fill-base pb-md">
					<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex" :activeColor="primaryColor"
						width="50%" height="80rpx" bgColor="#fff" lineClass="sm" :isRadius="true"></tab>
				</view>
			</view>
		</fixed>
		<view class="pl-md pr-md">
			<view class="l-item radius-16 fill-base pl-lg pr-lg mt-md" v-for="(item,index) in list.data" :key="index">
				<view class="flex-between l-item-title b-1px-b">
					<text class="f-paragraph text-bold">ID：{{item.id}}</text>
					<text class="f-desc c-caption">{{$util.formatTime(item.create_time * 1000)}}</text>
				</view>
				<view class="flex-center pt-lg pb-lg">
					<image :src="item.cover" mode="aspectFill" class="l-item-image radius-16"></image>
					<view class="flex-1 l-item-box">
						<view class="f-mini-title text-bold">{{item.name}}</view>
						<view class="flex-y-center pt-sm">
							<view class="flex-center">
								<text class="f-caption c-paragraph">年售：</text>
								<text class="f-caption">{{item.total_sale}}</text>
							</view>
							<view class="flex-center pl-lg">
								<text class="f-caption c-paragraph">真实销量：</text>
								<text class="f-caption">{{item.true_sale}}</text>
							</view>
						</view>
						<view class="flex-y-baseline pt-md">
							<view class="f-paragraph text-bold c-warning">¥{{item.price | handleNumber}}</view>
							<!-- <view class="f-ms-little text-delete pl-sm c-caption">¥{{item.init_price}}</view> -->
						</view>
					</view>
				</view>
				<view class="pb-lg flex-y-center">
					<view class="l-box-more rel">
						<text class="l-item-more f-paragraph" @tap.stop="openMore(index)">更多</text>
						<view class="del-order abs" v-if="item.isMore">
							<view class="del-order-top abs"></view>
							<view class="order-btn fill-base radius-10 pl-lg pr-lg">
								<view class="f-paragraph del-order-btn" @tap="toCopy(item.id,index)">复制</view>
								<view class="f-paragraph del-order-btn" @tap="updateStatus(item.id,item.status)" v-if="item.status == 0">上架</view>
								<view class="f-paragraph del-order-btn" @tap="updateStatus(item.id,item.status)" v-if="item.status == 1">下架</view>
							</view>
						</view>
					</view>
					<view class="l-item-btn flex-center f-caption ml-md" :style="{border: `1px solid #D2D2D2`}" 
					@tap="$util.goUrl({url: `/business/pages/package/manage/add?id=${item.id}`})">编辑</view>
					<view class="l-item-btn flex-center f-caption ml-md" :style="{color: primaryColor, border: `1px solid ${primaryColor}`}"
					@tap="$util.goUrl({url: `/business/pages/seckill/manage/add?package_id=${item.id}&seckill_id=${item.seckill_id}`})" 
					v-if="configInfo.plugAuth.seckill && item.status == 1">设为秒杀</view>
					<view class="l-item-btn flex-center f-caption ml-md c-warning" :style="{border: `1px solid #FF2404`}" 
					@tap="updateStatus(item.id, -1)">删除</view>
				</view>
			</view>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-max-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	let timer = null
	export default {
		data() {
			return {
				param: {
					page: 1,
					status: 1,
					name: ''
				},
				tabList: [{
					title: '上架中',
					id: 1,
					number: 0
				}, {
					title: '已下架',
					id: 2,
				}],
				activeIndex: 0,
				isLoad: false,
				loading: true,
				list: {
					data:[]
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
		}),
		filters:{
			handleNumber(val){
				return val.toFixed(1)
			}
		},
		onLoad() {
			this.$util.showLoading()
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.$util.showLoading()
			this.param.page += 1
			await this.getList()
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
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.getList()
			},
			initRefresh(){
				this.$util.showLoading()
				this.param.page = 1
				this.initIndex(true)
			},
			async getList(){
				let {
					list: oldList,
					param
				} = this
				let newList = await this.$api.business.packageList(param)
				this.tabList[0].title = `上架中(${newList.status_1})`
				this.tabList[1].title = `已下架(${newList.status_2})`
				newList.data.forEach(item => {
					item.isMore = false
				})
				if (param.page == 1) {
					this.list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList;
				}
				this.loading = false;
				this.isLoad = true
				this.$util.hideAll()
			},
			handerTabChange(index) {
				this.activeIndex = index
				this.param.status = this.tabList[index].id
				this.param.page = 1
				this.$util.showLoading()
				this.getList()
			},
			toSearch(data){
				clearTimeout(timer)
				timer = setTimeout(() => {
					this.param.page = 1
					this.param.name = data
					this.$util.showLoading()
					this.getList()
				},1000)
			},
			async updateStatus(id,status){
				let msg = ''
				if(status == -1){
					let [res_del, {
						confirm
					}] = await uni.showModal({
						content: `请确认是否要删除套餐`,
					})
					msg = '删除成功'
					if (!confirm) return;
				}else{
					status = status == 0 ? 1 : 0
					msg = status == 1 ? '上架成功' : '下架成功'
				}
				this.$util.showLoading()
				await this.$api.business.packageUpdateStatus({id,status})
				this.$util.hideAll()
				this.$util.showToast({
					title: msg
				});
				this.param.page = 1
				this.getList()
			},
			openMore(index){
				this.list.data[index].isMore = !this.list.data[index].isMore
			},
			toCopy(id,index){
				this.list.data[index].isMore = false
				this.$util.goUrl({url: `/business/pages/package/manage/add?id=${id}&type=copy`})
			}
		}
	}
</script>

<style lang="scss">
	.list-pages{
		.list-search{
			padding: 5rpx 10rpx 5rpx 30rpx;
			border-radius: 40rpx 40rpx 0px 0px;
			overflow: hidden;
			.add-btn{
				width: 134rpx;
				height: 80rpx;
				border-radius: 80rpx;
			}
		}
		.l-item-title{
			height: 83rpx;
		}
		.l-item-image{
			width: 150rpx;
			height: 150rpx;
			
		}
		.l-item-box{
			padding-left: 25rpx;
		}
		.l-item-btn{
			width: 137rpx;
			height: 54rpx;
			border-radius: 54rpx;
		}
		.l-box-more{
			width: 150rpx;
		}
		.l-item-more{
			color: #8E8E8E;
		}
		.del-order{
			left: -10rpx;
			top: 60rpx;
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
			.order-btn{
				box-shadow: -1px 0px 6px 0px rgba(200, 200, 200, 1);
			}
			.del-order-btn{
				height: 70rpx;
				line-height: 70rpx;
			}
		}
	}
</style>