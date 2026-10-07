<template>
	<view class='search-box'
		:style='{padding:`${padding}rpx`,margin:`${margin}rpx`,borderRadius:`${radius}rpx`,background:backgroundColor}'>
		<view class='search-item' :class="[{'flex-y-center': type == 'text'},{'flex-between': type == 'input'}]"
			:style='{height: height ? `${height}rpx` : `` ,borderRadius: height ? `${height}rpx` : searchStyleObj[searchStyle],background:frontColor,justifyContent:textAlign}'>
			<block v-if="type=='text'">
				<i class="iconfont iconsousuo c-title"></i>
				<view class='ml-md placeholder'>{{placeholder}}</view>
			</block>
			<block v-if="type=='input'">
				<view class="flex-y-center flex-1" v-if="iconLeft">
					<i class="iconfont iconsousuo c-title"></i>
					<input type='text' class="flex-1 ml-md mr-md f-paragraph c-title" :disabled="disabled"
						:placeholder='placeholder' placeholder-class='f-paragraph c-caption' confirm-type="search"
						@input="handerInput" :value="keyword" @confirm="confirm" :auto-focus="autofocus"></input>
				</view>
				<view class="flex-y-center flex-1 pr-lg" v-else>
					<input type='text' class="flex-1 ml-md mr-md f-paragraph c-title" :disabled="disabled"
						:placeholder='placeholder' placeholder-class='f-paragraph c-caption' confirm-type="search"
						@input="handerInput" :value="keyword" @confirm="confirm" :auto-focus="autofocus"></input>
					<i class="iconfont iconsousuo c-title"></i>
				</view>
				<view class='search-item-btn flex-center radius' :style="{background:primaryColor}" @tap='confirm'
					v-if="confirmSearch">搜索</view>
			</block>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		name: 'search',
		props: {
			type: {
				type: String,
				default () {
					return 'text'
				}
			},
			placeholder: {
				type: String,
				default () {
					return "输入关键字进行搜索"
				}
			},
			searchStyle: {
				type: String,
				default () {
					return 'circle'
				}
			},
			textAlign: {
				type: String,
				default () {
					return 'center'
				}
			},
			padding: {
				type: Number,
				default () {
					return 30
				}
			},
			margin: {
				type: Number,
				default () {
					return 0
				}
			},
			radius: {
				type: Number,
				default () {
					return 0
				}
			},
			backgroundColor: {
				type: String,
				default () {
					return '#fff'
				}
			},
			frontColor: {
				type: String,
				default () {
					return '#f4f6f8'
				}
			},
			autofocus: {
				type: Boolean,
				default () {
					return false
				}
			},
			disabled: {
				type: Boolean,
				default () {
					return false
				}
			},
			focus: {
				type: Boolean,
				default () {
					return false
				}
			},
			keyword: {
				type: String,
				default () {
					return ''
				}
			},
			confirmSearch: {
				type: Boolean,
				default () {
					return false
				}
			},
			iconLeft: {
				type: Boolean,
				default () {
					return true
				}
			},
			height: {
				type: [String,Number],
				default () {
					return 0
				}
			},
		},
		created() {
			
		},
		data() {
			return {
				searchStyleObj: {
					square: '0rpx',
					radius: '10rpx',
					circle: '30rpx'
				},
				text: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		methods: {
			confirm(e) {
				let val = this.text;
				this.$emit("confirm", val)
			},
			handerInput(e) {
				let val = e.detail.value;
				this.text = val;
				this.$emit("input", val)
			}
		},
	}
</script>


<style>
	.search-box {
		padding: 16rpx 16rpx;
		display: flex;
		align-items: center;
		background: #efeff5;
	}

	.search-item {
		width: 100%;
		height: 64rpx;
		background: #ffffff;
		border-radius: 30rpx;
		padding: 0 0 0 25rpx;
		line-height: 1;
		font-size: 28rpx;
		color: #888;
	}

	.placeholder {
		font-size: 28rpx;
		color: #999;
	}

	.search-item-btn {
		width: 110rpx;
		height: 64rpx;
		font-size: 26rpx;
		font-weight: 400;
		color: #ffffff;
	}

	.search-btn {
		padding: 0 20rpx 0 40rpx;
	}

	.search-icon {
		width: 30rpx;
		height: 30rpx;
	}

	.flex-1 {
		flex: 1;
	}

	.icon-md {
		width: 40rpx;
		height: 40rpx;
	}

	.ml-md {
		margin-left: 16rpx;
	}
</style>
