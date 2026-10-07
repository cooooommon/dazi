<!--
 * @Descripttion: 编辑商品
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2024-02-27 10:15:59
-->
<template>
  <div class="lb-goods-edit">
    <top-nav :title="navTitle" :isBack="true"></top-nav>
    <div class="page-main">
      <lb-classify-title title="基本信息"></lb-classify-title>
      <div style="height: 20px"></div>
      <el-form
        @submit.native.prevent
        :model="imgForm"
        :rules="imgFormRules"
        ref="imgForm"
        label-width="180px"
        class="img-form"
      >
        <el-form-item label="商品名称" prop="name">
          <el-input
            type="text"
            v-model="imgForm.name"
            placeholder="请输入商品名称"
            maxlength="38"
            show-word-limit
          ></el-input>
        </el-form-item>
        <el-form-item label="选择商品分类" prop="carte">
          <el-cascader
            v-model="imgForm.carte"
            :options="options"
            :props="defaultParams"
            clearable
          ></el-cascader>
        </el-form-item>
        <lb-classify-title title="商品信息"></lb-classify-title>
        <el-form-item label="封面图" prop="cover">
          <lb-cover
            :fileList="imgForm.cover"
            @selectedFiles="getGoodsCover"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸: 345 * 350</lb-tool-tips>
        </el-form-item>
        <el-form-item label="轮播图" prop="images">
          <lb-cover
            type="more"
            :fileList="imgForm.images"
            @selectedFiles="selectedFiles($event, 'images')"
            @moveFiles="moveFiles($event, 'images')"
          ></lb-cover>
          <lb-tool-tips>图片建议尺寸: 750 * 564</lb-tool-tips>
        </el-form-item>
        <!-- <el-form-item label="轮播图链接类型">
          <el-select
            @change="imgForm.image_url = ''"
            v-model="imgForm.jump_type"
            placeholder="请选择"
          >
            <el-option
              v-for="(citem, cindex) in link_options"
              :key="cindex"
              :label="citem.label"
              :value="citem.value"
            ></el-option>
          </el-select>

          <lb-tool-tips
            >当轮播图跳转链接与视频链接同时存在时，默认点击轮播图第一张图片就播放视频，点击其他图片就跳转轮播图链接</lb-tool-tips
          >
        </el-form-item> -->
        <!-- <el-form-item prop="image_url">
          <el-input
            v-model="imgForm.image_url"
            :placeholder="jumpType[imgForm.jump_type]"
          ></el-input>

          <lb-tool-tips
            >小程序跳转链接填写格式如下:
            <div class="mt-md">
              1、跳转到小程序首页
              <div class="mt-md c-link">
                所需参数如下：
                <div class="mt-sm">[1] appId</div>
              </div>
              <div class="mt-md">例如：wx581f0c1732ae745f</div>
            </div>
            <div class="mt-lg">
              2、跳转到小程序指定页面
              <div class="mt-md c-link">
                所需参数如下：
                <div class="mt-sm">[1] appId</div>
                <div class="mt-sm">[2] ; (英文状态下的分号)</div>
                <div class="mt-sm">[3] 页面路径及参数</div>
              </div>
              <div class="mt-md">
                例如：wx581f0c1732ae745f;pages/user/home?key=1&staff_id=1
              </div>
            </div>
          </lb-tool-tips>
        </el-form-item> -->
        <el-form-item label="轮播图跳转链接" prop="image_url">
          <el-input
            type="text"
            v-model="imgForm.image_url"
            placeholder="请输入网页链接"
            show-word-limit
          >
          </el-input>
        </el-form-item>
        <el-form-item label="视频" prop="video_url">
          <div class="item-warp">
            <div class="upload-file-warp">
              <input
                type="text"
                class="choice-file-input"
                v-model="imgForm.video_url"
                placeholder="选择视频文件"
              />
              <lb-cover
                type="button"
                fileType="video"
                @selectedFiles="getVideo"
              ></lb-cover>
            </div>
          </div>
        </el-form-item>
        <el-form-item label="商品价格" prop="price">
          <el-input
            type="text"
            v-model="imgForm.price"
            placeholder="请输入金额"
            show-word-limit
          >
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="商品详情" prop="desc">
          <lb-ueditor v-model="imgForm.desc" :destroy="true"></lb-ueditor>
        </el-form-item>
        <el-form-item label="联系平台" prop="phone">
          <el-input
            type="text"
            v-model="imgForm.phone"
            placeholder="请输入电话号码"
            show-word-limit
          ></el-input>
        </el-form-item>
        <el-form-item label="排序值" prop="sort">
          <el-input
            type="text"
            v-model="imgForm.sort"
            placeholder=""
            show-word-limit
          ></el-input>
          <lb-tool-tips>值越大, 排序越靠前</lb-tool-tips>
        </el-form-item>
      </el-form>
    </div>
    <el-dialog
      :title="dialogTitle"
      :visible.sync="dialogSwitch"
      width="400px"
      @keyup.enter.native="addSpecsName(2)"
      center
    >
      <div class="dialog-inner">
        <el-input
          type="textarea"
          :rows="4"
          resize="none"
          maxlength="50"
          show-word-limit
          v-model="specsName"
          placeholder="请输入规格名"
        ></el-input>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addSpecsName(1)">{{
          $t('action.cancel')
        }}</el-button>
        <el-button type="primary" @click="addSpecsName(2)">{{
          $t('action.comfirm')
        }}</el-button>
      </span>
    </el-dialog>
    <el-dialog
      :title="`批量设置${dialogPriceTitle}`"
      :visible.sync="dialogPriceVisible"
      width="300px"
      @keyup.enter.native="setBatchPrice(2)"
      center
    >
      <div class="dialog-inner">
        <el-input-number
          v-if="dialogPriceTitle === '库存'"
          v-model="batchValue"
          :controls="false"
          :precision="0"
          :min="0"
        ></el-input-number>
        <el-input-number
          v-else
          v-model="batchValue"
          :controls="false"
          :precision="2"
          :min="0.0"
          :max="999999999.99"
        ></el-input-number>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="setBatchPrice(1)">{{
          $t('action.cancel')
        }}</el-button>
        <el-button type="primary" @click="setBatchPrice(2)">{{
          $t('action.comfirm')
        }}</el-button>
      </span>
    </el-dialog>

    <div
      class="fiexd"
      style="bottom: 0px"
      :style="{ right: adSwitch ? '242px' : '22px' }"
    >
      <el-button type="primary" @click="submitGoodsInfo">{{
        $t('action.submit')
      }}</el-button>
      <el-button type="default" style="margin-left: 10px" @click="goBack">{{
        $t('action.back')
      }}</el-button>
    </div>
  </div>
