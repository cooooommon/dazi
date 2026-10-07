<!--
 * @Descripttion: 隐私协议
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2022-12-30 10:47:16
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
        <el-form-item label="用户隐私协议" prop="login_protocol">
          <lb-ueditor
            v-model="subForm.login_protocol"
            :destroy="true"
            :ueditorType="3"
          ></lb-ueditor>
        </el-form-item>
        <el-form-item label="个人信息保护指引" prop="information_protection">
          <lb-ueditor
            v-model="subForm.information_protection"
            :destroy="true"
            :ueditorType="3"
          ></lb-ueditor>
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
        login_protocol: '',
        information_protection: ''
      },
      subFormRules: {
        login_protocol: { required: true, validator: this.$reg.isNotNull, text: '用户隐私协议', reg_type: 2, trigger: 'blur' },
        information_protection: { required: true, validator: this.$reg.isNotNull, text: '个人信息保护指引', reg_type: 2, trigger: 'blur' }
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
