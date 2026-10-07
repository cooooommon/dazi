import $api from "@/api/index.js"
import $store from "@/store/index.js"
export default {
	state: {
		pageActive: false,
		activeIndex: 0,
		haveOperItem: false,
		tabList: [{
			title: '全部',
			id: 0,
		}, {
			title: '可服务',
			id: 1,
		}, {
			title: '服务中',
			id: 2
		}, {
			title: '可预约',
			id: 3
		}],
		cityId: 0,
		cityIndex: -1,
		cityList: [],
		param: {
			page: 1,
			ser_id: 0,
			ser_title: '全部服务',
			sex: -1,
			coach_name: '',
			order: 0
		},
		list: {
			data: [],
			last_page: 1,
			current_page: 1
		}
	},
	mutations: {
		async updateTechnicianItem(state, item) {
			let {
				key,
				val
			} = item
			state[key] = val
		}
	},
	actions: {
		async getCityList({
			commit,
			state
		}, param) {
			let {
				change = 0
			} = param
			let params = JSON.parse(JSON.stringify(param))
			delete params.change
			let d = await $api.base.getCity(params);
			let {
				cityId = 0
			} = state
			let ind = d.findIndex(v => {
				return change || !cityId ? v.is_select : v.id == cityId
			})
			console.log(d, ind)
			commit('updateTechnicianItem', {
				key: 'cityList',
				val: d
			})
			let cityIndex = ind
			// let cityIndex = ind === -1 ? 0 : ind
			commit('updateTechnicianItem', {
				key: 'cityIndex',
				val: cityIndex
			})
			commit('updateTechnicianItem', {
				key: 'cityId',
				val: d && d.length > 0 && cityIndex !== -1 ? d[cityIndex].id : 0
			})
		},
		async getServiceCoachList({
			commit,
			state
		}, param) {
			let {
				coach_format = 1
			} = $store.state.config.configInfo
			if (param.sex == 2) {
				delete param.sex
			}
			delete param.ser_title
			let methodModel = coach_format == 1 ? 'serviceCoachList' : 'typeServiceCoachList'
			let d = await $api.service[methodModel](param)
			let oldList = state.list;
			let newList = d;
			let list = {}
			if (param.page == 1) {
				list = newList;
			} else {
				newList.data = oldList.data.concat(newList.data)
				list = newList;
			}
			commit('updateTechnicianItem', {
				key: 'list',
				val: list
			})
		}
	},
}
