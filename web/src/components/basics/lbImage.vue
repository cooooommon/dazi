<template>
  <el-image :class="[{ 'bg-img': !src }]" :fit="fit" :src="src">
    <div slot="error" class="image-slot">
      <i class="el-icon-picture-outline"></i>
    </div>
  </el-image>
</template>

<script>
export default {
  props: {
    fit: {
      type: String,
      default: 'cover'
    },
    src: {
      type: String,
      default: ''
    }
  }
}
</script>

<style lang="scss" scoped>
.el-image {
  display: flex;
  justify-content: center;
  align-items: center;
  .image-slot {
    i {
      font-size: 20px;
    }
  }
}
.bg-img {
  background: $bgThemeColor;
}
</style>
