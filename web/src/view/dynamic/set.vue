<!--
 * @Description: 动态设置
 * @Author: xiao li
 * @Date: 2023-01-28 16:54:09
 * @LastEditTime: 2023-03-13 19:06:34
 * @LastEditors: xiao li
-->

<template>
  <div class="lb-dynamic-set">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="150px"
        class="submit-form"
      >
        <el-form-item label="是否开启动态发布" prop="dynamic_status">
          <el-radio-group v-model="subForm.dynamic_status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips>关闭后，手机端将不再展示动态发布相关内容</lb-tool-tips>
        </el-form-item>
        <div v-if="subForm.dynamic_status">
          <el-form-item label="动态审核方式" prop="dynamic_check">
            <el-radio-group v-model="subForm.dynamic_check">
              <el-radio :label="1">人工审核</el-radio>
              <el-radio :label="2">自动审核</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="评论审核方式" prop="dynamic_comment_check">
            <el-radio-group v-model="subForm.dynamic_comment_check">
              <el-radio :label="1">人工审核</el-radio>
              <el-radio :label="2">自动审核</el-radio>
            </el-radio-group>
          </el-form-item>
        </div>
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
export default {
  data () {
    return {
      subForm: {
        dynamic_status: 1,
        dynamic_check: 1,
        dynamic_comment_check: 1
      },
      subFormRules: {
        dynamic_status: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        dynamic_check: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        dynamic_comment_check: { required: true, type: 'number', message: '请选择', trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfoSchedule()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          this.$api.system.configUpdateSchedule(subForm).then(res => {
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
.lb-dynamic-set {
  width: 100%;
  .submit-form {
    .el-input {
      width: 300px;
    }
  }
}
</style>
