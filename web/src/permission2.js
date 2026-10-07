export default [
  // 权限路由
  {
    path: '/',
    redirect: '/technician',
    hidden: true // 是否展示在侧边栏的菜单里
  },
  //  向导管理
  {
    path: '/technician',
    component: 'Layout',
    redirect: '/technician/list',
    meta: {
      menuName: 'Technician',
      icon: 'icon-jishi',
      subNavName: [{
        name: 'TechnicianManage',
        url: [{
          name: 'TechnicianManage',
          url: '/technician/list'
        }]
      }]
    },
    children: [{
      path: 'list',
      name: 'TechnicianManage',
      component: '/technician/list',
      meta: {
        keepAlive: true,
        title: 'TechnicianManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'TechnicianManage',
          index: 0,
          auth: ['pagedata', 'add', 'edit']
        }]
      }
    }, {
      path: 'edit',
      name: 'TechnicianEdit',
      component: '/technician/edit',
      hidden: true,
      meta: {
        keepAlive: false,
        title: 'TechnicianManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'TechnicianEdit',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  //  订单管理
  {
    path: '/shop',
    component: 'Layout',
    redirect: '/shop/order/list',
    meta: {
      menuName: 'Shop',
      icon: 'icon-dingdanguanli',
      subNavName: [{
        name: 'ShopOrderManage',
        url: [{
          name: 'ShopOrder',
          url: '/shop/order/list'
        },
        // {
        //   name: 'ShopBellOrder',
        //   url: '/shop/order/bell'
        // },
        {
          name: 'ShopRefuseOrder',
          url: '/shop/order/refuse'
        }, {
          name: 'ShopRefund',
          url: '/shop/refund/list'
        }
          // , {
          //   name: 'ShopBellRefund',
          //   url: '/shop/refund/bell'
          // }
        ]
      }, {
        name: 'ShopEvaluate',
        url: [{
          name: 'ShopEvaluate',
          url: '/shop/evaluate/list'
        }]
      }]
    },
    children: [{
      path: 'order/list',
      name: 'ShopOrder',
      component: '/shop/order/list',
      meta: {
        keepAlive: true,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopOrder',
          index: 0,
          auth: ['pagedata', 'view', 'print', 'export', 'transferOrder', 'orderTaking', 'setOut', 'arrive', 'startService', 'serviceCompletion']
        }]
      }
    }, {
      path: 'order/bell',
      name: 'ShopBellOrder',
      component: '/shop/order/bell',
      meta: {
        keepAlive: true,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopBellOrder',
          index: 0,
          auth: ['pagedata', 'view', 'print', 'export', 'orderTaking', 'startService', 'serviceCompletion']
        }]
      }
    }, {
      path: 'order/refuse',
      name: 'ShopRefuseOrder',
      component: '/shop/order/refuse',
      meta: {
        keepAlive: true,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopRefuseOrder',
          index: 0,
          auth: ['pagedata', 'view', 'transferOrder', 'agreeRefund']
        }]
      }
    }, {
      path: 'order/detail',
      name: 'ShopOrderDetail',
      component: '/shop/order/detail',
      hidden: true,
      meta: {
        keepAlive: false,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopOrderDetail',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'refund/list',
      name: 'ShopRefund',
      component: '/shop/refund/list',
      meta: {
        keepAlive: true,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopRefund',
          index: 0,
          auth: ['pagedata', 'view', 'agreeRefund', 'rejectRefund']
        }]
      }
    }, {
      path: 'refund/bell',
      name: 'ShopBellRefund',
      component: '/shop/refund/bell',
      meta: {
        keepAlive: true,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopBellRefund',
          index: 0,
          auth: ['pagedata', 'view', 'agreeRefund', 'rejectRefund']
        }]
      }
    }, {
      path: 'refund/detail',
      name: 'ShopRefundDetail',
      component: '/shop/refund/detail',
      hidden: true,
      meta: {
        keepAlive: false,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopRefundDetail',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'evaluate/list',
      name: 'ShopEvaluate',
      component: '/shop/evaluate/list',
      meta: {
        keepAlive: true,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopEvaluate',
          index: 0,
          auth: ['pagedata', 'delete']
        }]
      }
    }]
  },
  //  财务管理
  {
    path: '/finance',
    component: 'Layout',
    redirect: '/finance/commission',
    meta: {
      menuName: 'Finance',
      icon: 'icon-caiwu',
      subNavName: [{
        name: 'FinanceManage',
        url: [{
          name: 'ShopCommissionList',
          url: '/finance/commission'
        }, {
          name: 'FinanceRecord',
          url: '/finance/record'
        }]
      }]
    },
    children: [{
      path: 'record',
      name: 'FinanceRecord',
      component: '/finance/finance/record',
      meta: {
        keepAlive: false,
        title: 'FinanceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FinanceRecord',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'commission',
      name: 'ShopCommissionList',
      component: '/finance/finance/commission',
      meta: {
        keepAlive: true,
        title: 'FinanceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopCommissionList',
          index: 0,
          auth: ['pagedata', 'cashOut']
        }]
      }
    }]
  },
  // 门店
  {
    path: '/store',
    component: 'Layout',
    redirect: '/store/list',
    meta: {
      menuName: 'Store',
      icon: 'iconshangjia',
      subNavName: [{
        name: 'StoreManage',
        url: [{
          name: 'StoreList',
          url: '/store/list'
        }, {
          name: 'StoreService',
          url: '/store/service'
        }]
      }]
    },
    children: [{
      path: 'list',
      name: 'StoreList',
      component: '/store/list/list',
      meta: {
        keepAlive: true,
        title: 'StoreManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreList',
          index: 0,
          auth: ['pagedata', 'edit']
        }]
      }
    }, {
      path: 'list/edit',
      name: 'StoreEdit',
      component: '/store/list/edit',
      meta: {
        title: 'StoreManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreEdit',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'service',
      name: 'StoreService',
      component: '/store/service/list',
      meta: {
        keepAlive: true,
        title: 'StoreManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreService',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'service/edit',
      name: 'StoreServiceEdit',
      component: '/store/service/edit',
      meta: {
        title: 'StoreManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreServiceEdit',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  //  订单通知
  {
    path: '/order',
    component: 'Layout',
    redirect: '/order/notice',
    isHidden: true,
    meta: {
      menuName: 'ShopOrderNotice',
      icon: 'icon-tongzhi'
    },
    children: [{
      path: 'notice',
      name: 'ShopOrderNoticeList',
      component: '/shop/notice',
      meta: {
        keepAlive: true,
        title: '',
        auth: [],
        isOnly: true,
        pagePermission: [{
          title: 'ShopOrderNoticeList',
          index: 0,
          auth: ['pagedata', 'read', 'delete']
        }]
      }
    }]
  },
  {
    path: '/nothing',
    component: 'Layout',
    redirect: '/nothing/index',
    hidden: true,
    meta: {
      menuName: 'Nothing', // 一级菜单标题
      icon: 'icon-caiwu' // 一级菜单的图标
    },
    children: [{
      path: 'index',
      name: 'Nothing',
      component: '/nothing/index',
      meta: {
        refresh: false,
        title: '', // 页面头部的标题
        isOnly: true, // 单独页面
        auth: [], // 单独页面按钮操作权限
        pagePermission: [{
          title: 'Nothing',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  {
    path: '*',
    redirect: '/404',
    hidden: true
  }
]
