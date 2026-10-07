<!--
 * @Description: 退款详情
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-03-31 11:07:57
 * @LastEditors: xiao li
-->

<template>
  <div class="lb-shop-order-edit">
    <top-nav :isBack="true" />
    <div class="page-main">
      <lb-tips :isIcon="false">
        <div class="flex-y-center">
          {{ statusType[subForm.status] }}
          <div class="flex-y-center" v-if="subForm.status === 2">
            ，退款金额：¥{{ subForm.refund_price }}
            <div class="ml-md" v-if="subForm.car_price">
              含车费：¥{{ subForm.car_price }}
            </div>
          </div>
        </div>
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        label-width="130px"
        size="mini"
      >
        <lb-classify-title title="用户信息"></lb-classify-title>
        <el-form-item label="用户ID：">
          <div>{{ subForm.user_id }}</div>
        </el-form-item>
        <el-form-item label="姓名：">
          <div>{{ subForm.address_info.user_name }}</div>
        </el-form-item>
        <el-form-item label="手机号：">
          <div>{{ subForm.address_info.mobile }}</div>
        </el-form-item>
        <el-form-item label="车费详情：" v-if="!subForm.is_add">
          <div class="flex-y-center">
            {{ carType[subForm.car_type] }}
            <div class="ml-md" v-if="subForm.car_type == 1">
              全程{{ subForm.distance }}，车费¥{{ subForm.pay_car_price }}
            </div>
          </div>
        </el-form-item>
        <el-form-item label="服务地址：">
          <div>
            {{
              `${subForm.address_info.address}${subForm.address_info.address_info}`
            }}
          </div>
        </el-form-item>
        <lb-classify-title
          :title="`${$t('action.attendantName')}信息`"
        ></lb-classify-title>
        <el-form-item :label="`${$t('action.attendantName')}：`">
          <div>{{ subForm.coach_info.coach_name }}</div>
        </el-form-item>
        <el-form-item :label="`${$t('action.attendantName')}头像：`">
          <lb-cover
            :fileList="[{ url: subForm.coach_info.work_img }]"
            :isToDel="false"
            size="small"
            type="more"
            :fileSize="1"
          ></lb-cover>
        </el-form-item>
        <el-form-item label="联系电话：">
          <div>{{ subForm.coach_info.mobile }}</div>
        </el-form-item>
        <lb-classify-title title="订单信息"></lb-classify-title>
        <el-form-item label="付款订单号：">
          <div>{{ subForm.pay_order_code }}</div>
        </el-form-item>
        <el-form-item label="退款订单号：">
          <div>{{ subForm.order_code }}</div>
        </el-form-item>
        <el-form-item label="微信退款订单号：" v-if="subForm.out_refund_no">
          <div>{{ subForm.out_refund_no }}</div>
        </el-form-item>
        <el-form-item label="代理商：" v-if="subForm.admin_id">
          <div>{{ subForm.admin_name }}</div>
        </el-form-item>
        <el-form-item label="申请退款时间：">
          <div>{{ subForm.create_time }}</div>
        </el-form-item>
        <el-form-item label="审核时间：" v-if="subForm.status !== 1">
          <div>{{ subForm.refund_time }}</div>
        </el-form-item>
        <el-form-item label="退款原因：">
          <div>{{ subForm.text }}</div>
        </el-form-item>
        <el-form-item
          label="上传图片："
          v-if="subForm.imgs && subForm.imgs.length > 0"
        >
          <div class="flex-warp">
            <div
              class="mr-md"
              v-for="(item, index) in subForm.imgs"
              :key="index"
            >
              <lb-image :src="item" />
            </div>
          </div>
        </el-form-item>
        <el-form-item
          label="处理退款："
          v-show="
            (pagePermission.includes('agreeRefund') ||
              pagePermission.includes('rejectRefund')) &&
            subForm.status === 1
          "
        >
          <lb-button
            size="mini"
            plain
            type="danger"
            @click="toRefuse"
            v-hasPermi="
              subForm.is_add === 1
                ? `shopBellRefund-rejectRefund`
                : `shopRefund-rejectRefund`
            "
            >{{ $t('action.rejectRefund') }}</lb-button
          >
          <lb-button
            size="mini"
            plain
            type="success"
            @click="showRefundDialog"
            v-hasPermi="
              subForm.is_add === 1
                ? `shopBellRefund-agreeRefund`
                : `shopRefund-agreeRefund`
            "
            >{{ $t('action.agreeRefund') }}</lb-button
          >
        </el-form-item>
      </el-form>
      <lb-classify-title title="退款服务"></lb-classify-title>
      <el-table
        v-loading="loading"
        :data="subForm.order_goods"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="goods_cover" label="服务图片">
          <template slot-scope="scope">
            <lb-image :src="scope.row.goods_cover" />
          </template>
        </el-table-column>
        <el-table-column prop="goods_name" label="服务名称"></el-table-column>
        <el-table-column prop="time_long" label="服务时长">
          <template slot-scope="scope">
            {{ scope.row.time_long ? `${scope.row.time_long}分钟` : '' }}
          </template>
        </el-table-column>
        <el-table-column prop="num" label="服务数量">
          <template slot-scope="scope">
            {{ scope.row.num || 1 }}
          </template>
        </el-table-column>
      </el-table>
      <div class="space-lg mt-lg mb-lg">
        合计数量：{{ subForm.all_goods_num || 1 }}
      </div>
      <lb-button type="primary" @click="$router.back(-1)">{{
        $t('action.back')
      }}</lb-button>

      <el-dialog
        title="立即退款"
        :visible.sync="dialogRefund"
        width="400px"
        center
      >
        <div class="refund-inner">
          <lb-tips :isIcon="false">请核对信息后输入需要退款的金额</lb-tips>
          <el-input
            :disabled="refundTotalMoney * 1 === 0"
            v-model="refundMoney"
            placeholder="请输入退款金额"
            style="width: 100%"
          ></el-input>
          <p class="mt-lg">
            实际可退款金额
            <span class="c-warning">￥{{ refundTotalMoney }}</span>
          </p>
          <p>退款金额不能大于可退款金额</p>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogRefund = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button type="primary" @click="toPassRefund">确认退款</el-button>
        </span>
      </el-dialog>
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
      navTitle: '',
      pagePermission: [],
      carType: {
        0: '公交/地铁',
        1: '出租车'
      },
      statusType: {
        1: '退款申请中',
        2: '同意退款',
        3: '拒绝退款'
      },
      subForm: {
        id: 0
      },
      dialogRefund: false,
      refundMoney: '',
      lockRefund: false
    }
  },
  async created () {
    let { id } = this.$route.query
    await this.getDetail(id)
    this.routesItem.routes.map(item => {
      if (item.path === '/shop') {
        item.children.map(aitem => {
          if (aitem.name === this.subForm.is_add === 1 ? 'ShopBellRefund' : 'ShopRefund') {
            this.pagePermission = aitem.meta.pagePermission[0].auth
          }
        })
      }
    })
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    async getDetail (id) {
      let { code, data } = await this.$api.shop.refundOrderInfo({ id })
      if (code !== 200) return
      this.subForm = data
    },
    showRefundDialog () {
      let { apply_price: money } = this.subForm
      this.refundTotalMoney = money
      this.refundMoney = money
      this.dialogRefund = true
    },
    /**
     * @method: 同意退款
     */
    async toPassRefund () {
      if (this.lockRefund) return
      let { id, apply_price: refundTotalMoney } = this.subForm
      let { refundMoney: price } = this
      let param = { id, price, text: '' }
      let reg = /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/
      if ((refundTotalMoney === 0 && price === 0) || (price > 0 && price <= refundTotalMoney && reg.test(price))) {
        let { code } = await this.$api.shop.passRefund(param)
        if (code !== 200) return
        this.$message.success(this.$t('tips.successSub'))
        this.dialogRefund = false
        this.refundMoney = ''
        await this.getDetail(id)
        this.lockRefund = false
      } else {
        this.$message.error('请核对金额再提交！')
      }
    },
    /**
     * @method: 拒绝退款
     */
    toRefuse () {
      this.$confirm(this.$t('tips.confirmNoRefund'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      })
        .then(() => {
          this.refuseRefund()
        })
        .catch(() => { })
    },
    async refuseRefund () {
      let { id } = this.subForm
      let { code } = await this.$api.shop.noPassRefund({ id, text: '' })
      if (code !== 200) return
      this.$message.success(this.$t('tips.successOper'))
      await this.getDetail(id)
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
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-shop-order-edit {
  .el-form {
    .el-image {
      width: 120px;
      height: 120px;
    }
  }
  .el-textarea {
    width: 600px;
  }
}
</style>
