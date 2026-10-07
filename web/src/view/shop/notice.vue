<!--
 * @Description: 订单通知
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-07-01 17:16:30
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-shop-notice">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="通知类型" prop="type">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.type"
              placeholder="请选择"
            >
              <el-option
                v-for="item in typeOptions"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="阅读状态" prop="have_look">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.have_look"
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
        </el-form>
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column
          prop="order_id"
          label="订单ID"
          width="120"
        ></el-table-column>
        <el-table-column prop="type" label="消息内容">
          <template slot-scope="scope">
            <div class="flex-y-center">
              <!-- {{
                scope.row.type === 1
                  ? '你有一笔新的订单，请及时联系向导处理'
                  : '你有一笔新的退款订单，请及时处理'
              }} -->
              {{ noticeTypeText[scope.row.type] }}
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="推送时间"> </el-table-column>
        <el-table-column prop="have_look" label="阅读状态">
          <template slot-scope="scope">
            {{ statusType[scope.row.have_look] }}
          </template>
        </el-table-column>
        <el-table-column min-width="160" label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="
                  updateItem(scope.row.id, 1, false),
                    $router.push(
                      scope.row.type === 1
                        ? `/shop/order/detail?id=${scope.row.order_id}`
                        : `/shop/refund/detail?id=${scope.row.order_id}`
                    )
                "
                v-if="
                  scope.row.type === 1
                    ? (scope.row.is_add === 0 && routesItem.ShopOrder) ||
                      (scope.row.is_add === 1 && routesItem.ShopBellOrder)
                    : (scope.row.is_add === 0 && routesItem.ShopRefund) ||
                      (scope.row.is_add === 1 && routesItem.ShopBellRefund)
                "
                >{{ $t('action.view') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id)"
                v-show="scope.row.have_look === 1"
                v-hasPermi="`${$route.name}-delete`"
                >{{ $t('action.delete') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="success"
                @click="updateItem(scope.row.id, 1)"
                v-show="scope.row.have_look === 0"
                v-hasPermi="`${$route.name}-read`"
                >阅读</lb-button
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
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import moment from 'moment'
export default {
  data () {
    return {
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      noticeTypeText: {
        1: `你有一笔新的订单，请及时联系${this.$t('action.attendantName')}处理`,
        2: `你有一笔新的退款订单，请及时处理`,
        3: `您有一笔新的订单，已被${this.$t('action.attendantName')}拒绝，请在后台及时处理转单，避免平台订单流失`,
        4: `你有一笔新的订单，${this.$t('action.attendantName')}长时间未接单，请联系${this.$t('action.attendantName')}及时接单`,
        5: `你有一笔新的订单有迟到风险，请跟进${this.$t('action.attendantName')}是否达到目的地`,
        6: `监测到有${this.$t('action.attendantName')}完成服务后，未离开目的地，请联系${this.$t('action.attendantName')}询问具体情况，如遇安全问题，请及时报警，如是跳单情况，根据平台规则自行处理`
      },
      typeOptions: [
        {
          label: '全部',
          value: 0
        }
      ],
      statusOptions: [
        {
          label: '全部',
          value: -1
        },
        {
          label: '未读',
          value: 0
        },
        {
          label: '已读',
          value: 1
        }
      ],
      statusType: {
        0: '未读',
        1: '已读'
      },
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        start_time: '',
        end_time: '',
        type: 0,
        have_look: -1,
        types: ''
      },
      tableData: [],
      total: 0
    }
  },
  activated () {
    // ShopOrder 服务订单  ShopRefuseOrder 拒单  ShopRefund 服务退款
    let { ShopOrderPage, ShopRefuseOrderPage, ShopRefundPage } = this.routesItem
    if (ShopRefundPage) {
      this.typeOptions.push({
        label: '退款订单',
        value: 2
      })
      this.searchForm.types += this.searchForm.types ? ',2' : '2'
    }
    if (ShopRefuseOrderPage) {
      this.typeOptions.push({
        label: '拒单通知',
        value: 3
      })
      this.searchForm.types += this.searchForm.types ? ',3' : '3'
    }
    if (ShopOrderPage) {
      this.typeOptions.splice(1, 0, {
        label: '订单',
        value: 1
      })
      this.typeOptions.push({
        label: '未接单通知',
        value: 4
      }, {
        label: '迟到通知',
        value: 5
      }, {
        label: '跳单通知',
        value: 6
      })
      this.searchForm.types += this.searchForm.types ? ',1,4,5,6' : '1,4,5,6'
    }
    this.getTableDataList()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    ...mapMutations(['changeRoutesItem']),
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
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      if (searchForm.have_look === -1) {
        delete searchForm.have_look
      }

      let { code, data } = await this.$api.shop.noticeList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    confirmDel (id) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      })
        .then(() => {
          this.updateItem(id, -1)
        })
        .catch(() => { })
    },
    async updateItem (id, status, show = true) {
      this.$api.shop.noticeUpdate({ id, have_look: status }).then((res) => {
        if (res.code === 200) {
          if (status === 1) {
            this.changeRoutesItem({ key: 'notice_num', val: this.routesItem.notice_num > 0 ? this.routesItem.notice_num - 1 : 0 })
          }
          if (show) {
            this.$message.success(
              this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper')
            )
          }
          if (status === -1) {
            this.searchForm.page =
              this.searchForm.page <
                Math.ceil((this.total - 1) / this.searchForm.limit)
                ? this.searchForm.page
                : Math.ceil((this.total - 1) / this.searchForm.limit)
          }
          this.getTableDataList()
        } else {
          if (status === -1) return
          this.getTableDataList()
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
.lb-shop-notice {
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
