<template>
	<view class="uni-status-bar" :style="style">
		<slot></slot>
	</view>
</template>

<script>

	import { getSystemInfo } from '@/utils/systemInfo.js';
	export default {
		computed: {
			style() {
				//#ifdef APP-PLUS
				return ''
				//#endif
				//#ifndef APP-PLUS
				return `height:${getSystemInfo().statusBarHeight}px`
				//#endif
			}
		}
	}
</script>

<style>
	.uni-status-bar {
		display: block;
		width: 100%;
		height: 20px;
		height: var(--status-bar-height);
	}
</style>
