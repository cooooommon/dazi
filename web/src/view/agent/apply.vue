<!--
 * @Description: 代理申请
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2024-05-06 16:05:33
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-appclass-classroom-list">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="提交时间" prop="start_time">
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
          <el-form-item label="状态" prop="status">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.status"
              placeholder="请选择"
            >
              <el-option
                v-for="item in statusList"
                :key="item.id"
                :label="item.title"
                :value="item.id"
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
        tooltip-effect="dark"
        style="width: 100%"
      >
        <el-table-column prop="name" label="姓名"> </el-table-column>
        <el-table-column prop="mobile" label="手机号"> </el-table-column>
        <el-table-column prop="city" label="申请加入的城市"> </el-table-column>

        <el-table-column prop="create_time" label="提交时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            <el-tag :type="statusText[scope.row.status].type">{{
              statusText[scope.row.status].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                v-show="scope.row.status == 1"
                @click="toRead(scope.row.id)"
                v-hasPermi="`${$route.name}-reviewed`"
                >已阅</lb-button
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
export default {
  components: {},
  data () {
    return {
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      loading: false,
      storeList: [],
      searchForm: {
        page: 1,
        limit: 10,
        status: 0,
        start_time: '',
        end_time: ''
      },
      tableData: [],
      total: 0,
      showDialog: false,
      subForm: {
        id: '',
        title: '',
        top: ''
      },
      subFormRules: {
        title: { required: true, type: 'string', message: '请输入标签名称', trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' }
      },
      statusList: [
        { id: 0, title: '全部' },
        { id: 1, title: '未读' },
        { id: 2, title: '已读' }
      ],
      statusText: {
        1: { text: '未读', type: 'danger' },
        2: { text: '已读', type: 'info' }
      }
    }
  },
  async created () {
    this.getTableDataList()
  },
  methods: {
    resetForm (name) {
      this.$refs[name].resetFields()
      this.searchForm.start_time = ''
      this.searchForm.end_time = ''
      this.getTableDataList(1)
    },
    toRead (id) {
      this.$api.agent.joinRead({ id }).then(res => {
        this.loading = false
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successOper'))
          this.handleCurrentChange(1)
        }
      })
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
     * @method: 获取列表
     */
    getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      this.loading = true
      this.$api.agent.joinList(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          this.tableData = res.data.data
          this.total = res.data.total
        }
      })
    },
    /**
     * @method: 删除
     * @param {*} id
     */
    confirmDel (id) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, -1)
      })
    },
    /**
     * @method: 上下架
     */
    async updateItem (id, status) {
      this.$api.shop.commentLableUpdate({ id, status }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status !== -1) return
          this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
          this.getTableDataList()
        } else {
          if (status === -1) return
          this.getTableDataList()
        }
      })
    },
    async toShowDialog (item = {}) {
      for (let key in this.subForm) {
        this.subForm[key] = item[key]
      }
      this.showDialog = !this.showDialog
    },
    async submitFormInfo () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let methodModel = this.subForm.id ? 'commentLableUpdate' : 'commentLableAdd'
        let { code } = await this.$api.shop[methodModel](this.subForm)
        if (code !== 200) return
        this.$message.success(this.$t(this.subForm.id ? 'tips.successRev' : 'tips.successSub'))
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
.lb-appclass-classroom-list {
  width: 100%;
  .page-main {
    width: 100%;
    .el-input,
    .el-select,
    .el-input-number {
      width: 200px;
    }
    .dialog-form {
      .el-input,
      .el-select,
      .el-input-number {
        width: 300px;
      }
    }
  }
}
</style>
