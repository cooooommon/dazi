<template>
	<view style="width:100%">
		<block v-if="needAuth">
			<view @tap.stop="toUserFrom(0)">
				<slot></slot>
			</view>
		</block>
		<block v-else>
			<view @tap.stop="toUserFrom(1)">
				<slot></slot>
			</view>
		</block>

		<uni-popup ref="show_auth_item" :maskClick="false">
			<view @tap.stop.prevent class="auth-box fill-base flex-column flex-center text-center radius-26">
				<view class="space-md"></view>
				<block v-if="pType == 'phone'">
					<image mode="aspectFill" class="auth-img" :src="`https://lbqny.migugu.com/admin/public/auth.png`">
					</image>
				</block>
				<view class="space-sm"></view>
				<view class="f-caption" :style="{color:primaryColor}">{{contentList[pType][0]}}</view>
				<view class="space-lg"></view>
				<view class="space-lg"></view>
				<block v-if="pType === 'phone'">
					<!-- #ifdef MP-WEIXIN -->
					<button hover-class="btn-hover" @tap.stop="authWxLogin"
						class="clear-btn flex-center auth-btn" :style="{backgroundColor:primaryColor,color:'white'}">
						{{btn_text||'微信一键登录'}}
					</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<button open-type="getPhoneNumber" hover-class="btn-hover" @getphonenumber="authPhone"
						class="clear-btn flex-center auth-btn" :style="{backgroundColor:primaryColor,color:'white'}">
						{{btn_text||contentList[pType][1]}}
					</button>
					<!-- #endif -->
				</block>
				<block v-if="pType === 'setting'">
					<button open-type="openSetting" hover-class="btn-hover" @opensetting="openSetting"
						class="clear-btn flex-center auth-btn" :style="{backgroundColor:primaryColor,color:'white'}">
						{{btn_text||contentList[pType][1]}}
					</button>
				</block>
				<view @tap.stop="go(pType == 'phone' &&  !userInfo.phone ? 2 :  1)" class="f-caption c-caption mt-md"
					v-if="!pMust">{{pType=='userInfo'?'暂不授权':'暂不登录'}}</view>
				<view class="space-md"></view>
			</view>
		</uni-popup>


		<uni-popup ref="show_info_item" :maskClick="false">
			<view @tap.stop.prevent class="common-popup-content popup-phone pd-lg flex-center flex-column fill-base">
				<view class="f-md-title c-black">请填写用户信息</view>
				<view class="space-lg pb-lg"></view>
				<view class="space-lg pb-lg"></view>
				<view class="flex-between" style="width:100%">
					<view class="flex-y-center c-title">
						<view class="c-warning">*</view>昵称
					</view>
					<!-- #ifdef MP-WEIXIN -->
					<input v-model="infoForm.nickName" type="nickname" maxlength="15"
						class="f-mini-title c-title text-right" style="width:80%" placeholder-class="c-placeholder"
						:placeholder="infoRule[0].errorMsg" />
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<input v-model="infoForm.nickName" type="text" maxlength="15"
						class="f-mini-title c-title text-right" style="width:80%" placeholder-class="c-placeholder"
						:placeholder="infoRule[0].errorMsg" />
					<!-- #endif -->
				</view>
				<view class="flex-between mt-md pt-md b-1px-t" style="width:100%">
					<view class="flex-y-center c-title">
						<view class="c-warning">*</view>头像
					</view>
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
						<image class="avatar radius" :src="infoForm.avatarUrl"></image>
					</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<image @tap.stop="toChooseImg" class="avatar radius" :src="infoForm.avatarUrl"></image>
					<!-- #endif -->
				</view>
				<view class="button">
					<view @tap.stop="$refs.show_info_item.close()" class="item-child">
						取消
					</view>
					<view @tap.stop="submit('info')" class="item-child"
						:style="{background: primaryColor,color:'#fff'}">
						确定
					</view>
				</view>
			</view>
		</uni-popup>

		<uni-popup ref="show_phone_item" :maskClick="false">
			<view @tap.stop.prevent class="common-popup-content popup-phone pd-lg flex-center flex-column fill-base">
				<view class="f-md-title c-black">请输入手机号</view>
				<view class="space-lg pb-lg"></view>
				<view class="space-lg pb-lg"></view>
				<view class="flex-center mb-lg">
					<view class="input-info sm mr-md radius-16">
						<input v-model="subForm.phone" type="number"
							class="item-input flex-y-center pl-lg pr-lg f-sm-title c-title"
							placeholder-class="c-placeholder" :placeholder="subRule[0].errorMsg" />
					</view>
					<view @tap="toSend" class="send-btn flex-center c-base radius-16"
						:style="{background:primaryColor}">
						{{authTime>0?`(${authTime}s)`:'发送'}}
					</view>
				</view>
				<view class="input-info radius-16">
					<input v-model="subForm.short_code" type="number"
						class="item-input flex-y-center pl-lg pr-lg f-sm-title c-title" maxlength="6"
						placeholder-class="c-placeholder" :placeholder="subRule[1].errorMsg" />
				</view>
				<view class="button">
					<view @tap.stop="go(3)" class="item-child" v-if="configInfo.user_force_login != 1">
						取消
					</view>
					<view @tap.stop="submit('sub')" class="item-child" :style="{background: primaryColor,color:'#fff'}">
						确定
					</view>
				</view>
			</view>
		</uni-popup>
		<!--用户来源-->
		<uni-popup ref="show_user_from" type="bottom" :maskClick="true" :zIndex="999">
			<view class="popup-user-from fill-base pl-lg pr-lg">
				<view class="flex-center text-bold f-ms-title user-from-title rel c-black">来源调研
				<text class="f-paragraph c-paragraph abs user-from-close" @tap="$refs.show_user_from.close()">取消</text></view>
				<view class="f-mini-title text-bold pt-sm pb-lg flex-y-center c-black">
					<i class="iconfont icon-required c-warning" style="font-size: 15px;"></i>
					<text>您是从哪个平台知道的我们？</text>
				</view>
				<picker @change="pickerChange($event,'from_type')" :value="fromTypeIndex"
					:range="fromTypeList" range-key="title">
					<view class="flex-between from-input fill-body pl-lg pr-lg c-black f-paragraph">
						{{ fromTypeIndex !== -1 ? fromTypeList[fromTypeIndex].title : '请选择'}}
						<i class="iconfont icon-right ml-sm rotate-90 text-bold" style="font-size: 28rpx;color: #BEC3CE;"></i>
					</view>
				</picker>
				<view class="f-mini-title text-bold pb-lg flex-y-center c-black" style="padding-top: 55rpx;">
					<i class="iconfont icon-required c-warning" style="font-size: 15px;"></i>
					<text>您是否有达人推荐？</text>
				</view>
				<view class="flex-y-center">
					<block v-for="(item,index) in [{title: '没有', id: 0}, {title: '有', id: 1}]" :key="index">
						<view class="flex-center" style="margin-right: 120rpx;" @tap="toShowType(item.id)">
							<i class="iconfont" :style="{color: userFrom.type==item.id?primaryColor:'#BEC3CE'}" style="font-size: 20px;"
							:class="[{'icon-xuanze-fill':userFrom.type==item.id},{'icon-xuanze':userFrom.type!=item.id}]"></i>
							<text class="pl-md c-title f-paragraph">{{item.title}}</text>
						</view>
					</block>
				</view>
				<block v-if="userFrom.type">
					<view class="f-mini-title text-bold pb-lg" style="padding-top: 55rpx;">达人账号</view>
					<view class="fill-body from-input flex-center">
						<input v-model="userFrom.from_name" class="from-input flex-1 pl-lg pr-lg" type="text" placeholder="请输入达人账号"/>
					</view>
				</block>
				<view class="flex-center ml-lg mr-lg user-from-btn c-base text-bold radius-16" :style="{background: primaryColor}" @tap="userFromConfirm">提交</view>
				<view class="space-safe"></view>
			</view>
		</uni-popup>


	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations,
	} from "vuex"
	import {
		wxLogin
	} from "@/utils/req.js"
	export default {
		components: {},
		name: 'auth',
		props: {
			needAuth: {
				type: Boolean,
				default () {
					return false
				}
			},
			must: {
				type: Boolean,
				default () {
					return false
				}
			},
			userMust: {
				type: Boolean,
				default () {
					return true
				}
			},
			showAuth: {
				type: Boolean,
				default () {
					return false
				}
			},
			type: {
				type: String,
				default () {
					return 'phone'
				}
			},
			btn_text: {
				type: String,
				default () {
					return ''
				}
			},
			haveGo: {
				type: Boolean,
				default () {
					return true
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
			mineInfo: state => state.user.mineInfo,
		}),
		data() {
			return {
				contentList: {
					userInfo: ['尊贵的用户，获取授权是为了能更好的为你服务', '立即授权'],
					phone: ['尊贵的用户，登录后我们才能更好的为你服务', '立即登录'],
					setting: ['为了功能正常使用，你需要打开设置并开启获取相应权限', '打开设置'],
				},
				pType: '',
				pMust: '',
				authTime: 0,
				timer: null,
				subForm: {
					phone: '',
					short_code: ''
				},
				subRule: [{
					name: "phone",
					checkType: "isMobile",
					errorMsg: "请输入手机号",
					regText: "手机号"
				}, {
					name: "short_code",
					checkType: "isNotNull",
					errorMsg: "请输入短信验证码"
				}],
				infoForm: {
					nickName: '',
					avatarUrl: ''
				},
				infoRule: [{
					name: "nickName",
					checkType: "isNotNull",
					errorMsg: "请输入用户昵称",
					regType: 2
				}, {
					name: "avatarUrl",
					checkType: "isNotNull",
					errorMsg: "请上传用户头像"
				}],
				lockTap: false,
				userFrom: {
					from_type: '', // 来源 11 抖音 12 视频号 13 小红书 14 其他
					from_name: '',
					type: 0
				},
				fromTypeIndex: -1,
				fromTypeList: [{
					title: '抖音', 
					id: 11
				},{
					title: '视频号', 
					id: 12
				},{
					title: '小红书', 
					id: 13
				},{
					title: '其他', 
					id: 14
				}],
				userFromRule: [{
					name: "from_type",
					checkType: "isNotNull",
					errorMsg: "请选择来源"
				}, {
					name: "from_name",
					checkType: "isNotNull",
					errorMsg: "请输入达人账号"
				}],
			}
		},
		created() {
			this.init();
		},
		methods: {
			...mapActions(['getUserInfo', 'getMineInfo', 'getAuthUserProfile', 'getAuthPhone', ]),
			...mapMutations(['updateConfigItem', 'updateUserItem']),
			init() {
				let {
					type,
					must,
					showAuth
				} = this
				
				this.$set(this, 'pType', type)
				this.$set(this, 'pMust', must)
				
				if (!showAuth) return
				let refs_key = type === 'userInfo' ? 'show_info_item' : 'show_auth_item'
				this.$refs[refs_key].open()
			},
			async userFromConfirm(){
				let msg = this.validate(this.userFrom, 'userFrom', true);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				await this.$api.user.updateFrom(this.userFrom)
				this.$util.showToast({
					title: '提交成功'
				});
				this.$refs.show_user_from.close()
				setTimeout(()=>{
					this.getMineInfo()
					this.getUserInfo()
					this.go(1)
				},500)
			},
			pickerChange(e, type){
				let {
					value = 0
				} = e.detail
				this.fromTypeIndex = value
				this.userFrom[type] = this.fromTypeList[value].id
			},
			toShowType(id){
				this.userFrom.type = id
			},
			async toUserFrom(index){
				let {
					user_from_switch = 0
				} = this.configInfo
				
				if(user_from_switch && this.userInfo.from_type == 1 && this.userInfo && this.userInfo.nickName && this.must){
					this.$refs.show_user_from.open()
					return
				}
				if(index){
					this.go(index)
				}else{
					this.toShowAuth()
				}
			},
			toShowAuth() {
				let {
					id: uid = 0,
					phone = '',
				} = this.userInfo
				let {
					short_code_status = 0,
					bind_phone_type = 0,
				} = this.configInfo
				if (!uid || (short_code_status && bind_phone_type && !phone)) {
					// this.updateUserItem({
					// 	key: 'loginPage',
					// 	val: `/pages/mine?type=1`
					// })
					// this.$util.goUrl({
					// 	url: `/pages/login`
					// })
					let pages = getCurrentPages()
					let {
						route,
						options = {}
					} = pages[pages.length - 1]
					let loginPage = this.$util.getUrlToStr(`/${route}`, options)
					this.updateUserItem({
						key: 'loginPage',
						val: loginPage
					})
					this.$util.goUrl({
						url: !uid ? `/pages/login` : `/user/pages/phone`
					})
					return
				}
				this.infoForm = this.$util.pick(this.userInfo, ['nickName',
					'avatarUrl'
				])
				let type = !phone ? 'phone' : 'userInfo'
				this.$set(this, 'pType', type)
				let refs_key = 'show_info_item'
				if (!phone) {
					// #ifdef MP-WEIXIN
					// 微信登录后不再弹手机号授权：未填昵称先补资料，已填直接放行
					if (!this.userInfo.nickName) {
						this.$set(this, 'pType', 'userInfo')
						this.$set(this, 'pMust', this.userMust)
						if (!this.pMust) return
						this.$refs.show_info_item.open()
					} else {
						this.go(1)
					}
					return
					// #endif
					// #ifndef MP-WEIXIN
					if (this.haveGo) {
						refs_key = 'show_phone_item'
						let {
							short_code_status = 0
						} = this.configInfo
						if (!short_code_status) {
							this.go(1)
							return
						}
					}
					// #endif
				}
				this.$refs[refs_key].open()
			},
			// 微信一键登录
			async authWxLogin() {
				if (this.lockTap) return
				this.lockTap = true
				try {
					let {
						id: uid = 0
					} = this.userInfo
					if (!uid) await wxLogin()
					let {
						nickName = ''
					} = this.userInfo
					if (nickName) {
						this.go(1)
						return
					}
					this.$set(this, 'pType', 'userInfo')
					this.$set(this, 'pMust', this.userMust)
					if (!this.pMust) return
					this.$refs.show_info_item.open()
				} catch (e) {
				} finally {
					this.lockTap = false
				}
			},
			// 授权手机号
			async authPhone(e) {
				let {
					pMust
				} = this
				let phone = await this.getAuthPhone({
					e,
				})
				if (!phone) {
					this.go(pMust ? 2 : 1)
					return false
				} else {
					let {
						nickName = '',
					} = this.userInfo
					if (nickName) {
						this.go(1)
						return
					}
					this.$set(this, 'pType', 'userInfo')
					this.$set(this, 'pMust', this.userMust)
					if (!this.pMust) return
					this.$refs.show_info_item.open()
				}
			},
			go(type = 1) {
				this.lockTap = false
				this.$emit(type == 1 ? 'go' : 'hide')
				let refs_key = type == 3 ? 'show_phone_item' : type == 4 ? 'show_info_item' : 'show_auth_item'
				let {
					id: uid = 0
				} = this.userInfo
				if (uid) {
					this.$refs[refs_key].close();
				}
				if (type != 3) return
				this.toResetItem('sub')
			},
			// 重置数据
			toResetItem(type) {
				if (type == 'sub') {
					this.timer && clearTimeout(this.timer)
					this.authTime = 0
					this.subForm = {
						phone: '',
						short_code: ''
					}
					return
				}
				this.infoForm = this.$util.pick(this.userInfo, ['nickName',
					'avatarUrl'
				])
			},
			//表单验证
			validate(param, ruleType, is_send = false) {
				let validate = new this.$util.Validate();
				this[`${ruleType}Rule`].map(item => {
					let {
						name,
					} = item
					if (ruleType == 'sub' && name == 'short_code' && is_send) return
					if (ruleType == 'userFrom' && name == 'from_name' && param.type == 0) return
					validate.add(param[name], item);
				})
				let message = validate.start();
				return message;
			},
			// 发送验证码
			async toSend() {
				let {
					authTime
				} = this
				if (authTime) return
				let {
					phone = ''
				} = this.subForm
				let msg = this.validate({
					phone
				}, 'sub', true);
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
					await this.$api.user.sendShortMsg({
						phone
					})
					this.$util.hideAll()
					this.lockTap = false
					let time = 60
					this.timer = setInterval(() => {
						if (time === 0) {
							clearInterval(this.timer)
							return
						}
						time--
						this.authTime = time
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
			// 获取头像
			onChooseAvatar(e) {
				let {
					avatarUrl
				} = e.detail
				this.infoForm.avatarUrl = avatarUrl
			},
			async toChooseImg() {
				let param = {
					count: 1,
					sizeType: ['compressed'],
					sourceType: ['album'],
				}
				let [res_upload, res_info] = await uni.chooseImage(param)
				if (res_upload) return
				let {
					size = 0,
						tempFiles,
						tempFilePath = ''
				} = res_info
				// 格式化图片参数
				this.$util.showLoading({
					title: "上传中"
				})
				let {
					attachment_path: path
				} = await this.$api.base.uploadFile({
					filePath: tempFiles[0].path,
					formData: {
						type: 'picture'
					}
				})
				this.infoForm.avatarUrl = path
				this.$util.hideAll()
			},
			// sub 授权手机号，info 用户信息
			async submit(formType) {
				let param = this.$util.deepCopy(this[`${formType}Form`])
				let msg = this.validate(param, formType);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}

				if (formType == 'sub' && param.short_code.length != 6) {
					this.$util.showToast({
						title: `请输入6位数短信验证码`
					})
					return
				}

				if (formType == 'info' && (param.avatarUrl.includes('wxfile://') || param.avatarUrl.includes(
						'//tmp/'))) {
					let {
						attachment_path: path
					} = await this.$api.base.uploadFile({
						filePath: param.avatarUrl,
						formData: {
							type: 'picture'
						}
					})
					param.avatarUrl = path
				}

				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				let methodModel = formType == 'sub' ? 'bindUserPhone' : 'userUpdate'
				let refs_key = formType == 'sub' ? 'show_phone_item' : 'show_info_item'
				try {
					await this.$api.user[methodModel](param)
					this.$util.hideAll()
					this.lockTap = false
					this.$refs[refs_key].close()
					this.toResetItem(formType)
					await this.getUserInfo()
					setTimeout(() => {
						this.$emit('go')
					}, 500)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			}
		},
	}
</script>



<style lang="scss">
	.auth-box {
		width: 630rpx;
		height: auto;
		padding: 30rpx;

		.auth-img {
			width: 322rpx;
			height: 341rpx;
			/* background: #f4f6f8; */
		}

		.auth-btn {
			width: 367rpx;
			height: 90rpx;
			border-radius: 90rpx;
		}

		.auth-info {
			width: 80rpx;
			height: 80rpx;
			background: #f4f6f8;
			border-radius: 50%;
		}

	}


	.popup-phone {
		width: 630rpx;

		.input-info {
			width: 570rpx;
			height: 90rpx;
			background: #F7F7F7;

			.item-input {
				height: 90rpx;
				font-size: 32rpx;
				text-align: left;
			}
		}

		.input-info.sm {
			width: 400rpx;
		}

		.send-btn {
			width: 150rpx;
			height: 90rpx;
		}
	}
	
	.popup-user-from{
		border-radius: 30rpx 30rpx 0rpx 0rpx;
		padding-bottom: 14rpx;
		.user-from-title{
			height: 132rpx;
		}
		.from-input{
			height: 88rpx;
			border-radius: 8rpx;
		}
		.user-from-close{
			right: 0;
		}
		.user-from-btn{
			height: 86rpx;
			margin-top: 54rpx;
		}
	}
</style>
