<!--
 * @Description: 轮播图
 * @Author: xiao li
 * @Date: 2021-07-04 00:16:14
 * @LastEditTime: 2024-02-20 15:04:19
 * @LastEditors: wen kun
-->
<template>
  <div
    class="diy-banner rel mt-md"
    :style="{
      paddingTop: o.data.style.whiteSpace / 2 + 'px',
      paddingBottom: o.data.style.whiteSpace / 2 + 'px',
      paddingLeft: o.data.style.wingBlank / 2 + 'px',
      paddingRight: o.data.style.wingBlank / 2 + 'px'
    }"
  >
    <el-carousel
      :height="
        o.data.style.number
          ? o.data.style.number / 2 + 'px'
          : o.data.style.height / 2 + 'px'
      "
      indicator-position="none"
    >
      <el-carousel-item v-for="(item, index) in o.data.bannerList" :key="index">
        <img class="banner-img" :src="item.img[0].url" />
      </el-carousel-item>
    </el-carousel>
    <!-- <div
      class="tab-list flex-warp fill-base radius abs"
      v-if="o.data.isShowFilter"
    >
      <div
        class="tab-item flex-center c-title rel"
        v-for="(item, index) in tabList"
        :key="index"
      >
        {{ item.title }}
        <div
          class="tab-item-sanjao abs"
          :style="{ right: item.title.length === 3 ? '-8px' : '' }"
          v-if="item.is_sign"
        >
          <i class="up iconfont icon-up-fill rel"></i>
          <i class="down iconfont icon-down-fill rel"></i>
        </div>
      </div>
    </div> -->
  </div>
</template>
<script>
export default {
  name: 'banner',
  props: {
    o: Object
  },
  data () {
    return {
      tabList: [{
        title: '全部',
        sort: 'top desc'
      }, {
        title: '价格',
        sort: 'price',
        sign: 0,
        is_sign: 1
      }, {
        title: '销量',
        sort: 'total_sale',
        sign: 0,
        is_sign: 1
      }, {
        title: '好评度',
        sort: 'star',
        sign: 0,
        is_sign: 1
      }]
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
.diy-banner {
  width: 100%;
  .banner-img {
    width: calc(100% - 30px);
    height: 100%;
    object-fit: cover;
    margin: 0 15px;
    border-radius: 16px;
  }
  .tab-list {
    width: calc(100% - 16px);
    margin-left: 8px;
    margin-right: 8px;
    bottom: 0;
    padding-right: 20px;
    z-index: 9;
    .tab-item {
      width: 25%;
      height: 40px;
      padding: 0 10px;
      .tab-item-sanjao {
        top: 6px;
        right: -3px;
        width: 15px;
        height: 25px;
        -webkit-transform: scale(0.6);
        transform: scale(0.6);
        .iconfont {
          font-size: 10px;
          color: #ccc;
        }

        .up {
          top: 0;
          left: 0;
        }

        .down {
          bottom: 12px;
          left: 0;
        }
      }
    }
  }
}
</style>