</template>

<script>
// import {reg} from '@/utils/reg'
import Uuid from 'uuid'
import { mapGetters } from 'vuex'
export default {
  data () {
    return {
      parimarForm: [], // 参数表单
      parimarOptions: [], // 下拉参数列表
      distribution: {},
      tips: {
        cash_1: '，请谨慎选择',
        cash_2: '，请谨慎输入此金额',
        user: {
          1: '用户消费, 其上线所得提成，无上线时无提成。提成为百分比，取值1%到100%',
          2: '用户消费, 其上线所得提成，无上线时无提成。提成为固定金额，最多保留两位小数，若输入的金额大于所购买规格的商品，也是按照输入的金额来进行提成'
        },
        staff: {
          1: `用户消费, 其对应员工所得提成。当消费的用户的上线与消费所属${this.$t(
            'menu.BusinessCard'
          )}为同一个人时, 只计算员工提成, 如果没有设置员工提成则计算分销提成, 都没有设置则不计算。提成为百分比，取值1%到100%`,
          2: `用户消费, 其对应员工所得提成。当消费的用户的上线与消费所属${this.$t(
            'menu.BusinessCard'
          )}为同一个人时, 只计算员工提成, 如果没有设置员工提成则计算分销提成, 都没有设置则不计算。提成为固定金额，最多保留两位小数，若输入的金额大于所购买规格的商品，也是按照输入的金额来进行提成`
        }
      },
      link_options: [{ label: '其他小程序', value: 2 }, { label: '跳转网页', value: 3 }],
      jumpType: {
        2: '请输入小程序appid', 3: '请输入网页链接'
      },
      testprice: 1,
      testContent: '测试',
      dialogSwitch: false,
      dialogTitle: '添加规格',
      dialogType: 'add',
      dialogIndex: '',
      specsName: '',
      batchValue: '0',
      dialogPriceTitle: '',
      dialogPriceVisible: false,
      id: '',
      navTitle: '',
      copySpecsItem: [], // 备份规格表格值
      // 树状单选下拉框配置
      defaultParams: {
        checkStrictly: true,
        multiple: true,
        label: 'name',
        value: 'id',
        isDisabled: true
      },
      parForm: {},
      specIsImg: false,
      // 图片表单
      imgForm: {
        name: '',
        carte: [],
        cover: [],
        images: [],
        image_url: '',
        jump_type: '',
        video_url: '',
        desc: '',
        price: '',
        phone: '',
        sort: 0
      },
      imgFormRules: {
        phone: {
          required: true,
          type: 'string',
          message: '请输入电话号码',
          trigger: 'blur'
        },
        price: {
          required: true,
          type: 'string',
          message: '请输入商品价格',
          trigger: 'blur'
        },
        name: {
          required: true,
          type: 'string',
          message: '请输入商品名称',
          trigger: 'blur'
        },
        carte: {
          required: true,
          type: 'array',
          message: '请选择商品分类',
          trigger: 'change'
        },
        cover: {
          required: true,
          type: 'array',
          message: '请上传图片',
          trigger: 'change'
        },
        images: {
          required: true,
          type: 'array',
          message: '请上传图片',
          trigger: 'change'
        },
        jump_type: {
          required: false,
          type: 'number',
          message: '请选择轮播图跳转方式',
          trigger: 'blur'
        },
        image_url: {
          required: false,
          type: 'string',
          message: '请输入链接地址',
          trigger: 'blur'
        },
        desc: {
          required: true,
          type: 'string',
          message: '请输入商品详情',
          trigger: 'blur'
        }
      },
      sendForm: {
        checkList: []
      },
      options: {},
      companyResult: [],
      isMemberAuth: false,
      levelList: [],
      memberForm: {
        is_member: 0,
        is_discount: 0,
        discount_data: []
      }
    }
  },
  async created () {
    let { id } = this.$route.query
    this.id = id
    this.navTitle = this.$t(id ? 'menu.MallGoodEdit' : 'menu.MallGoodAdd')
    await this.getSelectOptions()
    if (id) {
      await this.getGoodsInfo()
    }
  },
  methods: {
    changeParmar (id) {
      this.$api.getTemplateInfo({ template_id: id }).then(res => {
        this.parimarForm = res.data.info
      })
    },

    /**
     * @method 获取商品详情
     */
    async getGoodsInfo () {
      this.$api
        .mall.editGoods({
          id: this.id
        })
        .then(res => {
          if (res.code === 200) {
            let { data } = res
            for (let key in this.imgForm) {
              if (key === 'cover') {
                this.imgForm[key] = [
                  {
                    url: data.cover
                  }
                ]
              } else if (key === 'images') {
                this.imgForm[key] = data.images.map(item => {
                  return {
                    url: item
                  }
                })
              } else if (key === 'carte') {
                let arr = []
                data[key].forEach(item => {
                  arr.push([item])
                })
                this.imgForm[key] = arr
              } else {
                this.imgForm[key] = data[key]
              }
            }

            // 根据模板获取数据
            // if (!data.goods_info.template_id) return
            // this.changeParmar(data.goods_info.template_id)
          }
        })
    },
    /**
     * @method 生成表格
     */
    createTableData () {
      let { specsItem } = this.specsForm
      if (specsItem.length === 0) {
        this.specsForm.specsTable = []
      } else {
        let catArr = specsItem.map(item => {
          return item.cate
        })
        let testTableData = this.handleSpecsData(catArr)
        this.copySpecsItem.forEach(item => {
          testTableData.forEach(ele => {
            // eslint-disable-next-line eqeqeq
            if (item.id == ele.id) {
              ele.price = item.price
              ele.original_price = item.original_price
              ele.cost_price = item.cost_price
              ele.stock = item.stock
            }
          })
        })
        this.specsForm.specsTable = testTableData
      }
    },
    /**
     * @method 生成表格数据
     */
    handleSpecsData (arr) {
      if (arr.length > 1) {
        let len1 = arr[0].length
        let len2 = arr[1].length
        let newArr = arr.slice(0)
        let temp = []
        for (var i = 0; i < len1; i++) {
          for (var j = 0; j < len2; j++) {
            let id = arr[0][i].id + ',' + arr[1][j].id
            temp.push({
              id: id,
              title: arr[0][i].title + '-' + arr[1][j].title,
              price: '0.00',
              original_price: '0.00',
              cost_price: '0.00',
              stock: '0'
            })
          }
        }
        newArr.splice(0, 2, temp)
        return this.handleSpecsData(newArr)
      }
      return arr[0]
    },
    /**
     * @method 多选表格
     */
    handleSelectionChange (val) {
      this.specsForm.multipleSelection = val
    },
    getSelectOptions () {
      this.$api.mall.goodsCarteList().then(res => {
        if (res.code === 200) {
          let { data } = res
          // this.handleCompanyOptions(data)
          this.options = data
        }
      })
    },
    handleCompanyOptions (data, arr = []) {
      for (let i = 0, len = data.length; i < len; i++) {
        let newArr = JSON.parse(JSON.stringify(arr))
        newArr.push(data[i].id)
        this.companyResult.push(JSON.parse(JSON.stringify(newArr)))
        if (data[i].children && data[i].children.length) {
          this.handleCompanyOptions(data[i].children, newArr)
        }
      }
    },
    handleCloseTag (index, i) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.specsForm.specsItem[index].cate.splice(i, 1)
        this.$message.success(this.$t('tips.successDel'))
        this.createTableData()
      })
    },
    /**
     * @method 批量设置价格或者库存
     */
    setBatchPrice (type) {
      if (type === 2) {
        let { batchValue, dialogPriceTitle } = this
        let { multipleSelection } = this.specsForm
        let result = multipleSelection.map(item => {
          if (dialogPriceTitle === '原价') {
            item.original_price = batchValue
          } else if (dialogPriceTitle === '现价') {
            item.price = batchValue
          } else if (dialogPriceTitle === '成本') {
            item.cost_price = batchValue
          } else if (dialogPriceTitle === '库存') {
            item.stock = batchValue
          }
          return item
        })
        this.multipleSelection = result
      }
      this.batchValue = ''
      this.dialogPriceVisible = false
    },
    batchSetting (type) {
      this.dialogPriceTitle = type
      this.batchValue = 0
      this.dialogPriceVisible = true
    },
    /**
     * @method 删除规格名
     */
    delSpecsItem (index) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.specIsImg = this.specsForm.specsItem[index].is_img === 1 ? false : this.specIsImg
        this.specsForm.specsItem.splice(index, 1)
        this.$message.success(this.$t('tips.successDel'))
        this.createTableData()
      })
    },
    /**
     * @method 添加规格值
     */
    handleInputConfirm (index) {
      let { specsItem } = this.specsForm
      let { inputValue } = specsItem[index]
      inputValue = inputValue.replace(/^\s+|\s+$/g, '')
      if (!inputValue) {
        this.$message.error('请输入规格值')
        return
      }
      let hasSpecsValue = specsItem[index].cate.some(item => {
        return item.title === inputValue
      })
      if (hasSpecsValue) {
        this.$message.error('请勿添加相同的规格值！')
      } else {
        if (inputValue) {
          if (inputValue.length > 50) {
            this.$message.error('规格值最多只能输入50个字！')
          } else {
            this.specsForm.specsItem[index].cate.push({
              title: inputValue,
              id: Uuid(),
              price: '0.00',
              original_price: '0.00',
              cost_price: '0.00',
              stock: 0,
              image: ''
            })
            this.createTableData()
          }
        }
      }
      this.specsForm.specsItem[index].inputVisible = false
      this.specsForm.specsItem[index].inputValue = ''
    },
    showInput (index) {
      this.specsForm.specsItem[index].inputVisible = true
      this.$nextTick(_ => {
        this.$refs['saveTagInput' + index][0].$refs.input.focus()
      })
    },
    changeCheckBox (e) {
      this.authList.map(item => {
        this.setForm[item.key] = e.includes(item.title) ? 1 : 0
      })
    },
    /**
     * @method 返回上一页
     */
    goBack () {
      this.$route.meta.refresh = true
      this.$router.back(-1)
    },


    /**
     * @method 添加规格
     */
    addSpecsName (flag) {
      let specsName = JSON.parse(JSON.stringify(this.specsName)).replace(/^\s+|\s+$/g, '')
      if (flag === 2 && !specsName) {
        this.$message.error('请添加规格名！')
        return
      }
      if (flag === 2 && specsName) {
        let { specsItem } = this.specsForm
        let arr = specsItem.map(item => {
          return item.title
        })
        if (arr.indexOf(specsName) === -1) {
          if (this.dialogType === 'add') {
            this.specsForm.specsItem.push({
              inputVisible: false,
              inputValue: '',
              title: specsName,
              pid: Uuid(),
              cate: [],
              is_img: 0
            })
          } else {
            this.specsForm.specsItem[this.dialogIndex].title = specsName
          }
        } else {
          this.$message.error('请勿添加相同的规格名！')
        }
      }
      this.specsName = ''
      this.dialogIndex = ''
      this.dialogSwitch = false
    },

    /**
     * @method 添加或者修改规格名
     */
    addOrEditSpecsTitle (type, index) {
      if (type === 'add') {
        this.dialogTitle = '添加规格'
      } else if (type === 'edit') {
        this.dialogTitle = '编辑规格'
      }
      this.dialogSwitch = true
      this.dialogType = type
      this.specsName = this.specsForm.specsItem[index].title || ''
      if (index >= 0) this.dialogIndex = index
    },

    /**
     * @method 获取商品封面图
     */
    getGoodsCover (img) {
      this.imgForm.cover = img
    },

    /**
     * @method 设置规格图
     */
    getSpecImg (e, index, i) {
      this.specsForm.specsItem[index].cate[i].image = e.length > 0 ? e : ''
    },
    setSpecImg (index) {
      if (!this.specIsImg) {
        this.specIsImg = true
        this.specsForm.specsItem[index].is_img = 1
        if (this.specsForm.specsItem[index].cate.length < 1) {
          this.$confirm(`请先添加规格值`,
            this.$t('tips.reminder'),
            {
              confirmButtonText: this.$t('action.comfirm'),
              cancelButtonText: this.$t('action.cancel'),
              type: 'warning'
            }
          ).then(() => {
            this.showInput(index)
          })
        }
      } else {
        if (this.specsForm.specsItem[index].is_img === 1) {
          this.specsForm.specsItem[index].is_img = 0
          this.specIsImg = false
        } else {
          this.$confirm(
            this.$t('tips.confirmDeleteSpecImage'),
            this.$t('tips.reminder'),
            {
              confirmButtonText: this.$t('action.comfirm'),
              cancelButtonText: this.$t('action.cancel'),
              type: 'warning'
            }
          )
            .then(() => {
              if (this.specsForm.specsItem[index].cate.length < 1) {
                this.$confirm(`请先添加规格值`,
                  this.$t('tips.reminder'),
                  {
                    confirmButtonText: this.$t('action.comfirm'),
                    cancelButtonText: this.$t('action.cancel'),
                    type: 'warning'
                  }
                ).then(() => {
                  this.showInput(index)
                })
              }

              let { specsItem } = this.specsForm
              specsItem.map((item, i) => {
                item.is_img = index === i ? 1 : 0
                if (index === i) {
                  item.cate.map(aitem => {
                    if (!aitem.image) return
                    aitem.image =
                      typeof aitem.image === 'string'
                        ? [{ url: aitem.image }]
                        : aitem.image
                    // aitem.image =
                    //   typeof aitem.image === 'string'
                    //     ? [{ url: aitem.image }]
                    //     : aitem.image
                  })
                }
              })
              this.specsForm.specsItem = specsItem
            })
            .catch(() => { })
        }
      }
    },
    /**
     * @method 获取商品轮播图
     */
    selectedFiles (imgs, key) {
      this.imgForm[key].push(...imgs)
    },
    moveFiles (imgs, key) {
      this.imgForm[key] = imgs
    },
    getVideo (video) {
      this.imgForm.video_url = video[video.length - 1].url
    },
    /**
     * @method 提交商品信息
     */
    submitGoodsInfo () {
      let { imgForm } = this
      let flag = false
      const regMobile = /^(((\d{3,4}-)?[0-9]{7,8})|(13[0-9]|14[01456879]|15[0-35-9]|16[2567]|17[0-8]|18[0-9]|19[0-35-9])\d{8})$/
      this.$refs['imgForm'].validate(valid => {
        if (valid) {
          flag = true
        }
      })
      if (!flag) return
      if (!regMobile.test(imgForm.phone)) {
        return this.$message.error('请输入正确的手机号码')
      }
      let queryObj = JSON.parse(
        JSON.stringify({
          imgForm
        })
      )
      queryObj.imgForm.cover = queryObj.imgForm.cover.map(item => {
        return item.url
      })[0]
      queryObj.imgForm.images = queryObj.imgForm.images.map(item => {
        return item.url
      })
      // queryObj.imgForm.video_url = queryObj.imgForm.video_url.map(
      //   item => {
      //     return item.url
      //   }
      // )[0]
      let carte = []
      queryObj.imgForm.carte.forEach(item => {
        carte.push(item[0])
      })
      queryObj.imgForm.carte = carte
      if (this.id) {
        queryObj.imgForm.id = this.id
        this.$api.mall.editGoodsPost(queryObj.imgForm).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successSub'))
            this.goBack()
          }
        })
      } else {
        this.$api.mall.addGoods(queryObj.imgForm).then(res => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successSub'))
            this.goBack()
          }
        })
      }
    },
    checkSpecsItems (data) {
      let result = data.some(item => {
        return (
          item.price === undefined ||
          item.price === '' ||
          item.stock === undefined ||
          item.stock === ''
        )
      })
      return result
    }
  },
  computed: {
    ...mapGetters(['adSwitch'])
  },
  watch: {
    'imgForm.desc' (newValue, oldValue) {
      if (newValue && !oldValue) {
        this.$refs['imgForm'].clearValidate('desc')
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.parmai_view {
  display: flex;
  flex-wrap: wrap;
}
.lb-goods-edit {
  width: 100%;
  padding-bottom: 0px;
  position: relative;
  .el-form {
    margin: 20px 0;
  }

  .basic-form {
    .el-input {
      width: 300px;
    }

    .el-cascader {
      width: 300px;
    }

    .el-select {
      width: 300px;
    }
  }

  .relevant-form {
    padding-left: 80px;
    width: 1000px;
    .el-input {
      width: 160px;
    }
  }

  .img-form {
    .el-input {
      width: 300px;
    }
  }

  .set-form {
    .el-input {
      width: 300px;
      padding-right: -50px;

      i {
        font-style: normal;
        margin-right: 10px;
      }
    }
  }

  .specs-info {
    width: 925px;
    margin: 20px 0;

    .specs-item {
      width: 100%;
      display: flex;
      margin: 24px 0;

      .item-label {
        color: #606266;
        width: 180px;
        min-width: 180px;
        line-height: 35px;
        padding-right: 12px;
        text-align: right;

        .require {
          color: #f56c6c;
        }
      }

      .item-label-table {
        line-height: 50px;
      }

      .item-value {
        width: 100%;

        .table-bot {
          width: 100%;
          display: flex;
          align-items: center;

          span {
            margin-right: 20px;
          }

          .el-link {
            margin: 0 20px 0 0;
          }
        }
      }

      .item-value-specs-name {
        margin-bottom: 16px;

        .el-button {
          margin-left: 20px;
        }
      }

      .el-tag {
        margin: 5px 10px 5px 0px;
      }

      .input-new-tag {
        width: 90px;
        margin: 5px 10px 5px 0px;
        height: 28px;
        vertical-align: bottom;

        .el-input__inner {
          height: 28px !important;
        }
      }
    }
  }
}

.lb-input-number {
  width: 160px;
}

.dialog-inner {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;

  .el-input {
    width: 200px;
  }
}

.fiexd {
  height: 80px;
  position: fixed;
  background-color: rgb(255, 255, 255);
  box-shadow: rgb(240, 240, 240) 1px -2px 8px;
  bottom: 0px;
  left: 300px;
  z-index: 5;
  display: flex;
  padding-left: 100px;
  align-items: center;
}
.flex-warp {
  display: flex;
  flex-wrap: wrap;
}
.specsItem-tag-img {
  // display: inline-block;
  margin: 10px 10px 0 0;
}
.el-input,
.el-input-number,
.el-cascader,
.el-select {
  width: 300px;
}
</style>
