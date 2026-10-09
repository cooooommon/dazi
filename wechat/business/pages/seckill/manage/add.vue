<template>
	<view class="seckill-add" v-if="detail.id">
		<view class="l-item radius-16 fill-base pl-lg pr-lg mt-md ml-md mr-md mt-md">
			<view class="flex-between l-item-title b-1px-b">
				<text class="f-paragraph text-bold">ID：{{detail.id}}</text>
				<text class="f-desc c-caption">{{$util.formatTime(detail.create_time * 1000)}}</text>
			</view>
			<view class="flex-center pt-lg pb-lg">
				<image :src="detail.cover" mode="aspectFill" class="l-item-image radius-16"></image>
				<view class="flex-1 l-item-box">
					<view class="f-mini-title text-bold">{{detail.name}}</view>
					<view class="flex-y-center pt-sm">
						<view class="flex-center">
							<text class="f-caption c-paragraph">年售：</text>
							<text class="f-caption">{{detail.total_sale}}</text>
						</view>
						<view class="flex-center pl-lg">
							<text class="f-caption c-paragraph">真实销量：</text>
							<text class="f-caption">{{detail.true_sale}}</text>
						</view>
					</view>
					<view class="pt-md flex-y-baseline">
						<view class="f-paragraph text-bold c-warning">¥{{detail.price | handleNumber}}</view>
						<view class="f-ms-little text-delete pl-sm c-caption">¥{{detail.init_price}}</view>
					</view>
				</view>
			</view>
		</view>
		<view class="mt-md fill-base pl-lg pr-lg pb-lg ml-md mr-md radius-16">
			<view class="flex-y-center pt-lg">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">秒杀库存</text>
			</view>
			<view class="radius-16 fill-body mt-big flex-between pr-lg">
				<input v-model="param.stock" class="pl-lg pr-lg f-mini-title h-110 flex-1" maxlength="15" type="text"
					placeholder="请输入秒杀库存" />
			</view>
			<view class="flex-y-center pt-lg">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">秒杀价格</text>
			</view>
			<view class="radius-16 fill-body mt-big flex-between pr-lg">
				<input v-model="param.price" class="pl-lg pr-lg f-mini-title h-110 flex-1" maxlength="15" type="text"
					placeholder="请输入秒杀价格" />
				<text class="f-mini-title">元</text>
			</view>
			<view class="flex-y-center pt-lg">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">活动时间</text>
			</view>
			<view class="flex-between radius-16 fill-body mt-big">
				<view class="flex-1 radius-16 fill-body flex-center h-110" @tap="showTime('showStartDate')">
					<view class="f-mini-title" :class="[{'c-icontext':!param.start_time }]">
						{{param.start_time ? param.start_time : `开始时间`}}
					</view>
				</view>
				<view class="pl-sm pr-sm"> - </view>
				<view class="flex-1 radius-16 fill-body flex-center h-110" @tap="showTime('showEndDate')">
					<view class="f-mini-title" :class="[{'c-icontext':!param.end_time }]">
						{{param.end_time ? param.end_time : `结束时间`}}
					</view>
				</view>
			</view>
			<view class="flex-y-center pt-lg">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">限购数量（个/人）</text>
			</view>
			<view class="radius-16 fill-body mt-big flex-between pr-lg">
				<input v-model="param.limit" class="pl-lg pr-lg f-mini-title h-110 flex-1" maxlength="15" type="text"
					placeholder="请输入限购数量" />
			</view>
		</view>
		
		<w-picker mode="date" :startYear="startYear" :endYear="startYear*1 + 100" :current="true"
			fields="second" @confirm="onDateConfirm($event , 'start_time')" :disabled-after="false"
			ref="term_start_time" :themeColor="primaryColor" :visible.sync="showStartDate">
		</w-picker>
		<w-picker mode="date" :startYear="startYear" :endYear="startYear*1 + 100" :value="toDay" :current="false"
			fields="second" @confirm="onDateConfirm($event , 'end_time')" :disabled-after="false" ref="term_end_time"
			:themeColor="primaryColor" :visible.sync="showEndDate">
		</w-picker>
		
		<fix-bottom-button @confirm="submit" :text="[{type:'confirm',text:'提交' }]" bgColor="#fff" borderRadius="45rpx">
		</fix-bottom-button>
		<view class="space-max-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				rule: [{
					name: "stock",
					checkType: "isNumber",
					errorMsg: "秒杀库存",
					regType: 1
				},{
					name: "price",
					checkType: "isFloatNum",
					errorMsg: "请输入秒杀价格",
					regType: 2,
					dotLen: 1
				},{
					name: "start_time",
					checkType: "isNotNull",
					errorMsg: "请设置活动时间",
					regType: 1
				},{
					name: "limit",
					checkType: "isNumber",
					errorMsg: "限购数量",
					regType: 1
				}],
				param: {
					package_id: '',
					stock: '',
					price: '',
					start_time: '',
					end_time: '',
					limit: '',
					id: ''
				},
				detail: {},
				startYear: '',
				showStartDate: false,
				showEndDate: false,
				lockTap: false,
				options: {},
				oldStartTime: '',
				oldEndTime: '',
				oldStock: 0
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
		}),
		filters: {
			handleNumber(val) {
				return val.toFixed(1)
			}
		},
		onLoad(options) {
			this.startYear = this.$util.formatTime('', 'YY')
			this.param.package_id = options.package_id
			this.options = options
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
				uni.setNavigationBarTitle({
					title: this.options.seckill_id > 0 ? '编辑秒杀活动': '添加秒杀活动'
				})
				this.getPackageInfo()
				if(this.options.seckill_id > 0){
					this.getDetail()
				}
			},
			async getPackageInfo() {
				let data = await this.$api.business.packageInfo({
					id: this.param.package_id
				})
				this.detail = data
			},
			async getDetail() {
				let data = await this.$api.business.getSeckillInfo({
					id: this.options.seckill_id
				})
				
				this.oldStartTime = data.start_time*1000
				this.oldEndTime = data.end_time*1000
				this.oldStock = data.stock
				data.start_time = this.$util.formatTime(data.start_time*1000)
				data.end_time = this.$util.formatTime(data.end_time*1000)
				
				data.price = data.price && Number(data.price).toFixed(1)
				for(let key in this.param){
					this.param[key] = data[key]
				}
			},
			//表单验证
			validate(param) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					if(name == 'start_time'){
						let {
							start_time,
							end_time
						} = param
						if(!param.id){
							if(!start_time){
								validate.add(start_time, { name: "start_time", checkType: "isNotNull", errorMsg: "请设置活动开始时间", regType: 1 });
							}if(new Date(param.start_time).getTime() < new Date().getTime()){
								validate.add('', { name: "start_time", checkType: "isNotNull", errorMsg: "活动开始时间不能小于当前时间", regType: 1 });
							} else if(!end_time){
								validate.add(end_time,{ name: "end_time", checkType: "isNotNull", errorMsg: "请设置活动结束时间", regType: 1 });
							} else if(start_time > end_time ){
								validate.add('',{ name: "end_time", checkType: "isNotNull", errorMsg: "开始时间不能大于结束时间", regType: 1 });
							}
						}else if(new Date(start_time).getTime() > this.oldStartTime || new Date(end_time).getTime() < this.oldEndTime){
							validate.add('',{ name: "end_time", checkType: "isNotNull", errorMsg: "活动时间只能延长", regType: 1 });
						}
					}else{
						validate.add(param[name], item);
					}
				})
				let message = validate.start();
				return message;
			},
			async submit() {
				let param = this.$util.deepCopy(this.param)
				let msg = this.validate(param);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				
				if(this.oldStock > param.stock && param.id){
					this.$util.showToast({
						title: '秒杀库存不能减少'
					});
					return;
				}
				
				param.start_time = new Date(param.start_time).getTime() / 1000
				param.end_time = new Date(param.end_time).getTime() / 1000
				
				this.$util.showLoading()
				if (this.lockTap) return
				this.lockTap = true
				try {
					await this.$api.business[param.id ? 'seckillEdit' : 'seckillAdd'](param)
					this.$util.hideAll()
					this.$util.showToast({
						title: param.id ? '编辑成功' : '添加成功'
					});

					setTimeout(() => {
						this.lockTap = false
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: 'navigateBack'
						})
					}, 1000)
				} catch (e) {
					this.lockTap = false
				}

			},
			showTime(type){
				this[type] = true
			},
			onDateConfirm(e, type){
				this.param[type] = e.value
			}
		}
	}
</script>

<style lang="scss">
	.seckill-add {
		.l-item-title {
			height: 83rpx;
		}

		.l-item-image {
			width: 150rpx;
			height: 150rpx;

		}

		.l-item-box {
			padding-left: 25rpx;
		}

		.mt-big {
			margin-top: 25rpx;
		}

		.h-110 {
			height: 110rpx;
		}
	}
</style>