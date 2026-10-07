<!--
 * @Descripttion: 轮播图设置
 * @Author: xiao li
 * @Date: 2021-03-11 15:42:01
 * @LastEditors: wen kun
 * @LastEditTime: 2024-06-18 11:30:33
-->

<template>
  <div class="lb-system-banner">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <lb-button
          type="primary"
          icon="el-icon-plus"
          @click="$router.push(`/service/banner/edit`)"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.ServiceBannerAdd') }}</lb-button
        >
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <!-- <el-table-column prop="id" label="ID"></el-table-column> -->
        <el-table-column prop="img" label="图片">
          <template slot-scope="scope">
            <lb-image :src="scope.row.img" />
          </template>
        </el-table-column>
        <!-- <el-table-column prop="top" label="关联内容">
          <template slot-scope="scope">
            <el-tag type="primary" v-if="scope.row.banner_type == 2">
              视频
            </el-tag>
            <el-tag :type="connectType[scope.row.connect_type].type" v-else>
              {{ connectType[scope.row.connect_type].text }}
            </el-tag>
          </template>
        </el-table-column> -->
        <el-table-column prop="top" label="排序值"></el-table-column>
        <el-table-column label="是否上架">
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
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="$router.push(`/service/banner/edit?id=${scope.row.id}`)"
                v-hasPermi="`${$route.name}-edit`"
                >{{ $t('action.edit') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id)"
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
import moment from 'moment'
export default {
  data () {
    return {
      loading: false,
      connectType: {
        1: { type: 'primary', text: '查看大图' },
        2: { type: 'danger', text: '文章' }
      },
      searchForm: {
        title: '',
        page: 1,
        limit: 10
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
      let { searchForm } = this
      let { code, data } = await this.$api.service.bannerList(searchForm)
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
      }).then(() => {
        this.updateItem(id, -1)
      }).catch(() => {

      })
    },
    async updateItem (id, status) {
      this.$api.service.bannerUpdate({ id, status }).then(res => {
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
