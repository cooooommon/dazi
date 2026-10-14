//统一获取系统信息：
//H5 端从浏览器 UA/尺寸构造（uni 的 getSystemInfoSync 在 H5 会触发
//"getAppAuthorizeSetting is not yet implemented" 等未实现警告）；
//小程序端走新的分拆 API 合并（getWindowInfo/getDeviceInfo/getAppBaseInfo），
//旧基础库自动回退 uni.getSystemInfoSync
export function getSystemInfo() {
	// #ifdef H5
	const ua = navigator.userAgent
	const ios = /iPhone|iPad|iPod/i.test(ua)
	return {
		platform: ios ? 'ios' : 'android',
		system: ios ? 'iOS' : 'Android',
		model: '',
		statusBarHeight: 0,
		windowWidth: window.innerWidth,
		windowHeight: window.innerHeight,
		screenWidth: window.screen.width,
		screenHeight: window.screen.height,
		language: navigator.language,
		version: '',
		SDKVersion: ''
	}
	// #endif
	// #ifndef H5
	try {
		if (typeof wx !== 'undefined' && wx.getWindowInfo && wx.getDeviceInfo && wx.getAppBaseInfo) {
			let merged = Object.assign({},
				wx.getWindowInfo(),
				wx.getDeviceInfo(),
				wx.getAppBaseInfo()
			)
			// #ifdef MP-WEIXIN
			if (wx.getAppAuthorizeSetting) {
				Object.assign(merged, wx.getAppAuthorizeSetting())
			}
			// #endif
			return merged
		}
	} catch (e) {}
	return uni.getSystemInfoSync()
	// #endif
}
