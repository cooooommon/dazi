<!--
 * @Descripttion: 出行设置
 * @Author: xiao li
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: wen kun
 * @LastEditTime: 2024-05-06 10:34:31
-->
<template>
  <div class="lb-system-transaction">
    <top-nav />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="160px"
      >
        <el-form-item label="渠道码绑定方式：" prop="channel_bind_type">
          <el-radio-group v-model="subForm.channel_bind_type">
            <el-radio :label="2">时效期内扫码不换绑</el-radio>
            <el-radio :label="1">时效期内扫码可换绑</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >时效期内扫码不换绑：在渠道码时效性内，扫其他渠道商的二维码下单，不换绑新的渠道商，佣金依然返给之前的渠道商<br />
            时效期内扫码可换绑：在时效期内如果不扫码则返给原渠道商，扫码后可换绑新的渠道商，佣金发给新的渠道商</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="绑定渠道商时效" prop="channel_bind_time">
          <el-input v-model="subForm.channel_bind_time" placeholder="请输入渠道商时效">
            <template slot="append">小时</template>
          </el-input>
          <lb-tool-tips
            >当用户扫描渠道商二维码后将与该渠道商绑定关系，在此时效内下单，该渠道商均享受佣金提成</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="">
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
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
        channel_bind_type: 1,
        channel_bind_time: ''
      },
      subFormRules: {
        channel_bind_type: {
          required: true,
          type: 'number',
          message: '请选择',
          trigger: 'blur'
        },
        channel_bind_time: { required: true, validator: this.$reg.isNum, reg_type: 2, text: '渠道商时效性', trigger: 'blur' }
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
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let { subForm } = this
          this.$api.system.configUpdate(subForm).then((res) => {
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

<style lang="scss" >
.lb-system-transaction {
  width: 100%;
  .page-main {
    padding: 20px;
    .el-form {
      width: 100%;
      .el-form-item {
        margin-bottom: 24px;
        .el-select,
        .el-input-number,
        .el-input {
          width: 300px;
        }
      }
      .last-form-item {
        margin-top: 30px;
      }
      .item-tips {
        margin-left: 120px;
        margin-bottom: 24px;
        color: #999999;
      }
    }
  }
}
</style>
