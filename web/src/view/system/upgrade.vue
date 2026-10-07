<!--
 * @Descripttion: app设置
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-10-25 18:39:34
-->
<template>
  <div class="lb-system-wechat">
    <top-nav></top-nav>
    <div class="page-main" v-if="isLoad">
      <lb-tips>更新系统将覆盖服务器里面的代码文件，请谨慎操作</lb-tips>
      <el-form
        v-loading="firstLoading"
        @submit.native.prevent
        :model="upgradeInfo"
        label-width="150px"
        class="config-form"
        v-if="upgradeInfo && upgradeInfo.code === 20000"
      >
        <lb-classify-title title="基本信息"></lb-classify-title>
        <div style="height: 20px"></div>
        <!-- <el-form-item label="站点名称：">
          <span>{{ upgradeInfo.data.domain.title }}</span>
        </el-form-item> -->
        <el-form-item label="授权域名：">
          <span>{{ upgradeInfo.data.domain.url }}</span>
        </el-form-item>
        <el-form-item label="授权使用时间：">
          <span>{{ upgradeInfo.data.domain.use_time | handleTime }}</span>
        </el-form-item>
        <el-form-item label="更新到期时间：">
          <span>{{
            upgradeInfo.data.domain.update_service_time | handleTime
          }}</span>
        </el-form-item>
        <div style="height: 20px"></div>
        <div v-if="upgradeInfo.data.version">
          <lb-classify-title title="版本更新"></lb-classify-title>
          <el-form-item label="创建时间：">
            <span>{{ upgradeInfo.data.version.create_time }}</span>
          </el-form-item>
          <el-form-item label="版本名称：">
            <span>{{ upgradeInfo.data.version.title }}</span>
          </el-form-item>
          <el-form-item label="更新说明：">
            <el-input
              type="textarea"
              show-word-limit
              autosize
              :readonly="true"
              resize="none"
              v-model="upgradeInfo.data.version.description"
            ></el-input>
          </el-form-item>
        </div>

        <div style="height: 20px"></div>
        <lb-classify-title title="版本号"></lb-classify-title>
        <div style="height: 20px"></div>
        <el-form-item label="当前版本：">
          <span>{{ upgradeInfo.location_version_no }}</span>
        </el-form-item>
        <el-form-item label="最新版本：" v-if="upgradeInfo.data.version">
          <span>{{ upgradeInfo.data.version.no }}</span>
          <lb-button
            v-if="
              upgradeInfo.is_upgrade === true &&
              upgradeInfo.location_version_no !== upgradeInfo.data.version.no
            "
            type="danger"
            :loading="loading"
            plain
            @click="confirmUpdate"
            style="margin-left: 20px"
            >更新系统</lb-button
          >
        </el-form-item>
        <el-form-item label="版本历史记录：">
          <div class="flex-y-center">
            共
            <div class="c-warning">{{ total }}</div>
            条记录
          </div>
          <div
            class="el-textarea b-1px-b"
            v-for="(item, index) in tableData"
            :key="index"
          >
            <div class="flex-between c-caption">
              <div class="c-title text-bold">{{ item.title }}</div>
              <div>{{ item.create_time }}</div>
            </div>
            <div
              class="f-desc"
              :class="[{ 'ellipsis-4': !item.is_show }]"
              v-html="item.description"
            ></div>
            <div class="flex-between" v-if="!item.is_show">
              <div></div>
              <div
                @click="toChangeItem(index)"
                class="flex-y-center c-link cursor-pointer"
              >
                展开更多<i
                  class="iconfont icon-right text-bold ml-sm rotate-90"
                ></i>
              </div>
            </div>
          </div>
          <div
            @click="handleCurrentChange"
            class="flex-y-center c-link cursor-pointer"
            v-if="tableData.length < total"
          >
            展开更多<i
              class="iconfont icon-right text-bold ml-sm rotate-90"
            ></i>
          </div>
        </el-form-item>
        <div style="height: 30px"></div>
      </el-form>
      <div v-if="upgradeInfo && upgradeInfo.msg">
        <lb-tips>{{ upgradeInfo.msg }}</lb-tips>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  inject: ['reload'],
  data () {
    return {
      isLoad: false,
      firstLoading: false,
      loading: false,
      upgradeInfo: {},
      isWe7: window.lbConfig.isWe7,
      tableData: [],
      total: 0,
      searchForm: {
        page: 1,
        limit: 5
      }
    }
  },
  created () {
    this.getDetail()
    this.getTableDataList()
  },
  methods: {
    async getDetail () {
      this.firstLoading = true
      let { code, data } = await this.$api.system.getUpgradeInfo()
      this.firstLoading = false
      if (code !== 200) return
      this.upgradeInfo = data
      this.isLoad = true
    },
    toChangeItem (index) {
      this.tableData[index].is_show = true
    },
    handleCurrentChange () {
      let { page, limit } = this.searchForm
      let { total } = this
      if (page * limit > total) return
      this.searchForm.page = page * 1 + 1
      this.getTableDataList()
    },
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.loading = true
      let { code, data } = await this.$api.system.getUpRecord(this.searchForm)
      this.loading = false
      if (code !== 200) return
      if (data && typeof (data) === 'object') {
        data.data.map(item => {
          item.is_show = false
          item.description = item.description ? item.description.replace(/\n/g, '<br>') : '暂无记录'
        })
        this.tableData = this.tableData.concat(data.data)
        this.total = data.total
      }
    },
    confirmUpdate () {
      this.$confirm(`更新系统将覆盖服务器里面的代码文件，请谨慎操作，是否要更新？`, this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }
      ).then(() => {
        this.updateSysVersion()
      })
    },
    async updateSysVersion () {
      this.loading = true
      let { code, data } = await this.$api.system.upgrade()
      this.loading = false
      if (code !== 200) return
      if (data.code && data.code === -1) {
        this.$message.error(data.msg)
        return
      }
      this.$message.success('更新成功！')
      await this.$api.base.clearCache()
      window.location.reload()
      // this.reload()
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
.el-form-item {
  margin-bottom: 0px;
  span,
  .el-textarea {
    margin-left: 10px;
    max-width: 800px;
  }
}
</style>
