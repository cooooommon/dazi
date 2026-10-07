<!--
 * @Descripttion: 出行设置
 * @Author: xiao li
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-25 17:37:24
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
        label-width="220px"
      >
        <el-form-item label="是否开启公交/地铁出行方式：" prop="is_bus">
          <el-radio-group v-model="subForm.is_bus">
            <el-radio :label="1">开启</el-radio>
            <el-radio :label="2">关闭</el-radio>
          </el-radio-group>
          <lb-tool-tips>关闭之后，下单页面不可以选择公交/地铁方式</lb-tool-tips>
        </el-form-item>
        <el-form-item
          label="可出行时间："
          prop="time"
          v-if="subForm.is_bus === 1"
        >
          <el-time-select
            placeholder="开始时间"
            v-model="subForm.bus_start_time"
            :picker-options="{
              start: '00:00',
              step: '00:01',
              end: '24:00',
              maxTime: subForm.bus_end_time ? subForm.bus_end_time : ''
            }"
            style="width: 150px"
          ></el-time-select>
          <div>-</div>
          <el-time-select
            placeholder="结束时间"
            v-model="subForm.bus_end_time"
            :picker-options="{
              start: '00:00',
              step: '00:01',
              end: '24:00',
              minTime: subForm.bus_start_time
            }"
            style="width: 150px"
          ></el-time-select>
          <lb-tool-tips
            >可支持公交/地铁出行方式的时间段，例如设置8:00-22:00则表示这个时间段内下单用户是可以选择{{
              $t('action.attendantName')
            }}坐公交/地铁到达目的地</lb-tool-tips
          >
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
    let validateTime = (rule, value, callback) => {
      let { is_bus: bus, bus_start_time: start, bus_end_time: end } = this.subForm
      if (bus === 1 && (!start || !end)) {
        callback(new Error(!start ? `请选择开始时间` : `请选择结束时间`))
      } else {
        callback()
      }
    }
    return {
      subForm: {
        is_bus: 1, // 是否支持公交/地铁
        bus_start_time: '', // 开始时间
        bus_end_time: '' // 结束时间
      },
      subFormRules: {
        is_bus: {
          required: true,
          type: 'number',
          message: '请选择是否开启',
          trigger: 'blur'
        },
        time: { required: true, validator: validateTime, trigger: 'blur' }
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
