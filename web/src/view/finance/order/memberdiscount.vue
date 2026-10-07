<!--
 * @Descripttion: 会员卡订单
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2021-03-12 18:02:16
 * @LastEditors: wen kun
 * @LastEditTime: 2024-11-27 19:05:01
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
          <el-form-item label="下单时间" prop="start_time">
            <el-date-picker
              @change="getTableDataList(1)"
              v-model="searchForm.start_time"
              type="datetimerange"
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
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="user_id" label="客户ID"></el-table-column>
        <el-table-column prop="nickName" label="客户昵称"></el-table-column>
        <el-table-column prop="share_nickName" label="推荐人"></el-table-column>
        <el-table-column
          prop="title"
          label="购买套餐"
          min-width="120"
        ></el-table-column>
        <el-table-column prop="price" label="购买价格">
          <template slot-scope="scope"> ¥{{ scope.row.price }} </template>
        </el-table-column>
        <el-table-column prop="start_time" min-width="120" label="会员生效时间">
          <template slot-scope="scope">
            <div>{{ scope.row.start_time | handleTime(1) }}</div>
            <div>{{ scope.row.start_time | handleTime(2) }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="end_time" min-width="120" label="会员过期时间">
          <template slot-scope="scope">
            <div>{{ scope.row.end_time | handleTime(1) }}</div>
            <div>{{ scope.row.end_time | handleTime(2) }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="pay_model" label="支付方式" min-width="140">
          <template slot-scope="scope">
            <el-tag size="small" :type="payType[scope.row.pay_model].type">{{
              payType[scope.row.pay_model].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="share_cash" label="返佣金额">
          <template slot-scope="scope"> ¥{{ scope.row.share_cash }} </template>
        </el-table-column>
        <el-table-column
          prop="order_code"
          min-width="130"
          label="系统订单号"
        ></el-table-column>
        <el-table-column
          prop="transaction_id"
          min-width="130"
          label="付款订单号"
        ></el-table-column>
        <el-table-column prop="pay_time" min-width="120" label="下单时间">
          <template slot-scope="scope">
            <div>{{ scope.row.pay_time | handleTime(1) }}</div>
            <div>{{ scope.row.pay_time | handleTime(2) }}</div>
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
      payType: {
        1: { type: 'primary', text: '微信支付' },
        2: { type: 'warning', text: '余额支付' },
        3: { type: 'success', text: '支付宝支付' },
        4: { type: 'danger', text: '折扣卡支付' }
      },
      searchForm: {
        page: 1,
        limit: 10,
        start_time: '',
        end_time: '',
        order_code: ''
      },
      tableData: [],
      total: 0,
      downloadLoading: false
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
    getTableDataList (flag) {
      if (flag) this.searchForm.page = flag
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 1) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      this.$api.memberdiscount.cardOrderList(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          let { data, total } = res.data
          this.tableData = data
          this.total = total
        }
      })
    },
    /**
     * @name: 导出数据
     */
    toExportExcel () {
      let { total } = this
      if (total > 10000) {
        this.$message.error(`最多只能导出10000条数据，当前${total}条，请筛选数据点击搜索后再操作导出数据！`)
        return
      }
      this.downloadLoading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 1) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      let url = this.$util.getProCurrentHref()
      let keywords = url.indexOf('?') > 0 ? '' : '?'
      let flag = url.indexOf('?') > 0
      Object.getOwnPropertyNames(searchForm).forEach((keys, value) => {
        keywords += flag
          ? `&${keys}=${searchForm[keys]}`
          : `${keys}=${searchForm[keys]}`
        flag = true
      })
      let token = window.localStorage.getItem('massage_minitk')
      let dwonlaodUrl = `${url}/massage/admin/AdminExcel/balanceOrderList${keywords}&token=${token}`
      window.location.href = dwonlaodUrl
      setTimeout(() => {
        this.downloadLoading = false
      }, 5000)
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

    .class-menu-list {
      .item-child {
        width: 25%;
      }
    }
    .item-icon {
      width: 50px;
      height: 50px;
      .iconfont {
        font-size: 25px;
      }
    }
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
