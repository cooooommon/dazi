<!--
 * @Descripttion: 代理商设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-25 16:32:49
-->
<template>
  <div class="lb-system-wechat">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="160px"
        class="config-form"
      >
        <el-form-item
          :label="`绑定${$t('action.attendantName')}背景图`"
          prop="bind_technician_img"
        >
          <lb-cover
            :fileList="subForm.bind_technician_img"
            @selectedFiles="getCover($event, 'bind_technician_img')"
          ></lb-cover>
          <lb-tool-tips
            >图片建议尺寸：710 * 1138
            <div class="mt-sm">
              代理商邀请{{ $t('action.attendantName') }}海报，通过该海报可以将{{
                $t('action.attendantName')
              }}绑定在自己的账号下获得佣金
            </div>
            <div class="mt-sm">
              由于页面生成的二维码位置是固定的，所以设计海报时注意将中间的二维码位置留出来
            </div>
            <div class="mt-sm">
              海报背景图中不要出现诱导用户分享以及传播外链内容的
            </div>
            <div class="mt-sm">
              包括但不限于：以金钱奖励、实物奖励、虚拟奖品（包括但不限于红包、优惠券、代金券、积分、话费、流量）；声称分享可获得返佣等
            </div>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button type="danger" plain @click="toReset">{{
            $t('action.defaultSet')
          }}</lb-button>
        </el-form-item>
        <!-- <el-form-item label="代理商入驻" prop="agent_article_id">
          <el-tag
            @click="toShowDialog"
            @close="toClose"
            :closable="subForm.agent_article_id ? true : false"
            :type="subForm.agent_article_id ? 'primary' : 'danger'"
            class="cursor-pointer"
            >{{
              subForm.agent_article_id
                ? subForm.agent_article_title
                : `请选择文章`
            }}</el-tag
          >
          <lb-tool-tips>
            关联文章之后，个人中心将出现代理商入驻入口</lb-tool-tips
          >
        </el-form-item> -->
        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
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
    return {
      subForm: {
        bind_technician_img: [],
        agent_article_id: '',
        agent_article_title: ''
      },
      subFormRules: {
        bind_technician_img: { required: true, type: 'array', message: `请选择绑定${this.$t('action.attendantName')}背景图`, trigger: 'blur' }
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
      total: 0
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      data.bind_technician_img = data.bind_technician_img && data.bind_technician_img.length > 0 ? [{ url: data.bind_technician_img }] : []
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    getCover (img, key) {
      this.subForm[key] = img
    },
    toReset () {
      this.subForm.bind_technician_img = [{ url: 'https://lbqnyv2.migugu.com/bianzu18.png' }]
      this.submitForm()
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
    toClose () {
      this.subForm.agent_article_id = ''
      this.subForm.agent_article_title = ''
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
      this.subForm.agent_article_id = id
      this.subForm.agent_article_title = title
      this.showDialog = false
    },
    async submitForm () {
      let flag = false
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          flag = true
        }
      })
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      subForm.bind_technician_img = subForm.bind_technician_img[0].url
      delete subForm.agent_article_title
      if (flag) {
        await this.$api.system.configUpdate(subForm)
        this.$message.success(this.$t('tips.successSub'))
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
.lb-system-wechat {
  width: 100%;
  .config-form {
    .el-input {
      width: 300px;
    }
  }
}
</style>
