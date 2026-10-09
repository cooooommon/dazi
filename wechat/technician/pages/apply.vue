<template>
	<view class="apply-pages" v-if="isLoad">
		<block v-if="options.broker_id && broker.coach_status != 2">
			<view class="broker-pages">
				<abnor percent="150%" @confirm="bconfirm" @cancel="bcancel" :title="btitle[broker.coach_status]" :tip="btipArr[broker.coach_status]"
					:button="bbuttonArr[broker.coach_status]" :image="bimage[broker.coach_status]" :tipMax="broker.coach_status == 4? '690rpx':''"></abnor>
			</view>
		</block>
		<view class="page-height" v-if="options.admin_id && [2,3].includes(coach_status) || is_update">
			<abnor percent="150%" @confirm="confirm" @cancel="$util.goUrl({ url: 1, openType: `navigateBack` })"
				:title="title[coach_status]" :tip="tipArr[coach_status]" :button="buttonArr[coach_status]"
				:image="image[coach_status]"></abnor>
		</view>
		<block v-else>
			<view class="apply-form">
				<view class="fill-base">
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">昵称</view>
						<input v-model="form.coach_name" type="text" class="item-input flex-1"
							:class="[{'text-bold':form.coach_name}]" maxlength="15" placeholder-class="c-caption"
							:placeholder="rule[0].errorMsg" />
					</view>
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">姓名</view>
						<input v-model="form.nickname" type="text" class="item-input flex-1"
							:class="[{'text-bold':form.nickname}]" maxlength="10" placeholder-class="c-caption"
							:placeholder="rule[1].errorMsg" />
					</view>
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">性别</view>
						<view class="item-input text flex-y-center">
							<view @tap.stop="form.sex = index" class="flex-y-center" :class="[{'mr-lg':index==0}]"
								:style="{color:form.sex == index ? primaryColor:''}" v-for="(item,index) in ['男','女']"
								:key="index"><i class="iconfont icon-xuanze mr-sm"
									:class="[{'icon-radio-fill':form.sex == index}]"></i>{{item}}
							</view>
						</view>
					</view>
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">生日</view>
						<view class="item-input text">
							<picker @change="pickerChange($event,'birthday')" mode="date" :end="endYear"
								:value="form.birthday">
								<view class="flex-y-center"
									:class="[{'c-caption':!form.birthday},{'text-bold':form.birthday}]">
									{{form.birthday||'请选择'}}
									<i class="iconfont icongengduo ml-sm"></i>
								</view>
							</picker>
						</view>
					</view>
					<view class="flex-between ml-lg mr-lg b-1px-b" v-if="form.birthday">
						<view class="item-text">星座</view>
						<view class="item-input text flex-1 text-bold"> {{form.constellation}} </view>
					</view>
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">身高</view>
						<view class="flex-1 flex-y-center">
							<input v-model="form.height" type="digit" class="item-input flex-1"
								:class="[{'text-bold':form.height}]" :placeholder="rule[3].errorMsg" />
							<view class="ml-sm f-title c-title text-bold">cm</view>
						</view>
					</view>
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">体重</view>
						<view class="flex-1 flex-y-center">
							<input v-model="form.weight" type="digit" class="item-input flex-1"
								:class="[{'text-bold':form.weight}]" :placeholder="rule[4].errorMsg" />
							<view class="ml-sm f-title c-title text-bold">kg</view>
						</view>
					</view>
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">手机号</view>
						<input v-model="form.mobile" type="text" class="item-input flex-1"
							:class="[{'text-bold':form.mobile}]" :placeholder="rule[5].errorMsg" />
					</view>
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">意向工作城市</view>
						<view class="item-input text">
							<picker @change="pickerChange($event,'city')" :value="cityIndex" :range="cityList"
								range-key="title">
								<view class="flex-y-center"
									:class="[{'c-caption':cityIndex==-1},{'text-bold':cityIndex!=-1}]">
									{{cityIndex!=-1?cityList[cityIndex].title:'请选择'}}
									<i class="iconfont icongengduo ml-sm"></i>
								</view>
							</picker>
						</view>
					</view>
					<view class="flex-warp ml-lg mr-lg">
						<view class="item-input text" style="width:200rpx;text-align: left;">所在地址
						</view>
						<view class="item-input text flex-1">
							<view @tap.stop="toChooseLocation" class="flex-y-center">
								<view class="flex-1 text-right" :class="[{'text-bold':form.address}]">
									{{form.address || `点击右边图标设置`}}
								</view><i class="iconfont icondizhi_1 ml-sm" :style="{color: primaryColor}"></i>
							</view>
						</view>
					</view>
				</view>
				<view class="fill-base mt-md pb-lg">
					<view class="flex-between ml-lg mr-lg ">
						<view class="item-text">个人简介</view>
						<input :disabled="true" type="text" class="item-input flex-1" />
					</view>
					<view class="textarea-box ml-lg mr-lg radius-16">
						<textarea v-model="form.text" class="item-textarea pd-lg f-paragraph c-title"
							:class="[{'text-bold':form.text}]" maxlength="300" :placeholder="rule[8].errorMsg"
							placeholder-class="c-caption" />
						<view class="text-right f-paragraph pb-lg pr-lg" style="color:#C8CDD3">
							{{form.text.length>300?300:form.text.length}}/300
						</view>
					</view>
				</view>
				<view class="fill-base mt-md pb-lg">
					<view class="flex-between" style="padding:45rpx 30rpx 25rpx 30rpx">
						<view class="item-text">拥有的技能</view>
						<view @tap.stop="$util.goUrl({url:`/technician/pages/service`})"
							class="flex-y-center f-desc c-caption">更多技能<i class="iconfont icongengduo"></i></view>
					</view>
					<view class="service-list flex-warp ml-lg mr-lg">
						<block v-for="(item,index) in serviceList" :key="index">
							<view @tap.stop="toCheckItem(item.id)" class="list-item flex-center mt-md mr-md f-desc rel">
								<view class="list-item abs" :style="{background:primaryColor}"
									v-if="form.service.includes(item.id)">
								</view>
								<view :style="{color:form.service.includes(item.id)?primaryColor:''}">{{item.title}}
								</view>
							</view>
						</block>
						<view class="mt-md f-caption c-caption" v-if="serviceList.length == 0">暂无可选技能</view>
					</view>
				</view>
				<view class="fill-base mt-md">
					<view class="flex-between ml-lg mr-lg b-1px-b">
						<view class="item-text">身份证号</view>
						<input v-model="form.id_code" type="text" class="item-input flex-1"
							:class="[{'text-bold':form.id_code}]" :placeholder="rule[10].errorMsg" />
					</view>
					<view class="flex-between ml-lg mr-lg">
						<view class="item-text">身份证照片</view>
						<view class="item-input tips flex-1"></view>
					</view>
					<view class="flex-between ml-lg mr-lg pb-md">
						<upload @upload="imgUpload" :imagelist="form.id_card" imgtype="id_card" imgclass="md"
							text="身份证人像面" :imgsize="1" :pictureSize="5"></upload>
						<upload @upload="imgUpload" :imagelist="form.id_card_fan" imgtype="id_card_fan" imgclass="md"
							text="身份证国徽面" :imgsize="1" :pictureSize="5"></upload>
					</view>
					<view class="flex-between ml-lg mr-lg ">
						<upload @upload="imgUpload" :imagelist="form.id_card_people" imgtype="id_card_people"
							imgclass="md" text="手持身份证照片" :imgsize="1" :pictureSize="5"></upload>
					</view>
					<view class="item-input tips flex-1 ml-lg mr-lg" style="padding: 20rpx 0 20rpx 0">图片上传大小不超过5M
					</view>
				</view>
				<view class="fill-base mt-md">
					<view class="flex-between ml-lg mr-lg">
						<view class="item-text">工作形象照</view>
						<view class="item-input tips flex-1"></view>
					</view>
					<view class="flex-between ml-lg mr-lg ">
						<upload @upload="imgUpload" :imagelist="form.work_img" imgtype="work_img" text="上传图片"
							:imgsize="1" :pictureSize="5">
						</upload>
					</view>
					<view class="item-input tips flex-1 ml-lg mr-lg" style="padding: 0 0 20rpx 0;">图片建议尺寸: 216 * 216，图片上传大小不超过5M
					</view>
				</view>
				<view class="fill-base mt-md">
					<view class="flex-between ml-lg mr-lg">
						<view class="item-text" style="width: auto;">模特照（非必填）</view>
						<view class="item-input tips flex-1"></view>
					</view>
					<view class="flex-between ml-lg mr-lg ">
						<upload @upload="imgUpload" @del="imgUpload" :imagelist="form.model_img" filetype="picture"
							imgtype="model_img" text="上传图片" :imgsize="9" :pictureSize="5">
						</upload>
					</view>
					<view class="item-input tips flex-1 ml-lg mr-lg" :class="[{'mt-md': !(form.model_img.length % 3)}]" style="padding: 0 0 20rpx 0;">图片建议尺寸: 750 * n，图片上传大小不超过5M
					</view>
				</view>
				<view class="fill-base mt-md">
					<view class="flex-between ml-lg mr-lg">
						<view class="item-text">个人生活照</view>
						<view class="item-input tips flex-1"></view>
					</view>
					<view class="flex-between ml-lg mr-lg ">
						<upload @upload="imgUpload" @del="imgUpload" :imagelist="form.self_img" filetype="picture"
							imgtype="self_img" text="上传图片" :imgsize="9" :pictureSize="5">
						</upload>
					</view>
					<view class="item-input tips flex-1 ml-lg mr-lg" :class="[{'mt-md': !(form.self_img.length % 3)}]" style="padding: 0 0 20rpx 0;">图片建议尺寸: 750 * n，至少上传4张照片，图片上传大小不超过5M
					</view>
				</view>
				<view class="fill-base mt-md">
					<view class="flex-between ml-lg mr-lg">
						<view class="item-text">个人视频介绍</view>
						<view class="item-input tips flex-1"></view>
					</view>
					<view class="flex-between ml-lg mr-lg">
						<upload @upload="imgUpload" @del="imgUpload" :imagelist="form.video" filetype="video"
							imgtype="video" text="上传视频" :imgsize="1">
						</upload>
					</view>
					<view class="item-input tips flex-1 ml-lg mr-lg" style="padding: 0 0 20rpx 0;">视频上传大小不超过50M
					</view>
				</view>
				<view class="fill-base flex-y-center pl-lg pb-md">
					<i class="iconfont" :class="[{'icon-xuanze': !isAgreement}, {'icon-radio-fill': isAgreement}]" 
					style="font-size: 17px;" @tap="changeAgreement" :style="{color: isAgreement ? primaryColor : '#ccc'}"></i>
					<view class="f-caption pl-sm">
						<text @tap="changeAgreement">我已阅读并同意</text>
						<text class="c-alipay" @tap="$util.goUrl({url: `/technician/pages/agreement?type=registration_agreement`})">《{{$t('action.attendantName')}}注册协议》</text>
						<text class="c-alipay" @tap="$util.goUrl({url: `/technician/pages/agreement?type=billing_rules`})">《计费规则》</text>
						<text class="c-alipay" @tap="$util.goUrl({url: `/technician/pages/agreement?type=legal_notice`})">《法律声明》</text>
					</view>
					
				</view>
				<view class="flex-center f-caption c-caption pd-lg">
					{{options.is_edit == 1 ? '编辑资料将进入重新审核，审核通过之前将显示原资料' : '平台不会通过任何渠道泄露您的个人信息，请放心输入'}}
				</view>
			</view>
		
			<view class="space-max-footer"></view>
		
			<fix-bottom-button @confirm="submit" :text="[{text:'确定申请',type:'confirm',isAuth:true}]" bgColor="#fff">
			</fix-bottom-button>
		</block>
	</view>
