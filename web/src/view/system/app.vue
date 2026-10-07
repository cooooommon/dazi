<!--
 * @Descripttion: app设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:08:24
-->
<template>
  <div class="lb-system-wechat">
    <top-nav></top-nav>
    <div class="page-main">
      <lb-tips> APP下载页面链接：{{ link }} </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="120px"
        class="config-form"
      >
        <el-form-item label="AppID" prop="app_app_id">
          <el-input
            v-model="subForm.app_app_id"
            placeholder="请输入微信开放平台移动应用AppID"
          ></el-input>
          <lb-tool-tips
            >请输入微信开放平台申请移动应用的AppID，输入错误会影响APP的正常使用，谨慎修改</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="AppSecret" prop="app_app_secret">
          <el-input
            v-model="subForm.app_app_secret"
            placeholder="请输入微信开放平台移动应用AppSecret"
          ></el-input>
          <lb-tool-tips
            >请输入微信开放平台申请移动应用的AppSecret，输入错误会影响APP的正常使用，谨慎修改</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="APP下载引导图" prop="app_download_img">
          <lb-cover
            :fileList="subForm.app_download_img"
            @selectedFiles="getCover($event, 'app_download_img')"
          ></lb-cover>
          <lb-tool-tips
            >图片建议尺寸：1198 * 2320，图片内容建议居中展示
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="Android下载链接" prop="android_link">
          <el-input
            v-model="subForm.android_link"
            placeholder="请输入Android下载链接"
          ></el-input>
          <lb-tool-tips>建议将安卓包上传至云存储 </lb-tool-tips>
        </el-form-item>
        <el-form-item label="IOS下载链接" prop="ios_link">
          <el-input
            v-model="subForm.ios_link"
            placeholder="请输入IOS下载链接"
          ></el-input>
          <lb-tool-tips
            >例如：https://apps.apple.com/cn/app/具体ID值，可通过AppStore分享应用链接查看</lb-tool-tips
          >
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" 
            v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      link: '',
      arr: ['app_app_id', 'app_app_secret'],
      arr1: ['app_download_img', 'android_link', 'ios_link'],
      subForm: {
        app_app_id: '',
        app_app_secret: '',
        app_download_img: [],
        android_link: '',
        ios_link: ''
      },
      subFormRules: {
        app_app_id: { required: true, type: 'string', message: '请输入微信开放平台移动应用AppID', trigger: 'blur' },
        app_app_secret: { required: true, type: 'string', message: '请输入微信开放平台移动应用AppSecret', trigger: 'blur' }
      }
    }
  },
  created () {
    let url = window.location.href.split('/#')[0]
    this.link = `${url}/h5/?#/user/pages/app-download`
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let [config, appinfo] = await Promise.all([this.$api.system.configInfo(), this.$api.system.configInfoSchedule()])
      let item = this.$util.pick(config.data, this.arr)
      let aitem = this.$util.pick(appinfo.data, this.arr1)
      let data = Object.assign({}, item, aitem)
      data.app_download_img = data.app_download_img && data.app_download_img.length > 0 ? [{ url: data.app_download_img }] : []
      this.subForm = data
    },
    getCover (img, key) {
      this.subForm[key] = img
    },
    selectedFiles (imgs, key) {
      this.subForm[key].push(...imgs)
    },
    moveFiles (imgs, key) {
      this.subForm[key] = imgs
    },
    async submitForm () {
      let flag = false
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          flag = true
        }
      })
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      subForm.app_download_img = subForm.app_download_img && subForm.app_download_img.length > 0 ? subForm.app_download_img[0].url : ''
      let item = this.$util.pick(subForm, this.arr)
      let aitem = this.$util.pick(subForm, this.arr1)
      if ((aitem.android_link || aitem.ios_link) && !aitem.app_download_img) {
        this.$message.error(`请上传APP下载引导图`)
        return
      }
      if (flag) {
        await Promise.all([this.$api.system.configUpdate(item), this.$api.system.configUpdateSchedule(aitem)])
        this.$message.success(this.$t('tips.successSub'))
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-wechat {
  width: 100%;
  .config-form {
    .el-input {
      width: 300px;
    }
  }
}
</style>
