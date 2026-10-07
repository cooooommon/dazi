<!--
 * @Descripttion: app设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-25 17:38:41
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
        label-width="150px"
        class="config-form"
      >
        <el-form-item
          :label="`${$t('action.attendantName')}注册协议`"
          prop="registration_agreement"
        >
          <lb-ueditor
            v-model="subForm.registration_agreement"
            :destroy="true"
            :ueditorType="2"
          ></lb-ueditor>
        </el-form-item>
        <el-form-item label="计费规则" prop="billing_rules">
          <lb-ueditor
            v-model="subForm.billing_rules"
            :destroy="true"
            :ueditorType="2"
          ></lb-ueditor>
        </el-form-item>
        <el-form-item label="法律声明" prop="legal_notice">
          <lb-ueditor
            v-model="subForm.legal_notice"
            :destroy="true"
            :ueditorType="2"
          ></lb-ueditor>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  data () {
    return {
      subFormRules: {
        registration_agreement: { required: true, validator: this.$reg.isNotNull, text: this.$t('action.attendantName') + '注册协议', reg_type: 2, trigger: 'blur' },
        billing_rules: { required: true, validator: this.$reg.isNotNull, text: '计费规则', reg_type: 2, trigger: 'blur' },
        legal_notice: { required: true, validator: this.$reg.isNotNull, text: '法律声明', reg_type: 2, trigger: 'blur' }
      },
      subForm: {
        registration_agreement: '',
        billing_rules: '',
        legal_notice: ''
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.technician.agreement()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    async submitForm () {
      let flag = false
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          flag = true
        }
      })
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      if (flag) {
        await this.$api.technician.agreementPost(subForm)
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
.look-image {
  width: 297px;
  height: 660px;
  border-radius: 10px;
  border: 1px solid #eee;
}
</style>
