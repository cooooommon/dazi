<!--
 * @Description: 订单详情
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-11-27 11:54:25
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-shop-order-edit">
    <top-nav :isBack="true" />
    <div class="page-main">
      <!-- <lb-tips :isIcon="false">
        <div class="flex-warp">
          <div>
            {{ statusType[subForm.pay_type] }}
          </div>
          <div
            class="f-desc c-warning ml-md"
            v-if="subForm.coach_refund_time * 1 > 0"
          >
            <div>陪玩官拒单，拒单原因：</div>
            <div class="mt-md" v-html="subForm.coach_refund_text"></div>
          </div>
        </div>
      </lb-tips> -->

      <!-- <div class="mg-lg">
        <el-timeline :reverse="reverse">
          <el-timeline-item
            v-for="(item, index) in lineList"
            :key="index"
            :color="subForm.pay_type > item.pay_type - 1 ? '#409EFF' : ''"
          >
            <div class="flex-y-center">
              {{ item.title }}
              <div
                class="ml-md f-caption c-caption"
                :class="[{ 'c-link': subForm.pay_type > item.pay_type - 1 }]"
              >
                {{ subForm.pay_type > item.pay_type - 1 ? '' : '状态未开始' }}
              </div>
            </div>
            <div class="mt-sm" v-if="subForm[item.time] * 1 > 0">
              {{ subForm[item.time] | handleTime }}
            </div>
            <div
              class="mt-md"
              v-if="
                (index === 2 && subForm.pay_type > 4) ||
                (index === 4 && subForm.pay_type === 7)
              "
            >
              <lb-cover
                :fileList="[
                  {
                    url:
                      index === 2 && subForm.pay_type > 4
                        ? subForm.arrive_img
                        : subForm.end_img
                  }
                ]"
                :isToDel="false"
                fileType="image"
                type="more"
                size="small"
                :fileSize="1"
              ></lb-cover>
            </div>

            <div
              class="f-caption c-title"
              v-if="
                (index === 2 && subForm.pay_type > 4) ||
                (index === 4 && subForm.pay_type === 7)
              "
            >
              {{
                index === 2 && subForm.pay_type > 4
                  ? subForm.arr_address
                  : subForm.end_address
              }}
            </div>
          </el-timeline-item>
        </el-timeline>
      </div> -->
      <el-form
        @submit.native.prevent
        :model="subForm"
        label-width="130px"
        size="mini"
      >
        <lb-classify-title title="用户信息"></lb-classify-title>
        <el-form-item label="用户ID：">
          <div>{{ subForm.id }}</div>
        </el-form-item>
        <el-form-item label="下单人：">
          <div>{{ subForm.nickName }}</div>
        </el-form-item>
        <el-form-item label="联系方式：">
          <div>{{ subForm.phone }}</div>
        </el-form-item>
        <el-form-item label="服务地址：">
          <div>
            {{ subForm.address }}
          </div>
        </el-form-item>
        <el-form-item label="订单备注：" v-if="subForm.text">
          <div>{{ subForm.text }}</div>
        </el-form-item>
        <!-- <lb-classify-title title="陪玩官信息"></lb-classify-title>
        <el-form-item label="陪玩官：">
          <div>{{ subForm.coach_name }}</div>
        </el-form-item>
        <el-form-item label="陪玩官头像：">
          <lb-image class="order-img" :src="subForm.avatarUrl" />
        </el-form-item> -->
        <lb-classify-title title="订单信息"></lb-classify-title>
        <el-form-item label="订单状态：">
          <div>{{ statusType[subForm.status] }}</div>
        </el-form-item>
        <el-form-item label="系统订单号：">
          <div>{{ subForm.order_code }}</div>
        </el-form-item>
        <el-form-item label="微信商户订单号：" v-if="subForm.transaction_id">
          <div>{{ subForm.transaction_id }}</div>
        </el-form-item>
        <el-form-item label="下单时间：">
          <div>{{ subForm.create_time }}</div>
        </el-form-item>
        <el-form-item label="服务时间：">
          <div>
            {{ subForm.start_time }} -
            {{ subForm.end_time }}
          </div>
        </el-form-item>
        <!-- <el-form-item label="邀约服务金额：">
          <div>￥{{ subForm.type_price }}</div>
        </el-form-item> -->
        <el-form-item
          label="会员卡折扣："
          prop="member_balance"
          v-if="subForm.member_status"
        >
          <div class="c-warning">
            {{ subForm.member_balance }}折（优惠¥{{ subForm.member_discount }}）
          </div>
        </el-form-item>
        <el-form-item label="实际支付金额：" prop="price">
          <div>￥{{ subForm.price }}</div>
        </el-form-item>
        <el-form-item label="接单人：">
          <div>{{ subForm.coach_name || '无' }}</div>
        </el-form-item>
        <el-form-item label="服务时长：">
          <div>{{ subForm | mathMinute }}分钟</div>
        </el-form-item>
        <el-form-item label="支付方式："
          ><div class="flex-y-baseline">
            <i
              class="iconfont mr-sm"
              :class="payType[subForm.pay_type].icon"
            ></i
            >{{ payType[subForm.pay_type].text }}
          </div>
        </el-form-item>
        <el-form-item label="车费：">
          <div>{{ subForm.is_car == 1 ? '报销' : '不报销' }}</div>
          <div class="f-desc c-warning" v-if="subForm.is_car == 1">
            {{ $t('action.attendantName') }}到达目的地后与客户自行报销
          </div>
        </el-form-item>
      </el-form>
      <lb-classify-title title="发布内容"></lb-classify-title>
      <div style="padding-left: 50px">
        <div style="white-space: pre-line">
          {{ subForm.content }}
        </div>
        <div class="space-lg"></div>
        <div v-if="subForm.img.length">
          <lb-cover
            :fileList="[
              {
                url: subForm.img
              }
            ]"
            :isToDel="false"
            fileType="image"
            type="more"
            size="big"
            :fileSize="subForm.img.length"
          ></lb-cover>
        </div>
      </div>
      <!-- <el-table
        v-loading="loading"
        :data="subForm.order_goods"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="goods_cover" label="服务项目">
          <template slot-scope="scope">
            <lb-image :src="scope.row.goods_cover" />
          </template>
        </el-table-column>
        <el-table-column prop="goods_name" label="服务名称"></el-table-column>
        <el-table-column prop="time_long" label="服务时长">
          <template slot-scope="scope">
            {{ scope.row.time_long }}分钟
          </template>
        </el-table-column>
        <el-table-column prop="num" label="服务数量">
          <template slot-scope="scope">
            <div class="flex-y-center">
              x{{ scope.row.num }}
              <div
                class="f-icontext c-warning ml-sm"
                v-if="scope.row.refund_num"
              >
                {{ `已退${scope.row.refund_num}` }}
              </div>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <div class="space-lg mt-lg mb-lg">
        合计数量：{{ subForm.all_goods_num }}
      </div> -->
      <lb-button type="primary" @click="$router.back(-1)">{{
        $t('action.back')
      }}</lb-button>
    </div>
  </div>
