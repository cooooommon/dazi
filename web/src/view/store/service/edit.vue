<!--
 * @Description: 编辑服务
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-12-25 17:21:50
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-store-service-edit">
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
        <el-form-item label="封面图" prop="cover">
          <lb-cover
            :fileList="subForm.cover"
            @selectedFiles="getCover"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸: 400 * 400</lb-tool-tips>
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
        <el-form-item label="禁忌说明" prop="explain">
          <lb-ueditor v-model="subForm.explain" :destroy="true"></lb-ueditor>
        </el-form-item>
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
          >
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
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      navTitle: '',
      base_store: [],
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
        introduce: '',
        explain: '',
        notice: '',
        status: 1,
        coach: [],
        top: 0
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
        introduce: { required: true, type: 'string', message: '请输入项目介绍', trigger: 'blur' },
        explain: { required: true, type: 'string', message: '请输入禁忌说明', trigger: 'blur' },
        notice: { required: true, type: 'string', message: '请输入下单须知', trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' },
        coach: { required: true, type: 'array', message: '请选择关联' + this.$t('action.attendantName'), trigger: 'blur' }
      },
      searchForm: {
        page: 1,
        limit: 10,
        status: 2,
        is_user: 1,
        is_store: 1,
        name: ''
      },
      total: 0,
      loading: false,
      tableData: [],
      multipleSelection: [],
      showDialog: false
    }
  },
  created () {
    let { id } = this.$route.query
    if (id) {
      this.subForm.id = id
      this.getDetail(id)
    }
    this.navTitle = this.$t(id ? 'menu.ServiceEdit' : 'menu.ServiceAdd')
  },
  methods: {
    getCover (img) {
      this.subForm.cover = img
    },
    getBannerList (imgs) {
      this.subForm.imgs.push(...imgs)
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
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    changeStore (e) {
      let coach = this.subForm.coach.filter(item => {
        return e.includes(item.store_id)
      })
      this.subForm.coach = coach
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
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      searchForm.store = this.subForm.store
      let { code, data } = await this.$api.technician.coachList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    },
    handleDialogConfirm () {
      let coach = JSON.parse(JSON.stringify(this.subForm.coach))
      let arr1 = coach.length > 0 ? coach.map(item => { return item.id }) : []
      this.multipleSelection.map(item => {
        if (arr1.includes(item.id)) return
        coach.push(item)
      })
      this.subForm.coach = coach
      this.showDialog = false
    },
    confirmDel (id) {
      let index = this.subForm.coach.findIndex(item => {
        return item.id === id
      })
      this.subForm.coach.splice(index, 1)
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
        subForm.coach = arr
        let modelMethod = subForm.id ? 'serviceUpdate' : 'serviceAdd'
        this.$api.service[modelMethod](subForm).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
            this.$router.back(-1)
          }
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-store-service-edit {
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
