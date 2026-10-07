<!--
 * @Description: 编辑服务
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-12-18 16:15:25
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
        <el-form-item label="关联用户" prop="user_id">
          <el-tag
            class="cursor-pointer"
            :type="have_user_id ? 'info' : 'primary'"
            @click="toShowDialog('user')"
            >{{ subForm.user_id ? subForm.nickName : '选择关联用户' }}</el-tag
          >
        </el-form-item>
        <el-form-item label="经纪人姓名" prop="name">
          <el-input
            v-model="subForm.name"
            maxlength="10"
            show-word-limit
            placeholder="请输入经纪人姓名"
          ></el-input>
        </el-form-item>
        <el-form-item label="手机号" prop="mobile">
          <el-input
            v-model="subForm.mobile"
            show-word-limit
            placeholder="请输入手机号"
          ></el-input>
        </el-form-item>
        <el-form-item label="备注" prop="text">
          <el-input
            type="textarea"
            :rows="12"
            maxlength="300"
            resize="none"
            show-word-limit
            placeholder="请输入备注信息"
            v-model="subForm.text"
          ></el-input>
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
    <el-dialog title="关联用户" :visible.sync="showDialog" width="800px" center>
      <el-form
        :inline="true"
        :model="searchForm"
        ref="userForm"
        label-width="70px"
      >
        <el-form-item label="输入查询" prop="name">
          <el-input
            v-model="searchForm.name"
            placeholder="请输入用户昵称/手机号"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <lb-button
            size="medium"
            type="primary"
            icon="el-icon-search"
            style="margin-right: 5px"
            @click="getTableDataList(1, 'user')"
            >{{ $t('action.search') }}</lb-button
          >
          <lb-button
            size="medium"
            icon="el-icon-refresh-left"
            style="margin-right: 5px"
            @click="resetForm('user')"
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
        @current-change="handleSelectionChange($event, 'user')"
      >
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
        @handleSizeChange="handleSizeChange($event, 'user')"
        @handleCurrentChange="handleCurrentChange($event, 'user')"
      >
      </lb-page>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog = false">取 消</el-button>
        <el-button type="primary" @click="handleDialogConfirm('user')"
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
      id: '',
      navTitle: '',
      showMap: false,
      have_user_id: false,
      subForm: {
        id: 0,
        user_id: '',
        name: '',
        mobile: '',
        nickName: '',
        text: ''
      },
      subFormRules: {
        user_id: { required: true, type: 'number', message: '请关联用户', trigger: 'blur' },
        name: { required: true, type: 'string', message: '请输入经纪人姓名', trigger: 'blur' },
        mobile: { required: true, validator: this.$reg.isTel, text: '手机号', reg_type: 2, trigger: 'blur' }
      },
      searchForm: {
        name: '',
        page: 1,
        limit: 10
      },
      tableData: [],
      loading: false,
      total: 0,
      currentRow: {},
      showDialog: false
    }
  },
  async created () {
    let { id } = this.$route.query
    if (id) {
      this.subForm.id = id
      this.have_user_id = true
      await this.getDetail(id)
    }
    this.navTitle = this.$t(id ? 'menu.EconomyEdit' : 'menu.EconomyAdd')
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    handleClose (index) {
      this.dynamicTags.splice(index, 1)
    },

    showInput () {
      this.inputVisible = true
      this.$nextTick(_ => {
        this.$refs.saveTagInput.$refs.input.focus();
      })
    },
    /**
     * @name: 详情
     * @param {*} id
     */
    async getDetail (id) {
      let { code, data } = await this.$api.economy.getInfo({ id })
      if (code !== 200) return

      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    async toShowDialog (key) {
      if (this.have_user_id) {
        return
      }
      this.searchForm.name = ''
      await this.getTableDataList(1, key)
      this.showDialog = !this.showDialog
    },
    resetForm (form) {
      let name = `${form}Form`
      this.$refs[name].resetFields()
      this.getTableDataList(1, form)
    },
    handleSizeChange (val, key) {
      this.searchForm.limit = val
      this.handleCurrentChange(1, key)
    },
    handleCurrentChange (val, key) {
      this.searchForm.page = val
      this.getTableDataList('', key)
    },
    /**
     * @method 列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))

      let methodArr = {
        user: { methodKey: 'economy', methodModel: 'userList' },
        service: { methodKey: 'service', methodModel: 'serviceList' }
      }
      let { methodKey, methodModel } = methodArr[key]
      let { code, data } = await this.$api[methodKey][methodModel](searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    handleSelectionChange (val, key) {
      val = JSON.parse(JSON.stringify(val))
      let { id, nickName } = val
      val.nickName = nickName || `用户ID ${id}`
      this.currentRow = val
    },
    handleDialogConfirm (key) {
      if (this.currentRow === null || !this.currentRow.id) {
        this.$message.error(`请选择用户`)
        return
      }
      let { id = 0, nickName = '' } = this.currentRow
      this.subForm.user_id = id
      this.subForm.nickName = nickName
      this.showDialog = false
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
      console.log(flag)
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))

        let edit = 'update'
        let add = 'add'
        let methodModel = subForm.id ? edit : add
        this.$api.economy[methodModel](subForm).then((res) => {
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
.lb-system-banner-edit {
  width: 100%;
  .el-form {
    width: 100%;
    .el-select,
    .el-input-number,
    .el-cascader,
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
