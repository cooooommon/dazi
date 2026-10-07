<template>
	<view class="pages-mine" v-if="isLoad">
		<!-- #ifndef H5 -->
		<uni-nav-bar :fixed="true" :shadow="false" :statusBar="true" :title="userPageType == 2?'我是'+$t('action.attendantName'): navTitle"
			color="#ffffff" :backgroundColor="userPageType==2 ? primaryColor : ''">
		</uni-nav-bar>
		<view :style="{height:`${configInfo.navBarHeight}px`}"></view>
		<!-- #endif -->
		<image mode="aspectFill" lazy-load class="mine-user-bg abs"
			src="https://lbqny.migugu.com/admin/playwith/mine/service-nav-bg.png" v-if="userPageType==1"></image>
		<image mode="aspectFill" lazy-load class="mine-bg abs" :src="configInfo[image_type[userPageType]]"
			v-if="userPageType==2"></image>

		<!-- 用户 -->
		<!-- coach_status 1申请中，2已通过，3已取消授权，4已拒绝 -->
		<block v-if="userPageType == 1">
			<view class="pt-lg pl-lg pr-lg rel"
				:class="[{ 'flex-warp': userInfo && userInfo.nickName }, { 'flex-center': !userInfo || (userInfo &&!userInfo.nickName) } ]">
				<auth :needAuth="true" :must="true" :haveGo="false" class="avatar_view rel" style="width: 130rpx;">
					<!-- <image class="user-avatar-bg abs" src="https://lbqny.migugu.com/admin/playwith/mine/user-bg.png"
						v-if="userInfo && userInfo.id">
					</image> -->
					<view class="avatar_view">
						<image mode="aspectFill" class="avatar radius"
							:src="userInfo.avatarUrl || `https://lbqny.migugu.com/admin/anmo/mine/default_user.png`">
						</image>
					</view>
					<view class="flex-warp" style="margin-top: -26rpx;margin-left: 7rpx;">
						<view class="admin-tag flex-center rel c-base" style="border-radius: 4rpx;"
							v-if="userInfo.id && mineInfo.is_admin == 1">
							代理商
						</view>
					</view>
				</auth>
				<auth :needAuth="true" :must="true" :haveGo="false" class="flex-1"
					v-if="!userInfo || (userInfo && !userInfo.nickName)">
					<view class="f-md-title text-bold ml-md" style="color:#1D2541">登录
					</view>
				</auth>
				<view class="flex-1 ml-md mt-sm" v-else>
					<view class="flex-between flex-1">
						<view>
							<auth :needAuth="true" :must="true" :haveGo="false">
								<view class="flex-y-center">
									<view class="mr-sm f-sm-title text-bold max-300 ellipsis" style="color:#1D2541">
										{{ userInfo.nickName || '默认用户' }}
									</view>
									<!-- <i class="flex-1 iconfont iconbianjiziliao"></i> -->
								</view>
							</auth>
							<view class="admin-tag flex-center rel c-base radius-26" style="background:#CCDCE1;color:#717E8C">
								<i class="iconfont" :class="[{'iconjishi1':mineInfo.coach_status === 2},{'iconputonghuiyuan':mineInfo.coach_status !== 2}]"></i>
									{{mineInfo.coach_status === 2 ? mineInfo.coach_level && mineInfo.coach_level.length > 0 && mineInfo.coach_level.title ? mineInfo.coach_level.title : $t('action.attendantName') : '普通会员'}}
							</view>
						</view>
						<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width: auto;"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.goUrl({url:`/user/pages/setting`})">
							<view class="flex-y-center f-caption c-title">
								<text class="f-caption pr-sm">编辑资料</text>
								<i class="iconfont icongengduo" style="font-size:24rpx"></i>
							</view>
						</auth>
					</view>
				</view>
			</view>

			<!-- 卡券/关注/收藏 -->
			<view class="share-collect-list flex-x-center rel pb-sm">
				<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({ url: `/user/pages/stored/list` })"
					class="share-item flex-center flex-column">
					<view class="f-md-title text-bold flex-center">{{mineInfo | handleCash}}</view>
					<view class="f-caption flex-center">{{configInfo.balance_character || `余额`}}</view>
				</auth>
				
				<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({ url: `/user/pages/coupon/list` })"
					class="share-item flex-center flex-column">
					<view class="f-md-title text-bold flex-center">{{ mineInfo.coupon_count || 0 }}</view>
					<view class="f-caption flex-center">优惠券</view>
				</auth>
				
				<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" v-if="configInfo.plugAuth.integral"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({ url: `/user/pages/integra/detail` })"
					class="share-item flex-center flex-column">
					<view class="f-md-title text-bold flex-center">{{ Number((mineInfo.integral*1 + mineInfo.wait_integral*1).toFixed(2)) || 0 }}</view>
					<view class="f-caption flex-center">积分</view>
				</auth>
				
				<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({ url: `/user/pages/collect` })"
					class="share-item flex-center flex-column">
					<view class="f-md-title text-bold flex-center">{{ mineInfo.collect_count || 0 }}</view>
					<view class="f-caption flex-center">关注</view>
				</auth>
			</view>
			
			
			<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
				:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({ url: `/memberdiscount/pages/index` })"
				v-if="configInfo.plugAuth.member">
				<view class="rel flex-center mt-sm">
					<image class="member-card-bg" src="https://lbqny.migugu.com/admin/peiwan/member-bg.png" mode="aspectFill"></image>
					<view class="abs member-card-box pr-lg flex-between">
						<block v-if="mineInfo.member_info && mineInfo.member_info.id && new Date().getTime() < mineInfo.member_info.end_time*1000">
							<view class="">
								<view class="c-title f-paragraph text-bold ellipsis max-350">{{mineInfo.member_info.title}}</view>
								<view class="f-desc c-icontext" style="padding-top: 2px;">{{$util.getDaysBetweenDates(new Date().getTime() , mineInfo.member_info.end_time*1000)}}天后到期</view>
							</view>
							<view class="f-paragraph member-card-btn flex-center">去续费</view>
						</block>
						<block v-else>
							<view class="c-title f-paragraph text-bold ellipsis max-350">开通会员享超低折扣优惠</view>
							<view class="f-paragraph member-card-btn flex-center">立即开通</view>
						</block>
					</view>
				</view>
			</auth>
			


			<!-- 我的余额 -->
			<!-- <auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
				:type="!userInfo.phone ? 'phone' : 'userInfo'"
				@go="$util.toCheckLogin({url:`/user/pages/stored/list`})">
				<view class="mt-md ml-md mr-md radius-20 rel">
					<image mode="aspectFill" class="store-img radius-16"
						src="https://lbqny.migugu.com/admin/playwith/mine/balance-bg.png">
					</image>
					<view class="abs store-img flex-center" style="padding:33rpx 40rpx;top:0;left:0;z-index: 2;">
						<view class="flex-1 mr-lg c-base">
							<view class="f-icontext text-bold mt-sm">余额充值, 更多优惠</view>
							<view class="f-big-title text-bold">{{ mineInfo.balance || 0 }}</view>
						</view>
						<view class="store-btn flex-center f-paragraph c-base radius">
							充值
						</view>
					</view>
				</view>
			</auth> -->

			<view class="mine-menu-list box-shadow fill-base radius-24 rel">
				<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="$util.toCheckLogin({url:`/pages/order`})"
					>
					<view class="flex-between pl-lg pr-md menu-title">
						<view class="f-paragraph c-title text-bold">我的订单</view>
						<view class="flex-y-center f-caption" style="color:#999B9F">查看全部<i class="iconfont icongengduo"
								style="color: #999B9F;"></i></view>
					</view>
				</auth>
				<!-- <view @tap="$util.toCheckLogin({url:`/pages/order`})" class="menu-title flex-between pl-lg pr-md">
					<view class="f-paragraph c-title text-bold">我的订单</view>
					<view class="flex-y-center f-caption" style="color:#999B9F">查看全部<i class="iconfont icongengduo"
							style="color: #999B9F;"></i></view>
				</view> -->
				<view class="flex-warp pb-lg">
					<block v-for="(item, index) in orderList" :key="index">
						<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toJump('orderList', index)"
							class="item-child flex-center flex-column f-caption rel" style="width: 20%;">
							<view class="flex-center flex-column">
								<view class="abs dot-unread-number flex-center"
									:style="{width: item.number>99 ? '44rpx': item.number > 9 ? '34rpx' :'24rpx',right: item.number>99 ? '-12rpx': item.number > 9 ? '0rpx' :'12rpx'}"
									v-if="item.number > 0">
									{{item.number < 100 ? item.number : '99+'}}
								</view>
								<image class="order-img" :src="item.icon"></image>
								<view class="mt-sm" style="color:#2E3541">{{ item.text }}</view>
							</view>
						</auth>
					</block>
				</view>
			</view>
			
			<view class="mine-menu-list box-shadow fill-base radius-24 rel">
				<view class="menu-title flex-between pl-lg pr-sm">
					<view class="f-paragraph c-title text-bold">邀约订单</view>
				</view>
				<view class="flex-warp pb-lg">
					<block v-for="(item, index) in userFindList" :key="index">
						<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toJump('userFindList', index)"
							class="item-child flex-center flex-column" style="width: 25%">
							<view class=" flex-center flex-column f-caption c-title" >
								<view class="item-img rel flex-center radius">
									<view class="abs dot-unread-number flex-center"
										:style="{width: item.number>99 ? '44rpx': item.number > 9 ? '34rpx' :'24rpx',right: item.number>99 ? '-32rpx': item.number > 9 ? '-22rpx' :'-12rpx'}"
										v-if="item.number > 0">
										{{item.number < 100 ? item.number : '99+'}}
									</view>
									<!-- <view class="item-img radius abs" :style="{background:primaryColor}"></view> -->
									<i class="iconfont c-title" :class="item.icon" :style="{color:primaryColor,fontSize: `34px`}"></i>
								</view>
								<view class="mt-sm">{{ item.text }}</view>
							</view>
						</auth>
					</block>
				</view>
			</view>
			
			<!---->
			<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" v-if="mineInfo.coach_status != 2"
				:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toApply(1)" class="wizard-settle rel">
				<view class="rel mt-md">
					<image src="https://lbqny.migugu.com/admin/playwith/mine/settle-1.png" mode="aspectFill" class="settle-bg"></image>
					<view class="abs wizard-box flex-between pr-lg">
						<view class="">
							<view class="f-title c-base">{{$t('action.attendantName')}}入驻</view>
							<view class="f-caption c-base pt-sm">立即加入成为{{$t('action.attendantName')}}吧</view>
						</view>
						<view class="wizard-btn f-paragraph flex-center fill-base" :style="{color: primaryColor}">立即加入</view>
					</view>
				</view>
			</auth>
			
			<view class="mine-menu-list box-shadow fill-base radius-24 rel" v-if="configInfo.plugAuth.store">
				<view class="menu-title flex-between pl-lg pr-sm">
					<view class="f-paragraph c-title text-bold">我的团购</view>
				</view>
				<view class="flex-warp pb-lg">
					<block v-for="(item, index) in packageList" :key="index">
						<auth :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toJump('packageList', index)"
							class="item-child flex-center flex-column" style="width: 25%">
							<view class=" flex-center flex-column f-caption c-title" >
								<view class="item-img rel flex-center radius">
									<view class="abs dot-unread-number flex-center"
										:style="{width: item.number>99 ? '44rpx': item.number > 9 ? '34rpx' :'24rpx',right: item.number>99 ? '-32rpx': item.number > 9 ? '-22rpx' :'-12rpx'}"
										v-if="item.number > 0">
										{{item.number < 100 ? item.number : '99+'}}
									</view>
									<!-- <view class="item-img radius abs" :style="{background:primaryColor}"></view> -->
									<i class="iconfont c-title" :class="item.icon" :style="{color:primaryColor,fontSize: `34px`}"></i>
								</view>
								<view class="mt-sm">{{ item.text }}</view>
							</view>
						</auth>
					</block>
				</view>
			</view>

			<view class="mine-menu-list box-shadow fill-base radius-24 rel mt-md">
				<view class="menu-title flex-between pl-lg pr-sm">
					<view class="f-paragraph c-title text-bold">荐者有礼</view>
				</view>
				<view class="flex-warp pb-lg">
					<block v-for="(item, index) in mineInfo.is_fx ? distributionList : distributionApplyList" :key="index">
						<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" style="width: 25%;"
							:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toJump( mineInfo.is_fx ? 'distributionList' : 'distributionApplyList', index )">
							<view class="item-child flex-center flex-column f-caption c-paragraph"
								style="margin:10rpx 0 20rpx 0;width: 100%;"
								v-if="item.text == '绑定'+$t('action.attendantName') ? mineInfo.is_admin == 1 ? true : false : true ">
								<view class="item-img rel flex-center radius" style="background:#F8F8F8">
									<i class="iconfont c-title" :class="item.icon" :style="{color:primaryColor}"></i>
								</view>
								<view class="mt-sm">{{ item.text }}</view>
							</view>
						</auth>
					</block>
					
				</view>
			</view>



			<view class="mine-menu-list box-shadow fill-base radius-24 rel">
				<view class="menu-title flex-between pl-lg pr-sm">
					<view class="f-paragraph c-title text-bold">其他</view>
				</view>
				<view class="flex-warp pb-sm">
					
					<block v-for="(item, index) in toolList" :key="index">
						<!-- #ifdef MP-WEIXIN -->
						<button :open-type="configInfo.im_type == 2 ?'contact':''"
							class="clear-btn item-child flex-center flex-column"
							style="width: 25%;margin:10rpx 0 20rpx 0"
							v-if="item.text == '联系客服' && configInfo.im_type == 2">
							<view class="flex-center flex-column f-caption c-title">
								<i class="iconfont" :class="item.icon" :style="{color: primaryColor}"></i>
								<view class="mt-sm">{{ item.text }}</view>
							</view>
						</button>
						<view class="item-child flex-center flex-column f-caption c-title" v-else-if="item.text == '联系客服' && configInfo.im_type !== 2"
						@tap="toJump('toolList', index)"
							style="width: 25%;margin:10rpx 0 20rpx 0" >
							<i class="iconfont " :class="item.icon" :style="{color: primaryColor}"></i>
							<view class="mt-sm">{{ item.text }}</view>
						</view>
						<block v-else>
							<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
							v-if="item.text == '绑定'+$t('action.attendantName') ? mineInfo.is_admin == 1 ? true : false : true "
								:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toJump('toolList', index)" style="width: 25%;">
								<view class="item-child flex-center flex-column f-caption c-title"
									style="width: 100%;margin:10rpx 0 20rpx 0" >
									<i class="iconfont " :class="item.icon" :style="{color: primaryColor}"></i>
									<view class="mt-sm">{{ item.text }}</view>
								</view>
							</auth>
						</block>
						
						
						<!-- #endif -->
						<!-- #ifndef MP-WEIXIN --> 
						<view class="item-child flex-center flex-column f-caption c-title" v-if="item.text == '联系客服'"
						@tap="toJump('toolList', index)"
							style="width: 25%;margin:10rpx 0 20rpx 0">
							<i class="iconfont  " :class="item.icon" :style="{color: primaryColor}"></i>
							<view class="mt-sm">{{ item.text }}</view>
						</view>
						<block v-else>
							<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true"
								v-if="item.text == '绑定'+$t('action.attendantName') ? mineInfo.is_admin == 1 ? true : false : true "
								:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toJump('toolList', index)" style="width: 25%;">
								<view class="item-child flex-center flex-column f-caption c-title"
									style="width: 100%;margin:10rpx 0 20rpx 0">
									<i class="iconfont  " :class="item.icon" :style="{color: primaryColor}"></i>
									<view class="mt-sm">{{ item.text }}</view>
								</view>
							</auth>
						</block>
						<!-- #endif -->
					</block>
					<auth @tap.stop.prevent :needAuth="userInfo && (!userInfo.phone || !userInfo.nickName)" :must="true" 
					:type="!userInfo.phone ? 'phone' : 'userInfo'" @go="toChange" style="width: 25%;">
						<view class="item-child flex-center flex-column f-caption c-title"
							style="width: 100%;margin:10rpx 0 20rpx 0"
							v-if="mineInfo.coach_status == 2 || mineInfo.coach_status == 3">
							<i class="iconfont iconqiehuan1" :style="{color: primaryColor}"></i>
							<view class="mt-sm">切换{{$t('action.attendantName')}}端</view>
						</view>
					</auth>
				</view>
			</view>
		</block>
		<!-- 向导 -->
		<block v-if="userPageType == 2">

			<view class="addr-time-help-list flex-x-center f-desc rel"
				:style="{color:configInfo[font_type[userPageType]]}">
				<view @tap.stop="toChooseLocation(false)" class="flex-center flex-column">
					<i class="iconfont iconweizhigengxin1"></i>
					<view>位置更新</view>
				</view>
				<view @tap.stop="$util.goUrl({url:`/technician/pages/time-manage`})" class="flex-center flex-column">
					<i class="iconfont iconshijianguanli2"></i>
					<view>时间管理</view>
				</view>
				<view @tap.stop="toHelp" class="flex-center flex-column">
					<i class="iconfont iconyijianbaojing"></i>
					<view>一键报警</view>
				</view>
			</view>


			<view class="coach-info fill-base pt-lg pl-lg pr-lg pb-sm radius-16 rel" v-if="coachInfo.id">
				<view class="flex-center pb-lg">
					<!-- #ifdef H5 -->
					<view class="avatar radius">
						<view @tap.stop="toPreviewImage(index,1)" class="h5-image avatar radius"
							:style="{ backgroundImage : `url('${coachInfo.work_img}')`}">
						</view>
					</view>
					<!-- #endif -->
					<!-- #ifndef H5 -->
					<image mode="aspectFill" class="avatar radius" :src="coachInfo.work_img">
					</image>
					<!-- #endif -->
					<view class="flex-1 ml-md">
						<view class="flex-between">
							<view class="coach-name text-bold max-300 ellipsis">{{coachInfo.coach_name}}</view>
							<view @tap.stop="$util.goUrl({url:`/technician/pages/apply?is_edit=1`})"
								class="coach-text f-paragraph flex-y-center">个人信息<i class="iconfont icon-right"></i>
							</view>
						</view>
						<view class="flex-warp mt-sm">
							<view class="tag-item flex-center"
								:style="{color:primaryColor,border:`1rpx solid ${primaryColor}`}">
								已认证</view>
							<view class="tag-item flex-center ml-sm"
								:style="{color:coachInfo.is_work?primaryColor:'#5A677E',border:`1rpx solid ${coachInfo.is_work?primaryColor:`#5A677E`}`}"
								v-if="mineInfo.coach_status === 2">
								{{coachInfo.is_work ? textType[coachInfo.text_type] : '请假中'}}
							</view>
							<view class="tag-item flex-center ml-sm" v-if="configInfo.cash_type == 1"
								:style="{color:primaryColor,border:`1rpx solid ${primaryColor}`}">
								{{coachInfo.coach_level.title}}
							</view>
						</view>
					</view>
				</view>
				<view class="map-addr-info pt-lg b-1px-t">
					<view class="flex-center">
						<view class="map-addr flex-center rel">
							<view class="map-addr radius abs" :style="{background:primaryColor}"></view>
							<view class="flex-y-center f-desc" :style="{color:primaryColor}">
								<i class="iconfont icondangqianweizhi"></i>
								当前
							</view>
						</view>
						<view class="flex-1 text ml-md ellipsis">
							{{coachInfo.address}}
						</view>
					</view>
					<view class="location-change flex-between pt-sm">
						<view class="text f-paragraph">实时定位</view>
						<i @tap.stop="toChangeLocation" class="iconfont"
							:class="[{'icon-switch':!userInfo.coach_position},{'icon-switch-on':userInfo.coach_position}]"
							:style="{color:userInfo.coach_position?primaryColor:'#ddd'}"></i>
					</view>
				</view>
			</view>


			<view class="mine-count-list flex-between rel">
				<view class="cancel-auth iconfont icon-biaoqian c-caption flex-center abs"
					v-if="mineInfo.coach_status == 3">
					<view class="text-bold f-icontext abs">取消授权</view>
				</view>

				<view @tap.stop="$util.goUrl({ url: `/technician/pages/income/index`})"
					class="item-child mr-sm fill-base f-caption box-shadow radius-16">
					<view class="flex-y-baseline" :style="{color:primaryColor}">¥<view class="f-sm-title">
							{{coachInfo.service_price || 0}}
						</view>
					</view>
					<view class="flex-between mt-sm">
						<view class="text f-paragraph">服务收入</view>
						<view class="cash-btn flex-center f-desc c-base radius" :style="{ background: primaryColor}">去提现
						</view>
					</view>
				</view>
				<view @tap.stop="$util.goUrl({ url: `/user/pages/cash-out?type=carfee` })"
					class="item-child ml-sm pt-lg pb-lg pl-md pr-sm fill-base f-caption c-desc box-shadow radius-16 ">
					<view class="flex-y-baseline" :style="{color:primaryColor}">¥<view class="f-sm-title">
							{{coachInfo.car_price || 0}}
						</view>
					</view>
					<view class="flex-between mt-sm">
						<view class="text f-paragraph">车费</view>
						<view class="cash-btn flex-center f-desc c-base radius" :style="{ background: primaryColor }">
							去提现</view>
					</view>
				</view>
			</view>


			<view class="mine-menu-list box-shadow fill-base radius-16">
				<view class="menu-title flex-between pl-lg pr-sm">
					<view class="f-paragraph c-title text-bold">我的订单</view>
				</view>
				<view class="flex-warp pb-lg">
					<view @tap.stop="toJump('orderList2', index)"
						class="item-child flex-center flex-column f-caption c-title" style="width: 25%"
						v-for="(item, index) in orderList2" :key="index">
						<view class="item-img rel flex-center radius">
							<view class="abs dot-unread-number flex-center"
								:style="{width: item.number>99 ? '44rpx': item.number > 9 ? '34rpx' :'24rpx',right: item.number>99 ? '-32rpx': item.number > 9 ? '-22rpx' :'-12rpx'}"
								v-if="item.number > 0">
								{{item.number < 100 ? item.number : '99+'}}
							</view>
							<view class="item-img radius abs" :style="{background:primaryColor}"></view>
							<i class="iconfont c-title" :class="item.icon" :style="{color:primaryColor}"></i>
						</view>
						<view class="mt-sm">{{ item.text }}</view>
					</view>
				</view>
			</view>

			<view class="mine-menu-list box-shadow fill-base radius-16">
				<view class="menu-title flex-between pl-lg pr-sm">
					<view class="f-paragraph c-title text-bold">邀约订单</view>
				</view>
				<view class="flex-warp pb-lg">
					<view @tap.stop="toJump('findList', index)"
						class="item-child flex-center flex-column f-caption c-title" style="width: 25%"
						v-for="(item, index) in findList" :key="index">
						<view class="item-img rel flex-center radius">
							<view class="abs dot-unread-number flex-center"
								:style="{width: item.number>99 ? '44rpx': item.number > 9 ? '34rpx' :'',right: item.number>99 ? '-32rpx': item.number > 9 ? '-22rpx' :'-12rpx'}"
								v-if="item.number > 0">
								{{item.number < 100 ? item.number : '99+'}}
							</view>
							<view class="item-img radius abs" :style="{background:primaryColor}"></view>
							<i class="iconfont c-title" :class="item.icon" :style="{color:primaryColor}"></i>
						</view>
						<view class="mt-sm">{{ item.text }}</view>
					</view>
				</view>
			</view>
			
			<view class="mine-menu-list box-shadow fill-base radius-16">
				<view class="menu-title flex-between pl-lg pr-sm">
					<view class="f-paragraph c-title text-bold">业绩管理</view>
				</view>
				<view class="flex-warp pb-lg">
					<view @tap.stop="toJump('commissionList', index)"
						class="item-child flex-center flex-column f-caption c-title" style="width: 25%"
						v-for="(item, index) in commissionList" :key="index">
						<view class="item-img rel flex-center radius">
							<view class="item-img radius abs" :style="{background:primaryColor}"></view>
							<i class="iconfont c-title" :class="item.icon" :style="{color:primaryColor}"></i>
						</view>
						<view class="mt-sm">{{ item.text }}</view>
					</view>
				</view>
			</view>


			<view class="mine-menu-list box-shadow fill-base radius-16">
				<view class="menu-title flex-between pl-lg pr-sm">
					<view class="f-paragraph c-title text-bold">其他功能</view>
				</view>
				<view class="flex-warp pb-sm">
					<view @tap.stop="toJump('toolList2', index)"
						class="item-child flex-center flex-column f-caption c-title"
						style="width: 25%;margin:10rpx 0 20rpx 0" v-for="(item, index) in toolList2" :key="index">
						<i class="iconfont c-title" :class="item.icon" :style="{color:primaryColor}"></i>
						<view class="mt-sm">{{ item.text }}</view>
					</view>
				</view>
			</view>

		</block>

		<view class="space-footer"></view>
		<view :style="{height: `${configInfo.tabbarHeight}px`}"></view>
		<tabbar :cur="5"></tabbar>

		<!-- #ifdef APP-PLUS -->
		<login-info></login-info>
		<!-- #endif -->
		
	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
		mapMutations
	} from "vuex"
	import tabbar from "@/components/tabbar.vue"
	export default {
		components: {
			tabbar
		},
		data() {
			return {
				isLoad: false,
				options: {},
				textType: {
					1: '可接单',
					2: '接单中',
					3: '休息中',
					4: '不可接单'
				},
				is_share: true,
				// 我的订单
				orderList: [{
					icon: 'https://lbqny.migugu.com/admin/playwith/mine/pay-1.png',
					text: '待支付',
					url: '/pages/order?tab=1',
					number: 0
				}, {
					icon: 'https://lbqny.migugu.com/admin/playwith/mine/pay-2.png',
					text: '待服务',
					url: '/pages/order?tab=2',
					number: 0
				}, {
					icon: 'https://lbqny.migugu.com/admin/playwith/mine/pay-3.png',
					text: '服务中',
					url: '/pages/order?tab=3',
					number: 0
				}, {
					icon: 'https://lbqny.migugu.com/admin/playwith/mine/pay-4.png',
					text: '待评价',
					url: '/pages/order?tab=4',
					number: 0
				}, {
					icon: 'https://lbqny.migugu.com/admin/playwith/mine/pay-5.png',
					text: '退款/售后',
					url: '/user/pages/refund/list',
					number: 0
				}],
				orderList2: [{
					icon: 'icondaijiedan',
					text: '待接单',
					url: '/technician/pages/order/list',
					number: 0
				}, {
					icon: 'iconyijiedan',
					text: '待服务',
					url: '/technician/pages/order/list?tab=1',
					number: 0
				}, {
					icon: 'iconfuwuzhong',
					text: '服务中',
					url: '/technician/pages/order/list?tab=2',
					number: 0
				}, {
					icon: 'icontuikuan11',
					text: '退款/售后',
					url: '/user/pages/refund/list?is_coach=1',
					number: 0
				}],
				commissionList: [{
					icon: 'iconjifenmingxi',
					text: '储值返佣明细',
					url: '/technician/pages/income/cash-integral',
					number: 0
				}, {
					icon: 'iconfenchengmingxi',
					text: '分成明细',
					url: '/technician/pages/income/commission-list',
					number: 0
				}, {
					icon: 'iconyaoqingmingxi2',
					text: '邀约明细',
					url: '/technician/pages/income/invitation-list',
					number: 0
				}],
				findList: [
					{
						icon: 'icondaijiedan1',
						text: '待雇佣',
						url: '/find/pages/invitation/list?tab=1',
						number: 0
					}, {
						icon: 'iconyijiedan1',
						text: '已接单',
						url: '/find/pages/invitation/list?tab=2',
						number: 0
					}, {
						icon: 'iconyiwancheng1',
						text: '已完成',
						url: '/find/pages/invitation/list?tab=3',
						number: 0
					}
				],
				// 我的团购
				packageList: [
					{
						icon: 'icondaifukuan1',
						text: '待支付',
						url: '/business/pages/package/order/list?tab=1',
						number: 0
					}, {
						icon: 'icondaishiyong',
						text: '待使用',
						url: '/business/pages/package/order/list?tab=2',
						number: 0
					}, {
						icon: 'icondaipingjia4',
						text: '待评价',
						url: '/business/pages/package/order/list?tab=3',
						number: 0
					}, {
						icon: 'icontuikuan4',
						text: '退款/售后',
						url: '/business/pages/package/order/list?tab=4',
						number: 0
					}
				],
				// 用户端
				userFindList: [
					{
						icon: 'icondaijiedan1',
						text: '待雇佣',
						url: '/find/pages/invitation/list?userPageType=1&tab=1',
						number: 0
					}, {
						icon: 'iconyijiedan1',
						text: '已接单',
						url: '/find/pages/invitation/list?userPageType=1&tab=2',
						number: 0
					}, {
						icon: 'iconyiwancheng1',
						text: '已完成',
						url: '/find/pages/invitation/list?userPageType=1&tab=3',
						number: 0
					}
				],
				// 其他
				distributionList: [{
					icon: 'iconwodeshouyi',
					text: '我的收益',
					url: '/user/pages/distribution/income'
				}, {
					icon: 'icontuiguanghaibao',
					text: '推广海报',
					url: '/user/pages/distribution/choose-invite' //'/user/pages/distribution/poster'
				}, {
					icon: 'iconwodetuandui1',
					text: '我的邀请',
					url: '/user/pages/channel/my-invite'
				// }, {
				// 	icon: 'iconbangdingjishi',
				// 	text: '绑定向导',
				// 	url: '/user/pages/distribution/bind-technician'
				}],
				distributionApplyList: [{
					icon: 'iconshenqingfenxiaoyuan',
					text: '申请分销商',
					url: '/user/pages/distribution/apply'
				// }, {
				// 	icon: 'iconbangdingjishi',
				// 	text: '绑定向导',
				// 	url: '/user/pages/distribution/bind-technician'
				}],
				toolList: [
				{
					icon: 'iconhehuoren2',
					text: '合作加盟',
					url: `/technician/pages/join-us` ,
					class: 'agent'
				},
				{
					icon: 'icondizhi3',
					text: '地址管理',
					url: '/user/pages/address/list',
					class: 'address'
				}, {
					icon: 'iconfankui',
					text: '问题反馈',
					url: '/user/pages/feedback/box',
					class: 'question'
				}, {
					icon: 'iconkefu',
					text: '联系客服',
					url: '',
					class: 'contact'
				}, {
					icon: 'iconbangdingjishi',
					text: '绑定'+this.$t('action.attendantName'),
					url: '/user/pages/distribution/bind-technician',
				}
				// , {
				// 	icon: 'iconfabu',
				// 	text: '我的发布',
				// 	url: '/find/pages/invitation/list?userPageType=1',
				// 	class: 'find'
				// }
				],
				toolList2: [
					// {
					// 	icon: 'icondengjiguanli',
					// 	text: '等级管理',
					// 	url: '/technician/pages/level'
					// },
					{
						icon: 'iconchefeimingxi',
						text: '车费明细',
						url: '/technician/pages/car-fare'
					},
					{
						icon: 'iconchefeitixianjilu',
						text: '车费提现记录',
						url: '/user/pages/distribution/record?type=3'
					},
					{
						icon: 'iconwuliaoshangcheng',
						text: '物料商城',
						url: '/technician/pages/shop/list'
					},
					{
						icon: 'iconchapingshensu',
						text: '差评申诉',
						url: '/technician/pages/bad-comments/box'
					},
					// {
					// 	icon: 'iconpingbiyonghu',
					// 	text: '屏蔽用户',
					// 	url: '/technician/pages/shield'
					// },
					{
						icon: 'iconqiehuanjishiduan',
						text: '切换用户端',
						url: 'change'
					}
				],
				image_type: {
					1: 'user_image',
					2: 'coach_image'
				},
				font_type: {
					1: 'user_font_color',
					2: 'coach_font_color'
				},
				showAuth: false,
				offsetL: 360,
				offsetT: 0,
				navTitle: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
			configInfo: state => state.config.configInfo,
			commonOptions: state => state.user.commonOptions,
			userInfo: state => state.user.userInfo,
			userPageType: state => state.user.userPageType,
			locationChange: state => state.user.locationChange,
			location: state => state.user.location,
			mineInfo: state => state.user.mineInfo,
			coachInfo: state => state.user.coachInfo,
			autograph: state => state.user.autograph,
			isPhone: state => state.user.isPhone
		}),
		filters: {
			handleCash(val){
				return ((val.balance*1 || 0) + (val.package_cash*1 || 0)).toFixed(2)
			}
		},
		async onLoad(options) {
			this.options = options
			let {
				type = 1
			} = options
			let {
				coachport = false
			} = this.configInfo.plugAuth
			if (!type) {
				type = this.userPageType
			}
			this.updateUserItem({
				key: 'userPageType',
				val: coachport ? 1 : type
			})
			let {
				id: mine_id = -1
			} = this.mineInfo
			if (mine_id == -1) {
				this.$util.showLoading()
			}
			let {
					tabBar
			} = this.configInfo
			let ind = tabBar.findIndex(item => {
				return item.id == 5
			})
			let navTitle = tabBar[ind].name
			this.navTitle = navTitle
			// #ifdef H5
			uni.setNavigationBarTitle({
				title: navTitle
			})
			// #endif
			// await this.initIndex()
		},
		onShow() {
			if (this.userPageType == 2) {
				this.getOrderNumCall()
			}else if(this.userPageType == 1){
				this.getUserOrderNumCall()
				this.getOrderCount()
			}
			// let {
			// 	id = 0,
			// 		status = 0
			// } = this.coachInfo
			// let {
			// 	coachport = false
			// } = this.configInfo.plugAuth
			// if (id && [2, 3].includes(status) && this.userPageType == 2 && !coachport) {
			// 	setTimeout(() => {
			// 		this.getOrderNumCall()
			// 		this.getCoachInfo()
			// 	}, 1000)
			// }
			if(this.isPhone){
				this.$util.showLoading()
				this.updateUserItem({
					key: 'isPhone',
					val: false
				})
			}
			this.initIndex()
		},
		onPullDownRefresh() {
			// #ifndef APP-PLUS
			uni.showNavigationBarLoading()
			// #endif
			if(this.userPageType == 1){
				this.getUserOrderNumCall()
				this.getOrderCount()
			}
			this.initRefresh();
			uni.stopPullDownRefresh()
		},
		methods: {
			...mapActions(['getConfigInfo', 'getPlugAuth', 'getUserInfo', 'getMineInfo', 'getCoachInfo',
				'updateCommonOptions',
			]),
			...mapMutations(['updateUserItem']),
			async initIndex(refresh = false) {
				// #ifdef H5
				if (!refresh && this.$jweixin.isWechat()) {
					await this.$jweixin.initJssdk();
					this.$jweixin.wxReady(() => {
						this.$jweixin.hideOptionMenu()
					})
				}
				// #endif 
				await this.getConfigInfo()
				let levelInd = this.toolList2.findIndex(aitem => {
					return aitem.text === '等级管理'
				})
				if(levelInd == -1 && this.configInfo.cash_type == 1){
					this.toolList2.unshift({
						icon: 'icondengjiguanli',
						text: '等级管理',
						url: '/technician/pages/level'
					})
				}
				if(levelInd != -1 && this.configInfo.cash_type == 2){
					this.toolList2.splice(levelInd, 1)
				}
				
				if (!this.configInfo.id || refresh) {
					
				} else {
					this.getPlugAuth()
				}
				await this.getMineInfo()
				this.isLoad = true

				let {
					fx_check,
					agent_article_id,
					plugAuth = {},
					broker_check = 0
				} = this.configInfo
				let {
					coach_status,
					fx_status,
					is_admin = 0,
					is_staff = 0,
					broker_status = -1
				} = this.mineInfo
				
				let {
					dynamic = false,
					broker = false,
					channel = false,
					storeplus = false,
					channelstaff = false
				} = plugAuth
				
				let positionInd = 0

				if (coach_status == 2 || coach_status == 3) {
					await this.getCoachInfo()
				}
				this.updateUserItem({
					key: 'userPageType',
					val: coach_status == 2 || coach_status == 3 ? this.userPageType : 1
				})

				// let arr = ['coach_status', 'channel_status']
				// let textArr = {
				// 	// coach_status: {
				// 	// 	text: '申请向导',
				// 	// 	list: {
				// 	// 		icon: 'iconwoyaoruzhu',
				// 	// 		text: '申请向导',
				// 	// 		url: '/user/pages/apply',
				// 	// 		class: 'technician'
				// 	// 	}
				// 	// }, 
				// 	channel_status: {
				// 		text: is_staff == 1 ? '我是渠道商' : '申请渠道商',
				// 		list: {
				// 			icon: 'iconqudaoshang',
				// 			text: is_staff == 1 ? '我是渠道商' : '申请渠道商',
				// 			url: is_staff == 1 ? '/user/pages/channel/my-turnover' : '/user/pages/channel/apply' ,
				// 			class: 'channel'
				// 		},
				// 		list2: {
				// 			icon: 'iconqudaoshang',
				// 			text: '我是渠道商',
				// 			url: is_staff == 1 ? '/user/pages/channel/my-turnover' :'/user/pages/channel/index',
				// 			class: 'channel'
				// 		}
				// 	}
				// }
				if(channel){
					positionInd = 1
					if(this.mineInfo.channel_status == 2 || this.mineInfo.channel_status == 3){ //渠道商申请通过
						let ind = this.toolList.findIndex(item => {
							return item.text == '申请渠道商'
						})
						
						if(ind != -1){
							this.toolList.splice(ind , 1)	
						}
						
						let arr = this.toolList.filter(aitem => {
							return aitem.text === '我是渠道商'
						})
						if (arr.length === 0) {
							this.toolList.unshift({
								icon: 'iconqudaoshang',
								text: '我是渠道商',
								url: is_staff == 1 ? '/user/pages/channel/my-turnover' :'/user/pages/channel/index',
								class: 'channel'
							})
						}
					}else{
						let ind = this.toolList.findIndex(item => {
							return item.text == '我是渠道商'
						})
						
						if(ind != -1){
							this.toolList.splice(ind , 1)	
						}
						let arr = this.toolList.filter(aitem => {
							return aitem.text === (is_staff == 1 ? '我是渠道商' : '申请渠道商')
						})
						if (arr.length === 0) {
							this.toolList.unshift({
								icon: 'iconqudaoshang',
								text: is_staff == 1 ? '我是渠道商' : '申请渠道商',
								url: is_staff == 1 ? '/user/pages/channel/my-turnover' : '/user/pages/channel/apply' ,
								class: 'channel'
							})
						}
						// 渠道商二级分销
						if(!channelstaff && is_staff == 0){
							let _ind = this.toolList.findIndex(item => {
								return item.text == '我是渠道商'
							})
							
							if(_ind != -1){
								this.toolList.splice(_ind , 1)	
							}
							
							let ind = this.toolList.findIndex(item => {
								return item.text == '申请渠道商'
							})
							if(ind == -1){
								this.toolList.unshift({
									icon: 'iconqudaoshang',
									text: '申请渠道商',
									url: '/user/pages/channel/apply' ,
									class: 'channel'
								})
							}
						}
					}
					
				}
				
				

				if (this.userPageType == 2) {
					this.getOrderNumCall()
				}

				if (dynamic) {
					let dynamicInd = this.toolList2.findIndex(item => {
						return item.text == '动态发布'
					})
					if (plugAuth.dynamic && dynamicInd == -1) {
						let badInd = this.toolList2.findIndex(item => {
							return item.text == '差评申诉'
						})
						this.toolList2.splice(badInd + 1, 0, {
							icon: 'icon-dongtai1',
							text: '动态发布',
							url: '/dynamic/pages/technician/list'
						})
					}
				}
				if(storeplus){
					if([2,3].includes(this.mineInfo.store_status)){
						let storeInd = this.toolList.findIndex(item => {
							return item.text == '商家端'
						})
						console.log(storeInd , '=========> storeInd')
						if(storeInd == -1){
							this.toolList.splice(positionInd, 0, {
								icon: 'iconmendian',
								text: '商家端',
								url: `/business/pages/manage/index` ,
								class: 'business'
							})
						}
					}else{
						let storeInd = this.toolList.findIndex(item => {
							return item.text == '商家端'
						})
						if(storeInd != -1){
							this.toolList.splice(storeInd, 1)
						}
					}
				}
				
				if(broker && ((!broker_check && [2,3].includes(broker_status)) || broker_check)){
					let brokerInd = this.toolList.findIndex(item => {
						return item.type == 'broker'
					})
					let storeInd = this.toolList.findIndex(item => {
						return item.text == '商家端'
					})
					let url = [2,3].includes(broker_status) ? `/user/pages/broker/index` : `/user/pages/broker/apply` 
					let text = [2,3].includes(broker_status) ? '经纪人' : '申请经纪人'
					if(brokerInd == -1){
						this.toolList.splice(storeInd + 1, 0, {
							icon: 'iconjingjiren',
							text ,
							url ,
							class: 'broker',
							type: 'broker'
						})
					}else{
						this.toolList[brokerInd].text = text
						this.toolList[brokerInd].url = url
					}
				}else{
					let brokerInd = this.toolList.findIndex(item => {
						return item.type == 'broker'
					})
					if(brokerInd != -1){
						this.toolList.splice(brokerInd, 1)
					}
				}
				
				
				let addrInd = this.toolList.findIndex(item => {
					return item.text == '地址管理'
				})
				let agentInd = this.toolList.findIndex(item => {
					return item.text == '合作加盟'
				})
				
				if (!is_admin && agentInd == -1) {
					this.toolList.splice((addrInd - 1) || 0,0, {
						icon: 'iconhehuoren2',
						text: '合作加盟',
						url: `/technician/pages/join-us` ,
						class: 'agent'
					})
				}
				if(is_admin && agentInd != -1){
					this.toolList.splice(agentInd, 1)
				}
				
				this.$util.hideAll()
			},
			initRefresh() {
				this.initIndex(true)
			},
			async getOrderNumCall() {
				console.log('======> getOrderNumCall')
				try{
					let data = await this.$api.technician.getOrderNum()
					this.orderList2[0].number = data.wait //待接单
					this.orderList2[1].number = data.start //待服务
					this.orderList2[2].number = data.progress //服务中
					let Ddata = await this.$api.technician.getDemandNum()
					this.findList[0].number = Ddata.status_1  //待接单
				}catch(e){
					//TODO handle the exception
				}
			},
			// 用户端 我的订单
			async getUserOrderNumCall(){
				console.log('======> getUserOrderNumCall')
				try{
					let data = await this.$api.user.getOrderNum()
					this.orderList[0].number = data.pay //待接单
					this.orderList[1].number = data.service //待服务
					this.orderList[2].number = data.ing //服务中
					this.orderList[3].number = data.comment //待评价
					this.orderList[4].number = data.refund //退款数量
				}catch(e){
					//TODO handle the exception
				}
			},
			// 用户端 团购订单
			async getOrderCount(){
				console.log('======> getOrderCount')
				try{
					let data = await this.$api.business.orderCount()
					this.packageList[0].number = data.status_1
					this.packageList[1].number = data.status_2
				}catch(e){
					//TODO handle the exception
				}
			},
			// 选择地区
			async toChooseLocation(type) {
				if (this.userPageType == 2 && type) return
				await this.$util.checkAuth({
					type: 'userLocation'
				})

				let {
					lat: locaLat = '',
					lng: locaLng = ''
				} = this.coachInfo
				let param = {}

				// #ifndef MP-WEIXIN
				param = {
					latitude: locaLat,
					longitude: locaLng
				}
				// #endif

				let [, {
					address = '',
					longitude: lng,
					latitude: lat
				}] = await uni.chooseLocation(param);
				if (!address) return
				await this.$api.technician.coachUpdate({
					address,
					lng,
					lat
				})
				let data = this.$util.deepCopy(this.coachInfo)
				data.address = address
				this.updateUserItem({
					key: 'coachInfo',
					val: data
				})
				this.$util.showToast({
					title: `更新成功`
				})
			},
			// 实时定位
			async toChangeLocation() {
				let {
					coach_status
				} = this.mineInfo
				if (coach_status != 2) return
				let {
					coach_position = 0
				} = this.userInfo
				let cur = coach_position == 0 ? 1 : 0
				await this.$api.technician.coachUpdate({
					coach_position: cur
				})
				await this.getUserInfo()
				this.$util.showToast({
					title: `操作成功`
				})
				this.updateUserItem({
					key: 'locationChange',
					val: cur == 1
				})
			},
			toJump(key, index) {
				let {
					url,
					text
				} = this[key][index]
				if (['申请'+this.$t('action.attendantName'), '申请分销商', '申请渠道商'].includes(text)) {
					this.toApply(text == '申请'+this.$t('action.attendantName') ? 1 : text == '申请分销商' ? 2 : 3)
					return
				}
				if (text == '切换用户端') {
					this.toChange()
					return
				}
				if (text == '联系客服') {
					// let {
					// 	mobile: url,
					// 	im_type
					// } = this.configInfo
					// // #ifdef MP-WEIXIN
					// if (im_type == 2) return
					// // #endif
					// this.$util.goUrl({
					// 	url,
					// 	openType: 'call'

					// })
					this.toContact()
					return
				}
				// let openType = key == 'orderList' && index !== 4 ? `reLaunch` : 'navigateTo'
				let openType = 'navigateTo'
				this.$util.log(url)
				this.$util.toCheckLogin({
					url,
					openType
				})
			},
			toContact() {
				let {
					hide_admin_mobile,
					mobile,
					im_type,
					qywx_company_id,
					qywx_kid
				} = this.configInfo
				
				if (im_type == 3) {
					// #ifdef H5
					window.location.href = qywx_kid
					// #endif
					// #ifndef H5 
					// #ifdef MP-WEIXIN 
					try {
						console.log(qywx_kid, qywx_company_id)
						wx.openCustomerServiceChat({
							extInfo: {
								url: qywx_kid
							},
							corpId: qywx_company_id,
							fail(res) {
								uni.showToast({
									title: res.errorMsg,
									icon: 'none'
								})
							}
						})
					} catch (e) {
						this.$util.showToast({
							title: `请更新微信版本`
						})
					}
					// #endif
					// #ifdef APP-PLUS
					let wechat = null
					plus.share.getServices(res => {
						console.log(res, "==============res")
						wechat = res.find(i => i.id === 'weixin')
						if (wechat) {
							wechat.openCustomerServiceChat({
								corpid: qywx_company_id,
								url: qywx_kid
							}, err => {
								uni.showToast({
									title: err.errorMsg
								})
							})
						} else {
							uni.showToast({
								title: '当前环境不支持微信操作',
								icon: 'none'
							})
						}
					}, function() {
						uni.showToast({
							title: '获取服务失败，不支持该操作',
							icon: 'none'
						})
					})
					// #endif
					// #endif
					return
				}
			
				let url = mobile
				this.$util.goUrl({
					url,
					openType: 'call'
				})
			},
			async toAtv() {
				if (!this.mineInfo.is_atv) {
					this.$util.showToast({
						title: `暂无活动`
					})
					return
				}
				let options = this.commonOptions
				options.coupon_atv_id = 0
				await this.updateCommonOptions(options)
				this.$util.toCheckLogin({
					url: `/user/pages/coupon/share`
				})
			},
			// 申请向导/分销商/渠道商
			async toApply(type) {
				let {
					coach_status = -1,
						fx_status = -1,
						channel_status = -1
				} = this.mineInfo
				let status = type == 1 ? coach_status : type == 2 ? fx_status :
					channel_status
				let page = {
					1: `/technician/pages/apply`,
					2: `/user/pages/distribution/apply`,
					3: `/user/pages/channel/apply`
				}
				// -1未申请，1审核中，2审核通过，3取消授权，4审核失败
				let url = status == -1 ? page[type] :
					`/user/pages/apply-result?type=${type}`
				this.$util.log(url)
				this.$util.toCheckLogin({
					url
				})
			},
			// 切换用户/向导端
			async toChange() {
				uni.pageScrollTo({
					duration: 500,
					scrollTop: 0
				})
				let {
					userPageType = 1
				} = this
				if (userPageType == 2) {
					await this.getCoachInfo()
					this.getUserOrderNumCall()
					this.getOrderCount()
				}
				if (userPageType == 1) {
					this.getOrderNumCall()
				}
				this.updateUserItem({
					key: 'userPageType',
					val: userPageType == 2 ? 1 : 2
				})
			},
			onChange(e) {
				let {
					x,
					y
				} = e.detail
				this.$nextTick(() => {
					this.offsetL = x
					this.offsetT = y
				})
			},
			// 求助
			async toHelp() {
				// #ifdef H5
				if (this.$jweixin.isWechat()) {
					this.$util.showLoading()
					await this.$jweixin.wxReady2();
					let {
						latitude: lat = 0,
						longitude: lng = 0
					} = await this.$jweixin.getWxLocation()
					if (!lat) {
						this.$util.hideAll()
						this.$util.showToast({
							title: `请授权定位当前地址`
						})
						return
					}
					if (lat && lng) {
						let key = `${lat},${lng}`
						let data = await this.$api.base.getMapInfo({
							location: key
						})
						let {
							status,
							result
						} = JSON.parse(data)
						if (status == 0) {
							this.$util.hideAll()
							let {
								address
							} = result
							this.toPolice({
								lat,
								lng,
								address
							})
						}
					}
				}
				// #endif
				// #ifndef H5
				this.$util.showLoading()
				let {
					lat = '',
						lng = '',
						address = ''
				} = await this.$util.getBmapLocation()
				if (!lat) {
					this.$util.hideAll()
					this.$util.showToast({
						title: `请授权定位当前地址`
					})
					return
				}
				this.toPolice({
					lat,
					lng,
					address
				})
				// #endif
			},
			async toPolice(param) {
				await this.$api.technician.police(param)
				this.$util.hideAll()
				this.$util.showToast({
					title: `求救成功`
				})
			}
		}
	}
