<!--
 * @Description: 
 * @Author: xiao li
 * @Date: 2022-08-02 19:03:28
 * @LastEditTime: 2023-12-25 16:25:22
 * @LastEditors: wen kun
-->
<template>
  <div
    style="margin: 8px 8px 0 8px; border-radius: 10px"
    :style="{
      background: o.data.style.background,
      paddingTop: o.data.style.whiteSpace / 2 + 'px',
      paddingBottom: o.data.style.whiteSpace / 2 + 'px',
      paddingLeft: o.data.style.wingBlank / 2 + 'px',
      paddingRight: o.data.style.wingBlank / 2 + 'px'
    }"
  >
    <el-carousel
      :height="o.data.row.number === 1 ? '70px' : '150px'"
      indicator-position="none"
    >
      <el-carousel-item v-for="(item, index) in formatList" :key="index">
        <div class="column-box">
          <div
            class="column-item"
            v-for="(bitem, bindex) in item"
            :key="bindex"
            :style="{
              width: 100 / o.data.col.number + '%',
              color: o.data.style.fontColor,
              marginTop: bindex < o.data.col.number ? '0px' : '10px'
            }"
          >
            <img class="column-img radius" :src="bitem.img[0].url" />
            <div class="column-text" :style="{ color: o.data.style.fontColor }">
              {{ bitem.title }}
            </div>
          </div>
        </div>
      </el-carousel-item>
    </el-carousel>
  </div>
</template>

<script>
export default {
  name: 'column',
  props: {
    o: Object
  },
  data () {
    return {
      columnList: { title: '导航', data: { row: { number: 2, min: 1, max: 2 }, col: { number: 4, min: 4, max: 5 }, style: { fontColor: '#666', background: '#fff', whiteSpace: 30, wingBlank: 0 }, columnList: [{ title: '合作加盟', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/ox4e61dKfe5cqbWb4534942XgFev4VTi.png' }] }, { title: this.$t('action.attendantName') + '入驻', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/IZWJSYAlyjhEunHgKj5Xl2zg1DtVze56.png' }] }, { title: '按摩预约', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/jiza56OX9u0pD1mAERkfTVsyjUUTIUfI.png' }] }, { title: '文章详情', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/iHCaj5F1yFIAsoyUC3azmOJJxvDYWVwv.png' }] }, { title: '小程序', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/SU2rs9hBm4yvFfBCtrq8bNaTS3Dng7YK.jpeg' }] }, { title: '网页', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/CpVzz43zRQVKzpV4sQUDmhSUtusVlOqI.png' }] }, { title: '拨打电话', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/06/sKiMiF83NGvMaQMWi7Afxoq2jj4aN8Lo.jpg' }] }, { title: '全部服务', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/06/vN2TficK6sYzrnNipTbn7AicAN9PuImk.webp' }] }] } },
    }
  },
  computed: {
    formatList () {
      let o = JSON.parse(JSON.stringify(this.o))
      let list = o.data.columnList
      let index = 0
      let length = list.length
      let newList = []
      let colNum = o.data.col.number
      let rowNum = o.data.row.number
      let formatRowNum = length <= colNum ? 1 : rowNum

      let count = formatRowNum * colNum
      while (index < length) {
        newList.push(list.slice(index, index += count))
      }
      return newList
    }
  },
  methods: {}
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.column-box {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  overflow: hidden;
  box-sizing: content-box;
}
.column-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  height: 70px;
}
.column-img {
  width: 50px;
  height: 50px;
  object-fit: cover;
}
.column-text {
  font-size: 12px;
  text-align: center;
  color: #999;
  width: 100%;
  padding: 0 10%;
  display: block;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
</style>
