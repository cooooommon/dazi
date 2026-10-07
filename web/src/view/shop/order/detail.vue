<!--
 * @Description: 订单详情
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-11-26 18:55:05
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-shop-order-edit">
    <top-nav :isBack="true" />
    <div class="page-main">
      <lb-tips :isIcon="false">
        <div class="flex-warp">
          <div>
            {{ statusType[subForm.pay_type] }}
          </div>
          <div
            class="f-desc c-warning ml-md"
            v-if="
              (subForm.pay_type === -1 || subForm.pay_type === 8) &&
              subForm.coach_refund_time * 1 > 0
            "
          >
            <div class="flex-y-center">
              {{ $t('action.attendantName') }}拒单
              <el-popover placement="top-start" width="300" trigger="hover">
                <div class="f-desc c-title" slot>
                  <div v-html="subForm.coach_refund_text"></div>
                  <div class="f-caption c-caption mt-md">
                    拒单时间：{{ subForm.coach_refund_time | handleTime }}
                  </div>
                </div>
                <div class="c-link cursor-pointer ml-sm" slot="reference">
                  查看拒单原因
                </div>
              </el-popover>
            </div>
          </div>

          <div
            class="f-desc c-warning ml-md"
            v-if="!subForm.is_show && !subForm.coach_refund_time"
          >
            用户已删除
          </div>
        </div>
        <div
          class="f-desc c-warning mt-sm"
          style="margin-left: 52px"
          v-if="
            !subForm.is_show &&
            (subForm.pay_type === -1 || subForm.pay_type === 8) &&
            subForm.coach_refund_time
          "
        >
          用户已删除
        </div>
      </lb-tips>

      <div class="mg-lg">
        <el-timeline :reverse="reverse">
          <el-timeline-item
            v-for="(item, index) in lineList"
            :key="index"
            :color="
              (subForm.pay_type > item.pay_type - 1 &&
                subForm.pay_type !== 8) ||
              (subForm.pay_type == -1 && subForm[item.time] * 1 > 0)
                ? '#409EFF'
                : ''
            "
          >
            <div class="flex-y-center">
              {{ item.title }}
              <div
                class="ml-md f-caption c-caption"
                :class="[
                  {
                    'c-link':
                      subForm.pay_type > item.pay_type - 1 &&
                      subForm.pay_type !== 8 &&
                      ((item.title === '签字确认' && subForm.sign_img) ||
                        item.title !== '签字确认')
                  }
                ]"
              >
                {{
                  item.title === '签字确认' && !subForm.sign_img
                    ? '暂未签字确认'
                    : subForm.pay_type > item.pay_type - 1 ||
                      (subForm.pay_type == -1 && subForm[item.time] * 1 > 0)
                    ? ''
                    : '状态未开始'
                }}
              </div>
            </div>
            <div
              class="mt-sm"
              v-if="
                (subForm.pay_type > item.pay_type * 1 - 1 &&
                  subForm[item.time] * 1 > 0) ||
                (subForm.pay_type == -1 && subForm[item.time] * 1 > 0)
              "
            >
              {{ subForm[item.time] | handleTime }}
            </div>
            <div
              class="mt-md"
              v-if="
                (item.pay_type === 4 &&
                  subForm.pay_type > 3 &&
                  subForm.pay_type !== 8) ||
                (item.pay_type === 5 &&
                  subForm.pay_type > 4 &&
                  subForm.pay_type !== 8) ||
                (item.pay_type === 7 && subForm.pay_type === 7) ||
                (item.time && subForm[item.time] * 1 > 0)
              "
            >
              <lb-cover
                :fileList="[
                  {
                    url:
                      (item.pay_type === 5 &&
                        subForm.pay_type > 4 &&
                        subForm.pay_type !== 8) ||
                      (item.time && subForm[item.time] * 1 > 0)
                        ? subForm.arrive_img
                        : item.title === '签字确认'
                        ? subForm.sign_img
                        : subForm.end_img
                  }
                ]"
                :isToDel="false"
                fileType="image"
                type="more"
                size="small"
                :fileSize="1"
                v-if="
                  [
                    $t('action.attendantName') + '到达',
                    '服务完成',
                    '签字确认'
                  ].includes(item.title) && subForm[item.img]
                "
              ></lb-cover>
            </div>

            <div
              class="f-caption c-title"
              v-if="
                (item.pay_type === 4 &&
                  ((subForm.pay_type > 3 && subForm.pay_type !== 8) ||
                    subForm.pay_type === -1) &&
                  subForm.serout_address) ||
                (item.pay_type === 5 &&
                  subForm.pay_type > 4 &&
                  subForm.pay_type !== 8) ||
                (item.title === '服务完成' && subForm.pay_type === 7) ||
                (item.time && subForm[item.time] * 1 > 0)
              "
            >
              {{
                item.pay_type === 4 &&
                ((subForm.pay_type > 3 && subForm.pay_type !== 8) ||
                  subForm.pay_type === -1) &&
                subForm.serout_address
                  ? subForm.serout_address
                  : item.pay_type === 5 &&
                    ((subForm.pay_type > 4 && subForm.pay_type !== 8) ||
                      subForm.pay_type === -1) &&
                    subForm.arr_address
                  ? subForm.arr_address
                  : subForm.end_address
              }}
            </div>
          </el-timeline-item>
        </el-timeline>
      </div>
      <el-form
        @submit.native.prevent
        :model="subForm"
        label-width="130px"
        size="mini"
      >
        <lb-classify-title title="用户信息"></lb-classify-title>
        <el-form-item label="用户ID：">
          <div>{{ subForm.user_id }}</div>
        </el-form-item>
        <el-form-item label="下单人：">
          <div class="flex-y-center">
            {{ subForm.address_info.user_name }}
            <div
              @click="toShowDialog('label')"
              class="ml-md c-link cursor-pointer"
            >
              查看标签
            </div>
          </div>
        </el-form-item>
        <el-form-item label="联系方式：">
          <div>{{ subForm.address_info.mobile }}</div>
        </el-form-item>
        <el-form-item label="服务地址：" v-if="!subForm.store_id">
          <div>
            {{
              `${subForm.address_info.address}${subForm.address_info.address_info}`
            }}
          </div>
        </el-form-item>
        <el-form-item label="订单备注：" v-if="subForm.text">
          <div>{{ subForm.text }}</div>
        </el-form-item>
        <div v-if="subForm.store_id">
          <lb-classify-title title="门店信息"></lb-classify-title>
          <el-form-item label="门店名称：">
            <div>{{ subForm.store_info.title }}</div>
          </el-form-item>
          <el-form-item label="门店头像：">
            <lb-cover
              :fileList="[{ url: subForm.store_info.cover }]"
              :isToDel="false"
              size="small"
              type="more"
              :fileSize="1"
            ></lb-cover>
          </el-form-item>
          <el-form-item label="联系电话：">
            <div>{{ subForm.store_info.phone }}</div>
          </el-form-item>
          <el-form-item label="门店地址：" v-if="subForm.store_info.address">
            <div>
              {{ subForm.store_info.address }}
            </div>
          </el-form-item>
        </div>
        <lb-classify-title
          :title="`${$t('action.attendantName')}信息`"
        ></lb-classify-title>
        <el-form-item :label="`${$t('action.attendantName')}：`">
          <div>{{ subForm.coach_info.coach_name }}</div>
        </el-form-item>
        <el-form-item :label="`${$t('action.attendantName')}头像：`">
          <lb-cover
            :fileList="[{ url: subForm.coach_info.work_img }]"
            :isToDel="false"
            size="small"
            type="more"
            :fileSize="1"
          ></lb-cover>
        </el-form-item>
        <el-form-item label="联系电话：">
          <div>{{ subForm.coach_info.mobile }}</div>
        </el-form-item>
        <lb-classify-title title="订单信息"></lb-classify-title>
        <el-form-item label="主订单号：" v-if="subForm.is_add">
          <div
            @click="$router.replace({ query: { id: subForm.add_pid.id } })"
            class="c-warning cursor-pointer"
          >
            {{ subForm.add_pid.order_code }}
          </div>
        </el-form-item>
        <el-form-item
          label="子订单号："
          v-if="
            !subForm.is_add &&
            subForm.add_order_id &&
            subForm.add_order_id.length > 0
          "
        >
          <div
            @click="$router.replace({ query: { id: item.id } })"
            class="c-warning cursor-pointer"
            :class="[{ 'mt-md': index !== 0 }]"
            v-for="(item, index) in subForm.add_order_id"
            :key="index"
          >
            {{ item.order_code }}
          </div>
        </el-form-item>
        <el-form-item label="系统订单号：">
          <div>{{ subForm.order_code }}</div>
        </el-form-item>
        <el-form-item label="付款订单号：" v-if="subForm.transaction_id">
          <div>{{ subForm.transaction_id }}</div>
        </el-form-item>
        <el-form-item label="代理商：" v-if="subForm.admin_id">
          <div>{{ subForm.admin_name }}</div>
        </el-form-item>
        <el-form-item label="下单时间：">
          <div>{{ subForm.create_time | handleTime }}</div>
        </el-form-item>
        <el-form-item label="服务时间：">
          <div>
            {{ subForm.start_time | handleTime(3) }} -
            {{ subForm.end_time | handleTime(4) }}
          </div>
        </el-form-item>
        <el-form-item label="服务时长：">
          <div>{{ subForm.time_long }}分钟</div>
        </el-form-item>
        <div v-if="!subForm.is_add && !subForm.store_id">
          <el-form-item label="车费详情：">
            <div class="flex-y-center">
              {{ carType[subForm.car_type] }}
              <div class="ml-md" v-if="subForm.car_type == 1">
                全程{{ subForm.distance }}，车费¥{{ subForm.car_price }}
              </div>
            </div>
          </el-form-item>
          <el-form-item label="出行费用：" v-if="subForm.car_type === 1">
            <div class="c-warning">¥{{ subForm.car_price }}</div>
          </el-form-item>
        </div>
        <el-form-item label="服务项目费用：">
          <div class="c-warning">¥{{ subForm.init_service_price }}</div>
        </el-form-item>
        <el-form-item label="卡券优惠：" v-if="subForm.discount * 1 > 0">
          <div class="c-warning">-¥{{ subForm.discount }}</div>
        </el-form-item>
        <el-form-item label="会员卡折扣：" v-if="subForm.member_status">
          <div class="c-warning">
            {{ subForm.member_balance }}折（优惠¥{{ subForm.member_discount }}）
          </div>
        </el-form-item>
        <el-form-item label="实付金额：" v-if="!subForm.is_add">
          <div class="c-warning">¥{{ subForm.pay_price }}</div>
        </el-form-item>
        <el-form-item label="支付方式：">
          {{ payType[subForm.pay_model] }}
        </el-form-item>
      </el-form>
      <div v-if="subForm.dispatch_record.length > 0">
        <lb-classify-title title="转派记录"></lb-classify-title>
        <el-table
          :data="subForm.dispatch_record"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          style="width: 100%"
        >
          <el-table-column
            prop="old_coach_name"
            :label="`原${$t('action.attendantName')}`"
          >
            <template slot-scope="scope">
              {{ scope.row.old_coach_name }}
              <el-popover
                placement="top-start"
                width="300"
                trigger="hover"
                v-if="scope.$index === 0 && subForm.coach_refund_time"
              >
                <div class="f-desc c-title" slot>
                  <div v-html="subForm.coach_refund_text"></div>
                  <div class="f-caption c-caption mt-md">
                    拒单时间：{{ subForm.coach_refund_time | handleTime }}
                  </div>
                </div>
                <div class="c-link cursor-pointer" slot="reference">
                  查看拒单原因
                </div>
              </el-popover>
            </template>
          </el-table-column>
          <el-table-column
            prop="old_coach_img"
            :label="`原${$t('action.attendantName')}头像`"
          >
            <template slot-scope="scope">
              <lb-image :src="scope.row.old_coach_img" />
            </template>
          </el-table-column>
          <el-table-column
            prop="old_coach_mobile"
            :label="`原${$t('action.attendantName')}联系电话`"
            min-width="120"
          ></el-table-column>
          <el-table-column
            prop="now_coach_name"
            :label="`现${$t('action.attendantName')}`"
          ></el-table-column>
          <el-table-column
            prop="now_coach_img"
            :label="`现${$t('action.attendantName')}头像`"
          >
            <template slot-scope="scope">
              <lb-image :src="scope.row.now_coach_img" />
            </template>
          </el-table-column>
          <el-table-column
            prop="now_coach_mobile"
            :label="`现${$t('action.attendantName')}联系电话`"
            min-width="120"
          ></el-table-column>
          <el-table-column prop="create_time" label="转派时间" min-width="120">
            <template slot-scope="scope">
              <p>{{ scope.row.create_time | handleTime(1) }}</p>
              <p>{{ scope.row.create_time | handleTime(2) }}</p>
            </template>
          </el-table-column>
          <el-table-column prop="text" label="转派备注" min-width="200">
            <template slot-scope="scope">
              <div v-html="scope.row.text"></div>
            </template>
          </el-table-column>
        </el-table>
        <div class="space-lg"></div>
        <div class="space-lg"></div>
      </div>
      <lb-classify-title title="服务内容"></lb-classify-title>
      <el-table
        :data="subForm.order_goods"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="goods_cover" label="服务项目">
          <template slot-scope="scope">
            <lb-image :src="scope.row.goods_cover" />
          </template>
        </el-table-column>
        <el-table-column prop="goods_name" label="服务名称"></el-table-column>
        <el-table-column prop="time_long" label="服务时长">
          <template slot-scope="scope">
            {{ scope.row.time_long }}分钟
          </template>
        </el-table-column>
        <el-table-column prop="num" label="服务数量">
          <template slot-scope="scope">
            <div class="flex-y-center">
              x{{ scope.row.num }}
              <div
                class="f-icontext c-warning ml-sm"
                v-if="scope.row.refund_num"
              >
                {{ `已退${scope.row.refund_num}` }}
              </div>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <div class="space-lg mt-lg mb-lg">
        合计数量：{{ subForm.all_goods_num }}
      </div>
      <div v-if="upRecord.length > 0">
        <div class="space-lg"></div>
        <lb-classify-title title="服务升级记录"></lb-classify-title>
        <el-timeline>
          <el-timeline-item v-for="(item, index) in upRecord" :key="index">
            <div class="c-title text-bold mb-md">
              {{ item.create_time | handleTime }}
            </div>
            <div
              class="pd-lg f-desc c-caption"
              :class="[{ 'mt-md': aindex !== 0 }]"
              style="width: 400px; border: 1px solid #eee"
              v-for="(aitem, aindex) in item.order_goods"
              :key="aindex"
            >
              <div
                v-if="
                  aitem.original_order_goods &&
                  aitem.original_order_goods.goods_name
                "
              >
                <div class="flex-y-center">
                  原项目名称：
                  <div class="c-title">
                    {{ aitem.original_order_goods.goods_name }}
                  </div>
                </div>
                <div class="flex-y-center mt-sm">
                  价格：
                  <div class="c-title">
                    ¥ {{ aitem.original_order_goods.true_price }}
                  </div>
                </div>
              </div>
              <div class="flex-y-center mt-sm">
                升级项目名称：
                <div class="c-title">{{ aitem.goods_name }}</div>
              </div>
              <div class="flex-y-center mt-sm">
                升级项目数量：
                <div class="c-title">x{{ aitem.num }}</div>
              </div>
              <div class="flex-y-center mt-sm">
                补差价：
                <div class="c-title">
                  ¥
                  {{
                    aitem.true_price * 1 -
                    aitem.original_order_goods.true_price * 1
                  }}
                </div>
              </div>
            </div>
            <div class="mt-md flex-y-center">
              合计补差价：
              <div class="c-warning">¥{{ item.pay_price }}</div>
            </div>
          </el-timeline-item>
        </el-timeline>
      </div>
      <div class="space-lg" v-if="upRecord.length === 0"></div>
      <div v-if="routesItem.auth.virtual">
        <lb-classify-title title="服务过程录音文件"></lb-classify-title>
        <el-row class="page-search-form">
          <el-form
            @submit.native.prevent
            :inline="true"
            :model="searchForm"
            ref="searchForm"
          >
            <el-form-item label="拨打时间" prop="start_time">
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
              >
              </el-date-picker>
            </el-form-item>
          </el-form>
        </el-row>
        <el-table
          v-loading="loading"
          :data="tableData"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
        >
          <el-table-column prop="id" label="ID" fixed> </el-table-column>
          <el-table-column prop="record_name" label="文件名称" min-width="200">
          </el-table-column>
          <el-table-column prop="call_time" label="拨打时间" min-width="120">
            <template slot-scope="scope">
              <p>{{ scope.row.call_time | handleTime(1) }}</p>
              <p>{{ scope.row.call_time | handleTime(2) }}</p>
            </template>
          </el-table-column>
          <el-table-column prop="start_time" label="接通时间" min-width="120">
            <template slot-scope="scope">
              <p>{{ scope.row.start_time | handleTime(1) }}</p>
              <p>{{ scope.row.start_time | handleTime(2) }}</p>
            </template>
          </el-table-column>
          <el-table-column prop="end_time" label="挂断时间" min-width="120">
            <template slot-scope="scope">
              <p>{{ scope.row.end_time | handleTime(1) }}</p>
              <p>{{ scope.row.end_time | handleTime(2) }}</p>
            </template>
          </el-table-column>
          <el-table-column
            prop="phone_a"
            :label="`${$t('action.attendantName')}号码`"
            min-width="120"
          >
          </el-table-column>
          <el-table-column prop="phone_b" label="客户号码" min-width="120">
          </el-table-column>
          <el-table-column prop="phone_x" label="虚拟号码" min-width="120">
          </el-table-column>
          <el-table-column label="操作" min-width="160" fixed="right">
            <template slot-scope="scope">
              <div class="table-operate" v-if="scope.row.record_url">
                <lb-button
                  size="mini"
                  plain
                  type="primary"
                  @click="toShowDialog('file', scope.row)"
                  >播放</lb-button
                >
                <lb-button
                  size="mini"
                  plain
                  type="danger"
                  @click="toDownLoad(scope.row)"
                  >下载</lb-button
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
      </div>
      <lb-button type="primary" @click="$router.back(-1)">{{
        $t('action.back')
      }}</lb-button>
    </div>

    <el-dialog
      title="客户标签"
      :visible.sync="showDialog.label"
      width="700px"
      center
    >
      <div v-if="labelList.length === 0">暂无评价标签</div>
      <div class="flex-warp" v-if="labelList.length > 0">
        <el-tag
          class="mr-md mt-sm mb-sm"
          v-for="(item, index) in labelList"
          :key="index"
          >{{ item.title }}</el-tag
        >
      </div>
    </el-dialog>

    <el-dialog
      title="音频内容"
      :visible.sync="showDialog.file"
      width="600px"
      center
    >
      <div class="flex-center flex-column" style="padding: 50px">
        <audio controls loop :src="fileForm.record_url"></audio>
        <div class="f-title c-title text-bold pt-lg">
          {{ fileForm.record_name }}
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import moment from 'moment'
import { mapState } from 'vuex'
export default {
  data () {
    return {
      navTitle: '',
      carType: {
        0: '公交/地铁',
        1: '出租车'
      },
      payType: {
        1: '微信支付',
        2: '余额支付',
        3: '支付宝支付'
      },
      statusType: {
        '-1': '已取消',
        1: '待支付',
        2: '待服务',
        3: this.$t('action.attendantName') + '接单',
        4: this.$t('action.attendantName') + '出发',
        5: this.$t('action.attendantName') + '到达',
        6: '服务中',
        7: '已完成',
        8: '待转单'
      },
      lineList: [],
      base_service: [{
        pay_type: 3,
        title: this.$t('action.attendantName') + '接单',
        time: 'receiving_time',
        icon: 'iconjishijiedan'
      }, {
        pay_type: 4,
        title: this.$t('action.attendantName') + '出发',
        time: 'serout_time',
        icon: 'iconjishichufa'
      }, {
        pay_type: 5,
        title: this.$t('action.attendantName') + '到达',
        img: 'arrive_img',
        time: 'arrive_time',
        icon: 'iconjishidaoda'
      }, {
        pay_type: 6,
        title: '开始服务',
        time: 'start_service_time',
        icon: 'iconjishifuwu'
      }, {
        pay_type: 7,
        title: '服务完成',
        img: 'end_img',
        time: 'order_end_time',
        icon: 'iconjishiwancheng'
      }, {
        pay_type: 7,
        title: '签字确认',
        img: 'sign_img',
        time: 'sign_time',
        icon: ''
      }],
      base_bell: [{
        pay_type: 3,
        title: this.$t('action.attendantName') + '接单',
        time: 'receiving_time',
        icon: 'iconjishijiedan'
      }, {
        pay_type: 6,
        title: '开始服务',
        time: 'start_service_time',
        icon: 'iconjishifuwu'
      }, {
        pay_type: 7,
        title: '服务完成',
        img: 'end_img',
        time: 'order_end_time',
        icon: 'iconjishiwancheng'
      }],
      subForm: {},
      upRecord: {},
      showDialog: { label: false, file: false },
      labelList: [],
      fileForm: {
        record_url: '',
        record_name: ''
      },
      loading: false,
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      searchForm: {
        page: 1,
        limit: 10,
        start_time: '',
        end_time: ''
      },
      tableData: [],
      total: 0
    }
  },
  created () {
    let { id } = this.$route.query
    this.getDetail(id)
    this.getFormInfo()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.payType[2] = data.balance_character
    },
    async getDetail (id) {
      let { code, data } = await this.$api.shop.orderInfo({ id })
      if (code !== 200) return
      data.is_balance = data.balance * 1 > 0 ? 1 : 0
      let { coach_refund_text: text } = data
      data.coach_refund_text = text && text.length > 0 ? text.replace(/\n/g, '<br>') : '没有填写原因哦'
      data.dispatch_record.map(item => {
        item.text = item.now_coach_id === 0 ? item.text.replace(/\n/g, '<br>') : ''
      })
      let lineList = JSON.parse(JSON.stringify(data.is_add || data.store_id ? this.base_bell : this.base_service))
      if (data.store_id) {
        lineList.push({
          pay_type: 7,
          title: '签字确认',
          time: 'sign_time',
          icon: ''
        })
      }
      this.lineList = lineList
      this.subForm = data
      this.getUpRecord(id)
      let { virtual = false } = this.routesItem.auth
      if (!virtual) return
      this.getTableDataList()
    },
    async getUpRecord (id) {
      let { code, data } = await this.$api.shop.orderUpRecord({ order_id: id })
      if (code !== 200) return
      this.upRecord = data
    },
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handleCurrentChange(1)
    },
    handleCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    /**
     * @method: 获取列表
     */
    getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      searchForm.order_id = this.subForm.id
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      this.$api.system.phoneRecordList(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          res.data.data.map(item => {
            let arr = item.record_url && item.record_url.includes('?Expires=') ? item.record_url.split('?Expires=')[0].split('/') : []
            item.record_name = arr.length > 0 ? arr[arr.length - 1] : ''
          })
          this.tableData = res.data.data
          this.total = res.data.total
        }
      })
    },
    async toShowDialog (type, item = {}) {
      switch (type) {
        case 'label':
          let { user_id: uid } = this.subForm
          let { data } = await this.$api.shop.userLabelList({
            user_id: uid
          })
          this.labelList = data
          break
        case 'file':
          for (let key in this.fileForm) {
            this.fileForm[key] = item[key]
          }
          break
      }
      this.showDialog[type] = !this.showDialog[type]
    },
    toDownLoad (item) {
      let { record_url: src, record_name: title } = item
      let a = document.createElement('a') // 生成一个a元素
      let event = new MouseEvent('click') // 创建一个单击事件
      a.download = title // 设置图片名称
      a.href = src // 将生成的URL设置为a.href属性
      a.dispatchEvent(event) // 触发a的单击事件
    }
  },
  // 监听器
  watch: {
    async $route (route) {
      let { id = 0 } = route.query
      if (id * 1 !== this.subForm.id) {
        this.getDetail(id)
      }
    }
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : type === 3 ? moment(val * 1000).format('YYYY-MM-DD HH:mm') : type === 4 ? moment(val * 1000).format('HH:mm') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-shop-order-edit {
  .order-img {
    width: 120px;
    height: 120px;
  }
  .el-textarea {
    width: 600px;
  }
}
</style>
