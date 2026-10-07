<!--
 * @Descripttion: 交易设置
 * @Author: xiao li
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: wen kun
 * @LastEditTime: 2024-01-03 10:32:06
-->
<template>
  <div class="lb-system-transaction">
    <top-nav />
    <div class="page-main">
      <lb-tips>
        系统默认用户订单处于待核销状态或完成订单后24小时内可申请退款
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="120px"
      >
        <el-form-item label="提现方式" prop="checkList">
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
        <el-form-item label="转账方式" prop="company_pay">
          <el-radio-group v-model="subForm.company_pay">
            <el-radio :label="1">企业转账</el-radio>
            <el-radio :label="2">商家转账</el-radio>
          </el-radio-group>
          <lb-tool-tips>用于微信线上提现</lb-tool-tips>
        </el-form-item>

        <el-form-item label="最低提现额度" prop="cash_mini">
          <el-input
            v-model="subForm.cash_mini"
            placeholder="请输入最低提现额度"
          ></el-input>
          <lb-tool-tips
            >提现者申请提现的最低提现额度</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="扣税百分点" prop="tax_point">
          <el-input v-model="subForm.tax_point" placeholder="请输入扣税百分点">
            <template slot="append">%</template>
          </el-input>
          <lb-tool-tips
            >设置扣税百分比之后，提现者申请提现需要扣除税费</lb-tool-tips
          >
        </el-form-item>

        <!-- <el-form-item label="向导邀约提成" prop="coach_tc_ratio">
          <el-input
            v-model.number="subForm.coach_tc_ratio"
            placeholder="请输入向导邀约提成"
          >
            <template slot="append">%</template>
          </el-input>
          <lb-tool-tips
            >向导接单邀约能得到的提成=订单实际金额*（设置的百分比）%</lb-tool-tips
          >
        </el-form-item> -->
        <el-form-item label="佣金结算方式" prop="cash_type">
          <el-radio-group v-model="subForm.cash_type">
            <el-radio :label="1"
              >{{ $t('action.attendantName') }}浮动分佣比例模式</el-radio
            >
            <el-radio :label="2"
              >{{ $t('action.attendantName') }}固定分佣比例模式</el-radio
            >
          </el-radio-group>
          <lb-tool-tips>
            1、{{ $t('action.attendantName') }}浮动分佣比例模式
            {{
              $t('action.attendantName')
            }}根据自己的接单时长和业绩等维度自动升级到配置的多比例浮动里，平台各角色的分佣模式为
            <p>
              {{ $t('action.attendantName') }}分佣=订单实际支付金额{{
                $t('action.attendantName')
              }}浮动比例%
            </p>
            <p>分销员分佣=订单实际支付金额分销员比例%</p>
            <p>平台佣金分佣=订单实际支付金额平台对代理商的抽成比例%</p>
            <p>渠道商分佣=订单实际支付金额渠道商抽成比例%</p>
            <p>
              代理商分佣=订单实际支付金额-{{
                $t('action.attendantName')
              }}分佣-分销员分佣-平台分佣-渠道商分佣
            </p>

            2、{{ $t('action.attendantName') }}固定分佣比例模式
            平台可以对每一个{{
              $t('action.attendantName')
            }}单独设置一个固定比例，对代理商单独设置一个固定比例
            <p>
              {{ $t('action.attendantName') }}分佣=订单实际支付金额{{
                $t('action.attendantName')
              }}固定百分比%
            </p>
            <p>分销员分佣=订单实际支付金额分销员比例%</p>
            <p>代理商分佣=订单实际支付金额代理商固定百分比%</p>
            <p>渠道商分佣=订单实际支付金额渠道商百分比%</p>
            <p>
              平台抽成=订单实际支付金额-{{
                $t('action.attendantName')
              }}分佣-分销员分佣-代理商分佣-渠道商分佣
            </p>
          </lb-tool-tips>
        </el-form-item>

        <el-form-item label="交易规则" prop="trading_rules">
          <lb-ueditor
            v-model="subForm.trading_rules"
            :destroy="true"
            :ueditorType="3"
          ></lb-ueditor>
        </el-form-item>
        <el-form-item label="退款须知" prop="refund_notice">
          <el-input
            type="textarea"
            :rows="20"
            v-model="subForm.refund_notice"
            maxlength="1000"
            show-word-limit
            resize="none"
            placeholder="请输入退款须知"
          ></el-input>
        </el-form-item>
        <el-form-item label="提现须知" prop="withdrawal_notice">
          <el-input
            type="textarea"
            :rows="20"
            v-model="subForm.withdrawal_notice"
            maxlength="1000"
            show-word-limit
            resize="none"
            placeholder="请输入提现须知"
          ></el-input>
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
import { mapState, mapMutations, mapActions } from 'vuex'
export default {
  data () {
    let checkList = (rule, value, callback) => {
      let { wechat_transfer: wechat = 0, alipay_transfer: alipay = 0, under_transfer: under } = this.subForm
      if (!wechat && !alipay && !under) {
        callback(new Error(`请选择提现方式`))
      } else {
        callback()
      }
    }
    return {
      checkList: [],
      authList: [{ title: '微信提现', key: 'wechat_transfer' }, { title: '支付宝提现', key: 'alipay_transfer' }, { title: '线下提现', key: 'under_transfer', tips: `用于用户、${this.$t('action.attendantName')}或代理商提现时选择到账方式` }],
      subForm: {
        cash_mini: '',
        tax_point: '',
        wechat_transfer: 0,
        alipay_transfer: 0,
        under_transfer: 0,
        company_pay: 1,
        service_cover_time: '',
        trading_rules: '',
        // coach_tc_ratio: '', // 陪玩邀约提成
        cash_type: 1,
        refund_notice: '',
        withdrawal_notice: ''
      },
      subFormRules: {
        cash_mini: { required: true, validator: this.$reg.isMoney, trigger: 'blur' },
        tax_point: { required: true, validator: this.$reg.isPercent, trigger: 'blur' },
        checkList: { required: true, validator: checkList, trigger: ['blur', 'change'] },
        company_pay: { required: true, type: 'number', message: '请选择转账方式', trigger: 'blur' },
        cash_type: { required: true, type: 'number', message: '请选择佣金结算方式', trigger: 'blur' },
        // coach_tc_ratio: { required: true, validator: this.$reg.isPercent, type: 'number', message: '请输入0至100的整数', trigger: 'blur' }
      },
      config: {}
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  created () {
    this.getConfigInfo()
  },
  methods: {
    ...mapMutations(['changeRoutesItem']),
    async getConfigInfo () {
      let { code, data } = await this.$api.system.configInfo()
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
      this.config = data
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
        await this.$api.system.configUpdate(subForm)
        if (this.config.cash_type !== subForm.cash_type) { // cash_type == 2时隐藏向导等级
          this.routesItem.routes.forEach(item => {
            if (item.path === '/technician') {
              let levelInd = item.meta.subNavName[0].url.findIndex(bitem => {
                return bitem.name === 'TechnicianLevel'
              })
              if (levelInd !== -1) {
                item.meta.subNavName[0].url.splice(levelInd, 1)
              } else {
                item.meta.subNavName[0].url.splice(1, 0, {
                  name: 'TechnicianLevel',
                  url: '/technician/level'
                })
              }

              // let setInd = item.meta.subNavName[0].url.findIndex(bitem => {
              //   return bitem.name === 'TechnicianSet'
              // })
              // if (setInd !== -1) {
              //   item.meta.subNavName[0].url.splice(setInd, 1)
              // } else {
              //   item.meta.subNavName[0].url.splice(1, 0, {
              //     name: 'TechnicianSet',
              //     url: '/technician/set'
              //   })
              // }
            }
          })
          this.changeRoutesItem({ key: 'routes', val: this.routesItem.routes })
        }
        this.getConfigInfo()
        this.$message.success(this.$t('tips.successSub'))
      }
    }
  }
}
</script>

<style lang="scss" scoped>
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
