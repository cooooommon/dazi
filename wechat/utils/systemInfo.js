//统一获取系统信息：微信新基础库已废弃 getSystemInfoSync（拆分为
//getWindowInfo / getDeviceInfo / getAppBaseInfo 等），此处把新 API 合并成
//旧版同构对象；旧基础库或非微信端自动回退 uni.getSystemInfoSync
export function getSystemInfo() {
	try {
		if (typeof wx !== 'undefined' && wx.getWindowInfo && wx.getDeviceInfo && wx.getAppBaseInfo) {
			return Object.assign({},
				wx.getWindowInfo(),
				wx.getDeviceInfo(),
				wx.getAppBaseInfo(),
				wx.getAppAuthorizeSetting ? wx.getAppAuthorizeSetting() : {}
			)
		}
	} catch (e) {}
	return uni.getSystemInfoSync()
}
