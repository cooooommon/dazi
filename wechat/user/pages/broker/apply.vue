<template>
	<view class="apply-pages" v-if="isLoad">
		<view class="page-height fill-base" v-if="[1, 4].includes(status)">
			<abnor percent="150%" @confirm="abnorConfirm" @cancel="$util.goUrl({ url: 1, openType: `navigateBack` })"
				:title="title[status]" :tip="tipArr[status]" :button="buttonArr[status]"
				:image="image[status]" :tipMax="false"></abnor>
		</view>
		<block v-else>
			<view class="apply-form">
				<view class="pt-lg pb-sm f-title text-bold pl-lg">填写经纪人资格申请表单</view>
				<view class="fill-base mt-md radius-16">
					<view class="flex-between pl-lg pr-lg b-1px-b">
						<view class="item-text">您的姓名</view>
						<input v-model="form.name" type="text" class="item-input flex-1" maxlength="10"
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
		components: {},
		data() {
			return {
				cityIndex: 0,
				cityList: [],
				isLoad: false,
				options: {},
				form: {
					id: 0,
					name: '', //姓名 
					mobile: '', //手机号  
					text: '', //备注 
					pid: 0
				},
				rule: [{
						name: "name",
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
				lockTap: false,
				// -1未申请，1审核中，2审核通过，3取消授权，4审核失败(可再次申请)
				tipArr: {
					'1': [{
						text: '您已经成功提交申请，',
						text1: '审核将在 3 个工作日内出结果，请耐心等待。',
						color: 0
					}],
					'2': [{
						text: '快去管理订单吧',
						color: 0
					}],
					'3': [{
						text: '您的申请被驳回，您可以修改资料后重新申请',
						color: 0
					}],
					'4': [{
						text: '您的申请被驳回，',
						text1: '您可以修改资料后重新申请',
						color: 0
					}]
				},
				buttonArr: {
					'1': [{
						text: '返回',
						type: 'cancel'
					}],
					'2': [{
						text: '去管理',
						type: 'confirm'
					}],
					'4': [{
						text: '再次申请',
						type: 'confirm'
					}, {
						text: '返回',
						type: 'cancel'
					}]
				},
				title: {
					'1': '审核中',
					'2': '审核通过',
					'4': '审核驳回'
				},
				image: {
					'1': 'https://lbqny.migugu.com/admin/public/apply_wait.jpg',
					'2': 'https://lbqny.migugu.com/admin/public/apply_suc.jpg',
					'4': 'https://lbqny.migugu.com/admin/public/apply_fail.jpg'
				},
				status: 0, // 1 审核中 2通过 3取消  4驳回
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
				let data = await this.$api.mine.getBrokerInfo()
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				if (!data || !data.id) {
					this.$util.hideAll()
					return
				}
				for (let key in this.form) {
					this.form[key] = data[key]
				}
				if(data.status == 4 && data.sh_text){
					this.tipArr[data.status][0].text = data.sh_text
					this.tipArr[data.status][0].text1 = ''
				}
				this.status = data.status
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
					await this.$api.mine.applyBroker(param)
					this.$util.hideAll()
					this.$util.showToast({
						title: `提交成功`
					});
					setTimeout(() => {
						this.status = 1
					}, 1000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}

			},
			abnorConfirm(){
				this.status = 0
			}
		}
	}
</script>


<style lang="scss">
	.page-height{
		height: 100vh;
	}
</style>
