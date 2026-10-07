<!--
 * @Descripttion: 头部批注
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2021-04-15 14:21:09
-->
<template>
    <div class="lb-tips" :class="[type]">
        <i class="iconfont" :class="[icon]" v-if="isIcon"></i>
        <div class="custom-item">
          <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
  props: {
    icon: {
      type: String,
      default: 'icon-warn'
    },
    type: {
      type: String,
      default: 'danger'
    },
    isIcon: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style lang="scss" scoped>
  .lb-tips {
    width: 100%;
    padding: 20px;
    border-radius: 4px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-left: 5px solid $primaryColor;
    background: $primaryBgColor;
    i{
      color: $primaryColor;
      margin-right: 10px;
    }
    .custom-item{
      flex: 1;
    }
  }
  .lb-tips.danger {
    border-left: 5px solid $dangerColor;
    background: $dangerBgColor;
    i{
      color: $dangerColor;
    }
  }
</style>
