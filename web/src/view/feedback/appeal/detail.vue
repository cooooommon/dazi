<!--
 * @Description: 差评申诉详情
 * @Author: wen kun
 * @Date: 2022-09-28 11:59:21
 * @LastEditTime: 2023-03-13 19:06:48
 * @LastEditors: xiao li
-->

<template>
  <div class="lb-shop-order-edit">
    <top-nav :isBack="true" />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        label-width="130px"
        size="mini"
      >
        <el-form-item label="ID：">
          <div>{{ dataInfo.id }}</div>
        </el-form-item>
        <el-form-item label="反馈人：">
          <div>{{ dataInfo.coach_name }}</div>
        </el-form-item>
        <el-form-item label="反馈人手机号：">
          <div>{{ dataInfo.mobile }}</div>
        </el-form-item>
        <el-form-item label="订单编号：">
          <div>{{ dataInfo.order_code }}</div>
        </el-form-item>
        <el-form-item label="申述内容：">
          <div>
            {{ dataInfo.content }}
          </div>
        </el-form-item>
        <el-form-item label="处理结果：" v-if="dataInfo.status == 1">
          <div>
            <el-input
              type="textarea"
              :rows="6"
              placeholder="请输入内容"
              v-model="subForm.reply_content"
            >
            </el-input>
          </div>
        </el-form-item>
        <el-form-item label="处理结果：" v-else>
          <div>
            {{ subForm.reply_content || '无' }}
          </div>
        </el-form-item>
        <el-form-item>
          <lb-button
            type="primary"
            @click="submitForm"
            v-show="dataInfo.status == 1"
            v-preventReClick
            >{{ $t('action.submit') }}</lb-button
          >
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  data () {
    return {
      loading: false,
      subForm: {
        reply_content: ''
      },
      dataInfo: {}
    }
  },
  created () {
    let { id } = this.$route.query
    if (id) {
      this.getDetail(id)
    }
  },
  methods: {
    async getDetail (id) {
      let { code, data } = await this.$api.system.appealInfo({ id })
      if (code !== 200) return
      this.subForm.reply_content = data.reply_content
      this.subForm.id = data.id
      this.dataInfo = data
    },
    submitForm () {
      let { subForm } = this
      this.$api.system.appealHandle(subForm).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successSub'))
          this.goBack()
        }
      })
    },
    goBack () {
      this.$route.meta.refresh = true
      this.$router.back(-1)
    }
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : type === 3 ? moment(val * 1000).format('YYYY-MM-DD HH:mm') : type === 4 ? moment(val * 1000).format('HH:mm') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-shop-order-edit {
  .order-img {
    width: 120px;
    height: 120px;
  }
  .el-textarea {
    width: 600px;
  }
}
</style>
