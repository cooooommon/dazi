<!--
 * @Description: 问题反馈详情
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-03-13 19:06:43
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
        <el-form-item label="反馈类型：">
          <div>{{ dataInfo.type_name }}</div>
        </el-form-item>
        <el-form-item label="反馈人：">
          <div>{{ dataInfo.coach_name }}</div>
        </el-form-item>
        <el-form-item label="反馈人手机号：">
          <div>{{ dataInfo.mobile || '无' }}</div>
        </el-form-item>
        <el-form-item label="订单编号：">
          <div>{{ dataInfo.order_code || '无' }}</div>
        </el-form-item>
        <el-form-item label="反馈内容：">
          <div>{{ dataInfo.content || '无' }}</div>
          <div
            class="flex pt-md"
            v-if="dataInfo.images && dataInfo.images.length"
          >
            <lb-cover
              :fileList="dataInfo.images"
              :isToDel="false"
              fileType="image"
              type="more"
              size="big"
              :fileSize="1"
            ></lb-cover>
          </div>
          <div class="pt-md" v-if="dataInfo.video_url">
            <video
              controls
              width="500"
              height="300"
              :src="dataInfo.video_url"
            ></video>
          </div>
        </el-form-item>
        <el-form-item label="回复：" v-if="dataInfo.status == 1">
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
        <el-form-item label="回复：" v-else>
          <div>{{ subForm.reply_content || '无' }}</div>
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
    this.getDetail(id)
  },
  methods: {
    async getDetail (id) {
      let { code, data } = await this.$api.system.feedbackInfo({ id })
      if (code !== 200) return
      data.is_balance = data.balance * 1 > 0 ? 1 : 0
      let { coach_refund_text: text } = data
      data.coach_refund_text = text && text.length > 0 ? text.replace(/\n/g, '<br>') : '没有填写原因哦'
      this.subForm.reply_content = data.reply_content
      this.subForm.id = data.id
      let images = []
      if (data.images && data.images.length) {
        data.images.forEach(item => {
          images.push({ url: item })
        })
      }
      this.dataInfo = data
      this.dataInfo.images = images
    },
    submitForm () {
      let { subForm } = this
      this.$api.system.feedbackHandle(subForm).then(res => {
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
