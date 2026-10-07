<!--
 * @Description: 差评申诉
 * @Author: wen kun
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-04-30 16:03:35
 * @LastEditors: wen kun
-->
<template>
  <div class="lb-examine-goods">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <el-button
          @click="toChange(0)"
          :type="searchForm.list.status === 0 ? 'primary' : ''"
          plain
          size="medium"
          >全部（{{ count.all || 0 }}）</el-button
        >
        <el-button
          @click="toChange(1)"
          :type="searchForm.list.status === 1 ? 'primary' : ''"
          plain
          size="medium"
          >未处理（{{ count.nopass || 0 }}）</el-button
        >
        <el-button
          @click="toChange(2)"
          :type="searchForm.list.status === 2 ? 'primary' : ''"
          plain
          size="medium"
          >已处理（{{ count.pass || 0 }}）</el-button
        >
      </el-row>
      <div class="space-lg"></div>
      <el-table
        v-loading="loading.list"
        :data="tableData.list"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column
          prop="order_code"
          label="订单号"
          min-width="200"
        ></el-table-column>
        <el-table-column
          prop="coach_name"
          :label="`${$t('action.attendantName')}昵称`"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="mobile"
          label="手机号"
          min-width="120"
        ></el-table-column>
        <el-table-column prop="status" label="申诉状态">
          <template slot-scope="scope">
            <el-tag :type="statusText[scope.row.status].type">{{
              statusText[scope.row.status].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column
          prop="create_time"
          label="申诉时间"
          min-width="160"
        ></el-table-column>
        <el-table-column label="操作" min-width="200" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                :type="scope.row.status === 1 ? 'success' : 'primary'"
                @click="
                  $router.push(`/feedback/appeal/detail?id=${scope.row.id}`)
                "
                v-show="[1, 2].includes(scope.row.status)"
                v-hasPermi="
                  scope.row.status === 1
                    ? `${$route.name}-handle`
                    : `${$route.name}-view`
                "
                >{{
                  $t(scope.row.status === 1 ? 'action.handle' : 'action.view')
                }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="badComment(scope.row.order_id)"
                v-show="scope.row.status === 1"
                v-hasPermi="`${$route.name}-badComment`"
                >查看差评</lb-button
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
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import moment from 'moment'
export default {
  data () {
    return {
      loading: { list: false, user: false },
      count: {},
      searchForm: {
        list: {
          page: 1,
          limit: 10,
          status: 0
        },
        user: {
          page: 1,
          limit: 10,
          nickName: ''
        },
        status: 0
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
      showDialog: false,
      statusText: {
        1: {
          type: 'danger',
          text: '未处理'
        },
        2: {
          type: 'primary',
          text: '已处理'
        }
      }
    }
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
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
     * @method: 申述列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.tableData[key] = []
      this.loading[key] = true
      let searchForm = this.searchForm[key]
      let { code, data } = await this.$api.system.appealList(searchForm)
      this.loading[key] = false
      if (code !== 200) return
      this.tableData[key] = data.data
      this.total[key] = data.total
      this.count = {
        nopass: data.status1,
        pass: data.status2,
        all: Number(data.status1) + Number(data.status2)
      }
    },
    toChange (index) {
      this.searchForm.list.status = index
      this.getTableDataList(1, 'list')
    },
    badComment (id) {
      console.log(this.routesItem.routes)
      let routes = JSON.parse(JSON.stringify(this.routesItem.routes))
      let isUrl = false
      routes.map(item => {
        if (item.path === '/shop') {
          item.children.map(aitem => {
            if (aitem.name === 'ShopEvaluate') {
              isUrl = true
            }
          })
        }
      })
      if (isUrl) {
        this.$router.push(`/shop/evaluate/list?id=${id}`)
      } else {
        this.$message.error('暂无权限')
      }
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
