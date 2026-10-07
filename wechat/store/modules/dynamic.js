import $util from "@/utils/index.js"
import $api from "@/api/index.js"
export default {
	state: {
		pageActive: false,
		activeIndex: 0,
		haveOperItem: false,
		tabList: [{
			title: '推荐',
			id: 0,
		}, {
			title: '关注',
			id: 1,
			number: 0
		}],
		param: {
			page: 1,
			coach_name: ''
		},
		list: {
			data: [],
			last_page: 1,
			current_page: 1
		},
		follow_dynamic_num: 0,
		coach_status: -1
	},
	mutations: {
		async updateDynamicItem(state, item) {
			let {
				key,
				val
			} = item
			state[key] = val
		}
	},
	actions: {
		async getDynamicIndex({
			commit,
			state
		}, param) {
			let d = await $api.dynamic.getFollowData(param)
			commit('updateDynamicItem', {
				key: 'follow_dynamic_num',
				val: d.num
			})
			commit('updateDynamicItem', {
				key: 'coach_status',
				val: d.coach_status
			})
			let tabList = $util.deepCopy(state.tabList)
			tabList[1].number = d.num
			commit('updateDynamicItem', {
				key: 'tabList',
				val: tabList
			})
		},
		async getDynamicList({
			commit,
			state
		}, param) {
			let methodModel = state.activeIndex == 0 ? 'dynamicList' : 'followDynamicList'
			let d = await $api.dynamic[methodModel](param)
			let oldList = state.list;
			let newList = d;
			let list = {}
			if (param.page == 1) {
				list = newList;
			} else {
				newList.data = oldList.data.concat(newList.data)
				list = newList;
			}
			commit('updateDynamicItem', {
				key: 'param',
				val: param
			})
			commit('updateDynamicItem', {
				key: 'list',
				val: list
			})
		}
	},
}
