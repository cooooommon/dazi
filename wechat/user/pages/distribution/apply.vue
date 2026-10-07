<template>
	<view class="apply-pages" v-if="isLoad">
		<view class="apply-form">
			<view class="fill-base mt-md radius-16">
				<view class="flex-between pl-lg pr-lg b-1px-b">
					<view class="item-text">您的姓名</view>
					<input v-model="form.user_name" type="text" class="item-input flex-1" maxlength="20"
						:placeholder="rule[0].errorMsg" />
				</view>
				<view class="flex-between pl-lg pr-lg b-1px-b">
					<view class="item-text">手机号码</view>
					<input v-model="form.mobile" type="text" class="item-input flex-1"
						:placeholder="rule[1].errorMsg" />
				</view>
			</view>
			<view class="fill-base mt-md radius-16">
				<view class="flex-between pl-lg pr-lg">
					<view class="item-text">备注信息</view>
					<input :disabled="true" type="text" class="item-input flex-1" />
				</view>
				<textarea v-model="form.text" class="item-textarea pd-lg" maxlength="300" placeholder="输入备注信息" />
				<view class="text-right pb-lg pr-lg">
					{{form.text.length>300?300:form.text.length}}/300
				</view>
			</view>
		</view>

		<view class="space-max-footer"></view>

		<fix-bottom-button @confirm="submit" :text="[{text:'确定申请',type:'confirm',isAuth:true}]" bgColor="#fff">
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
		components: {},
		data() {
			return {
				cityIndex: 0,
				cityList: [],
				isLoad: false,
				options: {},
				form: {
					id: 0,
					user_name: '', //姓名 
					mobile: '', //手机号  
					text: '', //备注 
					pid: 0
				},
				rule: [{
						name: "user_name",
						checkType: "isNotNull",
						errorMsg: "请输入您的真实姓名",
						regType: 2
					},
					{
						name: "mobile",
						checkType: "isMobile",
						errorMsg: "请输入常用手机号码"
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

		}),
		async onLoad(options) {
			this.$util.showLoading()
			this.options = await this.updateCommonOptions(options)
			this.form.pid = this.options.pid || 0
			await this.initIndex()
			this.isLoad = true
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'updateCommonOptions']),
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
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				let data = await this.$api.mine.resellerInfo()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				if (!data.id) {
					this.$util.hideAll()
					return
				}
				for (let key in this.form) {
					this.form[key] = data[key]
				}
				this.$util.hideAll()
			},
			initRefresh() {
				this.initIndex(true)
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
				param.text = param.text.length > 300 ? param.text.substring(0, 300) : param.text
				try {
					await this.$api.mine.applyReseller(param)
					this.$util.hideAll()
					this.$util.showToast({
						title: `提交成功`
					});
					setTimeout(() => {
						if (getCurrentPages().length > 1) {
							this.$util.back()
						}
						this.$util.goUrl({
							url: '/user/pages/apply-result?type=2',
							openType: `redirectTo`
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
