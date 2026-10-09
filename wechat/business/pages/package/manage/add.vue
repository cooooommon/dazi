<template>
	<view class="add-pages" v-if="isLoad">
		<view class="h-100 text-bold pl-lg flex-y-center f-title">基本信息</view>
		<view class="fill-base pl-lg pr-lg add-box">
			<view class="flex-y-center">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">套餐名称</text>
			</view>
			<view class="radius-16 fill-body mt-big flex-between pr-lg">
				<input v-model="param.name" class="pl-lg pr-lg f-mini-title h-110 flex-1" maxlength="10" type="text"
					placeholder="标题示例: 鸡尾酒单人套餐" />
				<text class="f-mini-title">{{param.name.length > 10 ? 10 : param.name.length }}/10</text>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">套餐封面</text>
				<text class="f-mini-title c-text pl-md">建议尺寸：340*234</text>
			</view>
			<view class="mt-big">
				<upload @upload="imgUpload" :imagelist="param.cover" imgtype="cover" @del="imgUpload" text="上传图片"
					:imgsize="1"></upload>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">详情图</text>
				<text class="f-mini-title c-text pl-md">建议尺寸：750*527</text>
			</view>
			<view class="mt-big">
				<upload @upload="imgUpload" :imagelist="param.imgs" imgtype="imgs" @del="imgUpload" text="上传图片"
					:imgsize="9"></upload>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">副标题</text>
			</view>
			<view class="radius-16 fill-body mt-big flex-between pr-lg">
				<input v-model="param.sub_name" class="pl-lg pr-lg f-mini-title h-110 flex-1" maxlength="12" type="text"
					placeholder="请输入副标题" />
				<text class="f-mini-title">{{param.sub_name.length > 12 ? 12 : param.sub_name.length }}/12</text>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">现价</text>
			</view>
			<view class="radius-16 fill-body mt-big flex-between pr-lg">
				<input v-model="param.price" class="pl-lg pr-lg f-mini-title h-110 flex-1" maxlength="15" type="text"
					placeholder="请输入商品价格" />
				<text class="f-mini-title">元</text>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">划线价</text>
			</view>
			<view class="radius-16 fill-body mt-big flex-between pr-lg">
				<input v-model="param.init_price" class="pl-lg pr-lg f-mini-title h-110 flex-1" maxlength="15"
					type="text" placeholder="请输入划线价" />
				<text class="f-mini-title">元</text>
			</view>
			<block v-if="configInfo.plugAuth.integral">
				<view class="flex-between mt-50 pb-sm">
					<view class="flex-y-center">
						<i class="iconfont icon-required c-warning"></i>
						<text class="f-mini-title text-bold">积分抵扣</text>
					</view>
					<view class="flex-center">
						<view class="flex-center" style="padding-left: 50rpx;" @tap="hannelIntegral(item.id)"
						v-for="(item,index) in [{title: '开启',id: 1},{title: '关闭',id: 0}]" :key="index">
							<i class="iconfont" style="font-size: 38rpx;" :style="{color:param.is_integral == item.id?primaryColor: '#222' }"
							:class="[{'icon-radio-fill': param.is_integral == item.id},{'icon-xuanze': param.is_integral != item.id}]"></i>
							<text class="f-mini-title pl-sm">{{item.title}}</text>
						</view>
					</view>
				</view>
				<view class="flex-between f-mini-title pt-lg" v-if="param.is_integral">
					<view class="flex-center">
						<view class="radius-16 fill-body mr-sm" style="width: 230rpx;">
							<input v-model="param.integral" class="pl-lg pr-lg f-mini-title h-110 flex-1"
								type="text" placeholder="请输入积分" />
						</view>
						积分抵扣
					</view>
					<view class="flex-center">
						<view class="radius-16 fill-body mr-sm" style="width: 230rpx;">
							<input v-model="param.integral_to_money" class="pl-lg pr-lg f-mini-title h-110 flex-1"
								type="text" placeholder="请输入金额" />
						</view>
						元
					</view>
				</view>
			</block>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">虚拟销量</text>
			</view> 
			<view class="radius-16 fill-body mt-big flex-between">
				<input v-model="param.sale" class="pl-lg pr-lg f-mini-title h-110 flex-1" maxlength="15" type="text"
					placeholder="请输入虚拟销量" />
			</view>
		</view>
		<view class="h-100 text-bold pl-lg flex-y-center f-title mt-lg">团购详情</view>
		<view class="fill-base pl-lg pr-lg add-box">
			<view class="flex-between">
				<view class="flex-y-center">
					<i class="iconfont icon-required c-warning"></i>
					<text class="f-mini-title text-bold">创建套餐价格</text>
				</view>
				<view class="flex-center" @tap.stop="addSpecifications">
					<i class="iconfont icontianjia" :style="{color: primaryColor,fontSize: '20px'}"></i>
					<text class="f-paragraph pl-sm text-bold" :style="{color: primaryColor}">添加规格</text>
				</view>
			</view>
			<blcok v-for="(item,index) in skuList" :key="index">
				<view class="mt-45 flex-between">
					<text class="f-paragraph">规则名称: {{item.name}}</text>
					<view class="flex-between">
						<text class="f-mini-title pr-md c-success" @tap="updateSku('edit',index)">编辑</text>
						<text class="f-mini-title pl-md c-warning" @tap="updateSku('del',index)">删除</text>
					</view>
				</view>
				<blcok v-for="(citem,cindex) in item.price" :key="cindex">
					<view class="flex-between mt-lg">
						<view class="flex-1">
							<view class="flex-between">
								<view class="flex-y-center w-130">
									<i class="iconfont icon-required c-warning"></i>
									<text class="f-mini-title text-bold">规格值</text>
								</view>
								<view class="fill-body radius-16 flex-1">
									<input v-model="citem.name" class="h-110 flex-1 pl-lg pr-lg text-center" type="text"
										placeholder="示例:A酒瓶" />
								</view>
							</view>
							<view class="flex-between mt-lg">
								<view class="flex-y-center w-130">
									<i class="iconfont icon-required c-warning"></i>
									<text class="f-mini-title text-bold">数量</text>
								</view>
								<view class="fill-body radius-16 flex-1 flex-center pl-lg">
									<input v-model="citem.num" class="h-110 flex-1 pl-lg text-center" maxlength="13"
										type="number" placeholder="请输入" />
									<text class="pr-lg f-mini-title">份</text>
								</view>
							</view>
							<view class="flex-between mt-lg">
								<view class="flex-y-center w-130">
									<i class="iconfont icon-required c-warning"></i>
									<text class="f-mini-title text-bold">价格</text>
								</view>
								<view class="fill-body radius-16 flex-1 flex-center pl-lg">
									<input v-model="citem.price" class="h-110 flex-1 pl-lg text-center" maxlength="13"
										type="number" placeholder="请输入" />
									<text class="pr-lg f-mini-title">元</text>
								</view>
							</view>
						</view>
						<view class="pl-md" style="width: 60rpx;">
							<i class="iconfont icon-jian-fill c-warning" style="font-size: 40rpx;"
								v-if="item.price.length > 1" @tap="delSku(index,cindex)"></i>
						</view>
					</view>
				</blcok>
				<view class="flex-between" style="margin-top: 80rpx;">
					<view class="flex-y-center"></view>
					<view class="flex-center" @tap="addSku(index)">
						<i class="iconfont icontianjia" :style="{color: primaryColor,fontSize: '20px'}"></i>
						<text class="f-paragraph pl-sm text-bold" :style="{color: primaryColor}">添加规格值</text>
					</view>
				</view>
			</blcok>
		</view>
		<view class="fill-base pl-lg pr-lg h-120 flex-between mt-md"
			@tap="$util.goUrl({url: `/business/pages/package/manage/add-imageText`})">
			<text class="text-bold f-mini-title">图文详情</text>
			<i class="iconfont icongengduo" style="font-size: 13px;"></i>
		</view>
		<view class="h-100 text-bold pl-lg flex-y-center f-title mt-lg">购买须知</view>
		<view class="fill-base pl-lg pr-lg purchase-box">
			<view class="flex-y-center">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">有效期</text>
			</view>
			<view class="fill-body radius-16 flex-between pl-lg h-110 mt-big pr-lg" @tap="onChangePopup('term_type')">
				<view class="f-mini-title text-bold" :class="[{'c-icontext':!param.term_type }]">
					{{param.term_type ? termType[param.term_type] : `去设置`}}
				</view>
				<i class="iconfont icongengduo c-paragraph" style="font-size: 13px;"></i>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">使用时间</text>
			</view>
			<view class="fill-body radius-16 flex-between pl-lg h-110 mt-big pr-lg" @tap="onChangePopup('use_type')">
				<view class="f-mini-title text-bold" :class="[{'c-icontext': !param.use_type}]">
					{{useType[param.use_type] || `去设置`}}
				</view>
				<i class="iconfont icongengduo c-paragraph" style="font-size: 13px;"></i>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">预约信息</text>
			</view>
			<view class="fill-body radius-16 flex-between pl-lg h-110 mt-big pr-lg"
				@tap="onChangePopup('reservation_type')">
				<view class="f-mini-title text-bold" :class="[{'c-icontext': !param.reservation_type}]">
					{{param.reservation_type ? ( param.reservation_type == 1 ? `无需预约` : `需提前${param.reservation_day}预约`) : `去设置`}}
				</view>
				<i class="iconfont icongengduo c-paragraph" style="font-size: 13px;"></i>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">保障</text>
			</view>
			<view class="fill-body radius-16 flex-between pl-lg h-110 mt-big pr-lg" @tap="onChangePopup('ensure')">
				<view class=" f-mini-title text-bold" :class="[{ 'c-icontext':!param.ensure}]">
					{{ensureType[param.ensure] || `去设置`}}
				</view>
				<i class="iconfont icongengduo c-paragraph" style="font-size: 13px;"></i>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">使用规则</text>
			</view>
			<view class="pd-lg flex-center fill-body radius-16 mt-big">
				<textarea v-model="param.rule_text" class="flex-1 f-mini-title" name="" id="" cols="30" rows="10"
					maxlength="10000" placeholder="请输入规则"></textarea>
			</view>
			<view class="flex-y-center mt-50">
				<i class="iconfont icon-required c-warning"></i>
				<text class="f-mini-title text-bold">是否上架</text>
			</view>
			<view class="fill-body radius-16 flex-between pl-lg h-110 mt-big pr-lg" @tap="onChangePopup('status')">
				<view class=" f-mini-title text-bold" :class="[{ 'c-icontext': param.status == ''}]">
					{{ statusType[param.status] || `去设置`}}
				</view>
				<i class="iconfont icongengduo c-paragraph" style="font-size: 13px;"></i>
			</view>
		</view>

		<uni-popup ref="content_item" type="bottom" :maskClick="true">
			<view class="content-popup fill-base">
				<view class="pl-lg pr-lg">
					<view class="flex-between pb-lg b-1px-b">
						<text class="f-mini-title text-bold">{{popupTitle[popupType]}}</text>
						<text class="c-paragraph f-paragraph" @tap="$refs.content_item.close()">取消</text>
					</view>
					<block v-if="popupType == 'ensure'">
						<view class="ensure-box">
							<view class="mt-50" v-for="(item,index) in ensureList" :key="index">
								<view class="flex-between pb-md" @tap="popupChange(popupType, item.id)">
									<text class="f-mini-title text-bold">{{item.name}}</text>
									<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
										:style="{color: primaryColor}" v-if="param.ensure == item.id"></i>
									<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
								</view>
								<view class="f-desc c-caption">{{item.tips}}</view>
							</view>
						</view>
					</block>
					<block v-if="popupType == 'term_type'">
						<view class="pt-lg pb-lg">
							<view class="flex-between pt-sm pb-lg" @tap="popupChange(popupType, 1)">
								<text class="f-paragraph text-bold">指定日期</text>
								<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
									:style="{color: primaryColor}" v-if="param.term_type == 1"></i>
								<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
							</view>
							<view class="flex-between fill-body radius-10 h-90">
								<text class="f-paragraph flex-1 flex-center h-90"
									@tap="dateChange('showStartDate')">{{param.term_start_time||`请选择开始时间`}}</text>
								<text class="c-caption">——</text>
								<text class="f-paragraph flex-1 flex-center h-90"
									@tap="dateChange('showEndDate')">{{param.term_end_time||`请选择结束时间`}}</text>
							</view>
							<view class="flex-between pt-md" @tap="popupChange(popupType, 2)">
								<text class="f-paragraph text-bold">有效天数</text>
								<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
									:style="{color: primaryColor}" v-if="param.term_type == 2"></i>
								<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
							</view>
							<view class="pt-lg f-desc pb-md flex-y-center">
								·自购买当日起（<input v-model="param.days" class="text-center" type="number" placeholder="请输入"
									style="width: 120rpx;" />）天内可用
							</view>
							<view class="f-desc c-caption">
								<view class="">效期按自然天计算。</view>
								举例：如设置套餐当日起30天内可用，用户在5月18日14:00时领取优惠券，则该团购套餐的可用时间为5月18日的14:00:00至6月18日的14:00
								<view class="c-warning">注意：时间按自然天来算，不是月</view>
							</view>
						</view>
					</block>
					<view v-if="popupType == 'use_type'" class="pt-lg">
						<view class="flex-between pt-sm pb-lg" @tap="popupChange(popupType, 1)">
							<text class="f-paragraph text-bold">与门店营业时间一致</text>
							<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
								:style="{color: primaryColor}" v-if="param.use_type == 1"></i>
							<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
						</view>
						<view class="flex-between pt-md" @tap="popupChange(popupType, 2)">
							<text class="f-paragraph text-bold">自定义时间</text>
							<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
								:style="{color: primaryColor}" v-if="param.use_type == 2"></i>
							<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
						</view>
						<view class="c-caption f-desc">
							自定义时间只能选择门店营业时间内的时间
						</view>
						<view class="usetype-cont rel" :style="{width: `calc(100% / 7 * ${weekList.length})`}">
							<view class="flex-between pl-sm">
								<view class="flex-y-center usetype-box" v-for="(item, index) in weekList" :key="index">
									<text class="abs usetype-icon" @click="switchUsetype(index)"
										:style="{background: tradeweek.arr.indexOf(index) != -1 ? primaryColor : '#FFEDF3'}"></text>
									<view class="usetype-line flex-1 abs" :style="{background: tradeweek.line.indexOf(index) != -1 ? primaryColor : '#FFEDF3',
									width: weekList.length > 2 ? `calc(100% / ${weekList.length-1})` : `90%`}"
										v-if="index != weekList.length - 1"></view>
								</view>
							</view>
							<view class="flex-between pt-lg">
								<text class="f-paragraph" v-for="(item, index) in weekList" :key="index">{{item}}</text>
							</view>
						</view>
						<view class="flex-between fill-body radius-10 h-90" style="margin-bottom: 60rpx;">
							<text class="f-paragraph flex-1 flex-center h-90"
								@tap="dateChange('showStartTime')">{{param.use_start_time || `请选择开始时间`}}</text>
							<text class="c-caption">——</text>
							<text class="f-paragraph flex-1 flex-center h-90"
								@tap="dateChange('showEndTime')">{{param.use_end_time || `请选择结束时间`}}</text>
						</view>
					</view>
					<view v-if="popupType == 'reservation_type'" class="pt-lg">
						<view class="flex-between pt-sm pb-lg" @tap="popupChange(popupType, 1)">
							<text class="f-paragraph text-bold">无需预约</text>
							<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
								:style="{color: primaryColor}" v-if="param.reservation_type == 1"></i>
							<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
						</view>
						<view class="flex-between pt-md pb-md" @tap="popupChange(popupType, 2)">
							<text class="f-paragraph text-bold">提前预约</text>
							<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
								:style="{color: primaryColor}" v-if="param.reservation_type == 2"></i>
							<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
						</view>
						<view class="flex-between fill-body radius-10 h-90 pl-md pr-md" style="margin-bottom: 60rpx;">
							<text class="f-desc flex-center">·需提前几天预约</text>
							<view class="flex-center">
								<view class="flex-center reservation-icon" style="border-color: #DEDEDE;"
									@tap="reservationChange('reduce')">
									<i class="iconfont icon-jian-bold" style="color: #DEDEDE;"></i>
								</view>
								<text
									class="reservation-num flex-center text-bold pl-sm pr-sm">{{param.reservation_day}}</text>
								<view class="flex-center reservation-icon" @tap="reservationChange('add')">
									<i class="iconfont icon-jia-bold"></i>
								</view>
							</view>
						</view>
					</view>
					<view class="" v-if="popupType == 'status'" class="pt-lg">
						<view class="flex-between pt-sm pb-lg" @tap="popupChange(popupType, 2)">
							<text class="f-paragraph text-bold">上架</text>
							<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
								:style="{color: primaryColor}" v-if="param.status == 2"></i>
							<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
						</view>
						<view class="flex-between pt-md" style="margin-bottom: 60rpx;" @tap="popupChange(popupType, 1)">
							<text class="f-paragraph text-bold">下架</text>
							<i class="iconfont iconjishiwancheng" style="font-size: 40rpx;"
								:style="{color: primaryColor}" v-if="param.status == 1"></i>
							<i class="iconfont icon-xuanze" style="font-size: 40rpx;color: #BEC3CE;" v-else></i>
						</view>
					</view>
				</view>
				<view class="f-title flex-center content-btn radius-16 c-base" :style="{background: primaryColor}"
					@tap="popupComplete">完成</view>
				<view class="space-footer"></view>
			</view>
		</uni-popup>

		<uni-popup ref="specifications_item" type="center" :maskClick="true">
			<view class="tag-box fill-base mr-lg ml-lg radius-20 pd-lg">
				<view class="tag-title f-title pb-md flex-center">{{tagObj.isEdit?`编辑`:`添加`}}规格名</view>
				<view class="tag-value flex-center radius-16 pr-lg">
					<input type="text" class="flex-1 pl-lg f-paragraph" placeholder="请输入规格名" v-model="tagText"
						maxlength="10" />
					<text class="f-caption c-caption">{{tagText.length>10?10:tagText.length}}/10</text>
				</view>
				<view class="flex-center pt-lg">
					<text class="tag-btn flex-center btn-close radius-10 mr-lg"
						@tap="$refs.specifications_item.close()">取消</text>
					<text class="tag-btn flex-center c-base radius-10" :style="{backgroundColor: primaryColor}"
						@tap="addTagItem">确定</text>
				</view>
			</view>
		</uni-popup>


		<w-picker mode="date" :startYear="startYear" :endYear="startYear*1 + 100" :value="toDay" :current="false"
			fields="day" @confirm="onDateConfirm($event , 'term_start_time')" :disabled-after="false"
			ref="term_start_time" :themeColor="primaryColor" :visible.sync="showStartDate">
		</w-picker>
		<w-picker mode="date" :startYear="startYear" :endYear="startYear*1 + 100" :value="toDay" :current="false"
			fields="day" @confirm="onDateConfirm($event , 'term_end_time')" :disabled-after="false" ref="term_end_time"
			:themeColor="primaryColor" :visible.sync="showEndDate">
		</w-picker>

		<w-picker :visible.sync="showStartTime" mode="time" :value="param.use_start_time" :current="false"
			:second="false" :themeColor="primaryColor" @confirm="onTimeConfirm($event, 'use_start_time')" ref="time">
		</w-picker>
		<w-picker :visible.sync="showEndTime" mode="time" :value="param.use_end_time" :current="false" :second="false"
			:themeColor="primaryColor" @confirm="onTimeConfirm($event, 'use_end_time')" ref="time"></w-picker>

		<fix-bottom-button @confirm="submit" :text="[{type:'confirm',text:'提交' }]" bgColor="#fff" borderRadius="45rpx">
		</fix-bottom-button>
		<view class="space-max-footer"></view>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	export default {
		data() {
			return {
				isLoad: false,
				startYear: '',
				showStartDate: false,
				showEndDate: false,
				showStartTime: false,
				showEndTime: false,
				storeInfo: {},
				param: {
					id: '',
					name: '',
					sub_name: '',
					cover: '',
					price: '',
					init_price: '',
					sale: '',
					imgs: [],
					introduce: '',
					introduce_text: [],
					term_type: '', // 有效期类型 1使用时间 2有效时间
					term_start_time: '',
					term_end_time: '',
					days: '',
					use_type: '',
					use_trade_week: [],
					use_start_time: '',
					use_end_time: '',
					reservation_type: '',
					reservation_day: 1,
					rule_text: '',
					ensure: '',
					sku: [],
					status: '',
					is_integral: 0,
					integral: '',
					integral_to_money: ''
				},
				rule: [{
						name: "name",
						checkType: "isNotNull",
						errorMsg: "请输入套餐名称, 不超过10字",
						regType: 2
					}, {
						name: "cover",
						checkType: "isNotNull",
						errorMsg: "请上传套餐封面"
					}, {
						name: "imgs",
						checkType: "isNotNull",
						errorMsg: "请上传详情图"
					},
					{
						name: "sub_name",
						checkType: "isNotNull",
						errorMsg: "请输入副标题"
					},
					{
						name: "price",
						checkType: "isMoney",
						errorMsg: "请输入现价",
						regType: 1
					},
					{
						name: "init_price",
						checkType: "isMoney",
						errorMsg: "请输入划线价",
						regType: 1
					},
					{
						name: "integral",
						checkType: "isNumber",
						errorMsg: "积分",
						regType: 1
					},
					{
						name: "integral_to_money", 
						checkType: "isFloatNum",
						errorMsg: "请输入金额",
						regType: 1,
						dotLen: 1
					}, {
						name: "sale",
						checkType: "isZeroNumber",
						errorMsg: "请输入虚拟销量"
					}, {
						name: "sku",
						checkType: "isNotNull",
						errorMsg: "请创建套餐价格"
					},
					{
						name: 'term_type',
						checkType: "isNotNull",
						errorMsg: "请设置有效期"
					},
					{
						name: 'use_type',
						checkType: "isNotNull",
						errorMsg: "请选择使用时间"
					},
					{
						name: 'reservation_type',
						checkType: "isNotNull",
						errorMsg: "请设置预约信息"
					},
					{
						name: 'ensure',
						checkType: "isNotNull",
						errorMsg: "请选择保障"
					},
					{
						name: 'rule_text',
						checkType: "isNotNull",
						errorMsg: "请输入使用规则"
					},
					{
						name: 'status',
						checkType: "isNotNull",
						errorMsg: "请选择是否上下架"
					}
				],
				skuList: [],
				tagText: '',
				ensureList: [{
						id: 1,
						name: '随时退 (秒退款)',
						tips: '用户在核销前发起退款，系统自动秒退款，无需平台或者商家同意'
					},
					{
						id: 2,
						name: '人工退款',
						tips: '用户在核销前发起退款，需要平台或者商家处理退款'
					}
				],
				popupType: '',
				popupTitle: {
					ensure: '选择保障',
					term_type: '选择有效期',
					use_type: '选择使用时间',
					reservation_type: '预约信息',
					status: '选择上下架'
				},
				weekList: [],
				tradeweek: {
					arr: '',
					start: '',
					end: '',
					line: ''
				},
				termType: {
					1: '指定日期',
					2: '有效天数'
				},
				useType: {
					1: '与门店营业时间一致',
					2: '自定义时间'
				},
				statusType: {
					1: '下架',
					2: '上架'
				},
				ensureType: {
					1: '随时退 (秒退款)',
					2: '人工退款'
				},
				options: {},
				lockTap: false,
				tagObj: {
					isEdit: false,
					index: 0
				}
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
		}),
		onLoad(options) {
			this.options = options
			this.param.id = options && options.id ? options.id : ''
			this.startYear = this.$util.formatTime('', 'YY')
			this.$util.showLoading()
			uni.setNavigationBarTitle({
				title: options.id && !options.type ? '编辑套餐' : '新增套餐'
			})
			if (options.id) {
				this.getPackageInfo()
			}
			this.initIndex()
		},
		methods: {
			...mapActions(['getConfigInfo']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif
				if (!this.configInfo.id || refresh) {
					await this.getConfigInfo()
				}
				this.$util.setNavigationBarColor({
					bg: this.primaryColor
				})
				this.getStore()
			},
			async getPackageInfo() {
				let data = await this.$api.business.packageInfo({
					id: this.param.id
				})
				data.reservation_type = data.reservation_day ? 2 : 1
				data.cover = [{
					path: data.cover
				}]
				data.imgs = data.imgs.split(',').map(item => {
					return {
						path: item
					}
				})
				data.status += 1
				data.term_start_time = data.term_start_time ? this.$util.formatTime(data.term_start_time * 1000, 'YY-M-D') : 0
				data.term_end_time = data.term_end_time ? this.$util.formatTime(data.term_end_time * 1000, 'YY-M-D') : 0
				let weekArr = []
				if(data.use_trade_week){
					data.use_trade_week.split(',').forEach(item => {
						if (item == 0) {
							item = 7
						}
						weekArr.push(item - 1)
					})
					this.tradeweek = {
						arr: weekArr.join(','),
						start: weekArr[0],
						end: weekArr[weekArr.length - 1],
						line: ''
					}
					weekArr.pop()
					this.tradeweek.line = weekArr.join(',')
				}
				this.skuList = data.sku
				
				for (let key in this.param) {
					this.param[key] = data[key]
				}
			},
			async getStore() {
				let data = await this.$api.business.getStore()
				let weekList = ['周一', '周二', '周三', '周四', '周五', '周六', '周天']
				let weekArr = []
				data.trade_week.split(',').forEach(item => {
					item = item == 0 ? 7 : item
					item = item - 1
					weekArr.push(weekList[item])
				})
				this.weekList = weekArr
				this.storeInfo = data
				this.isLoad = true
				this.$util.hideAll()
			},
			imgUpload(e) {
				let {
					imagelist,
					imgtype
				} = e;
				this.param[imgtype] = imagelist;
			},
			addSpecifications() {
				this.tagText = ''
				this.tagObj.isEdit = false
				this.$refs.specifications_item.open()
			},
			validateSku() {
				let str = ''
				
				let {
					skuList
				} = this
				if (skuList.length == 0) {
					str = '请创建套餐价格'
				}
				for (let i = 0; i < skuList.length; i++) {
					for (let j = 0; j < skuList[i].price.length; j++) {
						if (!skuList[i].price[j].name) {
							return '请输入规格值'
						}
						if (skuList[i].price[j].num == '') {
							return '请输入规格数量'
						}
						if (skuList[i].price[j].num && !/^[1-9]+[0-9]*]*$/.test(skuList[i].price[j].num)) {
							return '规格数量必须大于0，并且为整数'
						}
						if (skuList[i].price[j].price == '') {
							return '请输入规格价格'
						}
						if (skuList[i].price[j].price * 1 == 0) {
							return '规格价格不能为0'
						}
						if (skuList[i].price[j].price && !/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/.test(
								skuList[i].price[j].price)) {
							return '规格价格最多1位小数'
						}
					}
				}
				return str
			},
			validatePopup(type) {
				let {
					param
				} = this
				if (type == 'term_type') { // 有效期
					if (!param.term_type) {
						return '请选择有效期'
					}
					if (param.term_type == 1 && !param.term_start_time) {
						return '有效期 - 请选择开始时间'
					}
					if (param.term_type == 1 && !param.term_end_time) {
						return '有效期 - 请选择结束时间'
					}
					if (param.term_start_time > param.term_end_time) {
						return '有效期 - 开始时间不能大于结束时间'
					}
					if (param.term_type == 2 && (!param.days || !/^[1-9]+[0-9]*]*$/.test(param.days))) {
						return '有效期 - 请输入有效天数'
					}
				} else if (type == 'use_type') { // 使用时间
					let {
						use_type,
						use_start_time: start,
						use_end_time: end,
					} = param
					let {
						//start,
						//end,
						line,
						arr
					} = this.tradeweek
					if (!use_type) {
						return '请选择使用时间'
					} else if (use_type == 2) {
						if (!arr) {
							return '使用时间 - 请选择自定义时间'
						}
						if (!start) {
							return '使用时间 - 自定义时间请选择开始时间'
						}
						if (!end) {
							return '使用时间 - 自定义时间请选择结束时间'
						}

						let {
							end_time: endtime,
							start_time: starttime
						} = this.storeInfo
						let newStartTime = new Date(`2023-11-23 ${start}`).getTime()
						let oldStartTime = new Date(`2023-11-23 ${starttime}`).getTime()
						let newEndTime = new Date(`2023-11-23 ${end}`).getTime()
						let oldEndTime = new Date(`2023-11-23 ${endtime}`).getTime()
						let startFlag = false
						let endFlag = false
						let week = this.tradeweek.arr
						if (week.length == 1 || week[0] === week[1]) {
							if (newStartTime >= newEndTime) {
								return `开始时间不能大于结束时间`
							} else if (oldStartTime > newStartTime) {
								return `开始时间不能小于门店开始时间`
							} else if (oldEndTime < newEndTime && oldStartTime < oldEndTime) {
								return `结束时间不能大于门店结束时间`
							}
						}
						// 门店开始时间大于门店结束时间  结束时间跨天
						if (oldStartTime > oldEndTime) {
							oldEndTime = new Date(`2023-11-24 ${endtime}`).getTime()
						}
						if (newStartTime < oldStartTime) {
							newStartTime = new Date(`2023-11-24 ${start}`).getTime()
						}
						// 当前开始时间大于当前结束时间  结束时间跨天
						if (newStartTime > newEndTime) {
							newEndTime = new Date(`2023-11-24 ${end}`).getTime()
						}
						if (newStartTime >= oldStartTime && newStartTime <= newEndTime) {
							startFlag = true
						}
						if (newEndTime <= oldEndTime && newEndTime >= newStartTime) {
							endFlag = true
						}
						if (!start || !end) {
							return !start ? `请选择开始时间` : `请选择结束时间`
						} else if (!startFlag) {
							return `开始时间不能小于门店开始时间`
						} else if (!endFlag) {
							return `结束时间不能大于门店结束时间`
						}
						// if ((start == end || (start && end == '')) && use_start_time >= use_end_time) {
						// 	return '使用时间 - 自定义时间开始时间必须小于结束时间'
						// }
					}
				} else if (type == 'ensure') { // 保障
					let {
						ensure
					} = this.param
					if (!ensure) {
						return '请选择保障'
					}
				} else if (type == 'status') { // 是否上架
					let {
						status
					} = this.param
					if (status == '') {
						return '请选择是否上架'
					}
				}
				return ''
			},
			//表单验证
			validate(param) {
				let validate = new this.$util.Validate();
				this.rule.map(item => {
					let {
						name,
					} = item
					if (name == 'sku') {
						param[name] = ''
						item.errorMsg = this.validateSku()
						if (!item.errorMsg) {
							param[name] = this.skuList
						}
					}
					if (['term_type', 'use_type', 'ensure', 'status'].includes(name)) { // 有效期 - 使用时间 - 保障 - 上下架
						param[name] = ''
						item.errorMsg = this.validatePopup(name)
						if (!item.errorMsg) {
							param[name] = this.param[name]
						}
					}
					if(['integral_to_money','integral'].includes(name) && ((this.configInfo.plugAuth.integral && param.is_integral == 0) || !this.configInfo.plugAuth.integral)){
						return
					}
					validate.add(param[name], item);
				})
				let message = validate.start();
				return message;
			},
			async submit() {
				let param = this.$util.deepCopy(this.param)
				let msg = this.validate(param);
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return;
				}
				param.sku = this.skuList
				param.reservation_day = param.reservation_type == 1 ? 0 : param.reservation_day
				param.imgs = param.imgs.map(item => {
					return item.path
				})
				param.cover = param.cover[0].path
				param.status = param.status * 1 - 1
				param.term_start_time = param.term_start_time ? this.$util.DateToUnix(param.term_start_time) : 0
				param.term_end_time = param.term_start_time ? this.$util.DateToUnix(param.term_end_time) : 0
				let newWeekArr = []
				if(this.tradeweek.arr){
					this.tradeweek.arr.split(',').forEach(item => {
						item = item * 1 + 1
						if (item === 7) {
							item = 0
						}
						newWeekArr.push(item)
					})
				}
				param.use_trade_week = newWeekArr
				this.$util.showLoading()
				if(this.lockTap) return
				this.lockTap = true
				try{
					await this.$api.business[param.id && !this.options.type ? 'packageEdit' : 'packageAdd'](param)
					this.$util.hideAll()
					this.$util.showToast({
						title: param.id && !this.options.type ? '编辑成功' : '新增成功'
					});
					
					setTimeout(() => {
						this.lockTap = false
						this.$util.back()
						this.$util.goUrl({
							url: 1,
							openType: 'navigateBack'
						})
					}, 1000)
				}catch(e){
					this.lockTap = false
				}
				
			},
			addTagItem() {
				if (!this.tagText) {
					return this.$util.showToast({
						title: '请输入规格名'
					})
				}
				let {
					isEdit,
					index
				} = this.tagObj
				if(!isEdit){
					this.skuList.push({
						name: this.tagText,
						price: [{
							name: '',
							num: '',
							price: ''
						}]
					})
				}else{
					this.skuList[index].name = this.tagText
				}
				this.$refs.specifications_item.close()
			},
			addSku(index) {
				this.skuList[index].price.push({
					name: '',
					num: '',
					price: ''
				})
			},
			delSku(index, cindex) {
				this.skuList[index].price.splice(cindex, 1)
			},
			onChangePopup(type) {
				this.popupType = type
				this.$refs.content_item.open()
			},
			popupChange(type, index) {
				if(this.popupType == 'use_type' && index == 1){
					this.param.use_start_time = ''
					this.param.use_end_time = ''
					this.tradeweek.arr = ''
					this.tradeweek.line = ''
				}
				this.param[type] = index
			},
			popupComplete() {
				let {
					popupType,
					param
				} = this
				let msg = ''
				if (['term_type', 'use_type', 'ensure', 'status'].includes(popupType)) {
					msg = this.validatePopup(popupType)
				}
				if (msg) {
					this.$util.showToast({
						title: msg
					});
					return
				}
				this.$refs.content_item.close()
			},
			dateChange(type) {
				this[type] = true
			},
			onDateConfirm(data, type) {
				this.param[type] = data.value
			},
			onTimeConfirm(data, type) {
				this.param[type] = data.result
			},
			reservationChange(type) {
				if (type == 'add') {
					this.param.reservation_day += 1
				} else {
					if (this.param.reservation_day == 1) {
						this.$util.showToast({
							title: '不能小于1'
						});
						return
					}
					this.param.reservation_day -= 1
				}
			},
			switchUsetype(index) {
				let {
					start,
					end,
					line,
					arr
				} = this.tradeweek

				if ((start != '' && end != '') || (start == '' && end == '')) {
					this.tradeweek.start = String(index)
					this.tradeweek.end = ''
					this.tradeweek.arr = String(index)
					this.tradeweek.line = ''
				} else if (start != '' && end == '') {
					this.tradeweek.end = String(index)
					if (start == index) {
						this.tradeweek.arr = String(index)
						this.tradeweek.line = ''
					} else if (start > index) {
						this.tradeweek.arr = this.$util.betweenNumbers(index, start * 1)
						this.tradeweek.line = this.$util.betweenNumbers(index, start * 1 - 1)
					} else {
						this.tradeweek.arr = this.$util.betweenNumbers(start * 1, index)
						this.tradeweek.line = this.$util.betweenNumbers(start * 1, index - 1)
					}
				}

			},
			async updateSku(type,index){
				let {
					name = ''
				} = this.skuList[index]
				if(type == 'edit'){
					this.$refs.specifications_item.open()
					this.tagText = name
					this.tagObj.isEdit = true
					this.tagObj.index = index
				}else{
					let [res_del, {
						confirm
					}] = await uni.showModal({
						cancelText: '我再想想',
						confirmText: '确认',
						content: `删除规格后，会将下面的规格值一并删除，确认删除该数据吗？`,
					})
					if (!confirm) return;
					this.skuList.splice(index, 1)
				}
			},
			hannelIntegral(id){
				this.param.is_integral = id
			}
		}
	}
