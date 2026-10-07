<!--
 * @Descripttion: 编辑活动
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: xiao li
 * @LastEditTime: 2023-03-27 17:39:03
-->
<template>
  <div class="lb-edit-card">
    <top-nav></top-nav>
    <div class="page-main" v-if="isLoad">
      <lb-tips
        ><div>
          推荐人：邀请一定数量的好友授权用户信息及手机号后才可获得相应奖励
        </div>
        <div class="mt-sm">
          被推荐人：被推荐人授权用户信息及手机号后即可获得相应奖励
        </div>
      </lb-tips>
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
        class="basic-form"
      >
        <el-form-item label="邀请有礼活动" prop="is_atv_status">
          <el-radio-group @change="toChangeAtv" v-model="subForm.is_atv_status">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >开启后，手机端个人中心会展示「邀请有礼」模块，如果有用户投诉，域名则可能被封</lb-tool-tips
          >
        </el-form-item>
        <div v-show="subForm.is_atv_status === 1">
          <el-form-item label="开启活动" prop="status">
            <el-radio-group v-model="subForm.status">
              <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
              <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
            </el-radio-group>
            <lb-tool-tips
              >开启后，在活动有效时间内，用户可参与活动</lb-tool-tips
            >
          </el-form-item>
          <el-form-item label="分享图" prop="share_img">
            <lb-cover
              :fileList="subForm.share_img"
              @selectedFiles="getCover"
            ></lb-cover>
            <lb-tool-tips>图片建议尺寸: 652 * 652</lb-tool-tips>
          </el-form-item>
          <el-form-item label="活动时间" prop="time">
            <el-date-picker
              v-model="subForm.time"
              type="datetimerange"
              range-separator="至"
              start-placeholder="开始时间"
              end-placeholder="结束时间"
              value-format="timestamp"
              :picker-options="pickerOptions"
            >
            </el-date-picker>
          </el-form-item>
          <el-form-item label="邀请好友数" prop="inv_user_num">
            <el-input-number
              class="lb-input-number"
              :controls="false"
              :min="1"
              :precision="0"
              v-model="subForm.inv_user_num"
              placeholder="请输入邀请好友数"
            ></el-input-number>
          </el-form-item>
          <el-form-item label="邀请有效期" prop="inv_time">
            <el-input-number
              class="lb-input-number"
              :controls="false"
              :min="1"
              :precision="0"
              v-model="subForm.inv_time"
              placeholder="请输入邀请有效期"
            ></el-input-number>
            <div>小时</div>
          </el-form-item>
          <el-form-item label="发起活动次数" prop="atv_num">
            <el-input-number
              class="lb-input-number"
              :controls="false"
              :min="1"
              :precision="0"
              v-model="subForm.atv_num"
              placeholder="请输入发起活动次数"
            ></el-input-number>
            <div>次/人</div>
          </el-form-item>
          <el-form-item label="获得奖励人" prop="inv_user">
            <el-checkbox-group @change="changeCheckBox" v-model="checkList">
              <div
                v-for="(item, index) in authList"
                :key="index"
                :style="{
                  display: 'inline-block',
                  marginLeft: index === 0 ? 0 : '15px'
                }"
              >
                <el-checkbox
                  :disabled="item.is_check ? true : false"
                  :label="item.title"
                ></el-checkbox>
                <lb-tool-tips v-if="item.tips">{{ item.tips }}</lb-tool-tips>
              </div>
            </el-checkbox-group>
          </el-form-item>
          <el-form-item label="获得卡券" prop="coupon"
            ><lb-button type="primary" icon="el-icon-plus" @click="toShowDialog"
              >选择卡券</lb-button
            >
            <el-table
              :data="subForm.coupon"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              class="mt-lg"
              style="width: 100%"
            >
              <el-table-column prop="id" label="ID"></el-table-column>
              <el-table-column prop="title" label="卡券名称"></el-table-column>
              <el-table-column prop="num" label="卡券数量">
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
              <el-table-column width="180" label="操作" fixed="right">
                <template slot-scope="scope">
                  <div class="table-operate">
                    <lb-button
                      size="mini"
                      plain
                      type="danger"
                      @click="confirmDel(scope.row.id)"
                      >{{ $t('action.delete') }}</lb-button
                    >
                  </div>
                </template>
              </el-table-column>
            </el-table>
          </el-form-item>
          <el-form-item>
            <lb-button type="primary" @click="submitForm" v-preventReClick>{{
              $t('action.submit')
            }}</lb-button>
          </el-form-item>
        </div>
      </el-form>

      <el-dialog
        title="获得卡券"
        :visible.sync="showDialog"
        width="800px"
        center
      >
        <el-form
          :inline="true"
          :model="searchForm"
          ref="searchForm"
          label-width="70px"
        >
          <el-form-item label="输入查询" prop="name">
            <el-input
              v-model="searchForm.name"
              placeholder="请输入卡券名称"
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
        <el-table
          :data="tableData"
          ref="multipleTable"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column prop="id" label="ID"></el-table-column>
          <el-table-column prop="title" label="卡券名称"></el-table-column>
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
export default {
  data () {
    let validateInt = (rule, value, callback) => {
      if (this.subForm.inv_user === 0 && this.subForm.to_inv_user === 0) {
        callback(new Error('请选择获得奖励人'))
      } else {
        callback()
      }
    }
    return {
      navTitle: '',
      isLoad: false,
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() < Date.now() - (24 * 3600 * 1000)
        }
      },
      checkList: [],
      options: [],
      value: [],
      authList: [
        {
          title: '推荐人',
          key: 'inv_user',
          is_check: true
        }, {
          title: '被推荐人',
          key: 'to_inv_user',
          tips: '勾选被推荐人，被推荐人授权用户信息及手机号后即可获得相应奖励'
        }
      ],
      // 基本信息
      subForm: {
        id: 0,
        is_atv_status: 1,
        status: 1,
        share_img: [],
        time: [],
        inv_user_num: '',
        inv_time: '',
        atv_num: '',
        inv_user: 1,
        to_inv_user: 0,
        coupon: []
      },
      subFormRules: {
        is_atv_status: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        status: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        share_img: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        time: { required: true, type: 'array', message: '请选择活动时间', trigger: 'blur' },
        inv_user_num: { required: true, type: 'number', message: '请输入邀请好友数', trigger: 'blur' },
        inv_time: { required: true, type: 'number', message: '请输入邀请有效期', trigger: 'blur' },
        atv_num: { required: true, type: 'number', message: '请输入发起活动次数', trigger: 'blur' },
        inv_user: { required: true, validator: validateInt, trigger: 'blur' },
        coupon: { required: true, type: 'array', message: '请选择获得卡券', trigger: 'blur' }
      },
      searchForm: {
        page: 1,
        limit: 10,
        status: 1,
        send_type: 0,
        name: ''
      },
      total: 0,
      loading: false,
      tableData: [],
      multipleSelection: [],
      showDialog: false
    }
  },
  created () {
    this.getDetail()
  },
  methods: {
    async getDetail (id) {
      let { code, data } = await this.$api.market.couponAtvInfo()
      if (code !== 200) return
      data.inv_user = 1
      data.share_img = data.share_img ? [{ url: data.share_img }] : []
      data.coupon = data.coupon || []
      data.time = [data.start_time * 1000, data.end_time * 1000]
      let checkItem = []
      this.authList.map(item => {
        if (data[item.key] === 1) {
          checkItem.push(item.title)
        }
      })
      this.checkList = checkItem
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      this.isLoad = true
    },
    getCover (img) {
      this.subForm.share_img = img
    },
    /**
     * @method: 多选框
     * @param {*} e
     */
    changeCheckBox (e) {
      this.authList.map(item => {
        this.subForm[item.key] = e.includes(item.title) ? 1 : 0
      })
    },
    async toShowDialog () {
      this.searchForm.name = ''
      await this.getTableDataList()
      this.showDialog = !this.showDialog
    },
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
      let { code, data } = await this.$api.market.couponList(this.searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    },
    handleDialogConfirm () {
      let coupon = JSON.parse(JSON.stringify(this.subForm.coupon))
      let arr1 = coupon.length > 0 ? coupon.map(item => { return item.id }) : []
      this.multipleSelection.map(item => {
        if (arr1.includes(item.id)) return
        item.num = item.num || 1
        coupon.push(item)
      })
      this.subForm.coupon = coupon
      this.showDialog = false
    },
    confirmDel (id) {
      let index = this.subForm.coupon.findIndex(item => {
        return item.id === id
      })
      this.subForm.coupon.splice(index, 1)
    },
    toChangeAtv () {
      let { is_atv_status: atv } = this.subForm
      this.$api.market.couponAtvUpdate({ is_atv_status: atv }).then((res) => {
        if (res.code === 200) {
          this.$message.success(this.$t('tips.successRev'))
        }
      })
    },
    /**
     * @name: 编辑
     * @param {*}
     */
    submitForm () {
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          subForm.share_img = subForm.share_img[0].url
          subForm.start_time = subForm.time[0] / 1000
          subForm.end_time = subForm.time[1] / 1000

          let arr = subForm.coupon.map(item => {
            return { id: item.id, num: item.num }
          })
          subForm.coupon = arr
          delete subForm.time
          this.$api.market.couponAtvUpdate(subForm).then((res) => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successRev'))
            }
          })
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-edit-card {
  width: 100%;
  .el-input,
  .el-select,
  .lb-input-number,
  .el-cascader {
    width: 300px;
  }
  .lb-input-number.mini {
    width: 150px;
  }
}
</style>
