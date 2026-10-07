<template>
  <div class="lb-system-payment">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref='subForm'
        :rules="subFormRules"
        label-width="120px"
      >
        <el-form-item
          label="商户号"
          prop="mch_id"
        >
          <el-input
            v-model="subForm.mch_id"
            placeholder="请输入商户号"
          ></el-input>
        </el-form-item>
        <el-form-item
          label="支付秘钥"
          prop="pay_key"
        >
          <el-input
            v-model="subForm.pay_key"
            placeholder="请输入支付秘钥"
          ></el-input>
        </el-form-item>
        <el-form-item
          label="cert_path"
          prop="cert_path"
        >
          <div class="upload-file-warp">
            <div class="upload-file-warp-content">{{subForm.cert_path | handleFileName}}</div>
            <lb-button
              class="lb-cover-wrap"
              size="mini"
              type="primary"
              @click="handleBtnUplaod('cert_path')"
            >{{$t('action.upload')}}</lb-button>
          </div>
        </el-form-item>
        <el-form-item
          label="key_path"
          prop="key_path"
        >
          <div class="upload-file-warp">
            <div class="upload-file-warp-content">{{subForm.key_path | handleFileName}}</div>
            <lb-button
              size="mini"
              type="primary"
              @click="handleBtnUplaod('key_path')"
            >{{$t('action.upload')}}</lb-button>
          </div>
        </el-form-item>
        <el-form-item>
          <lb-button
            type="primary"
            @click="submitForm"
            v-preventReClick
          >{{$t('action.submit')}}</lb-button>
        </el-form-item>
      </el-form>
    </div>
    <input
      type="file"
      accept="text"
      style="display:none"
      @change="uploadFiles"
      ref="inputFile"
    />
  </div>
</template>

<script>
export default {
  data () {
    let checkPayKey = (rule, value, callback) => {
      let reg = /^[0-9a-zA-Z]*$/g
      if (!reg.test(value)) {
        callback(new Error('请输入字母数字类型'))
      } else {
        callback()
      }
    }
    return {
      fileName: '',
      subForm: {
        mch_id: '',
        pay_key: '',
        cert_path: '',
        key_path: ''
      },
      subFormRules: {
        mch_id: {
          required: true,
          type: 'string',
          message: '请输入商户号',
          trigger: 'blur'
        },
        pay_key: [
          {
            required: true,
            type: 'string',
            message: '请输入支付秘钥',
            trigger: 'blur'
          },
          { validator: checkPayKey, trigger: 'blur' }
        ],
        cert_path: {
          required: true,
          type: 'string',
          message: '请上传证书',
          trigger: 'change'
        },
        key_path: {
          required: true,
          type: 'string',
          message: '请上传证书',
          trigger: 'change'
        }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    handleBtnUplaod (name) {
      this.fileName = name
      this.$refs['inputFile'].click()
    },
    uploadFiles (e) {
      let file = e.target.files[0]
      let { name } = file
      if (name.indexOf('pem') < 0) {
        this.$message.error('请上传pem格式的文件')
      } else {
        let formData = new FormData()
        formData.append('file[]', file)
        formData.append('type', 'cert')
        this.$api.upload.uploadFiles(formData).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successUpload'))
            this.subForm[this.fileName] = res.data[0].attachment
          }
        })
      }
    },
    async getFormInfo () {
      let { code, data } = await this.$api.system.payConfigInfo()
      if (code !== 200) return
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let { subForm } = this
          this.$api.system.payConfigUpdate(subForm).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
            }
          })
        }
      })
    },
    handleFileUrl (url) {
      if (url) {
        return url.substring(url.lastIndexOf('cert') + 1)
      } else {
        return ''
      }
    }
  },
  watch: {
    'subForm.cert_path' (newValue, oldValue) {
      if (newValue && !oldValue) {
        this.$refs['subForm'].clearValidate('cert_path')
      }
    },
    'subForm.key_path' (newValue, oldValue) {
      if (newValue && !oldValue) {
        this.$refs['subForm'].clearValidate('key_path')
      }
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
.lb-system-payment {
  width: 100%;
  .el-input {
    width: 300px;
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
</style>
