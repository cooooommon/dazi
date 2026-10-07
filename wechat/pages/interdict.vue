<template>
	<view class="">
		<!-- #ifndef H5 -->
		<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true" title="提示页"
			color="#333" backgroundColor=" ">
		</uni-nav-bar>
		<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
		<!-- #endif -->
		<view class="interdict">
			<view class="flex-center">
				<image src="https://lbqny.migugu.com/admin/public/no-blacklist.png" mode="aspectFill" class="bg"></image>
			</view>
			<view class="flex-center f-desc">很抱歉,您无法进入该系统</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data(){
			return{
				
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
		}),
	}
</script>

<style scoped>
	.interdict{
		padding-top: 300rpx;
	}
	.bg{
		width: 413rpx;
		height: 361rpx;
	}
</style>