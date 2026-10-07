<template>
	<uni-popup type="center" :maskClick="maskClick" ref="common_popup">
		<view class="common-popup-content fill-base">
			<view class="title" v-if="ptitle">{{ptitle}}</view>
			<view class="desc" v-if="pdesc">{{pdesc}}</view>
			<image mode="aspectFill" class="image" :class="imgSize" :src="info.image" v-if="info.image"></image>
			<view class="name" v-if="info.name">{{info.name}}</view>
			<view class="button">
				<block v-for="(item,index) in pbutton" :key="index">
					<view @tap.stop="toEmit(index)" class="item-child"
						:style="{background: item.type == 'confirm' ? primaryColor : '',color:item.type == 'confirm' ?'white':''}">
						{{item.title}}
					</view>
				</block>
			</view>
		</view>
	</uni-popup>
</template>

<script>
	const Types = {
		'REFUSE_ORDER': {
			title: '拒绝接单',
			desc: '请确认是否拒绝接单',
			text: '确认拒绝'
		},
		'CANCEL_ORDER': {
			title: '取消订单',
			desc: '请确认是否取消订单，取消后将无法恢复',
			text: '确认取消'
		},
		'DELETE_ORDER': {
			title: '删除订单',
			desc: '请确认是否删除订单，删除后将无法恢复',
			text: '确认删除'
		},
		'CANCEL_REFUND_ORDER': {
			title: '取消退款',
			desc: '请确认是否取消退款',
			text: '确认取消'
		},
		'NO_PASS_REFUND': {
			title: '拒绝退款',
			desc: '请确认是否拒绝退款',
			text: '确认拒绝'
		},
		'HX_CODE': {
			title: '核销码',
			desc: '请出示二维码给核销人员',
		},
		'HX_ORDER': {
			title: '核销订单',
			desc: '请确认是否核销订单',
			text: '确认核销'
		},
	}
	import {
		mapState,
		mapMutations
	} from "vuex"
	export default {
		components: {},
		props: {
			maskClick: {
				type: Boolean,
				default () {
					return false
				}
			},
			type: {
				type: String,
				default () {
					return 'CANCEL_ORDER'
				}
			},
			title: {
				type: String,
				default () {
					return ''
				}
			},
			desc: {
				type: String,
				default () {
					return ''
				}
			},
			info: {
				type: Object,
				default () {
					return {}
				}
			},
			button: {
				type: Array,
				default () {
					return [{
						title: '取消',
						type: 'cancel'
					}, {
						title: '确定取消',
						type: 'confirm'
					}]
				}
			},
			imgSize: {
				type: String,
				default () {
					return ''
				}
			}
		},
		created() {
			this.init();
		},
		data() {
			return {
				ptitle: '',
				pdesc: '',
				pbutton: [],
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		methods: {
			init() {
				let {
					type,
				} = this;
				if (Types[type]) {
					this.ptitle = this.title || Types[type].title
					this.pdesc = this.desc || Types[type].desc
					this.pbutton = this.button
					if (!Types[type].text) return
					this.pbutton[1].title = Types[type].text
				}
			},
			open() {
				this.$refs.common_popup.open()
			},
			close() {
				this.$refs.common_popup.close()
			},
			toEmit(index) {
				let {
					type
				} = this.button[index]
				if (type == 'cancel') {
					this.close()
				} else {
					this.$emit(type)
				}
			}
		},
	}
</script>

<style scoped lang="scss">
</style>
