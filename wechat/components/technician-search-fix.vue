<template>
	<view class="technician-search-fix">
		<fixed :zIndex="99">
			<view class="fix-info rel" :class="[from]">
				<image mode="widthFix" lazy-load class="technician-page-bg abs"
					src="https://lbqny.migugu.com/admin/playwith/mine/technician-nav-bg.png"></image>
				<view class="search-info rel">
					<!-- #ifdef MP-WEIXIN -->
					 <view :style="{height:`${navBarHeight}px`}"></view>
					 <!-- #endif -->
					 <!-- #ifdef H5 -->
					 <view style="height:50rpx"></view>
					 <!-- #endif -->
					 <!-- #ifdef APP-PLUS -->
					 <view style="height:80rpx"></view>
					 <!-- #endif -->
					<view class="flex-center pl-lg pr-lg pb-md">
						<view class="city-info">
							<picker @change="pickerChange($event)" :value="cityIndex" :range="cityList"
								range-key="title">
								<view class="city-info flex-y-center">
									<view>
										<block v-if="cityList&&cityList.length > 0">
											{{cityList[cityIndex].title.length > 3 ? cityList[cityIndex].title.substring(0,3) + '...':cityList[cityIndex].title}}
										</block>
										<block v-else>请选择</block>
									</view>
									<i class="iconfont iconshaixuanxia-1 ml-sm" style="color:#CDCDCD"></i>
								</view>
							</picker>
						</view>
						<view class="flex-1">
							<search @input="toSearch" type="input" :keyword="params.coach_name" :padding="6"
								:radius="50" backgroundColor="#fff" frontColor="#fff" :placeholder="`请输入${$t('action.attendantName')}昵称`">
							</search>
						</view>
					</view>
					<view @tap.stop="toChooseLocation" class="flex-between pl-lg pr-md">
						<view class="f-paragraph pr-md ellipsis" style="color: #5A677E;">
							{{location&&location.address ? location.address :  '定位中...'}}
						</view>
						<i class="iconfont icongengduo" style="font-size: 28rpx;"></i>
					</view>
					<view class="t-nav fill-base flex-between pl-lg pr-lg mt-md">
						<view class="flex-warp">
							<view class="type-item flex-center f-desc mr-md" @tap="toScreenType(item.id)"
							:style="{backgroundColor: params.order == item.id ? primaryColor: '',borderColor: params.order == item.id ? primaryColor : '#ddd',color: params.order == item.id ? '#fff' : '#778498'}"
							v-for="(item,index) in screenType" :key="index" >{{item.title}}</view>
						</view>
						<view class="flex-center" @tap="toShowChoose('service')">
							<text class="c-caption f-desc pr-sm" style="color: #FF6F8E;">高级筛选</text>
							<i class="iconfont iconshaixuan1" style="font-size: 24px;color: #FF6F8E;"></i>
						</view>
					</view>
					<!-- <view class="rel" style="width:750rpx;height:90rpx;overflow: hidden;" v-if="from=='list'">
						<view class="abs" style="top:20rpx;left:-46rpx;">
							<tab @change="handerTabChange" :list="tabList" :activeIndex="activeIndex*1"
								:activeColor="primaryColor" lineClass="sm" :lineColor="primaryColor"
								curClass="text-bold" activeColor="#000" color="#666666" bgColor="none" width="208rpx"
								height="70rpx" fontSize="28rpx">
							</tab>
						</view>
					</view> -->
					<!-- <view class="flex-y-center pt-md pl-lg pr-lg">
						<view @tap.stop="toShowChoose('sex')" class="flex-y-center f-desc" style="color:#1D1D1D">
							<view>性别</view>
							<view class="choose-sex-service flex-center ml-md f-caption c-black radius fill-base">
								<view class="flex-1">{{['男','女','全部'][params.sex]}}</view>
								<i class="iconfont iconshaixuanxia-1"></i>
							</view>
						</view>
						<view @tap.stop="toShowChoose('service')" class="flex-y-center f-desc"
							style="color:#1D1D1D;margin-left:70rpx;" v-if="from=='list'">
							<view>服务</view>
							<view class="choose-sex-service flex-center ml-md f-caption radius fill-base">
								<view class="flex-1 pr-md ellipsis"
									:class="[{'c-caption':!params.ser_title},{'c-black':params.ser_title}]"
									style="max-width: 295rpx;">
									{{params.ser_title ? params.ser_title : '请选择'}}
								</view>
								<i class="iconfont iconshaixuanxia-1"></i>
							</view>
						</view>
					</view> -->
				</view>
			</view>
		</fixed>

		<uni-popup ref="technician_all" type="bottom">
			<view @touchmove.stop.prevent class="fill-base pl-lg pr-lg common-popup-content" style="width: 100%;border-radius: 34rpx 34rpx 0 0;">
				<view class="all-title w-100 flex-center pb-lg rel">
					<text class="f-title text-bold">筛选</text>
					<i class="iconfont icon-add rotate-45 text-bold abs" style="right: 0;top: 0;" @tap="$refs.technician_all.close()"></i>
				</view>
				<view class="sex-box w-100">
					<view class="sex-title f-paragraph text-bold">性别</view>
					<view class="flex-warp pt-lg">
						<view class="type-item mr-md flex-center f-desc"
						:style="{backgroundColor: sexId == item.id ? primaryColor: '',borderColor: sexId == item.id ? primaryColor : '#ddd',color: sexId == item.id ? '#fff' : '#778498'}"
						v-for="(item,index) in sexList" :key="index" @tap="toCheckSex(item.id)">{{item.title}}</view>
					</view>
				</view>
				<view class="sex-box w-100">
					<view class="sex-title f-paragraph text-bold pt-lg">服务类型</view>
					<view class="pt-lg">
						<scroll-view scroll-y @scrolltolower="scrolltolower" :scroll-with-animation="true" lower-threshold="100"
							style="width: 100%;max-height: 20vh;">
							<view class="flex-warp">
								<view @tap.stop="toCheckItem(index)" class="flex-center mr-md f-desc type-item pl-md pr-md mb-md"
									:style="{backgroundColor: chooseInd == index ? primaryColor: '', borderColor: chooseInd == index ? primaryColor : '#ddd',color: chooseInd == index ? '#fff' : '#778498'}"
									v-for="(item,index) in list.data" :key="index">
									{{item.title}}
								</view>
							</view>
						</scroll-view>
					</view>
				</view>
				<view class="w-100">
					<view class="sex-title f-paragraph text-bold pt-sm">价格区间</view>
					<view class="pt-lg flex-y-center">
						<input type="number" class="input-price pl-md pr-md" placeholder="请输入" v-model="min_price"/>
						<text class="pl-md pr-md" style="color: #ddd;">——</text>
						<input type="number" class="input-price pl-md pr-md" placeholder="请输入" v-model="max_price"/>
					</view>
				</view>
				<view class="button">
					<view @tap.stop="toReset" class="item-child">
						重置
					</view>
					<view @tap.stop="toConfirm" class="item-child" :style="{background: primaryColor,color:'#fff'}">
						确定
					</view>
				</view>
				<view class="space-safe"></view>
			</view>
		</uni-popup>
		<uni-popup ref="choose_item" type="bottom" :custom="true">
			<view @touchmove.stop.prevent class="common-popup-content fill-base"
				style="width: 100%;border-radius: 34rpx 34rpx 0 0;">
				<view class="flex-center f-title c-title text-bold pb-lg">{{`选择${chooseArr[chooseType]}`}}</view>
				<scroll-view scroll-y @scrolltolower="scrolltolower" :scroll-with-animation="true" lower-threshold="100"
					style="width: 100%;" :style="{height:chooseType=='sex'?'20vh':'43vh'}">
					<view @tap.stop="toCheckItem(index)" class="flex-warp pt-sm pb-sm"
						:style="{color:chooseInd == index ? primaryColor: ''}"
						v-for="(item,index) in chooseType == 'sex' ? sexList : list.data" :key="index">
						<view class="f-title flex-1">
							{{item.title}}
						</view>
						<i class="iconfont c-caption"
							:class="[{'icon-xuanze':chooseInd != index},{'icon-radio-fill':chooseInd == index}]"
							style="font-size: 40rpx;" :style="{color:chooseInd == index ? primaryColor: ''}"></i>
					</view>
				</scroll-view>

				<view class="button">
					<view @tap.stop="$refs.choose_item.close()" class="item-child">
						取消
					</view>
					<view @tap.stop="toConfirm" class="item-child" :style="{background: primaryColor,color:'#fff'}">
						确定
					</view>
				</view>
				<view class="space-safe"></view>
				</block>
			</view>
		</uni-popup>
	</view>

