<!--
 * @Description: 秒杀活动
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2024-10-18 16:59:01
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-appclass-classroom-list">
    <top-nav />
    <div class="page-main">
      <lb-button
        size="medium"
        type="primary"
        icon="el-icon-plus"
        @click="$router.push('/storeshop/seckill/edit')"
        v-hasPermi="`${$route.name}-add`"
        >创建活动</lb-button
      >
      <div class="space-lg"></div>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="套餐名" prop="name">
            <el-input
              v-model="searchForm.name"
              placeholder="输入套餐名查询"
            ></el-input>
          </el-form-item>
          <el-form-item label="所属门店" prop="store_id">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.store_id"
              placeholder="请选择"
            >
              <el-option
                v-for="item in storeList"
                :key="item.id"
                :label="item.name"
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
        <el-table-column prop="id" label="ID"> </el-table-column>
        <el-table-column prop="name" label="秒杀商品名"> </el-table-column>
        <el-table-column prop="store_name" label="所属门店"> </el-table-column>
        <el-table-column prop="stock" label="活动总库存"> </el-table-column>
        <el-table-column prop="use_stock" label="剩余库存">
          <template slot-scope="scope">
            <div>{{ scope.row.stock - scope.row.use_stock }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="start_time" label="活动时间" min-width="160">
          <template slot-scope="scope">
            <p>{{ scope.row.start_time | handleTime(3) }} 至</p>
            <p>{{ scope.row.end_time | handleTime(3) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="price" label="秒杀价格">
          <template slot-scope="scope">
            <p>￥{{ scope.row.price }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="is_ad" label="设为广告">
          <template slot-scope="scope">
            <el-switch
              :disabled="
                $route.meta.pagePermission[0].auth.includes('edit')
                  ? false
                  : true
              "
              v-model="scope.row.is_ad"
              :active-value="1"
              :inactive-value="0"
              @change="updateItem(scope.row.id, scope.row.is_ad)"
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
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="
                  $router.push('/storeshop/seckill/edit?id=' + scope.row.id)
                "
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
  components: {},
  data () {
    return {
      loading: false,
      storeList: [],
      searchForm: {
        page: 1,
        limit: 10,
        name: '',
        store_id: 0
      },
      tableData: [],
      total: 0,
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
      },
      statusOptions: [
        {
          label: '全部',
          value: 0
        },
        {
          label: '申请中',
          value: 1
        },
        {
          label: '已通过',
          value: 2
        },
        {
          label: '已驳回',
          value: 4
        },
        {
          label: '重新审核',
          value: 3
        }
      ],
    }
  },
  async created () {
    this.getTableDataList()
    this.getStoreList()
  },
  methods: {
    async getStoreList (type = 1) {
      let { code, data } = await this.$api.storeshop.getStoreList()
      if (code !== 200) return
      data.unshift({ name: '全部', id: 0 })
      this.storeList = data
    },
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
      this.$api.storeshop.seckillGetList(this.searchForm).then(res => {
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
      this.$confirm('删除后，活动不再展示在手机端，用户和商家都不可见，确认删除该数据吗？', this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, -1, 'status')
      })
    },
    /**
     * @method: 上下架
     */
    async updateItem (id, status, type) {
      let param = {
        id,
        is_ad: status
      }
      if (type) {
        param = {
          id, status
        }
      }
      this.$api.storeshop.seckillEdit(param).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          // if (status !== -1) return
          if (type) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
          }
          this.getTableDataList()
        } else {
          if (status === -1) return
          this.getTableDataList()
        }
      })
    },
    getCover (img, key) {
      this.subForm[key] = img
    },
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
