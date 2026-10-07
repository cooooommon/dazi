<template>
	<view class="box">
		<view class="pl-lg pr-lg pb-lg fill-base">
			<view class="box-item">
				<view class="box-item-title f-paragraph pb-md text-bold flex">
					<text class="c-warning f-sm-title">*</text>
					<text>反馈类型</text>
				</view>
				<view class="radius-16 flex-warp">
					<block v-for="(item,index) in navList" :key="index">
						<view class="nav-item text-center f-desc c-paragraph mb-md fill-body" @tap="getNavIndex(index)"
							:class="(index % 3 > 0)&& 'ml-md'"
							:style="{backgroundColor: index == navIndex ? primaryColor : '' , color:index == navIndex ? '#fff' : ''}">
							{{item.title}}
						</view>
					</block>
				</view>
			</view>
			<view class="box-item">
				<view class="box-item-title">
					<view class="text-bold f-paragraph">订单编号</view>
					<view class="c-caption f-caption pt-md pb-lg">若涉及订单, 填入订单号有助解决问题(订单详情页可复制)</view>
				</view>
				<view class="pr-lg pl-lg fill-body radius-16 f-paragraph flex-between box-item-cont">
					<input type="number" placeholder="请输入订单号" @input="getInput" v-model="param.order_code" />
				</view>
			</view>
			<view class="box-item pt-md">
				<view class="box-item-title f-paragraph pb-sm text-bold flex">
					<text class="c-warning f-sm-title">*</text>
					<text>反馈内容</text>
				</view>
				<view class="pd-lg radius-16 fill-body" style="overflow: hidden;">
					<textarea style="width: 100%;" class="c-paragraph" cols="30" rows="10"
						placeholder="您宝贵的建议，是我们不断进步的动力！请详细描述遇到的问题" :value="param.content" maxlength="1000"
						@input="bindInput"></textarea>
					<view class="pt-md text-right c-5A677E f-paragraph">
						{{param.content.length > 1000 ? 1000 : param.content.length}} / 1000
					</view>
				</view>
			</view>
			<view class="box-item mt-md">
				<upload @upload="imgUpload" :imagelist="param.images" imgtype="images" @del="imgUpload" text="上传图片"
					:imgsize="3"></upload>
			</view>
			<view class="box-item mt-md">
				<upload @upload="imgUpload" @del="imgUpload" :imagelist="param.video_url" filetype="video"
					imgtype="video_url" text="上传视频" :imgsize="1" :videoSize="30">
				</upload>
			</view>
			<view class="f-desc c-caption pb-sm">*最多只能上传3张图片、1个视频（视频只支持30兆以内）</view>
		</view>
		<view class="f-caption c-warning pd-lg">注: 您反馈的意见问题，平台不会透露给他人，保护您的隐私</view>
		<view class="space-max-footer"></view>

		<fix-bottom-button @cancel="$util.goUrl({url:`/user/pages/feedback/list`})" @confirm="confirm"
			:text="[{text:'反馈记录',type:'cancel'},{text:'提交反馈',type:'confirm',isAuth:true}]" bgColor="#fff"
			:classType="2">
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
				param: {
					content: '',
					type_name: '', //反馈类型
					order_code: '', //订单号
					images: [],
					video_url: []
				},
				navList: [{
						title: '订单问题'
					},
					{
						title: '功能问题'
					},
					{
						title: '账号问题'
					},
					{
						title: '操作问题'
					},
					{
						title: 'BUG反馈'
					},
					{
						title: '其他'
					},
				],
				navIndex: -1
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.initIndex()
		},
		methods: {
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
			},
			bindInput(e) {
				this.$nextTick(function() {
					this.param.content = e.detail.value;
				})
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.param[imgtype] = imagelist;
			},
			confirm() {
				let {
					content,
					images,
					video_url
				} = this.param
				let param = this.$util.deepCopy(this.param)
				param.type_name = this.navIndex >= 0 && this.navList[this.navIndex].title
				if (images.length) {
					param.images = images.map(item => {
						return item.path
					})
				}
				if (video_url.length) {
					param.video_url = video_url[0].path
				}
				if (!param.type_name) {
					return this.$util.showToast({
						title: '请选择反馈类型'
					})
				}
				if (!content.trim()) {
					return this.$util.showToast({
						title: '请输入反馈内容'
					})
				}
				this.$util.showLoading()
				this.$api.mine.addFeedback(param).then(res => {
					this.$util.hideAll()
					this.$util.goUrl({
						url: `/user/pages/feedback/success`,
						openType: 'redirectTo'
					})
				})
			},
			getNavIndex(index) {
				this.navIndex = index
			},
			getInput(e) {
				this.$nextTick(function() {
					this.param.order_code = e.detail.value.replace(/\D/g, '')
				})
			}
		}
	}
</script>

<style lang="scss">
	.box-item {
		.box-item-title {
			padding-top: 40rpx;
		}

		.box-item-cont {
			height: 110rpx;

			input {
				width: 100%;
				height: 100%;
			}
		}

		.c-5A677E {
			color: #5A677E;
		}

		.nav-item {
			width: calc((100% - 40rpx) / 3);
			height: 68rpx;
			line-height: 68rpx;
			border-radius: 68rpx;
		}
	}
</style>
