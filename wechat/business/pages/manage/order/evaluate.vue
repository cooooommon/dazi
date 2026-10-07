<template>
	<view class="evaluate-pages" v-if="isLoad">
		<view class="pl-lg pr-lg radius-16 fill-base ml-md mr-md pt-lg mb-md mt-md">
			<view class="flex-between">
				<view class="flex-between flex-1">
					<view class="flex-center">
						<image :src="detail.avatarUrl" mode="aspectFill" class="evaluate-header">
						</image>
						<view class="pl-sm">
							<view class="f-caption ">{{detail.nickName}}</view>
							<view class="f-ms-little c-caption ">{{$util.formatTime(detail.create_time * 1000 , 'YY-M-D h:m')}}</view>
						</view>
					</view>
					<view class="flex-warp">
						<block v-for="(s,index) in detail.star*1" :key="index">
							<i class="iconfont iconpingjia1 icon-font-color icon-solid"></i>
						</block>
						<block v-for="(s,index) in (5 - detail.star*1)" :key="index">
							<i class="iconfont iconpingjia1 icon-empty"></i>
						</block>
					</view>
				</view>
			</view>
			<view class="e-text">
				<view class="f-desc pre-wrap">
					{{ detail.is_text ? detail.new_text : detail.text}}
				</view>
				<view @tap="changeMore(index)" class="f-caption pt-sm" style="color: #F04DAA;" v-if="detail.text.length > 95">{{detail.is_text ? `全部` : `收起`}}</view>
			</view>
			
			<view class="e-images flex-warp">
				<block v-for="(src,sindex) in detail.img" :key="sindex">
					<image @tap="$util.previewImage({current:src,urls:detail.img})" :src="src" mode="aspectFill" class="evaluate-image radius-16">
					</image>
				</block>
			</view>
		</view>
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
				isLoad: false,
				detail: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			mineInfo: state => state.user.mineInfo,
		}),
		onLoad(options) {
			this.options = options 
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
				this.getInfo()
			},
			async getInfo(){
				let data = await this.$api.business.seeComment({order_id: this.options.id})
				data.img = data.img ? data.img.split(',') : [],
				data.is_text = false
				data.new_text = data.text
				if(data.text.length > 95){
					data.is_text = true
					data.new_text = data.text.substring(0,95) + '...'
				}
				this.detail = data
				this.isLoad = true
				this.$util.hideAll()
			},
			changeMore(index){
				let {
					is_text
				} = this.detail
				this.detail.is_text = is_text ? false : true
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
