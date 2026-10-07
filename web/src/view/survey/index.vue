<!--
 * @Descripttion: 概况
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-07-13 17:07:24
-->
<template>
  <el-row class="lb-survey" v-loading="!isLoad">
    <div class="page-main" style="height: 552px">
      <div class="flex-between pb-lg f-title text-bold c-title b-1px-b">
        <div class="flex-y-baseline">平台总销售额 (不含车费)</div>
        <div>
          <el-button-group>
            <el-button
              @click="toChangeItem('rankInd', index)"
              :plain="rankInd !== index"
              type="primary"
              v-for="(item, index) in rankList"
              :key="index"
              >{{ item.title }}</el-button
            >
          </el-button-group>
          <el-date-picker
            @change="toChangeItem('start_time', $event)"
            v-model="searchForm.data.start_time"
            type="daterange"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            value-format="timestamp"
            :editable="false"
            :clearable="false"
            :picker-options="pickerOptions"
            :default-time="['00:00:00', '23:59:59']"
            style="margin-left: 10px"
            v-if="searchForm.data.day === 5"
          >
          </el-date-picker>
        </div>
      </div>
      <div class="space-lg"></div>
      <div class="data-count-list flex-warp">
        <div class="item-child pt-md">
          <div class="pb-lg c-title text-bold" style="font-size: 15px">
            销售额趋势
          </div>
          <div
            style="width: 100%; height: 400px; background: #fff"
            v-if="isLoad"
          >
            <sales-echarts :datas="tableData.data" />
          </div>
        </div>
        <div class="item-child f-paragraph pt-md">
          <div class="pb-lg c-title text-bold" style="font-size: 15px">
            代理商销售额排名 (不含车费)
          </div>
          <div class="flex-warp" style="margin-top: 20px">
            <div v-if="tableData.agent.length === 0">暂无代理商数据哦</div>
            <div
              :class="[{ 'b-1px-r': tableData.agent.length > 10 }]"
              :style="{
                width: '50%',
                paddingRight: tableData.agent.length > 10 ? '20px' : ''
              }"
            >
              <div v-for="(item, index) in tableData.agent" :key="index">
                <div
                  class="flex-between"
                  :class="[{ 'mt-md': index !== 0 }]"
                  v-if="index < 10"
                >
                  <div class="item-text flex-y-center">
                    <div
                      class="rank-tag flex-center radius mr-md"
                      :style="{
                        color: index < 3 ? '#fff' : '#000',
                        background: index < 3 ? colorType[index] : '#eee'
                      }"
                    >
                      <div class="text">{{ index + 1 }}</div>
                    </div>
                    <div class="ellipsis">
                      {{ item.username }}
                    </div>
                  </div>
                  <div class="c-title">¥{{ item.sale_price }}</div>
                </div>
              </div>
            </div>
            <div class="pl-lg" style="width: 50%; padding-left: 20px">
              <div v-for="(item, index) in tableData.agent" :key="index">
                <div
                  class="flex-between"
                  :class="[{ 'mt-md': index !== 10 }]"
                  v-if="index > 9"
                >
                  <div class="item-text flex-y-center">
                    <div
                      class="rank-tag flex-center radius mr-md"
                      :style="{
                        color: '#000',
                        background: '#eee'
                      }"
                    >
                      <div class="text">{{ index + 1 }}</div>
                    </div>
                    <div class="ellipsis">{{ item.username }}</div>
                  </div>
                  <div class="c-title">{{ item.sale_price }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="fill-body space-lg"></div>
    <!-- 人员统计 -->
    <div class="page-main">
      <div class="flex-between pb-lg f-title text-bold c-title b-1px-b">
        <div class="flex-y-baseline">人员统计</div>
      </div>
      <div class="class-menu-list flex-warp mt-lg pt-md">
        <div class="item-child flex-center">
          <div
            class="item-icon flex-center radius mb-sm"
            style="background: #608dff"
          >
            <i class="iconfont icon-account-line c-base"></i>
          </div>
          <div class="flex-1 ml-lg c-title">
            <div class="f-sm-title text-bold">
              {{ base_count.total_coach || 0 }}
            </div>
            <div class="flex-y-baseline">
              <div class="f-caption">全部{{ $t('action.attendantName') }}</div>
              <lb-tool-tips :padding="0"
                >平台入驻的全部{{ $t('action.attendantName') }}，手机端的全部{{
                  $t('action.attendantName')
                }}+未认证{{ $t('action.attendantName') }}</lb-tool-tips
              >
            </div>
          </div>
        </div>
        <div class="item-child flex-center">
          <div
            class="item-icon flex-center radius mb-sm"
            style="background: #2bcd8e"
          >
            <i class="iconfont icon-zaixian c-base"></i>
          </div>
          <div class="flex-1 ml-lg c-title">
            <div class="f-sm-title text-bold">
              {{ base_count.app_coach || 0 }}
            </div>
            <div class="flex-y-baseline">
              <div class="f-caption">
                可接单{{ $t('action.attendantName') }}人数
              </div>
              <!--当前在线向导人数-->
              <lb-tool-tips :padding="0"
                >当前在接单的{{
                  $t('action.attendantName')
                }}是指在工作时间范围内的{{
                  $t('action.attendantName')
                }}，同时打开了接单按钮的</lb-tool-tips
              >
            </div>
          </div>
        </div>
        <div class="item-child flex-center">
          <div
            class="item-icon flex-center radius mb-sm"
            style="background: #ff6b73"
          >
            <i class="iconfont icon-xiuxi c-base"></i>
          </div>
          <div class="flex-1 ml-lg c-title">
            <div class="f-sm-title text-bold">
              {{ base_count.rest_coach || 0 }}
            </div>
            <div class="flex-y-baseline">
              <div class="f-caption">
                休息{{ $t('action.attendantName') }}人数
              </div>
              <lb-tool-tips :padding="0"
                ><div>
                  1、不在工作时间内的所有{{ $t('action.attendantName') }}
                </div>
                2、关闭了某个时间段的{{
                  $t('action.attendantName')
                }}</lb-tool-tips
              >
            </div>
          </div>
        </div>
      </div>
      <div class="class-menu-list flex-warp mt-lg pt-md">
        <div class="item-child flex-center">
          <div
            class="item-icon flex-center radius mb-sm"
            style="background: #9370db"
          >
            <i class="iconfont icon-jiedan c-base"></i>
          </div>
          <div class="flex-1 ml-lg c-title">
            <div class="f-sm-title text-bold">
              {{ base_count.working_coach || 0 }}
            </div>
            <div class="flex-y-baseline">
              <div class="f-caption">
                接单中{{ $t('action.attendantName') }}人数
              </div>
              <lb-tool-tips :padding="0"
                >平台上有正在服务中的{{
                  $t('action.attendantName')
                }}人数</lb-tool-tips
              >
            </div>
          </div>
        </div>
        <div class="item-child flex-center">
          <div
            class="item-icon flex-center radius mb-sm"
            style="background: #daa520"
          >
            <i class="iconfont iconwodetuandui1 c-base"></i>
          </div>
          <div class="flex-1 ml-lg c-title">
            <div class="f-sm-title text-bold">
              {{ base_count.user_count || 0 }}
            </div>
            <div class="flex-y-baseline">
              <div class="f-caption">用户总数</div>
              <lb-tool-tips :padding="0"
                >平台的用户总数，仅统计授权过微信昵称头像或者手机号的用户，访客不统计</lb-tool-tips
              >
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- 营收数据 -->
    <div class="fill-body space-lg"></div>
    <div class="page-main">
      <div class="flex-between pb-lg f-title text-bold c-title b-1px-b">
        <div class="flex-y-baseline">营收数据</div>
        <el-date-picker
          @change="getTableDataList(1, 'list')"
          v-model="searchForm.list.start_time"
          type="daterange"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
          value-format="timestamp"
          :picker-options="pickerOptions"
          :default-time="['00:00:00', '23:59:59']"
        >
        </el-date-picker>
      </div>
      <div class="flex-warp mt-lg pt-md">
        <div class="item-child flex-center" style="margin-right: 100px">
          <div
            class="item-icon flex-center radius mb-sm"
            style="background: #608dff"
          >
            <i class="iconfont icon-caiwuguanli c-base"></i>
          </div>
          <div class="flex-1 ml-lg c-title">
            <div class="f-sm-title text-bold">
              {{ yesterday_count.price || 0 }}
            </div>
            <div class="flex-y-baseline">
              <div class="f-caption">昨日营收</div>
              <lb-tool-tips :padding="0">不含车费</lb-tool-tips>
            </div>
          </div>
        </div>
        <div class="item-child flex-center">
          <div
            class="item-icon flex-center radius mb-sm"
            style="background: #2bcd8e"
          >
            <i class="iconfont icon-dingdanguanli c-base"></i>
          </div>
          <div class="flex-1 ml-lg c-title">
            <div class="f-sm-title text-bold">
              {{ yesterday_count.count || 0 }}
            </div>
            <div class="flex-y-baseline">
              <div class="f-caption">昨日订单量</div>
              <lb-tool-tips :padding="0"
                >当日0点至24点的订单量，续单也算一单</lb-tool-tips
              >
            </div>
          </div>
        </div>
      </div>
      <div class="space-lg"></div>
      <el-table
        v-loading="loading.list"
        :data="tableData.list"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column
          prop="id"
          :label="`${$t('action.attendantName')}ID`"
        ></el-table-column>
        <el-table-column
          prop="coach_name"
          :label="`${$t('action.attendantName')}昵称`"
        ></el-table-column>
        <el-table-column
          prop="coach_level.title"
          :label="`${$t('action.attendantName')}等级`"
          v-if="cash_type == 1"
        >
          <template slot="header" slot-scope="scope">
            <div class="flex-y-center">
              <div>{{ $t('action.attendantName') }}等级</div>
              <lb-tool-tips padding="2"
                >当前{{ $t('action.attendantName') }}等级</lb-tool-tips
              >
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="coach_level.balance" label="提成比例">
          <template slot-scope="scope">
            {{
              `${
                cash_type == 1
                  ? scope.row.coach_level.balance
                  : scope.row.cash_balance
              }%`
            }}
          </template>
        </el-table-column>
        <el-table-column prop="total_coach_cash" label="提成总金额">
          <template slot-scope="scope">
            {{ `¥${scope.row.total_coach_cash}` }}
          </template>
        </el-table-column>
        <!-- <el-table-column prop="pay_price" label="结算时间"></el-table-column> -->
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
      <div class="space-md"></div>
    </div>
    <div class="fill-body space-lg"></div>
    <!-- 当前开放城市 -->
    <div class="page-main">
      <div class="flex-between pb-lg f-title text-bold c-title b-1px-b">
        <div class="flex-y-baseline">当前开放城市</div>
      </div>
      <div class="space-lg"></div>
      <div id="map-container-box"></div>
    </div>
  </el-row>
</template>

<script>
import moment from 'moment'
import salesEcharts from './salesEcharts'
export default {
  components: {
    salesEcharts
  },
  data () {
    return {
      isLoad: false,
      pickerOptions: {
        onPick: ({ maxDate, minDate }) => {
          this.selectDate = minDate.getTime()
          if (maxDate) {
            this.selectDate = ''
          }
        },
        disabledDate: (time) => {
          if (this.selectDate !== '') {
            const one = 59 * 24 * 3600 * 1000
            const minTime = this.selectDate - one
            const maxTime = this.selectDate + one
            return time.getTime() < minTime || time.getTime() > maxTime
          }
        }
      },
      rankInd: 2,
      rankList: [
        {
          id: 1,
          title: '今日'
        },
        {
          id: 2,
          title: '近7日'
        },
        {
          id: 3,
          title: '近30日'
        },
        {
          id: 4,
          title: '今年'
        },
        {
          id: 5,
          title: '自定义'
        }
      ],
      colorType: {
        0: '#608dff',
        1: '#2bcd8e',
        2: '#ff6b73'
      },
      map: null,
      isInitMap: false,
      base_count: {},
      yesterday_count: {},
      loading: { data: false, agent: false, list: false },
      searchForm: {
        data: {
          day: 3,
          start_time: '',
          end_time: ''
        },
        agent: {
          day: 3,
          start_time: '',
          end_time: ''
        },
        list: {
          page: 1,
          limit: 10,
          start_time: '',
          end_time: ''
        }
      },
      tableData: {
        data: [],
        agent: [],
        list: []
      },
      total: {
        data: 0,
        agent: 0,
        list: 0
      },
      cash_type: ''
    }
  },
  created () {
    this.initIndex()
    this.getConfigInfo()
  },
  destroyed () {
    this.destroyMap()
  },
  methods: {
    async getConfigInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.cash_type = data.cash_type
    },
    async initIndex () {
      await Promise.all([this.getTableDataList(1, 'data'), this.getTableDataList(1, 'agent')])
      this.isLoad = true
      let { data } = await this.$api.survey.coachAndUserData()
      this.base_count = data
      await this.getTableDataList(1, 'list')
      this.initMap()
    },
    initMap () {
      let { city_list: cityList } = this.base_count
      if (cityList.length === 0) return
      let center = new TMap.LatLng(39.25565142103588, 109.248046875)
      this.map = new TMap.Map('map-container-box', {
        zoom: 4.4,
        minZoom: 4.4,
        maxZoom: 7,
        center: center,
        mapStyleId: 'style1',
        pitchable: false,
        rotatable: false,
        scrollable: false,
        doubleClickZoom: false,
        renderOptions: {
          enableBloom: true
        },
        baseMap: {
          type: 'vector',
          features: ['base', 'building3d']
        }
      })
      this.isInitMap = true
      let dotArr = []
      cityList.map(item => {
        let { lat, lng, title } = item
        // 快照库里部分城市没有坐标，缺坐标的跳过，否则 GL SDK 生成标记会崩溃
        if (lat === null || lng === null || lat === undefined || lng === undefined || lat === '' || lng === '') return
        dotArr.push({
          position: new TMap.LatLng(lat, lng),
          styleId: 'small',
          content: title || ''
        })
      })
      var markerLayer = new TMap.MultiMarker({
        map: this.map,
        styles: {
          small: new TMap.MarkerStyle({
            width: 20,
            height: 25,
            anchor: { x: 17, y: 23 },
            src: 'https://mapapi.qq.com/web/lbs/visualizationApi/demo/img/big.png',
            color: '#333',
            size: 12,
            direction: 'bottom',
            offset: { x: 0, y: 4 },
            strokeColor: '#fff',
            strokeWidth: 1
          })
        },
        enableCollision: false,
        geometries: dotArr
      })
    },
    destroyMap () {
      if (!this.isInitMap) return
      this.map.destroy()
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
     * @method: 获取列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.tableData[key] = []
      this.loading[key] = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      let methodArr = {
        data: 'orderData',
        agent: 'agentOrderData',
        list: 'coachSaleData'
      }
      let methodModel = methodArr[key]
      let { code, data } = await this.$api.survey[methodModel](searchForm)
      this.loading[key] = false
      if (code !== 200) return
      if (['data', 'agent'].includes(key)) {
        this.tableData[key] = data
        return
      }
      let { data: datas, total = 0 } = data.list
      this.tableData[key] = datas
      this.total[key] = total
      let { yesterday_count: count, yesterday_price: price } = data
      this.yesterday_count = {
        count, price
      }
    },
    toChangeItem (key, val) {
      let { time = [] } = this.searchForm.data
      if (key === 'rankInd') {
        let arr = ['data', 'agent']
        let { id } = this.rankList[val]
        arr.map(item => {
          this.searchForm[item].day = id
          this.searchForm[item].start_time = time
        })
        this[key] = val
        if (val === 4 && this.searchForm.data.start_time.length === 0) return
      } else {
        this.searchForm.agent.start_time = val
      }
      this.getTableDataList(1, 'data')
      this.getTableDataList(1, 'agent')
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-survey {
  #map-container-box {
    width: 100%;
    height: 750px;
    margin: 0 auto;
  }
  .data-count-list {
    height: 420px;
    .item-child {
      height: 420px;
      .item-text {
        width: 70%;
        .rank-tag {
          width: 25px;
          height: 25px;
          .text {
            transform: scale(0.8);
          }
        }
        .ellipsis {
          max-width: calc(100% - 35px);
        }
      }
    }
    .item-child:nth-child(1) {
      width: 60%;
    }
    .item-child:nth-child(2) {
      width: 40%;
    }
  }
  .class-menu-list {
    margin-left: 50px;
    .item-child {
      width: 33%;
    }
  }
  .item-icon {
    width: 50px;
    height: 50px;
    .iconfont {
      font-size: 25px;
    }
  }
}
</style>
