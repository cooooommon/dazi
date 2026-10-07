<template>
  <div class="lb-cover-wrap">
    <div
      class="lb-cover"
      :class="[size]"
      @click="showUploadModel"
      v-if="type === 'single'"
    >
      <lb-image v-if="cover.length > 0" :src="cover[cover.length - 1].url" />
      <i v-else class="el-icon-plus"></i>
      <i
        v-if="cover.length > 0"
        @click.stop="deleteCover"
        class="el-icon-circle-close"
      ></i>
    </div>
    <div v-else-if="type === 'more'" class="lb-upload-more">
      <vuedraggable @change="dragChange" v-model="cover">
        <transition-group class="flex-warp">
          <div
            class="more-item"
            :class="[size]"
            v-for="(item, index) in cover"
            :key="`drag_${index}`"
          >
            <lb-image :src="item.url" />
            <div class="mask">
              <i
                @click="lookBigImg(item.url)"
                class="el-icon-zoom-in"
                v-if="item.url"
              ></i>
              <i
                @click="delImg(index)"
                class="el-icon-delete"
                v-if="isToDel"
              ></i>
            </div>
          </div>
        </transition-group>
      </vuedraggable>
      <div class="flex-warp" v-if="cover.length < fileSize">
        <div class="up-item" :class="[size]" @click="showUploadModel">
          <i class="el-icon-plus"></i>
        </div>
        <lb-tool-tips class="ml-sm" v-if="tips">{{
          `图片建议尺寸：${tips}`
        }}</lb-tool-tips>
      </div>
      <el-dialog
        :visible.sync="centerDialogVisible"
        width="800px"
        center
        :append-to-body="true"
      >
        <div class="dialog-inner-img">
          <lb-image class="dialog-img" :src="viewImg" />
        </div>
      </el-dialog>
    </div>
    <div v-else>
      <lb-button type="primary" size="mini" @click="showUploadModel" v-if="buttonType == 'button'"
      >选择</lb-button>
      <el-tag
        v-if="buttonType == 'tag'"
        :type="videoUrl ? '' : 'info'"
        :closable="videoUrl ? true : false"
        @close="delVideoUrl(videoUrl)"
        @click="showUploadModel"
      >
        <div v-if="videoUrl && videoUrl.length > 1000">{{
          videoUrl.substring(0, 14)
        }}</div>
        <div v-else>{{ videoUrl || '点击选择' }}</div>
      </el-tag>
    </div>
    <div v-if="isToDel">
      <lb-upload
        :paramData="paramData"
        :filesLength="fileList.length"
        :fileSize="fileSize"
        :multilineType="type"
        :visibles.sync="showUpload"
        :fileType="fileType"
        :fileAccept="fileAccept"
        @selectedFiles="selectedFiles"
      ></lb-upload>
    </div>
  </div>
</template>

