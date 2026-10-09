<template>
	<view class="order-pages" v-if="detail.id">
		<!-- 开始服务倒计时 -->
		<view class="fix" style="top:-100%;left: -100%;" v-if="detail.pay_type==6">
			<min-countdown :targetTime="detail.start_service_time_unix * 1000" :isPlay="true"></min-countdown>
		</view>

		<view class="item-child  pd-lg fill-base f-paragraph c-base" :style="{background:primaryColor}">
			<view class="text-bold">{{statusType[detail.pay_type]}}</view>
			<view class="f-caption mt-sm" v-if="detail.pay_type == 1 && detail.end_time > 0">请在<min-countdown
					:targetTime="over_time_text" @callback="countEnd"></min-countdown>内完成支付，逾期未支付，订单将自动取消</view>
			<view class="space-lg"></view>
		</view>

		<!-- // pay_type 1待支付，2待服务，3向导接单，4向导出发，5向导到达，6服务中，7服务完成，8待评价 -->
		<view
			class="menu-list flex-warp rel ml-lg mr-lg pt-lg pb-lg pl-md pr-md fill-base f-paragraph c-caption radius-16"
			:class="[{'add-bell':detail.is_add || detail.store_id}]">
			<view class="menu-line abs b-1px-b"></view>
			<block v-for="(item,index) in lineList" :key="index">
				<view class="item-child flex-column f-icontext c-paragraph"
					:style="{color:detail.pay_type > item.pay_type -1?primaryColor:''}" v-if="item.icon">
					<view class="item-img fill-base flex-center radius" style="margin: 0 auto;"
						:style="{borderColor:detail.pay_type > item.pay_type -1?primaryColor:''}">
						<i class="iconfont" :class="item.icon"></i>
					</view>
					<view class="pt-sm" style="margin: 0 auto;">{{item.title}}</view>
				</view>
			</block>
		</view>
		
		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16 flex-y-center">
			<text class="pr-md f-paragraph">服务还有</text>
			<min-countdown :targetTime="detail.end_service_time * 1000" @callback="timeCountEnd" :isPlay="true" :type="4" style="height: 46rpx;" :refresh="detail.end_service_time"></min-countdown>
			<text class="pl-md">结束</text>
		</view>


		<view class="item-child mt-md ml-lg mr-lg pd-lg fill-base radius-16">
			<view class="flex-between pb-lg">
				<view class="f-paragraph c-title max-380 ellipsis">服务内容</view>
			</view>
			<view class="flex-center" :class="[{'mb-lg':aindex != detail.order_goods.length -1}]"
				v-for="(aitem,aindex) in detail.order_goods" :key="aindex">
				<!-- #ifdef H5 -->
				<view class="avatar lg radius-16">
					<view class="h5-image avatar lg radius-16"
						:style="{ backgroundImage : `url('${aitem.goods_cover}')`}">
					</view>
				</view>
				<!-- #endif -->
				<!-- #ifndef H5 -->
				<image mode="aspectFill" class="avatar lg radius-16" :src="aitem.goods_cover"></image>
				<!-- #endif -->
				<view class="flex-1 ml-md">
					<view class="flex-between">
						<view class="f-mini-title c-title text-bold ellipsis"
							:class="[{'max-300':aitem.refund_num>0},{'max-450':aitem.refund_num == 0}]">
							{{aitem.goods_name}}
						</view>
						<view class="f-caption c-warning" v-if="aitem.refund_num>0">已退x{{aitem.refund_num}}</view>
					</view>
					<view class="f-caption c-caption mt-md">服务时长：{{aitem.time_long}}分钟</view>
					<view class="flex-between">
						<view class="flex-y-baseline f-caption c-warning">¥<view class="f-title text-bold">
								{{aitem.price}}
							</view>
						</view>
						<view class="c-paragraph">x{{aitem.num}}</view>
					</view>
				</view>
			</view>
		</view>

		<view class="store-info mt-md ml-lg mr-lg pd-lg  fill-base radius-16" v-if="detail.store_id">
			<view class="f-mini-title c-title text-bold pb-md">
				{{detail.store_info.title}}
			</view>
			<view class="flex-between">
				<view class="flex-y-center" style="color: #303030;">
					<i class="iconfont icondizhi1 mr-sm"></i>
					<view class="c-title flex-1 mr-md">
						<span>{{detail.store_info.address || `暂未设置门店地址`}}</span>
						<span @tap.stop="$util.goUrl({url:detail.store_info.address,openType:'copy'})"
							class="store-copy-btn radius-5 f-icontext ml-sm"
							:style="{color:primaryColor,borderColor:primaryColor}"
							v-if="detail.store_info.address">复制</span>
					</view>
				</view>
				<view class="flex-center">
					<view @tap.stop="$util.goUrl({url:detail.store_info.phone,openType:'call'})"
						class="item-icon rel flex-center radius-16">
						<view class="item-icon radius-16 abs" :style="{background:primaryColor}"></view>
						<i class="iconfont icondadianhua_1" :style="{color:primaryColor}"></i>
					</view>
					<view @tap.stop="toMap('store_info')" class="item-icon rel flex-center radius-16 ml-md"
						v-if="detail.store_info.address">
						<view class="item-icon radius-16 abs" :style="{background:primaryColor}"></view>
						<i class="iconfont icondizhi_1" :style="{color:primaryColor}"></i>
					</view>
				</view>
			</view>
		</view>


		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class="flex-between">
				<view>下单人</view>
				<view class="flex-y-center">
					<view class="c-title max-350 ellipsis">{{detail.address_info.user_name}}</view>
					<view @tap.stop="toLabel" class="ml-sm" :style="{color:primaryColor}">查看标签
					</view>
				</view>
			</view>
			<view @tap="toTel" class="flex-between mt-md">
				<view>联系方式</view>
				<view class="flex-y-center" :style="{color:primaryColor}">
					<i class="iconfont iconbodadianhua mr-sm"></i>
					<view>拨打电话</view>
				</view>
			</view>
			<view class="mt-md" v-if="!detail.store_id">
				<view>服务地址</view>
				<view class="flex-center">
					<view class="c-title mt-sm flex-1 mr-md">
						<span>{{`${detail.address_info.address}${detail.address_info.address_info}`}}</span>
						<span @tap="toCopy" class="copy-btn radius-5 f-icontext ml-sm">复制</span>
					</view>
					<view @tap="toMap('address_info')" class="flex-center flex-column f-icontext"
						:style="{color:primaryColor}">
						<i class="iconfont icon-dingwei" style="font-size: 42rpx;"></i>
						<view>立刻导航去</view>
					</view>
				</view>
			</view>
			<view class="mt-md" v-if="detail.text">
				<view>订单备注</view>
				<view class="c-title mt-sm">{{detail.text}}</view>
			</view>
		</view>

		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class="flex-between mt-md">
				<view>下单时间</view>
				<view class="c-title">{{detail.create_time}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>服务时间</view>
				<view class="c-title">{{detail.start_time}}</view>
			</view>
			<view class="flex-between mt-md">
				<view>服务时长</view>
				<view class="c-title">{{detail.time_long}}分钟</view>
			</view>
			<block v-if="!detail.is_add && !detail.store_id">
				<view class="flex-between mt-md">
					<view>车费详情</view>
					<view class="flex-y-center c-title">{{carType[detail.car_type]}}
						<view class="ml-md" v-if="detail.car_type == 1">全程{{detail.distance}}</view>
					</view>
				</view>
				<view class="flex-between mt-md" v-if="detail.car_type == 1">
					<view>出行费用</view>
					<view class="c-warning">出租车 ¥{{detail.car_price}}</view>
				</view>
			</block>
			<view class="flex-between mt-md">
				<view>服务项目费用</view>
				<view class="c-warning">¥{{detail.init_service_price}}</view>
			</view>
			<view class="flex-between mt-md" v-if="detail.discount*1 > 0">
				<view>卡券优惠</view>
				<view class="c-warning">-¥{{detail.discount}}</view>
			</view>
			<view class="flex-between mt-md" v-if="detail.member_status">
				<view>会员卡折扣</view>
				<view class="c-warning">{{detail.member_balance}}折 (优惠¥{{detail.member_discount}})</view>
			</view>
			<view class="flex-between mt-md">
				<view>支付方式</view>
				<view class="c-title">{{payType[detail.pay_model]}}</view>
			</view>
			<view class="flex-between mt-md pt-md b-1px-t">
				<view></view>
				<view class="flex-y-baseline c-title">总计：<view class="c-warning">¥{{detail.pay_price}}</view>
				</view>
			</view>
		</view>

		<view class="mt-md ml-lg mr-lg pd-lg fill-base f-paragraph c-caption radius-16">
			<view class=" flex-y-center pb-lg flex-warp">
				<view class="flex-between c-title">订单编号：</view>
				<view class="flex-between flex-1 ">
					<view class="c-title">{{detail.order_code}}</view>
					<view class="f-icontext d-copy text-center"
						@tap.stop="$util.goUrl({openType:'copy',url:detail.order_code})"
						:style="{borderColor:primaryColor ,color:primaryColor}">复制</view>
				</view>
			</view>
			<timeline :list="lineList" :info="detail"></timeline>
		</view>

		<view class="space-max-footer"></view>
		<view class="footer-info fix fill-base"
			v-if="statusPayType.includes(detail.pay_type) || (detail.pay_type == 7 && !detail.is_add && !detail.label_time)">
			<view class="flex-between pd-lg">
				<view></view>
				<view class="flex-center f-desc c-title">
					<!-- // pay_type 2待接单，5待服务，6服务中，7服务完成 -->
					<!-- 待接单 -->
					<block v-if="detail.pay_type == 2">
						<button @tap.stop="toTel" class="clear-btn order">联系客户</button>
						<button @tap.stop="toRefuse" class="clear-btn order">拒绝接单</button>
						<button @tap.stop="toConfirm(3)" class="clear-btn order"
							:style="{color:'#fff',background:primaryColor,borderColor:primaryColor}">确认接单</button>
					</block>
					<!-- 待服务 -->
					<block v-if="[3,4,5].includes(detail.pay_type)">
						<button @tap.stop="toTel" class="clear-btn order">联系客户</button>
						<button @tap.stop="toConfirm(detail.is_add || detail.store_id  ? 6 : detail.pay_type*1+1 )"
							class="clear-btn order"
							:style="{color:'#fff',background:primaryColor,borderColor:primaryColor}">{{detail.is_add || detail.store_id ? '开始服务' : detail.pay_type == 3 ? '已出发': detail.pay_type == 4 ?'拍照确认到达' : '开始服务'}}</button>
					</block>
					<!-- 服务中 -->
					<block v-if="detail.pay_type == 6">
						<button @tap.stop="toTel" class="clear-btn order">联系客户</button>
						<button @tap.stop="toConfirm(7)" class="clear-btn order"
							:style="{color:'#fff',background:primaryColor,borderColor:primaryColor}">拍照完成服务</button>
					</block>
					<!-- 已完成 -->
					<block v-if="detail.pay_type == 7">
						<button
							@tap.stop="$util.goUrl({url:`/technician/pages/order/label?id=${detail.id}&uid=${detail.user_id}`})"
							class="clear-btn order"
							:style="{color:'#fff',background:primaryColor,borderColor:primaryColor}">评价客户</button>

					</block>
				</view>
			</view>
			<view class="space-safe"></view>
		</view>

		<uni-popup ref="label_item" type="bottom" :custom="true">
			<view class="popup-label fill-base pd-lg rel">
				<i @tap.stop="$refs.label_item.close()" class="iconfont iconguanbi abs"></i>
				<view class="pb-md flex-center f-title c-title text-bold">客户标签</view>
				<view class="space-lg"></view>
				<view class="space-lg"></view>
				<scroll-view scroll-y style="width: 100%;max-height:50vh;">
					<view class="list-item f-caption">
						<view class="pb-lg f-paragraph" v-if="check_label.length==0">暂无评价标签</view>
						<view class="list-child cur radius rel" v-for="(item,index) in check_label" :key="index">
							<view class="bg-item radius abs"
								:style="{background:primaryColor,borderColor:primaryColor}">
							</view>
							<view class="tag-item flex-center" :style="{color:primaryColor}">{{item.title}}</view>
						</view>
					</view>
				</scroll-view>
				<view class="space-safe"></view>
			</view>
		</uni-popup>

		<uni-popup ref="refuse_item" type="center" :custom="true">
			<view class="common-popup-content fill-base pd-lg radius-34">
				<view class="title">拒绝接单</view>
				<view class="desc">请确认是否拒接接单</view>
				<textarea v-model="coach_refund_text" class="pd-lg textarea f-desc c-title mt-lg radius-20"
					maxlength="200" placeholder="请输入拒单原因" placeholder-class="f-desc c-caption"></textarea>
				<view class="flex-center mt-md" style="width: 540rpx">
					<view class="flex-1"></view>
					<view>{{coach_refund_text.length>200?200:coach_refund_text.length}}/200</view>
				</view>
				<view class="button">
					<view @tap.stop="$refs.refuse_item.close()" class="item-child">取消</view>
					<view @tap.stop="confirmRefuse" class="item-child c-base"
						:style="{background: primaryColor,color:'#fff'}">确定</view>
				</view>
			</view>
		</uni-popup>



		<uni-popup ref="oper_item" type="center" :custom="true">
			<view class="common-popup-content fill-base pd-lg radius-34">
				<view class="title">{{popupInfo.title}}</view>
				<view class="space-md"></view>
				<view class="flex-warp mt-md" style="width: 100%;" v-if="popupInfo.type !== 4">
					<view>拍照打卡：</view>
					<view class="flex-1">
						<upload @upload="imgUpload" @del="imgUpload" :imagelist="popupInfo.imgs" filetype="picture"
							imgtype="imgs" text="拍摄图片" :imgsize="1" :sourceType="2">
						</upload>
					</view>
				</view>
				<view class="flex-warp mt-md" style="width: 100%;">
					<view>当前地点：</view>
					<view @tap.stop="toGetLocation" class="flex-1"
						:class="[{'c-title':popupInfo.location.address},{'c-caption':!popupInfo.location.address}]">
						{{popupInfo.location.address || '点击获取定位'}}
					</view>
				</view>
				<view class="button">
					<view @tap.stop="$refs.oper_item.close()" class="item-child">取消</view>
					<view @tap.stop="confirmOper" class="item-child c-base"
						:style="{background: primaryColor,color:'#fff'}">确定</view>
				</view>
			</view>
		</uni-popup>

	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import timeline from '@/components/timeline.vue'
	export default {
		components: {
			timeline
		},
		data() {
			return {
				options: {},
				statusPayType: [2, 3, 4, 5, 6],
				statusType: {
					'-1': '已拒单',
					2: '待接单',
					3: '已接单',
					4: '已出发',
					5: '已到达',
					6: '服务中',
					7: '已完成',
				},
				carType: {
					0: '公交/地铁',
					1: '出租车'
				},
				payType: {
					1: '微信支付',
					2: '余额支付',
					3: '支付宝支付'
				},
				lineList: [],
				base_service: [{
					pay_type: 3,
					title: this.$t('action.orderTaking'),
					time: 'receiving_time',
					icon: 'iconjishijiedan'
				}, {
					pay_type: 4,
					title: this.$t('action.setOut'),
					time: 'serout_time',
					icon: 'iconjishichufa'
				}, {
					pay_type: 5,
					title: this.$t('action.arrive'),
					time: 'arrive_time',
					icon: 'iconjishidaoda'
				}, {
					pay_type: 6,
					title: '开始服务',
					time: 'start_service_time',
					icon: 'iconjishifuwu'
				}, {
					pay_type: 7,
					title: '服务完成',
					time: 'order_end_time',
					icon: 'iconjishiwancheng'
				}, {
					pay_type: 7,
					title: '签字确认',
					time: 'sign_time',
					icon: ''
				}],
				base_bell: [{
					pay_type: 3,
					title: this.$t('action.orderTaking'),
					time: 'receiving_time',
					icon: 'iconjishijiedan'
				}, {
					pay_type: 6,
					title: '开始服务',
					time: 'start_service_time',
					icon: 'iconjishifuwu'
				}, {
					pay_type: 7,
					title: '服务完成',
					time: 'order_end_time',
					icon: 'iconjishiwancheng'
				}],
				detail: {
					pay_type: 0
				},
				check_label: [],
				coach_refund_text: '',
				lockTap: false,
				popupInfo: {
					title: '',
					type: '',
					param: {},
					imgs: [],
					location: {
						lat: 0,
						lng: 0,
						address: ''
					}
				},
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			over_time_text() {
				return new Date().getTime() + this.detail.end_time * 1000
			}
		}),
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getCoachInfo']),
			...mapMutations(['']),
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
				if(this.configInfo.balance_character){
					this.payType[2] = this.configInfo.balance_character
				}
				let {
					id
				} = this.options
				let data = await this.$api.technician.orderInfo({
					id
				})
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				data.is_balance = data.balance * 1 > 0 ? 1 : 0
				let {
					pay_type,
					time_long,
					start_service_time,
					is_add = 0,
					store_id = 0
				} = data
				if (pay_type == 6) {
					data.start_service_time_unix = this.$util.DateToUnix(start_service_time) + time_long * 60
				}
				let lineList = this.$util.deepCopy(is_add || store_id ? this.base_bell : this.base_service)
				if (store_id) {
					lineList.push({
						pay_type: 7,
						title: '签字确认',
						time: 'sign_time',
						icon: ''
					})
				}
				this.lineList = lineList
				this.detail = data
			},
			initRefresh() {
				this.initIndex(true)
			},
			countEnd() {
				this.$util.log("倒计时完了")
				setTimeout(() => {
					this.initRefresh()
					this.$util.back()
				}, 1000)
			},
			timeCountEnd(){
				this.$util.log("倒计时完了")
				setTimeout(() => {
					if(this.detail.pay_type == 7){
						this.detail.end_service_time = -1
					}
					this.$util.back()
				}, 1000)
			},
			async toLabel() {
				let {
					user_id
				} = this.detail
				this.check_label = await this.$api.technician.userLabelList({
					user_id
				})
				this.$refs.label_item.open()
			},
			// 拒绝接单
			async toRefuse() {
				this.coach_refund_text = ''
				this.$refs.refuse_item.open()
			},
			// 确认：拒绝接单
			async confirmRefuse() {
				let {
					coach_refund_text
				} = this
				coach_refund_text = coach_refund_text.length > 0 ? coach_refund_text.replace(/(^\s*)|(\s*$)/g, "") : ''
				if (coach_refund_text.length == 0) {
					this.$util.showToast({
						title: `请输入拒单原因`
					})
					return
				}
				this.coach_refund_text = this.coach_refund_text.substring(0, 200)
				this.toConfirm(-1)
			},
			// 获取定位
			async toGetLocation() {
				let {
					lat: loca_lat = 0,
					lng: loca_lng = 0,
					address: loca_addr = ''
				} = this.popupInfo
				if (loca_lat && loca_lng && loca_addr) return
				// #ifdef H5
				if (this.$jweixin.isWechat()) {
					this.$util.showLoading()
					await this.$jweixin.wxReady2();
					let {
						latitude: lat = 0,
						longitude: lng = 0
					} = await this.$jweixin.getWxLocation()
					if (!lat) {
						this.$util.hideAll()
						this.$util.showToast({
							title: `请授权定位当前地址`
						})
						return
					}
					if (lat && lng) {
						
						let {
							address
						} = await this.$util.getMapInfo(false, {
							lat,
							lng
						})
						
						this.popupInfo.location = {
							lat,
							lng,
							address
						}
						this.$util.hideAll()
						
						// let key = `${lat},${lng}`
						// let data = await this.$api.base.getMapInfo({
						// 	location: key
						// })
						// let {
						// 	status,
						// 	result
						// } = JSON.parse(data)
						// if (status == 0) {
						// 	this.$util.hideAll()
						// 	let {
						// 		standard_address
						// 	} = result.formatted_addresses
						// 	let address = standard_address
						// 	this.popupInfo.location = {
						// 		lat,
						// 		lng,
						// 		address
						// 	}
						// }
					}
				}
				// #endif
				// #ifndef H5
				this.$util.showLoading()
				let {
					lat = '',
						lng = '',
						address = ''
				} = await this.$util.getBmapLocation()
				if (!lat) {
					this.$util.hideAll()
					this.$util.showToast({
						title: `请授权定位当前地址`
					})
					return
				}
				this.popupInfo.location = {
					lat,
					lng,
					address
				}
				this.$util.hideAll()
				// #endif
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.popupInfo[imgtype] = imagelist;
			},
			// 确认： 4已出发/5已到达/7服务完成
			async confirmOper() {
				let popupInfo = this.$util.deepCopy(this.popupInfo)
				let {
					type,
					param,
					imgs,
					location
				} = popupInfo
				let {
					lat = 0, lng = 0, address = ''
				} = location
				if ([5, 7].includes(type) && imgs.length === 0) {
					this.$util.showToast({
						title: `请先拍照`
					})
					return
				}
				if (!lat && !lng && !address) {
					this.$util.showToast({
						title: `请先获取当前定位地址`
					})
					return
				}
				if ([5, 7].includes(type)) {
					let {
						path
					} = imgs[0]
					location.path = path
				}
				this.toConfirmUpdate(type, param, location)
			},
			// type: -1拒绝接单，3确定接单，4已出发，5已到达，6开始服务，7完成服务
			async toConfirm(type) {
				let {
					id: order_id,
					end_service_time
				} = this.detail
				let param = {
					order_id,
					type
				}
				if (type == -1) {
					param.coach_refund_text = this.coach_refund_text
				}
				if(type == 7){ // 当倒计时未清零时
					if(end_service_time * 1000 > new Date().getTime()){
						 this.$util.showToast({
							title: '服务时间未结束，不能提前完成服务'
						})
						return
					}
				}

				if ([4, 5, 7].includes(type)) {
					let arr = {
						4: '确认出发',
						5: '确认到达',
						7: '确认完成',
					}
					let title = arr[type]
					this.popupInfo = Object.assign({}, this.popupInfo, {
						title,
						type,
						param,
						imgs: [],
						location: {
							lat: 0,
							lng: 0,
							address: ''
						}
					})
					// #ifndef MP-WEIXIN
					await this.toGetLocation()
					// #endif
					this.$refs.oper_item.open()
				} else {
					this.toConfirmUpdate(type, param)
				}
			},
			async toConfirmUpdate(type, param, location = {}) {
				if ([4, 5, 7].includes(type)) {
					let {
						lat,
						lng,
						address,
						path
					} = location
					if (type === 4) {
						param.serout_lat = lat
						param.serout_lng = lng
						param.serout_address = address
					}
					if (type == 5) {
						param.arrive_img = path
						param.arr_lat = lat
						param.arr_lng = lng
						param.arr_address = address
					}
					if (type == 7) {
						param.end_img = path
						param.end_lat = lat
						param.end_lng = lng
						param.end_address = address
					}
				}
				let msg = {
					'-1': '已拒绝接单',
					3: '接单成功',
					4: '已成功出发',
					5: '已成功到达',
					6: '已开始服务',
					7: '服务已完成'
				}
				if (this.lockTap) return;
				this.lockTap = true;
				this.$util.showLoading()
				try {
					await this.$api.technician.updateOrder(param)
					if (type == -1) {
						this.$refs.refuse_item.close()
					}
					if ([4, 5, 7].includes(type)) {
						this.$refs.oper_item.close()
					}
					if (type == 7) {
						await this.getCoachInfo()
					}
					this.$util.hideAll()
					this.$util.showToast({
						title: msg[type]
					})
					this.lockTap = false;
					this.initRefresh()
					this.$util.back()
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
					return
				}
			},
			// 咨询
			async toTel() {
				let {
					id: order_id,
					pay_type
				} = this.detail
				if ([2, 3, 4, 5, 6].includes(pay_type)) {
					let url = await this.$api.technician.getVirtualPhone({
						order_id
					})
					this.$util.goUrl({
						url,
						openType: `call`
					})
				} else {
					let msg = pay_type == 7 ? '服务结束' : '服务取消'
					this.$util.showToast({
						title: `${msg}不能联系客户哦`
					})
				}
			},
			// 复制定位
			toCopy() {
				let {
					address,
					address_info
				} = this.detail.address_info
				let url = `${address}${address_info}`
				this.$util.goUrl({
					url,
					openType: 'copy'
				})
			},
			// 查看定位
			async toMap(key) {
				let {
					address,
					address_info = '',
					lat,
					lng
				} = this.detail[key]
				await this.$util.checkAuth({
					type: 'userLocation'
				})
				await uni.getLocation({
					type: 'gcj02',
				})
				await uni.openLocation({
					latitude: lat * 1,
					longitude: lng * 1,
					name: address_info ? `${address} ${address_info}` : address,
					scale: 28
				})
			},
		}
	}
</script>


<style lang="scss">
	.d-copy {
		border: 1px solid transparent;
		width: 70rpx;
		border-radius: 8rpx;
	}

	.copy-btn {
		height: 32rpx;
		line-height: 32rpx;
		background: #EEEEEE;
		text-align: center;
		padding: 5rpx 10rpx;
	}

	.popup-label {
		border-radius: 16rpx 16rpx 0 0;

		.iconguanbi {
			top: 32rpx;
			right: 28rpx;
			color: #A8AEB8;
			font-size: 40rpx;
		}

		.list-item {

			.list-child {
				width: auto;
				min-width: 120rpx;
				height: 70rpx;
				display: inline-block;
				padding: 0 20rpx;
				margin: 0 20rpx 25rpx 0;

				.tag-item {
					height: 70rpx;

					.icon-add {
						font-size: 24rpx;
						margin-right: 5rpx;
					}
				}

				.bg-item {
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					opacity: 0.15;
				}

			}
		}
	}
</style>
