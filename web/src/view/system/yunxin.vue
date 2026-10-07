<!--
 * @Descripttion: 云信
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @LastEditTime: 2023-09-27 10:24:12
-->
<template>
  <div class="lb-group-news">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
      >
        <el-form-item label="云信appid" prop="winnerlook_appid">
          <el-input
            v-model="subForm.winnerlook_appid"
            placeholder="请输入云信appid"
          ></el-input>
        </el-form-item>
        <el-form-item label="云信token" prop="winnerlook_token">
          <el-input
            v-model="subForm.winnerlook_token"
            placeholder="请输入云信token"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <lb-button
            type="primary"
            @click="submitFormInfo('subForm')"
            v-preventReClick
            >{{ $t('action.submit') }}</lb-button
          >
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
        winnerlook_appid: '',
        winnerlook_token: ''
      },
      subFormRules: {
        winnerlook_appid: { required: true, validator: this.$reg.isNotNull, text: '云信appid', reg_type: 2, trigger: 'blur' },
        winnerlook_token: { required: true, validator: this.$reg.isNotNull, text: '云信token', reg_type: 2, trigger: 'blur' }
      }
    }
  },
  async created () {
    await this.getDetail()
  },
  methods: {
    async getDetail () {
      let { code, data } = await this.$api.system.virtualConfigInfo()
      if (code !== 200) return
      for (let i in this.subForm) {
        this.subForm[i] = data[i]
      }
    },
    submitFormInfo (name) {
      let flag = true
      this.$refs[name].validate(valid => {
        if (!valid) flag = false
      })
      if (!flag) return
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      this.$api.system.virtualConfigUpdate(subForm).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successSub'))
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-group-news {
  width: 100%;
  .page-main {
    .el-input {
      width: 300px;
    }
  }
}
</style>
