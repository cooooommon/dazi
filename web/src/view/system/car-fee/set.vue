<!--
 * @Descripttion: 交易设置
 * @Author: xiao li
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:09:51
-->
<template>
  <div class="lb-system-transaction">
    <top-nav />
    <div class="page-main">
      <lb-tips>
        出租出行{{ result.start_distance }}km内，起步{{
          result.start_price
        }}元。里程计价：{{ result.distance_price }}元/km
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="120px"
      >
        <el-form-item label="起步距离" prop="start_distance">
          <el-input
            v-model="subForm.start_distance"
            placeholder="请输入起步距离"
          >
            <template slot="append">km</template>
          </el-input>
        </el-form-item>
        <el-form-item label="起步价" prop="start_price">
          <el-input v-model="subForm.start_price" placeholder="请输入起步价">
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="里程计价" prop="distance_price">
          <el-input
            v-model="subForm.distance_price"
            placeholder="请输入里程计价"
          >
            <template slot="append">元/km</template>
          </el-input>
        </el-form-item>
        <el-form-item label="虚拟里程" prop="invented_distance">
          <el-input
            v-model="subForm.invented_distance"
            placeholder="请输入虚拟里程"
          >
            <template slot="append">%</template>
          </el-input>
          <lb-tool-tips
            >虚拟里程用于 距离计算短、车费计算少
            的情况可在后台增加一部分虚拟里程，减少{{
              $t('action.attendantName')
            }}损失
            <div class="mt-sm">
              用户端显示的距离=实际距离+实际距离*虚拟里程百分比
            </div></lb-tool-tips
          >
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
    return {
      result: {},
      subForm: {
        distance_free: '',
        distance_price: '',
        start_distance: '',
        start_price: '',
        invented_distance: ''
      },
      subFormRules: {
        start_distance: { required: true, validator: this.$reg.isFloatNum, trigger: 'blur' },
        start_price: { required: true, validator: this.$reg.isMoney, trigger: 'blur' },
        distance_price: { required: true, validator: this.$reg.isMoney, trigger: 'blur' },
        invented_distance: { required: true, validator: this.$reg.isPercent, trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.carConfigInfo()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      this.result = JSON.parse(JSON.stringify(this.subForm))
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let param = JSON.parse(JSON.stringify(this.subForm))
          this.$api.system.carConfigUpdate(param).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.result = param
            }
          })
        }
      })
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
