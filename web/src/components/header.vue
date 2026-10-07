<!-- 页面头部 -->
<template>
  <div class="lb-header">
    <div class="lb-left">
      <div class="logo">
        <!-- <el-image class="logo-img" :src="routesItem.logo" fit="cover" @click="$router.push('/')" ></el-image> -->
        <div class="flex-center logo-img c-base">
          <el-link :underline="false" href="/"
            ><i class="iconfont icon-home-fill c-link"></i
          ></el-link>
        </div>
      </div>
      <div v-if="isIndex" class="admin-title"></div>
      <div v-else class="menu-title">{{ $t('menu.' + title) }}</div>
    </div>
    <div class="lb-right">
      <el-link :underline="false" href="/" icon="el-icon-s-home"
        >返回首页</el-link
      >
      <el-link :underline="false" @click="clearCache" :icon="icon"
        >清除缓存</el-link
      >
      <div
        @click="$router.push(`/notice/list?have_look=0`)"
        slot="reference"
        v-if="
          routesItem.have_police_notice &&
          userInfo.is_admin !== 0 &&
          routesItem.isDistressNotice
        "
      >
        <el-badge
          :value="routesItem.police_num"
          :max="99"
          :hidden="!routesItem.police_num"
          class="notice-item"
        >
          <i class="iconfont iconyijianbaojing"></i>
          {{ $t('action.attendantName') }}求救通知
        </el-badge>
      </div>

      <el-popover
        placement="bottom"
        width="400"
        v-model="visible.order"
        v-if="routesItem.have_order_notice"
      >
        <div class="pd-sm f-desc" style="cursor: pointer">
          <div class="flex-between pb-lg b-1px-b">
            <div class="f-title c-title">订单通知</div>
            <div
              @click="toAllLook"
              :class="order_list.data.length > 0 ? 'c-link' : 'c-caption'"
              v-show="routesItem.have_order_read"
            >
              全部标为已读
            </div>
          </div>
          <div
            @click="updateItem(item)"
            class="item-child pt-lg pb-lg flex-center b-1px-b"
            v-for="(item, index) in order_list.data"
            :key="index"
          >
            <i class="iconfont icontongzhi c-link mr-lg"></i>
            <div class="flex-1 pd-lg fill-body radius-5">
              {{ noticeTypeText[item.type] }}
            </div>
          </div>
          <div
            class="flex-center c-caption pd-lg b-1px-b"
            v-if="order_list.data.length === 0"
          >
            暂无未读消息
          </div>
          <div
            @click="$router.push(`/order`), (visible.order = false)"
            class="flex-center pt-lg c-link"
          >
            前往通知中心<i
              class="iconfont icon-right ml-sm"
              style="font-size: 14px"
            ></i>
          </div>
        </div>
        <div
          @click="getNoticeList('order')"
          slot="reference"
          v-if="routesItem.isOrderNotification"
        >
          <el-badge
            :value="routesItem.notice_num"
            :max="99"
            :hidden="!routesItem.notice_num"
            class="notice-item"
          >
            <i class="iconfont icon-tongzhi-fill"></i>
          </el-badge>
        </div>
      </el-popover>

      <el-dropdown
        v-if="!isWe7"
        class="user-name"
        trigger="click"
        @command="handleCommand"
      >
        <span class="el-dropdown-link">
          {{ username }}
          <i class="el-icon-caret-bottom"></i>
        </span>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item command="myaccount">编辑账户</el-dropdown-item>
          <el-dropdown-item command="loginout">退出登录</el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
  </div>
</template>

<script>
// import Bus from '@/Bus.js'
import { mapState, mapMutations } from 'vuex'

