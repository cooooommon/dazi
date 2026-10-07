<!--
 * @Description: 评论管理
 * @Author: xiao li
 * @Date: 2023-01-28 16:54:46
 * @LastEditTime: 2024-05-16 10:11:28
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-dynamic-comment">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <el-button
          @click="toChange(0)"
          :type="searchForm.status === 0 ? 'primary' : ''"
          plain
          size="medium"
          >全部（{{ count.all || 0 }}）</el-button
        >
        <el-button
          @click="toChange(1)"
          :type="searchForm.status === 1 ? 'primary' : ''"
          plain
          size="medium"
          >未审核（{{ count.ing || 0 }}）</el-button
        >
        <el-button
          @click="toChange(2)"
          :type="searchForm.status === 2 ? 'primary' : ''"
          plain
          size="medium"
          >审核通过（{{ count.pass || 0 }}）</el-button
        >
        <el-button
          @click="toChange(3)"
          :type="searchForm.status === 3 ? 'primary' : ''"
          plain
          size="medium"
          >已驳回（{{ count.nopass || 0 }}）</el-button
        >
      </el-row>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="输入查询" prop="user_name">
            <el-input
              v-model="searchForm.user_name"
              placeholder="请输入用户姓名"
            ></el-input>
          </el-form-item>
          <el-form-item label="评论时间" prop="start_time">
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
          width="80"
          fixed
        ></el-table-column>
        <el-table-column
          prop="nickName"
          label="评论用户"
          width="200"
        ></el-table-column>
        <el-table-column
          prop="text"
          label="评论内容"
          min-width="400"
        ></el-table-column>
        <el-table-column prop="create_time" label="评论时间" min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" min-width="100">
          <template slot-scope="scope">
            <el-tag :type="statusText[scope.row.status].type">
              {{ statusText[scope.row.status].text }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <div v-if="scope.row.status === 1">
                <lb-button
                  size="mini"
                  plain
                  type="primary"
                  @click="confirmDel(scope.row.id, 2)"
                  v-hasPermi="`${$route.name}-pass`"
                  >通过</lb-button
                >
                <lb-button
                  size="mini"
                  plain
                  type="warning"
                  @click="confirmDel(scope.row.id, 3)"
                  v-hasPermi="`${$route.name}-reject`"
                  >驳回</lb-button
                >
              </div>
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id, -1)"
                v-show="scope.row.status !== 1"
                v-hasPermi="`${$route.name}-delete`"
                >{{ $t('action.delete') }}</lb-button
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
import { mapState } from 'vuex'
import moment from 'moment'
export default {
  data () {
    return {
      statusText: {
        1: {
          type: 'info',
          text: '未审核'
        },
        2: {
          type: '',
          text: '审核通过'
        },
        3: {
          type: 'danger',
          text: '已驳回'
        }
      },
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      searchForm: {
        page: 1,
        limit: 10,
        status: 0,
        start_time: '',
        end_time: '',
        user_name: ''
      },
      loading: false,
      tableData: [],
      total: 0,
      count: {}
    }
  },
  activated () {
    this.getTableDataList(1)
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
    async toChange (index) {
      this.searchForm.status = index
      this.getTableDataList(1)
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
      if (searchForm.status === -1) {
        searchForm.is_update = 1
        delete searchForm.status
      }
      let { code, data } = await this.$api.dynamic.commentList(
        searchForm
      )
      this.loading = false
      if (code !== 200) return
      let { all, nopass, ing, pass, total } = data
      this.tableData = data.data
      this.total = total
      this.count = { all, nopass, ing, pass }
    },
    confirmDel (id, status) {
      let msg = {
        '-1': '删除',
        2: '通过',
        3: '驳回'
      }
      this.$confirm(`你确认要${msg[status]}评论吗？`, this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, status)
      }).catch(() => { })
    },
    async updateItem (id, status) {
      let methodModel = status === -1 ? 'commentDel' : 'commentCheck'
      this.$api.dynamic[methodModel]({ id, status }).then((res) => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
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
.el-form {
  .el-input {
    width: 200px;
  }
  .el-image {
    width: 120px;
    height: 120px;
  }
  .el-textarea {
    width: 600px;
  }
}
</style>
