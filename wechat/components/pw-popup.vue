<template>
	<view class="">
		<uni-popup ref="pw_popup" :maskClick="maskClick">
			<view class="pw-index fill-base">
				<view class="pw-title text-center text-bold f-title radius-26 ">{{title}}</view>
				<view class="ml-md mr-md pl-md pr-md f-paragraph pw-cont text-center" v-if="content">{{content}}</view>
				<view class="ml-lg mr-lg f-paragraph pw-cont text-center" v-else>
					<slot></slot>
				</view>
				<view class="flex-center pl-lg pr-lg">
					<block v-for="(item,index) in text" :key="index" >
						<view @click="confirm(item)"  class="pw-btn text-center" :class="index==1?'ml-md':''" :style="{
							border:item.type=='confirm'?'':`1px solid #C7C7C7`,
							color:item.type=='confirm'?'#fff':'#222',
							backgroundColor:item.type=='confirm'?primaryColor:'#fff'}" >{{item.text}}</view>
					</block>
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		name: 'PwPopup',
		props:{
			content:{
				type:String,
				default: ''
			},
			text:{
				type:Array,
				default: [{
						text: '保存',
						type: 'confirm'
					}]
			},
			title:{
				type:String,
				default: '提示'
			},
			maskClick:{
				type: Boolean,
				default: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
		}),
		methods:{
			clear() {},
			open() {
				this.$refs.pw_popup.open()
			},
			close(type) {
				this.$refs.pw_popup.close()
			},
			confirm(item){
				this.$emit(item.type)
			}
		}
	}
</script>

<style lang="scss">
	.pw-index{
		width: 516rpx;
		padding-bottom: 40rpx;
		.pw-title{
			padding: 50rpx 0 40rpx 0;
		}
		.pw-cont{
			color: #4A4A4A;
			min-height: 154rpx;
			padding-bottom: 30rpx;
		}
		.pw-btn{
			width: 210rpx;
			height: 72rpx;
			border-radius: 72rpx;
			line-height: 72rpx;
		}
	}
</style>