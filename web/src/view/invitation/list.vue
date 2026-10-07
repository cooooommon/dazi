<!--
 * @Description: 审核列表
 * @Author: wen kun
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-11-27 11:58:05
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
          <el-form-item label="发布日期" prop="start_time">
            <el-date-picker
              @change="getTableDataList(1)"
              v-model="range"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              :picker-options="pickerOptions"
              value-format="timestamp"
            ></el-date-picker>
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
        <el-table-column prop="avatarUrl" label="发布人头像">
          <template slot-scope="scope">
            <lb-image :src="scope.row.avatarUrl" />
          </template>
        </el-table-column>
        <el-table-column prop="nickName" label="发布人"></el-table-column>
        <el-table-column prop="" label="服务时间">
          <template slot-scope="scope">
            <div>{{ scope.row.start_time }} -</div>
            <div>{{ scope.row.end_time }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="price" label="服务价格">
          <template slot-scope="scope">
            <div>{{ scope.row.price }}元/单</div>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="发布时间"></el-table-column>
        <el-table-column prop="address" label="发布地点"></el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            <el-tag>
              {{ scope.row.status == 1 ? '待审核 ' : '已审核' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="success"
                @click="examineItem(scope.row.id, 1)"
                v-show="scope.row.status == 1"
                v-hasPermi="`${$route.name}-examine`"
                >{{ $t('action.examine') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="primary"
                v-show="scope.row.status != 1"
                @click="examineItem(scope.row.id, 2)"
                v-hasPermi="`${$route.name}-viewDetail`"
                >{{ $t('action.viewDetail') }}</lb-button
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
        :title="isView == 1 ? '审核' : '查看'"
        :visible.sync="showDialog"
        width="800px"
        center
      >
        <el-form
          class="dialog-form"
          :model="orderInfo"
          ref=""
          label-width="140px"
          size="mini"
        >
          <el-form-item label="发布地点：" prop="address">
            <div>{{ orderInfo.address }}</div>
          </el-form-item>
          <el-form-item label="发布时间：" prop="create_time">
            <div>{{ orderInfo.create_time }}</div>
          </el-form-item>
          <el-form-item label="" prop="content">
            <div style="white-space: pre-wrap">{{ orderInfo.content }}</div>
          </el-form-item>
          <el-form-item label="" prop="img">
            <div class="flex-warp">
              <lb-cover
                :fileList="orderInfo.img"
                :isToDel="false"
                size="small"
                type="more"
                :fileSize="orderInfo.img.length"
              ></lb-cover>
            </div>
          </el-form-item>
          <el-form-item label="服务时间：" prop="content">
            <div>{{ orderInfo.start_time }} - {{ orderInfo.end_time }}</div>
          </el-form-item>
          <!-- <el-form-item label="服务价格：" prop="price">
            <div>{{ orderInfo.type_price }}元</div>
          </el-form-item> -->
          <el-form-item
            label="会员卡折扣："
            prop="member_balance"
            v-if="orderInfo.member_status"
          >
            <div class="c-warning">
              {{ orderInfo.member_balance }}折（优惠¥{{
                orderInfo.member_discount
              }}）
            </div>
          </el-form-item>
          <el-form-item label="实际支付金额：" prop="price">
            <div>{{ orderInfo.price }}元</div>
          </el-form-item>
          <el-form-item label="车费：" prop="is_car">
            <div>{{ orderInfo.is_car == 1 ? '报销' : '不报销' }}</div>
          </el-form-item>
          <el-form-item label="订单编号：" prop="order_code">
            <div>{{ orderInfo.order_code }}</div>
          </el-form-item>
        </el-form>
        <div class="space-lg b-1px-t"></div>
        <el-form
          @submit.native.prevent
          :model="subForm"
          ref="subForm"
          label-width="140px"
          size="mini"
        >
          <el-form-item label="审核结果：" prop="status" style="width: 50%">
            <div v-if="isView == 2">
              {{
                subForm.status == 0 ? '拒绝' : subForm.status > 0 ? '通过' : ''
              }}
            </div>
            <el-radio-group v-model="subForm.status" v-else>
              <el-radio :label="1">通过</el-radio>
              <el-radio :label="2">拒绝</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item
            label="拒绝原因："
            v-if="
              (isView == 1 && subForm.status == 2) ||
              (isView == 2 && subForm.status == 0)
            "
          >
            <el-input
              type="textarea"
              :rows="10"
              v-model="subForm.check_text"
              maxlength="300"
              show-word-limit
              resize="none"
              placeholder="请输入审核意见"
            ></el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="submitFormInfo" v-if="isView == 1"
            >确 定</el-button
          >
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
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > Date.now()
        }
      },
      range: '',
      statusOptions: [
        {
          label: '全部',
          value: -1
        },
        {
          label: '待审核',
          value: 0
        },
        {
          label: '已审核',
          value: 1
        }
      ],
      statusType: {
        0: '待审核',
        1: '已审核'
      },
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        status: -1,
        start_time: '',
        end_time: '',
        have_look: -1
      },
      tableData: [],
      total: 0,
      showDialog: false,
      orderInfo: {
        img: []
      },
      subForm: {
        id: 0,
        status: 1, // 1通过 2拒绝
        check_text: ''
      },
      isView: 1
    }
  },
  activated () {
    this.getTableDataList()
  },
  methods: {
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
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      let range = JSON.parse(JSON.stringify(this.range))
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      if (range && range.length > 0) {
        searchForm.start_time = parseInt(range[0] / 1000)
        searchForm.end_time = parseInt(range[1] / 1000) + 24 * 3600 - 1
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      if (searchForm.have_look === -1) {
        delete searchForm.have_look
      }
      let { code, data } = await this.$api.invitation.demandOrderList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    // 审核
    async examineItem (id, index) {
      this.subForm.id = id
      let { data } = await this.$api.invitation.demandOrderInfo({ id })
      data.img = data.img.length > 0 ? data.img.map(item => {
        return { url: item }
      }) : []
      console.log(data)
      this.subForm.status = data.status
      this.subForm.check_text = data.check_text
      this.isView = index
      this.orderInfo = data
      this.showDialog = true
    },
    submitFormInfo () {
      let { subForm } = this
      this.$api.invitation.demandExamine(subForm).then((res) => {
        if (res.code === 200) {
          this.$message.success(
            this.$t('tips.successOper')
          )
          this.showDialog = false
          this.getTableDataList()
        }
      })
      if (this.subForm.status === 1) {
        this.$api.invitation.sendMsg({ id: this.subForm.id })
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
