<!--
 * @Description: 会员套餐
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-08 16:06:24
 * @LastEditTime: 2024-11-26 15:49:04
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-custom-member-card-list">
    <top-nav></top-nav>
    <div class="page-main">
      <el-row class="page-top-operate flex-warp">
        <lb-button
          type="primary"
          @click="$router.push(`/custom/memberdiscount/card/edit`)"
          icon="el-icon-plus"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.CustomMemberdiscountCardAdd') }}</lb-button
        >
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" width="80"> </el-table-column>
        <el-table-column prop="title" label="套餐名称"> </el-table-column>
        <el-table-column prop="price" label="现价">
          <template slot-scope="scope"> ¥{{ scope.row.price }} </template>
        </el-table-column>
        <el-table-column prop="init_price" label="划线价">
          <template slot-scope="scope"> ¥{{ scope.row.init_price }} </template>
        </el-table-column>
        <el-table-column prop="day" label="有效期">
          <template slot-scope="scope">购买后{{ scope.row.day }}天内</template>
        </el-table-column>
        <el-table-column prop="color" label="标签">
          <template slot-scope="scope">
            <el-tag type="warning" size="small" v-if="scope.row.icon">{{
              scope.row.icon
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="top" label="排序值"> </el-table-column>
        <el-table-column prop="create_time" label="创建时间" min-width="110">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template></el-table-column
        >
        <el-table-column label="操作" width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                type="primary"
                size="mini"
                plain
                @click="
                  $router.push(
                    `/custom/memberdiscount/card/edit?id=${scope.row.id}`
                  )
                "
                v-hasPermi="`${$route.name}-edit`"
                >{{ $t('action.edit') }}</lb-button
              >
              <lb-button
                type="danger"
                size="mini"
                plain
                @click="confirmDel(scope.row.id, -1)"
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
      tableData: [],
      searchForm: {
        page: 1,
        limit: 10
      },
      total: 0
    }
  },
  async activated () {
    let page = 1
    if (Number(window.sessionStorage.getItem('currentPage'))) {
      page = Number(window.sessionStorage.getItem('currentPage'))
      window.sessionStorage.removeItem('currentPage')
    }
    this.getTableDataList(page)
  },
  beforeRouteLeave (to, from, next) {
    if (to.name === 'CustomMemberdiscountCardAddEdit') {
      let { id = 0 } = to.query
      if (id) {
        window.sessionStorage.setItem('currentPage', this.searchForm.page)
      }
    }
    next()
  },
  methods: {
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handleCurrentChange(1)
    },
    handleCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    getTableDataList () {
      this.loading = true
      let { searchForm } = this
      this.$api.memberdiscount.cardList(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          let { data, total } = res.data
          this.tableData = data
          this.total = total
        }
      })
    },
    confirmDel (id, status) {
      const h = this.$createElement
      this.$confirm(h('p', null, [
        h('p', null, this.$t('tips.confirmDelete')),
        h('p', { style: 'font-size:13px;line-height:1.2;color: red' }, '删除之后，已购买该会员卡的用户可继续享受对应会员权益，确认删除该条数据吗')
      ]), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, status)
      }).catch(() => {

      })
    },
    async updateItem (id, status) {
      this.$api.memberdiscount.cardUpdate({ id, status }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
          }
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
.page-main {
  .el-input,
  .el-input-number,
  .el-select {
    width: 300px;
  }
  .el-textarea {
    width: 600px;
  }
  .edui-default {
    width: 95%;
  }
}
</style>
