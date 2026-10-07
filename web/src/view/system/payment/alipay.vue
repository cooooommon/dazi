<template>
  <div class="lb-system-payment">
    <top-nav></top-nav>
    <div class="page-main">
      <lb-tips>
        <a
          class="c-link"
          href="https://opendocs.alipay.com/common/02kipl#%E5%85%AC%E9%92%A5%E6%96%B9%E5%BC%8F"
          target="_blank"
          >点击查看支付宝密钥生成教程</a
        >
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="150px"
      >
        <el-form-item label="是否启用支付宝支付" prop="alipay_status">
          <el-radio-group v-model="subForm.alipay_status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips>开启后，手机端下单可选择支付宝支付</lb-tool-tips>
        </el-form-item>
        <div v-if="subForm.alipay_status === 1">
          <el-form-item label="支付宝Appid" prop="ali_appid">
            <el-input
              v-model="subForm.ali_appid"
              placeholder="请输入支付宝Appid"
            ></el-input>
          </el-form-item>
          <el-form-item label="应用私钥" prop="ali_privatekey">
            <el-input
              type="textarea"
              resize="none"
              :rows="10"
              v-model="subForm.ali_privatekey"
              placeholder="请输入应用私钥"
            ></el-input>
          </el-form-item>
          <el-form-item label="支付宝公钥" prop="ali_publickey">
            <el-input
              type="textarea"
              resize="none"
              :rows="10"
              v-model="subForm.ali_publickey"
              placeholder="请输入支付宝公钥"
            ></el-input>
          </el-form-item>
        </div>
        <el-form-item>
          <lb-button type="primary" @click="submitFormInfo" v-preventReClick>{{
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
    let checkTextItem = (rule, value, callback) => {
      if (this.subForm.alipay_status === 1 && !value) {
        callback(new Error(`请输入${rule.text}`))
      } else {
        callback()
      }
    }
    return {
      subForm: {
        alipay_status: 0,
        ali_appid: '',
        ali_privatekey: '',
        ali_publickey: ''
      },
      subFormRules: {
        alipay_status: { required: true, type: 'number', message: '请选择是否开启支付宝支付', trigger: 'blur' },
        ali_appid: { required: true, validator: checkTextItem, text: '支付宝Appid', trigger: 'blur' },
        ali_privatekey: { required: true, validator: checkTextItem, text: '应用私钥', trigger: 'blur' },
        ali_publickey: { required: true, validator: checkTextItem, text: '支付宝公钥', trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.payConfigInfo()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    submitFormInfo () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let { subForm } = this
          this.$api.system.payConfigUpdate(subForm).then(res => {
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
.lb-system-payment {
  width: 100%;
  .el-input {
    width: 300px;
  }
  .el-textarea {
    width: 600px;
  }
}
</style>
