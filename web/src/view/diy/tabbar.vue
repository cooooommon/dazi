<!--
 * @Description: tabbar
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-17 16:13:46
 * @LastEditTime: 2024-12-02 14:54:13
 * @LastEditors: wen kun
-->
<template>
  <!-- 左侧 -->
  <div v-loading="!subForm.id" style="height: calc(100vh - 70px - 50px - 90px)">
    <div class="diy-content" v-if="subForm.id">
      <div class="diy-left" v-loading="componentsLoading">
        <div v-for="(item, index) in componentsList" :key="index">
          <div class="diy-left_title">{{ item.title }}</div>
          <div class="diy-left_btn_list">
            <div
              v-for="(citem, cindex) in item.data"
              :key="cindex"
              size="mini"
              class="diy-left_btn"
              @click="add(citem, item.type)"
            >
              <i
                class="iconfont diy-left_icon"
                :class="citem.icon || citem.iconPath"
              ></i>
              <div>{{ citem.title }}</div>
            </div>
          </div>
        </div>
        <div
          class="pd-lg"
          v-if="!componentsLoading && componentsList.length <= 0"
        >
          没有可用组件,该页面暂不能DIY,点击底部菜单切换到其他页面
        </div>
        <div class="pd-lg f-icontext c-warning" v-if="tabActiveKey === 1">
          <div class="f-desc">1、若需要DIY，请点击上方组件添加并设置</div>
          <div class="f-desc mt-sm">2、若未DIY组件，则不显示。</div>
          <div class="mt-md c-title">
            <div>
              <span class="c-warning mr-sm">新版系统</span>“服务管理 -
              轮播图设置”里面设置的数据<span class="c-warning">将</span
              >变为服务分类里的轮播图
            </div>
            <!-- <div class="mt-sm">
              <span class="c-link mr-sm">[推荐向导]</span>显示“服务管理 -
              服务分类”里面设置的数据，默认最多展示30条数据；若“{{
                $t('action.attendantName')
              }}管理 - {{ $t('action.attendantName') }}设置 ”里面的“{{
                $t('action.attendantName')
              }}入驻”开启，那么服务分类数量大于4，则展示在第5个位置，否则将展示在服务分类末尾
            </div> -->
          </div>
        </div>
      </div>
      <!-- 中间部分 -->
      <div class="diy-center">
        <div class="diy-title">{{ title }}</div>
        <div class="diy-phone">
          <nav-bar
            :tabActiveKey="tabActiveKey"
            :tabBar="subForm.tabbar"
          ></nav-bar>
          <div class="drag-box" ref="drag">
            <vuedraggable
              v-model="subForm.page[tabActiveKey]"
              :draggable="true"
            >
              <!--秒杀顶部 固定-->
              <seckillTop v-if="tabActiveKey == 9"></seckillTop>
              <transition-group>
                <div
                  v-for="(item, index) in subForm.page[tabActiveKey]"
                  :key="`drag_${index}`"
                  class="drag-item"
                  :class="[{ 'drag-item--select': index == activeIndex }]"
                  @click="select(item.id)"
                  @mouseover="hover(item.id)"
                >
                  <!-- 搜索 -->
                  <search
                    v-if="['search'].includes(item.type)"
                    :o="item"
                  ></search>
                  <!-- 轮播图 -->
                  <banner
                    v-if="['banner'].includes(item.type)"
                    :o="item"
                  ></banner>
                  <!-- 分类图 -->
                  <!-- <column v-if="item.type == 'column'" :o="item"></column> -->
                  <!-- 广告 -->
                  <imagewindow
                    v-if="item.type == 'imagewindow'"
                    :o="item"
                  ></imagewindow>
                  <!-- 推荐向导 -->
                  <recommend
                    v-if="
                      ['column', 'service-column'].includes(item.type) &&
                      tabActiveKey == 1
                    "
                    :o="item"
                    :primaryColor="subForm.selectedColor"
                  ></recommend>
                  <!-- 入驻/招商 -->
                  <settle
                    v-if="['settle', 'service-settle'].includes(item.type)"
                    :primaryColor="subForm.selectedColor"
                    :storeplus="routesItem.auth.storeplus"
                  ></settle>
                  <!-- 明星向导 -->
                  <wizardList
                    v-if="['star', 'wizard-list'].includes(item.type)"
                    :o="item"
                    :primaryColor="subForm.selectedColor"
                  ></wizardList>
                  <!-- 服务列表 -->
                  <service-list
                    v-if="['service-list', 'newfangled'].includes(item.type)"
                    :primaryColor="subForm.selectedColor"
                    :o="item"
                  ></service-list>
                  <!-- 门店分类 -->
                  <storeColumn
                    v-if="
                      ['column'].includes(item.type) &&
                      (tabActiveKey == 8 || tabActiveKey == 9)
                    "
                    :primaryColor="subForm.selectedColor"
                    :colNum="tabActiveKey == 8 ? 4 : 5"
                  ></storeColumn>
                  <!-- 门店列表 -->
                  <storeList
                    v-if="['list'].includes(item.type)"
                    :primaryColor="subForm.selectedColor"
                  ></storeList>
                  <!-- 秒杀列表 -->
                  <seckillList
                    v-if="['seckill'].includes(item.type)"
                    :primaryColor="subForm.selectedColor"
                  ></seckillList>

                  <!-- 操作栏 -->
                  <div class="op-area" v-if="hoverId === item.id">
                    <!-- 删除 -->
                    <div
                      class="op-area-item"
                      @click="opdel(index)"
                      v-if="item.isDelete"
                    >
                      <i
                        class="iconfont icon-add"
                        style="
                          font-size: 15px;
                          transform: rotate(45deg);
                          margin-top: -1px;
                        "
                      ></i>
                    </div>
                    <!-- 上移 moveHandle(index, 'opup') && tabActiveKey == 8 &&
                        item.type -->
                    <div
                      class="op-area-item"
                      @click="opup(index)"
                      v-if="index > 0"
                    >
                      <i
                        class="iconfont icon-left"
                        style="
                          font-size: 15px;
                          transform: rotate(90deg);
                          margin-top: -1px;
                        "
                      ></i>
                    </div>
                    <!-- 下移 moveHandle(index, 'opdown') &&
                        tabActiveKey == 8 &&
                        item.type -->
                    <div
                      class="op-area-item"
                      @click="opdown(index)"
                      v-if="index < subForm.page[tabActiveKey].length - 1"
                    >
                      <i
                        class="iconfont icon-left"
                        style="font-size: 15px; transform: rotate(270deg)"
                      ></i>
                    </div>
                  </div>
                </div>
              </transition-group>
            </vuedraggable>
            <div class="space-xl"></div>
          </div>

          <tab-bar
            :tabBar="subForm.tabbar"
            :nowPageKey="tabActiveKey"
            :selectedColor="subForm.selectedColor"
            @change="tabChange"
            :class="{ 'drag-item--select': 'tabbar' == activeId }"
          ></tab-bar>
        </div>
      </div>
      <!-- 右侧 -->
      <div class="diy-right">
        <!-- 底部菜单 -->
        <div v-if="activeId && activeIndex > -1">
          <div class="diy-right-title">
            <div class="diy-right_line"></div>
            <div>{{ subForm.page[tabActiveKey][activeIndex].title }}</div>
          </div>
          <div class="diy-right-content">
            <!-- 属性组件 -->
            <diy-setting
              :o="subForm.page[tabActiveKey][activeIndex]"
              :ckey="tabActiveKey"
              :activeIndex="activeIndex"
              :activeId="activeId"
              :componentsList="componentsList"
            ></diy-setting>
            <div class="space-xxl"></div>
          </div>
        </div>

        <div v-if="activeId == 'tabbar'">
          <div class="diy-right-title">
            <div class="diy-right_line"></div>
            <div>导航菜单</div>
          </div>
          <div class="diy-right-content">
            <tabbar-setting
              :o="subForm"
              :all-tab-bar="allTabBar"
              :nowPageKey="tabActiveKey"
              @del="ondelTabbar"
              @input="changeInput"
            ></tabbar-setting>
          </div>
        </div>
      </div>
    </div>
    <!-- diy底部 -->
    <div
      class="diy-foot"
      v-if="subForm.id"
      style="bottom: 0px"
      :style="{ right: adSwitch ? '242px' : '22px' }"
    >
      <!-- <el-button @click="goBack">取消</el-button> -->
      <el-button type="danger" :plain="true" @click="submitFormInfo(1)">{{
        $t('action.defaultSet')
      }}</el-button>
      <el-button
        style="margin-left: 20px"
        type="primary"
        @click="submitFormInfo(2)"
        >保存设置</el-button
      >
    </div>
  </div>
