<template>
	<view class="evaluate-pages" v-if="isLoad">
		<fixed>
			<view class="flex-y-center evaluate-screen-box fill-body">
				<block v-for="(item,index) in tabbar" :key="index">
					<view class="flex-center evaluate-screen pl-md pr-md f-caption mr-md" @tap="changeTab(index)"
					:style="{color: activeIndex ==index ? '#fff':'#9E9EB5',background:activeIndex ==index? primaryColor :'#fff' }">{{`${item.name} ${item.number}`}}</view>
				</block>
			</view>
		</fixed>
		<view class="pl-lg pr-lg radius-16 fill-base ml-md mr-md pt-lg mb-md" v-for="(item,index) in list.data" :key="index">
			<view class="flex-between">
				<view class="flex-between flex-1">
					<view class="flex-center">
						<image :src="item.avatarUrl" mode="aspectFill" class="evaluate-header">
						</image>
						<view class="pl-sm">
							<view class="f-caption ">{{item.nickName}}</view>
							<view class="f-ms-little c-caption ">{{$util.formatTime(item.create_time * 1000 , 'YY-M-D h:m')}}</view>
						</view>
					</view>
					<view class="flex-warp">
						<block v-for="(s,index) in item.star*1" :key="index">
							<i class="iconfont iconpingjia1 icon-font-color icon-solid"></i>
						</block>
						<block v-for="(s,index) in (5 - item.star*1)" :key="index">
							<i class="iconfont iconpingjia1 icon-empty"></i>
						</block>
					</view>
				</view>
			</view>
			<view class="e-text">
				<view class="f-desc pre-wrap">
					{{ item.is_text ? item.new_text : item.text}}
				</view>
				<view @tap="changeMore(index)" class="f-caption pt-sm" style="color: #F04DAA;" v-if="item.text.length > 95">{{item.is_text ? `全部` : `收起`}}</view>
			</view>
			
			<view class="e-images flex-warp">
				<block v-for="(src,sindex) in item.img" :key="sindex">
					<image @tap="$util.previewImage({current:src,urls:item.img})" :src="src" mode="aspectFill" class="evaluate-image radius-16">
					</image>
				</block>
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
	import siteInfo from '@/siteinfo.js';
	export default {
		data() {
			return {
				options: {},
				loading: true,
				isLoad: true,
				star: 3,
				tabbar: [
					{name: '全部',number: 0,id: 0},
					{name: '好评',number: 0,id: 1},
					{name: '差评',number: 0,id: 2}
				],
				activeIndex: 0,
				list: {
					data: []
				},
				param: {
					page: 1,
					store_id: '',
					type: 0, //0全部1好评 2差评
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			mineInfo: state => state.user.mineInfo,
		}),
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
		onLoad(options) {
			this.param.store_id = options.id
			this.$util.showLoading()
			this.initIndex()
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
				this.getList(1)
			},
			initRefresh(){
				this.$util.showLoading()
				this.initIndex(true)
			},
			changeTab(index){
				this.$util.showLoading()
				this.activeIndex = index
				this.param.type = this.tabbar[index].id
				this.getList(1)
			},
			async getList(page){
				if(page){
					this.param.page = 1
				}
				let {
					list: oldList,
					param
				} = this
				let newList = await this.$api.business.commentList(param)
				
				newList.data.forEach(item =>{
					item.is_text = false
					item.new_text = item.text
					if(item.text.length > 95){
						item.is_text = true
						item.new_text = item.text.substring(0,95) + '...'
					}
					item.img = item.img ? item.img.split(',') : []
				})
				if (param.page == 1) {
					this.list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList;
				}
				this.tabbar[0].number = newList.star_good*1 + newList.star_bad*1
				this.tabbar[1].number = newList.star_good
				this.tabbar[2].number = newList.star_bad
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
			},
			changeMore(index){
				let {
					is_text
				} = this.list.data[index]
				this.list.data[index].is_text = is_text ? false : true
			}
			
		}
	}
</script>

<style lang="scss">
	.evaluate-pages {
		.evaluate-screen-box{
			padding: 50rpx 30rpx 36rpx 30rpx;
		}
		.evaluate-screen{
			height: 64rpx;
			min-width: 158rpx;
			border-radius: 64rpx;
		}
		.evaluate-header{
			width: 72rpx;
			height: 72rpx;
			border-radius:72rpx ;
		}
		.icon-solid {
			background-image: linear-gradient(#FAD961, #F76B1C);
			margin-right: 2px;
		}
		
		.icon-empty {
			color: #E4E4E4;
			margin-right: 2px;
		}
		.e-text{
			padding: 25rpx 0 30rpx 0;
		}
		.e-images{
			padding-bottom: 25rpx;
			image {
				width: 212rpx;
				height: 212rpx;
				margin-bottom: 6rpx;
				margin-right: 6rpx;
			}
			image:nth-child(3n){
				margin-right: 0;
			}
		}
	}
</style>
