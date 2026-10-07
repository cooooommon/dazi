<!--
 * @Description: 卡券管理
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2023-03-27 17:38:45
 * @LastEditors: xiao li
-->
<template>
  <div class="lb-examine-goods">
    <top-nav />
    <div class="page-main">
      <lb-button
        type="primary"
        icon="el-icon-plus"
        @click="$router.push(`/market/coupon/edit`)"
        v-hasPermi="`${$route.name}-add`"
        >{{ $t('menu.MarketCouponAdd') }}</lb-button
      >
      <div class="space-lg"></div>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm.list"
          ref="listForm"
        >
          <el-form-item label="输入查询" prop="name">
            <el-input
              v-model="searchForm.list.name"
              placeholder="请输入卡券名称"
            ></el-input>
          </el-form-item>
          <el-form-item>
            <lb-button
              size="medium"
              type="primary"
              icon="el-icon-search"
              style="margin-right: 5px"
              @click="getTableDataList(1, 'list')"
              >{{ $t('action.search') }}</lb-button
            >
            <lb-button
              size="medium"
              icon="el-icon-refresh-left"
              style="margin-right: 5px"
              @click="resetForm('list')"
              >{{ $t('action.reset') }}</lb-button
            >
          </el-form-item>
        </el-form>
      </el-row>
      <el-table
        v-loading="loading.list"
        :data="tableData.list"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="title" label="卡券名称"></el-table-column>
        <!-- <el-table-column
          prop="stock"
          width="100"
          label="卡券数量"
        ></el-table-column> -->
        <el-table-column prop="type" label="使用条件">
          <template slot-scope="scope">
            <p>
              {{
                scope.row.type === 0
                  ? `消费满¥${scope.row.full}减¥${scope.row.discount}`
                  : `立减¥${scope.row.discount}`
              }}
            </p>
          </template>
        </el-table-column>
        <el-table-column prop="send_type" min-width="120" label="派发方式">
          <template slot-scope="scope">
            <p>{{ sendType[scope.row.send_type] }}</p>
          </template>
        </el-table-column>
        <el-table-column
          prop="top"
          width="100"
          label="排序值"
        ></el-table-column>
        <el-table-column prop="create_time" min-width="120" label="创建时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="是否上架">
          <template slot-scope="scope">
            <el-switch
              :disabled="
                $route.meta.pagePermission[0].auth.includes('edit')
                  ? false
                  : true
              "
              v-model="scope.row.status"
              :active-value="1"
              :inactive-value="0"
              @change="updateItem(scope.row.id, scope.row.status)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="120" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="$router.push(`/market/coupon/edit?id=${scope.row.id}`)"
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
              <lb-button
                size="mini"
                plain
                type="success"
                @click="toShowDialog(scope.row)"
                v-show="scope.row.send_type === 1"
                v-hasPermi="`${$route.name}-handOut`"
                >指定派发</lb-button
              >
            </div>
          </template>
        </el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.list.page"
        :pageSize="searchForm.list.limit"
        :total="total.list"
        @handleSizeChange="handleSizeChange($event, 'list')"
        @handleCurrentChange="handleCurrentChange($event, 'list')"
      >
      </lb-page>

      <el-dialog
        title="指定派发"
        :visible.sync="showDialog"
        width="1000px"
        center
      >
        <lb-tips>
          <div class="flex-y-center">
            卡券名称：
            <div class="c-link">{{ cur_coupon.title }}</div>
          </div>
          <div class="flex-y-center">
            使用条件：
            <div class="c-link">
              {{
                cur_coupon.type === 0
                  ? `消费满¥${cur_coupon.full}减¥${cur_coupon.discount}`
                  : `立减¥${cur_coupon.discount}`
              }}
            </div>
          </div>
        </lb-tips>
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
          ref="multipleTable"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column
            prop="id"
            width="100"
            label="用户ID"
          ></el-table-column>
          <el-table-column prop="nickName" label="用户昵称"></el-table-column>
          <el-table-column prop="avatarUrl" width="150" label="用户头像">
            <template slot-scope="scope">
              <lb-image :src="scope.row.avatarUrl" /> </template
          ></el-table-column>
          <el-table-column prop="phone" label="手机号"></el-table-column
          ><el-table-column prop="num" label="卡券数量" width="220">
            <template slot-scope="scope">
              <el-input-number
                class="lb-input-number mini"
                :controls="false"
                :min="1"
                :precision="0"
                v-model="scope.row.num"
                placeholder="请输入卡券数量"
              ></el-input-number>
            </template>
          </el-table-column>
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
          <el-button @click="showDialog = false">取 消</el-button>
          <el-button type="primary" @click="handleDialogConfirm"
            >确 定</el-button
          >
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  data () {
    return {
      loading: { list: false, user: false },
      sendType: {
        0: '活动派发',
        1: '平台定向派发',
        2: '用户领取'
      },
      searchForm: {
        list: {
          page: 1,
          limit: 10,
          name: ''
        },
        user: {
          page: 1,
          limit: 10,
          nickName: ''
        }
      },
      tableData: {
        list: [],
        user: []
      },
      total: {
        list: 0,
        user: 0
      },
      cur_coupon: {},
      multipleSelection: [],
      showDialog: false
    }
  },
  activated () {
    this.getTableDataList(1, 'list')
  },
  methods: {
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
     * @method: 获取列表：卡券列表/用户列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.tableData[key] = []
      this.loading[key] = true
      let searchForm = this.searchForm[key]
      let { code, data } = key === 'list' ? await this.$api.market.couponList(searchForm) : await this.$api.custom.userList(searchForm)
      this.loading[key] = false
      if (code !== 200) return
      this.tableData[key] = data.data
      this.total[key] = data.total
    },
    confirmDel (id) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      })
        .then(() => {
          this.updateItem(id, -1)
        })
        .catch(() => { })
    },
    async updateItem (id, status) {
      this.$api.market.couponUpdate({ id, status }).then((res) => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status === -1) {
            this.searchForm.list.page = this.searchForm.list.page < Math.ceil((this.total.list - 1) / this.searchForm.list.limit) ? this.searchForm.list.page : Math.ceil((this.total.list - 1) / this.searchForm.list.limit)
            this.getTableDataList('', 'list')
          }
        } else {
          if (status === -1) return
          this.getTableDataList('', 'list')
        }
      })
    },
    async toShowDialog (item) {
      this.searchForm.user.nickName = ''
      this.cur_coupon = JSON.parse(JSON.stringify(item))
      await this.getTableDataList(1, 'user')
      this.showDialog = !this.showDialog
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    },
    async handleDialogConfirm () {
      let multipleSelection = JSON.parse(JSON.stringify(this.multipleSelection))
      if (multipleSelection.length < 1) {
        this.$message.error(`请选择用户`)
        return
      }
      for (let key in multipleSelection) {
        let index = key * 1 + 1
        let { id, nickName, num = 0 } = multipleSelection[key]
        let name = nickName ? `；用户昵称：${nickName}` : ''
        if (!num) {
          this.$message.error(`选择用户 第${index}条数据：（用户ID：${id}${name}）未设置卡券数量`)
          return
        }
      }
      let user = multipleSelection.map(item => {
        return { id: item.id, num: item.num }
      })
      let param = {
        coupon_id: this.cur_coupon.id,
        user
      }
      let { code } = await this.$api.market.couponRecordAdd(param)
      if (code !== 200) {
        this.showDialog = false
        return
      }
      this.$message.success(`卡券派发成功`)
      this.getTableDataList('', 'list')
      this.showDialog = false
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
.el-table {
  .table-goods-info {
    width: 280px;
    height: 80px;
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    font-size: 12px;

    .goods-info-r {
      width: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-around;

      p {
        width: 210px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .price {
        color: red;
      }
    }

    .el-image {
      min-width: 70px;
      height: 70px;
      margin-right: 10px;
    }

    &:last-child {
      margin-bottom: 0;
    }
  }
}
</style>
