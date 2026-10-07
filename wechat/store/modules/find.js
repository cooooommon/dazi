import $util from "@/utils/index.js"
import $api from "@/api/index.js"
import {
	req
} from '@/utils/req.js';
export default {
	state: {
		pageActive: false,
		activeIndex: -1,
		tabList: [{
			title: '附近',
			id: 0
		}, {
			title: '服务分类',
			id: 1,
			// number: 10
		}, {
			title: '价格区间',
			id: 2
		}],
		distanceList:[
			{label:'由近及远',value:1},
			{label:'由远及近',value:2}
		],
		priceList:[
			{label:'全部',value:{start_price:'',end_price:''}},
			{label:'100以下',value:{start_price:0,end_price:100}},
			{label:'100-300',value:{start_price:100,end_price:300}},
			{label:'300-500',value:{start_price:300,end_price:500}},
			{label:'500-1000',value:{start_price:500,end_price:1000}},
			{label:'1000以上',value:{start_price:1000,end_price:''}},
		],
		param: {
			page: 1,
			pay_type: 0,
			lat:'',
			lng:'',
			limit:10,
			ser_id:'',//服务类型
			distance: 1, //附近排序 1由近到远 2由远到近
			coach_id: 0
		},
		list: {
			data: [],
			last_page: 1,
			current_page: 1
		},
		carList: {},
		skill: [],
		haveOperItem: false,
	},
	mutations: {
		updateFindItem(state, item) {
			let {
				key,
				val
			} = item
			state[key] = val
		}
	},
	actions: {
		async getFindList({
			commit,
			state
		}, param) {
			let d = await $api.find.demandOrderList(param)
			let oldList = state.list;
			let newList = d;
			let list = {}
			newList.data.forEach(item=> item.isMore = false)
			if (param.page == 1) {
				list = newList;
			} else {
				newList.data = oldList.data.concat(newList.data)
				list = newList;
			}
			commit('updateFindItem', {
				key: 'list',
				val: list
			})
		},
		// 获取购物车数据
		async getCarList({
			commit,
			state
		}, param) {
			let carList = await $api.order.carInfo(param)
			carList.list.map(item => {
				item.checked = false
			})
			commit('updateFindItem', {
				key: 'carList',
				val: carList
			})
		},
		async getSkill({
			commit,
			state
		}, param) {
			let d = await $api.find.getSkill()
			d.unshift({id:0,name:'全部'})
			commit('updateFindItem', {
				key: 'skill',
				val: d
			})
		}
	}
}
