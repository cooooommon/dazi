<template>
  <el-dialog :visible.sync="isShowPrompt" center :before-close="close">
    <el-form>
      <div solt="title" class="titleFont">服务到期通知</div>
      <div class="delog">
        <div>你开通的套餐{{promptData.status!==1?'已于':''}} {{ promptData.time }} {{promptData.status===1?'即将':''}}到期，请联系所属运营商续费后使用</div>
        <div>感谢对我们工作的理解与支持。</div>
        <el-button type="primary" size="small" class="pad" @click="close">我知道了</el-button>
      </div>
    </el-form>
  </el-dialog>
</template>
<script>
import { mapState, mapMutations } from 'vuex'
export default {
  //   props:{
  //       isShow:String
  //   },
  data () {
    return {
    }
  },
  created () {},
  computed: {
    ...mapState({
      promptData: state => state.routes.promptData,
      isShowPrompt: state => state.routes.isShowPrompt
    })
  },
  methods: {
    ...mapMutations(['changeIsShowPrompt']),
    close () {
      this.changeIsShowPrompt(false)
    },
    handleClose (done) {
      this.$confirm('确认关闭？')
        .then(_ => {
          this.changeIsShowPrompt(false)
        })
        .catch(_ => {})
    }
  }
}
</script>
<style  scoped>
.delog {
  display: flex;
  align-items: center;
  flex-direction: column;
  justify-content: flex-start;
}
.titleFont {
  text-align: center;
  font-size: 20px;
  color: #000000;
  font-weight: bold;
}
.pad {
  margin-top: 100px;
}
</style>
