<template>
	<view class="user-address-list">
		<view @tap.stop="toUpdateItem(index)" class="item-child mt-md pl-lg pr-lg fill-base radius-16"
			v-for="(item,index) in list.data" :key="index">
			<view class="flex-warp pt-lg pb-lg b-1px-b">
				<view class="address-icon flex-center c-base radius"
					:style="{background:`linear-gradient(to right, ${subColor}, ${primaryColor})`}"><i
						class="iconfont iconjuli"></i></view>
				<view class="address-info flex-1 ml-md">
					<view class="flex-y-baseline username c-title text-bold">{{item.user_name}}
						<view class="ml-md f-desc c-caption">{{item.mobile}}</view>
					</view>
					<view class="f-desc c-title ellipsis">{{`${item.address} ${item.address_info}`}}</view>
				</view>
			</view>
			<view class="oper-info f-paragraph c-caption flex-between">
				<view class="flex-y-center"
					:style="{color:options.check && item.id == check_id || !options.check &&  item.status==1?primaryColor:''}">
					<i class="iconfont icon-xuanze mr-sm"
						:class="[{'icon-xuanze-fill':options.check && item.id == check_id || !options.check &&  item.status==1}]"
						:style="{color:options.check && item.id == check_id || !options.check &&  item.status==1?primaryColor:''}"></i>
					<block v-if="options.check">{{item.id ==check_id?'当前选择地址':'点击选择'}}</block>
					<block v-else>{{item.status ==1?'默认地址':'设为默认'}}</block>
				</view>
				<view class="flex-center">
					<view @tap.stop="toDel(index)" class="pl-lg pr-lg">删除</view>
					<view @tap.stop="goDetail(index)" class="pl-lg">编辑</view>
				</view>
			</view>
		</view>


		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor :tip="[{ text: '你还没有添加地址哦~', color: 0 }]" v-if="!loading&&list.data.length<=0&&list.current_page==1">
		</abnor>

		<view class="space-max-footer"></view>

		<fix-bottom-button @confirm="$util.goUrl({url:`/user/pages/address/edit`})"
			:text="[{text:'添加新地址',type:'confirm'}]" bgColor="#fff"></fix-bottom-button>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		data() {
			return {
				options: {},
				check_id: 0,
				param: {
					page: 1,
				},
				list: {
					data: []
				},
				loading: true,
				lockTap: false,
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
			haveOperItem: state => state.order.haveOperItem,
		}),
		onLoad(options) {
			let {
				check = 0
			} = options
			this.options = options
			if (check) {
				let {
					address_info = {
						id: 0
					}
				} = this.$util.getPage(-1).orderInfo
				let {
					id
				} = address_info
				this.check_id = id
			}
			this.updateOrderItem({
				key: 'haveOperItem',
				val: false
			})
			this.initIndex()
		},
		onUnload() {
			let {
				check = 0
			} = this.options
			let {
				data = []
			} = this.list
			if (check && this.haveOperItem) {
				this.$util.getPage(-1).orderInfo.address_info = {}
				this.$util.back()
			}
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
			...mapMutations(['updateUserItem', 'updateOrderItem']),
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
					param
				} = this
				let newList = await this.$api.mine.addressList(param);

				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			async toUpdateItem(index) {
				let item = this.list.data[index]
				if (this.options.check) {
					this.$util.getPage(-1).orderInfo.address_info = item
					this.$util.back()
					this.$util.goUrl({
						url: 1,
						openType: `navigateBack`
					})
				} else {
					let {
						id,
						status
					} = item
					await this.$api.mine.addressUpdate({
						id,
						status: status == 0 ? 1 : 0
					})
					this.initRefresh()
				}
			},
			async toDel(index) {
				let [res_del, {
					confirm
				}] = await uni.showModal({
					title: '提示',
					content: "请确认是否要删除此数据?"
				})
				if (!confirm) return;
				let {
					id
				} = this.list.data[index]
				let {
					check_id
				} = this
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.mine.addressDel({
						id
					})
					this.$util.hideAll()
					this.$util.showToast({
						title: `删除成功`,
					});
					this.list.data.splice(index, 1)
					this.lockTap = false
					if (id == check_id) {
						this.updateOrderItem({
							key: 'haveOperItem',
							val: true
						})
					}
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
			goDetail(index) {
				let {
					id
				} = this.list.data[index]
				let url = `/user/pages/address/edit?id=${id}`
				this.$util.goUrl({
					url
				})
			},
		}
	}
</script>


<style lang="scss">
	.user-address-list {
		.address-icon {
			width: 64rpx;
			height: 64rpx;

			.iconfont {
				font-size: 38rpx;
			}
		}

		.address-info {
			max-width: 600rpx;
		}

		.username {
			font-size: 30rpx;
		}

		.oper-info {
			height: 80rpx;
		}
	}
</style>
