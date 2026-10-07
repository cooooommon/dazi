<!--
 * @Descripttion: 登录
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2024-12-02 13:55:47
-->
<template>
  <div class="lb-login">
    <el-form
      @submit.native.prevent
      ref="loginForm"
      :model="loginForm"
      :rules="loginRules"
      class="login-form"
      autocomplete="on"
      label-position="left"
    >
      <div class="title-container">
        <h3 class="title">用户登录</h3>
      </div>
      <el-form-item prop="username">
        <span class="svg-container">
          <i class="iconfont icon-username"></i>
        </span>
        <el-input
          ref="username"
          v-model="loginForm.username"
          placeholder="账号"
          name="username"
          type="text"
          tabindex="1"
          autocomplete="on"
        />
      </el-form-item>
      <el-tooltip
        v-model="capsTooltip"
        content="Caps lock is On"
        placement="right"
        manual
      >
        <el-form-item prop="passwd">
          <span class="svg-container">
            <i class="iconfont icon-mima"></i>
          </span>
          <el-input
            :key="passwordType"
            ref="password"
            v-model="loginForm.passwd"
            :type="passwordType"
            placeholder="密码"
            name="passwd"
            tabindex="2"
            autocomplete="on"
            @keyup.native="checkCapslock"
            @blur="capsTooltip = false"
            @keyup.enter.native="handleLogin"
          />
          <span class="show-pwd" @click="showPwd">
            <i
              :class="
                passwordType === 'password'
                  ? 'iconfont icon-eyeclose'
                  : 'iconfont icon-eyeopen'
              "
            />
          </span>
        </el-form-item>
      </el-tooltip>
      <el-form-item prop="codeText">
        <span class="svg-container">
          <i class="iconfont iconyanzhengma"></i>
        </span>
        <el-input
          ref="codeText"
          v-model="loginForm.codeText"
          placeholder="验证码"
          name="codeText"
          type="text"
          tabindex="3"
          autocomplete="on"
          style="width: 280px"
        />

        <div class="code-text" @click="refreshCode">
          <lb-identify
            :identifyCode="identifyCode"
            :fontSizeMin="30"
            :fontSizeMax="30"
          ></lb-identify>
        </div>
      </el-form-item>
      <el-button
        :loading="loading"
        type="primary"
        style="width: 100%; margin-bottom: 30px; height: 40px"
        @click.native.prevent="handleLogin"
        >登录</el-button
      >
    </el-form>
    <div class="lb-footer-html" v-html="fHtml"></div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
  name: 'Login',
  data () {
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6 || value.length > 30) {
        callback(new Error('请输入6-30位数的密码'))
      } else {
        callback()
      }
    }
    const validateCodeText = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请输入验证码'))
      } else if (!/^[a-z0-9]+$/.test(value) || value.length !== 4) {
        callback(new Error('请输入4位数的验证码，仅限英文小写字母或数字'))
      } else {
        callback()
      }
    }
    return {
      fHtml: '',
      identifyCode: '',
      loginForm: {
        username: '',
        passwd: '',
        codeText: ''
      },
      loginRules: {
        username: [{ required: true, trigger: 'blur', validator: this.$reg.isNotNull, reg_type: 2, text: '账号' }],
        passwd: [{ required: true, trigger: 'blur', validator: validatePassword }],
        codeText: [{ required: true, trigger: 'blur', validator: validateCodeText }]
      },
      passwordType: 'password',
      capsTooltip: false,
      loading: false,
      showDialog: false,
      redirect: undefined,
      otherQuery: {}
    }
  },
  watch: {
    $route: {
      handler: function (route) {
        const query = route.query
        if (query) {
          this.redirect = query.redirect
          this.otherQuery = this.getOtherQuery(query)
        }
      },
      immediate: true
    }
  },
  created () {
  },
  mounted () {
    if (this.loginForm.username === '') {
      this.$refs.username.focus()
    } else if (this.loginForm.passwd === '') {
      this.$refs.passwd.focus()
    }
    this.makeCode()
    this.getCopyrightInfo()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    ...mapMutations(['changeRoutesItem']),
    async getCopyrightInfo () {
      let { code, data } = await this.$api.base.getConfig()
      if (code !== 200) return
      let { record_type: type, record_no: no } = data
      if (!no) return
      let recordcode = type === 2 ? no.replace(/[^0-9]/ig, '') : ``
      let image = type === 1 ? `` : `<image class="mr-sm" src="https://lbqny.migugu.com/admin/public/information.png"></image>`
      let href = type === 1 ? `https://beian.miit.gov.cn/` : `https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=${recordcode}`
      this.fHtml = `<div class="flex-y-center c-base">备案号：${image} <a class="c-base" href="${href}" target="_blank">${no}</a></div>`
    },
    checkCapslock ({ shiftKey, key } = {}) {
      if (key && key.length === 1) {
        if ((shiftKey && (key >= 'a' && key <= 'z')) || (!shiftKey && (key >= 'A' && key <= 'Z'))) {
          this.capsTooltip = true
        } else {
          this.capsTooltip = false
        }
      }
      if (key === 'CapsLock' && this.capsTooltip === true) {
        this.capsTooltip = false
      }
    },
    showPwd () {
      if (this.passwordType === 'password') {
        this.passwordType = ''
      } else {
        this.passwordType = 'password'
      }
      this.$nextTick(() => {
        this.$refs.password.focus()
      })
    },
    setCookie (cname, cvalue, exdays) {
      var d = new Date()
      d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000))
      var expires = 'expires=' + d.toGMTString()
      document.cookie = `${cname}=${cvalue};${expires};`
    },
    refreshCode () {
      this.makeCode()
    },
    makeCode () {
      this.identifyCode = Math.random().toString(36).substr(2, 4)
      this.setCookie('codeText', this.identifyCode, 3)
    },
    handleLogin () {
      this.loading = true
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.$api.base.login(this.loginForm).then(res => {
            this.loading = false
            if (res.code === 200) {
              this.changeRoutesItem({ key: 'userInfo', val: res.data.user })
              window.localStorage.setItem('massage_minitk', res.data.token)
              window.localStorage.setItem('massage_ms_username', res.data.user.username)
              window.localStorage.setItem('massage_userInfo', JSON.stringify(res.data.user))
              this.$router.push('/')
            } else {
              this.refreshCode()
            }
          })
        } else {
          this.loading = false
          return false
        }
      })
    },
    getOtherQuery (query) {
      return Object.keys(query).reduce((acc, cur) => {
        if (cur !== 'redirect') {
          acc[cur] = query[cur]
        }
        return acc
      }, {})
    }
  }
}
</script>

