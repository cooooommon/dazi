<template>
	<view class="user-pages-article" v-if="isLoad">
		<view class="pd-lg f-paragraph fill-base">
			<parser :html="detail.text" @linkpress="linkpress" show-with-animation lazy-load>加载中...
			</parser>
		</view>

		<block v-if="detail.is_form">
			<view class="form-info mt-lg ml-md mr-md pd-lg fill-base radius-20">
				<view class="mt-md mb-lg" v-for="(item,index) in detail.field" :key="index">
					<view class="item-text flex-y-center"><i class="iconfont icon-required"
							:class="[{'c-warning':item.is_required},{'c-base':!item.is_required}]"></i>{{item.title}}
					</view>
					<input v-model="item.field_value" type="text" class="item-input mt-md pd-lg radius-16"
						:placeholder="`请输入${item.title}`" />
				</view>
			</view>
			
			<fix-bottom-button @confirm="submit" :text="[{text:'提交',type:'confirm',isAuth:true}]" bgColor="#fff">
			</fix-bottom-button>
		</block>
		<view :class="[{'space-footer':!detail.is_form},{'space-max-footer':detail.is_form}]"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import parser from "@/components/jyf-Parser/index"
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},
				isLoad: false,
				detail: {},
				pageLen: 1
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			loginPage: state => state.user.loginPage,
		}),
		onLoad(options) {
			this.options = options
			this.$util.showLoading()
			this.initIndex()
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
				let {
					id
				} = this.options
				let data = await this.$api.service.articleInfo({
					id
				})
				if (data.is_form) {
					data.field.map(aitem => {
						aitem.field_value = ''
					})
				}
				this.detail = data
				uni.setNavigationBarTitle({
					title: data.title
				})
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.$util.hideAll()
				this.isLoad = true
				this.pageLen = getCurrentPages().length
			},
			initRefresh() {
				this.initIndex(true)
			},
			linkpress(res) {
				// #ifdef APP-PLUS
				this.$util.goUrl({
					url: res.href,
					openType: 'web'
				})
				// #endif
			},
			async submit() {
				let {
					id: uid = 0,
					phone = ''
				} = this.userInfo
				let {
					loginType
				} = this
				let {
					short_code_status
				} = this.configInfo
				if (!uid || (loginType === 'weixin' && short_code_status && !phone)) {
					let {
						id,
						type
					} = this.options
					this.updateUserItem({
						key: 'loginPage',
						val: `/user/pages/article?id=${id}`
					})
					this.$util.goUrl({
						url: `/pages/login`
					})
					return
				}
				let {
					is_form,
					field
				} = this.detail
				if (is_form == 1) {
					field = this.$util.deepCopy(field)
					let isTel = /^(1[0-9]{10})$/
					for (let key in field) {
						let index = key * 1 + 1
						let {
							is_required,
							field_value,
							field_type,
							title
						} = field[key]

						if (field_type === 1) {
							field_value = field_value ? field_value.replace(/(^\s*)|(\s*$)/g, "") : field_value
						}

						if ((is_required && !field_value) || (field_type === 2 && (is_required || field_value) &&
								!isTel.test(field_value))) {
							this.$util.showToast({
								title: field_type === 2 && field_value && !isTel.test(field_value) ?
									`${field_value} ${title}无效` : `请输入${title}`
							})
							return
						}

					}

				}
				let {
					id: article_id
				} = this.options
				let sub_data = this.detail.field.map(item => {
					return {
						field_id: item.field_id,
						field_type: item.field_type,
						key: item.title,
						value: item.field_value
					}
				})

				let arr = sub_data.filter(item => {
					return item.value
				})
				if (arr.length === 0) {
					this.$util.showToast({
						title: `至少填写一项哦`
					})
					return
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					await this.$api.service.subArticleForm({
						article_id,
						sub_data
					})
					this.lockTap = false
					this.$util.hideAll()
					this.$util.showToast({
						title: `提交成功`
					})
					setTimeout(() => {
						let {
							pageLen
						} = this
						let url = pageLen > 1 ? 1 : `/pages/service`
						let openType = pageLen > 1 ? `navigateBack` : `reLaunch`
						this.$util.goUrl({
							url,
							openType
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
	.user-pages-info {

		.form-info {
			.item-input {
				color: #333;
				background: #F7F8FA;
			}
		}
	}
</style>
