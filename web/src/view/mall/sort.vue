<!--
 * @Descripttion: 商品分类
 * @Author: wen kun
 * @Date: 2021-03-11 15:42:01
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:07:09
-->

<template>
  <div class="lb-custom">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <lb-button
          size="medium"
          type="primary"
          icon="el-icon-plus"
          @click="setAddDialog"
          v-hasPermi="`${$route.name}-add`"
          >添加分类</lb-button
        >
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="name" label="分类名"></el-table-column>
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
        <el-table-column prop="sort" label="排序值"></el-table-column>
        <el-table-column prop="create_time" label="创建时间"></el-table-column>
        <el-table-column label="操作" min-width="120" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="toShowApply(scope.row.id)"
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
    <el-dialog
      :title="dialogTitle ? '添加分类' : '编辑分类'"
      :visible.sync="addDialog"
      width="500px"
      center
    >
      <el-form
        class="dialog-form"
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="100px"
      >
        <el-form-item label="分类名" prop="name">
          <el-input
            v-model="subForm.name"
            maxlength="10"
            show-word-limit
            placeholder="请输入分类名"
          ></el-input>
        </el-form-item>
        <el-form-item label="排序值" prop="sort">
          <el-input-number
            class="lb-input-number"
            :min="0"
            :controls="false"
            v-model="subForm.sort"
            placeholder="请输入排序值"
          ></el-input-number>
          <lb-tool-tips>值越大, 排序越靠前</lb-tool-tips>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addDialog = false">取 消</el-button>
        <el-button type="primary" @click="submitFormInfo" v-preventReClick
          >确 定</el-button
        >
      </span>
    </el-dialog>
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
        limit: 10
      },
      tableData: [],
      total: 0,
      addDialog: false,
      dialogTitle: true,
      subForm: {
        name: '',
        sort: 0,
        id: 0
      },
      subFormRules: {
        name: { required: true, validator: this.$reg.isNotNull, text: '分类名', reg_type: 2, trigger: 'blur' }
      }
    }
  },
  activated () {
    this.getTableDataList()
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
    setAddDialog () {
      this.subForm = {
        name: '',
        sort: 0
      }
      this.dialogTitle = true
      this.addDialog = true
    },
    async toShowApply (id) {
      let { data } = await this.$api.mall.editCarte({ id })
      this.subForm.id = 0
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      this.dialogTitle = false
      this.addDialog = true
    },
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let { searchForm } = this
      let { code, data } = await this.$api.mall.carteList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
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
      this.$api.mall.carteStatus({ id, status }).then(res => {
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
    async submitFormInfo () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        this.subForm.sort = this.subForm.sort || 0
        let { code } = await this.$api.mall[this.subForm.id ? 'editCartePost' : 'addCarte'](this.subForm)
        if (code !== 200) return
        this.$message.success(this.$t('tips.successSub'))
        this.addDialog = false
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
.dialog-form {
  .el-input,
  .el-select,
  .el-input-number {
    width: 300px;
  }
}
</style>
