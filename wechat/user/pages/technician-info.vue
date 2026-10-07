<template>
	<view class="technician-info pages-technician rel" v-if="detail.id && isLoad">
		<uni-nav-bar :fixed="true" :shadow="false" :zIndex="99"
			:statusBar="true" :onlyLeft="true" :color="`#fff`" backgroundColor="none">
			<view slot="left" class="rel flex-y-center">
				<view class="flex-center c-base radius" style="width:58rpx;height:58rpx;background:rgba(0,0,0,0.4)" 
				@tap="navigateBack">
					<i class="iconfont icongengduo" style="font-size: 26rpx;transform: rotate(180deg);"></i>
				</view>
				<view class="switch-banner flex-center ml-lg" v-if="detail.model_img && detail.model_img.length">
					<view class="banner-item-text f-icontext flex-center flex-1" @tap="changeBanner(0)" 
					:style="{color: bannerIndex == 0 ? `#302D2B` : `#fff`,background: bannerIndex == 0 ? '#fff' : '' }">模特照</view>
					<view class="banner-item-text f-icontext flex-center flex-1" @tap="changeBanner(1)" 
					:style="{color: bannerIndex == 1 ? `#302D2B` : `#fff`,background: bannerIndex == 1 ? '#fff' : ''}">生活照</view>
				</view>
			</view>
		</uni-nav-bar>

		<!-- #ifndef H5 -->
		<!-- <view :style="{height:`${configInfo.navBarHeight}px`}"></view> -->
		<!-- #endif -->
		<view class="fill-base">
			<banner @change="goBanner" :list="bannerIndex == 0 ? detail.model_img : detail.self_img" :margin="0" :autoplay="true" mode="aspectFit"
				:indicatorActiveColor="primaryColor" :height="1030" indicatorType="number" indicatorStyle="right" videoBottom="80rpx"
				v-if="detail.self_img.length > 0">
			</banner>
			<view class="ti-box fill-base">
				<view class="ti-box-head flex-y-center pl-lg pr-lg">
					<view class="rel ti-box-header flex-center fill-base">
						<image :src="detail.work_img" mode="aspectFill"></image>
					</view>
					<text class="pr-lg f-ms-title ellipsis max-400 text-bold ml-md flex-1">{{detail.coach_name}}</text>
					<view class="f-icontext h40 min94 flex-center rel" style="overflow: hidden">
						<view class="abs age-bg" :style="{background: detail.sex == 1 ? '#F72370' : '#319AFF'}"></view>
						<i class="iconfont" style="font-size: 11px;" :style="{color: detail.sex == 1 ? '#F72370' : '#319AFF'}" 
						:class="[{'iconnan-lanse': detail.sex == 0},{'iconnv-hongse': detail.sex == 1}]"></i> 
						<text :style="{color: detail.sex == 1 ? '#F72370' : '#319AFF'}" style="margin-left: 6rpx;">{{detail.age}}</text></view>
				</view>
				<view class="flex-y-center mt-md" style="margin-left: 80rpx;">
					<view class="pr-sm pl-sm text-center radius mr-md min94 f-desc user-info-item"
					v-for="(item,index) in coachInfoType" :key="index" 
					:style="{color: item.color,background: item.bgColor}">
						<text style="font-size: 0.8em;line-height: normal;">{{detail[item.key]+(item.key == 'height'?'cm': item.key == 'weight'? 'kg' :'')}}</text>
					</view>
				</view>
				<view class="count-list flex-between mt-lg pt-sm pb-lg">
					<view class="flex-center flex-column" v-for="(item,index) in countList" :key="index">
						<view class="f-sm-title c-black text-bold" v-if="item.key == `order_num`">{{detail[item.key] | handleOrderNumber}}</view>
						<view class="f-sm-title c-black text-bold" v-else :style="{opacity:item.key == 'star' && detail[item.key] == 0? 0: 1 }">{{detail[item.key]}}</view>
						<view class="text" v-if="item.key == 'star' && detail[item.key] == 0">暂无评分</view>
						<view class="text" v-else>{{item.title}}</view>
					</view>
				</view>
			</view>
			<!-- <view class="pd-lg">
				<view class="flex-between">
					<view class="f-md-title c-black text-bold " style="max-width: 320rpx;">
						<text class="mr-md">{{detail.coach_name}}</text>
						<text class="taking-order f-icontext">接单量{{detail.order_num | handleOrderNumber}}</text>
					</view>
					<view class="flex-y-center f-icontext c-caption">
						<view @tap.stop="toShield" class="flex-y-center mr-lg pr-md">
							<view class="like-label flex-center mr-sm radius"
								:style="{background:detail.is_shield?'#FF8D4C':''}">
								<i class="iconfont"
									:class="[{'iconbuxihuan c-caption':!detail.is_shield},{'iconbuxihuan-xuanzhong c-base':detail.is_shield}]"></i>
							</view>
							屏蔽
						</view>
						<view @tap.stop="toCollect" class="flex-y-center">
							<view class="like-label flex-center mr-sm radius"
								:style="{background:detail.is_collect?'#FF4C88':''}">
								<i class="iconfont"
									:class="[{'iconshoucang1 c-caption':!detail.is_collect},{'iconshoucang2 c-base':detail.is_collect}]"></i>
							</view>
							{{detail.collect_num}} 喜欢
						</view>
					</view>
				</view>
				<view class="count-list flex-center mt-lg pt-sm">
					<view class="flex-center flex-column" v-for="(item,index) in countList" :key="index">
						<view class="f-sm-title c-black text-bold">{{detail[item.key]}}</view>
						<view class="text">{{item.title}}</view>
					</view>
				</view>
			</view> -->
		</view>

		<view class="fill-base pl-lg pr-lg pb-lg pt-sm">
			<!-- <view class="f-mini-title c-title text-bold pt-lg pb-lg">个人介绍</view> -->
			<view class="introduce-info f-desc c-title pd-lg">
				<span v-if="!detail.showText">{{detail.text.substring(0,42) + '...'}}</span>
				<view v-else>
					<text decode="emsp" style="word-break:break-all;">{{detail.text}}</text>
				</view>
				<span @tap.stop="toShowHide(-1,'showText')" class="f-caption"
					:class="[{'ml-md':!detail.showText},{'mt-md':detail.showText}]" :style="{color:primaryColor}"
					v-if="detail.have_show_text">
					{{!detail.showText?'展开':'收起'}}
					<text class="iconfont ml-sm" :class="[{'iconxia':!detail.showText},{'iconshang':detail.showText}]"
						style="font-size: 24rpx"></text>
				</span>
			</view>
		</view>

		<view class="fill-base pl-lg pr-lg mt-md">
			<view class="f-mini-title c-title text-bold pt-lg pb-lg">TA拥有的技能</view>
			<!-- <view class="flex-warp pb-sm" v-if="detail.service && detail.service.length>0">
				<view class="item-label flex-center mr-md mb-md f-desc rel" v-for="(aitem,aindex) in detail.service"
					:key="aindex">
					<view class="item-label abs" :style="{background:primaryColor}">
					</view>
					<view :style="{color:primaryColor}">{{aitem.title}}</view>
				</view>
			</view> -->
			<scroll-view scroll-y="true" style="max-height: 575px;" @scrolltolower="serviceScroll">
				<view class="flex-center skill-item b-1px-b" v-for="(item,index) in serviceList.data" :key="index">
					<image :src="item.cover" mode="aspectFill" class="item-cover radius-10"></image>
					<view class="flex-between flex-1">
						<view class="flex-1 pl-md">
							<view class="flex flex-1" style="justify-content: space-between;align-items:flex-start">
								<view class="f-mini-title max-380 ellipsis">{{item.title}}</view>
								<view class="flex-y-center">
									<i class="iconfont iconwode2 icon-font-color" style="font-size: 22rpx;"></i>
									<text class="f-icontext c-desc">{{item.total_sale}}人选择</text>
								</view>
							</view>
							<view class="flex-y-center " style="opacity: 0;"> <!--pt-sm-->
								<i class="iconfont iconwode2 icon-font-color" style="font-size: 22rpx;"></i>
								<text class="f-icontext c-desc">{{item.total_sale}}人选择</text>
							</view>
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
									<text style="color: #FF2404;" class="f-caption">￥</text>
									<text style="color: #FF2404;" class="f-mini-title text-bold">{{item.price}}</text>
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
						<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="changeNum(1,index)" class="abs" style="width: 120rpx;right: 0;"><!--height: 60rpx; -->
							<view class="f-desc c-base item-btn flex-center abs mt-sm" style="right: 0;" 
							:style="{backgroundColor:primaryColor }">下单</view>
						</auth>
					</view>
				</view>
				<view class="space-lg"></view>
			</scroll-view>
			<view style="margin: 0 100rpx;" v-if="serviceList.current_page == 1&&serviceList.data.length<=0">
				<abnor></abnor>
			</view>
		</view>

		<view class="fill-base pl-lg pr-lg mt-md">
			<view class="f-mini-title c-title text-bold pt-lg pb-lg">TA的评价</view>
			<view class="list-message flex-warp pt-lg pb-lg" :class="[{'b-1px-t':index!=0}]"
				v-for="(item,index) in list.data" :key="index">
				<view class="flex-1">
					<view class="flex-between">
						<image :src="item.avatarUrl" mode="aspectFill" style="width: 60rpx;height: 60rpx;border-radius: 60rpx;"></image>
						<view class="f-mini-title c-title text-bold flex-1 ellipsis pr-md pl-md max-500">{{item.nickName}}</view>
						<view class="f-icontext c-caption">{{item.create_time}}</view>
					</view>
					<view class="f-paragraph c-title mt-md">
						<view>
							<span v-if="!item.showText">{{item.text.substring(0,100) + '...'}}</span>
							<text decode="emsp" style="word-break:break-all;" v-else>{{item.text}}</text>
						</view>
						<span @tap.stop="toShowHide(index,'showText')" class="mt-md" :style="{color:primaryColor}"
							v-if="item.have_show_text">
							{{!item.showText?'展开':'收起'}}
						</span>
					</view>
					<view class="mt-md flex-y-center">
						<text>{{$t('action.attendantName')}}颜值：</text> <i class="iconfont iconpingfen1" style="font-size: 25rpx;"
							:style="{color: aindex< item.star?'#FEC922':'#EFEFEF'}" v-for="(aitem,aindex) in 5"
							:key="aindex"></i>
					</view>
					<view class="flex-y-center mt-md">
						<text>服务态度：</text> <i class="iconfont iconpingfen1" style="font-size: 25rpx;"
							:style="{color: aindex< item.attitude_star?'#FEC922':'#EFEFEF'}" v-for="(aitem,aindex) in 5"
							:key="aindex"></i>
					</view>
					<view class="flex-y-center mt-md">
						<text>响应速度：</text> <i class="iconfont iconpingfen1" style="font-size: 25rpx;"
							:style="{color: aindex< item.speed_star?'#FEC922':'#EFEFEF'}" v-for="(aitem,aindex) in 5"
							:key="aindex"></i>
					</view>
					<!-- <view class="flex-warp mt-md" v-if="item.lable_text && item.lable_text.length>0">
						<view class="item-label mini flex-center mr-md mb-md f-icontext rel"
							v-for="(aitem,aindex) in item.lable_text" :key="aindex">
							<view class="item-label mini abs" :style="{background:primaryColor}">
							</view>
							<view :style="{color:primaryColor}">{{aitem}}</view>
						</view>
					</view> -->
				</view>
			</view>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		
		<!-- <view class="space-footer"></view> -->
		<view class="space-max-footer"></view>

		<!-- <fix-bottom-button @confirm="toShowPopup" :text="[{type:'confirm',text:'邀约',isAuth:true}]" bgColor="#fff">
		</fix-bottom-button> -->
		
		<fixed position="bottom" zIndex="99">
			<view class="fill-base">
				<view class="footer-box flex-between pl-lg pr-lg">
					<!-- <view class="contact box-shadow flex-center ml-lg">
						<i class="iconfont iconkefu1" style="font-size: 22px;color: #000;"></i>
					</view> -->
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="contact" class="contact flex-center ml-lg"
						style="border-radius: 100rpx;" v-if="configInfo.im_type == 2">
						<i class="iconfont iconkefu1" style="font-size: 22px;color: #000;"></i>
					</button>
					<button @tap.stop="toContact" class="contact flex-center ml-lg"
						style="border-radius: 100rpx;" v-else>
							<i class="iconfont iconkefu1" style="font-size: 22px;color: #000;"></i>
						</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<button @tap.stop="toContact" class="contact flex-center ml-lg"
						style="border-radius: 100rpx;">
							<i class="iconfont iconkefu1" style="font-size: 22px;color: #000;"></i>
						</button>
					<!-- #endif -->
					<view class="f-paragraph c-base flex-center collect-btn radius-16" @tap.stop="toCollect"
					:style="{background:primaryColor}">{{detail.is_collect?`取消收藏`:`收藏${$t('action.attendantName')}`}}</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</fixed>

		<uni-popup ref="technician_item" type="bottom">
			<view class="technician-popup fill-base">
				<view class="pd-lg flex-center">
					<image mode="aspectFill" class="item-avatar radius" :src="detail.work_img">
					</image>
					<view @tap.stop="$refs.technician_item.close()" class="flex-1 ml-md">
						<view class="flex-between">
							<view class="flex-y-baseline f-caption c-caption">
								<view class="f-title c-title text-bold mr-sm max-350 ellipsis">
									{{detail.coach_name}}
								</view>
							</view>
							<i class="iconfont icon-close"></i>
						</view>
					</view>
				</view>
				<view class="space-sm fill-body"></view>
				<scroll-view scroll-y @touchmove.stop.prevent class="list-content">
					<view class="list-item flex-center pd-lg fill-base radius-16" :class="[{'b-1px-t':index != 0}]"
						v-for="(item,index) in serviceList" :key="index">
						<image @tap.stop="goDetail(index)" mode="aspectFill" class="avatar lg radius-16"
							:src="item.cover"></image>
						<view class="flex-1 ml-md">
							<view @tap.stop="goDetail(index)" class="f-title c-title max-510 ellipsis">
								{{item.title}}
							</view>
							<view class="f-caption c-caption mt-sm mb-sm ellipsis">{{item.total_sale}}人选择</view>
							<view class="flex-between">
								<view class="flex-y-baseline f-desc c-caption max-350 ellipsis">
									<view class="text-delete mr-sm" v-if="item.init_price">¥{{item.init_price}}
									</view>
									<view class="f-title c-warning mr-sm">¥{{item.price}}</view>/
									{{item.time_long}}分钟
								</view>
								<view class="flex-warp">
									<block v-if="item.num">
										<button @tap.stop="changeNum(-1,index)" class="reduce"
											:style="{borderColor:primaryColor,color:primaryColor}"><i
												class="iconfont icon-jian-bold"></i></button>
										<button class="addreduce clear-btn">{{item.num || 0}}</button>
									</block>
									<button @tap.stop="changeNum(1,index)" class="add"
										:style="{background:primaryColor,borderColor:primaryColor}"><i
											class="iconfont icon-jia-bold"></i></button>
								</view>
							</view>
						</view>
					</view>
				</scroll-view>
				<view style="margin: 0 100rpx;" v-if="!loading&&serviceList.length<=0">
					<abnor></abnor>
				</view>
				<view class="flex-between pd-lg b-1px-t" v-if="carList.car_count > 0">
					<view class="flex-center">合计：<view class="f-title c-warning text-bold ml-sm">¥{{carList.car_price}}
						</view>
					</view>
					<view @tap.stop="toOrder" class="order-btn flex-center f-desc c-base radius"
						:style="{background: primaryColor}">提交订单
					</view>
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
		components: {},
		data() {
			return {
				isLoad: true,
				options: {},
				countList: [{
					title: '接单量',
					key: 'order_num'
				}, {
					title: '收藏量',
					key: 'collect_num'
				}, {
					title: '评分',
					key: 'star'
				}],
				coachInfoType: [{
					title: '身高',
					key: 'height',
					color: '#7F47D7',
					bgColor: '#F2E6FF'
				}, {
					title: '体重',
					key: 'weight',
					color: '#FF6121',
					bgColor: '#FEEBE7'
				}, {
					title: '星座',
					key: 'constellation',
					color: '#06BA61',
					bgColor: '#CFF5E8'
				}],
				detail: {},
				param: {
					page: 1,
					coach_id: ''
				},
				list: {
					data: []
				},
				servicePage: 1,
				loading: true,
				serviceList: {
					data: []
				},
				bannerIndex: 0 // banner 切换
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			carList: state => state.order.carList,
		}),
		filters:{
			handleOrderNumber(val){
				return val > 9999 ? ((val / 10000).toFixed(0) + 'W+') : val
			}
		},
		onLoad(options) {
			let {
				id
			} = options
			this.param.coach_id = id
			this.options = options
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
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'getCarList']),
			...mapMutations(['updateUserItem', 'updateTechnicianItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (this.$jweixin.isWechat()) {
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
				uni.setNavigationBarTitle({
					title: this.$t('action.attendantName') + '详情'
				})
				await this.getDetail()
				this.loading = false
				this.$util.hideAll()
				await this.getServiceList()
				await this.getList()
			},
			initRefresh() {
				this.param.page = 1
				this.servicePage = 1
				this.initIndex(true)
			},
			async getDetail() {
				let {
					id
				} = this.options
				let data = await this.$api.service.coachInfo({
					id
				});
				data.self_img = data.self_img.map((item) => {
					return {
						img: item
					}
				})
				// let arr = []
				// data.self_img.forEach(async function(item){
				// 	let [, {
				// 		width,
				// 		height
				// 	}] = await uni.getImageInfo({
				// 		src: item
				// 	})
				// 	arr.push({
				// 		img: item,
				// 		mode: (width / height > 1.25) ? 'aspectFill' : 'heightFix'
				// 	})
				// })
				// data.self_img = arr
				
				let isHaveVideo = data.video && data.video.length > 0
				
				data.self_img.map((item, index) => {
					item.jump_url = index == 0 && isHaveVideo ? data.video : item.img
					item.jump_type = index == 0 && isHaveVideo ? 'video' : 'picture'
				})
				if(data.model_img){
					data.model_img = data.model_img.map((item) => {
						return {
							img: item
						}
					})
					data.model_img.map((item, index) => {
						item.jump_url = index == 0 && isHaveVideo ? data.video : item.img
						item.jump_type = index == 0 && isHaveVideo ? 'video' : 'picture'
					})
				}else{
					this.bannerIndex = 1
				}
				
				let have_show_text = data.text.length > 42
				data.have_show_text = have_show_text
				data.showText = !have_show_text
				this.detail = data
				this.$util.hideAll()
			},
			async getList() {
				let {
					list: oldList,
					param,
				} = this
				let newList = await this.$api.service.commentList(param);
				newList.data.map(item => {
					let have_show_text = item.text.length > 100
					item.have_show_text = have_show_text
					item.showText = !have_show_text
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
			goBanner(item) {
				let current = item.img
				let urls = this.detail.self_img.map(item => {
					return item.img
				})
				if(this.bannerIndex == 0){
					urls = this.detail.model_img.map(item => {
						return item.img
					})
				}
				this.$util.previewImage({
					current,
					urls
				})
			},
			toContact() {
				let {
					hide_admin_mobile,
					mobile,
					im_type,
					qywx_company_id,
					qywx_kid
				} = this.configInfo
				if (im_type == 3) {
					// #ifdef H5
					window.location.href = qywx_kid
					// #endif
					// #ifndef H5 
					// #ifdef MP-WEIXIN 
					try {
						wx.openCustomerServiceChat({
							extInfo: {
								url: qywx_kid
							},
							corpId: qywx_company_id,
							fail(res) {
								uni.showToast({
									title: res.errorMsg,
									icon: 'none'
								})
							}
						})
					} catch (e) {
						this.$util.showToast({
							title: `请更新微信版本`
						})
					}
					// #endif
					// #ifdef APP-PLUS
					let wechat = null
					plus.share.getServices(res => {
						console.log(res, "==============res")
						wechat = res.find(i => i.id === 'weixin')
						if (wechat) {
							wechat.openCustomerServiceChat({
								corpid: qywx_company_id,
								url: qywx_kid
							}, err => {
								uni.showToast({
									title: err.errorMsg
								})
							})
						} else {
							uni.showToast({
								title: '当前环境不支持微信操作',
								icon: 'none'
							})
						}
					}, function() {
						uni.showToast({
							title: '获取服务失败，不支持该操作',
							icon: 'none'
						})
					})
					// #endif
					// #endif
					return
				}
			
				let url = mobile
				this.$util.goUrl({
					url,
					openType: 'call'
				})
			},
			toShowHide(index, key) {
				if (index == -1) {
					this.detail[key] = !this.detail[key]
					return
				}
				this.list.data[index][key] = !this.list.data[index][key]
			},
			async toShowPopup() {
				let {
					user_id = 0,
						is_work = 0
				} = this.detail
				if (!user_id || !is_work) return
				await this.getServiceList()
				this.$refs.technician_item.open()
			},
			async getServiceList() {
				let {
					id: coach_id,
					ser_id = ''
				} = this.options
				
				await this.getCarList({
					coach_id
				})
				
				let oldList = this.serviceList
				let newList = await this.$api.service.coachServiceListPage({
					coach_id,
					ser_id,
					limit: 5,
					page: this.servicePage
				})
				
				let arr = []
				if (this.carList.list && this.carList.list.length > 0) {
					this.carList.list.map(item => {
						arr.push(item.service_id)
					})
				}
				if (newList.data && newList.data.length > 0) {
					newList.data.map(item => {
						if (arr.includes(item.id)) {
							let carInd = arr.findIndex(aitem => {
								return aitem == item.id
							})
							item.num = this.carList.list[carInd].num
							item.cart_id = this.carList.list[carInd].id
						}
					})
				}
				if (this.servicePage == 1) {
					this.serviceList = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.serviceList = newList
				}
				this.$util.hideAll()
				//this.serviceList = data
			},
			// 加/减数量
			async changeNum(mol, serInd) {
				let {
					id: coach_id
				} = this.options
				let {
					id: service_id,
					cart_id = 0
				} = this.serviceList.data[serInd]
				if (this.lockTap) return;
				this.lockTap = true;
				let methodModel = mol > 0 ? 'addCar' : 'delCar'
				let param = mol > 0 ? {
					service_id,
					coach_id,
					num: 1
				} : {
					id: cart_id,
					num: 1
				}
				if (methodModel == 'delCar' && !param.id) {
					this.lockTap = false
					return
				}
				try {
					let add_cart_id = await this.$api.order[methodModel](param)
					let {
						num = 0,
							cart_id = 0
					} = this.serviceList.data[serInd]
					this.serviceList.data[serInd].num = num + mol
					if (add_cart_id && mol > 0 && !cart_id) {
						this.serviceList.data[serInd].cart_id = add_cart_id
					}
					if (this.serviceList.data[serInd].num < 1) {
						this.serviceList.data[serInd].cart_id = 0
					}
					await this.getCarList({
						coach_id
					})
					this.lockTap = false
				} catch (e) {
					this.lockTap = false
				}
				this.toOrder()
			},
			// 下单
			toOrder() {
				if (this.carList.car_count < 1) {
					this.$util.showToast({
						title: `请选择服务`
					})
					return
				}
				let {
					id
				} = this.options
				this.$refs.technician_item.close()
				// #ifdef H5
				this.pageScrollTo()
				// #endif
				this.$util.goUrl({
					url: `/user/pages/order?id=${id}`
				})
			},
			async toShield() {
				let {
					id
				} = this.options
				await this.$api.mine.shieldCoachAdd({
					type: 2,
					coach_id: id
				})
				this.updateUserItem({
					key: 'haveShieldOper',
					val: 2
				})
				this.$util.back()
				setTimeout(() => {
					this.$util.goUrl({
						url: 1,
						openType: `navigateBack`

					})
				}, 1000)
			},
			async toCollect() {
				let {
					id,
					is_collect,
					collect_num
				} = this.detail
				let methodModel = is_collect ? 'delCollect' : 'addCollect'
				await this.$api.mine[methodModel]({
					coach_id: id
				})
				this.$util.showToast({
					title: is_collect ? '取消成功' : '收藏成功'
				})
				this.detail.is_collect = is_collect == 1 ? 0 : 1
				this.detail.collect_num = is_collect == 1 ? collect_num - 1 :
					collect_num + 1
			},
			changeBanner(index){
				this.bannerIndex = index
			},
			onPlay(e) {},
			onPause(e) {},
			onWaiting(e) {},
			onProgress(e) {},
			onLoadedMetaData(e) {},
			serviceScroll(e){
				if (this.serviceList.current_page >= this.serviceList.last_page || this.serviceList.data.length >= this.serviceList.total) return;
				this.servicePage = this.servicePage + 1;
				this.$util.showLoading()
				this.getServiceList();
			},
			pageScrollTo(){
				uni.pageScrollTo({
					scrollTop: 0,
					duration: 0
				});
			},
			navigateBack(){
				// #ifndef H5
				uni.navigateBack({
					delta: 1
				})
				// #endif
				// #ifdef H5
				history.back()
				// #endif
			}
		},
		onPageScroll(e) {
			console.log(e)
		}
	}
</script>


<style lang="scss">
	.technician-info {
		.like-label {
			width: 42rpx;
			height: 42rpx;
			background: #EFEFEF;

			.iconfont {
				font-size: 24rpx;
				margin-top: 5rpx;
			}
		}

		.count-list {
			.flex-column {
				// width: 25%;
				padding: 0 80rpx;
				.text {
					font-size: 20rpx;
					color: #B1B1B1;
					margin-top: 5rpx;
				}
			}
		}

		.introduce-info {
			width: 690rpx;
			background: #F7F8FA;
			border-radius: 12rpx;
		}

		.item-label {
			min-width: 168rpx;
			height: 68rpx;
			padding: 0 20rpx;
			color: #4A4A4A;
			background: #F6F7F8;
			border-radius: 34rpx;
		}

		.item-label.mini {
			min-width: 120rpx;
			height: 48rpx;
		}

		.item-label.abs {
			opacity: 0.1;
			border-radius: 34rpx;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 1;
		}

		.list-message {
			.item-avatar {
				width: 52rpx;
				height: 52rpx;
				background: #f4f6f8;
			}

			.iconfont {
				font-size: 28rpx;
				margin-right: 5rpx;
			}
		}

	}
	.taking-order{
		color: #FF8D4C;
		background-color: #FDF1EB;
		padding: 2px 4px;
		font-weight: normal;
	}
	.skill-item{
		padding: 40rpx 0;
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
	
	.user-info-item{
		flex-direction: column;
		padding: 6rpx 0 8rpx 0;
		line-height: 1;
		display: inline-block;
		transform: rotate(360deg);
	}
	.ti-box{
		border-radius: 24rpx 24rpx 0 0 ;
		z-index: 999;
		margin-top: -80rpx;
		position: relative;
		.h40{
			height: 40rpx;
			line-height: normal;
		}
		.ti-box-num{
			padding-top: 50rpx;
		}
		.ti-box-head{
			border-radius: 24rpx 24rpx 0 0 ;
			height: 132rpx;
			background: linear-gradient( 180deg, #EAE4FD 0%, #FDFDFD 100%);
		}
		.ti-box-header{
			margin-top: -58rpx;
			width: 180rpx;
			height: 180rpx;
			border-radius: 180rpx;
			image{
				width: 163rpx;
				height: 163rpx;
				border-radius: 163rpx;
			}
		}
		
		.min94{
			min-width: 94rpx;
		}
		
		.age-bg{
			width: 100%;
			height: 40rpx;
			border-radius: 40rpx;
			opacity: 0.1;
			top: 0;
			left: 0;
		}
	}
	
	.footer-box{
		height: 112rpx;
		.collect-btn{
			width: 329rpx;
			height: 85rpx;
		}
		.contact{
			width: 62rpx;
			height: 62rpx;
			border-radius: 62rpx;
			box-shadow: 0px 0px 6px 3px rgba(220, 220, 220, 1);
		}
	}
	
	.switch-banner{
		width: 192rpx;
		// height: 56rpx;
		background: rgba(0,0,0,0.6);
		border-radius: 56rpx;
		padding: 4rpx;
		.banner-item-text{
			height: 53rpx;
			border-radius: 53rpx;
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
</style>
