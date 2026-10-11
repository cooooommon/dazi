/**
 * 静态资源地址（走后端域名，不写死）
 *
 * 资源统一放在后端 public/assets 下，本地路径与来源地址一一对应。
 * 例：assetUrl('admin/peiwan/bg.png')
 *  -> <后端 public 根>/assets/admin/peiwan/bg.png
 *
 * 后端 public 根取自 lbConfig.jsPath（由后端注入 index.html）：
 *   微擎环境 = /addons/<模块名>/core2/public/     独立部署 = /
 * 与 lbUeditor.vue 里加载 UEditor 用的是同一套机制。
 *
 * 模板里用 $util.assetUrl(...)；JS 里 import 本模块后直接调用 assetUrl(...)。
 */
export default function assetUrl (path) {
  const p = String(path || '').replace(/^\/+/, '')
  const base = (process.env.NODE_ENV === 'production' && window.lbConfig && window.lbConfig.jsPath)
    ? window.lbConfig.jsPath
    : '/'
  return base + 'assets/' + p
}