</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		props: {
			from: {
				type: String,
				default () {
					return 'list'
				}
			},
			params: {
				type: Object,
				default () {
					return {}
				}
			}
		},
		data() {
			return {
				screenType: [{title: '推荐',id: 1},{title: '新人',id: 2}],
				sexList: [
					{
					title: '全部',
					id: -1,
				}, 
				{
					title: '男',
					id: 0,
				}, {
					title: '女',
					id: 1
				}],
				param: {
					page: 1,
					limit: 20
				},
				list: {
					data: []
				},
				loading: true,
				chooseArr: {
					sex: '性别',
					service: '服务'
				},
				chooseType: 'service',
				chooseInd: -1,
				sexId: -1,
				min_price: '',
				max_price: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			plugAuth: state => state.config.configInfo.plugAuth,
			navBarHeight: state => state.config.configInfo.navBarHeight,
			cityId: state => state.technician.cityId,
			cityIndex: state => state.technician.cityIndex,
			cityList: state => state.technician.cityList,
			activeIndex: state => state.technician.activeIndex,
			tabList: state => state.technician.tabList,
			location: state => state.user.location,
		}),
		methods: {
			...mapMutations(['updateUserItem', 'updateTechnicianItem']),
			pickerChange(e, val) {
				let ind = e.target.value
				this.updateTechnicianItem({
					key: 'cityIndex',
					val: ind
				})
				this.updateTechnicianItem({
					key: 'cityId',
					val: this.cityList[ind].id
				})
				this.$emit('pickerChange')
			},
			toSearch(val) {
				this.$emit('search', val)
			},
			// 选择地区
			async toChooseLocation(e) {
				this.toEmit('chooseLocation')
				return
				await this.$util.checkAuth({
					type: 'userLocation'
				})

				let {
					lat: locaLat = '',
					lng: locaLng = ''
				} = this.location
				let param = {}
				if (!locaLat && !locaLng) {
					// #ifdef H5
					if (this.$jweixin.isWechat()) {
						this.$util.showLoading()
						await this.$jweixin.wxReady2();
						let {
							latitude,
							longitude
						} = await this.$jweixin.getWxLocation()
						locaLat = latitude
						locaLng = longitude
					}
					// #endif
					// #ifdef APP-PLUS
					let location = await this.$util.getLocationInfo()
					locaLat = location.lat
					locaLng = location.lng
					// #endif
				}

				// #ifndef MP-WEIXIN
				param = {
					latitude: locaLat,
					longitude: locaLng
				}
				// #endif

				let [, {
					address = '',
					longitude: lng,
					latitude: lat,
					province = '',
					city = '',
					district = '',
				}] = await uni.chooseLocation(param);
				if (!lng) return
				let location = {
					lng,
					lat,
					address,
					province,
					city,
					district
				}
				this.updateUserItem({
					key: 'location',
					val: location
				})
				this.toEmit('chooseLocation')
			},
			handerTabChange(index) {
				this.$refs.technician_all.open()
				this.$emit('handerTabChange', index)
			},
			async toShowChoose(key) {
				this.chooseType = key
				let {
					sex,
					ser_id
				} = this.params
				if (key == 'service') {
					this.param.page = 1
					await this.getList()
				}

				// let ind = this.sexList.findIndex(item => {
				// 	return item.id == sex
				// }) 
				this.sexId = sex
				this.chooseInd = this.list.data.findIndex(item => {
					return item.id == ser_id
				})
				this.$refs.technician_all.open()
				//this.$refs.choose_item.open()
			},
			toCheckItem(index) {
				this.chooseInd = index
			},
			toCheckSex(id){
				this.sexId = id
			},
			scrolltolower() {
				if (this.list.current_page >= this.list.last_page || this.loading) return;
				this.loading = true;
				this.param.page = this.param.page + 1
				this.getList();
			},
			async getList() {
				let {
					list: oldList,
					param,
				} = this
				let newList = await this.$api.service.serviceList(param);

				if (this.param.page == 1) {
					// newList.data.unshift({
					// 	id: 0,
					// 	title: '全部服务'
					// })
					this.list = newList
				} else {
					newList.data = oldList.data.concat(newList.data)
					this.list = newList
				}
				this.loading = false
				this.$util.hideAll()
			},
			toConfirm() {
				let {
					chooseType,
					chooseInd,
					min_price,
					max_price
				} = this
				
				if(max_price*1 < min_price*1){
					this.$util.showToast({
						title: `请输入正确的价格区间`
					})
					return
				}
				this.$refs.technician_all.close()
				//this.$refs.choose_item.close()
				let param = this.$util.deepCopy(this.params)
				param.page = 1
				let {
					id,
					title
				} = chooseType == 'sex' ? this.sexList[chooseInd] : (chooseInd != -1 ? this.list.data[chooseInd] : {id:0, title: '全部'})
				if (chooseType == 'sex') {
					param.sex = id
				} else {
					param.ser_id = id
					param.ser_title = title
				}
				param.sex = this.sexId
				delete param.min_price
				delete param.max_price
				if(min_price){
					param.min_price = min_price
				}
				if(max_price){
					param.max_price = max_price
				}
				this.$emit('changeParams', param)
			},
			toReset(){
				let param = this.$util.deepCopy(this.params)
				param.sex = -1
				this.chooseInd = -1
				this.sexId = -1
				param.ser_id = 0
				param.ser_title = '全部'
				this.min_price = ''
				this.max_price = ''
				delete param.min_price
				delete param.max_price
				this.$emit('changeParams', param)
				this.$refs.technician_all.close()
			},
			toEmit(key) {
				this.$emit(key)
			},
			toScreenType(id){
				let param = this.$util.deepCopy(this.params)
				param.order = id
				this.$emit('changeParams', param)
			}
		}
	}