</script>

<style lang="scss">
	.add-pages {
		.add-box {
			padding-top: 40rpx;
			padding-bottom: 40rpx;
		}

		.h-90 {
			height: 90rpx;
		}

		.h-100 {
			height: 100rpx;
		}

		.h-110 {
			height: 110rpx;
		}

		.h-120 {
			height: 120rpx;
		}

		.mt-50 {
			margin-top: 50rpx;
		}

		.mt-45 {
			margin-top: 45rpx;
		}

		.w-130 {
			width: 130rpx;
		}

		.mt-big {
			margin-top: 25rpx;
		}

		.tag-box {
			width: 600rpx;

			.tag-btn {
				width: 140rpx;
				height: 68rpx;
			}

			.btn-close {
				border: 1px solid #eee;
			}

			.tag-value {
				border: 1px solid #eee;

				input {
					height: 80rpx;
				}
			}
		}

		.purchase-box {
			padding-top: 50rpx;
			padding-bottom: 50rpx;
		}

		.content-popup {
			border-radius: 30rpx 30rpx 0px 0px;
			padding-top: 40rpx;
		}

		.ensure-box {
			padding-bottom: 70rpx;
		}

		.content-btn {
			margin: 10rpx 60rpx 10rpx 60rpx;
			height: 85rpx;
		}

		.usetype-cont {
			padding: 40rpx 0;

			.usetype-box {
				width: 38rpx;
				height: 38rpx;
			}

			.usetype-icon {
				width: 38rpx;
				height: 38rpx;
				border-radius: 38rpx;
				border: 2rpx solid #FFEDF3;
				background-color: #FFEDF3;
				z-index: 2;
			}

			.usetype-line {
				height: 10rpx;
				background-color: #FFEDF3;
				margin-left: 0rpx;
			}
		}

		.reservation-num {
			min-width: 54rpx;
		}

		.reservation-icon {
			width: 40rpx;
			height: 40rpx;
			border-radius: 7rpx;
			border: 2px solid #333;

			i {
				font-size: 13px !important;
			}
		}
	}
</style>
