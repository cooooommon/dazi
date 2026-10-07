<template>
	<view class="technician-income-cash-out" v-if="isLoad">

		<view class="mt-md ml-lg mr-lg pd-lg f-paragraph fill-base radius-16">
			<view class="f-title c-title text-bold">提现账户</view>
			<view @tap.stop="toChangeItem(index)" class="flex-center mt-lg" v-for="(item,index) in payList"
				:key="index">
				<i class="pay-icon iconfont c-caption"
					:class="[{'icon-xuanze':payInd != index},{'icon-radio-fill':payInd == index}]"
					:style="{color:payInd==index?primaryColor:'#BEC3CE'}"></i>
				<view class="flex-1 ml-lg f-mini-title c-title">
					<view class="flex-y-center">
						<view>{{item.title}} </view>
						<view class="flex-1 flex-between ml-md" v-if="item.id == 2">
							<view class="f-desc c-paragraph">
								{{userInfo.alipay_number ? userInfo.split_alipay_number : '暂无'}}
							</view>
							<view @tap.stop="$util.goUrl({url:`/user/pages/alipay-account`})"
								:style="{color:primaryColor}">{{userInfo.alipay_number?'修改':'去设置'}}
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="mt-md ml-lg mr-lg f-paragraph fill-base radius-16" style="overflow: hidden;">
			<view class="fill-space pd-lg b-1px-b">
				<view class="flex-between pt-md pb-md">
					<view class="f-title c-title text-bold">备注</view>
					<input v-model="text" class="flex-1 f-paragraph ml-md" type="text" placeholder="请输入到账信息" />
				</view>
			</view>
			<view class="pd-lg">
				<view class="f-title c-title text-bold">提现金额</view>
				<view class="flex-y-center pt-lg pb-md b-1px-b">
					<view class="text-bold">¥</view>
					<input v-on:input="checkInput($event,'apply_price')" v-model="apply_price"
						class="flex-1 f-paragraph ml-sm" type="digit" placeholder="请输入提现金额" />
				</view>
				<view @tap.stop="withAll" class="flex-y-center f-caption c-caption pt-md">可提现{{configInfo.balance_character||`余额`}}￥{{cash}}
					<view class="ml-md c-warning">全部提现
					</view>
				</view>
				<view class="flex-y-center f-caption c-caption pt-sm">最低提现额度<view class="ml-md c-warning">
						￥{{configInfo.cash_mini || 0}}
					</view>
				</view>
				<view class="space-lg"></view>
				<view class="space-lg"></view>
				<view @tap.stop="submit" class="cash-out-btn flex-center f-title c-base radius-16"
					:style="{background:primaryColor}">提现</view>
				<!-- <view class="flex-center f-caption c-caption mt-md" v-if="options.type == 'technician'">
					每笔订单收取{{balance}}%手续费</view> -->
			</view>
		</view>
		<view class="pd-lg">
			<view class="flex-between pb-md f-title c-title text-bold">提现须知</view>
			<view class="pre-wrap f-desc c-caption">{{configInfo.withdrawal_notice}}</view>
		</view>
		<view class="space-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions
	} from "vuex"
	export default {
		components: {},
		data() {
			return {
				options: {},
				isLoad: false,
				payList: [],
				payInd: 0,
				detail: {},
				balance: '',
				cash: '',
				apply_price: '',
				text: '',
				type: 1,
				lockTap: false,
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			coachInfo: state => state.user.coachInfo,
		}),
		async onLoad(options) {
			this.$util.showLoading()
			this.options = options
			await this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'getCoachInfo']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await Promise.all([this.getConfigInfo(), this.getUserInfo()])
				let {
					wechat_transfer,
					alipay_transfer,
					under_transfer
				} = this.configInfo
				let arr = []
				if (wechat_transfer == 1) {
					arr.push({
						id: 1,
						title: '微信零钱包'
					})
				}
				// #ifndef MP-WEIXIN
				if (alipay_transfer == 1) {
					arr.push({
						id: 2,
						title: '支付宝账户'
					})
				}
				// #endif
				if (under_transfer == 1) {
					arr.push({
						id: 0,
						title: '线下转账'
					})
				}
				this.payList = arr
				let {
					type
				} = this.options
				// type technician 向导；carfee 车费；distribution 分销商
				switch (type) {
					case 'technician':
						let {
							detail
						} = await this.$util.getPage(-1)
						let {
							cap_cash = 0
						} = detail
						let {
							balance = 0
						} = detail.coach_level
						this.balance = 100 - balance
						this.cash = cap_cash
						break
					case 'carfee':
						let {
							id: cid = 0
						} = this.coachInfo
						if (!cid) {
							await this.getCoachInfo()
						}
						this.cash = this.coachInfo.car_price
						break
					case 'distribution':
						let {
							new_cash = 0
						} = await this.$util.getPage(-1).detail
						this.cash = new_cash
						break
					case 'channel':
						let {
							cash = 0
						} = await this.$util.getPage(-1).detail
						this.cash = cash
						break
					case 'business':
						let {
							package_cash = 0
						} = await this.$util.getPage(-1).detail
						this.cash = package_cash
						break
					case 'store':
						let {
							cash: store_cash = 0
						} = await this.$util.getPage(-1).detail
						this.cash = store_cash
						break
					case 'broker':
						let {
							cash: broker_cash = 0
						} = await this.$util.getPage(-1).detail
						this.cash = broker_cash
						break
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.$util.hideAll()
				this.isLoad = true
			},
			initRefresh() {
				this.initIndex(true)
			},
			toChangeItem(val) {
				this.payInd = val
				this.lockTap = false
			},
			withAll() {
				this.apply_price = this.cash
			},
			checkInput(e, key) {
				let val = this.$util.formatMoney(e.detail.value)
				this.$nextTick(() => {
					this[key] = val
				})
			},
			async submit() {
				if (this.apply_price == '0.0') {
					this.$nextTick(() => {
						this.apply_price = '0'
					})
				}
				let {
					cash,
					apply_price,
					text,
					payList,
					payInd
				} = this
				let {
					alipay_number = '',
						alipay_name = ''
				} = this.userInfo

				let {
					id: apply_transfer
				} = payList[payInd]
				if (apply_transfer == 2 && (!alipay_number || !alipay_name)) {
					this.$util.showToast({
						title: !alipay_number ? `请先绑定支付宝账户` : `请先绑定支付宝账户姓名`
					})
					return
				}
				let {
					cash_mini
				} = this.configInfo
				text = text ? text.replace(/(^\s*)|(\s*$)/g, "") : ''
				if ((apply_transfer == 0 && !text) || !apply_price) {
					this.$util.showToast({
						title: apply_transfer == 0 && !text ? `请输入到账信息` : `请输入提现金额`
					})
					return
				}
				if (parseFloat(apply_price) < parseFloat(cash_mini)) {
					this.$util.showToast({
						title: `提现金额不能低于最低提现金额${cash_mini}`
					})
					return
				}
				if (parseFloat(apply_price) > parseFloat(cash)) {
					this.$util.showToast({
						title: `提现金额不能大于可提现金额${cash}`
					})
					return
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					let {
						type
					} = this.options
					// let methodModel = type == 'distribution' ? 'mine' : 'technician'
					let methodModel = {
						technician: 'technician',
						carfee: 'technician',
						distribution: 'mine',
						channel: 'channel',
						agent: 'agent',
						salesman: 'salesman',
						business: 'business',
						store: 'business',
						broker: 'mine'
					}
					let param = {
						apply_price,
						text,
						apply_transfer
					}
					// if (type != 'distribution') {
					// 	param.type = type == 'technician' ? 1 : 2
					// }
					if (type == 'technician' || type === 'carfee') {
						param.type = type == 'technician' ? 1 : 2
					}
					let methodName = 'applyWallet'
					if(type == 'store'){ // 门店
						methodName = 'storeApplyWallet'
					}else if(type == 'broker'){ //经纪人提现
						methodName = 'brokerApplyWallet'
					}
					await this.$api[methodModel[type]][methodName](param)
					this.$util.hideAll()
					this.$util.showToast({
						title: `提交成功`,
					});
					setTimeout(() => {
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: 'navigateBack'
						})
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			}
		}
	}
</script>


<style lang="scss">
	.technician-income-cash-out {
		.cash-out-btn {
			width: 622rpx;
			height: 88rpx;
			margin: 0 auto;
		}
	}
</style>
