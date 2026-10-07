<template>
  <div class="lb-system-notice">
    <top-nav />
    <div class="page-main">
      <el-form @submit.native.prevent label-width="220px">
        <el-form-item required label="请选择通知类型">
          <el-select v-model="notice_switch" placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
        </el-form-item>
      </el-form>

      <el-form
        v-show="notice_switch === 1"
        @submit.native.prevent
        :model="publicForm"
        :rules="publicFormRules"
        ref="publicForm"
        label-width="220px"
        class="baseform"
      >
        <el-form-item label="AppID" prop="gzh_appid">
          <el-input
            v-model="publicForm.gzh_appid"
            placeholder="请输入AppID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号的AppID，注意不是小程序的AppID</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="下单通知模板ID" prop="order_tmp_id">
          <el-input
            v-model="publicForm.order_tmp_id"
            placeholder="请输入下单通知模板ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的下单通知模板消息ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="取消预约通知模板ID" prop="cancel_tmp_id">
          <el-input
            v-model="publicForm.cancel_tmp_id"
            placeholder="请输入取消预约通知模板ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的退款通知模板消息ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          :label="`${$t('action.attendantName')}信息编辑通知模板ID`"
          prop="coachupdate_tmp_id"
        >
          <el-input
            v-model="publicForm.coachupdate_tmp_id"
            :placeholder="`请输入${$t(
              'action.attendantName'
            )}信息编辑通知模板ID`"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的{{
              $t('action.attendantName')
            }}信息编辑通知模板ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          :label="`${$t('action.attendantName')}求救通知模板ID`"
          prop="help_tmpl_id"
        >
          <el-input
            v-model="publicForm.help_tmpl_id"
            :placeholder="`请输入${$t('action.attendantName')}求救通知模板ID`"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的{{
              $t('action.attendantName')
            }}求救通知模板ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="订单服务通知模板ID" prop="order_service_tmpl_id">
          <el-input
            v-model="publicForm.order_service_tmpl_id"
            placeholder="请输入订单服务通知模板ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的订单服务通知模板ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="报名通知模版ID" prop="apply_tmp_id">
          <el-input
            v-model="publicForm.apply_tmp_id"
            placeholder="请输入报名通知模版ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的报名通知模版ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="报名通过通知模版ID" prop="apply_ok_tmp_id">
          <el-input
            v-model="publicForm.apply_ok_tmp_id"
            placeholder="请输入报名通过通知模版ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的报名通过通知模版ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="报名拒绝通知模版ID" prop="apply_no_tmp_id">
          <el-input
            v-model="publicForm.apply_no_tmp_id"
            placeholder="请输入报名拒绝通知模版ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的报名拒绝通知模版ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          label="用户端退款成功通知模版ID"
          prop="refund_pass_tmpl_id"
        >
          <el-input
            v-model="publicForm.refund_pass_tmpl_id"
            placeholder="请输入用户端退款成功通知模版ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的用户端退款成功通知模版ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          label="用户端退款失败通知模版ID"
          prop="refund_nopass_tmpl_id"
        >
          <el-input
            v-model="publicForm.refund_nopass_tmpl_id"
            placeholder="请输入用户端退款失败通知模版ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的用户端退款失败通知模版ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          label="抢单提醒通知模版ID"
          prop="demand_coach_rob_tmpl_id"
        >
          <el-input
            v-model="publicForm.demand_coach_rob_tmpl_id"
            placeholder="请输入抢单提醒通知模版ID"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的抢单提醒通知模版ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          :label="`${$t('action.attendantName')}端退款通知模版ID`"
          prop="refund_apply_coach_tmpl_id"
        >
          <el-input
            v-model="publicForm.refund_apply_coach_tmpl_id"
            :placeholder="`请输入${$t('action.attendantName')}端退款通知模版ID`"
          ></el-input>
          <lb-tool-tips
            >与这个小程序关联的微信公众号里面添加的{{
              $t('action.attendantName')
            }}端退款通知模版ID，与服务通知的模板ID类似</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="支付成功通知模版ID" prop="pay_success_tmpl_id">
          <el-input
            v-model="publicForm.pay_success_tmpl_id"
            :placeholder="`请输入支付成功通知模版ID`"
          ></el-input>
          <lb-tool-tips
            >用户下单支付成功后，收到下单成功通知，通知字段：下单人、支付金额、支付方式、下单时间</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="积分收益通知模版ID" prop="integral_tmpl_id">
          <el-input
            v-model="publicForm.integral_tmpl_id"
            :placeholder="`请输入积分收益通知模版ID`"
          ></el-input>
          <lb-tool-tips
            >用户下单支付成功后，如果还获得了积分，需要有积分收益到账通知。通知字段：收益积分、收益来源、到账时间</lb-tool-tips
          >
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitFrom('publicForm')">{{
            $t('action.submit')
          }}</lb-button>
        </el-form-item>
      </el-form>

      <el-form
        v-show="notice_switch === 2"
        @submit.native.prevent
        :model="enterpriseForm"
        :rules="enterpriseFormRules"
        ref="enterpriseForm"
        label-width="220px"
        class="baseform"
      >
        <el-form-item label="企业微信ID" prop="corpid">
          <el-input
            v-model="enterpriseForm.corpid"
            placeholder="请输入企业微信ID"
          ></el-input>
          <lb-tool-tips>每个企业都拥有唯一的corpid</lb-tool-tips>
        </el-form-item>
        <el-form-item label="应用密匙" prop="corpsecret">
          <el-input
            v-model="enterpriseForm.corpsecret"
            placeholder="请输入应用密匙"
          ></el-input>
          <lb-tool-tips
            >在企业微信中，将自己的小程序添加到应用就可以查看应用密匙</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="企业应用ID" prop="agentid">
          <el-input
            v-model="enterpriseForm.agentid"
            placeholder="请输入企业应用ID"
          ></el-input>
          <lb-tool-tips>企业应用ID，在企业微信应用与小程序中查看</lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitFrom('enterpriseForm')">{{
            $t('action.submit')
          }}</lb-button>
        </el-form-item>
      </el-form>
      <el-form
        v-show="notice_switch === 4"
        @submit.native.prevent
        :model="miniForm"
        :rules="miniFormRules"
        ref="miniForm"
        label-width="220px"
        class="baseform"
      >
        <el-form-item label="企业微信ID" prop="yq_corpid">
          <el-input
            v-model="miniForm.yq_corpid"
            placeholder="请输入企业微信ID"
          ></el-input>
          <lb-tool-tips>每个企业都拥有唯一的corpid</lb-tool-tips>
        </el-form-item>
        <el-form-item label="应用密匙" prop="yq_corpsecret">
          <el-input
            v-model="miniForm.yq_corpsecret"
            placeholder="请输入应用密匙"
          ></el-input>
          <lb-tool-tips
            >在企业微信应用中点击对应小程序查看应用密匙</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="企业应用ID" prop="yq_agentid">
          <el-input
            v-model="miniForm.yq_agentid"
            placeholder="请输入企业应用ID"
          ></el-input>
          <lb-tool-tips>企业应用ID，在企业微信应用与小程序中查看</lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitFrom('miniForm')">{{
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
      options: [
        {
          value: 1,
          label: '公众号通知'
          // },
          // {
          //   value: 2,
          //   label: '企业微信通知'
          // },
          // {
          //   value: 4,
          //   label: '企业微信小程序通知'
        }
      ],
      notice_switch: 1,
      miniForm: {
        yq_corpid: '',
        yq_corpsecret: '',
        yq_agentid: '',
        notice_switch: 4
      },
      miniFormRules: {
        yq_corpid: {
          required: true,
          message: '请输入',
          type: 'string',
          trigger: 'blur'
        },
        yq_corpsecret: {
          required: true,
          message: '请输入',
          type: 'string',
          trigger: 'blur'
        },
        yq_agentid: {
          required: true,
          message: '请输入',
          type: 'string',
          trigger: 'blur'
        },
        notice_switch: {
          required: true,
          message: '请选择',
          type: 'number',
          trigger: 'change'
        }
      },
      enterpriseForm: {
        corpid: '',
        corpsecret: '',
        agentid: '',
        notice_switch: 2
      },
      enterpriseFormRules: {
        corpid: {
          required: true,
          message: '请输入',
          type: 'string',
          trigger: 'blur'
        },
        corpsecret: {
          required: true,
          message: '请输入',
          type: 'string',
          trigger: 'blur'
        },
        agentid: {
          required: true,
          message: '请输入',
          type: 'string',
          trigger: 'blur'
        },
        notice_switch: {
          required: true,
          message: '请选择',
          type: 'number',
          trigger: 'change'
        }
      },
      publicForm: {
        gzh_appid: '',
        order_tmp_id: '',
        cancel_tmp_id: '',
        coachupdate_tmp_id: '',
        help_tmpl_id: '',
        order_service_tmpl_id: '',
        apply_tmp_id: '',
        apply_ok_tmp_id: '',
        apply_no_tmp_id: '',
        refund_pass_tmpl_id: '',
        refund_nopass_tmpl_id: '',
        demand_coach_rob_tmpl_id: '',
        refund_apply_coach_tmpl_id: '',
        pay_success_tmpl_id: '',
        integral_tmpl_id: ''
      },
      publicFormRules: {
        gzh_appid: {
          required: true,
          message: '请输入AppID',
          type: 'string',
          trigger: 'blur'
        },
        order_tmp_id: {
          required: true,
          message: '请输入下单通知模板ID',
          type: 'string',
          trigger: 'blur'
        },
        cancel_tmp_id: {
          required: true,
          message: '请输入取消预约通知模板ID',
          type: 'string',
          trigger: 'blur'
        },
        coachupdate_tmp_id: {
          required: true,
          message: `请输入${this.$t('action.attendantName')}信息编辑通知模板ID`,
          type: 'string',
          trigger: 'blur'
        },
        help_tmpl_id: {
          required: true,
          message: `请输入${this.$t('action.attendantName')}求救通知模板ID`,
          type: 'string',
          trigger: 'blur'
        },
        order_service_tmpl_id: {
          required: true,
          message: '请输入订单服务通知模板ID',
          type: 'string',
          trigger: 'blur'
        },
        notice_switch: {
          required: true,
          message: '请选择',
          type: 'number',
          trigger: 'change'
        },
        apply_tmp_id: {
          required: true,
          message: '请输入报名通知模版ID',
          type: 'string',
          trigger: 'blur'
        },
        apply_ok_tmp_id: {
          required: true,
          message: '请输入报名通过通知模版ID',
          type: 'string',
          trigger: 'blur'
        },
        apply_no_tmp_id: {
          required: true,
          message: '请输入报名拒绝通知模版ID',
          type: 'string',
          trigger: 'blur'
        },
        refund_pass_tmpl_id: {
          required: true,
          message: '请输入用户端退款成功通知模版ID',
          type: 'string',
          trigger: 'blur'
        },
        refund_nopass_tmpl_id: {
          required: true,
          message: '请输入用户端退款失败通知模版ID',
          type: 'string',
          trigger: 'blur'
        },
        demand_coach_rob_tmpl_id: {
          required: true,
          message: '请输入抢单提醒通知模版ID',
          type: 'string',
          trigger: 'blur'
        },
        refund_apply_coach_tmpl_id: {
          required: true,
          message: `请输入${this.$t('action.attendantName')}端退款通知模版ID`,
          type: 'string',
          trigger: 'blur'
        },
        pay_success_tmpl_id: {
          required: true,
          message: `请输入支付成功通知模版ID`,
          type: 'string',
          trigger: 'blur'
        },
        integral_tmpl_id: {
          required: true,
          message: `请输入积分收益通知模版ID`,
          type: 'string',
          trigger: 'blur'
        }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    getFormInfo () {
      this.$api.system.sendMsgConfigInfo().then(res => {
        if (res.code === 200) {
          let formArr = ['publicForm']
          for (let i = 0, len = formArr.length; i < len; i++) {
            for (let key in this[formArr[i]]) {
              this[formArr[i]][key] = res.data[key]
            }
          }
        }
      })
    },
    /**
     * @method 提交表单
     */
    submitFrom (name) {
      this.$refs[name].validate(valid => {
        if (valid) {
          let formInfo = this[name]
          this.$api.system.sendMsgConfigUpdate(formInfo).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.getFormInfo()
            }
          })
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-notice {
  width: 100%;
  .el-input,
  .el-select {
    width: 300px;
  }
}
</style>
