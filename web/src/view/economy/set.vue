<!--
 * @Descripttion: 出行设置
 * @Author: xiao li
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-25 16:44:00
-->
<template>
  <div class="lb-system-transaction">
    <top-nav />
    <div class="page-main">
      <lb-tips
        >经纪人的佣金由该笔订单服务{{ $t('action.attendantName') }}和该{{
          $t('action.attendantName')
        }}绑定的上级代理承担。
        <lb-tool-tips :padding="0"
          >例如：用户A下单实际付款除车费外为100元，经纪人B的返佣比例为10%，则经纪人B应得提成100*10%=10元
          <p>
            1、承担比例累计等于100%，平台不承担例如：{{
              $t('action.attendantName')
            }}承担30%，{{ $t('action.attendantName') }}所属代理承担70% 则：{{
              $t('action.attendantName')
            }}应承担10*30%=3元，{{
              $t('action.attendantName')
            }}所属代理应承担10*70%=7元
          </p>
          <p>
            2、承担比例累计小于100%，平台需承担平台需承担金额=10*(100-{{
              $t('action.attendantName')
            }}应承担比例 - 所属代理商应承担比例)%
          </p>
          <p>
            例如：{{ $t('action.attendantName') }}承担30%，{{
              $t('action.attendantName')
            }}所属代理承担0% 则：{{
              $t('action.attendantName')
            }}应承担10*30%=3元，平台应承担10*(100-30-0)%=7元
          </p>
          <p>
            {{ $t('action.attendantName') }}承担30%，{{
              $t('action.attendantName')
            }}所属代理承担50% 则：{{
              $t('action.attendantName')
            }}应承担10*30%=3元，{{
              $t('action.attendantName')
            }}所属代理应承担10*50%=5元，平台应承担10*(100-30-50)%=2元
          </p>
          <p class="pt-md">温馨提示：</p>
          <p class="c-warning">
            1)、若没有所属代理，则计算规则中所属代理商应承担的金额应由平台所出
          </p>
          <p class="c-warning">
            2)、若有所属代理且该代理在本订单中没有获得任何分成，则代理商无需承担
          </p>
          <p class="c-warning">
            3)、若有所属代理且该代理在本订单中所得分成小于应承担金额，
            则该代理需将所得分成全部给经纪人，因此经纪人实际所得金额小于应得金额
          </p>
        </lb-tool-tips>
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="220px"
      >
        <el-form-item
          :label="`${$t('action.attendantName')}经纪人审核`"
          prop="broker_check"
        >
          <el-radio-group v-model="subForm.broker_check">
            <el-radio :label="1">开启</el-radio>
            <el-radio :label="0">关闭</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >关闭之后，用户端无分销合伙人的申请入口，只能后台添加用户成为经纪人之后，<br />
            该经纪人角色用户能在手机端看到入口。</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="经纪人分销返佣" prop="broker_balance">
          <el-input
            v-model="subForm.broker_balance"
            placeholder="请输入经纪人分销返佣"
          >
            <template slot="append">%</template>
          </el-input>
          <lb-tool-tips
            >经纪人邀请用户成为{{ $t('action.attendantName') }}，{{
              $t('action.attendantName')
            }}产生订单后获得的每笔订单佣金</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          :label="`${$t('action.attendantName')}承担`"
          prop="broker_coach_balance"
        >
          <el-input
            v-model="subForm.broker_coach_balance"
            :placeholder="`请输入${$t('action.attendantName')}承担比例`"
          >
            <template slot="append">%</template>
          </el-input>
          <lb-tool-tips
            >{{ $t('action.attendantName') }}和{{
              $t('action.attendantName')
            }}所属代理商承担比例总计不能大于100%</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          :label="`${this.$t('action.attendantName')}所属代理商承担`"
          prop="broker_agent_balance"
        >
          <el-input
            v-model="subForm.broker_agent_balance"
            :placeholder="`请输入${this.$t(
              'action.attendantName'
            )}所属代理商承担比例`"
          >
            <template slot="append">%</template>
          </el-input>
          <lb-tool-tips
            >{{ $t('action.attendantName') }}和{{
              $t('action.attendantName')
            }}所属代理商承担比例总计不能大于100%</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="邀请用户推广海报背景图" prop="broker_poster">
          <lb-cover
            :fileList="subForm.broker_poster"
            @selectedFiles="getCover($event, 'broker_poster')"
          ></lb-cover>
          <lb-tool-tips
            >图片建议尺寸：750 * 1350<br />
            由于页面生成的二维码位置是固定的，所以设计海报时注意将中间的二维码位置留出来<br />
            海报背景图中不要出现诱导用户分享以及传播外链内容的<br />
            包括但不限于：以金钱奖励、实物奖励、虚拟奖品（包括但不限于红包、优惠券、代金券、积分、话费、流量）；<br />声称分享可获得返佣等
          </lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button type="danger" plain @click="toReset">{{
            $t('action.defaultSet')
          }}</lb-button>
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
        broker_check: 1,
        broker_balance: 0,
        broker_coach_balance: 0,
        broker_agent_balance: 0,
        broker_poster: ''
      },
      subFormRules: {
        broker_check: {
          required: true,
          type: 'number',
          message: '请选择是否开启',
          trigger: 'blur'
        },
        broker_balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur', decimal: 1 },
        broker_coach_balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur', decimal: 0 },
        broker_agent_balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur', decimal: 0 },
        broker_poster: { required: true, type: 'array', message: '请选择邀请用户推广海报背景图', trigger: 'blur' }
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
      data.broker_poster = [{ url: data.broker_poster }]
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    getCover (img, key) {
      console.log(img)
      this.subForm[key] = img
    },
    toReset () {
      this.subForm.broker_poster = [{ url: 'https://lbqny.migugu.com/admin/peiwan/invite-poster.png' }]
      this.submitForm()
    },
    submitForm () {
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          if (subForm.broker_coach_balance * 1 + subForm.broker_agent_balance * 1 > 100) {
            this.$message.error(`${this.$t('action.attendantName')}和${this.$t('action.attendantName')}所属代理商承担比例总计不能大于100%`)
            return
          }
          if (typeof subForm.broker_poster !== 'string') {
            subForm.broker_poster = subForm.broker_poster[0].url
          }
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
