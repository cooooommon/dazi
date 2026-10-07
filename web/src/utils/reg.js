export const reg = {
  tel: /^1[3-9]\d{9}$/,
  phone: /0\d{2,3}-\d{7,8}$/,
  phone400: /^400[0-9]{7}$/,
  email: /\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/,
  number: /^\+?[0-9]*$/,
  // 验证400电话|手机号码|座机号码
  isAllPhone: (rule, value, callback) => {
    rule.text = rule.text || '电话号码'
    let reg = /((^400)-([0-9]{7})$)|(^1[3-9]\d{9}$)|((^0\d{2,3})-(\d{7,8})$)/
    if (!value && !rule.noValid) {
      callback(new Error(`请输入${rule.text}`))
    } else if (reg.test(value)) {
      callback()
    } else if (rule.noValid && !value) {
      callback()
    } else {
      callback(new Error(`请输入有效的${rule.text}`))
    }
  },
  // 验证是否为数字
  isFloatNum: (rule, value, callback) => {
    rule.text = rule.text || '数字'
    rule.reg_type = rule.reg_type || 2
    let reg = rule.reg_type === 1 ? /^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/ : /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/
    if (value === null || value === '') {
      callback(new Error(`请输入${rule.text}`))
    } else if (!value || value * 1 === 0) {
      callback(new Error('请输入大于0的数字'))
    } else if (!reg.test(value)) {
      callback(new Error(`请输入正确的数字，最多保留${rule.reg_type}位小数`))
    } else {
      callback()
    }
  },
  // 验证是否为金额
  isMoney: (rule, value, callback) => {
    rule.text = rule.text || '金额'
    let reg = /^(([0-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/
    rule.reg_type = rule.reg_type || 2 // 默认保留两位小数
    if (rule.reg_type === 1) {
      reg = /^(([0-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1})))$/
    }
    if (value === '') {
      callback(new Error(`请输入${rule.text}`))
    } else if (!reg.test(value)) {
      callback(new Error(`请输入正确的${rule.text}，最多保留${rule.reg_type}位小数`))
    } else {
      callback()
    }
  },
  // 验证是否为百分比
  isPercent: (rule, value, callback) => {
    rule.text = rule.text || '百分比'
    rule.reg_type = rule.reg_type || 1 // 1 0-100；2 1-100
    rule.decimal = rule.decimal || 0 // 0不能输入小数 1能输入
    let reg = rule.reg_type === 1 ? /^(?:0|[1-9][0-9]?|100)$/ : /^(?:[1-9][0-9]?|100)$/
    let msg = rule.reg_type === 1 ? `0至100` : `1至100`
    let decimalReg = /^([0-9]{1,2}$)|(^[0-9]{1,2}\.[0-9]{1,2}$)|100$/

    if (value === null || value === '') {
      callback(new Error(`请输入${rule.text}`))
    } else if (!reg.test(value) && !rule.decimal) {
      callback(new Error(`请输入${msg}的整数`))
    } else if (!decimalReg.test(value) && rule.decimal) {
      callback(new Error(`请输入${msg}，最多保留2位小数`))
    } else {
      callback()
    }
  },
  // 验证是否是整数
  isNum: (rule, value, callback) => {
    rule.text = rule.text || '数字'
    rule.reg_type = rule.reg_type || 1 // 1可为0；2大于0
    let reg = rule.reg_type === 1 ? /^\+?[0-9]*$/ : /^[1-9]\d*$/
    if (value === '') {
      callback(new Error(`请输入${rule.text}`))
    } else if (!reg.test(value)) {
      callback(new Error(`请输入正确的${rule.text}`))
    } else {
      callback()
    }
  },
  // 验证手机号
  isTel: (rule, value, callback) => {
    rule.text = rule.text || '手机号码'
    if (!value) {
      callback(new Error(`请输入${rule.text}`))
    } else if (!/^1[3-9]\d{9}$/.test(value)) {
      callback(new Error(`请输入有效的${rule.text}`))
    } else {
      callback()
    }
  },
  // 验证座机号
  isPhone: (rule, value, callback) => {
    if (!value) {
      callback(new Error('座机号不可为空'))
    } else if (!/0\d{2,3}-\d{7,8}$/.test(value)) {
      callback(new Error('请输入有效的座机号'))
    } else {
      callback()
    }
  },
  isTelOrPhone: (rule, value, callback) => {
    rule.text = rule.text || '手机号码'
    if (!value) {
      callback(new Error(`请输入${rule.text}`))
    } else if (!/^(1[0-9]{10})$/.test(value) && !/0\d{2,3}-\d{7,8}|\(?0\d{2,3}[)-]?\d{7,8}|\(?0\d{2,3}[)-]*\d{7,8}$/.test(value) && !/^400[0-9]{7}/.test(value)) {
      callback(new Error(`请输入有效的${rule.text}`))
    } else {
      callback()
    }
  },
  // 验证座机号
  isPhone400: (rule, value, callback) => {
    if (!value) {
      callback(new Error('400座机号不可为空'))
    } else if (!/^400[0-9]{7}$/.test(value)) {
      callback(new Error('请输入有效的400座机号'))
    } else {
      callback()
    }
  },
  // 验证是否是英文和字母
  isNumCode: (rule, value, callback) => {
    rule.text = rule.text || '英文字母或数字'
    if (!value) {
      callback(new Error(`请输入${rule.text}`))
    } else if (!/^[A-Za-z0-9]+$/.test(value)) {
      callback(new Error('请输入英文字母或数字'))
    } else {
      callback()
    }
  },
  // 是否为空
  isNotNull: (rule, value, callback) => {
    rule.reg_type = rule.reg_type || 1 // 2非空
    rule.text = rule.text || '内容'
    if (rule.reg_type === 2 && value) {
      value = value.replace(/(^\s*)|(\s*$)/g, '')
    }
    if (value === '' || value === undefined || value === false || JSON.stringify(value) === '[]' || JSON.stringify(value) === '{}') {
      callback(new Error(`请输入${rule.text}`))
    } else {
      callback()
    }
  },
  isIdCard: (rule, value, callback) => {
    var regIdCard = /^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/
    if (value == null || value.length < 1) {
      callback(new Error('请输入身份证号码'))
    }
    if (regIdCard.test(value)) {
      if (value.length === 18) {
        var idCardWi = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2) // 将前17位加权因子保存在数组里
        var idCardY = new Array(1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2) //这是除以11后，可能产生的11位余数、验证码，也保存成数组
        var idCardWiSum = 0 //用来保存前17位各自乖以加权因子后的总和
        for (var i = 0; i < 17; i++) {
          idCardWiSum += value.substring(i, i + 1) * idCardWi[i]
        }
        var idCardMod = idCardWiSum % 11 //计算出校验码所在数组的位置
        var idCardLast = value.substring(17) //得到最后一位身份证号码
        //如果等于2，则说明校验码是10，身份证号码最后一位应该是X
        if (idCardMod === 2) {
          if (idCardLast === "X" || idCardLast === "x") {
            callback()
          } else {
            callback(new Error(`${value} 身份证号码最后一位应为X`))
          }
        } else {
          //用计算出的验证码与最后一位身份证号码匹配，如果一致，说明通过，否则是无效的身份证号码
          if (idCardLast * 1 === idCardY[idCardMod] * 1) {
            callback()
          } else {
            callback(new Error(`${value} 身份证号码无效`))
          }
        }
      }
    } else {
      callback(new Error(`${value} 身份证号码无效`))
    }
  },
  isLng: (rule, value, callback) => {
    let reg = /^[\-\+]?(0(\.\d{1,15})?|([1-9](\d)?)(\.\d{1,15})?|1[0-7]\d{1}(\.\d{1,15})?|180\.0{1,15})$/
    if (!value) {
      callback(new Error(`请输入经度`))
    } else if (!reg.test(value)) {
      callback(new Error('请输入正确的经度'))
    } else {
      callback()
    }
  },
  isLat: (rule, value, callback) => {
    let reg = /^[\-\+]?((0|([1-8]\d?))(\.\d{1,15})?|90(\.0{1,15})?)$/
    if (!value) {
      callback(new Error(`请输入纬度`))
    } else if (!reg.test(value)) {
      callback(new Error('请输入正确的纬度'))
    } else {
      callback()
    }
  }
}
