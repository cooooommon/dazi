<!-- 公共组件 -->
<template>
  <div class="home">
    <lb-header @handleAccount="handleAccount"></lb-header>
    <sidebar></sidebar>
    <div
      class="container"
      :style="{
        'margin-right': adSwitch ? '220px' : '0px',
        'margin-left': sideBarSwitch ? '280px' : '120px'
      }"
    >
      <!-- 内容区域 -->
      <div class="main">
        <transition name="fade" mode="out-in">
          <keep-alive :max="1">
            <router-view v-if="$route.meta.keepAlive"></router-view>
          </keep-alive>
        </transition>
        <transition name="fade" mode="out-in">
          <router-view v-if="!$route.meta.keepAlive"></router-view>
        </transition>
      </div>
      <div class="home-footer" v-html="fHtml"></div>
    </div>
    <!-- 右侧展开折叠栏 ad -->
    <!-- <ad></ad> -->
    <!-- <lb-footer></lb-footer> -->

    <!-- 编辑账户 -->
    <el-dialog
      title="编辑账户"
      :visible.sync="showDialog.account"
      width="520px"
    >
      <el-form
        @submit.native.prevent
        :model="accountForm"
        :rules="accountFormRules"
        ref="accountForm"
        label-width="130px"
        class="basic-form"
        style="padding-right: 30px"
      >
        <el-form-item label="账号" prop="account">
          <el-input
            v-model="accountForm.account"
            :disabled="true"
            placeholder="账号"
          ></el-input>
        </el-form-item>
        <!-- <el-form-item
          label="原密码"
          prop="old_passwd"
        >
          <el-input
            v-model="accountForm.old_passwd"
            placeholder="请输入原密码"
          ></el-input>
        </el-form-item> -->
        <el-form-item label="新密码" prop="new_passwd">
          <el-input
            v-model="accountForm.new_passwd"
            placeholder="请输入新密码"
          ></el-input>
        </el-form-item>
        <el-form-item label="确认新密码" prop="again_passwd">
          <el-input
            v-model="accountForm.again_passwd"
            placeholder="请确认新密码"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitFormInfo('accountForm')">{{
            $t('action.submit')
          }}</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- 通知提醒 -->
    <el-dialog
      :title="noticeType[ordernoticeForm.type]"
      :visible.sync="showDialog.ordernotice"
      :close-on-click-modal="false"
      :show-close="false"
      width="650px"
      center
    >
      <div style="padding: 0 30px">
        <div class="c-title text-bold" style="font-size: 15px">
          {{ noticeTypeText[ordernoticeForm.type] }}
        </div>
        <div class="flex-y-center c-paragraph mt-md">
          订单ID：{{ ordernoticeForm.order_id }}
          <div class="flex-y-center ml-lg">
            订单号：
            <div class="c-link cursor-pointer">
              {{ ordernoticeForm.order_code }}
            </div>
          </div>
        </div>
        <div class="c-paragraph mt-sm">
          通知时间：{{ ordernoticeForm.create_time }}
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="toChangeOrderNoticePop">忽 略</el-button>
        <el-button
          type="primary"
          v-if="
            routesItem.isOrderNoticeAudioPlay &&
            ((routesItem.ShopOrder &&
              [1, 4, 5, 6].includes(ordernoticeForm.type)) ||
              (routesItem.ShopRefuseOrder &&
                [3].includes(ordernoticeForm.type)) ||
              (routesItem.ShopRefund && [2].includes(ordernoticeForm.type)))
          "
          @click="goNoticeItem"
          v-preventReClick
          >去查看</el-button
        >
      </span>
    </el-dialog>
  </div>
</template>

