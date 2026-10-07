<!--
 * @Description: 订单列表
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-17 17:40:13
 * @LastEditTime: 2023-09-18 15:44:09
 * @LastEditors: DXV-RGWU-TUFH-RFCY-IEGMYY
-->

<template>
  <div class="diy-order-list">
    <div
      class="order-item mt-md ml-md mr-md pd-lg fill-base radius-16"
      v-for="(item, index) in orderList"
      :key="index"
    >
      <div class="flex-between pb-lg b-1px-b">
        <div class="f-paragraph c-title">订单号：{{ item.order_code }}</div>
        <div
          class="f-caption text-bold"
          :style="{
            color:
              item.pay_type == 1
                ? primaryColor
                : [2, 3, 4, 5, 6].includes(item.pay_type)
                ? subColor
                : item.pay_type == 7
                ? '#11C95E'
                : '#333'
          }"
        >
          {{ statusType[item.pay_type] }}
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
            <div class="c-title text-bold ellipsis" style="max-width: 200px">
              {{ aitem.goods_name }}
            </div>
            <div class="f-icontext c-desc">x{{ aitem.num }}</div>
          </div>
          <div
            class="flex-center"
            :style="{
              marginTop: aindex == item.order_goods.length - 1 ? '3px' : '20px'
            }"
          >
            <div class="flex-1 f-caption c-caption" style="margin-bottom: 2px">
              服务{{ $t('action.attendantName') }}：{{
                item.coach_info.coach_name
              }}
            </div>

            <div class="f-caption c-warning ml-sm" v-if="aitem.refund_num > 0">
              已退x{{ aitem.refund_num }}
            </div>
          </div>
          <div class="f-caption c-caption">预约时间：{{ item.start_time }}</div>
          <div
            class="flex-y-center f-caption c-caption"
            v-if="aindex == item.order_goods.length - 1"
          >
            总计：
            <div class="flex-y-baseline c-warning text-bold">
              ¥
              <div class="f-title">{{ item.pay_price }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex-between pt-lg">
        <div></div>
        <div class="flex-warp">
          <!-- 待支付 -->
          <div v-if="item.pay_type == 1">
            <div class="order-btn">取消订单</div>
            <div
              class="order-btn"
              :style="{
                color: '#fff',
                background: primaryColor,
                borderColor: primaryColor
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
          <div class="order-btn" v-if="item.can_refund > 0">申请退款</div>
          <!-- 已完成 -->
          <div class="order-btn" v-if="item.pay_type == 7 && !item.is_comment">
            去评价
          </div>
          <div
            class="order-btn"
            :style="{
              color: '#fff',
              background: primaryColor,
              borderColor: primaryColor
            }"
            v-if="item.pay_type == 7"
          >
            再来一单
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'serviceList',
  props: {
    o: Object,
    primaryColor: String
  },
  data () {
    return {
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
      orderList: [{
        order_code: '20220915181500068600000686',
        pay_type: 7,
        transaction_id: '20220915181500068600000686',
        pay_price: '108.00',
        start_time: '2022-09-17 02:08',
        end_time: '2022-09-17 02:18',
        coach_info: {
          coach_name: '孙梨'
        },
        order_goods: [{
          goods_name: '拔罐祛湿',
          goods_cover: 'https://lbqnyv3.migugu.com/image/666/23/02/ozPjt1ySlDaQhw85OqnEEJtCy3kds3ux.jpg',
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
        pay_price: '375.90',
        start_time: '2022-09-16 00:38',
        end_time: '2022-09-16 01:03',
        coach_info: {
          coach_name: '赵倩'
        },
        order_goods: [{
          goods_name: '拔罐祛湿',
          goods_cover: 'https://lbqnyv3.migugu.com/image/666/23/02/ozPjt1ySlDaQhw85OqnEEJtCy3kds3ux.jpg',
          price: 88,
          num: 1,
          can_refund_num: 1,
          true_price: 88
        }, {
          goods_name: '足疗按摩',
          goods_cover: 'https://lbqnyv3.migugu.com/image/666/23/09/d6j942iDfeqU7GX9g5vfHJV46ffk0xjL.jpeg',
          price: 89,
          num: 3,
          can_refund_num: 3,
          refund_num: 2,
          true_price: 89
        }],
        can_refund: 1
      }]
    }
  }
}
</script>
<style lang="scss" scoped>
.diy-order-list {
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
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }
    .order-btn {
      min-width: 65px;
      padding: 0 8px;
      line-height: 25px;
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
}
</style>
