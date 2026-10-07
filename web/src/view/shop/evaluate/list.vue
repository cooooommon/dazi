<!--
 * @Description: 评价标签
 * @Author: xiao li
 * @Date: 2021-07-04 13:00:37
 * @LastEditTime: 2024-04-18 14:48:57
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-shop-refund">
    <top-nav />
    <div class="page-main">
      <lb-button
        size="medium"
        type="primary"
        icon="el-icon-plus"
        @click="toShowDialog('add')"
        v-hasPermi="`${$route.name}-add`"
        >{{ $t('menu.ShopEvaluateAdd') }}</lb-button
      >
      <div class="space-lg"></div>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm.list"
          ref="listForm"
        >
          <el-form-item
            :label="`${$t('action.attendantName')}名称`"
            prop="coach_name"
          >
            <el-input
              v-model="searchForm.list.coach_name"
              :placeholder="`请输入${$t('action.attendantName')}名称`"
            ></el-input>
          </el-form-item>
          <el-form-item label="服务名称" prop="goods_name">
            <el-input
              v-model="searchForm.list.goods_name"
              placeholder="请输入服务名称"
            ></el-input>
          </el-form-item>
          <!-- <el-form-item label="星级" prop="star">
            <el-select
              @change="getTableDataList(1, 'list')"
              v-model="searchForm.list.star"
              placeholder="请选择"
            >
              <el-option
                v-for="item in statusOptions"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item> -->
          <el-form-item>
            <lb-button
              size="medium"
              type="primary"
              icon="el-icon-search"
              style="margin-right: 5px"
              @click="getTableDataList(1, 'list')"
              >{{ $t('action.search') }}</lb-button
            >
            <lb-button
              size="medium"
              icon="el-icon-refresh-left"
              style="margin-right: 5px"
              @click="resetForm('list')"
              >{{ $t('action.reset') }}</lb-button
            >
          </el-form-item>
        </el-form>
      </el-row>
      <el-table
        v-loading="loading.list"
        :data="tableData.list"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column
          prop="id"
          label="ID"
          width="80"
          fixed
        ></el-table-column>
        <el-table-column
          prop="goods_info_text"
          width="300"
          label="服务项目信息"
        >
          <template slot-scope="scope">
            <div v-if="scope.row.order_id">
              <div
                class="pb-sm"
                v-for="(citem, cindex) in scope.row.order_goods"
                :key="cindex"
              >
                <div class="flex-warp pt-md">
                  <lb-image class="avatar radius-5" :src="citem.goods_cover" />
                  <div
                    class="flex-1 f-caption c-caption ml-md"
                    style="width: 210px"
                  >
                    <div class="flex-between">
                      <div
                        class="f-paragraph c-title ellipsis"
                        :class="[{ 'max-300': citem.refund_num > 0 }]"
                        style="line-height: 1.2; margin-bottom: 4px"
                      >
                        {{ citem.goods_name }}
                      </div>
                      <div
                        class="f-caption c-warning"
                        v-if="citem.refund_num > 0"
                      >
                        已退x{{ citem.refund_num }}
                      </div>
                    </div>
                    <div class="flex-between f-caption mt-md">
                      <div class="c-warning ml-md">¥{{ citem.price }}</div>
                      <div>x{{ citem.goods_num || 1 }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else>-</div>
          </template>
        </el-table-column>
        <el-table-column prop="user_id" label="订单编号" width="160">
          <template slot-scope="scope">
            {{ scope.row.order_id ? scope.row.order_code : '' }}
          </template>
        </el-table-column>
        <el-table-column prop="user_id" label="客户ID">
          <template slot-scope="scope">
            {{ scope.row.order_id ? scope.row.user_id : '' }}
          </template>
        </el-table-column>
        <el-table-column prop="nickName" label="客户昵称">
          <template slot-scope="scope">
            {{ scope.row.order_id ? scope.row.nickName : '' }}
          </template>
        </el-table-column>
        <el-table-column
          prop="coach_name"
          :label="`${$t('action.attendantName')}`"
        ></el-table-column>
        <el-table-column
          prop="star"
          :label="`${$t('action.attendantName')}颜值`"
          min-width="160"
        >
          <template slot-scope="scope">
            <div class="flex-warp">
              <i
                class="iconfont iconyduixingxingkongxin c-caption mr-sm"
                :class="[
                  { 'iconyduixingxingshixin c-danger': index < scope.row.star }
                ]"
                v-for="(item, index) in 5"
                :key="index"
              ></i>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="attitude_star" label="服务态度" min-width="160">
          <template slot-scope="scope">
            <div class="flex-warp">
              <i
                class="iconfont iconyduixingxingkongxin c-caption mr-sm"
                :class="[
                  {
                    'iconyduixingxingshixin c-danger':
                      index < scope.row.attitude_star
                  }
                ]"
                v-for="(item, index) in 5"
                :key="index"
              ></i>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="speed_star" label="响应速度" min-width="160">
          <template slot-scope="scope">
            <div class="flex-warp">
              <i
                class="iconfont iconyduixingxingkongxin c-caption mr-sm"
                :class="[
                  {
                    'iconyduixingxingshixin c-danger':
                      index < scope.row.speed_star
                  }
                ]"
                v-for="(item, index) in 5"
                :key="index"
              ></i>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="text" label="评价内容" min-width="400">
          <template slot-scope="scope">
            <div class="pre-wrap">{{ scope.row.text }}</div>
          </template>
        </el-table-column>
        <!-- <el-table-column prop="lable_text" label="评价标签" min-width="300">
          <template slot-scope="scope">
            <el-tag
              class="mr-md mb-md"
              v-for="(item, index) in scope.row.lable_text"
              :key="index"
              >{{ item }}</el-tag
            >
          </template>
        </el-table-column> -->
        <!-- <el-table-column prop="create_time" min-width="120" label="评价时间">
          <template slot-scope="scope">
            <div>{{ scope.row.create_time | handleTime(1) }}</div>
            <div>{{ scope.row.create_time | handleTime(2) }}</div>
          </template>
        </el-table-column> -->
        <el-table-column width="90px" label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id)"
                v-hasPermi="`${$route.name}-delete`"
                >{{ $t('action.delete') }}</lb-button
              >
            </div>
          </template>
        </el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.list.page"
        :pageSize="searchForm.list.limit"
        :total="total.list"
        @handleSizeChange="handleSizeChange($event, 'list')"
        @handleCurrentChange="handleCurrentChange($event, 'list')"
      >
      </lb-page>

      <el-dialog
        :title="$t('menu.ShopEvaluateAdd')"
        :visible.sync="showDialog.add"
        width="800px"
        center
      >
        <el-form
          class="dialog-form"
          :model="subForm"
          ref="subForm"
          :rules="subFormRules"
          label-width="100px"
        >
          <el-form-item label="评价内容" prop="text">
            <el-input
              type="textarea"
              :rows="10"
              v-model="subForm.text"
              maxlength="300"
              show-word-limit
              resize="none"
              placeholder="请输入评价内容"
            ></el-input>
          </el-form-item>
          <el-form-item
            :label="`${$t('action.attendantName')}颜值`"
            prop="star"
          >
            <div class="">
              <div class="flex-warp">
                <div v-for="(item, index) in 5" :key="index">
                  <i
                    @click="checkStar('star', index * 1 + 1)"
                    class="star-icon text-bold iconfont c-danger mr-sm"
                    :class="[
                      { iconyduixingxingkongxin: subForm.star < index * 1 + 1 },
                      { iconyduixingxingshixin: subForm.star >= index * 1 + 1 }
                    ]"
                  ></i>
                </div>
              </div>
              <!-- <div class="flex-1 f-paragraph c-caption pl-lg">
                {{ subForm.star ? startObj[subForm.star - 1] : '请选择星级' }}
              </div> -->
            </div>
          </el-form-item>
          <el-form-item label="服务态度" prop="attitude_star">
            <div class="">
              <div class="flex-warp">
                <div v-for="(item, index) in 5" :key="index">
                  <i
                    @click="checkStar('attitude_star', index * 1 + 1)"
                    class="star-icon text-bold iconfont c-danger mr-sm"
                    :class="[
                      {
                        iconyduixingxingkongxin:
                          subForm.attitude_star < index * 1 + 1
                      },
                      {
                        iconyduixingxingshixin:
                          subForm.attitude_star >= index * 1 + 1
                      }
                    ]"
                  ></i>
                </div>
              </div>
              <!-- <div class="flex-1 f-paragraph c-caption pl-lg">
                {{
                  subForm.attitude_star
                    ? startObj[subForm.attitude_star - 1]
                    : '请选择星级'
                }}
              </div> -->
            </div>
          </el-form-item>
          <el-form-item label="响应速度" prop="speed_star">
            <div class="">
              <div class="flex-warp">
                <div v-for="(item, index) in 5" :key="index">
                  <i
                    @click="checkStar('speed_star', index * 1 + 1)"
                    class="star-icon text-bold iconfont c-danger mr-sm"
                    :class="[
                      {
                        iconyduixingxingkongxin:
                          subForm.speed_star < index * 1 + 1
                      },
                      {
                        iconyduixingxingshixin:
                          subForm.speed_star >= index * 1 + 1
                      }
                    ]"
                  ></i>
                </div>
              </div>
              <!-- <div class="flex-1 f-paragraph c-caption pl-lg">
                {{
                  subForm.speed_star
                    ? startObj[subForm.speed_star - 1]
                    : '请选择星级'
                }}
              </div> -->
            </div>
          </el-form-item>
          <!-- <el-form-item label="评价标签" prop="label">
            <div class="flex-warp f-caption c-title">
              <div
                @click="toChangeItem(index)"
                class="fill-body flex-center mt-md mr-md pl-lg pr-lg cursor-pointer radius"
                :class="[{ 'c-base': subForm.label.includes(item.id) }]"
                style="height: 30px"
                :style="{
                  background: subForm.label.includes(item.id) ? '#409EFF' : ''
                }"
                v-for="(item, index) in base_label"
                :key="index"
              >
                {{ item.title }}
              </div>
              <div
                class="c-link cursor-pointer"
                @click="$router.push(`/shop/evaluate/label`)"
                v-if="base_label.length == 0"
              >
                暂无评价标签，请前去添加
              </div>
            </div>
          </el-form-item> -->
          <el-form-item
            :label="`选择${$t('action.attendantName')}`"
            prop="coach_id"
          >
            <el-tag
              :type="subForm.coach_id ? 'primary' : 'danger'"
              @click="toShowDialog('technician')"
              >{{
                subForm.coach_id
                  ? subForm.coach_name
                  : '请选择' + $t('action.attendantName')
              }}</el-tag
            >
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.add = false">取 消</el-button>
          <el-button type="primary" @click="submitFormInfo" v-preventReClick
            >确 定</el-button
          >
        </span>
      </el-dialog>

      <el-dialog
        :title="`关联${$t('action.attendantName')}`"
        :visible.sync="showDialog.technician"
        width="800px"
        center
      >
        <el-form
          :inline="true"
          :model="searchForm.technician"
          ref="technicianForm"
          label-width="70px"
        >
          <el-form-item label="输入查询" prop="name">
            <el-input
              v-model="searchForm.technician.name"
              :placeholder="`请输入${$t('action.attendantName')}昵称/手机号`"
              style="width: 250px"
            ></el-input>
          </el-form-item>
          <el-form-item>
            <lb-button
              size="medium"
              type="primary"
              icon="el-icon-search"
              style="margin-right: 5px"
              @click="getTableDataList(1, 'technician')"
              >{{ $t('action.search') }}</lb-button
            >
            <lb-button
              size="medium"
              icon="el-icon-refresh-left"
              style="margin-right: 5px"
              @click="resetForm('technician')"
              >{{ $t('action.reset') }}</lb-button
            >
          </el-form-item>
        </el-form>
        <el-table
          :data="tableData.technician"
          ref="singleTable"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
          highlight-current-row
          @current-change="handleTableChange"
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
              <lb-image :src="scope.row.work_img" />
            </template>
          </el-table-column>
          <el-table-column
            prop="coach_name"
            :label="`${$t('action.attendantName')}昵称`"
          ></el-table-column>
          <el-table-column prop="mobile" label="手机号"></el-table-column>
        </el-table>
        <lb-page
          :batch="false"
          :page="searchForm.technician.page"
          :pageSize="searchForm.technician.limit"
          :total="total.technician"
          @handleSizeChange="handleSizeChange($event, 'technician')"
          @handleCurrentChange="handleCurrentChange($event, 'technician')"
        >
        </lb-page>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.technician = false">取 消</el-button>
          <el-button type="primary" @click="handleDialogConfirm"
            >确 定</el-button
          >
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  data () {
    return {
      statusOptions: [{ label: '全部', value: 0 }, { label: '五星', value: 5 }, { label: '四星', value: 4 }, { label: '三星', value: 3 }, { label: '二星', value: 2 }, { label: '一星', value: 1 }],
      statusType: {
        1: '退款申请中',
        2: '同意退款',
        3: '拒绝退款'
      },
      loading: { list: false, technician: false },
      searchForm: {
        list: {
          page: 1,
          limit: 10,
          coach_name: '',
          goods_name: '',
          star: 0,
          order_id: 0
        },
        technician: {
          page: 1,
          limit: 10,
          status: 2,
          is_user: 1,
          name: ''
        }
      },
      tableData: { list: [], technician: [] },
      total: { list: 0, technician: 0 },
      showDialog: { add: false, technician: false },
      startObj: ['不满意', '一般', '满意', '很满意', '非常满意'],
      subForm: {
        star: 5,
        speed_star: 5,
        attitude_star: 5,
        text: '',
        coach_id: '',
        coach_name: ''
      },
      subFormRules: {
        star: { required: true, type: 'number', message: '请选择评价星级', trigger: 'blur' },
        speed_star: { required: true, type: 'number', message: '请选择评价星级', trigger: 'blur' },
        attitude_star: { required: true, type: 'number', message: '请选择评价星级', trigger: 'blur' },
        text: { required: true, validator: this.$reg.isNotNull, text: '评价内容', reg_type: 2, trigger: 'blur' },
        label: { required: true, type: 'array', message: '请选择评价标签', trigger: 'blur' },
        coach_id: { required: true, type: 'number', message: `请选择${this.$t('action.attendantName')}`, trigger: 'blur' }
      },
      currentRow: {}
    }
  },
  async created () {
    let { id = 0 } = this.$route.query
    this.searchForm.order_id = id
    await this.getBaseInfo()
    this.getTableDataList(1, 'list')
  },
  methods: {
    async getBaseInfo () {
      let { code, data } = await this.$api.shop.lableList()
      if (code !== 200) return
      data.map(item => {
        item.is_check = false
      })
      this.base_label = data
    },
    resetForm (form) {
      let name = `${form}Form`
      this.$refs[name].resetFields()
      this.searchForm.list.order_id = 0
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
      if (flag) this.searchForm[key].page = 1
      this.loading[key] = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))
      let methodArr = {
        list: { methodKey: 'shop', methodModel: 'commentList' },
        technician: { methodKey: 'technician', methodModel: 'coachList' }
      }
      let { methodKey, methodModel } = methodArr[key]
      let { code, data } = await this.$api[methodKey][methodModel](searchForm)
      this.loading[key] = false
      if (code !== 200) return
      this.tableData[key] = data.data
      this.total[key] = data.total
    },
    confirmDel (id) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, -1)
      }).catch(() => { })
    },
    async updateItem (id, status) {
      this.$api.shop.commentUpdate({ id, status }).then((res) => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.list.page = this.searchForm.list.page < Math.ceil((this.total.list - 1) / this.searchForm.list.limit) ? this.searchForm.list.page : Math.ceil((this.total.list - 1) / this.searchForm.list.limit)
            this.getTableDataList('', 'list')
          }
        }
      })
    },
    async toShowDialog (key, item = {
      star: 5,
      text: '',
      coach_id: '',
      coach_name: '',
      speed_star: 5,
      attitude_star: 5
    }) {
      if (key === 'add') {
        this.subForm = item
      } else {
        this.currentRow = {}
        this.searchForm.technician.name = ''
        await this.getTableDataList(1, key)
      }
      this.showDialog[key] = !this.showDialog[key]
    },
    checkStar (type, val) {
      this.subForm[type] = val
    },
    toChangeItem (index) {
      let {
        id
      } = this.base_label[index]
      let ids = JSON.parse(JSON.stringify(this.subForm.label))
      let ind = ids && ids.length > 0 ? ids.findIndex(item => {
        return item === id
      }) : -1
      if (ind !== -1) {
        ids.splice(ind, 1)
      } else {
        ids.push(id)
      }
      this.subForm.label = ids
    },
    handleTableChange (val) {
      this.currentRow = val
    },
    handleDialogConfirm () {
      if (this.currentRow === null || !this.currentRow.id) {
        this.$message.error(`请选择${this.$t('action.attendantName')}`)
        return
      }
      let { id, coach_name: name } = this.currentRow
      this.subForm.coach_id = id
      this.subForm.coach_name = name
      this.showDialog.technician = false
    },
    async submitFormInfo () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        delete subForm.coach_name
        let { code } = await this.$api.shop.addCommentV2(subForm)
        if (code !== 200) return
        this.$message.success(this.$t('tips.successSub'))
        this.showDialog.add = false
        this.getTableDataList('', 'list')
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
.lb-shop-refund {
  .page-main {
    width: 100%;

    .el-select,
    .el-input-number,
    .el-input {
      width: 200px;
    }
  }
}

.none {
  display: none;
}
</style>
<style media="print">
.none {
  display: block;
}
</style>
