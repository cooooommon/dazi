/*
 * @Descripttion:
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 17:34:18
 */
import Vue from 'vue'
import Router from 'vue-router'
// import Layout from '@/components/layout'

Vue.use(Router)

const constantRoutes = [
  // 无权限路由
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/view/login'),
    hidden: true
  },
  // 404
  {
    path: '/404',
    name: '404',
    component: () => import('@/view/404'),
    hidden: true
  }
]

// 路由表
export default new Router({
  mode: 'hash',
  linkActiveClass: 'active',
  scrollBehavior: () => {
    return {
      x: 0,
      y: 0
    }
  },
  routes: constantRoutes
})
