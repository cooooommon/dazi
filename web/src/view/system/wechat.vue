<!--
 * @Descripttion: 小程序配置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2022-11-03 11:31:27
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
        label-width="120px"
        class="config-form"
      >
        <el-form-item label="AppID" prop="appid">
          <el-input
            v-model="subForm.appid"
            placeholder="请输入AppID"
          ></el-input>
          <lb-tool-tips
            >请输入小程序AppID，填写错误会影响小程序的正常使用，谨慎修改</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="AppSecret" prop="appsecret">
          <el-input
            v-model="subForm.appsecret"
            placeholder="请输入AppSecret"
          ></el-input>
          <lb-tool-tips
            >请输入小程序AppSecret，填写错误会影响小程序的正常使用，谨慎修改</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="小程序名" prop="app_name">
          <el-input
            v-model="subForm.app_name"
            maxlength="10"
            show-word-limit
            placeholder="请输入小程序名"
          ></el-input>
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
        appid: '',
        appsecret: '',
        app_name: ''
      },
      subFormRules: {
        appid: { required: true, validator: this.$reg.isNotNull, text: 'AppID', reg_type: 2, trigger: 'blur' },
        appsecret: { required: true, validator: this.$reg.isNotNull, text: 'AppSecret', reg_type: 2, trigger: 'blur' },
        app_name: { required: true, validator: this.$reg.isNotNull, text: '小程序名', reg_type: 2, trigger: 'blur' }
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
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let { subForm } = this
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
