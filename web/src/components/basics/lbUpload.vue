<template>
  <div>
    <el-dialog
      :title="uploadTitle"
      width="1000px"
      center
      :visible.sync="showUpload"
      :append-to-body="true"
      @close="beforeClose"
    >
      <el-progress
        class="mb-md"
        :percentage="progress"
        v-if="showProgress"
      ></el-progress>
      <!-- iframe视频 -->
      <div v-if="isIframe && fileType === 'video'" class="top-video-switch">
        <el-tabs class="lb-tabs" v-model="activeName">
          <el-tab-pane label="本地视频" name="1">
            <div v-loading="loading" class="img-view">
              <div
                class="img-item"
                v-for="(item, index) in fileList"
                :key="index"
                :style="{
                  'background-image': `url('${
                    lbFileType === 'image' ? item.url : defaultBGImg
                  }')`
                }"
                @click="choiceImg(index)"
              >
                <div class="img-name">{{ item.filename }}</div>
                <i
                  v-show="item.checked"
                  class="icon-selected el-icon-check"
                ></i>
                <i
                  class="icon-d el-icon-delete-solid"
                  @click.stop="confirmDel(1, 'single', item.id)"
                ></i>
              </div>
            </div>
          </el-tab-pane>
          <!-- <el-tab-pane label="提取网络视频" name="2">
            <div class="get-network-video">
              <lb-image
                src="https://lbqny.migugu.com/admin/public/iframe_video.jpeg"
              />
              <el-input
                v-model="networkVideo"
                placeholder="请填写通用代码"
              ></el-input>
              <lb-button type="primary" @click="getNetWorkVideo"
                >提 取</lb-button
              >
            </div>
          </el-tab-pane> -->
        </el-tabs>
      </div>
      <!-- 图片视频音频 -->
      <div v-else>
        <div v-if="!paramData.methodModel" style="padding-bottom: 20px">
          <el-button
            size="small"
            @click=";(showGroupType = 1), (showGroup = true)"
            >管理分组</el-button
          >
        </div>
        <el-tabs
          v-model="editableTabsValue"
          @tab-click="Initialize(false)"
          type="card"
          @tab-remove="removeTab"
        >
          <el-tab-pane
            :key="goodItem.id"
            v-for="goodItem in editableTabs"
            :label="goodItem.name"
            :name="goodItem.name"
          >
            <div v-loading="loading" class="img-view">
              <div
                class="img-item"
                v-for="(item, index) in fileList"
                :key="index"
                @click="choiceImg(index)"
                :style="{
                  'background-image': `url('${
                    lbFileType === 'image' ? item.url : defaultBGImg
                  }')`
                }"
              >
                <div class="img-name">{{ item.filename }}</div>
                <i
                  v-show="item.checked"
                  class="icon-selected el-icon-check"
                ></i>
                <div v-if="paramData.methodModel"></div>
                <div v-else>
                  <i
                    class="icon-d el-icon-delete-solid"
                    style="right: 32px"
                    @click.stop="confirmDel(1, 'single', item.id)"
                  ></i>
                  <i
                    class="icon-d el-icon-upload"
                    @click.stop="toRemove(item.id)"
                  ></i>
                </div>
              </div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>
      <div class="upload-page">
        <div class="operate" v-if="paramData.methodModel">
          <lb-button size="mini" type="primary" @click="selectedImgs">{{
            $t('action.comfirm')
          }}</lb-button>
        </div>
        <div class="operate" v-else>
          <el-checkbox
            :indeterminate="isIndeterminate"
            v-model="checkAll"
            :checked="checkAll"
            @change="handleCheckAllChange"
            >{{ $t('action.checkAll') }}</el-checkbox
          >
          <el-divider direction="vertical"></el-divider>
          <div>
            <span>{{ $t('action.selected') }}</span>
            <span class="count">{{ selectedFiles.length }}</span>
            <span>{{ $t('action.toSelected') }}</span>
            <span class="count">{{ fileSizeNumber - filesLength }}</span>
          </div>
          <lb-button size="mini" type="primary" @click="selectedImgs">{{
            $t('action.comfirm')
          }}</lb-button>
          <lb-button size="mini" type="danger" @click="confirmDel(1, 'more')">{{
            $t('action.delete')
          }}</lb-button>
          <lb-button size="mini" type="warning" @click="toRemove">{{
            $t('action.remove')
          }}</lb-button>

          <el-divider direction="vertical"></el-divider>
          <lb-button
            size="mini"
            type="success"
            @click="$refs['inputImg'].click()"
            >{{ $t('action.upload') }}</lb-button
          >
          <input
            type="file"
            :accept="acceptType"
            :multiple="lbFileType === 'image'"
            style="display: none"
            @change="uploadFiles"
            ref="inputImg"
          />
        </div>
        <el-pagination
          @current-change="handleCurrentChange"
          :current-page.sync="page"
          :page-size="limit"
          layout="total, prev, pager, next"
          :total="total"
        >
        </el-pagination>
      </div>
    </el-dialog>
    <el-dialog
      width="375px"
      center
      :visible.sync="showGroup"
      :append-to-body="true"
    >
      <ul class="infinite-list" style="overflow: auto">
        <div
          class="group_item"
          v-for="(item, index) in editableTabs"
          :key="index"
        >
          <div class="fen_item">
            <i class="el-icon-folder"></i>
            <span
              v-if="item.name !== '全部'"
              @click="addShowGroup(index)"
              style="padding-left: 20px; cursor: pointer"
              >{{ item.name }}</span
            >
            <span v-else style="padding-left: 20px; cursor: pointer">{{
              item.name
            }}</span>
          </div>
          <el-checkbox
            v-show="item.name !== '全部'"
            v-model="item.checked"
            @change="changeBox($event, index)"
          ></el-checkbox>
        </div>
      </ul>
      <div class="btn_view flex-center">
        <el-button type="primary" size="small" @click="showGroup = false" plain
          >取消</el-button
        >
        <el-button
          type="warning"
          size="small"
          @click="confirmDel(2)"
          v-if="showGroupType === 2"
          >{{ $t('action.remove') }}</el-button
        >
        <el-button
          type="danger"
          :disabled="isDisable"
          size="small"
          @click="delgroup"
          v-if="showGroupType === 1"
          >{{ $t('action.delete') }}</el-button
        >
        <el-button type="primary" size="small" @click="addShowGroup()">{{
          $t('action.add')
        }}</el-button>
      </div>
    </el-dialog>

    <el-dialog
      title="请输入分组名称"
      :visible.sync="showAddGroup"
      width="30%"
      center
      :append-to-body="true"
    >
      <el-form
        @submit.native.prevent
        :model="addForm"
        :rules="groupRules"
        ref="addForm"
      >
        <el-form-item prop="name">
          <el-input
            v-model="addForm.name"
            :maxlength="20"
            type="text"
            placeholder="请输入内容"
          ></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showAddGroup = false">取 消</el-button>
        <el-button type="primary" @click="subMitGroup">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import moment from 'moment'

