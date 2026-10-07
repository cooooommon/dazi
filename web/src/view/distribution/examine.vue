<template>
  <div class="lb-goods-list">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <el-button
          @click="toChange(0)"
          :type="searchForm.status === 0 ? 'primary' : ''"
          plain
          size="medium"
          >全部（{{ count.all || 0 }}）</el-button
        >
        <el-button
          @click="toChange(4)"
          :type="searchForm.status === 3 ? 'primary' : ''"
          plain
          size="medium"
          >未授权（{{ count.nopass || 0 }}）</el-button
        >
        <el-button
          @click="toChange(1)"
          :type="searchForm.status === 1 ? 'primary' : ''"
          plain
          size="medium"
          >申请中（{{ count.ing || 0 }}）</el-button
        >
        <el-button
          @click="toChange(2)"
          :type="searchForm.status === 2 ? 'primary' : ''"
          plain
          size="medium"
          >已授权（{{ count.pass || 0 }}）</el-button
        >
      </el-row>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="输入查询" prop="name">
            <el-input
              v-model="searchForm.name"
              placeholder="请输入姓名/手机号"
            ></el-input>
          </el-form-item>
          <el-form-item label="申请时间" prop="start_time">
            <el-date-picker
              @change="getTableDataList(1)"
              v-model="searchForm.start_time"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              value-format="timestamp"
              :picker-options="pickerOptions"
              :default-time="['00:00:00', '23:59:59']"
            ></el-date-picker>
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
        <el-table-column prop="id" label="ID" fixed></el-table-column>
        <el-table-column prop="user_id" label="用户ID"></el-table-column>
        <el-table-column prop="avatarUrl" label="头像">
          <template slot-scope="scope">
            <lb-image :src="scope.row.avatarUrl" />
          </template>
        </el-table-column>
        <el-table-column
          prop="nickName"
          label="昵称"
          :min-width="120"
        ></el-table-column>
        <el-table-column
          prop="user_name"
          label="姓名"
          :min-width="120"
        ></el-table-column>
        <el-table-column
          prop="mobile"
          label="手机号"
          :min-width="120"
        ></el-table-column>
        <el-table-column prop="text" label="备注信息" min-width="200">
          <template slot-scope="scope"
            ><div class="ellipsis-2" v-html="scope.row.text"></div>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="申请时间" :min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            <el-tag :type="statusText[scope.row.status].type">{{
              statusText[scope.row.status].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="
                  $router.push(
                    '/promotion/distribution/detail?id=' + scope.row.id
                  )
                "
                v-show="scope.row.status !== 1 && scope.row.status !== 3"
                v-hasPermi="`${$route.name}-view`"
                >{{ $t('action.view') }}</lb-button
              >

              <lb-button
                size="mini"
                plain
                type="success"
                @click="confirmDel(scope.row.id, -1)"
                v-show="scope.row.status !== 1"
                v-hasPermi="`${$route.name}-delete`"
                >{{ $t('action.delete') }}</lb-button
              >

              <lb-button
                size="mini"
                plain
                type="primary"
                @click="toShowApply(scope.row.id)"
                v-show="scope.row.status === 1 || scope.row.status === 3"
                v-hasPermi="
                  scope.row.status === 1
                    ? `${$route.name}-authDistribution`
                    : `${$route.name}-resetAuth`
                "
                >{{
                  $t(
                    scope.row.status === 1
                      ? 'action.authDistribution'
                      : 'action.resetAuth'
                  )
                }}</lb-button
              >

              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id, 3)"
                v-show="scope.row.status === 2"
                v-hasPermi="`${$route.name}-cancelAuth`"
                >{{ $t('action.cancelAuth') }}</lb-button
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
        title="申请详情"
        :visible.sync="showApply"
        width="800px"
        center
      >
        <div style="height: 60vh; overflow: auto" v-if="showApply">
          <el-form
            @submit.native.prevent
            :model="applyForm"
            label-width="130px"
            ref="applyForm"
            size="mini"
            :rules="applyFormRules"
          >
            <el-form-item label="用户ID：">
              <div>{{ applyForm.user_id }}</div>
            </el-form-item>
            <el-form-item label="昵称：">
              <div>{{ applyForm.nickName }}</div>
            </el-form-item>
            <el-form-item label="姓名：" prop="user_name">
              <div v-if="applyForm.status == 1 && applyForm.status == 3">
                {{ applyForm.user_name }}
              </div>
              <div v-else>
                <el-input
                  v-model="applyForm.user_name"
                  placeholder="请输入姓名"
                  size="medium"
                ></el-input>
              </div>
            </el-form-item>
            <el-form-item label="手机号：" prop="mobile">
              <div v-if="applyForm.status == 1 && applyForm.status == 3">
                {{ applyForm.mobile }}
              </div>
              <div v-else>
                <el-input
                  v-model="applyForm.mobile"
                  placeholder="请输入姓名"
                  size="medium"
                ></el-input>
              </div>
            </el-form-item>
            <el-form-item label="申请时间：">
              <div>
                {{ applyForm.create_time | handleTime() }}
              </div>
            </el-form-item>
            <!-- <el-form-item label="申请状态：">
              <div>{{ applyForm.status == 1 ? '申请' : '通过' }}</div>
            </el-form-item>
            <el-form-item label="处理时间：">
              <div>{{ applyForm.sh_time | handleTime() }}</div>
            </el-form-item> -->
            <el-form-item label="备注信息：">
              <div
                v-html="applyForm.text"
                v-if="applyForm.status == 1 && applyForm.status == 3"
              ></div>
              <div v-else>
                <el-input
                  type="textarea"
                  :rows="10"
                  v-model="applyForm.text"
                  maxlength="300"
                  show-word-limit
                  resize="none"
                  placeholder="请输入备注信息"
                ></el-input>
              </div>
            </el-form-item>
            <el-form-item label="下级数量：">
              <div>{{ applyForm.distribution_count }}</div>
            </el-form-item>
            <el-form-item label="邀请用户数：">
              <div>{{ applyForm.user_count }}</div>
            </el-form-item>
            <el-form-item label="上级：">
              <div>{{ applyForm.p_nickName }}</div>
            </el-form-item>
            <el-form-item label="订单成交额：">
              <div>{{ applyForm.price }}</div>
            </el-form-item>
            <el-form-item label="累计获得佣金：">
              <div>{{ applyForm.cash }}</div>
            </el-form-item>

            <div class="space-lg"></div>
            <div class="space-lg b-1px-t"></div>
            <div class="space-lg"></div>
            <div v-if="applyForm.status !== 1">
              <div class="flex-warp">
                <el-form-item
                  label="审核结果："
                  prop="status"
                  style="width: 50%"
                >
                  <el-tag :type="statusText[applyForm.status].type">{{
                    statusText[applyForm.status].text
                  }}</el-tag>
                </el-form-item>
                <el-form-item
                  label="审核时间："
                  prop=""
                  style="width: 50%"
                  v-show="applyForm.status != 1 && applyForm.sh_time"
                >
                  <div class="c-warning">
                    {{ applyForm.sh_time | handleTime() }}
                  </div>
                </el-form-item>
              </div>
              <el-form-item
                label="审核意见："
                prop="sh_text"
                v-if="applyForm.sh_text && applyForm.status !== 3"
              >
                <div>{{ applyForm.sh_text }}</div>
              </el-form-item>
            </div>
          </el-form>
          <div v-if="applyForm.status !== 1 && applyForm.status !== 3">
            <el-tabs
              v-model="dialogform.type"
              @tab-click="handleClick"
              type="card"
            >
              <el-tab-pane label="下级" name="1"></el-tab-pane>
              <el-tab-pane label="邀请用户数" name="2"></el-tab-pane>
            </el-tabs>
            <el-table
              v-loading="dialogLoading"
              :data="dialogTableData"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              style="width: 100%"
              v-if="dialogform.type == 1"
            >
              <el-table-column prop="avatarUrl" label="头像" key="avatarUrl">
                <template slot-scope="scope">
                  <lb-image :src="scope.row.avatarUrl" />
                </template>
              </el-table-column>
              <el-table-column
                prop="nickName"
                label="下级姓名"
                key="nickName"
              ></el-table-column>
              <el-table-column
                prop="pay_price"
                label="订单总金额"
                key="pay_price"
              >
                <template slot-scope="scope">
                  ￥{{ scope.row.pay_price }}
                </template>
              </el-table-column>
              <el-table-column
                prop="create_time"
                label="绑定时间"
                key="create_time"
              ></el-table-column>
            </el-table>
            <el-table
              v-loading="dialogLoading"
              :data="dialogTableData"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              style="width: 100%"
              v-if="dialogform.type == 2"
            >
              <el-table-column prop="avatarUrl" label="头像" key="avatarUrl">
                <template slot-scope="scope">
                  <lb-image :src="scope.row.avatarUrl" />
                </template>
              </el-table-column>
              <el-table-column
                prop="nickName"
                label="微信昵称"
                key="nickName"
              ></el-table-column>
            </el-table>
            <lb-page
              :batch="false"
              :page="dialogform.page"
              :pageSize="dialogform.limit"
              :total="dialogTotal"
              @handleSizeChange="dialogHandleSizeChange"
              @handleCurrentChange="dialogHandleCurrentChange"
            >
            </lb-page>
          </div>
          <el-form
            @submit.native.prevent
            :model="subForm"
            ref="subForm"
            :rules="subFormRules"
            label-width="130px"
            size="mini"
            v-if="applyForm.status === 1 || applyForm.status == 3"
          >
            <el-form-item label="审核结果：" prop="status" style="width: 50%">
              <el-radio-group v-model="subForm.status">
                <el-radio :label="2">通过</el-radio>
                <el-radio :label="4" v-if="applyForm.status === 1"
                  >驳回</el-radio
                >
              </el-radio-group>
            </el-form-item>
            <el-form-item label="审核意见：">
              <el-input
                type="textarea"
                :rows="10"
                v-model="subForm.sh_text"
                maxlength="300"
                show-word-limit
                resize="none"
                placeholder="请输入审核意见"
              ></el-input>
            </el-form-item>
          </el-form>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showApply = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button type="primary" @click="submitFormInfo" v-preventReClick>{{
            $t('action.comfirm')
          }}</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  data () {
    let checkStatus = (rule, value, callback) => {
      if (!this.subForm.status) {
        callback(new Error('请选择审核结果'))
      } else {
        callback()
      }
    }
    return {
      loading: false,
      statusText: {
        1: {
          type: 'info',
          text: '申请中'
        },
        2: {
          type: '',
          text: '已授权'
        },
        3: {
          type: 'danger',
          text: '取消授权'
        },
        4: {
          type: 'danger',
          text: '已驳回'
        }
      },
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      searchForm: {
        page: 1,
        limit: 10,
        status: 0,
        start_time: '',
        end_time: '',
        name: ''
      },
      tableData: [],
      total: 0,
      count: {},
      showApply: false,
      applyForm: {
        title: '',
        status: '',
        sh_text: ''
      },
      subForm: {
        id: 0,
        status: 0,
        sh_text: ''
      },
      subFormRules: {
        status: { required: true, validator: checkStatus, trigger: 'blur' }
      },
      dialogform: {
        page: 1,
        limit: 10,
        type: '1',
        id: ''
      },
      dialogTotal: 0,
      dialogLoading: false,
      dialogTableData: [],
      applyFormRules: {
        user_name: { required: true, type: 'string', message: '请输入姓名', trigger: 'blur' },
        mobile: { required: true, validator: this.$reg.isTel, text: '手机号', reg_type: 2, trigger: 'blur' }
      }
    }
  },
  created () {
    this.getTableDataList(1)
  },
  methods: {
    handleClick (e) {
      console.log(e)
      this.dialogform.page = 1
      this.getSubList()
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
    dialogHandleSizeChange (val) {
      this.dialogform.limit = val
      this.dialogHandleCurrentChange(1)
    },
    dialogHandleCurrentChange (val) {
      this.dialogform.page = val
      this.getSubList()
    },
    async toChange (index) {
      this.searchForm.status = index
      this.getTableDataList(1)
    },
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      let { code, data } = await this.$api.distribution.resellerList(searchForm)
      this.loading = false
      if (code !== 200) return
      data.data.map(item => {
        let text = item.text // || '该用户没有填写备注'
        item.text = text.replace(/\n/g, '<br>')
      })
      let { all, nopass, ing, pass, total } = data
      this.tableData = data.data
      this.total = total
      this.count = { all, nopass, ing, pass }
    },
    async toShowApply (id = 0) {
      let { code, data } = await this.$api.distribution.resellerInfo({ id })
      if (code !== 200) return
      let text = data.text // || '该用户没有填写备注'
      data.text = text.replace(/\n/g, '<br>')
      this.applyForm = data
      this.subForm = {
        id,
        status: 2,
        sh_text: ''
      }
      this.dialogform.id = id
      this.dialogform.type = '1'
      this.getSubList()
      this.showApply = !this.showApply
    },
    async getSubList () {
      this.dialogLoading = true
      let { code, data } = await this.$api.distribution.getSubList(this.dialogform)
      this.dialogLoading = false
      if (code !== 200) return
      this.dialogTableData = data.data
      this.dialogTotal = data.total
    },
    confirmDel (id, status) {
      this.$confirm(this.$t(status === 3 ? 'tips.confirmNoPass' : 'tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, status)
      }).catch(() => {

      })
    },
    async updateItem (id, status) {
      this.$api.distribution.resellerUpdate({ id, status }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
          }
          this.getTableDataList()
        } else {
          if (status === -1) return
          this.getTableDataList()
        }
      })
    },
    submitFormInfo () {
      let subForm = {
        id: this.applyForm.id,
        text: this.applyForm.text,
        user_name: this.applyForm.user_name,
        mobile: this.applyForm.mobile
      }
      let param = (this.applyForm.status === 1 || this.applyForm.status === 3) ? this.subForm : subForm
      let name = (this.applyForm.status === 1 || this.applyForm.status === 3) ? 'subForm' : 'applyForm'
      this.$refs[name].validate(valid => {
        if (valid) {
          this.$api.distribution.resellerUpdate(param).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showApply = false
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
.el-form {
  .el-input {
    width: 200px;
  }
  .el-image {
    width: 120px;
    height: 120px;
  }
  .el-textarea {
    width: 600px;
  }
}
</style>
