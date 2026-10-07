<!--
 * @Descripttion: 编辑文章
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:07:20
-->
<template>
  <div class="lb-market-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="120px"
      >
        <el-form-item label="文章标题" prop="title">
          <el-input
            v-model="subForm.title"
            maxlength="20"
            show-word-limit
            placeholder="请输入文章标题"
          ></el-input>
        </el-form-item>
        <el-form-item label="文章内容" prop="text">
          <lb-ueditor v-model="subForm.text" :destroy="true"></lb-ueditor>
        </el-form-item>
        <el-form-item label="是否关联表单" prop="is_form">
          <el-radio-group v-model="subForm.is_form">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="表单内容" prop="field" v-if="subForm.is_form">
          <el-select
            v-model="subForm.field"
            multiple
            collapse-tags
            placeholder="请选择"
          >
            <el-option
              v-for="item in base_enroll"
              :key="item.id"
              :label="item.title"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="排序值" prop="top">
          <el-input-number
            class="lb-input-number"
            :min="0"
            :controls="false"
            v-model="subForm.top"
            placeholder="请输入排序值"
          ></el-input-number>
          <lb-tool-tips>值越大, 排序越靠前</lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    let checkEnroll = (rule, value, callback) => {
      let { is_form: form } = this.subForm
      if (form === 1 && value.length === 0) {
        callback(new Error('请选择表单内容'))
      } else {
        callback()
      }
    }
    return {
      navTitle: '',
      base_enroll: [],
      subForm: {
        id: 0,
        title: '',
        text: '',
        is_form: 0,
        field: [],
        top: 0
      },
      subFormRules: {
        title: { required: true, validator: this.$reg.isNotNull, text: '文章标题', reg_type: 2, trigger: 'blur' },
        text: { required: true, type: 'string', message: '请输入文章内容', trigger: 'blur' },
        is_form: { required: true, type: 'number', message: '请选择是否关联表单', trigger: 'blur' },
        field: { required: true, validator: checkEnroll, trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' }
      }
    }
  },
  async created () {
    let { id = 0 } = this.$route.query
    this.subForm.id = id
    this.navTitle = this.$t(id ? 'menu.MarketArticleEdit' : 'menu.MarketArticleAdd')
    await this.getBaseInfo()
    if (!id) return
    this.getDetail()
  },
  methods: {
    async getBaseInfo () {
      let { code, data } = await this.$api.market.fieldSelect()
      if (code !== 200) return
      this.base_enroll = data
    },
    /**
     * @name: 详情
     * @param {*}
     */
    async getDetail () {
      let { id } = this.subForm
      let { code, data } = await this.$api.market.articleInfo({ id })
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    /**
     * @name: 新增/编辑
     * @param {*}
     */
    submitForm () {
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          let methodModel = subForm.id ? 'articleUpdate' : 'articleAdd'
          this.$api.market[methodModel](subForm).then((res) => {
            if (res.code === 200) {
              this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
              this.$router.back(-1)
            }
          })
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-market-edit {
  .page-main {
    .el-form-item {
      margin-bottom: 24px;
    }
    .last-form-item {
      margin-top: 30px;
    }
    .item-tips {
      margin-left: 120px;
      margin-bottom: 24px;
      color: #999999;
    }
    .el-input,
    .el-input-number,
    .el-cascader,
    .el-select {
      width: 300px;
    }
    .el-input-number.mini {
      width: 100px;
    }
    .el-textarea {
      width: 600px;
    }
  }
}
</style>
