<!--
 * @Description: 服务轮播图
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-19 14:36:30
 * @LastEditTime: 2023-08-23 15:15:59
 * @LastEditors: DXV-RGWU-TUFH-RFCY-IEGMYY
-->

<template>
  <div class="diy-service-column">
    <column :o="columnList"></column>
  </div>
</template>

<script>
import column from '@/components/diy/column.vue'
export default {
  name: 'serviceColumn',
  props: {
    o: Object,
    primaryColor: String
  },
  components: {
    column
  },
  data () {
    return {
      columnList: { title: '导航', data: { row: { number: 2, min: 1, max: 2 }, col: { number: 4, min: 4, max: 5 }, style: { fontColor: '#666', background: '#fff', whiteSpace: 30, wingBlank: 0 }, columnList: [{ title: '分类一', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/ox4e61dKfe5cqbWb4534942XgFev4VTi.png' }] }, { title: '分类二', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/IZWJSYAlyjhEunHgKj5Xl2zg1DtVze56.png' }] }, { title: '分类三', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/jiza56OX9u0pD1mAERkfTVsyjUUTIUfI.png' }] }, { title: '分类四', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/iHCaj5F1yFIAsoyUC3azmOJJxvDYWVwv.png' }] }, { title: '分类五', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/SU2rs9hBm4yvFfBCtrq8bNaTS3Dng7YK.jpeg' }] }, { title: '分类六', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/08/CpVzz43zRQVKzpV4sQUDmhSUtusVlOqI.png' }] }, { title: '分类七', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/06/sKiMiF83NGvMaQMWi7Afxoq2jj4aN8Lo.jpg' }] }, { title: '全部分类', img: [{ url: 'https://lbqnyv3.migugu.com/image/666/23/06/1O3IplgpnUUOlooDZMkieK2mZgE8kIzi.jpg' }] }] } }
    }
  }
}
</script>
<style lang="scss" scoped>
</style>
