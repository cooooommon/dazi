import {
	req,
	uploadFile
} from '../../utils/req.js';
export default {
	//虚拟支付订单状态查询（未支付时服务端会顺带查单兜底并发货）
	orderStatus(param) {
		return req.post("virtualpay/index/orderStatus", param)
	},
}
