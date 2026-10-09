<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import $api from "@/api/index.js"
	import $store from "@/store/index.js"
	export default {
		data() {
			return {
				timer: null,
				travelTimer: null,
				lockTap: false
			}
		},
		async mounted() {
			// #ifdef H5
			if (typeof window.entryUrl === 'undefined' || window.entryUrl === '') {
				window.entryUrl = window.location.href.split('#')[0]
			}
			if (window.location.href.indexOf('?#') < 0) {
				window.location.href = window.location.href.replace("#", "?#")
			}
			// #endif
		},
		async onLaunch() {
			let that = this
			uni.onLocationChange((res) => {
				let {
					latitude: lat,
					longitude: lng
				} = res 
				that.toChangeLocation({
					lat,
					lng
				})
			})

			let configInfo = uni.getStorageSync('configInfo') || ''
			if (configInfo) {
				this.updateConfigItem({
					key: 'configInfo',
					val: configInfo
				})
			}
			let isGzhLogin = uni.getStorageSync('isGzhLogin') || false
			this.updateUserItem({
				key: 'isGzhLogin',
				val: isGzhLogin
			})
			this.updateUserItem({
				key: 'changeAddr',
				val: false
			})
			let arr = ['userInfo', 'autograph', 'location', 'locaRefuse', 'appLogin', 'loginType',
				'loginPage'
			]
			arr.map(key => {
				let val = uni.getStorageSync(key) || ''
				if (key == 'locaRefuse') {
					val = $store.state.user.location.lat ? (val || false) : false
				}
				this.updateUserItem({
					key,
					val
				})
				// #ifdef APP-PLUS
				if (key == 'userInfo') {
					this.updateUserItem({
						key: 'isShowLogin',
						val: val && val.id ? false : true
					})
				}
				// #endif
			})
			let {
				commonOptions
			} = this
			let {
				coach_id = 0
			} = commonOptions
			if (coach_id) {
				commonOptions.coach_id = 0
				this.updateUserItem({
					key: 'commonOptions',
					val: commonOptions
				})
			}
			this.getConfigInfo()
			// #ifdef MP-WEIXIN
			await this.getPrivacySetting()
			// #endif 
		},
		async onShow() {
			// #ifdef MP-WEIXIN
			wx.onNeedPrivacyAuthorization((resolve, eventInfo) => {
				this.resolvePrivacyAuthorization = resolve
			})
			wx.requirePrivacyAuthorize({ //该接口请求隐私协议，会自动触发上边的wx.onNeedPrivacyAuthorization
				success: (suc) => {},
				fail: (fail) => {}
			})
			// #endif

			this.getUserCoachStatus()
			this.timerInit()

			let {
				coach_travel_order_id
			} = this
			this.updateOrderItem({
				key: 'coach_travel_order_id',
				val: coach_travel_order_id
			})
			uni.onNetworkStatusChange((res) => {
				let {
					isConnected
				} = res
				if (isConnected) return
				let methodArr = ['updateServiceItem', 'updateTechnicianItem', 'updateStoreItem',
					'updateDynamicItem', 'updateShopstoreItem', 'updateOrderItem'
				]
				methodArr.map(item => {
					$store.commit(item, {
						key: 'pageActive',
						val: false
					})
				})
			})
		},
		async onHide() {
			// #ifdef H5 
			let {
				status = 0,
					coach_position = 0
			} = this.userCoachStatus
			if (coach_position && status == 2) {
				this.updateUserItem({
					key: 'locationChange',
					val: false
				})
				this.toHidePage()
			}
			// #endif
			if (this.coach_travel_order_id) {
				this.toAddCoachTravel()
			}
		},
		onUnload() {
			if (this.timer) clearInterval(this.timer)
			if (this.travelTimer) clearInterval(this.travelTimer)
		},
		data() {
			return {
				resolvePrivacyAuthorization: ''
			}
		},
		watch: {
			resolvePrivacy(newval, oldval) {
				if (newval) {
					// #ifdef MP-WEIXIN
					this.resolvePrivacyAuthorization(newval)
					// #endif
				}
			},
			'configInfo.attendant_name'(newVal, oldVal) {
				if (newVal) {
					this.mergeLocaleMessage()
				}
			},
			useChooseLocation(newVal, oldVal) {
				let {
					status = 0,
						coach_position = 0,
						auto_coach_position = 0
				} = this.userCoachStatus
				if (status == 2 && auto_coach_position) {
					let cur = newVal ? 0 : 1
					let userCoachStatus = Object.assign({}, this.$util.deepCopy(this.userCoachStatus), {
						coach_position: cur
					})
					if (!newVal) {
						this.timerInit()
					}
					this.updateUserItem({
						key: 'userCoachStatus',
						val: userCoachStatus
					})
				}
			},
			userCoachStatus(newVal, oldVal) {
				let {
					status = 0,
						coach_position = 0,
				} = newVal
				if (status == 2) {
					if (!this.timer) {
						this.timerInit()
					}
					let locationChange = coach_position ? true : false
					this.updateUserItem({
						key: 'locationChange',
						val: locationChange
					})
					if (coach_position) {
						this.timerInit()
						this.updateUserItem({
							key: 'locationChange',
							val: false
						})
						setTimeout(() => {
							this.updateUserItem({
								key: 'locationChange',
								val: locationChange
							})
						}, 150)
					} else {
						setTimeout(() => {
							this.updateUserItem({
								key: 'locationChange',
								val: locationChange
							})
						}, 150)
					}
				} else {
					if (this.timer) clearInterval(this.timer)
				}
			},
			locationChange(newVal, oldVal) {
				if (newVal) {
					uni.startLocationUpdate()
				} else {
					uni.stopLocationUpdate()
				}
			},
			coach_travel_order_id(newVal, oldVal) {
				if (newVal) {
					this.toAddCoachTravel()
				}
			},
			coach_travel_arrive_id(newVal, oldVal) {
				if (this.travelTimer) clearInterval(this.travelTimer)
				if (newVal) {
					this.updateOrderItem({
						key: 'coach_travel_order_id',
						val: 0
					})
					this.toConfirmAddCoachTravel(newVal)
				}
			}
		},
		computed: mapState({
			configInfo: state => state.config.configInfo,
			old_attendant_name: state => state.config.old_attendant_name,
			old_channel_menu_name: state => state.config.old_channel_menu_name,
			commonOptions: state => state.user.commonOptions,
			locationChange: state => state.user.locationChange,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			userCoachStatus: state => state.user.userCoachStatus,
			useChooseLocation: state => state.user.useChooseLocation,
			changeOnAddr: state => state.user.changeOnAddr,
			coach_travel_order_id: state => state.order.coach_travel_order_id,
			resolvePrivacy: state => state.user.resolvePrivacy
		}),
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'getUserCoachStatus', 'getPrivacySetting']),
			...mapMutations(['updateUserItem', 'updateOrderItem', 'updateConfigItem']),
			timerInit() {
				if (this.timer) clearInterval(this.timer)
				this.timer = setInterval(() => {
					this.toResetUnix()
				}, 20000)
			},
			async toResetUnix() {
				let val = this.$util.deepCopy(this.userCoachStatus)
				let cur_unix = this.$util.DateToUnix(this.$util.formatTime(new Date(), 'YY-M-D h:m:s'))
				val.unix = cur_unix
				this.updateUserItem({
					key: 'userCoachStatus',
					val: val
				})
				let {
					unix = 0
				} = this.changeOnAddr
				if (!unix || (unix && cur_unix - unix >= 3)) {
					let {
						lat,
						lng
					} = await this.$util.getUtilLocation(false)
					if (lat && lng) {
						this.toChangeLocation({
							lat,
							lng
						})
					}
				}
			},
			async toHidePage(isHide = 1) {
				let {
					lat,
					lng
				} = await this.$util.getUtilLocation(false)
				if (lat && lng) {
					this.toChangeLocation({
						lat,
						lng
					}, isHide)
				}
			},
			async toChangeLocation(param, initDistance = 0) {
				let {
					lat,
					lng
				} = param
				let {
					status: coach_status = 0,
					coach_position = 0,
					lat: coach_lat,
					lng: coach_lng
				} = this.userCoachStatus
				if (coach_position && coach_status == 2) {
					let distance = this.$util.getDistance(lat, lng, coach_lat, coach_lng)
					let changeOnAddr = Object.assign({}, param, {
						unix: this.$util.DateToUnix(this.$util.formatTime(new Date(), 'YY-M-D h:m:s')),
						initDistance,
						distance
					})
					this.updateUserItem({
						key: 'changeOnAddr',
						val: changeOnAddr
					})
					let max = 150
					if (initDistance == 1 || distance * 1 >= max) {
						if (this.lockTap) return
						this.lockTap = true
						try {
							await this.$util.getMapInfo(false, {
								lat,
								lng
							})
							this.lockTap = false
						} catch (e) {
							this.lockTap = false
						}
					}
				}
			},
			// 轨迹
			async toAddCoachTravel() {
				let {
					coach_travel_order_id
				} = this
				let {
					coachtravel
				} = this.configInfo.plugAuth
				let {
					id: uid
				} = this.userInfo
				if (this.travelTimer) clearInterval(this.travelTimer)
				if (coachtravel && coach_travel_order_id && uid) {
					this.toConfirmAddCoachTravel(coach_travel_order_id)
					this.travelTimer = setInterval(() => {
						this.toConfirmAddCoachTravel(coach_travel_order_id)
					}, 5000)
				}
			},
			async toConfirmAddCoachTravel(order_id) {
				let {
					lat,
					lng
				} = await this.$util.getUtilLocation(false)
				if (lat && lng) {
					await this.$api.technician.coachTrajectoryAdd({
						order_id,
						text: {
							lat,
							lng,
							address: ''
						}
					})
				}
			},
			async mergeLocaleMessage() {
				let zh = JSON.parse(JSON.stringify(this.$i18n.messages.zh))
				let {
					attendant_name: name,
					channel_menu_name: cName
				} = this.configInfo
				let {
					old_attendant_name: oldName,
					old_channel_menu_name: oldCName
				} = this
				let reg = new RegExp(oldName, 'g')
				let cReg = new RegExp(oldCName, 'g')
				for (let i in zh.action) {
					if (zh.action[i].includes(oldName)) {
						zh.action[i] = zh.action[i].replace(reg, name)
					}
					if (zh.action[i].includes(oldCName)) {
						zh.action[i] = zh.action[i].replace(cReg, cName)
					}
				}
				this.$i18n.mergeLocaleMessage('zh', zh)
				this.updateConfigItem({
					key: 'old_attendant_name',
					val: name
				})
				this.updateConfigItem({
					key: 'old_channel_menu_name',
					val: cName
				})
			}
		}
	}
