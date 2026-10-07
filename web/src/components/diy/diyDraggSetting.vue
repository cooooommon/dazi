<template>
  <div>
    <!-- item====> {{ item }} {{ item.name }} -->
    <!-- 添加模板 -->
    <div v-if="item.type === 'Add'">
      <div
        class="add-btn"
        @click="addModule"
        v-if="o.data[o.data[item.name]].length < item.addNumber"
      >
        <div class="add-btn_title">
          <i class="iconfont iconjia1"></i>
          <span>{{ item.title }}</span>
        </div>
        <div class="add-btn_caption">
          最多添加{{ item.addNumber }}项,还可添加{{
            item.addNumber - o.data[o.data[item.name]].length
          }}项
        </div>
      </div>
      <div class="h-64" v-else></div>
    </div>
    <!-- 可拖拽组件 -->
    <vuedraggable
      class="drag-box"
      v-model="o.data[item.name]"
      ref="drag"
      v-else
    >
      <transition-group>
        <div
          class="drag-item data-item"
          :class="{ 'drag-item--select': index == activeIndex }"
          @mouseover="hover(aindex)"
          v-for="(aitem, aindex) in o.data[item.name]"
          :key="aindex + 1"
        >
          <!-- 图片/链接/链接地址(item.type === 'ImageLink') -->
          <div
            class="form-item"
            v-for="(bitem, bindex) in item.data"
            :key="bindex"
            v-show="
              (bitem.title === '链接地址' &&
                o.data[item.name][aindex].linkType !== 5) ||
              bitem.title !== '链接地址'
            "
          >
            <div class="form-label">{{ bitem.title }}</div>
            <div class="flex-1">
              <!-- 文本 -->
              <div v-if="bitem.type === 'Input'">
                <el-input
                  size="small"
                  v-model="o.data[item.name][aindex][bitem.name]"
                  :placeholder="`请输入` + bitem.title"
                  :maxlength="5"
                  show-word-limit
                ></el-input>
              </div>

              <!-- 单张图片上传 -->
              <div v-if="bitem.type === 'UploadImage'">
                <lb-cover
                  fileType="image"
                  :fileList="aitem[bitem.name]"
                  @selectedFiles="
                    onDraggDataChange(
                      $event,
                      bitem.type,
                      item.name,
                      aindex,
                      bitem.name
                    )
                  "
                ></lb-cover>
              </div>

              <!-- 下拉选择框 -->
              <div v-if="bitem.type === 'Select'">
                <el-select
                  @change="
                    changeSelect(
                      item.type,
                      bitem.type,
                      item.name,
                      aindex,
                      'link'
                    )
                  "
                  v-model="o.data[item.name][aindex][bitem.name]"
                  size="small"
                  placeholder="请选择"
                >
                  <el-option
                    v-for="(citem, cindex) in bitem.data"
                    :key="cindex"
                    :label="citem.label"
                    :value="citem.value"
                  ></el-option>
                </el-select>
                <div
                  class="f-icontext c-caption mt-md"
                  v-if="o.data[item.name][aindex][bitem.name] === 2"
                >
                  小程序跳转链接填写格式
                  <lb-tool-tips :padding="0"
                    >仅小程序端展示，跳转链接填写格式如下:
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
                </div>
              </div>

              <!-- 标签 -->
              <div v-if="item.type === 'ImageLink' && bitem.type === 'Tag'">
                <div
                  v-for="(citem, cindex) in o.data[item.name][aindex][
                    bitem.name
                  ]"
                  :key="cindex"
                >
                  <div v-if="o.data[item.name][aindex].linkType === 4">
                    <el-tag
                      :type="citem.title ? '' : 'info'"
                      :closable="citem.title ? true : false"
                      @close="toDelItem(bitem.type, item.name, aindex, 'link')"
                      @click="open(bitem.type, item.name, aindex, 'link')"
                    >
                      <div v-if="citem.title && citem.title.length > 14">{{
                        citem.title.substring(0, 14)
                      }}</div>
                      <div v-else>{{ citem.title || '点击选择' }}</div>
                    </el-tag>
                  </div>
                  <!--跳转视频-->
                  <div v-else-if="o.data[item.name][aindex].linkType === 6">
                    <lb-cover
                      type="button"
                      fileType="video"
                      :fileSize="1"
                      ref="videoVoice"
                      buttonType="tag"
                      :value="citem.url"
                      @selectedFiles="
                        getVoice($event, '', aindex, cindex, bitem.name)
                      "
                      @delVideoUrl="
                        getVoice($event, 'del', aindex, cindex, bitem.name)
                      "
                    ></lb-cover>
                  </div>
                  <div v-else-if="o.data[item.name][aindex].linkType !== 5">
                    <el-input
                      size="small"
                      v-model="citem.url"
                      :placeholder="`请输入` + [bitem.title]"
                    ></el-input>
                  </div>
                </div>
              </div>
              <!-- 描述文字 -->
              <div v-if="bitem.desc" class="f-icontext" style="color: red">
                <div v-if="item.type === 'ImageLink'">
                  <div v-if="o.data.style && o.data.style.number"
                    >{{ o.data.style.width || '750' }}*{{ o.data.style.number }}
                  </div>
                  <div v-else-if="o.data.imagewindowName"
                    >{{
                      o.data.imagewindowName === 'imagewindow-col-1'
                        ? '750*n'
                        : o.data.imagewindowName === 'imagewindow-col-3' &&
                          !aindex
                        ? '333*334'
                        : bitem.desc
                    }}
                  </div>
                  <div v-else>{{ bitem.desc }}</div></div
                ><div v-else>{{ bitem.desc }}</div>
              </div>

              <!-- END -->
            </div>
          </div>
          <!-- 操作栏 -->
          <div class="op-area">
            <!-- 删除 -->
            <div
              class="op-area-item"
              @click="opdel(item.name, hoverIndex)"
              v-if="item.isDelete && o.data[item.name].length > 1"
            >
              <i
                class="iconfont icon-add"
                style="
                  font-size: 15px;
                  transform: rotate(45deg);
                  margin-top: -1px;
                "
              ></i>
            </div>
            <!-- 上移 -->
            <div
              class="op-area-item"
              @click="opup(item.name, hoverIndex)"
              v-if="hoverIndex > 0"
            >
              <i
                class="iconfont icon-left"
                style="font-size: 15px; transform: rotate(90deg)"
              ></i>
            </div>
            <!-- 下移 -->
            <div
              class="op-area-item"
              @click="opdown(item.name, hoverIndex)"
              v-if="hoverIndex < o.data[item.name].length - 1"
            >
              <i
                class="iconfont icon-left"
                style="font-size: 15px; transform: rotate(270deg)"
              ></i>
            </div>
          </div>
          <!-- 操作栏 END-->
        </div>
      </transition-group>
    </vuedraggable>
    <!-- 弹窗 -->
    <el-dialog
      title="选择链接"
      :visible.sync="showDialog"
      width="1000px"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :append-to-body="true"
    >
      <div v-loading="loading">
        <div style="display: flex" v-if="moduleList.length > 0">
          <div class="dialog-left">
            <div class="dialog-left_title">选择链接类型</div>
            <div
              class="dialog-left_item"
              :class="{ 'dialog-left_item--select': aindex == activeIndex }"
              v-for="(aitem, aindex) in moduleList"
              :key="aindex"
              @click="moduleChildClick(aindex)"
            >
              {{ aitem.title }}
            </div>
          </div>
          <div style="flex: 1" v-if="moduleList[activeIndex].api_path">
            <el-row
              class="page-search-form"
              v-if="moduleList[activeIndex].page !== 'common_page'"
            >
              <el-form
                @submit.native.prevent
                :inline="true"
                :model="searchForm"
                ref="searchForm"
              >
                <el-form-item label="输入查询" prop="title">
                  <el-input
                    v-model="searchForm.title"
                    placeholder="请输入名称"
                  ></el-input>
                </el-form-item>
                <el-form-item>
                  <lb-button
                    size="medium"
                    type="primary"
                    icon="el-icon-search"
                    style="margin-right: 5px"
                    @click="getTableDataList(1)"
                    >{{ $t('action.search') }}</lb-button
                  >
                  <lb-button
                    size="medium"
                    icon="el-icon-refresh-left"
                    style="margin-right: 5px"
                    @click="resetForm('searchForm')"
                    >{{ $t('action.reset') }}</lb-button
                  >
                </el-form-item>
              </el-form>
            </el-row>

            <el-table
              ref="singleTable"
              :data="tableData"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              v-loading="tableLoading"
              highlight-current-row
              @current-change="handleCurrentChange"
              row-key="id"
              :default-expand-all="true"
              :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
            >
              <el-table-column
                prop="id"
                label="ID"
                width="150"
                :key="1"
                v-if="moduleList[activeIndex].page !== 'common_page'"
              ></el-table-column>
              <el-table-column
                prop="title"
                :key="2"
                label="名称"
              ></el-table-column>
              <el-table-column
                v-if="
                  moduleList[activeIndex].page ===
                  '/business/pages/package/detail'
                "
                prop="store_name"
                label="所属门店"
              ></el-table-column>
              <el-table-column
                prop="use_stock"
                label="剩余库存"
                v-if="
                  moduleList[activeIndex].page ===
                  '/business/pages/package/detail'
                "
              >
                <template slot-scope="scope">
                  <span>{{
                    scope.row.stock * 1 - scope.row.use_stock * 1
                  }}</span>
                </template></el-table-column
              >
            </el-table>
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer-diy">
        <div class="dialog-footer_op">
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="submit">确 定</el-button>
        </div>
        <el-pagination
          v-if="
            moduleList.length > 0 &&
            moduleList[activeIndex].api_path &&
            moduleList[activeIndex].page !== 'common_page'
          "
          @size-change="handleSizeChange"
          @current-change="handlePageCurrentChange"
          :total="total"
          :current-page="searchForm.page"
          :page-size="searchForm.limit"
          :page-sizes="[5, 10, 20]"
          layout="total, sizes, prev, pager, next, jumper"
        ></el-pagination>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { get } from '../../api/index'
