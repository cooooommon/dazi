import $util from "@/utils/index.js"
import $api from "@/api/index.js"
export default {
	state: {
		pageActive: false,
		haveOperItem: false,
		param: {
			page: 1,
			name: '',
			lng: 0,
			lat: 0,
			limit: 10,
			type_id: ''
		},
		list: {
			data: [],
			last_page: 1,
			current_page: 1
		},
		storeType: [],
		diyBanner: [],
		isStorePage: true
	},
	mutations: {
		async updateStoreItem(state, item) {
			let {
				key,
				val
			} = item
			state[key] = val
		}
	},
	actions: {
		async getStoreList({
			commit,
			state
		}, param) {
			let d = await $api.business.getList(param)
			let oldList = state.list;
			let newList = d;
			let list = {}
			if (param.page == 1) {
				list = newList;
			} else {
				newList.data = oldList.data.concat(newList.data)
				list = newList;
			}
			commit('updateStoreItem', {
				key: 'param',
				val: param
			})
			commit('updateStoreItem', {
				key: 'list',
				val: list
			})
		},
		async getStoreTypeList({
			commit,
			state
		}, param) {
			let d = await $api.shopstore.storeTypeList(param)
			d.forEach(item => {
				item.icon = item.img
				item.cate_name = item.name
			})
			commit('updateStoreItem', {
				key: 'storeType',
				val: d
			})
		}
	},
}
