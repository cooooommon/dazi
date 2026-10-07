<!--
 * @Description: 通知
 * @Author: DXV-RGWU-TUFH-RFCY-IEGMYY
 * @Date: 2023-06-25 09:59:16
 * @LastEditTime: 2024-07-01 14:07:56
 * @LastEditors: wen kun
-->
<template>
  <div class="lb-system-notice">
    <top-nav />
    <div class="page-main">
      <lb-classify-title
        title="下单通知"
        :tips="`订单来单，${$t(
          'action.attendantName'
        )}的通知方式默认有来电通知、短信通知以及公众号模版通知；通知代理商方式默认为来电通知`"
      ></lb-classify-title>

      <el-form
        @submit.native.prevent
        :model="orderForm"
        :rules="orderFormRules"
        ref="orderForm"
        label-width="140px"
        class="baseform"
      >
        <div class="c-title text-bold pb-lg">来电通知</div>
        <el-form-item label="是否通知管理员" prop="reminder_admin_status">
          <el-radio-group
            @change="changeAdminStatus($event, 'orderForm', 'notice_admin')"
            v-model="orderForm.reminder_admin_status"
          >
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >选择通知，用户下单后，以下设置的管理员将收到来电提醒</lb-tool-tips
          >
        </el-form-item>
        <div v-if="orderForm.reminder_admin_status === 1">
          <el-form-item label="管理员手机号" prop="reminder_admin_phone">
            <div
              class="mb-md"
              v-for="(item, index) in orderForm.reminder_admin_phone"
              :key="index"
            >
              <el-input
                v-model="item.phone"
                placeholder="请输入管理员手机号"
              ></el-input>
              <lb-button
                style="margin-left: 16px"
                type="danger"
                icon="el-icon-delete"
                @click="
                  toAddItem('orderForm', 'reminder_admin_phone', 2, index)
                "
                v-if="
                  orderForm.reminder_admin_phone.length > 1 ||
                  (orderForm.reminder_admin_phone.length === 1 && index !== 0)
                "
                >删除</lb-button
              >
              <lb-button
                style="margin-left: 16px"
                type="primary"
                icon="el-icon-plus"
                @click="toAddItem('orderForm', 'reminder_admin_phone', 1)"
                v-if="index === orderForm.reminder_admin_phone.length - 1"
                >新增</lb-button
              >
            </div>
          </el-form-item>
          <el-form-item label="是否通知平台" prop="notice_admin">
            <el-radio-group v-model="orderForm.notice_admin">
              <el-radio :label="1">通知</el-radio>
              <el-radio :label="0">不通知</el-radio>
            </el-radio-group>
            <lb-tool-tips
              >勾选不通知，代理商的订单以及代理商下面的{{
                $t('action.attendantName')
              }}有来单时都不会电话通知到平台的管理员</lb-tool-tips
            >
          </el-form-item>
        </div>
        <el-form-item label="是否通知代理商" prop="notice_agent">
          <el-radio-group v-model="orderForm.notice_agent">
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >勾选不通知，代理商的订单以及代理商下面的{{
              $t('action.attendantName')
            }}有来单时都不会电话通知到代理商</lb-tool-tips
          >
        </el-form-item>
      </el-form>
      <el-form
        @submit.native.prevent
        :model="helpForm"
        :rules="helpFormRules"
        ref="helpForm"
        label-width="140px"
      >
        <div class="c-title text-bold pb-lg">公众号通知</div>
        <el-form-item label="是否通知管理员" prop="order_tmpl_admin_status">
          <el-radio-group
            @change="
              changeAdminStatus($event, 'helpForm', 'order_tmpl_notice_admin')
            "
            v-model="helpForm.order_tmpl_admin_status"
          >
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >选择通知，用户下单后，以下设置的管理员会收到公众号模版消息通知。</lb-tool-tips
          >
        </el-form-item>
        <div v-if="helpForm.order_tmpl_admin_status == 1">
          <el-form-item label="公众号通知人员" prop="order_tmpl_text">
            <el-tag
              type="danger"
              class="cursor-pointer"
              @click="toShowDialog('order_tmpl_text')"
              >选择关联用户
            </el-tag>
            <el-table
              :data="helpForm.order_tmpl_text"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              class="mt-lg"
              style="width: 100%"
            >
              <el-table-column prop="id" label="用户ID"></el-table-column>
              <el-table-column prop="avatarUrl" label="头像">
                <template slot-scope="scope">
                  <lb-image :src="scope.row.avatarUrl" />
                </template>
              </el-table-column>
              <el-table-column prop="nickName" label="昵称"></el-table-column>
              <el-table-column prop="phone" label="手机号"></el-table-column>
              <el-table-column label="操作" fixed="right">
                <template slot-scope="scope">
                  <div class="table-operate">
                    <lb-button
                      size="mini"
                      plain
                      type="danger"
                      @click="
                        toAddItem(
                          'helpForm',
                          'order_tmpl_text',
                          2,
                          scope.$index
                        )
                      "
                      >{{ $t('action.delete') }}</lb-button
                    >
                  </div>
                </template>
              </el-table-column>
            </el-table>
          </el-form-item>
          <el-form-item label="是否通知平台" prop="order_tmpl_notice_admin">
            <el-radio-group v-model="helpForm.order_tmpl_notice_admin">
              <el-radio :label="1">通知</el-radio>
              <el-radio :label="0">不通知</el-radio>
            </el-radio-group>
            <lb-tool-tips
              >选择不通知，代理商的{{
                $t('action.attendantName')
              }}有来单时，不会通知到平台的管理员</lb-tool-tips
            >
          </el-form-item>
        </div>
        <el-form-item label="是否通知代理商" prop="order_tmpl_agent_status">
          <el-radio-group v-model="helpForm.order_tmpl_agent_status">
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >选择不通知，代理商的{{
              $t('action.attendantName')
            }}有来单时，不会以模版消息通知到代理商</lb-tool-tips
          >
        </el-form-item>
      </el-form>
      <div class="space-xxl"></div>
      <lb-classify-title
        title="求救通知"
        :tips="`代理商的${$t(
          'action.attendantName'
        )}求救时，默认代理商都会收到来电通知、短信通知和公众号通知`"
      ></lb-classify-title>
      <el-form
        @submit.native.prevent
        :model="helpForm"
        :rules="helpFormRules"
        ref="helpForm"
        label-width="140px"
      >
        <div class="c-title text-bold pb-lg">来电通知</div>
        <el-form-item label="是否通知管理员" prop="reminder_admin_status">
          <el-radio-group
            @change="
              changeAdminStatus($event, 'helpForm', 'reminder_notice_admin')
            "
            v-model="helpForm.reminder_admin_status"
          >
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >选择通知，平台{{
              $t('action.attendantName')
            }}求救时，以下设置的管理员将收到来电提醒
          </lb-tool-tips>
        </el-form-item>
        <div v-if="helpForm.reminder_admin_status === 1">
          <el-form-item label="管理员手机号" prop="reminder_notice_phone">
            <div
              class="mb-md"
              v-for="(item, index) in helpForm.reminder_notice_phone"
              :key="index"
            >
              <el-input
                v-model="item.phone"
                placeholder="请输入管理员手机号"
              ></el-input>
              <lb-button
                style="margin-left: 16px"
                type="danger"
                icon="el-icon-delete"
                @click="
                  toAddItem('helpForm', 'reminder_notice_phone', 2, index)
                "
                v-if="
                  helpForm.reminder_notice_phone.length > 1 ||
                  (helpForm.reminder_notice_phone.length === 1 && index !== 0)
                "
                >删除</lb-button
              >
              <lb-button
                style="margin-left: 16px"
                type="primary"
                icon="el-icon-plus"
                @click="toAddItem('helpForm', 'reminder_notice_phone', 1)"
                v-if="index === helpForm.reminder_notice_phone.length - 1"
                >新增</lb-button
              >
            </div>
          </el-form-item>
          <el-form-item label="是否通知平台" prop="reminder_notice_admin">
            <el-radio-group v-model="helpForm.reminder_notice_admin">
              <el-radio :label="1">通知</el-radio>
              <el-radio :label="0">不通知</el-radio>
            </el-radio-group>
            <lb-tool-tips
              >选择不通知，代理商的{{
                $t('action.attendantName')
              }}求救时，不会电话通知到平台的管理员</lb-tool-tips
            >
          </el-form-item>
        </div>
        <el-form-item label="是否通知代理商" prop="reminder_agent_status">
          <el-radio-group v-model="helpForm.reminder_agent_status">
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >勾选不通知，代理商下面的{{
              $t('action.attendantName')
            }}求救时都不会电话通知到代理商</lb-tool-tips
          >
        </el-form-item>
        <div class="c-title text-bold pt-lg pb-lg">短信通知</div>
        <el-form-item label="是否通知管理员" prop="short_admin_status">
          <el-radio-group
            @change="
              changeAdminStatus($event, 'helpForm', 'short_notice_admin')
            "
            v-model="helpForm.short_admin_status"
          >
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >选择通知，平台{{
              $t('action.attendantName')
            }}求救时，以下设置的管理员将收到短信提醒
          </lb-tool-tips>
        </el-form-item>
        <div v-if="helpForm.short_admin_status === 1">
          <el-form-item label="管理员手机号" prop="help_phone">
            <div
              class="mb-md"
              v-for="(item, index) in helpForm.help_phone"
              :key="index"
            >
              <el-input
                v-model="item.phone"
                placeholder="请输入管理员手机号"
              ></el-input>
              <lb-button
                style="margin-left: 16px"
                type="danger"
                icon="el-icon-delete"
                @click="toAddItem('helpForm', 'help_phone', 2, index)"
                v-if="
                  helpForm.help_phone.length > 1 ||
                  (helpForm.help_phone.length === 1 && index !== 0)
                "
                >删除</lb-button
              >
              <lb-button
                style="margin-left: 16px"
                type="primary"
                icon="el-icon-plus"
                @click="toAddItem('helpForm', 'help_phone', 1)"
                v-if="index === helpForm.help_phone.length - 1"
                >新增</lb-button
              >
            </div>
          </el-form-item>
          <el-form-item label="是否通知平台" prop="short_notice_admin">
            <el-radio-group v-model="helpForm.short_notice_admin">
              <el-radio :label="1">通知</el-radio>
              <el-radio :label="0">不通知</el-radio>
            </el-radio-group>
            <lb-tool-tips
              >选择不通知，代理商的{{
                $t('action.attendantName')
              }}求救时，不会短信通知到平台的管理员</lb-tool-tips
            >
          </el-form-item>
        </div>
        <el-form-item label="是否通知代理商" prop="short_agent_status">
          <el-radio-group v-model="helpForm.short_agent_status">
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >勾选不通知，代理商下面的{{
              $t('action.attendantName')
            }}求救时都不会短信通知到代理商</lb-tool-tips
          >
        </el-form-item>
        <div class="c-title text-bold pt-lg pb-lg">公众号通知</div>
        <el-form-item label="是否通知管理员" prop="tmpl_admin_status">
          <el-radio-group
            @change="changeAdminStatus($event, 'helpForm', 'tmpl_notice_admin')"
            v-model="helpForm.tmpl_admin_status"
          >
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >勾选不通知，代理商的订单以及代理商下面的{{
              $t('action.attendantName')
            }}求救时都不会以公众号模版消息方式通知到平台的管理员
          </lb-tool-tips>
        </el-form-item>
        <div v-if="helpForm.tmpl_admin_status === 1">
          <el-form-item label="公众号通知人员" prop="help_user_id">
            <el-tag
              type="danger"
              class="cursor-pointer"
              @click="toShowDialog('help_user_id')"
              >选择关联用户
            </el-tag>
            <el-table
              :data="helpForm.help_user_id"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              class="mt-lg"
              style="width: 100%"
            >
              <el-table-column prop="id" label="用户ID"></el-table-column>
              <el-table-column prop="avatarUrl" label="头像">
                <template slot-scope="scope">
                  <lb-image :src="scope.row.avatarUrl" />
                </template>
              </el-table-column>
              <el-table-column prop="nickName" label="昵称"></el-table-column>
              <el-table-column prop="phone" label="手机号"></el-table-column>
              <el-table-column label="操作" fixed="right">
                <template slot-scope="scope">
                  <div class="table-operate">
                    <lb-button
                      size="mini"
                      plain
                      type="danger"
                      @click="
                        toAddItem('helpForm', 'help_user_id', 2, scope.$index)
                      "
                      >{{ $t('action.delete') }}</lb-button
                    >
                  </div>
                </template>
              </el-table-column>
            </el-table>
          </el-form-item>
          <el-form-item label="是否通知平台" prop="tmpl_notice_admin">
            <el-radio-group v-model="helpForm.tmpl_notice_admin">
              <el-radio :label="1">通知</el-radio>
              <el-radio :label="0">不通知</el-radio>
            </el-radio-group>
            <lb-tool-tips
              >选择不通知，代理商的{{
                $t('action.attendantName')
              }}求救时，不会通知到平台的管理员</lb-tool-tips
            >
          </el-form-item>
        </div>
        <el-form-item label="是否通知代理商" prop="tmpl_agent_status">
          <el-radio-group v-model="helpForm.tmpl_agent_status">
            <el-radio :label="1">通知</el-radio>
            <el-radio :label="0">不通知</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >勾选不通知，代理商下面的{{
              $t('action.attendantName')
            }}求救时都不会通知到代理商</lb-tool-tips
          >
        </el-form-item>
        <div class="c-title text-bold pb-lg">求救设置</div>
        <el-form-item label="报警通知提示音" prop="help_voice">
          <div class="upload-file-warp">
            <input
              type="text"
              class="choice-file-input"
              v-model="helpForm.help_voice"
              placeholder="请选择报警通知提示音"
            />
            <lb-cover
              type="button"
              fileType="audio"
              :fileSize="1"
              @selectedFiles="getVoice"
            ></lb-cover>
          </div>
          <lb-tool-tips>
            自主上传报警提示音，{{
              $t('action.attendantName')
            }}寻求报警时，带有外放喇叭的电脑会自动发出声音
            <div class="mt-md">
              {{
                $t('action.attendantName')
              }}点击求救后并不会马上播放，每过10秒请求一次接口数据，当获取到有未读求救信息时才会播放
            </div>
            <div class="mt-sm">
              手动刷新页面后，需要手动点击页面任一可点击操作的位置（例如：点击菜单栏）后，当再次通过接口获取到未读数量时才会自动播放
            </div>
          </lb-tool-tips>
        </el-form-item>
      </el-form>
      <div class="space-xxl"></div>
      <lb-classify-title title="其他通知"></lb-classify-title>
      <el-form
        @submit.native.prevent
        :model="noticeForm"
        :rules="noticeFormRules"
        ref="noticeForm"
        label-width="140px"
        class="baseform"
      >
        <div class="c-title text-bold pb-lg">公众号通知</div>
        <el-form-item label="公众号模板通知" prop="wechat_tmpl">
          <el-radio-group v-model="noticeForm.wechat_tmpl">
            <el-radio :label="1">{{ $t('action.ON') }}</el-radio>
            <el-radio :label="0">{{ $t('action.OFF') }}</el-radio>
          </el-radio-group>
          <lb-tool-tips
            >选择开启，需要配置订单服务通知模板ID、公众号通知人员，当遇到以下情况，将通知关联人员和订单所属{{
              $t('action.attendantName')
            }}的关联代理商
            <div class="mt-md">
              1、{{ $t('action.attendantName') }}拒单后，通知平台和关联代理商。
            </div>
            <div class="mt-sm">
              2、{{
                $t('action.attendantName')
              }}长时间不接单，通知平台和关联代理商
            </div>
            <div class="mt-sm">
              3、代理商只会收到他自己{{ $t('action.attendantName') }}的通知。
            </div>
            <div class="mt-sm">
              4、服务完成后，{{
                $t('action.attendantName')
              }}在规定时间内未离开目的地，通知平台和代理商。
            </div>
            <div class="mt-sm">
              5、{{
                $t('action.attendantName')
              }}未按时到达目的地，迟到需提醒平台和代理商
            </div>
          </lb-tool-tips>
        </el-form-item>
        <div v-if="noticeForm.wechat_tmpl">
          <el-form-item label="公众号通知人员" prop="wechat_tmpl_admin">
            <el-tag
              type="danger"
              class="cursor-pointer"
              @click="toShowDialog('wechat_tmpl_admin')"
              >选择关联用户
            </el-tag>
            <el-table
              :data="noticeForm.wechat_tmpl_admin"
              :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
              class="mt-lg"
              style="width: 100%"
            >
              <el-table-column prop="id" label="用户ID"></el-table-column>
              <el-table-column prop="avatarUrl" label="头像">
                <template slot-scope="scope">
                  <lb-image :src="scope.row.avatarUrl" />
                </template>
              </el-table-column>
              <el-table-column prop="nickName" label="昵称"></el-table-column>
              <el-table-column prop="phone" label="手机号"></el-table-column>
              <el-table-column label="操作" fixed="right">
                <template slot-scope="scope">
                  <div class="table-operate">
                    <lb-button
                      size="mini"
                      plain
                      type="danger"
                      @click="
                        toAddItem(
                          'noticeForm',
                          'wechat_tmpl_admin',
                          2,
                          scope.$index
                        )
                      "
                      >{{ $t('action.delete') }}</lb-button
                    >
                  </div>
                </template>
              </el-table-column>
            </el-table>
          </el-form-item>
        </div>
      </el-form>
      <lb-button
        type="primary"
        style="margin-left: 140px"
        @click="submitFormInfo"
        v-preventReClick
        >{{ $t('action.submit') }}</lb-button
      >

      <el-dialog
        title="关联用户"
        :visible.sync="showDialog"
        width="800px"
        center
      >
        <el-form
          :inline="true"
          :model="searchForm"
          ref="searchForm"
          label-width="70px"
          class="dialog-form"
        >
          <el-form-item label="输入查询" prop="nickName">
            <el-input
              v-model="searchForm.nickName"
              placeholder="请输入微信昵称/手机号"
              style="width: 300px"
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
          :page="searchForm.page"
          :pageSize="searchForm.limit"
          :total="total"
          @handleSizeChange="handleSizeChange"
          @handleCurrentChange="handleCurrentChange"
        >
        </lb-page>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog = false">取 消</el-button>
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
    let checkAdminPhone = (rule, value, callback) => {
      let isTel = /^1[3-9]\d{9}$/
      for (let key in value) {
        let index = key * 1 + 1
        let { phone } = value[key]
        if (!phone || !isTel.test(phone)) {
          let errorMsg = !phone ? `请输入${rule.text}` : `${phone} 手机号无效`
          callback(new Error(`第${index}条数据：${errorMsg}`))
          return
        }
      }
      let arr = value.filter(item => {
        return item.phone
      })
      if (arr.length === value.length && arr.length > 0) {
        callback()
      }
    }
    let checkNoticeUser = (rule, value, callback) => {
      if (this.noticeForm.wechat_tmpl && (!value || value.length === 0)) {
        callback(new Error(`${rule.message}`))
      } else {
        callback()
      }
    }
    return {
      orderForm: {
        reminder_admin_status: 0,
        reminder_admin_phone: [],
        notice_admin: 0,
        notice_agent: 0
      },
      orderFormRules: {
        reminder_admin_status: { required: true, type: 'number', message: '请选择是否通知管理员', trigger: 'blur' },
        reminder_admin_phone: { required: true, validator: checkAdminPhone, text: '管理员手机号', trigger: 'blur' },
        notice_admin: { required: true, type: 'number', message: '请选择是否通知平台', trigger: 'blur' },
        notice_agent: { required: true, type: 'number', message: '请选择是否通知代理商', trigger: 'blur' }
      },
      helpForm: {
        reminder_admin_status: 1,
        reminder_notice_phone: [],
        reminder_notice_admin: 0,
        short_admin_status: 1,
        help_phone: [],
        short_notice_admin: 0,
        tmpl_admin_status: 1,
        help_user_id: [],
        tmpl_notice_admin: 0,
        reminder_agent_status: 0,
        short_agent_status: 0,
        tmpl_agent_status: 0,
        order_tmpl_agent_status: 0,
        order_tmpl_admin_status: 0,
        order_tmpl_notice_admin: 0,
        order_tmpl_text: [],
        help_voice: ''
      },
      helpFormRules: {
        reminder_admin_status: { required: true, type: 'number', message: '请选择是否通知管理员', trigger: 'blur' },
        reminder_notice_phone: { required: true, validator: checkAdminPhone, text: '管理员手机号', trigger: 'blur' },
        reminder_notice_admin: { required: true, type: 'number', message: '请选择是否通知平台', trigger: 'blur' },
        short_admin_status: { required: true, type: 'number', message: '请选择是否通知管理员', trigger: 'blur' },
        help_phone: { required: true, validator: checkAdminPhone, text: '管理员手机号', trigger: 'blur' },
        short_notice_admin: { required: true, type: 'number', message: '请选择是否通知平台', trigger: 'blur' },
        tmpl_admin_status: { required: true, type: 'number', message: '请选择是否通知管理员', trigger: 'blur' },
        help_user_id: { required: true, type: 'array', message: '请选择公众号通知人员', trigger: 'blur' },
        tmpl_notice_admin: { required: true, type: 'number', message: '请选择是否通知平台', trigger: 'blur' },
        reminder_agent_status: { required: true, type: 'number', message: '请选择是否通知代理商', trigger: 'blur' },
        short_agent_status: { required: true, type: 'number', message: '请选择是否通知代理商', trigger: 'blur' },
        tmpl_agent_status: { required: true, type: 'number', message: '请选择是否通知代理商', trigger: 'blur' },
        order_tmpl_agent_status: { required: true, type: 'number', message: '请选择是否通知代理商', trigger: 'blur' },
        order_tmpl_admin_status: { required: true, type: 'number', message: '请选择是否通知管理员', trigger: 'blur' },
        order_tmpl_notice_admin: { required: true, type: 'number', message: '请选择是否通知平台', trigger: 'blur' },
        order_tmpl_text: { required: true, type: 'array', message: '请选择公众号通知人员', trigger: 'blur' },
        help_voice: { required: true, type: 'string', message: '请选择报警通知提示音', trigger: 'blur' }
      },
      noticeForm: {
        wechat_tmpl: 0,
        wechat_tmpl_admin: []
      },
      noticeFormRules: {
        wechat_tmpl: { required: true, type: 'number', message: '请选择', trigger: 'change' },
        wechat_tmpl_admin: { required: true, validator: checkNoticeUser, message: '请选择公众号通知人员', trigger: 'blur' }
      },
      searchForm: {
        page: 1,
        limit: 10,
        nickName: ''
      },
      total: 0,
      loading: false,
      tableData: [],
      multipleSelection: [],
      showDialog: false,
      showDialogType: ''
    }
  },
  created () {
    this.getBaseInfo()
  },
  methods: {
    async getBaseInfo () {
      let [order, help, notice] = await Promise.all([this.$api.system.reminderConfigInfo(), this.$api.notice.helpConfigInfo(), this.$api.system.configInfo()])
      order.data.reminder_admin_phone = typeof (order.data.reminder_admin_phone) === 'object' && order.data.reminder_admin_phone.length > 0 ? order.data.reminder_admin_phone.map(item => {
        return { phone: item }
      }) : [{ phone: '' }]
      for (let i in this.orderForm) {
        this.orderForm[i] = order.data[i]
      }
      let phoneArr = ['reminder_notice_phone', 'help_phone']
      phoneArr.map(item => {
        help.data[item] = help.data[item] && typeof (help.data[item]) === 'object' && help.data[item].length > 0 ? help.data[item].map(aitem => {
          return { phone: aitem }
        }) : [{ phone: '' }]
      })
      for (let i in this.helpForm) {
        this.helpForm[i] = help.data[i]
      }
      notice.data.wechat_tmpl_admin = typeof (notice.data.wechat_tmpl_admin) === 'string' ? [] : notice.data.wechat_tmpl_admin
      for (let i in this.noticeForm) {
        this.noticeForm[i] = notice.data[i]
      }
    },
    async toShowDialog (key) {
      this.showDialogType = key
      this.searchForm.nickName = ''
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
      this.loading = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm))
      let { code, data } = await this.$api.custom.userList(searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    handleSelectionChange (val) {
      this.multipleSelection = val
    },
    handleDialogConfirm () {
      if (this.multipleSelection.length === 0) {
        this.$message.error(`请选择用户`)
        return
      }
      let { showDialogType: key } = this
      let keyArr = {
        wechat_tmpl_admin: 'noticeForm',
        help_user_id: 'helpForm',
        order_tmpl_text: 'helpForm'
      }
      let form = keyArr[key]
      let user = JSON.parse(JSON.stringify(this[form][key]))
      let arr1 = user.length > 0 ? user.map(item => { return item.id }) : []
      this.multipleSelection.map(item => {
        if (arr1.includes(item.id)) return
        user.push(item)
      })
      this[form][key] = user
      this.showDialog = false
    },
    /**
     * @method 获取语音文件
     */
    getVoice (file) {
      let len = file.length - 1
      this.helpForm.help_voice = file[len].url
    },
    /**
     * @method: 新增/删除
     */
    async toAddItem (form, key, type, index) {
      if (type === 2) {
        this[form][key].splice(index, 1)
      } else {
        this[form][key].push({ phone: '' })
      }
    },
    changeAdminStatus (val, form, key) {
      if (!val) {
        this[form][key] = 0
      }
    },
    async submitFormInfo () {
      let formArr = ['orderForm', 'helpForm', 'noticeForm']
      let flag = true
      for (let i = 0, len = formArr.length; i < len; i++) {
        this.$refs[formArr[i]].validate(valid => {
          if (!valid) {
            flag = false
            return false
          }
        })
      }
      if (!flag) return
      let isTel = /^1[3-9]\d{9}$/
      let orderForm = JSON.parse(JSON.stringify(this.orderForm))
      let { reminder_admin_phone: phone } = orderForm
      let arr = phone.filter(item => {
        return item.phone && isTel.test(item.phone)
      })
      orderForm.reminder_admin_phone = arr.map(aitem => {
        return aitem.phone
      })
      this.orderForm.reminder_admin_phone = arr.length > 0 ? arr : [{ phone: '' }]
      let helpForm = JSON.parse(JSON.stringify(this.helpForm))
      helpForm.help_user_id = helpForm.help_user_id.map(item => {
        return item.id
      })
      helpForm.order_tmpl_text = helpForm.order_tmpl_text.map(item => {
        return item.id
      })

      let phoneArr = ['reminder_notice_phone', 'help_phone']
      phoneArr.map(item => {
        let helpPhoneArr = helpForm[item].filter(aitem => {
          return aitem.phone && isTel.test(aitem.phone)
        })
        helpForm[item] = helpPhoneArr.map(bitem => {
          return bitem.phone
        })
        this.helpForm[item] = helpPhoneArr.length > 0 ? helpPhoneArr : [{ phone: '' }]
      })
      let noticeForm = JSON.parse(JSON.stringify(this.noticeForm))
      noticeForm.wechat_tmpl_admin = this.$util.getItems(noticeForm.wechat_tmpl_admin)
      await Promise.all([this.$api.system.reminderConfigUpdate(orderForm), this.$api.notice.helpConfigUpate(helpForm), this.$api.system.configUpdate(noticeForm)])
      this.$message.success(this.$t('tips.successSub'))
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-notice {
  width: 100%;
  .el-input,
  .el-select {
    width: 400px;
  }
}
</style>
