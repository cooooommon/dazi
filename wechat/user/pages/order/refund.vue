<template>
	<view class="user-order-refund" v-if="detail.id">
		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16">
			<view class="flex-between pb-lg b-1px-b">
				<view class="f-paragraph c-title max-380 ellipsis">订单号：{{detail.order_code}}</view>
				<view class="f-caption text-bold"
					:style="{color:detail.pay_type==2?primaryColor: detail.pay_type<6?subColor: detail.pay_type == 6 ? '#11C95E' : '#333'}">
					{{statusType[detail.pay_type]}}
				</view>
			</view>
			<block v-for="(item,index) in detail.order_goods" :key="index">
				<view @tap="handerRadioChange(index)" class="item-child flex-center mt-lg"
					v-if="item.can_refund_num > 0">
					<i class="iconfont mr-md" :class="[{'icon-xuanze':!item.checked},{'icon-xuanze-fill':item.checked}]"
						:style="{color:item.checked ? primaryColor : ''}"></i>
					<view class="flex-1">
						<view class="flex-center">
							<!-- #ifdef H5 -->
							<view class="goods-img radius-16">
								<view class="h5-image goods-img radius-16"
									:style="{ backgroundImage : `url('${item.goods_cover}')`}">
								</view>
							</view>
							<!-- #endif -->
							<!-- #ifndef H5 -->
							<image mode="aspectFill" class="goods-img radius-16" :src="item.goods_cover"></image>
							<!-- #endif -->

							<view class="flex-1 ml-md max-380">
								<view class="f-title c-title text-bold max-450 ellipsis">{{item.goods_name}}</view>
								<view class="f-caption c-caption">
									服务{{$t('action.attendantName')}}：{{detail.coach_info ? detail.coach_info.coach_name : '-'}}</view>
								<view class="f-caption c-caption">{{detail.start_time}}</view>
								<view class="flex-between">
									<view class="flex-y-baseline f-caption c-warning">¥<view class="f-title text-bold">
											{{item.true_price}}
										</view>
									</view>
									<view class="flex-warp" v-if="item.can_refund_num > 1">
										<button @tap.stop="changeNum(index,-1)" class="reduce"
											:style="{borderColor:primaryColor,color:primaryColor}"><i
												class="iconfont icon-jian-bold"></i></button>
										<button class="addreduce clear-btn">{{item.apply_num}}</button>
										<button @tap.stop="changeNum(index,1)" class="add"
											:style="{background:primaryColor,borderColor:primaryColor}"><i
												class="iconfont icon-jia-bold"></i></button>
									</view>
									<view class="c-paragraph" v-else>x{{item.apply_num}}</view>
								</view>

							</view>
						</view>
					</view>
				</view>
			</block>
			<view class="mt-lg pt-lg pb-lg f-paragraph c-title b-1px-tb"
				v-if="detail.can_refund_car_price && detail.car_price*1>0">
				<view class="flex-between">
					<view>服务金额</view>
					<view>¥{{detail.can_refund_price}}</view>
				</view>
				<block v-if="detail.can_refund_car_price">
					<view class="flex-between mt-sm">
						<view class="flex-y-baseline">车费<view class="f-icontext c-warning ml-sm">{{$t('action.attendantName')}}出发前全部服务退款将退还</view>
						</view>
						<view>¥{{detail.car_price}}</view>
					</view>
				</block>
			</view>
			<view class="mt-lg f-paragraph c-title flex-between"
				:class="[{'mt-sm':detail.can_refund_car_price && detail.car_price*1>0},{'pt-lg b-1px-t':!detail.can_refund_car_price || detail.can_refund_car_price && detail.car_price*1==0}]">
				<view>合计</view>
				<view class="f-title c-warning text-bold">
					{{`${can_refund_price}元`}}
				</view>
			</view>
		</view>
		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16">
			<view class="flex-between pb-lg f-title c-title text-bold">退款原因</view>
			<view class="textarea-info f-caption c-caption radius-16">
				<textarea v-model="form.text" class="input-textarea f-paragraph pd-lg" placeholder-class="f-paragraph"
					maxlength="300" placeholder="输入退款原因" />
				<view class="text-right pb-lg pr-lg">{{form.text.length>300?300:form.text.length}}/300</view>
			</view>
		</view>
		<view class="item-child mt-md ml-lg mr-lg pt-lg pl-lg pr-lg fill-base radius-16">
			<view class="flex-between pb-sm f-title c-title text-bold">上传图片</view>
			<view class="flex-between pt-sm">
				<upload @del="imgDel" @upload="imgUpload" :imagelist="form.imgs" imgtype="imgs" imgclass="mini"
					text="添加照片" :imgsize="5"></upload>
			</view>
			<view class="space-lg" v-if="form.imgs.length == 0"></view>
			<view class="space-sm" v-else></view>
		</view>
		<view class="item-child mt-md ml-lg mr-lg pt-lg pl-lg pr-lg fill-base radius-16 pb-lg">
			<view class="flex-between pb-md f-title c-title text-bold">退款须知</view>
			<view class="pre-wrap f-desc c-caption">{{configInfo.refund_notice}}</view>
		</view>

		<view class="space-max-footer"></view>

		<view class="refund-bottom-info fill-base fix pl-lg pr-lg">
			<view class="flex-between">
				<view @tap="selectAllItem" class="flex-y-center"><i class="iconfont mr-sm"
						:class="[{'icon-xuanze':!selectAll},{'icon-xuanze-fill':selectAll}]"
						:style="{color:selectAll? primaryColor : ''}"></i>
					全选
				</view>
				<view class="text-right flex-center">
					<view class="flex-y-center">
						<view class="f-caption c-caption mr-sm">共{{total_refund_num}}件</view>
						退款金额<view class="flex-y-baseline f-caption c-warning">¥<view class="f-title text-bold">
								{{total_refund_price}}
							</view>
						</view>
					</view>
					<button @tap.stop="toSubmit" class="clear-btn order"
						:style="{color:'#fff',background:primaryColor,borderColor:primaryColor}">提交申请</button>
				</view>
			</view>
			<view class="space-safe"></view>
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
		components: {},
		data() {
			return {
				options: {},
				detail: {},
				statusType: {
					'-1': '已取消',
					1: '待支付',
					2: '待服务',
					3: this.$t('action.orderTaking'),
					4: this.$t('action.setOut'),
					5: this.$t('action.arrive'),
					6: '服务中',
					7: '已完成',
					8: '已评价',
				},
				total_refund_num: 0,
				total_refund_price: 0,
				can_refund_num: 0,
				can_refund_price: 0,
				selectAll: false,
				selectAdd: false,
				form: {
					text: '',
					imgs: []
				},
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
		}),
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		methods: {
			...mapMutations(['updateUserItem']),
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
				this.getConfigInfo()
				let {
					id
				} = this.options
				let data = await this.$api.order.orderInfo({
					id
				})
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})

				let {
					car_price,
					can_refund_car_price = 0
				} = data

				let num = 0
				let can_refund_price = 0
				data.order_goods.map(item => {
					num += item.can_refund_num
					item.apply_num = item.can_refund_num
					item.checked = item.can_refund_num < 1
					if (item.apply_num > 0) {
						can_refund_price += item.true_price * item.apply_num
					}
				})
				let total_price = can_refund_price.toFixed(2) * 1
				data.can_refund_price = total_price
				this.detail = data
				this.can_refund_num = num
				this.can_refund_price = can_refund_car_price ? (total_price +
					car_price * 1).toFixed(2) : total_price
			},
			initRefresh() {
				this.initIndex(true)
			},
			imgDel(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.form[imgtype] = imagelist;
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.form[imgtype] = imagelist;
			},
			handerRadioChange(index) {
				let item = this.detail.order_goods[index];
				if (item.can_refund_num < 1) return
				let newChecked = !item.checked;
				this.detail.order_goods[index].checked = newChecked;
				this.checkIsSelectAll();
			},
			handerAddRadioChange() {
				let {
					add_price
				} = this.detail
				if (!add_price * 1) return
				this.selectAdd = !this.selectAdd
				this.checkIsSelectAll()
			},
			async changeNum(index, mod) {
				let item = this.detail.order_goods[index];
				let num = item.apply_num + mod;
				if (num < 1) {
					this.$util.showToast({
						title: `此商品最少可退1件`
					})
					return;
				}
				if (num > item.can_refund_num) {
					this.$util.showToast({
						title: `此商品最多可退${item.can_refund_num}件`
					})
					return;
				}
				this.detail.order_goods[index].apply_num = num;
				this.countPrice();
			},
			selectAllItem() {
				this.detail.order_goods.map((item) => {
					if (item.can_refund_num < 1) return
					item.checked = !this.selectAll
				})
				this.checkIsSelectAll();
			},
			checkIsSelectAll() {
				let list = []
				this.detail.order_goods.map(item => {
					if (item.can_refund_num < 1) return
					list.push(item)
				})
				this.selectAll = list.every((item) => {
					return item.checked
				})
				this.countPrice();
			},
			countPrice() {
				let {
					can_refund_num
				} = this
				let {
					car_price,
					can_refund_car_price = 0
				} = this.detail
				let price = 0
				let num = 0
				let list = []
				this.detail.order_goods.forEach(item => {
					if (item.can_refund_num < 1) return
					list.push(item)
				})
				list.forEach((item, index) => {
					if (item.checked) {
						price += parseFloat(item.true_price) * item.apply_num;
						num += item.apply_num
					}
				})
				let total_price = price.toFixed(2) * 1
				this.total_refund_num = num
				this.total_refund_price = can_refund_car_price && can_refund_num == num ? (total_price +
					car_price * 1).toFixed(2) : total_price
			},
			// 提交申请
			async toSubmit() {
				let {
					id: order_id,
					order_goods,
				} = this.detail
				let list = []
				order_goods.filter(item => {
					if (item.checked) {
						if (item.apply_num < 1) return
						list.push({
							id: item.id,
							num: item.apply_num
						})
					}
				})
				if (list.length < 1) {
					this.$util.showToast({
						title: `请选择商品`,
					});
					return
				}
				let param = this.$util.deepCopy(this.form)
				if (!param.text) {
					this.$util.showToast({
						title: `请输入退款原因`,
					});
					return
				}
				param.imgs = param.imgs.length > 0 ? param.imgs.map(item => {
					return item.path
				}) : []
				param = Object.assign({}, param, {
					order_id,
					list
				})

				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.order.applyOrder(param)
					this.$util.hideAll()
					this.$util.showToast({
						title: `提交成功`,
					});
					this.lockTap = false
					setTimeout(() => {
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: `navigateBack`
						})
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
		}
	}
</script>


<style lang="scss">
	.user-order-refund {


		.item-child {

			.icon-xuanze,
			.icon-xuanze-fill {
				font-size: 38rpx;
			}

			.goods-img {
				width: 172rpx;
				height: 172rpx;
			}

			.goods-spe {
				height: 44rpx;
				line-height: 44rpx;
				background: #F7F7F7;
			}

			.goods-num {
				width: 200rpx;
			}


			.textarea-info {
				background: #F7F7F7;

				.input-textarea {
					width: 570rpx;
					height: 300rpx;
				}
			}


		}

		.refund-bottom-info {
			bottom: 0;

			.iconfont {
				font-size: 38rpx;
			}

			.text-right {
				height: 110rpx;

				.order {
					margin-top: 0;
					border-radius: 30rpx;
				}
			}

		}
	}
</style>
