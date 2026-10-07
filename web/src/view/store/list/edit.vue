<!--
 * @Description: 编辑服务
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-07-19 18:03:23
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-store-list-edit">
    <top-nav :isBack="true" />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="130px"
      >
        <el-form-item label="门店头像" prop="cover">
          <lb-cover
            :fileList="subForm.cover"
            @selectedFiles="getCover($event, 'cover')"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸: 160 * 143</lb-tool-tips>
        </el-form-item>
        <el-form-item label="门店名称" prop="title">
          <el-input
            v-model="subForm.title"
            maxlength="15"
            show-word-limit
            placeholder="请输入门店名称"
          ></el-input>
        </el-form-item>
        <el-form-item label="门店认证名称" prop="attestation">
          <el-input
            v-model="subForm.attestation"
            maxlength="20"
            show-word-limit
            placeholder="请输入门店认证名称"
          ></el-input>
          <lb-tool-tips>营业执照上的企业名称</lb-tool-tips>
        </el-form-item>
        <el-form-item label="联系电话" prop="phone">
          <el-input
            v-model="subForm.phone"
            placeholder="请输入联系电话"
          ></el-input>
        </el-form-item>
        <el-form-item label="营业执照" prop="business_license">
          <lb-cover
            :fileList="subForm.business_license"
            @selectedFiles="getCover($event, 'business_license')"
          ></lb-cover>
        </el-form-item>
        <el-form-item label="商家简介" prop="text">
          <el-input
            type="textarea"
            :rows="10"
            v-model="subForm.text"
            maxlength="300"
            show-word-limit
            resize="none"
            placeholder="请输入商家简介"
          ></el-input>
        </el-form-item>
        <el-form-item label="营业时间" prop="time">
          <el-time-select
            placeholder="开始时间"
            v-model="subForm.start_time"
            :picker-options="{
              start: '00:00',
              step: '00:01',
              end: '24:00'
            }"
            style="width: 150px"
          ></el-time-select>
          <div>-</div>
          <el-time-select
            placeholder="结束时间"
            v-model="subForm.end_time"
            :picker-options="{
              start: '00:00',
              step: '00:01',
              end: '24:00'
            }"
            style="width: 150px"
          ></el-time-select>
          <lb-tool-tips
            >营业时间和入驻{{
              $t('action.attendantName')
            }}的工作时间设置一致</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="门店地址" prop="address">
          <el-input
            v-model="subForm.address"
            placeholder="请输入门店地址"
          ></el-input>
          <div class="mt-md mb-md">
            <el-input
              v-model="subForm.lng"
              placeholder="请输入门店经度"
            ></el-input>
          </div>
          <div>
            <el-input
              v-model="subForm.lat"
              placeholder="请输入门店纬度"
            ></el-input>
            <lb-button
              type="primary"
              class="getLocation"
              plain
              style="margin-left: 10px"
              @click="showMap = true"
              >获取经纬度</lb-button
            >
          </div>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>

      <lb-map
        :dialogVisible.sync="showMap"
        @selectedLatLng="getLatLng"
      ></lb-map>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    let checkTime = (rule, value, callback) => {
      let { start_time: start, end_time: end } = this.subForm
      if (!start || !end) {
        callback(new Error(!start ? `请选择开始时间` : `请选择结束时间`))
      } else {
        callback()
      }
    }
    let checkAddress = (rule, value, callback) => {
      let isLng = /^[\-\+]?(0(\.\d{1,15})?|([1-9](\d)?)(\.\d{1,15})?|1[0-7]\d{1}(\.\d{1,15})?|180\.0{1,15})$/
      let isLat = /^[\-\+]?((0|([1-8]\d?))(\.\d{1,15})?|90(\.0{1,15})?)$/
      let { address, lat, lng } = this.subForm
      address = address ? address.replace(/(^\s*)|(\s*$)/g, '') : ''
      if (!address) {
        callback(new Error(`请输入门店地址`))
      } else if (!lng || !isLng.test(lng)) {
        let msg = !lng ? `请输入门店经度` : `请输入正确的经度`
        callback(new Error(msg))
      } else if (!lat || !isLat.test(lat)) {
        let msg = !lat ? `请输入门店纬度` : `请输入正确的纬度`
        callback(new Error(msg))
      } else {
        callback()
      }
    }
    return {
      showMap: false,
      subForm: {
        id: 0,
        cover: [],
        title: '',
        attestation: '',
        phone: '',
        text: '',
        business_license: [],
        start_time: '00:00',
        end_time: '23:59',
        address: '',
        lat: '',
        lng: ''
      },
      subFormRules: {
        cover: { required: true, type: 'array', message: '请上传门店头像', trigger: ['blur', 'change'] },
        title: { required: true, type: 'string', message: '请输入门店名称', trigger: 'blur' },
        attestation: { required: true, type: 'string', message: '请输入门店认证名称', trigger: 'blur' },
        phone: { required: true, validator: this.$reg.isAllPhone, text: '联系电话', trigger: 'blur' },
        text: { required: true, type: 'string', message: '请输入商家简介', trigger: 'blur' },
        business_license: { required: true, type: 'array', message: '请上传营业执照', trigger: ['blur', 'change'] },
        time: { required: true, validator: checkTime, trigger: 'blur' },
        address: { required: true, validator: checkAddress, trigger: ['blur', 'change'] }
      }
    }
  },
  created () {
    let { id } = this.$route.query
    if (id) {
      this.subForm.id = id
      this.getDetail(id)
    }
  },
  methods: {
    /**
     * @name: 详情
     * @param {*} id
     */
    async getDetail (id) {
      let { code, data } = await this.$api.store.storeInfo({ id })
      if (code !== 200) return
      data.cover = [{ url: data.cover }]
      data.business_license = [{ url: data.business_license }]
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    getCover (img, key) {
      this.subForm[key] = img
    },
    getLatLng (latLng) {
      this.subForm.lat = latLng.lat
      this.subForm.lng = latLng.lng
      if (latLng.address) {
        this.subForm.address = latLng.address
      }
    },
    /**
     * @name: 新增/编辑
     * @param {*}
     */
    submitForm () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        subForm.cover = subForm.cover[0].url
        subForm.business_license = subForm.business_license[0].url
        this.$api.store.storeUpdate(subForm).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
            this.$router.back(-1)
          }
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-store-list-edit {
  width: 100%;
  .el-form {
    width: 100%;
    .el-select,
    .el-input-number,
    .el-input {
      width: 300px;
    }
    .el-textarea {
      width: 600px;
    }
  }
}
</style>
