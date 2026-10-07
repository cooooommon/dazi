<template>
	<view class="pages-technician-level" v-if="isLoad">
		<swiper class="image-container" :indicator-dots="false" @change="swiperChange" :current="currentIndex"
			v-if="levelList && levelList.length > 0">
			<swiper-item class="swiper-item" v-for="(item, index) in levelList" :key="index">
				<view class="level-line-info flex-center mt-lg ml-md mr-md rel">
					<view class="space-lg"></view>
					<i class="iconfont icon-level-line icon-font-color abs" :style="{
	backgroundImage: `-webkit-linear-gradient(0deg, #000 0%,  ${primaryColor} 50%, #000 100%)`}"></i>
					<view class="list-item flex-center flex-column" v-for="(aitem,aindex) in 3" :key="aindex">
						<view class="line-tag" :class="[{'left':aindex==0},{'center':aindex==1},{'right':aindex==2}]"
							v-if="(aindex==0 && index>0) || aindex == 1 || (aindex ==2 && index!=levelList.length-1)">
							<view class="tag-lg flex-center">
								<view class="tag-md flex-center">
									<view class="tag-sm flex-center" :style="{background:primaryColor}"
										v-if="aindex==1">
									</view>
								</view>
							</view>
						</view>
						<view class="line-text mt-sm"
							v-if="(aindex==0 && index>0) ||(aindex ==2 && index!=levelList.length-1)">
							{{aindex==0?levelList[index-1].title : levelList[index+1].title}}
						</view>
						<view class="text-tag flex-center mt-sm rel" :style="{background:primaryColor}"
							v-if="aindex===1">
							<view class="square-tag flex-center abs"
								:style="{background:primaryColor,background:`linear-gradient(-45deg, transparent 12rpx,${primaryColor} 0)`}">
								<view class="square-mini fill-base"></view>
							</view>
							<view class="tag-text fill-base flex-center pl-sm pr-sm" :style="{color:primaryColor}">
								{{item.title}}
							</view>
						</view>
					</view>
				</view>

				<view class="technician-level pd-lg ml-md mr-md mt-md rel"
					:style="{border:`1rpx solid ${primaryColor}`}">
					<i class="iconfont icon-level-icon icon-font-color abs" :style="{left:'60rpx',
				backgroundImage: `-webkit-linear-gradient(0deg, #000 36%,  ${primaryColor} 100%)`}"></i>
					<image mode="aspectFill" class="level-icon-img abs"
						src="https://lbqny.migugu.com/admin/anmo/technician/level-icon.png">
					</image>
					<view class="space-md"></view>
					<view class="flex-y-baseline level-title text-bold">{{item.title}}
						<view class="ml-sm f-desc">
							{{ index < curLevelInd ? '已解锁': index == curLevelInd ? '当前等级' : '未解锁' }}
						</view>
					</view>
					<view style="height: 55rpx;"></view>
					<view class="level-line radius rel">
						<view class="level-line cur radius abs" :style="{width:`35%`,background:primaryColor}"></view>
					</view>
					<view style="height: 25rpx;"></view>
					<view class="flex-between f-paragraph c-base text-bold">
						<view>{{item.data.title}}</view>
						<view>{{item.data.next_level_title}}</view>
					</view>
					<view class="flex-warp f-icontext mt-sm">
						<text v-for="(aitem,aindex) in item.data.differ_text"
							:key="aindex">{{aindex<2 || aindex==item.data.differ_text.length-1? aitem: `、${aitem}` }}</text>
					</view>
				</view>
			</swiper-item>
		</swiper>



		<scroll-view scroll-x class="count-list  mt-lg ml-md mr-md" v-if="coach_level.id">
			<view class="list-item flex-column" v-for="(item,index) in countList" :key="index">
				<view class="flex-center num text-bold" :style="{color:primaryColor}">
					{{coach_level[item.key]}}{{item.unit||''}}
				</view>
				<view class="flex-center text">{{item.text}}</view>
			</view>
		</scroll-view>

		<view @tap.stop="$refs.show_rule_item.open()" class="flex-y-center ml-md pt-lg pb-lg f-title c-base">
			<view class="text-bold">规则说明</view> <i class="iconfont iconshuyi_shuoming ml-sm"></i>
		</view>
		<scroll-view scroll-x class="rule-list flex-column ml-md mr-md pl-md pr-md"
			v-if="ruleList && ruleList.length > 0">
			<view class="list-item flex-warp" v-for="(item,index) in ruleList" :key="index">
				<view class="flex-center" :class="[{'title':bindex==0},{'text':bindex>0}]"
					:style="{borderTop: bindex!==0?`1rpx solid #19212C`:''}" v-for="(bitem,bindex) in item"
					:key="bindex">
					{{index==1 && bindex > 0?'¥':''}}{{bitem}}{{bindex > 0 ?unitArr[index] : ''}}
				</view>
			</view>
		</scroll-view>

		<view class="space-footer"></view>

		<uni-popup ref="show_rule_item" type="center" :maskClick="false">
			<view class="common-popup-content fill-base pd-lg radius-34">
				<view class="title">规则说明</view>
				<view class="f-desc c-title mt-lg">
					在折算周期（T）内，{{$t('action.attendantName')}}完成以下维度的考核指标后，{{$t('action.attendantName')}}可以在T+1个周期按照该等级算提成比例，例如折算周期为每月，{{$t('action.attendantName')}}在这个月内完成了多少，会在月底30号系统核算，满足某个等级标准之后，会在下一个月的1号生效
					<view class="mt-sm flex-1">
						最低业绩：{{$t('action.attendantName')}}服务的所有服务订单和续单订单，不包含车费。
					</view>
					<view class="mt-sm flex-1">
						续单率：续单订单金额/最低业绩
					</view>
					<view class="mt-sm flex-1">
						服务时长：{{$t('action.attendantName')}}接单服务的时间
					</view>
					<view class="mt-sm flex-1">
						在线时长：{{$t('action.attendantName')}}在线工作的时间
					</view>
					<view class="mt-sm flex-1">
						积分：若储值返佣设置勾选了返积分，则{{$t('action.attendantName')}}邀请用户充值将获得积分，积分和金额按照1:1比例换算
					</view>
				</view>
				<view class="button">
					<view @tap.stop="$refs.show_rule_item.close()" class="item-child c-base"
						:style="{background: primaryColor,color:'#fff'}">知道了</view>
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
	export default {
		components: {},
		data() {
			return {
				isLoad: false,
				options: {},
				currentIndex: 0,
				curLevelInd: 0,
				levelList: [],
				countList: [{
					key: 'coach_price',
					text: '本期业绩'
				}, {
					key: 'coach_integral',
					text: '本期积分'
				}, {
					key: 'coach_add_balance',
					text: '本期续单率',
					unit: '%'
				}, {
					key: 'coach_time_long',
					text: '本期服务时长(分钟)'
				}, {
					key: 'online_time',
					text: '本期在线时长(小时)'
				}],
				ruleList: [],
				unitArr: [],
				coach_level: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
		}),
		async onLoad(options) {
			this.$util.setNavigationBarColor({
				bg: '#19212C'
			})
			this.$util.showLoading()
			await this.initIndex()
			this.isLoad = true
		},
		methods: {
			...mapActions(['getUserInfo']),
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
				let data = await this.$api.technician.coachLevel()

				let {
					coach_level,
					level_list,
					coach_level_show = 1
				} = data

				level_list.map(item => {
					let {
						differ_time_long,
						differ_online_time,
						differ_price,
						differ_add_price,
						differ_integral
					} = item.data
					let differ_text = []
					if (differ_time_long || differ_online_time || differ_price || differ_add_price ||
						differ_integral) {
						differ_text = [`还差`]
						if (differ_time_long) {
							differ_text.push(`${differ_time_long}分钟服务时长`)
						}
						if (differ_online_time) {
							differ_text.push(`${differ_online_time}小时在线时长`)
						}
						if (differ_price) {
							differ_text.push(`${differ_price}业绩`)
						}
						if (differ_add_price) {
							differ_text.push(`${differ_add_price}元续单订单额`)
						}
						if (differ_integral) {
							differ_text.push(`${differ_integral}积分`)
						}
						differ_text.push(`可升级`)
					}
					item.data.differ_text = differ_text
				})

				let ruleList = [
					['等级'],
					['最低业绩'],
					['续单率'],
					['服务时长'],
					['在线时长'],
					['积分'],
				]
				let arr = ['title', 'price', 'add_balance', 'time_long', 'online_time', 'integral']
				let unitArr = ['', '', '%', '分钟', '小时', '']
				if (coach_level_show == 1) {
					ruleList.push(['分成比例'])
					arr.push('balance')
					unitArr.push('%')
				}
				arr.map((item, index) => {
					level_list.map(aitem => {
						let datas = item === 'time_long' ? `${aitem.lower}至${aitem[item]}` : aitem[
							item]
						ruleList[index].push(datas)
					})
				})
				let {
					id = 0
				} = coach_level
				let ind = level_list.findIndex(item => {
					return item.id == id
				})
				this.currentIndex = ind
				this.curLevelInd = ind
				this.ruleList = ruleList
				this.levelList = level_list
				this.unitArr = unitArr
				this.coach_level = coach_level
				this.isLoad = true
				this.$util.hideAll()
			},
			initRefresh() {
				this.initIndex(true)
			},
			swiperChange(e) {
				this.currentIndex = e.detail.current
			},
		}
	}
