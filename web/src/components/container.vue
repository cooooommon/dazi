<template>
    <div class="lb-container">
        <top-nav :nav='nav' @changNav='handleNav'></top-nav>
        <transition name="fade">
            <router-view></router-view>
        </transition>
    </div>
</template>

<script>
export default {
  data () {
    return {
      nav: []
    }
  },
  created () {
    this.nav = this.$route.meta.topMenu || []
  },
  methods: {
    handleNav (index) {}
  },
  watch: {
    $route: {
      deep: true,
      handler (val) {
        this.nav = val.meta.topMenu || []
      }
    }
  }
}
</script>

<style lang="scss" scoped>
    .lb-container{
        width: 100%;
        height: 500px;
        margin-left: 120px;
        margin-top: 120px;
        .fade-enter, .fade-leave-to {
          opacity: 0
        }
        .fade-leave, .fade-enter-to {
          opacity: 1
        }
        .fade-enter-active, .fade-leave-active {
          transition: all .2s
        }
    }
</style>
