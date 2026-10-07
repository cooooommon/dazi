<!--
 * @Description: 向导管理
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2024-11-22 14:40:18
 * @LastEditors: wen kun
-->
<template>
  <div class="lb-examine">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <lb-button
          size="medium"
          type="primary"
          icon="el-icon-plus"
          @click="$router.push(`/technician/edit`)"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.TechnicianAdd') }}</lb-button
        >
      </el-row>
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
          >已驳回（{{ count.nopass || 0 }}）</el-button
        >
        <el-button
          @click="toChange(-1)"
          :type="searchForm.status === -1 ? 'primary' : ''"
          plain
          size="medium"
          >重新审核（{{ count.update || 0 }}）</el-button
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
              style="width: 260px"
              :placeholder="`请输入${$t(
                'action.attendantName'
              )}姓名/昵称/手机号`"
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
          <el-form-item label="所属代理商" prop="admin_id">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.admin_id"
              placeholder="请选择代理商"
              filterable
            >
              <el-option
                v-for="item in base_agent"
                :key="item.id"
                :label="item.agent_name"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="认证状态" prop="is_user">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.is_user"
              placeholder="请选择"
            >
              <el-option
                v-for="item in userTypeList"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              ></el-option>
            </el-select>
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
        @selection-change="handleSelectionChange"
      >
        <el-table-column
          type="selection"
          width="55"
          v-if="cash_type == 2"
          key="selection"
        >
        </el-table-column>
        <el-table-column prop="id" label="ID" fixed key="ID"></el-table-column>
        <el-table-column
          prop="work_img"
          :label="`${$t('action.attendantName')}头像`"
          min-width="120"
          key="work_img"
        >
          <template slot-scope="scope">
            <lb-image :src="scope.row.work_img" />
          </template>
        </el-table-column>
        <el-table-column
          key="coach_name"
          prop="coach_name"
          label="昵称"
          min-width="120"
        ></el-table-column>
        <el-table-column
          key="nickname"
          prop="nickname"
          label="姓名"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="mobile"
          label="手机号"
          min-width="120"
          key="mobile"
        ></el-table-column>
        <el-table-column
          prop="create_time"
          label="申请时间"
          min-width="120"
          key="create_time"
        >
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="admin_add" label="申请人" key="admin_add">
          <template slot-scope="scope">
            <el-tag :type="addText[scope.row.admin_add].type">{{
              addText[scope.row.admin_add].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="认证状态" key="user_id">
          <template slot-scope="scope">
            <el-tag v-if="scope.row.user_id">已认证</el-tag>
            <el-tag type="danger" v-if="!scope.row.user_id">暂未认证</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" key="status">
          <template slot-scope="scope">
            <el-tag :type="statusText[scope.row.status].type">
              {{ statusText[scope.row.status].text }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="是否上班" key="is_work">
          <template slot-scope="scope">
            <el-tag :type="statusWork[scope.row.is_work].type">
              {{ statusWork[scope.row.is_work].text }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column
          prop="cash_balance"
          label="抽成比例"
          min-width="120"
          v-if="cash_type == 2"
          key="cash_balance"
        >
          <template slot-scope="scope">
            <div>{{ scope.row.cash_balance }}%</div>
          </template>
        </el-table-column>
        <el-table-column
          prop="admin_name"
          label="所属代理商"
          min-width="120"
          key="admin_name"
        ></el-table-column>
        <el-table-column
          prop="address"
          label="当前定位"
          min-width="200"
          key="address"
        ></el-table-column>
        <el-table-column
          prop="status"
          label="设为推荐"
          min-width="120"
          key="recommend"
          v-if="routesItem.auth.recommend"
        >
          <template slot-scope="scope">
            <el-switch
              :disabled="
                userInfo.is_admin !== 0 &&
                $route.meta.pagePermission[0].auth.includes('edit')
                  ? false
                  : true
              "
              v-model="scope.row.recommend"
              :active-value="1"
              :inactive-value="0"
              @change="updateItem(2, scope.row.id, scope.row.recommend)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="220" key="open" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <!-- 管理员
                    编辑：没有重新审核的数据可编辑
                    删除
                    重新审核
                    授权向导｜重新授权向导
                    取消授权
                    修改代理商
               -->
              <!-- 代理商
                    编辑：没有重新审核的数据可编辑
                    -->
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="$router.push(`/technician/edit?id=${scope.row.id}`)"
                v-show="scope.row.is_update !== 1"
                v-hasPermi="`${$route.name}-edit`"
                >{{ $t('action.edit') }}</lb-button
              >

              <div v-if="userInfo.is_admin !== 0">
                <lb-button
                  size="mini"
                  plain
                  type="danger"
                  @click="toShowApply(scope.row.id, 2)"
                  v-show="scope.row.is_update === 1"
                  v-hasPermi="`${$route.name}-resetExamine`"
                  >{{ $t('action.resetExamine') }}</lb-button
                >
                <lb-button
                  size="mini"
                  plain
                  type="danger"
                  @click="confirmDel(scope.row.id, -1)"
                  v-show="scope.row.status !== 1"
                  v-hasPermi="`${$route.name}-delete`"
                  >{{ $t('action.delete') }}</lb-button
                >
                <lb-button
                  size="mini"
                  plain
                  type="success"
                  @click="toShowApply(scope.row.id)"
                  v-show="scope.row.status === 1 || scope.row.status === 3"
                  v-hasPermi="
                    scope.row.status === 1
                      ? `${$route.name}-authTechnician`
                      : `${$route.name}-resetAuth`
                  "
                  >{{
                    $t(
                      scope.row.status === 1
                        ? 'action.authTechnician'
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
                  type="warning"
                  @click="confirmChange(scope.row)"
                  v-hasPermi="`${$route.name}-updateAgent`"
                  >{{ $t('action.updateAgent') }}</lb-button
                >
              </div>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <lb-page
        :batch="cash_type == 2"
        :page="searchForm.page"
        :pageSize="searchForm.limit"
        :total="total"
        :selected="batchArr.length"
        @handleSizeChange="handleSizeChange"
        @handleCurrentChange="handleCurrentChange"
      >
        <lb-button
          size="mini"
          plain
          type="primary"
          @click="editConfirmDialog"
          v-hasPermi="`${$route.name}-batchEditDrawProportion`"
          >批量修改抽成比例</lb-button
        >
      </lb-page>
      <el-dialog
        title="申请详情"
        :visible.sync="showDialog.apply"
        width="800px"
        center
        class="dialog-form"
      >
        <div style="height: 60vh; overflow: auto" v-if="showDialog.apply">
          <el-form
            @submit.native.prevent
            :model="applyForm"
            label-width="130px"
            size="mini"
          >
            <div class="flex-warp">
              <el-form-item label="用户ID：" style="width: 50%">
                <div>{{ applyForm.user_id }}</div>
              </el-form-item>
              <el-form-item label="微信昵称：" style="width: 50%">
                <div>{{ applyForm.nickName }}</div>
              </el-form-item>
            </div>
            <el-form-item label="昵称：">
              <div>{{ applyForm.coach_name }}</div>
            </el-form-item>
            <el-form-item label="姓名：">
              <div>{{ applyForm.nickname }}</div>
            </el-form-item>
            <el-form-item label="性别：">
              <div>{{ applyForm.sex === 0 ? '男' : '女' }}</div>
            </el-form-item>
            <el-form-item label="生日：">
              <div>{{ applyForm.birthday | handleTime(1) }}</div>
            </el-form-item>
            <el-form-item label="星座：">
              <div>{{ applyForm.constellation }}</div>
            </el-form-item>
            <el-form-item label="身高：">
              <div>{{ applyForm.height }}cm</div>
            </el-form-item>
            <el-form-item label="体重：">
              <div>{{ applyForm.weight }}kg</div>
            </el-form-item>
            <el-form-item label="手机号：">
              <div>{{ applyForm.mobile }}</div>
            </el-form-item>
            <el-form-item label="意向工作城市：">
              <div>{{ applyForm.city }}</div>
            </el-form-item>
            <el-form-item label="所在地址：">
              <div>{{ applyForm.address }}</div>
            </el-form-item>
            <el-form-item label="个人简介：">
              <div v-html="applyForm.text"></div>
            </el-form-item>
            <el-form-item label="拥有的技能：">
              <div class="flex-warp f-caption c-title">
                <div
                  @click="toChangeItem(index)"
                  class="fill-body flex-center mb-sm mr-md pl-lg pr-lg cursor-pointer radius"
                  style="height: 30px; color: #ff4c88; background: #fdf3f6"
                  v-for="(item, index) in applyForm.service"
                  :key="index"
                >
                  {{ item.title }}
                </div>
              </div>
            </el-form-item>
            <el-form-item label="身份证号：">
              <div>{{ applyForm.id_code }}</div>
            </el-form-item>
            <el-form-item label="身份证正反面：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="applyForm.id_card"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="applyForm.id_card.length"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="工作形象照：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="[{ url: applyForm.work_img }]"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="1"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item
              label="模特照："
              v-if="applyForm.model_img.length > 0"
            >
              <div class="flex-warp">
                <lb-cover
                  :fileList="applyForm.model_img"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="applyForm.model_img.length"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="个人生活照：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="applyForm.self_img"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="applyForm.self_img.length"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="个人视频介绍：" v-if="applyForm.video">
              <video
                controls
                width="500"
                height="300"
                :src="applyForm.video"
              ></video>
            </el-form-item>
            <el-form-item label="是否上班：">
              <div>{{ applyForm.is_work == 1 ? '上班' : '下班' }}</div>
            </el-form-item>
            <el-form-item label="接单时间：" v-if="applyForm.is_work == 1">
              <div>{{ applyForm | handleStartEndTime }}</div>
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
                v-if="applyForm.sh_text"
              >
                <div>{{ applyForm.sh_text }}</div>
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
            v-show="applyForm.status === 1 || applyForm.status == 3"
            v-hasPermi="
              applyForm.status === 1
                ? `${$route.name}-authTechnician`
                : `${$route.name}-resetAuth`
            "
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
          <el-button @click="showDialog.apply = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button
            type="primary"
            @click="submitForm('sub')"
            v-show="applyForm.status === 1 || applyForm.status === 3"
            v-hasPermi="
              applyForm.status === 1
                ? `${$route.name}-authTechnician`
                : `${$route.name}-resetAuth`
            "
            v-preventReClick
            >{{ $t('action.comfirm') }}</el-button
          >
        </span>
      </el-dialog>

      <el-dialog
        title="修改所属代理商"
        :visible.sync="showDialog.change"
        width="600px"
        center
        class="dialog-form"
      >
        <el-form
          @submit.native.prevent
          :model="changeForm"
          ref="changeForm"
          label-width="140px"
        >
          <el-form-item label="原代理商：" v-if="changeForm.admin_name">
            {{ changeForm.admin_name }}
          </el-form-item>
          <el-form-item label="修改所属代理商：">
            <el-select
              @change="getStoreList(2)"
              v-model="changeForm.admin_id"
              placeholder="请选择代理商"
              filterable
              clearable
              class="mt-md"
            >
              <el-option
                v-for="item in base_agent"
                :key="item.id"
                :label="item.agent_name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item
            label="挂靠门店"
            prop="store_id"
            v-if="routesItem.auth.store && base_store.length > 0"
          >
            <el-select
              v-model="changeForm.store_id"
              filterable
              clearable
              placeholder="请选择"
            >
              <el-option
                v-for="item in base_store"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.change = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button
            type="primary"
            @click="submitForm('change', 2)"
            v-preventReClick
            >{{ $t('action.comfirm') }}</el-button
          >
        </span>
      </el-dialog>

      <el-dialog
        :title="`修改${$t('action.attendantName')}抽成比例`"
        :visible.sync="showTakeDialog"
        width="500px"
        center
        class="dialog-form"
      >
        <el-form
          @submit.native.prevent
          :model="takeForm"
          ref="takeForm"
          label-width="110px"
          :rules="takeFormRules"
        >
          <el-form-item label="抽成比例：" prop="cash_balance">
            <el-input
              v-model.number="takeForm.cash_balance"
              placeholder="请输入抽成比例"
            >
              <template slot="append">%</template>
            </el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showTakeDialog = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button
            type="primary"
            @click="submitTakeForm('takeForm')"
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
      userInfo: {},
      loading: false,
      userTypeList: [{ id: 0, title: '全部' }, { id: 1, title: '已认证' }, { id: 2, title: '未认证' }],
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
      statusWork: {
        1: {
          type: '',
          text: '上班'
        },
        0: {
          type: 'danger',
          text: '下班'
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
        is_user: 0,
        start_time: '',
        end_time: '',
        admin_id: '',
        name: ''
      },
      tableData: [],
      batchArr: [],
      total: 0,
      count: {},
      showDialog: {
        apply: false,
        change: false
      },
      applyForm: {
        title: '',
        status: '',
        sh_text: ''
      },
      subForm: {
        id: 0,
        status: 0,
        sh_text: '',
        type: 1
      },
      subFormRules: {
        status: { required: true, validator: checkStatus, trigger: 'blur' }
      },
      base_agent: [],
      base_store: [],
      changeForm: {
        id: '',
        admin_name: '',
        admin_id: '',
        store_id: ''
      },
      showTakeDialog: false,
      takeForm: {
        cash_balance: 0,
        ids: []
      },
      takeFormRules: {
        cash_balance: { required: true, validator: this.$reg.isPercent, type: 'number', message: '请输入0至100的整数', trigger: 'blur' }
      },
      cash_type: ''
    }
  },
  created () {
    this.userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
    this.getBaseInfo()
  },
  async activated () {
    await this.getTableDataList(1)
    this.getConfigInfo()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    /**
     * @name: 批量选中
     * @param {*} selectedArr
     */
    handleSelectionChange (selectedArr) {
      this.batchArr = selectedArr.map(item => {
        return item.id
      })
    },
    editConfirmDialog () {
      if (this.batchArr.length < 1) {
        this.$message.error('请选择要操作的数据')
        return
      }
      this.takeForm.cash_balance = 0
      this.showTakeDialog = true
    },
    submitTakeForm (form) {
      let flag = true
      this.takeForm.ids = this.batchArr
      this.$refs[form].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        this.$api.technician.setBalance(this.takeForm).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successOper'))
            this.getTableDataList()
            this.showTakeDialog = false
          }
        })
      }
    },
    async getConfigInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.cash_type = data.cash_type
    },
    async getBaseInfo () {
      let { code, data } = await this.$api.agent.adminSelect()
      if (code !== 200) return
      this.base_agent = data
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
      let { code, data } = await this.$api.technician.coachList(
        searchForm
      )
      this.loading = false
      if (code !== 200) return
      let { all, nopass, ing, pass, total, update_num: update } = data
      this.tableData = data.data
      this.total = total
      this.count = { all, nopass, ing, pass, update }
    },
    async toShowApply (id = 0, type = 1) {
      let methodModel = type === 2 ? 'coachUpdateInfo' : 'coachInfo'
      let { code, data } = await this.$api.technician[methodModel]({ id })
      if (code !== 200) return
      let arr = ['id_card', 'self_img', 'model_img']
      arr.map(item => {
        data[item] = data[item] && data[item].length > 0 ? data[item].map(aitem => {
          return { url: aitem }
        }) : []
      })

      data.text = data.text ? data.text.replace(/\n/g, '<br>') : '-'
      this.applyForm = data
      this.subForm = {
        id,
        status: 2,
        sh_text: '',
        type
      }
      this.showDialog.apply = true
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
      this.$api.technician.coachUpdate(param).then((res) => {
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
    async confirmChange (item = {}) {
      item = JSON.parse(JSON.stringify(item))
      item.admin_id = item.admin_id || ''
      item.store_id = item.store_id || ''
      for (let key in this.changeForm) {
        this.changeForm[key] = item[key]
      }
      await this.getStoreList()
      this.showDialog.change = true
    },
    async getStoreList (type = 1) {
      if (type === 2) {
        this.changeForm.store_id = ''
      }
      let { admin_id: aid = 0 } = this.changeForm
      let store = []
      if (aid) {
        let { code, data } = await this.$api.technician.storeSelect({ admin_id: aid })
        if (code !== 200) return
        store = data
      }
      this.base_store = store
    },
    async submitForm (key, validate = 1) {
      let flag = true
      if (validate === 1) {
        this.$refs[`${key}Form`].validate(valid => {
          if (!valid) flag = false
        })
      }
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this[`${key}Form`]))
        let methodModel = key === 'sub' && subForm.type === 2 ? 'coachUpdateCheck' : 'coachUpdate'
        if (key === 'sub') {
          delete subForm.type
        } else {
          delete subForm.admin_name
        }
        this.$api.technician[methodModel](subForm).then((res) => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successSub'))
            this.showDialog[key === 'sub' ? 'apply' : key] = false
            this.getTableDataList('', 'list')
          }
        })
      }
    },
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    },
    handleStartEndTime (val) {
      let text = ''
      let { start_time: start, end_time: end } = val
      let day = moment(Date.now()).format('YYYY-MM-DD')
      if (start && end) {
        text = (moment(`${day} ${end}`).unix() < moment(`${day} ${start}`).unix()) ? `${start} 至 次日${end}` : `${start} 至 ${end}`
      }
      return text
    }
  }
}
</script>

<style lang="scss" scoped>
.dialog-form {
  .el-select {
    width: 300px;
  }
}
</style>
