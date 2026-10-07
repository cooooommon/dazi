<!--
 * @Descripttion: 财务管理
 * @Author: xiao li
 * @Date: 2021-03-15 14:27:25
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-25 16:46:39
-->
<template>
  <div class="lb-finance">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item
            :label="`${$t('action.attendantName')}名称`"
            prop="coach_name"
          >
            <el-input
              v-model="searchForm.coach_name"
              :placeholder="`请输入${$t('action.attendantName')}名称`"
            ></el-input>
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
          prop="coach_name"
          :label="`${$t('action.attendantName')}名称`"
        ></el-table-column>
        <el-table-column prop="staff_text" label="收入（元）">
          <template slot-scope="scope">
            <div class="flex-column">
              <div class="c-success">+¥{{ scope.row.order_price }}</div>
              <div>{{ scope.row.order_count }}笔</div>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="total_price" label="提现（元）">
          <template slot-scope="scope">
            <div class="flex-column">
              <div class="c-warning">-¥{{ scope.row.total_price }}</div>
              <div>{{ scope.row.total_count }}笔</div>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="total_price" label="实际到账（元）">
          <template slot-scope="scope">
            <div class="flex-column">
              <div class="c-success">+¥{{ scope.row.wallet_price }}</div>
              <div>{{ scope.row.wallet_count }}笔</div>
            </div>
          </template>
        </el-table-column>
        <el-table-column
          prop="total_price"
          :label="`当前${balance_character || '余额'}`"
        >
          <template slot-scope="scope">
            <div class="flex-column">
              <div class="c-link">¥{{ scope.row.service_car_price }}</div>
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
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  data () {
    return {
      loading: false,
      downloadLoading: false,
      searchForm: {
        page: 1,
        limit: 10,
        coach_name: ''
      },
      storeList: [],
      tableData: [],
      total: 0,
      balance_character: ''
    }
  },
  created () {
    this.getFormInfo()
    this.getTableDataList()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.balance_character = data.balance_character
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
    /**
     * @method 搜索订单列表
     */
    getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      this.$api.finance.financeList(this.searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          res.data.data.map(item => {
            item.service_car_price = (item.service_price + item.car_price).toFixed(2)
          })
          this.tableData = res.data.data
          this.total = res.data.total
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
.lb-finance {
  .page-main {
    width: 100%;

    .el-select,
    .el-input-number,
    .el-input {
      width: 200px;
    }
    .el-table {
      .table-goods-info {
        width: 280px;
        height: 80px;
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        font-size: 12px;

        .goods-info-r {
          width: 100%;
          display: flex;
          flex-direction: column;
          justify-content: space-around;

          p {
            width: 210px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
          }

          .price {
            color: red;
          }
        }

        .el-image {
          min-width: 70px;
          height: 70px;
          margin-right: 10px;
        }

        &:last-child {
          margin-bottom: 0;
        }
      }
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
