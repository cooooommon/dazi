<template>
  <div class="lb-goods-list">
    <top-nav />
    <div class="page-main">
      <lb-button
        size="medium"
        type="primary"
        icon="el-icon-plus"
        @click="$router.push(`/storeshop/edit`)"
        v-hasPermi="`${$route.name}-add`"
        >新增门店</lb-button
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
          >已通过（{{ count.pass || 0 }}）</el-button
        >
        <el-button
          @click="toChange(4)"
          :type="searchForm.status === 4 ? 'primary' : ''"
          plain
          size="medium"
          >已驳回（{{ count.refuse || 0 }}）</el-button
        >
        <el-button
          @click="toChange(3)"
          :type="searchForm.status === 3 ? 'primary' : ''"
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
          <el-form-item label="门店信息" prop="name">
            <el-input
              v-model="searchForm.name"
              placeholder="请输入门店名称查询门店"
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
          <el-form-item label="认证状态" prop="name">
            <el-select
              @change="getTableDataList(1)"
              v-model="searchForm.status"
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
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="name" label="店铺名称"></el-table-column>
        <el-table-column prop="cover" label="店铺封面">
          <template slot-scope="scope">
            <lb-image :src="scope.row.cover" />
          </template>
        </el-table-column>
        <el-table-column
          prop="mobile"
          label="商家电话"
          :min-width="120"
        ></el-table-column>
        <el-table-column prop="store_balance" label="门店比例">
          <template slot-scope="scope">
            <div>
              {{ scope.row.status == 2 ? scope.row.store_balance + '%' : '--' }}
            </div>
          </template>
        </el-table-column>
        <el-table-column
          prop="address"
          label="商家地址"
          :min-width="120"
        ></el-table-column>
        <el-table-column
          prop="create_time"
          label="申请时间"
          :min-width="120"
        ></el-table-column>
        <el-table-column prop="status" label="认证状态">
          <template slot-scope="scope">
            <el-tag :type="statusText[scope.row.status].type">{{
              statusText[scope.row.status].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="是否置顶" min-width="100">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="changeTopping(scope.row.id, 1)"
                v-hasPermi="`${$route.name}-topping`"
                >{{ $t('action.topping') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="changeTopping(scope.row.id, 2)"
                v-show="scope.row.is_top == 1"
                v-hasPermi="`${$route.name}-cancelTopping`"
                >{{ $t('action.cancelTopping') }}</lb-button
              >
            </div>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="$router.push(`/storeshop/edit?id=${scope.row.id}`)"
                v-show="
                  scope.row.is_update !== 1 &&
                  scope.row.status !== 3 &&
                  scope.row.status !== 1
                "
                v-hasPermi="`${$route.name}-edit`"
                >{{ $t('action.edit') }}</lb-button
              >
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
                @click="toShowApply(scope.row.id, 1)"
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
                v-show="scope.row.status == 2"
                @click="toPackage(scope.row.id)"
                v-hasPermi="`${$route.name}-package`"
                >{{ $t('action.package') }}</lb-button
              >
              <!--&& scope.row.package_num > 0-->
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
        :batch="true"
        :page="searchForm.page"
        :pageSize="searchForm.limit"
        :total="total"
        @handleSizeChange="handleSizeChange"
        @handleCurrentChange="handleCurrentChange"
        :selected="batchMultipleSelection.length"
      >
        <lb-button
          type="primary"
          @click="batchProportion"
          v-hasPermi="`${$route.name}-setScale`"
          >批量设置比例</lb-button
        >
      </lb-page>
      <el-dialog
        title="门店审核"
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
            <el-form-item label="用户ID：">
              <div class="flex-y-center">
                <div>{{ applyForm.user_id }}</div>
                <div class="flex-1" style="padding-left: 160px">
                  <span>用户昵称：</span>
                  <span>{{ applyForm.user_name }}</span>
                </div>
              </div>
            </el-form-item>
            <el-form-item label="店铺名称：">
              <div>{{ applyForm.name }}</div>
            </el-form-item>
            <el-form-item label="店铺封面：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="applyForm.cover"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="applyForm.cover.length"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="店铺详情图：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="applyForm.banner"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="applyForm.banner.length"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="商家电话：" v-if="applyForm.contact_type == 1">
              <div>{{ applyForm.mobile }}</div>
            </el-form-item>
            <el-form-item label="企业微信：" v-else>
              <div>{{ applyForm.qywx_kid }}</div>
            </el-form-item>
            <el-form-item label="营业执照：">
              <div class="flex-warp">
                <lb-cover
                  :fileList="applyForm.license"
                  :isToDel="false"
                  size="small"
                  type="more"
                  :fileSize="applyForm.license.length"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="店铺地址：">
              <div>{{ applyForm.address }}</div>
            </el-form-item>
            <el-form-item label="门牌号：">
              <div>{{ applyForm.info }}</div>
            </el-form-item>
            <el-form-item
              label="营业时间："
              v-if="applyForm.trade_week.length > 0"
            >
              <div class="flex pr-md">
                <span
                  class="pr-lg"
                  v-for="(item, index) in applyForm.trade_week"
                  :key="index"
                  >{{ item }}</span
                >
              </div>
              <div class="pt-lg">
                {{ applyForm.start_time }}
                <span class="pl-sm pr-sm">-</span>{{ applyForm.end_time }}
              </div>
            </el-form-item>
            <el-form-item label="门店所属分类：">
              <div class="flex-warp">
                <span
                  class="pr-lg"
                  v-for="(item, index) in applyForm.type_name"
                  :key="index"
                  >{{ item }}</span
                >
              </div>
            </el-form-item>
            <el-form-item label="门店标签：">
              <div class="flex-warp pr-md">
                <span
                  class="pr-lg"
                  v-for="(item, index) in applyForm.tag"
                  :key="index"
                  >{{ item }}</span
                >
              </div>
            </el-form-item>
            <el-form-item label="场地介绍：">
              <div class="pr-md">
                <p style="white-space: pre-wrap">{{ applyForm.intro }}</p>
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
                prop="check_msg"
                v-if="applyForm.check_msg && applyForm.status !== 3"
              >
                <div>{{ applyForm.check_msg }}</div>
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
                v-model="subForm.check_msg"
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
            @click="submitFormInfo"
            v-preventReClick
            v-if="applyForm.status === 1 || applyForm.status === 3"
            >{{ $t('action.comfirm') }}</el-button
          >
        </span>
      </el-dialog>
      <el-dialog
        title="修改比例"
        :visible.sync="showBatchSet"
        width="500px"
        center
      >
        <div class="">
          <el-form
            @submit.native.prevent
            :inline="true"
            :model="batchSetForm"
            :rules="batchSetFormRules"
            ref="batchSetForm"
            label-width="150px"
          >
            <el-form-item label="门店提成比例" prop="store_balance">
              <el-input
                v-model="batchSetForm.store_balance"
                placeholder="请输入提成比例"
              >
                <template slot="append">%</template>
              </el-input>
            </el-form-item>
          </el-form>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showBatchSet = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button
            type="primary"
            @click="showBatchSetFormInfo"
            v-preventReClick
            >{{ $t('action.comfirm') }}</el-button
          >
        </span>
      </el-dialog>
      <el-dialog
        title="设置门店套餐提成比例"
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
          <lb-tips
            >佣金规则说明：套餐订单佣金由门店、推广者、平台三者分得。
            <div class="mt-sm">门店提成=套餐订单实际支付金额*门店提成</div>
            <div class="mt-sm">推广者提成=套餐实际支付金额*推广者提成</div>
            <div class="mt-sm mb-sm">
              平台提成=套餐实际支付金额-门店提成-推广者提成
            </div></lb-tips
          >
          <el-form-item label="门店提成" prop="store_balance">
            <el-input-number
              v-model="scaleForm.store_balance"
              placeholder="请输入"
              min="0"
              max="100"
              precision="2"
              :controls="false"
              class="lb-input-number"
            ></el-input-number>
            <div>%</div>
          </el-form-item>
          <el-form-item label="推广者" prop="share_balance">
            <el-input-number
              v-model="scaleForm.share_balance"
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
        check_msg: ''
      },
      subForm: {
        id: 0,
        status: 0,
        check_msg: ''
      },
      subFormRules: {
        status: { required: true, validator: checkStatus, trigger: 'blur' }
      },
      showScale: false,
      scaleForm: {
        id: '',
        store_balance: 0,
        share_balance: 0
      },
      scaleFormRules: {
        store_balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur', reg_type: 1, decimal: 1 },
        share_balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur', reg_type: 1, decimal: 1 }
      },
      batchMultipleSelection: [],
      showBatchSet: false,
      batchSetForm: {
        ids: [],
        store_balance: ''
      },
      batchSetFormRules: {
        store_balance: { required: true, validator: this.$reg.isPercent, text: '提成比例', trigger: 'blur', decimal: 1 }
      }
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  created () {
    this.getTableDataList(1)
  },
  methods: {
    resetForm (form) {
      this.$refs[form].resetFields()
      this.searchForm.status = 0
      this.searchForm.is_update = 0
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
      let { code, data } = await this.$api.storeshop.getList(searchForm)
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
    async toShowApply (id = 0, type = '') {
      let medel = {
        1: 'info',
        2: 'reInfo'
      }
      let methodModel = medel[type]

      let { code, data } = await this.$api.storeshop[methodModel]({ id })
      for (let key in data) {
        if (['cover', 'license', 'banner'].includes(key)) {
          let imgArr = []
          data[key].split(',').forEach(item => {
            imgArr.push({ url: item })
          })
          data[key] = imgArr
        }
        if (key === 'tag') {
          data.tag = data.tag.split(',')
        }
      }
      if (code !== 200) return
      let text = data.text || '该用户没有填写备注'
      data.text = text.replace(/\n/g, '<br>')
      data.showState = type
      let week = {
        0: '周天',
        1: '周一',
        2: '周二',
        3: '周三',
        4: '周四',
        5: '周五',
        6: '周六'
      }
      let weekArr = []
      data.trade_week.split(',').forEach(item => {
        if (item != 0) {
          weekArr.push(week[item])
        }
      })
      if (data.trade_week.includes(0)) {
        weekArr.push('周天')
      }

      data.trade_week = weekArr
      this.applyForm = data
      this.subForm = {
        id,
        status: 2,
        check_msg: ''
      }
      this.showApply = !this.showApply
    },
    toPackage (id) {
      if (!this.routesItem.auth.store) {
        this.$message.error('暂无权限')
        return
      }
      this.$router.push(`/storeshop/package?id=${id}`)
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
      this.$api.storeshop.changeStatus({ id, status }).then(res => {
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
          let methodModel = ''
          if (this.applyForm.status === 1 || this.applyForm.status === 3) {
            methodModel = 'check'
          }
          if (this.applyForm.showState === 2) {
            methodModel = 'reCheck'
            if (param.status == 4) {
              param.status = 3
            }
            param.id = this.applyForm.id
          }
          this.$api.storeshop[methodModel](param).then(res => {
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
        store_balance: store = 0,
        share_balance: share = 0,
        id = 0
      } = item
      this.scaleForm.id = id
      this.scaleForm.share_balance = share
      this.scaleForm.store_balance = store

      this.showScale = true
    },
    scaleFormInfo () {
      this.$refs['scaleForm'].validate(valid => {
        if (valid) {
          let param = JSON.parse(JSON.stringify(this.scaleForm))
          if (param.store_balance * 1 + param.share_balance * 1 > 100) {
            this.$message.error(this.$t('门店提成 + 推广者提成不能大于100'))
            return
          }
          this.$api.storeshop.editStore(param).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showScale = false
              this.getTableDataList()
            }
          })
        }
      })
    },
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
    batchProportion () {
      if (!this.batchMultipleSelection.length) {
        this.$message.error('请选择数据')
        return
      }
      let arr = []
      this.batchMultipleSelection.forEach(item => {
        arr.push(item.id)
      })
      this.batchSetForm.store_balance = ''
      this.batchSetForm.ids = arr
      this.showBatchSet = true
    },
    async showBatchSetFormInfo () {
      this.$refs['batchSetForm'].validate(valid => {
        if (valid) {
          this.$api.storeshop.editStoreBalance(this.batchSetForm).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showBatchSet = false
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