</template>

<script>
	import wPicker from "@/components/w-picker/w-picker.vue";
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		components: {
			wPicker
		},
		data() {
			return {
				isLoad: false,
				options: {},
				cityList: [],
				cityIndex: -1,
				serviceList: [],
				// -1未申请，1审核中，2审核通过，3取消授权，4审核失败(可再次申请)
				tipArr: {
					'-1': [{
						text: '审核中不可编辑资料',
						color: 0
					}],
					'2': [{
						text: '快去管理订单吧',
						color: 0
					}],
					'3': [{
						text: '请联系平台管理人员询问原因',
						color: 0
					}]
				},
				buttonArr: {
					'-1': [{
						text: '返回',
						type: 'cancel'
					}],
					'2': [{
						text: '去管理',
						isAuth: true,
						type: 'confirm'
					}],
					'3': [{
						text: '再次申请',
						type: 'confirm'
					}, {
						text: '个人中心',
						type: 'cancel'
					}]
				},
				title: {
					'-1': '您已经成功提交申请',
					'2': `您已经是${this.$t('action.attendantName')}了`,
					'3': '平台管理员已取消授权'
				},
				image: {
					'-1': 'https://lbqny.migugu.com/admin/public/apply_wait.jpg',
					'2': 'https://lbqny.migugu.com/admin/public/apply_suc.jpg',
					'3': 'https://lbqny.migugu.com/admin/public/apply_fail.jpg'
				},
				is_update: 0,
				coach_status: 0,
				endYear: 0,
				form: {
					id: 0,
					nickname: '', //姓名
					coach_name: '', //昵称
					mobile: '', //手机号 
					sex: 0, //性别 
					birthday: '',
					constellation: '', // 星座
					height: '',
					weight: '',
					work_time: '', //从业年份 
					city_id: '', //城市 
					lng: '',
					lat: '',
					address: '', //详细地址 
					text: '', //个人简介
					service: [],
					id_code: '', //身份证号
					id_card: [], //身份证
					id_card_fan: [], // 身份证反面
					id_card_people: [], //手持身份证
					work_img: [], // 工作照
					self_img: [], // 生活照
					model_img: [], //模特照
					video: []
				},
				rule: [{
						name: "coach_name",
						checkType: "isNotNull",
						errorMsg: "请输入您兼职时的昵称",
						regType: 2
					}, {
						name: "nickname",
						checkType: "isNotNull",
						errorMsg: "请输入您的姓名",
						regType: 2
					}, {
						name: "birthday",
						checkType: "isNotNull",
						errorMsg: "请选择您的生日",
						regType: 2
					}, {
						name: "height",
						checkType: "isNumber",
						errorMsg: "身高",
						regType: 1
					}, {
						name: "weight",
						checkType: "isNumber",
						errorMsg: "体重",
						regType: 1
					}, {
						name: "mobile",
						checkType: "isMobile",
						errorMsg: "请输入手机号"
					}, {
						name: "city_id",
						checkType: "isNotNull",
						errorMsg: "请选择意向工作城市"
					}, {
						name: "address",
						checkType: "isNotNull",
						errorMsg: "请选择所在地址"
					}, {
						name: "text",
						checkType: "isNotNull",
						errorMsg: "请输入个人简介",
						regType: 2
					}, {
						name: "service",
						checkType: "isNotNull",
						errorMsg: "请选择所拥有的技能"
					}, {
						name: "id_code",
						checkType: "isIdCard",
						errorMsg: "请输入您的身份证号码"
					},
					{
						name: "id_card",
						checkType: "isNotNull",
						errorMsg: "请上传身份证人像面"
					},
					{
						name: "id_card_fan",
						checkType: "isNotNull",
						errorMsg: "请上传身份证国徽面"
					},
					// {
					// 	name: "id_card_people",
					// 	checkType: "isNotNull",
					// 	errorMsg: "请上传手持身份证照片"
					// },
					{
						name: "work_img",
						checkType: "isNotNull",
						errorMsg: "请上传工作形象照"
					},
					{
						name: "self_img",
						checkType: "isNotNull",
						errorMsg: "请上传个人生活照"
					},
					{
						name: "video",
						checkType: "isNotNull",
						errorMsg: "请上传个人视频介绍"
					},
					
				],
				lockTap: false,
				isAgreement: false,
				
				btitle: {
					'1': '等待审核',
					'2': '',
					'3': '取消授权',
					'4': '申请失败',
				},
				// -1未申请，1审核中，2审核通过，3取消授权，4审核失败(可再次申请)
				btipArr: {
					'1': [],
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
				bbuttonArr: {
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
				bimage: {
					'1': 'https://lbqny.migugu.com/admin/public/apply_wait.jpg',
					'2': 'https://lbqny.migugu.com/admin/public/apply_suc.jpg',
					'3': 'https://lbqny.migugu.com/admin/public/apply_fail.jpg',
					'4': 'https://lbqny.migugu.com/admin/public/apply_fail.jpg',
				},
				broker: {
					coach_status: 0
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			userInfo: state => state.user.userInfo,
			location: state => state.user.location,
			mineInfo: state => state.user.mineInfo,
		}),
		async onLoad(options) {
			let {
				admin_id = 0,
					is_edit = 0
			} = options
			options.is_edit = is_edit
			options = admin_id ? await this.updateCommonOptions(options) : options
			this.options = options
			this.$util.showLoading()
			let cur_time = new Date(Math.ceil(new Date().getTime()))
			this.endYear = this.$util.formatTime(cur_time, 'YY-M-D')
			await this.initIndex()
			let {
				coach_status
			} = this
			uni.setNavigationBarTitle({
				title: is_edit ? '编辑资料' : admin_id && [2, 3].includes(coach_status) ? `` : `申请${this.$t('action.attendantName')}`
			})
			this.isAgreement = is_edit ? true : false
			this.isLoad = true
		},
		watch: {
			'form.birthday'(newValue, oldValue) {
				if (newValue) {
					let arr = newValue.split('-')
					this.form.constellation = this.$util.getAstro(arr[1], arr[2])
				}
			}
		},
		methods: {
			...mapActions(['getConfigInfo', 'getUserInfo', 'updateCommonOptions', 'getMineInfo']),
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
				
				await this.getCityList()
				let [serviceList, data] = await Promise.all([this.$api.technician.serviceSelect({
					page: 1
				}), this.$api.technician.coachInfo()])
				this.serviceList = serviceList.data.length > 10 ? serviceList.data.slice(0, 10) : serviceList.data
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				if (data && !data.id) {
					this.broker.coach_status = 2
					this.$util.hideAll()
					return
				}
				data.birthday = this.$util.formatTime(data.birthday * 1000, 'YY-M-D')
				data.id_card = data.id_card ? data.id_card.map(item => {
					return {
						path: item
					}
				}) : []
				data.id_card_fan = data.id_card[1] ? [data.id_card[1]] : []
				data.id_card_people = data.id_card[2] ? [data.id_card[2]] : []
				data.id_card.splice(1, 3)
				data.work_img = [{
					path: data.work_img
				}]
				data.self_img = data.self_img.map(item => {
					return {
						path: item
					}
				})
				data.model_img = data.model_img ? data.model_img.map(item => {
					return {
						path: item
					}
				}) : []
				data.video = data.video && data.video.length > 0 ? [{
					path: data.video
				}] : []
				this.cityIndex = this.cityList.findIndex(item => {
					return item.id == data.city_id
				})
				for (let key in this.form) {
					this.form[key] = data[key]
				}
				if (data.status == 4 && data.sh_text) {
					this.btipArr[data.status][0].text = data.sh_text
				}
				this.broker.coach_status = data.status
				this.coach_status = data.is_update == 1 ? -1 : data.status
				this.is_update = data.is_update
				this.$util.hideAll()
			},
			initRefresh() {
				this.initIndex(true)
			},
			async getCityList() {
				let {
					location
				} = this
				if (!location.lat) {
					// #ifdef H5
					if (this.$jweixin.isWechat()) {
						this.$util.showLoading()
						// await this.$jweixin.initJssdk();
						await this.$jweixin.wxReady2();
						let {
							latitude: lat = 0,
							longitude: lng = 0
						} = await this.$jweixin.getWxLocation()
						location = {
							lng,
							lat,
							address: '定位失败',
							province: '',
							city: '',
							district: ''
						}
						if (lat && lng) {
							let key = `${lat},${lng}`
							let data = await this.$api.base.getMapInfo({
								location: key
							})
							let {
								status,
								result
							} = JSON.parse(data)
							if (status == 0) {
								let {
									address,
									address_component
								} = result
								let {
									province,
									city,
									district
								} = address_component
								location = {
									lng,
									lat,
									address,
									province,
									city,
									district
								}
							}
						}
					}
					// #endif
					// #ifndef H5
					location = await this.$util.getBmapLocation()
					// #endif

					this.updateUserItem({
						key: 'location',
						val: location
					})
				}

				let {
					lng = 0,
						lat = 0
				} = location
				if (lat && lng) {
					let city = await this.$api.base.getCity({
						lng,
						lat
					})
					this.$util.hideAll()
					this.cityList = city
					this.form.city_id = city.length > 0 ? city[0].id : 0
				}
			},
			pickerChange(e, key) {
				let val = e.target.value
				if (key === 'birthday') {
					// let unix = this.$util.DateToUnix(val)
					// if (unix > new Date(Math.ceil(new Date().getTime())) / 1000) {
					// 	this.$util.showToast({
					// 		title: `不能选择未来时间哦`
					// 	})
					// 	return
					// }
					this.form[key] = val
					return
				}
				this.cityIndex = val
				this.form.city_id = this.cityList[this.cityIndex].id
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.form[imgtype] = imagelist;
			},
			// 选择地区
			async toChooseLocation(e) {
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
				} = this.form

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
				this.form.address = address
				this.form.lng = longitude
				this.form.lat = latitude
			},
			// 选择技能
			toCheckItem(id) {
				let ind = this.form.service.findIndex(item => {
					return item == id
				})
				if (ind == -1) {
					this.form.service.push(id)
				} else {
					this.form.service.splice(ind, 1)
				}
			},
			// 去管理/再次申请
			async confirm() {
				let {
					coach_status
				} = this
				let page = {
					2: `/pages/mine?type=2`,
					3: `/technician/pages/apply`,
				}
				if(coach_status == 2){
					this.updateUserItem({
						key: 'userPageType',
						val: 2
					})
				}
				let url = page[coach_status]
				this.$util.log(url)
				this.$util.goUrl({
					url,
					openType: `reLaunch`
				})
			},
			//表单验证
			validate(param) {
				let {
					video_limit = 1
				} = this.configInfo
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					if(name == 'video' && video_limit == 0){
						param[name] = [{path: '111'}]
					}
					validate.add(param[name], item);
				})
				
				let message = validate.start();
				return message;
			},
			async submit() {
				let {
					video_limit = 1
				} = this.configInfo
				let param = this.$util.deepCopy(this.form)
				let arr = ['id_card', 'id_card_fan', 'id_card_people', 'work_img', 'video']
				arr.map(item => {
					param[item] = param[item].length > 0 ? param[item][0].path : ''
				})
				param.self_img = param.self_img.map(item => {
					return item.path
				})
				param.model_img = param.model_img.map(item => {
					return item.path
				})
				
				let msg = this.validate(param);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				if (param.self_img.length < 4){
					this.$util.showToast({
						title: `还需上传${4 - param.self_img.length}张生活照`
					});
					return
				}
				if(!this.isAgreement){
					this.$util.showToast({
						title: `请勾选《${this.$t('action.attendantName')}注册协议》《计费规则》《法律声明》`
					});
					return
				}
				let {
					admin_id = 0,
					broker_id = 0
				} = this.options
				if (admin_id) {
					param.admin_id = admin_id
				}
				if(broker_id){
					param.broker_id = broker_id
				}
				
				param.birthday = this.$util.DateToUnix(param.birthday)
				param.id_card = param.id_card_people ? [param.id_card, param.id_card_fan, param.id_card_people] : [param.id_card, param.id_card_fan]
				delete param.id_card_fan
				delete param.id_card_people
				if(video_limit == 0){
					delete param.video
				}
				if (this.lockTap) return
				this.lockTap = true
				this.$util.showLoading()
				try {
					let {
						is_edit = 0
					} = this.options
					let methodModel = param.id && is_edit ? 'coachUpdateV2' : 'coachApply'
					await this.$api.technician[methodModel](
						param)
					this.$util.hideAll()
					let msg = param.id && is_edit ? `` : `,即将跳转个人中心`
					this.$util.showToast({
						title: `提交成功${msg}`,
					})

					setTimeout(() => {
						if (param.id && is_edit) {
							this.coach_status = -1
							this.is_update = 1
							return
						}
						if (getCurrentPages().length > 1) {
							this.$util.back()
						}
						this.$util.goUrl({
							url: '/pages/mine',
							openType: `reLaunch`
						})
					}, 2000)
				} catch (e) {
					setTimeout(() => {
						this.lockTap = false
						this.$util.hideAll()
					}, 2000)
				}

			},
			changeAgreement(){
				this.isAgreement = !this.isAgreement
			},
			// 去管理/再次申请
			async bconfirm() {
				let {
					status
				} = this
				let {
					type = 1
				} = this.options
				let page = {
					1: `/technician/pages/apply`,
					2: `/user/pages/distribution/apply`,
					3: `/user/pages/channel/apply`
				}
				let url = status == 2 ? type == 1 ? `/pages/mine?type=2` : `/pages/service` : 'apply'//page[type]
				if(url == 'apply'){
					this.broker.coach_status = 2
					return
				}
				this.$util.goUrl({
					url,
					openType: status == 2 ? `reLaunch` : `redirectTo`
				})
			},
			// 返回首页
			bcancel() {
				if (getCurrentPages().length > 1) {
					this.$util.back()
					this.$util.goUrl({
						url: 1,
						openType: `navigateBack`
					})
					return
				}
				this.$util.goUrl({
					url: `/pages/mine`,
					openType: `reLaunch`
				})
			},
		}
	}