</script>

<style scoped lang="scss">
	.technician-search-fix {


		.technician-page-bg {
			width: 100%;
			height: 256rpx;
			z-index: 0;
		}

		.fix-info {
			/* #ifdef MP-WEIXIN */
			//height: 503rpx;
			height: 453rpx;
			/* #endif */
			/* #ifdef H5 */
			//height: 370rpx;
			height: 320rpx;
			/* #endif */
			/* #ifdef APP-PLUS */
			//height: 400rpx;
			height: 350rpx;
			/* #endif */
			background: #F6F7FB;
			overflow: hidden;
			.search-info {
				width: 100%;

				.city-info {
					width: 170rpx;

					.iconfont {
						font-size: 20rpx;
						transform: scale(0.5);
					}
				}

				.choose-sex-service {
					height: 51rpx;
					padding: 0 10rpx 0 20rpx;

					.iconfont {
						color: #666666;
						font-size: 18rpx;
						transform: scale(0.6);
					}
				}

				.choose-sex-service:nth-child(1) {
					width: 120rpx;
				}

				.choose-sex-service:nth-child(2) {
					min-width: 120rpx;
				}
			}
		}


		.fix-info.choose {
			/* #ifdef MP-WEIXIN */
			height: 453rpx;
			/* #endif */
			/* #ifndef MP-WEIXIN */
			height: 320rpx;
			/* #endif */
		}

	}
	.t-nav{
		height: 120rpx;
		border-top-left-radius: 30rpx;
		border-top-right-radius: 30rpx;
	}
	.type-item{
		min-width: 112rpx;
		height: 60rpx;
		border-radius: 60rpx;
		color: #778498;
		border: 1px solid #DDDDDD;
	}
	.w-100{
		width: 100%;
	}
	.input-price{
		width: 100rpx;
		border: 1px solid #ddd;
		border-radius: 4px;
		height: 60rpx;
		width: 120rpx;
	}
</style>
