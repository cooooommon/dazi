<template>
	<view class="pages-user-bell" v-if="isLoad">
		<fixed v-if="can_add_order">
			<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1" :activeColor="primaryColor"
				width="50%" height="100rpx"></tab>
			<view class="b-1px-b"></view>
		</fixed>
		<view class="pt-lg pl-lg pr-lg" v-if="activeIndex==0 && have_check.length>0">
			<view class="f-desc c-paragraph text-bold">已选择项目</view>
			<block v-for="(item, index) in have_check" :key="index">
				<view class="list-item pd-lg fill-base radius-16" :class="[{'mt-lg':index==0},{'mt-md':index!=0}]">
					<view class="flex-center">
						<!-- #ifdef H5 -->
						<view class="cover radius-16">
							<view class="h5-image cover radius-16" :style="{ backgroundImage : `url('${item.cover}')`}">
							</view>
						</view>
						<!-- #endif -->
						<!-- #ifndef H5 -->
						<image mode="aspectFill" class="cover radius-16" :src="item.cover"></image>
						<!-- #endif -->

						<view class="flex-1 ml-md" style="max-width: 456rpx;">
							<view class="flex-between">
								<view class="f-title c-title text-bold ellipsis" style="max-width: 250rpx;">
									{{ item.title }}
								</view>
								<view class="flex-warp">
									<button @tap.stop="changeNum(-1,index)" class="reduce"
										:style="{borderColor:primaryColor,color:primaryColor}"><i
											class="iconfont icon-jian-bold"></i></button>
									<button class="addreduce clear-btn">{{item.init_add_num || 0}}</button>

									<button @tap.stop="changeNum(1,index)" class="add"
										:style="{background:primaryColor,borderColor:primaryColor}"><i
											class="iconfont icon-jia-bold"></i></button>
								</view>

							</view>
							<view class="flex-warp mt-sm">
								<view class="time-long flex-center">{{ item.time_long }}分钟</view>
								<view class="f-caption c-caption">{{ item.total_sale }}人已预约</view>
							</view>
							<view class="flex-between mt-md">
								<view class="flex-y-baseline">
									<view class="flex-y-baseline f-icontext c-warning ml-sm mr-sm">¥<view
											class="f-sm-title">
											{{ item.price }}
										</view>
									</view>
									<view class="flex-y-baseline f-caption c-paragraph" v-if="!activeIndex">需补差价:<view
											class="c-warning">
											¥{{item.init_add_price}}</view>
									</view>
								</view>
								<view @tap.stop="toAddDelItem(2,index)" class="remove-btn flex-center f-caption radius"
									:style="{color:primaryColor,border:`1rpx solid ${primaryColor}`}">移除
								</view>
							</view>
						</view>
					</view>

					<view class="flex-between f-desc mt-lg pt-lg b-1px-t" style="color: #2E3541;">
						<view class="flex-y-center">{{item.order_goods_name | textFormat }}</view>
						<view class="shengji-icon-text flex-1 flex-center flex-column" :style="{color:primaryColor}">
							<view class="f-icontext">更换为</view>
							<i class="iconfont iconshengji"></i>
						</view>
						<view class="flex-y-center">{{item.title | textFormat }}</view>
					</view>
				</view>
			</block>
		</view>

		<view class="pd-md" v-if="activeIndex==1">
			<view class="flex-y-center f-title c-title text-bold"> 选择续单项目 </view>
		</view>

		<view class="flex-center pt-lg pb-sm pl-lg pr-lg fill-base mt-md rel" v-if="activeIndex === 0">
			<view class="flex-1 flex-y-center f-title c-title text-bold">
				将<view :style="{color:primaryColor}">{{order_goods[goodsInd].goods_name | textFormat }}</view>更换为以下项目
			</view>
			<!-- <view @tap.stop="showGoods = !showGoods" class="change-btn flex-center radius"
				:style="{color:primaryColor,border:`1rpx solid ${primaryColor}`}">切换项目 <i class="iconfont"
					:class="[{'icon-up-fill':showGoods},{'icon-down-fill':!showGoods}]"></i></view>
			<view class="change-goods-info pd-md abs" v-if="showGoods">
				<view @tap.stop="toChangeGoods(index)" class="goods-title flex-y-center"
					:class="[{'c-title':goodsInd!=index},{'c-base cur':goodsInd==index}]"
					:style="{background:goodsInd==index?primaryColor:'',borderColor:goodsInd==index?primaryColor:''}"
					v-for="(item,index) in order_goods" :key="index">{{item.goods_name}}
				</view>
			</view> -->
		</view>
		<view :class="[{'pl-lg pr-lg fill-base' :activeIndex==0}]">
			<block v-for="(item, index) in serviceList" :key="index">
				<view @tap.stop="toAddDelItem(1, index)" class="list-item flex-center"
					:class="[{'pt-lg pb-lg':activeIndex==0},{'ml-md mr-md pd-lg fill-base radius-16':activeIndex==1},{'b-1px-t':index!=0 && item.id != no_check_id && activeIndex==0},{'mt-md':index!=0&&activeIndex==1}]"
					v-if="(activeIndex==0&&!item.is_check) || activeIndex==1">
					<i class="iconfont" :class="[{'icon-xuanze':!item.is_check},{'icon-xuanze-fill':item.is_check}]"
						:style="{color:item.is_check?primaryColor:'#999'}"></i>
					<view class="flex-center flex-1 ml-md">
						<!-- #ifdef H5 -->
						<view class="cover radius-16">
							<view class="h5-image cover radius-16" :style="{ backgroundImage : `url('${item.cover}')`}">
							</view>
						</view>
						<!-- #endif -->
						<!-- #ifndef H5 -->
						<image mode="aspectFill" class="cover radius-16" :src="item.cover"></image>
						<!-- #endif -->

						<view class="flex-1 ml-md" :style="{maxWidth:activeIndex==0?'456rpx':'416rpx'}">
							<view class="flex-between">
								<view class="f-title c-title text-bold ellipsis" style="max-width: 250rpx;">
									{{ item.title }}
								</view>
								<view class="f-caption c-caption">{{ item.total_sale }}人已预约</view>
							</view>
							<view class="flex-between mt-sm">
								<view class="time-long flex-center">{{ item.time_long }}分钟</view>
								<view></view>
							</view>
							<view class="flex-center mt-md">
								<view class="flex-1 flex-y-baseline f-icontext c-warning ml-sm mr-sm">¥<view
										class="f-sm-title">
										{{ item.price }}
									</view>
								</view>
								<view class="flex-y-center f-paragraph c-paragraph" v-if="activeIndex==0">需补差价<view
										class="c-warning">¥{{item.init_add_price}}</view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</block>
			<view class="space-md" v-if="have_all_check || serviceList.length==0"></view>
		</view>

		<!-- <abnor v-if="(!loading&&serviceList.length==0) || (activeIndex==0 && have_all_check)"></abnor> -->

		<view class="space-max-footer"></view>

		<fix-bottom-button @cancel="$util.goUrl({url:1,openType:`navigateBack`})" @confirm="toOrder"
			:text="[{text:`暂不${navTitle}`,type:'cancel'},{text:'下单',type:'confirm',isAuth:true}]" bgColor="#fff"
			:classType="2">
		</fix-bottom-button>


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
				isLoad: false,
				options: {},
				navTitle: '',
				tabList: [{
					id: 1,
					title: '项目更换'
				}, {
					id: 2,
					title: '项目续单'
				}],
				activeIndex: 0,
				serviceList: [],
				serviceList_check: [],
				no_check_id: 0,
				have_check: [],
				have_check_num: 0,
				have_all_check: false,
				can_add_order: 0, // 是否可续单
				order_goods: [],
				order_uprecord: [],
				goodsInd: 0,
				showGoods: false,
				loading: true,
				popupInfo: {},
				lockTap: false,
				activeIndex1: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			carList: state => state.order.carList,
		}),
		onLoad(options) {
			this.options = options
			let {
				tab = 0
			} = options
			this.activeIndex = tab
			this.initIndex()
		},
		watch: {
			'have_check'(value) {
				if (this.activeIndex == 0) {
					this.toFormatHaveCheck()
				}
			}
		},
		filters: {
			textFormat(val) {
				let text = val
				if (val.length > 5) {
					text = val.substring(0, 5) + '...'
				}
				return text
			}
		},
		methods: {
			...mapActions(['getCarList']),
			...mapMutations(['updateOrderItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await this.getDetail()
				await this.getList()
				uni.setNavigationBarTitle({
					title: this.navTitle
				})
				this.isLoad = true
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.initIndex(true)
			},
			async getDetail() {
				let {
					id,
				} = this.options
				let {
					order_goods,
					can_add_order
				} = await this.$api.order.orderInfo({
					id
				})
				this.order_goods = order_goods
				this.can_add_order = can_add_order
				this.navTitle = can_add_order ? '更换/续单' : '更换项目'
			},
			async getList() {
				let {
					id: order_id,
					coach_id
				} = this.options
				await this.$api.order.delSomeCar({coach_id})
				let {
					id: order_goods_id,
					true_price,
				} = this.order_goods[this.goodsInd]
				await this.getCarList({
					coach_id,
					order_id
				})
				let {
					activeIndex
				} = this
				let methodModel = activeIndex === 0 ? 'getUpOrderGoods' : 'coachServiceList'
				let param = activeIndex === 0 ? {
					order_goods_id
				} : {
					coach_id,
					is_add: 1
				}
				let data = await this.$api.service[methodModel](param)
				if (activeIndex == 1) {
					data = data.data
				}

				let arr = []
				this.carList.list.map(item => {
					arr.push(item.service_id)
				})
				data.map(item => {
					item.is_check = false
					if (activeIndex == 0) {
						item.init_add_num = 1
						item.init_add_price = (item.price - true_price).toFixed(2)
						item.is_check = this.serviceList_check.includes(item.id)
					}
					if (arr.includes(item.id)) {
						let carInd = arr.findIndex(aitem => {
							return aitem == item.id
						})
						item.num = this.carList.list[carInd].num
						item.cart_id = this.carList.list[carInd].id
						item.is_check = true
					}
				})
				this.serviceList = data
				await this.toFormatHaveCheck(3)
				this.loading = false
				this.$util.hideAll()
			},
			handerTabChange(index) {
				this.activeIndex = index
				this.serviceList = []
				this.getList()
			},
			async toChangeGoods(index) {
				this.goodsInd = index
				this.showGoods = false
				await this.toFormatHaveCheck(2)
				this.handerTabChange(0)
			},
			async toFormatHaveCheck(type = 1) {
				let {
					id
				} = this.order_goods[this.goodsInd]
				if (type != 3) {
					let arr = this.have_check.filter(item => {
						return item.order_goods_id == id
					})
					this.have_all_check = arr.length == this.serviceList.length

					this.serviceList_check = arr.map(item => {
						return item.id
					})

					let have_check_num = 0
					arr.map(item => {
						have_check_num = have_check_num + item.init_add_num
					})
					this.have_check_num = have_check_num

				}

				if (type != 2) {
					let no_check = this.serviceList.filter(item => {
						return !item.is_check
					})
					this.no_check_id = no_check.length > 0 ? no_check[0].id : 0
				}
			},
			// 加/减数量
			async changeNum(mol, index) {
				let have_check = this.$util.deepCopy(this.have_check)
				let {
					init_add_num
				} = have_check[index]
				let {
					num: goods_num
				} = this.order_goods[this.goodsInd]
				let {
					have_check_num
				} = this
				let num = init_add_num + mol
				if ((have_check_num + mol > goods_num) || !num) {
					this.$util.showToast({
						title: !num ? `数量至少为1` : `数量不可大于${init_add_num}`
					})
					return
				}
				have_check[index].init_add_num = num
				this.have_check = have_check
			},
			// 选择更换项目/续单项目加购
			async toAddDelItem(type, index, flag = false) {
				let {
					activeIndex,
					have_check_num
				} = this
				console.log(type, index)
				this.activeIndex1 = index
				if (activeIndex == 0) {
					let {
						id: order_goods_id,
						num: goods_num,
						goods_name
					} = this.order_goods[this.goodsInd]

					if (type == 1 && have_check_num == goods_num) {
						this.$util.showToast({
							title: `当前项目最多可更换${goods_num}个服务数量`
						})
						return
					}
					if (type == 1) {
						this.serviceList[index].order_goods_id = order_goods_id
						this.serviceList[index].order_goods_name = goods_name
						this.serviceList[index].is_check = true
						this.have_check.push(this.serviceList[index])
					} else {
						let {
							id: serid,
							order_goods_id: ogid
						} = this.have_check[index]
						this.have_check.splice(index, 1)
						if (order_goods_id == ogid) {
							let serind = this.serviceList.findIndex(item => {
								return item.id === serid
							})
							this.serviceList[serind].is_check = false
						}
					}
					return
				}

				let {
					id: order_id,
					coach_id
				} = this.options
				let {
					id: service_id,
					is_check = false,
					num = 0,
					cart_id = 0
				} = this.serviceList[index]
				let check = !is_check
				if (this.lockTap) return;
				this.lockTap = true;
				let methodModel = check ? 'addCar' : 'delCar'
				let param = check ? {
					service_id,
					coach_id,
					order_id,
					num: 1
				} : {
					id: cart_id,
					num: 1
				}
				try {
					let add_cart_id = await this.$api.order[methodModel](param)
					this.serviceList.map(item => {
						item.is_check = false
					})
					this.serviceList[index].is_check = check
					this.serviceList[index].num = check ? 1 : 0
					if (add_cart_id && check && !cart_id) {
						this.serviceList[index].cart_id = add_cart_id
					}
					if (this.serviceList[index].num < 1) {
						this.serviceList[index].cart_id = 0
					}
					await this.getCarList({
						coach_id,
						order_id
					})
					this.lockTap = false
				} catch (e) {
					this.lockTap = false
				}

			},
			// 下单
			async toOrder() {
				let {
					have_check,
					activeIndex,
					carList
				} = this
				if ((activeIndex == 0 && have_check.length == 0) || (activeIndex == 1 && carList.car_count < 1)) {
					let msg = activeIndex == 0 ? `更换` : `续单`
					this.$util.showToast({
						title: `请选择${msg}服务`
					})
					return
				}
				let {
					id: order_id,
					coach_id
				} = this.options

				let val = {
					order_type: activeIndex
				}

				if (activeIndex == 0) {

					let order_goods = have_check.map(aitem => {
						return {
							order_goods_id: aitem.order_goods_id,
							service_id: aitem.id,
							num: aitem.init_add_num
						}
					})

					val = Object.assign({}, val, {
						order_id,
						order_goods
					})

				} else {
					val = Object.assign({}, val, {
						coach_id,
						order_id
					})
					await this.$api.order.checkAddOrder({
						order_id
					})
				}

				this.updateOrderItem({
					key: 'bellOrderParams',
					val
				})
				this.$util.goUrl({
					url: `/user/pages/bell/order`,
					openType: `redirectTo`
				})
			},
		}
	}
