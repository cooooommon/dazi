<template>
  <div class="lb-color-list" v-if="colorList.length > 0">
    <top-nav />
    <div class="page-main">
      <div class="flex-warp">
        <div class="mt-md mr-lg">导航渐变底色：</div>
        <div class="flex-1">
          <div class="flex-warp">
            <div class="flex-warp">
              <div v-for="(item, index) in gradualColorList" :key="index">
                <div
                  @click="changeIndex(index, 1)"
                  class="color-item mini"
                  :class="[{ active: index === gradualInd }]"
                  v-if="index < gradualColorList.length - 1"
                >
                  <div class="flex-center">
                    <div class="primaryColor flex-center">
                      <div class="color-bg" :style="{ background: item }"></div>
                    </div>
                  </div>
                  <i
                    class="iconfont icon-xuanze-fill flex-center"
                    v-if="index === gradualInd"
                  ></i>
                </div>
              </div>
            </div>
            <div
              @click="changeIndex(gradualColorList.length - 1, 1)"
              class="color-item mt-sm"
              :class="[{ active: gradualInd === gradualColorList.length - 1 }]"
              style="width: auto; padding: 0 2px"
            >
              <div class="flex-center" style="margin-top: 4px">
                <el-color-picker
                  size="mini"
                  style="margin-right: 4px"
                  v-model="gradualColorList[gradualColorList.length - 1]"
                ></el-color-picker>
              </div>
              <div class="flex-y-center" style="height: 18px; margin-top: 4px">
                <div style="line-height: 18px; font-size: 10px">自定义配色</div>
                <i
                  class="iconfont icon-xuanze flex-center"
                  :class="[
                    {
                      'icon-xuanze-fill':
                        gradualInd === gradualColorList.length - 1
                    }
                  ]"
                  style="margin: 0"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex-warp">
        <div class="mt-md mr-lg">当前配色方案：</div>
        <div class="flex-1">
          <div class="flex-warp">
            <div v-for="(item, index) in colorList" :key="index">
              <div
                @click="changeIndex(index)"
                class="color-item"
                :class="[{ active: index === colorInd }]"
                v-if="index < colorLen - 1"
              >
                <div class="flex-center">
                  <div
                    class="primaryColor"
                    :style="{ background: item.primaryColor }"
                  ></div>
                  <div
                    class="subColor"
                    :style="{ background: item.subColor }"
                  ></div>
                </div>
                <i
                  class="iconfont icon-xuanze-fill flex-center"
                  v-if="index === colorInd"
                ></i>
              </div>
            </div>
            <div
              v-if="colorList[colorLen - 1]"
              @click="changeIndex(colorLen - 1)"
              class="color-item"
              :class="[{ active: colorInd === colorLen - 1 }]"
              style="width: auto; padding: 0 2px"
            >
              <div class="flex-center" style="margin-top: 4px">
                <el-color-picker
                  size="medium"
                  style="margin-right: 4px"
                  v-model="colorList[colorLen - 1].primaryColor"
                ></el-color-picker>
                <el-color-picker
                  size="medium"
                  v-model="colorList[colorLen - 1].subColor"
                ></el-color-picker>
              </div>
              <div class="flex-y-center" style="height: 18px; margin-top: 4px">
                <div style="line-height: 18px; font-size: 10px">自定义配色</div>
                <i
                  class="iconfont icon-xuanze flex-center"
                  :class="[
                    {
                      'icon-xuanze-fill': colorInd === colorLen - 1
                    }
                  ]"
                  style="margin: 0"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="space-lg"></div>
      <div class="space-lg"></div>
      <div class="space-lg"></div>
      <div class="flex-warp">
        <div class="mr-lg">当前风格示例：</div>
        <div class="flex-1">
          <div class="c-warning pb-lg">
            当前仅为配色方案演示效果，页面布局以实际为准
          </div>

          <div class="flex-y-center">
            <div class="color-page fill-body rel">
              <img class="navbar-img abs" :src="navbar" />
              <img
                mode="aspectFill"
                class="service-page-bg abs"
                src="https://lbqny.migugu.com/admin/playwith/mine/service-nav-bg.png" /><!--https://lbqny.migugu.com/admin/playwith/mine/service-page-bg.png-->
              <div style="height: 58px"></div>

              <div class="rel" style="z-index:2">
                <div class="service-search flex-between fill-base ml-md radius">
                  <div class="flex-y-center">
                    <div class="f-desc">搜索服务名称</div>
                  </div>
                  <i class="iconfont iconsousuo c-title mr-md"></i>
                  <!-- <div class="btn flex-center f-desc c-base radius" :style="{background:colorList[colorInd].primaryColor}">搜索</div> -->
                </div>
                  <img class="banner-img mt-md ml-md mr-md" :src="banner" />
                  <!-- <div class="tab-info pt-md">
                    <div class="service-bg abs"></div>
                    <div class="tab-list flex-warp">
                      <div
                        class="tab-item flex-center c-title rel"
                        v-for="(item, index) in tabList"
                        :key="index"
                      >
                        {{ item.title }}
                        <div class="line abs" :style="{background:colorList[colorInd].primaryColor}" v-if="index===0"> 
                        </div>
                      </div>
                    </div>
                  </div> -->         
                  <div class="pt-md pl-md pr-md flex-between module">
                  <div class="rel" style="width: 150px;">
                      <img class="wizard" src="https://lbqny.migugu.com/admin/playwith/service/01.png" mode="aspectFill"></img>
                      <span class="c-base f-title abs wizard-title">{{$t('action.attendantName')}}入驻</span>
                    </div>
                  <div class="">
                    <div class="rel">
                      <img class="business" src="https://lbqny.migugu.com/admin/playwith/service/02.png" mode="aspectFill"></img>
                      <span class="c-base f-title abs business-title">商家入驻</span>
                    </div>
                    <div class="mt-sm rel" style="width:143px;">
                        <img class="join" src="https://lbqny.migugu.com/admin/playwith/service/03.png" mode="aspectFill"></img>
                        <span class="c-base f-title abs join-title">合作加盟</span>
                      </div>
                    
                  </div>
                </div>
                <div class="f-paragraph c-black text-bold pt-md pl-md">新奇玩法</div>
              <div
                class="service-item flex-center fill-base pd-md"
                v-for="(item, index) in serviceList"
                :key="index"
              >
                <img class="cover" :src="item.cover" />
                <div class="flex-between flex-1" style="width: 176px">
                  <div class="flex-1 pl-md">
                    <div class="flex-between">
                      <div class="f-mini-title max-330 ellipsis c-black">{{item.title}}</div>
                      <div class="flex-y-center">
                        <i class="iconfont iconwode2 icon-font-color" style="font-size: 11px;"></i>
                        <span class="f-icontext c-desc">{{item.total_sale}}人玩过</span>
                      </div>
                    </div>
                    <div class="pt-sm max-330 ellipsis f-caption c-caption">{{item.sub_title}}</div>
                    <div class="flex-y-center pt-md">
                      <div class="flex-center">
                        <!-- <image src="/static/images/time.png" mode="aspectFill" style="width: 30rpx;height: 30rpx;"></image> -->
                        <i class="iconfont iconshijian2" :style="{color:colorList[colorInd].primaryColor}"></i>
                        <div class="flex-center" style="padding-left: 4rpx;">
                          <span class="f-desc text-bold">{{item.time_long}}</span>
                          <span class="f-icontext">分钟</span>
                        </div>
                      </div>
                      <div class="flex-center pl-sm">
                        <span style="color: #FF2404;" class="f-caption">￥</span>
                        <span style="color: #FF2404;" class="f-mini-title text-bold">{{item.price}}</span>
                      </div>
                      <span class="f-icontext c-caption pl-sm" style="text-decoration: line-through">￥{{item.init_price}}</span>
                    </div>
                  </div>
                  <div class="f-desc c-base item-btn flex-center abs" style="right: 20px;" :style="{backgroundColor:colorList[colorInd].primaryColor }" >下单</div>
                </div>
              </div>
              </div>

              <div class="tabbar-list fill-base abs">
                <div class="flex-warp b-1px-t">
                  <div
                    class="tabbar-item flex-center flex-column"
                    :style="{
                      color:
                        index === 0
                          ? colorList[colorInd].primaryColor
                          : '#BCCAD9'
                    }"
                    v-for="(item, index) in tabBar"
                    :key="index"
                  >
                    <i
                      class="iconfont"
                      :class="[
                        { 'icon-font-color': index == 0 },
                        index === 0 ? item.selected_img : item.default_img
                      ]"
                      :style="{
                        backgroundImage:
                          index == 0
                            ? `linear-gradient(180deg, ${gradualColorList[gradualInd]} 0%, ${colorList[colorInd].primaryColor} 100%)`
                            : ''
                      }"
                    ></i>
                    <div class="text">{{ item.name }}</div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 向导 -->
            <div class="color-page fill-body ml-lg mr-lg rel">
              <img class="navbar-img abs" :src="navbar" />
              <!-- <img
                mode="aspectFill"
                class="technician-page-bg abs"
                src="https://lbqny.migugu.com/admin/playwith/mine/technician-page-bg.png" /> -->
              <div class="technician-page-bg abs" style="background:url(https://lbqny.migugu.com/admin/playwith/mine/technician-nav-bg.png)no-repeat left top / 100%"></div>
              <div style="height: 58px"></div>
                <div class="fix-info ml-md mr-md">
                  <div class="search-info pb-md">
                    <div class="flex-center">
                      <div class="city-info flex-y-center c-title">
                        成都市<i class="iconfont iconshaixuanxia-1"></i>
                      </div>
                      <div class="search-item fill-base flex-1 radius">
                        <i class="iconfont iconsousuo c-title mr-sm"></i>请输入{{$t('action.attendantName')}}昵称
                      </div>
                    </div>
                  </div>
                  <div class="flex-between">
                    <div class="f-desc pr-md ellipsis" style="color: #5A677E;z-index: 1">
                      四川省成都市武侯区某某街道
                    </div>
                    <i class="iconfont icongengduo" style="font-size: 12px;z-index: 1"></i>
                  </div>
              </div>
              <div class="t-nav fill-base flex-between pl-md pr-md mt-md">
                <div class="flex-warp flex-1">
                  <div class="type-item flex-center f-caption mr-md pl-md pr-md"
                  v-for="(item,index) in screenType" :key="index" >{{item.title}}</div>
                </div>
                <div class="flex-center">
                  <span class="c-caption f-caption pr-sm">高级筛选</span>
                  <i class="iconfont iconshaixuan1" style="font-size: 20px;"></i>
                </div>
              </div>
              <div class="flex-warp pl-md pr-md fill-base">
                <div class="technician-list-item mb-md box-shadow" :class="[{'mr-md':index%2==0}]" v-for="(info,index) in coachList" :key="index">
                  <div class="list-item fill-base radius-16">
                    <div class="work-img rel" style="overflow: hidden">
                      <img class="work-img" :src="info.work_img" />
                      <span class="abs technician-type" :class="[`text-type-${info.text_type}`]">{{textType[info.text_type]}}</span>
                      <span class="abs technician-tag flex-center f-little c-base pl-sm pr-sm" :class="[`tag-type-${info.text_type}`]" v-if="info.tag_name">
                        {{info.tag_name}}
                      </span>
                    </div>
                    <div class="content-info">
                      <div class="c-black text-bold ellipsis f-title max-300">{{info.coach_name}}
                        </div>
                        <div class="f-caption c-caption pt-sm ellipsis">身高{{info.height}}·体重{{info.weight}}·{{info.constellation}}</div>
                        <div class="f-caption c-caption pt-sm ellipsis max-300">{{info.text}}</div>
                        <div class="flex-between pt-sm">
                          <div class="flex-center">
                            <span style="color: #E94A48;" class="f-caption">￥</span>
                            <span style="color: #E94A48;" class="f-title text-bold">{{info.price}}</span>
                            <span class="f-caption" style="color: #9BA2AA;">/单</span>
                          </div>
                          <div class="flex-center">
                            <i class="icondizhi iconfont" style="color: #DDDDDD;"></i>
                            <span class="f-caption c-icontext " style="padding-left: 6rpx;">{{info.distance}}</span>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
  
              <div class="tabbar-list fill-base abs">
                <div class="flex-warp b-1px-t">
                  <div
                    class="tabbar-item flex-center flex-column"
                    :style="{
                      color:
                        index === 1
                          ? colorList[colorInd].primaryColor
                          : '#BCCAD9'
                    }"
                    v-for="(item, index) in tabBar"
                    :key="index"
                  >
                    <i
                      class="iconfont"
                      :class="[
                        { 'icon-font-color': index == 1 },
                        index === 1 ? item.selected_img : item.default_img
                      ]"
                      :style="{
                        backgroundImage:
                          index == 1
                            ? `linear-gradient(180deg, ${gradualColorList[gradualInd]} 0%, ${colorList[colorInd].primaryColor} 100%)`
                            : ''
                      }"
                    ></i>
                    <div class="text">{{ item.name }}</div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 订单 -->
            <div class="color-page fill-body rel">
              <img class="navbar-img abs" :src="navbar" />
              <div
                class="navbar-title"
                :style="{ background: colorList[colorInd].primaryColor }"
              >
                <div class="flex-center c-base">订单</div>
              </div>
              <div class="order-tablist flex-warp fill-base">
                <div
                  class="tablist-item flex-center rel"
                  v-for="(item, index) in orderTabList"
                  :key="index"
                  :style="{
                    width: 100 / orderTabList.length + '%',
                    color:
                      index === 0 ? colorList[colorInd].primaryColor : '#333'
                  }"
                >
                  {{ item.title }}
                  <div
                    class="line abs"
                    :style="{ background: colorList[colorInd].primaryColor }"
                    v-if="index === 0"
                  ></div>
                </div>
              </div>

              <div
                class="order-item mt-md ml-md mr-md pd-lg fill-base radius-16"
                v-for="(item, index) in orderList"
                :key="index"
              >
                <div class="flex-between pb-lg b-1px-b">
                  <div class="f-paragraph c-title text-bold max-300 ellipsis">
                    {{$t('action.attendantName')}}：{{ item.coach_info.coach_name }}
                  </div>
                  <!-- // pay_type 1待支付，2待服务，3向导接单，4向导出发，5向导到达，6服务中，7服务完成 -->
                  <div
                    class="f-icontext c-title max-300 ellipsis"
                    style="color: #adadad"
                  >
                    订单号：{{ item.order_code }}
                  </div>
                </div>
                <div
                  class="flex-center mt-lg"
                  v-for="(aitem, aindex) in item.order_goods"
                  :key="aindex"
                >
                  <img class="cover radius-16" :src="aitem.goods_cover" />
                  <div class="flex-1 ml-md">
                    <div class="flex-between">
                      <div class="c-title text-bold max-300 ellipsis">
                        {{ aitem.goods_name }}
                      </div>
                      <div class="f-icontext c-desc">x{{ aitem.num }}</div>
                    </div>
                    <div class="f-caption c-caption">
                      预约时间：{{ item.start_time }}
                    </div>
                    <div
                      class="flex-between"
                      style="margin-top: 10px"
                      v-if="
                        aindex == item.order_goods.length - 1 ||
                        aitem.refund_num > 0
                      "
                    >
                      <div>
                        <div
                          class="flex-y-baseline f-icontext c-caption"
                          v-if="aindex == item.order_goods.length - 1"
                        >
                          总计：
                          <div class="flex-y-baseline c-warning text-bold">
                            ¥
                            <div class="f-title">{{ item.pay_price }}</div>
                          </div>
                        </div>
                      </div>
                      <div>
                        <div
                          class="f-caption c-warning"
                          v-if="aitem.refund_num > 0"
                        >
                          已退x{{ aitem.refund_num }}
                        </div>
                      </div>
                    </div>
                    <div style="height: 30px" v-else></div>
                  </div>
                </div>
                <div class="flex-between mt-lg pt-lg b-1px-t">
                  <div
                    class="f-caption text-bold"
                    :style="{
                      color:
                        item.pay_type == 1
                          ? colorList[colorInd].primaryColor
                          : [2, 3, 4, 5, 6].includes(item.pay_type)
                          ? colorList[colorInd].subColor
                          : item.pay_type == 7
                          ? '#11C95E'
                          : '#333'
                    }"
                  >
                    {{ statusType[item.pay_type] }}
                  </div>
                  <div class="flex-warp">
                    <!-- // pay_type 1待支付，2待服务，3向导接单，4向导出发，5向导到达，6服务中，7服务完成，8待评价 -->
                    <!-- 待支付 -->
                    <div v-if="item.pay_type == 1">
                      <div class="order-btn">取消订单</div>
                      <div
                        class="order-btn"
                        :style="{
                          color: '#fff',
                          background: colorList[colorInd].primaryColor,
                          borderColor: colorList[colorInd].primaryColor
                        }"
                      >
                        去支付
                      </div>
                    </div>
                    <!-- 支付超时 -->
                    <div v-if="item.pay_type == -1 || item.pay_type == 7">
                      <div class="order-btn">删除</div>
                    </div>
                    <!-- 待服务 -->
                    <div class="order-btn" v-if="item.can_refund > 0">
                      申请退款
                    </div>
                    <!-- 已完成 -->
                    <div
                      class="order-btn"
                      v-if="item.pay_type == 7 && !item.is_comment"
                    >
                      去评价
                    </div>
                    <div
                      class="order-btn"
                      :style="{
                        color: '#fff',
                        background: colorList[colorInd].primaryColor,
                        borderColor: colorList[colorInd].primaryColor
                      }"
                      v-if="item.pay_type == 7"
                    >
                      再来一单
                    </div>
                  </div>
                </div>
              </div>

              <div class="tabbar-list fill-base abs">
                <div class="flex-warp b-1px-t">
                  <div
                    class="tabbar-item flex-center flex-column"
                    :style="{
                      color:
                        index === 2
                          ? colorList[colorInd].primaryColor
                          : '#BCCAD9'
                    }"
                    v-for="(item, index) in tabBar"
                    :key="index"
                  >
                    <i
                      class="iconfont"
                      :class="[
                        { 'icon-font-color': index == 2 },
                        index === 2 ? item.selected_img : item.default_img
                      ]"
                      :style="{
                        backgroundImage:
                          index == 2
                            ? `linear-gradient(180deg, ${gradualColorList[gradualInd]} 0%, ${colorList[colorInd].primaryColor} 100%)`
                            : ''
                      }"
                    ></i>
                    <div class="text">{{ item.name }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="space-lg"></div>
    </div>

    <div class="flex-center pd-lg b-1px-t">
      <el-button
        class="mr-md"
        type="danger"
        :plain="true"
        @click="getSystemDefaulDiy"
        >{{ $t('action.defaultSet') }}</el-button
      >
      <el-button type="primary" @click="submitForm" v-preventReClick
        >保存</el-button
      >
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      navbar: 'https://lbqny.migugu.com/admin/diy/navbar_white_none.png',
      banner:
        'https://lbqnyv3.migugu.com/image/666/23/04/b4dPRgrgoi419KMtPOMpt65M65IltXGR.png',
      colorList: [{
        primaryColor: '#843CFF',
        subColor: '#DF52FF'
      }, {
        primaryColor: '#A40035',
        subColor: '#F1C06B'
      }, {
        primaryColor: '#19c865',
        subColor: '#f86c53'
      }, {
        primaryColor: '#db4d4d',
        subColor: '#e28e5f'
      }, {
        primaryColor: '#dd6630',
        subColor: '#e39a32'
      }, {
        primaryColor: '#ebcc3b',
        subColor: '#20252d'
      }, {
        primaryColor: '#8ac4aa',
        subColor: '#232826'
      }, {
        primaryColor: '#8228f9',
        subColor: '#b49fe2'
      }, {
        primaryColor: '#ca2432',
        subColor: '#efd645'
      }, {
        primaryColor: '#db4d4d',
        subColor: '#555555'
      }, {
        primaryColor: '#85c077',
        subColor: '#b1dda7'
      }, {
        primaryColor: '#baaa6f',
        subColor: '#dcd5a9'
      }],
      gradualColorList: [
        '#DF52FF',
        '#739bc6',
        '#60a06a',
        '#d4b64c',
        '#c09e51',
        '#d5964b',
        '#c26a51',
        '#ffb6b1',
        '#b0b4c7',
        '#616570'
      ],
      gradualInd: 0,
      gradualColor: '#DF52FF',
      primaryColor: '#843CFF',
      subColor: '#DF52FF',
      colorInd: 0,
      colorLen: 0,
      tabBar: [{
        id: 1,
        name: '首页',
        default_img: 'iconshouye2',
        selected_img: 'iconshouye12'
      }, {
        id: 2,
        name: this.$t('action.attendantName'),
        default_img: 'iconpeiwanguan1',
        selected_img: 'iconpeiwanguan2'
      }, {
        id: 4,
        name: '订单',
        default_img: 'icondingdan3',
        selected_img: 'icondingdan2'
      }, {
        id: 5,
        name: '我的',
        default_img: 'iconwode-2',
        selected_img: 'iconwode-1'
      }],
      tabList: [{
        title: '全部',
        sort: 'top desc'
      }, {
        title: '价格',
        sort: 'price',
        sign: 0,
        is_sign: 1
      }, {
        title: '邀约量',
        sort: 'total_sale',
        sign: 0,
        is_sign: 1
      }, {
        title: '好评度',
        sort: 'star',
        sign: 0,
        is_sign: 1
      }],
      technicianTabList: [{
        title: '全部',
        id: 0
      }, {
        title: '可服务',
        id: 1
      }, {
        title: '服务中',
        id: 5
      }, {
        title: '可预约',
        id: 5
      }],
      orderTabList: [{
        title: '全部',
        id: 0
      }, {
        title: '待支付',
        id: 1
      }, {
        title: '待服务',
        id: 5
      }, {
        title: '服务中',
        id: 6
      }, {
        title: '已完成',
        id: 7
      }],
      imgType: {
        1: 'top',
        2: 'hot',
        3: 'new'
      },
      textType: {
        1: '可接单',
        2: '接单中',
        3: '休息中',
        4: '不可接单'
      },
      statusType: {
        '-1': '已取消',
        1: '待支付',
        2: '待服务',
        3: this.$t('action.attendantName') + '接单',
        4: this.$t('action.attendantName') + '出发',
        5: this.$t('action.attendantName') + '到达',
        6: '服务中',
        7: '已完成'
      },
      service_btn_color: '',
      service_font_color: '',
      screenType: [{
        title: '推荐'
      }, {
        title: '新人'
      }],
      serviceList: [{
        title: '密室逃脱',
        sub_title: '游戏',
        cover: 'https://card-1253902191.cos.ap-chengdu.myqcloud.com/image/666/22/02/ef2f99bc4b0549bcb4903bf6d77d07f5.jpg',
        price: 156,
        total_sale: 16,
        time_long: 4,
        init_price: 288
      }, {
        title: '线下唱歌',
        sub_title: '娱乐',
        cover: 'https://lbqnyv3.migugu.com/image/666/23/04/b4dPRgrgoi419KMtPOMpt65M65IltXGR.png',
        price: 128,
        total_sale: 10,
        time_long: 10,
        init_price: 200
      }, {
        title: '狼人杀',
        sub_title: '游戏',
        cover: 'https://lbqnyv3.migugu.com/image/666/23/04/1vL8gb728bCTt4gi7ZoyuLiqgenRXhRe.jpeg',
        price: 168,
        total_sale: 55,
        time_long: 10,
        init_price: 188.88
      }],
      coachList: [{
        user_id: 1,
        coach_name: '赵倩',
        work_img: 'https://card-1253902191.cos.ap-chengdu.myqcloud.com/image/666/22/02/8b4542f9bb8b48d0a533a227bb0dfec5.jpeg',
        is_work: 1,
        star: 5,
        distance: '4.89km',
        comment_num: 0,
        collect_num: 1,
        order_num: 0,
        is_collect: 0,
        near_time: '11:00',
        text_type: 1,
        coach_type_status: 3,
        height: 168,
        weight: 50,
        constellation: '双子座',
        text: '我是一个' + this.$t('action.attendantName'),
        price: 10,
        tag_name: '向往'
      }, {
        user_id: 1,
        coach_name: '李某嬬',
        work_img: 'https://card-1253902191.cos.ap-chengdu.myqcloud.com/image/666/22/02/ef2f99bc4b0549bcb4903bf6d77d07f5.jpg',
        is_work: 1,
        star: 4,
        distance: '5.88km',
        comment_num: 1,
        collect_num: 2,
        order_num: 1,
        is_collect: 1,
        near_time: '13:56',
        text_type: 2,
        coach_type_status: 2,
        height: 168,
        weight: 50,
        constellation: '双子座',
        text: '我是一个' + this.$t('action.attendantName'),
        price: 10,
        tag_name: '憧憬未来'
      }, {
        user_id: 1,
        coach_name: '任敏',
        work_img: 'https://card-1253902191.cos.ap-chengdu.myqcloud.com/image/666/22/09/e6af165706b2454f973a4f4f0e930303.jpg',
        is_work: 0,
        star: 4.5,
        distance: '7.99km',
        comment_num: 2,
        collect_num: 931,
        order_num: 32,
        is_collect: 1,
        near_time: '11:08',
        text_type: 3,
        coach_type_status: 1,
        height: 168,
        weight: 50,
        constellation: '双子座',
        text: '我是一个' + this.$t('action.attendantName'),
        price: 10,
        tag_name: '憧憬未来'
      }, {
        user_id: 1,
        coach_name: 'selina',
        work_img: 'https://card-1253902191.cos.ap-chengdu.myqcloud.com/image/666/22/09/e6af165706b2454f973a4f4f0e930303.jpg',
        is_work: 0,
        star: 4.5,
        distance: '3.99km',
        comment_num: 2,
        collect_num: 23,
        order_num: 32,
        is_collect: 1,
        near_time: '',
        text_type: 4,
        coach_type_status: 1,
        height: 168,
        weight: 50,
        constellation: '双子座',
        text: '我是一个' + this.$t('action.attendantName'),
        price: 10,
        tag_name: '憧憬未来'
      }],
      orderList: [{
        order_code: '20220915181500068600000686',
        pay_type: 7,
        transaction_id: '20220915181500068600000686',
        pay_price: '156.00',
        start_time: '2022-09-17 02:08',
        end_time: '2022-09-17 02:18',
        coach_info: {
          coach_name: '李某嬬'
        },
        order_goods: [{
          goods_name: '密室逃脱',
          goods_cover: 'https://card-1253902191.cos.ap-chengdu.myqcloud.com/image/666/22/02/ef2f99bc4b0549bcb4903bf6d77d07f5.jpg',
          price: 1,
          num: 1,
          can_refund_num: 1,
          true_price: 1
        }],
        can_refund: 0
      }, {
        order_code: '20220915171623025600000256',
        pay_type: 2,
        transaction_id: '20220915171623025600000256',
        pay_price: '632.00',
        start_time: '2022-09-16 00:38',
        end_time: '2022-09-16 01:03',
        coach_info: {
          coach_name: '李某嬬'
        },
        order_goods: [{
          goods_name: '线下唱歌',
          goods_cover: 'https://lbqnyv3.migugu.com/image/666/23/04/b4dPRgrgoi419KMtPOMpt65M65IltXGR.png',
          price: 1,
          num: 1,
          can_refund_num: 1,
          true_price: 1
        }, {
          goods_name: '狼人杀',
          goods_cover: 'https://lbqnyv3.migugu.com/image/666/23/04/1vL8gb728bCTt4gi7ZoyuLiqgenRXhRe.jpeg',
          price: 19.5,
          num: 3,
          can_refund_num: 3,
          refund_num: 2,
          true_price: 19.5
        }],
        can_refund: 1
      }]
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async changeIndex (index, key = 0) {
      if (key) {
        this.gradualInd = index
        return
      }
      this.colorInd = index
    },
    async getFormInfo () {
      let { data } = await this.$api.system.configInfo()
      let { gradualColor, primaryColor, subColor, service_btn_color: sbtn, service_font_color: sfont } = data
      sbtn = !sbtn ? '#282B34' : sbtn
      sfont = !sfont ? '#EBDDB1' : sfont
      this.service_btn_color = sbtn
      this.service_font_color = sfont
      gradualColor = gradualColor || '#DF52FF'

      let gradualInd = this.gradualColorList.findIndex(item => {
        return item === gradualColor
      })
      if (gradualInd === -1) {
        gradualInd = 10
      }
      this.gradualInd = gradualInd
      this.gradualColorList.push(gradualInd === -1 ? gradualColor : '')

      let colorList = JSON.parse(JSON.stringify(this.colorList))
      let arr = colorList.filter(item => {
        return item.primaryColor === primaryColor && item.subColor === subColor
      })
      colorList.push(arr.length === 0 ? {
        primaryColor,
        subColor
      } : {
        primaryColor: '',
        subColor: ''
      })

      this.colorList = colorList
      this.colorLen = colorList.length
      let ind = -1
      colorList.map((item, index) => {
        if (item.primaryColor === primaryColor && item.subColor === subColor) {
          ind = index
        }
      })
      this.colorInd = ind === -1 ? colorList.length - 1 : ind
    },
    async getSystemDefaulDiy () {
      this.colorInd = 0
      await this.submitForm()
    },
    async submitForm () {
      let gradualColor = this.gradualColorList[this.gradualInd]
      let { primaryColor, subColor } = this.colorList[this.colorInd]
      if (!gradualColor || !primaryColor || !subColor) {
        this.$message.error(!gradualColor ? '请选择导航渐变底色' : !primaryColor ? `请选择主色` : `请选择辅色`)
        return
      }
      let { code } = await this.$api.system.configUpdate({ gradualColor, primaryColor, subColor })
      if (code !== 200) return
      this.$message.success(this.$t('tips.successSave'))
    }
  }
}
</script>

