<!--
 * @Description: 订单管理
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-12-25 17:05:27
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-shop-order">
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
          <el-form-item
            :label="`${$t('action.attendantName')}昵称`"
            prop="coach_name"
          >
            <el-input
              v-model="searchForm.coach_name"
              :placeholder="`请输入${$t('action.attendantName')}昵称`"
            ></el-input>
          </el-form-item>
          <el-form-item label="系统订单号" prop="order_code">
            <el-input
              v-model="searchForm.order_code"
              placeholder="请输入系统订单号"
            ></el-input>
          </el-form-item>
          <el-form-item label="下单手机号" prop="mobile">
            <el-input
              v-model="searchForm.mobile"
              placeholder="请输入下单时填写的手机号"
              style="width: 200px"
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
          <el-form-item
            :label="`${$t('action.attendantName')}类型`"
            prop="is_coach"
          >
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.is_coach"
              placeholder="请选择"
            >
              <el-option
                v-for="item in coachTypeList"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item
            label="服务方式"
            prop="is_store"
            v-if="routesItem.auth.store"
          >
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.is_store"
              placeholder="请选择"
            >
              <el-option
                v-for="item in serviceTypeList"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              ></el-option>
            </el-select>
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
          <el-form-item label="状态" prop="pay_type">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.pay_type"
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
      <el-row class="page-top-operate">
        <lb-button
          size="mini"
          plain
          type="primary"
          icon="el-icon-printer"
          @click="printTable"
          v-hasPermi="`${$route.name}-print`"
        >
          {{ $t('action.print') }}</lb-button
        >
        <lb-button
          size="mini"
          plain
          type="primary"
          icon="el-icon-download"
          :loading="downloadLoading"
          @click="toExportExcel"
          v-hasPermi="`${$route.name}-export`"
        >
          {{ $t('action.export') }}</lb-button
        >
      </el-row>
      <div class="pb-lg">
        共{{ total }}条数据，订单金额共计：{{ order_price }}元，车费共计：{{
          car_price
        }}元
      </div>
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
                  <div class="f-caption" style="line-height: 1.4">
                    时长：{{ citem.time_long }} 分钟
                  </div>
                  <div
                    class="flex-between f-caption mt-sm"
                    style="line-height: 1.4"
                  >
                    <div class="c-warning">¥{{ citem.price }}</div>
                    <div>x{{ citem.num }}</div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </el-table-column>
        <el-table-column
          prop="user_name"
          label="下单人"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="mobile"
          min-width="120"
          label="下单手机号"
        ></el-table-column>
        <el-table-column
          prop="coach_info.coach_name"
          :label="$t('action.attendantName')"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="coach_id"
          :label="`${$t('action.attendantName')}类型`"
          min-width="120"
        >
          <template slot-scope="scope">
            <div>
              {{
                scope.row.coach_id
                  ? '入驻' + $t('action.attendantName')
                  : '非入驻' + $t('action.attendantName')
              }}
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="store_id" label="服务方式" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.store_id ? '到店服务' : '上门服务' }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="start_time" min-width="120" label="服务开始时间">
          <template slot-scope="scope">
            <div>{{ scope.row.start_time | handleTime(1) }}</div>
            <div>{{ scope.row.start_time | handleTime(2) }}</div>
          </template></el-table-column
        >
        <el-table-column
          prop="init_service_price"
          width="120"
          label="服务项目费用"
        >
          <template slot-scope="scope">
            ¥{{ scope.row.init_service_price }}
          </template>
        </el-table-column>
        <el-table-column prop="refund_price" label="退款金额">
          <template slot-scope="scope" v-if="scope.row.refund_price">
            <div>¥{{ scope.row.refund_price }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="add_pid" width="150" label="主订单号">
          <template
            slot-scope="scope"
            v-if="scope.row.add_pid && scope.row.add_pid.id"
          >
            <div
              @click="
                $router.push(`/shop/order/detail?id=${scope.row.add_pid.id}`)
              "
              class="c-warning cursor-pointer"
            >
              {{ scope.row.add_pid.order_code }}
            </div>
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
        <el-table-column
          prop="admin_name"
          min-width="150"
          label="代理商"
        ></el-table-column>
        <el-table-column prop="create_time" min-width="120" label="下单时间">
          <template slot-scope="scope">
            <div>{{ scope.row.create_time | handleTime(1) }}</div>
            <div>{{ scope.row.create_time | handleTime(2) }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="pay_model" label="支付方式" min-width="120">
          <template slot-scope="scope">
            {{ payType[scope.row.pay_model] }}
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            {{ statusType[scope.row.pay_type] }}
            <div
              class="f-icontext c-warning"
              v-if="scope.row.coach_refund_time && scope.row.pay_type === -1"
            >
              {{ $t('action.attendantName') }}拒单
            </div>
            <div class="f-icontext c-warning" v-if="!scope.row.is_show">
              用户已删除
            </div>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="200" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                type="primary"
                plain
                @click="$router.push(`/shop/order/detail?id=${scope.row.id}`)"
                v-hasPermi="`${$route.name}-view`"
                >{{ $t('action.view') }}</lb-button
              >
              <lb-button
                size="mini"
                type="success"
                plain
                @click="
                  toOperOrderItem({
                    order_id: scope.row.id,
                    type: scope.row.pay_type
                  })
                "
                v-show="[2, 3, 4, 5, 6].includes(scope.row.pay_type)"
                v-hasPermi="
                  `${$route.name}-${
                    technicianStatusOperType[scope.row.pay_type]
                  }`
                "
                >{{
                  $t(`action.${technicianStatusOperType[scope.row.pay_type]}`)
                }}</lb-button
              >
            </div>
          </template>
        </el-table-column>
      </el-table>
      <!-- 打印表格 -->
      <table
        v-show="print"
        style="font-size: 12px"
        ref="print"
        border="0"
        cellspacing="0"
        cellapdding="0"
      >
        <thead>
          <tr>
            <th
              style="border: 1px solid"
              v-for="(item, index) in printTableData[0]"
              :key="index"
            >
              {{ item }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in printTableData[1]" :key="index">
            <td style="border: 1px solid" v-for="(items, i) in item" :key="i">
              {{ items }}
            </td>
          </tr>
        </tbody>
      </table>
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
import { mapState } from 'vuex'
export default {
  data () {
    return {
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      statusOptions: [{ label: '全部订单', value: 0 }, { label: '已取消', value: -1 }, { label: '待支付', value: 1 }, { label: '待服务', value: 2 }, { label: this.$t('action.attendantName') + '接单', value: 3 }, { label: '服务中', value: 6 }, { label: '已完成', value: 7 }],
      coachTypeList: [{ id: 0, title: '全部' + this.$t('action.attendantName') }, { id: 1, title: '入驻' + this.$t('action.attendantName') }, { id: 2, title: '非入驻' + this.$t('action.attendantName') }],
      serviceTypeList: [{ id: 0, title: '全部服务' }, { id: 2, title: '上门服务' }, { id: 1, title: '到店服务' }],
      base_agent: [],
      carType: {
        0: '公交/地铁',
        1: '出租车'
      },
      payType: {
        1: '微信支付',
        2: '余额支付',
        3: '支付宝支付'
      },
      statusType: {
        '-1': '已取消',
        1: '待支付',
        2: '待服务',
        3: this.$t('action.attendantName') + '接单',
        4: this.$t('action.attendantName') + '出发',
        5: this.$t('action.attendantName') + '到达',
        6: '服务中',
        7: '已完成',
        8: '待转单'
      },
      technicianStatusType: {
        2: this.$t('action.attendantName') + '接单',
        3: '开始服务',
        6: '完成服务'
      },
      technicianStatusOperType: {
        2: 'orderTaking',
        3: 'startService',
        6: 'serviceCompletion'
      },
      loading: false,
      downloadLoading: false,
      print: false,
      searchForm: {
        page: 1,
        limit: 10,
        goods_name: '',
        coach_name: '',
        order_code: '',
        mobile: '',
        start_time: '',
        end_time: '',
        pay_type: 0,
        is_coach: 0,
        is_store: 0,
        admin_id: '',
        is_add: 1
      },
      printTableData: [],
      tableData: [],
      total: 0,
      order_price: 0,
      car_price: 0,
      lockTap: false
    }
  },
  async created () {
    this.getFormInfo()
    await this.getBaseInfo()
    this.getTableDataList()
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
      this.payType[2] = data.balance_character
    },
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
      this.$api.shop.orderList(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          let { data, total, order_price: oprice, car_price: cprice } = res.data
          data.map(item => {
            item.is_balance = item.balance * 1 > 0 ? 1 : 0
            item.refund_price = item.refund_price * 1 > 0 ? item.refund_price : ''
          })
          this.tableData = data
          this.total = total
          this.order_price = oprice
          this.car_price = cprice
          let arr = data.map(item => {
            item.goods_text = ``
            item.add_order_text = ``
            item.order_goods.map((aitem, aindex) => {
              item.goods_text += `${aindex === 0 ? '' : '；'}${aitem.goods_name} 时长：${aitem.time_long}分钟 单价：¥${aitem.price} 数量：x${aitem.num}`
            })
            if (item.add_pid && item.add_pid.id) {
              item.add_order_text = `${item.add_pid.order_code}`
            }
            return [
              item.id,
              item.goods_text,
              item.user_name,
              item.mobile,
              item.coach_info.coach_name,
              item.coach_id ? '入驻' + this.$t('action.attendantName') : '非入驻' + this.$t('action.attendantName'),
              item.store_id ? '到店服务' : '上门服务',
              moment(item.start_time * 1000).format('YYYY-MM-DD HH:mm:ss'),
              `¥${item.init_service_price}`,
              item.refund_price ? `¥${item.refund_price}` : '',
              item.add_order_text,
              item.order_code,
              item.transaction_id,
              item.admin_name,
              moment(item.create_time * 1000).format('YYYY-MM-DD HH:mm:ss'),
              this.payType[item.pay_model],
              this.statusType[item.pay_type]
            ]
          })
          this.printTableData = [
            [
              'ID',
              '服务项目信息',
              '下单人',
              '下单手机号',
              this.$t('action.attendantName'),
              this.$t('action.attendantName') + '类型',
              '服务方式',
              '服务开始时间',
              '服务项目费用',
              '退款金额',
              '主订单号',
              '系统订单号',
              '付款订单号',
              '代理商',
              '下单时间',
              '支付方式',
              '状态'
            ],
            arr
          ]
        }
      })
    },
    async toOperOrderItem (param) {
      let { type } = param
      let typeArr = {
        2: 3,
        3: 6,
        6: 7
      }
      param.type = typeArr[type]
      let operText = this.$t(`action.${this.technicianStatusOperType[type]}`)
      this.$confirm(`你确认要操作${operText}吗`, this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.toConfirmOperOrder(param)
      }).catch(() => { })
    },
    async toConfirmOperOrder (param) {
      if (this.lockTap) return
      this.lockTap = true
      let { code } = await this.$api.shop.adminUpdateOrder(param)
      this.lockTap = false
      if (code !== 200) return
      this.$message.success(this.$t('tips.successOper'))
      this.getTableDataList('', 'list')
    },
    /**
     * @method 导出订单
     */
    toExportExcel () {
      this.downloadLoading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
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
      let dwonlaodUrl = `${url}/massage/admin/AdminExcel/orderList${keywords}&token=${token}`
      window.location.href = dwonlaodUrl
      setTimeout(() => {
        this.downloadLoading = false
      }, 5000)
    },
    /**
     * @method 打印表格
     */
    printTable () {
      this.print = true
      setTimeout(() => {
        this.$print(this.$refs.print)
        this.print = false
      }, 50)
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
.lb-shop-order {
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
