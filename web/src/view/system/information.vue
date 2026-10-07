<!--
 * @Descripttion: app设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:08:50
-->
<template>
  <div class="lb-system-wechat">
    <top-nav></top-nav>
    <div class="page-main">
      <lb-tips>备案信息将展示在登录页面</lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
        class="config-form"
      >
        <el-form-item label="备案类型" prop="record_type">
          <el-radio-group v-model="subForm.record_type">
            <el-radio :label="1">ICP备案/许可证号</el-radio>
            <el-radio :label="2">网站联网备案号</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >用于登录页面展示，以百度备案号为例：
            <div class="mt-sm">ICP备案/许可证号：京ICP证030173号</div>
            <div class="mt-sm">网站联网备案号：京公网安备 11000002000001号</div>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="备案号" prop="record_no">
          <el-input
            v-model="subForm.record_no"
            maxlength="50"
            show-word-limit
            placeholder="请输入备案号"
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
        record_type: '',
        record_no: ''
      },
      subFormRules: {
        record_type: { required: true, type: 'number', message: '请选择备案类型', trigger: 'blur' },
        record_no: { required: true, validator: this.$reg.isNotNull, text: '备案号', reg_type: 2, trigger: 'blur' }
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
          subForm.record_type = subForm.record_no && subForm.record_no.includes('公网安备') ? 2 : 1
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
