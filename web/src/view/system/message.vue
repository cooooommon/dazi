<!--
 * @Descripttion: 群发短信
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-11-27 20:09:09
-->
<template>
  <div class="lb-group-news">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="180px"
      >
        <el-form-item label="短信通知运营商" prop="type">
          <el-radio-group v-model="subForm.type">
            <el-radio :label="1">阿里云</el-radio>
            <el-radio :label="2">容联七陌</el-radio>
            <el-radio :label="3">云信</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="签名" prop="short_sign" v-if="subForm.type !== 2">
          <el-input
            v-model="subForm.short_sign"
            placeholder="请输入签名"
          ></el-input>
          <lb-tool-tips
            >签名在{{
              subForm.type === 1 ? '阿里云' : '云信'
            }}短信签名管理处查看</lb-tool-tips
          >
        </el-form-item>

        <div v-if="subForm.type === 1">
          <el-form-item label="下单模板CODE" prop="order_short_code">
            <el-input
              v-model="subForm.order_short_code"
              placeholder="请输入下单模板CODE"
            ></el-input>
            <lb-tool-tips>短信模板CODE, 如: SMS_129755997</lb-tool-tips>
          </el-form-item>
          <el-form-item label="退单模板CODE" prop="refund_short_code">
            <el-input
              v-model="subForm.refund_short_code"
              placeholder="请输入下单模板CODE"
            ></el-input>
          </el-form-item>
          <el-form-item label="求救通知模板CODE" prop="help_short_code">
            <el-input
              v-model="subForm.help_short_code"
              placeholder="请输入求救通知模板CODE"
            ></el-input>
          </el-form-item>
        </div>
        <div v-if="subForm.type === 2">
          <el-form-item label="下单模板CODE" prop="moor_order_short_code">
            <el-input
              v-model="subForm.moor_order_short_code"
              placeholder="请输入下单模板CODE"
            ></el-input>
          </el-form-item>
          <el-form-item label="退单模板CODE" prop="moor_refund_short_code">
            <el-input
              v-model="subForm.moor_refund_short_code"
              placeholder="请输入下单模板CODE"
            ></el-input>
          </el-form-item>
          <el-form-item label="求救通知模板CODE" prop="moor_help_short_code">
            <el-input
              v-model="subForm.moor_help_short_code"
              placeholder="请输入求救通知模板CODE"
            ></el-input>
          </el-form-item>
        </div>
        <div v-if="subForm.type === 3">
          <el-form-item label="短信登录账号" prop="winner_user">
            <el-input
              v-model="subForm.winner_user"
              placeholder="请输入短信登录账号"
            ></el-input>
          </el-form-item>
          <el-form-item label="短信登录密码" prop="winner_pass">
            <el-input
              v-model="subForm.winner_pass"
              placeholder="请输入短信登录密码"
            ></el-input>
          </el-form-item>
          <el-form-item label="下单通知内容" prop="winner_order_text">
            <el-input
              v-model="subForm.winner_order_text"
              placeholder="请输入下单通知内容"
            ></el-input>
          </el-form-item>
          <el-form-item label="退款通知内容" prop="winner_refund_text">
            <el-input
              v-model="subForm.winner_refund_text"
              placeholder="请输入退款通知内容"
            ></el-input>
          </el-form-item>
          <el-form-item label="求救通知内容" prop="winner_police_text">
            <el-input
              v-model="subForm.winner_police_text"
              placeholder="请输入求救通知内容"
            ></el-input>
          </el-form-item>
        </div>

        <el-form-item label="是否启用短信验证码模板" prop="short_code_status">
          <el-radio-group v-model="subForm.short_code_status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >开启后，必须配置短信验证码模板CODE，用于手机端通过短信验证码收集用户手机号码
          </lb-tool-tips>
        </el-form-item>
        <div v-if="subForm.short_code_status === 1">
          <el-form-item label="绑定手机号方式" prop="bind_phone_type">
            <el-radio-group v-model="subForm.bind_phone_type">
              <el-radio :label="0">弹窗显示绑定手机号</el-radio>
              <el-radio :label="1">跳转到绑定手机号页面</el-radio>
            </el-radio-group>
            <lb-tool-tips
              >当用户在手机端点击登录且未绑定手机号时，若再次访问用户端其他页面，绑定手机号方式应以当前设置为准
              <div class="mt-md">手机端触发绑定手机号位置：</div>
              <div class="mt-sm">
                1、手机端点击登录按钮后，若未绑定手机号，将自动跳转至绑定手机号页面（这里默认跳转,与绑定手机号方式设置无关）
              </div>
              <div class="mt-sm">2、关联表单数据的文章详情，【提交】按钮</div>
              <div class="mt-sm">
                3、服务列表/详情页面，【选择{{
                  $t('action.attendantName')
                }}】按钮
              </div>
              <div class="mt-sm">
                4、{{ $t('action.attendantName') }}列表页面
              </div>
              <div class="mt-sm">
                5、我的页面，用户端【设置】按钮除外的其他功能模块
              </div>
            </lb-tool-tips>
          </el-form-item>
          <el-form-item
            label="短信验证码模板CODE"
            prop="short_code"
            v-if="subForm.type === 1"
          >
            <el-input
              v-model="subForm.short_code"
              placeholder="请输入短信验证码模板CODE"
            ></el-input>
            <lb-tool-tips>用于发送短信验证码</lb-tool-tips>
          </el-form-item>
          <el-form-item
            label="短信验证码模板CODE"
            prop="moor_short_code"
            v-if="subForm.type === 2"
          >
            <el-input
              v-model="subForm.moor_short_code"
              placeholder="请输入短信验证码模板CODE"
            ></el-input>
            <lb-tool-tips>用于发送短信验证码</lb-tool-tips>
          </el-form-item>
          <el-form-item
            label="验证码通知内容"
            prop="winner_code_text"
            v-if="subForm.type === 3"
          >
            <el-input
              v-model="subForm.winner_code_text"
              placeholder="请输入验证码通知内容"
            ></el-input>
            <lb-tool-tips>用于发送短信验证码</lb-tool-tips>
          </el-form-item>
        </div>
        <el-form-item>
          <lb-button
            type="primary"
            @click="submitFormInfo('subForm')"
            v-preventReClick
            >{{ $t('action.submit') }}</lb-button
          >
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    let checkItem = (rule, value, callback) => {
      let data = this.subForm[rule.key]
      let { type } = this.subForm
      data = data ? data.replace(/(^\s*)|(\s*$)/g, '') : ''
      if ((type !== 2 || (type === 2 && rule.key !== 'short_sign')) && !data) {
        callback(new Error(`请输入${rule.text}`))
      } else {
        callback()
      }
    }
    let checkShortCode = (rule, value, callback) => {
      let { type, short_code_status: status = 0 } = this.subForm
      value = value ? value.replace(/(^\s*)|(\s*$)/g, '') : ''
      if (status === 1 && !value) {
        callback(new Error(type === 3 ? '请输入验证码通知内容' : '请输入短信验证码模板CODE'))
      } else {
        callback()
      }
    }
    return {
      subForm: {
        type: 1,
        short_sign: '',
        order_short_code: '',
        refund_short_code: '',
        help_short_code: '',
        short_code_status: 0,
        bind_phone_type: 0,
        short_code: '',
        moor_order_short_code: '',
        moor_refund_short_code: '',
        moor_help_short_code: '',
        moor_short_code: '',
        winner_user: '',
        winner_pass: '',
        winner_order_text: '',
        winner_refund_text: '',
        winner_police_text: '',
        winner_code_text: ''
      },
      subFormRules: {
        type: { required: true, type: 'number', message: '请选择短信通知运营商', trigger: 'blur' },
        short_sign: { required: true, validator: checkItem, key: 'short_sign', text: '签名', trigger: 'blur' },
        order_short_code: { required: true, validator: checkItem, key: 'order_short_code', text: '下单模板CODE', trigger: 'blur' },
        refund_short_code: { required: true, validator: checkItem, key: 'refund_short_code', text: '退单模板CODE', trigger: 'blur' },
        help_short_code: { required: true, validator: checkItem, key: 'help_short_code', text: '求救通知模板CODE', trigger: 'blur' },
        moor_order_short_code: { required: true, validator: checkItem, key: 'moor_order_short_code', text: '下单模板CODE', trigger: 'blur' },
        moor_refund_short_code: { required: true, validator: checkItem, key: 'moor_refund_short_code', text: '退单模板CODE', trigger: 'blur' },
        moor_help_short_code: { required: true, validator: checkItem, key: 'moor_help_short_code', text: '求救通知模板CODE', trigger: 'blur' },
        winner_user: { required: true, validator: checkItem, key: 'winner_user', text: '短信登录账号', trigger: 'blur' },
        winner_pass: { required: true, validator: checkItem, key: 'winner_pass', text: '短信登录密码', trigger: 'blur' },
        winner_order_text: { required: true, validator: checkItem, key: 'winner_order_text', text: '下单通知内容', trigger: 'blur' },
        winner_refund_text: { required: true, validator: checkItem, key: 'winner_order_text', text: '退款通知内容', trigger: 'blur' },
        winner_police_text: { required: true, validator: checkItem, key: 'winner_police_text', text: '求救通知内容', trigger: 'blur' },
        short_code_status: { required: true, type: 'number', message: '请选择是否启用短信模板', trigger: 'blur' },
        bind_phone_type: { required: true, type: 'number', message: '请选择绑定手机号方式', trigger: 'blur' },
        short_code: { required: true, validator: checkShortCode, trigger: 'blur' },
        moor_short_code: { required: true, validator: checkShortCode, trigger: 'blur' },
        winner_code_text: { required: true, validator: checkShortCode, trigger: 'blur' }
      }
    }
  },
  async created () {
    await this.getDetail()
  },
  methods: {
    async getDetail () {
      let { code, data } = await this.$api.system.shortCodeConfigInfo()
      if (code !== 200) return
      for (let i in this.subForm) {
        this.subForm[i] = data[i]
      }
    },
    submitFormInfo (name) {
      let flag = true
      this.$refs[name].validate(valid => {
        if (!valid) flag = false
      })
      if (!flag) return
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      this.$api.system.shortCodeConfigUpdate(subForm).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successSub'))
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-group-news {
  width: 100%;
  .page-main {
    .el-input {
      width: 300px;
    }
  }
}
</style>
