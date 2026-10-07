<!--
 * @Descripttion: 账号设置
 * @Author: xiao li
 * @Date: 2021-03-11 15:42:01
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-28 10:26:59
-->

<template>
  <div class="lb-system-news">
    <top-nav />
    <div class="page-main">
      <lb-button
        type="primary"
        icon="el-icon-plus"
        @click="toShowDialog"
        v-hasPermi="`${$route.name}-add`"
        >{{ $t('menu.AccountAdd') }}</lb-button
      >
      <div class="space-lg"></div>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="账号" prop="title">
            <el-input
              v-model="searchForm.title"
              placeholder="请输入账号"
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
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="username" label="账号"></el-table-column>
        <el-table-column prop="passwd_text" label="密码"></el-table-column>
        <el-table-column prop="role" label="角色名称">
          <template slot-scope="scope">
            <el-tag
              type="primary"
              class="mt-sm mb-sm mr-sm"
              v-for="(item, index) in scope.row.role"
              :key="index"
              >{{ item.title }}</el-tag
            >
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column min-width="120" label="操作" fixed="right">
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
                v-show="scope.row.is_admin === 0"
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
        :title="$t(subForm.id ? 'menu.AccountEdit' : 'menu.AccountAdd')"
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
          <el-form-item label="账号" prop="username">
            <el-input
              :disabled="subForm.is_admin === 1"
              v-model="subForm.username"
              placeholder="请输入账号"
            ></el-input>
          </el-form-item>
          <el-form-item label="密码" prop="passwd">
            <el-input
              v-model="subForm.passwd"
              placeholder="请输入密码"
            ></el-input>
          </el-form-item>
          <el-form-item
            label="所属角色"
            prop="role"
            v-if="subForm.is_admin === 2"
          >
            <el-select
              v-model="subForm.role"
              multiple
              collapse-tags
              filterable
              clearable
              placeholder="请选择"
            >
              <el-option
                v-for="item in roleList"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              ></el-option>
            </el-select>
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
import { mapMutations } from 'vuex'
export default {
  data () {
    let validatePassword = (rule, value, callback) => {
      if (!value) {
        callback(new Error(`请输入${rule.text}`))
      } else if (!/^(\S){6,20}$/.test(value)) {
        callback(new Error('请输入6-20位非空白符的字符!'))
      } else {
        callback()
      }
    }
    let validateRole = (rule, value, callback) => {
      if (this.subForm.is_admin === 1) {
        callback()
      } else if (value.length === 0) {
        callback(new Error(`请选择所属角色`))
      } else {
        callback()
      }
    }
    return {
      username: window.sessionStorage.getItem('ms_username'),
      roleList: [],
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        title: '',
        type: 0
      },
      tableData: [],
      total: 0,
      showDialog: false,
      subForm: {
        id: 0,
        username: '',
        passwd: '',
        is_admin: 2,
        role: []
      },
      subFormRules: {
        username: { required: true, validator: this.$reg.isNotNull, text: '账号', reg_type: 2, trigger: 'blur' },
        passwd: { required: true, validator: validatePassword, text: '密码', trigger: 'blur' },
        role: { required: true, validator: validateRole, trigger: 'change' }
      }
    }
  },
  async activated () {
    await this.getBaseInfo()
    this.getTableDataList(1)
  },
  methods: {
    ...mapMutations(['changeRoutesItem']),
    async getBaseInfo () {
      let { code, data } = await this.$api.account.roleSelect()
      if (code !== 200) return
      this.roleList = data
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
      let { code, data } = await this.$api.account.adminList(this.searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    confirmDel (id) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, -1)
      }).catch(() => {

      })
    },
    async updateItem (id, status) {
      this.$api.account.adminUpdate({ id, status }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
            this.getTableDataList()
          }
        } else {
          if (status === -1) return
          this.getTableDataList()
        }
      })
    },
    async toShowDialog (item = { is_admin: 0, role: [] }) {
      let { id = 0 } = item
      if (id) {
        let { data } = await this.$api.account.adminInfo({ id })
        let { role } = data
        let arr = role.map(item => {
          return item.id
        })
        data.passwd = data.passwd_text
        data.role_info = role
        data.role = arr
        item = data
      } else {
        item.is_admin = 2
      }
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
        let methodModel = this.subForm.id ? 'adminUpdate' : 'adminAdd'
        let param = JSON.parse(JSON.stringify(this.subForm))
        delete param.is_admin
        if (!param.passwd) {
          delete param.passwd
        }
        let { code } = await this.$api.account[methodModel](param)
        if (code !== 200) return
        this.$message.success(this.$t(param.id ? 'tips.successRev' : 'tips.successSub'))
        this.showDialog = false
        if (this.username === param.username) {
          this.changeRoutesItem({ key: 'isAuth', val: false })
          sessionStorage.removeItem('minitk') // 删除token
          sessionStorage.removeItem('ms_username')
          this.$router.push('/login')
          window.location.reload()
          return
        }
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
.dialog-form {
  .el-input,
  .el-select {
    width: 300px;
  }
}
</style>
