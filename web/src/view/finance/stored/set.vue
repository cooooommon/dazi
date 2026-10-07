<!--
 * @Description: 储值设置
 * @Author: xiao li
 * @Date: 2023-02-20 09:51:55
 * @LastEditTime: 2023-04-06 19:04:28
 * @LastEditors: xiao li
-->

<template>
  <div class="lb-service-bell-set">
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
        <el-form-item label="储值充值功能" prop="recharge_status">
          <el-radio-group v-model="subForm.recharge_status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips>开启之后，手机用户端将会展示储值充值入口 </lb-tool-tips>
        </el-form-item>
        <el-form-item label="返佣形式" prop="checkList">
          <el-checkbox-group v-model="checkList" @change="changeCheckBox">
            <div
              v-for="(item, index) in authList"
              :key="index"
              :style="{
                display: 'inline-block',
                marginLeft: index === 0 ? 0 : '15px'
              }"
            >
              <el-checkbox :label="item.title"></el-checkbox>
              <lb-tool-tips v-if="item.tips">{{ item.tips }}</lb-tool-tips>
            </div>
          </el-checkbox-group>
        </el-form-item>
        <el-form-item
          label="返佣比例"
          prop="balance_balance"
          v-if="subForm.balance_cash === 1"
        >
          <el-input
            v-model="subForm.balance_balance"
            placeholder="请输入返佣比例"
          >
            <template slot="append">%</template>
          </el-input>
          <lb-tool-tips
            >返佣现金的比例按照储值实际支付金额的百分比返佣
          </lb-tool-tips>
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
import { mapState } from 'vuex'
export default {
  data () {
    let checkList = (rule, value, callback) => {
      let { balance_cash: cash = 0, balance_integral: integral = 0 } = this.subForm
      if (!cash && !integral) {
        callback(new Error(`请选择返佣形式`))
      } else {
        callback()
      }
    }
    let checkBalance = (rule, value, callback) => {
      let { balance_cash: cash = 0, balance_balance: balance = 0 } = this.subForm
      let reg = /^(?:[1-9][0-9]?|100)$/
      if (cash === 1 && (!balance || !reg.test(balance))) {
        callback(new Error(!balance ? `请输入返佣比例` : `请输入1至100的整数`))
      } else {
        callback()
      }
    }
    return {
      checkList: [],
      authList: [{ title: '返现金', key: 'balance_cash' }, { title: '返积分', key: 'balance_integral' }],
      subForm: {
        recharge_status: 1,
        balance_cash: 1,
        balance_integral: 1,
        balance_balance: ''
      },
      subFormRules: {
        recharge_status: { required: true, type: 'number', message: '请选择是否开启储值充值功能', trigger: 'blur' },
        checkList: { required: true, validator: checkList, trigger: ['blur', 'change'] },
        balance_balance: { required: true, validator: checkBalance, trigger: ['blur', 'change'] }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfoSchedule()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      let checkItem = []
      this.authList.map(item => {
        if (data[item.key] === 1) {
          checkItem.push(item.title)
        }
      })
      this.checkList = checkItem
    },
    changeCheckBox (e) {
      this.authList.map(item => {
        this.subForm[item.key] = e.includes(item.title) ? 1 : 0
      })
    },
    async submitForm () {
      let flag = false
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          flag = true
        }
      })
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      if (flag) {
        await this.$api.system.configUpdateSchedule(subForm)
        this.$message.success(this.$t('tips.successSub'))
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-service-bell-set {
  width: 100%;
  .config-form {
    .el-input,
    .el-input-number {
      width: 300px;
    }
  }
}
</style>
