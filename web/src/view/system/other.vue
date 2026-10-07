<template>
  <div class="lb-system-other">
    <top-nav />
    <div class="page-main">
      <el-form
        @submit.native.prevent
        :model="subForm"
        :rules="subFormRules"
        ref="subForm"
        label-width="140px"
      >
        <el-form-item label="订单超时" prop="over_time">
          <el-input v-model.number="subForm.over_time" placeholder="请输入分钟">
            <template slot="append">分钟</template>
          </el-input>
          <lb-tool-tips
            >订单未支付超时时间，超时将自动取消订单，单位：分钟</lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="时间间隔" prop="time_interval">
          <el-input
            v-model.number="subForm.time_interval"
            placeholder="请输入分钟"
          >
            <template slot="append">分钟</template>
          </el-input>
          <lb-tool-tips
            >{{
              $t('action.attendantName')
            }}结束上一单之后，与下一单的时间无间隔导致{{
              $t('action.attendantName')
            }}不能赶到下一个服务点
            <div class="mt-sm">可设置订单间隔，例如设置订单间隔为30分钟</div>
            <div class="mt-sm">
              上一个订单预约时间是14:00-15:00，则下一个用户预约同一个{{
                $t('action.attendantName')
              }}的开始时间为15:30之后
            </div></lb-tool-tips
          >
        </el-form-item>
        <el-form-item label="最长预约" prop="max_day">
          <el-select v-model="subForm.max_day" placeholder="请选择">
            <el-option
              v-for="item in longOptions"
              :key="item.id"
              :label="item.title"
              :value="item.id"
            ></el-option>
          </el-select>
          <lb-tool-tips>客户预约服务选择时间时可选择的时间期限</lb-tool-tips>
        </el-form-item>
        <el-form-item label="时长单位" prop="time_unit">
          <el-select v-model="subForm.time_unit" placeholder="请选择">
            <el-option
              v-for="item in timeOptions"
              :key="item.id"
              :label="item.title"
              :value="item.id"
            ></el-option>
          </el-select>
          <lb-tool-tips>划分工作时间的时间单位</lb-tool-tips>
        </el-form-item>

        <!-- <el-form-item label="平台联系电话" prop="mobile">
          <el-input
            v-model="subForm.mobile"
            placeholder="请输入平台联系电话"
          ></el-input>
          <lb-tool-tips
            >平台联系电话用于客户咨询，请填写有效的联系电话</lb-tool-tips
          >
        </el-form-item> -->
        <el-form-item label="语音播报音频" prop="countdown_voice">
          <div class="upload-file-warp">
            <input
              type="text"
              class="choice-file-input"
              v-model="subForm.countdown_voice"
              placeholder="请选择语音播报音频"
            />
            <lb-cover
              type="button"
              fileType="audio"
              :fileSize="1"
              @selectedFiles="getVoice"
            ></lb-cover>
          </div>
          <lb-tool-tips>
            <div>
              当{{
                $t('action.attendantName')
              }}点击开始服务，若订单服务总时长大于5分钟，则距离服务结束5分钟时，自动播放此音频文件
            </div>
            <div class="mt-md">
              1）此音频仅在{{
                $t('action.attendantName')
              }}端的订单列表页（仅在点击【开始服务】且未退出列表页面时有效）、订单详情页播放
            </div>
            <div class="mt-md">
              2）如有多个订单点击开始服务，则按照时间先后依次播报，当第二个订单到达播报时间时，若第一个订单尚未播报完毕则暂停并重新播报
            </div>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="客服类型" prop="im_type">
          <el-radio-group v-model="subForm.im_type">
            <el-radio :label="1">平台电话</el-radio>
            <el-radio :label="2" v-if="routesItem.auth.wechat"
              >小程序客服</el-radio
            >
            <el-radio :label="3">企业微信客服</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >勾选平台电话，用户可直接拨打平台的电话号码
            <div class="mt-sm" v-if="routesItem.auth.wechat">
              勾选小程序客服，在腾讯小程序官方后台添加客服后，可连接对话（仅对小程序端有效，其他端默认为拨打平台电话）
            </div>
            <div class="mt-sm">
              勾选企业微信客服，在腾讯企业微信官方后台添加客服后，可连接对话
            </div>
          </lb-tool-tips>
        </el-form-item>
        <div v-if="subForm.im_type !== 3">
          <el-form-item label="平台联系电话" prop="mobile">
            <el-input
              v-model="subForm.mobile"
              placeholder="请输入平台联系电话"
            ></el-input>
            <lb-tool-tips
              >用于客户咨询，请填写有效的联系电话，支持400电话
              <div class="mt-sm">400电话格式：400-xxxxxxx</div>
            </lb-tool-tips>
          </el-form-item>
        </div>
        <div v-if="subForm.im_type === 3">
          <el-form-item label="企业微信ID" prop="qywx_company_id">
            <el-input
              v-model.number="subForm.qywx_company_id"
              placeholder="请输入企业微信ID"
            >
            </el-input>
            <a
              class="c-warning ml-lg"
              href="https://docs.qq.com/doc/DWkxQVkJjU0VLUUhs"
              target="_blank"
              >点击查看配置企业微信客服文档</a
            >
          </el-form-item>
          <el-form-item label="企业微信客服" prop="qywx_kid">
            <div
              class="mb-md"
              v-for="(item, index) in subForm.qywx_kid"
              :key="index"
            >
              <el-input
                v-model="item.link"
                placeholder="请输入企业微信客服链接"
                style="width: 400px"
              ></el-input>
              <lb-button
                style="margin-left: 16px"
                type="danger"
                icon="el-icon-delete"
                @click="toAddItem(2, index)"
                v-if="
                  subForm.qywx_kid.length > 1 ||
                  (subForm.qywx_kid.length === 1 && index !== 0)
                "
                >删除</lb-button
              >
              <lb-button
                style="margin-left: 16px"
                type="primary"
                icon="el-icon-plus"
                @click="toAddItem(1)"
                v-if="index === subForm.qywx_kid.length - 1"
                >新增</lb-button
              >
            </div>
          </el-form-item>
        </div>
        <!-- <el-form-item label="匿名评价" prop="anonymous_evaluate">
          <el-radio-group v-model="subForm.anonymous_evaluate">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >开启之后，手机用户端的评价不显示真实头像和真实昵称
          </lb-tool-tips>
        </el-form-item> -->
        <el-form-item label="是否派单" prop="order_dispatch">
          <el-radio-group v-model="subForm.order_dispatch">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >开启派单后，{{
              $t('action.attendantName')
            }}拒单，则不会自动给客户进入退款环节，会进入派单环节，由平台客服人员手动派单给其他人员
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="余额显示文案" prop="balance_character">
          <el-input
            v-model="subForm.balance_character"
            placeholder="请输入余额显示文案"
            maxlength="10"
            show-word-limit
          ></el-input>
          <lb-tool-tips
            >修改后，手机端及后台的余额支付都会显示为自定义设置的文案信息，例如：抖币支付
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="代理商电话" prop="agent_phone">
          <el-radio-group v-model="subForm.agent_phone">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >关闭之后，代理商下的{{
              $t('action.attendantName')
            }}关联的订单，用户联系客服只能联系平台不能联系代理商
          </lb-tool-tips>
        </el-form-item>
        <el-form-item
          :label="`用户联系${$t('action.attendantName')}`"
          prop="user_contact_coach"
        >
          <el-radio-group v-model="subForm.user_contact_coach">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >开启之后，用户可以在订单详情里联系{{
              $t('action.attendantName')
            }}，如果未配置虚拟号功能，则会直接查看{{
              $t('action.attendantName')
            }}的真实电话号码
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="强制获取手机号" prop="user_force_login">
          <el-radio-group v-model="subForm.user_force_login">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >当选择开启强制获取手机号时：
            <p>
              1、若用户未登录，则在{{
                $t('action.attendantName')
              }}列表点击图片时，跳转登录页并且跳转手机号注册页面。
            </p>
            <p>
              2、若用户已登录且在未绑定手机号的情况下，在页面触发绑定手机号弹窗时，该弹窗将没有取消按钮
            </p>
          </lb-tool-tips>
        </el-form-item>
        <el-form-item label="用户下单路径" prop="place_order_path">
          <el-radio-group v-model="subForm.place_order_path">
            <el-radio :label="1">快速版</el-radio>
            <el-radio :label="2">详细版</el-radio>
          </el-radio-group>
          <lb-button
            class="ml-lg"
            @click="viewTutorial"
            type="primary"
            size="mini"
            plain
            v-preventReClick
            >查看教程</lb-button
          >
        </el-form-item>
        <el-form-item label="用户实时定位" prop="realtime_location">
          <el-radio-group v-model="subForm.realtime_location">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips>
            <div class="mt-md">
              实时定位需频繁使用腾讯地图的“逆地址解析（位置描述）”功能，个人开发者限制调用量上限（10000次/日），企业开发者限制调用量上限（3,000,000次/日）
            </div>
            <div class="mt-sm">
              请登录腾讯地图开发者平台，确认调用量限制数量，若还未认证企业开发者，请尽快认证，认证后将可享有更多限额次数
            </div>
            <div class="mt-lg">
              <a
                class="c-link cursor-pointer"
                href="https://docs.qq.com/doc/DWmRFUFRMRVRxQ0Rj"
                target="_blank"
                >如何查看腾讯地图应用配额？</a
              >
            </div>
            <div class="mt-md c-warning">
              开启实时定位后，在以下情况将自动获取用户当前所在地址：
              <div class="mt-sm">1)、用户首次进入系统</div>
              <div class="mt-sm">
                2)、切换【首页/{{ $t('action.attendantName')
                }}{{ routesItem.auth.map ? '/地图找人' : '' }}】等页面
              </div>
              <div class="mt-sm">
                3)、在【首页/{{ $t('action.attendantName')
                }}{{ routesItem.auth.map ? '/地图找人' : '' }}】页面下拉刷新
              </div>
            </div>
          </lb-tool-tips>
          <div class="lb-text-red-tips c-warning">
            开启此功能前需联系售后配置，若未经指导私下开启，导致业务高峰期用户无法定位下单、营收问题将由软件购买方自行承担
          </div>
        </el-form-item>
        <el-form-item label="用户来源表单" prop="user_from_switch">
          <el-radio-group v-model="subForm.user_from_switch">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >开启之后，通过微信公众号搜索进入系统的用户，<br />需要强制填写来源，不填写则不能操作下单不能浏览陪玩官等权限。
          </lb-tool-tips>
        </el-form-item>
        <el-form-item>
          <lb-button @click="submitForm" type="primary" v-preventReClick>{{
            $t('action.submit')
          }}</lb-button>
        </el-form-item>
      </el-form>
    </div>
    <el-dialog
      title=""
      :visible.sync="centerDialogVisible"
      width="700px"
      center
    >
      <div>
        快速版：用户点击服务预约，跳转显示与该服务关联的{{
          $t('action.attendantName')
        }}，点击{{ $t('action.attendantName') }}跳转下单页
      </div>
      <div class="pt-lg pb-lg">
        <img
          src="https://lbqny.migugu.com/admin/peiwan/place_order1.jpg"
          alt=""
          style="width: 100%"
        />
      </div>
      <div>
        详细版：用户点击服务预约，跳转显示与该服务关联的{{
          $t('action.attendantName')
        }}，需要跳转到{{ $t('action.attendantName') }}详情以后才能跳转下单页
      </div>
      <div class="pt-lg pb-lg">
        <img
          src="https://lbqny.migugu.com/admin/peiwan/place_order2.jpg"
          alt=""
          style="width: 100%"
        />
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="centerDialogVisible = false"
          >我已知晓</el-button
        >
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  data () {
    let checkImUser = (rule, value, callback) => {
      for (let key in value) {
        let index = key * 1 + 1
        let { link } = value[key]
        if (!link || (link && !link.includes('https://work.weixin.qq.com'))) {
          let msg = !link ? '请输入企业微信客服链接' : '请输入正确的企业微信客服链接'
          callback(new Error(`第${index}条数据：${msg}`))
          return
        }
      }
      let arr = value.filter(item => {
        return item.link
      })
      if (arr.length === value.length) {
        callback()
      }
    }
    return {
      centerDialogVisible: false,
      longOptions: [{ id: 3, title: '近3天' }, { id: 5, title: '近5天' }, { id: 7, title: '近7天' }],
      timeOptions: [{ id: 30, title: '半小时' }, { id: 60, title: '一小时' }, { id: 120, title: '两小时' }],
      subForm: {
        mobile: '',
        countdown_voice: '',
        im_type: 1,
        anonymous_evaluate: 0,
        order_dispatch: 0,
        balance_character: '',
        over_time: '',
        time_interval: '',
        max_day: '',
        time_unit: '',
        agent_phone: 1,
        qywx_company_id: '',
        qywx_kid: [],
        user_contact_coach: 1,
        user_force_login: 0,
        place_order_path: 1,
        realtime_location: 0,
        user_from_switch: 0
      },
      subFormRules: {
        im_type: { required: true, type: 'number', message: '请选择客服类型', trigger: 'blur' },
        anonymous_evaluate: { required: true, type: 'number', message: '请选择匿名评价', trigger: 'blur' },
        order_dispatch: { required: true, type: 'number', message: '请选择是否派单', trigger: 'blur' },
        balance_character: { required: true, validator: this.$reg.isNotNull, text: '余额显示文案', trigger: 'blur' },
        over_time: { required: true, validator: this.$reg.isNum, reg_type: 2, text: '分钟数', trigger: 'blur' },
        time_interval: { required: true, validator: this.$reg.isNum, reg_type: 2, text: '分钟数', trigger: 'blur' },
        max_day: { required: true, type: 'number', message: '请选择最长预约', trigger: 'blur' },
        time_unit: { required: true, type: 'number', message: '请选择时长单位', trigger: 'blur' },
        agent_phone: { required: true, type: 'number', message: '请选择是否启用代理商电话', trigger: 'blur' },
        qywx_company_id: { required: true, validator: this.$reg.isNotNull, reg_type: 2, text: '企业微信ID', trigger: 'blur' },
        qywx_kid: { required: true, validator: checkImUser, trigger: 'blur' },
        user_contact_coach: { required: true, type: 'number', message: '请选择是否启用用户联系' + this.$t('action.attendantName'), trigger: 'blur' },
        user_force_login: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        place_order_path: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        realtime_location: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        user_from_switch: { required: true, type: 'number', message: '请选择', trigger: 'blur' }
      }
    }
  },
  created () {
    this.getFormInfo()
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    async getFormInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      data.qywx_kid = data.qywx_kid && data.qywx_kid.length > 0 ? data.qywx_kid.split(',').map(item => {
        return { link: item }
      }) : [{ link: '' }]
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
    },
    /**
       * @method 获取语音文件
       */
    getVoice (file) {
      let len = file.length - 1
      this.subForm.countdown_voice = file[len].url
    },
    /**
     * @method: 新增/删除
     */
    async toAddItem (key, index) {
      if (key === 2) {
        this.subForm.qywx_kid.splice(index, 1)
      } else {
        this.subForm.qywx_kid.push({ link: '' })
      }
    },
    submitForm () {
      this.$refs['subForm'].validate(valid => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          let regPhone = /((^400)-([0-9]{7})$)|(^1[3-9]\d{9}$)|((^0\d{2,3})-(\d{7,8})$)/
          if (subForm.mobile && !regPhone.test(subForm.mobile)) {
            this.$message.error(`请输入有效的平台联系电话`)
            return
          }
          let arr = subForm.qywx_kid.filter(item => {
            return item.link
          }).map(aitem => {
            return aitem.link
          })
          subForm.qywx_kid = arr.join()
          this.$api.system.configUpdate(subForm).then(res => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
            }
          })
        }
      })
    },
    viewTutorial () {
      this.centerDialogVisible = true
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-other {
  width: 100%;

  .el-input,
  .el-select {
    width: 300px;
  }
}
</style>
