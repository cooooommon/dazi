<template>
	<view>
		<view class="ui-fixed" :class="{'fixed-top':position=='top','fixed-bottom':position=='bottom'}"
			:style="{top:position=='top'?`${top}px`:`` , zIndex: zIndex}">
			<slot></slot>
		</view>
		<view :style="{height:height+'px'}" v-if="position=='top'"></view>
	</view>
</template>

<script>
	export default {
		name: 'fixed',
		props: {
			position: {
				type: [String],
				default () {
					return 'top'
				}
			},
			top: {
				type: [Number],
				default () {
					return 0
				}
			},
			zIndex: {
				type: [Number],
				default () {
					return 999
				}
			},
			initHeight: {
				type: String || Number,
				default () {
					return ''
				}
			},
		},
		data() {
			return {
				height: uni.getSystemInfoSync().windowWidth * 100 / 750
			}
		},
		mounted() {
			setTimeout(() => {
				this.setHeight();
			}, 100)
		},
		watch: {
			initHeight(newVal, oldval) {
				this.$nextTick(() => {
					this.setHeight()
				})
			}
		},
		methods: {
			setHeight() {
				var _this = this;
				var query = uni.createSelectorQuery().in(_this);
				query.select('.ui-fixed').boundingClientRect(function(res) {
					_this.height = res.height;
				}).exec();
			}
		},
	}
</script>


<style>
	.ui-fixed {
		position: fixed;
		left: 0;
		width: 100%;
		z-index: 999;
	}

	.fixed-top {
		top: 0;
	}

	.fixed-bottom {
		bottom: 0;
	}
</style>