</script>


<style lang="scss">
	page {
		background: #19212C;
	}

	.pages-technician-level {


		.image-container {
			width: 750rpx;
			height: 504rpx;

			.swiper-item {
				width: 750rpx;
				height: 504rpx;

				.level-line-info {
					height: 120rpx;

					.icon-level-line {
						top: 30rpx;
						left: 0;
						font-size: 94rpx;
						z-index: -1;
					}

					.list-item {
						width: 33.33%;

						.line-tag {
							.tag-lg {
								width: 16px;
								height: 16px;
								border-radius: 50%;
								background: rgba(255, 255, 255, 0.3);

								.tag-md {
									width: 10px;
									height: 10px;
									border-radius: 50%;
									background: rgba(255, 255, 255, 0.7);
								}
							}

						}

						.line-tag.left,
						.line-tag.right {
							margin-top: 48rpx;
						}

						.line-tag.center {
							margin-top: 4rpx;
						}

						.line-tag.center {
							.tag-lg {
								width: 26px;
								height: 26px;
								border-radius: 50%;

								.tag-md {
									width: 18px;
									height: 18px;
									border-radius: 50%;

									.tag-sm {
										width: 14px;
										height: 14px;
										border-radius: 50%;
									}
								}
							}
						}

						.line-text {
							font-size: 20rpx;
							color: #E5F4F5;
						}

						.text-tag {
							margin-top: 16rpx;
							min-width: 70rpx;
							height: 38rpx;
							padding: 4rpx;
							border-radius: 8rpx;

							.square-tag {
								top: -4rpx;
								left: 50%;
								margin-left: -8rpx;
								width: 16rpx;
								height: 16rpx;
								transform: rotate(45deg);

								.square-mini {
									width: 8rpx;
									height: 8rpx;
								}
							}

							.tag-text {
								font-size: 20rpx;
								height: 30rpx;
								border-radius: 6rpx;
							}
						}
					}
				}
			}
		}



		.count-list {
			white-space: nowrap;
			width: 710rpx;
			height: 160rpx;
			background: rgba(255, 255, 255, 0.05);
			border-radius: 16rpx;

			.list-item {
				display: inline-block;
				width: 30%;
				margin-top: 34rpx;

				.num {
					font-size: 38rpx;
				}

				.text {
					font-size: 22rpx;
					color: #7E8DA3;
					margin-top: 5rpx;
				}
			}
		}

		.rule-list {
			white-space: nowrap;
			width: 670rpx;
			background: rgba(255, 255, 255, 0.05);
			border-radius: 16rpx;

			.list-item {
				display: inline-block;
				font-size: 26rpx;

				.title {
					width: 190rpx;
					height: 108rpx;
					color: #E5EBF5;
				}

				.text {
					width: 190rpx;
					height: 95rpx;
					color: #7E8DA3;
				}
			}
		}

		.iconshuyi_shuoming {
			font-size: 34rpx;
		}
	}
</style>
