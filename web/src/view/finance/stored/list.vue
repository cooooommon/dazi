<!--
 * @Description: 储值套餐
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2023-03-13 19:06:59
 * @LastEditors: xiao li
-->

<template>
  <div class="lb-appclass-classroom-list">
    <top-nav />
    <div class="page-main">
      <lb-button
        size="medium"
        type="primary"
        icon="el-icon-plus"
        @click="toShowDialog"
        v-hasPermi="`${$route.name}-add`"
        >{{ $t('menu.FinanceStoredAdd') }}</lb-button
      >
      <div class="space-lg"></div>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" fixed> </el-table-column>
        <el-table-column prop="title" label="套餐名称" min-width="200">
        </el-table-column>
        <el-table-column prop="price" label="购买价格" min-width="120">
          <template slot-scope="scope"> ¥{{ scope.row.price }} </template>
        </el-table-column>
        <el-table-column prop="true_price" label="实际充值" min-width="120">
          <template slot-scope="scope"> ¥{{ scope.row.true_price }} </template>
        </el-table-column>
        <el-table-column prop="top" label="排序值"> </el-table-column>
        <el-table-column prop="create_time" label="创建时间" min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="address" label="是否上架">
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
        <el-table-column label="操作" min-width="140" fixed="right">
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
        :title="subForm.id ? '编辑储值套餐' : '新增储值套餐'"
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
          <el-form-item label="套餐名称" prop="title">
            <el-input
              v-model="subForm.title"
              maxlength="20"
              show-word-limit
              placeholder="请输入套餐名称"
            ></el-input>
          </el-form-item>
          <el-form-item label="购买价格" prop="price">
            <el-input-number
              class="lb-input-number"
              :min="0.01"
              :precision="2"
              :controls="false"
              v-model="subForm.price"
              placeholder="请输入购买价格"
            ></el-input-number>
            <lb-tool-tips>用户所要支付的价格</lb-tool-tips>
          </el-form-item>
          <el-form-item label="实际充值" prop="true_price">
            <el-input
              v-model="subForm.true_price"
              placeholder="请输入实际充值"
            ></el-input>
            <lb-tool-tips>实际充入会员卡的价格</lb-tool-tips>
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
    let validatePrice = (rule, value, callback) => {
      if (!/^(([0-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/.test(value)) {
        callback(new Error('请输入正确的金额，最多保留两位小数'))
      } else {
        callback()
      }
    }
    return {
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
        id: '',
        title: '',
        price: '',
        true_price: '',
        top: 0
      },
      subFormRules: {
        title: { required: true, validator: this.$reg.isNotNull, text: '套餐名称', reg_type: 2, trigger: 'blur' },
        price: { required: true, validator: validatePrice, trigger: 'blur' },
        true_price: { required: true, validator: validatePrice, trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' }
      }
    }
  },
  async created () {
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
    getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      this.$api.finance.cardList(this.searchForm).then(res => {
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
      this.$api.finance.cardUpdate({ id, status }).then(res => {
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
        let methodModel = this.subForm.id ? 'cardUpdate' : 'cardAdd'
        let { code } = await this.$api.finance[methodModel](this.subForm)
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
