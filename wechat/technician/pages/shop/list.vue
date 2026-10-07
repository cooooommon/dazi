<template>
	<view class="shop" v-if="isLoad">
		<fixed :zIndex="998">
			<view class="shop-top rel">
				<view class="abs shop-top-bgcolor" :style="{backgroundColor:primaryColor}"></view>
				<view class="pl-md pr-md fill-base shop-search box-shadow pt-lg">
					<search class="flex-1" @input="toSearch" type="input" :confirmSearch="false" :padding="0"
						:keyword="param.name" :radius="30" placeholder="输入产品名称查询"></search>
					<view class="shop-nav flex-y-center flex-between mt-lg">
						<scroll-view scroll-x="true" class="flex-1 flex scroll-box" 
							:scroll-left="scrollLeft" :scroll-with-animation="true">
							<view @tap="toTabItemChange(index,item.id)" v-for="(item,index) in navList" :key="index"
								class="scroll-item" 
								:style="{ color: param.carte == item.id ? primaryColor : '#000'}">{{item.name}}</view>
						</scroll-view>
						<view class="flex-between shop-top-more" @tap="navMore">
							<view class="ml-sm c-paragraph f-caption">更多</view>
							<view class="icongengduo1 iconfont c-paragraph"></view>
						</view>
					</view>
				</view>
			</view>
		</fixed>
		<view class="shop-list pl-sm pr-sm flex">
			<block v-for="(item,index) in list.data" :key="index">
				<view class="shop-item ml-sm mr-sm fill-base radius-16"
					@tap.stop="$util.goUrl({url:`/technician/pages/shop/detail?id=${item.id}`})">
					<view class="shop-item-image">
						<image :src="item.cover" mode="aspectFill"></image>
					</view>
					<view class="pt-md pl-md pr-md">
						<view class="shop-item-title f-desc ellipsis">{{item.name}}</view>
						<view class="pt-sm c-warning">
							<text class="f-caption">¥</text>
							<text class="f-title text-bold">{{item.price}}</text>
						</view>
					</view>
				</view>
			</block>
		</view>
		<uni-popup ref="nav_more" type="top" :custom="true" :maskClick="false" top="124rpx">
			<view class="fill-base more-box pl-lg pr-lg pt-lg">
				<view class="more-box-title f-mini-title c-black text-bold">类别</view>
				<scroll-view class="more-list flex flex-warp" :scroll-y="true">
					<block v-for="(item,index) in navList" :key="index">
						<view class="more-item mb-md rel ellipsis" :class="index % 3 > 0 ? 'ml-md' : ''"
							@tap="toItemChange(index,item.id)">
							<view class="more-item-bg abs"
								:style="{backgroundColor: current.id == item.id ? primaryColor : '#F5F5F5'}">
							</view>
							<text class="text-center pl-sm pr-sm"
								:style="{color: current.id == item.id ? primaryColor : '#000'}">{{item.name}}</text>
						</view>
					</block>
				</scroll-view>
				<view class="more-footer flex-center">
					<view class="more-btn flex-center radius-16 c-black more-btn-reset f-paragraph mr-lg"
						@tap="setNav(1)">重置</view>
					<view class="more-btn flex-center radius-16 c-base f-paragraph"
						:style="{backgroundColor: primaryColor}" @tap="setNav(2)">确定</view>
				</view>
			</view>
		</uni-popup>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-footer"></view>
	</view>
</template>

