<template>
  <div>
    <div v-for="(item, index) in o.attr" :key="index + 1">
      <!-- 可拖拽组件 -->
      <div v-if="item.isDraggable || item.type === 'Add'">
        <diy-dragg-setting
          :o="o"
          :ckey="ckey"
          :componentsList="componentsList"
          :item="item"
          :index="index"
        ></diy-dragg-setting>
      </div>
      <!-- 不可拖拽组件 -->
      <div v-else>
        <div class="form-item">
          <div class="form-label">{{ item.title }}</div>
          <div class="flex-1">
            <!-- 颜色选择器 -->
            <el-color-picker
              v-model="o.data[item.name]"
              v-if="item.type === 'ColorPicker'"
            ></el-color-picker>

            <!-- 图片上传:UploadImage单张；UploadImages多张； -->
            <div v-if="item.type === 'UploadImage'">
              <lb-cover
                fileType="image"
                :fileList="o.data[item.name]"
                @selectedFiles="onDataChange(item.type, item.name, $event)"
              ></lb-cover>
            </div>

            <!-- 开关 -->
            <div v-if="item.type === 'Switch'">
              <el-switch v-model="o.data[item.name]"></el-switch>
            </div>

            <!-- 标签 -->
            <div v-if="item.type === 'Tag'">
              <el-tag
                v-for="(citem, cindex) in item.data"
                :key="cindex"
                closable
                :type="citem.type"
                >{{ citem.name }}</el-tag
              >
            </div>

            <!-- 文本 -->
            <div v-if="item.type === 'Input'">
              <div v-if="item.itemtype === 'number'">
                <el-input
                  type="number"
                  size="small"
                  v-model="o.data[item.name]"
                  :placeholder="
                    item.placeholder ? item.placeholder : `请输入${item.title}`
                  "
                  :min="0"
                  :max="item.max"
                ></el-input>
              </div>
              <div v-else>
                <el-input
                  type="text"
                  size="small"
                  v-model="o.data[item.name]"
                  :placeholder="
                    item.placeholder ? item.placeholder : `请输入${item.title}`
                  "
                  :maxlength="item.maxLength || ''"
                  show-word-limit
                ></el-input>
              </div>
            </div>

            <!-- 计数器 -->
            <div v-if="item.type === 'InputNumber'">
              <el-input-number
                size="small"
                v-model="o.data[item.name].number"
                @change="handleChange"
                :min="o.data[item.name].min"
                :max="o.data[item.name].max"
                :label="o.data[item.name].label"
              ></el-input-number>
            </div>

            <!-- 单选按钮 -->
            <div v-if="item.type === 'Radio'">
              <el-radio
                v-model="o.data[item.name]"
                :label="citem.label"
                v-for="(citem, cindex) in item.data"
                :key="cindex"
                >{{ citem.title }}</el-radio
              >
              <div class="mt-md" v-if="item.name === 'posterType'">
                <el-button
                  @click="showDialog = true"
                  plain
                  size="small"
                  type="primary"
                  >查看示例</el-button
                >
              </div>
            </div>

            <!-- 复选按钮 -->
            <div v-if="item.type === 'Checkbox'">
              <el-checkbox
                v-model="o.data[item.name]"
                v-for="(citem, cindex) in item.data"
                :key="cindex"
                >{{ citem.title }}</el-checkbox
              >
            </div>

            <!-- 下拉选择框 -->
            <div v-if="item.type === 'Select'">
              <el-select
                v-model="o.data[item.name]"
                placeholder="请选择"
                @change="
                  selectHandleChange(
                    $event,
                    item.name,
                    item.data,
                    item.changeTitle
                  )
                "
              >
                <el-option
                  v-for="citem in item.data"
                  :key="citem.value"
                  :label="citem.label"
                  :value="citem.value"
                ></el-option>
              </el-select>
            </div>

            <!-- 级联选择器 -->
            <div v-if="item.type === 'Cascader'">
              <el-cascader
                size="small"
                v-model="o.data[item.name]"
                :options="item.data"
                @change="cascaderHandleChange($event, item.name, item.data)"
              ></el-cascader>
            </div>

            <!-- 选择模板 -->
            <div v-if="item.type === 'ChooseModule'">
              <div class="module-list flex-warp">
                <div
                  @click="onDataChange(o.type, item.name, citem.name)"
                  class="module-item"
                  :class="[
                    { 'module-item--select': o.data[item.name] == citem.name }
                  ]"
                  v-for="(citem, cindex) in item.data"
                  :key="cindex"
                >
                  <img class="module-item_icon" :src="citem.img" />
                  <div class="module-item_text">{{ citem.title }}</div>
                </div>
              </div>
            </div>

            <!-- 描述文字 -->
            <div
              v-if="item.desc"
              class="f-icontext"
              style="color: red"
              :style="{ marginTop: item.type === 'Cascader' ? '5px' : '0' }"
            >
              {{ item.desc }}
            </div>

            <!-- END -->
          </div>
        </div>
      </div>
    </div>

    <el-dialog
      title="查看示例"
      :visible.sync="showDialog"
      :append-to-body="true"
      width="700px"
      center
      top="5vh"
    >
      <div class="flex-between">
        <div v-for="(item, index) in showDialogList" :key="index">
          <div class="flex-center pd-lg f-paragraph c-title">
            {{ item.title }}
          </div>
          <img class="look-image" :src="item.img" />
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog = false">知道了</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import diyDraggSetting from './diyDraggSetting.vue'
export default {
  components: {
    draggable,
    diyDraggSetting
  },
  data () {
    return {
      hoverIndex: '',
      showDialog: false,
      showDialogList: [{ title: '样式一', img: 'https://lbqny.migugu.com/admin/diy/share-poster1.jpeg' }, { title: '样式二', img: 'https://lbqny.migugu.com/admin/diy/share-poster2.jpeg' }],
      defaultImg: [{ url: 'https://lbqny.migugu.com/admin/diy/default.png' }],
      posterImg: [{ url: 'https://lbqny.migugu.com/admin/diy/invite-poster.jpg' }]
    }
  },
  name: 'diy-setting',
  props: {
    o: Object,
    activeId: Number,
    activeIndex: Number,
    ckey: Number,
    allTabBar: Object,
    page: Object,
    componentsList: Array
  },
  methods: {
    // 当数据发生改变时，执行保存操作
    onDataChange (type, key, value) {
      // alert(`${type},${key},${value}`)
      value = type === 'UploadImage' && value.length < 1 ? key === 'posterImg' ? this.posterImg : this.defaultImg : value
      let list = JSON.parse(JSON.stringify(this.o.data[key]))
      if (value.includes('imagewindow-col-')) {
        let number = value.split('imagewindow-col-')[1] * 1
        let ind = this.o.attr.findIndex(item => {
          return item.type === 'Add'
        })
        let desc = this.o.attr.findIndex(item => {
          return item.type === 'ImageLink'
        })
        // 单个广告图 最多能加10个
        this.o.attr[ind].addNumber = number === 1 ? 10 : number
        let addModule = this.o.attr[ind].data[0]
        this.o.attr[desc].data[0].desc = number === 3 ? '333*152' : '348*n'
        if (number === 2) {
          if (this.o.data[this.o.data.addMouduleName].length > 2) {
            this.o.data[this.o.data.addMouduleName].splice(2, 1)
          }
          if (this.o.data[this.o.data.addMouduleName].length !== 2) {
            this.o.data[this.o.data.addMouduleName].push(addModule)
          }
        }
        if (number === 3 && this.o.data[this.o.data.addMouduleName].length !== 3) {
          let arr = 3 - this.o.data[this.o.data.addMouduleName].length
          for (let i = 0; i < arr; i++) {
            this.o.data[this.o.data.addMouduleName].push(addModule)
          }
        }
      }
      if (type === 'UploadImages') {
        list.push(value)
      } else {
        list = value
      }
      this.o.data[key] = list
    },
    selectHandleChange (val, key, data, changeTitle) {
      if (!changeTitle) return
      data.map(item => {
        if (item.value === val) {
          this.o.data.title = item.label
        }
      })
    },
    cascaderHandleChange (val, key, data) {
      let type = `${key}_type`
      let title = `${key}_title`
      let resTitle = ''
      data.map(item => {
        if (item.value === val[0]) {
          item.children.map(aitem => {
            if (aitem.value === val[1]) {
              resTitle = aitem.label
            }
          })
        }
      })
      this.o.data[type] = val[0]
      this.o.data[key] = val[1]
      this.o.data[title] = resTitle
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.el-input,
.el-select,
.el-cascader {
  width: 208px;
}
.look-image {
  width: 297px;
  border-radius: 10px;
  border: 1px solid #eee;
}
</style>
