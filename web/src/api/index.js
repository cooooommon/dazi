import Vue from '@/main.js'
import axios from 'axios'
// import qs from 'qs'
import router from '../router'
import store from '../store'
// api 模块化
import apis from './modules'
// 开发环境走同域相对路径，由 webpack-dev-server 的 proxyTable 转发到线上后端
// （直连线上域名会跨域，登录用的 codeText cookie 也无法随跨站请求携带）
let developmentURL = `/`
// axios.defaults.withCredentials = true
axios.defaults.timeout = 300000
axios.defaults.baseURL = process.env.NODE_ENV === 'development' ? developmentURL : getProCurrentHref()
axios.interceptors.request.use(config => {
  if (window.localStorage.getItem('massage_minitk')) {
    config.headers['token'] = window.localStorage.getItem('massage_minitk')
    // 'Content-Type': 'application/x-www-form-urlencoded' // application/json
  }
  return config
}, error => {
  return Promise.reject(error)
})
axios.interceptors.response.use(res => {
  if (res.data.code === 401) { // token验证不合法或者没有
    Vue.$message.error(res.data.error)
    store.dispatch('changeMutaRoutesItem', { key: 'isAuth', val: false })
    window.localStorage.removeItem('massage_minitk')
    window.localStorage.removeItem('massage_ms_username')
    window.localStorage.removeItem('massage_userInfo')
    router.push('/login')
  } else if (res.data.code === 402) {
    Vue.$message.error(res.data.error)
  } else if (res.data.code === 400) {
    Vue.$message.error(res.data.error)
  }
  return res
}, err => {
  Vue.$message.error(err.message + ',请稍后再试')
  return Promise.reject(err.response)
})

/**
 * 封装get方法
 * @param url
 * @param data
 * @returns {Promise}
 */

export function get (url, params = {}, type = 'application/json') {
  return new Promise((resolve, reject) => {
    axios.get(url, {
      params: params,
      headers: {
        'Content-Type': type
      }
    })
      .then(response => {
        resolve(response.data)
      })
      .catch(err => {
        reject(err)
      })
  })
}

/**
 * 封装post请求
 * @param url
 * @param data
 * @returns {Promise}
 */

export function post (url, data = {}, type = 'application/json') {
  return new Promise((resolve, reject) => {
    axios.post(url, data, {
      headers: {
        'Content-Type': type
      }
    }).then(response => {
      resolve(response.data)
    }, err => {
      console.log(err)
      reject(err)
    })
  })
}

/**
 * 封装patch请求
 * @param url
 * @param data
 * @returns {Promise}
 */

export function patch (url, data = {}, type = 'application/json') {
  return new Promise((resolve, reject) => {
    axios.patch(url, data)
      .then(response => {
        resolve(response.data)
      }, err => {
        reject(err)
      })
  })
}

/**
 * 封装put请求
 * @param url
 * @param data
 * @returns {Promise}
 */

export function put (url, data = {}, type = 'application/json') {
  return new Promise((resolve, reject) => {
    axios.put(url, data)
      .then(response => {
        resolve(response.data)
      }, err => {
        reject(err)
      })
  })
}

/**
 * 封装del请求
 * @param url
 * @param data
 * @returns {Promise}
 */

export function del (url, params = {}, type = 'application/json') {
  return new Promise((resolve, reject) => {
    axios.delete(url, {
      params: params,
      headers: {
        'Content-Type': type
      }
    })
      .then(response => {
        resolve(response.data)
      })
      .catch(err => {
        reject(err)
      })
  })
}

/**
 * 封装post上传图片、视频、音频、文件的接口
 * @param url
 * @param data
 * @returns {Promise}
 */

export function postUpload (url, data = {}, progressCallback) {
  return new Promise((resolve, reject) => {
    axios.post(url, data, {
      headers: {
        'Content-Type': 'application/json'
      },
      onUploadProgress: progressCallback
    }) // qs.stringify(data)
      .then(response => {
        resolve(response.data)
      }, err => {
        console.log(err)
        reject(err)
      })
  })
}

/**
 * @method 获取七牛token
 * @param url
 * @param data
 * @returns {Promise}
 */
export function getQiniuToken (url, data = {}) {
  return new Promise((resolve, reject) => {
    axios.get(url, data)
      .then(response => {
        resolve(response.data)
      }, err => {
        reject(err)
      })
  })
}

/**
 * 获取数据的接口
 */
export const api = {
  ...apis
}

function getProCurrentHref () {
  let _href = window.location.href
  let index = _href.indexOf('#')
  let newHref = _href.slice(0, index)
  newHref = window.lbConfig.isWe7 ? newHref + '&s=' : window.location.origin
  return newHref
}