</script>


<style lang="scss">
	.apply-pages {
		.page-height {
			width: 100%;
			height: 100vh;
			background: #fff;
		}

		.apply-form {


			.item-text {
				font-size: 32rpx;
			}

			.item-input {
				font-size: 32rpx;
				color: #222;
				min-height: 76rpx;
				line-height: 40rpx;
				text-align: right;

				.iconfont {
					font-size: 40rpx
				}

				.icongengduo {
					font-size: 24rpx;
					color: #9B9B9B;
				}
			}

			.item-input.text {
				padding: 42rpx 0;
			}

			.item-input.tips {
				text-align: left;
				font-size: 26rpx;
				color: #999;
			}

			.textarea-box {
				width: 690rpx;
				background: #F7F8FA;

				.item-textarea {
					color: #333;
					height: 280rpx;
				}
			}

			.icongengduo {
				font-size: 24rpx;
				color: #9B9B9B;
			}

			.service-list {
				.list-item {
					min-width: 168rpx;
					height: 68rpx;
					padding: 0 20rpx;
					color: #4A4A4A;
					background: #F6F7F8;
					border-radius: 34rpx;
				}

				.list-item.abs {
					opacity: 0.1;
					border-radius: 34rpx;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					z-index: 1;
				}
			}
		}
	}
	.broker-pages{
		width: 100%;
		height: 100%;
		z-index: 999;
		position: fixed;
		left: 0;
		top: 0;
		background: #fff;
	}
</style>
