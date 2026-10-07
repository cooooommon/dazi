<template>
	<view class="min-countdown" :class="countdownClass">
		<rich-text :nodes="time" v-if="type == 1"></rich-text>
		<view class="flex-y-center" :style="{color:primaryColor}" v-if="type == 2">
			<view class="count-tag flex-center">{{time.d}}</view>天<view class="count-tag flex-center">{{time.h}}</view>时
			<view class="count-tag flex-center">{{time.m}}</view>分<view class="count-tag flex-center">{{time.s}}</view>秒
		</view>
		<view class="find-time flex-y-center" v-if="type == 3">
			<view class="rel find-time-item flex-center">
				<view class="abs find-time-bg" :style="{backgroundColor:primaryColor}"></view>
				<view class="f-icontext abs" :style="{color:primaryColor}">{{time.d}}</view>
			</view>
			<view class="f-little c-index-text">天</view>
			<view class="rel find-time-item flex-center">
				<view class="abs find-time-bg" :style="{backgroundColor:primaryColor}"></view>
				<view class="f-icontext abs" :style="{color:primaryColor}">{{time.h}}</view>
			</view>
			<view class="f-little c-index-text">时</view>
			<view class="rel find-time-item flex-center">
				<view class="abs find-time-bg" :style="{backgroundColor:primaryColor}"></view>
				<view class="f-icontext abs" :style="{color:primaryColor}">{{time.m}}</view>
			</view>
			<view class="f-little c-index-text">分</view>
			<view class="rel find-time-item flex-center">
				<view class="abs find-time-bg" :style="{backgroundColor:primaryColor}"></view>
				<view class="f-icontext abs" :style="{color:primaryColor}">{{time.s}}</view>
			</view>
			<view class="f-little c-index-text">秒</view>
		</view>
		<view class="find-time flex-center" v-if="type == 4">
			<block v-if="time.d > 0">
				<view class="rel order-time-item flex-center">
					<view class="abs find-time-bg" :style="{backgroundColor:primaryColor}"></view>
					<view class="f-paragraph abs text-bold" :style="{color:primaryColor}">{{time.d}}</view>
				</view>
				<view class="f-caption pl-sm pr-sm" :style="{color:primaryColor}">天</view>
			</block>
			<view class="rel order-time-item flex-center">
				<view class="abs find-time-bg" :style="{backgroundColor:primaryColor}"></view>
				<view class="f-paragraph abs text-bold" :style="{color:primaryColor}">{{time.h||'00'}}</view>
			</view>
			<view class="f-caption pl-sm pr-sm" :style="{color:primaryColor}">时</view>
			<view class="rel order-time-item flex-center">
				<view class="abs find-time-bg" :style="{backgroundColor:primaryColor}"></view>
				<view class="f-paragraph abs text-bold" :style="{color:primaryColor}">{{time.m||'00'}}</view>
			</view>
			<view class="f-caption pl-sm pr-sm" :style="{color:primaryColor}">分</view>
			<view class="rel order-time-item flex-center">
				<view class="abs find-time-bg" :style="{backgroundColor:primaryColor}"></view>
				<view class="f-paragraph abs text-bold" :style="{color:primaryColor}">{{time.s||'00'}}</view>
			</view>
			<view class="f-caption pl-sm " :style="{color:primaryColor}">秒</view>
		</view>
		<view class="flex-y-center" :style="{color:'#FF3A3D'}" v-if="type == 5">
			<view class="flex-center ml-sm mr-sm" :class="[{'count-tag-5 c-base f-ms-little': isDay == 1},{'c-warning f-paragraph': isDay == 0}]">{{time.d || '00'}}</view><text class="f-caption c-warning">天</text>
			<view class="count-tag-5 flex-center f-ms-little c-base">{{time.h || '00'}}</view>:
			<view class="count-tag-5 flex-center f-ms-little c-base">{{time.m || '00'}}</view>:
			<view class="count-tag-5 flex-center f-ms-little c-base" style="margin-right: 0;">{{time.s || '00'}}</view>
		</view>
		<view class="flex-y-center" :style="{color:'#FF3317'}" v-if="type == 6">
			{{time.d}}天 {{time.h}}:{{time.m}}:{{time.s}}
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from 'vuex';
	export default {
		name: 'min-countdown',
		props: {
			type: {
				type: Number,
				default: 1
			},
			newtargetTime: {
				type: String,
				default: ''
			},
			targetTime: {
				type: Number,
				default: 0
			},
			format: {
				type: String,
				default: '{%d}天{%h}小时{%m}分{%s}秒'
			},
			countdownClass: {
				type: String,
				default: ''
			},
			bgColor: {
				type: String,
				default: ''
			},
			isPlay: {
				type: Boolean,
				default: false
			},
			refresh: {
				type: [String, Number],
				default: ''
			},
			isDay: {
				type: [String, Number],
				default: 0
			}
		},
		watch: {
			refresh(oldValue, newValue){
				console.log(oldValue, newValue , '=============> oldValue, newValue')
				if(oldValue == -1){
					this.time = {
						h: '00',
						m: '00',
						s: '00'
					}
					return
				}
				this.getTime()
			}
		},
		data() {
			return {
				time: '00:00:00',
				audioBg: {},
				playBg: false,
			}
		},
		async mounted() {
			this.getTime()
			if (this.isPlay && !this.configInfo.id) {
				await this.getConfigInfo()
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			isHaveAudio: state => state.user.isHaveAudio,
		}),
		methods: {
			...mapActions(['getConfigInfo', 'toPlayAudio']),
			...mapMutations(['updateUserItem']),
			initIndex() {
				setTimeout(() => {
					this.getTime()
				}, 1000)
			},
			getTime() {
				let time = {}
				let format = this.format

				function formatNumber(num) {
					return num > 9 ? `${num}` : `0${num}`
				}
				let targetTime = this.targetTime
				if(this.newtargetTime){
					targetTime = this.newtargetTime.replace(/-/g,'/')
					targetTime = new Date(targetTime).getTime()
				}
				const gapTime = Math.ceil((targetTime - new Date().getTime()) / 1000)
				if (gapTime >= 0) {
					time.d = formatNumber(parseInt(gapTime / 86400))
					let lastTime = gapTime % 86400;
					time.h = formatNumber(parseInt(lastTime / 3600))
					lastTime = lastTime % 3600;
					time.m = formatNumber(parseInt(lastTime / 60))
					time.s = formatNumber(lastTime % 60);
					['d', 'h', 'm', 's'].forEach(item => {
						const day = time[item].split('');
						format = format.replace('{%' + item + '}', time[item])
						format = format.replace('{%' + item + '0}', day[0])
						format = format.replace('{%' + item + '1}', day[1])
						format = format.replace('{%' + item + '2}', day[2] ? day[2] : '0')
					})
					this.time = this.type == 1 ? format : time
					this.initIndex()
					if (this.isPlay && gapTime == 60 * 5) {
						this.toPlayAudio()
					}
				} else {
					this.$emit('callback')
				}
			},
			async toInitPlay() {
				if (!this.configInfo.id) {
					await this.getConfigInfo()
				}
				console.log("======toInitPlay")

				let {
					countdown_voice
				} = this.configInfo
				this.audioBg = uni.createInnerAudioContext();
				this.audioBg.src = countdown_voice
				this.audioBg.obeyMuteSwitch = false
				this.updateUserItem({
					key: 'isHaveAudio',
					val: true
				})
				console.log(this.audioBg, "=======this.audioBg")

				this.audioBg.onPlay(() => {
					console.log('onPlay')
					this.playBg = true
				})
				this.audioBg.onStop(() => {
					console.log('onStop')
					this.playBg = false
				})
				this.audioBg.onError(() => {
					console.log('onError')
					this.playBg = false
					this.audioBg.destroy()
				})
				this.audioBg.onEnded(() => {
					console.log('onEnded')
					this.playBg = false
				})
			},
			toPlay() {
				console.log("=====toPlay", this.playBg, this.audioBg)
				if (this.playBg) {
					this.audioBg.stop()
				}
				this.audioBg.play()
			}
		}
	}
</script>

<style scoped>
	.min-countdown {
		display: inline-flex;
		justify-content: center;
		align-items: center;
	}

	.count-tag {
		width: 56rpx;
		height: 56rpx;
		margin: 0 10rpx;
		background: linear-gradient(0deg, #F7D9AD 0%, #FBEACB 100%);
		border-radius: 8rpx;
	}
	.count-tag-5 {
		min-width: 36rpx;
		height: 36rpx;
		background: #FF3A3D;
		border-radius: 8rpx;
		margin: 0 6rpx;
	}
	.find-time .find-time-item{
		width: 34rpx;
		height: 34rpx;
		line-height: 34rpx;
	}
	.find-time .order-time-item{
		width: 46rpx;
		height: 46rpx;
		line-height: 46rpx;
		border-radius: 5rpx;
		overflow: hidden;
	}
	.find-time .find-time-bg{
		width: 100%;
		height: 100%;
		border-radius: 4rpx;
		opacity: 0.1;
	}
	.find-time .c-index-text{
		padding: 0 6rpx;
	}
</style>
