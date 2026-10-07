<!--
 * @Descripttion: 群发短信
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:08:20
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
        <el-form-item label="阿里云ID" prop="short_id">
          <el-input
            v-model="subForm.short_id"
            placeholder="请输入阿里云ID"
          ></el-input>
          <lb-tool-tips
            >此处应填写自己的阿里云AccessKey ID, 登录自己的阿里云账号,
            鼠标放到自己的头像处, 会展示出一个列表,
            点击列表中的accesskeys就能查看到信息</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="阿里云密匙" prop="short_secret">
          <el-input
            v-model="subForm.short_secret"
            placeholder="请输入阿里云密匙"
          ></el-input>
          <lb-tool-tips
            >此处应填写自己的阿里云Access Key Secret, 登录自己的阿里云账号,
            鼠标放到自己的头像处, 会展示出一个列表,
            点击列表中的accesskeys就能查看到信息</lb-tool-tips
          >
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitFormInfo('subForm')" 
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
        short_id: '',
        short_secret: ''
      },
      subFormRules: {
        short_id: { required: true, validator: this.$reg.isNotNull, text: '阿里云ID', reg_type: 2, trigger: 'blur' },
        short_secret: { required: true, validator: this.$reg.isNotNull, text: '阿里云密匙', reg_type: 2, trigger: 'blur' }
      }
    }
  },
  async created () {
    await this.getDetail()
  },
  methods: {
    async getDetail () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    submitFormInfo (name) {
      this.$refs[name].validate(valid => {
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
.lb-group-news {
  width: 100%;
  .page-main {
    .el-input {
      width: 300px;
    }
  }
}
</style>
