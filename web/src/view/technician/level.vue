<!--
 * @Description: 向导等级
 * @Author: xiao li
 * @Date: 2021-07-04 13:17:22
 * @LastEditTime: 2024-05-07 09:34:49
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-appclass-classroom-list" v-if="config.id">
    <top-nav />
    <div
      class="text-center"
      style="padding-top: 120px"
      v-if="userInfo.is_admin == 2 && config.cash_type == 2"
    >
      <div class="f-title text-bold pb-md">暂无权限~</div>
      <!-- <div>请在管理后台查看</div> -->
    </div>
    <div class="page-main" v-else>
      <lb-tips
        >服务累计时长：输入分钟数后，系统会自动计算区间；例如：
        <div class="mt-md">
          第一次新增的{{
            $t('action.attendantName')
          }}等级输入的分钟数为100，则显示的服务累计时长为：0至100分钟
        </div>
        <div class="mt-sm">
          第二次新增的{{
            $t('action.attendantName')
          }}等级输入的分钟数为200，则显示的服务累计时长为：100至200分钟
        </div>

        <div class="mt-lg">
          {{
            $t('action.attendantName')
          }}在本周期（T周期）折算之后，等级提成将会在T+1个周期生效，即{{
            $t('action.attendantName')
          }}这个月的维度考核达标后，可升等级，第二个周期按照新升级的等级计算
        </div>
      </lb-tips>
      <el-row class="page-top-operate">
        <lb-button
          size="medium"
          type="primary"
          icon="el-icon-plus"
          @click="toShowDialog('sub')"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.TechnicianLevelAdd') }}</lb-button
        >
        <lb-button
          size="medium"
          type="danger"
          icon="el-icon-setting"
          @click="toShowDialog('setting')"
          v-hasPermi="`${$route.name}-setCycle`"
          >{{ $t('action.setCycle') }}</lb-button
        >
      </el-row>
      <el-table
        v-loading="loading"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        tooltip-effect="dark"
        style="width: 100%"
      >
        <el-table-column prop="top" label="等级排序" fixed>
          <template slot-scope="scope">
            {{ `V${scope.row.top}` }}
          </template>
        </el-table-column>
        <el-table-column
          prop="title"
          :label="`${$t('action.attendantName')}等级`"
        >
        </el-table-column>
        <el-table-column prop="time_long" label="服务时长" min-width="120">
          <template slot-scope="scope">
            <p>{{ `${scope.row.lower}至${scope.row.time_long}` }}分钟</p>
          </template>
        </el-table-column>
        <el-table-column prop="online_time" label="在线时长">
          <template slot-scope="scope">
            <p>{{ `${scope.row.online_time}` }}小时</p>
          </template>
        </el-table-column>
        <el-table-column prop="price" label="最低业绩">
          <template slot-scope="scope">
            <p>¥{{ scope.row.price }}</p>
          </template>
        </el-table-column>
        <el-table-column prop="time_long" label="续单率">
          <template slot-scope="scope">
            <p>{{ scope.row.add_balance }}%</p>
          </template>
        </el-table-column>
        <el-table-column prop="integral" label="积分"></el-table-column>
        <el-table-column prop="balance" label="提成比例">
          <template slot-scope="scope">
            <p>{{ scope.row.balance }}%</p>
          </template>
        </el-table-column>
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
                plain
                type="primary"
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
        :page="searchForm.page"
        :pageSize="searchForm.limit"
        :total="total"
        @handleSizeChange="handleSizeChange"
        @handleCurrentChange="handleCurrentChange"
      >
      </lb-page>

      <el-dialog
        :title="
          subForm.id
            ? `编辑${$t('action.attendantName')}等级`
            : `新增${$t('action.attendantName')}等级`
        "
        :visible.sync="showDialog.sub"
        width="600px"
        center
      >
        <el-form
          class="dialog-form"
          :model="subForm"
          ref="subForm"
          :rules="subFormRules"
          label-width="120px"
        >
          <el-form-item
            :label="`${$t('action.attendantName')}等级`"
            prop="title"
          >
            <el-input
              v-model="subForm.title"
              maxlength="5"
              show-word-limit
              :placeholder="`请输入${$t('action.attendantName')}等级名称`"
            ></el-input>
            <lb-tool-tips
              >{{
                $t('action.attendantName')
              }}等级名称唯一性，不可重复</lb-tool-tips
            >
          </el-form-item>
          <el-form-item label="服务时长" prop="time_long">
            <el-input placeholder="输入分钟" v-model="subForm.time_long">
              <template slot="append">分钟</template>
            </el-input>
            <lb-tool-tips
              >{{
                $t('action.attendantName')
              }}在提成折算周期内的服务时长，输入0则表示不设置该维度的考核</lb-tool-tips
            >
          </el-form-item>
          <el-form-item label="在线时长" prop="online_time">
            <el-input placeholder="输入小时" v-model="subForm.online_time">
              <template slot="append">小时</template>
            </el-input>
            <lb-tool-tips
              >{{ $t('action.attendantName') }}在提成折算周期内的{{
                $t('action.attendantName')
              }}的在线工作时间，输入0则表示不设置该维度的考核</lb-tool-tips
            >
          </el-form-item>
          <el-form-item label="最低业绩" prop="price">
            <el-input placeholder="输入最低业绩" v-model="subForm.price">
              <template slot="append">元</template>
            </el-input>
            <lb-tool-tips
              >{{
                $t('action.attendantName')
              }}在提成折算周期内的订单实际支付金额总和</lb-tool-tips
            >
          </el-form-item>
          <el-form-item label="续单率" prop="add_balance">
            <el-input placeholder="输入百分比" v-model="subForm.add_balance">
              <template slot="append">%</template>
            </el-input>
            <lb-tool-tips
              >{{ $t('action.attendantName') }}在提成折算周期内的续单费用计算
              <div class="mt-sm">
                续单金额=最低业绩*续单率，续单订单金额大于等于续单金额即可满足条件
              </div>
            </lb-tool-tips>
          </el-form-item>
          <el-form-item label="积分" prop="integral">
            <el-input placeholder="输入积分" v-model="subForm.integral">
            </el-input>
            <lb-tool-tips>
              若储值返佣设置勾选了返积分，则{{
                $t('action.attendantName')
              }}邀请用户充值将获得积分，积分和金额按照1:1比例换算
              <div class="mt-sm">
                例如：充值1000元，获得积分1000，输入0则表示无要求
              </div>
            </lb-tool-tips>
          </el-form-item>
          <el-form-item label="提成比例" prop="balance">
            <el-input placeholder="输入百分比" v-model="subForm.balance">
              <template slot="append">%</template>
            </el-input>
            <lb-tool-tips
              ><p class="mb-sm">提成比例取值0%到100%</p>
              设置比例之后，{{
                $t('action.attendantName')
              }}提成金额=实际金额*设置的百分比</lb-tool-tips
            >
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.sub = false">取 消</el-button>
          <el-button
            type="primary"
            @click="submitFormInfo('sub')"
            v-preventReClick
            >确 定</el-button
          >
        </span>
      </el-dialog>
      <el-dialog
        title="设置折算周期"
        :visible.sync="showDialog.setting"
        width="600px"
        center
      >
        <lb-tips
          >每周默认是从周一到周日计算来循环
          <div class="mt-sm">
            每半月是指以每月1号到15号、16号到月末最后一天为周期来循环
          </div>
          <div class="mt-sm">每月默认是从每月1号到月末的最后一天来循环</div>
          <div class="mt-sm mb-sm">
            每个季度折算周期是默认1、2、3为一个季度，4、5、6为一个季度，7、8、9为一个季度，10、11、12为一个季度
          </div>
          每年默认一年365天为一个折算周期</lb-tips
        >
        <el-form
          class="dialog-form"
          :model="settingForm"
          ref="settingForm"
          :rules="settingFormRules"
          label-width="120px"
        >
          <el-form-item label="折算周期" prop="level_cycle">
            <el-select v-model="settingForm.level_cycle" placeholder="请选择">
              <el-option
                v-for="item in cycleList"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.setting = false">取 消</el-button>
          <el-button
            type="primary"
            @click="submitFormInfo('setting')"
            v-preventReClick
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
  components: {},
  data () {
    return {
      loading: false,
      storeList: [],
      searchForm: {
        page: 1,
        limit: 10
      },
      tableData: [],
      total: 0,
      cycleList: [{ id: 0, title: '不限' }, { id: 1, title: '每周' }, { id: 5, title: '每半月' }, { id: 2, title: '每月' }, { id: 3, title: '每季度' }, { id: 4, title: '每年' }],
      showDialog: {
        sub: false,
        setting: false
      },
      subForm: {
        id: '',
        title: '',
        time_long: '',
        online_time: '',
        balance: '',
        price: '',
        add_balance: '',
        integral: ''
      },
      subFormRules: {
        title: { required: true, validator: this.$reg.isNotNull, text: this.$t('action.attendantName') + '等级', reg_type: 2, trigger: 'blur' },
        time_long: { required: true, validator: this.$reg.isNum, text: '服务时长', reg_type: 2, trigger: 'blur' },
        online_time: { required: true, validator: this.$reg.isNum, text: '在线时长', reg_type: 2, trigger: 'blur' },
        balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur' },
        add_balance: { required: true, validator: this.$reg.isPercent, trigger: 'blur' },
        price: { required: true, validator: this.$reg.isMoney, text: '最低业绩', reg_type: 2, trigger: 'blur' },
        integral: { required: true, validator: this.$reg.isNum, text: '积分', trigger: 'blur' }
      },
      settingForm: {
        level_cycle: 0
      },
      settingFormRules: {},
      userInfo: JSON.parse(window.localStorage.getItem('massage_userInfo')),
      config: {}
    }
  },
  async created () {
    await this.getConfigInfo()
    this.getTableDataList()
  },
  methods: {
    async getConfigInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.config = data
    },
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
      this.$api.technician.levelList(this.searchForm).then(res => {
        this.loading = false
        if (res.code === 200) {
          this.tableData = res.data.data
          this.total = res.data.total
        }
      })
    },
    /**
     * @method: 删除
     * @param {*} id
     */
    confirmDel (id) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.updateItem(id, -1)
      })
    },
    /**
     * @method: 上下架
     */
    async updateItem (id, status) {
      this.$api.technician.levelUpdate({ id, status }).then(res => {
        if (res.code === 200) {
          this.$message.success(this.$t(status === -1 ? 'tips.successDel' : 'tips.successOper'))
          if (status !== -1) return
          this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
          this.getTableDataList()
        } else {
          if (status === -1) return
          this.getTableDataList()
        }
      })
    },
    async toShowDialog (type, item = {}) {
      if (type === 'setting') {
        let { data } = await this.$api.system.configInfo()
        item.level_cycle = data.level_cycle
      }
      for (let key in this[`${type}Form`]) {
        this[`${type}Form`][key] = item[key]
      }
      this.showDialog[type] = !this.showDialog[type]
    },
    async submitFormInfo (key) {
      let flag = true
      this.$refs[`${key}Form`].validate(valid => {
        if (!valid) flag = false
      })
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this[`${key}Form`]))
        let methodKey = key === 'setting' ? 'system' : 'technician'
        let methodModel = key === 'setting' ? 'configUpdate' : subForm.id ? 'levelUpdate' : 'levelAdd'
        let { code } = await this.$api[methodKey][methodModel](subForm)
        if (code !== 200) return
        this.$message.success(this.$t(key === 'setting' || !subForm.id ? 'tips.successSub' : 'tips.successRev'))
        this.showDialog[key] = false
        if (key === 'setting') return
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
.lb-appclass-classroom-list {
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
