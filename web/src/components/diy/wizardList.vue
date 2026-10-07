<!--
 * @Description: 服务列表
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-17 17:40:13
 * @LastEditTime: 2024-04-17 11:29:06
 * @LastEditors: wen kun
-->

<template>
  <div class="diy-service-list">
    <div class="f-title c-black text-bold pt-md pl-lg">
      {{ o.data.title }}
    </div>
    <div class="flex technician-list mt-md">
      <div
        class="technician-list-item"
        v-for="(info, index) in serviceList"
        :key="index"
        :class="[{ 'ml-md': index % 2 }]"
      >
        <div
          class="list-item fill-base radius-16 box-shadow"
          @tap.stop="toEmit('info')"
        >
          <div class="work-img rel">
            <img mode="aspectFill" class="work-img" :src="info.work_img" />
            <div
              class="abs technician-type"
              :class="[`text-type-${info.text_type}`]"
            >
              {{ textType[info.text_type] }}
            </div>
            <div
              class="abs technician-tag flex-center f-icontext c-base pl-sm pr-sm"
              :class="[`tag-type-${info.text_type}`]"
              v-if="info.tag_name"
            >
              {{ info.tag_name }}
            </div>

            <div class="abs content-box pl-md pr-md c-base pb-md">
              <div class="text-bold ellipsis f-paragraph max-270">
                {{ info.coach_name }}
              </div>
              <!-- <view class="f-caption c-caption pt-sm ellipsis">身高{{info.height}}·体重{{info.weight}}·{{info.constellation}}</view> -->
              <div class="flex-y-center pt-sm">
                <div
                  class="user-info-item flex-center f-ms-little"
                  style="color: #dcbbff; border: 1px solid #dcbbff"
                >
                  {{ info.height }}cm
                </div>
                <div
                  class="user-info-item flex-center ml-sm f-ms-little"
                  style="color: #ffb395; border: 1px solid #ffb395"
                >
                  {{ info.weight }}kg
                </div>
                <div
                  class="user-info-item flex-center ml-sm f-ms-little"
                  style="color: #9dfcdc; border: 1px solid #9dfcdc"
                >
                  {{ info.constellation }}
                </div>
              </div>
              <div class="f-icontext pt-sm ellipsis max-270">
                {{ info.text }}
              </div>
              <div class="flex-between pt-sm">
                <div class="flex-center">
                  <i
                    class="icondizhi2 iconfont"
                    style="color: #fff; font-size: 12px"
                  ></i>
                  <span class="f-ms-little" style="padding-left: 6rpx">{{
                    info.distance
                  }}</span>
                </div>
                <div class="flex-y-baseline">
                  <span style="color: #e94a48" class="f-ms-little">￥</span>
                  <span style="color: #e94a48" class="f-title text-bold">{{
                    info.price | handlePrice
                  }}</span>
                  <span class="f-ms-little" style="color: #e94a48">/单</span>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="content-info">
            <div class="c-black text-bold ellipsis f-title max-300">
              {{ info.coach_name }}
            </div>
            <div class="f-caption c-caption pt-sm ellipsis">
              身高{{ info.height }}·体重{{ info.weight }}·{{
                info.constellation
              }}
            </div>
            <div class="f-caption c-caption pt-sm ellipsis max-300">
              {{ info.text }}
            </div>
            <div class="flex-between pt-sm">
              <div class="flex-center">
                <span style="color: #e94a48" class="f-paragraph">￥</span>
                <span style="color: #e94a48" class="f-md-title text-bold">{{
                  info.price
                }}</span>
                <span class="f-caption" style="color: #9ba2aa">/单</span>
              </div>
              <div class="flex-center">
                <i class="icondizhi iconfont" style="color: #dddddd"></i>
                <span class="f-caption c-icontext" style="padding-left: 6rpx">{{
                  info.distance
                }}</span>
              </div>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'wizardList',
  props: {
    o: Object,
    primaryColor: String
  },
  data () {
    return {
      serviceList: [{
        age: 24,
        coach_name: '殊荣',
        constellation: '巨蟹座',
        distance: '17.35km',
        height: '160.0',
        price: '1',
        star: 5,
        tag_name: '肤白貌美1',
        text: '阿鲨环球网',
        text_type: 3,
        weight: '48.00',
        work_img: 'https://lbqnyv3.migugu.com/image/666/23/07/5296dbf3f69a4da18f7acd0496f9caa7.png'
      }, {
        age: 24,
        coach_name: '殊荣',
        constellation: '巨蟹座',
        distance: '17.35km',
        height: '160.0',
        price: '1',
        star: 5,
        tag_name: '肤白貌美1',
        text: '阿鲨环球网',
        text_type: 1,
        weight: '48.00',
        work_img: 'https://lbqnyv3.migugu.com/image/666/23/07/5296dbf3f69a4da18f7acd0496f9caa7.png'
      }],
      textType: {
        1: '可接单',
        2: '接单中',
        3: '休息中',
        4: '不可接单'
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.technician-list {
  padding: 0 15px;
}
.technician-list-item {
  .list-item {
    width: 167px;
    border-radius: 8px 8px 0 0;
    .work-img {
      width: 167px;
      height: 274px;
      border-radius: 8px 8px 0 0;
      overflow: hidden;
      object-fit: cover;

      .near-time-order {
        width: 167px;
        height: 17px;
        padding: 0 8px;
        font-size: 10px;
        background: rgba(0, 0, 0, 0.64);
        left: 0;
        bottom: 0;
        z-index: 2;
      }
    }

    .content-info {
      padding: 9px 7px;
      border-bottom-left-radius: 8px;
      border-bottom-right-radius: 8px;
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
          width: 75px;
          height: 26px;
          border-radius: 4px;
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
    .technician-type {
      width: 80px;
      -webkit-transform: rotate(45deg);
      transform: rotate(45deg);
      top: 10px;
      right: -20px;
      text-align: center;
      font-size: 10px;
      padding: 2px 0;
    }
    .technician-status {
      top: -1px;
      left: 0;
      min-width: 103px;
      height: 20px;
      padding: 0 7px;
      border-radius: 8px 0 13px 0;
      border: 1rpx solid #ffffff;
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

    .technician-tag {
      height: 20px;
      top: 9px;
      left: 9px;
      border-radius: 20px;
      z-index: 2;
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

    .tag-type-1,
    .tag-type-2,
    .tag-type-3,
    .tag-type-4 {
      color: #fff;
      background: rgba(0, 0, 0, 0.4);
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

  .content-box {
    padding-top: 25px;
    width: 167px;
    left: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, #000000 100%);
    .user-info-item {
      min-width: 36px;
      height: 15px;
      border-radius: 15px;
    }
    .f-ms-little {
      font-size: 9px;
    }
    .f-icontext {
      font-size: 11px;
    }
  }
}
</style>
