import $util from "@/utils/index.js"
import $api from "@/api/index.js"
export default {
	state: {
		pageActive: false,
		haveOperItem: false,
		param: {
			page: 1,
			coach_name: ''
		},
		list: {
			data: [],
			last_page: 1,
			current_page: 1
		}
	},
	mutations: {
		async updateShopstoreItem(state, item) {
			let {
				key,
				val
			} = item
			state[key] = val
		}
	},
	actions: {
		async getShopstoreList({
			commit,
			state
		}, param) {
			let d = await $api.shopstore.storeList(param)
			let oldList = state.list;
			let newList = d;
			let list = {}
			if (param.page == 1) {
				list = newList;
			} else {
				newList.data = oldList.data.concat(newList.data)
				list = newList;
			}
			commit('updateShopstoreItem', {
				key: 'param',
				val: param
			})
			commit('updateShopstoreItem', {
				key: 'list',
				val: list
			})
		}
	},
}