import * as qiniu from 'qiniu-js'
import { genUpToken } from '../../utils/qiniuToken'

const OSS = require('ali-oss')

const COS = require('cos-js-sdk-v5')

export default {
  components: {},
  props: {
    visibles: {
      type: Boolean,
      default: false
    },
    fileType: {
      type: String,
      default: 'image'
    },
    fileSize: {
      type: Number,
      default: 9
    },
    paramData: {
      type: Object,
      default () {
        return {}
      }
    },
    isIframe: {
      type: Boolean,
      default: false
    },
    filesLength: {
      type: Number,
      default: 0
    },
    multilineType: {
      type: String,
      default: 'single'
    },
    fileAccept: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      typeArr: {
        'picture': 1,
        'audio': 2,
        'video': 3,
        'cert': 4,
        'wxuploadkey': 5,
        'file': 6
      },
      typeTextArr: {
        'image': '图片',
        'audio': '音频',
        'video': '视频',
        'cert': '证书',
        'wxuploadkey': 5,
        'file': '文件'
      },
      uploadConfig: {},
      addForm: {
        name: ''
      },
      groupRules: {
        name: {
          required: false,
          type: 'string',
          message: '请输入分组名',
          trigger: 'blur'
        }
      },
      isDisable: false, // 是否禁用删除
      updateGropId: false, // 分组id
      group_index: 0, // 选中index
      selectIds: [],
      groupGoods: [

      ],
      name: '',
      showAddGroup: false, // 显示输入框
      groups: [
        {
          name: '全部',
          id: 0,
          conent: ''
        }
      ], // 分类信息
      showGroup: false, // 是否显示分组信息
      showGroupType: 1, // 1管理分组；2移动分组
      remove_param: {
        file_id: [],
        group_id: 0
      },
      editableTabsValue: '全部', // 分组index
      editableTabs: [],
      activeName: '1',
      networkVideo: '',
      loading: false,
      page: 1,
      limit: 10,
      total: 0,
      showUpload: false,
      isIndeterminate: false,
      checkAll: false,
      defaultBGImg: '', // 不是图片类型时的列表默认背景图
      fileList: [], // 图片、视频、音频列表数据
      uploadTitle: '', // 上传的标题
      lbFileType: 'image', // 文件类型
      acceptType: 'image/*', // 上传的文件类型
      progress: 0,
      fileSizeNumber: '',
      showProgress: false,
      checkedFiles: {},
      fileLen: this.filesLength
    }
  },
  created () {
    if (this.multilineType === 'single' || this.fileType === 'video' || this.fileType === 'file') {
      this.fileSizeNumber = 1
    } else {
      this.fileSizeNumber = this.fileSize
    }
    this.Initialize()
  },
  methods: {
    async Initialize (flag = true) {
      this.page = 1
      this.selectIds = []
      this.checkedFiles = {}
      this.fileLen = this.filesLength
      if (flag) {
        await this.getGroups()
      }
      this.handleFileType(this.fileType)
    },
    // 删除单个tab
    removeTab (targetName) {
      let tabs = this.editableTabs
      let activeName = this.editableTabsValue
      if (targetName === '全部') {
        return
      }
      // 调用确认框 回调执行函数
      this.configGroup(() => {
        let goodIndex = 0
        tabs.forEach((tab, index) => {
          if (tab.name === targetName) {
            goodIndex = index
            if (activeName === targetName) {
              let nextTab = tabs[index + 1] || tabs[index - 1]
              if (nextTab) {
                activeName = nextTab.name
              } else {
                activeName = '全部'
              }
            }
          }
        })
        this.$api.upload.delGroup({ group_id: tabs[goodIndex].id }).then(res => {
          if (res.code === 200) {
            this.editableTabsValue = activeName
            this.editableTabs = tabs.filter(tab => tab.name !== targetName)
            this.Initialize()
          }
        })
      })
    },
    /**
     * @name: 选择分组
     * @param {*} e
     * @param {*} index
     */
    changeBox (e, index) {
      this.$nextTick(() => {
        this.$refs.addForm.resetFields()
      })
      switch (this.showGroupType) {
        case 1:
          this.editableTabs[index].checked = e
          let id = this.editableTabs[index].id
          if (this.editableTabs[index].checked) {
            let goodIndex = this.selectIds.indexOf(id)
            if (goodIndex === -1) {
              this.selectIds.push(id)
            }
          } else {
            let goodIndex = this.selectIds.indexOf(id)
            if (goodIndex !== -1) {
              let fils = this.selectIds
              this.selectIds = fils.filter((item, itemIndex) => {
                return item !== id
              })
            }
          }
          break
        case 2:
          this.editableTabs.map((item, aindex) => {
            item.checked = index === aindex
            if (index === aindex) {
              this.remove_param.group_id = item.id
            }
          })
          break
      }
    },
    // 删除多个tab
    delgroup () {
      if (this.selectIds.length === 0) {
        return this.$message.error(`请选择分组`)
      }
      this.configGroup(() => {
        this.$api.upload.delGroups({ group_id: this.selectIds }).then(res => {
          this.showGroup = false
          if (res.code === 200) {
            this.editableTabsValue = '全部'
            this.selectIds = []
            this.Initialize()
          }
        })
      })
    },
    subMitGroup () {
      this.showAddGroup = false
      let { name } = this.addForm
      if (name.split(' ').join('').length > 0) {
        if (this.updateGropId) {
          this.$api.upload.updateGroup({
            group_id: this.updateGropId,
            group: { name: name }
          }).then(res => {
            this.editableTabsValue = '全部'
            this.updateGropId = ''
            this.Initialize()
          })
        } else {
          this.$api.upload.addGroup({ name }).then(res => {
            this.Initialize()
          })
        }
      } else {
        this.$message.warning(`请输入分组名字!`)
      }
    },
    addShowGroup (index) {
      if (index && !this.isDisable) {
        this.name = this.editableTabs[index].name
        this.updateGropId = this.editableTabs[index].id
      } else {
        this.addForm.name = ''
      }
      this.showGroup = false
      if (this.editableTabs.length <= 20) {
        this.showAddGroup = true
      } else {
        this.$message.warning(`最多可添加20个分组!`)
      }
    },
    /**
     * @method 根据fileType处理不同的文件类型
     */
    handleFileType (type) {
      this.lbFileType = type
      this.uploadTitle = this.paramData.methodModel ? this.paramData.uploadTitle : `${this.$t(`title.${type}Upload`)}`
      this.acceptType = this.fileAccept ? this.fileAccept : `${type}/*`
      if (type !== 'image') this.defaultBGImg = require(`@/assets/${type}.png`)
      if (this.showUpload) this.getFileList()
    },
    async getGroups () {
      this.$api.upload.getGroupsList().then(res => {
        if (res.data.groups.length === 0) {
          this.isDisable = true
        } else {
          this.isDisable = false
        }
        if (this.paramData.methodModel) {
          this.editableTabs = [
            {
              name: '全部',
              id: 0,
              conent: ''
            }
          ]
        } else {
          this.editableTabs = [
            {
              name: '全部',
              id: 0,
              conent: ''
            }
          ]
          res.data.groups.map(item => {
            item.checked = false
            this.editableTabs.push(item)
          })
        }
      })
    },
    beforeClose () {
      this.fileLen = this.filesLength
      this.checkedFiles = {}
      this.showUpload = false
    },
    /**
     * @method 获取图片、音频、视频
     */
    getFileList () {
      let tabs = this.editableTabs
      tabs.forEach((item, index) => {
        if (item.name === this.editableTabsValue) {
          this.group_index = index
        }
      })
      let groupGoods = this.editableTabs[this.group_index]
      this.loading = true
      let { page, limit, lbFileType, paramData } = this
      let type = this.typeArr[lbFileType]
      let options = ''
      if (this.group_index !== 0) {
        options = {
          page: page,
          limit: limit,
          type: type,
          group_id: groupGoods.id
        }
      } else {
        options = {
          page: page,
          limit: limit,
          type: type,
          group_id: ''
        }
      }

      if (paramData.methodModel) {
        let { param } = paramData
        param.page = page
        param.limit = limit
        this.$api[paramData.methodModel](param).then(res => {
          this.loading = false
          if (res.code === 200) {
            this.fileList = res.data.data.map(item => {
              item.checked = false
              item.url = item.img || item.cover
              item.filename = item.title
              return item
            })
            this.total = res.data.total
          }
        })
      } else {
        this.$api.upload.getUploadFiles(options).then(res => {
          this.loading = false
          if (res.code === 200) {
            this.fileList = res.data.files.map(item => {
              item.checked = false
              item.url = item.longbing_attachment_path ? item.longbing_attachment_path : item.attachment
              return item
            })
            this.total = res.data.total
          }
        })
      }
    },
    configGroup (func) {
      this.$confirm('你确定要删除吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        if (typeof func === 'function') {
          func()
        }
        // this.$message({
        //   type: 'success',
        //   message: '删除成功!'
        // })
      })
      // .catch(() => {
      //   this.$message({
      //     type: 'info',
      //     message: '已取消删除'
      //   })
      // })
    },
    /**
     * @method 切换分页
     */
    handleCurrentChange (val) {
      this.checkedFiles = {}
      this.fileLen = this.filesLength
      this.page = val
      this.getFileList()
    },

    /**
     * @method 全选
     */
    handleCheckAllChange (check) {
      if (!check) {
        this.fileLen = 0
        this.checkAll = false
        this.checkedFiles = {}
        this.fileList.forEach((item, index) => {
          item.checked = false
        })
        return
      }
      this.fileLen = this.filesLength
      let { fileSizeNumber, fileLen: selectNum } = this
      let length = Object.values(this.checkedFiles).length
      if (length === fileSizeNumber) {
        this.checkedFiles = {}
        this.fileList.forEach((item, index) => {
          item.checked = false
        })
        return
      }
      this.fileList.forEach((item, index) => {
        if (selectNum === fileSizeNumber) {
          return
        }
        if (item.checked) {
          selectNum++
        } else {
          item.checked = !item.checked
          selectNum++
        }
        this.checkedFiles['file' + index] = item
      })
      this.fileLen = selectNum
    },
    /**
     * @method 选中的图片、视频、音频
     */
    choiceImg (index) {
      // alert(this.fileLen + '_' + this.fileSize)
      // if (!this.isIframe && this.multilineType !== 'single' && !this.fileList[index].checked && this.selectedFiles.length >= this.fileSizeNumber) {
      if (!this.isIframe && this.multilineType !== 'single' && !this.fileList[index].checked && this.fileLen >= this.fileSize) {
        let unit = this.fileType === 'image' ? '张图片' : `个${this.typeTextArr[this.fileType]}`
        this.$message.warning(`最多可选择${this.fileSizeNumber}${unit}!`)
        return false
      } else {
        if (this.multilineType === 'single') {
          this.fileLen = 0
          this.fileList.map(item => {
            item.checked = false
          })
        }
        if (this.fileList[index].checked) this.fileLen--
        else this.fileLen++
        this.fileList[index].checked = !this.fileList[index].checked
        if (this.fileList[index].checked) {
          this.checkedFiles['file' + index] = this.fileList[index]
        } else {
          delete this.checkedFiles['file' + index]
        }
        let arr = this.fileList.filter(item => {
          return item.checked
        })
        this.isIndeterminate = arr.length > 0 && arr.length < this.fileList.length
        this.checkAll = arr.length === this.fileList.length
      }
    },
    /**
     * @name: 移动图片至其他分组
     * @param {*} id
     */
    toRemove (id) {
      let ids = id ? [id] : this.selectedFiles.map(item => {
        return item.id
      })
      if (ids.length < 1) {
        this.$message.error(`请选择要移动的文件`)
        return
      }
      this.remove_param.file_id = ids
      this.showGroupType = 2
      this.showGroup = true
    },
    /**
     * @name: 删除/移动 确认框
     * @param {*} key 1删除；2移动
     * @param {*} type
     * @param {*} fid
     */
    confirmDel (key, type, fid) {
      if (key === 1 && type === 'more' && this.selectedFiles.length === 0) {
        this.$message.error(`请选择要删除的文件`)
        return
      }
      if (key === 2 && !this.remove_param.group_id) {
        this.$message.error(`请选择分组`)
        return
      }
      this.$confirm(this.$t(key === 1 ? 'tips.confirmDelete' : 'tips.confirmRemove'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        let param = key === 1 ? type === 'more' ? {
          ids: this.selectedFiles.map(item => {
            return item.id
          })
        } : { ids: [fid] } : this.remove_param
        let methodModel = key === 1 ? 'delFiles' : 'moveGroup'
        this.$api.upload[methodModel](param).then(res => {
          if (res.code === 200) {
            if (key === 2) {
              this.remove_param = {
                file_id: [],
                group_id: 0
              }
              this.showGroup = false
              this.editableTabs.map(item => {
                item.checked = false
              })
            }
            this.$message.success(this.$t(key === 1 ? 'tips.successDel' : 'tips.successRemove'))
            this.handleCheckAllChange(false)
            this.getFileList()
          }
        })
      })
    },
    // 本地上传
    async localOssUpload (formData) {
      this.$api.upload.uploadFiles(formData, this.getProgress).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successUpload'))
          this.$nextTick(() => {
            this.$refs['inputImg'].value = null
          })
          this.getFileList()
        }
      })
    },
    // 阿里云 分片上传
    async aLiYunOssUpload (file, fileName, groupId, uploadType) {
      let { aliyun_access_key_id: accessKeyId, aliyun_access_key_secret: accessKeySecret, aliyun_bucket: bucket, aliyun_endpoint: endpoint } = this.uploadConfig
      let region = endpoint.split('.')[0]
      const client = new OSS({
        // region以杭州为例（oss-cn-hangzhou），其他region按实际情况填写。
        region,
        // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM用户进行API访问或日常运维，请登录RAM控制台创建RAM用户。
        accessKeyId,
        accessKeySecret,
        bucket
      })
      const progress = (p, _checkpoint) => {
        // console.log(_checkpoint, '分片上传的断点信息。') // 分片上传的断点信息。
        this.progress = parseInt((p * 100))
        if (this.progress >= 100) {
          setTimeout(() => {
            this.showProgress = false
          }, 500)
        }
      }
      let that = this
      try {
        // object-name可以定义为文件名（例如file.txt）或目录（例如abc/test/file.txt）的形式，实现将文件上传至Bucket根目录或Bucket下的指定目录。
        await client.multipartUpload(fileName, file, {
          progress,
          // meta是用户自定义的元数据，通过head接口可以获取到Object的meta数据。
          meta: {
            year: 2021,
            people: 'upload'
          },
          headers: {
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Headers': 'Origin,X-Requested-With,Content-Type,Accept,Authorization,sourcetype,token',
            'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, DELETE',
            'Access-Control-Max-Age': 3600,
            'Access-Control-Allow-Credentials': true,
            'Content-Length': 0,
            'Content-Type': file.type // 注意：根据图片或者文件的后缀来设置
          }
        })
        let head = await client.head(fileName)
        let param = {
          attachment: fileName,
          group_id: groupId,
          filename: file.name,
          type: uploadType,
          longbing_driver: 'aliyun'
        }
        that.toAddFile(param)
      } catch (e) {
        // 捕获超时异常。
        if (e.code === 'ConnectionTimeoutError') {
          console.log('TimeoutError')
        }
        console.log(e)
      }
    },
    // 七牛云上传
    async qiNiuYunOssUpload (file, fileName, groupId, uploadType) {
      let { qiniu_accesskey: AK, qiniu_secretkey: SK, qiniu_bucket: bucketName } = this.uploadConfig
      var token
      var policy = {}
      var deadline = Math.round(new Date().getTime() / 1000) + 3600
      policy.scope = bucketName
      policy.deadline = deadline
      token = genUpToken(AK, SK, policy)

      console.log('beforeUpload', file)
      var config = {
        useCdnDomain: true,
        region: null
      }
      var putExtra = {
        fname: fileName,
        params: {},
        mimeType: file.type
      }

      let that = this
      var observer = {
        next (res) {
          that.progress = parseInt(res.total.percent * 1)
          if (that.progress >= 100) {
            setTimeout(() => {
              that.showProgress = false
            }, 500)
          }
        },
        error (err) {
          console.log('subscription error =>', err)
        },
        complete (res) {
          console.log('subscription complete =>', res)
          let { key = '' } = res
          if (!key) return

          let param = {
            attachment: fileName,
            group_id: groupId,
            filename: file.name,
            type: uploadType,
            longbing_driver: 'qiniuyun'
          }
          that.toAddFile(param)
        }
      }

      let observable = qiniu.upload(file, fileName, token, putExtra, config)
      let subscription = observable.subscribe(observer) // 上传开始
      console.log('subscription', subscription)
      // subscription.unsubscribe() // 上传取消
    },
    async tengXunYunCosUpload (file, fileName, groupId, uploadType) {
      let { tenxunyun_secretid: SecretId, tenxunyun_secretkey: SecretKey, tenxunyun_bucket: Bucket, tenxunyun_region: Region } = this.uploadConfig
      var cos = new COS({
        SecretId,
        SecretKey
      })
      let that = this
      that.showProgress = true
      cos.putObject({
        Bucket, /* 必须 */
        Region, /* 存储桶所在地域，必须字段 */
        Key: fileName, /* 必须 */
        StorageClass: 'STANDARD',
        Body: file, // 上传文件对象
        onProgress: function (progressData) {
          that.progress = parseInt(progressData.percent * 100)
          if (that.progress >= 100) {
            setTimeout(() => {
              that.showProgress = false
            }, 500)
          }
        }
      }, function (err, data) {
        console.log(err || data)

        let param = {
          attachment: fileName,
          group_id: groupId,
          filename: file.name,
          type: uploadType,
          longbing_driver: 'tengxunyun'
        }
        that.toAddFile(param)
      })
    },
    async toAddFile (param) {
      await this.$api.upload.uploadAddFile(param)
      this.$nextTick(() => {
        this.$refs['inputImg'].value = null
      })
      await this.getFileList()
    },
    /**
     * @method 上传图片，视频，音频
     */
    async uploadFiles (e) {
      let { lbFileType } = this
      let files = e.target.files
      if (files.length === 0) return false
      this.editableTabs.forEach((item, index) => {
        if (item.name === this.editableTabsValue) {
          this.group_index = index
        }
      })
      let { id: groupId } = this.editableTabs[this.group_index]

      let { data: uploadConfig } = await this.$api.system.getOssConfig()
      let { open_oss: openOss, uniacid } = uploadConfig
      this.uploadConfig = uploadConfig

      // openOss 0本地上传，1阿里云OSS上传，2七牛云上传，3腾讯云上传
      let uploadType = lbFileType === 'image' ? 'picture' : lbFileType

      let formData = new FormData()

      let date = new Date().getTime()
      let year = moment(date).format('YY')
      let month = moment(date).format('MM')

      // key 上传路径：上传文件类型/小程序unicaid/年份/月份/上传文件名
      let baseDir = openOss === 1 ? `${uploadConfig.aliyun_base_dir}/` : ``

      for (let i = 0, len = files.length; i < len; i++) {
        let resetName = this.$util.randomWord(false, 32) + '.' + files[i].name.split('.')[1]
        let fileName = `${baseDir}${lbFileType}/${uniacid}/${year}/${month}/${resetName}`
        if (lbFileType === 'image') {
          const isGIF = files[i].type === 'image/gif'
          const isJPG = files[i].type === 'image/jpg'
          const isJPEG = files[i].type === 'image/jpeg'
          const isBMP = files[i].type === 'image/bmp'
          const isPNG = files[i].type === 'image/png'
          if (!isPNG && !isJPEG && !isJPG && !isBMP && !isGIF) {
            this.$nextTick(() => {
              this.$refs['inputImg'].value = null
            })
            this.$message.error('只支持上传gif/jpg/jpeg/bmp/png格式的图片!')
            return false
          }

          const isLt2M = files[i].size / 1024 / 1024 < 10
          if (!isLt2M) {
            this.$nextTick(() => {
              this.$refs['inputImg'].value = null
            })
            this.$message.error('上传图片大小不能超过 10MB!')
            return false
          }
        }

        this.progress = 0
        this.showProgress = true
        switch (openOss) {
          case 0:
            formData.append('file[]', files[i])
            break
          case 1:
            this.aLiYunOssUpload(files[i], fileName, groupId, this.typeArr[uploadType])
            break
          case 2:
            this.qiNiuYunOssUpload(files[i], fileName, groupId, this.typeArr[uploadType])
            break
          case 3:
            this.tengXunYunCosUpload(files[i], fileName, groupId, this.typeArr[uploadType])
            break
        }
      }
      if (openOss === 0) {
        formData.append('group_id', groupId)
        formData.append('type', uploadType)
        this.localOssUpload(formData)
      }
    },

    /**
     * @method 提取网络视频
     */
    getNetWorkVideo () {
      let { networkVideo } = this
      let _html = ''
      if (networkVideo.indexOf('<iframe') >= 0) {
        let htmlArr = networkVideo.split('src')
        _html = htmlArr[0] + ` wdith='300' height='200' src` + htmlArr[1]
      } else {
        _html = `<iframe frameborder="0" src="${networkVideo}" wdith='300' height='200' allowFullScreen="true"></iframe>`
      }
      this.$emit('selectedFiles', [{ type: 5, url: _html }])
      this.showUpload = false
    },

    /**
     * @method 获取上传的进度
     */
    getProgress (e) {
      this.progress = parseInt(e.loaded / e.total * 100) || 0
      if (this.progress >= 100) {
        setTimeout(() => {
          this.showProgress = false
        }, 500)
      }
    },

    /**
     * @method 点击确定选中的图片
     */
    selectedImgs () {
      let { fileList, checkedFiles, fileType, typeTextArr } = this
      let checkedImgs = Object.values(checkedFiles)
      fileList.forEach(item => {
        item.checked = false
      })
      if (checkedImgs && checkedImgs.length === 0) {
        this.$message.error(`请选择${typeTextArr[fileType]}`)
        return
      }
      this.$emit('selectedFiles', checkedImgs)
      this.showUpload = false
      this.checkedFiles = {}
    }
  },
  watch: {
    showAddGroup (val) {
      this.$nextTick(() => {
        this.$refs.addForm.resetFields()
      })
    },
    showUpload (val) {
      this.editableTabsValue = '全部'
      this.selectIds = []
      // this.getGroups()
      if (!val) {
        // let goodIndex = parseInt(this.editableTabsValue.split('&')[1])
        // let groupGoods = this.editableTabs[goodIndex]
        // groupGoods.content = []
        // this.fileList = []
        this.page = 1
        this.total = 0
      }
      this.$emit('update:visibles', val)
    },
    visibles (val) {
      this.handleFileType(this.fileType)
      this.showUpload = val
      if (val) {
        this.getFileList()
      }
    },
    fileList: {
      deep: true,
      handler (val) {
        let checkedArr = val.filter(item => {
          return item.checked
        })
        this.checkAll = checkedArr.length === val.length && val.length !== 0
        this.isIndeterminate = checkedArr.length > 0 && checkedArr.length < val.length
      }
    },
    filesLength (val) {
      this.fileLen = val
    }
  },
  computed: {
    selectedFiles () {
      return this.fileList.filter(item => {
        return item.checked
      })
    }
  },
  filters: {
    filterCheked (arr) {
      let a = arr.filter(item => {
        return item.checked
      })
      return a.length
    }
  },
  beforeDestroy () {
    this.$store.commit('handleUploadStatus', false)
  }
}
</script>

