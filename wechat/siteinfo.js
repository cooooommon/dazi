/**
 * 站点配置 —— 小程序所有接口地址、静态资源地址的唯一来源
 *
 * siteroot 会随构建模式自动切换：
 *   「运行」（HBuilderX 运行到小程序模拟器/浏览器）→ 本地后端
 *   「发行」（HBuilderX 发行/云打包）              → 线上 https://dazi.tunma.top
 *
 * 注意：两端都必须保留 /index.php，req.js 与 utils/asset.js 都按它来切分
 *      接口基址（split('/index.php')[0]）和静态资源基址。
 */
// 本地后端：nginx(8080) -> backend/public
// H5 下取当前页面的主机名 —— 桌面访问是 127.0.0.1，手机连 LAN 访问则是局域网 IP，
// 写死 127.0.0.1 的话手机会指向它自己。小程序里没有 window.location，固定用 127.0.0.1。
// #ifdef H5
var LOCAL_SITEROOT = 'http://' + (window.location.hostname || '127.0.0.1') + ':8080/index.php'
// #endif
// #ifndef H5
var LOCAL_SITEROOT = 'http://127.0.0.1:8080/index.php'
// #endif

var ONLINE_SITEROOT = 'https://dazi.tunma.top/index.php' // 线上

// process.env.NODE_ENV 由构建工具在编译期替换：运行=development，发行=production
var isDev = process.env.NODE_ENV !== 'production'

module.exports = {
	// 陪玩
	"uniacid": "666",
	"multiid": "0",
	"version": "3.0",
	"gzh_appid": "wx0253be52582aa0aa",
	"siteroot": isDev ? LOCAL_SITEROOT : ONLINE_SITEROOT
}
