<template>
	<view :class="['ui-mask ','center',effect,{'show':show}]" @tap="handleMaskTap" :style='{top:top,background:background}'>
		<slot></slot>
	</view>
</template>

<script>
	export default {
		name: 'mask',
		props: {
			background: {
				type: String,
				default: "rgba(0, 0, 0, 0.5)"
			},
			show: {
				type: Boolean
			},
			top: {
				type: String,
				default () {
					return '0'
				}
			},
			effect: {
				type: String
			},
			hideDelay: {
				type: Number
			},
			hideOnTap: {
				type: Number,
				default () {
					return 1
				}
			},
			blur: {
				type: String
			}
		},
		created() {

		},
		data() {
			return {

			}
		},
		watch: {
			show(newValue, oldValue) {
				var _this = this;

				if (newValue) {
					this.selfShow = false
				} else {
					if (this.hideDelay) {
						setTimeout(function() {
							_this.selfShow = false
						}, this.hideDelay);
					} else {
						_this.selfShow = false
					}
				}
			}
		},
		methods: {
			handleMaskTap(e) {
				if (this.hideOnTap) {
					this.show = false;
					this.$emit('hide')
				}
			}
		},
	}
</script>


<style>
	.ui-mask {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		width: 100%;
		visibility: hidden;
		opacity: 0;
		transition: all 0.25s ease-in;
		-webkit-backface-visibility: hidden;
		z-index: 0;
	}

	.center {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.ui-mask.scale-out {
		transition: all 0.25s ease-in;
		transform: scale(0);
	}

	.ui-mask.scale-out.show {
		transform: scale(1);
	}

	.ui-mask.scale-in {
		transition: all 0.25s ease-in;
		transform: scale(1.5);
	}

	.ui-mask.scale-in.show {
		transform: scale(1);
	}

	.ui-mask.show {
		opacity: 1;
		visibility: visible;
	}
</style>
