<!--
 * @Description:  渠道商审核
 * @Author: xiao li
 * @Date: 2022-09-02 09:21:18
 * @LastEditTime: 2024-05-06 10:30:28
 * @LastEditors: wen kun
-->
<template>
  <div class="lb-goods-list">
    <top-nav :isBack="true" />
    <div class="page-main">
      <div>
        <el-form
          @submit.native.prevent
          :model="applyForm"
          :rules="applyFormRules"
          ref="applyForm"
          label-width="150px"
          size="mini"
        >
          <el-form-item label="">
            <div class="flex-y-center">
              <lb-image :src="applyForm.avatarUrl" />
              <div class="pl-lg">
                <div>{{ applyForm.nickName }}</div>
                <div>ID：{{ applyForm.id }}</div>
                <div>申请时间：{{ applyForm.create_time | handleTime() }}</div>
              </div>
            </div>
          </el-form-item>
          <el-form-item label="姓名：" prop="user_name">
            <el-input
              v-model="applyForm.user_name"
              placeholder="请输入姓名"
              style="width: 300px"
              size="medium"
            >
            </el-input>
          </el-form-item>
          <el-form-item label="手机号：" prop="mobile">
            <el-input
              v-model="applyForm.mobile"
              placeholder="请输入手机号"
              style="width: 300px"
              size="medium"
            >
            </el-input>
          </el-form-item>
          <!-- <el-form-item label="审核结果：" prop="status" style="width: 50%">
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
            </el-form-item> -->
          <el-form-item label="备注信息：">
            <el-input
              type="textarea"
              :rows="10"
              v-model="applyForm.text"
              maxlength="300"
              show-word-limit
              resize="none"
              placeholder="请输入备注信息"
            >
            </el-input>
          </el-form-item>
          <el-form-item label="提成比例：" prop="balance">
            <el-input
              v-model="applyForm.balance"
              placeholder="请输入提成比例"
              style="width: 300px"
              size="medium"
              :max="100"
            >
              <template slot="append">%</template>
            </el-input>
          </el-form-item>
          <el-form-item label="渠道商时效性" prop="channel_bind_time">
            <el-input-number
              class="lb-input-number"
              v-model="applyForm.channel_bind_time"
              placeholder="请输入渠道商时效性"
              style="width: 300px"
              size="medium"
              :controls="false"
            >
              <!-- <template slot="append">小时</template> -->
            </el-input-number>
            <div>小时</div>
            <lb-tool-tips>不填则表示使用全局时效性</lb-tool-tips>
          </el-form-item>
          <el-form-item label="下级数：" v-if="auth.channelstaff">
            <div>{{ applyForm.staff_count }}</div>
          </el-form-item>
          <el-form-item label="累计佣金：">
            <div>{{ applyForm.total_cash }}</div>
          </el-form-item>
          <el-form-item label="已提现佣金：">
            <div>{{ applyForm.extract_total_price }}</div>
          </el-form-item>
          <el-form-item label="当前余额：" prop="cash">
            <div class="flex-between pr-lg">
              <el-input-number
                v-model="applyForm.cash"
                placeholder="请输入当前余额"
                style="width: 300px"
                size="medium"
                maxlength="10"
                class="lb-input-number"
                :min="0"
                :controls="false"
              >
              </el-input-number>
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="balanceRecord(applyForm.id)"
                v-hasPermi="`${$route.name}-balanceChangeRecord`"
                >{{ $t('action.balanceChangeRecord') }}</lb-button
              >
            </div>
          </el-form-item>
          <div class="space-lg"></div>
          <div class="space-lg b-1px-t"></div>
          <div class="space-lg"></div>
          <div class="flex-warp">
            <el-form-item label="审核结果：" prop="status" style="width: 50%">
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
        </el-form>
        <div v-if="auth.channelstaff">
          <div class="page-search-form">
            <el-form
              @submit.native.prevent
              :inline="true"
              :model="editSearchForm"
              ref="editSearchForm"
            >
              <el-form-item label="" prop="start_time">
                <el-date-picker
                  v-model="editSearchForm.start_time"
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
                  @click="getStaffList(1)"
                  >{{ $t('action.search') }}</lb-button
                >
                <lb-button
                  size="medium"
                  icon="el-icon-refresh-left"
                  style="margin-right: 5px"
                  @click="resetForm('editSearchForm')"
                  >{{ $t('action.reset') }}</lb-button
                >
              </el-form-item>
            </el-form>
          </div>
          <div>订单总金额：￥{{ order_price }}</div>
          <div class="space-lg"></div>
          <el-table
            v-loading="editloading"
            :data="editTableData"
            :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
            style="width: 100%"
          >
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
              prop="name"
              label="下级姓名"
              :min-width="120"
            ></el-table-column>
            <el-table-column prop="balance" label="下级提成" :min-width="120">
              <template slot-scope="scope">
                <div>{{ scope.row.balance }}%</div>
              </template>
            </el-table-column>
            <el-table-column prop="price" label="订单金额" :min-width="120">
              <template slot-scope="scope">
                <div>￥{{ scope.row.price }}</div>
              </template>
            </el-table-column>
            <el-table-column
              prop="total_cash"
              label="累计佣金"
              :min-width="120"
            >
              <template slot-scope="scope">
                <div>￥{{ scope.row.total_cash }}</div>
              </template>
            </el-table-column>
            <el-table-column prop="wait_cash" label="未入账" :min-width="120">
              <template slot-scope="scope">
                <div>￥{{ scope.row.wait_cash }}</div>
              </template>
            </el-table-column>
            <el-table-column label="操作" min-width="160" fixed="right">
              <template slot-scope="scope">
                <div class="table-operate">
                  <lb-button
                    size="mini"
                    plain
                    type="primary"
                    @click="replaceSuperior(scope.row.id)"
                    v-hasPermi="`${$route.name}-replacingSuperiors`"
                    >{{ $t('action.replacingSuperiors') }}</lb-button
                  >
                </div>
              </template>
            </el-table-column>
          </el-table>
          <lb-page
            :batch="false"
            :page="editSearchForm.page"
            :pageSize="editSearchForm.limit"
            :total="editTotal"
            @handleSizeChange="editHandleSizeChange"
            @handleCurrentChange="editHandleCurrentChange"
          ></lb-page>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="goBack">{{ $t('action.cancel') }}</el-button>
        <el-button type="primary" @click="submitFormInfo" v-preventReClick>{{
          $t('action.comfirm')
        }}</el-button>
      </span>
      <el-dialog
        title="修改上级"
        :visible.sync="showSuperior"
        width="800px"
        center
      >
        <div class="page-search-form">
          <el-form
            @submit.native.prevent
            :inline="true"
            :model="superiorSearchForm"
            ref="superiorSearchForm"
          >
            <el-form-item label="输入查询" prop="name">
              <el-input
                v-model="superiorSearchForm.name"
                placeholder="请输入姓名/手机号"
              ></el-input>
            </el-form-item>
            <el-form-item>
              <lb-button
                size="medium"
                type="primary"
                icon="el-icon-search"
                style="margin-right: 5px"
                @click="getStaffList(1)"
                >{{ $t('action.search') }}</lb-button
              >
              <lb-button
                size="medium"
                icon="el-icon-refresh-left"
                style="margin-right: 5px"
                @click="resetForm('superiorSearchForm')"
                >{{ $t('action.reset') }}</lb-button
              >
            </el-form-item>
          </el-form>
        </div>
        <el-table
          v-loading="superiorloading"
          :data="superiorTableData"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          style="width: 100%"
          highlight-current-row
          @current-change="handleSelectionChange($event, 1)"
        >
          <el-table-column
            prop="id"
            label="ID"
            :min-width="60"
          ></el-table-column>
          <el-table-column prop="avatarUrl" label="头像">
            <template slot-scope="scope">
              <lb-image :src="scope.row.avatarUrl" />
            </template>
          </el-table-column>
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
          <el-table-column prop="balance" label="提成比例" :min-width="120">
            <template slot-scope="scope">
              <div>{{ scope.row.balance }}%</div>
            </template>
          </el-table-column>
        </el-table>
        <lb-page
          :batch="false"
          :page="superiorSearchForm.page"
          :pageSize="superiorSearchForm.limit"
          :total="superiorTotal"
          @handleSizeChange="superiorHandleSizeChange"
          @handleCurrentChange="superiorHandleCurrentChange"
        ></lb-page>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showSuperior = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button
            type="primary"
            @click="superiorSubmitFormInfo"
            v-preventReClick
            >{{ $t('action.comfirm') }}</el-button
          >
        </span>
      </el-dialog>
      <el-dialog
        title="余额变动记录"
        :visible.sync="showBalanceRecord"
        width="800px"
        center
      >
        <el-table
          v-loading="balanceloading"
          :data="balanceTableData"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          style="width: 100%"
        >
          <el-table-column
            prop="change"
            label="变动金额"
            :min-width="120"
          ></el-table-column>
          <el-table-column
            prop="before"
            label="修改前"
            :min-width="120"
          ></el-table-column>
          <el-table-column prop="after" label="修改后" :min-width="120">
          </el-table-column>
          <el-table-column prop="create_time" label="操作时间" :min-width="120">
            <template slot-scope="scope">
              <p>{{ scope.row.create_time | handleTime(1) }}</p>
              <p>{{ scope.row.create_time | handleTime(2) }}</p>
            </template>
          </el-table-column>
        </el-table>
        <lb-page
          :batch="false"
          :page="balanceSearchForm.page"
          :pageSize="balanceSearchForm.limit"
          :total="balanceTotal"
          @handleSizeChange="balanceHandleSizeChange"
          @handleCurrentChange="balanceHandleCurrentChange"
        ></lb-page>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import { mapState, mapMutations } from 'vuex'
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
      balanceloading: false,
      editloading: false,
      superiorloading: false,
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
        sh_text: '',
        user_name: '',
        mobile: '',
        balance: 0,
        cash: 0
      },
      subForm: {
        id: 0,
        status: 0,
        sh_text: '',
        balance: ''
      },
      subFormRules: {
        status: { required: true, validator: checkStatus, trigger: 'blur' },
        balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur' }
      },
      batchMultipleSelection: [],
      isEdit: false,
      editSearchForm: {
        start_time: '',
        end_time: '',
        id: '',
        page: 1,
        limit: 10
      },
      editTableData: [],
      editTotal: 0,
      order_price: 0,
      applyFormRules: {
        user_name: { required: true, type: 'string', message: '请输入姓名', trigger: 'blur' },
        mobile: { required: true, validator: this.$reg.isTel, text: '手机号', reg_type: 2, trigger: 'blur' },
        balance: { required: true, validator: this.$reg.isPercent, text: '提成比例', trigger: 'blur', decimal: 1 },
        cash: { required: true, type: 'number', message: '请输入当前余额', trigger: 'blur' },
        // channel_bind_time: { required: true, validator: this.$reg.isNum, text: '渠道商时效性', trigger: 'blur' }
      },
      showSuperior: false,
      superiorTotal: 0,
      superiorTableData: [],
      superiorSearchForm: {
        page: 1,
        limit: 10,
        ids: [],
        name: '',
        status: 2
      },
      superiorForm: {
        channel_id: '',
        channel_staff_id: ''
      },
      showBatchSet: false,
      batchSetForm: {
        ids: [],
        balance: ''
      },
      batchSetFormRules: {
        balance: { required: true, validator: this.$reg.isPercent, text: '提成比例', trigger: 'blur', decimal: 1 }
      },
      balanceSearchForm: {
        page: 1,
        limit: 10,
        id: ''
      },
      balanceTableData: [],
      balanceTotal: 0,
      showBalanceRecord: false
    }
  },
  created () {
    let { id } = this.$route.query
    this.toShowApply(id, 1)
  },
  computed: {
    ...mapState({
      auth: state => state.routes.auth
    })
  },
  methods: {
    /**
     * @method: 规格-多选
     * @param {*} val
     */
    handleSelectionChange (val, type) {
      console.log(val, type)
      if (type === 1) {
        this.superiorForm.channel_id = val.id
      } else {
        this.batchMultipleSelection = val
      }
    },
    goBack () {
      this.$router.back(-1)
    },
    resetForm (form) {
      this.$refs[form].resetFields()
      if (form === 'editSearchForm') {
        this.getStaffList(1)
      } else if (form === 'superiorSearchForm') {
        this.getSuperiorTableDataList(1)
      } else {
        this.getTableDataList(1)
      }
    },
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handleCurrentChange(1)
    },
    handleCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    editHandleSizeChange (val) {
      this.editSearchForm.limit = val
      this.editHandleCurrentChange(1)
    },
    editHandleCurrentChange (val) {
      this.editSearchForm.page = val
      this.getStaffList()
    },
    superiorHandleSizeChange (val) {
      this.superiorSearchForm.limit = val
      this.superiorHandleCurrentChange(1)
    },
    superiorHandleCurrentChange (val) {
      this.superiorSearchForm.page = val
      this.getSuperiorTableDataList()
    },
    balanceHandleSizeChange (val) {
      this.balanceSearchForm.limit = val
      this.balanceHandleCurrentChange(1)
    },
    balanceHandleCurrentChange (val) {
      this.balanceSearchForm.page = val
      this.getBalanceTableDataList()
    },
    async toChange (index) {
      this.searchForm.status = index
      this.getTableDataList(1)
    },
    async getStaffList (flag) {
      if (flag) this.editSearchForm.page = 1
      this.editTableData = []
      this.editloading = true
      let editSearchForm = JSON.parse(JSON.stringify(this.editSearchForm))
      let { start_time: time } = editSearchForm
      if (time && time.length > 0) {
        editSearchForm.start_time = time[0] / 1000
        editSearchForm.end_time = time[1] / 1000
      } else {
        editSearchForm.start_time = ''
        editSearchForm.end_time = ''
      }
      let { code, data } = await this.$api.channel.staffList(editSearchForm)
      this.editloading = false
      if (code !== 200) return
      this.editTableData = data.data
      this.editTotal = data.total
      this.order_price = data.order_price
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
      let { code, data } = await this.$api.channel.channelList(searchForm)
      this.loading = false
      if (code !== 200) return
      data.data.map(item => {
        let text = item.text || '该用户没有填写备注'
        item.text = text.replace(/\n/g, '<br>')
      })
      let { all, nopass, ing, pass, total } = data
      this.tableData = data.data
      this.total = total
      this.count = { all, nopass, ing, pass }
    },
    async getSuperiorTableDataList (flag) {
      if (flag) this.superiorSearchForm.page = 1
      this.superiorTableData = []
      this.superiorloading = true
      let superiorSearchForm = JSON.parse(JSON.stringify(this.superiorSearchForm))

      let { code, data } = await this.$api.channel.channelList(superiorSearchForm)
      this.superiorloading = false
      if (code !== 200) return
      data.data.map(item => {
        let text = item.text || '该用户没有填写备注'
        item.text = text.replace(/\n/g, '<br>')
      })
      let { total } = data
      this.superiorTableData = data.data
      this.superiorTotal = total
    },
    async getBalanceTableDataList (flag) {
      if (flag) this.balanceSearchForm.page = 1
      this.balanceTableData = []
      this.balanceloading = true
      let balanceSearchForm = JSON.parse(JSON.stringify(this.balanceSearchForm))

      let { code, data } = await this.$api.channel.getCashList(balanceSearchForm)
      this.balanceloading = false
      if (code !== 200) return
      let { total } = data
      this.balanceTableData = data.data
      this.balanceTotal = total
    },
    async toShowApply (id = 0, type, show = true) {
      let { code, data } = await this.$api.channel.channelInfo({ id })
      if (code !== 200) return
      let text = data.text || '该用户没有填写备注'
      data.text = text.replace(/\n/g, '<br>')
      this.applyForm = data
      this.subForm = {
        id,
        status: 2,
        sh_text: ''
      }
      this.superiorForm.channel_id = ''
      if (type) {
        this.superiorSearchForm.ids = [id]
        this.editSearchForm.id = id
        this.getStaffList(1)
        this.isEdit = true
      } else {
        this.isEdit = false
      }
      if (show) {
        this.showApply = !this.showApply
      }
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
      this.$api.channel.channelUpdate({ id, status }).then(res => {
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
      let {
        user_name,
        mobile,
        balance,
        cash,
        id,
        text,
        channel_bind_time
      } = this.applyForm

      let name = this.isEdit ? 'applyForm' : 'subForm'
      let param = this.isEdit ? { user_name, mobile, balance, cash, id, text, channel_bind_time } : this.subForm

      this.$refs[name].validate(valid => {
        if (valid) {
          this.$api.channel.channelUpdate(param).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showApply = false
              this.goBack()
            }
          })
        }
      })
    },
    superiorSubmitFormInfo () {
      let { superiorForm } = this
      if (!superiorForm.channel_id) {
        this.$message.error('请选择')
        return
      }
      console.log(superiorForm)
      this.$api.channel.changeChannel(superiorForm).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successSub'))
          this.showSuperior = false
          this.toShowApply(this.applyForm.id, 1, false)
        }
      })
    },
    async replaceSuperior (id) {
      this.superiorForm.channel_staff_id = id
      await this.getSuperiorTableDataList(1)
      this.showSuperior = true
    },
    batchProportion () {
      if (!this.batchMultipleSelection.length) {
        this.$message.error('请选择')
        return
      }
      let arr = []
      this.batchMultipleSelection.forEach(item => {
        arr.push(item.id)
      })
      this.batchSetForm.balance = ''
      this.batchSetForm.ids = arr
      this.showBatchSet = true
    },
    async showBatchSetFormInfo () {
      this.$refs['batchSetForm'].validate(valid => {
        if (valid) {
          this.$api.channel.changeBalance(this.batchSetForm).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showBatchSet = false
              this.getTableDataList()
            }
          })
        }
      })
    },
    balanceRecord (id) {
      this.balanceSearchForm.id = id
      this.getBalanceTableDataList()
      this.showBalanceRecord = true
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
    width: 70px;
    height: 70px;
  }
  .el-textarea {
    width: 550px;
  }
}
</style>