export default {
  data () {
    return {
      isIndex: true,
      title: '',
      icon: 'el-icon-s-open',
      noticeTypeText: {
        1: `你有一笔新的订单，请及时联系${this.$t('action.attendantName')}处理`,
        2: `你有一笔新的退款订单，请及时处理`,
        3: `您有一笔新的订单，已被${this.$t('action.attendantName')}拒绝，请在后台及时处理转单，避免平台订单流失`,
        4: `你有一笔新的订单，${this.$t('action.attendantName')}长时间未接单，请联系${this.$t('action.attendantName')}及时接单`,
        5: `你有一笔新的订单有迟到风险，请跟进${this.$t('action.attendantName')}是否达到目的地`,
        6: `监测到有${this.$t('action.attendantName')}完成服务后，未离开目的地，请联系${this.$t('action.attendantName')}询问具体情况，如遇安全问题，请及时报警，如是跳单情况，根据平台规则自行处理`
      },
      order_list: { data: [] },
      visible: { order: false },
      isAdmin: window.lbConfig.isWe7 && window.lbConfig.is_founder,
      agentUrl: '',
      userInfo: JSON.parse(window.localStorage.getItem('massage_userInfo')),
      username: window.localStorage.getItem('massage_ms_username'),
      isWe7: window.lbConfig.isWe7,
      dialogVisible: false
    }
  },
  created () {
    this.handleTitle(this.$route.meta.title)
    this.handleAgentUrl()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    ...mapMutations(['changeRoutesItem']),
    async getNoticeList (key) {
      this.visible[key] = false
      let { ShopOrderPage, ShopRefuseOrderPage, ShopRefundPage } = this.routesItem
      let types = ''
      if (ShopRefundPage) { types += types ? ',2' : '2' }
      if (ShopRefuseOrderPage) {
        types += types ? ',3' : '3'
      }
      if (ShopOrderPage) {
        types += types ? ',1,4,5,6' : '1,4,5,6'
      }
      let { code, data } = await this.$api.shop.noticeList({ page: 1, limit: 5, type: 0, have_look: 0, types })
      if (code !== 200) return
      this[`${key}_list`] = data
      this.visible[key] = true
    },
    async toAllLook () {
      if (this.order_list.data.length === 0) return
      await this.$api.shop.allLook()
      this.$message.success(this.$t('tips.successOper'))
      this.changeRoutesItem({ key: 'notice_num', val: 0 })
      this.order_list.data = []
    },
    async updateItem (item) {
      let { id, order_id: oid = 0, is_add: add = 0, type = 1 } = item
      let { ShopOrder, ShopBellOrder, ShopRefund, ShopBellRefund } = this.routesItem
      if ((type === 1 && ((add === 0 && ShopOrder) || (add === 1 && ShopBellOrder))) || (type === 2 && ((add === 0 && ShopRefund) || (add === 1 && ShopBellRefund)))) {
        this.$router.push(type === 1 ? `/shop/order/detail?id=${oid}` : `/shop/refund/detail?id=${oid}`)
        this.visible.order = false
        await this.$api.shop.noticeUpdate({ id, have_look: 1 })
        this.changeRoutesItem({ key: 'notice_num', val: this.routesItem.notice_num > 0 ? this.routesItem.notice_num - 1 : 0 })
      }
    },
    // 用户名下拉菜单选择事件
    handleCommand (command) {
      if (command === 'myaccount') {
        this.$emit('handleAccount', true)
      } else if (command === 'loginout') {
        this.changeRoutesItem({ key: 'isAuth', val: false })
        window.localStorage.removeItem('massage_minitk')
        window.localStorage.removeItem('massage_ms_username')
        window.localStorage.removeItem('massage_userInfo')
        this.$router.push('/login')
        window.location.reload()
      }
    },
    clearCache () {
      this.icon = 'el-icon-loading'
      this.$api.base.clearCache().then(res => {
        this.icon = 'el-icon-s-open'
        if (res.code === 200) {
          this.$message.success('清除成功！')
        }
      })
    },
    handleTitle (title) {
      this.isIndex = !title
      this.title = title
    },
    handleAgentUrl () {
      let currentUrl = window.location.href
      let agentUrl = currentUrl.slice(0, currentUrl.indexOf('#')) + '&s=/agent/index'
      this.agentUrl = agentUrl
    }
  },
  watch: {
    $route: {
      handler (val, oldVal) {
        this.handleTitle(val.meta.title)
      },
      // 深度观察监听
      deep: true
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-header {
  width: 100%;
  height: 70px;
  position: fixed;
  top: 0;
  left: 0;
  // border-bottom: 1px solid #eff2f6;
  background: #ffffff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 10;

  .lb-left {
    display: flex;
    height: 70px;
    background: #273543;

    .logo {
      width: 120px;
      height: 70px;
      // background: #273543;
      display: flex;
      justify-content: center;
      align-items: center;

      .logo-img {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #fff;
        cursor: pointer;
      }
    }

    .admin-title {
      padding: 0 32px;
      display: flex;
      align-items: center;
      background: #ffffff;

      span {
        margin-right: 5px;
      }
    }

    .menu-title {
      height: 71px !important;
      width: 160px;
      border-right: 1px solid #eeeeee;
      border-bottom: 1px solid #eeeeee;
      height: 100%;
      display: flex;
      font-size: 16px;
      color: #101010;
      align-items: center;
      justify-content: center;
      background: #ffffff;
    }
  }

  .lb-right {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 30px;
    cursor: pointer;
    .notice-item {
      margin: 0 20px;
    }

    .el-dropdown-link {
      cursor: pointer;
    }

    .el-dropdown-menu__item {
      text-align: center;
    }

    span {
      margin: 0 5px 0 10px;
    }

    .el-link {
      margin-left: 10px;
    }
  }
}
</style>
