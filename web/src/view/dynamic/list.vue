<!--
 * @Description: 动态管理
 * @Author: xiao li
 * @Date: 2023-01-28 16:54:09
 * @LastEditTime: 2024-04-19 18:14:22
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-dynamic-list">
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
          @click="toChange(1)"
          :type="searchForm.status === 1 ? 'primary' : ''"
          plain
          size="medium"
          >未审核（{{ count.ing || 0 }}）</el-button
        >
        <el-button
          @click="toChange(2)"
          :type="searchForm.status === 2 ? 'primary' : ''"
          plain
          size="medium"
          >审核通过（{{ count.pass || 0 }}）</el-button
        >
        <el-button
          @click="toChange(3)"
          :type="searchForm.status === 3 ? 'primary' : ''"
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
          <el-form-item label="输入查询" prop="coach_name">
            <el-input
              v-model="searchForm.coach_name"
              placeholder="请输入发布人姓名"
            ></el-input>
          </el-form-item>
          <el-form-item label="发布时间" prop="start_time">
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
        <el-table-column prop="cover" label="封面图" min-width="120">
          <template slot-scope="scope">
            <lb-image :src="scope.row.cover" />
          </template>
        </el-table-column>
        <el-table-column
          prop="title"
          label="标题"
          min-width="200"
        ></el-table-column>
        <el-table-column prop="mobile" label="发布类型" min-width="120">
          <template slot-scope="scope">
            {{ typeText[scope.row.type] }}
          </template>
        </el-table-column>
        <el-table-column
          prop="coach_name"
          label="发布人"
          min-width="120"
        ></el-table-column>
        <el-table-column prop="cover" label="发布人头像" min-width="120">
          <template slot-scope="scope">
            <lb-image :src="scope.row.work_img" />
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="发布时间" min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="是否置顶" min-width="120">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.top"
              :active-value="1"
              :inactive-value="0"
              @change="updateItem(2, scope.row.id, scope.row.top)"
              :disabled="
                $route.meta.pagePermission[0].auth.includes('topping')
                  ? false
                  : true
              "
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            <el-tag :type="statusText[scope.row.status].type">
              {{ statusText[scope.row.status].text }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                :type="scope.row.status === 1 ? 'primary' : 'success'"
                @click="toShowApply(scope.row.id)"
                v-hasPermi="
                  scope.row.status === 1
                    ? `${$route.name}-examine`
                    : `${$route.name}-view`
                "
                >{{
                  $t(scope.row.status === 1 ? 'action.examine' : 'action.view')
                }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id)"
                v-show="scope.row.status !== 1"
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
        title="发布详情"
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
            <el-form-item label="内容：">
              <div class="flex-warp" v-if="applyForm.type === 1">
                <lb-cover
                  :fileList="applyForm.imgs"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="applyForm.imgs.length"
                ></lb-cover>
              </div>
              <video
                controls
                width="500"
                height="300"
                :src="applyForm.imgs[0].url"
                v-if="applyForm.type === 2"
              ></video>
            </el-form-item>
            <el-form-item label="标题：">
              <div>{{ applyForm.title }}</div>
            </el-form-item>
            <el-form-item label="正文：">
              <div v-html="applyForm.text"></div>
            </el-form-item>
            <el-form-item label="地点：">
              <div>{{ applyForm.address }}</div>
            </el-form-item>
            <el-form-item label="发布人：">
              <div class="flex-y-center">
                <img
                  class="avatar sm radius"
                  :src="applyForm.coach_info.work_img"
                />
                <div class="ml-md">{{ applyForm.coach_info.coach_name }}</div>
              </div>
            </el-form-item>
            <el-form-item label="发布时间：">
              <div>{{ applyForm.create_time | handleTime }}</div>
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
                  v-show="applyForm.status != 1 && applyForm.check_time"
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
            v-if="applyForm.status === 1"
          >
            <el-form-item label="审核结果：" prop="status" style="width: 50%">
              <el-radio-group v-model="subForm.status">
                <el-radio :label="2">通过</el-radio>
                <el-radio :label="3" v-if="applyForm.status === 1"
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
            v-show="applyForm.status === 1"
            v-preventReClick
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
      if (!this.subForm.status) {
        callback(new Error('请选择审核结果'))
      } else {
        callback()
      }
    }
    return {
      typeText: { 1: '图片', 2: '视频' },
      statusText: {
        1: {
          type: 'info',
          text: '未审核'
        },
        2: {
          type: '',
          text: '审核通过'
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
        status: 0,
        start_time: '',
        end_time: '',
        coach_name: ''
      },
      loading: false,
      tableData: [],
      total: 0,
      count: {},
      showApply: false,
      applyForm: {},
      subForm: {
        id: 0,
        status: 2,
        check_text: ''
      },
      subFormRules: {
        status: { required: true, validator: checkStatus, trigger: 'blur' }
      }
    }
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
      if (searchForm.status === -1) {
        searchForm.is_update = 1
        delete searchForm.status
      }
      let { code, data } = await this.$api.dynamic.dynamicList(
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
      let { code, data } = await this.$api.dynamic.dynamicInfo({ id })
      if (code !== 200) return
      data.imgs = data.imgs.map(aitem => {
        return { url: aitem }
      })
      data.text = data.text ? data.text.replace(/\n/g, '<br>') : '-'
      this.applyForm = data
      this.subForm = {
        id,
        status: 2,
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
        this.updateItem(1, id, -1)
      }).catch(() => { })
    },
    async updateItem (key, id, status) {
      let param = key === 1 ? { id } : { id, top: status }
      let methodModel = key === 1 ? 'dynamicDel' : 'dynamicTop'
      this.$api.dynamic[methodModel](param).then((res) => {
        if (res.code === 200) {
          this.$message.success(this.$t(key === 1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
          }
          this.getTableDataList()
        }
      })
    },
    submitForm () {
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let param = JSON.parse(JSON.stringify(this.subForm))
          this.$api.dynamic.dynamicCheck(param).then((res) => {
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
