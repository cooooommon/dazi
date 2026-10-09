export default [
  // 权限路由
  {
    path: '/',
    redirect: '/survey',
    hidden: true // 是否展示在侧边栏的菜单里
  },
  // 数据概览
  {
    path: '/survey',
    component: 'Layout',
    redirect: '/survey/index',
    meta: {
      menuName: 'Survey', // 一级菜单标题
      icon: 'icon-caiwu' // 一级菜单的图标
    },
    children: [{
      path: 'index',
      name: 'Survey',
      component: '/survey/index',
      meta: {
        refresh: false,
        title: '', // 页面头部的标题
        isOnly: true, // 单独页面
        auth: [], // 单独页面按钮操作权限
        pagePermission: [{
          title: 'Survey',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // 物料商城
  // {
  //   path: '/mall',
  //   component: 'Layout',
  //   redirect: '/mall/sort',
  //   meta: {
  //     menuName: 'Mall',
  //     icon: 'icon-gouwudai',
  //     subNavName: [{
  //       name: 'MallManage',
  //       url: [{
  //         name: 'MallSort',
  //         url: '/mall/sort'
  //       }, {
  //         name: 'MallGoodList',
  //         url: '/mall/list'
  //       }]
  //     }]
  //   },
  //   children: [{
  //     path: 'sort',
  //     name: 'MallSort',
  //     component: '/mall/sort',
  //     meta: {
  //       keepAlive: true,
  //       title: 'MallManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'MallSort',
  //         index: 0,
  //         auth: ['pagedata', 'add', 'edit', 'delete']
  //       }]
  //     }
  //   }, {
  //     path: 'list',
  //     name: 'MallGoodList',
  //     component: '/mall/list',
  //     meta: {
  //       keepAlive: true,
  //       title: 'MallManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'MallGoodList',
  //         index: 0,
  //         auth: ['pagedata', 'add', 'edit', 'delete']
  //       }]
  //     }
  //   }, {
  //     path: 'edit',
  //     name: 'MallGoodEdit',
  //     component: '/mall/edit',
  //     hidden: true,
  //     meta: {
  //       title: 'MallManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'MallGoodEdit',
  //         index: 0,
  //         auth: []
  //       }]
  //     }
  //   }]
  // },
  // 服务管理
  {
    path: '/service',
    component: 'Layout',
    redirect: '/service/list',
    meta: {
      menuName: 'Service',
      icon: 'icon-yuyue',
      subNavName: [{
        name: 'ServiceManage',
        url: [{
          name: 'ServiceManage',
          url: '/service/list'
        },
        {
          name: 'ServiceClassify',
          url: '/service/classify'
        },
          // {
          //   name: 'ServiceBell',
          //   url: '/service/bell'
          // },
          // {
          //   name: 'ServiceExamine',
          //   url: '/service/examine'
          // }
        ]
      }, {
        name: 'ServiceSet',
        url: [{
          name: 'ServiceBannerList',
          url: '/service/banner'
        }, {
          name: 'ServiceBellSet',
          url: '/service/bell-set'
        }]
      }]
    },
    children: [{
      path: 'list',
      name: 'ServiceManage',
      component: '/service/service/list',
      meta: {
        keepAlive: true,
        title: 'ServiceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ServiceManage',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'service/edit',
      name: 'ServiceEdit',
      component: '/service/service/edit',
      hidden: true,
      meta: {
        title: 'ServiceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ServiceEdit',
          index: 0,
          auth: []
        }]
      }
    },
    {
      path: 'classify',
      name: 'ServiceClassify',
      component: '/service/service/classify',
      meta: {
        title: 'ServiceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ServiceClassify',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    },
    // {
    //   path: 'bell',
    //   name: 'ServiceBell',
    //   component: '/service/bell/list',
    //   meta: {
    //     keepAlive: true,
    //     title: 'ServiceManage',
    //     auth: [],
    //     isOnly: false,
    //     pagePermission: [{
    //       title: 'ServiceBell',
    //       index: 0,
    //       auth: ['pagedata', 'add', 'edit', 'delete']
    //     }]
    //   }
    // },
    // {
    //   path: 'bell/edit',
    //   name: 'ServiceBellEdit',
    //   component: '/service/bell/edit',
    //   hidden: true,
    //   meta: {
    //     title: 'ServiceManage',
    //     auth: [],
    //     isOnly: false,
    //     pagePermission: [{
    //       title: 'ServiceBellEdit',
    //       index: 0,
    //       auth: []
    //     }]
    //   }
    // },
    // {
    //   path: 'examine',
    //   name: 'ServiceExamine',
    //   component: '/service/service/examine',
    //   meta: {
    //     keepAlive: true,
    //     title: 'ServiceManage',
    //     auth: [],
    //     isOnly: false,
    //     pagePermission: [{
    //       title: 'ServiceExamine',
    //       index: 0,
    //       auth: ['pagedata', 'examine', 'view', 'delete']
    //     }]
    //   }
    // }, 
    {
      path: 'banner',
      name: 'ServiceBannerList',
      component: '/service/set/list',
      meta: {
        keepAlive: true,
        title: 'ServiceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ServiceBannerList',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'banner/edit',
      name: 'ServiceBannerEdit',
      component: '/service/set/edit',
      hidden: true,
      meta: {
        title: 'ServiceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ServiceBannerEdit',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'bell-set',
      name: 'ServiceBellSet',
      component: '/service/set/bell-set',
      meta: {
        title: 'ServiceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ServiceBellSet',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // 向导管理
  {
    path: '/technician',
    component: 'Layout',
    redirect: '/technician/list',
    meta: {
      menuName: 'Technician',
      icon: 'iconanmo1',
      subNavName: [{
        name: 'TechnicianManage',
        url: [{
          name: 'TechnicianManage',
          url: '/technician/list'
        }, {
          name: 'TechnicianLevel',
          url: '/technician/level'
        }, {
          name: 'TechnicianTag',
          url: '/technician/tag'
        }, {
          name: 'TechnicianSet',
          url: '/technician/set'
        }, {
          name: 'TechnicianAgreement',
          url: '/technician/agreement'
        }
          // , { 向导解约
          //   name: 'tecTermination',
          //   url: '/technician/termination'
          // }
        ]
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
          auth: ['pagedata', 'add', 'edit', 'modifyBroker', 'selectSkills', 'modifyServicePrice', 'disassociate', 'delete', 'authTechnician', 'resetExamine', 'cancelAuth', 'resetAuth', 'updateAgent', 'batchEditDrawProportion']
        }]
      }
    }, {
      path: 'edit',
      name: 'TechnicianEdit',
      component: '/technician/edit',
      hidden: true,
      meta: {
        title: 'TechnicianManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'TechnicianEdit',
          index: 0,
          auth: ['pagedata', 'modifyBroker', 'selectSkills', 'modifyServicePrice', 'disassociate']
        }]
      }
    }, {
      path: 'level',
      name: 'TechnicianLevel',
      component: '/technician/level',
      meta: {
        keepAlive: true,
        title: 'TechnicianManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'TechnicianLevel',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete', 'setCycle']
        }]
      }
    }, {
      path: 'set',
      name: 'TechnicianSet',
      component: '/technician/set',
      meta: {
        title: 'TechnicianManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'TechnicianSet',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'agreement',
      name: 'TechnicianAgreement',
      component: '/technician/agreement',
      meta: {
        title: 'TechnicianManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'TechnicianAgreement',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'tag',
      name: 'TechnicianTag',
      component: '/technician/tag',
      meta: {
        keepAlive: true,
        title: 'TechnicianManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'TechnicianTag',
          index: 0,
          auth: ['pagedata', 'edit', 'delete', 'add']
        }]
      }
    }, {
      path: 'termination',
      name: 'tecTermination',
      component: '/technician/termination',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'TechnicianManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'tecTermination',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // 订单管理
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
        },
        {
          name: 'ShopRefund',
          url: '/shop/refund/list'
        }
          // {
          //   name: 'ShopBellRefund',
          //   url: '/shop/refund/bell'
          // }
        ]
      }, {
        name: 'ShopEvaluate',
        url: [{
          //   name: 'ShopEvaluateLabel',
          //   url: '/shop/evaluate/label'
          // },
          // {
          name: 'ShopEvaluate',
          url: '/shop/evaluate/list'
        }]
      }, {
        name: 'ShopCommission',
        url: [{
          name: 'ShopCommissiondistribution',
          url: '/shop/commission/distribution'
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
    },
    // {
    //   path: 'order/bell',
    //   name: 'ShopBellOrder',
    //   component: '/shop/order/bell',
    //   meta: {
    //     keepAlive: true,
    //     title: 'ShopOrderManage',
    //     auth: [],
    //     isOnly: false,
    //     pagePermission: [{
    //       title: 'ShopBellOrder',
    //       index: 0,
    //       auth: ['pagedata', 'view', 'print', 'export', 'orderTaking', 'startService', 'serviceCompletion']
    //     }]
    //   }
    // },
    {
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
    },
    // {
    //   path: 'refund/bell',
    //   name: 'ShopBellRefund',
    //   component: '/shop/refund/bell',
    //   meta: {
    //     keepAlive: true,
    //     title: 'ShopOrderManage',
    //     auth: [],
    //     isOnly: false,
    //     pagePermission: [{
    //       title: 'ShopBellRefund',
    //       index: 0,
    //       auth: ['pagedata', 'view', 'agreeRefund', 'rejectRefund']
    //     }]
    //   }
    // },
    {
      path: 'refund/detail',
      name: 'ShopRefundDetail',
      component: '/shop/refund/detail',
      hidden: true,
      meta: {
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
      //   path: 'evaluate/label',
      //   name: 'ShopEvaluateLabel',
      //   component: '/shop/evaluate/label',
      //   meta: {
      //     keepAlive: true,
      //     title: 'ShopOrderManage',
      //     auth: [],
      //     isOnly: false,
      //     pagePermission: [{
      //       title: 'ShopEvaluateLabel',
      //       index: 0,
      //       auth: ['pagedata', 'add', 'edit', 'delete']
      //     }]
      //   }
      // }, {
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
          auth: ['pagedata', 'add', 'delete']
        }]
      }
    }, {
      path: 'commission/distribution',
      name: 'ShopCommissiondistribution',
      component: '/shop/commission/distribution',
      meta: {
        keepAlive: true,
        title: 'ShopOrderManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ShopCommissiondistribution',
          index: 0,
          auth: ['pagedata', 'technicianTransfer']
        }]
      }
    }]
  },
  //  门店管理
  {
    path: '/storeshop',
    component: 'Layout',
    redirect: '/storeshop/classify',
    meta: {
      menuName: 'Storeshop',
      icon: 'iconshangjia',
      subNavName: [{
        name: 'StoreshopManage',
        url: [{
          name: 'StoreshopClassify',
          url: '/storeshop/classify',
          auth: 1
        }, {
          name: 'StoreshopExamine',
          url: '/storeshop/list',
          auth: 1
        }, {
          name: 'StoreshopPackageList',
          url: '/storeshop/package/list',
          auth: 1
        }, {
          name: 'StoreshopSeckillList',
          url: '/storeshop/seckill/list',
          auth: 1
        }]
      }, {
        name: 'StoreshopOrder',
        url: [{
          name: 'StoreshopOrderList',
          url: '/storeshop/order/list',
          auth: 1
        }, {
          name: 'StoreshopOrderRefund',
          url: '/storeshop/order/refund',
          auth: 1
        }]
      }, {
        name: 'StoreshopEvaluate',
        url: [{
          name: 'StoreshopEvaluate',
          url: '/storeshop/evaluate/list',
          auth: 1
        }]
      }]
    },
    children: [{
      path: 'classify',
      name: 'StoreshopClassify',
      component: '/storeshop/classify',
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopClassify',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'list',
      name: 'StoreshopExamine',
      component: '/storeshop/list',
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopExamine',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete', 'examine', 'resetExamine', 'cancelAuth', 'resetAuth', 'package', 'packageEdit', 'packageDelete', 'packageCopy', 'setScale', 'topping', 'cancelTopping']
        }]
      }
    },
    {
      path: 'edit',
      name: 'StoreshopEdit',
      component: '/storeshop/edit',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopEdit',
          index: 0,
          auth: ['view', 'pagedata']
        }]
      }
    },
    {
      path: 'package',
      name: 'StoreshopPackage',
      component: '/storeshop/package',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopPackage',
          index: 0,
          auth: ['pagedata', 'packageCopy', 'packageEdit', 'packageDelete']
        }]
      }
    },
    {
      path: 'package/list',
      name: 'StoreshopPackageList',
      component: '/storeshop/package/list',
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopPackageList',
          index: 0,
          auth: ['pagedata', 'copy', 'edit', 'delete', 'add']
        }]
      }
    },
    {
      path: 'package/add',
      name: 'StoreshopPackageAdd',
      component: '/storeshop/package/add',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopPackageAdd',
          index: 0,
          auth: ['pagedata']
        }]
      }
    },
    {
      path: 'seckill/list',
      name: 'StoreshopSeckillList',
      component: '/storeshop/seckill/list',
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopSeckillList',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    },
    {
      path: 'seckill/edit',
      name: 'StoreshopSeckillEdit',
      component: '/storeshop/seckill/edit',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopSeckillEdit',
          index: 0,
          auth: []
        }]
      }
    },
    {
      path: 'order/list',
      name: 'StoreshopOrderList',
      component: '/storeshop/order/list',
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopOrderList',
          index: 0,
          auth: ['pagedata', 'export', 'viewDetail']
        }]
      }
    }, {
      path: 'order/detail',
      name: 'StoreshopOrderDetail',
      component: '/storeshop/order/detail',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopOrderDetail',
          index: 0,
          auth: ['pagedata']
        }]
      }
    }, {
      path: 'order/refund',
      name: 'StoreshopOrderRefund',
      component: '/storeshop/order/refund',
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopOrderRefund',
          index: 0,
          auth: ['pagedata', 'view', 'agreeRefund', 'rejectRefund']
        }]
      }
    }, {
      path: 'order/refdetail',
      name: 'StoreshopOrderRefDetail',
      component: '/storeshop/order/refdetail',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopOrderRefDetail',
          index: 0,
          auth: ['pagedata']
        }]
      }
    }, {
      path: 'evaluate/list',
      name: 'StoreshopEvaluate',
      component: '/storeshop/evaluate/list',
      meta: {
        keepAlive: true,
        title: 'StoreshopManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'StoreshopEvaluate',
          index: 0,
          auth: ['pagedata', 'delete', 'add']
        }]
      }
    }]
  },
  //  邀约管理
  {
    path: '/invitation',
    component: 'Layout',
    redirect: '/invitation/list',
    meta: {
      menuName: 'Invitation',
      icon: 'icon-jishi',
      subNavName: [{
        name: 'InvitationManage',
        url: [{
          name: 'InvitationExamine',
          url: '/invitation/list',
          auth: 1
        }, {
          name: 'InvitationSetManage',
          url: '/invitation/set',
          auth: 1
        }, {
          name: 'SystemServiceType',
          url: '/invitation/serviceType',
          auth: 1
        }]
      }, {
        name: 'InvitationOrderManage',
        url: [
          {
            name: 'InvitationOrderManage',
            url: '/invitation/inviteList',
            auth: 1
          }, {
            name: 'InvitationShopRefund',
            url: '/invitation/refundList',
            auth: 1
          }, {
            name: 'InvitationDistribution',
            url: '/invitation/distribution',
            auth: 1
          }
        ]
      }]
    },
    children: [{
      path: 'list',
      name: 'InvitationExamine',
      component: '/invitation/list',
      meta: {
        keepAlive: true,
        title: 'InvitationManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'InvitationExamine',
          index: 0,
          auth: ['pagedata', 'viewDetail', 'examine']
        }]
      }
    }, {
      path: 'set',
      name: 'InvitationSetManage',
      component: '/invitation/set',
      meta: {
        keepAlive: true,
        title: 'InvitationManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'InvitationSetManage',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'serviceType',
      name: 'SystemServiceType',
      component: '/system/serviceType',
      meta: {
        title: 'InvitationManage',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemServiceType',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'inviteList',
      name: 'InvitationOrderManage',
      component: '/invitation/order/list',
      meta: {
        title: 'InvitationManage',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'InvitationOrderManage',
          index: 0,
          auth: ['pagedata', 'view', 'print', 'export']
        }]
      }
    }, {
      path: 'inviteDetail',
      name: 'InvitationShopOrderDetail',
      component: '/invitation/order/detail',
      hidden: true,
      meta: {
        keepAlive: false,
        title: 'InvitationManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'InvitationShopOrderDetail',
          index: 0,
          auth: ['pagedata', 'view', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'refundList',
      name: 'InvitationShopRefund',
      component: '/invitation/evaluate/list',
      meta: {
        title: 'InvitationManage',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'InvitationShopRefund',
          index: 0,
          auth: ['pagedata', 'view', 'rejectRefund', 'agreeRefund']
        }]
      }
    }, {
      path: 'distribution',
      name: 'InvitationDistribution',
      component: '/invitation/distribution',
      meta: {
        title: 'InvitationManage',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'InvitationDistribution',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // 分销管理
  // {
  //   path: '/distribution',
  //   component: 'Layout',
  //   redirect: '/distribution/examine',
  //   meta: {
  //     menuName: 'Distribution',
  //     icon: 'icon-shenhe',
  //     subNavName: [{
  //       name: 'DistributionManage',
  //       url: [{
  //         name: 'DistributionExamine',
  //         url: '/distribution/examine'
  //       }, {
  //         name: 'DistributionSet',
  //         url: '/distribution/set'
  //       }]
  //     }]
  //   },
  //   children: [{
  //     path: 'examine',
  //     name: 'DistributionExamine',
  //     component: '/distribution/examine',
  //     meta: {
  //       keepAlive: true,
  //       title: 'DistributionManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'DistributionExamine',
  //         index: 0,
  //         auth: ['pagedata', 'view', 'delete', 'authDistribution', 'cancelAuth', 'resetAuth']
  //       }]
  //     }
  //   }, {
  //     path: 'set',
  //     name: 'DistributionSet',
  //     component: '/distribution/set',
  //     meta: {
  //       keepAlive: true,
  //       title: 'DistributionManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'DistributionSet',
  //         index: 0,
  //         auth: []
  //       }]
  //     }
  //   }, {
  //     path: 'detail',
  //     name: 'DistributionDetail',
  //     component: '/distribution/detail',
  //     meta: {
  //       keepAlive: true,
  //       title: 'DistributionManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'DistributionDetail',
  //         index: 0,
  //         auth: []
  //       }]
  //     }
  //   }]
  // },
  // 经纪人管理
  // {
  //   path: '/economy',
  //   component: 'Layout',
  //   redirect: '/economy/examine',
  //   meta: {
  //     menuName: 'EconomyManage',
  //     icon: 'iconjingjiren',
  //     subNavName: [{
  //       name: 'EconomyManage',
  //       url: [{
  //         name: 'EconomyExamine',
  //         url: '/economy/examine'
  //       }, {
  //         name: 'EconomyRecord',
  //         url: '/economy/record'
  //       }, {
  //         name: 'EconomySet',
  //         url: '/economy/set'
  //       }]
  //     }]
  //   },
  //   children: [{
  //     path: 'examine',
  //     name: 'EconomyExamine',
  //     component: '/economy/examine',
  //     meta: {
  //       keepAlive: true,
  //       title: 'EconomyManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'EconomyExamine',
  //         index: 0,
  //         auth: ['pagedata', 'add', 'view', 'edit', 'resetAuth', 'delete', 'setScale', 'examine', 'cancelAuth']
  //       }]
  //     }
  //   }, {
  //     path: 'add',
  //     name: 'EconomyAdd',
  //     component: '/economy/add',
  //     meta: {
  //       keepAlive: true,
  //       title: 'EconomyManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'EconomyExamine',
  //         index: 0,
  //         auth: ['pagedata']
  //       }]
  //     }
  //   }, {
  //     path: 'record',
  //     name: 'EconomyRecord',
  //     component: '/economy/record',
  //     meta: {
  //       keepAlive: true,
  //       title: 'EconomyManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'EconomyRecord',
  //         index: 0,
  //         auth: ['pagedata', 'view']
  //       }]
  //     }
  //   }, {
  //     path: 'set',
  //     name: 'EconomySet',
  //     component: '/economy/set',
  //     meta: {
  //       keepAlive: true,
  //       title: 'EconomyManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'EconomySet',
  //         index: 0,
  //         auth: ['pagedata']
  //       }]
  //     }
  //   }]
  // },
  // 渠道商管理
  // {
  //   path: '/channel',
  //   component: 'Layout',
  //   redirect: '/channel/examine',
  //   meta: {
  //     menuName: 'Channel',
  //     icon: 'icon-zuzhi',
  //     subNavName: [{
  //       name: 'ChannelManage',
  //       url: [{
  //         name: 'ChannelExamine',
  //         url: '/channel/examine'
  //       }, {
  //         name: 'ChannelClassify',
  //         url: '/channel/classify'
  //       }, {
  //         name: 'ChannelFinance',
  //         url: '/channel/finance'
  //       }]
  //     }]
  //   },
  //   children: [{
  //     path: 'examine',
  //     name: 'ChannelExamine',
  //     component: '/channel/examine',
  //     meta: {
  //       keepAlive: true,
  //       title: 'ChannelManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'ChannelExamine',
  //         index: 0,
  //         auth: ['pagedata', 'edit', 'delete', 'authChannel', 'cancelAuth', 'resetAuth']
  //       }]
  //     }
  //   }, {
  //     path: 'classify',
  //     name: 'ChannelClassify',
  //     component: '/channel/classify',
  //     meta: {
  //       keepAlive: true,
  //       title: 'ChannelManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'ChannelClassify',
  //         index: 0,
  //         auth: ['pagedata', 'add', 'edit', 'delete']
  //       }]
  //     }
  //   }, {
  //     path: 'finance',
  //     name: 'ChannelFinance',
  //     component: '/channel/finance',
  //     meta: {
  //       keepAlive: true,
  //       title: 'ChannelManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'ChannelFinance',
  //         index: 0,
  //         auth: ['pagedata', 'print', 'export']
  //       }]
  //     }
  //   }, {
  //     path: 'detail',
  //     name: 'ChannelDetail',
  //     component: '/channel/detail',
  //     meta: {
  //       keepAlive: true,
  //       title: 'ChannelManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'ChannelDetail',
  //         index: 0,
  //         auth: ['pagedata', 'print', 'export']
  //       }]
  //     }
  //   }]
  // },
  // 推广管理
  {
    path: '/promotion',
    component: 'Layout',
    redirect: '/promotion/distribution/examine',
    meta: {
      menuName: 'Promotion',
      icon: 'icon-zuzhi',
      subNavName: [{
        name: 'DistributionManage',
        url: [{
          name: 'DistributionExamine',
          url: '/promotion/distribution/examine'
        }, {
          name: 'DistributionSet',
          url: '/promotion/distribution/set'
        }]
      }, {
        name: 'EconomyManage',
        url: [{
          name: 'EconomyExamine',
          url: '/promotion/economy/examine'
        }, {
          name: 'EconomyRecord',
          url: '/promotion/economy/record'
        }, {
          name: 'EconomySet',
          url: '/promotion/economy/set'
        }]
      }, {
        name: 'ChannelManage',
        url: [{
          name: 'ChannelExamine',
          url: '/promotion/channel/examine'
        }, {
          name: 'ChannelClassify',
          url: '/promotion/channel/classify'
        }, {
          name: 'ChannelFinance',
          url: '/promotion/channel/finance'
        }, {
          name: 'ChannelSet',
          url: '/promotion/channel/set'
        }]
      }]
    },
    children: [{
      path: 'distribution/examine',
      name: 'DistributionExamine',
      component: '/distribution/examine',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DistributionExamine',
          index: 0,
          auth: ['pagedata', 'view', 'delete', 'authDistribution', 'cancelAuth', 'resetAuth']
        }]
      }
    }, {
      path: 'distribution/set',
      name: 'DistributionSet',
      component: '/distribution/set',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DistributionSet',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'distribution/detail',
      name: 'DistributionDetail',
      component: '/distribution/detail',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DistributionDetail',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'economy/examine',
      name: 'EconomyExamine',
      component: '/economy/examine',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'EconomyExamine',
          index: 0,
          auth: ['pagedata', 'add', 'view', 'edit', 'resetAuth', 'delete', 'setScale', 'examine', 'cancelAuth']
        }]
      }
    }, {
      path: 'economy/add',
      name: 'EconomyAdd',
      component: '/economy/add',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'EconomyExamine',
          index: 0,
          auth: ['pagedata']
        }]
      }
    }, {
      path: 'economy/record',
      name: 'EconomyRecord',
      component: '/economy/record',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'EconomyRecord',
          index: 0,
          auth: ['pagedata', 'view']
        }]
      }
    }, {
      path: 'economy/set',
      name: 'EconomySet',
      component: '/economy/set',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'EconomySet',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'channel/examine',
      name: 'ChannelExamine',
      component: '/channel/examine',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ChannelExamine',
          index: 0,
          auth: ['pagedata', 'edit', 'balanceChangeRecord', 'replacingSuperiors', 'delete', 'authChannel', 'cancelAuth', 'resetAuth', 'setScale', 'setAging']
        }]
      }
    }, {
      path: 'channel/classify',
      name: 'ChannelClassify',
      component: '/channel/classify',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ChannelClassify',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'channel/finance',
      name: 'ChannelFinance',
      component: '/channel/finance',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ChannelFinance',
          index: 0,
          auth: ['pagedata', 'print', 'export']
        }]
      }
    },
    {
      path: 'channel/detail',
      name: 'ChannelDetail',
      component: '/channel/detail',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ChannelDetail',
          index: 0,
          auth: ['pagedata', 'balanceChangeRecord', 'replacingSuperiors']
        }]
      }
    }, {
      path: 'channel/set',
      name: 'ChannelSet',
      component: '/channel/set',
      meta: {
        keepAlive: true,
        title: 'PromotionManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'ChannelSet',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // 代理商
  {
    path: '/agent',
    component: 'Layout',
    redirect: '/agent/account',
    meta: {
      menuName: 'Agent',
      icon: 'icon-person-accounts',
      subNavName: [{
        name: 'AgentManage',
        url: [{
          name: 'AgentAccount',
          url: '/agent/account'
        }, {
          name: 'AgentSet',
          url: '/agent/set'
        }, {
          name: 'AgentApply',
          url: '/agent/apply'
        }]
      }]
    },
    children: [{
      path: 'account',
      name: 'AgentAccount',
      component: '/agent/account',
      meta: {
        keepAlive: false,
        title: 'AgentManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'AgentAccount',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'set',
      name: 'AgentSet',
      component: '/agent/set',
      meta: {
        title: 'AgentManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'AgentSet',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'apply',
      name: 'AgentApply',
      component: '/agent/apply',
      meta: {
        keepAlive: true,
        title: 'AgentManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'AgentApply',
          index: 0,
          auth: ['pagedata', 'reviewed']
        }]
      }
    }]
  },
  // 营销管理
  {
    path: '/market',
    component: 'Layout',
    redirect: '/market/article/list',
    meta: {
      menuName: 'Market',
      icon: 'icon-yingxiao',
      subNavName: [{
        name: 'MarketManage',
        url: [{
          name: 'MarketArticle',
          url: '/market/article/list'
        }, {
          name: 'MarketCoupon',
          url: '/market/coupon/list'
        }
          // , {
          //   name: 'MarketAtv',
          //   url: '/market/atv'
          // }
        ]
      }, {
        name: 'Mall',
        url: [{
          name: 'MallSort',
          url: '/market/mall/sort'
        }, {
          name: 'MallGoodList',
          url: '/market/mall/list'
        }
        ]
      }, {
        name: 'DynamicManage',
        url: [{
          name: 'DynamicList',
          url: '/market/dynamic/list'
        }, {
          name: 'DynamicComment',
          url: '/market/dynamic/comment'
        }, {
          name: 'DynamicSet',
          url: '/market/dynamic/set'
        }]
      }]
    },
    children: [{
      path: 'article/list',
      name: 'MarketArticle',
      component: '/market/article/list',
      meta: {
        keepAlive: true,
        title: 'MarketManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MarketArticle',
          url: 'list',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete', 'formData', 'export']
        }, {
          title: 'MarketArticleEnroll',
          url: 'enroll',
          index: 1,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'article/enroll',
      name: 'MarketArticleEnroll',
      component: '/market/article/enroll',
      meta: {
        keepAlive: true,
        title: 'MarketManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MarketArticleList',
          url: 'list',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete', 'formData', 'export']
        }, {
          title: 'MarketArticleEnroll',
          url: 'enroll',
          index: 1,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'article/edit',
      name: 'MarketArticleEdit',
      component: '/market/article/edit',
      hidden: true,
      meta: {
        title: 'MarketManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MarketArticleEdit',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'article/record',
      name: 'MarketArticleRecord',
      component: '/market/article/record',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'MarketManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MarketArticleRecord',
          index: 0,
          auth: ['export']
        }]
      }
    }, {
      path: 'coupon/list',
      name: 'MarketCoupon',
      component: '/market/coupon/list',
      meta: {
        keepAlive: true,
        title: 'MarketManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MarketCoupon',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete', 'handOut']
        }]
      }
    }, {
      path: 'coupon/edit',
      name: 'MarketCouponEdit',
      component: '/market/coupon/edit',
      hidden: true,
      meta: {
        title: 'MarketManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MarketCouponEdit',
          index: 0,
          auth: []
        }]
      }
    },
    // {
    //   path: 'atv',
    //   name: 'MarketAtv',
    //   component: '/market/atv',
    //   meta: {
    //     title: 'MarketManage',
    //     auth: [],
    //     isOnly: false,
    //     pagePermission: [{
    //       title: 'MarketAtv',
    //       index: 0,
    //       auth: []
    //     }]
    //   }
    // },
    {
      path: 'mall/sort',
      name: 'MallSort',
      component: '/mall/sort',
      meta: {
        keepAlive: true,
        title: 'MallManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MallSort',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'mall/list',
      name: 'MallGoodList',
      component: '/mall/list',
      meta: {
        keepAlive: true,
        title: 'MallManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MallGoodList',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'mall/edit',
      name: 'MallGoodEdit',
      component: '/mall/edit',
      hidden: true,
      meta: {
        title: 'MallManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'MallGoodEdit',
          index: 0,
          auth: []
        }]
      }
    },
    // 动态管理
    {
      path: 'dynamic/list',
      name: 'DynamicList',
      component: '/dynamic/list',
      meta: {
        keepAlive: true,
        title: 'DynamicManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DynamicList',
          index: 0,
          auth: ['pagedata', 'examine', 'view', 'delete', 'topping']
        }]
      }
    }, {
      path: 'dynamic/comment',
      name: 'DynamicComment',
      component: '/dynamic/comment',
      meta: {
        keepAlive: true,
        title: 'DynamicManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DynamicComment',
          index: 0,
          auth: ['pagedata', 'pass', 'reject', 'delete']
        }]
      }
    }, {
      path: 'dynamic/set',
      name: 'DynamicSet',
      component: '/dynamic/set',
      meta: {
        title: 'DynamicManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DynamicSet',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // 财务管理
  {
    path: '/finance',
    component: 'Layout',
    redirect: '/finance/stored/list',
    meta: {
      menuName: 'Finance',
      icon: 'icon-caiwuguanli',
      subNavName: [{
        name: 'FinanceStored',
        url: [{
          name: 'FinanceStored',
          url: '/finance/stored/list'
        }, {
          name: 'FinanceStoredOrder',
          url: '/finance/stored/order'
        }, {
          name: 'FinanceStoredSet',
          url: '/finance/stored/set'
        }]
      }, {
        name: 'FinanceRecManage',
        url: [{
          name: 'FinanceMemberdiscountOrder',
          url: '/finance/memberdiscount-order'
        }]
      }, {
        name: 'FinanceManage',
        url: [{
          name: 'FinanceManage',
          url: '/finance/list'
        }, {
          name: 'FinanceRecord',
          url: '/finance/record'
        }]
      }]
    },
    children: [{
      path: 'stored/list',
      name: 'FinanceStored',
      component: '/finance/stored/list',
      meta: {
        keepAlive: true,
        title: 'FinanceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FinanceStored',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'stored/order',
      name: 'FinanceStoredOrder',
      component: '/finance/stored/order',
      meta: {
        keepAlive: true,
        title: 'FinanceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FinanceStoredOrder',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'stored/set',
      name: 'FinanceStoredSet',
      component: '/finance/stored/set',
      meta: {
        title: 'FinanceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FinanceStoredSet',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'memberdiscount-order',
      name: 'FinanceMemberdiscountOrder',
      component: '/finance/order/memberdiscount',
      meta: {
        title: 'Finance',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FinanceMemberdiscountOrder',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'list',
      name: 'FinanceManage',
      component: '/finance/finance/list',
      meta: {
        keepAlive: true,
        title: 'FinanceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FinanceManage',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'record',
      name: 'FinanceRecord',
      component: '/finance/finance/record',
      meta: {
        title: 'FinanceManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FinanceRecord',
          index: 0,
          auth: ['pagedata', 'wechatCashOut', 'alipayCashOut', 'underlineCashOut', 'rejectCashOut']
        }]
      }
    }]
  },
  // 动态管理 转移到 营销管理
  // {
  //   path: '/dynamic',
  //   component: 'Layout',
  //   redirect: '/dynamic/list',
  //   meta: {
  //     menuName: 'Dynamic',
  //     icon: 'icon-dongtai1',
  //     subNavName: [{
  //       name: 'DynamicManage',
  //       url: [{
  //         name: 'DynamicList',
  //         url: '/dynamic/list'
  //       }, {
  //         name: 'DynamicComment',
  //         url: '/dynamic/comment'
  //       }, {
  //         name: 'DynamicSet',
  //         url: '/dynamic/set'
  //       }]
  //     }]
  //   },
  //   children: [{
  //     path: 'list',
  //     name: 'DynamicList',
  //     component: '/dynamic/list',
  //     meta: {
  //       keepAlive: true,
  //       title: 'DynamicManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'DynamicList',
  //         index: 0,
  //         auth: ['pagedata', 'examine', 'view', 'delete']
  //       }]
  //     }
  //   }, {
  //     path: 'comment',
  //     name: 'DynamicComment',
  //     component: '/dynamic/comment',
  //     meta: {
  //       keepAlive: true,
  //       title: 'DynamicManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'DynamicComment',
  //         index: 0,
  //         auth: ['pagedata', 'pass', 'reject', 'delete']
  //       }]
  //     }
  //   }, {
  //     path: 'set',
  //     name: 'DynamicSet',
  //     component: '/dynamic/set',
  //     meta: {
  //       title: 'DynamicManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'DynamicSet',
  //         index: 0,
  //         auth: []
  //       }]
  //     }
  //   }]
  // },
  // 门店
  // {
  //   path: '/store',
  //   component: 'Layout',
  //   redirect: '/store/manage',
  //   meta: {
  //     menuName: 'Store',
  //     icon: 'iconshangjia',
  //     subNavName: [{
  //       name: 'StoreManage',
  //       url: [{
  //         name: 'StoreList',
  //         url: '/store/manage'
  //       }]
  //     }]
  //   },
  //   children: [{
  //     path: 'manage',
  //     name: 'StoreList',
  //     component: '/store/manage/list',
  //     meta: {
  //       keepAlive: true,
  //       title: 'StoreManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'StoreList',
  //         index: 0,
  //         auth: ['pagedata', 'add', 'edit', 'delete']
  //       }]
  //     }
  //   }, {
  //     path: 'manage/edit',
  //     name: 'StoreAdd',
  //     component: '/store/manage/edit',
  //     hidden: true,
  //     meta: {
  //       title: 'StoreManage',
  //       auth: [],
  //       isOnly: false,
  //       pagePermission: [{
  //         title: 'StoreAdd',
  //         index: 0,
  //         auth: []
  //       }]
  //     }
  //   }]
  // },
  // 客户管理
  {
    path: '/custom',
    component: 'Layout',
    redirect: '/custom/list',
    meta: {
      menuName: 'Custom',
      icon: 'iconwodetuandui1',
      subNavName: [{
        name: 'CustomManage',
        url: [{
          name: 'CustomList',
          url: '/custom/list'
        }, {
          name: 'CustomLabel',
          url: '/custom/label'
        }, {
          name: 'CustomBlacklist',
          url: '/custom/blacklist'
        }]
      }, {
        name: 'CustomMemberdiscountManage',
        url: [{
          name: 'CustomMemberdiscountCard',
          url: '/custom/memberdiscount/card'
        }, {
          name: 'CustomMemberdiscountSet',
          url: '/custom/memberdiscount/set'
        }]
      }]
    },
    children: [{
      path: 'list',
      name: 'CustomList',
      component: '/custom/list',
      meta: {
        keepAlive: true,
        title: 'CustomManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'CustomList',
          index: 0,
          auth: ['pagedata', 'deleteTag', 'modifyBalance', 'blockUser', 'view', 'export', 'viewBalance', 'viewIntegral']
        }]
      }
    }, {
      path: 'detail',
      name: 'CustomDetail',
      component: '/custom/detail',
      hidden: true,
      meta: {
        keepAlive: true,
        title: 'CustomManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'CustomDetail',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'label',
      name: 'CustomLabel',
      component: '/custom/label',
      meta: {
        keepAlive: true,
        title: 'CustomManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'CustomLabel',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'blacklist',
      name: 'CustomBlacklist',
      component: '/custom/blacklist',
      meta: {
        keepAlive: true,
        title: 'CustomManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'CustomBlacklist',
          index: 0,
          auth: ['pagedata', 'deleteTag', 'removeBlacklist']
        }]
      }
    }, {
      path: 'memberdiscount/card',
      name: 'CustomMemberdiscountCard',
      component: '/custom/memberdiscount/card/list',
      meta: {
        keepAlive: true,
        title: 'CustomManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'CustomMemberdiscountCard',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'memberdiscount/card/edit',
      name: 'CustomMemberdiscountCardEdit',
      component: '/custom/memberdiscount/card/edit',
      hidden: true,
      meta: {
        title: 'CustomManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'CustomMemberdiscountCardEdit',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'memberdiscount/set',
      name: 'CustomMemberdiscountSet',
      component: '/custom/memberdiscount/set',
      meta: {
        title: 'CustomManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'CustomMemberdiscountSet',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // 求救通知
  {
    path: '/notice',
    component: 'Layout',
    redirect: '/notice/list',
    isHidden: true,
    meta: {
      menuName: 'Notice',
      icon: 'icon-tongzhi',
      // subNavName: [{
      //   name: 'NoticeManage',
      //   url: [{
      //     name: 'NoticeList',
      //     url: '/notice/list'
      //   }, {
      //     name: 'NoticeSet',
      //     url: '/notice/set'
      //   }]
      // }]
    },
    children: [{
      path: 'list',
      name: 'NoticeList',
      component: '/notice/list',
      meta: {
        keepAlive: true,
        title: '',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'NoticeList',
          index: 0,
          auth: ['pagedata', 'read', 'delete']
        }]
      }
    }
      // , {
      //   path: 'set',
      //   name: 'NoticeSet',
      //   component: '/notice/set',
      //   meta: {
      //     title: 'NoticeManage',
      //     auth: [],
      //     isOnly: false,
      //     pagePermission: [{
      //       title: 'NoticeSet',
      //       index: 0,
      //       auth: []
      //     }]
      //   }
      // }
    ]
  },
  // 订单通知
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
      name: 'ShopOrderNotice',
      component: '/shop/notice',
      meta: {
        keepAlive: true,
        title: '',
        auth: [],
        isOnly: true,
        pagePermission: [{
          title: 'ShopOrderNotice',
          index: 0,
          auth: ['pagedata', 'read', 'delete']
        }]
      }
    }]
  },
  // 问题反馈
  {
    path: '/feedback',
    component: 'Layout',
    redirect: '/feedback/list',
    meta: {
      menuName: 'Feedback',
      icon: 'iconchefeitixianjilu',
      subNavName: [{
        name: 'FeedbackManage',
        url: [{
          name: 'FeedbackList',
          url: '/feedback/list'
        }, {
          name: 'FeedbackAppealList',
          url: '/feedback/appeal/list'
        }]
      }]
    },
    children: [{
      path: 'list',
      name: 'FeedbackList',
      component: '/feedback/list',
      meta: {
        keepAlive: true,
        title: 'FeedbackManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FeedbackList',
          index: 0,
          auth: ['pagedata', 'view', 'handle']
        }]
      }
    }, {
      path: 'detail',
      name: 'FeedbackDetail',
      component: '/feedback/detail',
      hidden: true,
      meta: {
        title: 'FeedbackManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FeedbackDetail',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'appeal/list',
      name: 'FeedbackAppealList',
      component: '/feedback/appeal/list',
      meta: {
        keepAlive: true,
        title: 'FeedbackManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FeedbackAppealList',
          index: 0,
          auth: ['pagedata', 'view', 'handle', 'badComment']
        }]
      }
    }, {
      path: 'appeal/detail',
      name: 'FeedbackAppealDetail',
      component: '/feedback/appeal/detail',
      hidden: true,
      meta: {
        title: 'FeedbackManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'FeedbackAppealDetail',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // 权限管理
  {
    path: '/account',
    component: 'Layout',
    redirect: '/account/role',
    meta: {
      menuName: 'Account',
      icon: 'icon-account',
      subNavName: [{
        name: 'AccountManage',
        url: [{
          name: 'AccountRole',
          url: '/account/role'
        }, {
          name: 'AccountList',
          url: '/account/list'
        }]
      }, {
        name: 'AccountOperManage',
        url: [{
          name: 'AccountOperLog',
          url: '/account/log'
        }]
      }]
    },
    children: [{
      path: 'role',
      name: 'AccountRole',
      component: '/account/role',
      meta: {
        keepAlive: true,
        title: 'AccountManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'AccountRole',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'list',
      name: 'AccountList',
      component: '/account/list',
      meta: {
        keepAlive: true,
        title: 'AccountManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'AccountList',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'log',
      name: 'AccountOperLog',
      component: '/account/log',
      meta: {
        keepAlive: true,
        title: 'AccountManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'AccountOperLog',
          index: 0,
          auth: []
        }]
      }
    }]
  },
  // diy
  {
    path: '/diy',
    component: 'Layout',
    redirect: '/diy/color',
    meta: {
      menuName: 'Diy',
      icon: 'icon-chaifenyemian',
      subNavName: [{
        name: 'DiyManage',
        url: [{
          name: 'DiyColor',
          url: '/diy/color'
        }, {
          name: 'DiyTabbar',
          url: '/diy/tabbar'
        }, {
          name: 'DiySet',
          url: '/diy/set'
          // }, {
          //   name: 'DiyPlatetype',
          //   url: '/diy/platetype'
        }]
      }]
    },
    children: [{
      path: 'color',
      name: 'DiyColor',
      component: '/diy/color',
      meta: {
        title: 'DiyManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DiyColor',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'tabbar',
      name: 'DiyTabbar',
      component: '/diy/tabbar',
      meta: {
        title: 'DiyManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DiyTabbar',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'set',
      name: 'DiySet',
      component: '/diy/set',
      meta: {
        title: 'DiyManage',
        auth: [],
        isOnly: false,
        pagePermission: [{
          title: 'DiySet',
          index: 0,
          auth: []
        }]
      }
    },
      // {
      //   path: 'platetype',
      //   name: 'DiyPlatetype',
      //   component: '/diy/platetype',
      //   meta: {
      //     title: 'DiyManage',
      //     auth: [],
      //     isOnly: false,
      //     pagePermission: [{
      //       title: 'DiySet',
      //       index: 0,
      //       auth: []
      //     }]
      //   }
      // }
    ]
  },
  // 系统
  {
    path: '/sys',
    component: 'Layout',
    redirect: '/sys/upgrade',
    meta: {
      menuName: 'System',
      icon: 'icon-xitong',
      subNavName: [{
        name: 'SystemVersion',
        url: [{
          name: 'SystemUpgrade',
          url: '/sys/upgrade',
          auth: 1
        }, {
          name: 'SystemExamine',
          url: '/sys/examine',
          auth: 1
        }]
      }, {
        name: 'SystemSetting',
        url: [{
          name: 'SystemWechat',
          url: '/sys/wechat',
          auth: 1
        },
        {
          name: 'SystemH5',
          url: '/sys/h5',
          auth: 1
        },
        {
          name: 'SystemApp',
          url: '/sys/app',
          auth: 1
        },
        {
          name: 'SystemEmploy',
          url: '/sys/employ',
          auth: 1
        },
        {
          name: 'SystemInfo',
          url: '/sys/info',
          auth: 1
        },
        {
          name: 'SystemPayment',
          url: '/sys/payment',
          auth: 1
        },
        {
          name: 'SystemUpload',
          url: '/sys/upload',
          auth: 1
        },
        {
          name: 'SystemTransaction',
          url: '/sys/transaction',
          auth: 1
        },
        {
          name: 'SystemAllNotice',
          url: '/sys/notice',
          auth: 1
        },
        {
          name: 'SystemInformation',
          url: '/sys/information',
          auth: 1
        }
        ]
      }, {
        name: 'SystemMessageSet',
        url: [
          {
            name: 'SystemAliyun',
            url: '/sys/aliyun',
            auth: 1
          },
          {
            name: 'System7moor',
            url: '/sys/7moor',
            auth: 1
          },
          {
            name: 'SystemYunxin',
            url: '/sys/yunxin',
            auth: 1
          },
          {
            name: 'SystemVirtual',
            url: '/sys/virtual-set',
            auth: 1
          },
          {
            name: 'SystemReminder',
            url: '/sys/reminder',
            auth: 1
          },
          {
            name: 'SystemMessage',
            url: '/sys/message',
            auth: 1
          }, {
            name: 'SystemNoticeManage',
            url: '/sys/notice-manage',
            auth: 1
          }]
      }, {
        name: 'SystemOther',
        url: [{
          name: 'SystemPrint',
          url: '/sys/print',
          auth: 1
        },
        {
          name: 'SystemCarFee',
          url: '/sys/car-fee-set',
          auth: 1
        },
        {
          name: 'SystemCity',
          url: '/sys/city',
          auth: 1
        },
        {
          name: 'SystemTravel',
          url: '/sys/travel',
          auth: 1
        },
        {
          name: 'SystemOther',
          url: '/sys/other',
          auth: 1
        }
          //   ,
          // {
          //   name: 'SystemServiceType',
          //   url: '/sys/serviceType',
          //   auth: 1
          // }
        ]
      }]
    },
    children: [{
      path: 'upgrade',
      name: 'SystemUpgrade',
      component: '/system/upgrade',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemUpgrade',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'examine',
      name: 'SystemExamine',
      component: '/system/examine',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemExamine',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'wechat',
      name: 'SystemWechat',
      component: '/system/wechat',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemWechat',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'h5',
      name: 'SystemH5',
      component: '/system/h5',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemH5',
          index: 0,
          auth: []
        }]
      }
    },
    {
      path: 'app',
      name: 'SystemApp',
      component: '/system/app',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemApp',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'employ',
      name: 'SystemEmploy',
      component: '/system/employ',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemEmploy',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'info',
      name: 'SystemInfo',
      component: '/system/info',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemInfo',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'payment',
      name: 'SystemPaymentWechat',
      component: '/system/payment/wechat',
      meta: {
        title: 'SystemSetting',
        isOnly: false,
        auth: 1,
        pagePermission: [{
          title: 'SystemPaymentWechat',
          url: 'payment',
          index: 0,
          auth: []
        }, {
          title: 'SystemPaymentAlipay',
          url: 'alipay',
          index: 1,
          auth: []
        }, {
          title: 'SystemPaymentVirtualpay',
          url: 'virtualpay',
          index: 2,
          auth: []
        }]
      }
    },
    {
      path: 'alipay',
      name: 'SystemPaymentAlipay',
      component: '/system/payment/alipay',
      meta: {
        title: 'SystemSetting',
        isOnly: false,
        auth: 1,
        pagePermission: [{
          title: 'SystemPaymentWechat',
          url: 'payment',
          index: 0,
          auth: []
        }, {
          title: 'SystemPaymentAlipay',
          url: 'alipay',
          index: 1,
          auth: []
        }, {
          title: 'SystemPaymentVirtualpay',
          url: 'virtualpay',
          index: 2,
          auth: []
        }]
      }
    }, {
      path: 'virtualpay',
      name: 'SystemPaymentVirtualpay',
      component: '/system/payment/virtualpay',
      meta: {
        title: 'SystemSetting',
        isOnly: false,
        auth: 1,
        pagePermission: [{
          title: 'SystemPaymentWechat',
          url: 'payment',
          index: 0,
          auth: []
        }, {
          title: 'SystemPaymentAlipay',
          url: 'alipay',
          index: 1,
          auth: []
        }, {
          title: 'SystemPaymentVirtualpay',
          url: 'virtualpay',
          index: 2,
          auth: []
        }]
      }
    }, {
      path: 'upload',
      name: 'SystemUpload',
      component: '/system/upload',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemUpload',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'transaction',
      name: 'SystemTransaction',
      component: '/system/transaction',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemTransaction',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'notice',
      name: 'SystemAllNotice',
      component: '/system/notice',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemAllNotice',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'information',
      name: 'SystemInformation',
      component: '/system/information',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemInformation',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'aliyun',
      name: 'SystemAliyun',
      component: '/system/aliyun',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemAliyun',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: '7moor',
      name: 'System7moor',
      component: '/system/7moor',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'System7moor',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'yunxin',
      name: 'SystemYunxin',
      component: '/system/yunxin',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemYunxin',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'virtual-set',
      name: 'SystemVirtualSet',
      component: '/system/virtual/set',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemVirtualSet',
          url: 'virtual-set',
          index: 0,
          auth: []
        }, {
          title: 'SystemVirtualRecord',
          url: 'virtual-record',
          index: 1,
          auth: ['pagedata', 'play', 'download']
        }]
      }
    }, {
      path: 'virtual-record',
      name: 'SystemVirtualRecord',
      component: '/system/virtual/record',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemVirtualSet',
          url: 'virtual-set',
          index: 0,
          auth: []
        }, {
          title: 'SystemVirtualRecord',
          url: 'virtual-record',
          index: 1,
          auth: ['pagedata', 'play', 'download']
        }]
      }
    }, {
      path: 'reminder',
      name: 'SystemReminder',
      component: '/system/reminder',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemReminder',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'message',
      name: 'SystemMessage',
      component: '/system/message',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemMessage',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'notice-manage',
      name: 'SystemNoticeManage',
      component: '/system/notice-manage',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemNoticeManage',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'print',
      name: 'SystemPrint',
      component: '/system/print',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemPrint',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'car-fee-set',
      name: 'SystemCarFeeSet',
      component: '/system/car-fee/set',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemCarFeeSet',
          url: 'car-fee-set',
          index: 0,
          auth: []
        }, {
          title: 'SystemCarFeeCity',
          url: 'car-fee-city',
          index: 1,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'car-fee-city',
      name: 'SystemCarFeeCity',
      component: '/system/car-fee/city',
      meta: {
        keepAlive: true,
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemCarFeeSet',
          url: 'car-fee-set',
          index: 0,
          auth: []
        }, {
          title: 'SystemCarFeeCity',
          url: 'car-fee-city',
          index: 1,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'city',
      name: 'SystemCity',
      component: '/system/city',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemCity',
          index: 0,
          auth: ['pagedata', 'add', 'edit', 'delete']
        }]
      }
    }, {
      path: 'travel',
      name: 'SystemTravel',
      component: '/system/travel',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemTravel',
          index: 0,
          auth: []
        }]
      }
    }, {
      path: 'other',
      name: 'SystemOther',
      component: '/system/other',
      meta: {
        title: 'SystemSetting',
        auth: 1,
        isOnly: false,
        pagePermission: [{
          title: 'SystemOther',
          index: 0,
          auth: []
        }]
      }
    }
      // , {
      //   path: 'serviceType',
      //   name: 'SystemServiceType',
      //   component: '/system/serviceType',
      //   meta: {
      //     title: 'SystemSetting',
      //     auth: 1,
      //     isOnly: false,
      //     pagePermission: [{
      //       title: 'SystemServiceType',
      //       index: 0,
      //       auth: ['pagedata', 'view', 'add', 'edit', 'delete']
      //     }]
      //   }
      // }
    ]
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
