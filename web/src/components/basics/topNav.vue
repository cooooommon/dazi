<template>
  <div class="lb-top-nav">
    <div @click="goBack" class="nav-item"  v-if="title"><i class="iconfont icon-left" v-if="isBack"></i> {{title}}</div>
    <div @click="goBack" class="nav-item" v-else-if='nav.length === 1'><i class="iconfont icon-left" v-if="isBack"></i> {{$t('menu.' + nav[0].title)}}</div>
    <router-link
    v-else-if="nav.length > 1"
    v-for="(item,index) in nav"
    tag='div'
    class="nav-item"
    :key='index'
    active-class="nav-item-active"
    :to="item.url"
    >
    {{$t('menu.' + item.title)}}
    </router-link>
  </div>
</template>

<script>
export default {
  props: {
    active: {
      type: Number,
      default: 0
    },
    isBack: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      activeNav: this.active,
      nav: []
    }
  },
  created () {
    let {pagePermission} = this.$route.meta
    if (pagePermission) {
      this.nav = pagePermission
      this.activeNav = this.nav[0].index
    }
  },
  methods: {
    goBack () {
      if (!this.isBack) return
      this.$router.back(-1)
    }
  }
}
</script>

<style lang="scss" scoped>
    .lb-top-nav{
        width: 100%;
        height: 60px;
        border-bottom: 1px solid #E1E1E1;
        display: flex;
        align-items: center;
        padding: 0 10px;
        font-size: 14px;
        white-space: nowrap;
        .nav-item{
          height: 60px;
          padding: 0 20px;
          line-height: 60px;
          cursor: pointer;
          &::after{
            position: absolute;
            content: '';
            width: 0%;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
            height: 0px;
            background: $themeColor;
            transform: all 0.3 linear;
          }
        }
        .nav-item-active{
          color: $themeColor;
          position: relative;
          &::after{
            width: 90%;
            height: 2px;
          }
        }

    }
</style>
