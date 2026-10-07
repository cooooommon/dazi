<!--
 * @Descripttion: 上传配置
 * @Author: xiao li
 * @Date: 2021-03-11 15:38:36
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-13 19:09:31
-->
<template>
  <div class="lb-system-upload">
    <top-nav></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        label-width="130px"
        ref="baseForm"
        :model="baseForm"
        :rules="baseFormRules"
      >
        <!-- <el-form-item label="配置名称" prop="name">
          <el-input v-model="baseForm.name"></el-input>
        </el-form-item>-->
        <el-form-item required label="请选择上传类型">
          <el-select v-model="open_oss" placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item v-if="open_oss === 0">
          <lb-button
            type="primary"
            @click="submitForm('baseForm')"
            v-preventReClick
            >{{ $t('action.submit') }}</lb-button
          >
        </el-form-item>
      </el-form>
      <el-form
        @submit.native.prevent
        v-show="open_oss === 2"
        :model="qiniuyunForm"
        :rules="qiniuyunFormRules"
        ref="qiniuyunForm"
        label-width="130px"
      >
        <el-form-item label="AccessKey" prop="qiniu_accesskey">
          <el-input
            type="password"
            placeholder="请输入AccessKey"
            v-model="qiniuyunForm.qiniu_accesskey"
          ></el-input>
        </el-form-item>
        <el-form-item label="SecretKey" prop="qiniu_secretkey">
          <el-input
            type="password"
            placeholder="请输入SecretKey"
            v-model="qiniuyunForm.qiniu_secretkey"
          ></el-input>
        </el-form-item>
        <el-form-item label="仓库名称" prop="qiniu_bucket">
          <el-input
            placeholder="请输入仓库名称"
            v-model="qiniuyunForm.qiniu_bucket"
          ></el-input>
        </el-form-item>
        <el-form-item label="自定义域名" prop="qiniu_yuming">
          <el-input
            placeholder="请输入自定义域名"
            v-model="qiniuyunForm.qiniu_yuming"
          ></el-input>
          <lb-tool-tips>域名前面要加http://</lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button
            type="primary"
            @click="submitForm('qiniuyunForm')"
            v-preventReClick
            >{{ $t('action.submit') }}</lb-button
          >
        </el-form-item>
      </el-form>
      <el-form
        @submit.native.prevent
        v-show="open_oss === 1"
        :model="aliyunForm"
        :rules="aliyunFormRules"
        ref="aliyunForm"
        label-width="130px"
      >
        <el-form-item label="仓库名称" prop="aliyun_bucket">
          <el-input
            placeholder="请输入仓库名称"
            v-model="aliyunForm.aliyun_bucket"
          ></el-input>
        </el-form-item>
        <el-form-item label="AccessKeyId" prop="aliyun_access_key_id">
          <el-input
            type="password"
            placeholder="请输入AccessKeyId"
            v-model="aliyunForm.aliyun_access_key_id"
          ></el-input>
        </el-form-item>
        <el-form-item label="AccessKeySecret" prop="aliyun_access_key_secret">
          <el-input
            type="password"
            placeholder="请输入AccessKeySecret"
            v-model="aliyunForm.aliyun_access_key_secret"
          ></el-input>
        </el-form-item>
        <el-form-item label="上传根目录" prop="aliyun_base_dir">
          <el-input
            placeholder="请输入上传根目录"
            v-model="aliyunForm.aliyun_base_dir"
          ></el-input>
          <lb-tool-tips
            >代表仓库顶层目录名称,请以英文字母命名,前后不用加'/',例如：mingpian_image</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="自定义域名" prop="aliyun_zidinyi_yuming">
          <el-input
            placeholder="请输入自定义域名"
            v-model="aliyunForm.aliyun_zidinyi_yuming"
          ></el-input>
          <lb-tool-tips>自定义域名前面要加https://,结尾不用加'/'</lb-tool-tips>
        </el-form-item>
        <el-form-item label="Endpoint" prop="aliyun_endpoint">
          <el-input
            placeholder="请输入Endpoint"
            v-model="aliyunForm.aliyun_endpoint"
          ></el-input>
          <lb-tool-tips>例如：oss-cn-beijing.aliyuncs.com</lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button
            type="primary"
            @click="submitForm('aliyunForm')"
            v-preventReClick
            >{{ $t('action.submit') }}</lb-button
          >
        </el-form-item>
      </el-form>
      <el-form
        @submit.native.prevent
        v-show="open_oss === 3"
        :model="tengxunyunForm"
        :rules="tengxunyunFormRules"
        ref="tengxunyunForm"
        label-width="130px"
      >
        <el-form-item label="AppID" prop="tenxunyun_appid">
          <el-input
            type="password"
            placeholder="请输入AppID"
            v-model="tengxunyunForm.tenxunyun_appid"
          ></el-input>
        </el-form-item>
        <el-form-item label="SecretID" prop="tenxunyun_secretid">
          <el-input
            type="password"
            placeholder="请输入SecretID"
            v-model="tengxunyunForm.tenxunyun_secretid"
          ></el-input>
        </el-form-item>
        <el-form-item label="SecretKey" prop="tenxunyun_secretkey">
          <el-input
            type="password"
            placeholder="请输入SecretKey"
            v-model="tengxunyunForm.tenxunyun_secretkey"
          ></el-input>
        </el-form-item>
        <el-form-item label="仓库名称" prop="tenxunyun_bucket">
          <el-input
            placeholder="请输入仓库名称"
            v-model="tengxunyunForm.tenxunyun_bucket"
          ></el-input>
        </el-form-item>
        <el-form-item label="仓库地域" prop="tenxunyun_region">
          <el-input
            placeholder="请输入仓库地域"
            v-model="tengxunyunForm.tenxunyun_region"
          ></el-input>
        </el-form-item>
        <el-form-item label="仓库域名" prop="tenxunyun_yuming">
          <el-input
            placeholder="请输入仓库域名"
            v-model="tengxunyunForm.tenxunyun_yuming"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <lb-button
            type="primary"
            @click="submitForm('tengxunyunForm')"
            v-preventReClick
            >{{ $t('action.submit') }}</lb-button
          >
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      options: [
        {
          value: 0,
          label: '本地上传'
        },
        {
          value: 2,
          label: '七牛云上传'
        },
        {
          value: 1,
          label: '阿里云OSS上传'
        },
        {
          value: 3,
          label: '腾讯云上传'
        }
      ],
      baseForm: {
        id: ''
        // name: ''
      },
      baseFormRules: {
        // name: {
        //   required: true,
        //   type: 'string',
        //   message: '请输入配置名称',
        //   trigger: 'blur'
        // }
      },
      open_oss: 0,
      qiniuyunForm: {
        qiniu_accesskey: '',
        qiniu_secretkey: '',
        qiniu_bucket: '',
        qiniu_yuming: ''
        // qiniu_rules: ''
      },
      qiniuyunFormRules: {
        qiniu_accesskey: {
          required: true,
          type: 'string',
          message: '请输入AccessKey',
          trigger: 'blur'
        },
        qiniu_secretkey: {
          required: true,
          type: 'string',
          message: '请输入SecretKey',
          trigger: 'blur'
        },
        qiniu_bucket: {
          required: true,
          type: 'string',
          message: '请输入仓库名称',
          trigger: 'blur'
        },
        qiniu_yuming: {
          required: true,
          type: 'string',
          message: '请输入自定义域名',
          trigger: 'blur'
        }
        // qiniu_rules: {required: true, type: 'string', message: '请输入规则配置', trigger: 'blur'}
      },
      aliyunForm: {
        aliyun_bucket: '',
        aliyun_access_key_id: '',
        aliyun_access_key_secret: '',
        aliyun_base_dir: '',
        aliyun_zidinyi_yuming: '',
        aliyun_endpoint: ''
        // aliyun_rules: ''
      },
      aliyunFormRules: {
        aliyun_bucket: {
          required: true,
          type: 'string',
          message: '请输入仓库名称',
          trigger: 'blur'
        },
        aliyun_access_key_id: {
          required: true,
          type: 'string',
          message: '请输入AccessKeyId',
          trigger: 'blur'
        },
        aliyun_access_key_secret: {
          required: true,
          type: 'string',
          message: '请输入AccessKeySecret',
          trigger: 'blur'
        },
        aliyun_base_dir: {
          required: true,
          type: 'string',
          message: '请输入上传根目录',
          trigger: 'blur'
        },
        aliyun_zidinyi_yuming: {
          required: true,
          type: 'string',
          message: '请输入自定义域名',
          trigger: 'blur'
        },
        aliyun_endpoint: {
          required: true,
          type: 'string',
          message: '请输入Endpoint',
          trigger: 'blur'
        }
        // aliyun_rules: {required: true, type: 'string', message: '请输入规则配置', trigger: 'blur'}
      },
      tengxunyunForm: {
        tenxunyun_appid: '',
        tenxunyun_secretid: '',
        tenxunyun_secretkey: '',
        tenxunyun_bucket: '',
        tenxunyun_region: '',
        tenxunyun_yuming: ''
      },
      tengxunyunFormRules: {
        tenxunyun_appid: {
          required: true,
          type: 'string',
          message: '请输入AppId',
          trigger: 'blur'
        },
        tenxunyun_secretid: {
          required: true,
          type: 'string',
          message: '请输入SecretId',
          trigger: 'blur'
        },
        tenxunyun_secretkey: {
          required: true,
          type: 'string',
          message: '请输入SecretKey',
          trigger: 'blur'
        },
        tenxunyun_bucket: {
          required: true,
          type: 'string',
          message: '请输入仓库名称',
          trigger: 'blur'
        },
        tenxunyun_region: {
          required: false,
          type: 'string',
          message: '请输入仓库地域',
          trigger: 'blur'
        },
        tenxunyun_yuming: {
          required: true,
          type: 'string',
          message: '请输入仓库域名',
          trigger: 'blur'
        }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.getOssConfig()
      if (code !== 200) return
      this.open_oss = data.open_oss
      let formArr = ['baseForm', 'qiniuyunForm', 'aliyunForm', 'tengxunyunForm']
      for (let i = 0, len = formArr.length; i < len; i++) {
        for (let key in this[formArr[i]]) {
          this[formArr[i]][key] = data[key]
        }
      }
    },
    submitForm (name) {
      let formArr = ['baseForm']
      if (name !== 'baseForm') {
        formArr.push(name)
      }
      let flag = true
      for (let i = 0, len = formArr.length; i < len; i++) {
        this.$refs[formArr[i]].validate(valid => {
          if (!valid) {
            flag = false
            return false
          }
        })
      }
      if (flag) {
        let subForm = this[name]
        subForm.open_oss = this.open_oss
        this.$api.system.updateOssConfig({ oss_config: subForm }).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successSub'))
          }
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-upload {
  .el-form {
    .el-input,
    .el-select {
      width: 300px;
    }
  }

  .el-textarea {
    width: 600px;
  }
}
</style>
