import $api from "@/api/index.js"
export default {
	state: {
		pageActive: false,
		activeIndex: 0,
		tabList: [{
			title: '全部',
			sort: 'top desc',
		}, {
			title: '价格',
			sort: 'price',
			sign: 0,
		}, {
			title: '邀约量',
			sort: 'total_sale',
			sign: 0,
		}, {
			title: '好评度',
			sort: 'star',
			sign: 0
		}],
		param: {
			page: 1,
			sort: '',
			name: ''
		},
		indexParam: {
			page: 1,
			limit: 10
		},
		list: {
			data: [],
			last_page: 1,
			current_page: 1
		},
		banner: [],
		diyBanner: [],
		recommend_list: {
			data: []
		},
		recommend_style: 1
	},
	mutations: {
		async updateServiceItem(state, item) {
			let {
				key,
				val
			} = item
			state[key] = val
		}
	},
	actions: {
		async getServiceIndex({
			commit,
			state
		}, param) {
			let d = await $api.service.index(param);
			let {
				banner = [],
					recommend_list = {},
					recommend_style = 1
			} = d
			if(banner.length > 0){
				banner.forEach(item=>{
					item.jump_type = item.banner_type == 2 ? 'video' : 'image'
					item.jump_url = item.banner_type == 2 ? item.video_url : ''
				})
			}
			commit('updateServiceItem', {
				key: 'banner',
				val: banner
			})
			let oldList = state.recommend_list;
			let newList = recommend_list;
			let list = {}
			if (param.page == 1) {
				list = newList;
			} else {
				newList.data = oldList.data.concat(newList.data)
				list = newList;
			}
			commit('updateServiceItem', {
				key: 'recommend_list',
				val: list
			})
			commit('updateServiceItem', {
				key: 'recommend_style',
				val: recommend_style
			})
		},
		async getServiceList({
			commit,
			state
		}, param) {
			let d = await $api.service.serviceList(param)
			let oldList = state.list;
			let newList = d;
			let list = {}
			if (param.page == 1) {
				list = newList;
			} else {
				newList.data = oldList.data.concat(newList.data)
				list = newList;
			}
			commit('updateServiceItem', {
				key: 'list',
				val: list
			})
		}
	},
}
