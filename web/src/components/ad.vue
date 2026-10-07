<!--
 * @Descripttion: 左侧边栏广告
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2021-03-15 13:22:26
-->
<template>
    <div class="lb-ad" :class="!adSwitch ? 'ad-collapse' : ''">
      <div class="ad-main">
        <!-- <lb-image  src="@/assets/icon/apps.png"/> -->
      </div>
        <div class="flod"  @click="handleFold">{{adSwitch ? '折叠' : '展开'}}</div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  data () {
    return {
      key: '广告'
    }
  },
  created () {
  },
  methods: {
    handleFold () {
      let {adSwitch} = this
      this.$store.commit('handleAdSwitch', !adSwitch)
    }
  },
  computed: {
    ...mapGetters(['adSwitch'])
  }
}
</script>

<style lang="scss" scoped>
    .lb-ad{
        width: 220px;
        position: fixed;
        transition: width 0.2s linear;
        top: 70px;
        right: 0;
        height: 1000px;
        z-index: 2;
        .ad-main{
          position: absolute;
          top: 0;
          left: 0;
          width: 220px;
          background: #fff;
          height: 100%;
          font-size: 14px;
          padding: 10px;
        }
        .flod{
            position: absolute;
            top: 0;
            bottom: 0;
            left: -20px;
            margin: auto;
            width: 20px;
            height: 80px;
            background: #BFBFBF;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
            cursor: pointer;
            color: #fff;
            font-size: 14px;
        }
    }
    .ad-collapse{
        width: 0px;
    }
</style>
