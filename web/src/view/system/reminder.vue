<!--
 * @Descripttion: 来电通知
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2024-04-24 16:10:23
-->
<template>
  <div class="lb-group-news">
    <top-nav></top-nav>
    <div class="page-main">
      <lb-tips>
        <p class="c-warning">
          温馨提示：运营商规定，针对同一个“资质+用途“下的主叫进行流控限制，1次/分钟、5次/小时、20次/24小时（流控规则计时是从第一次正常外呼开始计时的）
        </p>
        <p class="c-link mt-sm">
          若被流控，则更改为短信通知。
          <!-- <a
            href="https://www.kancloud.cn/nora_123/shangmenyuyue/3100498"
            target="_blank"
            class="c-link"
            >点击查看阿里云来电通知配置文档</a
          > -->
        </p>
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="160px"
      >
        <el-form-item label="是否开启来电通知" prop="reminder_status">
          <el-radio-group v-model="subForm.reminder_status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >开启后，必须配置语音通知模板id, 用户下单时会电话通知{{
              $t('action.attendantName')
            }}</lb-tool-tips
          >
        </el-form-item>
        <div v-if="subForm.reminder_status === 1">
          <el-form-item label="语音通知运营商" prop="reminder_type">
            <el-radio-group v-model="subForm.reminder_type">
              <el-radio :label="1">阿里云</el-radio>
              <el-radio :label="2">容联七陌</el-radio>
              <el-radio :label="3">云信</el-radio>
            </el-radio-group>
          </el-form-item>
          <div v-if="subForm.reminder_type === 1">
            <el-form-item label="语音通知模式" prop="reminder_public">
              <el-radio-group v-model="subForm.reminder_public">
                <el-radio :label="0">专属模式</el-radio>
                <el-radio :label="1">公共模式</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="语音通知模板id" prop="reminder_tmpl_id">
              <el-input
                v-model="subForm.reminder_tmpl_id"
                placeholder="请输入语音通知模板id"
              ></el-input>
            </el-form-item>
            <el-form-item label="求救语音通知模版id" prop="help_tmpl_id">
              <el-input
                v-model="subForm.help_tmpl_id"
                placeholder="请输入求救语音通知模版id"
              ></el-input>
            </el-form-item>
            <el-form-item
              label="语音通知电话"
              prop="reminder_phone"
              v-if="subForm.reminder_public === 0"
            >
              <el-input
                v-model="subForm.reminder_phone"
                placeholder="请输入语音通知电话"
              ></el-input>
            </el-form-item>
          </div>
          <div v-if="subForm.reminder_type === 2">
            <el-form-item label="语音通知内容" prop="reminder_text">
              <el-input
                v-model="subForm.reminder_text"
                placeholder="请输入语音通知内容"
              ></el-input>
            </el-form-item>
            <el-form-item label="求救语音通知内容" prop="help_tmpl_text">
              <el-input
                v-model="subForm.help_tmpl_text"
                placeholder="请输入求救语音通知内容"
              ></el-input>
            </el-form-item>
            <el-form-item label="语音通知电话" prop="moor_reminder_phone">
              <el-input
                v-model="subForm.moor_reminder_phone"
                placeholder="请输入语音通知电话"
              ></el-input>
            </el-form-item>
          </div>
          <div v-if="subForm.reminder_type === 3">
            <el-form-item label="订单语音通知模板id" prop="reminder_tmpl_id">
              <el-input
                v-model="subForm.reminder_tmpl_id"
                placeholder="请输入订单语音通知模板id"
              ></el-input>
            </el-form-item>
            <el-form-item label="求救语音通知模版id" prop="help_tmpl_id">
              <el-input
                v-model="subForm.help_tmpl_id"
                placeholder="请输入求救语音通知模版id"
              ></el-input>
            </el-form-item>
          </div>
          <el-form-item label="语音通知定时任务" prop="reminder_timing">
            <el-input
              v-model="subForm.reminder_timing"
              placeholder="请输入语音通知定时任务分钟数"
            >
              <template slot="append">分钟</template>
            </el-input>
            <lb-tool-tips
              >填写0就是没有定时任务，仅下单的时候通知一次</lb-tool-tips
            >
          </el-form-item>
          <!-- <el-form-item label="是否通知代理商" prop="notice_agent">
            <el-radio-group v-model="subForm.notice_agent">
              <el-radio :label="1">通知</el-radio>
              <el-radio :label="0">不通知</el-radio>
            </el-radio-group>
            <lb-tool-tips
              >选择通知，用户下单后，订单所选{{
                $t('action.attendantName')
              }}的代理商将收到来电提醒</lb-tool-tips
            >
          </el-form-item> -->
          <!-- <el-form-item label="是否通知管理员" prop="reminder_admin_status">
            <el-radio-group v-model="subForm.reminder_admin_status">
              <el-radio :label="1">通知</el-radio>
              <el-radio :label="0">不通知</el-radio>
            </el-radio-group>
          </el-form-item> -->
          <!-- <el-form-item
            label="管理员手机号"
            prop="reminder_admin_phone"
            v-if="subForm.reminder_admin_status === 1"
          >
            <div
              class="mb-md"
              v-for="(item, index) in subForm.reminder_admin_phone"
              :key="index"
            >
              <el-input
                v-model="item.phone"
                placeholder="请输入管理员手机号"
              ></el-input>
              <lb-button
                style="margin-left: 16px"
                type="danger"
                icon="el-icon-delete"
                @click="toAddItem(2, index)"
                v-if="
                  subForm.reminder_admin_phone.length > 1 ||
                  (subForm.reminder_admin_phone.length === 1 && index !== 0)
                "
                >删除</lb-button
              >
              <lb-button
                style="margin-left: 16px"
                type="primary"
                icon="el-icon-plus"
                @click="toAddItem(1)"
                v-if="index === subForm.reminder_admin_phone.length - 1"
                >新增</lb-button
              >
            </div>
          </el-form-item> -->
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
      let { reminder_status: status = 0, reminder_type: type, reminder_public: reminder = 0 } = this.subForm
      data = data ? data.replace(/(^\s*)|(\s*$)/g, '') : ''
      if (status === 1 && ((type === 1 && ((rule.key === 'reminder_phone' && reminder === 0) || (rule.key !== 'reminder_phone'))) || type === 2) && !data) {
        callback(new Error(`请输入${rule.text}`))
      } else {
        callback()
      }
    }
    let checkTiming = (rule, value, callback) => {
      let isNum = /^\+?[0-9]*$/
      if (this.subForm.reminder_status === 1 && !isNum.test(value)) {
        callback(new Error(`请输入语音通知定时任务分钟数`))
      } else {
        callback()
      }
    }
    let checkAdminPhone = (rule, value, callback) => {
      let isTel = /^1[3-9]\d{9}$/
      for (let key in value) {
        let index = key * 1 + 1
        let { phone } = value[key]
        if (!phone || !isTel.test(phone)) {
          let errorMsg = !phone ? `请输入管理员手机号` : `${phone} 手机号无效`
          callback(new Error(`第${index}条数据：${errorMsg}`))
          return
        }
      }
      let arr = value.filter(item => {
        return item.phone
      })
      if (arr.length === value.length) {
        callback()
      }
    }
    return {
      subForm: {
        reminder_status: 0,
        reminder_type: 1,
        reminder_public: 0,
        reminder_tmpl_id: '',
        reminder_phone: '',
        reminder_text: '',
        help_tmpl_id: '',
        help_tmpl_text: '',
        moor_reminder_phone: '',
        reminder_timing: '',
        // notice_agent: 0,
        // reminder_admin_status: 0,
        // reminder_admin_phone: []
      },
      subFormRules: {
        reminder_status: { required: true, type: 'number', message: '请选择是否开启来电通知', trigger: 'blur' },
        reminder_type: { required: true, type: 'number', message: '请选择语音通知运营商', trigger: 'blur' },
        reminder_public: { required: true, type: 'number', message: '请选择语音通知模式', trigger: 'blur' },
        reminder_tmpl_id: { required: true, validator: checkItem, key: 'reminder_tmpl_id', text: '语音通知模板id', trigger: 'blur' },
        reminder_phone: { required: true, validator: checkItem, key: 'reminder_phone', text: '语音通知电话', trigger: 'blur' },
        reminder_text: { required: true, validator: checkItem, key: 'reminder_text', text: '语音通知内容', trigger: 'blur' },
        moor_reminder_phone: { required: true, validator: checkItem, key: 'moor_reminder_phone', text: '语音通知电话', trigger: 'blur' },
        reminder_timing: { required: true, validator: checkTiming, trigger: 'blur' },
        // notice_agent: { required: true, trigger: 'blur' },
        // reminder_admin_status: { required: true, trigger: 'blur' },
        // reminder_admin_phone: { required: true, validator: checkAdminPhone, trigger: 'blur' }
      }
    }
  },
  async created () {
    await this.getDetail()
  },
  methods: {
    async getDetail () {
      let { code, data } = await this.$api.system.reminderConfigInfo()
      if (code !== 200) return
      // data.reminder_admin_phone = data.reminder_admin_phone.length > 0 ? data.reminder_admin_phone.map(item => {
      //   return { phone: item }
      // }) : [{ phone: '' }]
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    /**
     * @method: 新增/删除
     */
    async toAddItem (key, index) {
      if (key === 2) {
        this.subForm.reminder_admin_phone.splice(index, 1)
      } else {
        this.subForm.reminder_admin_phone.push({ phone: '' })
      }
    },
    submitFormInfo (name) {
      this.$refs[name].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          // let { reminder_admin_phone: phone } = subForm
          // let isTel = /^1[3-9]\d{9}$/
          // let arr = phone.filter(item => {
          //   return item.phone && isTel.test(item.phone)
          // })
          // subForm.reminder_admin_phone = arr.map(aitem => {
          //   return aitem.phone
          // })
          this.$api.system.reminderConfigUpdate(subForm).then(res => {
            if (res.code === 200) {
              // this.subForm.reminder_admin_phone = arr
              this.$message.success(this.$t('tips.successSub'))
            }
          })
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
