<template>
	<view class="pages-index" v-if="isLoad">
		<!-- #ifndef H5 -->
		<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true" title="发布邀请" color="#ffffff"
			:backgroundColor="primaryColor">
			<view slot="left" class="c-base f-desc" @tap.stop="$util.goUrl({openType:'navigateBack',url:1})">取消
			</view>
		</uni-nav-bar>
		<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
		<!-- #endif -->
		<view class="box">
			<view class="box-item b-1px-b pb-lg">
				<upload @upload="imgUpload" :imagelist="param.img" imgtype="img" @del="imgUpload" text="上传照片"
					:imgsize="9"></upload>
			</view>
			<view class="box-item b-1px-b pb-lg pt-lg">
				<view class="flex">
					<textarea class="flex-1" cols="30" rows="10" placeholder="说点什么吧~" v-model="param.content"></textarea>
				</view>
				<view class="flex-y-center" @tap.stop="setAddress">
					<i class="iconfont icon-dingwei " style="color: #A4A4A4;"></i>
					<text class="f-desc c-desc" style="color: #A4A4A4;padding-left: 6rpx;">
						{{param.address || '添加地点'}}</text>
				</view>
			</view>
			<block v-if="configInfo.demand_price_check == 2">
				<uni-swipe-action ref="swipeAction">
					<!--:threshold="index"  :auto-close="false" :show="isOpened"-->
					<uni-swipe-action-item v-for="(item,index) in serveTypeList" :key="item.pid" 
					:right-options="item.options" @click="bindClick($event, index)" :disabled="serveTypeList.length == 1"
					@change="changeAction($event, index)">
						<view class="flex-between">
							<view class="flex-1">
								<view class="box-item  flex-between" v-if="item.id" @tap="$util.goUrl({url:`/find/pages/choose-serve?index=${index}`})">
									<view class="flex-y-center flex-1">
										<view class="iconbianzu-8_2x iconfont" :style="{color:primaryColor}"></view>
										<view class="f-title c-index-text pl-md flex-1">{{item.name}}</view>
									</view>
									<text class="f-title pl-md">{{item.price}}元</text>
								</view>
								<view class="" v-else>
									<view class="box-item flex-between" @tap="$util.goUrl({url:`/find/pages/choose-serve?index=${index}`})">
										<view class="flex-y-center">
											<view class="iconbianzu-8_2x iconfont" :style="{color:primaryColor}"></view>
											<view class="f-title c-index-text pl-md">服务类型</view>
										</view>
										<view class="icongengduo iconfont" style="color: #9B9B9B;font-size: 12px;"></view>
									</view>
								</view>
								<view class="box-item b-1px-b flex-between">
									<view class="flex-y-center">
										<view class="iconyaoqingrenshu iconfont" :style="{color:primaryColor}"></view>
										<view class="f-title c-index-text pl-md">邀请人数</view>
									</view>
									<view class="flex-1">
										<input @blur="hanlderNumber" class="text-right f-title" v-model="item.number" type="number" maxlength="8" placeholder="请输入" />
									</view>
								</view>
							</view>
						</view>
					</uni-swipe-action-item>
				</uni-swipe-action>
				<view class="box-item flex-between">
					<view class="flex-y-center" >
						<view class="icontianjia1 iconfont" :style="{color:primaryColor}" style="font-size: 25px;" @tap="addType"></view>
						<view class="f-desc pl-md" :style="{color:primaryColor}">向左滑动删除服务类型</view>
					</view>
				</view>
			</block>
			
			<view class="b-1px-b" v-if="configInfo.demand_price_check == 1">
				<view class="box-item flex-between" @tap="$util.goUrl({url:`/find/pages/choose-serve?id=${param.ser_id}`})">
					<view class="flex-y-center">
						<view class="iconbianzu-8_2x iconfont" :style="{color:primaryColor}"></view>
						<view class="f-title c-index-text pl-md">服务类型</view>
					</view>
					<view class="icongengduo iconfont" style="color: #9B9B9B;font-size: 12px;"></view>
				</view>
				<view class="pb-lg" v-if="serveType">
					<text class="pl-lg pr-lg pt-sm pb-sm fill-body f-caption c-text radius-10">{{serveType}}</text>
				</view>
			</view>
			
			<view class="box-item b-1px-b flex-between">
				<view class="flex-y-center">
					<view class="iconbianzu-8_2x_1_ iconfont" :style="{color:primaryColor}"></view>
					<view class="f-title c-index-text pl-md">隐藏头像和昵称</view>
				</view>
				<view @tap.stop="param.is_hide = param.is_hide ? 0 : 1">
					<view class="iconkai iconfont" v-if="param.is_hide" style="font-size: 52rpx;" :style="{color:primaryColor}"></view>
					<view class="iconguan iconfont" v-else style="color: #E5E5E5;font-size: 52rpx;"></view>
				</view>
			</view>
			<view class="box-item b-1px-b flex-between" v-if="configInfo.demand_price_check == 1">
				<view class="flex-y-center">
					<view class="iconjinrong_financebeifen-2_2x iconfont" :style="{color:primaryColor}"></view>
					<view class="f-title c-index-text pl-md">价格</view>
				</view>
				<view class="flex-1">
					<input class="text-right f-title" @blur="hanlderPrice" v-model="param.price" type="digit" maxlength="8" placeholder="请输入金额" />
				</view>
			</view>
			<view class="box-item b-1px-b flex-between" @tap="setServe(1)">
				<view class="flex-y-center">
					<view class="iconshijianguanli iconfont" :style="{color:primaryColor}"></view>
					<view class="f-title c-index-text pl-md">服务开始时间</view>
				</view>
				<view class="flex-y-center">
					<text class="f-title c-index-text pr-sm">{{startDate || '请选择'}}</text>
					<view class="icongengduo iconfont" style="color: #9B9B9B;font-size: 12px;"></view>
				</view>
			</view>
			<view class="box-item b-1px-b flex-between" @tap="setServe(2)">
				<view class="flex-y-center">
					<view class="iconshijianguanli iconfont" :style="{color:primaryColor}"></view>
					<view class="f-title c-index-text pl-md">服务结束时间</view>
				</view>
				<view class="flex-y-center">
					<text class="f-title c-index-text pr-sm">{{endDate || '请选择'}}</text>
					<view class="icongengduo iconfont" style="color: #9B9B9B;font-size: 12px;"></view>
				</view>
			</view>
			<view class="box-item b-1px-b flex-between">
				<view class="flex-y-center">
					<view class="iconbodadianhua iconfont" :style="{color:primaryColor}"></view>
					<view class="f-title c-index-text pl-md">联系电话</view>
				</view>
				<view class="flex-1">
					<input class="text-right f-title" v-model="param.phone" type="number" placeholder="请输入电话号码" />
				</view>
			</view>
			<view class="box-item b-1px-b pt-lg pb-lg">
				<view class="flex-between pt-sm">
					<view class="flex-y-center">
						<view class="iconchefei1 iconfont" :style="{color:primaryColor}"></view>
						<view class="f-title c-index-text pl-md">车费</view>
					</view>
					<view class="flex-center">
						<view class="flex-center" v-for="(item,index) in ['报销', '不报销']" :key="index" style="margin-left: 60rpx;" @tap="changeCar(index)">
							<i class="iconfont " style="font-size: 20px;" :style="{color: carIndex == index ? primaryColor : '#c7c7c7'}"
							:class="[{'icon-xuanze': carIndex != index},{'icon-xuanze-fill': carIndex == index}]"></i>
							<text class="f-title c-disable pl-md" :style="{color: carIndex == index ? primaryColor : '#c7c7c7'}">{{item}}</text>
						</view>
					</view>
				</view>
				<view class="f-desc text-right pt-md" style="color: #E82F21;" v-if="carIndex == 0">{{$t('action.attendantName')}}到达目的地后与客户自行报销</view>
			</view>
			<view class="box-item flex-between pt-lg pb-lg b-1px-b"
				v-if="configInfo.plugAuth.member && mineInfo.member_info"> 
				<view class="f-title c-title">会员折扣</view>
				<view class="flex-y-center f-paragraph c-warning">
					{{`${mineInfo.member_info.member_discount}折 (优惠¥${Number((param.price*1 - totalPrice*1).toFixed(2))})`}}
				</view>
			</view>
			<view class="">
				<blcok v-for="(item,index) in paymentList" :key="index">
					<view class="payment-item flex-between b-1px-b" @tap="param.pay_type = item.id">
						<view class="flex-y-center">
							<view class="flex-center icon-box" :class="item.class">
								<view :class="item.icon" class="iconfont icon-font-color" :style="{fontSize:item.fontSize}"></view>
							</view>
							<view class="f-desc pl-md flex-y-center">{{item.title}} <text v-if="item.id == 3">(余额{{mineInfo.balance}})</text> </view>
						</view>
						<view v-if="param.pay_type == item.id" class="iconfont icon-xuanze-fill" :style="{color:primaryColor,fontSize:'20px'}"></view>
						<view v-else class="iconfont icon-xuanze" style="font-size: 20px;color: #BEC3CE;"></view>
					</view>
				</blcok>
			</view>
		</view>
		<w-picker mode="date" :startYear="startYear" :endYear="startYear*1 + 100" :value="toDay" :current="false"
			fields="minute" @confirm="onConfirm($event , current)" :disabled-after="false" ref="day"
			:themeColor="primaryColor" :visible.sync="showDate">
		</w-picker>
		<view class="space-max-footer"></view>
		<!-- <fix-bottom-button @confirm="toFind" :text="[{type:'confirm',text:'发布' }]" bgColor="#fff"
			borderRadius="16rpx">
		</fix-bottom-button> -->
		<fixed position="bottom" :zIndex="997">
			<view class="fill-base b-1px-t pd-lg">
				<view class="flex-between pl-sm">
					<view class="flex-center">
						<text class="f-paragraph c-caption">合计：</text>
						<text class="f-paragraph c-warning">{{Number(totalPrice) || 0}}元</text>
					</view>
					<view class="flex-center">
						<view class="fixed-btn f-ms-title c-paragraph flex-center " style="border: 1px solid #979797;"
						@tap.stop="$util.goUrl({openType:'navigateBack',url:1})">取消</view>
						<view class="fixed-btn f-ms-title c-base flex-center ml-md " :style="{background:primaryColor}" @tap="toFind">发布</view>
					</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</fixed>
		<pwPopup ref="pwpopup" :text="[{type:'confirm',text:'我已知晓'}]" @confirm="submit" title="发布说明" :maskClick="true"
			content="发布内容不得涉及政治、色情、赌博等违规词语，如发布可能会被系统自动删除，删除之后，支付金额自动返回原账户。邀约订单成单后，不可退款。"></pwPopup>
		<!-- <uni-popup ref="payment" :maskClick="true" type="bottom">
			<view class="payment-list fill-base pl-lg pr-lg pt-lg">
				<blcok v-for="(item,index) in paymentList" :key="index">
					<view class="payment-item flex-between b-1px-b" @tap="param.pay_type = item.id">
						<view class="flex-y-center">
							<view :class="item.icon" class="iconfont" :style="{color:item.iconColor,fontSize:'28px'}"></view>
							<text class="f-title pl-md">{{item.title}}</text>
						</view>
						<view v-if="param.pay_type == item.id" class="iconfont icon-xuanze-fill" :style="{color:primaryColor,fontSize:'20px'}"></view>
						<view v-else class="iconfont icon-xuanze" style="font-size: 20px;"></view>
					</view>
				</blcok>
				<view class="payment-btn text-bold text-center f-title c-base ml-lg mr-lg mt-lg radius-16" 
				:style="{background:primaryColor}" @tap.stop="submit">确定</view>
				<view class="space-safe"></view>
			</view>
		</uni-popup> -->
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
				isLoad: false,
				paymentList:[
					//{id:1,title:'支付宝支付',fontSize:'44rpx',icon:'icon-alipay-fill',class: 'alipay'},
					{id:2,title:'微信支付',fontSize:'40rpx',icon:'icon-weixin',class: 'wechat'},
					{id:3,title:'账户余额',fontSize:'36rpx',icon:'iconyinhangqia',class: 'balance'}
				],
				param: {
					img: [],
					address: '',
					pay_type: 2,//支付方式1支付宝 2微信 3余额
					lat:'',
					lng:'',
					content:'',//内容
					ser_id:'',//服务类型
					is_hide:0,//是否匿名发布 1是0否
					price: '',//金额
					start_time:'',//开始时间
					end_time:'',//结束时间
					phone:'',
					ser_data: []
				},
				totalPrice: 0,
				rule: [
				// {
				// 	name: "img",
				// 	checkType: "isNotNull",
				// 	errorMsg: "请上传图片",
				// 	regText: "上传图片"
				// }, 
				{
					name: "content",
					checkType: "isNotNull",
					errorMsg: "说点什么吧~",
					regText: "内容"
				}, {
					name: "address",
					checkType: "isNotNull",
					errorMsg: "请添加地点",
					regText: "地址"
				}, {
					name: "ser_id",
					checkType: "isNotNull",
					errorMsg: "请选择服务类型",
					regText: "服务类型"
				}, {
					name: "price",
					checkType: "isMoney",
					errorMsg: "请输入金额",
					regText: "价格"
				}, {
					name: "start_time",
					checkType: "isNotNull",
					errorMsg: "请选择服务开始时间",
					regText: "服务开始时间"
				}, {
					name: "end_time",
					checkType: "isNotNull",
					errorMsg: "请选择服务结束时间",
					regText: "服务结束时间"
				}, {
					name: "phone",
					checkType: "isAllPhone",
					errorMsg: "请输入电话号码",
					regText: "电话号码"
				}],
				startYear: 2022,
				toDay: '',
				showDate: false,
				startDate: '',
				endDate: '',
				current: 1,
				serveType:'',
				lockTap:false,
				carIndex: 0,
				serveTypeList: [{pid: 1, id: '', name: '', number: 1,isOpened: 'none' ,
					options: [{
						text: '删除',
						icon: 'icon-del',
						marginLeft: `20rpx`,
						style: {
							backgroundColor: 'rgb(255,58,49)',
							fontSize: `28rpx`
						}
					}]
				}],
				isAddType: false,
				options1: [{
					text: '删除',
					icon: 'icon-del',
					marginLeft: `20rpx`,
					style: {
						backgroundColor: 'rgb(255,58,49)',
						fontSize: `28rpx`
					}
				}]
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
			location: state => state.user.location,
		}),
		async onLoad(options) {
			//this.toDay = this.$util.formatTime(new Date(), 'YY-M-D ')
			if(!this.mineInfo.id){
				await this.getMineInfo()
			}
			let {balance} = this.mineInfo
			if(balance){
				this.param.pay_type = 3
			}
			// #ifdef H5
			if ( this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			await this.getConfigInfo()
			if(this.configInfo.balance_character){
				this.paymentList.forEach(item => {
					if(item.id == 3){
						item.title = this.configInfo.balance_character
					}
				})
			}
			let {
				alipay_status = 0
			} = this.configInfo
			
			if (alipay_status) {
				// #ifndef MP-WEIXIN
				let pay = this.paymentList.findIndex(item => {
					return item.id == 1
				})
				if (pay === -1) {
					this.paymentList.splice(0, 0, {
						id: 1,
						title: '支付宝支付',
						fontSize:'44rpx',
						icon:'icon-alipay-fill',
						class: 'alipay'
					})
				}
				// #endif
			}
			this.isLoad = true
		},

		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods: {
			...mapMutations(['updateUserItem', 'updateOrderItem']),
			...mapActions([ 'getUserInfo', 'getMineInfo', 'getConfigInfo']),
			async setAddress() {
				await this.$util.checkAuth({
					type: 'userLocation'
				})
				
				let {
					lat: locaLat = '',
					lng: locaLng = ''
				} = this.location
				
				let param = {}
				if (!locaLat && !locaLng) {
					// #ifdef H5
					if (this.$jweixin.isWechat()) {
						this.$util.showLoading()
						await this.$jweixin.wxReady2();
						let {
							latitude,
							longitude
						} = await this.$jweixin.getWxLocation()
						locaLat = latitude
						locaLng = longitude
					}
					// #endif
					// #ifdef APP-PLUS
					let location = await this.$util.getLocationInfo()
					locaLat = location.lat
					locaLng = location.lng
					// #endif
				}
				
				// #ifndef MP-WEIXIN
				param = {
					latitude: locaLat,
					longitude: locaLng
				}
				// #endif
				
				let [, {
					address = '',
					longitude,
					latitude
				}] = await uni.chooseLocation(param);
				if (!address) return
				this.param.address = address
				this.param.lng = longitude
				this.param.lat = latitude
			},
			setServe(index) {
				this.current = index
				if (index == 1) {
					this.startYear = new Date().getFullYear() - 1
				} else {
					this.startYear = new Date().getFullYear()
				}
				this.showDate = true
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.param[imgtype] = imagelist;
			},
			onConfirm(e, index) {
				if (index == 1) {
					if(new Date(e.value.replace(/-/g,"/")).getTime() < new Date().getTime()){
						return this.$util.showToast({
							title: '开始时间不能小于当前时间'
						});
					}else if(this.param.end_time && this.param.end_time < new Date(e.value.replace(/-/g,"/")).getTime() / 1000){
						return this.$util.showToast({
							title: '开始时间不能大于结束时间'
						});
					}
					this.startDate = e.result
					this.param.start_time = new Date(e.value.replace(/-/g,"/")).getTime() / 1000
				}else{
					if(new Date(e.value.replace(/-/g,"/")).getTime() < new Date().getTime()){
						return this.$util.showToast({
							title: '结束时间不能小于当前时间'
						});
					}else if(this.param.start_time > new Date(e.value.replace(/-/g,"/")).getTime() / 1000){
						return this.$util.showToast({
							title: '结束时间不能小于开始时间'
						});
					}
					this.endDate = e.result
					this.param.end_time = new Date(e.value.replace(/-/g,"/")).getTime() / 1000
				}
				// this.param.start_time = new Date(e.value[0] + ' 00:00:00').getTime() / 1000
				// this.param.end_time = new Date(e.value[1] + ' 23:59:59').getTime() / 1000
				// this.initRefresh()
			},
			//表单验证
			validate(param, is_send = false) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					if (name == 'code' && is_send) return
					if(this.configInfo.demand_price_check == 2 ){
						if(name == 'price'){
							validate.add(1, item);
							return
						}
						if(name == 'ser_id'){
							let arr = []
							let is_number = false
							this.serveTypeList.forEach(item => {
								if(item.id){
									if(!/^[1-9]+[0-9]*]*$/.test(item.number)){
										is_number = true
									}
									arr.push({ser_id: item.id,num: item.number})
								}
							})
							if(arr.length == 0){
								item.errorMsg = "请选择服务类型"
								validate.add('', item);
								return
							}
							if(is_number){
								item.errorMsg = "请输入正确的邀请人数"
								validate.add('', item);
								return
							}
							this.param.ser_data = arr
						}
					}
					validate.add(param[name], item);
				})
				let message = validate.start();
				return message;
			},
			toFind(){
				let param = this.$util.deepCopy(this.param)
				let msg = this.validate(param, true);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				this.$refs.pwpopup.open()
			},
			async submit(){
				let param = this.$util.deepCopy(this.param)
				param.img = param.img.map(item => {return item.path})
				param.is_car = this.carIndex == 0 ? 1 : 0
				let  {
					demand_price_check = 1
				} = this.configInfo
				if(demand_price_check == 2){
					delete param.ser_id
					delete param.price
				}else{
					delete param.ser_data
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				setTimeout(()=>{
					this.lockTap = false
				},5000)
				let methodModel = demand_price_check == 1 ? `payDemandOrder` : `payManyDemandOrder`
				let {order_id , pay_list ,is_pay} = await this.$api.find[methodModel](param)
				// #ifdef H5
				if (param.pay_type == 1) {
					this.updateOrderItem({
						key: 'alipayOrderParams',
						val: {
							order_id ,
							page: 4,
							is_demand: demand_price_check
						}
					})
					this.$util.goUrl({
						url: `/user/pages/alipay-result`
					})
					setTimeout(() => {
						this.getMineInfo()
						this.$util.back()
					}, 3000)
					return
				}
				
				// #endif
				if(is_pay == 1){
					this.$util.hideAll()
					this.lockTap = false
					this.$util.showToast({
						title: '发布成功'
					});
					this.$util.back()
					setTimeout(()=>{
						this.$util.goUrl({url:1,openType: "navigateBack"})
					},1000)
				}else if(pay_list){
					try{
						if(param.pay_type == 1){
							await this.$util.alipayPay(pay_list)
						}else if(param.pay_type == 2){
							await this.$util.pay(pay_list)
						}
						this.$util.hideAll()
						this.lockTap = false
						this.$util.showToast({
							title: '发布成功'
						});
						// this.$refs.pwpopup.close()
						// this.$refs.payment.close()
						this.$util.back()
						setTimeout(()=>{
							this.$util.goUrl({url:1,openType: "navigateBack"})
						},1000)
					}catch(e){
						setTimeout(() => {
							this.lockTap = false
							this.$util.hideAll()
						}, 2000)
					}
				}
			},
			changeCar(index){
				this.carIndex = index
			},
			addType(){
				let pid = this.serveTypeList.length *1 + 1
				this.serveTypeList.push({pid , id: '', name: '', number: 1, isOpened: 'none', options: this.options1})
			},
			bindClick(e,index){
				uni.showModal({
					title: '提示',
					content: '是否删除',
					success: res => {
						if (res.confirm) {
							this.$refs.swipeAction.closeAll()
							this.serveTypeList.splice(index, 1)
							let totalPrice = 0
							let oldPrice = 0
							let discount = 1
							if(this.configInfo.plugAuth.member && this.mineInfo.member_info){
								discount = this.mineInfo.member_info.member_discount / 10
							}
							this.serveTypeList.forEach(item => {
								oldPrice += (item.price ? Number((Number(item.price)*(item.number || 1)).toFixed(2)) : 0)
								totalPrice += (item.price ? Number((Number(item.price)*(item.number || 1)*discount).toFixed(2)) : 0)
							})
							this.param.price = Number(oldPrice.toFixed(2))
							this.totalPrice = totalPrice
						} else if (res.cancel) {
						}
					}
				});
			},
			changeAction(e, index) {
				// this.serveTypeList[index].isOpened = e
			},
			hanlderNumber(){
				let totalPrice = 0
				let oldPrice = 0
				let discount = 1
				if(this.configInfo.plugAuth.member && this.mineInfo.member_info){
					discount = this.mineInfo.member_info.member_discount / 10
				}
				this.serveTypeList.forEach(item => {
					oldPrice += (item.price ? Number((Number(item.price)*(item.number || 1)).toFixed(2)) : 0)
					totalPrice += (item.price ? Number((Number(item.price)*(item.number || 1)*discount).toFixed(2)) : 0)
				})
				this.totalPrice = totalPrice
				this.param.price = oldPrice
			},
			hanlderPrice(){
				let discount = 1
				if(this.configInfo.plugAuth.member && this.mineInfo.member_info){
					discount = this.mineInfo.member_info.member_discount / 10
				}
				this.totalPrice = (this.param.price * discount).toFixed(2)
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff;
	}

	.pages-index {
		.box {
			padding: 30rpx 30rpx;

			.box-item {
				min-height: 123rpx;
			}
		}
		.payment-item{
			height: 162rpx;
			line-height: 162rpx;
			.icon-box{
				width: 70rpx;
				height: 70rpx;
				border-radius: 70rpx;
			}
			.alipay{
				background-color: #E9F5FE;
				.iconfont{
					background-image: linear-gradient(180deg, #46BCFF 0%, #2587FF 100%)
				}
			}
			.wechat{
				background-color: #E7F9EE;
				.iconfont{
					background-image: linear-gradient(180deg, #19C865 0%, #19C865 100%)
				}
			}
			.balance{
				background-color: #FDF3EB;
				.iconfont{
					background-image: linear-gradient(180deg, #FFC25F 0%, #FE793D 100%)
				}
			}
		}
		.payment-btn{
			height: 88rpx;
			line-height: 88rpx;
		}
		
		.fixed-btn{
			width: 180rpx;
			height: 90rpx;
			border-radius: 90rpx;
		}
	}
</style>
