import {
  api
} from '@/api'
import router from '@/router'
import Layout from '@/components/layout'
import COS from 'cos-js-sdk-v5'
const _import = require('../../router/_import_' + process.env.NODE_ENV)
const state = {
  promptData: '',
  isFirst: true,
  isAuth: false,
  isW7: false,
  isShowPrompt: false, // 是否显示到期提醒
  systemCopyInfo: '', // 获取微擎系统版本信息
  allRoutes: [], // 所有页面
  routes: [],
  have_police_notice: true, // 是否有求救通知权限
  have_police_read: true, // 求救通知是否有阅读权限
  have_order_notice: true, // 是否订单通知权限
  have_order_read: true, // 订单通知是否有阅读权限
  notice_num: 0,
  police_num: 0,
  notice_info: {},
  notice_audio: {},
  help_voice: '',
  ShopOrder: false,
  ShopBellOrder: false,
  ShopRefund: false,
  ShopBellRefund: false,
  ShopRefuseOrder: false,
  ShopOrderPage: false,
  ShopBellOrderPage: false,
  ShopRefundPage: false,
  ShopBellRefundPage: false,
  ShopRefuseOrderPage: false,
  userInfo: {},
  auth: {},
  old_attendant_name: '向导',
  attendant_name: '向导',
  agent_service_type: 0,
  isDistressNotice: true, // 求救通知
  isOrderNotification: true, // 订单通知
  isOrderNoticeAudioPlay: true // 订单通知是否播放语音
}
const getters = {
  isAuth: state => {
    return state.isAuth
  },
  isW7: state => {
    return state.isW7
  },
  routes: state => {
    return state.routes
  }
}
const mutations = {
  // 修改到期提醒数据
  changePromptData (state, item) {
    let { status } = item
    if (status === 2) {
      state.isShowPrompt = true
    } else if (status === 1 && state.isFirst) {
      state.isFirst = false
      state.isShowPrompt = true
    }
    state.promptData = item
  },
  // 修改是否显示到期提醒
  changeIsShowPrompt (state, item) {
    state.isShowPrompt = item
  },
  changeRoutesItem (state, item) {
    // console.log('changeItem ==>', item)
    let { key, val } = item
    state[key] = val
  },
  saveRoutes (state, item = []) {
    state.routes = item
    state.isAuth = true
  }
}
const actions = {
  changeMutaRoutesItem ({ commit }, obj) {
    commit('changeRoutesItem', obj)
  },
  async getUserPromission ({
    commit
  }, obj) {
    // 正式请求接口获取路由
    let [w7Tmp, saasAuth] = await Promise.all([api.base.getW7TmpV2(), api.base.getSaasAuth()])
    if (saasAuth.code !== 200) return
    let [adminNode, data] = await Promise.all([api.account.adminNodeInfo(), api.system.configInfo()])
    let { w7tmp } = w7Tmp.data
    let auth = saasAuth.data
    let { is_admin: isAdmin, node, attendant_name: attendant = '向导' } = adminNode.data
    let config = data.data

    commit('changeRoutesItem', { key: 'attendant_name', val: attendant })

    // isAdmin = 2
    // node = [{ node: 'TechnicianManage', auth: ['pagedata', 'edit'] }]

    for (let i in auth) {
      auth[i] = auth[i] === true ? 1 : auth[i] === false ? 0 : auth[i]
    }

    // examine 微信代码提交审核
    // employ 应用设置
    // link 链接管理
    auth.examine = auth.wechat === 1 ? 1 : 0
    auth.employ = (auth.h5 === 1 || auth.app === 1) ? 1 : 0
    auth.link = auth.h5 === 1 ? 1 : 0

    commit('changeRoutesItem', { key: 'systemCopyInfo', val: w7tmp })
    commit('changeRoutesItem', { key: 'auth', val: auth })

    let routes = JSON.parse(JSON.stringify(obj.routes))
    commit('changeRoutesItem', { key: 'allRoutes', val: routes })
    let allRoutes = []
    let sysAuthArr = ['examine', 'wechat', 'h5', 'app', 'employ', 'link', 'virtual', 'reminder', 'fdd']
    let routesAuthArr = ['/store', '/account', '/agent', '/adapay', '/invitation'] // '/dynamic'动态, // invitation 邀约管理
    routes.map(item => {
      if (routesAuthArr.includes(item.path)) {
        let newAuth = item.path === '/invitation' ? auth.demand : item.path === '/account' ? auth.node || auth.mobilenode : item.path === '/agent' ? isAdmin !== 0 ? true : auth.fdd : auth[item.path.split('/')[1]]
        item.auth = newAuth
        item.hidden = !newAuth
      }

      // 营销管理 - 动态管理
      if (item.path === '/market') {
        let nameList = []
        if (!auth.dynamic) {
          let dynamicInd = item.meta.subNavName.findIndex(aitem => {
            return aitem.name === 'DynamicManage'
          })
          if (dynamicInd !== -1) {
            item.meta.subNavName.splice(dynamicInd, 1)
            nameList = nameList.concat(['DynamicList', 'DynamicComment', 'DynamicSet'])
          }
        }
        item.children.forEach(aitem => {
          if (nameList.includes(aitem.name)) {
            aitem.hidden = true
          }
        })
      }
      if (item.path === '/promotion') { // 推广管理  经纪人 渠道商菜单权限
        let nameList = []
        if (!auth.channel) { // 渠道商
          let channelInd = item.meta.subNavName.findIndex(aitem => {
            return aitem.name === 'ChannelManage'
          })
          if (channelInd !== -1 && !auth.member) {
            item.meta.subNavName.splice(channelInd, 1)
            nameList = nameList.concat(['ChannelExamine', 'ChannelClassify', 'ChannelFinance', 'ChannelSet'])
          }
        }
        if (!auth.broker) { // 经纪人
          let brokerInd = item.meta.subNavName.findIndex(aitem => {
            return aitem.name === 'EconomyManage'
          })
          if (brokerInd !== -1 && !auth.member) {
            item.meta.subNavName.splice(brokerInd, 1)
            nameList = nameList.concat(['EconomyExamine', 'EconomyRecord', 'EconomySet'])
          }
        }
        item.children.forEach(aitem => {
          if (nameList.includes(aitem.name)) {
            aitem.hidden = true
          }
        })
      }
      if (item.path === '/custom') {
        // let memberInd = item.meta.subNavName.findIndex(aitem => {
        //   return aitem.name === 'CustomMemberManage'
        // })
        // if (memberInd !== -1 && !auth.member) {
        //   item.meta.subNavName.splice(memberInd, 1)
        // }
        // item.children = item.children.filter(aitem => {
        //   if (aitem.path === 'user') {
        //     let { auth: userAuth } = aitem.meta.pagePermission[0]
        //     let newUserAuth = userAuth.filter(bitem => {
        //       return (bitem.includes('memberGrowth') && auth.member) || !bitem.includes('memberGrowth')
        //     })
        //     aitem.meta.pagePermission[0].auth = newUserAuth
        //   }
        //   aitem.hidden = ['member/level/edit', 'member/rights/edit'].includes(aitem.path) ? true : aitem.path.includes('member/') && !auth.member
        //   return (aitem.path.includes('member/') && auth.member) || !aitem.path.includes('member/')
        // })

        let memberIndex = item.meta.subNavName.findIndex(aitem => {
          return aitem.name === 'CustomMemberdiscountManage'
        })
        if (memberIndex !== -1 && !auth.member) {
          item.meta.subNavName.splice(memberIndex, 1)
        }
        item.children.map(aitem => {
          aitem.hidden = ['CustomMemberdiscountCard', 'CustomMemberdiscountSet'].includes(aitem.name) && !auth.member ? true : aitem.hidden
        })


        let customInd = item.children.findIndex(item => {
          return item.name === 'CustomList'
        })
        let customAuthInd = item.children[customInd].meta.pagePermission[0].auth.findIndex(item => {
          return item === 'viewIntegral'
        })
        if (!auth.integral) {
          item.children[customInd].meta.pagePermission[0].auth.splice(customAuthInd, 1)
        }
      }
      if (item.path === '/finance') {
        let memberIndex = item.meta.subNavName.findIndex(aitem => {
          return aitem.name === 'FinanceRecManage'
        })
        if (memberIndex !== -1 && !auth.member) {
          item.meta.subNavName.splice(memberIndex, 1)
        }
        item.children.map(aitem => {
          aitem.hidden = ['FinanceMemberdiscountOrder'].includes(aitem.name) && !auth.member ? true : aitem.hidden
          if (aitem.path === 'record') {
            let { auth: userAuth } = aitem.meta.pagePermission[0]
            let newUserAuth = userAuth.filter(bitem => {
              return (bitem.includes('adapayCashOut') && auth.adapay) || !bitem.includes('adapayCashOut')
            })
            aitem.meta.pagePermission[0].auth = newUserAuth
          }
        })
      }
      if (item.path === '/sys') {
        item.meta.subNavName.map(aitem => {
          aitem.url.map(bitem => {
            let authUrl = bitem.url.split('/sys/')[1]
            let authKey = authUrl.split('-')[0]
            if (sysAuthArr.includes(authKey)) {
              bitem.auth = auth[authKey]
              let ind = item.children.findIndex(citem => {
                return citem.path.includes(authKey)
              })
              item.children[ind].meta.auth = auth[authKey]
              if (authUrl === 'virtual-set') {
                item.children[ind + 1].meta.auth = auth[authKey]
              }
              if (authUrl === 'fdd-set') {
                item.children[ind + 1].meta.auth = auth[authKey]
              }
            }
          })
        })
        let subNavName = JSON.parse(JSON.stringify(item.meta.subNavName))
        subNavName.map(aitem => {
          aitem.url = aitem.url.filter(bitem => {
            return bitem.auth === 1
          })
        })
        item.meta.subNavName = subNavName
        item.children = item.children.filter(aitem => {
          return aitem.meta.auth === 1
        })
      }
      // 门店权限
      if (item.path === '/storeshop') {
        if (!auth.storeplus) {
          item.hidden = !auth.storeplus
        }
        if (!auth.seckill || !auth.store) { // 秒杀权限
          let seckillInd = item.meta.subNavName[0].url.findIndex(item => {
            return item.name === 'StoreshopSeckillList'
          })
          if (seckillInd !== -1) {
            item.meta.subNavName[0].url.splice(seckillInd, 1)
          }
          seckillInd = item.children.findIndex(item => {
            return item.name === 'StoreshopSeckillList'
          })
          if (seckillInd !== -1) {
            item.children[seckillInd].hidden = true
          }
        }
        if (!auth.store) {
          let orderInd = item.meta.subNavName.findIndex(item => {
            return item.name === 'StoreshopOrder'
          })
          if (orderInd !== -1) {
            item.meta.subNavName.splice(orderInd, 1)
          }
          let evaluateInd = item.meta.subNavName.findIndex(item => {
            return item.name === 'StoreshopEvaluate'
          })
          if (evaluateInd !== -1) {
            item.meta.subNavName.splice(evaluateInd, 1)
          }

          let storeInd = item.meta.subNavName[0].url.findIndex(item => {
            return item.name === 'StoreshopPackageList'
          })
          if (storeInd !== -1) {
            item.meta.subNavName[0].url.splice(storeInd, 1)
          }
          item.children.forEach(element => {
            if (element.name === 'StoreshopExamine') {
              let paInd = element.meta.pagePermission[0].auth.indexOf('package')
              element.meta.pagePermission[0].auth.splice(paInd, 1)
              let scInd = element.meta.pagePermission[0].auth.indexOf('setScale')
              element.meta.pagePermission[0].auth.splice(scInd, 1)
              // 关闭套餐 - 取消编辑，删除，复制权限
              let peInd = element.meta.pagePermission[0].auth.indexOf('packageEdit')
              element.meta.pagePermission[0].auth.splice(peInd, 1)
              let pdInd = element.meta.pagePermission[0].auth.indexOf('packageDelete')
              element.meta.pagePermission[0].auth.splice(pdInd, 1)
              let pcInd = element.meta.pagePermission[0].auth.indexOf('packageCopy')
              element.meta.pagePermission[0].auth.splice(pcInd, 1)
            }
            if (['StoreshopPackageList', 'StoreshopOrderList', 'StoreshopOrderRefund', 'StoreshopEvaluate'].includes(element.name)) {
              element.hidden = true
            }
          })
        }
      }
      // 交易设置 佣金结算方式
      if (item.path === '/technician' && isAdmin !== 2) {
        item.meta.subNavName.map(aitem => {
          if (aitem.name === 'TechnicianManage') {
            let levelInd = aitem.url.findIndex(bitem => {
              return bitem.name === 'TechnicianLevel'
            })
            if (config.cash_type === 2 && levelInd !== -1) {
              aitem.url.splice(levelInd, 1)
            }

            // let setInd = aitem.url.findIndex(bitem => {
            //   return bitem.name === 'TechnicianSet'
            // })
            // if (config.cash_type === 2 && setInd !== -1) {
            //   aitem.url.splice(setInd, 1)
            // }
          }
        })
      }
    })

    let routesAuthArrDatas = routes.filter(item => {
      return (routesAuthArr.includes(item.path) && item.auth) || !routesAuthArr.includes(item.path)
    })
    routes = routesAuthArrDatas

    // 普通用户
    if (auth.node && isAdmin === 2) {
      // node.push({
      //   auth: ['pagedata'],
      //   node: 'NoticeList'
      // }, {
      //   auth: ['pagedata'],
      //   node: 'ShopOrderNotice'
      // })
      let arr = node.map(item => { return item.node })
      let isHaveArticle = arr.includes('MarketArticle') && arr.includes('MarketArticleEnroll') ? 1 : arr.includes('MarketArticle') || arr.includes('MarketArticleEnroll') ? arr.includes('MarketArticle') ? 2 : 3 : 0
      let isHavePayment = arr.includes('SystemPaymentWechat') && arr.includes('SystemPaymentAlipay') ? 1 : arr.includes('SystemPaymentWechat') || arr.includes('SystemPaymentAlipay') ? arr.includes('SystemPaymentWechat') ? 2 : 3 : 0
      let isHaveVirtual = arr.includes('SystemVirtualSet') && arr.includes('SystemVirtualRecord') ? 1 : arr.includes('SystemVirtualSet') || arr.includes('SystemVirtualRecord') ? arr.includes('SystemVirtualSet') ? 2 : 3 : 0
      let isHaveFdd = arr.includes('SystemFddSet') && arr.includes('SystemFddRecord') ? 1 : arr.includes('SystemFddSet') || arr.includes('SystemFddRecord') ? arr.includes('SystemFddSet') ? 2 : 3 : 0
      let isHaveCarFee = arr.includes('SystemCarFeeSet') && arr.includes('SystemCarFeeCity') ? 1 : arr.includes('SystemCarFeeSet') || arr.includes('SystemCarFeeCity') ? arr.includes('SystemCarFeeSet') ? 2 : 3 : 0
      // console.log(isHaveArticle, isHavePayment, isHaveVirtual, isHaveFdd, isHaveCarFee)
      let newRoutes = []
      routes.map(item => {
        if (item.hidden) return
        let children = []
        item.children.map(aitem => {
          if (arr.includes(aitem.name)) {
            let ind = node.findIndex(bitem => {
              return bitem.node === aitem.name
            })
            aitem.meta.pagePermission.map(citem => {
              if (citem.title === aitem.name) {
                citem.auth = node[ind].auth
              }
            })

            if ((isHaveArticle === 2 && aitem.name === 'MarketArticle') || (isHavePayment === 2 && aitem.name === 'SystemPaymentWechat') || (isHaveVirtual === 2 && aitem.name === 'SystemVirtualSet') || (isHaveFdd === 2 && aitem.name === 'SystemFddSet') || (isHaveCarFee === 2 && aitem.name === 'SystemCarFeeSet')) {
              aitem.meta.pagePermission.splice(1, 1)
            }
            if ((isHaveArticle === 3 && aitem.name === 'MarketArticleEnroll') || (isHavePayment === 3 && aitem.name === 'SystemPaymentAlipay') || (isHaveVirtual === 3 && aitem.name === 'SystemVirtualRecord') || (isHaveFdd === 3 && aitem.name === 'SystemFddRecord') || (isHaveCarFee === 3 && aitem.name === 'SystemCarFeeCity')) {
              aitem.meta.pagePermission.splice(0, 1)
            }

            children.push(aitem)
          }
        })
        let hidden = item.children.filter(aitem => {
          return (arr.includes('TechnicianEdit') && aitem.hidden && aitem.name !== 'TechnicianEdit') || (!arr.includes('TechnicianEdit') && aitem.hidden)
        })
        if (!arr.includes('TechnicianEdit')) {
          hidden.map(aitem => {
            if (aitem.name === 'TechnicianEdit') {
              aitem.meta.pagePermission[0].auth = []
            }
          })
        }
        if (children.length > 0) {
          let arr = children.map(item => {
            return item.name
          })
          item.children = [...children, ...hidden]
          if (item.meta.subNavName && item.meta.subNavName.length > 0) {
            let subNavName = []
            item.meta.subNavName.map(aitem => {
              if (aitem.name === 'MarketManage') {
                if (isHaveArticle === 3) {
                  let urlInd = aitem.url.findIndex(citem => {
                    return citem.name === 'MarketArticle'
                  })
                  aitem.url[urlInd].url = '/market/article/enroll'
                }
              }
              if (aitem.name === 'SystemSetting') {
                if (isHavePayment === 3) {
                  let urlInd = aitem.url.findIndex(citem => {
                    return citem.name === 'SystemPayment'
                  })
                  aitem.url[urlInd].url = '/sys/alipay'
                }
              }
              if (aitem.name === 'SystemMessageSet') {
                if (isHaveVirtual === 3) {
                  let urlInd = aitem.url.findIndex(citem => {
                    return citem.name === 'SystemVirtual'
                  })
                  aitem.url[urlInd].url = '/sys/virtual-record'
                }
              }
              if (aitem.name === 'SystemOther') {
                if (isHaveFdd === 3) {
                  let urlInd = aitem.url.findIndex(citem => {
                    return citem.name === 'SystemFdd'
                  })
                  aitem.url[urlInd].url = '/sys/fdd-record'
                }
                if (isHaveCarFee === 3) {
                  let urlInd = aitem.url.findIndex(citem => {
                    return citem.name === 'SystemCarFee'
                  })
                  aitem.url[urlInd].url = '/sys/car-fee-city'
                }
              }

              let url = aitem.url.filter(bitem => {
                return arr.includes(bitem.name) || (isHaveArticle && bitem.name === 'MarketArticle') || (isHavePayment && bitem.name === 'SystemPayment') || (isHaveVirtual && bitem.name === 'SystemVirtual') || (isHaveCarFee && bitem.name === 'SystemCarFee')
              })

              if (url.length > 0) {
                subNavName.push({ name: aitem.name, url })
              }
            })
            if (subNavName.length > 0) {
              item.redirect = subNavName[0].url[0].url
              item.meta.subNavName = subNavName
            }
          }
          newRoutes.push(item)
        }
      })
      // 勾选了一个 并且插件关闭 默认一个暂无权限的页面
      if (newRoutes.length === 0) {
        let item = JSON.parse(JSON.stringify(obj.routes))
        newRoutes.unshift(item[item.length - 2])
      } else {
        let isHidden = true
        newRoutes.forEach((item, index) => {
          let del = true
          item.children.forEach(aitem => {
            if (!aitem.hidden) {
              isHidden = false
              del = false
            }
          })
          if (del) {
            newRoutes.splice(index, 1)
          }
        })
        if (isHidden) {
          let item = JSON.parse(JSON.stringify(obj.routes))
          newRoutes.unshift(item[item.length - 2])
        }
      }
      allRoutes = [...newRoutes, {
        path: '*',
        redirect: '/404',
        hidden: true
      }]
    } else {
      allRoutes = routes
    }

    let police = -1
    let policeAuth = []
    let order = -1
    let orderAuth = []
    let isDistressNotice = true
    let isOrderNotification = true
    let isOrderNoticeAudioPlay = true
    if (isAdmin === 2) {
      isOrderNoticeAudioPlay = false
      isOrderNotification = false
      isDistressNotice = false
    }
    allRoutes.findIndex((item, index) => {
      if (item.path === '/shop') {
        item.children.map(aitem => {
          if (['ShopOrder', 'ShopBellOrder', 'ShopRefund', 'ShopBellRefund', 'ShopRefuseOrder'].includes(aitem.name)) {
            commit('changeRoutesItem', { key: `${aitem.name}Page`, val: true })
            commit('changeRoutesItem', { key: aitem.name, val: aitem.meta.pagePermission[0].auth.includes('view') })
          }
        })
      }

      if (item.path === '/notice') {
        police = item.children.findIndex(item => {
          return item.name === 'NoticeList'
        })
        policeAuth = police === -1 ? [] : item.children[police].meta.pagePermission[0].auth
        isDistressNotice = true
      }
      if (item.path === '/order') {
        order = index
        orderAuth = item.children[0].meta.pagePermission[0].auth
        item.children.forEach(aitem => {
          if (aitem.name === 'ShopOrderNotice') {
            if (aitem.meta.pagePermission[0].auth.includes('read')) {
              isOrderNoticeAudioPlay = true
            }
            isOrderNotification = true
          }
        })
      }
      if (item.path === '/account') {
        if (isAdmin === 0 && auth.mobilenode !== 1) {
          allRoutes.splice(index, 1)
        } else {
          if (auth.mobilenode !== 1) {
            item.meta.subNavName.splice(0, 1)
            item.redirect = '/account/role'
          }
          if (isAdmin === 1 && auth.node !== 1) {
            item.meta.subNavName.splice(1, 1)
          }
          if (auth.mobilenode !== 1 || (isAdmin === 1 && auth.node !== 1)) {
            let nodeAuthArr = auth.mobilenode !== 1 ? ['phone'] : ['role', 'list']
            let arr = item.children.filter(aitem => {
              return !nodeAuthArr.includes(aitem.path)
            })
            item.children = arr
          }
        }
      }
      // 门店管理  关联套餐
      if (item.path === '/storeshop') {
        let ind = item.children.findIndex(aitem => {
          return aitem.name === 'StoreshopExamine'
        })
        let _ind = item.children.findIndex(aitem => {
          return aitem.name === 'StoreshopPackage'
        })
        let packageind = item.children.findIndex(aitem => {
          return aitem.name === 'StoreshopPackageList'
        })
        // let storeInd = item.meta.subNavName[0].url.findIndex(sitem => {
        //   return sitem.name === 'StoreshopPackageList'
        // })

        // if (!auth.seckill) {
        //   item.meta.subNavName[0].url[storeInd].name = 'StoreshopPackageManage'
        //   item.children[packageind].name = 'StoreshopPackageManage'
        //   item.children[packageind].meta.pagePermission[0].title = 'StoreshopPackageManage'
        // }

        let seckillind = item.children.findIndex(aitem => {
          return aitem.name === 'StoreshopSeckillList'
        })
        // 秒杀活动新增 控制 团购/套餐管理 设为秒杀按钮
        if (packageind !== -1 && seckillind !== -1 && item.children[seckillind].meta.pagePermission[0].auth.includes('add') && auth.seckill) {
          item.children[packageind].meta.pagePermission[0].auth.push('setSeckill')
        }
        if (_ind !== -1 && ind !== -1) {
          item.children[_ind].meta.pagePermission[0].auth = item.children[ind].meta.pagePermission[0].auth
        }
      }
      // 推广管理  渠道管理
      if (item.path === '/promotion') {
        let ind = item.children.findIndex(aitem => {
          return aitem.name === 'ChannelExamine'
        })
        let _ind = item.children.findIndex(aitem => {
          return aitem.name === 'ChannelDetail'
        })
        if (!auth.channelstaff) {
          let authInd = item.children[ind].meta.pagePermission[0].auth.findIndex(itemauth => {
            return itemauth === 'replacingSuperiors'
          })
          if (authInd !== -1) {
            item.children[ind].meta.pagePermission[0].auth.splice(authInd, 1)
          }
        }
        if (_ind !== -1 && ind !== -1) {
          item.children[_ind].meta.pagePermission[0].auth = item.children[ind].meta.pagePermission[0].auth
        }
      }
      // 助娱达人管理 - 已关联技能
      if (item.path === '/technician') {
        let ind = item.children.findIndex(aitem => {
          return aitem.name === 'TechnicianManage'
        })
        let _ind = item.children.findIndex(aitem => {
          return aitem.name === 'TechnicianEdit'
        })
        if (!auth.broker) {
          let authInd = item.children[ind].meta.pagePermission[0].auth.findIndex(itemauth => {
            return itemauth === 'modifyBroker'
          })
          if (authInd !== -1) {
            item.children[ind].meta.pagePermission[0].auth.splice(authInd, 1)
          }
        }
        if (_ind !== -1 && ind !== -1) {
          item.children[_ind].meta.pagePermission[0].auth = item.children[ind].meta.pagePermission[0].auth
        }
      }
      // 营销管理 - 文章管理 - 表单数据-导出
      if (item.path === '/market') {
        let ind = item.children.findIndex(aitem => {
          return aitem.name === 'MarketArticle'
        })
        let _ind = item.children.findIndex(aitem => {
          return aitem.name === 'MarketArticleRecord'
        })
        if (_ind !== -1 && ind !== -1) {
          item.children[_ind].meta.pagePermission[0].auth = item.children[ind].meta.pagePermission[0].auth
        }
      }
    })

    commit('changeRoutesItem', { key: `isDistressNotice`, val: isDistressNotice })
    commit('changeRoutesItem', { key: `isOrderNotification`, val: isOrderNotification })
    commit('changeRoutesItem', { key: `isOrderNoticeAudioPlay`, val: isOrderNoticeAudioPlay })

    // commit('changeRoutesItem', { key: 'have_police_notice', val: police !== -1 })
    commit('changeRoutesItem', { key: 'have_police_read', val: policeAuth.includes('read') })
    // commit('changeRoutesItem', { key: 'have_order_notice', val: order !== -1 })
    // commit('changeRoutesItem', { key: 'have_order_read', val: orderAuth.includes('read') })

    commit('saveRoutes', allRoutes)
    obj.routes = allRoutes
    let { path = '' } = obj.to
    let arr = []
    allRoutes.map(item => {
      if (item.children && item.children.length) {
        item.children.map(aitem => {
          arr.push(`${item.path}/${aitem.path}`)
        })
      }
    })
    if (!path || (path && !arr.includes(path))) {
      obj.to = allRoutes[0]
    }
    routerGo(allRoutes, obj)
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}

function routerGo (routes, obj) {
  let getRouter = filterAsyncRouter(routes) // 过滤路由
  router.options.routes.push(...getRouter)
  router.addRoutes(getRouter) // 动态添加路由
  // localStorage.setItem('routes', JSON.stringify(getRouter))
  obj.next({
    ...obj.to,
    replace: true
  })
}

function filterAsyncRouter (asyncRouterMap) { // 遍历后台传来的路由字符串，转换为组件对象
  const accessedRouters = asyncRouterMap.filter(route => {
    if (route.component) {
      if (route.component === 'Layout') { // Layout组件特殊处理
        route.component = Layout
      } else {
        route.component = _import(route.component)
      }
    }
    if (route.children && route.children.length) {
      route.children = filterAsyncRouter(route.children)
    }
    return true
  })
  return accessedRouters
}
