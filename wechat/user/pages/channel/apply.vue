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
				<view class="flex-between pl-lg pr-lg b-1px-b">
					<view class="item-text">申请渠道</view>
					<view class="item-input flex-1 text">
						<picker @change="pickerChange($event)" :value="channelInd" :range="channelList"
							range-key="title">
							<view class="flex-between" style="color: #A9A9A9;">
								{{channelInd!=-1 && channelList.length > 0?channelList[channelInd].title:'请选择您能接入的渠道'}}
								<i class="iconfont icon-right ml-sm" style="font-size: 28rpx;"></i>
							</view>
						</picker>
					</view>
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
				channelInd: -1,
				channelList: [],
				isLoad: false,
				options: {},
				form: {
					id: 0,
					user_name: '', //姓名 
					mobile: '', //手机号  
					cate_id: '', // 渠道
					text: ''
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
					},
					{
						name: "cate_id",
						checkType: "isNotNull",
						errorMsg: "请选择您能接入的渠道"
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
			this.options = options
			await this.initIndex()
			this.isLoad = true
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo']),
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
				let [list, data] = await Promise.all([this.$api.channel.channelCateSelect(), this.$api.channel
					.channelInfo()
				])
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.channelList = list
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
			pickerChange(e, val) {
				this.channelInd = e.target.value
				this.form.cate_id = this.channelList[this.channelInd].id
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
				param.text = param.text.length > 300 ? param.text.substring(0, 300) : param.text
				this.$util.showLoading()
				try {
					await this.$api.channel.applyChannel(param)
					this.$util.hideAll()
					this.$util.showToast({
						title: `提交成功`
					});
					setTimeout(() => {
						this.$util.back()
						this.$util.goUrl({
							url: '/user/pages/apply-result?type=3',
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
