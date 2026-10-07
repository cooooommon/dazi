<!--
 * @Description: 
 * @Author: wen kun
 * @Date: 2023-12-06 11:09:40
 * @LastEditTime: 2023-12-09 15:54:15
 * @LastEditors: wen kun
-->
<template>
  <div class="lb-system-banner-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div class="page-main">
      <lb-tips :isIcon="false">
        <div>
          <div class="">{{ statusType[detail.status] }}</div>
          <div class="pt-sm pre-wrap" v-if="detail.refund_text">
            原因：{{ detail.refund_text }}
          </div>
        </div>
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        label-width="130px"
        size="mini"
      >
        <lb-classify-title title="订单信息"></lb-classify-title>
        <el-form-item label="系统订单号：" prop="order_code">
          <div>{{ detail.order_code }}</div>
        </el-form-item>
        <el-form-item label="退款订单号：" prop="refund_code">
          <div>{{ detail.refund_code }}</div>
        </el-form-item>
        <el-form-item label="手机号：" prop="mobile">
          <div>{{ detail.mobile }}</div>
        </el-form-item>
        <el-form-item label="下单人：" prop="nickName">
          <div>{{ detail.nickName }}</div>
        </el-form-item>
        <el-form-item label="申请退款时间：" prop="create_time">
          <div>{{ detail.create_time | handleTime }}</div>
        </el-form-item>
        <el-form-item label="审核时间：" prop="refund_time">
          <div>{{ detail.refund_time | handleTime }}</div>
        </el-form-item>
        <el-form-item label="退款原因：" prop="text" v-if="detail.text">
          <div>{{ detail.text }}</div>
        </el-form-item>
      </el-form>
      <lb-classify-title title="套餐信息"></lb-classify-title>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="cover" label="套餐内容">
          <template slot-scope="scope">
            <lb-image :src="scope.row.cover" />
          </template>
        </el-table-column>
        <el-table-column prop="name" label="套餐名称"> </el-table-column>
        <el-table-column prop="reservation_day" label="预约方式">
        </el-table-column>
        <el-table-column prop="price" label="价格"> </el-table-column>
        <el-table-column prop="num" label="数量"> </el-table-column>
        <el-table-column prop="end_time" label="到期时间">
          <template slot-scope="scope">
            <div>
              {{ scope.row.end_time | handleTime(1) }}
              {{ scope.row.end_time | handleTime(3) }}
            </div>
          </template>
        </el-table-column>
      </el-table>
      <div style="width: 500px" class="pt-lg pb-lg">
        <div class="flex-center">
          <span class="flex-1">券码</span>
          <span class="flex-1">状态</span>
        </div>
        <div
          class="pt-lg flex-center"
          v-for="(item, index) in detail.code_info"
          :key="index"
        >
          <span class="flex-1">{{ item.code_num }}</span>
          <span class="flex-1">{{ couponStatus[item.status] }}</span>
        </div>
      </div>
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        label-width="130px"
        size="mini"
      >
        <lb-classify-title title="门店信息"></lb-classify-title>
        <el-form-item label="门店名称：" prop="store_name">
          <div>{{ detail.store_name }}</div>
        </el-form-item>
        <el-form-item label="营业时间：" prop="trade_date">
          <div>{{ detail.trade_date }}</div>
        </el-form-item>
        <el-form-item label="门店地址：" prop="address">
          <div>{{ detail.address + detail.info }}</div>
        </el-form-item>
        <el-form-item label="商家电话：" prop="mobile">
          <div>{{ detail.mobile }}</div>
        </el-form-item>
        <lb-classify-title title="团购信息"></lb-classify-title>
        <div>
          <div class="pb-lg" v-for="(item, index) in detail.sku" :key="index">
            <div class="text-bold">· {{ item.name }}</div>
            <div
              class="pt-lg flex"
              v-for="(citem, cindex) in item.price"
              :key="cindex"
            >
              <span style="width: 300px">{{ citem.name }}</span>
              <span style="width: 100px">{{ citem.num }}份</span>
              <span style="width: 100px">￥{{ citem.price }}</span>
            </div>
          </div>
        </div>
        <lb-classify-title title="温馨提示"></lb-classify-title>
        <el-form-item label="有效期：" prop="start_time">
          <div>
            <span>{{ detail.start_time | handleTime(1) }}</span>
            <span>{{ detail.start_time | handleTime(3) }}</span>
            至
            <span>{{ detail.end_time | handleTime(1) }}</span>
            <span>{{ detail.end_time | handleTime(3) }}</span>
          </div>
        </el-form-item>
        <el-form-item label="使用时间：" prop="use_start_time">
          <div>
            {{
              `${detail.use_start_time} - ${
                (detail.use_end_time < detail.use_start_time ? `次日` : ``) +
                detail.use_end_time
              }`
            }}
          </div>
        </el-form-item>
        <el-form-item label="使用规则：" prop="rule_text">
          <div>{{ detail.rule_text }}</div>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<script>
import { mapState } from 'vuex'
import moment from 'moment'
export default {
  data () {
    return {
      loading: false,
      tableData: [],
      detail: {},
      payType: {
        1: '微信支付',
        2: '余额支付',
        3: '支付宝支付'
      },
      statusType: {
        1: '退款申请中',
        2: '同意退款',
        3: '拒绝退款'
      },
      couponStatus: {
        1: '退款中',
        2: '已退款',
        3: '退款失败'
      }
    }
  },
  async created () {
    let { id } = this.$route.query
    this.getOrderInfo(id)
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : type === 3 ? moment(val * 1000).format('HH:mm') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    }
  },
  methods: {
    async getOrderInfo (id) {
      let { code, data } = await this.$api.storeshop.refundInfo({ id })
      if (code !== 200) return
      this.tableData = [
        {
          name: data.name,
          reservation_day: data.reservation_day > 0 ? `提前${data.reservation_day}天预约` : `无需预约`,
          price: '￥' + data.price,
          num: data.num,
          end_time: data.end_time,
          cover: data.cover
        }
      ]
      let max = data.trade_week.substring(data.trade_week.length - 1)
      let min = data.trade_week.substring(0, 1)
      let week = ['周天', '周一', '周二', '周三', '周四', '周五', '周六']
      data.trade_date = `${week[min]}至${week[max]} ${data.store_start_time}-${data.store_end_time}`
      this.detail = data
    }
  }
}
</script>
<style lang="scss" scoped>
.lb-system-banner-edit {
  width: 100%;
  .el-form {
    width: 100%;
    .el-select,
    .el-input-number,
    .el-cascader,
    .el-input {
      width: 300px;
    }
    .el-textarea {
      width: 600px;
    }
    .el-tag {
      cursor: pointer;
    }
    .trade-week {
      width: 500px;
      padding: 0 10px 40px 10px;
    }
    .el-slider {
      white-space: nowrap;
    }
  }
}
</style>
