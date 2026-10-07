<!--
 * @Descripttion:
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2022-08-11 12:28:27
-->
<!-- 页面底部 -->
<template>
  <div v-html="fHtml" class="lb-footer"></div>
</template>

<script>
export default {
  data () {
    return {
      fHtml: ''
    }
  },
  created () {
  },
  methods: {
  }
}
</script>

<style lang="scss" scoped>
.lb-footer {
  width: 100%;
  height: 49px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-top: 1px solid #ccc;
  color: #ccc;
  font-size: 12px;
  z-index: 10;
  background: #fff;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  margin: auto;
  line-height: 1;
}
</style>