</script>


<style lang="scss">
	.pages-user-bell {
		.change-goods-info {
			top: 89rpx;
			right: 30rpx;
			min-width: 218rpx;
			min-height: 280rpx;
			max-height: 500rpx;
			overflow-y: auto;
			background: #FFFFFF;
			box-shadow: 0px 6px 16px 0px rgba(222, 222, 222, 0.37);
			border-radius: 16px;
			border: 1px solid #F5F5F5;
			z-index: 99999;

			.goods-title {
				font-size: 20rpx;
				margin: 15rpx 0;
			}

			.cur {
				height: 36rpx;
				padding: 0 9rpx;
				border-radius: 4rpx;
			}
		}

		.list-item {

			.icon-xuanze,
			.icon-xuanze-fill {
				font-size: 40rpx;
			}

			.cover {
				width: 155rpx;
				height: 155rpx;
			}

			.time-long {
				min-width: 72rpx;
				height: 30rpx;
				padding: 0 5rpx;
				background: linear-gradient(270deg, #4C545A 0%, #282B34 100%);
				border-radius: 4rpx;
				font-size: 20rpx;
				color: #FFEEB9;
				margin-right: 16rpx;
			}

			.f-icontext {
				font-size: 18rpx;
			}

			.text-delete {
				font-size: 20rpx;
				color: #B9B9B9;
			}

			.remove-btn {
				width: 110rpx;
				height: 54rpx;
				transform: rotateZ(360deg);
			}

			.shengji-icon-text {
				height: 60rpx
			}

		}

		.change-btn {
			width: 125rpx;
			height: 46rpx;
			font-size: 20rpx;
			transform: rotateZ(360deg);

			.iconfont {
				font-size: 20rpx;
				transform: scale(0.8);
			}
		}
	}
</style>