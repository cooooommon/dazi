<!--
 * @Description: 客户详情
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-12-02 14:02:00
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-technician-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div class="page-main" v-if="subForm.id">
      <div class="flex-center c-caption">
        <img class="work-img radius-16" :src="subForm.avatarUrl" />
        <div class="flex-1 ml-lg">
          <div class="flex-y-center">
            <div class="f-title c-title text-bold">
              {{ subForm.nickName
              }}<span
                class="f-caption c-warning ml-sm"
                v-if="subForm.status === -1"
              >
                (用户已注销)
              </span>
            </div>
          </div>
          <div class="info-list flex-y-center mt-lg mb-sm">
            <div class="info-item">
              储值余额：<span class="c-title">¥{{ subForm.balance || 0 }}</span>
            </div>
            <div class="info-item">
              消费总金额：<span class="c-title"
                >¥{{ subForm.total_consumption || 0 }}</span
              >
              <lb-tool-tips :padding="0"
                >包含门店套餐购买和邀约订单以及下单向导的订单金额，不包含退款金额。</lb-tool-tips
              >
            </div>
            <div class="info-item" v-if="routesItem.auth.integral">
              累计获得积分：<span class="c-title"
                >¥{{
                  Number(
                    (
                      subForm.total_integral * 1 +
                      subForm.wait_integral * 1
                    ).toFixed(2)
                  ) || 0
                }}</span
              >
            </div>
            <div class="info-item" v-if="routesItem.auth.integral">
              剩余积分：<span class="c-title"
                >¥{{ subForm.integral || 0 }}</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="space-lg fill-body" v-if="subForm.id"></div>
    <div class="page-main">
      <el-tabs type="card" v-model="activeName" @tab-click="handleClick">
        <el-tab-pane label="用户信息" name="label">
          <div class="space-lg"></div>
          <lb-classify-title title="基本信息"></lb-classify-title>
          <div class="info-list flex-y-center mt-sm mb-sm">
            <div class="info-item">
              用户ID：<span class="c-title">{{ subForm.id }}</span>
            </div>
            <div class="info-item">
              手机号码：<span class="c-title">{{ subForm.phone || '-' }}</span>
            </div>
            <div class="info-item">
              支付宝账号：<span class="c-title">{{
                subForm.alipay_number || '-'
              }}</span>
            </div>
            <div class="info-item">
              客户来源：<span class="c-title"> {{ subForm.from_name }}</span>
            </div>
          </div>
          <div class="space-lg"></div>
          <div>
            <lb-classify-title title="用户概况"></lb-classify-title>
            <div>
              <div v-if="routesItem.auth.member">
                <div>
                  <span>用户等级:</span>
                  <span class="pl-lg" v-if="subForm.member_info">{{
                    subForm.member_info.title
                  }}</span>
                  <span v-else>无</span>
                </div>
                <div class="pt-md">
                  <span>到期时间:</span>
                  <span class="pl-lg" v-if="subForm.member_info">{{
                    subForm.member_info.end_time | handleTime
                  }}</span>
                  <span v-else>--</span>
                </div>
                <div class="pt-md pb-md">
                  <span>会员注册时间:</span>
                  <span class="pl-lg" v-if="subForm.member_info">{{
                    subForm.member_info.start_time | handleTime
                  }}</span>
                  <span v-else>--</span>
                </div>
              </div>
              <div>
                <span>创建时间:</span>
                <span class="pl-lg">{{
                  subForm.create_time | handleTime
                }}</span>
              </div>
            </div>
            <div class="c-title text-bold pb-md pt-md">标签：</div>
            <div class="flex-warp pb-lg">
              <div v-for="(item, index) in labelForm" :key="index">
                <el-tag
                  size="small"
                  @close="toDelLabel(index)"
                  :closable="pagePermission.includes('deleteTag')"
                  class="mr-md mt-sm mb-sm"
                  v-show="is_show ? index < labelForm.length : index < 9"
                  >{{ item.title }}</el-tag
                >
              </div>

              <div
                @click="toShowLabel"
                class="flex-center f-paragraph c-link cursor-pointer"
                v-if="labelForm.length > 9"
              >
                {{ is_show ? '收起更多' : '展开更多' }}
                <i
                  class="iconfont"
                  style="font-size: 10px"
                  :class="[{ iconshang: is_show }, { iconxia: !is_show }]"
                ></i>
              </div>
              <div v-if="labelForm.length === 0">
                <div class="f-caption c-caption">暂无数据</div>
                <div style="height: 50px"></div>
              </div>
            </div>
          </div>
        </el-tab-pane>
        <el-tab-pane
          label="余额变动明细"
          name="record"
          v-if="pagePermission.includes('viewBalance')"
        >
          <el-table
            v-loading="loading.record"
            :data="tableData.record"
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
            <el-table-column prop="text" label="备注">
              <template slot-scope="scope">
                <el-popover placement="top-start" width="350" trigger="hover">
                  <div class="f-caption c-title" slot>
                    <div class="c-caption pb-sm">备注：</div>
                    <div
                      style="max-height: 80vh; overflow: auto"
                      v-html="scope.row.text"
                    ></div>
                  </div>
                  <div
                    class="ellipsis-2"
                    v-html="scope.row.text"
                    slot="reference"
                  ></div>
                </el-popover>
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
            :page="searchForm.record.page"
            :pageSize="searchForm.record.limit"
            :total="total.record"
            @handleSizeChange="handleSizeChange($event, 'record')"
            @handleCurrentChange="handleCurrentChange($event, 'record')"
          >
          </lb-page>
        </el-tab-pane>
        <el-tab-pane
          label="积分明细"
          name="integral"
          v-if="
            pagePermission.includes('viewIntegral') && routesItem.auth.integral
          "
        >
          <el-table
            v-loading="loading.integral"
            :data="tableData.integral"
            :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
            style="width: 100%"
          >
            <el-table-column prop="id" label="ID" width="120"></el-table-column>
            <el-table-column prop="" label="明细说明" min-width="200">
              <template slot-scope="scope">
                <span>
                  {{ integralTypeText[scope.row.type]
                  }}<span v-if="scope.row.add && scope.row.type != 4"
                    >消费¥{{ scope.row.order_info.pay_price }}</span
                  >
                  <span> ，{{ scope.row.add ? '获得积分' : '消费积分' }} </span>
                  <span
                    :class="[
                      { 'c-link': scope.row.add },
                      { 'c-warning': !scope.row.add }
                    ]"
                    >{{ scope.row.add ? '+' : '-' }}{{ scope.row.change }}</span
                  >
                </span>
                ，剩余积分<span class="ml-sm c-success">{{
                  scope.row.after
                }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态" width="120">
              <template slot-scope="scope">
                <el-tag :type="statusText[scope.row.status].type">{{
                  statusText[scope.row.status].title
                }}</el-tag>
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
            :page="searchForm.integral.page"
            :pageSize="searchForm.integral.limit"
            :total="total.integral"
            @handleSizeChange="handleSizeChange($event, 'integral')"
            @handleCurrentChange="handleCurrentChange($event, 'integral')"
          >
          </lb-page>
        </el-tab-pane>
      </el-tabs>
    </div>

    <!-- 修改所在地区 -->
    <el-dialog
      :title="$t('action.updateUserArea')"
      :visible.sync="showDialog.editaddr"
      width="600px"
      center
      class="dialog-form"
    >
      <el-form
        @submit.native.prevent
        :model="editaddrForm"
        ref="editaddrForm"
        :rules="editaddrFormRules"
        label-width="140px"
        class="dialog-form"
      >
        <el-form-item label="所在地区" prop="data">
          <el-cascader
            size="large"
            :options="areaOptions"
            v-model="editaddrForm.data"
            @change="handleChange"
            placeholder="请选择省市区"
            :props="{ checkStrictly: true }"
          ></el-cascader>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog.editaddr = false">{{
          $t('action.cancel')
        }}</el-button>
        <el-button
          type="primary"
          @click="submitFormInfo('editaddr')"
          v-preventReClick
          >{{ $t('action.comfirm') }}</el-button
        >
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { regionData, CodeToText, TextToCode } from 'element-china-area-data'
import moment from 'moment'
export default {
  data () {
    let checkUserArea = (rule, value, callback) => {
      let { area = '' } = this.editaddrForm
      if (value.length === 0 || !area) {
        callback(new Error(value.length === 0 ? `请选择${rule.text}` : `请选择${rule.text}，必须精确到区县`))
      } else {
        callback()
      }
    }
    return {
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      pagePermission: [],
      areaOptions: regionData,
      CodeToText,
      TextToCode,
      activeName: 'label',
      carType: {
        0: '公交/地铁',
        1: '出租车'
      },
      couponStatusType: {
        1: '待使用',
        2: '已使用',
        3: '已过期'
      },
      typeText: { 1: '充值', 2: '消费', 3: '消费退款', 4: '消费', 5: '扣款', 6: '退款' },
      sourceTypeList: [{
        title: `全部`,
        id: -1
      }, {
        title: `公众号搜索`,
        id: 0,
        tips: `用户通过公众号名称直接搜索关注进入系统的`
      }, {
        title: `${this.$t('action.resellerName')}邀请粉丝`,
        id: 2,
        auth: 'reseller',
        agent: 'reseller_auth',
        tips: `用户进入系统初始方式是通过扫描新用户粉丝海报码或点击${this.$t('action.resellerName')}分享链接进入的`
      }, {
        title: `${this.$t('action.resellerName')}邀请下级二维码`,
        id: 10,
        auth: 'reseller',
        agent: 'reseller_auth',
        tips: `用户进入系统初始方式是通过扫描${this.$t('action.resellerName')}的邀请下级${this.$t('action.resellerName')}海报码进入的`
      }, {
        title: `业务员邀请${this.$t('action.channelName')}二维码`,
        id: 8,
        auth: 'salesman',
        agent: 'salesman_auth',
        tips: `用户进入系统初始方式是通过扫描某业务员出示的邀请${this.$t('action.channelName')}海报码进入系统的`
      }, {
        title: `原生渠道码`,
        id: 9,
        auth: 'channel',
        agent: 'channel_auth',
        tips: `用户进入系统的初始方式是通过扫描某个${this.$t('action.channelName')}的默认码进入系统的`
      }, {
        title: `渠道码`,
        id: 1,
        auth: 'channel',
        agent: 'channel_auth',
        tips: '用户进入系统的初始方式是通过扫描某个渠道码进入系统的'
      }, {
        title: `${this.$t('action.brokerName')}邀请${this.$t('action.attendantName')}二维码`,
        id: 3,
        auth: 'coachbroker',
        agent: 'partner_auth',
        tips: `用户进入系统初始方式是通过扫描了某个${this.$t('action.brokerName')}的邀请${this.$t('action.attendantName')}入驻海报码进入的`
      }, {
        title: `${this.$t('action.attendantName')}邀请用户充值`,
        id: 7,
        agent: 'agent_coach_auth',
        tips: `用户进入系统初始方式是通过扫描某${this.$t('action.attendantName')}出示的邀请充值海报码进入系统的`
      }, {
        title: `${this.$t('action.attendantName')}邀请用户购买折扣卡`,
        id: 14,
        auth: 'balancediscount',
        agent: 'agent_coach_auth',
        tips: `用户进入系统初始方式是通过扫描某${this.$t('action.attendantName')}出示的邀请购买折扣卡海报码进入系统的`
      }, {
        title: `${this.$t('action.attendantName')}邀请用户购买会员卡`,
        id: 13,
        auth: 'memberdiscount',
        agent: 'agent_coach_auth',
        tips: `用户进入系统初始方式是通过扫描某${this.$t('action.attendantName')}出示的邀请购买会员卡海报码进入系统的`
      }, {
        title: `${this.$t('action.agentName')}邀请${this.$t('action.resellerName')}二维码`,
        id: 11,
        auth: 'reseller',
        agent: 'reseller_auth',
        tips: `用户进入系统初始方式是通过扫描了${this.$t('action.agentName')}的邀请${this.$t('action.resellerName')}海报码进入的`
      }, {
        title: `${this.$t('action.agentName')}邀请业务员二维码`,
        id: 5,
        auth: 'salesman',
        agent: 'salesman_auth',
        tips: `用户进入系统初始方式是通过扫描某${this.$t('action.agentName')}出示的邀请业务员海报码进入的`
      }, {
        title: `${this.$t('action.agentName')}邀请${this.$t('action.channelName')}二维码`,
        id: 6,
        auth: 'channel',
        agent: 'channel_auth',
        tips: `用户进入系统初始方式是通过扫描某${this.$t('action.agentName')}出示的邀请${this.$t('action.channelName')}海报码进入的`
      }, {
        title: `${this.$t('action.agentName')}邀请${this.$t('action.attendantName')}二维码`,
        id: 4,
        agent: 'agent_coach_auth',
        tips: `用户进入系统初始方式是通过扫描了某${this.$t('action.agentName')}的邀请${this.$t('action.attendantName')}海报码进入的`
      }, {
        title: `${this.$t('action.agentName')}邀请${this.$t('action.agentName')}二维码`,
        id: 12,
        agent: 'sub_agent_auth',
        tips: `用户进入系统初始方式是通过扫描了${this.$t('action.agentName')}的邀请${this.$t('action.agentName')}海报码进入的`
      }],
      integralTypeText: { 1: '预约订单下单', 2: '邀约订单下单', 3: '套餐订单抵扣', 4: '套餐订单退款' },
      subForm: {},
      labelForm: [],
      is_show: false,
      loading: { label: false, order: false, record: false, growthrecord: false, coupon: false, discount: false, coach: false },
      searchForm: {
        label: {
          page: 1,
          limit: 10,
          user_id: ''
        },
        record: {
          page: 1,
          limit: 10,
          user_id: ''
        },
        integral: {
          page: 1,
          limit: 10,
          user_id: ''
        }
      },
      tableData: { label: [], order: [], record: [], growthrecord: [], coupon: [], discount: [], coach: [], integral: [] },
      total: { label: 0, order: 0, record: 0, growthrecord: 0, coupon: 0, discount: 0, coach: 0, integral: 0 },
      currentRow: {},
      showDialog: { editaddr: false },
      editaddrForm: {},
      editaddrFormRules: {
        data: { required: false, validator: checkUserArea, text: '所在地区', trigger: 'blur' }
      },
      statusText: {
        1: { title: '冻结中', type: 'danger' },
        2: { title: '已到账', type: '' }
      }
    }
  },
  async created () {
    this.routesItem.routes.map(item => {
      if (item.path === '/custom') {
        item.children.map(aitem => {
          if (aitem.name === 'CustomList') {
            this.pagePermission = aitem.meta.pagePermission[0].auth
          }
        })
      }
    })
    let { id = 0 } = this.$route.query
    this.subForm.id = id
    await this.getDetail(id)
    this.getTableDataList(1, '')
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    /**
     * @name: 详情
     * @param {*} id
     */
    async getDetail (id) {
      let { code, data } = await this.$api.custom.getUserInfo({ id })
      if (code !== 200) return

      let key = ['total_consumption', 'balance', 'integral', 'total_integral']
      key.map(item => {
        data[item] = Number(data[item].toFixed(2))
      })
      this.labelForm = data.user_label
      this.subForm = data
    },
    handleClick (e) {
      let { name } = e
      this.activeName = name
      if (name === 'label') {
        return
      }
      this.getTableDataList(1, name)
    },
    resetForm (form) {
      let name = `${form}Form`
      this.$refs[name].resetFields()
      this.getTableDataList(1, form)
    },
    handleSizeChange (val, key) {
      this.searchForm[key].limit = val
      this.handleCurrentChange(1, key)
    },
    handleCurrentChange (val, key) {
      this.searchForm[key].page = val
      this.getTableDataList('', key)
    },
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = flag
      this.loading[key] = true
      this.tableData[key] = []
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))
      let { id } = this.subForm
      searchForm.user_id = id

      let methodArr = {
        label: { methodKey: 'custom', methodModel: 'coachCommentUserData' },
        record: { methodKey: 'custom', methodModel: 'payWater' },
        integral: { methodKey: 'custom', methodModel: 'integralList' }
      }
      let { methodKey, methodModel } = methodArr[key]
      let { code, data } = await this.$api[methodKey][methodModel](searchForm)
      this.loading[key] = false
      if (code !== 200) return
      if (key === 'record') {
        data.data.map(item => {
          item.price = Math.abs(item.price)
          item.text = item.text ? item.text.replace(/\n/g, '<br />') : ''
        })
      }
      // if (key === 'label') {
      //   if (searchForm.page === 1) {
      //     this.labelForm = data.user_label
      //   }
      //   data.list.data.map(item => {
      //     item.text = item.text ? item.text.replace(/\n/g, '<br>') : '暂无内容'
      //   })
      // }
      this.tableData[key] = key === 'label' ? data.list.data : data.data
      this.total[key] = key === 'label' ? data.list.total : data.total
    },
    toShowLabel () {
      this.is_show = !this.is_show
    },
    toShowDialog (key) {
      if (key === 'editaddr') {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        let { id, province = '', city = '', area = '' } = subForm
        let data = []
        if (province) {
          if (province.includes('特别行政区')) {
            city = area
            area = ''
          }
          data.push(this.TextToCode[province].code)
        }
        if (province && city) {
          city = province === city ? area.includes('县') ? '县' : '市辖区' : city === area ? province === '新疆维吾尔自治区' ? '自治区直辖县级行政区划' : '省直辖县级行政区划' : city
          data.push(this.TextToCode[province][city].code)
        }
        if (province && city && area) {
          data.push(this.TextToCode[province][city][area].code)
        }
        this.editaddrForm = Object.assign({}, this.editaddrForm, { id, data, province, city: subForm.city, area: subForm.area })
      }
      this.showDialog[key] = !this.showDialog[key]
    },
    handleChange (e) {
      let province = e && e.length > 0 ? this.CodeToText[e[0]] : ''
      let city = e && e.length > 1 ? this.CodeToText[e[1]] : ''
      let area = e && e.length > 2 ? this.CodeToText[e[2]] : ''
      if (city.includes('市辖区') || (province === '重庆市' && city.includes('县'))) {
        city = province
      }
      if (province.includes('特别行政区')) {
        area = city
        city = province
      }
      if (city.includes('直辖县级行政区划')) {
        city = area
      }
      this.editaddrForm.province = province
      this.editaddrForm.city = city
      this.editaddrForm.area = area
    },
    async toDelLabel (index) {
      let { id: uid } = this.subForm
      let { label_id: id } = this.labelForm[index]
      let { code } = await this.$api.custom.delUserLabel({ user_id: uid, label_id: id })
      if (code !== 200) return
      this.$message.success(this.$t('tips.successDel'))
      this.labelForm.splice(index, 1)
    },
    confirmDel (id) {
      this.$confirm(`你确认要${this.$t('action.coachEditDelService')}吗？`, this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, 'service')
      }).catch(() => {

      })
    },
    async updateItem (id, key) {
      let { id: cid } = this.subForm
      this.$api.technician.delCoachService({ id, coach_id: cid }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successOper'))
          this.searchForm[key].page = this.searchForm[key].page < Math.ceil((this.total[key] - 1) / this.searchForm[key].limit) ? this.searchForm[key].page : Math.ceil((this.total[key] - 1) / this.searchForm[key].limit)
          this.getTableDataList('', key)
        }
      })
    },
    async submitFormInfo (key) {
      let validate = true
      this.$refs[`${key}Form`].validate(valid => {
        if (!valid) validate = false
      })
      if (!validate) return
      let subForm = JSON.parse(JSON.stringify(this[`${key}Form`]))
      if (key === 'editaddr') {
        delete subForm.data
      }
      this.$api.custom.updateUserAddress(subForm).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successSub'))
          this.showDialog[key] = false
          this.getDetail(subForm.id)
        }
      })
    }
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : type === 3 ? moment(val * 1000).format('YYYY.MM.DD') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    },
    handleTitle (val, list, text) {
      let arr = list.filter(item => {
        return item.id === val
      })
      let title = arr && arr.length > 0 ? arr[0].title : ''
      return val * 1 > 0 && text && text !== null ? `${text}-${title}` : title
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-technician-edit {
  width: 100%;
  .work-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
  }
  .info-list {
    width: calc(100% - 100px);
    .info-item {
      width: 25%;
      min-width: 200px;
    }
  }
  .label-eva-item {
    .avatar {
      width: 40px;
      height: 40px;
    }
    .name {
      height: 40px;
    }
  }
  .dialog-form {
    .el-cascader {
      width: 300px;
    }
  }
}
</style>