</template>
  
  <script>
import moment from 'moment'
export default {
  data () {
    return {
      loading: false,
      navTitle: '',
      carType: {
        0: '公交/地铁',
        1: '出租车'
      },
      payType: {
        1: {
          icon: 'iconzhifubao c-alipay',
          text: '支付宝'
        },
        2: {
          icon: 'iconweixinzhifu1 c-success',
          text: '微信支付'
        },
        3: {
          icon: 'iconqianbao c-warning',
          text: '账户余额'
        }
      },
      statusType: {
        '-2': '超时取消', '-1': '用户取消', 0: '审核拒绝', 1: '待审核', 2: '待接单', 3: '已接单', 4: '已完成'
      },
      lineList: [{
        pay_type: 3,
        title: this.$t('action.attendantName') + '接单',
        time: 'receiving_time'
      }, {
        pay_type: 4,
        title: this.$t('action.attendantName') + '出发',
        time: 'serout_time'
      }, {
        pay_type: 5,
        title: this.$t('action.attendantName') + '到达',
        time: 'arrive_time'
      }, {
        pay_type: 6,
        title: '开始服务',
        time: 'start_service_time'
      }, {
        pay_type: 7,
        title: '服务完成',
        time: 'order_end_time'
      }],
      subForm: {}
    }
  },
  created () {
    let { id } = this.$route.query
    this.getDetail(id)
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.payType[3].text = data.balance_character
    },
    async getDetail (id) {
      let { code, data } = await this.$api.invitation.demandInfo({ id })
      if (code !== 200) return
      data.is_balance = data.balance * 1 > 0 ? 1 : 0
      let { coach_refund_text: text } = data
      data.coach_refund_text = text && text.length > 0 ? text.replace(/\n/g, '<br>') : '没有填写原因哦'
      this.subForm = data
    }
  },
  // 监听器
  watch: {
    async $route (route) {
      let { id = 0 } = route.query
      if (id * 1 !== this.subForm.id) {
        this.getDetail(id)
      }
    }
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : type === 3 ? moment(val * 1000).format('YYYY-MM-DD HH:mm') : type === 4 ? moment(val * 1000).format('HH:mm') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    },
    mathMinute (val) {
      const date1 = new Date(val.start_time)
      const date2 = new Date(val.end_time)
      const s1 = date1.getTime()
      const s2 = date2.getTime()
      const Minute2 = Number((s2 - s1) / 60000)
      return Minute2
    }
  }
}
  </script>
  
  <style lang="scss" scoped>
.lb-shop-order-edit {
  .order-img {
    width: 120px;
    height: 120px;
  }
  .el-textarea {
    width: 600px;
  }
}
</style>
  