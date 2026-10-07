<!--
 * @Description:
 * @Author: wen kun
 * @Date: 2023-10-19 16:35:52
 * @LastEditTime: 2024-04-17 11:03:05
 * @LastEditors: wen kun
-->
<template>
  <div class="pt-md">
    <div class="radius-15 fill-base pd-md" style="margin: 0 15px">
      <div class="flex-between pb-lg">
        <div class="f-paragraph c-black text-bold">
          {{ o.data.title }}
        </div>
        <div class="flex-y-center f-caption c-caption">
          查看更多<i class="iconfont icon-right" style="font-size: 24rpx"></i>
        </div>
      </div>
      <div class="flex-y-center">
        <div
          @tap.stop="toTechnician(index)"
          class="recommend-item type-1 pd-md"
          v-for="(item, index) in list"
          :key="index"
        >
          <div class="flex-center pb-sm">
            <img
              mode="aspectFill"
              lazy-load
              class="cover radius"
              :src="item.work_img"
            />
            <div class="flex-1 ml-sm">
              <div class="f-desc ellipsis">{{ item.coach_name }}</div>
              <div class="flex-y-baseline" style="margin-top: 4rpx">
                <i
                  class="iconfont iconyduixingxingshixin icon-font-color iconpingfen1"
                ></i>
                <div class="star-text flex-y-center f-caption">
                  {{ item.star }}
                </div>
              </div>
            </div>
          </div>
          <div class="flex-center">
            <div
              class="new-technician flex-center f-icontext"
              :style="{
                color: primaryColor,
                border: `1rpx solid ${primaryColor}`
              }"
              v-if="item.is_new"
            >
              新人
            </div>
            <div class="f-icontext c-caption" v-else>
              30天接单{{ item.order_count || 0 }}
            </div>
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
      list: [
        {
          coach_name: "666--男",
          id: 812,
          is_new: 0,
          order_count: 5,
          star: 5,
          work_img: "https://lbqnyv3.migugu.com/image/666/23/08/mSvn4TlBPvCCN2c2C9VNs7JO2h0gnxqO.jpeg"
        }, {
          coach_name: "刘星-女",
          id: 211,
          is_new: 0,
          order_count: 0,
          star: 4,
          work_img: "https://longbing.oss-cn-beijing.aliyuncs.com/testing/image/666/23/02/392797589c784d84a1ac43ab0322c537.jpeg"
        }
      ]
    }
  },
  mounted () {
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
.recommend-item {
  display: inline-block;
}

.cover {
  width: 35px;
  height: 35px;
  border-radius: 35px;
}
.ellipsis {
  max-width: 60px;
}
.iconpingfen1 {
  font-size: 10px;
  background-image: -webkit-linear-gradient(270deg, #fad961 0%, #f76b1c 100%);
}
.star-text {
  height: 13px;
  color: #ff9519;
  margin-left: 3px;
}
.new-tag {
  width: 33px;
  height: 15px;
  transform: rotateZ(360deg);
  border-radius: 2px;
}
.new-text {
  height: 15px;
  color: #050505;
}
.recommend-item.type-1 {
  width: 112px;
  height: 75rpx;
  border-radius: 6px;
  margin-left: 10px;
}
.recommend-item:nth-child(1) {
  margin-left: 0;
}

.recommend-item.type-1:nth-child(3n-2) {
  background: linear-gradient(139deg, #daf4ff 0%, #e4f1ff 100%);
}

.recommend-item.type-1:nth-child(3n-1) {
  background: linear-gradient(139deg, #f0daff 0%, #ffe4e4 100%);
}

.recommend-item.type-1:nth-child(3n) {
  background: linear-gradient(139deg, #ffebda 0%, #ffe4e4 100%);
}
</style>
