<!--
 * @Description: ''
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2024-04-18 14:53:13
 * @LastEditors: wen kun
-->
<template>
  <div class="lb-examine">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <lb-button
          size="medium"
          type="primary"
          icon="el-icon-plus"
          @click="$router.push(`/market/article/edit`)"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.MarketArticleAdd') }}</lb-button
        >
      </el-row>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="输入查询" prop="title">
            <el-input
              v-model="searchForm.title"
              placeholder="请输入文章标题"
            ></el-input>
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
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="title" label="文章标题"></el-table-column>
        <el-table-column prop="top" label="排序值"></el-table-column>
        <el-table-column prop="create_time" label="创建时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="是否上架">
          <template slot-scope="scope">
            <el-switch
              :disabled="
                $route.meta.pagePermission[0].auth.includes('edit')
                  ? false
                  : true
              "
              v-model="scope.row.status"
              :active-value="1"
              :inactive-value="0"
              @change="updateItem(scope.row.id, scope.row.status)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="120" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="$router.push(`/market/article/edit?id=${scope.row.id}`)"
                v-hasPermi="`${$route.name}-edit`"
                >{{ $t('action.edit') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id, -1)"
                v-hasPermi="`${$route.name}-delete`"
                >{{ $t('action.delete') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="success"
                @click="
                  $router.push(`/market/article/record?id=${scope.row.id}`)
                "
                v-show="scope.row.is_form === 1"
                v-hasPermi="`${$route.name}-formData`"
                >{{ $t('action.formData') }}</lb-button
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
  data () {
    return {
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        title: ''
      },
      tableData: [],
      total: 0
    }
  },
  activated () {
    this.getTableDataList(1)
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
      let { code, data } = await this.$api.market.articleList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    confirmDel (id, status) {
      this.$confirm(this.$t(status === 3 ? 'tips.confirmNoPass' : 'tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, status)
      }).catch(() => { })
    },
    async updateItem (id, status) {
      this.$api.market.articleUpdate({ id, status }).then((res) => {
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
