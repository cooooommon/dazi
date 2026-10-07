//网络错误
const networkError = function({
	code = 0,
	msg = '网络异常'
} = {}) {
	if (code !== 0) return;
	uni.showToast({
		title: msg,
		icon: 'none',
		duration: 4000
	})
}
//服务器错误处理
const serverError = function({
	code = -1,
	msg = '服务器错误'
} = {}) {
	uni.showModal({
		title: '提示',
		content: msg,
		showCancel: false
	})
}
//错误信息
const msgError = function({
	msg = '错误'
} = {}) {
	if (msg.length > 20) {
		uni.showModal({
			title: '',
			content: msg,
			showCancel: false
		})
	} else {
		uni.showToast({
			title: msg,
			icon: 'none',
			duration: 4000
		})
	}
}
export {
	networkError,
	serverError,
	msgError
}
