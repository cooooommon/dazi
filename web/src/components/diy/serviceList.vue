<!--
 * @Description: 服务列表
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-17 17:40:13
 * @LastEditTime: 2024-11-26 15:23:38
 * @LastEditors: wen kun
-->

<template>
  <div class="diy-service-list">
    <div class="f-title c-black text-bold pt-md pl-lg">{{ o.data.title }}</div>
    <div
      class="service-item flex-center fill-base rel"
      v-for="(item, index) in serviceList"
      :key="index"
    >
      <img class="cover" :src="item.cover" />
      <div class="flex-1 ml-md" style="width: 239px">
        <div class="flex-between">
          <div
            class="f-paragraph c-title text-bold ellipsis"
            style="max-width: 160px"
          >
            {{ item.title }}
          </div>
          <div class="f-icontext c-caption">
            <i
              class="iconfont iconwode2 icon-font-color"
              style="font-size: 10px"
            ></i>
            {{ item.total_sale }}人选择
          </div>
        </div>
        <div
          class="f-icontext c-caption mt-sm mb-sm ellipsis"
          style="height: 15px"
        >
          {{ item.sub_title || '' }}
        </div>
        <div class="flex-y-center" style="margin-top: 7px">
          <i class="iconfont iconshijian2" :style="{ color: primaryColor }"></i>
          <div class="flex-center" style="padding-left: 2px">
            <span class="f-desc text-bold">{{ item.time_long }}</span>
            <span class="f-icontext">分钟</span>
          </div>
        </div>
        <div class="flex-y-center" style="margin-top: 7px">
          <div class="flex-center">
            <span style="color: #ff2404" class="f-caption">￥</span>
            <div class="flex-y-baseline">
              <span style="color: #ff2404" class="f-title text-bold">{{
                item.price
              }}</span>
              <span style="color: #ff2404" class="f-icontext">起</span>
            </div>
          </div>
          <div class="flex-center mem-price" v-if="routesItem.auth.member">
            <div class="rel mem-price-bg flex-center">
              <div
                class="abs c-base"
                style="padding-right: 2px; font-size: 11px; zoom: 0.8"
              >
                会员价
              </div>
              <img
                class="mem-price-img"
                src="https://lbqny.migugu.com/admin/member/member-bg.png"
                mode="aspectFill"
              />
            </div>
            <div
              class="mem-price-text text-bold f-icontext"
              style="font-size: 11px"
            >
              ¥{{ item.member_price }}
            </div>
          </div>
        </div>
        <div
          class="f-desc c-base item-btn flex-center abs"
          :style="{ backgroundColor: primaryColor }"
        >
          下单
        </div>
        <!-- <div class="flex-y-center f-caption c-caption">
          <i
            class="iconfont iconshijian"
            style="font-size: 12px; margin-right: 2px"
            :style="{ color: primaryColor }"
          ></i
          >{{ item.time_long }}分钟
        </div>
        <div class="flex-between">
          <div class="flex-y-center f-desc c-caption ellipsis">
            <div class="flex-y-baseline f-icontext c-warning">
              <div class="small-text">¥</div>
              <div class="f-title">
                {{ item.price }}
              </div>
            </div>
          </div>
          <div
            class="item-btn flex-center f-caption c-base"
            :style="{ background: primaryColor }"
          >
            <div class="flex-center">选择{{ $t('action.attendantName') }}</div>
          </div>
        </div> -->
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters, mapMutations } from 'vuex'

export default {
  name: 'serviceList',
  props: {
    o: Object,
    primaryColor: String
  },
  data () {
    return {
      serviceList: [{
        title: '酒吧微醺',
        cover: 'https://lbqny.migugu.com/admin/peiwan/diy-01.jpeg',
        price: 128,
        total_sale: 10,
        time_long: 20,
        init_price: 99,
        sub_title: '喝酒游戏陪玩',
        member_price: '108'
      }, {
        title: '电影鉴赏',
        cover: 'https://lbqny.migugu.com/admin/peiwan/diy-02.webp',
        price: 88,
        total_sale: 74,
        time_long: 10,
        init_price: 99,
        sub_title: '多主题电影高品质鉴赏',
        member_price: '68'
      }, {
        title: '酒吧微醺',
        cover: 'https://lbqny.migugu.com/admin/peiwan/diy-01.jpeg',
        price: 68,
        total_sale: 16,
        time_long: 20,
        init_price: 99,
        sub_title: '喝酒游戏陪玩',
        member_price: '48'
      }]
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    }),
    ...mapGetters(['adSwitch'])
  },
}
</script>
<style lang="scss" scoped>
.diy-service-list {
  .service-item {
    margin: 15px;
    margin-bottom: 0;
    padding: 12px;
    border-radius: 8px;
    .cover {
      width: 90px;
      height: 102px;
      border-radius: 8px;
      object-fit: cover;
    }

    .time-long {
      min-width: 36px;
      height: 15px;
      padding: 0 2px;
      background: linear-gradient(270deg, #4c545a 0%, #282b34 100%);
      border-radius: 2px;
      font-size: 10px;
      transform: scale(0.9);
      color: #ffeeb9;
      margin-right: 4px;
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

    // .item-btn {
    //   min-width: 65px;
    //   height: 23px;
    //   border-radius: 4px;
    //   padding: 0 8px;
    // }
    .item-btn {
      width: 60px;
      height: 30px;
      border-radius: 30px;
      right: 10px;
      top: 50%;
      margin-top: 7px;
    }
    .iconwode2 {
      background-image: linear-gradient(#9fa4b6, #bfc5cf);
    }

    .mem-price {
      border: 1px solid #ff2d22;
      border-radius: 4px;
      margin-left: 7px;
      .mem-price-bg {
        margin-left: -1px;
      }
      .mem-price-img {
        width: 36px;
        height: 13px;
      }
      .mem-price-text {
        color: #ff2d22;
        padding: 0 4px;
        line-height: normal;
        zoom: 0.8;
      }
    }
  }
}
</style>
