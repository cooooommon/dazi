<!--
 * @Description: 退款管理
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-04-05 12:09:33
 * @LastEditors: xiao li
-->

<template>
  <div class="lb-shop-refund">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="服务名称" prop="goods_name">
            <el-input
              v-model="searchForm.goods_name"
              placeholder="请输入服务名称"
            ></el-input>
          </el-form-item>
          <el-form-item label="订单号" prop="order_code">
            <el-input
              v-model="searchForm.order_code"
              placeholder="请输入付款/退款订单号"
            ></el-input>
          </el-form-item>
          <el-form-item label="代理商" prop="admin_id">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.admin_id"
              placeholder="请选择代理商"
              filterable
            >
              <el-option
                v-for="item in base_agent"
                :key="item.id"
                :label="item.agent_name"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="状态" prop="status">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.status"
              placeholder="请选择"
            >
              <el-option
                v-for="item in statusOptions"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <lb-button
              size="medium"
              type="primary"
              icon="el-icon-search"
              style="margin-right: 5px"
              @click="getTableDataList(1)"
              >{{ $t('action.search') }}</lb-button
            >
            <lb-button
              size="medium"
              icon="el-icon-refresh-left"
              style="margin-right: 5px"
              @click="resetForm('searchForm')"
              >{{ $t('action.reset') }}</lb-button
            >
          </el-form-item>
        </el-form>
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column
          prop="id"
          label="ID"
          width="80"
          fixed
        ></el-table-column>
        <el-table-column
          prop="goods_info_text"
          width="300"
          label="服务项目信息"
        >
          <template slot-scope="scope">
            <div
              class="pb-sm"
              v-for="(citem, cindex) in scope.row.order_goods"
              :key="cindex"
            >
              <div class="flex-center pt-md">
                <lb-image class="avatar radius-5" :src="citem.goods_cover" />
                <div
                  class="flex-1 f-caption c-caption ml-md"
                  style="width: 210px"
                >
                  <div class="flex-between">
                    <div
                      class="f-paragraph c-title ellipsis"
                      :class="[{ 'max-300': citem.refund_num > 0 }]"
                      style="line-height: 1.2; margin-bottom: 4px"
                    >
                      {{ citem.goods_name }}
                    </div>
                    <div
                      class="f-caption c-warning"
                      v-if="citem.refund_num > 0"
                    >
                      已退x{{ citem.refund_num }}
                    </div>
                  </div>
                  <div
                    class="f-caption"
                    style="line-height: 1.4"
                    v-if="citem.time_long * 1 > 0"
                  >
                    时长：{{ citem.time_long }} 分钟
                  </div>
                  <div class="flex-between f-caption mt-md">
                    <div class="c-warning">¥{{ citem.goods_price }}</div>
                    <div>x{{ citem.num || 1 }}</div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="user_name" label="下单人"></el-table-column>
        <el-table-column
          prop="coach_info.coach_name"
          :label="$t('action.attendantName')"
        ></el-table-column>
        <el-table-column prop="apply_price" label="申请退款金额" width="150">
          <template slot-scope="scope">
            <div>¥{{ scope.row.apply_price }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="refund_price" label="退款金额">
          <template slot-scope="scope">
            <div>¥{{ scope.row.refund_price }}</div>
          </template>
        </el-table-column>
        <el-table-column
          prop="pay_order_code"
          min-width="150"
          label="付款订单号"
        ></el-table-column>
        <el-table-column
          prop="order_code"
          min-width="150"
          label="退款订单号"
        ></el-table-column>
        <el-table-column
          prop="out_refund_no"
          min-width="150"
          label="微信退款订单号"
        ></el-table-column>
        <el-table-column
          prop="admin_name"
          min-width="150"
          label="代理商"
        ></el-table-column>
        <el-table-column
          prop="create_time"
          min-width="120"
          label="申请退款时间"
        >
          <template slot-scope="scope">
            <div>{{ scope.row.create_time | handleTime(1) }}</div>
            <div>{{ scope.row.create_time | handleTime(2) }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="status_text" label="状态">
          <template slot-scope="scope">
            {{ statusType[scope.row.status] }}
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="220" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="$router.push(`/shop/refund/detail?id=${scope.row.id}`)"
                v-hasPermi="`${$route.name}-view`"
                >{{ $t('action.view') }}</lb-button
              >
              <div v-if="scope.row.status === 1">
                <lb-button
                  size="mini"
                  plain
                  type="danger"
                  @click="toRefuse(scope.row.id)"
                  v-hasPermi="`${$route.name}-rejectRefund`"
                  >{{ $t('action.rejectRefund') }}</lb-button
                >
                <lb-button
                  size="mini"
                  plain
                  type="success"
                  @click="showRefundDialog(scope.row.id, scope.row.apply_price)"
                  v-hasPermi="`${$route.name}-agreeRefund`"
                  >{{ $t('action.agreeRefund') }}</lb-button
                >
              </div>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.page"
        :pageSize="searchForm.limit"
        :total="total"
        @handleSizeChange="handleSizeChange"
        @handleCurrentChange="handleCurrentChange"
      >
      </lb-page>

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
import moment from 'moment'
export default {
  data () {
    return {
      base_agent: [],
      statusOptions: [
        {
          label: '全部订单',
          value: 0
        },
        {
          label: '退款申请中',
          value: 1
        },
        {
          label: '同意退款',
          value: 2
        },
        {
          label: '拒绝退款',
          value: 3
        }
      ],
      statusType: {
        1: '退款申请中',
        2: '同意退款',
        3: '拒绝退款'
      },
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        goods_name: '',
        order_code: '',
        status: 0,
        admin_id: '',
        is_add: 1
      },
      tableData: [],
      total: 0,
      dialogRefund: false,
      refundId: '',
      refundMoney: '',
      refundTotalMoney: '',
      lockTap: false
    }
  },
  async created () {
    await this.getBaseInfo()
    this.getTableDataList()
  },
  methods: {
    async getBaseInfo () {
      let { code, data } = await this.$api.agent.adminSelect()
      if (code !== 200) return
      this.base_agent = data
    },
    resetForm (form) {
      this.$refs[form].resetFields()
      this.getTableDataList(1)
    },
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handleCurrentChange(1)
    },
    handleCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let { searchForm } = this
      let { code, data } = await this.$api.shop.refundOrderList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    showRefundDialog (id, money) {
      this.refundId = id
      this.refundTotalMoney = money
      this.refundMoney = money
      this.dialogRefund = true
    },
    /**
     * @method: 同意退款
     */
    async toPassRefund () {
      if (this.lockTap) return
      let { refundId: id, refundMoney: price, refundTotalMoney } = this
      let param = { id, price, text: '' }
      let reg = /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/
      if ((refundTotalMoney === 0 && price === 0) || (price > 0 && price <= refundTotalMoney && reg.test(price))) {
        this.lockTap = true
        let { code } = await this.$api.shop.passRefund(param)
        this.lockTap = false
        if (code !== 200) return
        this.$message.success(this.$t('tips.successSub'))
        this.dialogRefund = false
        this.refundMoney = ''
        this.getTableDataList()
      } else {
        this.$message.error('请核对金额再提交！')
      }
    },
    /**
     * @method: 拒绝退款
     * @param {*} id
     */
    toRefuse (id) {
      this.$confirm(this.$t('tips.confirmNoRefund'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      })
        .then(() => {
          this.refuseRefund(id)
        })
        .catch(() => { })
    },
    async refuseRefund (id) {
      let { code } = await this.$api.shop.noPassRefund({ id, text: '' })
      if (code !== 200) return
      this.$message.success(this.$t('tips.successOper'))
      this.getTableDataList()
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
.lb-shop-refund {
  .page-main {
    width: 100%;

    .el-select,
    .el-input-number,
    .el-input {
      width: 200px;
    }
  }
}

.none {
  display: none;
}
</style>
<style media="print">
.none {
  display: block;
}
</style>
