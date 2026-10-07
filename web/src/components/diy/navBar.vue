<template>
  <div class="diy-phone_top rel">
    <div class="nav-title" style="position: absolute">
      <div
        v-for="(item, index) in tabBar"
        :key="index"
        v-show="item.id === tabActiveKey"
        >{{ item.name }}</div
      >
    </div>
  </div>
</template>

<script>
export default {
  name: 'navbar',
  props: {
    tabActiveKey: Number,
    tabBar: Array
  },
  methods: {
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.diy-phone_top {
  width: 100%;
  height: 66px;
  text-align: center;
  background: url(../../assets/nav-bg.png) center center / contain no-repeat
    rgb(255, 255, 255);
  position: relative;
}

.back-user {
  width: 80px;
  height: 28px;
  border-radius: 28px;
  margin-top: 23px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border: 1px solid #eeeeee;
  margin-left: 16px;
}

.back-user_avatar {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #f4f6f8;
}

.back-user_text {
  font-size: 8px;
  line-height: 28px;
  margin-left: 2px;
}
.nav-title {
  width: 100px;
  left: 50%;
  margin-left: -50px;
  line-height: 44px;
  font-size: 16px;
  top: 15px;
}

.nav-store {
  position: absolute;
  height: 44px;
  top: 25px;
  height: 25px;
  background: #f7f8fa;
  width: 106px;
  border-radius: 25px;
  left: 15px;
}
</style>
