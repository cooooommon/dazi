<template>
	<view class="technician-time-manage">

		<view class="fill-base pd-lg flex-between">
			<view class="flex-1 flex-y-baseline f-title c-title">是否上班<view class="f-paragraph ml-sm"
					:style="{color:form.is_work==1?primaryColor:'#999'}">
					{{form.is_work==1?'上班':'下班'}}
				</view>
			</view>
			<view @tap.stop="toChangeItem">
				<i class="iconfont icon-switch c-caption ml-sm" :class="[{'icon-switch-on':form.is_work==1}]"
					:style="{ color: form.is_work==1 ? primaryColor : ''}"></i>
			</view>
		</view>

		<view class="fill-base mt-md b-1px-b">
			<view class="f-title c-title pd-lg">选择接单时间</view>
		</view>
		<view class="flex-center fill-base f-paragraph c-desc pt-lg pb-lg b-1px-b">
			<view @tap.stop="toShowTime('start_time')" class="item-time flex-center flex-column">
				<view>开始时间</view>
				<view class="mt-sm" :style="{color:form.start_time ? primaryColor : '#999'}">
					{{form.start_time || '选择时间'}}
				</view>
			</view>
			<view @tap.stop="toShowTime('end_time')" class="item-time flex-center flex-column  b-1px-l">
				<view>结束时间</view>
				<view class="mt-sm" :style="{color:form.end_time ? primaryColor : '#999'}">
					{{is_next_day?'次日':''}}{{form.end_time || '选择时间'}}
				</view>
			</view>
		</view>
		<view class="fill-base pb-lg" style="border-bottom-left-radius: 32rpx;border-bottom-right-radius: 32rpx;">
			<view class="flex pl-md pr-md pt-lg pb-lg">
				<view class="f-mini-title">设置不可接单时间</view>
				<view class="f-paragraph c-disable">（只能设置近{{dayList.length}}天的时间）</view>
			</view>
			<tab @change="handerTabChange" :list="dayList" :activeIndex="dayCurrent*1" :activeColor="primaryColor"
				width="150rpx" height="80rpx"></tab>
		</view>
		<view class="date-list flex pt-md" v-if="dayList.length > 0">
			<view class="date-item radius-16 flex-center ml-md mt-md" @tap="getDateTime(index)"
				:style="{ background : form.coach_status != 3 && item.status == 0 && item.is_order==0 && !isTimes(item.time_str) ? primaryColor : '' , color:item.status == 0 && item.is_order==0 && !isTimes(item.time_str) ? '#fff' : ''}"
				:class="isTimes(item.time_str) || item.is_order==1 || form.coach_status == 3 ? 'date-item-prohibit c-disable' : 'fill-base c-5A677E'"
				v-for="(item,index) in dayList[dayCurrent].sub" :key="index">
				<view class="text-center">
					<view class="f-mini-title">{{item.time_text}}</view>
					<view class="f-caption">{{isTimes(item.time_str) || item.status == 0 ? '不可预约' : '可预约'}}</view>
				</view>
			</view>
		</view>
		<view class="space-max-footer"></view>
		<w-picker :visible.sync="showTime" mode="time" :value="toDayTime" :current="false" :second="false"
			:themeColor="primaryColor" @confirm="onConfirm" ref="time"></w-picker>

		<fix-bottom-button @confirm="submit" :text="[{text:'保存',type:'confirm',isAuth:true}]" bgColor="#fff"
			v-if="form.coach_status !=3 "></fix-bottom-button>

	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	import wPicker from "@/components/w-picker/w-picker.vue";
	import tab from "@/components/tab.vue"
	export default {
		components: {
			wPicker,
			tab
		},
		data() {
			return {
				toDay: '',
				toDayTime: '',
				showKey: '',
				showTime: false,
				form: {
					id: 0,
					is_work: 0,
					start_time: '',
					end_time: '',
					coach_status: 0,
					time_unit: ''
				},
				is_next_day: false,
				dayList: [],
				dayCurrent: 0,
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.initIndex()
		},
		methods: {
			...mapMutations(['updateTechnicianItem']),
			async initIndex() {
				// #ifdef H5
				if (this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				let cur_time = new Date(Math.ceil(new Date().getTime()))
				this.toDay = this.$util.formatTime(cur_time, 'YY-M-D')
				this.toDayTime = this.$util.formatTime(cur_time, 'h:m')
				let data = await this.$api.technician.timeConfig()
				for (let key in this.form) {
					this.form[key] = data[key]
				}
				data.day_list.forEach((item, index) => {
					item.title = index == 0 ? '今天' : item.dat_text
					item.sub = []
				})
				this.form.time_unit = data.time_unit
				this.dayList = data.day_list
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.toCount()
			},
			toChangeItem() {
				if (this.form.coach_status == 3) return
				this.form.is_work = this.form.is_work == 1 ? 0 : 1
			},
			toShowTime(key) {
				if (this.form.coach_status == 3) return
				this.showKey = key
				this.showTime = true
			},
			onConfirm(val) {
				this.form[this.showKey] = val.result;
				this.toCount()
			},
			toCount() {
				let {
					toDay = ''
				} = this
				let {
					start_time = '',
						end_time = ''
				} = this.form
				if (toDay && start_time && end_time) {
					let start = `${toDay} ${start_time}`
					let end = `${toDay} ${end_time}`
					this.is_next_day = this.$util.DateToUnix(start) >= this.$util.DateToUnix(end)
					this.dayList.forEach(item => {
						item.sub = []
					})
					this.getTimeCall()
				}
			},
			async getTimeCall() {
				this.$util.showLoading()
				let {
					start_time = '',
						end_time = ''
				} = this.form
				let {
					dayCurrent,
					dayList
				} = this
				let data = await this.$api.technician.getTime({
					start_time,
					end_time,
					dat_str: dayList[dayCurrent].dat_str
				})
				this.dayList[dayCurrent].sub = data
				this.$util.hideAll()
			},
			setDate(index) {
				this.dayCurrent = index
			},
			getDateTime(index) {
				if (this.form.coach_status == 3) return
				let {
					dayCurrent
				} = this
				let {
					sub = []
				} = this.dayList[dayCurrent]
				let {
					is_click,
					is_order,
					status
				} = sub[index]
				if (sub && is_order == 1) return
				this.dayList[dayCurrent].sub[index].is_click = is_click == 0 ? 1 : 0
				this.dayList[dayCurrent].sub[index].status = status == 0 ? 1 : 0
			},
			handerTabChange(index) {
				if (this.form.coach_status == 3) return
				this.dayCurrent = index
				if (this.dayList[index].sub && this.dayList[index].sub.length) return
				this.getTimeCall()
			},
			isTimes(value) {
				let date = new Date()
				return date.getTime() / 1000 > value
			},
			async submit() {
				let param = this.$util.deepCopy(this.form)
				if (!param.start_time || !param.end_time) {
					this.$util.showToast({
						title: !param.start_time ? `请选择开始时间` : `请选择结束时间`
					})
					return
				}
				delete param.coach_status
				let dayList = this.$util.deepCopy(this.dayList)
				param.time_text = dayList
				this.$util.showLoading()
				await this.$api.technician.setTimeConfig(param)
				this.$util.hideAll()
				this.$util.showToast({
					title: `保存成功`
				});
				this.updateTechnicianItem({
					key: 'haveOperItem',
					val: true
				})
				setTimeout(() => {
					this.$util.back()
					this.$util.goUrl({
						url: 1,
						openType: `navigateBack`
					})
				}, 1000)

			}
		}
	}
</script>


<style lang="scss">
	.technician-time-manage {
		.iconfont {
			font-size: 80rpx;
			line-height: 34rpx;
		}

		.item-time {
			width: 50%;
		}

		.date-title {
			padding-bottom: 60rpx;

			text {
				border-bottom: 3px solid transparent;
			}
		}

		.date-list {
			word-break: break-all;
			flex-flow: wrap;

			.date-item {
				width: 163rpx;
				height: 110rpx;
				border: 1px solid #E5E5E5;
			}

			.date-item-prohibit {
				background: #F6F7F7;
			}

			.c-5A677E {
				color: #5A677E;
			}
		}
	}
</style>
