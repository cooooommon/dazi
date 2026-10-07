<template>
	<uni-popup type="center" ref="open_miniprogram_popup">
		<view class="common-popup-content fill-base">
			<view class="title">跳转小程序</view>
			<image mode="aspectFill" class="image" :src="info.img" v-if="info.img"></image>
			<view class="name" v-if="info.title">{{info.title}}</view>
			<view class="button">
				<view @tap.stop="close" class="item-child">取消 </view>
				<wx-open-launch-weapp :appid="info.appid" :path="info.path">
					<script type="text/wxtag-template">
						<style>
				          .btn {
						    width:120px;
						    height:45px;
				            font-size: 14px;
				            text-align: center;
				            color: #ffffff; 
				            outline: none;
				            border: 0;
				            border-radius: 45px;
				            padding: 12px 40px;
							background: #19c865;
							margin:0 10px;
							padding:0px;
				          }
				        </style>
				        <button class="btn">立即前往</button>
				      </script>
				</wx-open-launch-weapp>
			</view>
		</view>
	</uni-popup>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		props: {
			info: {
				type: Object,
				default () {
					return {}
				}
			},
		},
		data() {
			return {}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		methods: {
			...mapActions(['getConfigInfo']),
			...mapMutations(['updateUserItem']),
			open() {
				this.$refs.open_miniprogram_popup.open()
			},
			close() {
				this.$refs.open_miniprogram_popup.close()
			},
		},
	}
</script>

<style scoped lang="scss">
	.change-user-box {
		height: 93rpx;
		padding: 5rpx;

		.change-user-btn {
			width: 83rpx;
			height: 83rpx;
		}

	}
</style>