<!--
 * @Description: ''
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2024-04-18 14:45:45
 * @LastEditors: wen kun
-->
<template>
  <div class="lb-examine">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <el-button
          @click="toChange(0)"
          :type="searchForm.check_status === 0 ? 'primary' : ''"
          plain
          size="medium"
          >全部（{{ count.all || 0 }}）</el-button
        >
        <el-button
          @click="toChange(1)"
          :type="searchForm.check_status === 1 ? 'primary' : ''"
          plain
          size="medium"
          >审核中（{{ count.ing || 0 }}）</el-button
        >
        <el-button
          @click="toChange(2)"
          :type="searchForm.check_status === 2 ? 'primary' : ''"
          plain
          size="medium"
          >已通过（{{ count.pass || 0 }}）</el-button
        >
        <el-button
          @click="toChange(3)"
          :type="searchForm.check_status === 3 ? 'primary' : ''"
          plain
          size="medium"
          >已驳回（{{ count.nopass || 0 }}）</el-button
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
              placeholder="请输入服务名称"
            ></el-input>
          </el-form-item>
          <el-form-item label="提交审核时间" prop="start_time">
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
        <el-table-column prop="cover" label="封面图">
          <template slot-scope="scope">
            <lb-image :src="scope.row.cover" />
          </template>
        </el-table-column>
        <el-table-column
          prop="title"
          label="服务名称"
          min-width="120"
        ></el-table-column>
        <el-table-column prop="price" label="服务价格">
          <template slot-scope="scope">
            {{ `¥${scope.row.price}` }}
          </template>
        </el-table-column>
        <!-- <el-table-column prop="init_price" label="服务原价">
          <template slot-scope="scope">
            {{ `¥${scope.row.init_price}` }}
          </template>
        </el-table-column> -->
        <el-table-column prop="time_long" label="服务时长">
          <template slot-scope="scope">
            {{ `${scope.row.time_long}分钟` }}
          </template>
        </el-table-column>
        <el-table-column prop="top" label="排序值"></el-table-column>
        <el-table-column prop="admin_name" label="创建人"></el-table-column>
        <el-table-column
          prop="create_time"
          label="提交审核时间"
          min-width="120"
        >
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope" v-if="scope.row.check_status">
            <el-tag :type="statusText[scope.row.check_status].type">
              {{ statusText[scope.row.check_status].text }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                :type="scope.row.check_status === 1 ? 'primary' : 'success'"
                @click="toShowApply(scope.row.id)"
                v-hasPermi="
                  scope.row.check_status === 1
                    ? `${$route.name}-examine`
                    : `${$route.name}-view`
                "
                >{{
                  $t(
                    scope.row.check_status === 1
                      ? 'action.examine'
                      : 'action.view'
                  )
                }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id, -1)"
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
            size="mini"
          >
            <el-form-item label="服务名称：">
              <div>{{ applyForm.title }}</div>
            </el-form-item>
            <el-form-item label="副标题：">
              <div>{{ applyForm.sub_title }}</div>
            </el-form-item>
            <el-form-item label="封面图：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="[{ url: applyForm.cover }]"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="1"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="轮播图：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="applyForm.imgs"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="applyForm.imgs.length"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="服务价格：">
              <div>¥{{ applyForm.price }}</div>
            </el-form-item>
            <el-form-item label="服务原价：">
              <div>¥{{ applyForm.init_price }}</div>
            </el-form-item>
            <el-form-item label="虚拟销售量：">
              <div>{{ applyForm.sale }}</div>
            </el-form-item>
            <el-form-item label="服务时长：">
              <div>{{ applyForm.time_long }}分钟</div>
            </el-form-item>
            <el-form-item label="排序值：">
              <div>{{ applyForm.top }}</div>
            </el-form-item>
            <el-form-item label="项目介绍" prop="introduce">
              <lb-ueditor
                v-model="applyForm.introduce"
                :destroy="true"
              ></lb-ueditor>
            </el-form-item>
            <el-form-item label="禁忌说明" prop="explain">
              <lb-ueditor
                v-model="applyForm.explain"
                :destroy="true"
              ></lb-ueditor>
            </el-form-item>
            <el-form-item label="下单须知" prop="notice">
              <lb-ueditor
                v-model="applyForm.notice"
                :destroy="true"
              ></lb-ueditor>
            </el-form-item>
            <el-form-item
              :label="`关联${$t('action.attendantName')}`"
              prop="coach"
            >
              <el-table
                :data="applyForm.coach"
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
                    <lb-image
                      :src="scope.row.work_img"
                      style="width: 50px; height: 50px"
                    />
                  </template>
                </el-table-column>
                <el-table-column
                  prop="coach_name"
                  :label="`${$t('action.attendantName')}名称`"
                ></el-table-column>
              </el-table>
            </el-form-item>
            <div class="space-lg"></div>
            <div class="space-lg b-1px-t"></div>
            <div class="space-lg"></div>
            <div v-if="applyForm.check_status !== 1">
              <div class="flex-warp">
                <el-form-item
                  label="审核结果："
                  prop="status"
                  style="width: 50%"
                >
                  <el-tag :type="statusText[applyForm.check_status].type">{{
                    statusText[applyForm.check_status].text
                  }}</el-tag>
                </el-form-item>
                <el-form-item
                  label="审核时间："
                  prop=""
                  style="width: 50%"
                  v-show="applyForm.check_status != 0 && applyForm.check_time"
                >
                  <div class="c-warning">
                    {{ applyForm.check_time | handleTime() }}
                  </div>
                </el-form-item>
              </div>
              <el-form-item
                label="审核意见："
                prop="check_text"
                v-if="applyForm.check_text"
              >
                <div>{{ applyForm.check_text }}</div>
              </el-form-item>
            </div>
          </el-form>
          <el-form
            @submit.native.prevent
            :model="subForm"
            ref="subForm"
            :rules="subFormRules"
            label-width="130px"
            size="mini"
            v-if="applyForm.check_status === 1"
          >
            <el-form-item
              label="审核结果："
              prop="check_status"
              style="width: 50%"
            >
              <el-radio-group v-model="subForm.check_status">
                <el-radio :label="2">通过</el-radio>
                <el-radio :label="3" v-if="applyForm.check_status === 1"
                  >驳回</el-radio
                >
              </el-radio-group>
            </el-form-item>
            <el-form-item label="审核意见：">
              <el-input
                type="textarea"
                :rows="10"
                v-model="subForm.check_text"
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
          <el-button
            type="primary"
            @click="submitForm"
            v-preventReClick
            v-show="applyForm.check_status === 1"
            >{{ $t('action.comfirm') }}</el-button
          >
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
export default {
  data () {
    let checkStatus = (rule, value, callback) => {
      if (!this.subForm.check_status) {
        callback(new Error('请选择审核结果'))
      } else {
        callback()
      }
    }
    return {
      userInfo: {},
      loading: false,
      addText: {
        0: {
          type: 'info',
          text: '用户'
        },
        1: {
          type: '',
          text: '平台'
        }
      },
      statusText: {
        1: {
          type: 'info',
          text: '审核中'
        },
        2: {
          type: '',
          text: '已通过'
        },
        3: {
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
        check_status: 0,
        type: 2,
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
        check_text: ''
      },
      subForm: {
        id: 0,
        status: 0,
        check_text: ''
      },
      subFormRules: {
        status: { required: true, validator: checkStatus, trigger: 'blur' }
      }
    }
  },
  created () {
    this.userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
  },
  activated () {
    this.getTableDataList(1)
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
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
    async toChange (index) {
      this.searchForm.check_status = index
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
      let { code, data } = await this.$api.service.serviceList(
        searchForm
      )
      this.loading = false
      if (code !== 200) return
      let { all, nopass, ing, pass, total } = data
      this.tableData = data.data
      this.total = total
      this.count = { all, nopass, ing, pass }
    },
    async toShowApply (id = 0) {
      let { code, data } = await this.$api.service.serviceInfo({ id })
      if (code !== 200) return
      let arr = ['imgs']
      arr.map(item => {
        data[item] = data[item].length > 0 ? data[item].map(aitem => {
          return { url: aitem }
        }) : []
      })
      this.applyForm = data
      this.subForm = {
        id,
        check_status: 2,
        check_text: ''
      }
      this.showApply = !this.showApply
    },
    confirmDel (id, status) {
      this.$confirm(this.$t(status === 3 ? 'tips.confirmNoPass' : 'tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(1, id, status)
      }).catch(() => { })
    },
    async updateItem (key, id, status) {
      let param = key === 1 ? { id, status } : { id, recommend: status }
      this.$api.service.serviceUpdate(param).then((res) => {
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
    submitForm () {
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let param = JSON.parse(JSON.stringify(this.subForm))
          this.$api.service.checkStoreGoods(param).then((res) => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showApply = false
              this.getTableDataList()
            }
          })
        }
      })
    },
    confirmedit (row) {
      let item = JSON.parse(JSON.stringify(row))
      if (item.admin_id === 0) {
        item.admin_id = ''
      }
      this.editInfo = item
      this.franchiseeDialog = true
    },
    submitFranchisee () {
      let { id, admin_id: aid = 0 } = this.editInfo
      if (!aid) {
        this.$message.error(`请选择代理商`)
        return
      }
      this.$api.technician.coachUpdate({ id, admin_id: aid }).then((res) => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successOper'))
          this.franchiseeDialog = false
          this.getTableDataList()
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
.f_r_m_c {
  display: flex;
  align-items: center;
}
.f1 {
  display: flex;
  flex: 1;
}
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
