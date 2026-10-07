<!--
 * @Descripttion: 板式设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-06-21 17:10:45
-->
<template>
  <div class="lb-plate-type">
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
        <el-form-item>
          <div class="flex">
            <div class="mr-lg">
              <img
                src="https://lbqny.migugu.com/admin/playwith/service/web-01.png"
                alt=""
                srcset=""
                style="width: 275px"
              />
            </div>
            <div>
              <img
                src="https://lbqny.migugu.com/admin/playwith/service/web-02.png"
                alt=""
                srcset=""
                style="width: 275px"
              />
            </div>
          </div>
        </el-form-item>
        <el-form-item>
          <el-radio-group
            v-model="subForm.index_type"
            style="width: 550px"
            class="flex-center"
          >
            <el-radio :label="1">样式一</el-radio>
            <el-radio :label="2" style="margin-left: 200px">样式二</el-radio>
          </el-radio-group>
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
export default {
  data () {
    return {
      subForm: {
        index_type: 1
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
      this.subForm.index_type = data.index_type
    },
    submitForm () {
      this.$api.system.configUpdate(this.subForm).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successSub'))
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-plate-type {
}
</style>