import vuedraggable from 'vuedraggable'
export default {
  components: {
    vuedraggable
  },
  data () {
    return {
      defaultImg: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }],
      hoverIndex: '',
      tableData: [],
      currentRow: { id: 0, title: '' },
      openObj: {},
      showDialog: false,
      moduleList: [],
      activeIndex: 0,
      searchForm: {
        page: 1,
        limit: 10,
        title: '',
        status: 1
      },
      total: 0,
      loading: false,
      tableLoading: false
    }
  },
  name: 'diy-dragg-setting',
  props: {
    o: Object,
    ckey: Number,
    item: Object,
    index: Number,
    allTabBar: Object,
    page: Object,
    componentsList: Array
  },
  created () {
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    hover (index) {
      this.hoverIndex = index
    },
    // 交换组件
    swapItems (arr, index1, index2) {
      arr[index1] = arr.splice(index2, 1, arr[index1])[0]
      return arr
    },
    // 删除
    opdel (key, index) {
      let list = JSON.parse(JSON.stringify(this.o.data[key]))
      list.splice(index, 1)
      this.o.data[key] = list
    },
    // 上移
    opup (key, index) {
      this.o.data[key] = this.swapItems(this.o.data[key], index, index - 1)
    },
    // 下移
    opdown (key, index) {
      this.o.data[key] = this.swapItems(this.o.data[key], index, index + 1)
    },
    // 添加模板
    async addModule () {
      let o = JSON.parse(JSON.stringify(this.o))
      let { type, compontents, data } = o
      let key = data.addMouduleName
      let list = o.data[key]
      let componentsList = JSON.parse(JSON.stringify(this.componentsList))
      componentsList.map(bitem => {
        if (bitem.type === compontents) {
          bitem.data.map(citem => {
            if (citem.type === type) {
              list.push(citem.data[citem.data.addMouduleName][0])
              this.o.data[key] = list
            }
          })
        }
      })
    },
    // 当数据发生改变时，执行保存操作
    onDraggDataChange (value, type, key1, index, key2) {
      value = key2 === 'img' ? value.length < 1 ? this.defaultImg : [{ url: value[0].url }] : value
      let list = JSON.parse(JSON.stringify(this.o.data[key1][index][key2]))
      if (type === 'UploadImages') {
        list.push(value)
      } else {
        list = value
      }
      this.o.data[key1][index][key2] = list
      if (key2 === 'link' && list[0].title) {
        this.o.data[key1][index].title = list[0].title.substring(0, 5)
      }
    },
    changeSelect (atype, type, key1, index, key2) {
      if (atype === 'ImageLink' && type === 'Select') {
        this.toDelItem(type, key1, index, key2)
      }
    },
    toDelItem (type, key1, index, key2) {
      let obj = {
        title: '',
        url: ''
      }
      this.onDraggDataChange([obj], type, key1, index, key2)
    },
    handleCurrentChange (val) {
      this.currentRow = val
    },
    resetForm (form) {
      this.$refs[form].resetFields()
      this.getTableDataList(1)
    },
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handlePageCurrentChange(1)
    },
    handlePageCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    async openVideo () {
      this.$refs.videoVoice.showUpload = true
    },
    async open (type, key1, index, key2) {
      this.openObj = { type, key1, index, key2 }
      this.loading = true
      // 门店diy修改
      let moduleType = this.ckey === 8 ? 'getFunctionPageListForStore' : 'getFunctionPageList'
      let res = await this.$api.diy[moduleType]()
      this.loading = false
      this.showDialog = true
      if (res.code === 200) {
        this.moduleList = res.data
        this.searchForm.page = 1
        this.searchForm.title = ''
        this.currentRow = {}
        await this.getTableDataList()
        this.$refs.singleTable.setCurrentRow({})
      }
    },
    moduleChildClick (index) {
      this.activeIndex = index
      this.searchForm.page = 1
      this.searchForm.limit = 10
      this.searchForm.title = ''
      this.getTableDataList()
    },
    async getTableDataList (page) {
      if (page) {
        this.searchForm.page = page
        this.currentRow = {}
      }
      let item = this.moduleList[this.activeIndex]
      if (!item.api_path) {
        this.tableData = []
        this.total = 0
        return
      }
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      this.tableLoading = true
      let { code, data } = await get(item.api_path, searchForm)
      this.tableLoading = false
      if (code !== 200) return
      let isCommon = item.page === 'common_page'
      this.tableData = isCommon ? data : data.data
      this.total = isCommon ? data.length : data.total
    },
    getVoice (file, type = '', aindex, cindex, name) {
      if (type == 'del') {
        this.o.data[this.item.name][aindex][name][cindex].url = ''
        return
      }
      let len = file.length - 1
      this.o.data[this.item.name][aindex][name][cindex].url = file[len].url
      // this.subForm.video = file[len].url
    },
    submit () {
      let item = this.moduleList[this.activeIndex]
      if (!item) {
        this.$message.error('请选择')
        return
      }
      let { title = '', page: url = '' } = item
      let obj = {
        title, url
      }
      let isCommon = url === 'common_page'
      if (item.api_path) {
        let { id = 0, title = '', path = '' } = this.currentRow
        if (!id && !path) {
          this.$message.error('请点击列表选择一项')
          return
        } else {
          obj.title = title || '无标题'
          let curURL = isCommon ? path : `${url}?id=${id}&title=${title}`
          obj.url = curURL
        }
      }
      let { type, key1, index, key2 } = this.openObj
      this.onDraggDataChange([obj], type, key1, index, key2)
      this.showDialog = false
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.form-label {
  width: 90px;
  margin-left: 10px;
}
.el-input,
.el-select,
.el-cascader {
  width: 208px;
}
</style>
