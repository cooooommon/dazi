<template>
	<view>
		<view class="flex-center"
			:style='{paddingLeft:wingBlank/2+"rpx",paddingRight:wingBlank/2+"rpx"}'
			v-if="colType=='imagewindow-col-3'">
			<view @tap="goDiyUrl(list[0])" class='img-item' style="width: 50%;"
				:style='{paddingTop:whiteSpace+"rpx",paddingLeft:wingBlank/2+"rpx",paddingRight:wingBlank/2+"rpx"}'>
				<image mode="aspectFill" class="img" :src='list[0].img[0].url' style="height: 334rpx;"></image>
			</view>
			<view class="flex-center flex-column" style="width: 50%;">
				<block v-for="(item,index) in list" :key="index">
					<block v-if="index === 0"></block>
					<view @tap="goDiyUrl(item)" class='img-item' v-else
						:style='{width:"100%",paddingTop:whiteSpace/1+"rpx",paddingLeft:wingBlank/2+"rpx",paddingRight:wingBlank/2+"rpx"}'>
						<image mode="aspectFill" class="img" :src='item.img[0].url' style="height: 152rpx;"></image>
					</view>
				</block>
			</view>
		</view>
		<view class='img-box'
			:style='{paddingLeft:wingBlank/2+"rpx",paddingRight:wingBlank/2+"rpx"}' v-else>
			<view @tap="goDiyUrl(item)" class='img-item' v-for="(item,index) in list" :key="index"
				:style='{width:colType == "imagewindow-col-1" ? "100%" :(100/layout)+"%",paddingTop:whiteSpace+"rpx",paddingLeft:wingBlank/2+"rpx",paddingRight:wingBlank/2+"rpx"}'>
				<image class="img" :src='item.img[0].url' mode='widthFix'></image>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions
	} from 'vuex';
	export default {
		name: 'image-window',
		props: {
			isDiyPage: {
				type: Boolean,
				default () {
					return false
				}
			},
			colType: {
				type: String,
				default () {
					return 'imagewindow-col-2'
				}
			},
			list: {
				type: Array,
				default () {
					return [{
						id: 1,
						imgUrl: 'http://static.yoshop.xany6.com/20180928164910decaa3848.png'
					}]
				}
			},
			layout: {
				type: Number,
				default () {
					return 2
				}
			},
			wingBlank: {
				type: Number,
				default () {
					return 0
				}
			},
			whiteSpace: {
				type: Number,
				default () {
					return 0
				}
			},
			backgroundColor: {
				type: String,
				default () {
					return '#fff'
				}
			}
		},
		created() {

		},
		computed: mapState({
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
		}),
		data() {
			return {

			}
		},
		methods: {
			goDiyUrl(item) {
				if (this.isDiyPage) {
					this.$emit("change", item)
				} else {
					let url = item.link[0].url
					if (!url) return
					if (url.includes('staff_id=')) {
						url += this.commonOptions.staff_id
					}
					let openType = this.configInfo.methodObj[item.linkType]
					if (url.includes('key=') && openType != 'miniProgram') {
						openType = 'reLaunch'
					}
					this.$util.log(url)
					this.$util.goUrl({
						openType,
						url
					})
				}
			}

		},
	}
</script>


<style>
	.img-box {
		display: flex;
		flex-wrap: wrap;
		align-items: flex-start;
	}

	.img-item {
		width: 25%;
	}

	.img {
		width: 100%;
		display: block;
	}
</style>
