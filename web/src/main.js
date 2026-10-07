/*
 * @Descripttion:
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-20 18:28:52
 */
// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import i18n from './i18n' // 语言包
import ElementUI from 'element-ui'
import store from './store'
import router from './router'
import 'element-ui/lib/theme-chalk/index.css'
import {
  api
} from './api' // 接口
import routes1 from './permission'
import routes2 from './permission2'
import util from './utils/public'
import { reg } from './utils/reg'
import Print from './utils/print'
import './components/basics'
import Nprogress from 'nprogress'
import vuescroll from 'vuescroll'
import 'nprogress/nprogress.css'
import 'vuescroll/dist/vuescroll.css'
import '../static/Ueditor/ueditor.config.js'
import '../static/Ueditor/ueditor.all.js'
import '../static/Ueditor/lang/zh-cn/zh-cn.js'

import VueClipboard from 'vue-clipboard2'
// 全局directive指令
import directives from './directives'

// import * as fundebug from 'fundebug-javascript'
// import fundebugVue from 'fundebug-vue'
Vue.use(VueClipboard)
Vue.use(vuescroll)

// 注册本页全局指令方法
Object.keys(directives).forEach(key => {
  Vue.directive(key, directives[key])
})

Vue.config.productionTip = false
Vue.use(ElementUI, {
  size: 'big',
  zIndex: 3000
})
Vue.use(Print)
// $t 兜底：页面销毁后 vue-i18n 会在 nextTick 把实例的 _i18n 置空，
// 此时在途异步回调里的 this.$t 会抛 Cannot read properties of null。
// 回退到全局 i18n 实例，保证切换页面时在途请求不报错。
const raw$T = Vue.prototype.$t
Vue.prototype.$t = function (key) {
  var values = []
  var len = arguments.length - 1
  while (len-- > 0) values[len] = arguments[len + 1]
  if (this && this._i18n) return raw$T.apply(this, [key].concat(values))
  return i18n.t.apply(i18n, [key].concat(values))
}
Vue.prototype.$api = api
Vue.prototype.$util = util
Vue.prototype.$reg = reg
// fundebug.apikey = '56f27b98d76e39ea573b64a5f751972e07ca07308451598fb3183dff3d4b6651'
// fundebugVue(fundebug, Vue)
router.beforeEach((to, from, next) => {
  Nprogress.start()
  if (to.path === '/login') {
    if (window.localStorage.getItem('massage_minitk')) {
      next('/')
    } else {
      next()
    }
  } else if (!store.getters.isAuth) {
    api.base.getIsWeiVersion().then(res => {
      if (res.code === 200) {
        if (!window.localStorage.getItem('massage_minitk') && !res.data) {
          next('/login')
        } else {
          let userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
          let routes = userInfo.is_admin === 0 ? routes2 : routes1
          store.dispatch('getUserPromission', {
            to,
            next,
            routes
          })
        }
      }
    })
  } else {
    // if (from.name === 'TechnicianManage') {
    //   let userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
    //   let routes = userInfo.is_admin === 0 ? routes2 : routes1
    //   store.dispatch('getUserPromission', {
    //     to,
    //     next,
    //     routes
    //   })
    // } else {
    next()
    // }
  }
})
router.afterEach(() => {
  Nprogress.done()
})

/* eslint-disable no-new */
let vue = new Vue({
  el: '#app',
  router,
  i18n,
  store,
  components: {
    App
  },
  template: '<App/>'
})
export default vue