</script>

<style lang="scss">
	.pages-mine {
		.mine-user-bg {
			width: 100%;
			height: 739rpx;
			/* #ifdef H5 */
			z-index: 0;
			/* #endif */
			/* #ifndef H5 */
			z-index: -1;
			/* #endif */
			top: 0;
		}

		.mine-bg {
			width: 100%;
			height: 368rpx;
			/* #ifdef H5 */
			z-index: 0;
			/* #endif */
			/* #ifndef H5 */
			z-index: -1;
			/* #endif */
		}

		.mine-master-bg {
			width: 100%;
			height: 514rpx;
			z-index: -1;
		}

		.avatar_view {
			width: 130rpx;
			height: 130rpx;

			.avatar {
				width: 120rpx;
				height: 120rpx;
				overflow: hidden;

				open-data {
					width: 120rpx;
					height: 120rpx;
				}
			}

			.user-avatar-bg {
				width: 154rpx;
				height: 154rpx;
				top: -10rpx;
				left: -17rpx;
				z-index: 1;
			}
		}

		.admin-tag {
			min-width: 105rpx;
			height: 32rpx;
			padding: 0 10rpx;
			font-size: 20rpx;
			transform: rotateZ(360deg);
			background: linear-gradient(to right, #AAB8FF,#627CFB);
			.iconfont {
				font-size: 20rpx;
				margin-right: 2rpx;
			}

			.bg {
				opacity: 0.1;
				border-radius: 18rpx;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				z-index: 1;
			}
		}

		.iconbianjiziliao {
			font-size: 36rpx;
		}

		.icon-xitong {
			font-size: 36rpx;
		}

		// 定位/时间/求救
		.addr-time-help-list {
			width: 100%;
			padding: 40rpx 0;

			.flex-center {
				width: 33.33%;

				.iconfont {
					font-size: 60rpx;
					margin-bottom: 10rpx;
				}
			}

		}

		// 向导信息
		.coach-info {
			margin: 0 25rpx;

			.avatar {
				width: 140rpx;
				height: 140rpx;
			}

			.coach-name {
				font-size: 34rpx;
				color: #142C57
			}

			.coach-text {
				color: #5A677E
			}

			.icon-right {
				font-size: 22rpx
			}

			.tag-item {
				min-width: 92rpx;
				height: 36rpx;
				padding: 0 10rpx;
				font-size: 24rpx;
				border-radius: 6rpx;
				transform: rotateZ(360deg);
			}

			.map-addr-info {
				.map-addr {
					width: 102rpx;
					height: 46rpx;
					padding-right: 6rpx;

					.iconfont {
						margin-right: 2rpx;
						font-size: 26rpx;
					}
				}

				.map-addr.abs {
					top: 0;
					left: 0;
					opacity: 0.1;
				}

				.text {
					color: #5A677E;
				}

				.location-change {
					.iconfont {
						font-size: 70rpx;
					}
				}

			}
		}

		// 收入
		.mine-count-list {
			margin: 20rpx 25rpx 0 25rpx;

			.cancel-auth {
				width: 110rpx;
				height: 100rpx;
				font-size: 100rpx;
				top: -20rpx;
				right: 55rpx;

				.text-bold {
					height: 26rpx;
					transform: rotate(-32deg);
				}
			}

			.item-child {
				width: 50%;
				padding: 28rpx;

				.text {
					color: #5A677E;
				}

				.cash-btn {
					width: 108rpx;
					height: 46rpx;
					transform: rotateZ(360deg);
				}
			}

		}


		.dot-unread-number {
			top: 0;
			right: 0;
			width: 24rpx;
			height: 24rpx;
			line-height: 24rpx;
			text-align: center;
			color: #fff;
			font-size: 18rpx;
			border-radius: 24rpx;
			background-color: #F1381F;
		}

		// 卡券/关注/收藏
		.share-collect-list {
			margin: 36rpx 25rpx 0 25rpx;

			.share-item {
				width: 50%;
				height: 105rpx;
				color: #AAB3C4;

				.f-md-title {
					color: #313131;
				}
			}
		}

		.store-img {
			width: 710rpx;
			height: 140rpx;
		}

		.store-btn {
			width: 119rpx;
			height: 57rpx;
			color: #643BE1;
			background: #FFFFFF;
			box-shadow: 0rpx 2rpx 12rpx 0rpx rgba(77, 91, 228, 0.28), inset 0rpx 1rpx 41rpx 0rpx rgba(173, 133, 255, 0.24);
			border-radius: 29rpx;
		}

		// 分享-卡券
		.share-atv-img {
			width: 710rpx;
			height: 177rpx;
			margin: 0 auto;
		}

		// 我的订单/其他
		.mine-menu-list {
			margin: 20rpx 25rpx 0 25rpx;

			.menu-title {
				height: 90rpx;

				.iconfont {
					font-size: 24rpx;
				}
			}

			.item-child {
				width: 20%;
				margin: 10rpx 0;

				.iconfont {
					font-size: 52rpx;
				}

				.item-img {
					width: 88rpx;
					height: 88rpx;

					.iconfont {
						font-size: 44rpx;
					}

					.item-img {
						top: 0;
						left: 0;
						opacity: 0.1;
					}
				}

				.order-img {
					width: 92rpx;
					height: 92rpx;
				}

				.address {
					background-image: linear-gradient(180deg, #13DCE4 0%, #3DCEFD 100%)
				}

				.question {
					background-image: linear-gradient(180deg, #FFC25F 0%, #FE793D 100%)
				}

				.contact {
					background-image: linear-gradient(180deg, #FF9E6C 0%, #FF447F 100%)
				}
				.find {
					background-image: linear-gradient(180deg, #13DCE4 0%, #3DCEFD 100%)
				}
				.agent {
					background-image: linear-gradient(180deg, #FF9E6C 0%, #FF447F 100%)
				}

				.channel {
					background-image: linear-gradient(180deg, #FFC25F 0%, #FE793D 100%)
				}

				.technician {
					background-image: linear-gradient(180deg, #46BCFF 0%, #2587FF 50%, #1976FF 100%)
				}

				.change {
					background-image: linear-gradient(180deg, #BD76E8 0%, #6C12DA 100%)
				}
			}
		}
		.wizard-settle{
			height: 250rpx;
			overflow: hidden;
			.settle-bg{
				width: 755rpx;
				height: 268rpx;
				margin-bottom: -50rpx;
			}
			.wizard-box{
				bottom: 55rpx;
				right: 30rpx;
				width: 494rpx;
				height: 148rpx;
			}
			.wizard-btn{
				width: 160rpx;
				height: 54rpx;
				border-radius: 54rpx;
			}
		}
		
		
		/*会员卡*/
		.member-card-box{
			width: 700rpx;
			height: 126rpx;
			padding-left: 150rpx;
			.member-card-btn{
				width: 149rpx;
				height: 54rpx;
				border-radius: 54rpx;
				color: #F2DBC9;
				background: linear-gradient( 270deg, #301A0B 0%, #301A0B 100%);
			}
		}
		.member-card-bg{
			width: 700rpx;
			height: 126rpx;
		}
	}
</style>
