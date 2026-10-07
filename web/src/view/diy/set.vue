<!--
 * @Descripttion: 其他设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-25 17:52:47
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
        <el-form-item label="行业服务人员名称" prop="attendant_name">
          <el-input
            v-model="subForm.attendant_name"
            maxlength="5"
            show-word-limit
            placeholder="请输入行业服务人员名称"
          ></el-input>
        </el-form-item>
        <lb-classify-title
          :title="`${$t('action.attendantName')}页面 - 可服务按钮`"
        ></lb-classify-title>
        <el-form-item label="按钮颜色" prop="service_btn_color">
          <div class="flex-warp">
            <div class="flex-warp mt-sm">
              <div v-for="(item, index) in colorList" :key="index">
                <div
                  @click="changeIndex(index, 'service_btn_color')"
                  class="color-item"
                  :class="[{ active: index === ind.service_btn_color }]"
                >
                  <div class="flex-center">
                    <div class="primaryColor flex-center">
                      <div class="color-bg" :style="{ background: item }"></div>
                    </div>
                  </div>
                  <i
                    class="iconfont icon-xuanze-fill flex-center"
                    v-if="index === ind.service_btn_color"
                  ></i>
                </div>
              </div>
            </div>
            <div
              @click="changeIndex(9, 'service_btn_color')"
              class="color-item mt-sm"
              :class="[{ active: ind.service_btn_color === 9 }]"
              style="width: auto; padding: 0 2px"
            >
              <div class="flex-center" style="margin-top: 4px">
                <el-color-picker
                  @change="changeColor($event, 'service_btn_color')"
                  size="mini"
                  style="margin-right: 4px"
                  v-model="color.service_btn_color"
                ></el-color-picker>
              </div>
              <div class="flex-y-center" style="height: 18px; margin-top: 4px">
                <div style="line-height: 18px; font-size: 10px">自定义配色</div>
                <i
                  class="iconfont icon-xuanze flex-center"
                  :class="[
                    {
                      'icon-xuanze-fill': ind.service_btn_color === 9
                    }
                  ]"
                  style="margin: 0"
                ></i>
              </div>
            </div>
          </div>
        </el-form-item>
        <el-form-item label="文字颜色" prop="service_font_color">
          <div class="flex-warp">
            <div class="flex-warp mt-sm">
              <div v-for="(item, index) in colorList" :key="index">
                <div
                  @click="changeIndex(index, 'service_font_color')"
                  class="color-item"
                  :class="[{ active: index === ind.service_font_color }]"
                >
                  <div class="flex-center">
                    <div class="primaryColor flex-center">
                      <div class="color-bg" :style="{ background: item }"></div>
                    </div>
                  </div>
                  <i
                    class="iconfont icon-xuanze-fill flex-center"
                    v-if="index === ind.service_font_color"
                  ></i>
                </div>
              </div>
            </div>
            <div
              @click="changeIndex(9, 'service_font_color')"
              class="color-item mt-sm"
              :class="[{ active: ind.service_font_color === 9 }]"
              style="width: auto; padding: 0 2px"
            >
              <div class="flex-center" style="margin-top: 4px">
                <el-color-picker
                  @change="changeColor($event, 'service_font_color')"
                  size="mini"
                  style="margin-right: 4px"
                  v-model="color.service_font_color"
                ></el-color-picker>
              </div>
              <div class="flex-y-center" style="height: 18px; margin-top: 4px">
                <div style="line-height: 18px; font-size: 10px">自定义配色</div>
                <i
                  class="iconfont icon-xuanze flex-center"
                  :class="[
                    {
                      'icon-xuanze-fill': ind.service_font_color === 9
                    }
                  ]"
                  style="margin: 0"
                ></i>
              </div>
            </div>
          </div>
        </el-form-item>
        <el-form-item>
          <lb-button type="danger" plain @click="toReset('service')">{{
            $t('action.defaultSet')
          }}</lb-button>
        </el-form-item>
        <!-- <lb-classify-title title="个人中心 - 用户端"></lb-classify-title>
        <el-form-item label="背景图" prop="user_image">
          <lb-cover
            :fileList="subForm.user_image"
            @selectedFiles="getCover($event, 'user_image')"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸：750 * 368</lb-tool-tips>
        </el-form-item>
        <el-form-item label="文字颜色" prop="user_font_color">
          <div class="flex-warp">
            <div class="flex-warp mt-sm">
              <div v-for="(item, index) in colorList" :key="index">
                <div
                  @click="changeIndex(index, 'user_font_color')"
                  class="color-item"
                  :class="[{ active: index === ind.user_font_color }]"
                >
                  <div class="flex-center">
                    <div class="primaryColor flex-center">
                      <div class="color-bg" :style="{ background: item }"></div>
                    </div>
                  </div>
                  <i
                    class="iconfont icon-xuanze-fill flex-center"
                    v-if="index === ind.user_font_color"
                  ></i>
                </div>
              </div>
            </div>
            <div
              @click="changeIndex(9, 'user_font_color')"
              class="color-item mt-sm"
              :class="[{ active: ind.user_font_color === 9 }]"
              style="width: auto; padding: 0 2px"
            >
              <div class="flex-center" style="margin-top: 4px">
                <el-color-picker
                  @change="changeColor($event, 'user_font_color')"
                  size="mini"
                  style="margin-right: 4px"
                  v-model="color.user_font_color"
                ></el-color-picker>
              </div>
              <div class="flex-y-center" style="height: 18px; margin-top: 4px">
                <div style="line-height: 18px; font-size: 10px">自定义配色</div>
                <i
                  class="iconfont icon-xuanze flex-center"
                  :class="[
                    {
                      'icon-xuanze-fill': ind.user_font_color === 9
                    }
                  ]"
                  style="margin: 0"
                ></i>
              </div>
            </div>
          </div>
        </el-form-item>
        <el-form-item>
          <lb-button type="danger" plain @click="toReset('user')">{{
            $t('action.defaultSet')
          }}</lb-button>
        </el-form-item> -->
        <lb-classify-title
          :title="`个人中心 - ${$t('action.attendantName')}端`"
        ></lb-classify-title>
        <el-form-item label="背景图" prop="coach_image">
          <lb-cover
            :fileList="subForm.coach_image"
            @selectedFiles="getCover($event, 'coach_image')"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸：750 * 368</lb-tool-tips>
        </el-form-item>
        <el-form-item label="文字颜色" prop="coach_font_color">
          <div class="flex-warp">
            <div class="flex-warp mt-sm">
              <div v-for="(item, index) in colorList" :key="index">
                <div
                  @click="changeIndex(index, 'coach_font_color')"
                  class="color-item"
                  :class="[{ active: index === ind.coach_font_color }]"
                >
                  <div class="flex-center">
                    <div class="primaryColor flex-center">
                      <div class="color-bg" :style="{ background: item }"></div>
                    </div>
                  </div>
                  <i
                    class="iconfont icon-xuanze-fill flex-center"
                    v-if="index === ind.coach_font_color"
                  ></i>
                </div>
              </div>
            </div>
            <div
              @click="changeIndex(9, 'coach_font_color')"
              class="color-item mt-sm"
              :class="[{ active: ind.coach_font_color === 9 }]"
              style="width: auto; padding: 0 2px"
            >
              <div class="flex-center" style="margin-top: 4px">
                <el-color-picker
                  @change="changeColor($event, 'coach_font_color')"
                  size="mini"
                  style="margin-right: 4px"
                  v-model="color.coach_font_color"
                ></el-color-picker>
              </div>
              <div class="flex-y-center" style="height: 18px; margin-top: 4px">
                <div style="line-height: 18px; font-size: 10px">自定义配色</div>
                <i
                  class="iconfont icon-xuanze flex-center"
                  :class="[
                    {
                      'icon-xuanze-fill': ind.coach_font_color === 9
                    }
                  ]"
                  style="margin: 0"
                ></i>
              </div>
            </div>
          </div>
        </el-form-item>
        <el-form-item>
          <lb-button type="danger" plain @click="toReset('coach')">{{
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
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      default_img: 'https://lbqny.migugu.com/admin/anmo/mine/bg.png',
      colorList: [
        '#739bc6',
        '#60a06a',
        '#d4b64c',
        '#c09e51',
        '#d5964b',
        '#c26a51',
        '#ffb6b1',
        '#b0b4c7',
        '#616570'
      ],
      color: {
        service_btn_color: '',
        service_font_color: '',
        user_font_color: '',
        coach_font_color: ''
      },
      ind: {
        service_btn_color: 0,
        service_font_color: 0,
        user_font_color: 0,
        coach_font_color: 0
      },
      subForm: {
        service_btn_color: '',
        service_font_color: '',
        user_font_color: '',
        user_image: [],
        coach_font_color: '',
        coach_image: [],
        attendant_name: ''
      },
      subFormRules: {
        service_btn_color: { required: true, type: 'string', message: '请选择按钮颜色', trigger: 'blur' },
        service_font_color: { required: true, type: 'string', message: '请选择文字颜色', trigger: 'blur' },
        user_font_color: { required: true, type: 'string', message: '请选择文字颜色', trigger: 'blur' },
        user_image: { required: true, type: 'array', message: '请选择背景图', trigger: 'blur' },
        coach_font_color: { required: true, type: 'string', message: '请选择文字颜色', trigger: 'blur' },
        coach_image: { required: true, type: 'array', message: '请选择背景图', trigger: 'blur' },
        attendant_name: { required: true, validator: this.$reg.isNotNull, text: '行业服务人员名称', reg_type: 2, trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    ...mapMutations(['changeRoutesItem']),
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      let { service_btn_color: sbtn, service_font_color: sfont, user_font_color: ufont, coach_font_color: cfont, user_image: uimg, coach_image: cimg } = data
      data.service_btn_color = !sbtn ? '#282B34' : sbtn
      data.service_font_color = !sfont ? '#EBDDB1' : sfont
      data.user_font_color = !ufont ? '#ffffff' : ufont
      data.coach_font_color = !cfont ? '#ffffff' : cfont
      data.user_image = [{ url: uimg || this.default_img }]
      data.coach_image = [{ url: cimg || this.default_img }]
      let sbind = this.colorList.findIndex(item => {
        return item === data.service_btn_color
      })
      let sfind = this.colorList.findIndex(item => {
        return item === data.service_font_color
      })
      let uind = this.colorList.findIndex(item => {
        return item === data.user_font_color
      })
      let cind = this.colorList.findIndex(item => {
        return item === data.coach_font_color
      })
      if (sbind === -1) {
        sbind = 9
      }
      if (sfind === -1) {
        sfind = 9
      }
      if (uind === -1) {
        uind = 9
      }
      if (cind === -1) {
        cind = 9
      }
      this.ind.service_btn_color = sbind
      this.ind.service_font_color = sfind
      this.ind.user_font_color = uind
      this.ind.coach_font_color = cind
      this.color.service_btn_color = data.service_btn_color
      this.color.service_font_color = data.service_font_color
      this.color.user_font_color = data.user_font_color
      this.color.coach_font_color = data.coach_font_color
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    getCover (img, key) {
      this.subForm[key] = img
    },
    async changeIndex (index, key) {
      this.ind[key] = index
      this.subForm[key] = this.colorList[index]
    },
    changeColor (e, key) {
      this.subForm[key] = e
    },
    toReset (key) {
      if (key === 'service') {
        this.ind[`${key}_btn_color`] = 9
        this.ind[`${key}_font_color`] = 9
        this.color[`${key}_btn_color`] = '#282B34'
        this.color[`${key}_font_color`] = '#EBDDB1'
        this.subForm[`${key}_btn_color`] = '#282B34'
        this.subForm[`${key}_font_color`] = '#EBDDB1'
        this.submitForm()
        return
      }
      this.ind[`${key}_font_color`] = 9
      this.color[`${key}_font_color`] = '#ffffff'
      this.subForm[`${key}_font_color`] = '#ffffff'
      this.subForm[`${key}_image`] = [{ url: this.default_img }]
      this.submitForm()
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          subForm.user_image = subForm.user_image[0].url
          subForm.coach_image = subForm.coach_image[0].url
          this.$api.system.configUpdate(subForm).then(res => {
            if (res.code === 200) {
              this.changeRoutesItem({ key: 'attendant_name', val: subForm.attendant_name })
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

    .color-item {
      width: 40px;
      height: 58px;
      margin-right: 5px;
      padding-top: 4px;
      border: 1px solid white;
      cursor: pointer;
      .primaryColor {
        width: 28px;
        height: 28px;
        border-radius: 3px;
        border: 1px solid #e6e6e6;
        .color-bg {
          width: 20px;
          height: 20px;
          border-radius: 3px;
        }
      }
      .iconfont {
        color: #429dff;
        margin-top: 5px;
      }
    }
    .color-item.active {
      border: 1px solid #429dff;
      border-radius: 3px;
      box-shadow: 0px 4px 12px 1px rgba(66, 157, 255, 0.15);
    }
  }
}
</style>
