<!--
 * @Descripttion: 客户管理
 * @Author: xiao li
 * @Date: 2021-03-11 15:42:01
 * @LastEditors: wen kun
 * @LastEditTime: 2024-12-02 16:02:32
-->

<template>
  <div class="lb-custom">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="用户ID" prop="id">
            <el-input
              v-model="searchForm.id"
              placeholder="请输入用户ID"
            ></el-input>
          </el-form-item>
          <el-form-item label="微信昵称" prop="nickName">
            <el-input
              v-model="searchForm.nickName"
              placeholder="请输入微信昵称"
            ></el-input>
          </el-form-item>
          <el-form-item label="授权类型" prop="type">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.type"
              placeholder="请选择"
            >
              <el-option
                v-for="item in statusOptions"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="客户来源" prop="form_type">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.form_type"
              placeholder="请选择"
            >
              <el-option
                v-for="item in typeOptions"
                :key="item.value"
                :label="item.label"
                :value="item.value"
                v-show="
                  (item.auth &&
                    (item.auth == 'channelstaff'
                      ? routesItem.auth.channelstaff && routesItem.auth.channel
                      : routesItem.auth[item.auth])) ||
                  !item.auth
                "
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="加入时间" prop="start_time">
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
      <el-row class="page-top-operate">
        <lb-button
          size="mini"
          plain
          type="primary"
          icon="el-icon-download"
          :loading="downloadLoading"
          @click="toExportExcel"
          v-hasPermi="`${$route.name}-export`"
        >
          {{ $t('action.export') }}</lb-button
        >
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="nickName" label="微信昵称"></el-table-column>
        <el-table-column prop="avatarUrl" label="微信头像">
          <template slot-scope="scope">
            <lb-image :src="scope.row.avatarUrl" />
          </template>
        </el-table-column>
        <el-table-column prop="phone" label="手机号"></el-table-column>
        <el-table-column prop="balance" label="账户余额">
          <template slot-scope="scope">
            ￥{{ scope.row.balance }}
            <!-- <div>
              <el-link
                icon="el-icon-edit"
                :underline="false"
                type="primary"
                @click="toShowDialog(scope.row)"
                v-show="pagePermission.includes('modifyBalance')"
              >
                {{ $t('action.modifyBalance') }}
              </el-link>
            </div> -->
          </template>
        </el-table-column>
        <el-table-column prop="from_name" label="客户来源"></el-table-column>
        <el-table-column prop="user_label" label="用户标签" min-width="180">
          <template slot-scope="scope">
            <el-tag
              @close="toDelLabel(scope.row.id, item.label_id)"
              :closable="pagePermission.includes('deleteTag')"
              class="mr-md mt-sm mb-sm"
              v-for="(item, index) in scope.row.user_label"
              :key="index"
              >{{ item.title }}</el-tag
            >
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="加入时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column label="操作" fixed="right" min-width="200px">
          <template slot-scope="scope">
            <!-- <lb-button
              size="mini"
              plain
              type="danger"
              @click="confirmBlock(scope.row.id)"
              v-hasPermi="`${$route.name}-blockUser`"
              >{{ $t('action.blockUser') }}</lb-button
            > -->
            <lb-button
              size="mini"
              plain
              type="primary"
              @click="$router.push(`/custom/detail?id=${scope.row.id}`)"
              v-hasPermi="`${$route.name}-view`"
              >{{ $t('action.view') }}</lb-button
            >
            <el-dropdown
              @command="handleMenuCommand($event, scope.row)"
              v-if="
                (routesItem.auth.member &&
                  $route.meta.pagePermission[0].auth.includes('blockUser')) ||
                $route.meta.pagePermission[0].auth.includes('modifyBalance')
              "
            >
              <el-button type="danger" plain size="mini">
                更多菜单<i class="el-icon-arrow-down el-icon--right"></i>
              </el-button>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item
                  command="blockUser"
                  v-show="
                    $route.meta.pagePermission[0].auth.includes('blockUser')
                  "
                  >{{ $t('action.blockUser') }}</el-dropdown-item
                >
                <el-dropdown-item
                  command="modifyBalance"
                  v-show="
                    $route.meta.pagePermission[0].auth.includes('modifyBalance')
                  "
                  >{{ $t('action.modifyBalance') }}</el-dropdown-item
                >
              </el-dropdown-menu>
            </el-dropdown>
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
        title="修改余额"
        :visible.sync="showDialog"
        width="700px"
        center
      >
        <div class="flex-y-center pl-xl pb-lg">
          <lb-image
            style="width: 80px; height: 80px"
            :src="subForm.info.avatarUrl"
          />
          <div class="pl-lg">
            <div class="f-title text-bold">{{ subForm.info.nickName }}</div>
            <div class="pt-lg">原储值余额：￥{{ subForm.info.balance }}</div>
          </div>
        </div>
        <el-form
          class="dialog-form"
          :model="subForm"
          ref="subForm"
          :rules="subFormRules"
          label-width="120px"
        >
          <el-form-item label="选择储值方式" prop="type">
            <el-radio-group v-model="subForm.type">
              <el-radio :label="1">选择套餐</el-radio>
              <el-radio :label="2">自定义充值</el-radio>
              <el-radio :label="3">减扣余额</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="充值金额" prop="price" v-if="subForm.type === 2">
            <el-input
              v-model="subForm.price"
              placeholder="请输入充值金额"
              style="width: 250px"
            ></el-input>
          </el-form-item>
          <el-form-item label="减扣余额" prop="price" v-if="subForm.type === 3">
            <el-input
              v-model="subForm.price"
              placeholder="请输入减扣余额"
              style="width: 250px"
            ></el-input>
          </el-form-item>
        </el-form>
        <div class="flex" v-if="subForm.type === 1">
          <div
            class="pr-md"
            style="width: 120px; text-align: right; padding-top: 12px"
          >
            充值套餐
          </div>
          <div>
            <el-table
              v-loading="cardLoading"
              :data="cardTableData"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              style="width: 550px"
              highlight-current-row
              @current-change="handleSelectionChange($event)"
            >
              <el-table-column prop="title" label="套餐名称" min-width="200">
              </el-table-column>
              <el-table-column prop="price" label="购买价格" min-width="120">
                <template slot-scope="scope"> ¥{{ scope.row.price }} </template>
              </el-table-column>
              <el-table-column
                prop="true_price"
                label="实际充值"
                min-width="120"
              >
                <template slot-scope="scope">
                  ¥{{ scope.row.true_price }}
                </template>
              </el-table-column>
            </el-table>
            <lb-page
              :batch="false"
              :page="cardForm.page"
              :pageSize="cardForm.limit"
              :total="cardTotal"
              @handleSizeChange="handleSizeChange($event, 'card')"
              @handleCurrentChange="handleCurrentChange($event, 'card')"
            >
            </lb-page>
          </div>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="handleDialogConfirm"
            >确 定</el-button
          >
        </span>
      </el-dialog>
      <el-dialog
        :title="$t('action.balanceModify')"
        :visible.sync="showBalanceModify"
        width="800px"
        center
      >
        <el-table
          v-loading="loadingRecord"
          :data="tableDataRecord"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          style="width: 100%"
        >
          <el-table-column prop="id" label="ID" width="120"></el-table-column>
          <el-table-column
            prop="control_name"
            label="操作者"
            width="120"
          ></el-table-column>
          <el-table-column prop="" label="操作记录" min-width="200">
            <template slot-scope="scope">
              <span>
                {{ typeText[scope.row.type] }}【{{ scope.row.goods_title }}】
                <span
                  :class="[
                    { 'c-link': scope.row.add },
                    { 'c-warning': !scope.row.add }
                  ]"
                  >{{ scope.row.add ? '+' : '-' }}¥{{ scope.row.price }}</span
                >
              </span>
              ，现余额<span class="ml-sm c-success"
                >¥{{ scope.row.after_balance }}</span
              >
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="操作时间" width="120">
            <template slot-scope="scope">
              <p>{{ scope.row.create_time | handleTime(1) }}</p>
              <p>{{ scope.row.create_time | handleTime(2) }}</p>
            </template>
          </el-table-column>
        </el-table>
        <lb-page
          :batch="false"
          :page="recordForm.page"
          :pageSize="recordForm.limit"
          :total="totalRecord"
          @handleSizeChange="handleSizeChange($event, 'record')"
          @handleCurrentChange="handleCurrentChange($event, 'record')"
        >
        </lb-page>
      </el-dialog>
    </div>
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
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      statusOptions: [
        {
          label: '全部',
          value: 0
        },
        {
          // label: '已授权手机号',
          label: '未授权微信昵称/头像',
          value: 1
        },
        {
          label: '已授权微信昵称/头像',
          value: 2
        }
      ],
      typeOptions: [{
        label: '全部',
        value: 0
      }, {
        label: '公众号搜索',
        value: 1
      }, {
        label: '用户分享链接',
        value: 2
      }, {
        label: '分销码',
        value: 3
      }, {
        label: '渠道商邀请用户二维码',
        value: 4,
        auth: 'channel'
      }, {
        label: '渠道商员工码',
        value: 5,
        auth: 'channelstaff'
      }, {
        label: '经纪人邀请码',
        value: 6,
        auth: 'broker'
      }, {
        label: '代理商邀请码',
        value: 7
      }, {
        label: this.$t('action.attendantName') + '-充值邀请码',
        value: 8
      }, {
        label: '渠道商邀请员工二维码',
        value: 9,
        auth: 'channelstaff'
      }, {
        label: '分销员邀请用户购买会员卡',
        value: 10,
        auth: 'member'
      }, {
        label: '抖音',
        value: 11
      }, {
        label: '视频号',
        value: 12
      }, {
        label: '小红书',
        value: 13
      }, {
        label: '其他',
        value: 14
      }],
      typeText: { 1: '充值', 2: '消费', 3: '消费退款', 4: '消费', 5: '扣款', 6: '退款' },
      pagePermission: [],
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        id: '',
        nickName: '',
        start_time: '',
        end_time: '',
        type: 0,
        form_type: 0
      },
      tableData: [],
      total: 0,
      downloadLoading: false,
      showDialog: false,
      subForm: {
        card_id: 0,
        is_add: 1, // 1增加 -1减少
        price: '',
        user_id: [],
        type: 1,
        info: {}
      },
      subFormRules: {
        type: { required: true, message: '请选择储值方式', trigger: 'change' },
        price: { required: true, validator: this.$reg.isFloatNum, text: '金额', reg_type: 2, trigger: 'blur' },
      },
      showBalanceModify: false,
      loadingRecord: false,
      tableDataRecord: [],
      totalRecord: 0,
      recordForm: {
        user_id: '',
        page: 1,
        limit: 10
      },
      cardLoading: false,
      cardTableData: [],
      cardTotal: 0,
      cardForm: {
        page: 1,
        limit: 10,
        status: 1
      },

      //
      activeName: 'label'
    }
  },
  async activated () {
    this.routesItem.routes.map(item => {
      if (item.path === '/custom') {
        item.children.map(aitem => {
          if (aitem.name === 'CustomList') {
            this.pagePermission = aitem.meta.pagePermission[0].auth
          }
        })
      }
    })
    // let ind = this.typeOptions.findIndex(item => {
    //   return item.value === 10
    // })
    // if (!this.routesItem.auth.member) {
    //   this.typeOptions.splice(ind, 1)
    // }
    await this.getTableDataList()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    handleMenuCommand (type, item) {
      let {
        id
      } = item
      switch (type) {
        case 'blockUser':
          this.confirmBlock(id)
          break
        case 'modifyBalance':
          this.toShowDialog(item)
          break
      }
    },
    resetForm (form) {
      this.$refs[form].resetFields()
      this.getTableDataList(1)
    },
    handleSizeChange (val, type = 'search') {
      this[`${type}Form`].limit = val
      this.handleCurrentChange(1, type)
    },
    handleCurrentChange (val, type = 'search') {
      this[`${type}Form`].page = val
      if (type === 'record') {
        this.getRecordDataList()
      } else if (type === 'card') {
        this.getCardList()
      } else {
        this.getTableDataList()
      }
    },
    async getRecordDataList (flag) {
      if (flag) this.recordForm.page = 1
      this.tableDataRecord = []
      this.loadingRecord = true

      let { code, data } = await this.$api.custom.payWater(this.recordForm)
      this.loadingRecord = false
      if (code !== 200) return
      this.tableDataRecord = data.data
      this.totalRecord = data.total
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
      let { code, data } = await this.$api.custom.userList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    // 拉黑
    confirmBlock (id) {
      this.$confirm(`操作后，用户无法访问手机端系统，同时该用户不再展示在客户列表里，如需查看，请在【客户黑名单】里查看，确认需要拉黑用户吗`, this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: `我再想想`,
        type: 'warning'
      }).then(() => {
        this.$api.custom.setBlacklist({ id, is_on: 1 }).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successOper'))
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
            this.getTableDataList()
          }
        })
      }).catch(() => {

      })
    },
    confirmDel (id, status) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, status)
      }).catch(() => {

      })
    },
    async updateItem (id, status) {
      this.$api.storeStatusUpdate({ id, status }).then(res => {
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
    async toDelLabel (uid, id) {
      let { code } = await this.$api.custom.delUserLabel({ user_id: uid, label_id: id })
      if (code !== 200) return
      this.$message.success(this.$t('tips.successDel'))
      this.getTableDataList()
    },
    toShowDialog (item) {
      this.subForm.info = item
      this.subForm.user_id = [item.id]
      this.subForm.card_id = ''
      this.subForm.type = 1
      this.subForm.price = ''
      this.getCardList()
      this.showDialog = true
    },
    handleDialogConfirm () {
      let flag = false
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          flag = true
        }
      })
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        if (!subForm.card_id && subForm.type === 1) {
          this.$message.error(`请选择套餐`)
          return
        }
        subForm.is_add = subForm.type === 3 ? -1 : 1
        delete subForm.info
        delete subForm.type
        this.$api.custom.payBalanceOrder(subForm).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successOper'))
            this.getTableDataList()
            this.showDialog = false
          }
        })
      }
    },
    async getCardList (flag) {
      if (flag) this.searchForm.page = 1
      this.cardLoading = true
      this.$api.finance.cardList(this.cardForm).then(res => {
        this.cardLoading = false
        if (res.code === 200) {
          this.cardTableData = res.data.data
          this.cardTotal = res.data.total
        }
      })
    },
    async changeBalanceModify (id) {
      this.recordForm.user_id = id
      this.getRecordDataList(1)
      this.showBalanceModify = true
    },
    handleSelectionChange (e) {
      this.subForm.card_id = e.id
    },
    /**
    * @method 导出订单
    */
    toExportExcel () {
      this.downloadLoading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      let url = this.$util.getProCurrentHref()
      let keywords = url.indexOf('?') > 0 ? '' : '?'
      let flag = url.indexOf('?') > 0
      Object.getOwnPropertyNames(searchForm).forEach((keys, value) => {
        keywords += flag
          ? `&${keys}=${searchForm[keys]}`
          : `${keys}=${searchForm[keys]}`
        flag = true
      })
      let token = window.localStorage.getItem('massage_minitk')
      let dwonlaodUrl = `${url}/massage/admin/AdminExcel/userList${keywords}&token=${token}`
      window.location.href = dwonlaodUrl
      setTimeout(() => {
        this.downloadLoading = false
      }, 5000)
    },
    handleClick (e) {
      let { name } = e
      this.activeName = name
      // this.getTableDataList(1, name)
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
</style>
