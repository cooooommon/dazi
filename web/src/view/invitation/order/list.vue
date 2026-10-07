<!--
 * @Description: 订单管理
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-04-29 11:18:24
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
          <el-form-item label="服务名称" prop="ser_name">
            <el-input
              v-model="searchForm.ser_name"
              placeholder="请输入服务名称"
            ></el-input>
          </el-form-item>
          <el-form-item
            :label="`${$t('action.attendantName')}姓名`"
            prop="coach_name"
          >
            <el-input
              v-model="searchForm.coach_name"
              :placeholder="`请输入${$t('action.attendantName')}姓名`"
            ></el-input>
          </el-form-item>
          <el-form-item label="系统订单号" prop="order_code">
            <el-input
              v-model="searchForm.order_code"
              placeholder="请输入系统订单号"
            ></el-input>
          </el-form-item>
          <el-form-item label="下单手机号" prop="phone">
            <el-input
              v-model="searchForm.phone"
              placeholder="请输入下单时填写的手机号"
              style="width: 200px"
            ></el-input>
          </el-form-item>
          <el-form-item label="日期" prop="start_time">
            <el-date-picker
              @change="getTableDataList(1)"
              v-model="range"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              value-format="timestamp"
              :picker-options="pickerOptions"
            >
            </el-date-picker>
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
          <!-- <el-form-item label="陪玩官" prop="coach_id">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.coach_id"
              placeholder="请选择"
            >
              <el-option
                v-for="item in coachOptions"
                :key="item.id"
                :label="item.coach_name"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item> -->
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
          >打印</lb-button
        >
        <lb-button
          size="mini"
          plain
          type="primary"
          icon="el-icon-download"
          :loading="downloadLoading"
          @click="exportOrder"
          v-hasPermi="`${$route.name}-export`"
          >导出</lb-button
        >
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
        <el-table-column prop="name" label="服务类型"></el-table-column>
        <el-table-column prop="start_time" min-width="120" label="服务时间">
          <template slot-scope="scope">
            <div>{{ scope.row.start_time }} -</div>
            <div>{{ scope.row.end_time }}</div>
          </template></el-table-column
        >
        <el-table-column prop="price" width="120" label="邀约服务金额">
          <template slot-scope="scope"> ¥{{ scope.row.price }} </template>
        </el-table-column>
        <el-table-column prop="nickName" label="客户昵称"></el-table-column>
        <el-table-column
          prop="phone"
          width="120"
          label="手机号"
        ></el-table-column>
        <el-table-column prop="coach_name" label="接单人"></el-table-column>
        <el-table-column prop="pay_type" label="支付方式">
          <template slot-scope="scope">
            {{ payType[scope.row.pay_type] }}
          </template>
        </el-table-column>
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
        <el-table-column
          prop="create_time"
          min-width="120"
          label="下单时间"
        ></el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            {{ statusType[scope.row.status] }}
          </template>
        </el-table-column>
        <!-- <el-table-column prop="hx_user_text" label="核销人"></el-table-column> -->
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                type="primary"
                plain
                @click="
                  $router.push(`/invitation/inviteDetail?id=${scope.row.id}`)
                "
                v-hasPermi="`${$route.name}-view`"
                >{{ $t('action.view') }}</lb-button
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
import pubic from '@/utils/public'
export default {
  data () {
    return {
      loading: false,
      downloadLoading: false,
      print: false,
      searchForm: {
        page: 1,
        limit: 10,
        ser_name: '',
        coach_name: '',
        order_code: '',
        phone: '',
        status: 0,
        coach_id: 0
      },
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > Date.now()
        }
      },
      range: '',
      statusOptions: [
        {
          label: '全部',
          value: 0
        },
        {
          label: '待接单',
          value: 1
        },
        {
          label: '已接单',
          value: 2
        },
        {
          label: '已完成',
          value: 3
        }
        // ,
        // {
        //   label: '售后',
        //   value: 4
        // },
        // {
        //   label: '待审核',
        //   value: 5
        // }
      ],
      coachOptions: [],
      carType: {
        0: '公交/地铁',
        1: '出租车'
      },
      payType: {},
      statusType: {
        '-2': '超时取消', '-1': '用户取消', 0: '审核拒绝', 1: '待审核', 2: '待接单', 3: '已接单', 4: '已完成'
      },
      printTableData: [],
      tableData: [],
      total: 0,
      order_price: 0
    }
  },
  async created () {
    await this.getFormInfo()
    this.getTableDataList()
    this.getCoachList()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.payType = {
        1: '支付宝',
        2: '微信支付',
        3: data.balance_character || '账户余额'
      }
    },
    resetForm (form) {
      this.range = ''
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
    // 获取陪玩官列表
    async getCoachList () {
      let { code, data } = await this.$api.technician.coachList({
        page: 1,
        limit: 99999,
        status: 0
      })
      data.data.unshift({ id: 0, coach_name: '全部' })
      this.coachOptions = data.data
    },

    /**
     * @method 搜索订单列表
     */
    getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let range = JSON.parse(JSON.stringify(this.range))
      if (range && range.length > 0) {
        searchForm.start_time = parseInt(range[0] / 1000)
        searchForm.end_time = parseInt(range[1] / 1000) + 24 * 3600 - 1
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      this.$api.invitation.demandOder(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          let { data, total, order_price: price } = res.data
          data.map(item => {
            item.is_balance = item.balance * 1 > 0 ? 1 : 0
            item.refund_price = item.refund_price * 1 > 0 ? item.refund_price : ''
          })
          this.tableData = data
          this.total = total
          this.order_price = price
          let arr = data.map(item => {
            item.start_end = item.start_time + '-' + item.end_time
            return [
              item.id,
              item.name,
              item.start_end,
              item.price,
              item.nickName,
              item.phone,
              item.coach_name,
              this.payType[item.pay_type],
              item.order_code,
              item.transaction_id,
              item.create_time,
              this.statusType[item.status]
            ]
          })
          this.printTableData = [
            [
              'ID',
              '服务类型',
              '服务时间',
              '邀约服务金额',
              '客户昵称',
              '手机号',
              '接单人',
              '支付方式',
              '系统订单号',
              '商户订单号',
              '下单时间',
              '状态'
            ],
            arr
          ]
        }
      })
    },

    /**
     * @method 导出订单
     */
    exportOrder () {
      this.downloadLoading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let range = JSON.parse(JSON.stringify(this.range))
      if (range && range.length > 0) {
        searchForm.start_time = range[0] / 1000
        searchForm.end_time = range[1] / 1000 + 24 * 3600 - 1
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      let url = pubic.getProCurrentHref()
      let keywords = url.indexOf('?') > 0 ? '' : '?'
      let flag = url.indexOf('?') > 0
      Object.getOwnPropertyNames(searchForm).forEach((keys, value) => {
        keywords += flag
          ? `&${keys}=${searchForm[keys]}`
          : `${keys}=${searchForm[keys]}`
        flag = true
      })
      let token = window.localStorage.getItem('massage_minitk')
      let dwonlaodUrl = `${url}/massage/admin/AdminExcel/demandOrder${keywords}&token=${token}`
      window.location.href = dwonlaodUrl
      setTimeout(() => {
        this.downloadLoading = false
      }, 5000)
    },
    /**
     * @method 跳转详情
     */
    jumpToDetail () {
      this.$router.push()
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
  