<!--
 * @Description: 新增套餐
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-11-29 15:36:48
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-system-banner-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="130px"
      >
        <lb-classify-title title="基本信息"></lb-classify-title>
        <el-form-item prop="cover" label="套餐封面">
          <lb-cover
            :fileList="subForm.cover"
            @selectedFiles="getCover($event, 'cover')"
            @moveFiles="moveFiles($event, 'cover')"
          ></lb-cover>
          <lb-tool-tips>图片比例: 340*234</lb-tool-tips>
        </el-form-item>
        <el-form-item label="套餐名称" prop="name">
          <el-input
            v-model="subForm.name"
            maxlength="10"
            show-word-limit
            placeholder="请输入套餐名称"
          ></el-input>
        </el-form-item>
        <el-form-item label="副标题" prop="sub_name">
          <el-input
            v-model="subForm.sub_name"
            maxlength="12"
            show-word-limit
            placeholder="请输入副标题"
          ></el-input>
        </el-form-item>
        <el-form-item label="现价" prop="price">
          <el-input placeholder="请输入现价" v-model="subForm.price">
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="划线价" prop="init_price">
          <el-input placeholder="请输入划线价" v-model="subForm.init_price">
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item
          label="积分抵扣"
          prop="is_integral"
          v-if="routesItem.auth.integral"
        >
          <el-radio-group v-model="subForm.is_integral">
            <el-radio :label="1">开启</el-radio>
            <el-radio :label="0">关闭</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >会员有积分的情况下，下单该套餐可使用积分去抵扣部分金额或者全部金额</lb-tool-tips
          >
          <div class="pt-md" v-if="subForm.is_integral == 1">
            <el-input-number
              :min="1"
              :precision="0"
              :controls="false"
              v-model.number="subForm.integral"
              class="mini mr-sm"
              placeholder="请输入"
              style="width: 120px"
            ></el-input-number>
            积分可以抵扣
            <el-input-number
              :min="0.1"
              :precision="1"
              :controls="false"
              v-model.number="subForm.integral_to_money"
              class="mini ml-sm mr-sm"
              placeholder="请输入"
              style="width: 120px"
            ></el-input-number>
            元
          </div>
        </el-form-item>
        <el-form-item label="虚拟销量" prop="sale">
          <el-input-number
            class="lb-input-number"
            :min="0"
            :precision="0"
            :controls="false"
            v-model="subForm.sale"
            placeholder="请输入虚拟销量"
          ></el-input-number>
        </el-form-item>
        <el-form-item label="详情图" prop="imgs">
          <lb-cover
            :fileList="subForm.imgs"
            fileType="image"
            type="more"
            @selectedFiles="getBannerList($event, 'imgs')"
            @moveFiles="moveFiles($event, 'imgs')"
            :fileSize="9"
          ></lb-cover>
          <lb-tool-tips>图片比例: 750*527</lb-tool-tips>
        </el-form-item>
        <lb-classify-title title="团购详情"></lb-classify-title>
        <el-form-item label="创建套餐规格" prop="sku">
          <lb-button
            size="small"
            type="primary"
            icon="el-icon-plus"
            plain
            @click="changeShowDialog('specs')"
            >添加规格</lb-button
          >
          <div v-if="subForm.sku.length > 0">
            <div v-for="(item, index) in subForm.sku" :key="index">
              <div class="space-lg"></div>
              <div>
                <span>规格名：{{ item.name }}</span>
                <lb-button
                  size="mini"
                  type="primary"
                  icon="el-icon-edit"
                  plain
                  class="ml-lg"
                  @click="changeShowDialog('specs', { name: item.name, index })"
                  >编辑</lb-button
                >
                <lb-button
                  size="mini"
                  type="danger"
                  icon="el-icon-delete"
                  plain
                  class="ml-lg"
                  @click="delSku(index)"
                  >删除</lb-button
                >
              </div>
              <div class="space-lg"></div>
              <el-table
                :data="item.price"
                :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
                tooltip-effect="dark"
                style="width: 100%"
              >
                <el-table-column prop="name" label="规格值">
                  <template slot-scope="scope">
                    <el-input
                      placeholder="请输入内容"
                      v-model="item.price[scope.$index].name"
                      maxlength="25"
                      style="width: auto"
                    >
                    </el-input>
                  </template>
                </el-table-column>
                <el-table-column prop="name" label="数量">
                  <template slot-scope="scope">
                    <el-input-number
                      placeholder="请输入内容"
                      class="lb-input-number"
                      :min="0"
                      :precision="0"
                      :controls="false"
                      v-model="item.price[scope.$index].num"
                      style="width: auto"
                    >
                    </el-input-number>
                  </template>
                </el-table-column>
                <el-table-column prop="name" label="价格">
                  <template slot-scope="scope">
                    <el-input-number
                      placeholder="请输入价格"
                      class="lb-input-number"
                      :min="0"
                      :precision="1"
                      :controls="false"
                      v-model="item.price[scope.$index].price"
                      style="width: auto"
                    >
                    </el-input-number>
                  </template>
                </el-table-column>
                <el-table-column label="操作" min-width="60" fixed="right">
                  <template slot-scope="scope">
                    <div class="table-operate">
                      <el-button
                        type="danger"
                        icon="el-icon-delete"
                        circle
                        @click="delItemSku(index, scope.$index)"
                        size="mini"
                        class="mr-md"
                        v-if="
                          (scope.$index == 0 && item.price.length > 1) ||
                          scope.$index != 0
                        "
                      ></el-button>
                      <el-button
                        type="primary"
                        icon="el-icon-plus"
                        circle
                        @click="addItemSku(index)"
                        size="mini"
                        v-if="
                          scope.$index == item.price.length - 1 ||
                          (scope.$index == 0 && item.price.length == 1)
                        "
                      ></el-button>
                    </div>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </div>
        </el-form-item>
        <el-form-item label="图文详情" prop="introduce">
          <lb-ueditor v-model="subForm.introduce" :destroy="true"></lb-ueditor>
        </el-form-item>
        <lb-classify-title title="购买须知"></lb-classify-title>
        <el-form-item label="所属门店" prop="store_id">
          <el-tag
            class="cursor-pointer"
            :type="have_user_id ? 'info' : 'primary'"
            @click="toShowDialog('store')"
            >{{ subForm.store_id ? subForm.store_name : '选择门店' }}</el-tag
          >
        </el-form-item>
        <el-form-item label="有效期" prop="term_type">
          <el-radio-group v-model="subForm.term_type">
            <el-radio :label="1">指定日期</el-radio>
            <el-radio :label="2">有效天数</el-radio>
          </el-radio-group>
          <div v-if="subForm.term_type == 1">
            <el-date-picker
              @change="getTermTime"
              v-model="subForm.term_start_end"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              value-format="timestamp"
              :picker-options="pickerOptions"
              :default-time="['00:00:00', '23:59:59']"
            ></el-date-picker>
            <lb-tool-tips>下单的客户必须在该时间段内使用</lb-tool-tips>
          </div>
          <div v-if="subForm.term_type == 2">
            自购买当日起
            <el-input-number
              class="lb-input-number"
              :min="1"
              :precision="0"
              :controls="false"
              v-model="subForm.days"
              placeholder="请输入"
              style="width: 150px"
            ></el-input-number>
            天内可用
            <lb-tool-tips
              >有效期按自然天计算。
              <p>
                举例：如设置套餐当日起30天内可用，用户在5月18日14:00时领取优惠券，<br />则该团购套餐的可用时间为5月18日的14:00:00至6月18日的14:00
              </p>
              <p>注意：时间按自然天来算，不是月</p>
            </lb-tool-tips>
          </div>
        </el-form-item>
        <el-form-item label="使用时间" prop="use_trade_week">
          <el-radio-group v-model="subForm.use_type">
            <el-radio :label="1">与门店营业时间一致</el-radio>
            <el-radio :label="2">自定义时间</el-radio>
          </el-radio-group>
          <lb-tool-tips>自定义时间只能选择门店营业时间内的时间</lb-tool-tips>
          <div v-if="subForm.use_type == 2">
            <div class="trade-week">
              <el-slider
                @change="changeTradeWeek"
                v-model="subForm.use_trade_week"
                range
                show-stops
                :marks="weekMarks"
                :max="subForm.use_trade_week_max"
                :min="subForm.use_trade_week_min"
                :show-tooltip="false"
                :disabled="isSlider"
              >
              </el-slider>
            </div>
            <div class="flex-y-center">
              <el-time-select
                placeholder="开始时间"
                v-model="subForm.use_start_time"
                :picker-options="{
                  start: '00:00',
                  step: '00:01',
                  end: '23:59'
                }"
                style="width: 150px"
              ></el-time-select>
              <span class="pl-md pr-md">至</span>
              <el-time-select
                placeholder="结束时间"
                v-model="subForm.use_end_time"
                :picker-options="{
                  start: '00:00',
                  step: '00:01',
                  end: '23:59'
                }"
                style="width: 150px"
              ></el-time-select>
              <lb-button
                icon="el-icon-refresh-left"
                style="margin-left: 10px"
                @click="resetTime"
                type="warning"
                >{{ $t('action.reset') }}</lb-button
              >
            </div>
          </div>
        </el-form-item>
        <el-form-item label="预约信息" prop="reservation_day">
          <el-radio-group v-model="subForm.reservation_type">
            <el-radio :label="1">无需预约</el-radio>
            <el-radio :label="2"
              >需提前<el-input-number
                class="lb-input-number"
                :min="1"
                :precision="0"
                :controls="false"
                v-model="subForm.reservation_day"
                placeholder="请输入"
                style="width: 150px; margin: 0 5px"
              ></el-input-number
              >天预约</el-radio
            >
          </el-radio-group>
        </el-form-item>
        <el-form-item label="使用规则" prop="rule_text">
          <el-input
            type="textarea"
            :rows="10"
            maxlength="1000"
            resize="none"
            show-word-limit
            placeholder="请输入使用规则"
            v-model="subForm.rule_text"
          ></el-input>
        </el-form-item>
        <el-form-item label="保障" prop="ensure">
          <el-radio-group v-model="subForm.ensure">
            <el-radio :label="1">随时退（秒退款）</el-radio>
            <el-radio :label="2">人工退款</el-radio>
          </el-radio-group>
          <lb-tool-tips>
            <p>
              1、随时退：用户在核销前发起退款，系统自动秒退款，无需平台或者商家同意。
            </p>
            <p>2、人工退款：用户在核销前发起退款，需要平台或者商家处理退款。</p>
          </lb-tool-tips>
        </el-form-item>

        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>
      <lb-map
        :dialogVisible.sync="showMap"
        @selectedLatLng="getLatLng"
      ></lb-map>
    </div>
    <el-dialog
      :title="specsForm.index > -1 ? '编辑规格' : '添加规格'"
      :visible.sync="showDialog.specs"
      width="500px"
      center
    >
      <el-form
        class="dialog-form"
        :model="specsForm"
        ref="specsForm"
        :rules="specsFormRules"
        label-width="100px"
      >
        <el-form-item label="规格名" prop="name">
          <el-input
            v-model="specsForm.name"
            maxlength="10"
            show-word-limit
            placeholder="请输入规格名"
          ></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog.specs = false">取 消</el-button>
        <el-button type="primary" @click="submitFormInfo">确 定</el-button>
      </span>
    </el-dialog>
    <el-dialog
      title="选择门店"
      :visible.sync="showDialog.store"
      width="800px"
      center
    >
      <el-form
        :inline="true"
        :model="searchForm.store"
        ref="storeForm"
        label-width="70px"
      >
        <el-form-item label="输入查询" prop="name">
          <el-input
            v-model="searchForm.store.name"
            placeholder="输入关键词搜索店铺"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <lb-button
            size="medium"
            type="primary"
            icon="el-icon-search"
            style="margin-right: 5px"
            @click="getTableDataList(1, 'store')"
            >{{ $t('action.search') }}</lb-button
          >
          <lb-button
            size="medium"
            icon="el-icon-refresh-left"
            style="margin-right: 5px"
            @click="resetForm('store')"
            >{{ $t('action.reset') }}</lb-button
          >
        </el-form-item>
      </el-form>
      <el-table
        :data="tableData.store"
        ref="singleTable"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
        highlight-current-row
        @current-change="handleSelectionChange($event, 'store')"
      >
        <el-table-column prop="name" label="店铺名称"></el-table-column>
        <el-table-column prop="cover" label="店铺封面">
          <template slot-scope="scope">
            <lb-image :src="scope.row.cover" />
          </template>
        </el-table-column>
        <el-table-column prop="mobile" label="商家电话"></el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.store.page"
        :pageSize="searchForm.store.limit"
        :total="total.store"
        @handleSizeChange="handleSizeChange($event, 'store')"
        @handleCurrentChange="handleCurrentChange($event, 'store')"
      >
      </lb-page>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog.store = false">取 消</el-button>
        <el-button type="primary" @click="handleDialogConfirm('store')"
          >确 定</el-button
        >
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
export default {
  data () {
    let validateTime = (rule, value, callback) => {
      let { use_start_time: start, use_end_time: end, use_type: userType, store_id: storeid, use_trade_week: week } = this.subForm
      let { end_time: endtime, start_time: starttime } = this.currentRow
      let newStartTime = new Date(`2023-11-23 ${start}`).getTime()
      let oldStartTime = new Date(`2023-11-23 ${starttime}`).getTime()
      let newEndTime = new Date(`2023-11-23 ${end}`).getTime()
      let oldEndTime = new Date(`2023-11-23 ${endtime}`).getTime()
      let startFlag = false
      let endFlag = false

      if (userType === 1) {
        callback()
        return
      }

      if (!start) {
        callback(new Error(`请选择开始时间`))
        return
      }
      if (!end) {
        callback(new Error(`请选择结束时间`))
        return
      }

      if (week[0] === week[1]) {
        console.log(oldEndTime, newEndTime)
        if (newStartTime >= newEndTime) {
          callback(new Error(`开始时间不能大于结束时间`))
        } else if (oldStartTime > newStartTime) {
          callback(new Error(`开始时间不能小于门店开始时间`))
        } else if (oldEndTime < newEndTime && oldStartTime < oldEndTime) {
          callback(new Error(`结束时间不能大于门店结束时间`))
        } else {
          callback()
        }
        return
      }
      // 门店开始时间大于门店结束时间  结束时间跨天
      if (oldStartTime > oldEndTime) {
        oldEndTime = new Date(`2023-11-24 ${endtime}`).getTime()
      }
      if (newStartTime < oldStartTime) {
        newStartTime = new Date(`2023-11-24 ${start}`).getTime()
      }
      // 当前开始时间大于当前结束时间  结束时间跨天
      if (newStartTime > newEndTime) {
        newEndTime = new Date(`2023-11-24 ${end}`).getTime()
      }
      console.log('开始时间大于结束时间', newStartTime, oldStartTime, week)
      // 选择的开始时间 >= 门店开始时间 并且 选择的开始时间 <= 选择的结束时间
      if (newStartTime >= oldStartTime && newStartTime <= newEndTime) {
        startFlag = true
      }
      if (newEndTime <= oldEndTime && newEndTime >= newStartTime) {
        endFlag = true
      }

      if (userType === 2 && storeid) {
        if (!start || !end) {
          callback(new Error(!start ? `请选择开始时间` : `请选择结束时间`))
        } else if (!startFlag) {
          callback(new Error(`开始时间不能小于门店开始时间`))
        } else if (!endFlag) {
          callback(new Error(`结束时间不能大于门店结束时间`))
        } else {
          callback()
        }
      } else {
        callback()
      }
    }
    let validateReservation = (rule, value, callback) => {
      let { reservation_day: day, reservation_type: type } = this.subForm
      if (type === 2 && !day) {
        callback(new Error(`请输入`))
      } else {
        callback()
      }
    }
    let validateSku = (rule, value, callback) => {
      let { sku } = this.subForm
      if (sku.length === 0) {
        callback(new Error(`请添加规格`))
      } else {
        for (let i = 0; i < sku.length; i++) {
          for (let j = 0; j < sku[i].price.length; j++) {
            if (!sku[i].price[j].name) {
              callback(new Error(`请输入规格值`))
              return
            }
            if (!sku[i].price[j].num && sku[i].price[j].num !== 0) {
              callback(new Error(`请输入规格数量`))
              return
            }
            if (sku[i].price[j].num <= 0) {
              callback(new Error(`规格数量不能为0`))
              return
            }
            if (!sku[i].price[j].price && sku[i].price[j].price !== 0) {
              callback(new Error(`请输入规格价格`))
              return
            }
            if (sku[i].price[j].price <= 0) {
              callback(new Error(`规格价格不能为0`))
              return
            }
          }
        }
        callback()
      }
    }
    let validateTerm = (rule, value, callback) => {
      let { term_type: termtype, term_start_time: start, term_end_time: end, days } = this.subForm
      if (termtype === 1) {
        if (!start || !end) {
          callback(new Error(`请选择指定日期`))
        } else {
          callback()
        }
      } else {
        if (!days) {
          callback(new Error(`请输入有效天数`))
        } else {
          callback()
        }
      }
    }
    let validateIntegral = (rule, value, callback) => {
      let { is_integral: isintegral, integral, integral_to_money: money } = this.subForm
      if (isintegral) {
        if (!integral) {
          callback(new Error(`请输入积分`))
        } else if (!money) {
          callback(new Error(`请输入金额`))
        } else {
          callback()
        }
      }
    }
    return {
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() < (moment(moment(Date.now()).format('YYYY-MM-DD')).unix()) * 1000
        }
      },
      id: '',
      navTitle: '',
      showMap: false,
      have_user_id: false,
      subForm: {
        id: 0,
        store_id: '',
        store_name: '',
        name: '',
        sub_name: '',
        cover: '',
        price: '',
        init_price: '',
        sale: 0,
        imgs: [],
        introduce: '',
        term_type: 1,
        term_start_time: '',
        term_end_time: '',
        days: 1,
        use_type: 1,
        use_trade_week: [0, 6],
        use_start_time: '',
        use_end_time: '',
        reservation_day: 0,
        rule_text: '',
        ensure: 1,
        sku: [],
        // 不传的字段
        reservation_type: 1,
        term_start_end: '',
        use_trade_week_max: 6,
        use_trade_week_min: 0,
        is_integral: 0,
        integral: '',
        integral_to_money: ''
      },
      subFormRules: {
        store_id: { required: true, type: 'number', message: '请选择门店', trigger: 'blur' },
        name: { required: true, validator: this.$reg.isNotNull, text: '套餐名称', reg_type: 2, trigger: 'blur' },
        sub_name: { required: true, validator: this.$reg.isNotNull, text: '副标题', reg_type: 2, trigger: 'blur' },
        cover: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        price: { required: true, validator: this.$reg.isMoney, text: '现价', trigger: 'blur', reg_type: 1 },
        init_price: { required: true, validator: this.$reg.isMoney, text: '划线价', trigger: 'blur', reg_type: 1 },
        sale: { required: true, type: 'number', message: '请输入虚拟销量', trigger: 'blur' },
        imgs: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        sku: { required: true, validator: validateSku, trigger: 'blur' },
        term_type: { required: true, validator: validateTerm, trigger: 'blur' },
        use_trade_week: { required: true, validator: validateTime, trigger: 'blur' },
        reservation_day: { required: true, validator: validateReservation, trigger: 'blur' },
        rule_text: { required: true, type: 'string', message: '请输入使用规则', trigger: 'blur' },
        ensure: { required: true, type: 'number', message: '请选择保障', trigger: 'blur' },
        is_integral: { required: true, validator: validateIntegral, trigger: 'blur' }
      },
      searchForm: {
        store: {
          page: 1,
          limit: 10,
          name: '',
          status: 2
        },
        service: {
          page: 1,
          limit: 10,
          name: ''
        }
      },
      total: { store: 0, service: 0 },
      loading: { store: false, service: false },
      tableData: { store: [], service: [] },
      showDialog: { store: false, specs: false },
      currentRow: {},
      multipleSelection: [],
      coachPriceList: [],
      userInfo: {},
      inputVisible: false,
      inputValue: '',
      weekMarks: {
        0: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周一')
        },
        1: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周二')
        },
        2: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周三')
        },
        3: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周四')
        },
        4: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周五')
        },
        5: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周六')
        },
        6: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周天')
        }
      },
      specsTableData: [],
      specsLoading: false,
      specsForm: {
        name: '',
        index: ''
      },
      specsFormRules: {
        name: { required: true, validator: this.$reg.isNotNull, text: '规格名', reg_type: 2, trigger: 'blur' }
      },
      isCopy: false,
      isSlider: false
    }
  },
  async created () {
    this.userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
    let { id, type = '' } = this.$route.query
    if (id) {
      this.subForm.id = id
      if (type) {
        this.isCopy = true
      }
      await this.getDetail(id)
    }

    console.log(id)
    this.navTitle = this.$t(id ? 'menu.StoreshopPackageEdit' : 'menu.StoreshopPackageAdd')
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    getCover (img, key) {
      this.subForm[key] = img
      this.$refs['subForm'].validate()
    },
    moveFiles (imgs, key) {
      this.subForm[key] = imgs
      this.$refs['subForm'].validate()
    },
    getBannerList (imgs, key) {
      this.subForm[key].push(...imgs)
      this.$refs['subForm'].validate()
    },
    /**
     * @method 获取经纬度
     */
    getLatLng (latLng) {
      this.subForm.lat = latLng.lat
      this.subForm.lng = latLng.lng
    },
    /**
     * @name: 详情
     * @param {*} id
     */
    async getDetail (id) {
      let { code, data } = await this.$api.storeshop.packageInfo({ id })
      if (code !== 200) return
      data.cover = [{ url: data.cover }]
      let arr = ['imgs']
      data.imgs = data.imgs.split(',')
      arr.map((item) => {
        data[item] = data[item] ? data[item].map((aitem) => {
          return { url: aitem }
        }) : []
      })
      // let smax = data.store.trade_week.substring(data.store.trade_week.length - 1)
      // let smin = data.store.trade_week.substring(0, 1)
      // let max = data.use_trade_week.substring(data.use_trade_week.length - 1)
      // let min = data.use_trade_week.substring(0, 1)
      // if (min == 0) {
      //   min = 6
      // } else {
      //   min = min - 1
      // }
      // if (max == 0) {
      //   max = 6
      // } else {
      //   max = max - 1
      // }
      let weekObj = {
        smax: data.store.trade_week.substring(data.store.trade_week.length - 1),
        smin: data.store.trade_week.substring(0, 1),
        max: data.use_trade_week.substring(data.use_trade_week.length - 1),
        min: data.use_trade_week.substring(0, 1)
      }
      if (weekObj.smax === weekObj.smin) {
        this.isSlider = true
      }
      let weekArr = ['smax', 'smin', 'max', 'min']
      weekArr.forEach(key => {
        if (weekObj[key] == 0) {
          weekObj[key] = 6
        } else {
          weekObj[key] = weekObj[key] - 1
        }
      })

      this.currentRow = !this.isCopy ? data.store : {}
      data.store_name = data.store.name
      data.reservation_type = data.reservation_day === 0 ? 1 : 2
      data.term_start_end = data.term_start_time ? [data.term_start_time * 1000, data.term_end_time * 1000] : []
      data.use_trade_week = [weekObj.min, weekObj.max]
      data.use_trade_week_max = this.isSlider ? 6 : weekObj.smax
      data.use_trade_week_min = this.isSlider ? 0 : weekObj.smin

      data.is_integral = data.is_integral || 0
      let copyType = ['store_id', 'use_type', 'use_trade_week', 'use_trade_week_max', 'use_trade_week_min'] // 'term_type', 'term_start_end', 'term_start_time', 'term_end_time',
      for (let key in this.subForm) {
        if (this.isCopy) {
          if (!copyType.includes(key)) {
            this.subForm[key] = data[key]
          }
        } else {
          this.subForm[key] = data[key]
        }
      }
      // let coachPriceList = []
      // data.service.forEach(item => {
      //   coachPriceList.push({ ser_id: item.id, price: item.price || 0 })
      // })
      // this.coachPriceList = coachPriceList
    },
    async toShowDialog (key) {
      if (key === 'store') {
        this.searchForm[key].name = ''
      } else {
        this.searchForm[key].name = ''
      }
      await this.getTableDataList(1, key)
      this.showDialog[key] = !this.showDialog[key]
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
    /**
     * @method 列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.loading[key] = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))

      let methodArr = {
        store: { methodKey: 'storeshop', methodModel: 'getList' },
        service: { methodKey: 'service', methodModel: 'serviceList' }
      }
      let { methodKey, methodModel } = methodArr[key]
      let { code, data } = await this.$api[methodKey][methodModel](searchForm)
      this.loading[key] = false
      if (code !== 200) return
      this.tableData[key] = data.data
      this.total[key] = data.total
    },
    handleSelectionChange (val, key) {
      if (key === 'store') {
        val = JSON.parse(JSON.stringify(val))
        let { id, nickName } = val
        val.nickName = nickName || `门店ID ${id}`
        this.currentRow = val
        return
      }
      this.multipleSelection = val
    },
    handleDialogConfirm (key) {
      if (key === 'store') {
        if (this.currentRow === null || !this.currentRow.id) {
          this.$message.error(`请选择门店`)
          return
        }
        this.resetTime()
      } else {
        let service = JSON.parse(JSON.stringify(this.subForm.service))
        let arr1 = service.length > 0 ? service.map(item => { return item.id }) : []
        this.multipleSelection.map(item => {
          if (arr1.includes(item.id)) return
          service.push(item)
        })
        this.subForm.service = service
        let coachPriceList = []
        service.forEach(item => {
          coachPriceList.push({ ser_id: item.id, price: item.price || 0 })
        })
        this.coachPriceList = coachPriceList
      }
      this.showDialog[key] = false
    },
    /**
     * @name: 新增/编辑
     * @param {*}
     */
    submitForm () {
      let flag = true
      this.$refs['subForm'].validate((valid) => {
        if (!valid) flag = false
      })
      console.log(flag, this.subForm, this.isCopy)
      if (this.subForm.price * 1 > this.subForm.init_price * 1) {
        this.$message.error('划线价不能小于现价')
        return
      }
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        let arr = ['imgs']
        arr.map((item) => {
          subForm[item] = subForm[item].length > 0 && subForm[item].map((aitem) => {
            return aitem.url
          })
        })
        if (subForm.use_type === 2) {
          let weekArr = this.$util.betweenNumbers(...subForm.use_trade_week).split(',')
          let newWeekArr = []
          weekArr.forEach(item => {
            item = item * 1 + 1
            if (item === 7) {
              item = 0
            }
            newWeekArr.push(item)
          })
          subForm.use_trade_week = newWeekArr
        }
        subForm.reservation_day = subForm.reservation_type === 1 ? 0 : subForm.reservation_day
        subForm.cover = subForm.cover[0].url
        if (this.isCopy) {
          subForm.id = ''
        }
        let edit = 'packageEdit'
        let add = 'packageAdd'

        let methodModel = subForm.id ? edit : add
        this.$api.storeshop[methodModel](subForm).then((res) => {
          if (res.code === 200) {
            this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
            this.$router.back(-1)
          }
        })
      }
    },
    setPrice (index) {
      if (!this.coachPriceList[index].price) {
        this.coachPriceList[index].price = 0
      }
    },
    changeTradeWeek (numArr) {
      let numberArr = this.$util.betweenNumbers(...numArr).split(',')
      for (let key in this.weekMarks) {
        this.weekMarks[key].style.color = '#909399'
        if (numberArr.includes(key)) {
          this.weekMarks[key].style.color = '#1989FA'
        }
      }
    },
    resetTime () {
      let { id = 0, name = '', trade_week: tradeWeek = '1, 0', end_time: end, start_time: start } = this.currentRow
      this.subForm.store_id = id
      this.subForm.store_name = name
      let oldMin = tradeWeek.substring(0, 1)
      let oldMax = tradeWeek.substring(tradeWeek.length - 1)
      console.log(oldMin, oldMax)
      if (oldMin === oldMax) {
        this.isSlider = true
      } else {
        this.isSlider = false
      }
      let max = 6
      let min = 0
      if (oldMin == 0) {
        min = 6
      } else {
        min = oldMin * 1 - 1
      }
      if (oldMax == 0) {
        max = 6
      } else {
        max = oldMax * 1 - 1
      }

      this.subForm.use_trade_week_max = this.isSlider ? 6 : max
      this.subForm.use_trade_week_min = this.isSlider ? 0 : min
      this.subForm.use_trade_week = [min, max]
      this.subForm.use_start_time = start
      this.subForm.use_end_time = end
    },
    changeShowDialog (key, item = { name: '', index: -1 }) {
      this.specsForm = item
      console.log(item)
      this.showDialog[key] = true
    },
    submitFormInfo () {
      let flag = true
      this.$refs['specsForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let { name, index = '' } = this.specsForm
        if (index == -1) {
          this.subForm.sku.push({
            name,
            price: [
              { name: '', num: '', price: '' }
            ]
          })
        } else {
          this.subForm.sku[index].name = name
        }
        this.showDialog.specs = false
      }
    },
    delItemSku (index, cindex) {
      this.subForm.sku[index].price.splice(cindex, 1)
    },
    addItemSku (index) {
      this.subForm.sku[index].price.push({ name: '', num: '', price: '' })
    },
    delSku (index) {
      this.subForm.sku.splice(index, 1)
    },
    getTermTime () {
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      let { term_start_end: time } = subForm
      if (time && time.length > 0) {
        this.subForm.term_start_time = time[0] / 1000
        this.subForm.term_end_time = time[1] / 1000
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-banner-edit {
  width: 100%;
  .el-form {
    width: 100%;
    .el-select,
    .el-input-number,
    .el-input {
      width: 300px;
    }
    .el-textarea {
      width: 600px;
    }
    .el-tag {
      cursor: pointer;
    }
    .trade-week {
      width: 500px;
      padding: 0 10px 40px 10px;
    }
    .el-slider {
      white-space: nowrap;
    }
  }
}
</style>
