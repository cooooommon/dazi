<!--
 * @Description: 添加秒杀活动
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-10-18 19:05:00
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-system-banner-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="130px"
      >
        <el-form-item label="关联门店" prop="store_id">
          <el-tag
            class="cursor-pointer"
            :type="subForm.id ? 'info' : 'primary'"
            @click="toShowDialog('store')"
            >{{ subForm.store_id ? subForm.store_name : '选择门店' }}</el-tag
          >
        </el-form-item>
        <el-form-item label="关联套餐" prop="package_id">
          <el-tag
            class="cursor-pointer"
            :type="subForm.id ? 'info' : 'primary'"
            @click="toShowDialog('package')"
            >{{
              subForm.package_id ? subForm.package_name : '选择套餐'
            }}</el-tag
          >
          <div class="pt-md">
            <el-table
              :data="packageTable"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              tooltip-effect="dark"
              style="width: 800px"
            >
              <el-table-column prop="id" label="ID"></el-table-column>
              <el-table-column prop="cover" label="封面图">
                <template slot-scope="scope">
                  <lb-image :src="scope.row.cover" />
                </template>
              </el-table-column>
              <el-table-column prop="name" label="套餐名称"></el-table-column>
              <el-table-column prop="price" label="现价"></el-table-column>
              <el-table-column
                prop="true_sale"
                label="真实销量"
              ></el-table-column>
            </el-table>
          </div>
        </el-form-item>
        <el-form-item label="秒杀库存" prop="stock">
          <el-input-number
            class="lb-input-number"
            :min="stockMin"
            :precision="0"
            :controls="false"
            v-model="subForm.stock"
            placeholder="请输入秒杀库存"
          ></el-input-number>
        </el-form-item>
        <el-form-item label="秒杀价格" prop="price">
          <el-input-number
            class="lb-input-number"
            :min="0"
            :precision="1"
            :controls="false"
            v-model="subForm.price"
            placeholder="请输入秒杀价格"
          ></el-input-number>
        </el-form-item>
        <el-form-item label="限购数量" prop="limit">
          <el-input placeholder="请输入限购数量" v-model="subForm.limit">
            <template slot="append">个/人</template>
          </el-input>
        </el-form-item>
        <el-form-item label="活动时间" prop="start_time">
          <el-date-picker
            v-model="subForm.start_time"
            type="datetimerange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            value-format="timestamp"
            :picker-options="pickerOptions"
            :default-time="['00:00:00', '23:59:59']"
            @change="getTermTime"
          ></el-date-picker>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>
    </div>
    <el-dialog
      title="选择门店"
      :visible.sync="showDialog.store"
      width="800px"
      center
    >
      <el-form
        :inline="true"
        :model="searchForm.store"
        ref="storeForm"
        label-width="70px"
      >
        <el-form-item label="输入查询" prop="name">
          <el-input
            v-model="searchForm.store.name"
            placeholder="输入店铺名称搜索"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <lb-button
            size="medium"
            type="primary"
            icon="el-icon-search"
            style="margin-right: 5px"
            @click="getTableDataList(1, 'store')"
            >{{ $t('action.search') }}</lb-button
          >
          <lb-button
            size="medium"
            icon="el-icon-refresh-left"
            style="margin-right: 5px"
            @click="resetForm('store')"
            >{{ $t('action.reset') }}</lb-button
          >
        </el-form-item>
      </el-form>
      <el-table
        :data="tableData.store"
        ref="singleTable"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
        highlight-current-row
        @current-change="handleSelectionChange($event, 'store')"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="name" label="店铺名称"></el-table-column>
        <el-table-column prop="type_name" label="所属分类" min-width="120">
          <template slot-scope="scope">
            <div>
              <el-tag
                class="mr-sm mt-sm mb-sm ml-sm"
                v-for="(item, index) in scope.row.type_name"
                :key="index"
                >{{ item }}</el-tag
              >
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="cover" label="店铺封面">
          <template slot-scope="scope">
            <lb-image :src="scope.row.cover" />
          </template>
        </el-table-column>
        <el-table-column prop="mobile" label="商家电话"></el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.store.page"
        :pageSize="searchForm.store.limit"
        :total="total.store"
        @handleSizeChange="handleSizeChange($event, 'store')"
        @handleCurrentChange="handleCurrentChange($event, 'store')"
      >
      </lb-page>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog.store = false">取 消</el-button>
        <el-button type="primary" @click="handleDialogConfirm('store')"
          >确 定</el-button
        >
      </span>
    </el-dialog>
    <el-dialog
      title="选择套餐"
      :visible.sync="showDialog.package"
      width="800px"
      center
    >
      <el-form
        :inline="true"
        :model="searchForm.package"
        ref="packageForm"
        label-width="70px"
      >
        <el-form-item label="输入查询" prop="name">
          <el-input
            v-model="searchForm.package.name"
            placeholder="输入套餐名称查询"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <lb-button
            size="medium"
            type="primary"
            icon="el-icon-search"
            style="margin-right: 5px"
            @click="getTableDataList(1, 'package')"
            >{{ $t('action.search') }}</lb-button
          >
          <lb-button
            size="medium"
            icon="el-icon-refresh-left"
            style="margin-right: 5px"
            @click="resetForm('package')"
            >{{ $t('action.reset') }}</lb-button
          >
        </el-form-item>
      </el-form>
      <el-table
        :data="tableData.package"
        ref="singleTable"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
        highlight-current-row
        @current-change="handleSelectionChange($event, 'package')"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="name" label="套餐名称"></el-table-column>
        <el-table-column prop="cover" label="封面图">
          <template slot-scope="scope">
            <lb-image :src="scope.row.cover" />
          </template>
        </el-table-column>
        <el-table-column prop="price" label="现价"></el-table-column>
        <el-table-column prop="true_sale" label="真实销量"></el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.package.page"
        :pageSize="searchForm.package.limit"
        :total="total.package"
        @handleSizeChange="handleSizeChange($event, 'package')"
        @handleCurrentChange="handleCurrentChange($event, 'package')"
      >
      </lb-page>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog.package = false">取 消</el-button>
        <el-button type="primary" @click="handleDialogConfirm('package')"
          >确 定</el-button
        >
      </span>
    </el-dialog>
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
          return (
            time.getTime() <
            moment(moment(Date.now()).format('YYYY-MM-DD')).unix() * 1000
          )
        }
      },
      id: '',
      navTitle: '',
      have_user_id: false,
      subForm: {
        id: 0,
        store_id: '',
        store_name: '',
        package_id: '',
        package_name: '',
        price: '',
        stock: '',
        start_time: '',
        end_time: '',
        limit: ''
      },
      subFormRules: {
        store_id: { required: true, type: 'number', message: '请选择门店', trigger: 'blur' },
        package_id: { required: true, type: 'number', message: '请选择套餐', trigger: 'blur' },
        price: { required: true, validator: this.$reg.isMoney, text: '秒杀价格', trigger: 'blur', reg_type: 1 },
        stock: { required: true, type: 'number', message: '请输入库存', trigger: 'blur' },
        limit: { required: true, validator: this.$reg.isNum, text: '限购数量', trigger: 'blur', reg_type: 2 },
        start_time: { required: true, type: 'array', message: '请选择活动时间', trigger: 'blur' },
      },
      searchForm: {
        store: {
          page: 1,
          limit: 10,
          name: '',
          status: 2
        },
        package: {
          page: 1,
          limit: 10,
          name: '',
          status: 1,
          store_id: ''
        }
      },
      total: { store: 0, package: 0 },
      loading: { store: false, package: false },
      tableData: { store: [], package: [] },
      showDialog: { store: false, package: false },
      currentRow: {},
      multipleSelection: [],
      userInfo: {},
      packageTable: [],
      stockMin: 1,
      oldStartTime: '',
      oldEndTime: ''
    }
  },
  async created () {
    this.userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
    let { id, package_id } = this.$route.query
    if (id) {
      this.subForm.id = id
      await this.getDetail(id)
    }
    if (package_id) {
      this.getPackageDetail(package_id)
    }

    console.log(id)
    this.navTitle = this.$t(
      id ? 'menu.StoreshopSeckillEdit' : 'menu.StoreshopSeckillAdd'
    )
  },
  computed: {
    ...mapState({
      routesItem: (state) => state.routes
    })
  },
  methods: {
    async getPackageDetail (id) {
      let { code, data } = await this.$api.storeshop.packageInfo({ id })
      if (code !== 200) return
      this.searchForm.package.store_id = data.store_id
      this.subForm.package_id = data.id
      this.subForm.package_name = data.name
      this.subForm.store_id = data.store_id
      this.subForm.store_name = data.store.name
      this.packageTable = [{ id: data.id, cover: data.cover, name: data.name, price: data.price, true_sale: data.true_sale }]
    },
    /**
     * @name: 详情
     * @param {*} id
     */
    async getDetail (id) {
      let { code, data } = await this.$api.storeshop.getSeckillEdit({ id })
      if (code !== 200) return
      data.package_name = data.name
      this.oldStartTime = JSON.parse(JSON.stringify(data.start_time * 1000))
      this.oldEndTime = JSON.parse(JSON.stringify(data.end_time * 1000))
      data.start_time = [data.start_time * 1000, data.end_time * 1000]
      this.packageTable = [{ id: data.package_id, cover: data.cover, name: data.name, price: data.package_price, true_sale: data.true_sale }]
      this.searchForm.package.store_id = data.store_id
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      this.stockMin = data.stock
    },
    async toShowDialog (key) {
      if (this.subForm.id) return
      if (key === 'store') {
        this.searchForm[key].name = ''
      } else {
        if (!this.subForm.store_id) {
          this.$message.error('请先选择门店')
          return
        }
        this.searchForm[key].name = ''
      }
      await this.getTableDataList(1, key)
      this.showDialog[key] = !this.showDialog[key]
    },
    resetForm (form) {
      let name = `${form}Form`
      this.$refs[name].resetFields()
      this.getTableDataList(1, form)
    },
    handleSizeChange (val, key) {
      this.searchForm[key].limit = val
      this.handleCurrentChange(1, key)
    },
    handleCurrentChange (val, key) {
      this.searchForm[key].page = val
      this.getTableDataList('', key)
    },
    /**
     * @method 列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.loading[key] = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))

      let methodArr = {
        store: { methodKey: 'storeshop', methodModel: 'getList' },
        package: { methodKey: 'storeshop', methodModel: 'packageList' }
      }
      let { methodKey, methodModel } = methodArr[key]
      let { code, data } = await this.$api[methodKey][methodModel](searchForm)
      this.loading[key] = false
      if (code !== 200) return
      this.tableData[key] = data.data
      this.total[key] = data.total
    },
    handleSelectionChange (val, key) {
      if (key === 'store') {
        val = JSON.parse(JSON.stringify(val))
        let { id, nickName } = val
        val.nickName = nickName || `门店ID ${id}`
        this.currentRow = val
        return
      }
      this.multipleSelection = val
    },
    handleDialogConfirm (key) {
      if ((this.currentRow === null || !this.currentRow.id) && key === 'store') {
        this.$message.error(`请选择门店`)
        return
      }
      if ((this.multipleSelection === null || !this.multipleSelection.id) && key === 'package') {
        this.$message.error(`请选择套餐`)
        return
      }
      if (key === 'store') {
        this.packageTable = []
        this.subForm.package_id = ''
        this.subForm.package_name = ''
      } else {
        this.packageTable = [this.multipleSelection]
        this.subForm.package_id = this.multipleSelection.id
        this.subForm.package_name = this.multipleSelection.name
      }
      this.subForm.store_id = this.currentRow.id
      this.subForm.store_name = this.currentRow.name
      this.searchForm.package.store_id = this.currentRow.id
      this.showDialog[key] = false
    },
    /**
     * @name: 新增/编辑
     * @param {*}
     */
    submitForm () {
      console.log(this.subForm)
      let flag = true
      this.$refs['subForm'].validate((valid) => {
        if (!valid) flag = false
      })
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        subForm.start_time = this.subForm.start_time[0] / 1000
        subForm.end_time = this.subForm.start_time[1] / 1000
        delete subForm.store_name
        delete subForm.package_name
        let methodModel = subForm.id ? 'seckillEdit' : 'seckillAdd'
        this.$api.storeshop[methodModel](subForm).then((res) => {
          if (res.code === 200) {
            this.$message.success(
              this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub')
            )
            this.$router.back(-1)
          }
        })
      }
    },
    getTermTime (e) {
      console.log(e, this.oldStartTime, this.oldEndTime, e[0] > this.oldStartTime, e[1] < this.oldEndTime)
      if ((e[0] > this.oldStartTime || e[1] < this.oldEndTime) && this.subForm.id) {
        this.$message.error(`活动时间只能延长`)
        this.subForm.start_time = [this.oldStartTime, this.oldEndTime]
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-banner-edit {
  width: 100%;
  .el-form {
    width: 100%;
    .el-select,
    .el-input-number,
    .el-input {
      width: 300px;
    }
    .el-textarea {
      width: 600px;
    }
    .el-tag {
      cursor: pointer;
    }
    .trade-week {
      width: 500px;
      padding: 0 10px 40px 10px;
    }
    .el-slider {
      white-space: nowrap;
    }
  }
}
</style>
