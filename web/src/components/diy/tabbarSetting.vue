<template>
  <div>
    <el-dropdown @command="handleCommand">
      <el-button type="primary">
        <i class="el-icon-circle-plus"></i>
        添加导航
      </el-button>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item
          :disabled="isSelect(item.id)"
          v-for="(item, index) in allTabBar"
          :key="index"
          :command="index"
          >{{ item.name }}</el-dropdown-item
        >
      </el-dropdown-menu>
    </el-dropdown>
    <div style="margin-top: 8px">最多添加5个底部导航,可删除后重新添加</div>
    <div v-for="(element, index) in o.tabbar" :key="element.id">
      <div class="data-item" v-if="element.id === 1" style="margin-bottom: 0px">
        <div class="data-item_title" @click="onToggleChange(element.id)">
          <div class="flex-1">
            {{ element.name }}{{ element.id == active ? '(修改中)' : '' }}
          </div>
          <i
            class="el-icon-error"
            style="font-size: 18px; color: #bbbbbb"
            @click.stop="del(index, element.id)"
            v-if="![1, 2, 5].includes(element.id)"
          ></i>
        </div>
        <div v-if="element.id == active">
          <div class="data-item_line"></div>
          <div class="form-item">
            <div class="form-label">名称</div>
            <div style="flex: 1">
              <el-input
                @input="changeInput(element.name, element.id)"
                size="mini"
                v-model="element.name"
                class="flex-1"
                placeholder="请输入名称"
                type="text"
                :maxlength="5"
                show-word-limit
              ></el-input>
            </div>
          </div>
          <div
            @click="toChangeIcon(index, element.id)"
            class="form-item cursor-pointer"
          >
            <div class="form-label">图标</div>
            <div class="form-icon_box">
              <div class="form-icon_top">
                <i
                  class="iconfont"
                  :class="element.default_img"
                  :style="{
                    fontSize: 30 + 'px',
                    color:
                      element.default_img == element.selected_img
                        ? '#BCCAD9'
                        : '#666'
                  }"
                ></i>
              </div>
              <div class="form-icon_text">未选中</div>
            </div>
            <div class="form-icon_box">
              <div class="form-icon_top">
                <i
                  class="iconfont"
                  :class="element.selected_img"
                  :style="{ fontSize: 30 + 'px', color: o.selectedColor }"
                ></i>
              </div>
              <div class="form-icon_text">已选中</div>
            </div>
            <div class="form-change_text">修改</div>
          </div>
          <div class="form-item">
            <div class="form-label">链接</div>
            <el-tag>{{ diyTitleObj[element.id] }}</el-tag>
          </div>
        </div>
      </div>
    </div>
    <vuedraggable
      class="drag-box"
      v-model="o.tabbar"
      ref="drag"
      filter=".undraggable"
      :move="onMove"
      @update="datadragEnd"
      @start="datadragStart"
      @end="datadragEnding"
    >
      <transition-group>
        <div
          :class="[{ undraggable: element.id === 1 }]"
          v-for="(element, index) in o.tabbar"
          :key="element.id"
        >
          <!--:class="[{ undraggable: element.id === 1 }]"-->
          <div class="data-item" v-if="element.id !== 1">
            <div class="data-item_title" @click="onToggleChange(element.id)">
              <div class="flex-1">
                {{ element.name }}{{ element.id == active ? '(修改中)' : '' }}
              </div>
              <i
                class="el-icon-error"
                style="font-size: 18px; color: #bbbbbb"
                @click.stop="del(index, element.id)"
                v-if="![1, 2, 5].includes(element.id)"
              ></i>
            </div>
            <div v-if="element.id == active">
              <div class="data-item_line"></div>
              <div class="form-item">
                <div class="form-label">名称</div>
                <div style="flex: 1">
                  <el-input
                    @input="changeInput(element.name, element.id)"
                    size="mini"
                    v-model="element.name"
                    class="flex-1"
                    placeholder="请输入名称"
                    type="text"
                    :maxlength="5"
                    show-word-limit
                  ></el-input>
                </div>
              </div>
              <div
                @click="toChangeIcon(index, element.id)"
                class="form-item cursor-pointer"
              >
                <div class="form-label">图标</div>
                <div class="form-icon_box">
                  <div class="form-icon_top">
                    <i
                      class="iconfont"
                      :class="element.default_img"
                      :style="{
                        fontSize: 30 + 'px',
                        color:
                          element.default_img == element.selected_img
                            ? '#BCCAD9'
                            : '#666'
                      }"
                    ></i>
                  </div>
                  <div class="form-icon_text">未选中</div>
                </div>
                <div class="form-icon_box">
                  <div class="form-icon_top">
                    <i
                      class="iconfont"
                      :class="element.selected_img"
                      :style="{ fontSize: 30 + 'px', color: o.selectedColor }"
                    ></i>
                  </div>
                  <div class="form-icon_text">已选中</div>
                </div>
                <div class="form-change_text">修改</div>
              </div>
              <div class="form-item">
                <div class="form-label">链接</div>
                <el-tag>{{ diyTitleObj[element.id] }}</el-tag>
              </div>
            </div>
          </div>
        </div>
      </transition-group>
    </vuedraggable>

    <el-dialog
      title="图标选择"
      :visible.sync="dialogVisible"
      :append-to-body="true"
      width="950px"
    >
      <div class="flex-center c-warning">
        两种风格的展示效果不一致，请选择风格统一的图标
      </div>
      <div class="diy-icon-list">
        <div class="c-title text-bold pl-lg">风格一</div>
        <div class="flex-warp">
          <div
            @click="toCheckIcon(index)"
            class="diy-item-child"
            :class="[{ check: index == iconInd }]"
            v-for="(item, index) in iconList"
            :key="index"
            v-show="
              item.default_img !== item.selected_img && item.is_sign !== 1
            "
          >
            <div class="diy-item-icon">
              <div class="diy-item-icon-child">
                <i
                  class="iconfont"
                  style="font-size: 18px"
                  :class="item.default_img"
                  :style="{
                    color:
                      item.default_img == item.selected_img ? '#BCCAD9' : '#666'
                  }"
                ></i>
              </div>
              <div class="diy-item-icon-child ml">
                <i
                  class="iconfont"
                  style="font-size: 18px"
                  :class="item.selected_img"
                  :style="{ color: o.selectedColor }"
                ></i>
              </div>
            </div>
            <div class="diy-item-text">{{ item.name }}</div>
          </div>
        </div>
        <div class="c-title text-bold pl-lg">风格二</div>
        <div class="flex-warp">
          <div
            @click="toCheckIcon(index)"
            class="diy-item-child"
            :class="[{ check: index == iconInd }]"
            v-for="(item, index) in iconList"
            :key="index"
            v-show="item.default_img === item.selected_img"
          >
            <div class="diy-item-icon">
              <div class="diy-item-icon-child">
                <i
                  class="iconfont"
                  style="font-size: 18px"
                  :class="item.default_img"
                  :style="{
                    color:
                      item.default_img == item.selected_img ? '#BCCAD9' : '#666'
                  }"
                ></i>
              </div>
              <div class="diy-item-icon-child ml">
                <i
                  class="iconfont"
                  style="font-size: 18px"
                  :class="item.selected_img"
                  :style="{ color: o.selectedColor }"
                ></i>
              </div>
            </div>
            <div class="diy-item-text">{{ item.name }}</div>
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="toConfirmIcon">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import vuedraggable from 'vuedraggable'
export default {
  components: {
    vuedraggable
  },
  data () {
    return {
      active: '',
      diyTitleObj: {
        1: '首页',
        2: this.$t('action.attendantName'),
        3: '动态',
        5: '我的',
        7: '发现',
        8: '门店',
        9: '秒杀'
      },
      tabInd: -1,
      dialogVisible: false,
      iconInd: -1,
      iconList: [{
        name: '首页 - 1',
        default_img: 'iconshouye11',
        selected_img: 'iconshouye21'
      }, {
        name: '首页 - 2',
        default_img: 'iconshouye2',
        selected_img: 'iconshouye12'
      }, {
        name: '首页 - 3',
        default_img: 'icon-home',
        selected_img: 'icon-home-fill'
      }, {
        name: '首页 - 1',
        default_img: 'iconshouye21',
        selected_img: 'iconshouye21'
      }, {
        name: '首页 - 2',
        default_img: 'iconshouye12',
        selected_img: 'iconshouye12'
      }, {
        name: '首页 - 3',
        default_img: 'iconshouye31',
        selected_img: 'iconshouye31'
      }, {
        name: '首页 - 4',
        default_img: 'iconshouye1',
        selected_img: 'iconshouye1'
      }, {
        name: `${this.$t('action.attendantName')} - 1`,
        default_img: 'iconanmo1',
        selected_img: 'iconanmo2'
      }, {
        name: `${this.$t('action.attendantName')} - 2`,
        default_img: 'iconbangdingjishi1',
        selected_img: 'iconshenqingjishi1'
      }, {
        name: `${this.$t('action.attendantName')} - 1`,
        default_img: 'iconanmo2',
        selected_img: 'iconanmo2'
      }, {
        name: `${this.$t('action.attendantName')} - 2`,
        default_img: 'iconshenqingjishi1',
        selected_img: 'iconshenqingjishi1'
      }, {
        name: `${this.$t('action.attendantName')} - 3`,
        default_img: 'iconjishi1',
        selected_img: 'iconjishi1'
      }, {
        name: `${this.$t('action.attendantName')} - 4`,
        default_img: 'iconjishi11',
        selected_img: 'iconjishi11'
        // }, {
        //   name: '动态 - 1',
        //   default_img: 'icondongtai',
        //   selected_img: 'icondongtai-fill'
        // }, {
        //   name: '动态 - 2',
        //   default_img: 'icon-dongtai1',
        //   selected_img: 'icon-dongtai2'
        // }, {
        //   name: '动态 - 3',
        //   default_img: 'iconfaxian-2',
        //   selected_img: 'iconfaxian-1'
        // }, {
        //   name: '动态 - 1',
        //   default_img: 'icondongtai-fill',
        //   selected_img: 'icondongtai-fill'
        // }, {
        //   name: '动态 - 2',
        //   default_img: 'icon-dongtai2',
        //   selected_img: 'icon-dongtai2'
        // }, {
        //   name: '动态 - 3',
        //   default_img: 'iconfaxian-1',
        //   selected_img: 'iconfaxian-1'
      }, {
        name: '门店 - 1',
        default_img: 'iconmendian11',
        selected_img: 'iconmendian1'
      }, {
        name: '门店 - 1',
        default_img: 'iconmendian1',
        selected_img: 'iconmendian1'
      }, {
        name: '门店 - 2',
        default_img: 'iconwoshiqudao1',
        selected_img: 'iconwoshiqudao1'
        // }, {
        //   name: '地图 - 1',
        //   default_img: 'icondituzhaoren2',
        //   selected_img: 'icondituzhaoren1'
        // }, {
        //   name: '地图 - 2',
        //   default_img: 'iconbiaoqianye-dituzhaoren2',
        //   selected_img: 'iconbiaoqianye-dituzhaoren1'
        // }, {
        //   name: '地图 - 1',
        //   default_img: 'icondituzhaoren1',
        //   selected_img: 'icondituzhaoren1'
        // }, {
        //   name: '地图 - 2',
        //   default_img: 'iconbiaoqianye-dituzhaoren1',
        //   selected_img: 'iconbiaoqianye-dituzhaoren1'
      }, {
        name: '订单 - 1',
        default_img: 'icondingdan3',
        selected_img: 'icondingdan2'
      }, {
        name: '订单 - 1',
        default_img: 'icondingdan2',
        selected_img: 'icondingdan2'
      }, {
        name: '订单 - 2',
        default_img: 'iconshangcheng2',
        selected_img: 'iconshangcheng2'
      }, {
        name: '订单 - 3',
        default_img: 'icondingdan4',
        selected_img: 'icondingdan4'
      }, {
        name: '订单 - 4',
        default_img: 'icondingdan11',
        selected_img: 'icondingdan11'
      }, {
        name: '我的 - 1',
        default_img: 'iconwode1',
        selected_img: 'iconwode2'
      }, {
        name: '我的 - 2',
        default_img: 'iconwode-2',
        selected_img: 'iconwode-1'
      }, {
        name: '我的 - 3',
        default_img: 'icon-mine',
        selected_img: 'icon-mine-fill'
      }, {
        name: '我的 - 1',
        default_img: 'icon-mine-fill',
        selected_img: 'icon-mine-fill'
      }, {
        name: '我的 - 2',
        default_img: 'iconxinzengguanzhu',
        selected_img: 'iconxinzengguanzhu'
      }, {
        name: '我的 - 3',
        default_img: 'iconwode11',
        selected_img: 'iconwode11'
      }, {
        name: '我的 - 4',
        default_img: 'iconwode12',
        selected_img: 'iconwode12'
      }, {
        name: '我的 - 5',
        default_img: 'iconwode3',
        selected_img: 'iconwode3'
      }, {
        name: '发现 - 1',
        default_img: 'iconfaxian-2',
        selected_img: 'iconfaxian-1'
      }, {
        name: '秒杀 - 1',
        default_img: 'iconmiaosha',
        selected_img: 'iconmiaosha-2',
        is_sign: 0
      }],
      showUndraggable: true
    }
  },
  name: 'tabbar-setting',
  props: {
    o: Object,
    allTabBar: Array,
    nowPageKey: Number
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  mounted () {
    if (!this.routesItem.auth.seckill || !this.routesItem.auth.storeplus) {
      this.iconList.map(item => {
        if (item.is_sign === 0) {
          item.is_sign = 1
        }
      })
    }
  },
  methods: {
    onMove (e) {
      let { index } = e.relatedContext
      return index !== 0
    },
    datadragEnd (evt) {
      let tabbar = this.o.tabbar
      let key = this.nowPageKey
      this.$emit('moveTab', { key, tabbar })
    },
    datadragEnding (evt) {
      this.showUndraggable = true
    },
    datadragStart (evt) {
      if (evt.oldIndex === 0) {
        evt.preventDefault()
        this.showUndraggable = false
      }
    },
    radioChange (val) {
      if (val === 1) {
        let list = JSON.parse(JSON.stringify((this.o.tabbar)))
        if (list.length > 5) {
          this.$message.error(`最多添加5个底部导航`)
          list.map((item, index) => {
            if (item.key === 20001) {
              item.is_show = 0
            }
          })
          this.o.tabbar = list
        } else {
          this.$emit('radioChange', this.o)
        }
      } else {
        this.$emit('radioChange', this.o)
      }
    },
    changeInput (val, active) {
      this.$emit('input', {
        val,
        active
      })
    },
    isSelect (key) {
      let isSelect = this.o.tabbar.some(e => {
        return e.id === key
      })
      return isSelect
    },
    // 删除导航菜单
    del (index, key) {
      if (this.o.tabbar.length <= 1) {
        this.$message.error(`至少要有1个底部导航`)
        return
      }
      this.$emit('del', { index, key })
    },
    onToggleChange (key) {
      if (this.active === key) {
        this.active = ''
        this.showUndraggable = true
      } else {
        this.active = key
        this.showUndraggable = false
      }
    },
    // 修改图标
    toChangeIcon (index, key) {
      // if (key < 10000) return;
      this.tabInd = index
      this.dialogVisible = true
      this.iconInd = -1
    },
    // 选择图标
    toCheckIcon (index) {
      this.iconInd = index
    },
    // 确认并关闭图标库
    toConfirmIcon () {
      let {
        tabInd,
        iconInd
      } = this
      if (iconInd === -1) {
        this.$message.error(`请选择图标`)
        return
      }
      let {
        default_img: dimg,
        selected_img: simg
      } = this.iconList[iconInd]
      this.o.tabbar[tabInd].default_img = dimg
      this.o.tabbar[tabInd].selected_img = simg
      this.tabInd = -1
      this.iconInd = -1
      this.dialogVisible = false
    },
    // 添加导航
    handleCommand (index) {
      let number = 5
      if (this.o.tabbar.length >= number) {
        this.$message.error(`最多添加5个底部导航`)
        return
      }
      let item = this.allTabBar[index]
      this.o.tabbar.push(item)
      this.page[item.key] = {
        list: []
      }
      this.radioChange()
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.el-dropdown-menu {
  width: 115px;
  max-height: 65vh;
  overflow: auto;
}
.data-item {
  width: 100%;
  border: 1px solid #eeeeee;
  background: #f4f6f9;
  margin-top: 10px;
  position: relative;
}

.data-item:last-child {
  margin-bottom: 10px;
}

.data-item_title {
  height: 40px;
  display: flex;
  align-items: center;
  padding: 0 10px;
}

.data-item_line {
  height: 1px;
  background: #eee;
}

.del-box {
  position: absolute;
  right: -4px;
  top: -4px;
  z-index: 20;
}

.form-item {
  display: flex;
  align-items: center;
  padding: 10px;
}

.form-label {
  width: 80px;
  padding-left: 26px;
}

.form-explain {
  font-size: 12px;
  color: #888;
  margin-left: 10px;
}

.flex-1 {
  flex: 1;
}

.bold {
  font-weight: bold;
}

.add-btn {
  width: 320px;
  height: 70px;
  display: flex;
  align-items: center;
  border: 1px solid #eeeeee;
  background: #fff;
  margin-top: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.add-btn_title {
  color: #429dff;
  font-size: 14px;
}

.add-btn_caption {
  color: #999;
  font-size: 12px;
  margin-top: 4px;
}

.add-tip {
  font-size: 14px;
  color: #333;
  margin: 18px 0;
}

.flex-between {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.form-icon_box {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  margin-right: 10px;
}

.form-icon_top {
  border: 1px solid #eee;
  width: 70px;
  height: 70px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-change_text {
  margin-top: -80px;
  color: #409eff;
}

.form-icon_text {
  color: #999999;
  font-size: 14px;
  margin-top: 6px;
}

.diy-icon-list {
  max-height: 450px;
  overflow: auto;
}

.diy-item-child {
  width: 90px;
  height: 90px;
  padding: 10px;
  border-radius: 4px;
  margin: 0px 5px;
  cursor: pointer;
}

.diy-item-icon {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 10px;
}

.diy-item-icon-child {
  width: 30px;
  height: 30px;
  border-radius: 4px;
  background: rgba(238, 238, 238, 1);
  display: flex;
  align-items: center;
  justify-content: center;
}

.diy-item-text {
  height: 14px;
  font-size: 12px;
  font-family: Microsoft YaHei;
  font-weight: 200;
  color: rgba(0, 0, 0, 1);
  text-align: center;
  margin: 5px 0;
}

.diy-item-child.check {
  background: rgba(243, 243, 243, 1);
}

.diy-item-child.check .diy-item-icon-child {
  background: rgba(255, 255, 255, 1);
}
</style>
