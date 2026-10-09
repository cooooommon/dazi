<template>
	<view class="wizard-index" v-if="isLoad">
		<fixed>
			<view class="search-box flex-between pr-lg fill-base">
				<search class="flex-1" @input="toSearch" :keyword="param.name" type="input" :padding="30" :height="70" :radius="0" backgroundColor="#fff"
					:placeholder="`搜索${$t('action.attendantName')}姓名`">
				</search>
				<view class="flex-center" @tap="changeScreenItem">
					<text class="f-paragraph">筛选</text>
					<i class="iconfont iconshaixuanxia-1 text-bold" style="font-size: 5px;color: #CDCDCD;margin-left: 8rpx;"></i>
				</view>
			</view>
		</fixed>
		<view class="mt-md ml-md mr-md radius-16 fill-base pl-lg pr-lg" v-for="(item,index) in list.data" :key="index">
			<view class="h-88 flex-between b-1px-b">
				<view class="flex-center">
					<text class="f-desc c-paragraph">下单人：</text>
					<text class="f-mini-title text-bold">{{item.nickName}}</text>
				</view>
				<view class="f-paragraph" :style="{color: item.status == 1 ? '#F99346' : primaryColor}">{{item.status == 1?`未入账`:`已入账`}}</view>
			</view>
			<view class="pt-md flex-y-center">
				<view class="f-desc c-paragraph" style="width: 157rpx;">项目付款：</view>
				<text class="f-desc">¥{{item.true_service_price}}</text>
			</view>
			<view class="pt-md flex-y-center">
				<view class="f-desc c-paragraph" style="width: 157rpx;">下单时间：</view>
				<text class="f-desc">{{$util.formatTime(item.create_time*1000,'YY.M.D h:m:s')}}</text>
			</view>
			<view class="pt-md flex-y-center">
				<view class="f-desc c-paragraph" style="width: 157rpx;">预计佣金：</view>
				<text class="f-desc c-warning">¥{{item.cash}}</text>
			</view>
			<view class="pt-md pb-lg flex-y-center">
				<view class="f-desc c-paragraph" style="width: 157rpx;">接单{{$t('action.attendantName')}}：</view>
				<text class="f-desc">{{item.coach_name}}</text>
			</view>
		</view>
		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
		<view class="space-footer"></view>
		
		<uni-popup ref="screen_item" type="bottom">
			<view @touchmove.stop.prevent class="fill-base pl-lg pr-lg common-popup-content" style="width: 100%;border-radius: 34rpx 34rpx 0 0;">
				<view class="all-title w-100 flex-center pb-lg rel">
					<text class="f-title text-bold">筛选</text>
					<i class="iconfont icon-add rotate-45 text-bold abs" style="right: 0;top: 0;" @tap="$refs.screen_item.close()"></i>
				</view>
				<view class="sex-box w-100">
					<view class=" f-paragraph text-bold">状态</view>
					<view class="flex-warp pt-lg">
						<view class="type-item mr-md flex-center f-desc"
						:style="{backgroundColor: statusInd == item.id ? primaryColor: '',borderColor: statusInd == item.id ? primaryColor : '#ddd',color: statusInd == item.id ? '#fff' : '#778498'}"
						v-for="(item,index) in statusList" :key="index" @tap="toCheckItem(item.id, 'status')">{{item.name}}</view>
					</view>
				</view>
				<view class="sex-box w-100">
					<view class=" f-paragraph text-bold pt-lg">时间</view>
					<view class="pt-lg">
						<scroll-view scroll-y @scrolltolower="scrolltolower" :scroll-with-animation="true" lower-threshold="100"
							style="width: 100%;max-height: 20vh;">
							<view class="flex">
								<view @tap.stop="toCheckItem(index, 'date')" class="flex-1 flex-center f-desc type-item-date pl-md pr-md mb-md"
									:style="{backgroundColor: chooseInd == index ? primaryColor: '', borderColor: chooseInd == index ? primaryColor : '#ddd',color: chooseInd == index ? '#fff' : '#778498'}"
									v-for="(item,index) in dateList" :key="index" :class="[{'ml-md': index > 0}]">
									{{item.name}}
								</view>
							</view>
						</scroll-view>
					</view>
				</view>
				<view class="flex-center pb-lg pt-lg" style="width: 100%;" v-if="chooseInd == 3">
					<view @tap.stop="toShowTime('start_time')" class="item-child flex-center flex-column flex-1">
						<view>开始时间</view>
						<view class="mt-sm" :style="{color:check_time.start_time ? primaryColor : '#999'}">
							{{check_time.start_time || '选择时间'}}
						</view>
					</view>
					<view @tap.stop="toShowTime('end_time')" class="item-child flex-center flex-column flex-1  b-1px-l">
						<view>结束时间</view>
						<view class="mt-sm" :style="{color:check_time.end_time ? primaryColor : '#999'}">
							{{check_time.end_time || '选择时间'}}
						</view>
					</view>
				</view>
				<view class="button">
					<view @tap.stop="toReset" class="item-child">
						重置
					</view>
					<view @tap.stop="toConfirm" class="item-child" :style="{background: primaryColor,color:'#fff'}">
						查询
					</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</uni-popup>
		<w-picker mode="date" :startYear="startYear*1-10" :endYear="startYear"
			:value="curDay" :current="false" fields="day"
			@confirm="onConfirm($event)" :disabled-after="false" ref="day" :themeColor="primaryColor"
			:visible.sync="showDate">
		</w-picker>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import wPicker from "@/components/w-picker/w-picker.vue";
	let timer = null
	export default {
		components: {
			wPicker
		},
		data() {
			return{
				isLoad: false,
				loading: true,
				list: {
					data: []
				},
				param: {
					page: 1,
					name: '',
					start_time: '',
					end_time: '',
					status: 0
				},
				statusType: {
					1: {
						name: '未入账',
						color: '#F99346'
					}
				},
				statusList: [
					{name: '全部', id: 0},
					{name: '未入账', id: 1},
					{name: '已入账', id: 2}
				],
				dateList: [
					{name: '今日', id: 0, start_time: 0,end_time: 0},
					{name: '近7天', id: 1, start_time: 0,end_time: 0},
					{name: '近30日', id: 2, start_time: 0,end_time: 0},
					{name: '自定义', id: 3}
				],
				statusInd: 0,
				chooseInd: 2, // 默认选中近30日
				check_time: {
					start_time: '',
					end_time: ''
				},
				startYear: '',
				curDay: '',
				showKey: '',
				showDate: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			plugAuth: state => state.config.configInfo.plugAuth,
		}),
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onLoad() {
			this.$util.showLoading()
			let cur_time = new Date(Math.ceil(new Date().getTime()))
			this.curDay = this.$util.formatTime(cur_time, 'YY-M-D')
			this.startYear = this.$util.formatTime(cur_time, 'YY')
			let today = this.$util.formatTime(cur_time, 'YY-M-D') + ' 23:59:59'
			let one = 3600 * 24 * 1000
			let time = this.$util.DateToUnix(today) * 1000
			
			this.dateList[0].start_time = this.$util.DateToUnix(this.$util.formatTime(cur_time, 'YY-M-D')) 
			this.dateList[0].end_time = this.$util.DateToUnix(today)
			this.dateList[1].start_time = this.$util.DateToUnix(this.$util.formatTime(time - 6 * one, 'YY-M-D')) 
			this.dateList[1].end_time = this.$util.DateToUnix(today)
			this.dateList[2].start_time = this.$util.DateToUnix(this.$util.formatTime(time - 29 * one, 'YY-M-D')) 
			this.dateList[2].end_time = this.$util.DateToUnix(today)
			
			this.param.start_time = this.$util.DateToUnix(this.$util.formatTime(time - 29 * one, 'YY-M-D')) 
			this.param.end_time = this.$util.DateToUnix(today)
			this.initIndex()
			
		},
		methods: {
			...mapActions(['getConfigInfo', 'getPlugAuth', 'getUserInfo', 'getMineInfo', 'getCoachInfo',
				'updateCommonOptions',
			]),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				await this.getList(1)
			},
			async getList(page){
				if(page){
					this.param.page = 1
				}
				let {
					list: oldList,
					param
				} = this
				let newList = await this.$api.mine.cashList(param)
				if (param.page == 1) {
					this.list = newList;
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList;
				}
				this.loading = false;
				this.isLoad = true
				this.$util.hideAll()
			},
			initRefresh() {
				this.$util.showLoading()
				this.initIndex(true)
			},
			changeScreenItem(){
				this.$refs.screen_item.open()
			},
			toShowTime(key) {
				if (key == 'end_time' && !this.check_time.start_time) {
					this.$util.showToast({
						title: `请选择开始时间`
					})
					return
				}
				let showTime = this.check_time[key]
				if (showTime) {
					this.curDay = showTime
				}
				
				this.showKey = key
				this.showDate = true
			},
			async onConfirm(val) {
				let {
					start_time,
					end_time
				} = this.check_time
				let {
					showKey
				} = this
				let show_unit = this.$util.DateToUnix(showKey == 'month' ? `${val.result}-01` : val.result)
				let start_unit = start_time ? this.$util.DateToUnix(start_time) : 0
				let end_unit = end_time ? this.$util.DateToUnix(end_time) : 0
				let cur_month = this.$util.formatTime(new Date(Math.ceil(new Date().getTime())), 'YY-M-D')
				let cur_unit = this.$util.DateToUnix(cur_month) + 1
			
				let msgType = {
					month: '开始月份',
					start_time: '开始时间',
					end_time: '结束时间',
				}
			
				if (show_unit > cur_unit) {
					this.$util.showToast({
						title: `${msgType[showKey]}不能选择未来时间哦`
					})
					return
				}
			
				if (((showKey == 'start_time' && end_unit && end_unit < this.$util.DateToUnix(val
							.result)) ||
						(showKey == 'end_time' && start_unit && start_unit > this.$util.DateToUnix(val.result)))) {
					this.$util.showToast({
						title: `结束时间不能小于开始时间`
					})
					return
				}
				this.check_time[showKey] = val.result
				// if(showKey == 'end_time'){
				// 	this.chooseInd = -1
				// }
			},
			toCheckItem(val , type){
				if(type == 'status'){
					this.statusInd = val
				}else{
					this.chooseInd = val
					this.check_time = {
						start_time: '',
						end_time: ''
					}
				}
			},
			toReset(){
				this.chooseInd = 2,
				this.statusInd = 0
				this.check_time = {
					start_time: '',
					end_time: ''
				}
				this.param.status = 0
				this.param.start_time = this.dateList[this.chooseInd].start_time
				this.param.end_time = this.dateList[this.chooseInd].end_time
				this.getList(1)
				this.$refs.screen_item.close()
			},
			toConfirm(){
				let { 
					statusInd ,
					chooseInd ,
					dateList,
					check_time
				} = this
				if(chooseInd == 3 ){
					if(!check_time.start_time){
						this.$util.showToast({
							title: `请选择开始时间`
						})
						return
					}else if(!check_time.end_time){
						this.$util.showToast({
							title: `请选择结束时间`
						})
						return
					}
				}
				if(check_time.start_time && check_time.end_time){
					this.param.start_time = this.$util.DateToUnix(check_time.start_time + ' 00:00:00')
					this.param.end_time = this.$util.DateToUnix(check_time.end_time + ' 23:59:59')
				}else{
					if(chooseInd > -1 && chooseInd != 3){
						this.param.start_time = dateList[chooseInd].start_time
						this.param.end_time = dateList[chooseInd].end_time
					}
				}
				this.param.status = statusInd
				this.getList(1)
				this.$refs.screen_item.close()
			},
			toSearch(val){
				clearTimeout(timer)
				timer = setTimeout(()=>{
					this.param.name = val
					this.getList(1)
				},1000)
			}
		},
		async onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.loading = true;
			this.$util.showLoading()
			this.param.page += 1
			await this.getList()
		},
	}
</script>

<style lang="scss" scoped>
	.wizard-index{
		.w-header{
			width: 140rpx;
			height: 140rpx;
			border-radius: 140rpx;
		}
		.w-status{
			width: 100rpx;
			height: 32rpx;
			border-radius: 32rpx;
			bottom: 0;
			left: 50%;
			transform: translate(-50% , 0);
		}
		.h-88{
			height: 88rpx;
		}
		
		.type-item{
			min-width: 150rpx;
			height: 60rpx;
			border-radius: 60rpx;
			color: #778498;
			border: 1px solid #DDDDDD;
		}
		.type-item-date{
			height: 60rpx;
			border-radius: 60rpx;
			color: #778498;
			border: 1px solid #DDDDDD;
		}
		.w-100{
			width: 100%;
		}
		.date-item{
			width: 160rpx;
		}
	}
</style>