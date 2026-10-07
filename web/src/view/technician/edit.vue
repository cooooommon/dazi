<!--
 * @Description: 编辑服务
 * @Author: xiao li
 * @Date: 2021-07-03 11:41:05
 * @LastEditTime: 2024-11-22 13:43:55
 * @LastEditors: wen kun
-->

<template>
  <div class="lb-system-banner-edit">
    <top-nav :title="navTitle" :isBack="true" />
    <div v-if="detail.id" class="page-main">
      <div class="flex-center c-caption">
        <img class="work-img radius-15" :src="detail.work_img" />
        <div class="flex-1 ml-lg">
          <div class="flex-y-center">
            <div class="f-title c-title text-bold">
              {{ detail.coach_name }}
            </div>
            <el-tag
              size="small"
              class="ml-lg"
              :type="detail.user_id > 0 ? 'success' : 'info'"
              >{{ detail.user_id > 0 ? '已认证' : '未认证' }}</el-tag
            >
            <el-tag
              size="small"
              class="ml-lg"
              :type="statusText[detail.status].type"
            >
              {{ statusText[detail.status].text }}
            </el-tag>

            <el-popover
              placement="top-start"
              width="400"
              trigger="hover"
              v-if="detail.status === 4"
            >
              <div class="f-caption c-title" slot>
                <div class="f-caption c-title" slot>
                  <div class="c-caption pb-sm">驳回原因：</div>
                  <div v-html="detail.sh_text"></div>
                </div>
                <div class="f-caption c-caption mt-md">
                  驳回时间：{{ detail.sh_time | handleTime }}
                </div>
              </div>
              <span
                class="iconfont iconwentifankui1 c-warning ml-sm"
                slot="reference"
              ></span>
            </el-popover>
          </div>
          <div class="mt-md mb-sm">
            ID：<span class="c-title">{{ detail.id }}</span>
          </div>
          <div class="mt-sm mb-sm">
            性别：<span class="c-title">{{
              detail.sex === 0 ? '男' : '女'
            }}</span>
          </div>
        </div>
      </div>
      <div class="flex-y-center pt-md pb-lg mb-md c-caption">
        <div>
          手机号：<span class="c-title">{{ detail.mobile }}</span>
        </div>
        <div style="margin: 0 50px">
          申请时间：<span class="c-title">{{
            detail.create_time | handleTime
          }}</span>
        </div>
        <div v-if="routesItem.auth.broker" class="flex-y-center">
          <div class="pr-lg">
            {{ $t('action.attendantName') }}经纪人：<span class="c-title">{{
              detail.broker_name
            }}</span>
          </div>
          <!-- <lb-button
            v-if="detail.broker_id && userInfo.is_admin == 1"
            size="mini"
            plain
            type="danger"
            @click="unbind(detail.id)"
            >解除绑定</lb-button
          > -->
          <lb-button
            size="mini"
            plain
            type="danger"
            @click="editBroker"
            v-hasPermi="`${$route.name}-modifyBroker`"
            >{{ $t('action.modifyBroker') }}</lb-button
          >
        </div>
      </div>
      <el-table
        :data="countData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
      >
        <el-table-column prop="service_price" label="" min-width="130">
          <template slot="header" slot-scope="scope">
            <div style="margin-top: 7px; padding: 0px">
              账户余额
              <lb-tool-tips :padding="2"
                >当前该{{
                  $t('action.attendantName')
                }}账户里待提走的金额</lb-tool-tips
              >
            </div>
          </template>
          <template slot-scope="scope">
            <div class="ml-md">¥{{ scope.row.service_price }}</div>
          </template>
        </el-table-column>
        <!-- <el-table-column
          prop="credit_value"
          label="信用分"
          min-width="100"
          v-if="routesItem.auth.coachcredit"
        ></el-table-column> -->
        <el-table-column prop="true_star" label="" min-width="100">
          <template slot="header" slot-scope="scope">
            <div style="margin-top: 7px; padding: 0">
              评分
              <lb-tool-tips :padding="2">加上虚拟的刷单的评分</lb-tool-tips>
            </div>
          </template>
          <template slot-scope="scope">
            <div class="ml-md">
              {{ scope.row.true_star > 0 ? scope.row.true_star : '暂无评分' }}
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="coach_price" label="" min-width="130">
          <template slot="header" slot-scope="scope">
            <div style="margin-top: 7px; padding: 0">
              本期业绩
              <lb-tool-tips :padding="2">当前周期内的业绩</lb-tool-tips>
            </div>
          </template>
          <template slot-scope="scope">
            <div class="ml-md">¥{{ scope.row.coach_price }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="online_time" label="" min-width="130">
          <template slot="header" slot-scope="scope">
            <div style="margin-top: 7px; padding: 0">
              在线时长
              <lb-tool-tips :padding="2">当前周期内的在线时长</lb-tool-tips>
            </div>
          </template>
          <template slot-scope="scope">
            <div class="ml-md">{{ scope.row.online_time }}小时</div>
          </template>
        </el-table-column>
        <el-table-column prop="coach_time_long" label="" min-width="130">
          <template slot="header" slot-scope="scope">
            <div style="margin-top: 7px; padding: 0">
              服务时长
              <lb-tool-tips :padding="2">当前周期内的服务时长</lb-tool-tips>
            </div>
          </template>
          <template slot-scope="scope">
            <div class="ml-md">{{ scope.row.coach_time_long }}小时</div>
          </template>
        </el-table-column>
        <el-table-column prop="coach_add_balance" label="" min-width="120">
          <template slot="header" slot-scope="scope">
            <div style="margin-top: 7px; padding: 0">
              续单率
              <lb-tool-tips :padding="2">当前周期内的加钟率</lb-tool-tips>
            </div>
          </template>
          <template slot-scope="scope">
            <div class="ml-md">{{ scope.row.coach_add_balance }}%</div>
          </template>
        </el-table-column>
        <el-table-column prop="coach_integral" label="" min-width="100">
          <template slot="header" slot-scope="scope">
            <div style="margin-top: 7px; padding: 0">
              积分
              <lb-tool-tips :padding="2"
                >当前周期内的引导客户充值获得的积分值</lb-tool-tips
              >
            </div>
          </template>
          <template slot-scope="scope">
            <div class="ml-md">{{ scope.row.coach_integral }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="cash_balance" label="" min-width="160">
          <template slot="header" slot-scope="scope">
            <div style="margin-top: 7px; padding: 0">
              本期提成比例
              <lb-tool-tips :padding="2">当前周期内的佣金提成比例</lb-tool-tips>
            </div>
          </template>
          <template slot-scope="scope">
            <div class="ml-md">{{ scope.row.cash_balance }}%</div>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div class="space-lg fill-body" v-if="detail.id"></div>
    <div class="page-main">
      <el-tabs type="card" v-model="activeName">
        <!--@tab-click="handleClick"-->
        <el-tab-pane label="基础信息" name="sub">
          <el-form
            @submit.native.prevent
            :model="subForm"
            ref="subForm"
            :rules="subFormRules"
            label-width="130px"
          >
            <el-form-item label="关联用户" prop="user_id">
              <el-tag
                class="cursor-pointer"
                :type="have_user_id ? 'info' : 'primary'"
                @click="toShowDialog('user')"
                >{{
                  subForm.user_id ? subForm.nickName : '选择关联用户'
                }}</el-tag
              >
            </el-form-item>
            <el-form-item
              :label="`${$t('action.attendantName')}姓名`"
              prop="nickname"
            >
              <el-input
                v-model="subForm.nickname"
                maxlength="10"
                show-word-limit
                :placeholder="`请输入${$t('action.attendantName')}姓名`"
              ></el-input>
            </el-form-item>
            <el-form-item
              :label="`${$t('action.attendantName')}昵称`"
              prop="coach_name"
            >
              <el-input
                v-model="subForm.coach_name"
                maxlength="15"
                show-word-limit
                :placeholder="`请输入${$t('action.attendantName')}昵称`"
              ></el-input>
            </el-form-item>
            <el-form-item label="性别" prop="sex">
              <el-radio-group v-model="subForm.sex">
                <el-radio :label="0">男</el-radio>
                <el-radio :label="1">女</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="生日" prop="birthday">
              <el-date-picker
                v-model="subForm.birthday"
                type="date"
                placeholder="请选择日期"
                value-format="timestamp"
                :picker-options="pickerOptions"
              ></el-date-picker>
            </el-form-item>
            <el-form-item
              label="星座"
              prop="constellation"
              v-if="subForm.birthday"
            >
              {{ subForm.constellation }}
            </el-form-item>
            <el-form-item label="身高" prop="height">
              <el-input placeholder="请输入身高" v-model="subForm.height">
                <template slot="append">cm</template>
              </el-input>
            </el-form-item>
            <el-form-item label="体重" prop="weight">
              <el-input placeholder="请输入体重" v-model="subForm.weight">
                <template slot="append">kg</template>
              </el-input>
            </el-form-item>
            <el-form-item label="手机号" prop="mobile">
              <el-input
                v-model="subForm.mobile"
                placeholder="请输入手机号"
              ></el-input>
            </el-form-item>
            <el-form-item label="个性标签" prop="tag_id">
              <el-select v-model="subForm.tag_id" placeholder="请选择">
                <el-option
                  v-for="item in base_tag"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                >
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="意向工作城市" prop="city_id">
              <el-select v-model="subForm.city_id" placeholder="请选择">
                <el-option
                  v-for="item in base_city"
                  :key="item.id"
                  :label="item.title"
                  :value="item.id"
                >
                </el-option>
              </el-select>
            </el-form-item>
            <!-- <el-form-item
          label="挂靠门店"
          prop="store_id"
          v-if="
            routesItem.auth.store &&
            (!subForm.id || (subForm.admin_id && base_store.length > 0))
          "
        >
          <el-select
            @change="changeStore"
            v-model="subForm.store_id"
            filterable
            clearable
            placeholder="请选择"
          >
            <el-option
              v-for="item in base_store"
              :key="item.id"
              :label="item.title"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item> -->
            <el-form-item label="所在地址" prop="address">
              <el-input
                v-model="subForm.address"
                placeholder="请输入所在地址"
              ></el-input>
            </el-form-item>
            <el-form-item label="经度" prop="lng">
              <el-input
                v-model="subForm.lng"
                placeholder="请输入经度"
              ></el-input>
            </el-form-item>
            <el-form-item label="纬度" prop="lat">
              <el-input
                v-model="subForm.lat"
                placeholder="请输入纬度"
              ></el-input>
              <lb-button
                @click="showMap = true"
                type="primary"
                plain
                size="mini"
                >获取经纬度</lb-button
              >
            </el-form-item>
            <el-form-item label="个人简介" prop="text">
              <el-input
                type="textarea"
                :rows="10"
                maxlength="300"
                resize="none"
                show-word-limit
                placeholder="请输入个人简介"
                v-model="subForm.text"
              ></el-input>
            </el-form-item>

            <el-form-item label="拥有的技能" prop="service" v-if="!detail.id">
              <lb-button
                type="primary"
                icon="el-icon-plus"
                @click="toShowDialog('service')"
                v-if="userInfo.is_admin !== 0"
                >选择技能</lb-button
              >
              <el-table
                :data="subForm.service"
                :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
                class="mt-lg"
                style="width: 100%"
              >
                <el-table-column prop="id" label="ID"></el-table-column>
                <el-table-column prop="cover" label="封面图">
                  <template slot-scope="scope">
                    <lb-image :src="scope.row.cover" />
                  </template>
                </el-table-column>
                <el-table-column
                  prop="title"
                  label="服务名称"
                  min-width="120"
                ></el-table-column>
                <el-table-column prop="service_price" label="服务现价">
                  <template slot-scope="scope">
                    {{ `￥${scope.row.service_price}` }}
                  </template>
                </el-table-column>
                <el-table-column prop="price" label="服务价格" min-width="150">
                  <template slot-scope="scope">
                    <div class="table-operate flex-y-center">
                      <span>￥</span>
                      <el-input-number
                        class="lb-input-number"
                        style="width: 100px"
                        :min="0"
                        :precision="2"
                        :controls="false"
                        placeholder="请输入"
                        v-model="coachPriceList[scope.$index].price"
                        @blur="setPrice(scope.$index)"
                      ></el-input-number>
                    </div>
                  </template>
                </el-table-column>
                <el-table-column prop="time_long" label="服务时长">
                  <template slot-scope="scope">
                    {{ `${scope.row.time_long}分钟` }}
                  </template>
                </el-table-column>
                <el-table-column label="操作" fixed="right">
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

            <el-form-item label="身份证号" prop="id_code">
              <el-input
                v-model="subForm.id_code"
                placeholder="请输入身份证号"
              ></el-input>
            </el-form-item>
            <el-form-item label="身份证照片" prop="id_card">
              <lb-cover
                :fileList="subForm.id_card"
                fileType="image"
                type="more"
                @selectedFiles="getBannerList($event, 'id_card')"
                :fileSize="3"
              ></lb-cover>
              <lb-tool-tips
                >请分别上传身份证人像面、身份证国徽面、手持身份证照片</lb-tool-tips
              >
            </el-form-item>
            <el-form-item label="工作形象照" prop="work_img">
              <lb-cover
                :fileList="subForm.work_img"
                @selectedFiles="getCover($event, 'work_img')"
              ></lb-cover>
              <lb-tool-tips>图片建议尺寸: 334 * 548</lb-tool-tips>
            </el-form-item>
            <el-form-item label="模特照" prop="model_img">
              <lb-cover
                :fileList="subForm.model_img"
                fileType="image"
                type="more"
                tips="750 * 1030"
                @selectedFiles="getBannerList($event, 'model_img')"
                :fileSize="9"
              ></lb-cover>
            </el-form-item>
            <el-form-item label="个人生活照" prop="self_img">
              <lb-cover
                :fileList="subForm.self_img"
                fileType="image"
                type="more"
                tips="750 * 1030，最少上传4张"
                @selectedFiles="getBannerList($event, 'self_img')"
                :fileSize="9"
              ></lb-cover>
            </el-form-item>

            <el-form-item label="个人视频介绍" prop="video">
              <div class="upload-file-warp">
                <input
                  type="text"
                  class="choice-file-input"
                  v-model="subForm.video"
                  placeholder="请选择视频"
                />
                <lb-cover
                  type="button"
                  fileType="video"
                  :fileSize="1"
                  @selectedFiles="getVoice"
                ></lb-cover>
              </div>
            </el-form-item>
            <el-form-item label="虚拟订单量" prop="order_num">
              <el-input
                v-model.number="subForm.order_num"
                placeholder="请输入虚拟订单量"
                maxlength="10"
              ></el-input>
            </el-form-item>
            <el-form-item
              :label="`${$t('action.attendantName')}抽成比例`"
              prop="cash_balance"
              v-if="cash_type == 2"
            >
              <el-input
                v-model.number="subForm.cash_balance"
                placeholder="请输入百分比，取值0-100"
              >
                <template slot="append">%</template>
              </el-input>
            </el-form-item>
            <el-form-item label="是否上班" prop="is_work">
              <el-radio-group v-model="subForm.is_work">
                <el-radio :label="1">上班</el-radio>
                <el-radio :label="0">下班</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item
              label="选择接单时间"
              prop="time"
              v-if="subForm.is_work == 1"
            >
              <el-time-select
                placeholder="开始时间"
                v-model="subForm.start_time"
                :picker-options="{
                  start: '00:00',
                  step: '00:01',
                  end: '24:00'
                }"
                style="width: 150px"
              ></el-time-select>
              <div>-</div>
              <el-time-select
                placeholder="结束时间"
                v-model="subForm.end_time"
                :picker-options="{
                  start: '00:00',
                  step: '00:01',
                  end: '24:00'
                }"
                style="width: 150px"
              ></el-time-select>
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
        </el-tab-pane>
        <el-tab-pane label="已关联技能" name="service" v-if="detail.id">
          <lb-button
            type="primary"
            icon="el-icon-plus"
            @click="toShowDialog('service')"
            v-show="userInfo.is_admin !== 0"
            v-hasPermi="`${$route.name}-selectSkills`"
            >选择技能</lb-button
          >
          <el-table
            :data="subForm.service"
            :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
            class="mt-lg"
            style="width: 100%"
          >
            <el-table-column prop="id" label="ID"></el-table-column>
            <el-table-column prop="cover" label="封面图">
              <template slot-scope="scope">
                <lb-image :src="scope.row.cover" />
              </template>
            </el-table-column>
            <el-table-column
              prop="title"
              label="服务名称"
              min-width="120"
            ></el-table-column>
            <el-table-column prop="service_price" label="服务现价">
              <template slot-scope="scope">
                {{ `￥${scope.row.service_price}` }}
              </template>
            </el-table-column>
            <el-table-column prop="balance" label="服务提成">
              <template slot-scope="scope">
                {{ `${Number(scope.row.balance)}%` }}
              </template>
            </el-table-column>
            <el-table-column prop="price" label="服务价格">
              <template slot-scope="scope">
                <div>{{ `¥${scope.row.price}` }}</div>
                <div
                  @click="toShowDialog('price', scope.row)"
                  class="c-link cursor-pointer"
                  v-hasPermi="`${$route.name}-modifyServicePrice`"
                >
                  <i class="el-icon-edit"></i>
                  修改服务价格
                </div>
              </template>
              <!-- <template slot-scope="scope">
                <div class="table-operate flex-y-center">
                  <span>￥</span>
                  <el-input-number
                    class="lb-input-number"
                    style="width: 100px"
                    :min="0"
                    :precision="2"
                    :controls="false"
                    placeholder="请输入"
                    v-model="coachPriceList[scope.$index].price"
                    @blur="setPrice(scope.$index)"
                  ></el-input-number>
                </div>
              </template> -->
            </el-table-column>
            <el-table-column prop="time_long" label="服务时长">
              <template slot-scope="scope">
                {{ `${scope.row.time_long}分钟` }}
              </template>
            </el-table-column>
            <el-table-column label="操作" fixed="right" min-width="100">
              <template slot-scope="scope">
                <div class="table-operate">
                  <lb-button
                    size="mini"
                    plain
                    type="danger"
                    v-hasPermi="`${$route.name}-disassociate`"
                    @click="confirmDel(scope.row.id, 'del', scope.row.data_id)"
                    >取消关联</lb-button
                  >
                </div>
              </template>
            </el-table-column>
          </el-table>
        </el-tab-pane>
      </el-tabs>
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
          :data="tableData.user"
          ref="singleTable"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
          highlight-current-row
          @current-change="handleSelectionChange($event, 'user')"
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
          <el-button type="primary" @click="handleDialogConfirm('user')"
            >确 定</el-button
          >
        </span>
      </el-dialog>
      <!--选择技能-->
      <el-dialog
        title="选择技能"
        :visible.sync="showDialog.service"
        width="800px"
        center
      >
        <el-form
          :inline="true"
          :model="searchForm.service"
          ref="serviceForm"
          label-width="70px"
        >
          <el-form-item label="输入查询" prop="name">
            <el-input
              v-model="searchForm.service.name"
              placeholder="请输入服务名称"
            ></el-input>
          </el-form-item>
          <el-form-item>
            <lb-button
              size="medium"
              type="primary"
              icon="el-icon-search"
              style="margin-right: 5px"
              @click="getTableDataList(1, 'service')"
              >{{ $t('action.search') }}</lb-button
            >
            <lb-button
              size="medium"
              icon="el-icon-refresh-left"
              style="margin-right: 5px"
              @click="resetForm('service')"
              >{{ $t('action.reset') }}</lb-button
            >
          </el-form-item>
        </el-form>
        <el-table
          :data="tableData.service"
          ref="multipleTable"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
          @selection-change="handleSelectionChange($event, 'service')"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column prop="id" label="ID"></el-table-column>
          <el-table-column prop="cover" label="封面图">
            <template slot-scope="scope">
              <lb-image :src="scope.row.cover" />
            </template>
          </el-table-column>
          <el-table-column
            prop="title"
            label="服务名称"
            min-width="120"
          ></el-table-column>
          <el-table-column prop="service_price" label="服务价格">
            <template slot-scope="scope">
              {{ `¥${scope.row.service_price}` }}
            </template>
          </el-table-column>
          <el-table-column
            prop="price"
            :label="`${$t('action.attendantName')}服务价格`"
            width="150"
          >
            <template slot-scope="scope">
              <div class="table-operate flex-y-center">
                <el-input-number
                  class="lb-input-number"
                  style="width: 100px"
                  :min="0"
                  :precision="2"
                  :controls="false"
                  placeholder="请输入"
                  v-model="scope.row.price"
                ></el-input-number>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="time_long" label="服务时长">
            <template slot-scope="scope">
              {{ `${scope.row.time_long}分钟` }}
            </template>
          </el-table-column>
        </el-table>
        <lb-page
          :batch="false"
          :page="searchForm.service.page"
          :pageSize="searchForm.service.limit"
          :total="total.service"
          @handleSizeChange="handleSizeChange($event, 'service')"
          @handleCurrentChange="handleCurrentChange($event, 'service')"
        >
        </lb-page>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.service = false">取 消</el-button>
          <el-button type="primary" @click="handleDialogConfirm('service')"
            >确 定</el-button
          >
        </span>
      </el-dialog>
      <!-- 修改服务价格 -->
      <el-dialog
        title="修改服务价格"
        :visible.sync="showDialog.price"
        width="600px"
        center
      >
        <el-form
          :model="priceForm"
          ref="priceForm"
          :rules="priceFormRules"
          label-width="140px"
        >
          <div class="flex-center" style="margin: 0 0 10px 48px">
            <lb-image
              :src="priceForm.cover"
              :isLook="true"
              style="width: 60px; height: 60px; object-fit: cover"
            />
            <div class="flex-1 ml-md f-caption c-caption">
              <div class="f-paragraph c-title text-bold">
                {{ priceForm.title }}
              </div>
              <div class="mt-sm">
                服务价格：<span class="c-warning"
                  >¥{{ priceForm.service_price }}</span
                >
              </div>
            </div>
          </div>
          <el-form-item
            :label="$t('action.attendantName') + '服务价格'"
            prop="price"
          >
            <el-input
              v-model="priceForm.price"
              :placeholder="'请输入' + $t('action.attendantName') + '服务价格'"
            ></el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.price = false">取 消</el-button>
          <el-button
            type="primary"
            @click="submitFormInfo('price')"
            v-preventReClick
            >确 定</el-button
          >
        </span>
      </el-dialog>
      <el-dialog
        title="修改经纪人"
        :visible.sync="showEditBroker"
        width="500px"
        center
      >
        <el-form
          @submit.native.prevent
          :model="brokerForm"
          label-width="150px"
          size="small"
        >
          <el-form-item label="原经纪人：" prop="balance">
            <div>{{ detail.broker_name }}</div>
          </el-form-item>
          <el-form-item label="修改所属经纪人：" prop="balance1">
            <el-tag
              :type="brokerForm.id == 0 ? 'danger' : ''"
              @click="toShowDialog('broker')"
              :closable="brokerForm.id > 0"
              @close="handleClose"
              >{{ brokerForm.id ? brokerForm.name : `选择经纪人` }}</el-tag
            >
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showEditBroker = false">{{
            $t('action.cancel')
          }}</el-button>
          <el-button type="primary" @click="brokerFormInfo" v-preventReClick>{{
            $t('action.comfirm')
          }}</el-button>
        </span>
      </el-dialog>
      <!--选择经纪人-->
      <el-dialog
        title="选择经纪人"
        :visible.sync="showDialog.broker"
        width="800px"
        center
      >
        <el-form
          :inline="true"
          :model="searchForm.broker"
          ref="brokerForm"
          label-width="70px"
        >
          <el-form-item label="输入查询" prop="name">
            <el-input
              v-model="searchForm.broker.name"
              placeholder="请输入经纪人姓名/手机号"
            ></el-input>
          </el-form-item>
          <el-form-item>
            <lb-button
              size="medium"
              type="primary"
              icon="el-icon-search"
              style="margin-right: 5px"
              @click="getTableDataList(1, 'broker')"
              >{{ $t('action.search') }}</lb-button
            >
            <lb-button
              size="medium"
              icon="el-icon-refresh-left"
              style="margin-right: 5px"
              @click="resetForm('broker')"
              >{{ $t('action.reset') }}</lb-button
            >
          </el-form-item>
        </el-form>
        <el-table
          :data="tableData.broker"
          ref="multipleTable"
          :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
          tooltip-effect="dark"
          style="width: 100%"
          highlight-current-row
          @current-change="handleSelectionChange($event, 'broker')"
        >
          <el-table-column prop="id" label="ID"></el-table-column>
          <el-table-column prop="user_id" label="用户ID"></el-table-column>
          <el-table-column prop="avatarUrl" label="头像">
            <template slot-scope="scope">
              <lb-image :src="scope.row.avatarUrl" />
            </template>
          </el-table-column>
          <el-table-column prop="name" label="姓名"></el-table-column>
          <el-table-column prop="mobile" label="手机号"></el-table-column>
          <el-table-column prop="create_time" label="入驻时间">
            <template slot-scope="scope">
              <p>{{ scope.row.create_time | handleTime(1) }}</p>
              <p>{{ scope.row.create_time | handleTime(2) }}</p>
            </template>
          </el-table-column>
        </el-table>
        <lb-page
          :batch="false"
          :page="searchForm.broker.page"
          :pageSize="searchForm.broker.limit"
          :total="total.broker"
          @handleSizeChange="handleSizeChange($event, 'broker')"
          @handleCurrentChange="handleCurrentChange($event, 'broker')"
        >
        </lb-page>
        <span slot="footer" class="dialog-footer">
          <el-button @click="showDialog.broker = false">取 消</el-button>
          <el-button type="primary" @click="handleDialogConfirm('broker')"
            >确 定</el-button
          >
        </span>
      </el-dialog>
      <lb-map
        :dialogVisible.sync="showMap"
        @selectedLatLng="getLatLng"
      ></lb-map>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
