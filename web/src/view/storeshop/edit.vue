<!--
 * @Description: 编辑服务
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-11-20 14:12:57
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-system-banner-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="130px"
      >
        <el-form-item label="关联用户" prop="user_id">
          <el-tag
            class="cursor-pointer"
            :type="have_user_id ? 'info' : 'primary'"
            @click="toShowDialog('user')"
            >{{ subForm.user_id ? subForm.nickName : '选择关联用户' }}</el-tag
          >
        </el-form-item>
        <el-form-item label="店铺名称" prop="name">
          <el-input
            v-model="subForm.name"
            maxlength="15"
            show-word-limit
            placeholder="请输入店铺名称"
          ></el-input>
        </el-form-item>
        <el-form-item prop="cover" label="店铺封面">
          <lb-cover
            :fileList="subForm.cover"
            @selectedFiles="getCover($event, 'cover')"
          ></lb-cover>
          <lb-tool-tips>图片比例: 1:1</lb-tool-tips>
        </el-form-item>
        <el-form-item label="店铺详情图" prop="banner">
          <lb-cover
            :fileList="subForm.banner"
            fileType="image"
            type="more"
            @selectedFiles="getBannerList($event, 'banner')"
            :fileSize="9"
          ></lb-cover>
          <lb-tool-tips>图片比例: 750*564</lb-tool-tips>
        </el-form-item>
        <el-form-item
          label="商家联系方式"
          prop="contact_type"
          v-if="im_type == 3"
        >
          <el-radio-group v-model="subForm.contact_type">
            <el-radio :label="1">电话</el-radio>
            <el-radio :label="2">企业微信</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item
          label="商家电话"
          prop="mobile"
          v-if="subForm.contact_type == 1 || im_type != 3"
        >
          <el-input
            v-model="subForm.mobile"
            placeholder="请输入手机号或者座机号"
          ></el-input>
        </el-form-item>
        <el-form-item
          label="企业微信客服"
          prop="qywx_kid"
          v-if="subForm.contact_type == 2 && im_type == 3"
        >
          <el-input
            v-model="subForm.qywx_kid"
            placeholder="请输入企业微信客服"
          ></el-input>
          <a
            class="c-warning ml-lg"
            href="https://docs.qq.com/doc/DWkxQVkJjU0VLUUhs"
            target="_blank"
            >点击查看配置企业微信客服文档</a
          >
        </el-form-item>
        <el-form-item prop="license" label="营业执照">
          <lb-cover
            :fileList="subForm.license"
            @selectedFiles="getCover($event, 'license')"
          ></lb-cover>
          <!-- <lb-tool-tips>图片建议尺寸: 334 * 392</lb-tool-tips> -->
        </el-form-item>
        <el-form-item label="店铺地址" prop="address">
          <el-input
            v-model="subForm.address"
            placeholder="请输入店铺地址"
          ></el-input>
        </el-form-item>
        <el-form-item label="经度" prop="lng">
          <el-input v-model="subForm.lng" placeholder="请输入经度"></el-input>
        </el-form-item>
        <el-form-item label="纬度" prop="lat">
          <el-input v-model="subForm.lat" placeholder="请输入纬度"></el-input>
          <lb-button @click="showMap = true" type="primary" plain size="mini"
            >获取经纬度</lb-button
          >
        </el-form-item>
        <el-form-item label="门牌号" prop="info">
          <el-input
            v-model="subForm.info"
            placeholder="请输入门牌号"
          ></el-input>
        </el-form-item>
        <el-form-item label="营业时间" prop="trade_week">
          <div class="trade-week">
            <el-slider
              @change="changeTradeWeek"
              v-model="subForm.trade_week"
              range
              show-stops
              :marks="weekMarks"
              :max="6"
              :show-tooltip="false"
            >
            </el-slider>
          </div>
          <div class="flex-y-center">
            <el-time-select
              placeholder="开始时间"
              v-model="subForm.start_time"
              :picker-options="{
                start: '00:00',
                step: '00:01',
                end: '23:59'
              }"
              style="width: 150px"
            ></el-time-select>
            <span class="pl-md pr-md">至</span>
            <el-time-select
              placeholder="结束时间"
              v-model="subForm.end_time"
              :picker-options="{
                start: '00:00',
                step: '00:01',
                end: '23:59'
              }"
              style="width: 150px"
            ></el-time-select>
            <lb-button
              icon="el-icon-refresh-left"
              style="margin-left: 10px"
              @click="resetTime"
              type="warning"
              >{{ $t('action.reset') }}</lb-button
            >
          </div>
        </el-form-item>
        <el-form-item label="门店所属分类" prop="type_id">
          <!-- <el-select v-model="subForm.type_id" placeholder="请选择">
            <el-option
              v-for="item in base_tag"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            >
            </el-option>
          </el-select> -->
          <el-cascader
            placeholder="请选择"
            :options="base_tag"
            v-model="subForm.type_id"
            :props="{
              checkStrictly: true,
              label: 'name',
              value: 'id',
              multiple: true
            }"
            filterable
          >
          </el-cascader>
        </el-form-item>
        <el-form-item label="门店标签" prop="tag">
          <el-tag
            :key="tag"
            v-for="(tag, index) in dynamicTags"
            :closable="tag.disable"
            :disable-transitions="false"
            @close="handleClose(index)"
            class="mr-md"
            :effect="tag.effect"
            :type="tag.effect == 'plain' ? 'info' : ''"
            @click="changeTag(index)"
          >
            {{ tag.value }}
          </el-tag>
          <el-input
            class="input-new-tag"
            v-if="inputVisible"
            v-model="inputValue"
            ref="saveTagInput"
            size="small"
            @keyup.enter.native="handleInputConfirm"
            @blur="handleInputConfirm"
            maxlength="10"
            show-word-limit
            style="width: 220px"
          >
          </el-input>
          <el-button
            v-else
            class="button-new-tag"
            size="small"
            @click="showInput"
            >+ 添加</el-button
          >
        </el-form-item>
        <el-form-item label="场地介绍" prop="intro">
          <el-input
            type="textarea"
            :rows="12"
            maxlength="400"
            resize="none"
            show-word-limit
            placeholder="请输入场地介绍"
            v-model="subForm.intro"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <lb-button type="primary" @click="submitForm" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>

      <el-dialog
        title="关联用户"
        :visible.sync="showDialog.user"
        width="800px"
        center
      >
        <el-form
          :inline="true"
          :model="searchForm.user"
          ref="userForm"
          label-width="70px"
        >
          <el-form-item label="输入查询" prop="nickName">
            <el-input
              v-model="searchForm.user.nickName"
              placeholder="请输入用户昵称/手机号"
            ></el-input>
          </el-form-item>
          <el-form-item>
            <lb-button
              size="medium"
              type="primary"
              icon="el-icon-search"
              style="margin-right: 5px"
              @click="getTableDataList(1, 'user')"
              >{{ $t('action.search') }}</lb-button
            >
            <lb-button
              size="medium"
              icon="el-icon-refresh-left"
              style="margin-right: 5px"
              @click="resetForm('user')"
              >{{ $t('action.reset') }}</lb-button
            >
          </el-form-item>
        </el-form>
        <el-table
          :data="tableData.user"
          ref="singleTable"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
          highlight-current-row
          @current-change="handleSelectionChange($event, 'user')"
        >
          <el-table-column prop="id" label="用户ID"></el-table-column>
          <el-table-column prop="avatarUrl" label="头像">
            <template slot-scope="scope">
              <lb-image :src="scope.row.avatarUrl" />
            </template>
          </el-table-column>
          <el-table-column prop="nickName" label="昵称"></el-table-column>
          <el-table-column prop="phone" label="手机号"></el-table-column>
        </el-table>
        <lb-page
          :batch="false"
          :page="searchForm.user.page"
          :pageSize="searchForm.user.limit"
          :total="total.user"
          @handleSizeChange="handleSizeChange($event, 'user')"
          @handleCurrentChange="handleCurrentChange($event, 'user')"
        >
        </lb-page>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.user = false">取 消</el-button>
          <el-button type="primary" @click="handleDialogConfirm('user')"
            >确 定</el-button
          >
        </span>
      </el-dialog>
      <lb-map
        :dialogVisible.sync="showMap"
        @selectedLatLng="getLatLng"
      ></lb-map>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
