<!--
 * @Description:
 * @Author: wen kun
 * @Date: 2023-10-19 16:35:52
 * @LastEditTime: 2024-10-24 14:45:27
 * @LastEditors: wen kun
-->
<template>
  <div class="pt-md flex-1 pl-lg pr-lg">
    <div class="flex-between">
      <div
        class="tab-item flex-center flex-1 rel"
        v-for="(item, index) in tabList"
        :key="index"
        :style="{
          background: activeIndex == index ? '#fff' : '#E9E8EF',
          color: activeIndex == index ? primaryColor : '#78777b'
        }"
        :class="[{ 'text-bold': activeIndex == index }]"
      >
        {{ item.title }}
      </div>
    </div>
    <div class="pt-md">
      <div
        class="s-item fill-base flex-between"
        :style="{ marginTop: index == 0 ? '0' : '8px' }"
        v-for="(item, index) in 3"
        :key="index"
      >
        <div class="">
          <div class="item-cover rel">
            <div
              class="f-little flex-center abs item-label c-base"
              v-if="index == 0"
            >
              广告
            </div>
            <img
              src="https://lbqnyv3.migugu.com/image/666/23/12/9598b7d980634c96815f9b5043074d54.jpeg"
              class="item-cover"
            />
          </div>
          <div class="mt-sm flex-y-center">
            <div class="item-slider">
              <div
                class="item-slider-num"
                :style="{ width: `${6.4 * 6}px` }"
              ></div>
            </div>
            <span class="f-little c-78777B">已抢60%</span>
          </div>
        </div>
        <div class="item-cont rel">
          <div class="f-title text-bold">
            【尝鲜】初 · 精品足道+美食小吃不限量
          </div>
          <div class="flex-y-center pt-sm">
            <i class="iconfont icondizhi" :style="{ color: primaryColor }"></i>
            <span class="f-icontext c-78777B">美食美客</span>
            <div class="item-cont-line"></div>
            <span class="f-icontext c-78777B">2.5 km</span>
          </div>
          <div class="item-time flex-y-center f-icontext mt-sm">
            <div class="item-time-left flex-center c-base">仅剩</div>
            <div class="item-time-text pl-sm pr-sm text-bold c-warning">
              1天 12:25:34
            </div>
          </div>
          <div class="abs radius-15 item-btn flex-1 flex-between">
            <div class="flex-y-baseline">
              <span class="f-caption text-bold c-warning">￥</span>
              <span
                class="f-caption text-bold c-warning"
                style="font-size: 23px"
                >220.00</span
              >
              <span class="f-caption c-caption text-delete">￥250.98</span>
            </div>
            <img
              src="https://lbqny.migugu.com/admin/peiwan/seckill-btn-1.png"
              alt=""
              class="item-btn-img"
            />
          </div>
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
  },
  data () {
    return {
      activeIndex: 0,
      tabList: [{ title: '智能排序', id: 1 }, { title: '销量优先', id: 2 }, { title: '距离优先', id: 3 }],
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
.tab-item {
  height: 36px;
  margin-left: 8px;
  border-radius: 10px;
}
.tab-item:nth-child(1) {
  margin-left: 0;
}
.s-item {
  margin-top: 8px;
  padding: 12px;
  border-radius: 10px;
  .item-cover {
    width: 110px;
    height: 110px;
    border-radius: 10px;
    margin-right: 12px;
    object-fit: cover;
  }
  .item-label {
    width: 36px;
    height: 18px;
    border-radius: 10px 0 10px 0;
    left: 0;
    top: 0;
    background-color: #ff8b00;
  }
  .item-slider {
    width: 64px;
    height: 8px;
    margin-right: 4px;
    background: #fff2ed;
    border-radius: 8px;
    .item-slider-num {
      height: 8px;
      border-radius: 8px;
      background: linear-gradient(90deg, #ff8559 0%, #ff3317 100%);
    }
  }
  .item-cont {
    height: 130px;
    .item-cont-line {
      height: 12px;
      width: 1px;
      border: 1px solid #b1b0b2;
      margin: 0 8px;
    }
    .item-time {
      height: 17px;
      background: #fff2ed;
      border-radius: 4px;
      display: inline-flex;
    }
    .item-time-left {
      width: 34px;
      height: 17px;
      border-radius: 4px 0 0 4px;
      background: #ff3317;
    }
    .item-btn {
      height: 32px;
      background-color: #fff2ed;
      bottom: 0;
      width: 100%;
      padding: 0 8px;
    }
    .item-btn-img {
      width: 53px;
      height: 32px;
    }
  }
  .c-78777B {
    color: #78777b;
  }
}
</style>
