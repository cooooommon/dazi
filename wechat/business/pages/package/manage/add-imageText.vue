<template>
	<view class="add-pages">
		<view class="add-tips" v-if="list.length == 0">
			<view class="flex-center">
				<image src="https://lbqny.migugu.com/admin/peiwan/add-package.png" mode="aspectFill" class="tips-img"></image>
			</view>
			<view class="tips-text f-desc flex-center">添加丰富的内容, 介绍你的产品</view>
			<view class="pt-lg flex-center pt-sm">
				<view class="flex-center f-mini-title tips-btn c-base" :style="{background: primaryColor}" @tap="richtextOpen(-1)">立即上传</view>
			</view>
		</view>
		<block v-if="list.length > 0">
			<view class="add-list">
				<blcok v-for="(item,index) in list" :key="index">
					<view class="ml-md mr-md mt-md fill-base radius-16 pd-lg" >
						<view class="add-cont flex-center pt-lg pb-lg">
							<textarea v-model="item.value" placeholder="请输入" name="" id="" cols="30" rows="10" class="add-cont flex-1" v-if="item.type == 1"></textarea>
							<image v-if="[2,3].includes(item.type)" :src="item.value" mode="aspectFill" class="flex-1 add-cont"></image>
						</view>
						<view class="pt-lg flex" style="justify-content: flex-end;">
							<view class="mr-md add-item-btn f-caption flex-center c-warning" style="border-color: #FF2404;" @tap="delChange(index)">删除</view>
							<view class="f-caption add-item-btn flex-center mr-md" v-if="index > 0" @tap="moveUp(index)">上移</view>
							<view class="f-caption add-item-btn flex-center" @tap="richtextOpen(index)">+ 插入</view>
						</view>
					</view>
				</blcok>
			</view>
			
			<fixed position="bottom" :zIndex="99">
				<view class="fill-base">
					<view class="bottom-box flex-center">
						<view class="mr-lg flex-center add-cont-btn rel f-mini-title" :style="{color: primaryColor}" @tap="richtextOpen(-1)">
							+添加内容
							<view class="add-cont-btn-bg abs" :style="{background: primaryColor}"></view>
						</view>
						<view class="flex-center f-mini-title c-base save-btn" :style="{background: primaryColor}" @tap="onSubmit">保存</view>
					</view>
					<view class="space-safe"></view>
				</view>
			</fixed>
			<view class="space-max-footer"></view>
		</block>
		
		<uni-popup ref="rich_text" type="bottom" :maskClick="true">
			<view class="fill-base rich-text flex">
				<view class="flex-column flex-center flex-1" @tap="addChange(1)">
					<view class="flex-center rich-text-box">
						<i class="iconwenben iconfont" style="font-size: 18px;"></i>
					</view>
					<view class="f-desc pt-sm">文本</view>
				</view>
				<view class="flex-column flex-center flex-1" @tap="addChange(2)">
					<view class="flex-center rich-text-box">
						<i class="iconcamera-fill iconfont" style="font-size: 20px;"></i>
					</view>
					<view class="f-desc pt-sm">相机</view>
				</view>
				<view class="flex-column flex-center flex-1" @tap="addChange(3)">
					<view class="flex-center rich-text-box">
						<i class="icontupian iconfont" style="font-size: 20px;"></i>
					</view>
					<view class="f-desc pt-sm">照片</view>
				</view>
				<view class="flex-1"></view>
			</view>
		</uni-popup>
		
		
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
				introduce:'',
				list: [],
				listPushInd: -1
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
		}),
		onLoad() {
			this.initIndex()
			this.introduce = this.$util.getPage(-1).param.introduce
			this.list = this.$util.getPage(-1).param.introduce_text || []
			console.log(this.list)
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
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
			},
			async addChange(type){
				let { listPushInd } = this
				if(type == 1){
					if(listPushInd == -1){
						this.list.push({
							type,
							value: ''
						})
					}else{
						this.list.splice(listPushInd*1 + 1,0,{
							type,
							value: ''
						})
					}
					
				}else{
					let param = {
						count: 1,
						mediaType: ['image'],
						sizeType: ['compressed']
					}
					param.sourceType = type == 2 ? ['camera'] : ['album']
					console.log(type ,param )
					let [res_upload, res_info] = await uni.chooseImage(param);
					if (res_upload) return
					console.log(res_info)
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
					this.$util.hideAll()
					if(listPushInd == -1){
						this.list.push({
							type,
							value: path
						})
					}else{
						this.list.splice(listPushInd*1 + 1,0,{
							type,
							value: path
						})
					}
				}
				
				this.$refs.rich_text.close()
			},
			delChange(index){
				this.list.splice(index, 1)
			},
			richtextOpen(index = -1){
				this.listPushInd = index
				this.$refs.rich_text.open()
			},
			moveUp(index){
				let list = this.$util.deepCopy(this.list)
				let item = list.splice(index,1)
				list.splice(index - 1,0,item[0])
				this.list = list
			},
			onSubmit(){
				let introduce = ''
				this.list.forEach(item => {
					if(item.value){
						if(item.type == 1){
							item.value.split('\n').forEach(p => {
								introduce += `<p>${p.replace(/ /g, '&nbsp;')}</p>`
							})
						}else if([2,3].includes(item.type)){
							introduce += `<img src="${item.value}" />`
						}
					}
				})
				this.$util.getPage(-1).param.introduce = introduce
				this.$util.getPage(-1).param.introduce_text = this.list
				this.$util.goUrl({url: 1 ,openType: 'navigateBack'})
			}
		}
	}
</script>

<style lang="scss">
	.add-pages{
		.add-tips{
			padding-top: 188rpx;
			.tips-img{
				width: 267rpx;
				height: 267rpx;
				border-radius: 267rpx;
				
			}
			.tips-text{
				padding-top: 46rpx;
			}
			.tips-btn{
				width: 380rpx;
				height: 90rpx;
				border-radius: 90rpx;
			}
		}
		.rich-text{
			padding-bottom: 50rpx;
			height: 320rpx;
			.rich-text-box{
				width: 100rpx;
				height: 100rpx;
				border: 1px solid #ccc ;
				border-radius: 10rpx;
			}
		}
		.add-list{
			.add-cont{
				height: 380rpx;
				width: 650rpx;
			}
			.add-item-btn{
				width: 139rpx;
				height: 56rpx;
				border-radius: 56rpx;
				border: 1px solid #D2D2D2;
			}
		}
		.bottom-box{
			height: 116rpx;
			.add-cont-btn{
				width: 210rpx;
				height: 80rpx;
				border-radius: 80rpx;
				.add-cont-btn-bg{
					width: 210rpx;
					height: 80rpx;
					border-radius: 80rpx;
					opacity: 0.1;
					left: 0;
					top: 0;
				}
			}
			.save-btn{
				width: 396rpx;
				height: 80rpx;
				border-radius: 80rpx;
			}
		}
	}
</style>