<style lang="scss">
$bg: #283443;
$light_gray: #fff;
$cursor: #fff;
@supports (-webkit-mask: none) and (not (cater-color: $cursor)) {
  .lb-login .el-input input {
    color: $cursor;
  }
}
/* reset element-ui css */
.lb-login {
  .el-input {
    display: inline-block;
    height: 47px;
    width: 85%;
    input {
      background: transparent;
      border: 0px;
      -webkit-appearance: none;
      border-radius: 0px;
      padding: 12px 5px 12px 15px;
      color: $light_gray;
      height: 47px;
      caret-color: $cursor;
      &:-webkit-autofill {
        box-shadow: 0 0 0px 1000px $bg inset !important;
        -webkit-text-fill-color: $cursor !important;
      }
    }
  }
  .el-form-item {
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    color: #454545;
  }
  .el-form-item__content {
    line-height: 34px;
  }
}

.lb-footer-html {
  width: 100%;
  height: 49px;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10;
  font-size: 13px;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
}
</style>

<style lang="scss" scoped>
$bg: #2d3a4b;
$dark_gray: #889aa4;
$light_gray: #eee;
.lb-login {
  min-height: 100%;
  width: 100%;
  background-color: $bg;
  overflow: hidden;
  .login-form {
    position: relative;
    width: 520px;
    max-width: 100%;
    padding: 160px 35px 0;
    margin: 0 auto;
    overflow: hidden;
  }
  .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;
    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }
  .svg-container {
    padding: 6px 5px 6px 15px;
    color: $dark_gray;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }
  .title-container {
    position: relative;
    .title {
      font-size: 26px;
      color: $light_gray;
      margin: 0px auto 40px auto;
      text-align: center;
      font-weight: bold;
    }
  }
  .show-pwd {
    position: absolute;
    right: 10px;
    top: 7px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }
  .thirdparty-button {
    position: absolute;
    right: 0;
    bottom: 6px;
  }
  .code-text {
    width: 160px;
    height: 40px;
    position: absolute;
    right: 9px;
    bottom: 5px;
  }
  @media only screen and (max-width: 470px) {
    .thirdparty-button {
      display: none;
    }
  }
}
</style>
