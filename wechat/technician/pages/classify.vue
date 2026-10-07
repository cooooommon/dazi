<template>
	<view class="pages-home" v-if="isLoad">
		<!-- #ifndef H5 -->
		<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true" :title="title" :leftIcon="options.pid ? 'iconshouye11' : 'icon-left'"
			color="#000" :backgroundColor="''">
		</uni-nav-bar>
		<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
		<!-- #endif -->
		<image mode="aspectFill" lazy-load class="service-page-bg abs"
			src="https://lbqny.migugu.com/admin/playwith/mine/classify-nav-bg.png"></image>
		
		<!-- #ifndef MP-WEIXIN -->
		<view style="height:30rpx"></view>
		<!-- #endif -->
		<view class="pb-lg" v-if="banner.length > 0">
			<banner @change="goBanner" :list="banner" :height="345" :margin="0" :autoplay="true"
				:previousMargin="0" :nextMargin="0" :indicatorActiveColor="primaryColor" :dotWidth="20"
				:dotBottom="5" :borderRadius="30" :widthRL="20">
			</banner>
		</view>
		<view class="pl-md pr-md rel">
			<view class="item-box pd-lg radius-24 flex-y-center fill-base mb-md" v-for="(item,index) in list.data" :key="index"
			@tap="$util.goUrl({url: `/user/pages/detail?id=${item.id}`})">
				<image :src="item.cover" mode="aspectFill" class="radius-16 item-cover"></image>
				<view class="flex-between flex-1 rel">
					<view class="flex-1 pl-md">
						<view class="flex-1 flex" style="justify-content: space-between;align-items:flex-start">
							<view class="f-mini-title max-330 ellipsis">{{item.title}}</view>
							<view class="flex-y-center">
								<i class="iconfont iconwode2 icon-font-color" style="font-size: 22rpx;"></i>
								<text class="f-icontext c-desc">{{item.total_sale}}人选择</text>
							</view>
						</view>
						<view class="pt-sm max-330 ellipsis f-caption c-caption">{{item.sub_title}}</view>
						<view class="flex-y-center" style="margin-top: 16rpx;">
							<!-- <image src="/static/images/time.png" mode="aspectFill" style="width: 30rpx;height: 30rpx;"></image> -->
							<i class="iconfont iconshijian2" :style="{color:primaryColor}"></i>
							<view class="flex-center" style="padding-left: 4rpx;">
								<text class="f-desc text-bold">{{item.time_long}}</text>
								<text class="f-icontext">分钟</text>
							</view>
						</view>
						<view class="flex-y-center" style="margin-top: 14rpx;">
							<view class="flex-center">
								<text style="color: #FF2404;" class="f-caption">¥</text>
								<view class="flex-y-baseline">
									<text style="color: #FF2404;"
										class="f-sm-title text-bold">{{item.price}}</text>
									<text style="color: #FF2404;" class="f-icontext">起</text>
								</view>
							</view>
							<view class="flex-center mem-price" v-if="item.member_price">
								<view class="rel mem-price-bg flex-center">
									<view class="abs f-ms-little c-base" style="padding-right: 2px;z-index: 2;">会员价</view>
									<image class="mem-price-img" src="https://lbqny.migugu.com/admin/member/member-bg.png" mode="aspectFill"></image>
								</view>
								<view class="mem-price-text text-bold f-ms-little">¥{{item.member_price}}</view>
							</view>
							<!-- <text class="f-icontext c-caption pl-sm" style="text-decoration: line-through">￥{{item.init_price}}</text> -->
						</view>
					</view>
					<view class="abs" style="right: 0rpx;top: 110rpx;">
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toChoose(index)" class="abs" style="width: 120rpx;right: 0; ">
							<view class="f-desc c-base item-btn flex-center" 
								:style="{backgroundColor:primaryColor }">下单</view>
						</auth>
						<!-- <view class="f-desc c-base item-btn flex-center" @tap="toChoose(index)"
							:style="{backgroundColor:primaryColor }">下单</view> -->
					</view>
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
				isLoad: false,
				banner: [],
				options: {},
				loading: true,
				param: {
					page: 1,
					service_type: 0
				},
				list: {
					data: [],
					current_page: 1,
					last_page: 1
				},
				title: ''
			}
		},
		computed: mapState({
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			primaryColor: state => state.config.configInfo.primaryColor,
			isIos: state => state.config.configInfo.isIos,
		}),
		async onPullDownRefresh() {
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
			this.$util.showLoading()
			this.getList();
		},
		async onLoad(options) {
			this.options = options
			this.param.service_type = options.id
			this.$util.showLoading()
			this.title = options.title
			await this.initIndex()
			this.isLoad = true
			uni.setNavigationBarTitle({
				title: `${options.title}` || '分类'
			})
		},
		async onShow() {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
			async initIndex(refresh = false) {
				
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				await Promise.all([this.serviceTypeInfo() , this.getList(this.param)])
				
			},
			async serviceTypeInfo(){
				let {
					id = 0
				} = this.options
				let data = await this.$api.technician.serviceTypeInfo({id})
				if(data && data.banner){
					data.banner.forEach(item=>{
						item.jump_type = item.banner_type == 2 ? 'video' : 'image'
						item.jump_url = item.banner_type == 2 ? item.video_url : ''
					})
					this.banner = data.banner
				}
			},
			async getList(){
				let {
					list: oldList,
					param
				} = this
				let newList = await this.$api.technician.serviceList(param)
				if(newList){
					if (this.param.page == 1) {
						this.list = newList
					} else {
						newList.data = oldList.data.concat(newList.data)
						this.list = newList
					}
				}
				this.loading = false
				this.$util.hideAll()
			},
			initRefresh(){
				this.$util.showLoading()
				this.param.page = 1
				this.initIndex(true)
			},
			// 轮播图/广告图跳转
			goBanner(e) {
				// connect_type 1查看大图，2文章
				let {
					connect_type,
					type_id: id = 0,
					img: current
				} = e
				switch (connect_type) {
					case 1:
						this.$util.previewImage({
							current,
							urls: [current]
						})
						break;
					case 2:
						this.$util.goUrl({
							url: `/user/pages/article?id=${id}`
						})
						break;
				}
			},
			// 选择向导
			toChoose(index) {
				let {
					id
				} = this.list.data[index]
				let url = `/user/pages/choose-technician?id=${id}`
				this.$util.toCheckLogin({
					url
				})
			},
		}
	}
</script>

<style lang="scss">
	.pages-home {
		.service-page-bg {
			width: 750rpx;
			// height: 739rpx;
			height: 177rpx;
			/* #ifdef H5 */
			z-index: 0;
			/* #endif */
			/* #ifndef H5 */
			z-index: -1;
			/* #endif */
			left: 0;
			top: 0;
		}
		.item-box{
			.item-cover{
				width: 180rpx;
				height: 204rpx;
			}
			.iconwode2{
				background-image: linear-gradient(#9FA4B6,#BFC5CF);
			}
			.item-btn{
				width: 120rpx;
				height: 60rpx;
				border-radius: 60rpx;
			}
		}
		.mem-price{
			border: 1px solid #FF2D22;
			border-radius: 8rpx;
			margin-left: 14rpx;
			.mem-price-bg{
				margin-left: -1px;
			}
			.mem-price-img{
				width: 74rpx;
				height: 27rpx;
			}
			.mem-price-text{
				color: #FF2D22;
				padding: 0 4px;
				line-height: normal;
			}
		}
	}
</style>