<template>
	<view class="content" :style="{background: bgColor}">
		<block v-for="(item,index) in text" :key="index">
			<!-- 无间隔的按钮 -->
			<auth @tap.stop.prevent :needAuth="item.isAuth && (userInfo && (!userInfo.phone || !userInfo.nickName))"
				:must="item.isAuth" :type="!userInfo.phone ? 'phone' : 'userInfo'" @go="confirm(item)" :style="{
				width:(100/ text.length)+ '%'}" v-if="classType === 1">
				<view class="bottom-view" :style="{
				margin:text.length==1?'0 30rpx':index==0?'0 0 0 30rpx':'0 30rpx 0 0',
				borderRadius:text.length==1?borderRadius:index==0?'88rpx 0 0 88rpx':'0 88rpx 88rpx 0',
				background: item.type == 'confirm'? primaryColor : subColor}">
					{{ item.text }}
				</view>
			</auth>
			<!-- 有间隔的圆弧形按钮 -->
			<auth @tap.stop.prevent :needAuth="item.isAuth && (userInfo && (!userInfo.phone || !userInfo.nickName))"
				:must="item.isAuth" :type="!userInfo.phone ? 'phone' : 'userInfo'" @go="confirm(item)" :style="{
				width:text.length==2 ? index==0 ? '390rpx' : '360rpx' : (100/ text.length) + '%'}" v-if="classType === 2">
				<view class="bottom-view" :style="{
				margin:text.length==1?'0 30rpx':index==0?'0 30rpx 0 30rpx':'0 30rpx 0 0',
				borderRadius:borderRadius,
				background: item.type == 'confirm'? primaryColor : '',
				color: item.type === 'confirm' ?  '#fff' : item.color || '#000',
				transform: 'rotateZ(360deg)',
				border: item.type == 'confirm'? `1rpx solid ${primaryColor}` : `1rpx solid ${ item.color || '#979797'}`}">
					{{ item.text }}
				</view>
			</auth>
		</block>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		props: {
			text: {
				type: Array,
				default () {
					return [{
						text: '保存',
						type: 'confirm'
					}]
				}
			},
			bgColor: {
				type: String,
				default () {
					return '#F8F8F8'
				}
			},
			classType: {
				type: Number,
				default () {
					return 1
				}
			},
			borderRadius: {
				type: String,
				default () {
					return '88rpx'
				}
			},
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
		}),
		methods: {
			confirm(item) {
				this.$emit(item.type)
			}
		}
	}
</script>

<style scoped lang="scss">
	.content {
		position: fixed;
		bottom: 0rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		width: 100%;
		height: 128rpx;
		z-index: 997;
		height: calc(128rpx + env(safe-area-inset-bottom) / 2);
		padding-bottom: calc(env(safe-area-inset-bottom) / 2);

		.bottom-view {
			width: auto;
			height: 88rpx;
			line-height: 88rpx;
			text-align: center;
			font-size: 32rpx;
			font-weight: bold;
			color: #FFFFFF;
		}
	}
</style>
