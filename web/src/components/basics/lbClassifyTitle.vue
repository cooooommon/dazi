<!--
 * @Description: 
 * @Author: xiao li
 * @Date: 2021-07-04 00:16:14
 * @LastEditTime: 2021-07-04 15:50:58
 * @LastEditors: xiao li
-->
<template>
  <div class="lb-goods-edit-classify mb-lg">
    <div class="title">{{ title }}</div>
    <span v-if="tips" class="tips">{{ tips }}</span>
  </div>
</template>

<script>
export default {
  props: ['title', 'tips']
}
</script>

<style lang="scss" scoped>
.lb-goods-edit-classify {
  width: 100%;
  border-top: 1px solid $lineColor;
  display: flex;
  align-items: center;
  .title {
    display: inline-block;
    padding: 6px 15px;
    color: $themeColor;
    background: $columnBgColor;
    margin-right: 10px;
  }
  .tips {
    color: $tipsColor;
    font-size: 12px;
  }
}
</style>