<script>
import vuedraggable from 'vuedraggable'
export default {
  components: {
    vuedraggable
  },
  props: {
    type: {
      type: String,
      default () {
        return 'single'
      }
    },
    size: {
      type: String,
      default () {
        return 'big'
      }
    },
    tips: {
      type: String,
      default () {
        return ''
      }
    },
    fileType: {
      type: String,
      default () {
        return 'image'
      }
    },
    fileAccept: {
      type: String,
      default () {
        return ''
      }
    },
    fileSize: {
      type: Number,
      default () {
        return 9
      }
    },
    fileList: {
      type: [Array, String],
      default () {
        return []
      }
    },
    isToDel: {
      type: Boolean,
      default () {
        return true
      }
    },
    paramData: {
      type: Object,
      default () {
        return {}
      }
    },
    buttonType: {
      type: String,
      default () {
        return 'button'
      }
    },
    value: {
      type: String,
      default () {
        return ''
      }
    }
  },
  data () {
    return {
      showUpload: false,
      cover: [],
      centerDialogVisible: false,
      viewImg: '',
      videoUrl: ''
    }
  },
  created () {
    if (this.fileList && this.fileList.length) {
      this.cover = this.fileList
    }
    this.videoUrl = this.value 
  },
  methods: {
    selectedFiles (img) {
      if (!img.length) return false
      let selectedImgs = this.type === 'single' ? [img[img.length - 1]] : img
      this.cover = selectedImgs
      if(this.buttonType === 'tag'){
        let len = selectedImgs.length - 1
        this.videoUrl = selectedImgs[len].url
      }
      this.$emit('selectedFiles', selectedImgs)
    },
    delVideoUrl(){
       this.videoUrl = ''
      this.$emit('delVideoUrl')
    },
    dragChange (e) {
      this.$emit('moveFiles', this.cover)
    },
    showUploadModel () {
      this.showUpload = true
    },
    lookBigImg (url) {
      this.viewImg = url
      this.centerDialogVisible = true
    },
    delImg (index) {
      this.cover.splice(index, 1)
    },
    deleteCover () {
      this.cover = []
      this.$emit('selectedFiles', [])
    }
  },
  watch: {
    fileList: {
      deep: true,
      handler (val) {
        if (val && typeof val === 'object') {
          this.cover = val
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-cover-wrap {
  display: inline-block;
  .lb-cover.small {
    width: 60px;
    height: 60px;
    .el-image {
      width: 60px;
      height: 60px;
    }
    i {
      font-size: 20px;
      line-height: 60px;
    }
    .el-icon-circle-close {
      font-size: 18px;
      line-height: 18px;
    }
  }

  .lb-cover.middle {
    width: 80px;
    height: 80px;
    .el-image {
      width: 80px;
      height: 80px;
    }
    i {
      font-size: 20px;
      line-height: 80px;
    }
    .el-icon-circle-close {
      font-size: 22px;
      line-height: 22px;
    }
  }
  .lb-cover {
    width: 100px;
    height: 100px;
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    text-align: center;
    overflow: hidden;
    position: relative;
    .el-image {
      width: 100px;
      height: 100px;
    }
    i {
      font-size: 26px;
      line-height: 100px;
    }
    &:hover {
      border: 1px dashed #09f;
      .el-icon-circle-close {
        display: inline-block;
      }
    }
    .el-icon-circle-close {
      display: none;
      z-index: 10;
      position: absolute;
      right: 5px;
      top: 5px;
      line-height: 1;
    }
  }
  .lb-upload-more {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    .more-item.small {
      width: 60px;
      height: 60px;
      .el-image {
        width: 60px;
        height: 60px;
        z-index: 5;
      }
    }
    .more-item.middle {
      width: 80px;
      height: 80px;
      .el-image {
        width: 80px;
        height: 80px;
        z-index: 5;
      }
    }
    .more-item {
      width: 100px;
      height: 100px;
      border: 1px solid #d9d9d9;
      border-radius: 6px;
      position: relative;
      margin-right: 10px;
      margin-bottom: 10px;
      overflow: hidden;
      .el-image {
        width: 100px;
        height: 100px;
        z-index: 5;
      }
      .mask {
        display: none;
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        font-size: 20px;
        color: #fff;
        justify-content: center;
        align-items: center;
        z-index: 10;
        top: 0;
        left: 0;
        i {
          margin: 0 10px;
          cursor: pointer;
        }
      }
      &:hover {
        .mask {
          display: flex;
        }
      }
    }
    .up-item {
      width: 100px;
      height: 100px;
      border: 1px dashed #d9d9d9;
      border-radius: 6px;
      cursor: pointer;
      text-align: center;
      i {
        font-size: 26px;
        line-height: 100px;
      }
      &:hover {
        border: 1px dashed #09f;
      }
    }
    .up-item.small {
      width: 60px;
      height: 60px;
      border: 1px dashed #d9d9d9;
      border-radius: 6px;
      cursor: pointer;
      text-align: center;
      i {
        font-size: 18px;
        line-height: 60px;
      }
      &:hover {
        border: 1px dashed #09f;
      }
    }
    .up-item.middle {
      width: 80px;
      height: 80px;
      border: 1px dashed #d9d9d9;
      border-radius: 6px;
      cursor: pointer;
      text-align: center;
      i {
        font-size: 22px;
        line-height: 80px;
      }
      &:hover {
        border: 1px dashed #09f;
      }
    }
  }
  .dialog-inner-img {
    display: flex;
    justify-content: center;
    align-items: center;
    .dialog-img {
      width: 100%;
    }
  }
  .el-tag{
    white-space: normal;
    word-break: break-word;
    height:auto;
  }
}
</style>
