<!--
 * @Description:
 * @Author: wen kun
 * @Date: 2023-10-19 16:35:52
 * @LastEditTime: 2024-02-20 17:23:45
 * @LastEditors: wen kun
-->
<template>
  <div class="pt-md flex-1 pl-lg pr-lg">
    <div
      class="mb-md radius-15 fill-base pb-lg pt-lg pl-lg pr-lg flex-center"
      v-for="(item, index) in 3"
      :key="index"
    >
      <div class="store-img radius-15 rel">
        <img
          src="https://card-1253902191.cos.ap-chengdu.myqcloud.com/image/666/22/09/e6af165706b2454f973a4f4f0e930303.jpg"
          class="store-img radius-15"
        />
        <span
          class="abs advert c-base flex-center f-ms-little"
          v-if="index == 0"
          :style="{ background: primaryColor }"
          >广告</span
        >
      </div>
      <div class="flex-1 pl-md">
        <div class="flex-between pb-sm">
          <span class="text-bold f-title ellipsis max-340"
            >独家代理红酒易购商城今日大狂欢</span
          >
          <span class="f-desc" :style="{ color: primaryColor }">2.65km</span>
        </div>
        <div class="f-caption" style="color: #5a677e">
          KTV/游戏厅/主播陪玩/桌球/棋牌
        </div>
        <div class="flex-y-center pt-sm">
          <i
            class="iconfont iconjuli1"
            style="
              color: #5a5a5d;
              font-size: 13px;
              margin-right: 2px;
              margin-top: 6rpx;
            "
          ></i>
          <span style="color: #5a5a5d" class="f-caption ellipsis-2"
            >湖南省长沙市长沙县三一路2号33栋101</span
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'search',
  props: {
    o: Object,
    primaryColor: String
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.store-img {
  width: 68px;
  height: 68px;
  overflow: hidden;
  object-fit: cover;
}
.advert {
  width: 27px;
  height: 14px;
  border-radius: 0px 2px 2px 0px;
  top: 0;
  left: 0;
  font-size: 10px;
}
</style>