export default {
  data () {
    let validateTime = (rule, value, callback) => {
      let { start_time: start, end_time: end } = this.subForm
      if (!start || !end) {
        callback(new Error(!start ? `请选择开始时间` : `请选择结束时间`))
      } else {
        callback()
      }
    }
    return {
      pickerOptions: {
        disabledDate (time) {
          return time.getTime() > (moment(moment(Date.now()).format('YYYY-MM-DD')).unix() + 24 * 3600 - 1) * 1000
        }
      },
      base_city: [],
      base_store: [],
      id: '',
      navTitle: '',
      showMap: false,
      have_user_id: false,
      subForm: {
        id: 0,
        admin_id: 0,
        user_id: 0,
        nickName: '',
        nickname: '',
        coach_name: '', // 姓名
        mobile: '', // 手机号
        sex: 0, // 性别
        birthday: '',
        constellation: '', // 星座
        height: '',
        weight: '',
        city_id: '', // 城市id
        store_id: '', // 门店id
        lng: '',
        lat: '',
        address: '', // 所在地址
        text: '', // 个人简介
        service: [],
        id_code: '', // 身份证号
        id_card: [], // 身份证
        work_img: [], // 工作照
        self_img: [], // 生活照
        model_img: [], // 模特照
        video: '',
        status: 2,
        order_num: 0,
        tag_id: '',
        cash_balance: 0,
        is_work: 1,
        start_time: '',
        end_time: ''

      },
      subFormRules: {
        coach_name: { required: true, validator: this.$reg.isNotNull, text: this.$t('action.attendantName') + '昵称', reg_type: 2, trigger: 'blur' },
        nickname: { required: true, validator: this.$reg.isNotNull, text: this.$t('action.attendantName') + '姓名', reg_type: 2, trigger: 'blur' },
        mobile: { required: true, validator: this.$reg.isTel, text: '手机号', reg_type: 2, trigger: 'blur' },
        sex: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        birthday: { required: true, type: 'number', message: '请选择日期', trigger: 'blur' },
        height: { required: true, validator: this.$reg.isNum, reg_type: 2, text: '身高', trigger: 'blur' },
        weight: { required: true, validator: this.$reg.isNum, reg_type: 2, text: '体重', trigger: 'blur' },
        city_id: { required: true, type: 'number', message: '请选择意向工作城市', trigger: 'blur' },
        address: { required: true, type: 'string', message: '请输入地址', trigger: 'blur' },
        lng: { required: true, validator: this.$reg.isLng, trigger: 'blur' },
        lat: { required: true, validator: this.$reg.isLat, trigger: 'blur' },
        id_code: { required: true, validator: this.$reg.isIdCard, trigger: 'blur' },
        text: { required: true, type: 'string', message: '请输入个人简介', trigger: 'blur' },
        service: { required: true, type: 'array', message: '请选择所拥有的技能', trigger: 'blur' },
        id_card: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        work_img: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        self_img: { required: true, type: 'array', message: '请上传图片', trigger: 'blur' },
        order_num: { required: true, validator: this.$reg.isNum, trigger: 'blur' },
        cash_balance: { required: true, validator: this.$reg.isPercent, type: 'number', message: '请输入0至100的整数', trigger: 'blur' },
        is_work: { required: true, type: 'number', message: '请选择', trigger: 'blur' },
        time: { required: true, validator: validateTime, trigger: 'blur' },
        video: { required: true, type: 'string', message: '请上传个人视频介绍', trigger: 'blur' }
      },
      searchForm: {
        user: {
          page: 1,
          limit: 10,
          nickName: ''
        },
        service: {
          page: 1,
          limit: 10,
          name: ''
        },
        broker: {
          page: 1,
          limit: 10,
          name: '',
          status: 2
        }
      },
      total: { user: 0, service: 0, broker: 0 },
      loading: { user: false, service: false, broker: false },
      tableData: { user: [], service: [], broker: [] },
      showDialog: { user: false, service: false, price: false, broker: false },
      currentRow: {},
      multipleSelection: [],
      cash_type: '',
      coachPriceList: [],
      userInfo: {},
      detail: {},
      authStatusText: {
        0: {
          type: 'info',
          text: '未认证'
        },
        1: {
          type: '',
          text: '认证中'
        },
        2: {
          type: 'success',
          text: '已认证'
        },
        3: {
          type: 'warning',
          text: '取消认证'
        }
      },
      statusText: {
        1: {
          type: 'info',
          text: '申请中'
        },
        2: {
          type: '',
          text: '已授权'
        },
        3: {
          type: 'danger',
          text: '取消授权'
        },
        4: {
          type: 'danger',
          text: '已驳回'
        }
      },
      activeName: 'sub',
      countData: [],
      priceForm: {
        price: ''
      },
      priceFormRules: {
        price: { required: true, validator: this.$reg.isMoney, text: this.$t('action.attendantName') + '服务价格', trigger: 'blur' }
      },
      showEditBroker: false,
      choiceDialog: false,
      brokerForm: {
        name: '',
        id: ''
      },
      base_tag: []
    }
  },
  async created () {
    this.userInfo = JSON.parse(window.localStorage.getItem('massage_userInfo'))
    if (this.userInfo.is_admin !== 1) {
      this.subFormRules.service.required = false
    }
    let { id } = this.$route.query
    if (id) {
      this.subForm.id = id
      await this.getDetail(id)
    }
    this.navTitle = this.$t(id ? 'menu.TechnicianEdit' : 'menu.TechnicianAdd')
    this.getBaseInfo()
    this.getConfigInfo()
  },
  watch: {
    'subForm.birthday' (newValue, oldValue) {
      if (newValue) {
        let month = moment(newValue).format('MM')
        let day = moment(newValue).format('DD')
        this.subForm.constellation = this.$util.getAstro(month, day)
      }
    }
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    },
  },
  computed: {
    ...mapState({
      routesItem: state => state.routes
    })
  },
  methods: {
    async getConfigInfo () {
      let { code, data } = await this.$api.system.configInfo()
      if (code !== 200) return
      this.cash_type = data.cash_type
      if (data.video_limit === 0) {
        this.subFormRules.video.required = false
      }
    },
    async getBaseInfo () {
      let { admin_id: aid = 0 } = this.subForm
      let [city, store, tag] = await Promise.all([this.$api.system.getCity(), this.$api.technician.storeSelect({ admin_id: aid }), this.$api.technician.coachTagList()])
      this.base_city = city.data
      this.base_store = store.data
      this.base_tag = tag.data
    },
    getCover (img, key) {
      this.subForm[key] = img
    },
    getBannerList (imgs, key) {
      this.subForm[key].push(...imgs)
    },
    /**
     * @method 获取经纬度
     */
    getLatLng (latLng) {
      this.subForm.lat = latLng.lat
      this.subForm.lng = latLng.lng
      if (latLng.address) {
        this.subForm.address = latLng.address
      }
    },
    /**
     * @name: 详情
     * @param {*} id
     */
    async getDetail (id) {
      let [info, count] = await Promise.all([this.$api.technician.coachInfo({ id }), this.$api.technician.coachCashData({ id })])
      let { code, data } = info
      if (code !== 200) return
      let detail = JSON.parse(JSON.stringify(data))
      detail.sh_text = detail.status === 4 ? detail.sh_text && detail.sh_text.length > 0 ? detail.sh_text.replace(/\n/g, '<br>') : '没有填写原因哦' : ''
      this.detail = detail
      this.countData = [count.data]
      data.nickName = data.user_id
        ? data.nickName || `用户ID ${data.user_id}`
        : ''
      data.work_img = [{ url: data.work_img }]
      data.tag_id = data.tag_id || ''
      let arr = ['id_card', 'self_img', 'model_img']
      arr.map((item) => {
        data[item] = data[item] ? data[item].map((aitem) => {
          return { url: aitem }
        }) : []
      })
      data.birthday = data.birthday * 1000
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      this.have_user_id = data.id && data.user_id
      let coachPriceList = []
      data.service.forEach(item => {
        coachPriceList.push({ ser_id: item.id, price: item.price || 0 })
      })
      this.coachPriceList = coachPriceList
    },
    getVoice (file) {
      let len = file.length - 1
      this.subForm.video = file[len].url
    },
    changeStore (sid) {
      let arr = this.base_store.filter(item => {
        return item.id === sid
      })
      this.subForm.admin_id = arr[0].admin_id
    },
    async toShowDialog (key, data) {
      try {
        if (key === 'user') {
          let { have_user_id: have } = this
          if (have) return
          this.searchForm[key].nickName = ''
        } else {
          this.searchForm[key].name = ''
        }
      } catch (error) { }
      if (key !== 'price') {
        await this.getTableDataList(1, key)
      } else {
        this.priceForm = JSON.parse(JSON.stringify(data))
      }
      this.showDialog[key] = !this.showDialog[key]
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
     * @method 列表
     */
    async getTableDataList (flag, key) {
      if (flag) this.searchForm[key].page = 1
      this.loading[key] = true
      let searchForm = JSON.parse(JSON.stringify(this.searchForm[key]))

      let methodArr = {
        user: { methodKey: 'technician', methodModel: 'coachUserList' },
        service: { methodKey: 'service', methodModel: 'serviceList' },
        broker: { methodKey: 'economy', methodModel: 'getList' }
      }
      let { methodKey, methodModel } = methodArr[key]
      let { code, data } = await this.$api[methodKey][methodModel](searchForm)
      this.loading[key] = false
      if (code !== 200) return
      if (key === 'service') {
        data.data.forEach(item => {
          item.service_price = JSON.parse(JSON.stringify(item.price))
        })
      }
      this.tableData[key] = data.data
      this.total[key] = data.total
    },
    handleSelectionChange (val, key) {
      if (key === 'user') {
        val = JSON.parse(JSON.stringify(val))
        let { id, nickName } = val
        val.nickName = nickName || `用户ID ${id}`
        this.currentRow = val
        return
      } else if (key === 'broker') {
        this.currentRow = val
        return
      }
      this.multipleSelection = val
    },
    handleClose () {
      this.brokerForm.name = ''
      this.brokerForm.id = 0
    },
    async handleDialogConfirm (key) {
      if (key === 'user') {
        if (this.currentRow === null || !this.currentRow.id) {
          this.$message.error(`请选择用户`)
          return
        }
        let { id = 0, nickName = '' } = this.currentRow
        this.subForm.user_id = id
        this.subForm.nickName = nickName
      } else if (key === 'broker') {
        if (this.currentRow === null || !this.currentRow.id) {
          this.$message.error(`请选择经纪人`)
          return
        }
        let { id = 0, name = '' } = this.currentRow
        this.brokerForm.name = name
        this.brokerForm.id = id
      } else {
        if (this.multipleSelection.length == 0) {
          this.$message.error(`请选择技能`)
          return
        }
        let service = JSON.parse(JSON.stringify(this.subForm.service))
        let arr = []
        let arr1 = service.length > 0 ? service.map(item => { return item.id }) : []
        this.multipleSelection.map(item => {
          arr.push({ ser_id: item.id, price: item.price })
          if (arr1.includes(item.id)) return
          service.push(item)
        })

        if (this.subForm.id) {
          await this.$api.technician.coachServiceAdd({ id: this.subForm.id, service: arr })
          // service.forEach(item => {
          //   arr.forEach(a => {
          //     if (item.id == a.ser_id) {
          //       item.price = a.price
          //     }
          //   })
          // })
          this.getDetail(this.subForm.id)
          this.$message.success(this.$t('tips.successOper'))
        }

        this.subForm.service = service
        let coachPriceList = []
        service.forEach(item => {
          coachPriceList.push({ ser_id: item.id, price: item.price || 0 })
        })
        this.coachPriceList = coachPriceList
      }
      this.showDialog[key] = false
    },
    editBroker () {
      this.brokerForm.id = this.detail.broker_id
      this.brokerForm.name = this.detail.broker_name
      this.showEditBroker = true
    },
    brokerFormInfo () {
      let {
        id: broker_id = 0
      } = this.brokerForm
      this.$api.storeshop.cancelBroker({ id: this.detail.id, broker_id }).then(res => {
        this.$message.success(this.$t('tips.successOper'))
        this.getDetail(this.subForm.id)
        // this.$router.back(-1)
        this.showEditBroker = false
      })
    },
    async confirmDel (id, type, dataid) {
      let index = this.subForm.service.findIndex(item => {
        return item.id === id
      })
      let flag = true
      if (type) {
        await this.$confirm('确认要取消关联的服务项目吗？', this.$t('tips.reminder'), {
          confirmButtonText: this.$t('action.comfirm'),
          cancelButtonText: this.$t('action.cancel'),
          type: 'warning'
        }).then(async () => {
          await this.$api.technician.coachServiceUpdate({
            data_id: dataid,
            status: -1,
            coach_id: this.subForm.id
          }).then(res => {
            if (res.code !== 200) return
            this.$message.success(this.$t('tips.successOper'))
          })
        }).catch((res) => {
          flag = false
        })
      }
      if (flag) {
        this.subForm.service.splice(index, 1)
        let coachPriceList = []
        this.subForm.service.forEach(item => {
          coachPriceList.push({ ser_id: item.id, price: item.price || 0 })
        })
        this.coachPriceList = coachPriceList
      }
    },
    /**
     * @name: 新增/编辑
     * @param {*}
     */
    submitForm () {
      let flag = true
      this.$refs['subForm'].validate((valid) => {
        if (!valid) flag = false
      })
      console.log(flag)
      if (flag) {
        let subForm = JSON.parse(JSON.stringify(this.subForm))
        if (subForm.id_card.length < 2) {
          this.$message.error(`身份证照片请分别上传身份证人像面、身份证国徽面`)
          return
        }
        if (subForm.self_img.length < 4) {
          this.$message.error(`个人生活照最少上传4张`)
          return
        }
        let arr = ['id_card', 'self_img', 'model_img']
        arr.map((item) => {
          subForm[item] = subForm[item].length > 0 && subForm[item].map((aitem) => {
            return aitem.url
          })
        })
        subForm.work_img = subForm.work_img[0].url
        // let ids = subForm.service.map(item => {
        //   return item.id
        // })
        subForm.service = this.coachPriceList
        subForm.birthday = subForm.birthday / 1000
        delete subForm.nickName
        if (subForm.id) {
          delete subForm.admin_id
        }
        let edit = 'coachDataUpdate'
        let add = 'coachAdd'
        if (this.userInfo.is_admin !== 1 && this.userInfo.is_admin !== 2) { // 1平台
          edit = 'coachUpdateAdmin'
        }
        if (subForm.id) {
          delete subForm.service
        }
        let methodModel = subForm.id ? edit : add
        this.$api.technician[methodModel](subForm).then((res) => {
          if (res.code === 200) {
            this.$message.success(this.$t(subForm.id ? 'tips.successRev' : 'tips.successSub'))
            this.$router.back(-1)
          }
        })
      }
    },
    setPrice (index) {
      if (!this.coachPriceList[index].price) {
        this.coachPriceList[index].price = 0
      }
    },
    unbind (id) {
      this.$confirm((`请确认是否解除绑定`), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.$api.storeshop.cancelBroker({ id }).then(res => {
          this.$message.success(this.$t('tips.successOper'))
          this.getDetail(this.subForm.id)
          // this.$router.back(-1)
        })
      }).catch(() => { })
    },
    submitFormInfo (type) {
      console.log(this.priceForm)
      let flag = true
      this.$refs['priceForm'].validate((valid) => {
        if (!valid) flag = false
      })
      if (flag) {
        let {
          data_id: id = 0,
          price = 0
        } = this.priceForm
        this.$api.technician.coachServiceUpdate({ data_id: id, price, coach_id: this.subForm.id }).then(res => {
          this.$message.success(this.$t('tips.successOper'))
          this.subForm.service.forEach(item => {
            if (item.data_id === id) {
              item.price = price
            }
          })
          this.showDialog[type] = false
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-banner-edit {
  width: 100%;
  .el-form {
    width: 100%;
    .el-select,
    .el-input-number,
    .el-input {
      width: 300px;
    }
    .el-textarea {
      width: 600px;
    }
    .el-tag {
      cursor: pointer;
    }
  }
  .work-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
  }
}
</style>
