<!--
 * @Descripttion: 批注
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2021-05-12 09:28:32
-->
<template>
  <div>
    <div
      class="lb-tool-tips"
      :class="[type]"
      v-if="mode === 'text'"
    >
      <slot></slot>
    </div>
      <el-tooltip
        class="tool-tips"
        effect="dark"
        placement="right"
        :style="{paddingTop:`${padding}px`}"
        v-if="mode === 'tooltip'"
      >
        <div
          class="content"
          slot="content"
        ><slot></slot></div>
        <i class="el-icon-question"></i>
      </el-tooltip>
  </div>
</template>

<script>
export default {
  props: {
    mode: {
      type: String,
      default: 'tooltip'
    },
    type: {
      type: String,
      default: 'c-caption'
    },
    padding: {
      type: [Number, String],
      default: 10
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-tool-tips {
  font-size: 12px;
  line-height: 1.6;
  padding-top: 10px;
}
.lb-tool-tips.c-link{
  cursor: pointer;
}
.tool-tips {
  margin-left: 5px;
  vertical-align: top;
  font-size: 18px;
  color: #333;
  .content {
    max-width: 300px;
  }
  .el-icon-question {
    cursor: pointer;
  }
}
</style>