</template>

<script>
import vuedraggable from 'vuedraggable'
import navBar from '@/components/diy/navBar.vue'
import tabBar from '@/components/diy/tabBar.vue'
import tabbarSetting from '@/components/diy/tabbarSetting.vue'
import diySetting from '@/components/diy/diySetting.vue'

import search from '@/components/diy/search.vue'
import banner from '@/components/diy/banner.vue'
import column from '@/components/diy/column.vue'
import imagewindow from '@/components/diy/imagewindow.vue'
import settle from '@/components/diy/settle.vue'
import recommend from '@/components/diy/recommend.vue'
import storeColumn from '@/components/diy/storeColumn.vue' // 门店分类
import storeList from '@/components/diy/storeList.vue' // 门店列表
import wizardList from '@/components/diy/wizardList.vue' // 明星向导
import seckillList from '@/components/diy/seckillList.vue'
import seckillTop from '@/components/diy/seckillTop.vue'

import serviceColumn from '@/components/diy/serviceColumn.vue'
import serviceList from '@/components/diy/serviceList.vue' // 新奇玩法

import { mapState, mapGetters, mapMutations } from 'vuex'
import Uuid from 'uuid'
export default {
  name: 'diy',
  components: {
    vuedraggable,
    navBar,
    tabBar,
    tabbarSetting,
    diySetting,
    banner,
    column,
    imagewindow,
    serviceColumn,
    serviceList,
    search,
    settle,
    recommend,
    wizardList,
    storeColumn,
    storeList,
    seckillList,
    seckillTop
  },
  props: {},
  data () {
    return {
      title: '',
      default_params: {
        page: {
          1: [],
          2: [],
          8: [],
          5: []
        },
        tabbar: [{
          id: 1,
          name: '首页',
          default_img: 'iconshouye2',
          selected_img: 'iconshouye12'
        }, {
          id: 2,
          name: this.$t('action.attendantName'),
          default_img: 'iconpeiwanguan1',
          selected_img: 'iconpeiwanguan2'
        },
        {
          id: 8,
          name: '门店',
          default_img: 'iconmendian11',
          selected_img: 'iconmendian1'
        },
        {
          id: 5,
          name: '我的',
          default_img: 'iconwode-2',
          selected_img: 'iconwode-1'
        }]
      },
      searchModule: { title: '搜索', type: 'search', icon: 'iconsousuo', isDelete: true, addNumber: 1, attr: [], data: { addMouduleName: 'searchList', searchList: [], placeholder: `搜索${this.$t('action.attendantName')}昵称` } },
      bannerModule: { title: '轮播图', type: 'banner', icon: 'iconlunbo', isDelete: true, addNumber: 1, attr: [{ title: '选择模板', type: 'ChooseModule', name: 'bannerName', data: [{ title: '通屏轮播', name: 'banner-tongping', img: 'https://lbqny.migugu.com/admin/diy/banner-tongping.png' }] }, { title: '图片列表', type: 'ImageLink', name: 'bannerList', isDraggable: true, isDelete: true, data: [{ title: '图片', type: 'UploadImage', name: 'img', desc: '100*100' }, { title: '链接类型', type: 'Select', name: 'linkType', data: [{ label: '系统内部页面', value: 4 }, { label: '其他小程序', value: 2 }, { label: '跳转网页', value: 3 }, { label: '拨打电话', value: 1 }, { label: '跳转视频', value: 6 }, { label: '查看大图', value: 5 }] }, { title: '链接地址', type: 'Tag', name: 'link' }] }, { title: '添加模板', type: 'Add', name: 'addMouduleName', addNumber: 10, data: [{ link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }] }], data: { style: { width: 750, number: 400, min: 200, max: 800, label: '请输入', height: 400, whiteSpace: 0, wingBlank: 0 }, bannerName: 'banner-tongping', isShowFilter: true, addMouduleName: 'bannerList', bannerList: [{ link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }] } },
      // columnModule: { title: '推荐向导', type: 'column', icon: 'icondaohang', isDelete: true, addNumber: 1, attr: [{ title: '多少行', type: 'InputNumber', name: 'row' }, { title: '每行多少列', type: 'InputNumber', name: 'col' }, { title: '图片列表', type: 'ImageLink', name: 'columnList', isDraggable: true, isDelete: true, data: [{ title: '按钮', type: 'Input', name: 'title' }, { title: '图片', type: 'UploadImage', name: 'img', desc: '100*100' }, { title: '链接类型', type: 'Select', name: 'linkType', data: [{ label: '系统内部页面', value: 4 }, { label: '其他小程序', value: 2 }, { label: '跳转网页', value: 3 }, { label: '拨打电话', value: 1 }, { label: '查看大图', value: 5 }] }, { title: '链接地址', type: 'Tag', name: 'link' }] }, { title: '添加模板', type: 'Add', name: 'addMouduleName', addNumber: 30, data: [{ link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }] }], data: { row: { number: 2, min: 1, max: 2 }, col: { number: 4, min: 4, max: 5 }, style: { fontColor: '#666', background: '#ffffff', whiteSpace: 30, wingBlank: 0 }, addMouduleName: 'columnList', columnList: [{ title: '按钮', link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { title: '按钮', link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { title: '按钮', link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { title: '按钮', link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { title: '按钮', link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { title: '按钮', link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { title: '按钮', link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { title: '按钮', link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }] } },
      settleModule: { title: '入驻/招商', type: 'settle', icon: 'iconwoyaoruzhu', isDelete: true, addNumber: 1, attr: [], data: { addMouduleName: 'settleList', settleList: [] } },
      newfangledModule: { title: '新奇玩法', type: 'newfangled', icon: 'icondizhiguanli', isDelete: true, addNumber: 1, attr: [{ maxLength: 10, name: 'title', title: '模板名称', type: 'Input' }], data: { title: `新奇玩法`, type: 1, addMouduleName: 'newfangledList', newfangledList: [] } },
      starModule: { title: '明星' + this.$t('action.attendantName'), type: 'star', icon: 'iconpeiwanguan1', isDelete: true, addNumber: 1, attr: [{ maxLength: 10, name: 'title', title: '模板名称', type: 'Input' }], data: { title: `明星${this.$t('action.attendantName')}`, type: 1, addMouduleName: 'starList', starList: [] } },
      columnModule: { title: '推荐' + this.$t('action.attendantName'), type: 'column', icon: 'icondaohang', isDelete: true, addNumber: 1, attr: [{ maxLength: 10, name: 'title', title: '模板名称', type: 'Input' }], data: { title: `推荐${this.$t('action.attendantName')}`, type: 1, addMouduleName: 'columnList', columnList: [] } },
      imagewindow: { title: '广告图', type: 'imagewindow', icon: 'iconguanggaotu', isDelete: true, addNumber: 999, attr: [{ title: '选择模板', type: 'ChooseModule', name: 'imagewindowName', data: [{ title: '1*1', name: "imagewindow-col-1", img: "http:\/\/lbqny.migugu.com\/admin\/diy\/imagewindow-col-1.png" }, { title: "1*2", name: "imagewindow-col-2", img: "http:\/\/lbqny.migugu.com\/admin\/diy\/imagewindow-col-2.png" }, { title: "1*3", name: 'imagewindow-col-3', img: "http:\/\/lbqny.migugu.com\/admin\/diy\/imagewindow-col-3.png" }] }, { title: "图片列表", type: "ImageLink", name: "imagewindowList", isDraggable: true, isDelete: true, data: [{ title: "图片", type: "UploadImage", name: "img", desc: "333*152" }, { title: "链接类型", type: "Select", name: "linkType", data: [{ label: "系统内部页面", value: 4 }, { label: "其他小程序", value: 2 }, { label: "跳转网页", value: 3 }, { label: "拨打电话", value: 1 }] }, { title: "链接地址", type: "Tag", name: "link" }] }, { title: "添加模板", type: "Add", name: "addMouduleName", addNumber: 10, data: [{ link: [{ title: "" }], linkType: 4, img: [{ url: "http:\/\/lbqny.migugu.com\/admin\/diy\/default.png" }] }] }], data: { row: { number: 2 }, style: { wingBlank: 30, whiteSpace: 30, backgroundColor: "#fff" }, imagewindowName: "imagewindow-col-1", addMouduleName: "imagewindowList", imagewindowList: [{ link: [{ title: "" }], linkType: 4, img: [{ "url": "http:\/\/lbqny.migugu.com\/admin\/diy\/default.png" }] }, { link: [{ title: "" }], linkType: 4, img: [{ url: "http:\/\/lbqny.migugu.com\/admin\/diy\/default.png" }] }, { link: [{ title: "" }], linkType: 4, img: [{ url: "http:\/\/lbqny.migugu.com\/admin\/diy\/default.png" }] }] } },

      storeColumnModule: { title: '门店分类', type: 'column', icon: 'icondaohang', isDelete: true, addNumber: 1, attr: [], data: { title: `推荐${this.$t('action.attendantName')}`, type: 1, addMouduleName: 'columnList', columnList: [] } },
      storeListModule: { title: '门店列表', type: 'list', icon: 'iconliebiao', isDelete: true, addNumber: 1, attr: [], data: { addMouduleName: 'storeList', storeList: [] } },
      storeBannerModule: { title: '轮播图', type: 'banner', icon: 'iconlunbo', isDelete: true, addNumber: 1, attr: [{ title: '选择模板', type: 'ChooseModule', name: 'bannerName', data: [{ title: '通屏轮播', name: 'banner-tongping', img: 'https://lbqny.migugu.com/admin/diy/banner-tongping.png' }] }, { title: '图片列表', type: 'ImageLink', name: 'bannerList', isDraggable: true, isDelete: true, data: [{ title: '图片', type: 'UploadImage', name: 'img', desc: '100*100' }, { title: '链接类型', type: 'Select', name: 'linkType', data: [{ label: '系统内部页面', value: 4 }, { label: '跳转网页', value: 3 }, { label: '拨打电话', value: 1 }, { label: '查看大图', value: 5 }] }, { title: '链接地址', type: 'Tag', name: 'link' }] }, { title: '添加模板', type: 'Add', name: 'addMouduleName', addNumber: 10, data: [{ link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }] }], data: { style: { width: 710, number: 300, min: 200, max: 800, label: '请输入', height: 400, whiteSpace: 0, wingBlank: 0 }, bannerName: 'banner-tongping', isShowFilter: true, addMouduleName: 'bannerList', bannerList: [{ link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }] } },

      // 秒杀
      seckillBannerModule: { title: '轮播图', type: 'banner', icon: 'iconlunbo', isDelete: true, addNumber: 1, attr: [{ title: '选择模板', type: 'ChooseModule', name: 'bannerName', data: [{ title: '通屏轮播', name: 'banner-tongping', img: 'https://lbqny.migugu.com/admin/diy/banner-tongping.png' }] }, { title: '图片列表', type: 'ImageLink', name: 'bannerList', isDraggable: true, isDelete: true, data: [{ title: '图片', type: 'UploadImage', name: 'img', desc: '100*100' }, { title: '链接类型', type: 'Select', name: 'linkType', data: [{ label: '系统内部页面', value: 4 }, { label: '其他小程序', value: 2 }, { label: '跳转网页', value: 3 }, { label: '拨打电话', value: 1 }, { label: '跳转视频', value: 6 }, { label: '查看大图', value: 5 }] }, { title: '链接地址', type: 'Tag', name: 'link' }] }, { title: '添加模板', type: 'Add', name: 'addMouduleName', addNumber: 10, data: [{ link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }] }], data: { style: { width: 710, number: 400, min: 200, max: 800, label: '请输入', height: 400, whiteSpace: 0, wingBlank: 0 }, bannerName: 'banner-tongping', isShowFilter: true, addMouduleName: 'bannerList', bannerList: [{ link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }, { link: [{ title: '' }], linkType: 4, img: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }] }] } },
      seckillColumnModule: { title: '分类', type: 'column', icon: 'icondaohang', isDelete: true, addNumber: 1, attr: [], data: { title: `推荐${this.$t('action.attendantName')}`, type: 1, addMouduleName: 'columnList', columnList: [] } },
      seckillListModule: { title: '套餐列表', type: 'seckill', icon: 'iconliebiao', isDelete: true, addNumber: 1, attr: [], data: { addMouduleName: 'seckillList', seckillList: [] } },

      storeSearchModule: { id: 'store-search-1', compontents: 'base', title: '搜索', type: 'search', icon: 'iconsousuo', isDelete: true, addNumber: 1, attr: [], data: { title: '搜索', placeholder: `请输入场地名称` } },
      storeListModuleData: { id: 'store-list-1', compontents: 'base', title: '门店列表', type: 'list', icon: 'iconliebiao', isDelete: true, addNumber: 1, attr: [], data: { title: '门店列表' } },
      storeColumnModuleData: { id: 'store-column-1', compontents: 'base', title: '门店分类', type: 'column', icon: 'icondaohang', isDelete: true, addNumber: 1, attr: [], data: { title: '门店分类', columnList: [] } },

      serviceSearchModule: { id: 'service-search-1', compontents: 'base', title: '搜索', type: 'search', icon: 'iconsousuo', isDelete: true, addNumber: 1, attr: [], data: { title: '搜索', placeholder: `搜索${this.$t('action.attendantName')}昵称` } },
      serviceBannerModule: { id: 'service-banner-1', compontents: 'base', title: '轮播图', type: 'service-banner', icon: 'iconrenwu', isDelete: true, addNumber: 1, attr: [], data: { title: '轮播图', style: { number: 400, min: 200, max: 800, label: '请输入', height: 400, whiteSpace: 0, wingBlank: 0 }, isShowFilter: true, bannerList: [{ img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/05/coLq1uUiDPgsYSUJrcX6qQfMGt87Cg7K.jpg' }] }, { img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/LJey4EdOPAPXMTCsreDECr62DuJEq7LY.jpg' }] }] } },
      serviceColumnModule: { id: 'service-column-1', compontents: 'base', title: '推荐' + this.$t('action.attendantName'), type: 'column', icon: 'iconrenwu', isDelete: true, addNumber: 1, attr: [], data: { title: '推荐' + this.$t('action.attendantName'), columnList: [] } },
      serviceSettleModule: { id: 'service-settle-1', compontents: 'base', title: '入驻/招商', type: 'settle', icon: 'iconsousuo', isDelete: true, addNumber: 1, attr: [], data: { title: '入驻/招商' } },
      serviceListModule: { id: 'service-list-1', compontents: 'base', title: '新奇玩法', type: 'newfangled', icon: 'iconrenwu', isDelete: true, addNumber: 1, attr: [], data: { title: '新奇玩法' } },
      serviceWizardModule: { id: 'wizard-list-1', compontents: 'base', title: '明星' + this.$t('action.attendantName'), type: 'star', icon: 'iconrenwu', isDelete: true, addNumber: 1, attr: [], data: { title: '明星' + this.$t('action.attendantName') } },
      componentsLoading: false,
      componentsList: [],
      subForm: {},
      allTabBar: {},
      activeId: '',
      hoverId: '',
      activeIndex: -1,
      tabActiveKey: -1,
      diyTitleObj: {
        1: '首页',
        2: this.$t('action.attendantName'),
        3: '动态',
        5: '我的',
        7: '发现',
        8: '门店',
        9: '秒杀'
      }
    }
  },
  async created () {
    this.initIndex()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    }),
    ...mapGetters(['adSwitch'])
  },
  methods: {
    ...mapMutations(['changeRoutesItem']),
    async initIndex () {
      let [info, all, config] = await Promise.all([this.$api.diy.diyInfo(), this.$api.diy.getTabbar(), await this.$api.system.configInfo()])
      if (!this.routesItem.auth.storeplus) {
        let storeplusInd = info.data.tabbar.findIndex(item => {
          return item.id == 8 // '门店'
        })
        if (storeplusInd !== -1) {
          info.data.tabbar.splice(storeplusInd, 1)
        }
        let allInd = all.data.findIndex(item => {
          return item.id == 8 // '门店'
        })
        if (allInd !== -1) {
          all.data.splice(allInd, 1)
        }
      }
      // info.data.page[8] = info.data.page[8].map(item => { return item !== '' })
      // if (info.data.page[8]) {
      //   let arr = info.data.page[8].filter(item => {
      //     return ['banner', 'column', 'search', 'list'].includes(item.type)
      //   })
      //   info.data.page[8] = arr
      // }

      if (info.data.page[1]) {
        let bannerInd = -1
        let sBannerInd = -1
        let columnInd = -1
        let searchInd = -1
        let settleInd = -1
        let listInd = -1
        info.data.page[1].map((item, index) => {
          if (item.type === 'banner') {
            bannerInd = index
            if (!item.data.hasOwnProperty('isShowFilter')) {
              info.data.page[1][index].data.isShowFilter = true
            }
          }
          if (item.type === 'service-banner') {
            sBannerInd = index
          }
          if (item.type === 'column' && this.routesItem.auth.recommend) {
            columnInd = index
          }
          if (item.type === 'search') {
            searchInd = index
          }
          if (item.type === 'settle') {
            settleInd = index
          }
          if ((item.type === 'star' && this.routesItem.auth.recommend) || item.type === 'newfangled') {
            listInd = index
          }
        })
        let ssdata = JSON.parse(JSON.stringify(this.serviceSearchModule))
        let sbdata = sBannerInd === -1 ? JSON.parse(JSON.stringify(this.serviceBannerModule)) : info.data.page[1][sBannerInd]
        let scdata = this.routesItem.auth.recommend ? JSON.parse(JSON.stringify(this.serviceColumnModule)) : ''
        let sldata = JSON.parse(JSON.stringify(this.serviceListModule))
        let sedata = JSON.parse(JSON.stringify(this.serviceSettleModule))
        // let swdata = JSON.parse(JSON.stringify(this.serviceWizardModule))
        this.default_params.page[1] = [ssdata, sedata, sldata] // scdata, // JSON.parse(JSON.stringify(this.serviceBannerModule)),

        let _ssdata = JSON.parse(JSON.stringify(this.storeSearchModule))
        let slist = JSON.parse(JSON.stringify(this.storeListModuleData))
        let scolumn = JSON.parse(JSON.stringify(this.storeColumnModuleData))
        this.default_params.page[8] = [_ssdata, '', scolumn, slist]

        let moduleArr = [
          searchInd === -1 ? '' : info.data.page[1][searchInd],
          bannerInd === -1 ? '' : info.data.page[1][bannerInd],
          columnInd === -1 ? '' : info.data.page[1][columnInd],
          settleInd === -1 ? '' : info.data.page[1][settleInd],
          listInd === -1 ? '' : info.data.page[1][listInd]]
        const newArr = moduleArr.filter((element) => element !== '')
        // info.data.page[1] = newArr
      }
      let typeArr = ['column', 'star']
      info.data.page[1].forEach(item => {
        if (typeArr.includes(item.type)) {
          item.title = item.title.substring(0, 2) + config.data.attendant_name
          item.data.title = item.data.title || (item.title.substring(0, 2) + config.data.attendant_name)
        }
        if (item.type === 'newfangled') {
          item.data.title = '新奇玩法'
        }
      })
      this.subForm = info.data
      this.allTabBar = all.data
      let { tabbar } = info.data
      this.tabActiveKey = tabbar[0].id
      this.activeId = 'tabbar'
      await this.getComponentsList()
    },
    // 根据底部菜单key获取组件列表
    getComponentsList () {
      let search = JSON.parse(JSON.stringify(this.searchModule))
      let banner = JSON.parse(JSON.stringify(this.bannerModule))
      let storeBanner = JSON.parse(JSON.stringify(this.storeBannerModule))
      let seckillBannerModule = JSON.parse(JSON.stringify(this.seckillBannerModule))
      let column = JSON.parse(JSON.stringify(this.columnModule))
      if (this.tabActiveKey === 8) {
        column = JSON.parse(JSON.stringify(this.storeColumnModule))
      } else if (this.tabActiveKey === 9) { // 秒杀
        column = JSON.parse(JSON.stringify(this.seckillColumnModule))
      }
      let settle = JSON.parse(JSON.stringify(this.settleModule))
      let newfangled = JSON.parse(JSON.stringify(this.newfangledModule))
      let star = JSON.parse(JSON.stringify(this.starModule))
      let list = JSON.parse(JSON.stringify(this.storeListModule))
      let seckill = JSON.parse(JSON.stringify(this.seckillListModule))
      let imagewindow = JSON.parse(JSON.stringify(this.imagewindow))
      this.componentsList = this.tabActiveKey === 1 ? this.routesItem.auth.recommend ? [{ title: '基础组件', type: 'base', data: [banner, column, search, settle, newfangled, star, imagewindow] }] : [{ title: '基础组件', type: 'base', data: [banner, search, settle, newfangled, imagewindow] }] : []
      // 门店基础组件
      if (this.tabActiveKey === 8) {
        search.data.placeholder = '请输入场地名称'
        this.componentsList = [{ title: '基础组件', type: 'base', data: [storeBanner, column, search, list] }]
      }
      if (this.tabActiveKey === 9) { // 秒杀
        search.data.placeholder = '搜索套餐名称'
        this.componentsList = [{ title: '基础组件', type: 'base', data: [seckillBannerModule, seckill] }]
      }
      // this.componentsList = this.tabActiveKey === 8 ? [{ title: '基础组件', type: 'base', data: [storeBanner, column, search, list] }] : this.componentsList
    },
    // 添加组件
    async add (item, compontents) {
      let pageAdd = 0
      if (!this.subForm.page[this.tabActiveKey]) {
        this.subForm.page[this.tabActiveKey] = []
      }
      if (this.subForm.page[this.tabActiveKey].length > 0) {
        this.subForm.page[this.tabActiveKey].map(citem => {
          if (citem.type === item.type) {
            pageAdd++
          }
        })
      }
      if (item.addNumber === 0 || item.addNumber === pageAdd) {
        this.$message.error(`不能添加${item.title}了`)
        return
      }
      let aitem = JSON.parse(JSON.stringify(item))
      let componentsList = JSON.parse(JSON.stringify(this.componentsList))
      componentsList.map(bitem => {
        if (bitem.type === compontents) {
          bitem.data.map(citem => {
            if (citem.type === aitem.type) {
              aitem.data[aitem.data.addMouduleName] = citem.data[citem.data.addMouduleName]
            }
          })
        }
      })
      let resList = JSON.parse(JSON.stringify(this.subForm.page[this.tabActiveKey]))
      console.log(resList, 0, this.subForm.page, 11111111111111)

      let id = +new Date() + resList.length + 1
      aitem.id = id
      aitem.compontents = compontents
      // resList.push(aitem)
      // 指定位置添加推荐向导
      // if (item.type === 'banner') {
      //   let { isShowFilter } = resList[0] ? resList[0].data : { isShowFilter: false }
      //   aitem.data.isShowFilter = isShowFilter
      // }
      // let spliceIndObj = {
      //   banner: 1,
      //   column: 2,
      //   search: 0,
      //   settle: 3,
      //   newfangled: 4,
      //   star: 4,
      //   list: 3
      // }
      // let spliceInd = spliceIndObj[item.type]
      // if (resList[spliceInd]) {
      //   resList.splice(spliceInd, 1, aitem)
      // } else {
      //   resList[spliceInd] = aitem
      // }
      // resList[spliceInd] = aitem
      let newfangled = resList.findIndex(item => {
        return item.type === 'newfangled'
      })
      let star = resList.findIndex(item => {
        return item.type === 'star'
      })
      if (newfangled !== -1 && item.type === 'star') {
        resList.splice(newfangled, 1, aitem)
      } else if (star !== -1 && item.type === 'newfangled') {
        resList.splice(star, 1, aitem)
      } else {
        resList.splice(resList.length, 0, aitem)
      }
      this.subForm.page[this.tabActiveKey] = resList
      let data = JSON.parse(JSON.stringify(this.subForm.page[this.tabActiveKey]))
      this.activeId = id
      this.activeIndex = data.findIndex(current => {
        return current.id === id
      })
      // 添加组件完滚动到底部
      this.$nextTick(() => {
        let divDom = this.$refs.drag
        divDom.scrollTop = divDom.scrollHeight
      })
    },
    // 选中
    async select (id) {
      this.activeId = id
      this.activeIndex = this.subForm.page[this.tabActiveKey] && this.subForm.page[this.tabActiveKey].findIndex(item => {
        return item.id === this.activeId
      })
      if (id === 'tabbar') return
      let { tabActiveKey, activeIndex } = this
      if (this.subForm.page[tabActiveKey].length < 1) return
      let { attr, compontents, type } = this.subForm.page[tabActiveKey][activeIndex]
      let componentsList = JSON.parse(JSON.stringify(this.componentsList))
      let comIndex = componentsList.findIndex(item => {
        return item.type === compontents
      })
      // let comTypeArr = type === 'service-banner' ? [this.serviceBannerModule] : componentsList[comIndex].data.filter(item => {
      //   return item.type === type
      // })
      let comTypeArr = componentsList[comIndex].data.filter(item => {
        return item.type === type
      })
      let comArr = comTypeArr && comTypeArr.length > 0 ? comTypeArr[0].attr : []
      if (type === 'imagewindow') {
        let addInd = attr.findIndex(item => {
          return item.type === 'Add'
        })
        let { addNumber } = attr[addInd]
        console.log(attr, addNumber)
        comArr[addInd].addNumber = addNumber
      }
      this.subForm.page[tabActiveKey][activeIndex].attr = comArr
    },
    hover (id) {
      this.hoverId = id
    },
    // tabBar点击事件
    tabChange (e) {
      let { key } = e
      this.tabActiveKey = key
      this.changeRoutesItem({ key: 'diyKey', val: key })
      this.select('tabbar')
      this.getComponentsList()
    },
    // 交换组件
    swapItems (arr, index1, index2) {
      arr[index1] = arr.splice(index2, 1, arr[index1])[0]
      return arr
    },
    // 删除
    opdel (index) {
      let item = this.subForm.page[this.tabActiveKey][index]
      let moduleKey = {
        banner: [1, 'serviceBannerModule'],
        column: [2, 'serviceColumnModule'],
        search: [0, 'serviceSearchModule']
      }
      // if (['banner'].includes(item.type) && this.tabActiveKey === 1) {
      //   let additems = JSON.parse(JSON.stringify(this[moduleKey[item.type][1]]))
      //   // if (item.type === 'banner') {
      //   //   let { isShowFilter } = this.subForm.page[this.tabActiveKey][0].data
      //   //   additems.data.isShowFilter = isShowFilter
      //   // }
      //   this.subForm.page[this.tabActiveKey].splice([moduleKey[item.type][0]], 1, additems)
      // } else {
      //   this.subForm.page[this.tabActiveKey].splice(index, 1, '')
      // }
      this.subForm.page[this.tabActiveKey].splice(index, 1)
      if (item.id === this.activeId) {
        this.activeId = ''
      }
    },
    // 上移
    opup (index) {
      this.subForm.page[this.tabActiveKey] = this.swapItems(
        this.subForm.page[this.tabActiveKey],
        index,
        index - 1
      )
    },
    // 下移
    opdown (index) {
      this.subForm.page[this.tabActiveKey] = this.swapItems(
        this.subForm.page[this.tabActiveKey],
        index,
        index + 1
      )
    },
    // 删除
    async ondelTabbar (e) {
      let { key, index } = e
      this.subForm.tabbar.splice(index, 1)
      delete this.subForm.page[key]
      // 操作的等于选中的
      if (key === this.tabActiveKey) {
        let { id: firstKey } = this.subForm.tabbar[index - 1]
        this.tabActiveKey = firstKey
        this.changeRoutesItem({ key: 'diyKey', val: firstKey })
        this.getComponentsList()
      }
    },
    changeInput (val, active) {
    },
    /**
     * @method 返回上一页
     */
    goBack () {
      this.$route.meta.refresh = true
      this.$router.back(-1)
    },
    moveHandle (index, type) {
      // if (this.tabActiveKey === 8) {
      let item = this.subForm.page[this.tabActiveKey]
      let flag = true
      let number = 0
      if (type === 'opup') { // 上移
        item.forEach((element, eindex) => {
          if (element && !number) {
            number = eindex
            if (number && index === eindex) {
              flag = false
            }
          }
        })
        if (!index) {
          flag = false
        }
      } else {
        let arr = []
        item.forEach((element, eindex) => {
          if (element) {
            arr.push(eindex)
          }
        })
        if (arr[arr.length - 1] === index) {
          flag = false
        }
      }
      return flag
      // }
    },
    /**
     * @name: paramType 1默认；2编辑
     */
    async submitFormInfo (paramType) {
      let param = paramType === 1 ? this.default_params : JSON.parse(JSON.stringify(this.$util.pick(this.subForm, ['page', 'tabbar'])))

      if (paramType === 2) {
        for (let i in param.tabbar) {
          let { name } = param.tabbar[i]
          if (!name.replace(/(^\s*)|(\s*$)/g, '')) {
            this.$message.error(`推荐${this.$t('action.attendantName')}菜单 第${i * 1 + 1}条数据 请输入菜单名称！`)
            return
          }
        }

        // 模块名称必输入
        let typeArr = ['column', 'star', 'newfangled']
        for (let i = 0; i < param.page[1].length; i++) {
          if (typeArr.includes(param.page[1][i].type) && param.page[1][i].data.title !== undefined && !param.page[1][i].data.title.replace(/(^\s*)|(\s*$)/g, '')) {
            this.$message.error(`请输入${param.page[1][i].title}模板名称`)
            return
          }
        }

        if (param.page[1] && param.page[1].length > 0) {
          let arr = param.page[1].filter(item => {
            return ['banner', 'column', 'search', 'settle', 'newfangled', 'star', 'imagewindow'].includes(item.type)
          })
          param.page[1] = arr
        }

        let titleArr = this.diyTitleObj
        let regArr = {
          1: { title: '电话号码', reg: /((^400)-([0-9]{7})$)|(^1[3-9]\d{9}$)|((^0\d{2,3})-(\d{7,8})$)/ },
          2: { title: '小程序appid', reg: /^wx(?=.*\d)/ },
          3: { title: '网页链接', reg: /http[s]{0,1}:\/\/([\w.]+\/?)\S*/ }
        }

        let moduleType = ['banner', 'column', 'imagewindow']
        let mouduleArr = []

        let titleMsg = false
        for (let tKey in titleArr) {
          let item = tKey
          if (param.page[item] && param.page[item].length > 0) {
            param.page[item].map(aitem => {
              if (moduleType.includes(aitem.type)) {
                aitem.data[`${aitem.type}List`].map((bitem, bindex) => {
                  bitem.module_title = aitem.title
                  bitem.ind = bindex * 1 + 1
                })
                let mArr = aitem.data[`${aitem.type}List`]
                mouduleArr = mouduleArr.concat(mArr)
              }
            })

            if (mouduleArr.length > 0) {
              for (let mKey in mouduleArr) {
                let { title, linkType, link, ind, module_title: mTitle } = mouduleArr[mKey]
                if (mTitle === '推荐' + this.$t('action.attendantName') && !title.replace(/(^\s*)|(\s*$)/g, '')) {
                  titleMsg = true
                  this.$message.error(`${titleArr[tKey]} ${mTitle} 第${ind}条数据 请输入按钮文字 `)
                  return
                }
                if (linkType === 4 && !link[0].url) {
                  titleMsg = true
                  this.$message.error(`${titleArr[tKey]} ${mTitle} 第${ind}条数据 请选择系统内部页面 `)
                  return
                }
                if (linkType === 6 && !link[0].url) {
                  titleMsg = true
                  this.$message.error(`${titleArr[tKey]} ${mTitle} 第${ind}条数据 请选择跳转视频 `)
                  return
                }
                if (![4, 5, 6].includes(linkType)) {
                  let { title, reg } = regArr[linkType]
                  if (!reg.test(link[0].url)) {
                    titleMsg = true
                    this.$message.error(`${titleArr[tKey]} ${mTitle} 第${ind}条数据 链接地址 ${link[0].url} 无效 请输入正确的${title} `)
                    return
                  }
                }
              }
            }
          }
        }
        if (titleMsg) return
      } else {
        // this.activeId = 'tabbar'
        this.activeIndex = -1
      }
      // 秒杀
      let ind = param.tabbar.findIndex(item => {
        return item.id === 9
      })
      if (ind !== -1 && !param.page[9]) {
        param.page[9] = []
      }
      let sind = param.tabbar.findIndex(item => {
        return item.id === 8
      })
      if (sind !== -1 && !param.page[8]) {
        param.page[8] = []
      }

      await this.$api.diy.diyUpdate(param)

      this.$message.success(this.$t('tips.successOper'))
      if (paramType !== 1) return
      this.initIndex()
    }
  }
}
</script>
<style scoped>
.diy-content {
  display: flex;
  justify-content: space-between;
  width: 100%;
  background: #f4f6f8;
  height: calc(100vh - 70px - 110px);
  position: relative;
}
.diy-foot {
  position: fixed;
  left: 300px;
  bottom: 0;
  box-sizing: border-box;
  height: 90px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 360px 0 272px;
  background: #fff;
  z-index: 10;
  border-top: 1px solid #eeeeee;
}

