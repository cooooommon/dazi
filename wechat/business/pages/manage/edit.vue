<template>
	<view class="" v-if="isLoad">
		<view class="user-apply-result" v-if=" showStatus && status!=2 || is_update == 1">
			<abnor percent="150%" @confirm="confirm" @cancel="cancel" :title="title[status]" :tip="tipArr[status]"
				:button="buttonArr[status]" :image="image[status]" :tipMax="status == 4? '690rpx':''"></abnor>
		</view>
		
		<view class="form fill-base" v-if="isContent">
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">您的店铺名称</view>
				<view class="radius-16 form-value flex-center pr-lg">
					<input class="pl-lg pr-lg flex-1 f-mini-title" type="text" maxlength="15" :placeholder="rule[0].errorMsg" v-model="param.name" />
					<text class="f-caption c-caption">{{param.name.length > 15 ? 15 : param.name.length}}/15</text>
				</view>
			</view>
			<block v-if="configInfo.im_type == 3">
				<view class="form-item pl-lg pr-lg flex-between">
					<text class="f-mini-title text-bold">商家联系方式</text>
					<view class="flex-center">
						<view class="flex-center" style="padding-left: 50rpx;" @tap="hannelIntegral(item.id)"
						v-for="(item,index) in [{title: '电话',id: 1},{title: '企业微信',id: 2}]" :key="index">
							<i class="iconfont" style="font-size: 38rpx;" :style="{color:param.contact_type == item.id?primaryColor: '#222' }"
							:class="[{'icon-radio-fill': param.contact_type == item.id},{'icon-xuanze': param.contact_type != item.id}]"></i>
							<text class="f-mini-title pl-sm">{{item.title}}</text>
						</view>
					</view>
				</view>
				<view class="pt-lg pl-lg pr-lg">
					<view class="radius-16 fill-body mr-sm">
						<input v-if="param.contact_type==1" class="pl-lg pr-lg flex-1 f-mini-title h-110" type="text" :placeholder="rule[1].errorMsg" v-model="param.mobile"/>
						<input v-else v-model="param.qywx_kid" class="pl-lg pr-lg f-mini-title h-110 flex-1"
							type="text" placeholder="请输入企业微信客服" />
					</view>
				</view>
			</block>
			<view v-else class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">商家电话</view>
				<view class="radius-16 form-value flex-center">
					<input class="pl-lg pr-lg flex-1 f-mini-title" type="text" :placeholder="rule[1].errorMsg" v-model="param.mobile"/>
				</view>
			</view>
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">店铺地址</view>
				<view class="radius-16 form-value flex-center pr-lg" @tap="toChooseLocation">
					<input class="pl-lg pr-lg flex-1 f-mini-title" type="text" disabled :placeholder="rule[2].errorMsg" v-if="!param.address" />
					<view class="pl-lg pr-lg flex-1 f-mini-title ellipsis-2 pt-lg pb-lg" v-else>
						{{param.address}}
					</view>
					<view class="flex-center address-icon" :style="{backgroundColor: primaryColor}">
						<i class="iconfont iconweizhigengxin c-base" style="font-size: 24rpx;"></i>
					</view>
				</view>
			</view>
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">门牌号</view>
				<view class="radius-16 form-value flex-center">
					<input class="pl-lg pr-lg flex-1 f-mini-title" type="text" :placeholder="rule[3].errorMsg" v-model="param.info" />
				</view>
			</view>
			
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">营业时间</view>
				<view class="form-tags flex-warp">
					<block v-for="(item,index) in storeDate" :key="index">
						<view class="flex-center pl-lg pr-lg tag-item rel mr-md mb-sm mt-sm" @tap="changeDate(item.id)"
							style="background-color: #F7F8FA;">
							<text class="f-caption" style="z-index: 1;"
								:style="{color: param.trade_week.indexOf(item.id) != -1 ? primaryColor : `#000`}">{{item.title}}</text>
							<view class="tag-bg abs" v-if="param.trade_week.indexOf(item.id) != -1" :style="{backgroundColor: primaryColor}"></view>
						</view>
					</block>
				</view>
				<view class="mt-md radius-16 fill-body flex-between h-110 pl-lg pr-lg" @tap="changeTime">
					<text class="f-paragraph text-bold">时间段</text>
					<view class="flex-center">
						<text class="f-paragraph">{{ param.start_time ? (param.start_time + ' — ' + (param.end_time || '请选择结束时间')) : '请选择时间段'}}</text>
						<i class="iconfont icon-right text-bold" style="font-size: 12px;color: #AFB2B8;"></i>
					</view>
				</view>
			</view>
			
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">门店所属分类</view>
				<view class="form-tags flex-warp">
					<block v-for="(item,index) in storeType" :key="index">
						<view class="flex-center pl-lg pr-lg tag-item rel mr-md mb-sm mt-sm" @tap="changeType(index)"
							style="background-color: #F7F8FA;">
							<text class="f-caption" style="z-index: 1;"
								:style="{color: param.type_id.includes(String(item.id)) ? primaryColor : `#000`}">{{item.name}}</text>
							<view class="tag-bg abs" v-if="param.type_id.includes(String(item.id))" :style="{backgroundColor: primaryColor}"></view>
						</view>
					</block>
				</view>
			</view>
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-md">门店标签</view>
				<view class="form-tags flex-warp">
					<block v-for="(item,index) in tagList" :key="index">
						<view class="flex-center pl-lg pr-lg tag-item rel mr-md mb-sm mt-sm" @tap="changeTag(index)"
							style="background-color: #F7F8FA;">
							<text class="f-caption" style="z-index: 1;"
								:style="{color: item.checked ? primaryColor : `#000`}">{{item.title}}</text>
							<view class="tag-bg abs" v-if="item.checked" :style="{backgroundColor: primaryColor}"></view>
							<i class="iconfont iconguanbi del-icon" v-if="item.del" @tap.stop="delTag(index)"></i>
						</view>
					</block>
					<view class="flex-center pl-lg pr-lg tag-item rel mr-md mb-sm mt-sm" @tap="addTag"
						style="background-color: #F7F8FA;">
						<text class="f-caption" style="z-index: 1;">自定义标签</text>
					</view>
				</view>
			</view>
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">场地介绍</view>
				<view class="radius-16 form-value">
					<textarea class="form-textarea pd-lg" :placeholder="rule[9].errorMsg" v-model="param.intro" maxlength="400"></textarea>
					<view class="text-right f-caption c-caption pr-lg pb-md">
						{{param.intro.length>400 ? 400: param.intro.length}}/400
					</view>
				</view>
			</view>
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">您的店铺封面 <text class="c-text f-mini-title">（单张）图片比例1:1</text>
				</view>
				<view class="radius-16">
					<upload @upload="imgUpload" :imagelist="param.cover" imgtype="cover" @del="imgUpload" text="上传图片"
						:imgsize="1"></upload>
				</view>
			</view>
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">您的店铺详情图<text class="c-text f-mini-title">（多张）图片比例750*564</text>
				</view>
				<view class="radius-16">
					<upload @upload="imgUpload" :imagelist="param.banner" imgtype="banner" @del="imgUpload" text="上传图片"
						:imgsize="9"></upload>
				</view>
			</view>
			<view class="form-item pl-lg pr-lg">
				<view class="form-title f-mini-title text-bold pb-lg">营业执照<text class="c-text f-mini-title">（用于平台审核,请放心上传）</text></view>
				<view class="radius-16">
					<upload @upload="imgUpload" :imagelist="param.license" imgtype="license" @del="imgUpload" text="上传图片"
						:imgsize="1"></upload>
				</view>
			</view>
			
			<uni-popup ref="tag_item" type="center" :maskClick="true">
				<view class="tag-box fill-base mr-lg ml-lg radius-20 pd-lg">
					<view class="tag-title f-title pb-md flex-center">添加标签</view>
					<view class="tag-value flex-center radius-16 pr-lg">
						<input type="text" class="flex-1 pl-lg f-paragraph" placeholder="请输入标签" v-model="tagText" maxlength="10" />
						<text class="f-caption c-caption">{{tagText.length>10?10:tagText.length}}/10</text>
					</view>
					<view class="flex-center pt-lg">
						<text class="tag-btn flex-center btn-close radius-10 mr-lg" @tap="$refs.tag_item.close()">取消</text>
						<text class="tag-btn flex-center c-base radius-10" :style="{backgroundColor: primaryColor}" @tap="addTagItem">确定</text>
					</view>
				</view>
			</uni-popup>
			
			<w-picker :visible.sync="showStartTime" mode="time" value="00:00" :current="true" :second="false"
				:themeColor="primaryColor" @confirm="onConfirm" ref="time" :interval="true">
			</w-picker>
			<!-- <w-picker :visible.sync="showStartTime" mode="time" :value="param.start_time" :current="false" :second="false"
				:themeColor="primaryColor" @confirm="onConfirm($event, 'start')" ref="time">
				<view class="flex-center">开始时间</view>
			</w-picker>
			<w-picker :visible.sync="showEndTime" mode="time" :value="param.end_time" :current="false" :second="false"
				:themeColor="primaryColor" @confirm="onConfirm($event, 'end')" ref="time">
				<view class="flex-center">结束时间</view>
			</w-picker> -->
				
			<view class="space-max-footer"></view>
			<fix-bottom-button @confirm="submit" :text="[{type:'confirm',text:'提交' }]" bgColor="#fff" borderRadius="16rpx">
			</fix-bottom-button>
		</view>
	</view>
	
	
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import wPicker from "@/components/w-picker/w-picker.vue";
	export default {
		components: {
			wPicker
		},
		data() {
			return {
				tagList: [{
						title: '空调开放',
						checked: false,
						del: false
					},
					{
						title: '免费wifi',
						checked: false,
						del: false
					},
					{
						title: '免费停车位',
						checked: false,
						del: false
					}
				],
				tagText: '',
				param: {
					store_id: '',
					name: '',
					cover: [],
					mobile: '',
					license: [],
					banner: [],
					lng: 0,
					lat: 0,
					address: '',
					info: '',
					tag: '',
					intro: '',
					type_id: [],
					trade_week: '',
					start_time: '',
					end_time: '',
					contact_type: 1,
					qywx_kid: ''
				},
				rule: [{
					name: "name",
					checkType: "isNotNull",
					errorMsg: "请输入店铺名称, 不超过15字",
					regType: 2
				}, {
					name: "mobile",
					checkType: "isPh_zj",
					errorMsg: "请输入手机号或坐座机号"
				}, {
					name: "address",
					checkType: "isNotNull",
					errorMsg: "请选择店铺地址"
				}, {
					name: "info",
					checkType: "isNotNull",
					errorMsg: "请输入门牌号"
				}, {
					name: "trade_week",
					checkType: "isNotNull",
					errorMsg: "请选择营业时间"
				}, {
					name: "start_time",
					checkType: "isNotNull",
					errorMsg: "请选择营业时间"
				}, {
					name: "end_time",
					checkType: "isNotNull",
					errorMsg: "请选择营业时间"
				}, {
					name: "type_id",
					checkType: "isNotNull",
					errorMsg: "请选择门店所属分类"
				}, {
					name: "tag",
					checkType: "isNotNull",
					errorMsg: "请选择门店标签"
				}, {
					name: "intro",
					checkType: "isNotNull",
					errorMsg: "请输入场地介绍"
				}, {
					name: "cover",
					checkType: "isNotNull",
					errorMsg: "请上传您的店铺封面"
				}, {
					name: "banner",
					checkType: "isNotNull",
					errorMsg: "请上传您的店铺详情图"
				},{
					name: "license",
					checkType: "isNotNull",
					errorMsg: "请上传营业执照"
				}],
				storeInfo: '',
				lockTap: false,
				tipArr: {
					'1': [{
						text: `请您耐心等待审核结果，`,
						text1: '审核通过后，您的店铺将会展示在平台里',
						color: 0
					}],
					'2': [],
					'3': [{
						text: '平台管理员已取消授权',
						color: 0
					}],
					'4': [{
						text: '请联系平台管理人员询问失败原因',
						color: 0
					}]
				},
				buttonArr: {
					'1': [{
						text: '返回',
						type: 'cancel'
					}],
					'2': [{
						text: '',
						type: 'confirm'
					}],
					'3': [{
						text: '返回',
						type: 'cancel'
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
					'1': '等待审核',
					'2': '',
					'3': '取消授权',
					'4': '申请失败',
				},
				image: {
					'1': 'https://lbqny.migugu.com/admin/public/apply_wait.jpg',
					'2': 'https://lbqny.migugu.com/admin/public/apply_suc.jpg',
					'3': 'https://lbqny.migugu.com/admin/public/apply_fail.jpg',
					'4': 'https://lbqny.migugu.com/admin/public/apply_fail.jpg',
				},
				status: '',
				is_update: '',
				isLoad: false,
				isContent: false,
				showStatus: false,
				storeType: [],
				storeDate: [
					{title :'周一', id: 1},
					{title :'周二', id: 2},
					{title :'周三', id: 3},
					{title :'周四', id: 4},
					{title :'周五', id: 5},
					{title :'周六', id: 6},
					{title :'周天', id: 0}
				],
				showStartTime: false,
				showEndTime: false
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
		}),
		onLoad() {
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
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.getStoreTypeList()
				this.getStore()
			},
			async getStoreTypeList(){
				let data = await this.$api.business.storeTypeList()
				this.storeType = data
			},
			async getStore(){
				let data = await this.$api.business.getStore()
				if(!data || data&&data.status == 2 && data.is_update == 0 || data.status == -1 ){
					this.isContent = true
				}
				
				if(data){
					this.status = data.is_update == 1 ? 1 : data.status
					this.is_update = data.is_update
					data.store_id = data.id
					data.contact_type = data.contact_type || 1 
					data.type_id = typeof data.type_id == 'number' ? String(data.type_id).split(',') : data.type_id.split(',')
					for(let key in this.param){
						if(['cover','license','banner'].includes(key)){
							let imgArr = []
							data[key].split(',').forEach(item => {
								imgArr.push({path:item})
							})
							this.param[key] = imgArr
						}else{
							this.param[key] = data[key]
						}
					}
					
					let arr = []
					let tagArr = data.tag && data.tag.split(',')
					let newTag = this.$util.deepCopy(tagArr)
					tagArr.forEach((item,index) => {
						this.tagList.forEach(tag =>{
							if(tag.title == item){
								tag.checked = true
								newTag = newTag.filter(item => tag.title != item)
							}
						})
					})
					if(newTag.length > 0){
						newTag.forEach(item => {
							this.tagList.push({title:item,checked: true,del: true})
						})
					}
					// 审核失败原因
					if(data.status == 4 && data.check_msg){
						this.tipArr[data.status][0].text = data.check_msg
					}
				}
				this.showStatus = data ? true : false
				this.storeInfo = data
				this.isLoad = true
			},
			//表单验证
			validate(param) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					if(name == 'mobile' && param.contact_type == 2 && this.configInfo.im_type == 3){
						item.checkType = 'isNotNull'
						item.errorMsg = '请输入企业微信客服'
						validate.add(param.qywx_kid, item);
					}else{
						validate.add(param[name], item);
					}
				})
				let message = validate.start();
				return message;
			},
			async submit(){
				let param = this.$util.deepCopy(this.param)
				let arr = ['cover', 'license']
				arr.map(item => {
					param[item] = param[item].length > 0 ? param[item][0].path : ''
				})
				if(param.banner){
					param.banner = param.banner.map(item => {
						return item.path
					})
					param.banner = param.banner.join(',')
				}
				param.type_id = param.type_id.join(',')
				// param.trade_week = param.trade_week.split(',')
				
				param.tag = ''
				this.tagList.forEach(item=>{
					if(item.checked){
						param.tag += param.tag ? ',' + item.title : item.title
					}
				})
				
				if(this.configInfo.im_type != 3){
					param.contact_type = 1
				}
				
				let msg = this.validate(param);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				if( param.trade_week.length == 1 && param.start_time >= param.end_time ){
					this.$util.showToast({
						title: `开始时间不能大于结束时间`
					});
					return
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try{
					let methodModel = this.storeInfo.status == 2 ? 'edit' : 'apply'
					
					let status = await this.$api.business[methodModel](param)
					this.$util.hideAll()
					this.$util.showToast({
						title: `提交成功`
					});
					setTimeout(() => {
						this.status = typeof(status) == 'object' ? status.status : status
						this.showStatus = true
						this.isContent = false
					}, 1000)
				}catch(e){
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.param[imgtype] = imagelist;
			},
			changeTag(index) {
				this.tagList[index].checked = !this.tagList[index].checked
			},
			changeType(index){
				let {
					id
				} = this.storeType[index]
				let {
					type_id
				} = this.param
				
				let typeInd = type_id.findIndex(item => {
					return item == id
				})
				if(typeInd == -1){
					this.param.type_id.push(String(id))
				}else{
					this.param.type_id.splice(typeInd, 1)
				}
			},
			changeDate(week){
				let {
					trade_week
				} = this.param
				if(trade_week.length == 1){
					let start = Number(this.$util.deepCopy(trade_week)) || 7
					let end = Number(this.$util.deepCopy(week)) || 7
					if(end == start){
						return
					}
					if(start > end){
						this.param.trade_week = this.$util.betweenNumbers(end, start).replace('7', '0')
					}else{
						this.param.trade_week = this.$util.betweenNumbers(start, end).replace('7', '0')
					}
				}else{
					this.param.trade_week = String(week)
				}
			},
			delTag(index){
				this.tagList.splice(index,1)
			},
			addTag(){
				this.tagText = ''
				this.$refs.tag_item.open()
			},
			addTagItem(){
				if(!this.tagText){
					return this.$util.showToast({title: '请输入标签'})
				}
				let ind = this.tagList.findIndex(item => {
					return item.title == this.tagText
				})
				if(ind !== -1){
					return this.$util.showToast({title: '请勿添加重复的标签'})
				}
				this.tagList.push({
					title: this.tagText,
					checked: true,
					del: true
				})
				this.$refs.tag_item.close()
			},
			// 选择地区
			async toChooseLocation(type) {
				await this.$util.checkAuth({
					type: 'userLocation'
				})
				let {
					lat: locaLat = '',
					lng: locaLng = ''
				} = this.location
				let {
					id = 0,
						lat: addrLat,
						lng: addrLng
				} = this.param

				if (id) {
					locaLat = addrLat
					locaLng = addrLng
				}

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
					let location = await this.$util.getBmapLocation()
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
					longitude,
					latitude
				}] = await uni.chooseLocation(param);
				if (!address) return
				this.param.address = address
				this.param.lng = longitude
				this.param.lat = latitude
			},
			// 去管理/再次申请
			async confirm() {
				let {
					status
				} = this
				
				if(status == 2){
					this.$util.goUrl({
						url: `/pages/service`,
						openType: `reLaunch`
					})
				}else{
					this.isContent = true
					this.showStatus = false
				}
				
			},
			// 返回首页
			cancel() {
				if (getCurrentPages().length > 1) {
					this.$util.goUrl({
						url: 1,
						openType: `navigateBack`
					})
					return
				}
				this.$util.goUrl({
					url: `/pages/service`,
					openType: `reLaunch`
				})
			},
			changeTime(){
				let {
					start_time = '',
					end_time = ''
				} = this.param
				
				// if(!start_time){
				// 	this.showStartTime = true
				// }else if(!end_time){
				// 	this.showEndTime = true
				// }else{
				// 	this.showStartTime = true
				// }
				this.showStartTime = true
			},
			onConfirm(e, type){
				console.log(e ,'=======> onConfirm')
				
				this.param.start_time = e.start || '00:00'
				this.param.end_time = e.end || '00:00'
			},
			hannelIntegral(id){
				this.param.contact_type = id
			}
		}
	}
</script>

<style lang="scss">
	.form-item {
		padding-top: 50rpx;

		.form-value {
			min-height: 110rpx;
			background-color: #F7F8FA;

			input {
				height: 110rpx;
			}
		}

		.form-tags {
			.tag-item {
				height: 56rpx;
				border-radius: 56rpx;

				.tag-bg {
					opacity: 0.05;
					width: 100%;
					height: 100%;
					border-radius: 56rpx;
				}
			}
			.del-icon{
				position: absolute;
				top: -6rpx;
				right: -6rpx;
			}
		}

		.form-textarea {
			height: 200rpx;
			width: calc(100% - 60rpx)
		}

		.address-icon {
			width: 40rpx;
			height: 40rpx;
			border-radius: 40rpx;
		}
	}
	
	.tag-box{
		width: 600rpx;
		.tag-btn{
			width: 140rpx;
			height: 68rpx;
		}
		.btn-close{
			border: 1px solid #eee;
		}
		.tag-value{
			border: 1px solid #eee;
			input{
				height: 80rpx;
			}
		}
	}
	
	.user-apply-result{
		background: #fff;
		height: 100%;
		position: absolute;
		width: 100%;
	}
	
	.h-110{
		height: 110rpx;
	}
</style>
