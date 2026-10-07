<!--
 * @Descripttion: app设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2024-05-08 15:17:26
-->
<template>
  <div class="lb-system-wechat">
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
        <el-form-item label="分销商审核" prop="fx_check">
          <el-radio-group v-model="subForm.fx_check">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="推广海报背景图" prop="promotion_poster_img">
          <lb-cover
            :fileList="subForm.promotion_poster_img"
            @selectedFiles="getCover($event, 'promotion_poster_img')"
          ></lb-cover>
          <lb-tool-tips
            >图片建议尺寸：710 * 1152
            <div class="mt-sm">
              由于页面生成的二维码位置是固定的，所以设计海报时注意将中间的二维码位置留出来
            </div>
            <div class="mt-sm">
              海报背景图中不要出现诱导用户分享以及传播外链内容的
            </div>
            <div class="mt-sm">
              包括但不限于：以金钱奖励、实物奖励、虚拟奖品（包括但不限于红包、优惠券、代金券、积分、话费、流量）；声称分享可获得返佣等
            </div>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button type="danger" plain @click="toReset(1)">{{
            $t('action.defaultSet')
          }}</lb-button>
        </el-form-item>

        <el-form-item label="下级推广海报" prop="distribution_poster_img">
          <lb-cover
            :fileList="subForm.distribution_poster_img"
            @selectedFiles="getCover($event, 'distribution_poster_img')"
          ></lb-cover>
          <lb-tool-tips
            >图片建议尺寸：750 * 1430
            <div class="mt-sm">
              由于页面生成的二维码位置是固定的，所以设计海报时注意将中间的二维码位置留出来
            </div>
            <div class="mt-sm">
              海报背景图中不要出现诱导用户分享以及传播外链内容的
            </div>
            <div class="mt-sm">
              包括但不限于：以金钱奖励、实物奖励、虚拟奖品（包括但不限于红包、优惠券、代金券、积分、话费、流量）；声称分享可获得返佣等
            </div>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button type="danger" plain @click="toReset(2)">{{
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
    return {
      subForm: {
        fx_check: '',
        promotion_poster_img: [],
        distribution_poster_img: []
      },
      subFormRules: {
        fx_check: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        promotion_poster_img: { required: true, type: 'array', message: '请选择推广海报背景图', trigger: 'blur' },
        distribution_poster_img: { required: true, type: 'array', message: '请选择下级推广海报', trigger: 'blur' }
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
      data.promotion_poster_img = [{ url: data.promotion_poster_img }]
      data.distribution_poster_img = [{ url: data.distribution_poster_img }]
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    getCover (img, key) {
      console.log(img)
      this.subForm[key] = img
    },
    toReset (type) {
      if (type === 1) {
        this.subForm.promotion_poster_img = [{ url: 'https://lbqny.migugu.com/admin/peiwan/fx-share1.png' }]
      } else {
        this.subForm.distribution_poster_img = [{ url: 'https://lbqny.migugu.com/admin/anmo/mine/fx-share-down.png' }]
      }
      this.submitForm(type)
    },
    submitForm (type = 0) {
      this.$refs['subForm'].validate(valid => {
        if (valid || type) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          // if (type === 1) {
          //   subForm.promotion_poster_img = subForm.promotion_poster_img[0].url
          // } else if (type === 2) {
          //   subForm.distribution_poster_img = subForm.distribution_poster_img[0].url
          // }
          let arr = ['promotion_poster_img', 'distribution_poster_img']
          arr.forEach(item => {
            console.log(typeof item)
            if (typeof subForm[item] !== 'string') {
              subForm[item] = subForm[item][0].url
            }
          })
          this.$api.system.configUpdate(subForm).then(res => {
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

<style lang="scss" scoped>
.lb-system-wechat {
  width: 100%;
  .config-form {
    .el-input {
      width: 300px;
    }
  }
}
</style>
