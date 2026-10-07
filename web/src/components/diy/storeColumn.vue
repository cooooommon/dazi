<!--
 * @Description:
 * @Author: wen kun
 * @Date: 2023-10-19 16:35:52
 * @LastEditTime: 2024-10-17 18:44:23
 * @LastEditors: wen kun
-->
<template>
  <div class="flex-center pt-md">
    <div class="fill-base pt-sm flex-warp flex-1 column-box pb-lg ml-lg mr-lg">
      <div
        class="mt-md"
        :class="[
          { 'column-item-4': colNum == 4 },
          { 'column-item-5': colNum == 5 }
        ]"
        v-for="(item, index) in colNum == 4 ? 8 : 10"
        :key="index"
      >
        <img src="https://lbqny.migugu.com/admin/diy/default.png" alt="" />
        <div class="flex-center">分类</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'search',
  props: {
    o: Object,
    colNum: Number
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.column-box {
  border-radius: 12px;
  padding-left: 10px;
}
.column-item-4 {
  padding: 0 15px;
}
.column-item-5 {
  padding: 0 7px;
}
.column-box img {
  height: 50px;
  width: 50px;
  object-fit: cover;
}
</style>
