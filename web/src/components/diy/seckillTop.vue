<!--
 * @Description:
 * @Author: wen kun
 * @Date: 2023-10-19 16:35:52
 * @LastEditTime: 2024-10-18 18:17:14
 * @LastEditors: wen kun
-->
<template>
  <div class="flex-between pl-lg pr-lg pt-md seck-top">
    <img
      src="https://lbqny.migugu.com/admin/peiwan/seckill-text.png"
      mode="aspectFill"
      class="seckill-icon"
    />
    <div class="flex-center pl-lg">
      <i class="iconfont icondizhi" :style="{ color: primaryColor }"></i>
      <!--icondizhi5 -->
      <span class="f-paragraph text-bold" style="line-height: 1"
        >易上创客中心</span
      >
      <i class="iconfont icon-right text-bold" style="font-size: 12px"></i>
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

    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
.seckill-icon {
  width: 92px;
  height: 26px;
}
</style>
