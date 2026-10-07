<!--
 * @Descripttion: 订单管理
 * @Author: xiao li
 * @Date: 2021-03-12 18:02:16
 * @LastEditors: wen kun
 * @LastEditTime: 2024-07-11 11:50:37
-->
<template>
  <div class="lb-finance-stored-order">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="系统订单号" prop="order_code">
            <el-input
              v-model="searchForm.order_code"
              placeholder="请输入系统订单号"
            ></el-input>
          </el-form-item>
          <el-form-item label="日期" prop="start_time">
            <el-date-picker
              @change="getTableDataList(1)"
              v-model="searchForm.start_time"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              value-format="timestamp"
              :picker-options="pickerOptions"
              :default-time="['00:00:00', '23:59:59']"
            >
            </el-date-picker>
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
        <el-table-column prop="id" label="ID" fixed></el-table-column>
        <el-table-column prop="user_id" label="客户ID"></el-table-column>
        <el-table-column
          prop="nick_name"
          label="客户昵称"
          min-width="120"
        ></el-table-column>
        <el-table-column prop="coach_name" min-width="140">
          <template slot="header">
            <div>
              {{ `关联${$t('action.attendantName')}` }}
              <lb-tool-tips padding="3">{{
                `此订单是否有为某个${$t('action.attendantName')}充值`
              }}</lb-tool-tips>
            </div>
          </template>
          <template slot-scope="scope">
            {{ scope.row.coach_name || '-' }}
          </template>
        </el-table-column>
        <el-table-column
          prop="title"
          label="套餐名称"
          min-width="200"
        ></el-table-column>
        <el-table-column prop="pay_price" label="实际支付" min-width="120">
          <template slot-scope="scope"> ¥{{ scope.row.pay_price }} </template>
        </el-table-column>
        <el-table-column prop="true_price" label="充值金额" min-width="120">
          <template slot-scope="scope"> ¥{{ scope.row.true_price }} </template>
        </el-table-column>
        <el-table-column prop="comm_balance" label="返佣比例" min-width="110">
          <template slot="header">
            <div>
              返佣比例<lb-tool-tips padding="3"
                >{{
                  $t('action.attendantName')
                }}邀请用户储值下单成功后，获得的佣金比例</lb-tool-tips
              >
            </div>
          </template>

          <template slot-scope="scope" v-if="scope.row.coach_id">
            {{
              scope.row.comm_balance * 1 > 0
                ? `${scope.row.comm_balance}%`
                : '-'
            }}
          </template>
        </el-table-column>
        <el-table-column prop="comm_cash" label="储值佣金">
          <template slot-scope="scope" v-if="scope.row.coach_id">
            {{ scope.row.comm_cash * 1 > 0 ? `¥${scope.row.comm_cash}` : '-' }}
          </template>
        </el-table-column>
        <el-table-column
          prop="order_code"
          min-width="150"
          label="系统订单号"
        ></el-table-column>
        <el-table-column
          prop="transaction_id"
          min-width="150"
          label="付款订单号"
        ></el-table-column>
        <el-table-column prop="create_time" min-width="120" label="下单时间">
          <template slot-scope="scope">
            <div>{{ scope.row.create_time | handleTime(1) }}</div>
            <div>{{ scope.row.create_time | handleTime(2) }}</div>
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
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  data () {
    return {
      loading: false,
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      searchForm: {
        page: 1,
        limit: 10,
        start_time: '',
        end_time: '',
        order_code: ''
      },
      tableData: [],
      total: 0
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
    /**
     * @method 搜索订单列表
     */
    getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      this.$api.finance.orderList(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          let { data, total } = res.data
          this.tableData = data
          this.total = total
        }
      })
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
.lb-finance-stored-order {
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
