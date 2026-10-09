<template>
  <div class="lb-system-payment">
    <top-nav></top-nav>
    <div class="page-main">
      <lb-tips>
        <div class="vp-tips">
          <p>1、OfferID 与现网AppKey 在小程序后台【支付与交易 - 虚拟支付 - 基本配置】获取；</p>
          <p>2、代币兑换比例须与小程序后台【虚拟支付 - 代币配置】一致（如1元=100代币填100），修改后请两处同步；</p>
          <p>3、代币需在小程序后台【代币配置】完成「发布」才能收款（修改比例后需重新发布，变更可能需审核），否则拉起支付报 -15009 COIN_NOT_PUBLISH（代币未发布）；</p>
          <p>4、请同时在小程序后台【虚拟支付 - 基础配置 - 发货推送配置】填写 /index.php/virtualpay/notify/receive（前缀为你小程序请求的域名）。</p>
        </div>
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="150px"
      >
        <el-form-item label="是否启用虚拟支付" prop="enabled">
          <el-radio-group v-model="subForm.enabled">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips>开启后，小程序端微信支付改走虚拟支付（代币模式）；关闭后恢复原微信支付</lb-tool-tips>
        </el-form-item>
        <div v-if="subForm.enabled === 1">
          <el-form-item label="OfferID" prop="offer_id">
            <el-input
              v-model="subForm.offer_id"
              placeholder="请输入OfferID"
            ></el-input>
          </el-form-item>
          <el-form-item label="现网AppKey" prop="app_key">
            <el-input
              v-model="subForm.app_key"
              :placeholder="hasKey ? '留空表示不修改' : '请输入现网AppKey'"
            ></el-input>
            <div class="vp-key-tip" v-if="hasKey">当前已配置：{{maskKey}}</div>
          </el-form-item>
          <el-form-item label="代币兑换比例" prop="coin_rate">
            <el-input-number
              v-model="subForm.coin_rate"
              :min="1"
              :step="1"
              step-strictly
              controls-position="right"
            ></el-input-number>
            <span class="vp-rate-tip">1元 = coin_rate 个代币</span>
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
    let checkRequireWhenOn = (rule, value, callback) => {
      if (this.subForm.enabled === 1 && !value) {
        callback(new Error(`请输入${rule.text}`))
      } else {
        callback()
      }
    }
    // 已配置过密钥时允许留空（表示不修改）
    let checkAppKey = (rule, value, callback) => {
      if (this.subForm.enabled === 1 && !this.hasKey && !value) {
        callback(new Error('请输入现网AppKey'))
      } else {
        callback()
      }
    }
    return {
      hasKey: false,
      maskKey: '',
      subForm: {
        enabled: 0,
        offer_id: '',
        app_key: '',
        coin_rate: 1
      },
      subFormRules: {
        enabled: { required: true, type: 'number', message: '请选择是否启用虚拟支付', trigger: 'blur' },
        offer_id: { required: true, validator: checkRequireWhenOn, text: 'OfferID', trigger: 'blur' },
        app_key: { required: true, validator: checkAppKey, trigger: 'blur' },
        coin_rate: { required: true, type: 'number', min: 1, message: '请填写不小于1的整数比例', trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.virtualpayConfigInfo()
      if (code !== 200) return
      let config = (data && data.config) || {}
      this.hasKey = !!(config.app_key)
      this.maskKey = config.app_key || ''
      this.subForm.enabled = Number(config.enabled) === 1 ? 1 : 0
      this.subForm.offer_id = config.offer_id || ''
      this.subForm.coin_rate = Number(config.coin_rate) > 0 ? Number(config.coin_rate) : 1
    },
    submitFormInfo () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let { subForm } = this
          this.$api.system.virtualpayConfigUpdate(subForm).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.subForm.app_key = ''
              this.getFormInfo()
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
  .vp-tips {
    p {
      line-height: 24px;
    }
  }
  .vp-key-tip {
    font-size: 12px;
    color: #999;
    line-height: 20px;
  }
  .vp-rate-tip {
    margin-left: 10px;
    font-size: 12px;
    color: #999;
  }
}
</style>