<script>
import lbHeader from './header'
import lbFooter from './footer'
import sidebar from './sidebar'
import ad from './ad'
import { mapGetters, mapState, mapMutations } from 'vuex'
import moment from 'moment'
export default {
  name: 'Home',
  data () {
    let validatePassword = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请输入密码'))
      } else if (!/^(\S){6,20}$/.test(value)) {
        callback(new Error('请输入6-20位非空白符的字符!'))
      } else {
        callback()
      }
    }
    let validateAgainPassword = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请再次输入密码'))
      } else if (value !== this.accountForm.new_passwd) {
        callback(new Error('两次输入密码不一致'))
      } else {
        callback()
      }
    }
    return {
      isChangeRoutes: true,
      fHtml: 'Dragon-armes-sujet-Compétence',
      noticeType: {
        1: `来单通知`,
        2: `退款通知`,
        3: `拒单通知`,
        4: `未接单通知`,
        5: `服务迟到通知`,
        6: `跳单预警通知`
      },
      noticeTypeText: {
        1: `你有一笔新的订单，请及时联系${this.$t('action.attendantName')}处理`,
        2: `你有一笔新的退款订单，请及时处理`,
        3: `您有一笔新的订单，已被${this.$t('action.attendantName')}拒绝，请在后台及时处理转单，避免平台订单流失`,
        4: `你有一笔新的订单，${this.$t('action.attendantName')}长时间未接单，请联系${this.$t('action.attendantName')}及时接单`,
        5: `你有一笔新的订单有迟到风险，请跟进${this.$t('action.attendantName')}是否达到目的地`,
        6: `监测到有${this.$t('action.attendantName')}完成服务后，未离开目的地，请联系${this.$t('action.attendantName')}询问具体情况，如遇安全问题，请及时报警，如是跳单情况，根据平台规则自行处理`
      },
      showDialog: { account: false, ordernotice: false },
      accountForm: {
        account: window.localStorage.getItem('massage_ms_username'),
        new_passwd: ''
      },
      accountFormRules: {
        new_passwd: {
          required: true,
          type: 'string',
          validator: validatePassword,
          trigger: 'blur'
        },
        again_passwd: {
          required: true,
          type: 'string',
          validator: validateAgainPassword,
          trigger: 'blur'
        }
      },
      ordernoticeForm: {},
      authForm: {
        pass: ''
      },
      authFormRules: {
        pass: { required: true, type: 'string', message: '请输入获取到的激活口令', trigger: 'blur' }
      },
      modelAuthImg: 'https://lbqny.migugu.com/admin/card/WechatIMG322.jpeg',
      modelAuthList: {
        // https://lbqny.migugu.com/weixin/WechatIMG495.jpeg
        longbing_card: 'https://lbqny.migugu.com/admin/card/WechatIMG322.jpeg',
        longbing_radarstore: 'https://lbqny.migugu.com/admin/card/WechatIMG322.jpeg',
        longbing_decorate: 'https://lbqny.migugu.com/admin/card/WechatIMG322.jpeg',
        longbing_shortvideo: 'https://lbqny.migugu.com/admin/card/WechatIMG322.jpeg',
        longbing_restaurant: 'https://lbqny.migugu.com/admin/card/WechatIMG322.jpeg',
        longbing_member: 'https://lbqny.migugu.com/admin/card/WechatIMG322.jpeg'
      },
      userInfo: JSON.parse(window.localStorage.getItem('massage_userInfo'))
    }
  },
  components: {
    lbHeader,
    lbFooter,
    sidebar,
    ad
  },
  created () {
    this.getCopyrightInfo()
  },
  computed: {
    ...mapGetters(['adSwitch', 'sideBarSwitch']),
    ...mapState({
      routesItem: state => state.routes
    })
  },
  // 监听器
  watch: {
    async $route (to, from) {
    },
    'routesItem.notice_info' (val, oldval) {
      let { id = 0 } = val
      let { id: oid = 0 } = oldval
      if (!id || id === oid) return
      this.ordernoticeForm = val
      // ShopOrder 服务订单  ShopRefuseOrder 拒单  ShopRefund 服务退款
      let { ShopOrderPage, ShopRefuseOrderPage, ShopRefundPage } = this.routesItem
      if (val.notice_type === 2 && ([5, 6].includes(val.type) || (ShopOrderPage && [1, 4].includes(val.type)) || (ShopRefuseOrderPage && [3].includes(val.type)) || (ShopRefundPage && [2].includes(val.type)))) {
        this.showDialog.ordernotice = true
      } else {
        this.showDialog.ordernotice = false
      }
    }
  },
  methods: {
    ...mapMutations(['changeIsShowPrompt', 'changeRoutesItem']),
    getCopyrightInfo () {
      let { systemCopyInfo } = this.routesItem
      if (systemCopyInfo === 1) {
        this.fHtml = ''
      } else {
        if (!systemCopyInfo.footerleft) {
          this.fHtml =
            `<div>Powered by <a class='el-link el-link--info' href="http://www.we7.cc"><b>微擎</b></a>
                  v${systemCopyInfo.version} © 2014-2015
                  <a class='el-link el-link--info' href="http://www.we7.cc">www.we7.cc</a></div>`
        } else {
          this.fHtml = systemCopyInfo.footerleft
        }
        if (systemCopyInfo.icp) {
          this.fHtml = this.fHtml +
            `<div>备案号：<a class='el-link el-link--info' href="http://www.miitbeian.gov.cn" target="_blank">${systemCopyInfo.icp}</a></div>`
        }
      }
    },
    handleAccount (flag) {
      this.showDialog.account = flag
    },
    async toChangeOrderNoticePop () {
      let { id } = this.ordernoticeForm
      await this.$api.shop.noticeUpdate({ id, have_look: 1 })
      this.changeRoutesItem({ key: 'notice_num', val: this.routesItem.notice_num > 0 ? this.routesItem.notice_num - 1 : 0 })
      this.changeRoutesItem({ key: 'notice_info', val: {} })
      this.showDialog.ordernotice = false
    },
    submitFormInfo (name) {
      let flag = true
      this.$refs[name].validate(valid => {
        if (!valid) flag = false
      })
      if (!flag) return
      let {
        new_passwd: pass
      } = JSON.parse(JSON.stringify(this.accountForm))
      this.$api.base.updatePasswd({
        pass
      }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successRev'))
          this.changeRoutesItem({ key: 'isAuth', val: false })
          window.localStorage.removeItem('massage_minitk')
          window.localStorage.removeItem('massage_ms_username')
          window.localStorage.removeItem('massage_userInfo')
          this.$router.push('/login')
          window.location.reload()
        }
      })
    },
    submitAuthForm (name) {
      this.$refs[name].validate(valid => {
        if (valid) {
          let { pass } = this.authForm
          this.$api.baseGiveAuth({
            pass
          }).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.changeRoutesItem({ key: 'isFreeAuth', val: false })
            }
          })
        }
      })
    },
    async goNoticeItem () {
      let { id, order_id: oid = 0, is_add: add = 0, type = 1 } = this.ordernoticeForm
      let { ShopOrder, ShopBellOrder, ShopRefund, ShopBellRefund, ShopRefuseOrder } = this.routesItem
      if (([1, 4, 5, 6].includes(type) && ((add === 0 && ShopOrder) || (add === 1 && ShopBellOrder))) || (type === 2 && ((add === 0 && ShopRefund) || (add === 1 && ShopBellRefund))) || (type === 3 && ShopRefuseOrder)) {
        let url = type === 2 ? `/shop/refund/detail?id=${oid}` : type === 3 ? `/shop/order/refuse` : `/shop/order/detail?id=${oid}`
        this.$router.push(url)
        this.showDialog.ordernotice = false
        await this.$api.shop.noticeUpdate({ id, have_look: 1, is_pop: 1 })
        this.changeRoutesItem({ key: 'notice_num', val: this.routesItem.notice_num > 0 ? this.routesItem.notice_num - 1 : 0 })
      }
    }
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    }
  }
}
</script>

<style lang="scss" scoped>
.home {
  border: 1px solid transparent;
  background: #f0f0f0;

  .container {
    margin: 70px 0 0 140px;
    padding: 20px;
    background: $bgThemeColor;
    transition: margin 0.2s linear;

    .main {
      width: 100%;
      background: #fff;
      position: relative;
      min-height: calc(100vh - 70px - 92px);
    }

    .home-footer {
      height: 49px;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #ccc;
      font-size: 12px;
      z-index: 10;
      margin: 0 auto;
      line-height: 1;
      width: 50%;
    }
  }
}

.fade-transform-leave-active,
.fade-transform-enter-active {
  transition: all 0.5s;
}

.fade-transform-enter {
  opacity: 0;
  transform: translateX(-30px);
}

.fade-transform-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.slide-fade-enter-active {
  transition: all 2s ease;
}

.slide-fade-leave-active {
  transition: all 0s ease;
}

.slide-fade-enter,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}

.fade-leave,
.fade-enter-to {
  opacity: 1;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.2s;
}
.flex-center {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
