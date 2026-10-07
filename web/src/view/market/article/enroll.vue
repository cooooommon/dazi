<!--
 * @Description: 报名信息
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2023-03-31 15:35:38
 * @LastEditors: xiao li
-->

<template>
  <div class="lb-appclass-classroom-list">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <lb-button
          size="medium"
          type="primary"
          icon="el-icon-plus"
          @click="toShowDialog"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.MarketArticleEnrollAdd') }}</lb-button
        >
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"> </el-table-column>
        <el-table-column prop="title" label="字段名称"> </el-table-column>
        <el-table-column prop="type" label="字段类型">
          <template slot-scope="scope">
            {{ fieldType[scope.row.field_type * 1 - 1] }}
          </template>
        </el-table-column>
        <el-table-column prop="type" label="是否必填">
          <template slot-scope="scope">
            {{ requiredType[scope.row.is_required] }}
          </template>
        </el-table-column>
        <el-table-column prop="top" label="排序值"> </el-table-column>
        <el-table-column prop="status" label="是否上架">
          <template slot-scope="scope">
            <el-switch
              :disabled="pagePermission.includes('edit') ? false : true"
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
                @click="toShowDialog(scope.row)"
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

      <el-dialog
        :title="
          $t(
            subForm.id
              ? 'menu.MarketArticleEnrollEdit'
              : 'menu.MarketArticleEnrollAdd'
          )
        "
        :visible.sync="showDialog"
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
          <el-form-item label="字段名称" prop="title">
            <el-input
              v-model="subForm.title"
              maxlength="10"
              show-word-limit
              placeholder="请输入字段名称"
            ></el-input>
          </el-form-item>
          <el-form-item label="字段类型" prop="field_type">
            <el-radio-group :disabled="!!subForm.id" v-model="subForm.field_type">
              <el-radio :label="1">姓名</el-radio>
              <el-radio :label="2">手机号</el-radio>
              <el-radio :label="3">其他</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="是否必填" prop="is_required">
            <el-radio-group v-model="subForm.is_required">
              <el-radio :label="0">非必填</el-radio>
              <el-radio :label="1">必填</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="排序值" prop="top">
            <el-input-number
              class="lb-input-number"
              v-model="subForm.top"
              :controls="false"
              :precision="0"
              :min="0"
              placeholder="请输入排序值"
            ></el-input-number>
            <lb-tool-tips>值越大, 排序越靠前</lb-tool-tips>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="submitFormInfo" v-preventReClick
            >确 定</el-button
          >
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  components: {},
  data () {
    return {
      pagePermission: [],
      fieldType: ['姓名', '手机号', '其他'],
      requiredType: ['非必填', '必填'],
      loading: false,
      storeList: [],
      searchForm: {
        page: 1,
        limit: 10
      },
      tableData: [],
      total: 0,
      showDialog: false,
      subForm: {
        id: 0,
        title: '',
        field_type: 1,
        is_required: 0,
        top: 0
      },
      subFormRules: {
        title: { required: true, validator: this.$reg.isNotNull, text: '字段名称', reg_type: 2, trigger: 'blur' },
        field_type: { required: true, type: 'number', message: '请选择字段类型', trigger: 'blur' },
        is_required: { required: true, type: 'number', message: '请选择是否必填', trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' }
      }
    }
  },
  activated () {
    this.pagePermission = this.$route.meta.pagePermission.filter(item => {
      return item.title === this.$route.name
    })[0].auth
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
    /**
     * @method: 获取列表
     */
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let { code, data } = await this.$api.market.fieldList(this.searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
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
      this.$api.market.fieldUpdate({ id, status }).then(res => {
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
    async toShowDialog (item = {
      id: 0,
      title: '',
      field_type: 1,
      top: 0
    }) {
      item = JSON.parse(JSON.stringify(item))
      for (let key in this.subForm) {
        this.subForm[key] = item[key]
      }
      this.showDialog = !this.showDialog
    },
    /**
     * @method: 新增/删除
     */
    async toAddItem (key, index) {
      if (key === 1) {
        this.subForm.select.splice(index, 1)
      } else {
        this.subForm.select.push({ title: '' })
      }
    },
    async submitFormInfo () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        let methodModel = subForm.id ? 'fieldUpdate' : 'fieldAdd'
        let { code } = await this.$api.market[methodModel](subForm)
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
