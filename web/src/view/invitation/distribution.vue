<!--
 * @Description: 佣金信息
 * @Author: xiao li
 * @Date: 2022-07-14 09:50:19
 * @LastEditTime: 2023-12-25 16:47:02
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
          <el-form-item label="佣金获得者" prop="top_name">
            <el-input
              v-model="searchForm.top_name"
              placeholder="请输入佣金获得者姓名"
            ></el-input>
          </el-form-item>
          <!-- <el-form-item label="系统订单号" prop="order_code">
            <el-input
              v-model="searchForm.order_code"
              placeholder="请输入系统订单号"
            ></el-input>
          </el-form-item> -->
          <!-- <el-form-item label="佣金类型" prop="type">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.type"
              placeholder="请选择"
            >
              <el-option
                v-for="item in cashTypeList"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item> -->
          <el-form-item label="状态" prop="status">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.status"
              placeholder="请选择"
            >
              <el-option
                v-for="item in statusTypeList"
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
          min-width="80"
          fixed
        ></el-table-column>
        <el-table-column prop="top_name" label="佣金获得者" min-width="120">
          <template slot-scope="scope">
            {{
              scope.row.type == 13 ? scope.row.coach_name : scope.row.top_name
            }}
          </template>
        </el-table-column>
        <el-table-column
          prop="nickName"
          label="来源"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="order_code"
          width="150"
          label="系统订单号"
        ></el-table-column>
        <el-table-column
          prop="transaction_id"
          width="150"
          label="商户订单号"
        ></el-table-column>
        <el-table-column prop="type" label="类型" min-width="120">
          <template slot-scope="scope">
            <div v-if="scope.row.type == 13">
              {{ $t('action.attendantName') }}
            </div>
            <div class="flex-y-center" v-else>
              <div>
                {{
                  [2, 5, 6].includes(scope.row.type)
                    ? cityType[scope.row.city_type]
                    : ''
                }}
              </div>
              <div>{{ typeText[scope.row.type] }}</div>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            {{ statusType[scope.row.status] }}
          </template>
        </el-table-column>
        <el-table-column prop="order_goods" min-width="300" label="提成比例">
          <template slot-scope="scope">
            <div v-if="scope.row.type === 1 && scope.row.order_type == 1">
              <div
                class="pb-sm"
                v-for="(citem, cindex) in scope.row.order_goods"
                :key="cindex"
                style="width: 250px"
              >
                <div class="flex-center pt-md">
                  <lb-image class="avatar radius-5" :src="citem.goods_cover" />
                  <div
                    class="flex-1 f-caption c-caption ml-md"
                    style="width: 180px"
                  >
                    <div class="flex-between">
                      <div
                        class="f-paragraph c-title ellipsis"
                        style="line-height: 1.2; margin-bottom: 4px"
                      >
                        {{ citem.goods_name }}
                      </div>
                    </div>
                    <div class="flex-y-center">
                      提成比例
                      <div class="c-warning ml-sm">{{ citem.balance }}%</div>
                    </div>
                    <div class="flex-between f-caption">
                      <div class="c-warning">¥{{ citem.true_price }}</div>
                      <div>x{{ citem.num }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else-if="scope.row.type === 8">-</div>
            <div v-else>{{ scope.row.balance }}%</div>
          </template>
        </el-table-column>
        <el-table-column prop="pay_price" label="订单总金额" min-width="150">
          <template slot-scope="scope"> ¥{{ scope.row.pay_price }}</template>
        </el-table-column>
        <!-- <el-table-column prop="refund_price" label="退款金额" min-width="150">
          <template slot-scope="scope"> ¥{{ scope.row.refund_price }}</template>
        </el-table-column> -->
        <el-table-column prop="cash" label="此单提成金额" min-width="120">
          <template slot-scope="scope"> ¥{{ scope.row.cash }}</template>
        </el-table-column>
        <el-table-column prop="create_time" min-width="120" label="时间">
          <template slot-scope="scope">
            <div>{{ scope.row.create_time | handleTime(1) }}</div>
            <div>{{ scope.row.create_time | handleTime(2) }}</div>
          </template>
        </el-table-column>
        <!-- <el-table-column label="操作" min-width="120">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                :disabled="scope.row.cash_status === 1"
                size="mini"
                plain
                :type="scope.row.cash_status === 0 ? 'primary' : 'info'"
                @click="
                  scope.row.cash_status === 0
                    ? confirmCashOut(scope.row.id)
                    : ''
                "
                v-if="scope.row.coach_cash_control === 1"
                >{{
                  scope.row.cash_status === 0 ? '向导转账' : '已转账'
                }}</lb-button
              >
            </div>
          </template>
        </el-table-column> -->
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
      userInfo: JSON.parse(window.localStorage.getItem('massage_userInfo')),
      statusTypeList: [
        {
          label: '全部',
          value: 0
        },
        {
          label: '未入账',
          value: 1
        },
        {
          label: '已入账',
          value: 2
        }
      ],
      cashTypeList: [
        {
          label: '全部',
          value: 0
        },
        {
          label: '分销商',
          value: 1
        },
        {
          label: '代理商',
          value: 2
        },
        {
          label: this.$t('action.attendantName'),
          value: 3
        },
        {
          label: '车费',
          value: 8
        }
      ],
      statusType: {
        1: '未入账',
        2: '已入账'
      },
      cityType: {
        1: '城市',
        2: '区县',
        3: '省'
      },
      typeText: {
        1: '分销商',
        2: '代理商',
        3: this.$t('action.attendantName'),
        4: '分销商',
        5: '代理商',
        6: '代理商'
      },
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        order_code: '',
        top_name: '',
        type: 0,
        status: 0,
        order_type: 2
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
      this.tableData = []
      this.loading = true
      let { searchForm } = this
      let { code, data } = await this.$api.shop.commList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    confirmCashOut (id) {
      this.$confirm(`点击按钮之后，表示该笔提成已经结算给线下${this.$t('action.attendantName')}，确认已结算了吗？`, this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id)
      }).catch(() => { })
    },
    async updateItem (id) {
      this.$api.shop.adminUpdateCoachCommisson({ id }).then((res) => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successOper'))
        }
        this.getTableDataList()
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
