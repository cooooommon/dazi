<!--
 * @Description: 续单设置
 * @Author: xiao li
 * @Date: 2023-02-20 09:51:55
 * @LastEditTime: 2023-07-17 16:46:57
 * @LastEditors: wen kun
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
        <el-form-item label="是否开启" prop="clock_cash_status">
          <el-radio-group v-model="subForm.clock_cash_status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
            <lb-tool-tips :padding="0"
              >开启之后，将以以下设置的续单返佣比例为准，否则将以{{
                $t('action.attendantName')
              }}实际的提成比例为准</lb-tool-tips
            >
          </el-radio-group>
        </el-form-item>
        <el-form-item
          label="续单返佣比例"
          prop="list"
          v-if="subForm.clock_cash_status === 1"
        >
          <div
            class="flex-y-center"
            :class="[{ 'mt-md': index !== 0 }]"
            v-for="(item, index) in subForm.list"
            :key="index"
          >
            <div style="width: 150px">
              {{
                index * 1 + 1 === 1 ? `首次` : `第${index * 1 + 1}次`
              }}续单返佣比例
            </div>
            <el-input v-model="item.balance" placeholder="请输入返佣比例">
              <template slot="append">%</template>
            </el-input>
            <lb-tool-tips v-if="index === 0"
              >若用户实际下单次数超过了后台设置的次数返佣比例，则超出的订单以最后一次设置的比例为准
              <div class="mt-sm">
                例如：后台设置了首次续单返佣70%，第二次续单返佣比例80%，但是未设置第三次续单的返佣比例
              </div>
              <div class="mt-sm">
                若用户已续单2次，第三次续单的返佣比例应按照80%返给{{
                  $t('action.attendantName')
                }}
              </div>
            </lb-tool-tips>
            <el-button
              type="danger"
              size="mini"
              plain
              style="margin-left: 15px"
              @click="toAddDel(1, index)"
              v-if="index !== 0"
              >{{ $t('action.delete') }}</el-button
            >
            <el-button
              type="primary"
              size="mini"
              plain
              style="margin-left: 15px"
              @click="toAddDel(2, index)"
              v-if="index === subForm.list.length - 1"
              >{{ $t('action.add') }}</el-button
            >
          </div>
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
    return {
      subForm: {
        clock_cash_status: 1,
        list: []
      },
      subFormRules: {
        clock_cash_status: { required: true, type: 'number', message: '请选择', trigger: 'blur' }
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
      let { code, data } = await this.$api.service.addClockInfo()
      if (code !== 200) return
      data.list = data.list.length === 0 ? [{ balance: '' }] : data.list
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    toAddDel (key = 1, index) {
      if (key === 1) {
        this.subForm.list.splice(index, 1)
      } else {
        let ind = index === 0 ? 0 : index - 1
        let { balance } = this.subForm.list[ind]
        let reg = /^(?:0|[1-9][0-9]?|100)$/
        let text = ind === 0 ? '首次' : `第${ind * 1 + 1}次`
        let msg = balance === '' ? '请输入返佣比例' : '请输入0至100的整数'
        if (balance === '' || !reg.test(balance)) {
          this.$message.error(`${text}续单返佣比例：${msg}`)
          return
        }
        this.subForm.list.push({ balance: '' })
      }
    },
    async submitForm () {
      let flag = false
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          flag = true
        }
      })
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      let reg = /^(?:0|[1-9][0-9]?|100)$/
      if (subForm.clock_cash_status === 1) {
        for (let key in subForm.list) {
          let index = key * 1 + 1
          let { balance } = subForm.list[key]
          if (balance === '' || !reg.test(balance)) {
            let text = index === 1 ? '首次' : `第${index}次`
            let msg = balance === '' ? '请输入返佣比例' : '请输入0至100的整数'
            this.$message.error(`${text}续单返佣比例：${msg}`)
            return
          }
        }
      } else {
        subForm.list = []
      }
      subForm.list.map((item, index) => {
        item.times = index * 1 + 1
      })
      if (flag) {
        await this.$api.service.addClockUpdate(subForm)
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
