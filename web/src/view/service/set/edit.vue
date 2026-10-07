<!--
 * @Descripttion: 编辑轮播图
 * @Author: xiao li
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: wen kun
 * @LastEditTime: 2024-06-25 18:44:16
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
        <el-form-item label="图片" prop="img">
          <lb-cover
            :fileList="subForm.img"
            @selectedFiles="getCover"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸: 710 * 345</lb-tool-tips>
        </el-form-item>
        <el-form-item label="类型" prop="banner_type">
          <el-radio-group v-model="subForm.banner_type">
            <el-radio :label="1">图片</el-radio>
            <el-radio :label="2">视频</el-radio>
          </el-radio-group>
        </el-form-item>
        <div v-if="subForm.banner_type == 1">
          <el-form-item label="关联内容" prop="connect_type">
            <el-radio-group v-model="subForm.connect_type">
              <el-radio :label="1">查看大图</el-radio>
              <el-radio :label="2">文章</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item
            label=""
            prop="type_id"
            v-if="subForm.connect_type === 2"
          >
            <el-tag
              @click="toShowDialog"
              :type="subForm.type_id ? 'primary' : 'danger'"
              >{{ subForm.type_id ? subForm.type_title : `请选择文章` }}</el-tag
            >
          </el-form-item>
        </div>
        <div v-else>
          <el-form-item label="上传视频" prop="video_url">
            <div class="upload-file-warp">
              <input
                type="text"
                class="choice-file-input"
                v-model="subForm.video_url"
                placeholder="请上传视频"
              />
              <lb-cover
                type="button"
                fileType="video"
                :fileSize="1"
                @selectedFiles="getVoice"
              ></lb-cover>
            </div>
          </el-form-item>
        </div>
        <el-form-item label="关联服务分类" prop="service_type">
          <el-select v-model="subForm.service_type" placeholder="请选择">
            <el-option
              v-for="item in typeList"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
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
      title="选择文章"
      :visible.sync="showDialog"
      width="1000px"
      center
    >
      <el-form
        @submit.native.prevent
        :inline="true"
        :model="searchForm"
        ref="searchForm"
      >
        <el-form-item label="输入查询" prop="title">
          <el-input
            v-model="searchForm.title"
            placeholder="请输入文章标题"
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
        ref="singleTable"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
        highlight-current-row
        @current-change="handleSelectionChange"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="title" label="文章标题"></el-table-column>
        <el-table-column prop="top" label="排序值"></el-table-column>
        <el-table-column prop="create_time" label="创建时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
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
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog = false">取 消</el-button>
        <el-button type="primary" @click="handleDialogConfirm">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  data () {
    let checkType = (rule, value, callback) => {
      let { connect_type: type } = this.subForm
      let { typeText } = this
      if (type !== 1 && !value) {
        let msg = `请选择${typeText[type]}`
        callback(msg)
      } else {
        callback()
      }
    }
    return {
      navTitle: '',
      typeText: {
        1: '查看大图',
        2: '文章'
      },
      articleOptions: [],
      subForm: {
        img: '',
        connect_type: 1,
        type_id: '',
        type_title: '',
        top: 0,
        banner_type: 1,
        video_url: '',
        service_type: ''
      },
      subFormRules: {
        img: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        connect_type: { required: true, type: 'number', message: '请选择关联内容', trigger: 'blur' },
        type_id: { required: true, validator: checkType, trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' },
        video_url: { required: true, type: 'string', message: '请上传视频', trigger: 'blur' },
        banner_type: { required: true, type: 'number', message: '请选择类型', trigger: 'blur' },
        service_type: { required: true, type: 'number', message: '请选择服务分类', trigger: 'blur' }
      },
      showDialog: false,
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        title: '',
        status: 1
      },
      tableData: [],
      total: 0,
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
    this.navTitle = this.$t(id ? 'menu.ServiceBannerEdit' : 'menu.ServiceBannerAdd')
  },
  methods: {
    /**
     * @method 获取图片
     */
    getCover (img) {
      this.subForm.img = img
    },
    async getDetail (id) {
      let { code, data } = await this.$api.service.bannerInfo({ id })
      if (code !== 200) return
      data.img = [{ url: data.img }]
      data.link = data.link * 1 > 0 ? data.link * 1 : ''
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    // 服务分类
    async getTypeListNoPage () {
      let { code, data } = await this.$api.service.typeListNoPage()
      if (code !== 200) return
      this.typeList = data
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
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { code, data } = await this.$api.market.articleList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    async toShowDialog () {
      this.searchForm.title = ''
      await this.getTableDataList(1)
      this.showDialog = !this.showDialog
    },
    handleSelectionChange (val) {
      val = JSON.parse(JSON.stringify(val))
      this.currentRow = val
    },
    handleDialogConfirm () {
      if (this.currentRow === null || !this.currentRow.id) {
        this.$message.error(`请选择文章`)
        return
      }
      let { id = 0, title = '' } = this.currentRow
      this.subForm.type_id = id
      this.subForm.type_title = title
      this.showDialog = false
    },
    /**
     * @method 新增/编辑
     */
    submitForm () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        subForm.img = subForm.img[0].url
        delete subForm.type_title
        let modelMethod = subForm.id ? 'bannerUpdate' : 'bannerAdd'
        this.$api.service[modelMethod](subForm).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
            this.$router.back(-1)
          }
        })
      }
    },
    getVoice (file) {
      let len = file.length - 1
      this.subForm.video_url = file[len].url
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
.lb-system-banner-edit {
  width: 100%;
  .el-form {
    width: 100%;
    .el-form-item {
      margin-bottom: 24px;
      .el-select,
      .el-input-number,
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
</style>
