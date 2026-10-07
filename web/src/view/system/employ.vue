<!--
 * @Descripttion: 隐私协议
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-04-28 20:05:18
-->
<template>
  <div class="lb-system-wechat">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
        class="config-form"
      >
        <el-form-item label="应用图标" prop="app_logo">
          <lb-cover
            :fileList="subForm.app_logo"
            @selectedFiles="getCover($event, 'app_logo')"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸：160 * 160，用于登录页面展示</lb-tool-tips>
        </el-form-item>
        <el-form-item label="应用名称" prop="app_text">
          <el-input
            v-model="subForm.app_text"
            maxlength="10"
            show-word-limit
            placeholder="请输入应用名称"
          ></el-input>
          <lb-tool-tips>用于登录页面展示</lb-tool-tips>
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
      subForm: {
        app_logo: [],
        app_text: ''
      },
      subFormRules: {
        app_logo: { required: true, type: 'array', message: '请上传应用图标', trigger: 'blur' },
        app_text: { required: true, type: 'string', message: '请输入应用名称', trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      data.app_logo = data.app_logo ? [{ url: data.app_logo }] : []
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
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
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          subForm.app_logo = subForm.app_logo[0].url
          this.$api.system.configUpdate(subForm).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
            }
          })
        }
      })
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
