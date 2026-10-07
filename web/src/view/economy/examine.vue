<template>
  <div class="lb-goods-list">
    <top-nav />
    <div class="page-main">
      <lb-button
        size="medium"
        type="primary"
        icon="el-icon-plus"
        v-hasPermi="`${$route.name}-add`"
        @click="$router.push(`/promotion/economy/add`)"
        >新增经纪人</lb-button
      >
      <div class="space-lg"></div>
      <el-row class="page-top-operate">
        <el-button
          @click="toChange(0)"
          :type="searchForm.status === 0 ? 'primary' : ''"
          plain
          size="medium"
          >全部（{{ count.all || 0 }}）</el-button
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
        <el-button
          @click="toChange(4)"
          :type="searchForm.status === 4 ? 'primary' : ''"
          plain
          size="medium"
          >已驳回（{{ count.refuse || 0 }}）</el-button
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
        <el-table-column prop="id" label="ID"></el-table-column>
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
          prop="name"
          label="姓名"
          :min-width="120"
        ></el-table-column>
        <el-table-column
          prop="mobile"
          label="手机号"
          :min-width="120"
        ></el-table-column>
        <el-table-column prop="text" label="备注" :min-width="120">
          <template slot-scope="scope">
            <div v-html="scope.row.text"></div>
          </template>
        </el-table-column>
        <el-table-column prop="balance" label="抽成比例" :min-width="120">
          <template slot-scope="scope"> {{ scope.row.balance }}% </template>
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
                  $router.push(`/promotion/economy/add?id=${scope.row.id}`)
                "
                v-show="scope.row.status == 2"
                v-hasPermi="`${$route.name}-edit`"
                >{{ $t('action.edit') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="toShowApply(scope.row, 4)"
                v-show="scope.row.status == 4"
                v-hasPermi="`${$route.name}-view`"
                >{{ $t('action.view') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id, -1)"
                v-show="scope.row.status !== 1 && scope.row.status !== 4"
                v-hasPermi="`${$route.name}-delete`"
                >{{ $t('action.delete') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="success"
                @click="toShowApply(scope.row, 1)"
                v-show="scope.row.status === 1 || scope.row.status === 3"
                v-hasPermi="
                  scope.row.status === 1
                    ? `${$route.name}-examine`
                    : `${$route.name}-resetAuth`
                "
                >{{
                  $t(
                    scope.row.status === 1
                      ? 'action.examine'
                      : 'action.resetAuth'
                  )
                }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="success"
                @click="confirmDel(scope.row.id, 3)"
                v-show="scope.row.status === 2"
                v-hasPermi="`${$route.name}-cancelAuth`"
                >{{ $t('action.cancelAuth') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="success"
                @click="setScaleItem(scope.row)"
                v-show="scope.row.status == 2"
                v-hasPermi="`${$route.name}-setScale`"
                >{{ $t('action.setScale') }}</lb-button
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
        title="经纪人审核"
        :visible.sync="showApply"
        width="800px"
        center
      >
        <div style="height: 60vh; overflow: auto" v-if="showApply">
          <el-form
            @submit.native.prevent
            :model="applyForm"
            label-width="130px"
          >
            <el-form-item label="用户ID：">
              <div>{{ applyForm.user_id }}</div>
            </el-form-item>
            <el-form-item label="头像：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="[{ url: applyForm.avatarUrl }]"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="1"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="昵称：">
              <div>{{ applyForm.nickName }}</div>
            </el-form-item>
            <el-form-item label="姓名：">
              <div>{{ applyForm.name }}</div>
            </el-form-item>
            <el-form-item label="手机号：">
              <div>{{ applyForm.mobile }}</div>
            </el-form-item>
            <el-form-item label="备注：">
              <div v-html="applyForm.text"></div>
            </el-form-item>
            <el-form-item label="申请时间：">
              <div>
                {{ applyForm.create_time | handleTime() }}
              </div>
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
                <div class="pre-wrap">{{ applyForm.sh_text }}</div>
              </el-form-item>
            </div>
          </el-form>
          <el-form
            @submit.native.prevent
            :model="subForm"
            ref="subForm"
            :rules="subFormRules"
            label-width="130px"
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
            <el-form-item label="提成比例：" v-if="subForm.status == 2">
              <el-input-number
                class="lb-input-number"
                :min="0"
                :max="100"
                :precision="2"
                :controls="false"
                v-model="subForm.balance"
                placeholder="请输入提成比例"
              ></el-input-number>
              <div>%</div>
              <div class="c-warning">
                提成比例不填写或者填写为0默认使用全局提成比例
              </div>
            </el-form-item>
            <el-form-item label="驳回理由：" v-if="subForm.status == 4">
              <el-input
                type="textarea"
                :rows="10"
                v-model="subForm.sh_text"
                maxlength="400"
                show-word-limit
                resize="none"
                placeholder="请输入驳回理由"
              ></el-input>
            </el-form-item>
          </el-form>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showApply = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button
            type="primary"
            @click="submitFormInfo"
            v-preventReClick
            v-if="applyForm.status === 1 || applyForm.status === 3"
            >{{ $t('action.comfirm') }}</el-button
          >
        </span>
      </el-dialog>
      <el-dialog
        title="设置比例"
        :visible.sync="showScale"
        width="500px"
        center
      >
        <el-form
          @submit.native.prevent
          :model="scaleForm"
          :rules="scaleFormRules"
          ref="scaleForm"
          label-width="130px"
        >
          <el-form-item label="抽成比例" prop="balance">
            <el-input-number
              v-model="scaleForm.balance"
              placeholder="请输入"
              min="0"
              max="100"
              precision="2"
              :controls="false"
              class="lb-input-number"
            ></el-input-number>
            <div>%</div>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showScale = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button type="primary" @click="scaleFormInfo" v-preventReClick>{{
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
      statusOptions: [
        {
          label: '全部',
          value: 0
        },
        {
          label: '申请中',
          value: 1
        },
        {
          label: '已通过',
          value: 2
        },
        {
          label: '已驳回',
          value: 4
        },
        {
          label: '重新审核',
          value: 3
        }
      ],
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      searchForm: {
        page: 1,
        limit: 10,
        status: 0, // 状态 1申请中2通过3取消授权4驳回
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
        sh_text: '',
        balance: 0
      },
      subFormRules: {
        status: { required: true, validator: checkStatus, trigger: 'blur' }
      },
      showScale: false,
      scaleForm: {
        id: '',
        balance: ''
      },
      scaleFormRules: {
        balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur', reg_type: 1, decimal: 1 }
      }
    }
  },
  created () {
    this.getTableDataList(1)
  },
  methods: {
    resetForm (form) {
      this.$refs[form].resetFields()
      this.searchForm.status = 0
      this.searchForm.is_update = 0
      this.searchForm.start_time = ''
      this.searchForm.end_time = ''
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
      if (searchForm.status === 3) {
        searchForm.status = ''
        searchForm.is_update = 1
      }
      let { code, data } = await this.$api.economy.getList(searchForm)
      this.loading = false
      if (code !== 200) return
      data.data.map(item => {
        let text = item.text || '该用户没有填写备注'
        item.text = text.replace(/\n/g, '<br>')
      })
      let { all_count: all, apply_count: ing, pass_count: pass, update_count: update, refuse_count: refuse, total } = data
      this.tableData = data.data
      this.total = total
      this.count = { all, ing, pass, refuse, update }
    },
    async toShowApply (data = {}, type = '') {
      this.applyForm = data
      this.subForm = {
        id: data.id,
        status: 2,
        sh_text: '',
        balance: 0
      }
      this.showApply = !this.showApply
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
      this.$api.economy.update({ id, status }).then(res => {
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
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let param = JSON.parse(JSON.stringify(this.subForm))

          this.$api.economy.update(param).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showApply = false
              this.getTableDataList()
            }
          })
        }
      })
    },
    // 置顶
    changeTopping (id, status) {
      this.$api.storeshop.storeTop({ id, status }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successOper'))
          this.getTableDataList()
        }
      })
    },
    setScaleItem (item) {
      console.log(item)
      let {
        balance = 0,
        id = 0
      } = item
      this.scaleForm.id = id
      this.scaleForm.balance = balance
      this.showScale = true
    },
    scaleFormInfo () {
      this.$refs['scaleForm'].validate(valid => {
        if (valid) {
          let param = JSON.parse(JSON.stringify(this.scaleForm))
          this.$api.economy.update(param).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showScale = false
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
