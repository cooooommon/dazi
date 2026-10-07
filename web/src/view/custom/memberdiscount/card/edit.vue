<!--
 * @Description: 编辑会员卡套餐
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-08-08 16:06:24
 * @LastEditTime: 2024-11-20 17:10:04
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-custom-member-card-edit">
    <top-nav :title="navTitle" :isBack="true"></top-nav>
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
        class="basic-form"
      >
        <el-form-item label="套餐名称" prop="title">
          <el-input
            v-model="subForm.title"
            maxlength="5"
            show-word-limit
            placeholder="请输入会员套餐名称，例如：年卡会员"
          ></el-input>
        </el-form-item>
        <el-form-item label="套餐期限" prop="day">
          自购买日起
          <el-input
            v-model.number="subForm.day"
            :disabled="subForm.id * 1 > 0"
            class="mini ml-md mr-md"
            placeholder="请输入天数"
          >
          </el-input>
          天内有效
        </el-form-item>
        <el-form-item label="套餐现价" prop="price">
          <el-input v-model="subForm.price" placeholder="请输入套餐现价">
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="套餐划线价" prop="init_price">
          <el-input v-model="subForm.init_price" placeholder="请输入套餐划线价">
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="套餐标签" prop="icon">
          <el-input
            v-model="subForm.icon"
            maxlength="5"
            show-word-limit
            placeholder="请输入会员套餐标签"
          ></el-input>
        </el-form-item>
        <el-form-item label="排序值" prop="top">
          <el-input-number
            class="lb-input-number"
            v-model="subForm.top"
            :controls="false"
            :precision="0"
            :min="0"
            placeholder="请输入排序值"
          ></el-input-number>
          <lb-tool-tips>值越大, 排序越靠前</lb-tool-tips>
        </el-form-item>
        <el-form-item label="其他权益" prop="text">
          <el-input
            type="textarea"
            :rows="10"
            maxlength="1000"
            resize="none"
            show-word-limit
            placeholder="请输入其他权益"
            v-model="subForm.text"
          ></el-input>
        </el-form-item>
        <!-- <el-form-item label="选择优惠券" prop="coupon">
          <lb-button
            type="danger"
            size="small"
            icon="el-icon-plus"
            @click="toShowDialog('coupon')"
            >选择卡券</lb-button
          >
          <div class="space-lg"></div>
          <el-table
            :data="subForm.coupon"
            :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
            style="width: 600px"
          >
            <el-table-column prop="title" label="优惠券名称"></el-table-column>
            <el-table-column prop="title" label="使用条件">
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
            <el-table-column prop="num" label="数量" width="220">
              <template slot-scope="scope">
                <el-input-number
                  v-model="scope.row.num"
                  :precision="0"
                  :min="1"
                  :max="1000"
                ></el-input-number>
              </template>
            </el-table-column>
            <el-table-column label="操作" width="100">
              <template slot-scope="scope">
                <div class="table-operate">
                  <lb-button
                    size="mini"
                    plain
                    type="danger"
                    @click="toDelItem(scope.$index)"
                    >{{ $t('action.delete') }}</lb-button
                  >
                </div>
              </template>
            </el-table-column>
          </el-table>
        </el-form-item> -->

        <div style="height: 30px"></div>
        <el-form-item>
          <lb-button type="primary" @click="submitFormInfo" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>

      <el-dialog
        title="选择卡券"
        :visible.sync="showDialog.coupon"
        width="900px"
        top="5vh"
        center
      >
        <el-form
          :inline="true"
          :model="searchForm"
          ref="searchForm"
          label-width="70px"
          class="dialog-form"
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
          <el-table-column prop="title" label="数量" width="260">
            <template slot-scope="scope">
              <el-input-number
                v-model="scope.row.num"
                :precision="0"
                :min="1"
                :max="1000"
              ></el-input-number>
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
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.coupon = false">取 消</el-button>
          <el-button
            type="primary"
            @click="handleDialogConfirm"
            v-preventReClick
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
    return {
      navTitle: '',
      subForm: {
        id: '',
        title: '',
        day: '',
        price: '',
        init_price: '',
        icon: '',
        top: 0,
        coupon: [],
        text: ''
      },
      subFormRules: {
        title: { required: true, validator: this.$reg.isNotNull, reg_type: 2, text: '套餐名称', trigger: 'blur' },
        day: { required: true, validator: this.$reg.isNum, reg_type: 2, text: '天数', trigger: 'blur' },
        price: { required: true, validator: this.$reg.isMoney, reg_type: 1, text: '套餐现价', trigger: 'blur' },
        init_price: { required: true, validator: this.$reg.isMoney, reg_type: 1, text: '套餐划线价格', trigger: 'blur' },
        top: { required: true, type: 'number', message: '请输入排序值', trigger: 'blur' }
      },
      searchForm: {
        page: 1,
        limit: 10,
        status: 1,
        send_type: 3,
        name: ''
      },
      total: 0,
      loading: false,
      tableData: [],
      multipleSelection: [],
      showDialog: { coupon: false }
    }
  },
  async created () {
    let { id = 0 } = this.$route.query
    this.navTitle = this.$t(id ? 'menu.CustomMemberdiscountCardEdit' : 'menu.CustomMemberdiscountCardAdd')
    this.subForm.id = id
    if (id) {
      await this.getDetail()
    }
  },
  methods: {
    async getDetail () {
      let { id } = this.subForm
      let { code, data } = await this.$api.memberdiscount.cardInfo({ id })
      if (code !== 200) return
      data.coupon = data.coupon ? data.coupon.map(item => {
        item.id = item.coupon_id
        return item
      }) : []
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    async toShowDialog (key) {
      this.searchForm.page = 1
      this.searchForm.name = ''
      await this.getTableDataList()
      this.showDialog[key] = !this.showDialog[key]
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
      if (flag) this.searchForm.page = flag
      this.loading = true
      let { code, data } = await this.$api.market.couponList(this.searchForm)
      this.loading = false
      if (code !== 200) return
      let { coupon } = this.subForm
      let arr = coupon.map(item => {
        return item.id
      })
      data.data.map(item => {
        item.num = 1
        if (arr.includes(item.id)) {
          let ind = coupon.findIndex(aitem => {
            return item.id === aitem.id
          })
          item.num = coupon[ind].num
        }
      })
      this.tableData = data.data
      this.total = data.total
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    },
    handleDialogConfirm () {
      if (this.multipleSelection.length === 0) {
        this.$message.error(`请选择卡券`)
        return
      }
      let coupon = JSON.parse(JSON.stringify(this.subForm.coupon))
      let arr1 = coupon.length > 0 ? coupon.map(item => { return item.id }) : []
      this.multipleSelection.map(item => {
        if (arr1.includes(item.id)) {
          let ind = coupon.findIndex(aitem => {
            return aitem.id === item.id
          })
          coupon[ind].num = item.num
        } else {
          coupon.push(item)
        }
      })
      this.subForm.coupon = coupon
      this.showDialog.coupon = false
    },
    toDelItem (index) {
      this.subForm.coupon.splice(index, 1)
    },
    async submitFormInfo () {
      let validate = true
      this.$refs['subForm'].validate(valid => {
        if (!valid) validate = false
      })
      if (!validate) return
      let subForm = JSON.parse(JSON.stringify(this.subForm))
      if (subForm.price * 1 >= subForm.init_price * 1) {
        this.$message.error(`套餐划线价必须大于套餐现价！`)
        return
      }
      subForm.coupon = subForm.coupon.length > 0 ? subForm.coupon.map((item) => {
        return { coupon_id: item.id, num: item.num || 0 }
      }) : []
      let methodModel = subForm.id ? 'cardUpdate' : 'cardAdd'
      let { code } = await this.$api.memberdiscount[methodModel](subForm)
      if (code !== 200) return
      this.$message.success(this.$t('tips.successSub'))
      this.$router.back(-1)
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-custom-member-card-edit {
  width: 100%;

  .el-input,
  .el-select,
  .lb-input-number,
  .el-cascader {
    width: 300px;
  }
  .el-textarea {
    width: 600px;
  }
  .el-input.mini {
    width: 120px;
  }

  .dialog-form {
    .el-input {
      width: 300px;
    }
  }
}
</style>