export default {
  data () {
    let validateTime = (rule, value, callback) => {
      let { start_time: start, end_time: end, trade_week: week } = this.subForm
      if (!start || !end) {
        callback(new Error(!start ? `请选择开始时间` : `请选择结束时间`))
      } else if (week[0] === week[1]) {
        if (start >= end) {
          callback(new Error(`开始时间不能大于结束时间`))
        } else {
          callback()
        }
      } else {
        callback()
      }
    }
    return {
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      id: '',
      navTitle: '',
      showMap: false,
      have_user_id: false,
      subForm: {
        id: 0,
        user_id: '',
        nickName: '',
        name: '',
        cover: '',
        banner: [],
        mobile: '',
        license: '',
        lng: '',
        lat: '',
        address: '',
        info: '',
        tag: [],
        intro: '',
        type_id: [],
        trade_week: [0, 6],
        start_time: '',
        end_time: '',
        contact_type: 1,
        qywx_kid: ''
      },
      subFormRules: {
        user_id: { required: true, type: 'number', message: '请选择关联用户', trigger: 'blur' },
        name: { required: true, validator: this.$reg.isNotNull, text: '店铺名称', reg_type: 2, trigger: 'blur' },
        cover: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        banner: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        mobile: { required: true, validator: this.$reg.isTelOrPhone, text: '手机号或者座机号' },
        license: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        lng: { required: true, validator: this.$reg.isLng, trigger: 'blur' },
        lat: { required: true, validator: this.$reg.isLat, trigger: 'blur' },
        address: { required: true, type: 'string', message: '请输入店铺地址', trigger: 'blur' },
        info: { required: true, type: 'string', message: '请输入门牌号', trigger: 'blur' },
        tag: { required: true, type: 'array', message: '请选择门店标签', trigger: 'blur' },
        intro: { required: true, type: 'string', message: '请输入场地介绍', trigger: 'blur' },
        type_id: { required: true, type: 'array', message: '请选择门店所属分类', trigger: 'blur' },
        trade_week: { required: true, validator: validateTime, trigger: 'blur' },
        contact_type: { required: true, type: 'number', message: '请选择商家联系方式', trigger: 'blur' },
        qywx_kid: { required: true, type: 'string', message: '请输入企业微信客服', trigger: 'blur' }
      },
      searchForm: {
        user: {
          page: 1,
          limit: 10,
          nickName: ''
        },
        service: {
          page: 1,
          limit: 10,
          name: ''
        }
      },
      total: { user: 0, service: 0 },
      loading: { user: false, service: false },
      tableData: { user: [], service: [] },
      showDialog: { user: false, service: false },
      currentRow: {},
      multipleSelection: [],
      cash_type: '',
      coachPriceList: [],
      userInfo: {},
      dynamicTags: [
        { value: '空调开放', disable: false, effect: 'plain' },
        { value: '免费wifi', disable: false, effect: 'plain' },
        { value: '免费停车位', disable: false, effect: 'plain' }
      ],
      inputVisible: false,
      inputValue: '',
      base_tag: [],
      weekMarks: {
        0: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周一')
        },
        1: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周二')
        },
        2: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周三')
        },
        3: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周四')
        },
        4: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周五')
        },
        5: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周六')
        },
        6: {
          style: {
            color: '#1989FA'
          },
          label: this.$createElement('strong', '周天')
        }
      },
      im_type: 1
    }
  },
  async created () {
    // this.userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
    // if (this.userInfo.is_admin !== 1) {
    //   this.subFormRules.service.required = false
    // }
    let { id } = this.$route.query
    if (id) {
      this.subForm.id = id
      await this.getDetail(id)
    }
    this.navTitle = this.$t(id ? 'menu.StoreEdit' : 'menu.StoreAdd')
    this.getBaseInfo()
    this.getConfigInfo()
  },
  watch: {
    'subForm.birthday' (newValue, oldValue) {
      if (newValue) {
        let month = moment(newValue).format('MM')
        let day = moment(newValue).format('DD')
        this.subForm.constellation = this.$util.getAstro(month, day)
      }
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    handleClose (index) {
      this.dynamicTags.splice(index, 1)
    },

    showInput () {
      this.inputVisible = true
      this.$nextTick(_ => {
        this.$refs.saveTagInput.$refs.input.focus();
      })
    },
    changeTag (index) {
      let effect = this.dynamicTags[index].effect
      this.dynamicTags[index].effect = effect === 'plain' ? 'dark' : 'plain'
      let tagArr = []
      this.dynamicTags.forEach(item => {
        if (item.effect === 'dark') {
          tagArr.push(item.value)
        }
      })
      this.subForm.tag = tagArr
    },

    handleInputConfirm () {
      let inputValue = this.inputValue
      if (inputValue) {
        let tagInd = this.dynamicTags.findIndex(item => {
          return item.value === inputValue
        })
        if (tagInd === -1) {
          this.dynamicTags.push({ value: inputValue, disable: true, effect: 'plain' })
        }
        if (tagInd !== -1) {
          this.$message.error(`门店标签不能相同`)
          return
        }
      }
      this.inputVisible = false
      this.inputValue = ''
    },
    async getConfigInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.im_type = data.im_type
      this.cash_type = data.cash_type
    },
    async getBaseInfo () {
      let { data } = await this.$api.storeshop.typeListNoPage()
      this.base_tag = data
    },
    getCover (img, key) {
      this.subForm[key] = img
    },
    getBannerList (imgs, key) {
      this.subForm[key].push(...imgs)
    },
    /**
     * @method 获取经纬度
     */
    getLatLng (latLng) {
      this.subForm.lat = latLng.lat
      this.subForm.lng = latLng.lng
      if (latLng.address) {
        this.subForm.address = latLng.address
      }
    },
    /**
     * @name: 详情
     * @param {*} id
     */
    async getDetail (id) {
      let { code, data } = await this.$api.storeshop.info({ id })
      if (code !== 200) return
      let arr = ['banner', 'cover', 'license']
      arr.map((item) => {
        data[item] = data[item] ? data[item].split(',').map((aitem) => {
          return { url: aitem }
        }) : []
      })
      data.nickName = data.user_id ? data.user_name : ''
      let min = data.trade_week.substring(0, 1)
      let max = data.trade_week.substring(data.trade_week.length - 1) == 0 ? 7 : data.trade_week.substring(data.trade_week.length - 1)
      data.trade_week = [min * 1 - 1, max * 1 - 1]
      let tag = data.tag.split(',')
      tag.forEach(item => {
        let ind = this.dynamicTags.findIndex(li => {
          return li.value === item
        })
        if (ind === -1) {
          this.dynamicTags.push({ value: item, disable: true, effect: 'dark' })
        } else {
          this.dynamicTags[ind].effect = 'dark'
        }
      })
      data.tag = tag
      // this.dynamicTags.push({ value: inputValue, disable: true, effect: 'plain' })
      data.type_id = data.type_id.split(',').map(item => {
        return [item]
      })
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      // data.nickName = data.user_id
      //   ? data.nickName || `用户ID ${data.user_id}`
      //   : ''
      // data.work_img = [{ url: data.work_img }]
      // data.tag_id = data.tag_id || ''
      // let arr = ['id_card', 'self_img', 'model_img']
      // arr.map((item) => {
      //   data[item] = data[item] ? data[item].map((aitem) => {
      //     return { url: aitem }
      //   }) : []
      // })
      // data.birthday = data.birthday * 1000
      // for (let key in this.subForm) {
      //   this.subForm[key] = data[key]
      // }
      // this.have_user_id = data.id && data.user_id
      // let coachPriceList = []
      // data.service.forEach(item => {
      //   coachPriceList.push({ ser_id: item.id, price: item.price || 0 })
      // })
      // this.coachPriceList = coachPriceList
    },
    async toShowDialog (key) {
      if (key === 'user') {
        let { have_user_id: have } = this
        if (have) return
        this.searchForm[key].nickName = ''
      } else {
        this.searchForm[key].name = ''
      }
      await this.getTableDataList(1, key)
      this.showDialog[key] = !this.showDialog[key]
    },
    resetForm (form) {
      let name = `${form}Form`
      this.$refs[name].resetFields()
      this.getTableDataList(1, form)
    },
    handleSizeChange (val, key) {
      this.searchForm[key].limit = val
      this.handleCurrentChange(1, key)
    },
    handleCurrentChange (val, key) {
      this.searchForm[key].page = val
      this.getTableDataList('', key)
    },
    /**
     * @method 列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.loading[key] = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))

      let methodArr = {
        user: { methodKey: 'storeshop', methodModel: 'storeUserList' },
        service: { methodKey: 'service', methodModel: 'serviceList' }
      }
      let { methodKey, methodModel } = methodArr[key]
      let { code, data } = await this.$api[methodKey][methodModel](searchForm)
      this.loading[key] = false
      if (code !== 200) return
      this.tableData[key] = data.data
      this.total[key] = data.total
    },
    handleSelectionChange (val, key) {
      if (key === 'user') {
        val = JSON.parse(JSON.stringify(val))
        let { id, nickName } = val
        val.nickName = nickName || `用户ID ${id}`
        this.currentRow = val
        return
      }
      this.multipleSelection = val
    },
    handleDialogConfirm (key) {
      if (key === 'user') {
        if (this.currentRow === null || !this.currentRow.id) {
          this.$message.error(`请选择用户`)
          return
        }
        let { id = 0, nickName = '' } = this.currentRow
        this.subForm.user_id = id
        this.subForm.nickName = nickName
      } else {
        let service = JSON.parse(JSON.stringify(this.subForm.service))
        let arr1 = service.length > 0 ? service.map(item => { return item.id }) : []
        this.multipleSelection.map(item => {
          if (arr1.includes(item.id)) return
          service.push(item)
        })
        this.subForm.service = service
        let coachPriceList = []
        service.forEach(item => {
          coachPriceList.push({ ser_id: item.id, price: item.price || 0 })
        })
        this.coachPriceList = coachPriceList
      }
      this.showDialog[key] = false
    },
    confirmDel (id) {
      let index = this.subForm.service.findIndex(item => {
        return item.id === id
      })
      this.subForm.service.splice(index, 1)
      let coachPriceList = []
      this.subForm.service.forEach(item => {
        coachPriceList.push({ ser_id: item.id, price: item.price || 0 })
      })
      this.coachPriceList = coachPriceList
    },
    /**
     * @name: 新增/编辑
     * @param {*}
     */
    submitForm () {
      console.log(this.subForm)
      let flag = true
      this.$refs['subForm'].validate((valid) => {
        if (!valid) flag = false
      })
      console.log(flag)
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        let arr = ['banner']
        arr.map((item) => {
          subForm[item] = subForm[item].length > 0 && subForm[item].map((aitem) => {
            return aitem.url
          })
        })
        let weekArr = this.$util.betweenNumbers(...subForm.trade_week).split(',')
        let newWeekArr = []
        weekArr.forEach(item => {
          item = item * 1 + 1
          if (item === 7) {
            item = 0
          }
          newWeekArr.push(item)
        })
        subForm.type_id = subForm.type_id.map(item => {
          return item[0]
        }).join(',')
        subForm.trade_week = newWeekArr
        subForm.cover = subForm.cover[0].url
        subForm.license = subForm.license[0].url
        delete subForm.nickName
        let edit = 'editStore'
        let add = 'addStore'
        // if (this.userInfo.is_admin !== 1) { // 1平台
        //   edit = 'coachUpdateAdmin'
        // }
        let methodModel = subForm.id ? edit : add
        this.$api.storeshop[methodModel](subForm).then((res) => {
          if (res.code === 200) {
            this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
            this.$router.back(-1)
          }
        })
      }
    },
    setPrice (index) {
      if (!this.coachPriceList[index].price) {
        this.coachPriceList[index].price = 0
      }
    },
    changeTradeWeek (numArr) {
      let numberArr = this.$util.betweenNumbers(...numArr).split(',')
      for (let key in this.weekMarks) {
        this.weekMarks[key].style.color = '#909399'
        if (numberArr.includes(key)) {
          this.weekMarks[key].style.color = '#1989FA'
        }
      }
    },
    resetTime () {
      this.subForm.start_time = ''
      this.subForm.end_time = ''
      this.subForm.trade_week = [0, 6]
      this.changeTradeWeek([0, 6])
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-banner-edit {
  width: 100%;
  .el-form {
    width: 100%;
    .el-select,
    .el-input-number,
    .el-cascader,
    .el-input {
      width: 300px;
    }
    .el-textarea {
      width: 600px;
    }
    .el-tag {
      cursor: pointer;
    }
    .trade-week {
      width: 500px;
      padding: 0 10px 40px 10px;
    }
    .el-slider {
      white-space: nowrap;
    }
  }
}
</style>
