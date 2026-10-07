<template>
  <div class="lb-goods-list">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm.list"
          ref="searchForm"
        >
          <el-form-item label="输入查询" prop="name">
            <el-input
              v-model="searchForm.list.name"
              placeholder="请输入经纪人姓名/昵称/手机号"
            ></el-input>
          </el-form-item>
          <el-form-item label="入驻时间" prop="start_time">
            <el-date-picker
              @change="getTableDataList(1, 'list')"
              v-model="searchForm.list.start_time"
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
              @click="resetForm('searchForm')"
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
        <el-table-column prop="user_id" label="用户ID"></el-table-column>
        <el-table-column prop="avatarUrl" label="微信头像">
          <template slot-scope="scope">
            <lb-image :src="scope.row.avatarUrl" />
          </template>
        </el-table-column>
        <el-table-column
          prop="nickName"
          label="微信昵称"
          :min-width="120"
        ></el-table-column>
        <el-table-column
          prop="name"
          label="经纪人姓名"
          :min-width="120"
        ></el-table-column>
        <el-table-column
          prop="mobile"
          label="手机号"
          :min-width="120"
        ></el-table-column>
        <el-table-column prop="create_time" label="入驻时间" :min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column
          prop="coach_num"
          :label="`累计邀请${$t('action.attendantName')}`"
          :min-width="120"
        ></el-table-column>
        <el-table-column
          prop="order_num"
          label="累计成交订单数量"
          :min-width="140"
        ></el-table-column>
        <el-table-column
          prop="total_cash"
          label="累计获得佣金"
          :min-width="120"
        >
          <template slot-scope="scope"> ￥{{ scope.row.total_cash }} </template>
        </el-table-column>

        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="toShowApply(scope.row, 4)"
                v-hasPermi="`${$route.name}-view`"
                >查看邀请{{ $t('action.attendantName') }}</lb-button
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
      <el-dialog
        :title="`邀请的${$t('action.attendantName')}`"
        :visible.sync="showApply"
        width="800px"
        center
      >
        <el-table
          v-loading="loading.user"
          :data="tableData.user"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          style="width: 100%"
        >
          <el-table-column
            prop="work_img"
            :label="`${$t('action.attendantName')}头像`"
          >
            <template slot-scope="scope">
              <lb-image :src="scope.row.work_img" />
            </template>
          </el-table-column>
          <el-table-column
            prop="coach_name"
            :label="`${$t('action.attendantName')}名称`"
            :min-width="120"
          ></el-table-column>
          <el-table-column
            prop="agent_name"
            label="所属代理"
            :min-width="120"
          ></el-table-column>
          <el-table-column
            prop="name"
            label="经纪人姓名"
            :min-width="120"
          ></el-table-column>
          <el-table-column
            prop="title"
            label="工作城市"
            :min-width="120"
          ></el-table-column>
        </el-table>
        <lb-page
          :batch="false"
          :page="searchForm.user.page"
          :pageSize="searchForm.user.limit"
          :total="total.user"
          @handleSizeChange="handleSizeChange($event, 'user')"
          @handleCurrentChange="handleCurrentChange($event, 'user')"
        >
        </lb-page>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click="showApply = false" v-preventReClick
            >我已知晓</el-button
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
    let checkStatus = (rule, value, callback) => {
      if (!this.subForm.status) {
        callback(new Error('请选择审核结果'))
      } else {
        callback()
      }
    }
    return {
      loading: {
        list: false,
        user: false
      },
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
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
          broker_id: ''
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
      count: {},
      showApply: false,
      subFormRules: {
        status: { required: true, validator: checkStatus, trigger: 'blur' }
      },
      showScale: false
    }
  },
  created () {
    this.getTableDataList(1, 'list')
  },
  methods: {
    resetForm (form) {
      this.$refs[form].resetFields()
      this.searchForm.list.start_time = 0
      this.searchForm.list.end_time = 0
      this.getTableDataList(1, 'list')
    },
    handleSizeChange (val, key) {
      this.searchForm[key].limit = val
      this.handleCurrentChange(1, key)
    },
    handleCurrentChange (val, key) {
      this.searchForm[key].page = val
      this.getTableDataList('', key)
    },
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.tableData[key] = []
      this.loading[key] = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))
      if (key == 'list') {
        let { start_time: time } = searchForm
        if (time && time.length > 0) {
          searchForm.start_time = time[0] / 1000
          searchForm.end_time = time[1] / 1000
        } else {
          searchForm.start_time = ''
          searchForm.end_time = ''
        }
      }
      let medel = {
        list: 'getData',
        user: 'coachList'
      }
      let methodModel = medel[key]
      let { code, data } = await this.$api.economy[methodModel](searchForm)
      this.loading[key] = false
      if (code !== 200) return
      data.data.map(item => {
        let text = item.text || '该用户没有填写备注'
        item.text = text.replace(/\n/g, '<br>')
      })
      let { total } = data
      this.tableData[key] = data.data
      this.total[key] = total
    },
    async toShowApply (data = {}, type = '') {
      this.searchForm.user.broker_id = data.id
      this.getTableDataList(1, 'user')
      this.showApply = !this.showApply
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
    width: 250px;
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
