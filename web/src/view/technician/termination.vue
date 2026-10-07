<!--
 * @Description: 向导解约申请
 * @Author: wen kun
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-03-27 17:37:33
 * @LastEditors: xiao li
-->
<template>
  <div class="lb-examine-goods">
    <top-nav />
    <div class="page-main">
      <div class="space-lg"></div>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm.list"
          ref="listForm"
        >
          <el-form-item label="申请时间" prop="start_time">
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
            ></el-date-picker>
          </el-form-item>
          <el-form-item>
            <lb-button
              size="medium"
              type="primary"
              icon="el-icon-search"
              style="margin-right: 5px"
              @click="getTableDataList(1, 'list')"
              >{{ $t('action.search') }}</lb-button
            >
            <lb-button
              size="medium"
              icon="el-icon-refresh-left"
              style="margin-right: 5px"
              @click="resetForm('list')"
              >{{ $t('action.reset') }}</lb-button
            >
          </el-form-item>
        </el-form>
      </el-row>
      <el-table
        v-loading="loading.list"
        :data="tableData.list"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="title" label="姓名"></el-table-column>
        <el-table-column prop="title" label="手机号"></el-table-column>
        <el-table-column prop="title" label="身份证号"></el-table-column>
        <el-table-column prop="title" label="住址"></el-table-column>
        <el-table-column prop="title" label="申请原因"></el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.list.page"
        :pageSize="searchForm.list.limit"
        :total="total.list"
        @handleSizeChange="handleSizeChange($event, 'list')"
        @handleCurrentChange="handleCurrentChange($event, 'list')"
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
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      loading: { list: false, user: false },
      searchForm: {
        list: {
          page: 1,
          limit: 10,
          start_time: '',
          end_time: '',
          name: ''
        },
        user: {
          page: 1,
          limit: 10,
          nickName: ''
        }
      },
      tableData: {
        list: [],
        user: []
      },
      total: {
        list: 0,
        user: 0
      },
      cur_coupon: {},
      multipleSelection: [],
      showDialog: false
    }
  },
  activated () {
    this.getTableDataList(1, 'list')
  },
  methods: {
    resetForm (form) {
      let name = `${form}Form`
      this.$refs[name].resetFields()
      this.getTableDataList(1, form)
    },
    handleSizeChange (val, key) {
      this.searchForm[key].limit = val
      this.handleCurrentChange(1, key)
    },
    handleCurrentChange (val, key) {
      this.searchForm[key].page = val
      this.getTableDataList('', key)
    },
    /**
     * @method: 获取列表：卡券列表/用户列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.tableData[key] = []
      this.loading[key] = true
      let searchForm = this.searchForm[key]
      let { code, data } = key === 'list' ? await this.$api.market.couponList(searchForm) : await this.$api.custom.userList(searchForm)
      this.loading[key] = false
      if (code !== 200) return
      this.tableData[key] = data.data
      this.total[key] = data.total
    },
    async toShowDialog (item) {
      this.searchForm.user.nickName = ''
      this.cur_coupon = JSON.parse(JSON.stringify(item))
      await this.getTableDataList(1, 'user')
      this.showDialog = !this.showDialog
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    }
  }
}
</script>

<style lang="scss" scoped>
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
</style>
