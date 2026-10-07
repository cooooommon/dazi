<!--
 * @Descripttion: 报名情况
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2024-05-16 10:15:54
-->
<template>
  <div>
    <top-nav :isBack="true"></top-nav>
    <div class="page-main">
      <div class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="提交时间" prop="range">
            <el-date-picker
              @change="toResetForm"
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
        </el-form>
      </div>
      <div class="page-search-form" style="padding-bottom: 24px">
        <el-row>
          <el-col :span="18" class="text-bold flex-y-center">数据列表</el-col>
          <el-col :span="6" class="text-right">
            <lb-button
              plain
              type="primary"
              icon="el-icon-download"
              :loading="downloadLoading"
              @click="toExportExcel"
              v-hasPermi="`${$route.name}-export`"
              >{{ $t('action.export') }}</lb-button
            >
          </el-col>
        </el-row>
        <div class="space-lg"></div>
        <el-table
          ref="multipleTable"
          v-loading="loading"
          :data="tableData"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column
            v-for="(item, index) in tableHeader"
            :key="index"
            :label="item.title"
          >
            <template slot-scope="scope" min-width="120">
              <div v-if="item.field_id == 'create_time'">
                <p>{{ scope.row[item.field_id] | handleTime(1) }}</p>
                <p>{{ scope.row[item.field_id] | handleTime(2) }}</p>
              </div>
              <img
                v-else-if="item.field_id === 'avatarUrl'"
                class="avatar"
                :src="scope.row[item.field_id]"
              />
              <span v-else>{{ scope.row[item.field_id] }}</span>
            </template>
          </el-table-column>
        </el-table>
        <lb-page
          :isShowBatch="false"
          :page="searchForm.page"
          :pageSize="searchForm.limit"
          :total="total"
          :selected="multipleSelection.length"
          @handleSizeChange="handleSizeChange"
          @handleCurrentChange="handleCurrentChange"
        >
        </lb-page>
      </div>
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
      searchForm: {
        article_id: 0,
        page: 1,
        limit: 10,
        start_time: '',
        end_time: ''
      },
      downloadLoading: false,
      multipleSelection: [],
      loading: false,
      tableHeader: [],
      tableData: [],
      total: 0
    }
  },
  async created () {
    let { id } = this.$route.query
    this.searchForm.article_id = id
    await this.getTableHeader()
    this.getTableDataList()
  },
  methods: {
    async getTableHeader () {
      let { article_id: id } = this.searchForm
      let { code, data } = await this.$api.market.subTitle({ article_id: id })
      if (code !== 200) return
      data.push({
        field_id: 'create_time',
        title: '提交时间'
      })
      data.unshift({
        field_id: 'user_id',
        title: '用户ID'
      }, {
        field_id: 'nickName',
        title: '微信昵称'
      }, {
        field_id: 'avatarUrl',
        title: '微信头像'
      })
      this.tableHeader = data
      this.loading = false
    },
    toResetForm () {
      this.$refs.multipleTable.clearSelection()
      this.getTableDataList(1)
    },
    resetForm (name) {
      this.$refs[name].resetFields()
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
      this.$api.market.subDataList(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          this.tableData = res.data.data
          this.total = res.data.total
        }
      })
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    },
    /**
       * @method 导出
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
      let ids = []
      let multipleSelection = JSON.parse(JSON.stringify(this.multipleSelection))
      if (typeof (multipleSelection) === 'object' && multipleSelection && multipleSelection.length > 0) {
        multipleSelection.map(item => {
          ids.push(item.id)
        })
      }
      searchForm.id = ids
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
      let dwonlaodUrl = `${url}/massage/admin/AdminExcel/subDataList${keywords}&token=${token}`
      window.location.href = dwonlaodUrl
      setTimeout(() => {
        this.downloadLoading = false
      }, 5000)
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
.avatar {
  width: 30px;
  height: 30px;
}
</style>
