<!--
 * @Description:
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-07-03 10:32:06
 * @LastEditors: wen kun
-->
<template>
  <div id="app">
    <router-view v-if="isRouterAlive" />
    <prompt></prompt>
  </div>
</template>
<script>
import { mapState, mapMutations } from 'vuex'
import prompt from './components/prompt.vue'
export default {
  name: 'App',
  components: {
    prompt
  },
  provide () {
    return {
      reload: this.reload
    }
  },
  data () {
    return {
      isRouterAlive: true,
      status: '',
      isFirst: false,
      name: 'App',
      timer: null,
      previous: null,
      token: '',
      noticeNum: null,
      audio: null
    }
  },
  // 监听器
  watch: {
    async $route (to, from) {
    },
    'routesItem.attendant_name' (val, oldval) {
      this.mergeLocaleMessage()
    },
    'routesItem.channel_menu_name' (val, oldval) {
      this.mergeLocaleMessage()
    },
    'routesItem.have_police_notice' (val, oldval) {
      if (val) {
        if (this.noticeNum) {
          clearInterval(this.noticeNum)
        }
        this.getNoticeNum()
      }
    },
    'routesItem.have_order_notice' (val, oldval) {
      if (this.noticeNum) {
        clearInterval(this.noticeNum)
      }
      this.getNoticeNum()
    },
    // 求救通知
    'routesItem.police_num' (val, oldval) {
      let { is_admin: admin = 0 } = JSON.parse(window.localStorage.getItem('massage_userInfo'))
      let { help_voice: voice, have_police_read: read, notice_info: final = {} } = this.routesItem
      if (admin && read && final.notice_type === 1) {
        if (val > oldval && !this.audio) {
          this.playAudio(voice, true)
        }
        if (val > 0) {
          this.toPlay(voice, true)
        }
        if (val === 0 && this.audio) {
          this.audio.load()
          this.audio.src = ''
        }
      }
    },
    // 订单通知
    'routesItem.notice_audio' (val, oldval) {
      // let { police_num: police, have_police_read: read } = this.routesItem
      let { id = 0, type, notice_type: noticetype } = val
      let { id: oid } = oldval
      if (noticetype === 1 && id === oid) return
      // 1下单 2退款 3拒单  4未接单
      // ShopOrder 服务订单  ShopRefuseOrder 拒单  ShopRefund 服务退款
      let { ShopOrderPage, ShopRefuseOrderPage, ShopRefundPage } = this.routesItem
      if (!this.routesItem.isOrderNoticeAudioPlay) {
        return
      }
      if ((!ShopOrderPage && (type === 4 || type === 1)) || (!ShopRefuseOrderPage && type === 3) || (!ShopRefundPage && type === 2)) {
        return
      }
      let src = `https://lbqny.migugu.com/admin/anmo/pc/order_speech${type}.mp3`
      if (!this.audio) {
        this.playAudio(src, false)
      } else {
        this.toPlay(src, false)
      }
    }
  },
  created () {
    this.getNoticeNum()
    this.mergeLocaleMessage()
  },
  mounted () {
    let that = this
    that.audio = null
    window.onresize = () => {
      that.fnThrottle(() => {
        let clientWidth = document.documentElement.clientWidth
        if (clientWidth < 1200) {
          that.$store.commit('handleAdSwitch', false)
        }
      }, 300)()
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    ...mapMutations(['changePromptData', 'changeIsShowPrompt', 'changeRoutesItem']),
    reload () {
      this.isRouterAlive = false
      this.$nextTick(() => {
        this.isRouterAlive = true
      })
    },
    async getNoticeNum () {
      if (!this.audio) {
        this.playAudio('', false)
      }
      let { have_police_notice: police = 0, have_order_notice: order = 0 } = this.routesItem
      if (police || order) {
        this.getNoticeCount()
        this.noticeNum = setInterval(() => {
          this.getNoticeCount()
        }, 10000)
      }
    },
    async getNoticeCount () {
      let minitk = window.localStorage.getItem('massage_minitk')
      if (!minitk) return
      let { ShopOrderPage, ShopRefuseOrderPage, ShopRefundPage } = this.routesItem
      let types = ''
      if (ShopRefundPage) { types += types ? ',2' : '2' }
      if (ShopRefuseOrderPage) {
        types += types ? ',3' : '3'
      }
      if (ShopOrderPage) {
        types += types ? ',1,4,5,6' : '1,4,5,6'
      }
      let res = await this.$api.shop.noLookCount({ types })
      // 未登录/业务错误时响应没有 data 字段，兜底空对象防止崩溃
      let data = (res && res.data) || {}
      let { notice_num: notice = 0, police_num: police = 0, help_voice: voice = '', final = {} } = data
      this.changeRoutesItem({ key: 'notice_num', val: notice })
      this.changeRoutesItem({ key: 'police_num', val: police })
      if (process.env.NODE_ENV == 'development') {
        notice = 0
        police = 0
      }
      let { id, is_admin: admin = 0 } = JSON.parse(window.localStorage.getItem('massage_userInfo'))
      let { have_police_read: read, isOrderNotification: isNotice } = this.routesItem

      this.changeRoutesItem({ key: 'notice_info', val: final })
      if (id && admin && read && police > 0 && this.audio && this.audio.paused && final.notice_type === 1) {
        this.toPlay(voice, true)
      }
      this.changeRoutesItem({ key: 'help_voice', val: voice })
      if (notice > 0 && final.notice_type === 2 && isNotice) {
        this.changeRoutesItem({ key: 'notice_audio', val: final })
      }
    },
    playAudio (src, loop) {
      if (this.$route.path === '/count') return
      if (!src) return // 未配置提示音时不播放，避免空 src 报「音频加载失败」
      let that = this
      this.audio = new Audio()
      this.audio.src = src
      this.audio.loop = loop
      let playPromise
      playPromise = this.audio.play()
      if (playPromise) {
        playPromise.then((res) => {
          that.audio.pause()
        }).catch((e) => {
          console.error(e, '音频加载失败'+src)
        })
      }
    },
    toPlay (src, loop) {
      if (this.$route.path === '/count') return
      if (!src) return // 未配置提示音时不播放
      this.audio.load()
      this.audio.src = src
      this.audio.loop = loop
      this.audio.play()
    },
    async mergeLocaleMessage () {
      let zh = JSON.parse(JSON.stringify(this.$i18n.messages.zh))
      let { old_attendant_name: oldName, attendant_name: name, old_channel_menu_name: oldChannelName = '', channel_menu_name: channelName = '' } = this.routesItem
      let reg = new RegExp(oldName, 'g')
      // let channelReg = new RegExp(oldChannelName, 'g')
      for (let i in zh.action) {
        if (zh.action[i].includes(oldName)) {
          zh.action[i] = zh.action[i].replace(reg, name)
        }
        // if (zh.action[i].includes(oldChannelName)) {
        //   zh.action[i] = zh.action[i].replace(channelReg, channelName)
        // }
      }
      for (let i in zh.menu) {
        if (zh.menu[i].includes(oldName)) {
          zh.menu[i] = zh.menu[i].replace(reg, name)
        }
        // if (zh.menu[i].includes(oldChannelName)) {
        //   zh.menu[i] = zh.menu[i].replace(channelReg, channelName)
        // }
      }
      zh.tips.franchiseeDelete = zh.tips.franchiseeDelete.replace(reg, name)
      this.$i18n.mergeLocaleMessage('zh', zh)
      this.changeRoutesItem({ key: 'old_attendant_name', val: name })
      // this.changeRoutesItem({ key: 'old_channel_menu_name', val: channelName })
    },
    fnThrottle (fn, delay, atleast) {
      let that = this
      return function () {
        let now = +new Date()
        if (!that.previous) that.previous = now
        if (atleast && now - that.previous > atleast) {
          fn()
          that.previous = now
          clearTimeout(that.timer)
        } else {
          clearTimeout(that.timer)
          that.timer = setTimeout(function () {
            fn()
            that.previous = null
          }, delay)
        }
      }
    }
  }
}
</script>

<style lang='scss'>
@import url('./style/reset.css');
@import url('./style/icon.css');
@import url('./style/base.css');
@import url('./style/diy.css');
@import url('./style/common.css');
</style>
