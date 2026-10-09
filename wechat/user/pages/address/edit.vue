<template>
	<view class="apply-pages" v-if="isLoad">
		<view class="apply-form pl-lg pr-lg">
			<view class="fill-base mt-md radius-16">
				<view class="flex-between ml-lg mr-lg b-1px-b">
					<view class="item-text">联系人</view>
					<input v-model="form.user_name" type="text" class="item-input flex-1" maxlength="20"
						:placeholder="rule[0].errorMsg" />
				</view>
				<view class="flex-between ml-lg mr-lg">
					<view class="item-text">手机号</view>
					<input v-model="form.mobile" type="text" class="item-input flex-1"
						:placeholder="rule[1].errorMsg" />
				</view>
			</view>
			<view class="fill-base mt-md radius-16">
				<view class="flex-between ml-lg mr-lg b-1px-b">
					<view class="item-text">选择地区</view>
					<view @tap.stop="toChooseLocation" class="item-input text flex-1">
						<view class="flex-y-center text-right">
							<view class="flex-1 text-right">{{form.address || `点击右边图标设置`}}</view><i
								class="iconfont iconjuli ml-sm" :style="{color: primaryColor}"></i>
						</view>
					</view>
				</view>
				<view class="flex-between ml-lg mr-lg">
					<view class="item-text">详细地址</view>
					<input v-model="form.address_info" type="text" class="item-input flex-1"
						:placeholder="rule[3].errorMsg" />
				</view>
			</view>
			<view class="fill-base mt-md radius-16">
				<view class="flex-between ml-lg mr-lg">
					<view class="item-text flex-1" style="width: 300rpx;">设为默认地址</view>
					<!-- status 1默认； -->
					<view @tap.stop="form.status = form.status ==1 ? 0 : 1" class="item-input">
						<i class="iconfont icon-switch" :class="[{'icon-switch-on':form.status==1}]"
							:style="{color:form.status==1? primaryColor:'#f2f2f2'}"></i>
					</view>
				</view>
			</view>
			<view class="f-icontext c-caption mt-md"> 设置后，下单时优先展示该地址</view>
		</view>

		<view class="space-max-footer"></view>

		<fix-bottom-button @cancel="toDel" @confirm="submit" :text="textArr" bgColor="#fff"></fix-bottom-button>

	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		data() {
			return {
				isLoad: false,
				options: {},
				textArr: [{
					text: '删除',
					type: 'cancel'
				}, {
					text: '保存',
					type: 'confirm'
				}],
				form: {
					id: 0,
					user_name: '',
					mobile: '',
					address: '',
					address_info: '',
					lng: '',
					lat: '',
					status: 0
				},
				rule: [{
						name: "user_name",
						checkType: "isNotNull",
						errorMsg: "输入联系人姓名",
						regType: 2
					}, {
						name: "mobile",
						checkType: "isMobile",
						errorMsg: "输入手机号"
					},
					{
						name: "address",
						checkType: "isNotNull",
						errorMsg: "设置地区"
					},
					{
						name: "address_info",
						checkType: "isNotNull",
						errorMsg: "输入街道、楼牌号等",
						regType: 2
					},
				],
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			location: state => state.user.location,
		}),
		onLoad(options) {
			this.options = options
			this.initIndex()
		},
		methods: {
			...mapMutations(['updateUserItem', 'updateOrderItem']),
			async initIndex() {
				// #ifdef H5
				if (this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				let {
					id = 0
				} = this.options
				uni.setNavigationBarTitle({
					title: id ? `编辑地址` : `添加新地址`
				})
				if (!id) {
					this.textArr.splice(0, 1)
					this.isLoad = true
					this.$util.setNavigationBarColor({
						bg: this.primaryColor
					})
					return
				}
				let data = await this.$api.mine.addressInfo({
					id
				})
				for (let key in this.form) {
					this.form[key] = data[key]
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.isLoad = true
			},
			// 选择地区
			async toChooseLocation(e) {
				await this.$util.checkAuth({
					type: 'userLocation'
				})
				let {
					lat: locaLat = '',
					lng: locaLng = ''
				} = this.location
				let {
					id = 0,
						lat: addrLat,
						lng: addrLng
				} = this.form

				if (id) {
					locaLat = addrLat
					locaLng = addrLng
				}

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
					latitude,
					name
				}] = await uni.chooseLocation(param);
				if (!address) return
				this.form.address = address
				this.form.lng = longitude
				this.form.lat = latitude
			},
			async toDel() {
				let [res_del, {
					confirm
				}] = await uni.showModal({
					title: '提示',
					content: "请确认是否要删除此数据?"
				})
				if (!confirm) return;
				let {
					id
				} = this.form
				let {
					check_id
				} = this.$util.getPage(-1)
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.mine.addressDel({
						id
					})
					this.$util.hideAll()
					this.$util.showToast({
						title: `删除成功`,
					});
					this.lockTap = false
					if (id == check_id) {
						this.updateOrderItem({
							key: 'haveOperItem',
							val: true
						})
					}
					setTimeout(() => {
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: `navigateBack`
						})
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
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
			async submit() {
				let param = this.$util.deepCopy(this.form)
				let msg = this.validate(param);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					let methodModel = param.id ? `addressUpdate` : `addressAdd`
					await this.$api.mine[methodModel](param)
					this.$util.hideAll()
					this.$util.showToast({
						title: `保存成功`,
					});
					setTimeout(() => {
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: `navigateBack`
						})
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			}
		}
	}
</script>


<style lang="scss">
</style>
