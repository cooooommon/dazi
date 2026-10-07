<template>
	<view class="evaluate-pages" v-if="isLoad">
		<view class="pd-lg mt-md mb-md radius-32 fill-base add-upload">
			<upload @upload="imgUpload" :imagelist="param.img" imgtype="img" @del="imgUpload" text="上传图片"
				:imgsize="9" :imageSee="options.type == 'see'"></upload>
		</view>
		<view class="mt-md radius-32 fill-base pt-lg evaluate-box">
			<view class="b-1px-b">
				<textarea v-model="param.text" maxlength="300" cols="30" rows="10" style="width: 100%;height: 400rpx;" placeholder="分享真实消费体验可以帮助到更多人哟~"></textarea>
			</view>
			<view class="evaluate-score">
				<view class="f-paragraph text-bold pb-lg">总体评分</view>
				<view class="flex-warp">
					<block v-for="(item,index) in 5" :key="index">
						<i @tap="changeStar(index*1+1)" class="iconfont mr-sm iconpingfen1" style="font-size: 22px;"
							:class="[{'icon-empty':param.star<index*1+1},{'icon-font-color icon-solid':param.star>=index*1+1}]"></i>
					</block>
				</view>
			</view>
			<view class="release flex-between">
				<view class="">
					<view class="flex-y-center" @tap="changeAnony">
						<i class="iconfont icon-xuanze-fill" :style="{color: primaryColor}" v-if="param.is_hide == 1"></i>
						<i class="iconfont icon-xuanze" style="color: #BEC3CE;" v-else></i>
						<text class="f-caption pl-sm">匿名</text>
					</view>
					<view class="pt-sm f-caption c-caption">匿名会隐藏头像和昵称</view>
				</view>
				<view class="flex-center release-btn" :style="{background: primaryColor}" @tap="submit" v-if="!options.type">
					<i class="iconfont iconfabu c-base"></i>
					<text class="c-base f-mini-title pl-sm">发布</text>
				</view>
			</view>
		</view>
		
		<view class="space-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import siteInfo from '@/siteinfo.js';
	import parser from "@/components/jyf-Parser/index"
	export default {
		components: {
			parser
		},
		data() {
			return {
				options: {},
				detail: {},
				isLoad: true,
				param: {
					order_id: '',
					img: [],
					text: '',
					star: 5,
					is_hide: 0
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			gradualColor: state => state.config.configInfo.gradualColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.param.order_id = options.id
			this.options = options
			if(options.type == 'see'){
				this.$util.showLoading()
			}
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo']),
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
				if(this.options.type == 'see'){
					this.getInfo()
				}
			},
			async getInfo(){
				let data = await this.$api.business.seeComment({order_id: this.options.id})
				data.img = data.img ? data.img.split(',').map(item => {
					return {path: item}
				}) : []
				for(let key in this.param){
					this.param[key] = data[key]
				}
				this.$util.hideAll()
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.param[imgtype] = imagelist;
			},
			changeAnony(){
				let {
					is_hide
				} = this.param
				this.param.is_hide = is_hide == 1 ? 0 : 1
			},
			changeStar(index){
				this.param.star = index
			},
			async submit(){
				let param = this.$util.deepCopy(this.param)
				let {
					img,
					text
				} = param
				if (img.length == 0) {
					this.$util.showToast({
						title: '请上传图片'
					})
					return
				}
				if (!text) {
					this.$util.showToast({
						title: '请输入评价内容'
					})
					return
				}
				param.img = param.img.map(item => {
					return item.path
				})
				this.$util.showLoading()
				await this.$api.business.addComment(param)
				this.$util.hideAll()
				this.$util.showToast({
					title: '评价成功'
				})
				setTimeout(()=>{
					this.$util.getPage(-1).initRefresh()
					this.$util.goUrl({url: 1 ,openType: 'navigateBack'})
				},1000)
			}
			
		}
	}
</script>

<style lang="scss">
	.evaluate-pages {
		.add-upload{
			min-height: 219rpx;
		}
		.evaluate-score{
			padding-top: 56rpx;
		}
		.evaluate-box{
			padding-left: 50rpx;
			padding-right: 50rpx;
			border-bottom-left-radius: 0;
			border-bottom-right-radius: 0;
			padding-bottom: 70rpx;
		}
		.icon-solid {
			background-image: linear-gradient(#FAD961, #F76B1C);
			margin-right: 5px;
		}
		.icon-empty {
			color: #E4E4E4;
			margin-right: 5px;
		}
		.release{
			height: 106rpx;
			margin-top: 350rpx;
			.release-btn{
				width: 198rpx;
				height: 106rpx;
				border-radius: 106rpx;
			}
		}
	}
</style>
