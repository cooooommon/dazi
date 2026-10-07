<!--
 * @Descripttion: 群发短信
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-05-12 13:54:47
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
        <el-form-item label="容联七陌ID" prop="moor_id">
          <el-input
            v-model="subForm.moor_id"
            placeholder="请输入容联七陌ID"
          ></el-input>
        </el-form-item>
        <el-form-item label="容联七陌密匙" prop="moor_secret">
          <el-input
            v-model="subForm.moor_secret"
            placeholder="请输入容联七陌密匙"
          ></el-input>
        </el-form-item>
        <el-form-item label="容联七陌地址" prop="moor_url">
          <el-input
            v-model="subForm.moor_url"
            placeholder="请输入容联七陌密匙"
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
        moor_id: '',
        moor_secret: '',
        moor_url: ''
      },
      subFormRules: {
        moor_id: { required: true, validator: this.$reg.isNotNull, text: '容联七陌ID', reg_type: 2, trigger: 'blur' },
        moor_secret: { required: true, validator: this.$reg.isNotNull, text: '容联七陌密匙', reg_type: 2, trigger: 'blur' },
        moor_url: { required: true, validator: this.$reg.isNotNull, text: '容联七陌地址', reg_type: 2, trigger: 'blur' }
      }
    }
  },
  async created () {
    await this.getDetail()
  },
  methods: {
    async getDetail () {
      let { code, data } = await this.$api.system.reminderConfigInfo()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    submitFormInfo (name) {
      this.$refs[name].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          this.$api.system.reminderConfigUpdate(subForm).then(res => {
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
.lb-group-news {
  width: 100%;
  .page-main {
    .el-input {
      width: 300px;
    }
  }
}
</style>
