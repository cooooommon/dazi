<!--
 * @Description: 问题反馈
 * @Author: wen kun
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-03-27 17:39:23
 * @LastEditors: xiao li
-->
<template>
  <div class="lb-examine-goods">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <el-button
          @click="toChange(0)"
          :type="searchForm.list.status === 0 ? 'primary' : ''"
          plain
          size="medium"
          >全部（{{ count.all || 0 }}）</el-button
        >
        <el-button
          @click="toChange(1)"
          :type="searchForm.list.status === 1 ? 'primary' : ''"
          plain
          size="medium"
          >未处理（{{ count.nopass || 0 }}）</el-button
        >
        <el-button
          @click="toChange(2)"
          :type="searchForm.list.status === 2 ? 'primary' : ''"
          plain
          size="medium"
          >已处理（{{ count.pass || 0 }}）</el-button
        >
      </el-row>
      <div class="space-lg"></div>
      <el-table
        v-loading="loading.list"
        :data="tableData.list"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="type_name" label="反馈类型"></el-table-column>
        <el-table-column prop="coach_name" label="反馈人"></el-table-column>
        <el-table-column prop="mobile" label="反馈人手机号"></el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            <el-tag :type="statusText[scope.row.status].type">{{
              statusText[scope.row.status].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column
          prop="create_time"
          label="反馈时间"
          min-width="140"
        ></el-table-column>
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                :type="scope.row.status === 1 ? 'success' : 'primary'"
                @click="$router.push(`/feedback/detail?id=${scope.row.id}`)"
                v-show="[1, 2].includes(scope.row.status)"
                v-hasPermi="
                  scope.row.status === 1
                    ? `${$route.name}-handle`
                    : `${$route.name}-view`
                "
                >{{
                  $t(scope.row.status === 1 ? 'action.handle' : 'action.view')
                }}</lb-button
              >
            </div>
          </template>
        </el-table-column>
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
      loading: { list: false, user: false },
      count: {},
      searchForm: {
        list: {
          page: 1,
          limit: 10,
          status: 0
        },
        user: {
          page: 1,
          limit: 10,
          nickName: ''
        },
        status: 0
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
      showDialog: false,
      statusText: {
        1: {
          type: 'danger',
          text: '未处理'
        },
        2: {
          type: 'primary',
          text: '已处理'
        }
      }
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
    toChange (index) {
      this.searchForm.list.status = index
      this.getTableDataList(1, 'list')
    },
    /**
     * @method: 反馈列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.tableData[key] = []
      this.loading[key] = true
      let searchForm = this.searchForm[key]
      let { code, data } = await this.$api.system.feedbackList(searchForm)
      this.loading[key] = false
      if (code !== 200) return
      this.tableData[key] = data.data
      this.total[key] = data.total
      this.count = {
        nopass: data.status1,
        pass: data.status2,
        all: Number(data.status1) + Number(data.status2)
      }
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
