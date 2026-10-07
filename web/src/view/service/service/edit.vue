<!--
 * @Description: 编辑服务
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-11-14 16:56:36
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-system-banner-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="130px"
      >
        <el-form-item label="服务名称" prop="title">
          <el-input
            v-model="subForm.title"
            maxlength="15"
            show-word-limit
            placeholder="请输入服务名称"
          ></el-input>
        </el-form-item>
        <el-form-item label="副标题" prop="sub_title">
          <el-input
            v-model="subForm.sub_title"
            maxlength="30"
            show-word-limit
            placeholder="请输入副标题"
          ></el-input>
        </el-form-item>
        <el-form-item label="服务分类" prop="service_type">
          <el-select
            v-model="subForm.service_type"
            clearable
            placeholder="请选择"
          >
            <el-option
              v-for="item in typeList"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="封面图" prop="cover">
          <lb-cover
            :fileList="subForm.cover"
            @selectedFiles="getCover"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸: 180 * 204</lb-tool-tips>
        </el-form-item>
        <el-form-item label="轮播图" prop="imgs">
          <lb-cover
            :fileList="subForm.imgs"
            fileType="image"
            type="more"
            tips="750 * 562"
            @selectedFiles="getBannerList"
            :fileSize="9"
          ></lb-cover>
        </el-form-item>
        <el-form-item label="服务价格" prop="price">
          <el-input
            v-model="subForm.price"
            placeholder="请输入服务价格"
          ></el-input>
          <div>元</div>
        </el-form-item>
        <!-- <el-form-item label="服务原价" prop="init_price">
          <el-input
            v-model="subForm.init_price"
            placeholder="请输入服务原价"
          ></el-input>
          <div>元</div>
        </el-form-item> -->
        <el-form-item label="虚拟销售量" prop="sale">
          <el-input
            v-model.number="subForm.sale"
            placeholder="请输入虚拟销售量"
          ></el-input>
          <div>人选择</div>
          <lb-tool-tips>该虚拟销售量=虚拟+实际销售量</lb-tool-tips>
        </el-form-item>
        <el-form-item label="服务时长" prop="time_long">
          <el-input
            v-model.number="subForm.time_long"
            placeholder="请输入服务时长"
          ></el-input>
          <div>分钟</div>
          <lb-tool-tips>一次服务的时间段，一般为60分钟</lb-tool-tips>
        </el-form-item>
        <el-form-item label="分销返佣" prop="com_balance">
          <el-input v-model="subForm.com_balance" placeholder="请输入分销返佣">
            <template slot="append">%</template>
          </el-input>
          <lb-tool-tips>实际支付金额的百分比</lb-tool-tips>
        </el-form-item>
        <el-form-item label="起购数量" prop="min_num">
          <el-input
            v-model.number="subForm.min_num"
            placeholder="请输入起购数量"
          ></el-input>
          <lb-tool-tips
            >用户下单的起购数量。例如：设置服务时长为1小时，<br />设置起购数量为3，则用户下达的起购时长为3小时</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="是否计车费" prop="is_car">
          <el-radio-group v-model="subForm.is_car">
            <el-radio :label="1">计车费</el-radio>
            <el-radio :label="0">不计车费</el-radio>
          </el-radio-group>
          <lb-tool-tips>
            1、不计车费：用户下单无需支付向导的车费。
            <div class="mt-sm">2、计车费：用户下单时，需要支付向导车费。</div>
          </lb-tool-tips>
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
        <el-form-item label="项目介绍" prop="introduce">
          <lb-ueditor v-model="subForm.introduce" :destroy="true"></lb-ueditor>
        </el-form-item>
        <!-- <el-form-item label="禁忌说明" prop="explain">
          <lb-ueditor v-model="subForm.explain" :destroy="true"></lb-ueditor>
        </el-form-item> -->
        <el-form-item label="下单须知" prop="notice">
          <lb-ueditor v-model="subForm.notice" :destroy="true"></lb-ueditor>
        </el-form-item>
        <el-form-item :label="`关联${$t('action.attendantName')}`" prop="coach"
          ><lb-button type="primary" icon="el-icon-plus" @click="toShowDialog"
            >选择{{ $t('action.attendantName') }}</lb-button
          >
          <el-table
            :data="subForm.coach"
            :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
            class="mt-lg"
            style="width: 100%"
            @selection-change="handleSelectionChangeBalance"
          >
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column
              prop="id"
              :label="`${$t('action.attendantName')}ID`"
            ></el-table-column>
            <el-table-column
              prop="work_img"
              :label="`${$t('action.attendantName')}头像`"
            >
              <template slot-scope="scope">
                <lb-image :src="scope.row.work_img" />
              </template>
            </el-table-column>
            <el-table-column
              prop="coach_name"
              :label="`${$t('action.attendantName')}名称`"
            ></el-table-column>
            <el-table-column :label="`${$t('action.attendantName')}价格`">
              <template slot-scope="scope">
                <div class="table-operate flex-y-center">
                  <span>￥</span>
                  <el-input-number
                    class="lb-input-number"
                    style="width: 100px"
                    :min="0"
                    :precision="2"
                    :controls="false"
                    placeholder="请输入"
                    v-model="coachPriceList[scope.$index].price"
                    @blur="setPrice(scope.$index)"
                  ></el-input-number>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="服务提成">
              <template slot-scope="scope">
                <div class="table-operate flex-y-center">
                  <el-input-number
                    class="lb-input-number"
                    style="width: 100px"
                    :min="0"
                    :max="100"
                    :precision="0"
                    :controls="false"
                    placeholder="请输入"
                    v-model="coachPriceList[scope.$index].balance"
                    @blur="setBalance(scope.$index)"
                  ></el-input-number>
                  <span>%</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="操作" fixed="right">
              <template slot-scope="scope">
                <div class="table-operate">
                  <lb-button
                    size="mini"
                    plain
                    type="danger"
                    @click="confirmDel(scope.row.id)"
                    >{{ $t('action.delete') }}</lb-button
                  >
                </div>
              </template>
            </el-table-column>
          </el-table>
        </el-form-item>
        <div style="padding-left: 130px">
          <lb-page
            :batch="true"
            :page="searchForm.page"
            :pageSize="searchForm.limit"
            :total="total"
            @handleSizeChange="handleSizeChange"
            @handleCurrentChange="handleCurrentChange"
            :selected="batchMultipleSelection.length"
            :isPagination="false"
          >
            <lb-button type="primary" @click="batchProportion(1)"
              >批量设置价格</lb-button
            >
            <lb-button type="warning" @click="batchProportion(2)"
              >批量设置提成比例</lb-button
            >
          </lb-page>
        </div>
        <div class="pt-lg"></div>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>

      <el-dialog
        :title="`关联${$t('action.attendantName')}`"
        :visible.sync="showDialog"
        width="800px"
        center
      >
        <el-form
          :inline="true"
          :model="searchForm"
          ref="searchForm"
          label-width="70px"
        >
          <el-form-item label="输入查询" prop="name">
            <el-input
              v-model="searchForm.name"
              :placeholder="`请输入${$t('action.attendantName')}昵称/手机号`"
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
        <el-table
          :data="tableData"
          ref="multipleTable"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column
            prop="id"
            :label="`${$t('action.attendantName')}ID`"
          ></el-table-column>
          <el-table-column
            prop="work_img"
            :label="`${$t('action.attendantName')}头像`"
          >
            <template slot-scope="scope">
              <lb-image :src="scope.row.work_img" />
            </template>
          </el-table-column>
          <el-table-column
            prop="coach_name"
            :label="`${$t('action.attendantName')}昵称`"
          ></el-table-column>
          <el-table-column prop="mobile" label="手机号"></el-table-column>
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
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="handleDialogConfirm"
            >确 定</el-button
          >
        </span>
      </el-dialog>

      <el-dialog
        :title="dialogForm.type == 1 ? '批量设置价格' : '批量设置服务提成比例'"
        :visible.sync="showBatchDialog"
        width="600px"
        center
      >
        <el-form
          :inline="true"
          :model="dialogForm"
          ref="dialogForm"
          :rules="dialogFormRules"
          label-width="170px"
        >
          <el-form-item
            v-if="dialogForm.type == 1"
            :label="`${$t('action.attendantName')}价格`"
            prop="price"
          >
            <el-input
              v-model="dialogForm.price"
              :placeholder="`请输入${$t('action.attendantName')}价格`"
            >
              <template slot="append">元</template>
            </el-input>
          </el-form-item>
          <el-form-item
            v-if="dialogForm.type == 2"
            label="服务提成"
            prop="balance"
          >
            <el-input
              v-model="dialogForm.balance"
              :placeholder="`请输入服务提成`"
            >
              <template slot="append">%</template>
            </el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showBatchDialog = false">取 消</el-button>
          <el-button type="primary" @click="handleDialogBatchConfirm"
            >确 定</el-button
          >
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      id: '',
      navTitle: '',
      articleOptions: [],
      subForm: {
        title: '',
        sub_title: '',
        cover: [],
        imgs: [],
        price: '',
        init_price: '',
        sale: '',
        true_sale: '',
        time_long: '',
        com_balance: '',
        introduce: '',
        explain: '',
        notice: '',
        status: 1,
        coach: [],
        top: 0,
        min_num: '',
        is_car: 1,
        service_type: ''
      },
      subFormRules: {
        title: { required: true, type: 'string', message: '请输入服务名称', trigger: 'blur' },
        sub_title: { required: true, type: 'string', message: '请输入副标题', trigger: 'blur' },
        cover: { required: true, type: 'array', message: '请上传图片', trigger: ['blur', 'change'] },
        imgs: { required: true, type: 'array', message: '请上传图片', trigger: ['blur', 'change'] },
        price: { required: true, validator: this.$reg.isMoney, trigger: 'blur' },
        init_price: { required: true, validator: this.$reg.isMoney, trigger: 'blur' },
        sale: { required: true, validator: this.$reg.isNum, trigger: 'blur' },
        time_long: { required: true, validator: this.$reg.isNum, reg_type: 2, trigger: 'blur' },
        com_balance: { required: true, validator: this.$reg.isPercent, text: '分销返佣', trigger: 'blur' },
        introduce: { required: true, type: 'string', message: '请输入项目介绍', trigger: 'blur' },
        // explain: { required: true, type: 'string', message: '请输入禁忌说明', trigger: 'blur' },
        notice: { required: true, type: 'string', message: '请输入下单须知', trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' },
        // coach: { required: true, type: 'array', message: '请选择关联向导', trigger: 'blur' },
        min_num: { required: true, validator: this.$reg.isNum, reg_type: 2, trigger: 'blur' },
        is_car: { required: true, type: 'number', message: '请选择是否计车费', trigger: 'blur' },
        // service_type: { required: true, type: 'number', message: '请选择服务分类', trigger: 'blur' }
      },
      searchForm: {
        page: 1,
        limit: 10,
        status: 2,
        is_user: 1,
        name: ''
      },
      total: 0,
      loading: false,
      tableData: [],
      multipleSelection: [],
      showDialog: false,
      coachPriceList: [],
      batchMultipleSelection: [],
      dialogForm: {
        price: '',
        balance: '',
        type: 1
      },
      dialogFormRules: {
        price: { required: true, validator: this.$reg.isMoney, trigger: 'blur' },
        balance: { required: true, validator: this.$reg.isPercent, reg_type: 1, trigger: 'blur' }
      },
      showBatchDialog: false,
      typeList: []
    }
  },
  created () {
    let { id } = this.$route.query
    if (id) {
      this.subForm.id = id
      this.getDetail(id)
    }
    this.getTypeListNoPage()
    this.navTitle = this.$t(id ? 'menu.ServiceEdit' : 'menu.ServiceAdd')
  },
  methods: {
    getCover (img) {
      this.subForm.cover = img
    },
    getBannerList (imgs) {
      this.subForm.imgs.push(...imgs)
    },
    // 服务分类
    async getTypeListNoPage () {
      let { code, data } = await this.$api.service.typeListNoPage()
      if (code !== 200) return
      this.typeList = data
    },
    /**
     * @name: 详情
     * @param {*} id
     */
    async getDetail (id) {
      let { code, data } = await this.$api.service.serviceInfo({ id })
      if (code !== 200) return
      data.cover = [{ url: data.cover }]
      data.imgs = data.imgs.map(item => {
        return { url: item }
      })
      data.service_type = data.service_type > 0 ? data.service_type : ''
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      let coachPriceList = []
      data.coach.forEach(item => {
        coachPriceList.push({ coach_id: item.id, price: item.price || this.subForm.price || 0, balance: item.balance || 0 })
      })
      this.coachPriceList = coachPriceList
    },
    async toShowDialog () {
      this.searchForm.name = ''
      await this.getTableDataList()
      this.showDialog = !this.showDialog
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
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let { code, data } = await this.$api.technician.coachList(this.searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    },
    handleSelectionChangeBalance (val) {
      this.batchMultipleSelection = val
    },
    batchProportion (index) {
      if (!this.batchMultipleSelection.length) {
        this.$message.error('请选择数据')
        return
      }
      this.dialogForm.price = ''
      this.dialogForm.balance = ''
      this.showBatchDialog = true
      this.dialogForm.type = index
    },
    handleDialogBatchConfirm () {
      let flag = true
      this.$refs['dialogForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let {
          type = 1,
          price = 0,
          balance = 0
        } = this.dialogForm
        console.log(this.coachPriceList, this.batchMultipleSelection)
        this.coachPriceList.forEach(item => {
          this.batchMultipleSelection.forEach(item2 => {
            if (item.coach_id === item2.id) {
              if (type === 1) {
                item.price = price
              } else {
                item.balance = balance
              }
            }
          })
        })
        this.showBatchDialog = false
      }
    },
    handleDialogConfirm () {
      let coach = JSON.parse(JSON.stringify(this.subForm.coach))
      let arr1 = coach.length > 0 ? coach.map(item => { return item.id }) : []
      this.multipleSelection.map(item => {
        if (arr1.includes(item.id)) return
        coach.push(item)
      })
      let coachPriceList = []
      coach.forEach(item => {
        coachPriceList.push({ coach_id: item.id, price: item.price || this.subForm.price || 0, balance: item.balance || 0 })
      })
      this.coachPriceList = coachPriceList
      this.subForm.coach = coach
      this.showDialog = false
    },
    confirmDel (id) {
      let index = this.subForm.coach.findIndex(item => {
        return item.id === id
      })
      this.subForm.coach.splice(index, 1)
      let coachPriceList = []
      this.subForm.coach.forEach(item => {
        coachPriceList.push({ coach_id: item.id, price: item.price || this.subForm.price || 0, balance: item.balance || 0 })
      })
      this.coachPriceList = coachPriceList
    },
    /**
     * @name: 新增/编辑
     * @param {*}
     */
    submitForm () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        subForm.cover = subForm.cover[0].url
        subForm.imgs = subForm.imgs.map(item => {
          return item.url
        })
        let arr = subForm.coach.map(item => {
          return item.id
        })
        console.log(this.coachPriceList)
        subForm.coach = this.coachPriceList // arr
        let modelMethod = subForm.id ? 'serviceUpdate' : 'serviceAdd'
        this.$api.service[modelMethod](subForm).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
            this.$router.back(-1)
          }
        })
      }
    },
    setPrice (index) {
      if (!this.coachPriceList[index].price) {
        this.coachPriceList[index].price = 0
      }
    },
    setBalance (index) {
      if (!this.coachPriceList[index].balance) {
        this.coachPriceList[index].balance = 0
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
  }
}
</style>
