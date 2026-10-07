<template>
	<view class="staff-page">
		<fixed>
			<view class="search-info flex-between fill-base">
				<view class="item-search pt-sm pb-sm flex-1">
					<search @input="toSearch" @confirm="toSearch" type="input" :padding="20" placeholder="搜索员工姓名"></search>
				</view>
				<view @tap="$refs.rank_item.open()" class="flex-center pr-md f-paragraph c-title">
					<!-- <i class="iconfont iconshaixuan2 mr-sm"></i> -->
					筛选
					<i class="iconfont icon-down" style="font-size: 8px;color: #CDCDCD;margin-left: 6rpx;"></i>
				</view>
			</view>
		</fixed>
		<block v-if="isLoad">
			<view class="ml-md mr-md mt-md fill-base radius-16" v-for="(item,index) in list.data" :key="index">
				<view class="pd-lg flex">
					<image :src="item.avatarUrl" mode="aspectFill" class="staff-header"></image>
					<view class="pl-md flex-1">
						<view class="f-title text-bold">{{item.nickName}}</view>
						<view class="f-desc pt-sm c-paragraph flex-between">累计订单金额 <text class="f-min-title text-bold c-title">¥{{item.price}}</text></view>
						<view class="f-desc pt-sm c-paragraph flex-between">累计佣金 <text class="f-min-title text-bold c-title">¥{{item.total_cash}}</text></view>
						<view class="f-desc pt-sm c-paragraph flex-between">未入账 <text class="f-min-title text-bold c-title">¥{{item.wait_cash}}</text></view>
					</view>
				</view>
				<view class="pb-lg pt-lg ml-lg mr-lg b-1px-t flex-between">
					<view class="">
						<view class="flex-y-center">
							<text class="f-desc c-paragraph">姓名：</text>
							<text class="f-paragraph text-bold">{{item.name}}</text>
						</view>
						<view class="flex-y-center pt-sm">
							<text class="f-desc c-paragraph">佣金：</text>
							<text class="f-paragraph text-bold c-price">{{item.balance}}%</text>
						</view>
					</view>
					<view class="flex-center">
						<view class="flex-column flex-center" @tap="edit(item)">
							<i class="iconfont iconbianji"></i>
							<text class="f-desc c-paragraph pt-sm">编辑</text>
						</view>
						<view class="flex-column flex-center" style="margin-left: 70rpx;">
							<i class="iconfont iconjiechuguanxi" style="font-size: 18px;"></i>
							<text class="f-desc c-paragraph pt-sm c-warning" @tap="edit(item, -1)">解除关系</text>
						</view>
					</view>
				</view>
			</view>
			
			<uni-popup type="center" ref="edit_box">
				<view class="fill-base edit-box pb-lg pt-md">
					<view class="f-title flex-center pt-lg pb-lg text-bold mb-md">修改备注</view>
					<view class="flex-center">
						<text class="pr-lg">姓名</text>
						<view class="edit-input flex-between">
							<input class="pl-lg pr-lg flex-1" type="text" v-model="editParam.name" :placeholder="rule[0].errorMsg" />
						</view>
					</view>
					<view class="flex-center mt-lg">
						<text class="pr-lg">比例</text>
						<view class="edit-input flex-between">
							<input class="pl-lg pr-lg flex-1" type="text" v-model="editParam.balance" :placeholder="rule[1].errorMsg" />
							<text class="f-mini-title pr-lg">%</text>
						</view>
					</view>
					<view class="flex-center mt-lg pt-lg pb-md">
						<view class="edit-btn radius-10 c-paragraph flex-center mr-lg" style="border: 1px solid #666;" @tap="$refs.edit_box.close()">取消</view>
						<view class="edit-btn radius-10 c-base flex-center" :style="{background: primaryColor}" @tap="determine">确定</view>
					</view>
				</view>
			</uni-popup>
			<uni-popup ref="rank_item" type="bottom" :maskClick="false">
				<view class="popup-rank fill-base">
					<view class="flex-between pd-lg">
						<view class="f-title c-title text-bold">选择筛选条件</view>
						<view @tap="toConfirm(1)" class="f-caption c-caption">取消</view>
					</view>
					<view class="pd-lg">
						<view class="pt-lg pb-lg f-paragraph text-bold">时间</view>
						<view class="flex-warp pb-lg">
							<view @tap="toChangeItem(index,'tabInd')" class="item-rank flex-center f-desc c-title radius-16"
								:class="[{'ml-md':index!=0}]"
								:style="{background:index==tabInd?primaryColor:'',color:index==tabInd?'#fff':'',borderColor:index==tabInd?primaryColor:''}"
								v-for="(item,index) in tabList" :key="index">{{item.title}}</view>
						</view>
						<view class="flex-between pt-lg pb-lg" v-if="tabInd==3">
							<view class="f-paragraph text-bold">开始时间</view>
							<picker mode="date" start="1900-01-01" :end="today" @change="pickerChange($event,'start_time')">
								<view class="flex-y-center f-title"
									:class="[{'c-title':tabList[3].start_time},{'f-caption c-caption':!tabList[3].start_time}]">
									{{tabList[3].start_time || '请选择'}} <i class="iconfont icon-right c-caption"></i>
								</view>
							</picker>
						</view>
						<view class="flex-between pt-md pb-lg" v-if="tabInd==3">
							<view class="f-paragraph text-bold">结束时间</view>
							<picker mode="date" start="1900-01-01" :end="today" @change="pickerChange($event,'end_time')">
								<view class="flex-y-center f-title"
									:class="[{'c-title':tabList[3].end_time},{'f-caption c-caption':!tabList[3].end_time}]">
									{{tabList[3].end_time || '请选择'}} <i class="iconfont icon-right c-caption"></i>
								</view>
							</picker>
						</view>
					</view>
					<view class="btn-info flex-center pd-lg">
						<view @tap="toConfirm(2)" class="item-child flex-center fill-base f-desc radius">重置</view>
						<view @tap="toConfirm(3)" class="item-child flex-center f-desc c-base radius"
							:style="{background:primaryColor,borderColor:primaryColor}">查询</view>
					</view>
					<view class="space-safe"></view>
				</view>
			</uni-popup>
			
			<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
			</load-more>
			<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>
			
			<view class="space-footer"></view>
		</block>
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
			return{
				isLoad: false,
				param: {
					page: 1,
					name: ''
				},
				list: {
					data: []
				},
				loading: true,
				editParam: {
					name: '',
					balance: '',
					id: ''
				},
				rule: [{
					name: "name",
					checkType: "isNotNull",
					errorMsg: "请输入姓名",
					regType: 2
				},{
					name: "balance",
					checkType: "isPercent",
					errorMsg: "请输入比例",
					regType: 1
				}],
				lockTap: false,
				tabInd: -1,
				tabList: [{
					id: 1,
					title: '今日',
					start_time: '',
					end_time: ''
				}, {
					id: 2,
					title: '近7日',
					start_time: '',
					end_time: ''
				}, {
					id: 3,
					title: '近30日',
					start_time: '',
					end_time: ''
				}, {
					id: 4,
					title: '自定义',
					start_time: '',
					end_time: ''
				}],
				today: '',
				preCheck: {
					tabInd: -1,
					typeId: 0,
					start_time: '',
					end_time: ''
				},
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onReachBottom() {
			if (this.list.current_page >= this.list.last_page || this.loading) return;
			this.param.page = this.param.page + 1;
			this.loading = true;
			this.getList();
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		onLoad() {
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
				this.$util.showLoading()
				let cur_time = new Date(Math.ceil(new Date().getTime()))
				let today = this.$util.formatTime(cur_time, 'YY-M-D')
				let one = 3600 * 24 * 1000
				let time = this.$util.DateToUnix(today) * 1000
				this.today = today
				this.tabList[0].start_time = today
				this.tabList[0].end_time = today
				this.tabList[1].start_time = this.$util.formatTime(time - 7 * one, 'YY-M-D')
				this.tabList[1].end_time = today
				this.tabList[2].start_time = this.$util.formatTime(time - 30 * one, 'YY-M-D')
				this.tabList[2].end_time = today
				this.getList()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			initRefresh() {
				this.param.page = 1
				this.initIndex(true)
			},
			toSearch(val) {
				this.param.page = 1
				this.param.name = val
				this.getList()
			},
			async getList() {
				let {
					list: oldList,
					tabInd,
					tabList
				} = this
				
				let param = this.$util.deepCopy(this.param)
				
				if(tabInd == -1){
					param.start_time = ''
					param.end_time = ''
				}else{
					let {
						start_time,
						end_time
					} = tabList[tabInd]
					
					param.start_time = this.$util.DateToUnix(start_time)
					param.end_time = this.$util.DateToUnix(end_time) + 24 * 3600 - 1
				}
				
				let newList = await this.$api.channel.staffList(param);
			
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.isLoad = true
				this.loading = false
				this.$util.hideAll()
			},
			pickerChange(e, type) {
				let {
					start_time,
					end_time
				} = this.tabList[3]
				if (type == 'start_time') {
					start_time = e.detail.value
				} else {
					end_time = e.detail.value
				}
			
				if (start_time && end_time && this.$util.DateToUnix(start_time) > this.$util.DateToUnix(end_time)) {
					this.$util.showToast({
						title: `开始时间不能大于结束时间`
					})
					return
				}
			
				this.tabList[3][type] = e.detail.value
			},
			toChangeItem(index, key) {
				if(this[key] == index && key == 'tabInd') return
				this.preCheck.start_time = ''
				this.preCheck.end_time = ''
				this.tabList[3].start_time = ''
				this.tabList[3].end_time = ''
				this[key] = index
			},
			// type 1取消；2重置；3查询
			toConfirm(type, ind = 3) {
				if (type == 2) {
					ind = 0
				}
				let {
					start_time = '',
						end_time = ''
				} = type == 1 ? this.preCheck : this.tabList[ind]
				let {
					tabInd,
					typeId
				} = type == 2 ? {
					tabInd: -1,
					typeId: 0
				} : type == 3 ? this : this.preCheck
				if (type == 3) {
					if ((!start_time || !end_time) && tabInd == 3) {
						this.$util.showToast({
							title: !start_time ? `请选择开始时间` : `请选择结束时间`
						})
						return
					}
					this.preCheck.start_time = start_time
					this.preCheck.end_time = end_time
					this.preCheck.tabInd = tabInd
					this.preCheck.typeId = typeId
				} else {
					this.tabList[ind].start_time = start_time
					this.tabList[ind].end_time = end_time
					this.tabInd = tabInd
					this.typeId = typeId
				}
				
				if (type != 2) {
					this.$refs.rank_item.close()
					this.param.page = 1
					this.getList()
				}
			},
			async edit(item, status){
				let {
					id,
					name,
					balance
				} = item
				this.editParam.id = id
				this.editParam.name = name
				this.editParam.balance = balance
				if(status == -1){
					if (this.lockTap) return
					this.lockTap = true
					let that = this
					uni.showModal({
						title: '提示',
						content: '是否确定解除关系？',
						success:async function (res) {
							if (res.confirm) {
								that.$util.showLoading()
								try{
									await that.$api.channel.updateStaff({id, status})
									that.$refs.edit_box.close()
									that.$util.showToast({
										title: '解除成功'
									});
									that.getList()
									that.lockTap = false
									that.$util.hideAll()
								}catch(e){
									setTimeout(() => {
										that.lockTap = false
										that.$util.hideAll()
									}, 2000)
								}
							}
						}
					});
				}else{
					this.$refs.edit_box.open()
				}
			},
			//表单验证
			validate(param) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					validate.add(param[name], item);
				})
				let message = validate.start();
				return message;
			},
			async determine(){
				let msg = this.validate(this.editParam);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try{
					await this.$api.channel.updateStaff(this.editParam)
					this.$refs.edit_box.close()
					this.$util.showToast({
						title: '修改成功'
					});
					this.getList()
					this.lockTap = false
				}catch(e){
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.staff-page{
		.staff-header{
			width: 124rpx;
			height: 124rpx;
			border-radius: 124rpx;
		}
		.c-price{
			color: #FF0000;
		}
		.edit-box{
			width: 690rpx;
			.edit-input{
				width: 400rpx;
				height: 80rpx;
				border: 1px solid #ccc;
				border-radius: 6rpx;
				input{
					height: 80rpx;
				}
			}
			.edit-btn{
				width: 160rpx;
				height: 80rpx;
			}
		}
		.popup-rank {
			border-radius: 34rpx 34rpx 0 0;
			
			.item-rank {
				width: 157rpx;
				height: 72rpx;
				border: 1px solid #E5E5E5;
			}
			
			.btn-info {
				background: #F9F9F9;
			
				.item-child {
					width: 320rpx;
					height: 80rpx;
					background: #FFFFFF;
					border: 1rpx solid #C7C7C7;
					margin: 0 14rpx;
				}
			}
			
			.space-safe {
				background: #F9F9F9;
			}
		}
	}
</style>