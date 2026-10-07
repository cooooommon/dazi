<!--
 * @Descripttion: 虚拟号码
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-10-19 10:44:06
-->
<template>
  <div class="lb-group-news">
    <top-nav></top-nav>
    <div class="page-main">
      <lb-tips>
        <a
          href="https://www.kancloud.cn/nora_123/shangmenyuyue/3100499"
          target="_blank"
          class="c-link"
          >点击查看阿里云虚拟号码配置文档</a
        ></lb-tips
      >
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="160px"
      >
        <el-form-item label="是否开启虚拟号码" prop="virtual_status">
          <el-radio-group v-model="subForm.virtual_status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips>
            开启后，必须配置阿里云号码池、容联七陌或云信虚拟号码
            <div class="mt-sm">
              用于手机端订单信息里面的电话仅显示部分且拨打电话时显示虚拟号码
            </div>
          </lb-tool-tips>
        </el-form-item>
        <div v-if="subForm.virtual_status === 1">
          <el-form-item label="虚拟号码运营商" prop="virtual_type">
            <el-radio-group v-model="subForm.virtual_type">
              <el-radio :label="1">阿里云</el-radio>
              <el-radio :label="2">容联七陌</el-radio>
              <el-radio :label="3">云信</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item
            label="号码池"
            prop="pool_key"
            v-if="subForm.virtual_type === 1"
          >
            <el-input
              v-model="subForm.pool_key"
              placeholder="请输入号码池"
            ></el-input>
            <lb-tool-tips>号码池, 请前往阿里云获取配置 </lb-tool-tips>
          </el-form-item>
          <div v-if="subForm.virtual_type === 2">
            <el-form-item label="虚拟号模式" prop="moor_virtual_type">
              <el-radio-group v-model="subForm.moor_virtual_type">
                <el-radio :label="1">普通模式</el-radio>
                <el-radio :label="2">双向回呼模式</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="虚拟号码" prop="moor_phone_arr">
              <div
                class="mb-md"
                v-for="(item, index) in subForm.moor_phone_arr"
                :key="index"
              >
                <el-input
                  v-model="item.phone"
                  placeholder="请输入手机号码"
                ></el-input>
                <lb-button
                  style="margin-left: 16px"
                  type="danger"
                  icon="el-icon-delete"
                  @click="toAddItem('moor_phone_arr', 2, index)"
                  v-if="
                    subForm.moor_phone_arr.length > 1 ||
                    (subForm.moor_phone_arr.length === 1 && index !== 0)
                  "
                  >删除</lb-button
                >
                <lb-button
                  style="margin-left: 16px"
                  type="primary"
                  icon="el-icon-plus"
                  @click="toAddItem('moor_phone_arr', 1)"
                  v-if="index === subForm.moor_phone_arr.length - 1"
                  >新增</lb-button
                >
              </div>
            </el-form-item>
          </div>
          <div v-if="subForm.virtual_type === 3">
            <el-form-item label="虚拟号码" prop="winnerlook_phone_arr">
              <div
                class="mb-md"
                v-for="(item, index) in subForm.winnerlook_phone_arr"
                :key="index"
              >
                <el-input
                  v-model="item.phone"
                  placeholder="请输入手机号码"
                ></el-input>
                <lb-button
                  style="margin-left: 16px"
                  type="danger"
                  icon="el-icon-delete"
                  @click="toAddItem('winnerlook_phone_arr', 2, index)"
                  v-if="
                    subForm.winnerlook_phone_arr.length > 1 ||
                    (subForm.winnerlook_phone_arr.length === 1 && index !== 0)
                  "
                  >删除</lb-button
                >
                <lb-button
                  style="margin-left: 16px"
                  type="primary"
                  icon="el-icon-plus"
                  @click="toAddItem('winnerlook_phone_arr', 1)"
                  v-if="index === subForm.winnerlook_phone_arr.length - 1"
                  >新增</lb-button
                >
              </div>
            </el-form-item>
          </div>
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
    let checkPoolKey = (rule, value, callback) => {
      if (this.subForm.virtual_status === 1 && !value) {
        callback(new Error('请输入号码池'))
      } else {
        callback()
      }
    }
    let checkType = (rule, value, callback) => {
      if (this.subForm.virtual_type === 2 && !value) {
        callback(new Error('请选择虚拟号模式'))
      } else {
        callback()
      }
    }
    let checkPhone = (rule, value, callback) => {
      for (let key in value) {
        let index = key * 1 + 1
        let { phone } = value[key]
        if (!phone) {
          callback(new Error(`第${index}条数据：请输入管理员手机号`))
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
        virtual_status: 0,
        virtual_type: 1,
        moor_virtual_type: 1,
        pool_key: '',
        moor_phone_arr: [],
        winnerlook_phone_arr: []
      },
      subFormRules: {
        virtual_status: { required: true, type: 'number', message: '请选择是否开启虚拟号码', trigger: 'blur' },
        virtual_type: { required: true, type: 'number', message: '请选择虚拟号码运营商', trigger: 'blur' },
        moor_virtual_type: { required: true, validator: checkType, trigger: 'blur' },
        pool_key: { required: true, validator: checkPoolKey, trigger: 'blur' },
        moor_phone_arr: { required: true, validator: checkPhone, trigger: 'blur' },
        winnerlook_phone_arr: { required: true, validator: checkPhone, trigger: 'blur' }
      }
    }
  },
  async created () {
    await this.getDetail()
  },
  methods: {
    async getDetail () {
      let { code, data } = await this.$api.system.virtualConfigInfo()
      if (code !== 200) return

      let phoneArr = ['moor_phone_arr', 'winnerlook_phone_arr']
      phoneArr.map(item => {
        data[item] = data[item] && data[item].length > 0 ? data[item].map(aitem => {
          return { phone: aitem }
        }) : [{ phone: '' }]
      })
      for (let i in this.subForm) {
        this.subForm[i] = data[i]
      }
    },
    /**
     * @method: 新增/删除
     */
    async toAddItem (key, type, index) {
      if (type === 2) {
        this.subForm[key].splice(index, 1)
      } else {
        this.subForm[key].push({ phone: '' })
      }
    },
    async submitFormInfo (name) {
      let flag = true
      this.$refs[name].validate(valid => {
        if (!valid) flag = false
      })
      if (!flag) return
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      let { moor_phone_arr: phone, winnerlook_phone_arr: wphone } = subForm
      let arr = phone.filter(item => {
        return item.phone
      })
      subForm.moor_phone_arr = arr.map(aitem => {
        return aitem.phone
      })
      let warr = wphone.filter(item => {
        return item.phone
      })
      subForm.winnerlook_phone_arr = warr.map(aitem => {
        return aitem.phone
      })
      let { code } = await this.$api.system.virtualConfigUpdate(subForm)
      if (code !== 200) return
      this.$message.success(this.$t('tips.successSub'))
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
