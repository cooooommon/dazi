<!--
 * @Descripttion: 提现申请
 * @Author: xiao li
 * @Date: 2021-03-15 14:28:04
 * @LastEditors: wen kun
 * @LastEditTime: 2024-02-27 18:01:50
-->
<template>
  <div class="lb-finance-record">
    <top-nav></top-nav>
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
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
          <el-form-item label="类型" prop="type">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.type"
              placeholder="请选择"
            >
              <el-option
                v-for="(item, index) in typeListOptions"
                :key="index"
                :label="item.name"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="提现号" prop="code">
            <el-input
              v-model="searchForm.code"
              placeholder="请输入提现号"
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
        <el-table-column prop="id" label="ID" fixed></el-table-column>
        <el-table-column prop="coach_name" label="姓名"></el-table-column>
        <el-table-column
          prop="code"
          label="提现号"
          width="150"
        ></el-table-column>
        <el-table-column prop="text" label="备注" width="200"></el-table-column>
        <el-table-column prop="apply_price" label="申请金额">
          <template slot-scope="scope">
            <span>¥{{ scope.row.apply_price }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="true_price" label="到账金额" min-width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.status === 2">{{
              `¥${scope.row.true_price}`
            }}</span>
          </template>
        </el-table-column>
        <el-table-column
          prop="apply_transfer"
          label="申请提现方式"
          min-width="120"
        >
          <template slot-scope="scope">
            <el-tag :type="applyTransfer[scope.row.apply_transfer].type">{{
              applyTransfer[scope.row.apply_transfer].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="apply_transfer" label="到账方式" min-width="120">
          <template slot-scope="scope" v-if="scope.row.status === 2">
            <el-tag :type="applyTransfer[scope.row.online].type">{{
              applyTransfer[scope.row.online].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="total_price" label="提现类型">
          <template slot-scope="scope">
            <span>{{ typeList[scope.row.type] }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            <span>{{ statusType[scope.row.status] }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="申请时间" min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column label="处理时间" min-width="120">
          <template slot-scope="scope">
            <div v-if="scope.row.status !== 1">
              <p>{{ scope.row.sh_time | handleTime(1) }}</p>
              <p>{{ scope.row.sh_time | handleTime(2) }}</p>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="280" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate" v-if="userInfo.is_admin !== 0">
              <div v-if="scope.row.status === 1">
                <lb-button
                  size="mini"
                  plain
                  type="primary"
                  @click="confirmAccount(scope.row.id, 1)"
                  v-hasPermi="`${$route.name}-wechatCashOut`"
                  >{{ $t('action.wechatCashOut') }}</lb-button
                >
                <lb-button
                  size="mini"
                  plain
                  type="success"
                  @click="confirmAccount(scope.row.id, 2)"
                  v-hasPermi="`${$route.name}-alipayCashOut`"
                  >{{ $t('action.alipayCashOut') }}</lb-button
                >
                <lb-button
                  size="mini"
                  plain
                  type="warning"
                  @click="confirmAccount(scope.row.id)"
                  v-hasPermi="`${$route.name}-underlineCashOut`"
                  >{{ $t('action.underlineCashOut') }}</lb-button
                >
                <lb-button
                  size="mini"
                  plain
                  type="danger"
                  @click="refuseAccount(scope.row.id)"
                  v-hasPermi="`${$route.name}-rejectCashOut`"
                  >{{ $t('action.rejectCashOut') }}</lb-button
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
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
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
          label: '未到账',
          value: 1
        },
        {
          label: '已到账',
          value: 2
        },
        {
          label: '已拒绝',
          value: 3
        }
      ],
      statusType: {
        1: '未到账',
        2: '已到账',
        3: '已拒绝'
      },
      typeListOptions: [
        { name: '全部', id: 0 },
        { name: '服务费', id: 1 },
        { name: '车费', id: 2 },
        { name: '代理商', id: 3 },
        { name: '分销商', id: 4 },
        { name: '渠道商', id: 5 },
        { name: '门店', id: 6 },
        { name: '经纪人', id: 7 }],
      typeList: ['全部', '服务费', '车费', '代理商', '分销商', '渠道商', '门店', '经纪人'],
      applyTransfer: {
        0: { type: 'warning', text: '线下转账' },
        1: { type: 'primary', text: '微信' },
        2: { type: 'success', text: '支付宝' }
      },
      searchForm: {
        page: 1,
        limit: 10,
        type: 0,
        // type: 1,
        status: 0,
        code: ''
      },
      loading: false,
      total: 0,
      tableData: [],
      userInfo: {}
    }
  },
  created () {
    this.getTableDataList()
    this.userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
    let storeplus = this.routesItem.auth.storeplus
    let channel = this.routesItem.auth.channel
    if (!storeplus) {
      let storeInd = this.typeListOptions.findIndex(item => {
        return item.id === 6
      })
      if (storeInd !== -1) {
        this.typeListOptions.splice(storeInd, 1)
      }
    }
    if (!channel) { // 渠道商
      let channelInd = this.typeListOptions.findIndex(item => {
        return item.id === 5
      })
      if (channelInd !== -1) {
        this.typeListOptions.splice(channelInd, 1)
      }
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
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
      let { code, data } = await this.$api.finance.walletList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    /**
     * @method 在线转账/线下转账
     */
    confirmAccount (id, online = 0) {
      this.$confirm(this.$t('tips.confirmOperate'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.$api.finance.walletPass({ id, status: 2, online }).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successOper'))
            this.getTableDataList()
          }
        })
      })
    },
    /**
     * @method 拒绝提现
     */
    refuseAccount (id) {
      this.$confirm(`你确认要拒绝提现吗？`, this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.$api.finance.walletNoPass({ id, status: 3 }).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successOper'))
            this.getTableDataList()
          }
        })
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
.lb-finance-record {
  width: 100%;
}
</style>
