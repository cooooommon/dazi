/*
 * @Description: 
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-11-22 15:06:41
 * @LastEditors: wen kun
 */
export default {
  debounce (fn, delay = 300) { // 默认300毫秒
    let timer
    return function (...args) {
      if (timer) {
        clearTimeout(timer)
      }
      timer = setTimeout(() => {
        fn.apply(this, args) // this 指向vue
      }, delay)
    }
  },
  getProCurrentHref () {
    let _href = window.location.href
    let index = _href.indexOf('#')
    let newHref = _href.slice(0, index)
    newHref = window.lbConfig.isWe7 ? newHref + '&s=' : window.location.origin + '/index.php'
    return newHref
  },
  pick (obj, arr) {
    return arr.reduce((acc, curr) => {
      if (curr in obj) {
        acc[curr] = obj[curr]
      }
      return acc
    }, {})
  },
  getItems (o, type = 'id', sign = ',') {
    let items = []
    o = o || []
    o.forEach((item) => {
      items.push(item[type])
    })
    return items.join(sign)
  },
  randomWord (randomFlag, min, max) {
    let str = ''
    let range = min
    let arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
    // 随机产生
    if (randomFlag) {
      range = Math.round(Math.random() * (max - min)) + min
    }
    for (var i = 0; i < range; i++) {
      let pos = Math.round(Math.random() * (arr.length - 1))
      str += arr[pos]
    }
    return str
  },
  // 根据生日的月份和日期，计算星座。
  getAstro (month, day) {
    var s = '摩羯水瓶双鱼白羊金牛双子巨蟹狮子处女天秤天蝎射手摩羯'
    var arr = [20, 19, 21, 20, 21, 22, 23, 23, 23, 24, 23, 22]
    return s.substr(month * 2 - (day < arr[month - 1] ? 2 : 0), 2) + '座'
  },
  // 两个数字之间的整数
  betweenNumbers (arr, arr2) {
    let a = parseInt(arr)
    let b = parseInt(arr2)
    let c = []
    if (arr - b < 0) {
      const number = Math.abs(a - b) + 1
      for (let i = a; i < a + number; i++) {
        c.push(i)
      }
    } else if (a - b === 0) {
      c.push(a)
    } else if (a - b > 0) {
      const number = Math.abs(a - b)
      for (let i = a; i > number; i--) {
        c.push(i)
      }
    }
    return c.join(',')
  }
}
