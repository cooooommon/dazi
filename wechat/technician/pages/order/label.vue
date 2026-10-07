<template>
	<view class="pages-technician-label">

		<view class="flex-y-center pd-lg">
			<view class="f-title c-title text-bold">推荐标签</view>
			<view class="f-caption c-caption ml-md">点击添加标签</view>
		</view>

		<view class="list-item ml-lg mr-lg f-caption">
			<view v-if="base_label.length==0">暂无标签</view>
			<view @tap="toAddDel(1,index)" class="list-child radius"
				:style="{color:item.is_select === 1 ? '#CBCBCB' : '#666'}"
				v-for="(item,index) in base_label" :key="index">
				<view class="tag-item flex-center">
					<i class="iconfont icon-add"></i>
					<view>{{item.title}}</view>
				</view>
			</view>
		</view>

		<view class="flex-y-center pd-lg mt-lg">
			<view class="f-title c-title text-bold">已选标签</view>
		</view>
		<view class="list-item ml-lg mr-lg f-caption">
			<view v-if="check_label.length==0">暂无选中标签</view>
			<view @tap="toAddDel(2,index)" class="list-child cur radius rel" style="border: none;"
				v-for="(item,index) in check_label" :key="index">
				<view class="bg-item radius abs" :style="{background:primaryColor,borderColor:primaryColor}"></view>
				<i class="iconfont icon-guanbi-fill abs"></i>
				<view class="tag-item flex-center" :style="{color:primaryColor}">{{item.title}}</view>
			</view>
		</view>

		<view class="space-max-footer"></view>

		<fix-bottom-button @confirm="submitForm" :text="[{text:'确定',type:'confirm'}]" bgColor="#fff">
		</fix-bottom-button>


	</view>
</template>

<script>
	import {
		mapState,
		mapActions,
	} from 'vuex';
	export default {
		data() {
			return {
				options: {},
				base_label: [],
				check_label: []
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor
		}),
		async onLoad(options) {
			this.options = options
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			this.initIndex()
		},
		methods: {
			...mapActions([]),
			async initIndex() {
				let data = await this.$api.technician.labelList()
				data.map(item => {
					item.selected = 0
				})
				this.base_label = data
			},
			toAddDel(type, index) {
				let base_label = this.$util.deepCopy(this.base_label)
				if (type == 1) {
					let {
						is_select
					} = base_label[index]
					if (is_select == 1) return
					base_label[index].is_select = 1
					this.base_label = base_label
					let check_label = base_label.filter(item => {
						return item.is_select == 1
					})
					this.check_label = check_label
					return
				}
				let check_label = this.$util.deepCopy(this.check_label)
				let {
					id
				} = check_label[index]
				let ind = base_label.findIndex(item => {
					return item.id == id
				})
				base_label[ind].is_select = 0
				this.base_label = base_label
				check_label.splice(index, 1)
				this.check_label = check_label
			},
			async submitForm() {
				let label = this.check_label.map(item => {
					return item.id
				})
				if (label.length == 0) {
					this.$util.showToast({
						title: '请选中标签'
					})
					return
				}
				let {
					id,
					uid,
				} = this.options
				let param = {
					order_id: id,
					user_id: uid,
					label
				}
				this.$util.showLoading();
				await this.$api.technician.userLabelAdd(param);
				this.$util.hideAll();
				this.$util.showToast({
					title: '评价成功'
				})
				this.$util.back()
				setTimeout(() => {
					this.$util.goUrl({
						url: 1,
						openType: `navigateBack`
					})
				}, 1000)
			}
		}
	}
</script>

<style lang="scss">
	page {
		background: #fff;
	}

	.pages-technician-label {
		.list-item {

			.list-child {
				width: auto;
				min-width: 120rpx;
				height: 70rpx;
				display: inline-block;
				padding: 0 20rpx;
				margin: 0 18rpx 23rpx 0;
				border: 1rpx solid #E5E5E5;

				.tag-item {
					height: 70rpx;

					.icon-add {
						font-size: 24rpx;
						margin-right: 5rpx;
					}
				}

				.bg-item {
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					opacity: 0.15;
				}

				.icon-guanbi-fill {
					top: -10rpx;
					right: 2rpx;
					color: #B5BCC8;
					font-size: 28rpx;
				}

			}

			.cur {
				margin: 0 20rpx 28rpx 0;
			}
		}
	}
</style>
