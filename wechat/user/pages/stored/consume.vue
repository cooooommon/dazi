<template>
	<view class="stored-record-pages">
		<fixed>
			<view class="list-time flex-center f-paragraph c-title fill-base b-1px-b">
				<view @tap.stop="toChangeItem(index,'rankInd')" class="item-child flex-center"
					:style="{color: rankInd == index ? primaryColor : ''}" v-for="(item,index) in rankList"
					:key="index">{{item}}
					<i class="iconfont icon-right rotate-90 ml-sm" v-if="item == '日期筛选'"></i>
				</view>
			</view>
		</fixed>

		<view class="cate-item-area mt-md ml-lg mr-lg fill-base radius-16" @tap.stop="toChangeItem(index,'curInd')"
			v-for="(item,index) in list.data" :key="index">
			<view class="content f-title c-title pd-lg">
				<view class="flex-between">
					<view class="max-446 ellipsis">{{item.goods_title}}</view>
					<view class="c-warning">{{item.add==1?'+':'-'}}{{item.price}}</view>
				</view>
				<view class="flex-between f-caption c-caption">
					<view>{{item.create_time}}</view>
					<view>余额：{{item.after_balance}}</view>
				</view>
			</view>
		</view>


		<load-more :noMore="list.current_page>=list.last_page&&list.data.length>0" :loading="loading" v-if="loading">
		</load-more>
		<abnor v-if="!loading&&list.data.length<=0&&list.current_page==1"></abnor>

		<view class="space-footer"></view>

		<uni-popup ref="show_choose_time" type="top" :custom="true" :maskClick="false">
			<view style="height: 95rpx;"></view>
			<view class="popup-choose-time fill-base f-paragraph c-desc pt-lg pb-lg">
				<view class="flex-center">
					<view @tap.stop="toShowTime('start_time')" class="item-child flex-center flex-column">
						<view>开始时间</view>
						<view class="mt-sm" :style="{color:param.start_time ? primaryColor : '#999'}">
							{{param.start_time || '选择时间'}}
						</view>
					</view>
					<view @tap.stop="toShowTime('end_time')" class="item-child flex-center flex-column  b-1px-l">
						<view>结束时间</view>
						<view class="mt-sm" :style="{color:param.end_time ? primaryColor : '#999'}">
							{{param.end_time || '选择时间'}}
						</view>
					</view>
				</view>
			</view>
		</uni-popup>

		<w-picker mode="date" :startYear="startYear*1-10" :endYear="startYear" :value="toDay" :current="false"
			fields="day" @confirm="onConfirm($event)" :disabled-after="false" ref="day" :themeColor="primaryColor"
			:visible.sync="showDate">
		</w-picker>

	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	import wPicker from "@/components/w-picker/w-picker.vue";
	export default {
		components: {
			wPicker
		},
		data() {
			return {
				toDay: '',
				startYear: '',
				showKey: '',
				showDate: false,
				rankList: ['本月', '日期筛选'],
				rankInd: 0,
				start_time: '',
				param: {
					page: 1,
					start_time: '',
					end_time: ''
				},
				list: {
					data: []
				},
				loading: true,
				curInd: -1,
				startX: 0, //开始坐标
				startY: 0, //开始坐标
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		onLoad() {
			this.$util.showLoading()
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
				let cur_time = new Date(Math.ceil(new Date().getTime()))
				this.toDay = this.$util.formatTime(cur_time, 'YY-M-D')
				this.startYear = this.$util.formatTime(cur_time, 'YY')
				this.start_time = this.$util.formatTime(cur_time, 'YY-M') + '-01'
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
					rankInd,
					start_time,
					toDay
				} = this
				let param = this.$util.deepCopy(this.param)
				if (rankInd == 0) {
					param.start_time = start_time
					param.end_time = toDay
				}
				param.start_time = this.$util.DateToUnix(param.start_time)
				param.end_time = this.$util.DateToUnix(param.end_time) + 24 * 3600 - 1
				let newList = await this.$api.mine.payWater(param);

				newList.data.map(item => {
					item.isTouchMove = false
				})
				if (this.param.page == 1) {
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			async toChangeItem(index, key) {
				this[key] = index
				if (key == 'rankInd') {
					let methodModel = index == 1 ? 'open' : 'close'
					this.$refs.show_choose_time[methodModel]()
					if (index == 1) return
					this.param.page = 1
					await this.getList()
				}
			},
			toShowTime(key) {
				if (key == 'end_time' && !this.param.start_time) {
					this.$util.showToast({
						title: `请选择开始时间`
					})
					return
				}
				this.showKey = key
				this.showDate = true
			},
			async onConfirm(val) {
				let {
					start_time,
					end_time
				} = this.param
				let {
					showKey
				} = this

				let show_unit = this.$util.DateToUnix(showKey == 'month' ? `${val.result}-01` : val.result)
				let start_unit = start_time ? this.$util.DateToUnix(start_time) : 0
				let end_unit = end_time ? this.$util.DateToUnix(end_time) : 0

				if ((showKey == 'start_time' && end_unit && end_unit < this.$util.DateToUnix(val
						.result)) ||
					(showKey == 'end_time' && start_unit && start_unit > this.$util.DateToUnix(val.result))) {
					this.$util.showToast({
						title: `结束时间不能小于开始时间`
					})
					return
				}

				this.param[showKey] = val.result;
				if (showKey == 'end_time') {
					this.showDate = false
					this.$refs.show_choose_time.close()
					this.param.page = 1
					await this.getList()
				}
			},
			async toDel(index) {
				let {
					id,
					title
				} = this.list.data[index]
				let [res_del, {
					confirm
				}] = await uni.showModal({
					content: `请确认是否要删除‘${title}'`,
				})
				if (!confirm) return;
				let param = {
					id,
					status: -1
				}
				await this.$api.master.goodsCateUpdate(param)
				this.$util.showToast({
					title: `删除成功`
				})
				this.list.data.splice(index, 1)
			},
			//手指触摸动作开始 记录起点X坐标
			touchstart(e) {
				//开始触摸时 重置所有删除
				this.list.data.forEach(item => {
					if (item.isTouchMove) //只操作为true的
						item.isTouchMove = false;
				})
				this.startX = e.changedTouches[0].clientX
				this.startY = e.changedTouches[0].clientY
			},
			//滑动事件处理
			touchmove(e) {
				let {
					startX,
					startY
				} = this
				let {
					index
				} = e.currentTarget.dataset //当前索引

				let {
					clientX,
					clientY
				} = e.changedTouches[0]
				let touchMoveX = clientX
				let touchMoveY = clientY
				//获取滑动角度
				let angle = this.angle({
					X: startX,
					Y: startY
				}, {
					X: touchMoveX,
					Y: touchMoveY
				});
				this.list.data.forEach((v, i) => {
					v.isTouchMove = false
					//滑动超过30度角 return
					if (Math.abs(angle) > 30) return;
					if (i == index) {
						if (touchMoveX > startX) //右滑
							v.isTouchMove = false
						else //左滑
							v.isTouchMove = true
					}
				})
			},
			/**
			 * 计算滑动角度
			 * @param {Object} start 起点坐标
			 * @param {Object} end 终点坐标
			 */
			angle(start, end) {
				var _X = end.X - start.X,
					_Y = end.Y - start.Y
				//返回角度 /Math.atan()返回数字的反正切值
				return 360 * Math.atan(_Y / _X) / (2 * Math.PI);
			},
		}
	}
</script>


<style lang="scss">
</style>
