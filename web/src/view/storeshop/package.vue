<!--
 * @Description: 团购/套餐管理
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2024-04-29 16:24:40
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-appclass-classroom-list">
    <top-nav :isBack="true" />
    <div class="page-main">
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"> </el-table-column>
        <el-table-column prop="cover" label="封面图">
          <template slot-scope="scope">
            <lb-image :src="scope.row.cover" />
          </template>
        </el-table-column>
        <el-table-column prop="name" label="套餐名称"> </el-table-column>
        <el-table-column prop="price" label="现价"> </el-table-column>
        <el-table-column prop="init_price" label="原价"> </el-table-column>
        <el-table-column prop="total_sale" label="年售"> </el-table-column>
        <el-table-column prop="true_sale" label="真实销量"> </el-table-column>
        <el-table-column prop="store_name" label="所属门店"> </el-table-column>
        <el-table-column prop="status" label="是否上架">
          <template slot-scope="scope">
            <el-switch
              :disabled="
                $route.meta.pagePermission[0].auth.includes('packageEdit')
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
        <el-table-column prop="create_time" label="创建时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="
                  $router.push(`/storeshop/package/add?id=${scope.row.id}`)
                "
                v-hasPermi="`${$route.name}-packageEdit`"
                >{{ $t('action.edit') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id)"
                v-hasPermi="`${$route.name}-packageDelete`"
                >{{ $t('action.delete') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="success"
                @click="
                  $router.push(
                    `/storeshop/package/add?id=${scope.row.id}&type=copy`
                  )
                "
                v-hasPermi="`${$route.name}-packageCopy`"
                >{{ $t('action.copy') }}</lb-button
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
        :title="subForm.id ? '编辑分类' : '添加分类'"
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
          <el-form-item label="分类名称" prop="name">
            <el-input
              v-model="subForm.name"
              maxlength="5"
              show-word-limit
              placeholder="请输入分类名称"
            ></el-input>
          </el-form-item>
          <el-form-item label="图标" prop="img">
            <lb-cover
              :fileList="subForm.img"
              @selectedFiles="getCover($event, 'img')"
            ></lb-cover>
            <lb-tool-tips>图标建议尺寸: 50 * 50</lb-tool-tips>
          </el-form-item>
          <el-form-item label="排序值" prop="top">
            <el-input-number
              class="lb-input-number"
              :min="0"
              :controls="false"
              v-model="subForm.top"
              placeholder="请输入排序值"
            ></el-input-number>
            <lb-tool-tips>值越大, 排序越靠前</lb-tool-tips>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="submitFormInfo">确 定</el-button>
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
      loading: false,
      storeList: [],
      searchForm: {
        page: 1,
        limit: 10,
        name: '',
        store_id: ''
      },
      tableData: [],
      total: 0,
      showDialog: false,
      subForm: {
        id: '',
        name: '',
        top: 0,
        img: ''
      },
      subFormRules: {
        name: { required: true, type: 'string', message: '请输入分类名称', trigger: 'blur' },
        img: { required: true, type: 'array', message: '请上传图标', trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' }
      }
    }
  },
  async created () {
    let { id } = this.$route.query
    if (id) {
      this.searchForm.store_id = id
    }
    this.getTableDataList()
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
    /**
     * @method: 获取列表
     */
    getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      this.$api.storeshop.packageList(this.searchForm).then(res => {
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
      this.$api.storeshop.packageUpdateStatus({ id, status }).then(res => {
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
    async toShowDialog (item = { top: 0 }) {
      item.img = item.img ? [{ url: item.img }] : []
      for (let key in this.subForm) {
        this.subForm[key] = item[key]
      }
      this.showDialog = !this.showDialog
    },
    getCover (img, key) {
      this.subForm[key] = img
    },
    async submitFormInfo () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      subForm.img = subForm.img[0].url
      if (flag) {
        let methodModel = subForm.id ? 'typeUpdate' : 'typeAdd'
        let { code } = await this.$api.storeshop[methodModel](subForm)
        if (code !== 200) return
        this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
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