</script>

<style lang="scss">
	@import "./styles/index.scss";

	/* #ifdef H5 */
	uni-page-head {
		display: none;
	}

	/* #endif */
	.map-box .csssprite {
		border: 2px solid #fff !important;
		box-sizing: border-box;
		box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.1);
		border-radius: 50% !important;
	}

	.map-box a div .csssprite {
		display: none;
	}

	page {
		font-size: 28rpx;
		color: #222;
		line-height: 1.5;
		background: #F6F6F6;
		font-family: -apple-system-font, Helvetica Neue, Helvetica, sans-serif;
	}

	input {
		// font-family: PingFangSC-Medium, PingFang SC, -apple-system-font, Helvetica Neue, Helvetica, sans-serif;
	}

	input::-webkit-input-placeholder {
		/* WebKit browsers */
		color: #A9A9A9;
	}

	input:-moz-placeholder {
		/* Mozilla Firefox 4 to 18 */
		color: #A9A9A9;
	}

	input::-moz-placeholder {
		/* Mozilla Firefox 19+ */
		color: #A9A9A9;
	}

	input:-ms-input-placeholder {
		/* Internet Explorer 10+ */
		color: #A9A9A9;
	}

	view {
		box-sizing: border-box;
	}

	image {
		display: block;
	}

	.h5-image {
		background-position: center center;
		background-size: cover;
		background-repeat: no-repeat;
	}

	/*隐藏滚动条*/
	::-webkit-scrollbar {
		width: 0;
		height: 0;
		color: transparent;
	}

	/* #ifdef MP-BAIDU */
	.swan-button.swan-button-radius-ios {
		border-radius: 0;
	}

	/* #endif */
</style>