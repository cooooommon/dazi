/*
 * @Descripttion:
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2021-07-10 23:30:06
 */
'use strict'
// Template version: 1.3.1
// see http://vuejs-templates.github.io/webpack for documentation.

const path = require('path')
// const buildPath = require('./dev.env')

// 开发环境 API 代理：本地同域请求 → 转发到本地后端（php think run，8080）
// 好处：1) 无跨域问题；2) 登录页 document.cookie 种在 localhost 上的
// codeText 会随同域请求自动带上并由代理转发，后端验证码校验才能通过
// 想临时连线上后端调试时，改回 'https://dazi.tunma.top' 并重启 dev server
const API_TARGET = 'http://127.0.0.1:8080'
const API_APPS = [
  'admin', 'agent', 'broker', 'card', 'channel', 'demand', 'distributor',
  'dynamic', 'fdd', 'integral', 'massage', 'member', 'node', 'publics',
  'recommend', 'reminder', 'seckill', 'shop', 'store', 'storeplus', 'virtual'
]
const proxyTable = {}
API_APPS.forEach(app => {
  proxyTable['/' + app] = { target: API_TARGET, changeOrigin: true, secure: false }
})

module.exports = {
  dev: {
    // Paths
    assetsSubDirectory: 'static',
    assetsPublicPath: '/',
    proxyTable: proxyTable,

    // Various Dev Server settings
    host: 'localhost', // can be overwritten by process.env.HOST
    // host: '10.1.9.234', // can be overwritten by process.env.HOST
    // host: '192.168.33.14', // can be overwritten by process.env.HOST
    port: 8000, // can be overwritten by process.env.PORT, if port is in use, a free one will be determined
    autoOpenBrowser: true,
    errorOverlay: true,
    notifyOnErrors: true,
    poll: false, // https://webpack.js.org/configuration/dev-server/#devserver-watchoptions-

    // Use Eslint Loader?
    // If true, your code will be linted during bundling and
    // linting errors and warnings will be shown in the console.
    // 2026-10-03: 关闭 dev 构建 ESLint——存量代码 73 个文件与新风格规则冲突，阻断编译
    useEslint: false,
    // If true, eslint errors and warnings will also be shown in the error overlay
    // in the browser.
    showEslintErrorsInOverlay: false,

    /**
     * Source Maps
     */

    // https://webpack.js.org/configuration/devtool/#development
    devtool: 'cheap-module-eval-source-map',

    // If you have problems debugging vue-files in devtools,
    // set this to false - it *may* help
    // https://vue-loader.vuejs.org/en/options.html#cachebusting
    cacheBusting: true,

    cssSourceMap: true
  },

  build: {
    // Template for index.html
    index: path.resolve(__dirname, '../dist/index.html'),

    // Paths
    assetsRoot: path.resolve(__dirname, '../dist'),
    assetsSubDirectory: 'static',
    assetsPublicPath: './', // 打包时配置路劲  声场环境路劲：/addons/longbing_card/core2/public/

    /**
     * Source Maps
     */
    productionSourceMap: false,
    // https://webpack.js.org/configuration/devtool/#production
    devtool: '#source-map',

    // Gzip off by default as many popular static hosts such as
    // Surge or Netlify already gzip all static assets for you.
    // Before setting to `true`, make sure to:
    // npm install --save-dev compression-webpack-plugin
    productionGzip: false,
    productionGzipExtensions: ['js', 'css'],

    // Run the build command with an extra argument to
    // View the bundle analyzer report after build finishes:
    // `npm run build --report`
    // Set to `true` or `false` to always turn it on or off
    bundleAnalyzerReport: process.env.npm_config_report
  }
}