.diy-left,
.diy-right {
  width: 272px;
  border: 1px solid #eeeeee;
  height: calc(100vh - 70px - 110px);
  overflow-x: hidden;
  overflow-y: scroll;
  z-index: 9;
  background: #fff;
  border-bottom: none;
}

.diy-right {
  width: 360px;
}

.diy-right::-webkit-scrollbar,
.diy-left::-webkit-scrollbar {
  /*滚动条整体样式*/
  width: 0;
  /*高宽分别对应横竖滚动条的尺寸*/
  height: 0;
}

.diy-left_btn_list {
  display: flex;
  flex-wrap: wrap;
  color: #7d7e80;
  font-size: 12px;
}

.diy-left_btn {
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
  width: 90px;
  height: 90px;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.diy-left_btn_list .diy-left_btn:not(:nth-child(3n)) {
  border-right: 1px solid #eee;
}

.diy-left_btn_list .diy-left_btn:not(:nth-child(-n + 3)) {
  border-top: none;
}

.diy-left_title {
  height: 60px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666666;
  font-size: 16px;
}

.diy-left_icon {
  font-size: 26px;
  margin-bottom: 5px;
}

.diy-right-title {
  width: 358px;
  height: 70px;
  font-size: 16px;
  display: flex;
  align-items: center;
  /* margin: 10px; */
  /* padding: 0 10px; */
  background: #f4f6f9;
  border: 10px solid white;
  line-height: 1;
  position: absolute;
  top: 1px;
  z-index: 22;
}

.diy-right_line {
  height: 18px;
  width: 4px;
  border-radius: 2px;
  background: #429dff;
  margin-right: 10px;
}

.diy-right-content {
  padding: 70px 10px 0 10px;
}
.diy-center {
  display: flex;
  align-items: center;
  flex-direction: column;
}

.diy-phone {
  width: 375px;
  border-radius: 3px;
  box-shadow: 0 3px 10px #dcdcdc;
  border: 1px solid #ddd;
  flex: 1;
  /* max-height: 677px; */
  min-height: calc(100vh - 70px - 110px - 60px);
  max-height: calc(100vh - 70px - 110px - 60px);
  /* min-height: calc(100vh - 70px - 110px - 120px);
  max-height: calc(100vh - 70px - 110px - 120px); */
  overflow: auto;
  background: #efeff5;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}
.diy-title {
  font-size: 16px;
  text-align: center;
  margin-bottom: 10px;
}

.fiexd {
  height: 80px;
  position: fixed;
  background-color: rgb(255, 255, 255);
  box-shadow: rgb(240, 240, 240) 1px -2px 8px;
  bottom: 0px;
  left: 300px;
  z-index: 5;
  display: flex;
  padding-left: 100px;
  align-items: center;
}
</style>
