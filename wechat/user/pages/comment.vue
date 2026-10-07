<template>
	<view class="user-pages-comment">
		<view class="list-message flex-warp mt-md ml-lg mr-lg pd-lg fill-base radius-16" :class="[{'b-1px-t':index!=0}]"
			v-for="(item,index) in list.data" :key="index">
			<image mode="aspectFill" class="item-avatar radius" :src="item.avatarUrl"></image>
			<view class="flex-1 ml-md">
				<view class="flex-between">
					<view class="flex-y-center">
						<view class="f-paragraph c-title mr-md">{{item.nickName}}</view>
						<view class="flex-warp">
							<i class="iconfont iconyduixingxingshixin"
								:style="{color: aindex< item.star?subColor:'#eee'}" v-for="(aitem,aindex) in 5"
								:key="aindex"></i>
						</view>
					</view>
					<view class="f-icontext c-caption">{{item.create_time}}</view>
				</view>
				<view class="flex-warp mt-sm">
					<view class="pt-sm pb-sm pl-md pr-md mt-sm mr-sm radius fill-body f-caption c-desc"
						v-for="(item,index) in item.lable_text" :key="index">{{item}}</view>
				</view>
				<view class="f-caption c-caption mt-md">
					<text decode="emsp" style="word-break:break-all;">{{item.text}}</text>
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
		components: {},
		data() {
			return {
				options: {},
				param: {
					page: 1,
					coach_id: ''
				},
				list: {
					data: []
				},
				loading: true,
				showInd: 0,
				showType: '',
				lockTap: false,
				serviceList: [],
				commentList: []
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			carList: state => state.order.carList,
		}),
		onLoad(options) {
			this.$util.showLoading()
			let {
				id
			} = this.$util.getPage(-1).list.data[options.index]
			this.param.coach_id = id
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
			...mapActions(['getCarList']),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			async getList() {
				let {
					list: oldList,
					param,
				} = this
				let newList = await this.$api.service.commentList(param);

				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			}
		}
	}
</script>


<style lang="scss">
	.user-pages-comment {
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
</style>
