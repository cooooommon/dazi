<!--
 * @Description: tabBar
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-16 17:05:00
 * @LastEditTime: 2023-08-21 18:01:26
 * @LastEditors: DXV-RGWU-TUFH-RFCY-IEGMYY
-->
<template>
  <div class="diy-tabbar-list">
    <div class="tabbar-box flex-center" :style="{ background: '#ffffff' }">
      <div
        v-for="(item, index) in tabBar"
        :key="index"
        class="list-item flex-center flex-column"
        :style="{
          color:
            item.id == nowPageKey
              ? selectedColor
              : item.default_img == item.selected_img
              ? '#BCCAD9'
              : '#666',
          width: 100 / tabBar.length + '%'
        }"
        @click="change(item.id)"
      >
        <i
          :class="[
            'item-icon',
            'iconfont',
            item.id == nowPageKey ? item.selected_img : item.default_img
          ]"
        ></i>
        <div class="item-text">
          {{ item.name }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'tabbar',
  props: {
    tabBar: Array,
    nowPageKey: Number,
    selectedColor: String
  },
  methods: {
    change (key) {
      this.$emit('change', { key })
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
.diy-tabbar-list {
  .tabbar-box {
    background: white;
    font-size: 11px;
    border-top: 1px solid #f3f3f3;
    height: 56px;
    width: 100%;
    .list-item {
      .item-icon {
        font-size: 20px;
        line-height: 20px;
        margin-bottom: 1px;
      }

      .item-text {
        font-size: 11px;
        line-height: 1.5;
      }
    }
  }
}
</style>
