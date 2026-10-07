<!--
 * @Descripttion: 上传微信审核
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:08:37
-->
<template>
  <div class="lb-sys-sprowechat">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
        class="config-form"
      >
        <el-form-item label="代码上传密钥" prop="key">
          <div class="upload-file-warp">
            <div class="upload-file-warp-content">
              {{ subForm.key | handleFileName }}
            </div>
            <lb-button
              class="lb-cover-wrap"
              size="mini"
              type="primary"
              @click="handleBtnUplaod('key')"
              >{{ $t('action.upload') }}</lb-button
            >
          </div>
        </el-form-item>
        <input
          type="file"
          accept="text"
          style="display: none"
          @change="uploadFiles"
          ref="inputFile"
        />
        <el-form-item label="版本号" prop="version">
          <el-input
            v-model="subForm.version"
            type="text"
            maxlength="30"
            show-word-limit
            placeholder="请输入版本号"
          ></el-input>
        </el-form-item>
        <el-form-item label="版本描述" prop="content">
          <el-input
            v-model="subForm.content"
            type="text"
            maxlength="30"
            show-word-limit
            placeholder="请输入版本描述"
          ></el-input>
        </el-form-item>
        <el-form-item label="小程序代码版本" prop="wxapp_version">
          <el-select
            v-model="subForm.wxapp_version"
            filterable
            clearable
            placeholder="请选择"
          >
            <el-option
              v-for="item in selectOptions"
              :key="item.key"
              :label="item.key"
              :value="item.key"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitFormInfo('subForm')"
            v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
        </el-form-item>
      </el-form>

      <el-dialog
        title="温馨提示"
        :visible.sync="isSubmit"
        width="30%"
        :before-close="handleClose"
      >
        <div class="flex-center">
          <div class="el-icon-loading" style="font-size: 20px"></div>
          <div class="flex-1 mr-lg" style="display: inline-block">
            代码正在上传中，请耐心等待！
          </div>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click="isSubmit = false">知道了</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      isSubmit: false,
      isSubmitStatus: 0,
      fileName: '',
      selectOptions: [],
      subForm: {
        app_id: [],
        key: '',
        version: '',
        content: '',
        wxapp_version: ''
      },
      subFormRules: {
        key: {
          required: true,
          type: 'string',
          message: '请上传密钥',
          trigger: 'change'
        },
        wxapp_version: {
          required: true,
          type: 'string',
          message: '请选择小程序代码版本',
          trigger: 'change'
        }
      }
    }
  },
  async created () {
    await this.getBaseInfo()
  },
  methods: {
    async getBaseInfo () {
      let [version, info] = await Promise.all([
        this.$api.system.wxappVersion(),
        this.$api.system.wxUploadInfo()
      ])

      let arr = []
      ;(version.data || []).map(item => {
        arr.push({ key: item })
      })

      this.selectOptions = arr
      this.subForm = info.data
    },
    handleClose () {
      this.isSubmit = false
    },
    handleBtnUplaod (name) {
      this.fileName = name
      this.$refs['inputFile'].click()
    },
    uploadFiles (e) {
      let file = e.target.files[0]
      let { name } = file
      if (name.indexOf('key') < 0) {
        this.$message.error('请上传key格式的文件')
      } else {
        let formData = new FormData()
        formData.append('file[]', file)
        formData.append('type', 'wxuploadkey')
        this.$api.upload.uploadFiles(formData).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successUpload'))
            this.subForm[this.fileName] = res.data[0].attachment
            let param = this.$util.pick(this.subForm, ['app_id', 'key', 'version', 'content'])
            this.$api.system.wxUploadUpdate(param)
          }
        })
      }
    },
    submitFormInfo (name) {
      if (this.isSubmitStatus === 1) {
        this.isSubmit = true
        return
      }
      this.$refs[name].validate(valid => {
        if (valid) {
          let param = this.$util.pick(this.subForm, ['app_id', 'key', 'version', 'content'])
          let loadparam = this.$util.pick(this.subForm, ['wxapp_version'])
          this.$api.system.wxUploadUpdate(param).then(res => {
            if (res.code !== 200) return
            this.isSubmit = true
            this.isSubmitStatus = 1
            this.$api.system.uploadWxapp(loadparam).then(res => {
              this.isSubmit = false
              this.isSubmitStatus = 0
              if (!res.data.msg) return
              this.$message({
                type: res.data.code === 20000 ? 'success' : 'error',
                message: res.data.msg,
                duration: 0,
                showClose: true
              })
            })
          })
        }
      })
    }
  },
  filters: {
    handleFileName (url) {
      if (url) {
        return url.substring(url.lastIndexOf('/') + 1)
      } else {
        return '请上传文件'
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-sys-sprowechat {
  width: 100%;
  .config-form {
    .flex-center {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .flex-1 {
      flex: 1;
    }
    .flex-between {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .el-input,
    .el-select {
      width: 300px;
    }
    .el-textarea {
      width: 600px;
    }
    .upload-file-warp {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .upload-file-warp-content {
      width: 85%;
      display: inline-block;
    }
  }
}
</style>
