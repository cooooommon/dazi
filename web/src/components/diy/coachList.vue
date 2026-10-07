<!--
 * @Description: 技-师列表
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-17 17:40:13
 * @LastEditTime: 2023-09-18 16:11:11
 * @LastEditors: DXV-RGWU-TUFH-RFCY-IEGMYY
-->

<template>
  <div class="diy-coach-list">
    <div
      class="coach-item flex-center fill-base rel"
      :style="{ marginTop: index === 0 ? '0' : '' }"
      v-for="(item, index) in coachList"
      :key="index"
    >
      <img
        mode="aspectFill"
        class="king-img abs"
        src="https://lbqny.migugu.com/admin/anmo/mine/king.gif"
        v-if="item.coach_type_status === 1"
      />
      <div class="flex-center flex-column">
        <div class="item-img rel">
          <img class="item-img radius" :src="item.work_img" />
          <img
            class="abs"
            :class="[`${imgType[item.coach_type_status]}-img`]"
            :src="
              item.coach_type_status === 3
                ? `https://lbqny.migugu.com/admin/anmo/mine/${
                    imgType[item.coach_type_status]
                  }.png`
                : `https://lbqny.migugu.com/admin/anmo/mine/${
                    imgType[item.coach_type_status]
                  }.gif`
            "
            v-if="item.coach_type_status"
          />
        </div>
        <div
          class="item-tag flex-center f-icontext c-base radius"
          :style="{
            background:
              item.text_type === 1
                ? sbcolor
                : item.text_type == 3
                ? primaryColor
                : '',
            color:
              item.text_type === 1
                ? sfcolor
                : item.text_type === 3
                ? '#fff'
                : ''
          }"
        >
          <div class="scale-9">
            {{ textType[item.text_type] }}
          </div>
        </div>
      </div>
      <div class="flex-1 ml-md max-510">
        <div class="flex-between">
          <div class="flex-y-center f-desc c-title">
            <div class="text-bold">
              {{ item.coach_name }}
            </div>
            <div
              class="more-img flex-center ml-sm f-caption c-base"
              :style="{
                background: primaryColor
              }"
            >
              <div class="scale-8">更多照片</div>
            </div>
          </div>
          <div class="flex-y-center f-icontext c-caption" v-if="item.near_time">
            <div class="can-service-btn flex-center rel">
              <div
                class="bg abs"
                :style="{
                  background: primaryColor
                }"
              ></div>
              <div
                class="text flex-center abs scale-9"
                :style="{
                  color: primaryColor
                }"
              >
                最早可约 {{ item.near_time }}
              </div>
            </div>
          </div>
        </div>
        <div class="flex-between mt-sm mb-md pb-md b-1px-b">
          <div class="flex-y-center f-icontext">
            <div class="flex-y-center">
              <i class="iconfont iconyduixingxingshixin icon-font-color"></i>
              <div class="star-text">{{ item.star }}</div>
            </div>
            <div class="order-num flex-y-center scale-9">
              已服务
              <div class="f-desc text-bold">
                {{ item.order_num > 9999 ? '9999+' : item.order_num }}
              </div>
              单
            </div>
          </div>

          <div class="flex-center">
            <i class="iconfont iconjuli" :style="{ color: primaryColor }"></i>
            <div class="f-icontext c-title">{{ item.distance }}</div>
          </div>
        </div>
        <div class="flex-between">
          <div class="flex-y-center f-desc c-caption">
            <div class="flex-y-center scale-9">
              <i class="iconfont iconpinglun mr-sm"></i>{{ item.comment_num }}
            </div>
            <div class="flex-y-center scale-9 ml-sm">
              <i
                class="iconfont mr-sm"
                :class="[
                  { iconshoucang1: !item.is_collect },
                  { iconshoucang2: item.is_collect }
                ]"
                :style="{
                  color: item.is_collect ? primaryColor : ''
                }"
              ></i
              >{{ item.collect_num }}
            </div>
            <div class="flex-y-center scale-9 ml-sm">
              <i class="iconfont iconxiangqing mr-sm"></i>
              <div class="f-icontext">详情</div>
            </div>
          </div>

          <div
            class="item-btn flex-center f-caption c-base"
            :style="{
              background: item.user_id ? primaryColor : '#888'
            }"
          >
            立即预约
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'coachList',
  props: {
    o: Object,
    primaryColor: String,
    sfcolor: String,
    sbcolor: String
  },
  data () {
    return {
      imgType: {
        1: 'top',
        2: 'hot',
        3: 'new',
        4: 'recommend'
      },
      textType: {
        1: '可服务',
        2: '服务中',
        3: '可预约'
      },
      coachList: [{
        user_id: 1,
        coach_name: '赵倩',
        work_img: 'https://lbqny.migugu.com/admin/anmo/pc/technician1.png',
        is_work: 1,
        star: 5,
        distance: '600m',
        comment_num: 55,
        collect_num: 101,
        order_num: 120,
        is_collect: 1,
        near_time: '11:00',
        text_type: 1,
        coach_type_status: 1
      }, {
        user_id: 1,
        coach_name: '孙梨',
        work_img: 'https://lbqny.migugu.com/admin/anmo/pc/technician2.png',
        is_work: 1,
        star: 4,
        distance: '1.88km',
        comment_num: 10,
        collect_num: 20,
        order_num: 18,
        is_collect: 0,
        near_time: '13:00',
        text_type: 2,
        coach_type_status: 2
      }, {
        user_id: 1,
        coach_name: '周舞',
        work_img: 'https://lbqny.migugu.com/admin/anmo/pc/technician3.png',
        is_work: 0,
        star: 4.5,
        distance: '3.99km',
        comment_num: 2,
        collect_num: 3,
        order_num: 2,
        is_collect: 0,
        near_time: '14:00',
        text_type: 3,
        coach_type_status: 3
      }, {
        user_id: 1,
        coach_name: '郑望',
        work_img: 'https://lbqnyv3.migugu.com/image/666/23/02/89FqhbuQ6h02aDlUGW3gjBnaSoElQ14S.jpg',
        is_work: 0,
        star: 4.9,
        distance: '5.12km',
        comment_num: 12,
        collect_num: 18,
        order_num: 14,
        is_collect: 0,
        near_time: '15:30',
        text_type: 3,
        coach_type_status: 4
      }]
    }
  }
}
</script>
<style lang="scss" scoped>
.diy-coach-list {
  .coach-item {
    margin: 8px;
    margin-bottom: 0;
    padding: 12px;
    border-radius: 8px;
    .top-tag {
      width: 18px;
      height: 13px;
      color: #b75e1d;
      background: linear-gradient(90deg, #dfb885 0%, #fce0ad 100%);
      border-radius: 3px 0 3px 0;
      top: 0;
      left: 0;
      .text {
        transform: scale(0.8);
      }
    }

    .item-img {
      width: 58px;
      height: 58px;
      object-fit: cover;
    }

    .king-img {
      width: 30px;
      height: 30px;
      top: 58px;
      left: 3px;
      z-index: 1;
    }

    .top-img {
      width: 82px;
      height: 82px;
      top: -15px;
      left: -11px;
    }

    .hot-img {
      width: 40px;
      height: 40px;
      top: 34px;
      left: 10px;
    }

    .new-img {
      width: 19px;
      height: 26px;
      top: 41px;
      left: 39px;
    }

    .recommend-img {
      width: 85px;
      height: 85px;
      top: 14px;
      left: -12px;
    }

    .item-tag {
      width: 50px;
      height: 16px;
      color: #000;
      background: rgba(216, 216, 216, 0.3);
      margin-top: 9px;
      margin-bottom: 5px;
    }
    .more-img {
      height: 16px;
      border-radius: 4px;
    }

    .can-service-btn {
      width: 80px;
      height: 17px;
      border-radius: 3px;

      .bg {
        width: 80px;
        height: 17px;
        opacity: 0.1;
        border-radius: 3px;
        top: 0;
        left: 0;
        z-index: 1;
      }

      .text {
        width: 86px;
        height: 17px;
        top: 0;
        left: -3px;
        z-index: 2;
      }
    }
    .iconyduixingxingshixin {
      font-size: 14px;
      background-image: -webkit-linear-gradient(
        270deg,
        #fad961 0%,
        #f76b1c 100%
      );
    }

    .star-text {
      color: #ff9519;
      margin-left: 3px;
    }

    .order-num {
      color: #4d4d4d;
      margin-left: 3px;
    }

    .item-btn {
      width: 60px;
      height: 23px;
      border-radius: 4px;
    }
  }
}
</style>
