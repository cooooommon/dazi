<!--
 * @Description:
 * @Author: wen kun
 * @Date: 2023-10-19 16:35:52
 * @LastEditTime: 2024-02-20 15:04:07
 * @LastEditors: wen kun
-->
<template>
  <div class="flex-center pt-md">
    <div class="search-item flex-1 fill-base">
      <div class="iconfont iconsousuo" style="font-size: 14px; margin: 0 auto">
        {{ o.data.placeholder }}
      </div>
    </div>
    <div style="" v-if="o.data.isShowCateAll === true">
      <div
        class="iconfont iconshangpinfenlei pl-md"
        style="font-size: 20px"
      ></div>
      <div class="text-right" style="font-size: 10px">分类</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'search',
  props: {
    o: Object
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.search-item {
  height: 32px;
  border-radius: 16px;
  padding: 0 8px 0 17px;
  display: flex;
  align-items: center;
  line-height: 1;
  font-size: 14px;
  color: #888;
  margin: 0 15px;
}
</style>
