<!--
 * @Description: 代理商管理
 * @Author: xiao li
 * @Date: 2022-10-27 13:20:33
 * @LastEditTime: 2024-01-03 10:36:11
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-shop-order">
    <top-nav />
    <div class="page-main">
      <el-row class="page-top-operate">
        <lb-button
          size="medium"
          type="primary"
          icon="el-icon-plus"
          @click="toShowDialog('sub')"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.AgentAccountAdd') }}</lb-button
        >
      </el-row>
      <el-row class="page-search-form">
        <el-form
          @submit.native.prevent
          :inline="true"
          :model="searchForm.list"
          ref="listForm"
        >
          <el-form-item label="账号名称" prop="username">
            <el-input
              v-model="searchForm.list.username"
              placeholder="请输入账号名称"
            ></el-input>
          </el-form-item>
          <el-form-item label="关联用户" prop="nickName">
            <el-input
              v-model="searchForm.list.nickName"
              placeholder="请输入关联用户"
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
        <el-table-column prop="id" label="ID" fixed></el-table-column>
        <el-table-column
          prop="username"
          label="账号名称"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="passwd_text"
          label="账号密码"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="agent_name"
          label="真实姓名"
          min-width="120"
        ></el-table-column>
        <el-table-column prop="city_type" label="代理等级" min-width="120">
          <template slot-scope="scope">
            <el-tag :type="cityType[scope.row.city_type].type">{{
              cityType[scope.row.city_type].text
            }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="city" label="省/城市/区县" min-width="120">
          <template slot-scope="scope">
            <div>
              {{
                scope.row.city_type === 3
                  ? scope.row.province
                  : scope.row.city_type === 1
                  ? scope.row.city_id
                    ? `${scope.row.province} ${scope.row.city}`
                    : '-'
                  : scope.row.city_id
                  ? scope.row.province
                    ? `${scope.row.province} ${scope.row.city} ${scope.row.area}`
                    : `${scope.row.city} ${scope.row.area}`
                  : '-'
              }}
            </div>
          </template>
        </el-table-column>
        <el-table-column
          prop="user_id"
          label="关联用户ID"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="nickName"
          label="关联用户昵称"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="phone"
          label="服务电话"
          min-width="120"
        ></el-table-column>
        <el-table-column
          prop="balance"
          :label="cash_type == 2 ? `代理商抽成比例` : `平台抽成比例`"
          min-width="120"
        >
          <template slot-scope="scope">
            <div>{{ scope.row.balance }}%</div>
          </template>
        </el-table-column>
        <!-- <el-table-column
          prop="admin_pid"
          label="上级代理账号名称"
          min-width="150"
        >
          <template slot-scope="scope">
            <div>{{ scope.row.admin_pid ? scope.row.admin_ptitle : '-' }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="balance" label="上级代理抽成" min-width="120">
          <template slot-scope="scope">
            <div>
              {{ scope.row.admin_pid ? `${scope.row.level_balance}%` : '-' }}
            </div>
          </template>
        </el-table-column> -->
        <el-table-column prop="create_time" label="创建时间" min-width="120">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="160" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                type="primary"
                plain
                @click="toShowDialog('sub', scope.row)"
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
        :page="searchForm.list.page"
        :pageSize="searchForm.list.limit"
        :total="total.list"
        @handleSizeChange="handleSizeChange($event, 'list')"
        @handleCurrentChange="handleCurrentChange($event, 'list')"
      >
      </lb-page>
    </div>
    <!-- 新增/编辑账号 -->
    <el-dialog
      :title="$t(subForm.id ? 'menu.AgentAccountEdit' : 'menu.AgentAccountAdd')"
      :visible.sync="showDialog.sub"
      width="770px"
      center
      :append-to-body="true"
    >
      <lb-tips>
        <p class="c-link">
          {{ $t('action.attendantName') }}浮动分佣比例模式：{{
            $t('action.attendantName')
          }}提成 > 分销商 > {{ $t('action.attendantName') }}经纪人 > 平台提成 >
          渠道商 > 城市代理
        </p>
        <p class="c-link pt-sm">
          {{ $t('action.attendantName') }}固定分佣比例模式：{{
            $t('action.attendantName')
          }}提成 > 城市代理 > 分销商提成 >
          {{ $t('action.attendantName') }}经纪人 > 渠道商 > 平台提成
        </p>
        <p
          class="c-warning mt-sm"
          style="margin-left: 125px"
          @click="toShowDialog('rule')"
        >
          点击查看具体规则
        </p>
      </lb-tips>
      <el-form
        class="dialog-form"
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
      >
        <el-form-item label="账号" prop="username">
          <el-input
            v-model="subForm.username"
            placeholder="请输入账号"
            maxlength="20"
            show-word-limit
          ></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="passwd_text">
          <el-input
            v-model="subForm.passwd_text"
            placeholder="请输入密码"
            maxlength="20"
            show-word-limit
          ></el-input>
        </el-form-item>
        <el-form-item label="真实姓名" prop="agent_name">
          <el-input
            v-model="subForm.agent_name"
            placeholder="请输入真实姓名"
            maxlength="20"
            show-word-limit
          ></el-input>
        </el-form-item>
        <el-form-item label="代理等级" prop="city_type">
          <el-radio-group
            @change="getCityList($event)"
            v-model="subForm.city_type"
          >
            <!-- <el-radio :label="3">省代理</el-radio> -->
            <el-radio :label="1">城市代理</el-radio>
            <!-- <el-radio :label="2">区县代理</el-radio> -->
          </el-radio-group>
        </el-form-item>
        <el-form-item
          :label="`选择${cityType[subForm.city_type].text}`"
          prop="city_data"
        >
          <el-cascader
            size="large"
            :options="base_city"
            v-model="subForm.city_data"
            @change="handleChange"
            :placeholder="`请选择${cityType[subForm.city_type].text}`"
            :props="{ checkStrictly: true, label: 'title', value: 'id' }"
          ></el-cascader>
        </el-form-item>
        <el-form-item label="关联用户" prop="user_id">
          <el-tag class="cursor-pointer" @click="toShowDialog('user')">{{
            subForm.user_id
              ? subForm.nickName || `用户ID ${subForm.user_id}`
              : '选择关联用户'
          }}</el-tag>
        </el-form-item>
        <el-form-item label="服务电话" prop="phone">
          <el-input
            v-model="subForm.phone"
            placeholder="请输入服务电话"
          ></el-input>
          <lb-tool-tips
            >代理商服务电话用于客户咨询，请填写有效的联系电话</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="平台抽成比例" prop="balance" v-if="cash_type == 1">
          <el-input-number
            class="lb-input-number"
            :min="0"
            :max="100"
            :precision="0"
            :controls="false"
            v-model="subForm.balance"
            placeholder="请输入平台抽成比例"
          ></el-input-number>
          <div>%</div>
          <lb-tool-tips>
            <p class="mb-sm">平台抽成取值0%到100%</p>
            <p class="mb-sm">
              设置为0%，表示平台不抽成，除去{{
                $t('action.attendantName')
              }}和分销商的提成后，剩下都归代理商
            </p>
            <p>
              设置100%，则表示代理商无提成，除去{{
                $t('action.attendantName')
              }}和代理商的提成后，剩下都归平台
            </p>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="代理商抽成比例" prop="balance" v-else>
          <el-input-number
            class="lb-input-number"
            :min="0"
            :max="100"
            :precision="0"
            :controls="false"
            v-model="subForm.balance"
            placeholder="请输入代理商抽成比例"
          ></el-input-number>
          <div>%</div>
          <lb-tool-tips>
            <p>代理商分佣=订单实际支付金额*代理商抽成比例%</p>
          </lb-tool-tips>
        </el-form-item>
        <!-- <el-form-item
          label="关联上级代理"
          prop=""
          v-if="subForm.city_type !== 3 && subForm.city_data.length > 0"
        >
          <el-tag class="cursor-pointer" @click="toShowDialog('pid')">{{
            subForm.admin_pid ? subForm.admin_ptitle : '选择关联上级代理'
          }}</el-tag>
        </el-form-item>
        <el-form-item
          label="上级代理抽成"
          prop="level_balance"
          v-if="subForm.city_type !== 3 && subForm.admin_pid"
        >
          <el-input-number
            class="lb-input-number"
            :min="0"
            :max="100"
            :precision="0"
            :controls="false"
            v-model="subForm.level_balance"
            placeholder="请输入上级代理抽成"
          ></el-input-number>
          <div>%</div>

          <lb-tool-tips
            >{{
              cityType[subForm.city_type].text
            }}代理需要分给上级代理的比例。例如{{
              cityType[subForm.city_type].text
            }}代理可分得40%
            <div class="mt-sm">
              如果设置了上级抽成10%，则{{
                cityType[subForm.city_type].text
              }}代理只分得30%
            </div>
          </lb-tool-tips>
        </el-form-item> -->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="showDialog.sub = false">{{
          $t('action.cancel')
        }}</el-button>
        <el-button
          type="primary"
          @click="submitForm('sub', 1)"
          v-preventReClick
          >{{ $t('action.comfirm') }}</el-button
        >
      </div>
    </el-dialog>
    <!-- 关联用户 -->
    <el-dialog
      title="关联用户"
      :visible.sync="showDialog.user"
      width="800px"
      center
    >
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
        v-loading="loading.user"
        :data="tableData.user"
        ref="singleTable"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
        highlight-current-row
        @current-change="handleTableChange($event, 'user')"
      >
        <el-table-column prop="id" label="用户ID"></el-table-column>
        <el-table-column prop="avatarUrl" label="头像">
          <template slot-scope="scope">
            <lb-image :src="scope.row.avatarUrl" />
          </template>
        </el-table-column>
        <el-table-column prop="nickName" label="昵称"></el-table-column>
        <el-table-column prop="phone" label="手机号"></el-table-column>
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
        <el-button @click="showDialog.user = false">取 消</el-button>
        <el-button
          type="primary"
          @click="submitForm('user', 2)"
          v-preventReClick
          >确 定</el-button
        >
      </span>
    </el-dialog>
    <!-- 关联上级代理 -->
    <el-dialog
      title="关联上级代理"
      :visible.sync="showDialog.pid"
      width="800px"
      center
    >
      <el-form
        :inline="true"
        :model="searchForm.pid"
        ref="pidForm"
        label-width="70px"
      >
        <el-form-item label="输入查询" prop="username">
          <el-input
            v-model="searchForm.pid.username"
            placeholder="请输入账号名称"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <lb-button
            size="medium"
            type="primary"
            icon="el-icon-search"
            style="margin-right: 5px"
            @click="getTableDataList(1, 'pid')"
            >{{ $t('action.search') }}</lb-button
          >
          <lb-button
            size="medium"
            icon="el-icon-refresh-left"
            style="margin-right: 5px"
            @click="resetForm('pid')"
            >{{ $t('action.reset') }}</lb-button
          >
        </el-form-item>
      </el-form>
      <el-table
        v-loading="loading.pid"
        :data="tableData.pid"
        ref="singleTable"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
        highlight-current-row
        @current-change="handleTableChange($event, 'pid')"
      >
        <el-table-column prop="user_id" label="用户ID"></el-table-column>
        <el-table-column prop="nickName" label="用户昵称"></el-table-column>
        <el-table-column prop="username" label="账号名称"></el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.pid.page"
        :pageSize="searchForm.pid.limit"
        :total="total.pid"
        @handleSizeChange="handleSizeChange($event, 'pid')"
        @handleCurrentChange="handleCurrentChange($event, 'pid')"
      >
      </lb-page>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog.pid = false">取 消</el-button>
        <el-button type="primary" @click="submitForm('pid', 2)" v-preventReClick
          >确 定</el-button
        >
      </span>
    </el-dialog>
    <!-- 提成方式计算规则 -->
    <el-dialog
      title="提成方式计算规则"
      :visible.sync="showDialog.rule"
      width="800px"
      center
    >
      <div class="f-title text-bold c-black pb-md">
        {{ $t('action.attendantName') }}浮动分佣比例模式：
      </div>
      <p class="mb-sm">例如：订单实付金额为100元（不含车费）</p>
      <p class="mb-sm">
        1、{{ $t('action.attendantName') }}提成比例75%，分销商提成比例为1%，{{
          $t('action.attendantName')
        }}经纪人提成比例为4%，平台提成比例为10%，渠道商提成比例为5%
      </p>
      <p class="mb-md c-warning" style="margin-left: 20px">
        当 {{ $t('action.attendantName') }}提成比例 + 分销商提成比例 +
        平台提成比例+渠道商提成比例 >= 100% 时，城市代理商无提成
      </p>
      <p class="mb-sm">
        2、{{ $t('action.attendantName') }}提成比例75%，分销商提成比例为1%，{{
          $t('action.attendantName')
        }}经纪人提成比例4%，渠道商提成比例5%，平台提成比例为10%
      </p>
      <p class="mb-sm c-link" style="margin-left: 20px">
        城市代理商提成金额 = 100 * ( 100 - 75 -1- 5 - 10)%
      </p>
      <p class="mb-sm c-link" style="margin-left: 151px">= 100 * 9%</p>
      <p class="mb-sm c-link" style="margin-left: 151px">= 9元</p>
      <p class="mb-sm c-link" style="margin-left: 20px">
        如果经纪人提成里代理商承担了50%，则代理商最终获得9-2=7元。
      </p>
      <div class="f-title text-bold c-black pb-md pt-lg">
        {{ $t('action.attendantName') }}固定分佣比例模式：
      </div>
      <div>
        <p class="mb-sm">
          1、系统分佣可对每个{{
            $t('action.attendantName')
          }}设置不同的分佣比例（不走系统的浮动比例），可以对不同代理商设置不同分佣比例，分销商角色统一固定比例，分佣设置如下：
          以1000元订单为例
        </p>
        <p class="mb-sm pl-md">
          手动设置{{
            $t('action.attendantName')
          }}比例80%，分销商比例1%，代理商比例10%，渠道商提成比例4%
        </p>
        <p class="mb-sm pl-md">
          {{ $t('action.attendantName') }}佣金=订单实际支付金额*{{
            $t('action.attendantName')
          }}比例=1000*80%=800
        </p>
        <p class="mb-sm pl-md">
          分销商佣金=订单实际支付金额*分销商比例=1000*1%=10
        </p>
        <p class="mb-sm pl-md">
          代理商佣金=订单实际支付金额*代理商比例=1000*10%=100
        </p>
        <p class="mb-sm pl-md">
          渠道商佣金=订单实际支付金额*渠道商比例=1000*4%=40
        </p>
        <p class="mb-sm pl-md">
          平台佣金=订单实际支付金额-{{
            $t('action.attendantName')
          }}佣金-分销商佣金-代理商佣金-渠道商佣金
        </p>
        <p class="mb-sm pl-md">
          佣金返佣先后顺序为{{
            $t('action.attendantName')
          }}》代理商》分销商》渠道商》平台
        </p>
        <p class="mb-sm pl-md">
          若设置5个角色比例超过100%，按照上诉分佣优先级返佣，超出比例佣金后续人员不得分佣
        </p>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  data () {
    return {
      cityType: {
        3: {
          type: 'success',
          text: '省'
        },
        1: {
          type: 'primary',
          text: '城市'
        },
        2: {
          type: 'danger',
          text: '区县'
        }
      },
      loading: { list: false, user: false, pid: false },
      searchForm: {
        list: {
          page: 1,
          limit: 10,
          username: '',
          nickName: ''
        },
        user: {
          page: 1,
          limit: 10,
          nickName: ''
        },
        pid: {
          page: 1,
          limit: 10,
          nickName: '',
          city_id: ''
        }
      },
      tableData: {
        list: [],
        user: [],
        pid: []
      },
      total: {
        list: 0,
        user: 0,
        pid: 0
      },
      currentRow: {},
      base_city: [],
      showDialog: { sub: false, user: false, pid: false, rule: false },
      subForm: {
        id: 0,
        username: '',
        passwd_text: '',
        agent_name: '',
        phone: '',
        city_type: 1,
        city_id: '',
        city_data: [],
        balance: '',
        admin_pid: '',
        admin_ptitle: '',
        level_balance: '',
        user_id: '',
        nickName: ''
      },
      subFormRules: {
        username: { required: true, validator: this.$reg.isNotNull, reg_type: 2, text: '账号', trigger: 'blur' },
        passwd_text: { required: true, type: 'string', message: '请输入密码', trigger: 'blur' },
        agent_name: { required: true, validator: this.$reg.isNotNull, reg_type: 2, text: '真实姓名', trigger: 'blur' },
        phone: { required: true, validator: this.$reg.isAllPhone, text: '服务电话', trigger: 'blur' },
        city_type: { required: true, type: 'number', message: '请选择代理等级', trigger: 'blur' },
        balance: { required: true, type: 'number', message: '请输入平台抽成', trigger: 'blur' },
        user_id: { required: true, type: 'number', message: '请选择关联用户', trigger: 'blur' }
      },
      cash_type: ''
    }
  },
  created () {
    this.getTableDataList(1, 'list')
    this.getConfigInfo()
  },
  methods: {
    // 城市级联选择变更（选中值已由 v-model 收集，此占位避免模板引用未定义方法）
    handleChange () {},
    async getConfigInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.cash_type = data.cash_type
      if (data.cash_type == 2) {
        this.subFormRules.balance.message = '请输入代理商抽成比例'
      }
    },
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
     * @method 获取列表
     */
    getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.loading[key] = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))
      if (key === 'pid') {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        let { id = 0, city_type: ctype, city_data: cdata } = subForm
        searchForm.id = id
        if (ctype !== 1) {
          cdata.splice(cdata.length - 1, 1)
        }
        searchForm.city_id = ctype === 1 ? [cdata[0]] : cdata
      }
      let methodArr = {
        list: 'franchiseeList',
        user: 'userList',
        pid: 'franchiseeList'
      }
      let methodModel = methodArr[key]
      this.$api.agent[methodModel](searchForm).then((res) => {
        this.loading[key] = false
        if (res.code === 200) {
          let { data, total } = res.data
          this.tableData[key] = data
          this.total[key] = total
        }
      })
    },
    async toShowDialog (key, item = {}) {
      item = JSON.parse(JSON.stringify(item))
      if (key === 'user' || key === 'pid') {
        if (key === 'user') {
          this.searchForm.user.nickName = ''
        }
        if (key === 'pid') {
          let { city_type: ctype = 1, city_data: cdata } = this.subForm
          if ((ctype === 1 && cdata.length < 2) || (ctype === 2 && cdata.length < 3)) {
            this.$message.error(`请选择${this.cityType[ctype].text}`)
            return
          }
        }
        await this.getTableDataList(1, key)
      }
      if (key === 'sub') {
        if (!item.id) {
          item = { city_type: 1, city_data: [] }
        }
        for (let keys in this[`${key}Form`]) {
          this[`${key}Form`][keys] = item[keys]
        }
        await this.getCityList()
      }
      this.showDialog[key] = true
    },
    async getCityList (type = 0) {
      let { city_type: ctype = 3, city_data: cdata } = this.subForm
      if (type) {
        if (type === 1 && cdata.length > 2) {
          cdata.splice(2, 1)
        }
        if (type === 3 && cdata.length > 0) {
          cdata = [cdata[0]]
        }
        this.subForm.city_data = cdata
      }
      let { code, data } = await this.$api.system.citySelect({ city_type: 3 })
      if (code !== 200) return
      data.map(item => {
        if (ctype === 3) {
          delete item.children
        }
        if (item.children) {
          item.children.map(aitem => {
            if (ctype === 1 || aitem.children.length === 0) {
              delete aitem.children
            }
          })
        }
      })
      this.base_city = data
    },
    handleTableChange (val, type) {
      val = JSON.parse(JSON.stringify(val))
      let { id, nickName, username = '' } = val
      if (type === 'pid') {
        val.admin_ptitle = username
      } else {
        val.nickName = nickName || `用户ID ${id}`
      }
      this.currentRow = val
    },
    async submitForm (key, validate = 1) {
      let flag = true
      if (validate === 1) {
        this.$refs[`${key}Form`].validate(valid => {
          if (!valid) flag = false
        })
      }
      if (flag) {
        if (key !== 'sub') {
          if (this.currentRow === null || !this.currentRow.id) {
            this.$message.error(key === 'user' ? `请选择用户` : `请选择上级代理`)
            return
          }
          let { id = 0, nickName = '', username = '' } = this.currentRow
          if (key === 'user') {
            this.subForm.user_id = id
            this.subForm.nickName = nickName
          } else {
            this.subForm.admin_pid = id
            this.subForm.admin_ptitle = username
          }
          this.showDialog[key] = false
          return
        }
        let { cityType } = this
        let subForm = JSON.parse(JSON.stringify(this[`${key}Form`]))
        if (subForm.username.length < 2 || subForm.passwd_text.length < 6) {
          this.$message.error(subForm.username.length < 2 ? `账号不能少于2位数` : `密码不能少于6位数`)
          return
        }
        let { city_type: ctype, city_data: cdata } = subForm
        if ((ctype === 1 && cdata.length < 2) || (ctype === 2 && cdata.length < 3) || (ctype === 3 && cdata.length === 0)) {
          this.$message.error(`请选择${cityType[ctype].text}`)
          return
        }
        subForm.city_id = cdata[cdata.length - 1]
        let balance = 100 - subForm.balance * 1
        if (subForm.admin_pid && subForm.level_balance * 1 >= balance) {
          this.$message.error(`上级代理抽成不能大于或等于当前代理提成 (当前代理提成${balance}%)`)
          return
        }
        delete subForm.city_data
        delete subForm.admin_ptitle
        delete subForm.nickName
        let methodModel = subForm.id ? 'adminUpdate' : 'adminAdd'
        let { code } = await this.$api.agent[methodModel](subForm)
        if (code !== 200) return
        this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
        this.showDialog[key] = false
        this.getTableDataList('', 'list')
      }
    },
    confirmDel (id) {
      this.$confirm(this.$t('tips.franchiseeDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.$api.agent.adminStatusUpdate({ id, status: '-1' }).then((res) => {
          this.$message.success(this.$t('tips.successDel'))
          this.searchForm.list.page = this.searchForm.list.page < Math.ceil((this.total.list - 1) / this.searchForm.list.limit) ? this.searchForm.list.page : Math.ceil((this.total.list - 1) / this.searchForm.list.limit)
          this.getTableDataList('', 'list')
        })
      })
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

<style lang="scss">
.dialog-form {
  .el-input,
  .el-input-number {
    width: 300px;
  }
}
</style>
