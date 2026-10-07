<!--
 * @Description: 佣金信息
 * @Author: xiao li
 * @Date: 2021-07-04 13:00:37
 * @LastEditTime: 2024-02-28 15:25:47
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-finance-list2">
    <top-nav />
    <div class="page-main">
      <lb-tips
        >代理商在提现时，会将{{
          $t('action.attendantName')
        }}的提成以及自己的分成一并提现出来，平台统一处理，代理商再将提现出来的费用私下结算给线下{{
          $t('action.attendantName')
        }}</lb-tips
      >
      <el-row class="page-search-form">
        <div class="flex-center pt-sm pb-lg c-title">
          <div>
            <div>
              可用{{ balance_character || '余额' }}（包含线下{{
                $t('action.attendantName')
              }}的提成）
            </div>
            <div class="f-lg-title mt-md flex-y-center cursor-pointer">
              <div class="c-link mr-md">{{ total_cash || 0 }}</div>
              <el-button
                type="primary"
                size="mini"
                plain
                @click="showDialog = true"
                >提现</el-button
              >
              <div
                class="f-title c-link ml-md"
                @click="$router.push(`/finance/record`)"
              >
                提现记录
              </div>
            </div>
          </div>
          <div class="flex-1 flex-center">
            <div>
              <div>总金额（元）</div>
              <div class="f-lg-title mt-md">
                {{ total_wallet_cash || 0 }}
              </div>
            </div>
          </div>
          <div>
            <div>
              未入账（元）<lb-tool-tips :padding="0">
                平台未到账的服务订单金额
              </lb-tool-tips>
            </div>
            <div class="f-lg-title mt-md">
              {{ unrecorded_cash || 0 }}
            </div>
          </div>
        </div>
      </el-row>
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
          <el-form-item label="系统订单号" prop="order_code">
            <el-input
              v-model="searchForm.order_code"
              placeholder="请输入系统订单号"
            ></el-input>
          </el-form-item>
          <el-form-item label="佣金类型" prop="status">
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
          </el-form-item>
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
        <el-table-column
          prop="top_name"
          label="佣金获得者"
          min-width="120"
        ></el-table-column>
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
        <el-table-column prop="type" label="佣金类型" min-width="120">
          <template slot-scope="scope">
            <div class="flex-y-center">
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
        <el-table-column prop="pay_price" label="订单总金额" min-width="120">
          <template slot-scope="scope"> ¥{{ scope.row.pay_price }}</template>
        </el-table-column>
        <el-table-column prop="order_goods" label="提成比例" min-width="300">
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
        <el-table-column prop="cash" label="此单提成金额" min-width="120">
          <template slot-scope="scope"> ¥{{ scope.row.cash }}</template>
        </el-table-column>
        <el-table-column prop="create_time" min-width="120" label="时间">
          <template slot-scope="scope">
            <div>{{ scope.row.create_time | handleTime(1) }}</div>
            <div>{{ scope.row.create_time | handleTime(2) }}</div>
          </template>
        </el-table-column>

        <el-table-column label="操作" min-width="120" fixed="right">
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
                  scope.row.cash_status === 0
                    ? $t('action.attendantName') + '转账'
                    : '已转账'
                }}</lb-button
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

    <el-dialog
      title="提现信息核对"
      :visible.sync="showDialog"
      width="600px"
      center
    >
      <el-form
        class="dialog-form"
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="120px"
      >
        <el-form-item label="提现金额" prop="apply_price">
          <el-input placeholder="请输入提现金额" v-model="subForm.apply_price">
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="备注信息" prop="text">
          <el-input
            type="textarea"
            :rows="10"
            v-model="subForm.text"
            maxlength="300"
            show-word-limit
            resize="none"
            placeholder="请输入备注信息"
          ></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog = false">取 消</el-button>
        <el-button
          type="primary"
          @click="submitFormInfo('sub')"
          v-preventReClick
          >确 定</el-button
        >
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
export default {
  data () {
    return {
      total_cash: 0,
      unrecorded_cash: 0,
      wallet_cash: 0,
      total_wallet_cash: 0,
      showDialog: false,
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
        },
        {
          label: '渠道商',
          value: 10
        },
        {
          label: '经纪人',
          value: 16
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
        6: '代理商',
        8: '车费',
        10: '渠道商',
        16: '经纪人'
      },
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        order_code: '',
        top_name: '',
        status: 0,
        type: 0
      },
      tableData: [],
      total: 0,
      subForm: {
        apply_price: '',
        text: ''
      },
      subFormRules: {
        apply_price: { required: true, validator: this.$reg.isMoney, text: '提现金额', trigger: 'blur' }
      },
      balance_character: ''
    }
  },
  created () {
    this.getFormInfo()
    this.getTableDataList()
    let channel = this.routesItem.auth.channel
    if (!channel) { // 渠道商
      let channelInd = this.cashTypeList.findIndex(item => {
        return item.value === 10
      })
      if (channelInd !== -1) {
        this.cashTypeList.splice(channelInd, 1)
      }
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.balance_character = data.balance_character
    },
    resetForm (form) {
      this.$refs[form].resetFields()
      this.searchForm.type = 0
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
      let { code, data } = await this.$api.agent.cashList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
      let arr = ['total_cash', 'unrecorded_cash', 'wallet_cash']
      arr.map(item => {
        this[item] = data[item]
      })
      this.total_wallet_cash = (data.total_cash * 1 + data.wallet_cash * 1).toFixed(2)
      this.subForm.apply_price = this.total_cash
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
    },
    async submitFormInfo () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let param = JSON.parse(JSON.stringify(this.subForm))
        let { code } = await this.$api.agent.applyWallet(param)
        if (code !== 200) return
        this.$message.success(this.$t('tips.successSub'))
        this.showDialog = false
        this.getTableDataList()
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
.lb-finance-list2 {
  .page-main {
    width: 100%;

    .el-select,
    .el-input-number,
    .el-input {
      width: 200px;
    }
  }
  .dialog-form {
    .el-select,
    .el-input-number,
    .el-input {
      width: 300px;
    }
  }
}
</style>
