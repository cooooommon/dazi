<!--
 * @Descripttion: 邀约设置
 * @Author: wen kun
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: wen kun
 * @LastEditTime: 2024-04-01 18:03:24
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
        label-width="150px"
      >
        <el-form-item label="邀约人工审核" prop="is_demand_order_check">
          <el-radio-group v-model="subForm.is_demand_order_check">
            <el-radio :label="1">开启</el-radio>
            <el-radio :label="0">关闭</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >邀约审核开启之后，用户在移动端发布的邀约内容需要平台人工审核之后才可以通过发布展示</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="邀约分销员比例" prop="demand_user_balance">
          <el-input-number
            class="lb-input-number"
            :min="0"
            :max="100"
            :precision="0"
            :controls="false"
            v-model="subForm.demand_user_balance"
            placeholder="请输入邀约分销员比例"
          ></el-input-number>
          <div>%</div>
          <lb-tool-tips
            >用户发布邀约需求，如果成单后，需要给上级分销员返佣</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="价格结算" prop="demand_price_check">
          <el-radio-group v-model="subForm.demand_price_check">
            <el-radio :label="1">自定义输入</el-radio>
            <el-radio :label="2">根据服务类型单价计算</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >1、自定义输入发单价格，即选择系统设置的服务类型，<br />用户自己输入价格下单，一次只能邀约1人，只能发布一个订单；
            <p>
              2、根据服务类型单价计算，即系统设置好每个服务类型的单价，<br />根据单价和邀约的人数算出总价下单，一次可以邀约多个人。
            </p>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm">{{
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
        is_demand_order_check: 1,
        demand_user_balance: 0,
        demand_price_check: 1
      },
      subFormRules: {
        is_demand_order_check: {
          required: true,
          type: 'number',
          message: '请选择是否开启',
          trigger: 'blur'
        },
        demand_user_balance: { required: true, type: 'number', message: '请输入邀约分销员比例', trigger: 'blur' },
        demand_price_check: { required: true, type: 'number', message: '请选择价格结算', trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.invitation.demandSetting()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    submitForm () {
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let { subForm } = this
          this.$api.invitation.demandSettingPost(subForm).then((res) => {
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
