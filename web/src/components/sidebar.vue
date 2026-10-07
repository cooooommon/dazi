<!-- 右侧边栏 -->
<template>
  <div class="lb-sidebar">
    <div class="menu">
      <vue-scroll :ops="ops">
        <ul class="menu-top" @click="showDialog">
          <router-link
            v-for="(item, index) in routes"
            tag="li"
            :key="index"
            active-class="menu-active"
            :to="item.path"
            :style="{ marginTop: item.meta.menuName === 'diy' ? '50px' : '0' }"
            v-show="!item.isHidden"
          >
            <i
              v-if="item.meta.icon"
              class="iconfont"
              :class="item.meta.icon"
            ></i>
            <img v-else :src="item.meta.img" />
            {{ $t('menu.' + item.meta.menuName) }}
          </router-link>
        </ul>
      </vue-scroll>
    </div>
    <div :class="subnav.length > 0 ? 'isopen' : ''" class="submenu">
      <vue-scroll :ops="ops">
        <el-collapse
          v-for="(item, index) in subnav"
          :key="index"
          v-model="activeNames"
        >
          <el-collapse-item :title="$t('menu.' + item.name)" :name="index">
            <div class="item" v-for="(items, indexs) in item.url" :key="indexs">
              <router-link
                tag="span"
                active-class="el-collapse-item-active"
                :to="items.url"
                >{{ $t('menu.' + items.name) }}</router-link
              >
            </div>
          </el-collapse-item>
        </el-collapse>
      </vue-scroll>
    </div>
  </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex'
import vuescroll from 'vuescroll'
export default {
  data () {
    return {
      isFirst: true,
      routes: [], // 路由表
      subnav: [], // 二级菜单表
      activeNames: [], // 二级菜单展开的配置
      ops: {
        vuescroll: {
          wheelScrollDuration: 600
        },
        scrollPanel: {
          initialScrollY: false,
          initialScrollX: false,
          scrollingX: true,
          scrollingY: true,
          speed: 1000,
          easing: undefined,
          verticalNativeBarPos: 'right'
        },
        rail: {},
        bar: {
          showDelay: 500,
          onlyShowBarOnScroll: true,
          keepShow: false,
          background: '#c1c1c1',
          opacity: 0.5,
          hoverStyle: false,
          specifyBorderRadius: false,
          minSize: false,
          size: '6px',
          disable: false
        }
      }
    }
  },
  computed: {
    ...mapState({
      promptData: (state) => state.routes.promptData
    })
  },
  components: {
    vuescroll
  },
  created () {
    this.handleRoute()
    this.handleSubnav(this.$route.name)
  },
  methods: {
    ...mapMutations(['changePromptData']),
    showDialog () {
      // this.$api.getAgentAuthStatus().then(res => {
      //   this.changePromptData(res.data)
      //   this.status = res.data.status
      // })
    },
    /**
     * @method 处理路由表，渲染到侧边栏
     */
    handleRoute () {
      let { routes } = this.$store.getters
      this.routes = routes.filter((item) => {
        if (!item.hidden) {
          return item
        }
      })
    },
    /**
     * @method 处理二级菜单导航
     */
    handleSubnav (name) {
      let { routes } = this
      for (let i = 0, len = routes.length; i < len; i++) {
        let children = routes[i].children
        for (let j = 0, l = children.length; j < l; j++) {
          if (children[j].name === name) {
            this.subnav = routes[i].meta.subNavName || []
            this.$store.commit(
              'handleSideBarSwitch',
              this.subnav.length > 0
            )
            this.openSubnav()
            break
          }
        }
      }
    },
    /**
     * @method 展开二级菜单
     */
    openSubnav () {
      let arr = []
      this.subnav.forEach((item, index) => {
        arr.push(index)
      })
      this.activeNames = arr
    }
  },
  watch: {
    $route: {
      handler (val, oldVal) {
        this.handleSubnav(val.name)
      },
      // 深度观察监听
      deep: true
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-sidebar {
  position: fixed;
  top: 70px;
  left: 0;
  display: flex;
  height: calc(100% - 70px);
  z-index: 2;
  .menu {
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 120px;
    height: 100%;
    background: #273543;
    z-index: 3;
    .menu-top {
      width: 100%;
      color: #cccccc;
      font-size: 14px;
      text-align: center;
      line-height: 50px;
      li {
        width: 100%;
        // height: 50px;
        min-height: 50px;
        padding: 15px 8px;
        line-height: 20px;
        // border-top: 1px solid #cccccc;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        i {
          margin-right: 10px;
        }
        img {
          display: inline-block;
          width: 20px;
          height: 20px;
          margin-right: 10px;
        }
        &:hover {
          color: #09f;
        }
      }
      .menu-active {
        background: #fff;
        color: #273543;
        &:hover {
          color: #273543;
        }
      }
    }
  }
  .submenu {
    position: absolute;
    top: 0;
    left: 120px;
    z-index: 2;
    width: 159px;
    height: 100%;
    background: #fff;
    display: none;
    transition: all 0.2s linear;
    .el-collapse {
      width: 130px;
      margin: 0 auto;
      border-top: 1px;
      .item {
        width: 100%;
        cursor: pointer;
        text-align: center;
        span {
          display: inline-block;
          width: 100%;
          line-height: 20px;
        }
        &:hover {
          span {
            color: #09f;
          }
          .el-collapse-item-active {
            color: #273543;
          }
        }
        .el-collapse-item-active {
          width: 100%;
          display: inline-block;
          background: #f0f0f0;
          border-radius: 2px;
          text-align: center;
        }
      }
    }
  }
  .isopen {
    width: 159px;
    display: block;
  }
}
</style>
