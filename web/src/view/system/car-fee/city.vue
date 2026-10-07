<!--
 * @Descripttion: 交易设置
 * @Author: xiao li
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-25 17:37:40
-->
<template>
  <div class="lb-system-transaction">
    <top-nav />
    <div class="page-main">
      <lb-tips>
        未单独设置车费的城市，将使用默认的全局设置车费模式，如果有单独设置城市车费，则全局模式失效
      </lb-tips>

      <el-row class="page-top-operate">
        <lb-button
          type="primary"
          icon="el-icon-plus"
          @click="toShowDialog"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.SystemCarFeeCityAdd') }}</lb-button
        >
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" fixed></el-table-column>
        <el-table-column
          prop="city_name"
          label="城市"
          min-width="120"
        ></el-table-column>
        <el-table-column label="起步距离" prop="start_distance" min-width="120">
          <template slot-scope="scope">
            {{ `${scope.row.start_distance} km` }}
          </template>
        </el-table-column>
        <el-table-column label="起步价" prop="start_price" min-width="120">
          <template slot-scope="scope">
            {{ `${scope.row.start_price} 元` }}
          </template>
        </el-table-column>
        <el-table-column label="里程计价" prop="distance_price" min-width="120">
          <template slot-scope="scope">
            {{ `${scope.row.distance_price} 元/km` }}
          </template>
        </el-table-column>
        <el-table-column label="" prop="invented_distance" min-width="120">
          <template slot="header" slot-scope="scope">
            <div>
              虚拟里程
              <lb-tool-tips padding="11"
                >虚拟里程用于
                距离计算短、车费计算少的情况可在后台增加一部分虚拟里程，减少{{
                  $t('action.attendantName')
                }}损失
                <div class="mt-sm">
                  用户端显示的距离=实际距离+实际距离*虚拟里程百分比
                </div>
              </lb-tool-tips>
            </div>
          </template>
          <template slot-scope="scope">
            {{ `${scope.row.invented_distance} %` }}
          </template>
        </el-table-column>
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
        <el-table-column prop="create_time" label="创建时间" min-width="120">
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
              ? 'menu.SystemCarFeeCityEdit'
              : 'menu.SystemCarFeeCityAdd'
          )
        "
        :visible.sync="showDialog"
        width="550px"
        center
      >
        <el-form
          @submit.native.prevent
          :model="subForm"
          ref="subForm"
          :rules="subFormRules"
          label-width="120px"
        >
          <el-form-item label="城市" prop="city_id">
            <el-select v-model="subForm.city_id" placeholder="请选择">
              <el-option
                v-for="item in base_city"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="起步距离" prop="start_distance">
            <el-input
              v-model="subForm.start_distance"
              placeholder="请输入起步距离"
            >
              <template slot="append">km</template>
            </el-input>
          </el-form-item>
          <el-form-item label="起步价" prop="start_price">
            <el-input v-model="subForm.start_price" placeholder="请输入起步价">
              <template slot="append">元</template>
            </el-input>
          </el-form-item>
          <el-form-item label="里程计价" prop="distance_price">
            <el-input
              v-model="subForm.distance_price"
              placeholder="请输入里程计价"
            >
              <template slot="append">元/km</template>
            </el-input>
          </el-form-item>
          <el-form-item label="虚拟里程" prop="invented_distance">
            <el-input
              v-model="subForm.invented_distance"
              placeholder="请输入虚拟里程"
            >
              <template slot="append">%</template>
            </el-input>
            <lb-tool-tips
              >虚拟里程用于 距离计算短、车费计算少
              的情况可在后台增加一部分虚拟里程，减少{{
                $t('action.attendantName')
              }}损失
              <div class="mt-sm">
                用户端显示的距离=实际距离+实际距离*虚拟里程百分比
              </div></lb-tool-tips
            >
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="submitForm" v-preventReClick
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
  data () {
    return {
      loading: false,
      pagePermission: [],
      base_city: [],
      searchForm: {
        page: 1,
        limit: 10,
        name: ''
      },
      tableData: [],
      total: 0,
      subForm: {
        id: 0,
        city_id: '',
        distance_free: '',
        distance_price: '',
        start_distance: '',
        start_price: '',
        invented_distance: ''
      },
      subFormRules: {
        city_id: { required: true, type: 'number', message: '请选择城市', trigger: 'blur' },
        start_distance: { required: true, validator: this.$reg.isFloatNum, trigger: 'blur' },
        start_price: { required: true, validator: this.$reg.isMoney, trigger: 'blur' },
        distance_price: { required: true, validator: this.$reg.isMoney, trigger: 'blur' },
        invented_distance: { required: true, validator: this.$reg.isPercent, trigger: 'blur' }
      },
      showDialog: false
    }
  },
  async created () {
    this.pagePermission = this.$route.meta.pagePermission.filter(item => {
      return item.title === this.$route.name
    })[0].auth
    await this.getCityList()
    this.getTableDataList()
  },
  methods: {
    async getCityList () {
      this.subForm.city_id = []
      let { code, data } = await this.$api.system.citySelect()
      if (code !== 200) return
      this.base_city = data
    },
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handleCurrentChange(1)
    },
    handleCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    async toChange (index) {
      this.searchForm.status = index
      this.getTableDataList(1)
    },
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let { code, data } = await this.$api.system.getCarConfigList(this.searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    async toShowDialog (item = {}) {
      let data = JSON.parse(JSON.stringify(item))
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      this.showDialog = true
    },
    /**
     * @method: 上下架
     */
    async updateItem (id, status) {
      let param = status === -1 ? { id } : { id, status }
      let methodModel = status === -1 ? 'getCarConfigDel' : 'getCarConfigUpdate'
      this.$api.system[methodModel](param).then(res => {
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
    confirmDel (id) {
      this.$confirm(`删除之后，该城市的车费默认使用全局车费设置，确认删除吗？`, this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, -1)
      })
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          let methodModel = subForm.id ? 'getCarConfigUpdate' : 'getCarConfigAdd'
          this.$api.system[methodModel](subForm).then((res) => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showDialog = false
              this.getTableDataList()
            }
          })
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
.lb-system-transaction {
  width: 100%;
  .page-main {
    padding: 20px;
    .el-form {
      width: 100%;
      .el-form-item {
        margin-bottom: 24px;
        .el-select,
        .el-input-number,
        .el-cascader,
        .el-input {
          width: 300px;
        }
      }
      .last-form-item {
        margin-top: 30px;
      }
      .item-tips {
        margin-left: 120px;
        margin-bottom: 24px;
        color: #999999;
      }
    }
  }
}
</style>
