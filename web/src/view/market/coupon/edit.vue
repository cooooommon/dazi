<!--
 * @Descripttion: 编辑卡券
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:07
 * @LastEditors: wen kun
 * @LastEditTime: 2023-07-03 17:50:40
-->
<template>
  <div class="lb-market-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="120px"
      >
        <el-form-item label="卡券名称" prop="title">
          <el-input
            v-model="subForm.title"
            maxlength="20"
            show-word-limit
            placeholder="请输入卡券名称"
          ></el-input>
        </el-form-item>
        <el-form-item label="使用条件" prop="type">
          <el-radio-group
            v-model="subForm.type"
            @change="redMarketChange($event, 'type')"
          >
            <el-radio :label="0">消费满</el-radio>
            <el-radio :label="1">无门槛</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item prop="full">
          <div v-if="subForm.type === 0">
            消费满
            <el-input
              v-model="subForm.full"
              placeholder="请输入消费金额"
              style="width: 130px"
            ></el-input>
          </div>
          {{ subForm.type === 0 ? '元可用' : '立减' }}
          <el-input
            v-model="subForm.discount"
            placeholder="请输入优惠金额"
            style="width: 130px"
          ></el-input>
          <div v-if="subForm.type === 1">元</div>
        </el-form-item>
        <el-form-item label="使用规则" prop="rule">
          <el-input
            type="textarea"
            :rows="10"
            maxlength="1000"
            resize="none"
            show-word-limit
            placeholder="请输入使用规则"
            v-model="subForm.rule"
          ></el-input>
        </el-form-item>
        <el-form-item label="优惠详情" prop="text">
          <el-input
            type="textarea"
            :rows="10"
            maxlength="1000"
            resize="none"
            show-word-limit
            placeholder="请输入优惠详情"
            v-model="subForm.text"
          ></el-input>
        </el-form-item>

        <el-form-item label="派发方式" prop="send_type">
          <el-radio-group
            v-model="subForm.send_type"
            :disabled="subForm.id ? true : false"
            @change="redMarketChange($event, 'send_type')"
          >
            <!-- <el-radio :label="0">活动派发</el-radio> -->
            <el-radio :label="2">用户领取</el-radio>
            <el-radio :label="1">平台定向派发</el-radio>
          </el-radio-group>
          <lb-tool-tips>
            <!-- <div>
              活动派发：需要参与活动，活动参与成功之后系统自动派发到用户的卡包
            </div> -->
            <div class="mt-sm">平台定向派发：平台指定派发到用户的卡包</div>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item
          label="领取身份"
          prop="user_limit"
          v-if="subForm.send_type == 2"
        >
          <el-radio-group v-model="subForm.user_limit">
            <el-radio :label="1">不限制，任何人可领取</el-radio>
            <el-radio :label="2">仅限新用户</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item
          label="卡券库存"
          prop="stock"
          v-if="subForm.send_type == 2"
        >
          <el-input-number
            class="lb-input-number"
            :min="0"
            :controls="false"
            v-model="subForm.stock"
            placeholder="请输入卡券数量"
          ></el-input-number>
        </el-form-item>
        <el-form-item label="使用时间" prop="time_limit">
          <el-radio-group v-model="subForm.time_limit">
            <el-radio :label="0">指定日期</el-radio>
            <el-radio :label="1">有效天数</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label prop="start_time" v-if="subForm.time_limit === 0">
          <el-date-picker
            v-model="subForm.start_time"
            type="datetime"
            value-format="timestamp"
            placeholder="选择开始日期时间"
          ></el-date-picker>
        </el-form-item>
        <el-form-item label prop="end_time" v-if="subForm.time_limit === 0">
          <el-date-picker
            v-model="subForm.end_time"
            type="datetime"
            value-format="timestamp"
            placeholder="选择结束日期时间"
          ></el-date-picker>
          <lb-tool-tips>领取的卡券必须在该时间前使用</lb-tool-tips>
        </el-form-item>
        <el-form-item label prop="day" v-if="subForm.time_limit === 1">
          <em class="text-normal">自领券当日起</em>
          <el-input-number
            v-model="subForm.day"
            class="lb-input-number mini"
            :controls="false"
            :min="1"
            :precision="0"
          ></el-input-number>
          <em class="text-normal">天内可用</em>
          <lb-tool-tips
            >有效期按自然天计算。
            <div class="mt-sm mb-sm">
              举例：如设置领券当日起30天内可用，用户在5月18日14:00时领取卡券，则该卡券的可用时间为5月18日的14:00:00至6月18日的14:00
            </div>
            注意：时间按自然天来算，不是月
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="排序值" prop="top">
          <el-input-number
            class="lb-input-number"
            :min="0"
            :controls="false"
            v-model="subForm.top"
            placeholder="请输入排序值"
          ></el-input-number>
          <lb-tool-tips>值越大, 排序越靠前</lb-tool-tips>
        </el-form-item>
        <el-form-item label="限用服务" prop="service">
          <lb-button type="primary" icon="el-icon-plus" @click="toShowDialog"
            >选择服务</lb-button
          >
          <el-table
            :data="subForm.service"
            :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
            class="mt-lg"
            style="width: 100%"
          >
            <el-table-column prop="id" label="ID"></el-table-column>
            <el-table-column prop="title" label="服务名称"></el-table-column>
            <el-table-column prop="price" label="服务价格"></el-table-column>
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
          <lb-button @click="$router.back(-1)">{{
            $t('action.back')
          }}</lb-button>
        </el-form-item>
      </el-form>

      <el-dialog
        title="限用服务"
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
              placeholder="请输入服务名称"
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
          <el-table-column prop="title" label="服务名称"></el-table-column>
          <el-table-column prop="price" label="服务价格"></el-table-column>
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
    let validateStartTime = (rule, value, callback) => {
      if (this.subForm.time_limit === 0) {
        if (value.length === 0) {
          callback(new Error('请选择开始时间'));
        } else {
          callback();
        }
      } else {
        callback();
      }
    };
    let validateEndTime = (rule, value, callback) => {
      if (this.subForm.time_limit === 0) {
        if (value.length === 0) {
          callback(new Error('请选择到期时间'));
        } else {
          callback();
        }
      } else {
        callback();
      }
    };
    let validateFull = (rule, value, callback) => {
      let regRule =
        /^(([0-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/;
      if (this.subForm.type === 0) {
        if (!regRule.test(value)) {
          callback(new Error('请输入消费金额'));
        } else {
          if (!regRule.test(this.subForm.discount)) {
            callback(new Error('请输入优惠金额'));
          } else if (value * 1 < this.subForm.discount * 1) {
            callback(new Error('优惠金额不能大于消费金额'));
          } else {
            callback();
          }
        }
      } else if (this.subForm.type === 1) {
        if (!regRule.test(this.subForm.discount)) {
          callback(new Error('请输入优惠金额'));
        } else {
          callback();
        }
      }
    };
    return {
      navTitle: '',
      subForm: {
        id: 0,
        title: '',
        stock: '',
        type: 0,
        full: '',
        discount: '',
        rule: '',
        text: '',
        time_limit: 0,
        user_limit: 1,
        day: 1,
        end_time: '',
        start_time: '',
        send_type: 2,
        status: 1,
        service: [],
        top: 0,
      },
      send_type2Rules: {
        stock: {
          required: true,
          type: 'number',
          message: '请输入卡券库存',
          trigger: 'blur',
        },
        user_limit: {
          required: true,
          message: '请选择领取身份',
          trigger: 'blur',
        },
      },
      send_typeRules: {
        title: {
          required: true,
          message: '请输入卡券名称',
          trigger: 'blur',
        },

        type: {
          required: true,
          message: '请选择使用条件',
          trigger: 'blur',
        },
        full: { validator: validateFull, trigger: 'change' },
        rule: {
          required: true,
          type: 'string',
          message: '请输入使用规则',
          trigger: 'blur',
        },
        text: {
          required: true,
          type: 'string',
          message: '请输入优惠详情',
          trigger: 'blur',
        },
        send_type: {
          required: true,
          message: '请选择派发方式',
          trigger: 'blur',
        },
        time_limit: {
          required: true,
          message: '请选择使用时间',
          trigger: 'blur',
        },

        start_time: { validator: validateStartTime, trigger: 'change' },
        end_time: { validator: validateEndTime, trigger: 'change' },
        top: {
          required: true,
          type: 'number',
          message: '请输入排序值',
          trigger: 'blur',
        },
        service: {
          required: true,
          type: 'array',
          message: '请选择限用服务',
          trigger: 'blur',
        },
      },
      subFormRules: {},
      searchForm: {
        page: 1,
        limit: 10,
        status: 1,
        name: '',
      },
      total: 0,
      loading: false,
      tableData: [],
      multipleSelection: [],
      showDialog: false,
    };
  },
  watch: {
    'subForm.send_type': {
      handler (val, oldVal) {
        this.subFormRules = this.send_typeRules;
        if (val == 2) {
          this.subFormRules = {
            ...this.send_typeRules,
            ...this.send_type2Rules,
          };
        }
      },
      // 深度观察监听
      deep: true,
    },
  },
  created () {
    // this.subFormRules = this.send_typeRules;
    this.subFormRules = {
      ...this.send_typeRules,
      ...this.send_type2Rules,
    }
    let { id = 0 } = this.$route.query;
    this.subForm.id = id;
    this.navTitle = this.$t(
      id ? 'menu.MarketCouponEdit' : 'menu.MarketCouponAdd'
    );
    if (!id) return;
    this.getDetail();
  },
  methods: {
    /**
     * @name: 详情
     * @param {*}
     */
    async getDetail () {
      let { id } = this.subForm;
      let { code, data } = await this.$api.market.couponInfo({ id });
      if (code !== 200) return;
      data.start_time = data.start_time ? data.start_time * 1000 : '';
      data.end_time = data.end_time ? data.end_time * 1000 : '';
      for (let key in this.subForm) {
        this.subForm[key] = data[key];
      }
    },
    async toShowDialog () {
      await this.getTableDataList();
      this.showDialog = !this.showDialog;
    },
    resetForm (form) {
      this.$refs[form].resetFields();
      this.getTableDataList(1);
    },
    handleSizeChange (val) {
      this.searchForm.limit = val;
      this.handleCurrentChange(1);
    },
    handleCurrentChange (val) {
      this.searchForm.page = val;
      this.getTableDataList();
    },
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1;
      this.tableData = []
      this.loading = true;
      let { code, data } = await this.$api.service.serviceList(
        this.searchForm
      );
      this.loading = false;
      if (code !== 200) return;
      this.tableData = data.data;
      this.total = data.total;
    },
    handleSelectionChange (val) {
      this.multipleSelection = val;
    },
    handleDialogConfirm () {
      let service = JSON.parse(JSON.stringify(this.subForm.service));
      let arr1 =
        service.length > 0
          ? service.map((item) => {
            return item.id;
          })
          : [];
      this.multipleSelection.map((item) => {
        if (arr1.includes(item.id)) return;
        service.push(item);
      });
      this.subForm.service = service;
      this.showDialog = false;
    },
    confirmDel (id) {
      let index = this.subForm.service.findIndex((item) => {
        return item.id === id;
      });
      this.subForm.service.splice(index, 1);
    },
    /**
     * @name: 新增/编辑
     * @param {*}
     */
    submitForm () {
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          let methodModel = subForm.id ? 'couponUpdate' : 'couponAdd'

          subForm.start_time = subForm.time_limit === 0 ? subForm.start_time / 1000 : ''
          subForm.end_time = subForm.time_limit === 0 ? subForm.end_time / 1000 : ''
          subForm.day = subForm.time_limit === 0 ? '' : subForm.day

          let arr = subForm.service.map((item) => {
            return item.id
          })
          subForm.service = arr

          this.$api.market[methodModel](subForm).then((res) => {
            if (res.code === 200) {
              this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
              this.$router.back(-1)
            }
          })
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-market-edit {
  .page-main {
    .el-form-item {
      margin-bottom: 24px;
    }
    .last-form-item {
      margin-top: 30px;
    }
    .item-tips {
      margin-left: 120px;
      margin-bottom: 24px;
      color: #999999;
    }
    .el-input,
    .el-input-number,
    .el-cascader,
    .el-select {
      width: 300px;
    }
    .el-input-number.mini {
      width: 100px;
    }
    .el-textarea {
      width: 600px;
    }
  }
}
</style>
