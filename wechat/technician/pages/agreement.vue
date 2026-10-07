<template>
	<view class="pd-lg" v-if="isLoad">
		<abnor v-if="!config[options.type]"></abnor>
		<view v-else>
			<parser :html="config[options.type]" @linkpress="linkpress" show-with-animation lazy-load>加载中...</parser>
		</view>
	</view>
</template>

<script>
	import parser from "@/components/jyf-Parser/index"
	export default{
		components:{parser},
		data(){
			return{
				config: {},
				isLoad: false,
				options: {
					type: ''
				},
				navTitle: {
					registration_agreement: this.$t('action.attendantName') + '注册协议',
					billing_rules: '计费规则',
					legal_notice: '法律声明',
				} 
			}
		},
		async onLoad(options) {
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			this.$util.showLoading();
			uni.setNavigationBarTitle({
				title: this.navTitle[options.type]
			})
			uni.setNavigationBarColor({
			    frontColor: '#000000',
				backgroundColor: '#ffffff'
			})
			this.options = options
			this.config = await this.$api.technician.agreement()
			this.isLoad = true
			this.$util.hideAll();
		},
		methods:{
			linkpress(res) {
				// #ifdef APP-PLUS
				if (/http/.test(res.href))
					this.$util.goUrl({
						url: res.href,
						openType: 'web'
					})
				// #endif
			},
		}
	}
</script>

<style></style>