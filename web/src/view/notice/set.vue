<template>
  <div class="lb-system-other">
    <top-nav />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
      >
        <!-- <el-form-item label="通知人员手机号" prop="help_phone">
          <el-input
            v-model="subForm.help_phone"
            placeholder="请输入通知人员手机号"
          ></el-input>
          <lb-tool-tips>
            <div class="mb-sm">向导求救可直接通知平台指定人员，帮忙报警</div>
            启用短信模板后会给该手机号发送求救短信；请前往【系统设置】-【短信通知】配置短信模板</lb-tool-tips
          >
        </el-form-item> -->
        <!-- <el-form-item label="公众号通知人员" prop="help_user_id">
          <el-tag type="primary" class="cursor-pointer" @click="toShowDialog"
            >选择关联用户
          </el-tag>
          <el-table
            :data="subForm.help_user_id"
            :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
            class="mt-lg"
            style="width: 100%"
          >
            <el-table-column prop="id" label="用户ID"></el-table-column>
            <el-table-column prop="avatarUrl" label="头像">
              <template slot-scope="scope">
                <lb-image :src="scope.row.avatarUrl" />
              </template>
            </el-table-column>
            <el-table-column prop="nickName" label="昵称"></el-table-column>
            <el-table-column prop="phone" label="手机号"></el-table-column>
            <el-table-column label="操作">
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
        </el-form-item> -->
        <el-form-item label="报警通知提示音" prop="help_voice">
          <div class="upload-file-warp">
            <input
              type="text"
              class="choice-file-input"
              v-model="subForm.help_voice"
              placeholder="请选择报警通知提示音"
            />
            <lb-cover
              type="button"
              fileType="audio"
              :fileSize="1"
              @selectedFiles="getVoice"
            ></lb-cover>
          </div>
          <lb-tool-tips>
            自主上传报警提示音，{{
              $t('action.attendantName')
            }}寻求报警时，带有外放喇叭的电脑会自动发出声音
            <div class="mt-md">
              {{
                $t('action.attendantName')
              }}点击求救后并不会马上播放，每过10秒请求一次接口数据，当获取到有未读求救信息时才会播放
            </div>
            <div class="mt-sm">
              手动刷新页面后，需要手动点击页面任一可点击操作的位置（例如：点击菜单栏）后，当再次通过接口获取到未读数量时才会自动播放
            </div>
          </lb-tool-tips>
        </el-form-item>

        <el-form-item>
          <lb-button @click="submitForm" type="primary" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
        </el-form-item>
      </el-form>
    </div>
    <el-dialog title="关联用户" :visible.sync="showDialog" width="800px" center>
      <el-form
        :inline="true"
        :model="searchForm"
        ref="searchForm"
        label-width="70px"
      >
        <el-form-item label="输入查询" prop="nickName">
          <el-input
            v-model="searchForm.nickName"
            placeholder="请输入用户昵称"
            style="width: 200px"
          ></el-input>
        </el-form-item>
        <el-form-item label="" prop="phone">
          <el-input
            v-model="searchForm.phone"
            placeholder="请输入手机号"
            style="width: 200px"
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
        <el-table-column prop="id" label="用户ID"></el-table-column>
        <el-table-column prop="avatarUrl" label="头像">
          <template slot-scope="scope">
            <lb-image :src="scope.row.avatarUrl" />
          </template>
        </el-table-column>
        <el-table-column prop="nickName" label="昵称"></el-table-column>
        <el-table-column prop="phone" label="手机号"></el-table-column>
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
export default {
  data () {
    return {
      subForm: {
        help_phone: '',
        help_user_id: [],
        help_voice: ''
      },
      subFormRules: {
        help_phone: { required: true, validator: this.$reg.isTel, text: '通知人员手机号', trigger: 'blur' },
        help_user_id: { required: true, type: 'array', message: '请选择公众号通知人员', trigger: 'blur' },
        help_voice: { required: true, type: 'string', message: '请选择报警通知提示音', trigger: 'blur' }
      },
      searchForm: {
        page: 1,
        limit: 10,
        nickName: '',
        phone: ''
      },
      total: 0,
      loading: false,
      tableData: [],
      multipleSelection: [],
      showDialog: false
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.notice.helpConfigInfo()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    async toShowDialog () {
      this.searchForm.nickName = ''
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
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { code, data } = await this.$api.custom.userList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    },
    handleDialogConfirm () {
      let user = JSON.parse(JSON.stringify(this.subForm.help_user_id))
      let arr1 = user.length > 0 ? user.map(item => { return item.id }) : []
      this.multipleSelection.map(item => {
        if (arr1.includes(item.id)) return
        user.push(item)
      })
      this.subForm.help_user_id = user
      this.showDialog = false
    },
    confirmDel (id) {
      let index = this.subForm.help_user_id.findIndex(item => {
        return item.id === id
      })
      this.subForm.help_user_id.splice(index, 1)
    },
    /**
     * @method 获取语音文件
     */
    getVoice (file) {
      let len = file.length - 1
      this.subForm.help_voice = file[len].url
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          subForm.help_user_id = subForm.help_user_id.map(item => {
            return item.id
          })
          this.$api.notice.helpConfigUpate(subForm).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
            }
          })
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-other {
  width: 100%;

  .el-input,
  .el-select {
    width: 300px;
  }
}
</style>
