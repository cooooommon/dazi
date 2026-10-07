<template>
	<view class="dynamic-technician-add">
		<view class="pd-lg">
			<view class="pb-md" :class="[{'flex-warp': subForm.type == 2 }]">
				<view class="mr-md" v-if="subForm.type == 2">
					<upload @upload="imgUpload" @del="imgUpload" :imagelist="subForm.cover" filetype="picture"
						imgtype="cover" text="上传封面" :imgsize="1">
					</upload>
				</view>
				<upload @upload="imgUpload" @del="imgUpload" :imagelist="subForm.imgs"
					:filetype="subForm.type == 1 ? 'picture' : 'video'" imgtype="imgs"
					:text="subForm.type == 1 ? '上传图片' : '上传视频'" :imgsize="subForm.type == 1 ? 9 : 1">
				</upload>
			</view>
			<view class="space-md"></view>
			<input v-model="subForm.title" class="f-mini-title c-title" type="text" placeholder="填写标题有更多赞哟~"
				placeholder-class="f-mini-title c-caption" maxlength="10" />
			<view class="space-lg b-1px-b"></view>
			<view class="space-lg"></view>
			<textarea v-model="subForm.text" class="textarea f-desc c-title" maxlength="400"
				placeholder="还有更多的想法也一起写到正文吧~" placeholder-class="f-desc c-caption"></textarea>
		</view>
		<view @tap.stop="toChooseLocation" class="pt-lg pl-lg pr-md flex-center f-desc">
			<i class="iconfont icon-dingwei"></i>
			<view class="flex-1 ml-sm mr-lg c-title ellipsis-2">
				{{subForm.lat ? subForm.address : '添加地点'}}
			</view>
			<i class="iconfont icongengduo"></i>
		</view>
		<view class="space-max-footer"></view>
		<fix-bottom-button @confirm="submit" :text="[{text:subForm.id?'编辑动态':'发布动态',type:'confirm'}]" bgColor="#fff">
		</fix-bottom-button>
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
				options: {},
				subForm: {
					id: 0,
					cover: [],
					imgs: [],
					type: 1,
					title: '',
					text: '',
					lat: '',
					lng: '',
					address: ''
				},
				rule: [{
						name: "cover",
						checkType: "isNotNull",
						errorMsg: "请上传封面图"
					}, {
						name: "imgs",
						checkType: "isNotNull",
						errorMsg: ""
					}, {
						name: "title",
						checkType: "isNotNull",
						errorMsg: "请输入标题",
						regType: 2
					},
					{
						name: "text",
						checkType: "isNotNull",
						errorMsg: "请输入正文内容",
						regType: 2
					},
					{
						name: "address",
						checkType: "isNotNull",
						errorMsg: "请选择定位地点"
					}
				],
				lockTap: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			haveShieldOper: state => state.user.haveShieldOper,
		}),
		onLoad(options) {
			let {
				type
			} = options
			type = type * 1
			this.options = options
			this.subForm.type = type
			this.rule[1].errorMsg = type == 1 ? '请上传图片' : '请上传视频'
			this.initIndex()
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
		},
		methods: {
			...mapMutations(['updateUserItem']),
			async initIndex() {
				let {
					id = 0
				} = this.options
				if (!id) return
				let data = await this.$api.dynamic.coachDynamicInfo({
					id
				})
				data.cover = [{
					path: data.cover
				}]
				data.imgs = data.imgs.map(item => {
					return {
						path: item
					}
				})
				for (let key in this.subForm) {
					this.subForm[key] = data[key]
				}
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.subForm[imgtype] = imagelist;
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
				} = this.subForm
				
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
					let location = await this.$util.getBmapLocation()
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
					longitude: lng,
					latitude: lat,
				}] = await uni.chooseLocation(param);
				if (!lng) return
				this.subForm.address = address
				this.subForm.lat = lat
				this.subForm.lng = lng
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
				let subForm = this.$util.deepCopy(this.subForm)
				subForm.imgs = subForm.imgs && subForm.imgs.length > 0 ? subForm.imgs.map(item => {
					return item.path
				}) : []
				let url = subForm.type == 1 ? subForm.imgs.length > 0 ? subForm.imgs[0] : '' : subForm.cover && subForm
					.cover.length > 0 ? subForm.cover[0].path : ''
				subForm.cover = url
				let msg = this.validate(subForm);
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
					let methodModel = subForm.id ? 'dynamicUpdate' : 'dynamicAdd'
					let {
						status = 1
					} = await this.$api.dynamic[methodModel](subForm)
					this.$util.hideAll()
					this.$util.showToast({
						title: status == 1 ? '正在审核中' : subForm.id ? `编辑成功` : `发布成功`
					})
					this.updateUserItem({
						key: 'haveShieldOper',
						val: 1
					})
					setTimeout(() => {
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: `navigateBack`
						})
					}, 2000)
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
	page {
		background: #fff;
	}

	.dynamic-technician-add {
		.icon-dingwei {
			font-size: 28rpx;
		}

		.icongengduo {
			font-size: 26rpx;
			color: #9B9B9B;
		}

		.textarea {
			width: 690rpx;
		}
	}
</style>