<script>
	let play = null
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				loading: true,
				isLoad: false,
				activeIndex: 0,
				param: {
					page: 1,
					limit: 10,
					name: '',
					carte: 0
				},
				list: {
					data: []
				},
				navList: [],
				current: {
					index: 0,
					id: 0
				},
				scrollLeft: 0,
				contentScrollW:0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		async onLoad() {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			await this.carteListCall()
			this.initIndex()
		},
		methods: {
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
			getScrollW() {
				let query = uni.createSelectorQuery().in(this);
				query.select('.scroll-box').boundingClientRect(data => {
					// 拿到 scroll-view 组件宽度
					this.contentScrollW = data.width
				}).exec();

				query.selectAll('.scroll-item').boundingClientRect(data => {
					let dataLen = data.length;
					for (let i = 0; i < dataLen; i++) {
						//  scroll-view 子元素组件距离左边栏的距离
						this.navList[i].left = data[i].left;
						//  scroll-view 子元素组件宽度
						this.navList[i].width = data[i].width
					}
				}).exec()
			},
			
			toSearch(val) {
				clearTimeout(play)
				play = setTimeout(() => {
					this.param.name = val
					this.initRefresh()
				}, 1000)
			},
			toTabItemChange(index, id) {
				this.scrollLeft = this.navList[index].left - this.contentScrollW / 2 + this.navList[index].width / 2 - 20;
				this.param.carte = id;
				this.toItemChange(index, id)
				this.initRefresh()
			},
			toItemChange(index, id) {
				this.current = {
					index,
					id
				}
			},
			navMore() {
				this.$refs.nav_more.open()
			},
			setNav(index) {
				if (index == 1) { //重置
					this.param.name = ''
					this.toTabItemChange(0, 0)
				} else {
					this.toTabItemChange(this.current.index, this.current.id)
				}

				this.$refs.nav_more.close()
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				this.$util.showLoading()
				let {
					list: oldList,
					param,
					activeIndex
				} = this
				let newList = await this.$api.technician.goodsList(param)
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.isLoad = true
				this.$util.hideAll()
			},
			async carteListCall(type) {
				this.navList = await this.$api.technician.carteList()
				this.navList.unshift({
					id: 0,
					name: '全部'
				})
				if(type){
					let flag = false
					this.navList.forEach(item => {
						if(item.id == this.param.carte){
							flag = true
						}
					})
					if(!flag){
						this.toTabItemChange(0, 0)
					}
				}
				setTimeout(()=>{this.getScrollW()},1000)
			}
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			this.carteListCall(true)
			uni.stopPullDownRefresh()
		},
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
	}
</script>

<style lang="scss">
	.shop {
		.shop-top {
			padding-top: 10rpx;

			.shop-top-bgcolor {
				height: 60rpx;
				top: 0;
				left: 0;
				width: 100%;
				z-index: -1;
			}

			.scroll-box {
				width: 100%;
				overflow-x: auto;
				white-space: nowrap;

				.scroll-item {
					padding-right: 40rpx;
					position: relative;
					display: inline-block;
					vertical-align: top;
				}
			}

			.shop-top-more {
				width: 112rpx;
				box-shadow: 0px 0px 5px #eee;
				height: 94rpx;
			}
		}

		.shop-search {
			border-top-left-radius: 40rpx;
			border-top-right-radius: 40rpx;
			overflow: hidden;
			margin-bottom: -2rpx;

			.shop-top-time {
				width: 70rpx;
				height: 70rpx;

				.shop-top-bg {
					width: 70rpx;
					height: 70rpx;
					border-radius: 70rpx;
					opacity: 0.1;
				}
			}
		}

		.shop-nav {
			height: 94rpx;
			overflow: hidden;
		}

		.shop-list {
			flex-flow: wrap;

			.shop-item {
				width: calc(50% - 20rpx);
				height: 483rpx;
				overflow: hidden;
				margin-top: 25rpx;

				.shop-item-image {
					height: 350rpx;
					width: 100%;

					image {
						width: 100%;
						height: 100%;
					}
				}
			}
		}

		.more-box {
			.more-list {
				padding-top: 40rpx;
				max-height: 290rpx;
			}

			.more-item {
				width: calc((100% - 40rpx) / 3);
				height: 78rpx;
				background-color: #F5F5F5;
				border-radius: 8rpx;
				display: inline-block;
				vertical-align: top;
				text-align: center;
				line-height: 78rpx;
				overflow: hidden;

				.more-item-bg {
					width: 100%;
					height: 100%;
					opacity: 0.1;
				}

				text {
					width: 100%;
				}
			}

			.more-footer {
				padding-top: 140rpx;
				padding-bottom: 40rpx;

				.more-btn {
					width: 260rpx;
					height: 72rpx;
				}

				.more-btn-reset {
					background-color: #f2f2f2;
				}
			}
		}
	}
</style>
