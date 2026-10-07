<!--
 * @Descripttion: 编辑打印机
 * @Author: xiao li
 * @Date: 2020-09-30 10:32:19
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:09:10
-->
<template>
  <div class="lb-ordering-editPrint">
    <top-nav />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="aForm"
        label-width="120px"
        class="basic-form"
      >
        <el-form-item label="打印机名称" prop="title">
          <el-input
            v-model="subForm.title"
            maxlength="40"
            placeholder="请输入打印机名称"
          ></el-input>
        </el-form-item>
        <el-form-item label="选择打印机" prop="type">
          <el-select v-model="subForm.type" placeholder="请选择打印机">
            <el-option
              v-for="item in printList"
              :key="item.id"
              :label="item.title"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>

        <el-form
          @submit.native.prevent
          :model="subForm"
          :rules="bFormRules"
          ref="bForm"
          label-width="120px"
          class="basic-form"
          v-if="subForm.type === 1"
        >
          <el-form-item label="USER" prop="user">
            <el-input
              v-model="subForm.user"
              placeholder="请输入飞鹅云后台注册用户名"
            ></el-input>
          </el-form-item>
          <el-form-item label="UKEY" prop="api_key">
            <el-input
              v-model="subForm.api_key"
              placeholder="请输入飞鹅云注册账号后生成的UKEY"
            ></el-input>
          </el-form-item>
          <el-form-item label="SN" prop="printer_key">
            <el-input
              v-model="subForm.printer_key"
              placeholder="请输入打印机编号"
            ></el-input>
          </el-form-item>
        </el-form>
        <el-form-item label="自动打印" prop="auto">
          <el-switch v-model="subForm.auto"></el-switch>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitFormInfo"
            v-preventReClick>{{
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
      navTitle: '',
      printList: [{
        id: 1,
        title: '飞鹅打印'
      }],
      checkNum: [],
      numList: [{
        title: '用户小票',
        key: 'user_ticket',
        number: 'user_ticket_num',
        tips: '接单后打印小票'
      }, {
        title: '后厨小票',
        key: 'kitchen_ticket',
        number: 'kitchen_ticket_num',
        tips: '正餐接单、快餐下单、外卖接单后打印小票'
      }
      ],
      printNumList: [1, 2, 3, 4, 5],
      subForm: {
        store_id: 0,
        title: '',
        type: 1, // 1:飞蛾 2:易联云',
        user: '',
        code: '',
        api_key: '',
        printer_key: '',
        auto: false,
        user_ticket: 0,
        user_ticket_num: 1,
        kitchen_ticket: 0,
        kitchen_ticket_num: 1
      },
      subFormRules: {
        title: { required: true, validator: this.$reg.isNotNull, text: '打印机名称', reg_type: 2, trigger: 'blur' },
        type: { required: true },
        auto: { required: true }
      },
      bFormRules: {
        user: { required: true, type: 'string', message: '请输入飞鹅云后台注册用户名', trigger: 'blur' },
        api_key: { required: true, type: 'string', message: '请输入飞鹅云注册账号后生成的UKEY', trigger: 'blur' },
        printer_key: { required: true, type: 'string', message: '请输入打印机编号', trigger: 'blur' }
      }
    }
  },
  created () {
    this.getDetail()
  },
  methods: {
    changeCheckBox (e, key, key1) {
      if (key === 'subForm') {
        this[key][key1] = e
      } else {
        this[key].map(item => {
          this.subForm[item.key] = e.includes(item.title) ? 1 : 0
        })
      }
    },
    async getDetail () {
      let { code, data } = await this.$api.system.printerInfo()
      if (code !== 200) return
      data.auto = data.auto === 1
      let arr = ['api_key', 'code', 'user', 'printer_key']
      arr.map(item => {
        if (data[item] === '0') {
          data[item] = ''
        }
      })
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      let checkNum = []
      this.numList.map(item => {
        if (data[item.key] === 1) {
          checkNum.push(item.title)
        }
      })
      this.checkNum = checkNum
    },
    async submitFormInfo () {
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      let formArr = ['aForm']
      if (subForm.type === 1) {
        formArr.push('bForm')
      }
      let flag = true
      for (let i = 0, len = formArr.length; i < len; i++) {
        this.$refs[formArr[i]].validate(valid => {
          if (!valid) {
            flag = false
            return false
          }
        })
      }
      if (flag) {
        subForm.auto = subForm.auto === true ? 1 : 0
        let { code } = await this.$api.system.printerUpdate(subForm)
        if (code !== 200) return
        this.$message.success(this.$t('tips.successSub'))
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-ordering-editPrint {
  width: 100%;
  .el-input,
  .el-select,
  .lb-input-number,
  .el-cascader {
    width: 400px;
  }
  .flex-warp {
    .el-select {
      width: 230px;
    }
  }
}
</style>
