<!--
 * @Descripttion:
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: wen kun
 * @LastEditTime: 2024-06-20 10:15:23
-->
<template>
  <div>
    <div
      class="flex-center"
      :style="{
        // paddingTop: o.data.style.whiteSpace / 4 + 'px',
        // paddingBottom: o.data.style.whiteSpace / 3 + 'px',
        paddingLeft: o.data.style.wingBlank / 2 + 'px',
        paddingRight: o.data.style.wingBlank / 3 + 'px'
      }"
      v-if="o.data.imagewindowName === 'imagewindow-col-3'"
    >
      <div
        class="img-item"
        style="width: 50%"
        :style="{
          marginTop: o.data.style.whiteSpace / 2 + 'px',
          // marginBottom: o.data.style.whiteSpace / 4 + 'px',
          marginRight: o.data.style.wingBlank / 4 + 'px',
          width: 166 + 'px',
          height: 166 + 'px'
        }"
      >
        <img class="img" :src="o.data.imagewindowList[0].img[0].url" />
      </div>
      <div class="flex-center flex-column pl-md pr-md" style="width: 50%">
        <div v-for="(item, index) in o.data.imagewindowList" :key="index">
          <div v-if="index === 0"></div>
          <div
            v-else
            class="img-item"
            :style="{
              marginTop:
                (index == 2
                  ? o.data.style.whiteSpace / 4
                  : o.data.style.whiteSpace / 2) + 'px',
              // marginBottom: o.data.style.whiteSpace / 4 + 'px',
              width: 166 + 'px',
              height: 158 / 2 + 'px'
            }"
          >
            <img class="img" :src="item.img[0].url" /></div
        ></div>
      </div>
    </div>
    <div
      v-else
      class="img-box"
      :style="{
        // background: o.data.style.backgroundColor,
        paddingLeft: o.data.style.wingBlank / 4 + 'px',
        paddingRight: o.data.style.wingBlank / 4 + 'px'
      }"
    >
      <div
        class="img-item"
        v-for="(item, index) in o.data.imagewindowList"
        :key="index"
        :style="{
          width:
            o.data.imagewindowName === 'imagewindow-col-1'
              ? '100%'
              : 100 / o.data.row.number + '%',
          paddingTop: o.data.style.whiteSpace / 2 + 'px',
          // paddingBottom: o.data.style.whiteSpace / 10 + 'px',
          paddingLeft: o.data.style.wingBlank / 4 + 'px',
          paddingRight: o.data.style.wingBlank / 4 + 'px'
        }"
      >
        <img class="img" :src="item.img[0].url" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'imagewindow',
  props: {
    o: Object
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.img-box {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
}
.img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}
</style>
