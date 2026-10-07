<!--
 * @Descripttion: 角色设置
 * @Author: xiao li
 * @Date: 2021-03-11 15:42:01
 * @LastEditors: wen kun
 * @LastEditTime: 2024-11-29 10:23:42
-->

<template>
  <div class="lb-system-news">
    <top-nav />
    <div class="page-main">
      <lb-button
        type="primary"
        icon="el-icon-plus"
        @click="toShowDialog"
        v-hasPermi="`${$route.name}-add`"
        >{{ $t('menu.AccountRoleAdd') }}</lb-button
      >
      <div class="space-lg"></div>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="角色名称" prop="title">
            <el-input
              v-model="searchForm.title"
              placeholder="请输入角色名称"
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
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="title" label="角色名称"></el-table-column>
        <el-table-column prop="create_time" label="创建时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column min-width="120" label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="toShowDialog(scope.row)"
                v-hasPermi="`${$route.name}-edit`"
                >{{ $t('action.edit') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id)"
                v-hasPermi="`${$route.name}-delete`"
                >{{ $t('action.delete') }}</lb-button
              >
            </div>
          </template>
        </el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.page"
        :pageSize="searchForm.limit"
        :total="total"
        @handleSizeChange="handleSizeChange"
        @handleCurrentChange="handleCurrentChange"
      >
      </lb-page>

      <el-dialog
        :title="$t(subForm.id ? 'menu.AccountRoleEdit' : 'menu.AccountRoleAdd')"
        :visible.sync="showDialog"
        width="500px"
        center
      >
        <el-form
          class="dialog-form"
          :model="subForm"
          ref="subForm"
          :rules="subFormRules"
          label-width="100px"
        >
          <el-form-item label="角色名称" prop="title">
            <el-input
              v-model="subForm.title"
              placeholder="请输入角色名称"
              maxlength="20"
              show-word-limit
            ></el-input>
          </el-form-item>
          <el-form-item label="选择权限" prop="role">
            <el-checkbox
              v-model="checkTreeAll"
              @change="handleCheckTreeAllChange"
              >全选</el-checkbox
            >
            <div class="data-box">
              <vue-scroll :ops="ops">
                <el-tree
                  :data="authPermiList"
                  show-checkbox
                  default-expand-all
                  node-key="id"
                  ref="tree"
                  highlight-current
                  :props="defaultProps"
                  :default-checked-keys="currentNodeKey"
                  @check-change="handleClickTreeNode"
                  @check="currentChecked"
                >
                </el-tree>
              </vue-scroll>
            </div>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="submitFormInfo" v-preventReClick
            >确 定</el-button
          >
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
export default {
  data () {
    let validateRole = (rule, value, callback) => {
      let arr = this.toReturnPermi()
      if (arr.length === 0) {
        callback(new Error(`请选择权限`))
      } else {
        callback()
      }
    }
    return {
      loading: false,
      searchForm: {
        page: 1,
        limit: 10,
        title: '',
        type: 0
      },
      tableData: [],
      total: 0,
      count: 0,
      checkTreeAll: false,
      authPermiList: [],
      ops: {
        vuescroll: {
          wheelScrollDuration: 600
        },
        scrollPanel: {
          initialScrollY: false,
          initialScrollX: false,
          scrollingX: false,
          scrollingY: true,
          speed: 2000,
          easing: 'easeInOutQuart',
          verticalNativeBarPos: 'right'
        },
        rail: {},
        bar: {
          showDelay: 500,
          onlyShowBarOnScroll: false,
          keepShow: false,
          background: '#c1c1c1',
          opacity: 0.5,
          hoverStyle: false,
          specifyBorderRadius: false,
          minSize: false,
          size: '6px',
          disable: false
        }
      },
      defaultProps: {
        children: 'children',
        label: 'title'
      },
      currentNodeKey: [],
      showDialog: false,
      dialogType: 1,
      subForm: {
        id: 0,
        title: '',
        node: []
      },
      subFormRules: {
        title: { required: true, validator: this.$reg.isNotNull, text: '角色名称', reg_type: 2, trigger: 'blur' },
        role: { required: true, validator: validateRole, trigger: 'change' }
      },
      customUserView: ''
    }
  },
  async created () {
    this.getTableDataList(1)
    let allRoutes = JSON.parse(JSON.stringify(this.allRoutes))
    let arr = []
    allRoutes.map((item, index) => {
      let id = index * 1 + 1
      let children = []
      if (item.hidden || ['/account'].includes(item.path)) return
      item.children.map((aitem, aindex) => {
        let aid = aindex * 1 + 1
        if (aitem.hidden) return
        // 子账号 设为秒杀 跟随秒杀活动菜单新增权限使用
        if (aitem.name === 'StoreshopPackageList') {
          let aIndex = aitem.meta.pagePermission[0].auth.findIndex(aitem => {
            return aitem === 'setSeckill'
          })
          if (aIndex !== -1) {
            aitem.meta.pagePermission[0].auth.splice(aIndex, 1)
          }
        }
        let child = []
        let perInd = ['MarketArticleEnroll', 'SystemVirtualRecord', 'SystemCarFeeCity'].includes(aitem.name) ? 1 : 0
        aitem.meta.pagePermission[perInd].auth.map((bitem, bindex) => {
          let cid = bindex * 1 + 1
          let did = `${id}-${aid}-${cid}`
          if (aitem.name === 'CustomList') {
            if (bitem === 'view') {
              this.customUserView = did
            }
          }
          child.push({ id: `${id}-${aid}-${cid}`, name: bitem, title: this.$t(`action.${bitem}`), selected: false })
        })
        children.push({ id: `${id}-${aid}`, name: aitem.name, title: this.$t(`menu.${aitem.name}`), children: child, selected: false })
      })
      arr.push({ id: `${id}`, name: item.meta.menuName, title: this.$t(`menu.${item.meta.menuName}`), children, selected: false })
    })
    this.authPermiList = arr
  },
  computed: {
    ...mapState({
      allRoutes: state => state.routes.allRoutes
    })
  },
  methods: {
    resetForm (form) {
      this.$refs[form].resetFields()
      this.getTableDataList(1)
    },
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handleCurrentChange(1)
    },
    handleCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let { code, data } = await this.$api.account.roleList(this.searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    confirmDel (id) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, -1)
      }).catch(() => {

      })
    },
    async updateItem (id, status) {
      this.$api.account.roleUpdate({ id, status }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
            this.getTableDataList()
          }
        } else {
          if (status === -1) return
          this.getTableDataList()
        }
      })
    },
    async toShowDialog (item = {}) {
      for (let key in this.subForm) {
        this.subForm[key] = item[key]
      }
      let { id = 0 } = item
      this.showDialog = !this.showDialog
      if (!id) {
        this.currentNodeKey = []
        this.$refs.tree.setCheckedKeys([])
        return
      }
      let { data } = await this.$api.account.roleInfo({ id })
      let { node } = data
      let arr = node.map(item => { return item.node })
      let authPermiList = JSON.parse(JSON.stringify(this.authPermiList))
      let datas = []
      let count = 0
      authPermiList.map(item => {
        item.children.map(aitem => {
          if (aitem.children.length === 0) {
            count++
          } else {
            count += aitem.children.length
          }
          if (arr.includes(aitem.name)) {
            let ind = node.findIndex(bitem => {
              return bitem.node === aitem.name
            })
            let { auth } = node[ind]
            if (aitem.children.length === 0) {
              datas.push(aitem)
            }
            aitem.children.map(citem => {
              if (auth.includes(citem.name)) {
                datas.push(citem)
              }
            })
          }
        })
      })
      this.count = count
      this.checkTreeAll = datas.length === count
      this.$refs.tree.setCheckedNodes(datas)
    },
    handleClickTreeNode (obj) {
      obj.selected = !obj.selected
    },
    currentChecked (nodeObj, SelectedObj) {
      let { checkedKeys, halfCheckedNodes } = SelectedObj
      halfCheckedNodes.map(item => {
        this.handleNodeChildren(item, checkedKeys)
      })
      this.currentNodeKey = checkedKeys

      let arr = this.toReturnPermi(2)
      let count = 0
      arr.map(item => {
        count += item.auth.length === 0 ? 1 : item.auth.length
      })
      this.checkTreeAll = this.count === count
    },
    handleNodeChildren (item, checkedKeys) {
      let arr = item.children.map(aitem => {
        return aitem.name
      })
      if (arr.includes('pagedata')) {
        let checkArr = item.children.filter(bitem => {
          return bitem.selected
        })
        item.children.map(bitem => {
          if (bitem.name === 'pagedata' && checkArr.length > 0 && !checkedKeys.includes(bitem.id)) {
            checkedKeys.push(bitem.id)
          }
          if (['viewBalance', 'viewIntegral'].includes(bitem.name) && checkArr.length > 0 && checkedKeys.includes(bitem.id) && !checkedKeys.includes(this.customUserView)) {
            checkedKeys.push(this.customUserView)
          }
        })
      } else {
        item.children.map(aitem => {
          this.handleNodeChildren(aitem, checkedKeys)
        })
      }
    },
    handleCheckTreeAllChange () {
      let { checkTreeAll } = this
      let arr = this.authPermiList.map((item, index) => {
        return item.id
      })
      let keys = checkTreeAll ? arr : []
      this.$refs.tree.setCheckedKeys(keys)
    },
    toReturnPermi (key = 1) {
      let authPermiList = JSON.parse(JSON.stringify(this.authPermiList))
      let arr = []
      authPermiList.map(item => {
        if (item.children.length === 0 && !item.selected) return
        item.children.map(aitem => {
          if (aitem.children.length === 0 && !aitem.selected) return
          let auth = []
          aitem.children.map(bitem => {
            if (!bitem.selected && key === 1) return
            if (key === 2 && !bitem.selected && ((bitem.name !== 'pagedata') || (bitem.name === 'pagedata' && aitem.children.length === 1))) {
              return
            }
            auth.push(bitem.name)
          })
          if (aitem.children.length > 0 && auth.length === 0) return
          arr.push({ node: aitem.name, auth })
        })
      })
      return arr
    },
    async submitFormInfo () {
      let flag = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let methodModel = this.subForm.id ? 'roleUpdate' : 'roleAdd'
        let param = JSON.parse(JSON.stringify(this.subForm))
        param.node = this.toReturnPermi()
        if (param.node.length === 1 && param.node[0].node === 'ShopOrderNotice') {
          this.$message.error(`不能只选择订单通知哦`)
          return
        }
        let flag = false
        param.node.forEach(item => {
          if (['ShopOrder', 'ShopBellOrder', 'ShopRefund', 'ShopBellRefund', 'ShopRefuseOrder'].includes(item.node)) {
            flag = true
          }
        })

        let orderNoticeInd = param.node.findIndex(item => {
          return item.node === 'ShopOrderNotice'
        })

        if (!flag && orderNoticeInd !== -1) {
          this.$message.error(`请选择【服务订单、拒单管理、服务退款】里面的任意一项，不能只选择订单通知哦`)
          return
        }
        let { code } = await this.$api.account[methodModel](param)
        if (code !== 200) return
        this.$message.success(this.$t(param.id ? 'tips.successRev' : 'tips.successSub'))
        this.showDialog = false
        this.getTableDataList()
      }
    }
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    }
  }
}
</script>

<style lang="scss" scoped>
.page-main {
  .dialog-form {
    .el-input {
      width: 300px;
    }
  }
  .data-box {
    height: 50vh;
  }
}
</style>
