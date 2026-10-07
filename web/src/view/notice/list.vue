<!--
 * @Description: 通知
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-12-25 16:47:38
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
          <el-form-item label="推送时间" prop="start_time">
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
        </el-form>
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="text" label="消息内容" min-width="300">
          <template slot-scope="scope">
            <div class="flex-y-center">
              {{ $t('action.attendantName') }}ID：
              <div class="c-warning">{{ scope.row.coach_id }}</div>
              ，{{ $t('action.attendantName') }}昵称：
              <div class="c-warning">{{ scope.row.coach_name }}</div>
            </div>
            <div>{{ scope.row.text }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="推送时间">
          <template slot-scope="scope">
            <div>{{ scope.row.create_time | handleTime(1) }}</div>
            <div>{{ scope.row.create_time | handleTime(2) }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="have_look" label="阅读状态">
          <template slot-scope="scope">
            {{ statusType[scope.row.have_look] }}
          </template>
        </el-table-column>
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="updateItem(scope.row.id, 1)"
                v-show="scope.row.have_look === 0"
                v-hasPermi="`${$route.name}-read`"
                >阅读</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id)"
                v-hasPermi="`${$route.name}-delete`"
                v-show="scope.row.have_look === 1"
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
import moment from 'moment'
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
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
        have_look: -1
      },
      tableData: [],
      total: 0
    }
  },
  activated () {
    let { have_look: look = -1 } = this.$route.query
    this.searchForm.have_look = look * 1
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
      let { code, data } = await this.$api.notice.policeList(searchForm)
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
    async updateItem (id, status) {
      let param = status === -1 ? { id, status } : { id, have_look: status }
      this.$api.notice.policeUpdate(param).then((res) => {
        if (res.code === 200) {
          this.$message.success(
            this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper')
          )
          if (status === -1) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
          } else {
            let { police_num: num = 0 } = this.routesItem
            if (num * 1 > 0) {
              this.changeRoutesItem({ key: 'police_num', val: num * 1 - 1 })
            }
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
