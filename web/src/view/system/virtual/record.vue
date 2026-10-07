<!--
 * @Description: 录音文件
 * @Author: xiao li
 * @Date: 2022-12-09 16:40:59
 * @LastEditTime: 2023-12-25 17:38:16
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-system-virtual-record">
    <top-nav />
    <div class="page-main">
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm"
          ref="searchForm"
        >
          <el-form-item label="拨打时间" prop="start_time">
            <el-date-picker
              @change="getTableDataList(1)"
              v-model="searchForm.start_time"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              value-format="timestamp"
              :picker-options="pickerOptions"
              :default-time="['00:00:00', '23:59:59']"
            >
            </el-date-picker>
          </el-form-item>
        </el-form>
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" fixed> </el-table-column>
        <el-table-column prop="record_name" label="文件名称" min-width="200">
        </el-table-column>
        <el-table-column prop="call_time" label="拨打时间" min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.call_time | handleTime(1) }}</p>
            <p>{{ scope.row.call_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="start_time" label="接通时间" min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.start_time | handleTime(1) }}</p>
            <p>{{ scope.row.start_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="end_time" label="挂断时间" min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.end_time | handleTime(1) }}</p>
            <p>{{ scope.row.end_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column
          prop="phone_a"
          :label="`${$t('action.attendantName')}号码`"
          min-width="120"
        >
        </el-table-column>
        <el-table-column prop="phone_b" label="客户号码" min-width="120">
        </el-table-column>
        <el-table-column prop="phone_x" label="虚拟号码" min-width="120">
        </el-table-column>
        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate" v-if="scope.row.record_url">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="toShowDialog(scope.row)"
                v-hasPermi="`${$route.name}-play`"
                >{{ $t('action.play') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="toDownLoad(scope.row)"
                v-hasPermi="`${$route.name}-download`"
                >{{ $t('action.download') }}</lb-button
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
        title="音频内容"
        :visible.sync="showDialog"
        width="600px"
        center
      >
        <div class="flex-center flex-column" style="padding: 50px">
          <audio ref="audio_item" controls :src="subForm.record_url"></audio>
          <div class="f-title c-title text-bold pt-lg">
            {{ subForm.record_name }}
          </div>
        </div>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  components: {},
  data () {
    return {
      loading: false,
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      storeList: [],
      searchForm: {
        page: 1,
        limit: 10,
        start_time: '',
        end_time: ''
      },
      tableData: [],
      total: 0,
      showDialog: false,
      subForm: {
        record_url: '',
        record_name: ''
      }
    }
  },
  async created () {
    this.getTableDataList()
  },
  methods: {
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handleCurrentChange(1)
    },
    handleCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    /**
     * @method: 获取列表
     */
    getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { start_time: time } = searchForm
      if (time && time.length > 0) {
        searchForm.start_time = time[0] / 1000
        searchForm.end_time = time[1] / 1000
      } else {
        searchForm.start_time = ''
        searchForm.end_time = ''
      }
      this.$api.system.phoneRecordList(searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          res.data.data.map(item => {
            let arr = item.record_url && item.record_url.includes('?Expires=') ? item.record_url.split('?Expires=')[0].split('/') : []
            item.record_name = arr.length > 0 ? arr[arr.length - 1] : ''
          })
          this.tableData = res.data.data
          this.total = res.data.total
        }
      })
    },
    async toShowDialog (item = {}) {
      for (let key in this.subForm) {
        this.subForm[key] = item[key]
      }
      this.showDialog = !this.showDialog
    },
    toDownLoad (item) {
      let { record_url: src, record_name: title } = item
      let a = document.createElement('a') // 生成一个a元素
      let event = new MouseEvent('click') // 创建一个单击事件
      a.download = title // 设置图片名称
      a.href = src // 将生成的URL设置为a.href属性
      a.dispatchEvent(event) // 触发a的单击事件
    }
  },
  watch: {
    showDialog (newVal, oldVal) {
      if (newVal === false) {
        this.$refs.audio_item.pause()
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
.lb-system-virtual-record {
  width: 100%;
  .page-main {
    width: 100%;
    .el-input,
    .el-select,
    .el-input-number {
      width: 200px;
    }
    .dialog-form {
      .el-input,
      .el-select,
      .el-input-number {
        width: 300px;
      }
    }
  }
}
</style>