<style lang="scss" scoped>
.color-item {
  width: 84px;
  height: 68px;
  margin: 10px 10px 0 0;
  padding-top: 2px;
  border: 1px solid white;
  cursor: pointer;
  .iconfont {
    color: #429dff;
    margin-top: 5px;
  }
}
.color-item.active {
  border: 1px solid #429dff;
  border-radius: 5px;
  box-shadow: 0px 4px 12px 1px rgba(66, 157, 255, 0.15);
}
.color-item.mini {
  width: 40px;
  height: 58px;
  margin-right: 5px;
  padding-top: 4px;
  border: 1px solid white;
  cursor: pointer;
  .primaryColor {
    width: 28px;
    height: 28px;
    border-radius: 3px;
    border: 1px solid #e6e6e6;
    .color-bg {
      width: 20px;
      height: 20px;
      border-radius: 3px;
    }
  }
}
.color-item.mini.active {
  border: 1px solid #429dff;
  border-radius: 3px;
  box-shadow: 0px 4px 12px 1px rgba(66, 157, 255, 0.15);
}
.primaryColor,
.subColor {
  width: 40px;
  height: 40px;
  border-radius: 5px 0 0 5px;
}
.subColor {
  border-radius: 0 5px 5px 0;
}
.color-page {
  width: 300px;
  height: 700px;
  overflow: hidden;
  border-radius: 5px;
  border: 1px solid #eee;
  .navbar-img {
    width: 300px;
    height: 52px;
    z-index: 2;
  }
  .navbar-title {
    width: 300px;
    height: 52px;
    .flex-center {
      height: 52px;
      padding-top: 18px;
    }
  }
  .banner-img {
    width: 280px;
    height: 78px;
    border-radius: 8px;
    display: block;
    object-fit: cover;
    z-index: 1;
  }
  // 首页
  .service-page-bg {
    width: 100%;
    height: 350px;
    z-index: 1;
  }

  .service-search {
    width: 280px;
    height: 34px;
    padding: 0 2px 0 15px;

    .flex-y-center {
      .f-desc {
        color: #c7c7c7;
      }
    }

    .btn {
      width: 50px;
      height: 30px;
    }
  }

  .tab-info {
    width: 300px;
    height: 45px;

    .service-bg {
      width: 300px;
      height: 45px;
      background: linear-gradient(
        180deg,
        rgba(255, 255, 255, 0) 0%,
        #ffffff 100%
      );
      z-index: 1;
    }

    .tab-list {
      width: 300px;
      .tab-item {
        width: 27%;
        height: 40px;
        color: #666666;
        z-index: 3;
        .line {
          width: 10px;
          height: 2px;
          border-radius: 4px;
          bottom: 0;
          left: 50%;
          margin-left: -5px;
          z-index: 3;
        }
      }
      .tab-item:nth-child(1) {
        width: 19%;
        color: #333;
        font-weight: bold;
      }
    }
  }

  .service-item {
    padding: 10px;
    margin: 10px;
    border-radius: 8px;
    .cover {
      width: 72px;
      height: 72px;
      border-radius: 8px;
      object-fit: cover;
    }

    .f-icontext {
      font-size: 9px;
    }

    .small-text {
      font-size: 9px;
      margin-left: 2px;
    }
    .text-delete {
      color: #b9b9b9;
      transform: scale(0.8);
    }

    .item-btn {
      width: 55px;
      height: 23px;
      border-radius: 23px;
    }

    .iconwode2 {
      background-image: linear-gradient(#9fa4b6, #bfc5cf);
    }
  }
  // 向导

  .technician-page-bg {
    width: 300px;
    height: 130px;
  }

  .fix-info {
    background: #f6f7fb;
    z-index: 2;

    .search-info {
      width: 100%;
      border-radius: 20px;
      font-size: 13px;
      z-index: 2;

      .city-info {
        width: 65px;
        z-index: 1;

        .iconfont {
          font-size: 10px;
          transform: scale(0.4);
        }
      }
      .search-item {
        height: 32px;
        padding: 0 15px;
        line-height: 32px;
        z-index: 3;
        .iconfont {
          font-size: 14px;
        }
      }
    }

    .choose-sex-service {
      height: 25px;
      padding: 0 5px 0 8px;

      .iconfont {
        color: #666666;
        font-size: 9px;
        transform: scale(0.45);
      }
    }
  }

  .technician-list-item {
    .list-item {
      width: 133.5px;

      .work-img {
        width: 133.5px;
        height: 157px;
        border-radius: 8px 8px 0 0;
        object-fit: cover;

        .near-time-order {
          width: 133.5px;
          height: 17px;
          padding: 0 8px;
          font-size: 10px;
          background: rgba(0, 0, 0, 0.64);
          left: 0;
          bottom: 0;
          z-index: 2;
          .time {
            margin-left: -14px;
            transform: scale(0.7);
          }
          .order {
            margin-right: -10px;
            transform: scale(0.7);
          }
        }
      }

      .content-info {
        padding: 9px 7px;

        .star {
          color: #ff9300;

          .iconfont {
            font-size: 13px;
            margin-right: 2px;
          }
        }

        .btn-list {
          margin-top: 6px;

          .btn-item {
            width: 55px;
            height: 20px;
            border-radius: 3px;
            transform: rotateZ(360deg);
          }
        }

        .count-list {
          color: #b1a7a9;
          font-size: 11px;

          .iconfont {
            font-size: 14px;
            color: #999;
            margin-right: 2px;
          }
        }
      }

      .technician-status {
        top: 0;
        left: 0;
        min-width: 100px;
        height: 20px;
        padding: 0 7px;
        border-radius: 8px 0 13px 0;
        border: 1px solid #ffffff;
        border-top: transparent;
        border-left: transparent;
        transform: rotateZ(360deg);
        z-index: 2;

        .line {
          width: 1px;
          height: 8px;
        }

        .iconfont {
          font-size: 11px;
          margin-right: 2px;
        }
      }

      .text-type-1 {
        color: #4600e6;
        background: linear-gradient(
          270deg,
          #ae95f9 0%,
          #d9ccff 51%,
          #c8b6ff 100%
        );

        .line {
          background: #4600e6;
        }
      }

      .text-type-2 {
        color: #ff1e48;
        background: linear-gradient(
          270deg,
          #ffa3b4 0%,
          #ffccd5 37%,
          #ffc5cf 100%
        );

        .line {
          background: #ff1e48;
        }
      }

      .text-type-3 {
        color: #ff701e;
        background: linear-gradient(
          270deg,
          #ffcfa3 0%,
          #ffe0cc 43%,
          #ffd0a4 100%
        );

        .line {
          background: #ff701e;
        }
      }

      .text-type-4 {
        color: #333;
        background: linear-gradient(
          270deg,
          #a9a9a9 0%,
          #dcdcdc 43%,
          #c0c0c0 100%
        );

        .line {
          background: #333;
        }
      }
    }
  }
  // 订单
  .order-tablist {
    width: 100%;
    height: 50px;
    .tablist-item {
      .line {
        width: 40px;
        height: 3px;
        border-radius: 3px;
        left: 50%;
        bottom: 0;
        margin-left: -20px;
      }
    }
  }
  .order-item {
    margin: 8px;
    margin-bottom: 0;
    padding: 12px;
    border-radius: 8px;
    .grayscale {
      .c-title,
      .c-warning {
        color: #999;
      }
    }

    .cover {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 8px;
    }
    .order-btn {
      min-width: 60px;
      padding: 0 5px;
      line-height: 22px;
      color: #5b5b5b;
      background: #fff;
      border-radius: 4px;
      border: 1px solid #979797;
      transform: rotateZ(360deg);
      font-size: 10px;
      font-weight: bold;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-left: 10px;
    }
  }
  .tabbar-list {
    width: 100%;
    height: 44px;
    bottom: 0;
    .tabbar-item {
      width: 25%;
      height: 44px;
      .iconfont {
        margin-top: 5px;
      }
      .text {
        font-size: 10px;
        transform: scale(0.8);
      }
    }
  }
}

.module {
  .wizard {
    width: 146px;
    height: 106px;
  }
  .business {
    width: 132px;
    height: 48px;
  }
  .join {
    width: 142px;
    height: 48px;
    margin-left: -10px;
  }
  .wizard-title {
    top: 10px;
    left: 10px;
  }
  .business-title,
  .join-title {
    top: 10px;
    left: 18px;
  }
}
.t-nav {
  height: 45px;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
}
.type-item {
  min-width: 45px;
  height: 26px;
  border-radius: 26px;
  color: #778498;
  border: 1px solid #dddddd;
}
.w-100 {
  width: 100%;
}

.technician-type {
  width: 74px;
  -webkit-transform: rotate(45deg);
  transform: rotate(45deg);
  top: 8px;
  right: -20px;
  text-align: center;
  font-size: 8px;
  padding: 2px 0;
}

.technician-tag {
  height: 16px;
  top: 2px;
  left: 2px;
  border-radius: 16px;
  z-index: 2;
  font-size: 8px;
}

.tag-type-1,
.tag-type-2,
.tag-type-3,
.tag-type-4 {
  color: #fff;
  background: rgba(0, 0, 0, 0.4);
}
</style>
