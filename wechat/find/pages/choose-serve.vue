<template>
	<view class="pages-index">
		<fixed>
			<view class="nav-search">
				<search @input="toSearch" type="input" :padding="0" :radius="30" backgroundColor="#F0F0F0"
					placeholder="搜索服务名称">
				</search>
			</view>
		</fixed>
		<view class="box">
			<block v-for="(item,index) in skillList" :key="index">
				<view class="box-item b-1px-b flex-between" @tap="setCurrent(index , ids.includes(item.id))">
					<view class="f-min-title" :class="[{'c-desc': !ids.includes(item.id) }, {'c-disable': ids.includes(item.id)}]">
						<text class="flex-1">{{item.name}}</text>
						<text :class="[{'c-warning': !ids.includes(item.id) }, {'c-disable': ids.includes(item.id)}]" v-if="configInfo.demand_price_check == 2">（￥{{item.price}}）</text> 
					</view>
					<view v-if="current == index" class="iconfont icon-xuanze-fill" style="font-size: 20px;" :style="{color: primaryColor}"></view>
					<view v-else class="iconfont icon-xuanze" style="font-size: 20px;color: #ccc;"></view>
				</view>
			</block>
		</view>
		<view class="space-max-footer"></view>
		<fix-bottom-button @confirm="confirm"
			:text="[{type:'confirm',text:'完成' }]" bgColor="#fff" borderRadius="16rpx">
		</fix-bottom-button>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	let timer = null
	export default {
		data() {
			return {
				params:{
					name:'',
					is_all:0
				},
				current: -1,
				skillList:[],
				id:'',
				ids: [],
				options: {}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,			
			mineInfo: state => state.user.mineInfo,
		}),
		async onLoad(options) {
			this.options = options
			if(this.configInfo.demand_price_check == 1){
				this.id = options.id
			}else if(this.configInfo.demand_price_check == 2){
				let serveTypeList = this.$util.deepCopy(this.$util.getPage(-1).serveTypeList)
				let ids = []
				serveTypeList.forEach((item,index) => {
					if(options.index != index){
						ids.push(item.id)
					}else{
						this.id = item.id
					}
					
				})
				this.ids = ids
			}
			
			// #ifdef H5
			if (this.$jweixin.isWechat()) {
				await this.$jweixin.initJssdk();
				this.$jweixin.wxReady(() => {
					this.$jweixin.hideOptionMenu()
				})
			}
			// #endif
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.getList()
		},
		
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods: {
			initRefresh(){
				this.getList()
			},
			async getList(){
				let {params} = this
				let data = await this.$api.find.getSkill(params)
				data.forEach((item,index) => {
					if(item.id == this.id){
						this.current = index
					}
				})
				this.skillList = data
			},
			toSearch(val){
				clearTimeout(timer)
				timer = setTimeout(()=>{
					this.params.name = val
					this.getList()
				},1000)
			},
			confirm(){
				let {current, ids, options} = this
				if(current == -1){
					return this.$util.showToast({
						title: '请选择服务类型'
					})
				}
				let {
					name = '',
					id = 0,
					price
				} = this.skillList[current]
				this.$util.getPage(-1).serveType = name
				this.$util.getPage(-1).param.ser_id = id
				let serveTypeList = this.$util.deepCopy(this.$util.getPage(-1).serveTypeList)
				if(options.index >= 0){
					serveTypeList[options.index] = Object.assign({}, this.skillList[current], {
						number: serveTypeList[options.index].number
					}) 
					let totalPrice = 0
					let oldPrice = 0
					let discount = 1
					if(this.configInfo.plugAuth.member && this.mineInfo.member_info){
						discount = this.mineInfo.member_info.member_discount / 10
					}
					serveTypeList.forEach(item => {
						totalPrice += (item.price ? Number(Number(item.price)*(item.number*1 || 1)*discount) : 0)
						oldPrice += (item.price ? Number(Number(item.price)*(item.number*1 || 1)) : 0)
					})
					this.$util.getPage(-1).param.price = Number(oldPrice.toFixed(2))
					this.$util.getPage(-1).totalPrice = Number(totalPrice.toFixed(2))
					let data = this.$util.deepCopy(this.$util.getPage(-1).serveTypeList[options.index])
					data.name = name
					data.id = id
					data.price = price
					serveTypeList[options.index] = data
					this.$util.getPage(-1).serveTypeList = serveTypeList
				}
				
				this.$util.goUrl({url: 1 ,openType: "navigateBack" })
			},
			setCurrent(index, flag){
				if(flag) return
				this.current = index 
				this.id = this.skillList[index].id
			}
		}
	}
</script>

<style lang="scss">
	page{
		background-color: #fff;
	}
	.pages-index {
		.nav-search{
			padding: 17rpx 22rpx;
		}
		.box{
			padding: 0 50rpx;
			.box-item{
				height: 140rpx;
			}
		}
	}
</style>
