/*
 * @Description: 
 * @Author: wen kun
 * @Date: 2024-09-11 10:35:30
 * @LastEditTime: 2024-10-18 16:09:03
 * @LastEditors: wen kun
 */
import store from '../store'
export default {
  // 是否有按钮权限判定
  hasPermi: {
    inserted (el, bind) {
      let routes = JSON.parse(JSON.stringify(store.getters.routes))
      let arr = bind.value.split('-')
      routes.map(item => {
        if (!item.children) return
        item.children.map(aitem => {
          if (aitem.name === arr[0]) {
            let ind = aitem.meta.pagePermission.findIndex(bitem => {
              return bitem.title === aitem.name
            })
            if (
              !aitem.meta.pagePermission[ind].auth.includes(arr[1])
            ) {
              if (!!window.ActiveXObject || 'ActiveXObject' in window) {
                el.parentNode.removeChild(el)
              } else {
                el.remove()
              }
            }
          }
        })
      })
    }
  },
  // 防止重复点击
  preventReClick: {
    inserted (el, bind) {
      el.addEventListener('click', () => {
        if (!el.disabled) {
          el.disabled = true
          setTimeout(() => {
            el.disabled = false
          }, bind.value || 1000)
        }
      })
    }
  }
}
