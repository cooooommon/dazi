<!--
 * @Descripttion: app设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2024-07-01 11:46:30
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
        label-width="170px"
        class="config-form"
      >
        <lb-classify-title
          v-if="cash_type == 1"
          title="提成设置"
        ></lb-classify-title>
        <el-form-item
          :label="`${$t('action.attendantName')}提成计算方式`"
          prop="is_current"
          v-if="cash_type == 1"
        >
          <el-radio-group v-model="subForm.is_current">
            <div class="pt-md pb-md">
              <el-radio :label="1"
                >周期结束后，等级和维度数据清零
                <lb-tool-tips :padding="0"
                  >{{
                    $t('action.attendantName')
                  }}在设置的周期时间内可逐步升级，升级后的每一笔订单按照等级后的等级提成计算
                  <div class="mt-sm">
                    周期结束后，{{
                      $t('action.attendantName')
                    }}在上一周期内累积的所有业绩，服务时长、在线时长、最低业绩、续单率、积分以及达到的等级全部清零
                  </div></lb-tool-tips
                ></el-radio
              >
            </div>
            <el-radio :label="2"
              >周期结束后，只清零维度数据，等级按照上期核算最高等级计算
              <lb-tool-tips :padding="0"
                >{{
                  $t('action.attendantName')
                }}在本周期（T周期）累积的所有业绩，服务时长、在线时长、最低业绩、续单率、积分，
                <div class="mt-sm mb-sm">
                  在周期结束时将5个维度数据清零，同时核算{{
                    $t('action.attendantName')
                  }}的5个维度数据满足哪个等级，以最高等级记录，
                </div>
                再下一周期（T+1）按照该等级提成比例计算每一笔订单</lb-tool-tips
              >
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <!-- <el-form-item
          label="推荐向导样式"
          prop="recommend_style"
          v-if="routesItem.auth.recommend"
        >
          <el-radio-group v-model="subForm.recommend_style">
            <el-radio :label="1">样式一</el-radio>
            <el-radio :label="2">样式二</el-radio>
          </el-radio-group>

          <el-button
            @click="toShowDialog('recommend')"
            type="primary"
            size="mini"
            plain
            style="margin-left: 20px"
            >查看示例</el-button
          >
        </el-form-item>
        <el-form-item label="向导列表页样式" prop="coach_format">
          <el-radio-group v-model="subForm.coach_format">
            <el-radio :label="1">样式一</el-radio>
            <el-radio :label="2">样式二</el-radio>
          </el-radio-group>

          <el-button
            @click="toShowDialog('format')"
            type="primary"
            size="mini"
            plain
            class="show-btn"
            style="margin-left: 20px"
            >查看示例</el-button
          >
        </el-form-item> -->
        <el-form-item
          :label="`显示${$t('action.attendantName')}提成比例`"
          prop="coach_level_show"
          v-if="cash_type == 1"
        >
          <el-radio-group v-model="subForm.coach_level_show">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >开启之后，{{ $t('action.attendantName') }}端等级管理页面显示{{
              $t('action.attendantName')
            }}的提成比例，关闭则不显示</lb-tool-tips
          >
        </el-form-item>
        <lb-classify-title title="提醒设置"></lb-classify-title>
        <el-form-item label="订单超时" prop="coach_receiving_minute">
          用户下单后，{{ $t('action.attendantName') }}超过
          <el-input-number
            class="lb-input-number"
            v-model="subForm.coach_receiving_minute"
            placeholder="请输入"
            style="width: 150px"
            :max="9999999999"
            :min="0"
            :controls="false"
          ></el-input-number>
          分钟未接单，提醒平台和关联代理商
        </el-form-item>
        <el-form-item label="跳单提醒" prop="jump_order_minute">
          向导完成订单后
          <el-input-number
            class="lb-input-number"
            v-model="subForm.jump_order_minute"
            placeholder="请输入"
            style="width: 150px"
            :max="9999999999"
            :min="0"
            :controls="false"
            :precision="0"
          ></el-input-number>
          分钟未离开目的地
          <el-input-number
            class="lb-input-number"
            v-model="subForm.jump_order_distance"
            placeholder="请输入"
            style="width: 150px"
            :max="9999999999"
            :min="0"
            :controls="false"
            :precision="2"
          ></el-input-number>
          公里，提醒平台和关联代理商
        </el-form-item>
        <el-form-item label="迟到提醒" prop="service_lat_minute">
          <el-radio-group v-model="subForm.service_lat_type">
            <el-radio :label="0">服务开始前</el-radio>
            <el-radio :label="1">服务开始后</el-radio>
          </el-radio-group>
          <div class="pt-md">
            <el-input-number
              class="lb-input-number"
              v-model="subForm.service_lat_minute"
              placeholder="请输入"
              style="width: 150px"
              :max="9999999999"
              :min="0"
              :precision="0"
              :controls="false"
            ></el-input-number>
            分钟，向导未点达到目的地，提醒平台和关联代理商
          </div>
        </el-form-item>
        <lb-classify-title title="其他设置"></lb-classify-title>
        <el-form-item
          :label="`${$t('action.attendantName')}评分权重设置`"
          prop="comment_ratio"
        >
          颜值占比
          <el-input-number
            class="lb-input-number"
            v-model="subForm.appearance"
            placeholder="请输入"
            style="width: 150px"
            :max="100"
            :min="0"
            :precision="0"
            :controls="false"
          ></el-input-number>
          % + 服务态度
          <el-input-number
            class="lb-input-number"
            v-model="subForm.service"
            placeholder="请输入"
            style="width: 150px"
            :max="100"
            :min="0"
            :precision="0"
            :controls="false"
          ></el-input-number>
          % + 响应速度
          <el-input-number
            class="lb-input-number"
            v-model="subForm.response"
            placeholder="请输入"
            style="width: 150px"
            :max="100"
            :min="0"
            :precision="0"
            :controls="false"
          ></el-input-number>
          %
          <lb-tool-tips
            >系统根据颜值、服务态度和响应速度的占比来综合得出评分，
            <p>
              这3个维度的占比合计100%；例如设置颜值40%、服务态度30%则响应速度最后为30%，
            </p>
            <p>
              每个维度的分值是有每个订单的评分之和除以总订单数量得出每个维度的平均分值，
            </p>
            <p>
              例如通过计算后，颜值的平均分值为4.4，服务态度的平均值4.5，{{
                $t('action.attendantName')
              }}的响应速度平均值为4.9，
            </p>
            则该{{
              $t('action.attendantName')
            }}的综合评分=4.4*40%+4.5*30%+4.9*30%=4.58，评分显示只保留小数点后一位，四舍五入后，综合值为4.6分</lb-tool-tips
          >
        </el-form-item>
        <el-form-item
          :label="`${$t('action.attendantName')}个人视频`"
          prop="video_limit"
        >
          <el-radio-group v-model="subForm.video_limit">
            <el-radio :label="1">必填</el-radio>
            <el-radio :label="0">非必填</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >设置非必填之后，{{
              $t('action.attendantName')
            }}在手机端入驻时，<br />将不需要强制上传视频，后台编辑资料时也不用强制上传视频。</lb-tool-tips
          >
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
        </el-form-item>
      </el-form>
    </div>

    <el-dialog
      title="查看示例"
      :visible.sync="showDialog"
      :append-to-body="true"
      width="700px"
      center
      top="5vh"
    >
      <div class="flex-between">
        <div v-for="(item, index) in list[dialogType]" :key="index">
          <div class="flex-center pd-lg f-paragraph c-title">
            {{ item.title }}
          </div>
          <img class="look-image" :src="item.img" />
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog = false">知道了</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  data () {
    let commentRatio = (rule, value, callback) => {
      let {
        appearance = 0,
        service = 0,
        response = 0
      } = this.subForm
      if (!appearance || !service || !response) {
        callback(new Error('请输入'))
      } else if (parseInt(appearance) + parseInt(service) + parseInt(response) !== 100) {
        callback(new Error('输入的比例相加必须等于100'))
      } else {
        callback()
      }
    }
    let jumpSingle = (rule, value, callback) => {
      let {
        jump_order_minute: minute = '',
        jump_order_distance: distance = ''
      } = this.subForm
      if (distance === '' || minute === '') {
        callback(new Error('请输入'))
      } else {
        callback()
      }
    }
    return {
      list: {
        recommend: [{ title: '样式一', img: 'https://lbqny.migugu.com/admin/anmo/technician/recommend_1_1.jpeg' }, { title: '样式二', img: 'https://lbqny.migugu.com/admin/anmo/technician/recommend_2_1.jpeg' }],
        format: [{ title: '样式一', img: 'https://lbqny.migugu.com/admin/anmo/technician/list_1.jpeg' }, { title: '样式二', img: 'https://lbqny.migugu.com/admin/anmo/technician/list_2.jpeg' }]
      },
      showDialog: false,
      dialogType: '',
      subForm: {
        is_current: 1,
        recommend_style: 1,
        coach_level_show: 1,
        coach_format: 1,
        coach_receiving_minute: '',
        comment_ratio: 0,
        coach_comment_ratio: '',
        appearance: 100,
        service: 0,
        response: 0,
        video_limit: 1,
        jump_order_distance: '',
        jump_order_minute: '',
        service_lat_type: '',
        service_lat_minute: ''
      },
      subFormRules: {
        coach_level_show: { required: true, type: 'number', message: `显示${this.$t('action.attendantName')}提成比例`, trigger: 'blur' },
        coach_receiving_minute: { required: true, type: 'number', message: '请输入', trigger: 'blur' },
        comment_ratio: { required: true, validator: commentRatio, trigger: 'blur' },
        video_limit: { required: true, type: 'number', message: `请选择${this.$t('action.attendantName')}个人视频`, trigger: 'blur' },
        service_lat_minute: { required: true, type: 'number', message: '请输入', trigger: 'blur' },
        jump_order_minute: { required: true, validator: jumpSingle, trigger: 'blur' },
      },
      cash_type: 2
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
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      let [appearance, service, response] = data.coach_comment_ratio.split(',')
      data.appearance = appearance
      data.service = service
      data.response = response

      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      this.cash_type = data.cash_type
    },
    toShowDialog (type) {
      this.dialogType = type
      this.showDialog = true
    },
    async submitForm () {
      let flag = false
      this.subForm.comment_ratio = parseInt(this.subForm.appearance) + parseInt(this.subForm.service) + parseInt(this.subForm.response)
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          flag = true
        }
      })
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      subForm.coach_comment_ratio = subForm.appearance + ',' + subForm.service + ',' + subForm.response
      delete subForm.comment_ratio
      delete subForm.appearance
      delete subForm.service
      delete subForm.response
      if (flag) {
        await this.$api.system.configUpdate(subForm)
        this.$message.success(this.$t('tips.successSub'))
      }
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
.look-image {
  width: 297px;
  height: 660px;
  border-radius: 10px;
  border: 1px solid #eee;
}
</style>
