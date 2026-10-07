import $util from "@/utils/index.js"
import $api from "@/api/index.js"
export default {
	state: {
		old_attendant_name: '向导',
		configInfo: {
			id: 0,
			isIos: uni.getSystemInfoSync().system.includes('iOS'),
			navBarHeight: uni.getSystemInfoSync().statusBarHeight * 1 + 44,
			curSysHeight: '',
			tabbarHeight: '',
			methodObj: {
				1: 'call',
				2: 'miniProgram',
				3: 'web',
				4: 'navigateTo'
			},
			tabBar: [{
				id: 1,
				name: '首页',
				default_img: 'iconshouye2',
				selected_img: 'iconshouye12'
			}, {
				id: 2,
				name: '向导',
				default_img: 'iconpeiwanguan1',
				selected_img: 'iconpeiwanguan2'
			}, 
			// {
			// 	id: 4,
			// 	name: '订单',
			// 	default_img: 'icondingdan3',
			// 	selected_img: 'icondingdan2'
			// }, 
			{
				id: 8,
				name: '门店',
				default_img: 'iconmendian11',
				selected_img: 'iconmendian1'
			},
			{
				id: 5,
				name: '我的',
				default_img: 'iconwode-2',
				selected_img: 'iconwode-1'
			}],
			page: {
				1: []
			}
		},
		pageArr: ['/pages/service', '/pages/technician', '/pages/store', '/pages/find','/pages/dynamic',
			'/pages/order', '/pages/mine'
		],
		audioBg: {},
		playBg: false,
		isHaveAudio: false,
		avatarUrl: 'https://lbqnyv2.migugu.com/defaultAvatar.png', //默认头像
	},
	mutations: {
		//修改信息
		updateConfigItem(state, item) {
			let {
				key,
				val
			} = item
			state[key] = val
			if (key !== 'configInfo') return
			uni.setStorageSync('configInfo', val)
			let {
				countdown_voice
			} = val
			if (state.isHaveAudio) {
				state.audioBg.src = countdown_voice
				return
			}
			state.audioBg = uni.createInnerAudioContext();
			state.isHaveAudio = true
			state.audioBg.src = countdown_voice
			// #ifndef APP-PLUS
			state.audioBg.obeyMuteSwitch = false
			// #endif
			let play_method = [{
				method: 'onPlay',
				msg: '开始播放',
				status: true,
			}, {
				method: 'onStop',
				msg: '结束播放',
				status: false,
			}, {
				method: 'onError',
				msg: '报错Error',
				status: false,
			}, {
				method: 'onEnded',
				msg: '自然结束播放',
				status: false,
			}];
			play_method.map(item => {
				state.audioBg[item.method](() => {
					console.log('bg=>', item.msg)
					state.playBg = item.status;
				})
			})
		}
	},
	actions: {
		// 获取基本配置
		async getConfigInfo({
			commit,
			state
		}, param) {
			let [config, plugAuth] = await Promise.all([$api.base.configInfo(), $api.base.plugAuth()])
			config.plugAuth = plugAuth
			if (!config.primaryColor) {
				config.primaryColor = '#843CFF'
			}
			if (!config.subColor) {
				config.subColor = '#DF52FF'
			}
			if (!config.gradualColor) {
				config.gradualColor = '#DF52FF'
			}
			if (!config.user_image) {
				config.user_image = 'https://lbqny.migugu.com/admin/anmo/mine/bg.png'
			}
			if (!config.coach_image) {
				config.coach_image = 'https://lbqny.migugu.com/admin/anmo/mine/bg.png'
			}
			if (!config.service_btn_color) {
				config.service_btn_color = '#282B34'
			}
			if (!config.service_font_color) {
				config.service_font_color = '#EBDDB1'
			}
			if (!config.user_font_color) {
				config.user_font_color = '#ffffff'
			}
			if (!config.coach_font_color) {
				config.coach_font_color = '#ffffff'
			}

			let base_config = $util.pick(state.configInfo, ['isIos', 'navBarHeight', 'curSysHeight',
				'tabbarHeight', 'methodObj', 'tabBar'
			])

			let {
				dynamic = false,
				store = false,
				demand = false,
				storeplus = false,
				seckill = false
			} = plugAuth
			
			let {
				tabBar = []
			} = base_config

			let mineInd = tabBar.findIndex(item => {
				return item.default_img == 'iconpeiwanguan1'
			})

			if (mineInd == -1) {
				tabBar = [{
					id: 1,
					name: '首页',
					default_img: 'iconshouye2',
					selected_img: 'iconshouye12'
				}, {
					id: 2,
					name: '向导',
					default_img: 'iconpeiwanguan1',
					selected_img: 'iconpeiwanguan2'
				}, 
				// {
				// 	id: 4,
				// 	name: '订单',
				// 	default_img: 'icondingdan3',
				// 	selected_img: 'icondingdan2'
				// }, 
				// {
				// 	id: 8,
				// 	name: '门店',
				// 	default_img: 'iconmendian11',
				// 	selected_img: 'iconmendian2'
				// },
				{
					id: 5,
					name: '我的',
					default_img: 'iconwode-2',
					selected_img: 'iconwode-1'
				}]
			}

			let ind = tabBar.findIndex(item => {
				return item.name == '动态'
			})

			if (ind == -1) { // dynamic && 
				tabBar.splice(2, 0, {
					id: 3,
					name: '动态',
					default_img: 'icon-dongtai1',
					selected_img: 'icon-dongtai2'
				})
			}
			if (ind !== -1) { // !dynamic
				tabBar.splice(ind, 1)
			}

			let storeInd = config.tabBar.findIndex(item => {
				return item.id == 8 // '门店'
			})

			// if (storeplus && storeInd == -1) {
			// 	config.tabBar.splice(2, 0, {
			// 		id: 8,
			// 		name: '门店',
			// 		default_img: 'iconmendian11',
			// 		selected_img: 'iconmendian1'
			// 	})
			// }
			
			if (!storeplus && storeInd !== -1) {
				config.tabBar.splice(storeInd, 1)
			}
			
			let seckillInd = config.tabBar.findIndex(item => {
				return item.id == 9 // '秒杀'
			})
			
			// 默认顶部数据
			if(config.page[9] && config.page[9].length == 0){
				config.page[9] = [{type: ''}]
			}
			
			if (seckill&&store&&storeplus && seckillInd == -1 && config.page[9]) {
				config.tabBar.splice(2, 0, {
					id: 9,
					name: '秒杀',
					default_img: 'iconmiaosha',
					selected_img: 'iconmiaosha-2'
				})
			}
			
			if ((!seckill || !store || !storeplus) && seckillInd !== -1) {
				config.tabBar.splice(seckillInd, 1)
			}
			
			
			let findInd = tabBar.findIndex(item => {
				return item.name == '发现'
			})
			
			if (demand && findInd == -1) {
				tabBar.splice(2, 0, {
					id: 7,
					name: '发现',
					default_img: 'iconfaxian-2',
					selected_img: 'iconfaxian-1'
				})
			}
			if (!demand && findInd !== -1) {
				tabBar.splice(findInd, 1)
			}
			
			// 后台diy配置顺序
			base_config.tabBar = config.tabBar
			let data = Object.assign({}, config, base_config)
			commit('updateConfigItem', {
				key: 'configInfo',
				val: data
			})
		},
		// 获取插件授权
		async getPlugAuth({
			commit,
			state
		}, param) {
			let plugAuth = await $api.base.plugAuth()
			let data = Object.assign({}, state.configInfo, {
				plugAuth
			})
			commit('updateConfigItem', {
				key: 'configInfo',
				val: data
			})
		},
		toPlayAudio({
			commit,
			state
		}, param) {
			if (state.playBg) {
				state.audioBg.stop()
			}
			state.audioBg.play()
		}
	}
}
