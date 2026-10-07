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
			order: 1
		},
		list: {
			data: [],
			last_page: 1,
			current_page: 1
		},
		tabList: [{title: '智能排序',id: 1},{title: '销量优先',id: 2},{title: '距离优先',id: 3}],
		activeIndex: 0,
		storeType: [],
		diyBanner: [],
		isStorePage: true
	},
	mutations: {
		async updateSeckillItem(state, item) {
			let {
				key,
				val
			} = item
			state[key] = val
		}
	},
	actions: {
		async getSeckillList({
			commit,
			state
		}, param) {
			param.order = state.activeIndex + 1
			let d = await $api.business.seckillList(param)
			let oldList = state.list;
			let newList = d;
			let list = {}
			if (param.page == 1) {
				list = newList;
			} else {
				newList.data = oldList.data.concat(newList.data)
				list = newList;
			}
			commit('updateSeckillItem', {
				key: 'param',
				val: param
			})
			commit('updateSeckillItem', {
				key: 'list',
				val: list
			})
		},
		async getSeckillTypeList({
			commit,
			state
		}, param) {
			let d = await $api.shopstore.storeTypeList(param)
			d.forEach(item => {
				item.icon = item.img
				item.cate_name = item.name
			})
			commit('updateSeckillItem', {
				key: 'storeType',
				val: d
			})
		}
	},
}
