<!--
 * @Description: 会员基础设置
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-08 16:06:24
 * @LastEditTime: 2024-11-26 16:44:18
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-custom-member-set">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
      >
        <el-form-item label="会员设置" prop="status">
          <el-radio-group v-model="subForm.status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips>开启后，会员折扣和会员套餐才能生效</lb-tool-tips>
        </el-form-item>
        <div v-if="subForm.status === 1">
          <el-form-item label="会员卡折扣" prop="discount">
            <el-input placeholder v-model="subForm.discount">
              <template slot="append">折</template>
            </el-input>
            <lb-tool-tips
              >取值0.1-9.9的数值，支持输入小数，保留小数点后1位</lb-tool-tips
            >
          </el-form-item>
          <el-form-item
            label="积分抵扣"
            prop="integra"
            v-if="routesItem.auth.integral"
          >
            每消费1元可获得
            <el-input-number
              :min="0"
              :precision="0"
              :controls="false"
              v-model.number="subForm.integra"
              class="mini ml-md mr-md"
              placeholder="请输入积分抵扣"
            >
            </el-input-number>
            积分
          </el-form-item>
          <el-form-item label="会员卡返佣" prop="balance">
            <el-input placeholder v-model="subForm.balance">
              <template slot="append">%</template>
            </el-input>
            <lb-tool-tips>
              分销员邀请新用户购买会员卡，获得新用户购买会员卡金额的百分比，<br />
              取值0-100的数值，支持输入小数，保留小数点后2位。
            </lb-tool-tips>
          </el-form-item>
          <el-form-item label="会员协议" prop="text">
            <lb-ueditor
              v-model="subForm.text"
              :destroy="true"
              :ueditorType="2"
            ></lb-ueditor>
          </el-form-item>
        </div>
        <el-form-item>
          <lb-button type="primary" @click="submitFormInfo" v-preventReClick>{{
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
    let checkDiscount = (rule, value, callback) => {
      let reg = /^(?:[1-9]?|([0-9]*\.\d{1}))$/
      if (value === '' || !reg.test(value) || (value && value * 1 > 10)) {
        callback(new Error(value === '' ? `请输入${rule.text}` : `请输入正确${rule.text}，0.1至9.9，最多保留1位小数`))
      } else {
        callback()
      }
    }

    return {
      navTitle: '',
      subForm: { status: 0, discount: '', balance: '', text: '', integra: '' },
      subFormRules: {
        status: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        discount: { required: true, validator: checkDiscount, text: '会员卡折扣', decimal: 1, trigger: 'blur' },
        balance: { required: true, validator: this.$reg.isPercent, text: '会员卡返佣', trigger: 'blur', decimal: 1 },
        text: { required: true, validator: this.$reg.isNotNull, text: '会员协议', reg_type: 2, trigger: 'blur' },
        integra: { required: true, type: 'number', message: '请输入积分抵扣', trigger: 'blur' }
      }
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  async created () {
    this.getDetail()
  },
  methods: {
    async getDetail () {
      let { data } = await this.$api.memberdiscount.getConfigSet()
      data.text = data.text === null ? '' : data.text
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    async submitFormInfo () {
      let { status } = this.subForm
      let validate = true
      if (status === 1) {
        this.$refs['subForm'].validate(valid => {
          if (!valid) validate = false
        })
      }
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      if (!validate) return
      await this.$api.memberdiscount.configSet(subForm)
      this.$message.success(this.$t('tips.successSub'))
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-custom-member-set {
  width: 100%;
  .el-input,
  .el-select,
  .lb-input-number,
  .el-cascader,
  .el-textarea {
    width: 300px;
  }
}
</style>
