<!--
 * @Description: 退款管理
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-05-06 16:08:10
 * @LastEditors: wen kun
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
          <el-form-item label="订单号" prop="order_code">
            <el-input
              v-model="searchForm.order_code"
              placeholder="请输入订单号查询"
            ></el-input>
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
        <el-table-column prop="name" label="服务类型"></el-table-column>
        <el-table-column prop="id" label="客户ID"></el-table-column>
        <el-table-column prop="nickName" label="客户昵称"></el-table-column>
        <el-table-column prop="coach_name" label="接单人"></el-table-column>
        <el-table-column prop="price" label="实付金额">
          <template slot-scope="scope">
            <div>¥{{ scope.row.price }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="price" label="退款金额">
          <template slot-scope="scope">
            <div>¥{{ scope.row.price }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="is_refund" label="状态">
          <template slot-scope="scope">
            <div v-if="scope.row.is_refund != 0">
              {{ statusType[scope.row.is_refund] }}
            </div>
          </template>
        </el-table-column>
        <el-table-column
          prop="order_code"
          width="150"
          label="订单号"
        ></el-table-column>
        <el-table-column label="操作" min-width="220" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                v-show="scope.row.is_refund !== 1"
                v-hasPermi="`${$route.name}-view`"
                @click="
                  $router.push(`/invitation/inviteDetail?id=${scope.row.id}`)
                "
                >{{ $t('action.view') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="toRefuse(scope.row.id, 2)"
                v-show="scope.row.is_refund === 1"
                v-hasPermi="`${$route.name}-rejectRefund`"
                >{{ $t('action.rejectRefund') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="success"
                v-show="scope.row.is_refund === 1"
                @click="toRefuse(scope.row.id, 1)"
                v-hasPermi="`${$route.name}-agreeRefund`"
                >{{ $t('action.agreeRefund') }}</lb-button
              >
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
      statusOptions: [
        {
          label: '全部',
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
        order_code: '',
        status: 0 // 状态 0全部 1待接单 2已接单 3已完成 4售后
      },
      tableData: [],
      total: 0,
      dialogRefund: false,
      refundId: '',
      refundMoney: '',
      refundTotalMoney: '',
      lockRefund: false
    }
  },
  created () {
    this.getTableDataList()
  },
  methods: {
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
      this.loading = true
      let { searchForm } = this
      let { code, data } = await this.$api.invitation.demandRefundOrder(searchForm)
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
      if (this.lockRefund) return
      let { refundId: id, refundMoney: price, refundTotalMoney } = this
      let param = { id, price, status: 1 }
      let reg = /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/
      if ((refundTotalMoney === 0 && price === 0) || (price > 0 && price <= refundTotalMoney && reg.test(price))) {
        let { code } = await this.$api.shop.passRefund(param)
        if (code !== 200) return
        this.$message.success(this.$t('tips.successSub'))
        this.dialogRefund = false
        this.refundMoney = ''
        this.getTableDataList()
        this.lockRefund = false
      } else {
        this.$message.error('请核对金额再提交！')
      }
    },
    /**
     * @method: 拒绝退款
     * @param {*} id
     */
    toRefuse (id, status) {
      this.$confirm(this.$t(status === 2 ? 'tips.confirmNoRefund' : 'tips.confirmAgreeRefund'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      })
        .then(() => {
          this.refuseRefund(id, status)
        })
        .catch(() => { })
    },
    async refuseRefund (id, status) {
      let { code } = await this.$api.invitation.demandRefund({ id, status })
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
  