<style lang="scss" scoped>
.fen_item {
  display: flex;
  align-items: center;
}
.group_item {
  display: flex;
  margin: 10px 0;
  align-items: center;
  justify-content: space-between;
}
.btn_view {
  margin: 0 50px;
  // display: flex;
  // align-items: center;
  // justify-content: space-between;
  .el-button {
    margin: 0 15px;
  }
}
.img-view {
  width: 100%;
  height: 398px;
  padding: 20px 0;
  // border-top: 1px solid $lineColor;
  border-bottom: 1px solid $lineColor;
  display: flex;
  flex-wrap: wrap;
  position: relative;
  .loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, 50%);
  }
  .img-item {
    width: 16%;
    height: 140px;
    background-color: $columnBgColor;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center center;
    margin: 2%;
    position: relative;
    border: 2px solid $columnBgColor;
    cursor: pointer;
    .img-name {
      width: 100%;
      height: 32px;
      position: absolute;
      bottom: 0px;
      right: 0;
      left: 0;
      margin: auto;
      background: rgba($color: #000000, $alpha: 0.5);
      z-index: 10;
      line-height: 32px;
      color: #fff;
      padding: 0 20px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .icon-selected {
      display: inline-block;
      width: 100%;
      height: 100%;
      background: rgba($color: #000000, $alpha: 0.5);
      position: absolute;
      top: 0;
      left: 0;
      z-index: 15;
      color: #fff;
      font-size: 60px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .icon-d {
      display: none;
      width: 32px;
      height: 32px;
      background: #f56c6c;
      color: #fff;
      text-align: center;
      line-height: 32px;
      position: absolute;
      right: 0;
      bottom: 0;
      z-index: 20;
      font-size: 20px;
    }
    .icon-d.el-icon-upload {
      background: #e6a23c;
    }
    &:hover {
      border: 2px solid #4c88d8;
      width: 16%;
      .icon-d {
        display: inline-block;
      }
    }
  }
}
.lb-tabs .img-view {
  border-top: none;
}
.top-video-switch {
  width: 100%;
  border-bottom: 1px solid $lineColor;
}
.get-network-video {
  height: 398px;
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  align-items: center;
  .el-input {
    width: 500px;
  }
}
.upload-page {
  margin-top: 20px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  .operate {
    display: flex;
    align-items: center;
    .el-checkbox {
      margin-right: 10px;
    }
    .count {
      color: #4c88d8;
      margin-left: 5px;
      margin-right: 10px;
    }
    .el-button {
      margin-left: 10px;
    }
  }
}
</style>
