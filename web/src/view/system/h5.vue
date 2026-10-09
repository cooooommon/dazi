<!--
 * @Descripttion: 公众号设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:08:42
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
        label-width="120px"
        class="config-form"
      >
        <el-form-item label="AppID" prop="web_app_id">
          <el-input
            v-model="subForm.web_app_id"
            placeholder="请输入公众号AppID"
          ></el-input>
          <lb-tool-tips
            >请输入公众号AppID，输入错误会影响H5的正常使用，谨慎修改</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="AppSecret" prop="web_app_secret">
          <el-input
            v-model="subForm.web_app_secret"
            placeholder="请输入公众号AppSecret"
          ></el-input>
          <lb-tool-tips
            >请输入公众号AppSecret，输入错误会影响H5的正常使用，谨慎修改</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="腾讯地图key" prop="map_secret">
          <el-input
            v-model="subForm.map_secret"
            placeholder="请输入腾讯地图key"
          ></el-input>
          <lb-tool-tips
            >请输入腾讯地图key，输入错误会影响H5的正常使用，谨慎修改</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="百度地图key" prop="bmap_key">
          <el-input
            v-model="subForm.bmap_key"
            placeholder="请输入百度地图key"
          ></el-input>
          <lb-tool-tips
            >百度地图开放平台（lbsyun.baidu.com）创建「微信小程序」类型应用获取；位置解析服务选百度时必填</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="位置解析服务" prop="map_type">
          <el-radio-group v-model="subForm.map_type">
            <el-radio label="baidu">百度地图</el-radio>
            <el-radio label="tencent">腾讯地图</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >小程序端「定位并解析所在地址」使用的地图服务：选百度时请填写百度地图key，选腾讯时请填写腾讯地图key，保存后重新进入小程序生效</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="公众号二维码" prop="web_code_img">
          <lb-cover
            :fileList="subForm.web_code_img"
            @selectedFiles="getCover($event, 'web_code_img')"
          ></lb-cover>
          <lb-tool-tips
            >图片建议尺寸：750 * n(高度不限)
            <div class="mt-sm">
              商家/平台需要自己上传自己设计好的公众号二维码海报图，用户进入h5之前可以先关注公众号
            </div>
            <div class="mt-sm">
              用户扫描分销商的推广海报，就会直接跳转到引导用户关注公众号的页面
            </div>
          </lb-tool-tips>
          <div></div>
          <a
            class="c-link cursor-pointer"
            href="https://docs.qq.com/doc/DWXlGeVlWdlhOTnh6"
            target="_blank"
            >如何获取公众号二维码？</a
          >
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" 
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
      subForm: {
        web_app_id: '',
        web_app_secret: '',
        map_secret: '',
        bmap_key: '',
        map_type: 'baidu',
        web_code_img: []
      },
      subFormRules: {
        web_app_id: { required: true, validator: this.$reg.isNotNull, text: '公众号AppID', reg_type: 2, trigger: 'blur' },
        web_app_secret: { required: true, validator: this.$reg.isNotNull, text: '公众号AppSecret', reg_type: 2, trigger: 'blur' },
        map_secret: { required: true, validator: this.$reg.isNotNull, text: '腾讯地图key', reg_type: 2, trigger: 'blur' },
        web_code_img: { required: true, type: 'array', message: '请上传公众号二维码', trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    getCover (img, key) {
      this.subForm[key] = img
    },
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      data.web_code_img = data.web_code_img ? [{ url: data.web_code_img }] : []
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      //老数据无 map_type 字段时给默认值
      if (this.subForm.map_type !== 'tencent') this.subForm.map_type = 'baidu'
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          subForm.web_code_img = subForm.web_code_img[0].url
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
