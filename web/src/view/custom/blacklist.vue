<!--
 * @Descripttion: 客户管理
 * @Author: xiao li
 * @Date: 2021-03-11 15:42:01
 * @LastEditors: wen kun
 * @LastEditTime: 2024-04-29 18:50:06
-->

<template>
  <div class="lb-custom">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="用户ID" prop="id">
            <el-input
              v-model="searchForm.id"
              placeholder="请输入用户ID"
            ></el-input>
          </el-form-item>
          <el-form-item label="输入查询" prop="nickName">
            <el-input
              v-model="searchForm.nickName"
              placeholder="请输入微信昵称/手机号"
            ></el-input>
          </el-form-item>
          <!-- <el-form-item label="授权类型" prop="type">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.type"
              placeholder="请选择"
            >
              <el-option
                v-for="item in statusOptions"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item> -->
          <!-- <el-form-item label="加入时间" prop="start_time">
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
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="nickName" label="微信昵称"></el-table-column>
        <el-table-column prop="avatarUrl" label="微信头像">
          <template slot-scope="scope">
            <lb-image :src="scope.row.avatarUrl" />
          </template>
        </el-table-column>
        <el-table-column prop="phone" label="手机号"></el-table-column>
        <el-table-column prop="user_label" label="用户标签" min-width="180">
          <template slot-scope="scope">
            <!--:closable="pagePermission.includes('deleteTag')"-->
            <el-tag
              @close="toDelLabel(scope.row.id, item.label_id)"
              :closable="
                $route.meta.pagePermission[0].auth.includes('deleteTag')
              "
              class="mr-md mt-sm mb-sm"
              v-for="(item, index) in scope.row.user_label"
              :key="index"
              >{{ item.title }}</el-tag
            >
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="加入时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <lb-button
              size="mini"
              plain
              type="primary"
              @click="removeBlack(scope.row.id)"
              v-hasPermi="`${$route.name}-removeBlacklist`"
              >{{ $t('action.removeBlacklist') }}</lb-button
            >
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
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      statusOptions: [
        {
          label: '全部',
          value: 0
        },
        {
          // label: '已授权手机号',
          label: '未授权微信昵称/头像',
          value: 1
        },
        {
          label: '已授权微信昵称/头像',
          value: 2
        }
      ],
      pagePermission: [],
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        id: '',
        nickName: '',
        // start_time: '',
        // end_time: '',
        // type: 0
      },
      tableData: [],
      total: 0
    }
  },
  async activated () {
    this.routesItem.routes.map(item => {
      if (item.path === '/custom') {
        item.children.map(aitem => {
          if (aitem.name === 'CustomList') {
            this.pagePermission = aitem.meta.pagePermission[0].auth
          }
        })
      }
    })
    await this.getTableDataList()
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
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      //   let { start_time: time } = searchForm
      //   if (time && time.length > 0) {
      //     searchForm.start_time = time[0] / 1000
      //     searchForm.end_time = time[1] / 1000
      //   } else {
      //     searchForm.start_time = ''
      //     searchForm.end_time = ''
      //   }
      let { code, data } = await this.$api.custom.blacklist(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    removeBlack (id) {
      this.$api.custom.setBlacklist({ id, is_on: 0 }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successOper'))
          this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
          this.getTableDataList()
        }
      })
    },
    confirmDel (id, status) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, status)
      }).catch(() => {

      })
    },
    async updateItem (id, status) {
      this.$api.storeStatusUpdate({ id, status }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
            this.getTableDataList()
          }
        } else {
          if (status === -1) return
          this.getTableDataList()
        }
      })
    },
    async toDelLabel (uid, id) {
      let { code } = await this.$api.custom.delUserLabel({ user_id: uid, label_id: id })
      if (code !== 200) return
      this.$message.success(this.$t('tips.successDel'))
      this.getTableDataList()
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
</style>
